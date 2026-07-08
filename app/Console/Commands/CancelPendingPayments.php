<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\PaymentSecurityLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Annule les commandes en attente depuis plus de 35 minutes et restaure le stock.
 * GeniusPay expire les paiements après 30 minutes → on laisse 5 min de marge.
 */
class CancelPendingPayments extends Command
{
    protected $signature   = 'payments:cancel-pending';
    protected $description = 'Annule les commandes GeniusPay en attente expirées et restaure le stock';

    public function handle(): void
    {
        $cutoff = now()->subMinutes(35);

        $orders = Order::where('status', 'pending')
            ->where('created_at', '<', $cutoff)
            ->whereHas('payment', fn($q) => $q->where('status', 'pending')
                ->where('payment_method', '!=', 'cash_on_delivery')
            )
            ->with('payment', 'items.product')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('Aucune commande en attente expirée.');
            return;
        }

        $count = 0;

        foreach ($orders as $order) {
            try {
                DB::transaction(function () use ($order) {
                    $order->restoreStock();
                    $order->update(['status' => 'cancelled']);
                    $order->payment->update([
                        'status'         => 'failed',
                        'failure_reason' => 'Paiement expiré — délai de 30 minutes dépassé.',
                    ]);
                });

                PaymentSecurityLog::log(PaymentSecurityLog::PAYMENT_FAILED, [
                    'reason'    => 'Paiement expiré (nettoyage automatique)',
                    'order'     => $order->order_number,
                    'reference' => $order->payment->transaction_reference,
                ], $order->user_id, $order->id);

                $count++;
                $this->line("  ✓ Commande {$order->order_number} annulée — stock restauré.");

            } catch (\Exception $e) {
                Log::error("payments:cancel-pending — erreur sur commande {$order->id}", [
                    'error' => $e->getMessage(),
                ]);
                $this->error("  ✗ Erreur sur commande {$order->order_number} : {$e->getMessage()}");
            }
        }

        $this->info("{$count} commande(s) annulée(s) et stock restauré.");
        Log::info("payments:cancel-pending — {$count} commande(s) traitée(s).");
    }
}
