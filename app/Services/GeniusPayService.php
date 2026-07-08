<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class GeniusPayService
{
    private string $baseUrl;
    private string $apiKey;
    private string $apiSecret;
    private string $webhookSecret;

    public function __construct()
    {
        $this->baseUrl       = config('services.geniuspay.base_url');
        $this->apiKey        = config('services.geniuspay.key');
        $this->apiSecret     = config('services.geniuspay.secret');
        $this->webhookSecret = config('services.geniuspay.webhook_secret');
    }

    private function headers(): array
    {
        return [
            'X-API-Key'    => $this->apiKey,
            'X-API-Secret' => $this->apiSecret,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];
    }

    /**
     * Crée un paiement GeniusPay et retourne les données (checkout_url + reference).
     */
    public function createPayment(Order $order): array
    {
        $customer = [
            'name'    => $order->user->name,
            'email'   => $order->user->email,
            'country' => 'CI',
        ];

        // Ajouter le téléphone si disponible (requis pour déclencher la confirmation mobile)
        $phone = $order->shipping_phone ?? $order->user->phone ?? null;
        if ($phone) {
            $customer['phone'] = $phone;
        }

        $response = Http::withHeaders($this->headers())
            ->timeout(30)
            ->post($this->baseUrl . '/payments', [
                'amount'      => (int) ($order->payment->amount ?? $order->total_amount),
                'currency'    => 'XOF',
                'description' => "Commande {$order->order_number} — ShopCI",
                'customer'    => $customer,
                'metadata'    => [
                    'order_id'     => $order->id,
                    'order_number' => $order->order_number,
                ],
                'success_url' => route('payment.callback', $order) . '?gp_result=success',
                'error_url'   => route('payment.callback', $order) . '?gp_result=error',
            ]);

        \Illuminate\Support\Facades\Log::info('GeniusPay createPayment', [
            'status' => $response->status(),
            'body'   => $response->json(),
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'GeniusPay — impossible de créer le paiement : ' . $response->body()
            );
        }

        return $response->json('data') ?? $response->json();
    }

    /**
     * Crée un paiement GeniusPay pour une tranche spécifique.
     */
    public function createInstallmentPayment(Order $order, \App\Models\Installment $installment): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout(30)
            ->post($this->baseUrl . '/payments', [
                'amount'      => (int) $installment->amount,
                'currency'    => 'XOF',
                'description' => "Tranche {$installment->installment_number}/{$order->installment_count} — Commande {$order->order_number}",
                'customer'    => [
                    'name'    => $order->user->name,
                    'email'   => $order->user->email,
                    'country' => 'CI',
                ],
                'metadata'    => [
                    'order_id'           => $order->id,
                    'installment_id'     => $installment->id,
                    'installment_number' => $installment->installment_number,
                ],
                'success_url' => route('installment.callback', [$order, $installment]) . '?gp_result=success',
                'error_url'   => route('installment.callback', [$order, $installment]) . '?gp_result=error',
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'GeniusPay — impossible de créer le paiement de tranche : ' . $response->body()
            );
        }

        return $response->json('data');
    }

    /**
     * Récupère le statut d'un paiement via sa référence GeniusPay.
     */
    public function getPayment(string $reference): array
    {
        $response = Http::withHeaders($this->headers())
            ->timeout(15)
            ->get($this->baseUrl . '/payments/' . $reference);

        // 404 = GeniusPay ne connaît plus cette transaction (paiement abandonné ou échoué en sandbox)
        if ($response->status() === 404) {
            return ['status' => 'failed', 'failure_reason' => 'Transaction introuvable (paiement non complété).'];
        }

        if (!$response->successful()) {
            throw new \RuntimeException('GeniusPay — erreur API : ' . $response->body());
        }

        return $response->json('data') ?? [];
    }

    /**
     * Vérifie la signature HMAC-SHA256 du webhook GeniusPay.
     * Algorithme : HMAC-SHA256(timestamp + "." + raw_json_body, webhook_secret)
     */
    public function verifyWebhookSignature(string $signature, string $timestamp, string $rawPayload): bool
    {
        $data     = $timestamp . '.' . $rawPayload;
        $expected = hash_hmac('sha256', $data, $this->webhookSecret);
        return hash_equals($expected, $signature);
    }
}
