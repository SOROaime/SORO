@extends('layouts.app')

@section('title', 'Mes Commandes')

@section('content')
<div class="container py-5">
    <h1 class="section-title mb-4">
        <i class="bi bi-bag-check me-2"></i>Mes Commandes
    </h1>

    @if($orders->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-bag-x display-3 text-muted"></i>
            <h3 class="mt-3 text-muted">Aucune commande pour l'instant</h3>
            <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">
                <i class="bi bi-grid me-2"></i>Commencer mes achats
            </a>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($orders as $order)
                <div class="card border-0 rounded-4 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            {{-- Numéro & date --}}
                            <div class="col-md-3">
                                <div class="fw-bold text-primary">{{ $order->order_number }}</div>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $order->created_at->format('d/m/Y à H:i') }}
                                </div>
                            </div>

                            {{-- Articles --}}
                            <div class="col-md-3">
                                <div class="text-muted small mb-1">Articles</div>
                                <div class="fw-semibold">
                                    {{ $order->items->count() }} produit(s)
                                </div>
                            </div>

                            {{-- Total --}}
                            <div class="col-md-2">
                                <div class="text-muted small mb-1">Total</div>
                                <div class="fw-bold fs-5 text-primary">{{ $order->formatted_total }}</div>
                            </div>

                            {{-- Statut --}}
                            <div class="col-md-2">
                                <span class="badge bg-{{ $order->status_color }} status-badge fs-6">
                                    {{ $order->status_label }}
                                </span>
                            </div>

                            {{-- Actions --}}
                            <div class="col-md-2 text-end">
                                <a href="{{ route('orders.show', $order) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i>Détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
