<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $icons = [
            'pending'    => '⏳',
            'paid'       => '✅',
            'processing' => '📦',
            'shipped'    => '🚚',
            'delivered'  => '🎉',
            'cancelled'  => '❌',
            'refunded'   => '💰',
        ];

        $icon  = $icons[$this->order->status] ?? '📋';
        $label = $this->order->status_label;

        return (new MailMessage)
            ->subject("{$icon} Commande {$this->order->order_number} — {$label}")
            ->view('emails.order-status', ['order' => $this->order, 'icon' => $icon]);
    }
}
