<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstallmentScheduleNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $total = $this->order->installment_count;

        return (new MailMessage)
            ->subject("📅 Votre plan de paiement — {$this->order->order_number}")
            ->view('emails.installment-schedule', [
                'order' => $this->order,
                'total' => $total,
            ]);
    }
}
