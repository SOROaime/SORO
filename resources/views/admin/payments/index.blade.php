@extends('layouts.admin')

@section('title', 'Paiements')

@section('breadcrumb')
    <li class="breadcrumb-item active">Paiements</li>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="fw-900 mb-1" style="font-size:1.8rem;letter-spacing:-.04em;">Paiements</h1>
        <p class="text-muted mb-0" style="font-size:.88rem;">
            <span class="fw-700" style="color:var(--primary);">{{ $payments->total() }}</span> transaction(s) au total
        </p>
    </div>
</div>

{{-- Filtres --}}
<div class="card stat-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.payments.index') }}" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <select name="status" class="form-select fw-600">
                        <option value="">Tous les statuts</option>
                        @foreach(\App\Models\Payment::STATUS_LABELS as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-600">
                        <i class="bi bi-funnel me-1"></i>Filtrer
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body p-0">
        @if($payments->isEmpty())
            <div class="text-center py-5 text-muted">
                <div style="width:72px;height:72px;background:#f1f5f9;border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="bi bi-credit-card fs-2"></i>
                </div>
                <p class="mb-0 fw-600">Aucune transaction pour le moment.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:.875rem;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th class="ps-4 fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;padding:.85rem .75rem;">Référence</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Commande</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Client</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Montant</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Méthode</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Carte</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Statut</th>
                            <th class="fw-600 text-muted border-0 pe-4" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td class="ps-4">
                                <code style="font-size:.75rem;background:#f8fafc;padding:.2em .5em;border-radius:6px;border:1px solid var(--border);color:#475569;">
                                    {{ $payment->transaction_reference }}
                                </code>
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.show', $payment->order) }}"
                                   class="fw-700 text-decoration-none" style="color:var(--primary);">
                                    {{ $payment->order->order_number }}
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:28px;height:28px;border-radius:50%;
                                                background:linear-gradient(135deg,var(--primary),#7c3aed);
                                                display:flex;align-items:center;justify-content:center;
                                                font-size:.7rem;font-weight:800;color:#fff;flex-shrink:0;">
                                        {{ strtoupper(substr($payment->order->user->name,0,1)) }}
                                    </div>
                                    <span class="fw-600">{{ $payment->order->user->name }}</span>
                                </div>
                            </td>
                            <td class="fw-800" style="color:var(--primary);">{{ $payment->formatted_amount }}</td>
                            <td>
                                <span style="padding:.2em .65em;border-radius:20px;font-size:.72rem;font-weight:700;background:#f1f5f9;color:#475569;">
                                    {{ $payment->payment_method }}
                                </span>
                            </td>
                            <td class="text-muted" style="font-size:.82rem;font-family:monospace;">
                                @if($payment->card_last_four)
                                    •••• {{ $payment->card_last_four }}
                                @else
                                    <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>
                            <td>
                                @php $ps = $payment->status; @endphp
                                <span style="padding:.25em .75em;border-radius:20px;font-size:.72rem;font-weight:800;
                                             background:{{ $ps === 'success' ? '#dcfce7' : '#fee2e2' }};
                                             color:{{ $ps === 'success' ? '#16a34a' : '#dc2626' }};">
                                    <i class="bi bi-{{ $ps === 'success' ? 'check-circle' : 'x-circle' }} me-1"></i>
                                    {{ $payment->status_label }}
                                </span>
                            </td>
                            <td class="text-muted pe-4" style="font-size:.82rem;">
                                {{ $payment->created_at->format('d/m/Y') }}<br>
                                <span style="font-size:.72rem;">{{ $payment->created_at->format('H:i') }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3"
                 style="border-top:1px solid var(--border);">
                <div class="text-muted" style="font-size:.82rem;">
                    {{ $payments->total() }} transaction(s) — Page {{ $payments->currentPage() }} / {{ $payments->lastPage() }}
                </div>
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
