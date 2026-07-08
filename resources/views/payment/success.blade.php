@extends('layouts.app')

@section('title', 'Paiement réussi')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">

            {{-- Icône succès animée --}}
            <div class="text-center mb-5">
                <div class="success-circle mx-auto mb-4">
                    @if(isset($cod) && $cod)
                        <i class="bi bi-truck"></i>
                    @else
                        <i class="bi bi-check-lg"></i>
                    @endif
                </div>
                @if(isset($cod) && $cod)
                    <h1 class="fw-900 mb-2" style="font-size:2rem;color:#16a34a;letter-spacing:-.04em;">
                        Commande confirmée !
                    </h1>
                    <p class="text-muted" style="font-size:1.05rem;">
                        Merci, <strong>{{ auth()->user()->name }}</strong> !<br>
                        Vous paierez <strong>{{ $order->formatted_total }}</strong> en espèces à la livraison.
                    </p>
                @else
                    <h1 class="fw-900 mb-2" style="font-size:2rem;color:#16a34a;letter-spacing:-.04em;">
                        Paiement réussi !
                    </h1>
                    <p class="text-muted" style="font-size:1.05rem;">
                        Merci pour votre commande, <strong>{{ auth()->user()->name }}</strong>.<br>
                        Vous recevrez une confirmation prochainement.
                    </p>
                @endif
            </div>

            {{-- Récapitulatif --}}
            <div class="card mb-4" style="border-radius:20px;border:1.5px solid var(--border);">
                <div class="card-header bg-white py-3 px-4"
                     style="border-bottom:1.5px solid var(--border);border-radius:20px 20px 0 0;">
                    <h5 class="fw-800 mb-0 d-flex align-items-center gap-2">
                        <div style="width:34px;height:34px;background:#dcfce7;border-radius:10px;
                                    display:flex;align-items:center;justify-content:center;color:#16a34a;">
                            <i class="bi bi-receipt"></i>
                        </div>
                        Récapitulatif de commande
                    </h5>
                </div>
                <div class="card-body p-4">

                    {{-- Infos clés --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:var(--light-bg);border:1px solid var(--border);">
                                <div class="text-muted mb-1" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">N° Commande</div>
                                <div class="fw-800" style="color:var(--primary);font-size:1rem;">
                                    {{ $order->order_number }}
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:var(--light-bg);border:1px solid var(--border);">
                                <div class="text-muted mb-1" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">Référence paiement</div>
                                <div style="font-size:.74rem;font-weight:600;font-family:monospace;word-break:break-all;">
                                    {{ $order->payment->transaction_reference }}
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:#dcfce7;border:1px solid #bbf7d0;">
                                <div class="mb-1" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;font-weight:600;color:#166534;">Montant payé</div>
                                <div class="fw-900" style="font-size:1.4rem;color:#16a34a;letter-spacing:-.04em;">
                                    {{ $order->payment->formatted_amount }}
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:var(--light-bg);border:1px solid var(--border);">
                                <div class="text-muted mb-1" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">Date & heure</div>
                                <div class="fw-600" style="font-size:.85rem;">
                                    {{ ($order->payment->paid_at ?? now())->format('d/m/Y') }}<br>
                                    <span class="text-muted" style="font-size:.78rem;">
                                        {{ ($order->payment->paid_at ?? now())->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="border-color:var(--border);">

                    <div class="text-muted mb-3" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;font-weight:700;">
                        Articles commandés
                    </div>
                    @foreach($order->items as $item)
                    <div class="d-flex justify-content-between align-items-center py-2"
                         style="border-bottom:1px solid var(--border);font-size:.88rem;">
                        <div>
                            <span class="fw-600">{{ $item->product_name }}</span>
                            <span class="text-muted ms-2" style="font-size:.82rem;">× {{ $item->quantity }}</span>
                        </div>
                        <span class="fw-800" style="color:var(--primary);">{{ $item->formatted_subtotal }}</span>
                    </div>
                    @endforeach

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-1">
                        <span class="fw-800" style="font-size:1rem;">Total</span>
                        <span class="fw-900" style="font-size:1.15rem;color:#16a34a;">
                            {{ $order->payment->formatted_amount }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('orders.show', $order) }}" class="btn btn-primary px-5 fw-700">
                    <i class="bi bi-eye me-2"></i>Voir la commande
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary px-4 fw-600">
                    <i class="bi bi-grid me-2"></i>Continuer mes achats
                </a>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
.success-circle {
    width: 96px; height: 96px;
    background: linear-gradient(135deg, #16a34a, #22c55e);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 2.8rem;
    box-shadow: 0 12px 40px rgba(22,163,74,.35);
    animation: popIn .65s cubic-bezier(.175,.885,.32,1.275) forwards;
}
@keyframes popIn {
    0%   { transform: scale(0); opacity: 0; }
    60%  { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}
</style>
@endpush
@endsection
