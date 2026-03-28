@extends('layouts.admin')

@section('title', 'Paiements')

@section('breadcrumb')
    <li class="breadcrumb-item active">Paiements</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2 fw-bold mb-0">Paiements</h1>
    <div class="text-muted small">{{ $payments->total() }} transaction(s)</div>
</div>

{{-- Filtres --}}
<div class="card stat-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.payments.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        @foreach(\App\Models\Payment::STATUS_LABELS as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body p-0">
        @if($payments->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-credit-card display-4"></i>
                <p class="mt-3">Aucun paiement.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Référence</th>
                            <th>Commande</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Méthode</th>
                            <th>Carte</th>
                            <th>Statut</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td class="ps-4">
                                    <code class="small">{{ $payment->transaction_reference }}</code>
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $payment->order) }}"
                                       class="text-primary text-decoration-none fw-semibold">
                                        {{ $payment->order->order_number }}
                                    </a>
                                </td>
                                <td>{{ $payment->order->user->name }}</td>
                                <td class="fw-bold">{{ $payment->formatted_amount }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $payment->payment_method }}</span>
                                </td>
                                <td class="text-muted small">
                                    @if($payment->card_last_four)
                                        •••• {{ $payment->card_last_four }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $payment->status_color }} status-badge">
                                        {{ $payment->status_label }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ $payment->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center p-4 border-top">
                <div class="text-muted small">{{ $payments->total() }} transaction(s)</div>
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
