@extends('layouts.admin')
@section('title', 'Tranches de paiement')

@section('breadcrumb')
    <li class="breadcrumb-item active">Tranches de paiement</li>
@endsection

@section('content')

<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">
            <i class="bi bi-calendar-week me-2" style="color:#b45309;"></i>Tranches de paiement
        </h1>
        <p class="text-muted mb-0" style="font-size:.875rem;">
            Suivi de tous les paiements échelonnés
        </p>
    </div>
</div>

{{-- ── KPI Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:14px;background:#fef9c3;
                            display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;">
                    ⏳
                </div>
                <div>
                    <div class="fw-900" style="font-size:1.6rem;letter-spacing:-.04em;color:#b45309;">
                        {{ $stats['pending'] }}
                    </div>
                    <div class="text-muted" style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                        En attente
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:14px;background:#fee2e2;
                            display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;">
                    🔴
                </div>
                <div>
                    <div class="fw-900" style="font-size:1.6rem;letter-spacing:-.04em;color:#dc2626;">
                        {{ $stats['overdue'] }}
                    </div>
                    <div class="text-muted" style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                        En retard
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:14px;background:#dcfce7;
                            display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;">
                    ✅
                </div>
                <div>
                    <div class="fw-900" style="font-size:1.6rem;letter-spacing:-.04em;color:#16a34a;">
                        {{ $stats['paid'] }}
                    </div>
                    <div class="text-muted" style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                        Payées
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:14px;background:#dbeafe;
                            display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;">
                    💰
                </div>
                <div>
                    <div class="fw-900" style="font-size:1.2rem;letter-spacing:-.03em;color:#1d4ed8;">
                        {{ number_format($stats['total_pending_amount'], 0, ',', ' ') }} F
                    </div>
                    <div class="text-muted" style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                        Montant à encaisser
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Filtres ── --}}
<div class="card mb-4">
    <div class="card-body py-3 px-4">
        <form method="GET" class="d-flex gap-3 align-items-end flex-wrap">
            <div style="flex:1;min-width:200px;">
                <label class="form-label mb-1" style="font-size:.78rem;">Recherche (N° commande)</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="ORD-20260510-00001"
                       value="{{ request('search') }}">
            </div>
            <div style="min-width:160px;">
                <label class="form-label mb-1" style="font-size:.78rem;">Statut</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="paid"    {{ request('status') === 'paid'    ? 'selected' : '' }}>Payées</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i>Filtrer
                </button>
                <a href="{{ route('admin.installments.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x"></i>Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ── Tableau ── --}}
<div class="card">
    <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-700 d-flex align-items-center gap-2" style="font-size:.95rem;">
            <i class="bi bi-list-check" style="color:#b45309;"></i>
            Toutes les tranches
            <span style="background:#f1f5f9;color:#64748b;padding:.15em .55em;border-radius:20px;font-size:.72rem;font-weight:700;">
                {{ $installments->total() }}
            </span>
        </h5>
    </div>

    @if($installments->isEmpty())
    <div class="card-body text-center py-5">
        <div style="font-size:2.5rem;margin-bottom:1rem;">📅</div>
        <p class="text-muted fw-600 mb-0">Aucune tranche trouvée.</p>
    </div>
    @else
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Commande</th>
                    <th>Client</th>
                    <th>Tranche</th>
                    <th>Montant</th>
                    <th>Échéance</th>
                    <th>Statut</th>
                    <th>Payée le</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($installments as $inst)
                @php
                    $isOverdue = $inst->status === 'pending' && $inst->due_date->isPast();
                    $daysLeft  = now()->diffInDays($inst->due_date, false);
                @endphp
                <tr style="{{ $isOverdue ? 'background:#fff5f5;' : '' }}">

                    {{-- Commande --}}
                    <td>
                        <a href="{{ route('admin.orders.show', $inst->order) }}"
                           class="fw-700 text-decoration-none"
                           style="color:var(--primary);font-size:.85rem;">
                            {{ $inst->order->order_number }}
                        </a>
                    </td>

                    {{-- Client --}}
                    <td>
                        <div class="fw-600" style="font-size:.85rem;">{{ $inst->order->user->name }}</div>
                        <div class="text-muted" style="font-size:.74rem;">{{ $inst->order->user->email }}</div>
                    </td>

                    {{-- Numéro de tranche --}}
                    <td>
                        <span style="background:#f1f5f9;color:#475569;padding:.25em .7em;
                                     border-radius:20px;font-size:.78rem;font-weight:800;">
                            {{ $inst->installment_number }}/{{ $inst->order->installment_count }}
                        </span>
                    </td>

                    {{-- Montant --}}
                    <td class="fw-800" style="color:var(--primary);font-size:.9rem;">
                        {{ $inst->formatted_amount }}
                    </td>

                    {{-- Échéance --}}
                    <td>
                        <div class="fw-600" style="font-size:.85rem;
                            color:{{ $isOverdue ? '#dc2626' : ($daysLeft <= 7 && $inst->status === 'pending' ? '#b45309' : 'inherit') }};">
                            {{ $inst->due_date->format('d/m/Y') }}
                        </div>
                        @if($inst->status === 'pending')
                            @if($isOverdue)
                                <div style="font-size:.72rem;color:#dc2626;font-weight:700;">
                                    🔴 En retard de {{ abs($daysLeft) }} jour(s)
                                </div>
                            @elseif($daysLeft <= 7)
                                <div style="font-size:.72rem;color:#b45309;font-weight:700;">
                                    ⚠️ Dans {{ $daysLeft }} jour(s)
                                </div>
                            @else
                                <div style="font-size:.72rem;color:#64748b;">
                                    Dans {{ $daysLeft }} jour(s)
                                </div>
                            @endif
                        @endif
                    </td>

                    {{-- Statut --}}
                    <td>
                        @if($inst->status === 'paid')
                            <span class="status-pill" style="background:#dcfce7;color:#166534;">
                                <i class="bi bi-check-circle-fill"></i>Payée
                            </span>
                        @elseif($isOverdue)
                            <span class="status-pill" style="background:#fee2e2;color:#dc2626;">
                                <i class="bi bi-exclamation-circle-fill"></i>En retard
                            </span>
                        @else
                            <span class="status-pill" style="background:#fef9c3;color:#854d0e;">
                                <i class="bi bi-clock"></i>En attente
                            </span>
                        @endif
                    </td>

                    {{-- Date paiement --}}
                    <td style="font-size:.82rem;color:#64748b;">
                        {{ $inst->paid_at ? $inst->paid_at->format('d/m/Y H:i') : '—' }}
                    </td>

                    {{-- Action --}}
                    <td>
                        @if($inst->status !== 'paid')
                        <form action="{{ route('admin.installments.pay', [$inst->order, $inst]) }}"
                              method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="btn btn-sm fw-700"
                                    style="background:#16a34a;color:#fff;border-radius:8px;font-size:.75rem;white-space:nowrap;"
                                    onclick="return confirm('Marquer cette tranche comme payée ?')">
                                <i class="bi bi-check2"></i>Marquer payée
                            </button>
                        </form>
                        @else
                        <span style="font-size:.75rem;color:#94a3b8;">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($installments->hasPages())
    <div class="card-body border-top d-flex justify-content-center py-3">
        {{ $installments->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
