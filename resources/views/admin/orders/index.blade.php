@extends('layouts.admin')

@section('title', 'Commandes')

@section('breadcrumb')
    <li class="breadcrumb-item active">Commandes</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2 fw-bold mb-0">Gestion des commandes</h1>
    <div class="text-muted small">{{ $orders->total() }} commande(s)</div>
</div>

{{-- Filtres --}}
<div class="card stat-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.orders.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control"
                           placeholder="N° commande..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        @foreach(\App\Models\Order::STATUS_LABELS as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body p-0">
        @if($orders->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bag-x display-4"></i>
                <p class="mt-3">Aucune commande trouvée.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">N° Commande</th>
                            <th>Client</th>
                            <th>Articles</th>
                            <th>Total</th>
                            <th>Paiement</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">{{ $order->order_number }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td>{{ $order->items->count() }}</td>
                                <td class="fw-bold">{{ $order->formatted_total }}</td>
                                <td>
                                    @if($order->payment)
                                        <span class="badge bg-{{ $order->payment->status_color }}">
                                            {{ $order->payment->status_label }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $order->status_color }} status-badge">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $order->created_at->format('d/m/Y') }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i>Détails
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center p-4 border-top">
                <div class="text-muted small">{{ $orders->total() }} commande(s)</div>
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
