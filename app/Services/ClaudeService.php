<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;

class ClaudeService
{
    private string $groqKey;
    private string $groqModel = 'llama-3.1-8b-instant'; // modèle léger = réponse rapide

    public function __construct()
    {
        $this->groqKey = config('services.groq.key', '');
    }

    public function chat(array $history): string
    {
        if ($this->groqKey) {
            try {
                set_time_limit(120); // évite le timeout PHP sur appel externe

                $messages = array_merge(
                    [['role' => 'system', 'content' => $this->systemPrompt()]],
                    $history
                );

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->groqKey,
                    'Content-Type'  => 'application/json',
                ])->timeout(55)->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => $this->groqModel,
                    'max_tokens'  => 500,
                    'temperature' => 0.7,
                    'messages'    => $messages,
                ]);

                if ($response->successful()) {
                    $text = $response->json('choices.0.message.content');
                    if ($text) return $text;
                }

                \Illuminate\Support\Facades\Log::warning('Groq chat failed', [
                    'status' => $response->status(),
                    'body'   => substr($response->body(), 0, 300),
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Groq exception: ' . $e->getMessage());
            }
        }

        // Mode fallback : réponses prédéfinies
        return $this->fallback($history);
    }

    private function fallback(array $history): string
    {
        $last    = collect($history)->where('role', 'user')->last();
        $message = strtolower($last['content'] ?? '');

        // Produits
        if ($this->contains($message, ['produit', 'article', 'catalogue', 'vend', 'achet', 'dispo'])) {
            $products = Product::active()->take(6)->get();
            if ($products->isEmpty()) {
                return "Notre catalogue est en cours de mise à jour. Revenez bientôt ou contactez-nous à aimesoro81@gmail.com 😊";
            }
            $list = $products->map(fn($p) => "• {$p->name} — " . number_format($p->price, 0, ',', ' ') . " FCFA")->join("\n");
            return "Voici quelques produits disponibles :\n{$list}\n\nConsultez notre catalogue complet sur le site !";
        }

        // Livraison
        if ($this->contains($message, ['livraison', 'livrer', 'délai', 'délais', 'expédi', 'envoyer'])) {
            return "La livraison est 100% gratuite partout en Côte d'Ivoire 🚚. Le délai est généralement de 24 à 72h selon votre localisation.";
        }

        // Paiement
        if ($this->contains($message, ['paiement', 'payer', 'paye', 'prix', 'tarif', 'combien', 'coût'])) {
            return "Nous acceptons plusieurs modes de paiement 💳 :\n• GeniusPay (carte bancaire, Orange Money, MTN Money, Wave)\n• Paiement à la livraison\n• Paiement en 2x, 3x ou 4x sans frais !";
        }

        // Tranche / plusieurs fois
        if ($this->contains($message, ['tranche', 'plusieurs fois', 'mensualité', 'fois', 'échelonné'])) {
            return "Oui, vous pouvez payer en plusieurs fois sans frais ! 🎉 Choisissez 2x, 3x ou 4x au moment du checkout. La première tranche est payée immédiatement, les suivantes selon les échéances.";
        }

        // Commande / suivi
        if ($this->contains($message, ['commande', 'suivi', 'statut', 'où est', 'tracking', 'annul'])) {
            return "Pour suivre ou gérer votre commande, connectez-vous sur le site et rendez-vous dans **Mes commandes** 📦. Vous y verrez le statut en temps réel.";
        }

        // Retour / remboursement
        if ($this->contains($message, ['retour', 'rembours', 'échange', 'renvoi', 'défaut'])) {
            return "Pour tout retour ou remboursement, contactez notre support à aimesoro81@gmail.com en indiquant votre numéro de commande. Nous traiterons votre demande dans les 48h 🙏";
        }

        // Contact / support
        if ($this->contains($message, ['contact', 'support', 'aide', 'help', 'joindre', 'email', 'mail', 'téléphone'])) {
            return "Vous pouvez nous contacter par email à aimesoro81@gmail.com. Nous répondons généralement dans les 24h ouvrées 📧";
        }

        // Bonjour / salut
        if ($this->contains($message, ['bonjour', 'bonsoir', 'salut', 'hello', 'hi', 'coucou', 'bonne journée'])) {
            return "Bonjour ! Je suis Sara, votre assistante ShopCI 😊 Je peux vous aider avec nos produits, la livraison, le paiement ou le suivi de commandes. Que puis-je faire pour vous ?";
        }

        // Merci
        if ($this->contains($message, ['merci', 'super', 'parfait', 'excellent', 'nickel', 'ok', 'bien'])) {
            return "Avec plaisir ! 😊 N'hésitez pas si vous avez d'autres questions. Bonne navigation sur ShopCI !";
        }

        // Par défaut
        return "Je suis là pour vous aider avec nos produits, la livraison, le paiement et les commandes 😊 Pouvez-vous préciser votre question ? Vous pouvez aussi nous contacter directement à aimesoro81@gmail.com";
    }

    private function contains(string $text, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            if (str_contains($text, $kw)) return true;
        }
        return false;
    }

    private function systemPrompt(): string
    {
        $products = Product::active()->inStock()->get()
            ->map(fn($p) => "{$p->name} ({$p->category}) — " . number_format($p->price, 0, ',', ' ') . " FCFA, stock:{$p->stock}")
            ->join("\n");

        $catalogue = $products ?: "Aucun produit en stock actuellement.";

        return "Tu es Sara, assistante de ShopCI (boutique en ligne en Côte d'Ivoire). "
             . "Réponds TOUJOURS en français, sois concise et utile.\n\n"
             . "CATALOGUE COMPLET EN STOCK :\n{$catalogue}\n\n"
             . "INFOS SHOPCI :\n"
             . "- Livraison gratuite partout en Côte d'Ivoire (24-72h)\n"
             . "- Paiement : Orange Money, MTN Money, Wave, carte bancaire, paiement à la livraison\n"
             . "- Paiement en 2x, 3x ou 4x sans frais\n"
             . "- Support : aimesoro81@gmail.com\n\n"
             . "RÈGLES :\n"
             . "- Pour recommandations : cite nom + prix + raison du choix\n"
             . "- N'invente JAMAIS un produit absent du catalogue\n"
             . "- Pour suivi commande : dire de se connecter > Mes commandes\n"
             . "- Si produit non disponible : le dire honnêtement";
    }
}
