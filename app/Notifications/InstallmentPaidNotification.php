<?php

namespace App\Notifications;

use App\Models\Installment;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstallmentPaidNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public Installment $installment) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $paidCount = $this->order->installments->where('status', 'paid')->count();
        $total     = $this->order->installment_count;
        $remaining = $total - $paidCount;

        return (new MailMessage)
            ->subject("✅ Tranche {$this->installment->installment_number}/{$total} reçue — {$this->order->order_number}")
            ->view('emails.installment-paid', [
                'order'       => $this->order,
                'installment' => $this->installment,
                'paidCount'   => $paidCount,
                'total'       => $total,
                'remaining'   => $remaining,
            ]);
    }
}
