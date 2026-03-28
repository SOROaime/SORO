@extends('layouts.app')

@section('title', 'Paiement réussi')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 text-center">

            {{-- Icône succès avec animation --}}
            <div class="mb-4">
                <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center"
                     style="width:100px;height:100px;">
                    <i class="bi bi-check-lg text-white" style="font-size:3rem;"></i>
                </div>
            </div>

            <h1 class="fw-bold text-success mb-2">Paiement réussi !</h1>
            <p class="lead text-muted mb-4">
                Merci pour votre commande. Vous recevrez bientôt la confirmation.
            </p>

            <div class="card border-0 rounded-4 shadow-sm mb-4 text-start">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-receipt me-2"></i>Récapitulatif</h5>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="text-muted small">N° Commande</div>
                            <div class="fw-bold text-primary">{{ $order->order_number }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Référence paiement</div>
                            <code class="small">{{ $order->payment->transaction_reference }}</code>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Montant payé</div>
                            <div class="fw-bold fs-5 text-success">{{ $order->payment->formatted_amount }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Date</div>
                            <div>{{ $order->payment->paid_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="fw-semibold mb-3">Articles commandés</h6>
                    @foreach($order->items as $item)
                        <div class="d-flex justify-content-between mb-2 small">
                            <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                            <span class="fw-semibold">{{ $item->formatted_subtotal }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('orders.show', $order) }}" class="btn btn-primary px-4">
                    <i class="bi bi-eye me-2"></i>Voir la commande
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-grid me-2"></i>Continuer mes achats
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes popIn {
        0% { transform: scale(0); opacity: 0; }
        80% { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }
    .rounded-circle { animation: popIn .5s ease forwards; }
</style>
@endpush
@endsection
