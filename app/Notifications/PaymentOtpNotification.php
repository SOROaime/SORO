<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Couche 4 — Authentification renforcée (2FA)
 *
 * Envoie un code OTP à 6 chiffres par email pour confirmer
 * chaque intention de paiement. Valide 5 minutes, usage unique.
 */
class PaymentOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $otp,
        private string $orderTotal
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🔐 Code de confirmation de paiement — ShopCI')
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Vous avez initié un paiement de **{$this->orderTotal}** sur ShopCI.")
            ->line('Votre code de confirmation à usage unique est :')
            ->line("## {$this->otp}")
            ->line('**Ce code expire dans 5 minutes.** Ne le communiquez à personne.')
            ->line("Si vous n'avez pas initié ce paiement, ignorez cet email et contactez notre support immédiatement.")
            ->salutation("L'équipe ShopCI — Sécurité Paiements");
    }
}
