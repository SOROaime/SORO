@extends('layouts.admin')

@section('title', 'Commande ' . $order->order_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Commandes</a></li>
    <li class="breadcrumb-item active">{{ $order->order_number }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 fw-bold mb-1">{{ $order->order_number }}</h1>
        <p class="text-muted mb-0">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
    </div>
    <span class="badge bg-{{ $order->status_color }} fs-6 px-3 py-2">{{ $order->status_label }}</span>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Articles --}}
        <div class="card stat-card mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="mb-0 fw-bold"><i class="bi bi-box me-2"></i>Articles</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Produit</th>
                                <th class="text-center">Qté</th>
                                <th class="text-end">P.U.</th>
                                <th class="text-end pe-4">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold">{{ $item->product_name }}</div>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ $item->formatted_unit_price }}</td>
                                    <td class="text-end pe-4 fw-bold">{{ $item->formatted_subtotal }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="3" class="text-end fw-bold ps-4">Total</td>
                                <td class="text-end pe-4 fw-bold text-primary fs-5">{{ $order->formatted_total }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Livraison --}}
        @if($order->shipping_address)
            <div class="card stat-card">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-truck me-2"></i>Livraison</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <p class="mb-1">{{ $order->shipping_address }}</p>
                    <p class="mb-0">{{ $order->shipping_postal_code }} {{ $order->shipping_city }}</p>
                    @if($order->notes)
                        <p class="text-muted small mt-2">Note : {{ $order->notes }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        {{-- Modifier statut --}}
        <div class="card stat-card mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Modifier le statut</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <select name="status" class="form-select">
                            @foreach(\App\Models\Order::STATUS_LABELS as $value => $label)
                                <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check me-1"></i>Mettre à jour
                    </button>
                </form>
            </div>
        </div>

        {{-- Client --}}
        <div class="card stat-card mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="mb-0 fw-bold"><i class="bi bi-person me-2"></i>Client</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="fw-semibold">{{ $order->user->name }}</div>
                <div class="text-muted small">{{ $order->user->email }}</div>
            </div>
        </div>

        {{-- Paiement --}}
        @if($order->payment)
            <div class="card stat-card">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2"></i>Paiement</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted small">Statut</span>
                        <span class="badge bg-{{ $order->payment->status_color }}">
                            {{ $order->payment->status_label }}
                        </span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted small">Montant</span>
                        <span class="fw-bold">{{ $order->payment->formatted_amount }}</span>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">Référence</div>
                        <code class="small">{{ $order->payment->transaction_reference }}</code>
                    </div>
                    @if($order->payment->card_last_four)
                        <div class="text-muted small">
                            Carte : •••• {{ $order->payment->card_last_four }}
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
