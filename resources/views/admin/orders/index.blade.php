@extends('layouts.admin')

@section('title', 'Commandes')

@section('breadcrumb')
    <li class="breadcrumb-item active">Commandes</li>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="fw-900 mb-1" style="font-size:1.8rem;letter-spacing:-.04em;">Gestion des commandes</h1>
        <p class="text-muted mb-0" style="font-size:.88rem;">
            <span class="fw-700" style="color:var(--primary);">{{ $orders->total() }}</span> commande(s) au total
        </p>
    </div>
</div>

{{-- Filtres --}}
<div class="card stat-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.orders.index') }}" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="N° commande ou client..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
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
                    <button type="submit" class="btn btn-primary w-100 fw-600">
                        <i class="bi bi-funnel me-1"></i>Filtrer
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body p-0">
        @if($orders->isEmpty())
            <div class="text-center py-5 text-muted">
                <div style="width:72px;height:72px;background:#f1f5f9;border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="bi bi-bag-x fs-2"></i>
                </div>
                <p class="mb-0 fw-600">Aucune commande trouvée.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:.875rem;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th class="ps-4 fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;padding:.85rem .75rem;">N° Commande</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Client</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Articles</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Total</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Paiement</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Statut</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Date</th>
                            <th class="fw-600 text-muted border-0 text-end pe-4" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        @php
                            $statusColors = ['pending'=>'#f59e0b','paid'=>'#16a34a','processing'=>'#2563eb',
                                             'shipped'=>'#7c3aed','delivered'=>'#16a34a','cancelled'=>'#dc2626','refunded'=>'#64748b'];
                            $statusBgs    = ['pending'=>'#fef9c3','paid'=>'#dcfce7','processing'=>'#dbeafe',
                                             'shipped'=>'#ede9fe','delivered'=>'#dcfce7','cancelled'=>'#fee2e2','refunded'=>'#f1f5f9'];
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="fw-700 text-decoration-none"
                                   style="color:var(--primary);">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:28px;height:28px;border-radius:50%;
                                                background:linear-gradient(135deg,var(--primary),var(--primary-d));
                                                display:flex;align-items:center;justify-content:center;
                                                font-size:.7rem;font-weight:800;color:#fff;flex-shrink:0;">
                                        {{ strtoupper(substr($order->user->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-600">{{ $order->user->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span style="background:#f1f5f9;color:var(--text-muted);padding:.2em .7em;border-radius:20px;font-size:.75rem;font-weight:700;">
                                    {{ $order->items->count() }} article(s)
                                </span>
                            </td>
                            <td class="fw-800" style="color:var(--primary);">{{ $order->formatted_total }}</td>
                            <td>
                                @if($order->payment)
                                    @php $ps = $order->payment->status; @endphp
                                    <span style="padding:.25em .75em;border-radius:20px;font-size:.72rem;font-weight:700;
                                                 background:{{ $ps === 'success' ? '#dcfce7' : '#fee2e2' }};
                                                 color:{{ $ps === 'success' ? '#16a34a' : '#dc2626' }};">
                                        {{ $order->payment->status_label }}
                                    </span>
                                @else
                                    <span style="color:var(--text-muted);font-size:.8rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <span style="padding:.25em .75em;border-radius:20px;font-size:.72rem;font-weight:700;
                                             background:{{ $statusBgs[$order->status] ?? '#f1f5f9' }};
                                             color:{{ $statusColors[$order->status] ?? '#64748b' }};">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="text-muted" style="font-size:.82rem;">
                                {{ $order->created_at->format('d/m/Y') }}<br>
                                <span style="font-size:.72rem;">{{ $order->created_at->format('H:i') }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="btn btn-sm"
                                   style="background:#dbeafe;border:none;color:var(--primary);width:32px;height:32px;padding:0;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;"
                                   title="Voir les détails">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center px-4 py-3"
                 style="border-top:1px solid var(--border);">
                <div class="text-muted" style="font-size:.82rem;">
                    {{ $orders->total() }} commande(s) — Page {{ $orders->currentPage() }} / {{ $orders->lastPage() }}
                </div>
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
