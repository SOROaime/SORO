<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'min:2', 'max:100'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:25'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'name.required'    => 'Votre nom est obligatoire.',
            'email.required'   => 'Votre email est obligatoire.',
            'email.email'      => 'L\'email n\'est pas valide.',
            'subject.required' => 'Le sujet est obligatoire.',
            'message.required' => 'Votre message est obligatoire.',
            'message.min'      => 'Le message doit contenir au moins 10 caractères.',
        ]);

        // Sanitisation anti-XSS
        $data = array_map(fn($v) => is_string($v) ? strip_tags(trim($v)) : $v, $data);

        try {
            Mail::raw(
                "Nouveau message de contact — ShopCI\n\n" .
                "Nom    : {$data['name']}\n" .
                "Email  : {$data['email']}\n" .
                "Tél    : " . ($data['phone'] ?? 'Non renseigné') . "\n" .
                "Sujet  : {$data['subject']}\n\n" .
                "Message :\n{$data['message']}",
                function ($mail) use ($data) {
                    $mail->to(config('mail.from.address'))
                         ->replyTo($data['email'], $data['name'])
                         ->subject('[ShopCI Contact] ' . $data['subject']);
                }
            );
        } catch (\Exception $e) {
            report($e);
            return back()->withInput()->with('error', 'L\'envoi a échoué. Veuillez réessayer ou nous contacter directement par email.');
        }

        return back()->with('success', 'Votre message a bien été envoyé ! Nous vous répondrons dans les 24h.');
    }
}
