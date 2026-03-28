@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('breadcrumb')
    <li class="breadcrumb-item active">Tableau de bord</li>
@endsection

@section('content')

{{-- TITRE --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 fw-bold mb-1">Tableau de bord</h1>
        <p class="text-muted mb-0">Bienvenue, {{ auth()->user()->name }}</p>
    </div>
    <div class="text-muted small">
        <i class="bi bi-clock me-1"></i>{{ now()->format('d/m/Y H:i') }}
    </div>
</div>

{{-- ===== CARTES STATISTIQUES ===== --}}
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase mb-1">Produits actifs</div>
                        <div class="h2 fw-bold mb-0">{{ $stats['active_products'] }}</div>
                        <div class="text-muted small">/ {{ $stats['total_products'] }} total</div>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase mb-1">Commandes totales</div>
                        <div class="h2 fw-bold mb-0">{{ $stats['total_orders'] }}</div>
                        <div class="text-muted small">{{ $stats['pending_orders'] }} en attente</div>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-bag-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase mb-1">Revenus totaux</div>
                        <div class="h2 fw-bold mb-0">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} €</div>
                        <div class="text-muted small">Aujourd'hui : {{ number_format($stats['today_revenue'], 2, ',', ' ') }} €</div>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-currency-euro"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase mb-1">Utilisateurs</div>
                        <div class="h2 fw-bold mb-0">{{ $stats['total_users'] }}</div>
                        <div class="text-muted small">Clients enregistrés</div>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Dernières commandes --}}
    <div class="col-lg-8">
        <div class="card stat-card">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between">
                <h5 class="mb-0 fw-bold"><i class="bi bi-bag-check me-2"></i>Dernières commandes</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary btn-sm">Voir tout</a>
            </div>
            <div class="card-body p-0">
                @if($recentOrders->isEmpty())
                    <div class="text-center py-4 text-muted">Aucune commande.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">N° Commande</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td class="ps-4">
                                            <a href="{{ route('admin.orders.show', $order) }}"
                                               class="text-primary fw-semibold text-decoration-none">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td>{{ $order->user->name }}</td>
                                        <td class="fw-semibold">{{ $order->formatted_total }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status_color }} status-badge">
                                                {{ $order->status_label }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $order->created_at->format('d/m/Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Produits faible stock --}}
    <div class="col-lg-4">
        <div class="card stat-card">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between">
                <h5 class="mb-0 fw-bold"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Stock faible</h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-warning btn-sm">Gérer</a>
            </div>
            <div class="card-body p-0">
                @if($lowStockProducts->isEmpty())
                    <div class="text-center py-4 text-success">
                        <i class="bi bi-check-circle me-1"></i>Tous les stocks sont OK
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($lowStockProducts as $product)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold small">{{ Str::limit($product->name, 25) }}</div>
                                </div>
                                <span class="badge {{ $product->stock === 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                    {{ $product->stock }} restants
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Actions rapides --}}
        <div class="card stat-card mt-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="mb-0 fw-bold">Actions rapides</h5>
            </div>
            <div class="card-body p-3">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-2"></i>Nouveau produit
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
                       class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-clock me-2"></i>Commandes en attente ({{ $stats['pending_orders'] }})
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-credit-card me-2"></i>Voir les paiements
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
