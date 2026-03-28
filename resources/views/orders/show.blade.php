@extends('layouts.app')

@section('title', 'Commande ' . $order->order_number)

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="section-title mb-1">Commande #{{ $order->order_number }}</h1>
            <p class="text-muted mb-0">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <div>
            <span class="badge bg-{{ $order->status_color }} fs-6 px-3 py-2">
                {{ $order->status_label }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        {{-- Articles commandés --}}
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-box me-2"></i>Articles commandés</h5>
                </div>
                <div class="card-body p-0">
                    @foreach($order->items as $item)
                        <div class="d-flex align-items-center gap-3 p-4 border-top">
                            <img
                                src="{{ $item->product->image_url ?? 'https://placehold.co/70x70/e2e8f0/94a3b8?text=?' }}"
                                alt="{{ $item->product_name }}"
                                class="rounded-3"
                                style="width: 70px; height: 70px; object-fit: cover;"
                                onerror="this.src='https://placehold.co/70x70/e2e8f0/94a3b8?text=?'"
                            >
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $item->product_name }}</div>
                                <div class="text-muted small">
                                    Prix unitaire : {{ $item->formatted_unit_price }}
                                </div>
                            </div>
                            <div class="text-muted">× {{ $item->quantity }}</div>
                            <div class="fw-bold text-primary">{{ $item->formatted_subtotal }}</div>
                        </div>
                    @endforeach

                    <div class="p-4 border-top bg-light rounded-bottom-4">
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total</span>
                            <span class="text-primary">{{ $order->formatted_total }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Adresse livraison --}}
            @if($order->shipping_address)
                <div class="card border-0 rounded-4 shadow-sm">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-truck me-2"></i>Adresse de livraison</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <p class="mb-1">{{ $order->shipping_address }}</p>
                        <p class="mb-0">{{ $order->shipping_postal_code }} {{ $order->shipping_city }}</p>
                        @if($order->notes)
                            <p class="mt-2 text-muted small"><em>Note : {{ $order->notes }}</em></p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Paiement & actions --}}
        <div class="col-lg-4">
            {{-- Infos paiement --}}
            @if($order->payment)
                <div class="card border-0 rounded-4 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2"></i>Paiement</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="mb-2">
                            <span class="text-muted small">Statut :</span>
                            <span class="badge bg-{{ $order->payment->status_color }} ms-1">
                                {{ $order->payment->status_label }}
                            </span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted small">Référence :</span>
                            <code class="small ms-1">{{ $order->payment->transaction_reference }}</code>
                        </div>
                        @if($order->payment->card_last_four)
                            <div class="mb-2">
                                <span class="text-muted small">Carte :</span>
                                <span class="ms-1">•••• •••• •••• {{ $order->payment->card_last_four }}</span>
                            </div>
                        @endif
                        @if($order->payment->paid_at)
                            <div>
                                <span class="text-muted small">Payé le :</span>
                                <span class="ms-1 small">{{ $order->payment->paid_at->format('d/m/Y à H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body px-4 py-4">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="bi bi-arrow-left me-2"></i>Mes commandes
                    </a>
                    @if($order->canBeCancelled())
                        <form action="{{ route('orders.cancel', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Annuler cette commande ?')">
                                <i class="bi bi-x-circle me-2"></i>Annuler la commande
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
