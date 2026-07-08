<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\ClaudeService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(private ClaudeService $claude) {}

    /** Envoyer un message et obtenir la réponse de l'IA */
    public function send(Request $request)
    {
        // Toujours répondre en JSON même en cas d'erreur
        $request->headers->set('Accept', 'application/json');

        $request->validate(['message' => ['required', 'string', 'max:1000']]);

        $conversation = $this->getOrCreateConversation($request);

        // Sauvegarder le message utilisateur
        $conversation->messages()->create([
            'role'    => 'user',
            'content' => $request->message,
        ]);

        // Construire l'historique (10 derniers messages pour le contexte)
        $history = $conversation->messages()
            ->latest()->take(10)->get()->reverse()
            ->map(fn($m) => ['role' => $m->role, 'content' => $m->content])
            ->values()->toArray();

        try {
            $reply = $this->claude->chat($history);
        } catch (\Exception $e) {
            report($e);
            $reply = "Je rencontre une difficulté technique. Veuillez réessayer dans un instant ou contacter notre support.";
        }

        // Sauvegarder la réponse
        $conversation->messages()->create([
            'role'    => 'assistant',
            'content' => $reply,
        ]);

        return response()->json([
            'reply' => $reply,
            'source' => 'ai', // toujours ai — le fallback est transparent pour le client
        ]);
    }

    /** Historique de la conversation en cours */
    public function history(Request $request)
    {
        $conversation = $this->findConversation($request);

        if (!$conversation) {
            return response()->json(['messages' => []]);
        }

        $messages = $conversation->messages()->get()->map(fn($m) => [
            'role'    => $m->role,
            'content' => $m->content,
            'time'    => $m->created_at->format('H:i'),
        ]);

        return response()->json(['messages' => $messages]);
    }

    // ── Admin ───────────────────────────────────────────────────────

    public function adminIndex()
    {
        $conversations = ChatConversation::with('user')
            ->withCount('messages')
            ->latest()
            ->paginate(20);

        return view('admin.conversations.index', compact('conversations'));
    }

    public function adminShow(ChatConversation $conversation)
    {
        $conversation->load('user', 'messages');
        return view('admin.conversations.show', compact('conversation'));
    }

    // ── Helpers ─────────────────────────────────────────────────────

    private function getOrCreateConversation(Request $request): ChatConversation
    {
        $existing = $this->findConversation($request);
        if ($existing) return $existing;

        $conversation = ChatConversation::create([
            'user_id'    => auth()->id(),
            'session_id' => $request->session()->getId(),
            'guest_name' => auth()->check() ? null : null,
        ]);

        $request->session()->put('chat_conversation_id', $conversation->id);

        return $conversation;
    }

    private function findConversation(Request $request): ?ChatConversation
    {
        if (auth()->check()) {
            return ChatConversation::where('user_id', auth()->id())
                ->latest()->first();
        }

        $id = $request->session()->get('chat_conversation_id');
        if ($id) {
            return ChatConversation::find($id);
        }

        return null;
    }
}
