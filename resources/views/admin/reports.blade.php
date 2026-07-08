@extends('layouts.admin')
@section('title', 'Rapports & Statistiques')

@section('breadcrumb')
    <li class="breadcrumb-item active">Rapports & Statistiques</li>
@endsection

@section('content')

{{-- ── EN-TÊTE ── --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 class="fw-900 mb-1" style="font-size:1.8rem;letter-spacing:-.04em;">
            <i class="bi bi-bar-chart-line me-2" style="color:var(--primary);"></i>Rapports & Statistiques
        </h1>
        <p class="text-muted mb-0" style="font-size:.88rem;">Analyse des ventes et performances produits</p>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        {{-- Filtre période --}}
        <form method="GET" id="periodForm" class="d-flex align-items-center gap-2">
            <select name="period" class="form-select form-select-sm fw-600" style="width:auto;"
                    onchange="document.getElementById('periodForm').submit()">
                @foreach([7=>'7 derniers jours', 30=>'30 derniers jours', 90=>'3 derniers mois', 365=>'Cette année'] as $val => $label)
                    <option value="{{ $val }}" {{ $period == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </form>
        {{-- Export CSV --}}
        <a href="{{ route('admin.reports.export', ['period' => $period]) }}"
           class="btn btn-success btn-sm fw-700">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exporter CSV
        </a>
        {{-- Imprimer / PDF --}}
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm fw-700">
            <i class="bi bi-printer me-1"></i>Imprimer
        </button>
    </div>
</div>

{{-- ── KPI CARDS ── --}}
<div class="row g-3 mb-4">
    @php
    $kpis = [
        ['label' => 'Revenus période',    'value' => number_format($periodStats['revenue'],0,',',' ').' FCFA',
         'icon' => 'bi-cash-coin',        'color' => '#16a34a', 'bg' => '#dcfce7'],
        ['label' => 'Commandes',          'value' => $periodStats['orders'],
         'icon' => 'bi-bag-check',        'color' => '#2563eb', 'bg' => '#dbeafe'],
        ['label' => 'Panier moyen',       'value' => number_format($periodStats['avg_order'],0,',',' ').' FCFA',
         'icon' => 'bi-cart3',            'color' => '#d97706', 'bg' => '#fef9c3'],
        ['label' => 'Nouveaux clients',   'value' => $periodStats['new_customers'],
         'icon' => 'bi-person-plus',      'color' => '#7c3aed', 'bg' => '#ede9fe'],
    ];
    @endphp
    @foreach($kpis as $k)
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 h-100" style="border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div style="width:52px;height:52px;border-radius:14px;background:{{ $k['bg'] }};
                            color:{{ $k['color'] }};font-size:1.4rem;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi {{ $k['icon'] }}"></i>
                </div>
                <div>
                    <div class="text-muted fw-600" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;">{{ $k['label'] }}</div>
                    <div class="fw-900" style="font-size:1.5rem;letter-spacing:-.04em;color:#0f172a;line-height:1.1;">{{ $k['value'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── LIGNE 1 : Évolution des revenus + Commandes par statut ── --}}
<div class="row g-4 mb-4">

    {{-- Graphique revenus --}}
    <div class="col-lg-8">
        <div class="card border-0 h-100" style="border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center"
                 style="border-bottom:1px solid #f1f5f9;border-radius:16px 16px 0 0;">
                <h6 class="mb-0 fw-800 d-flex align-items-center gap-2">
                    <div style="width:8px;height:8px;border-radius:50%;background:#2563eb;"></div>
                    Évolution des revenus
                </h6>
                <span class="text-muted" style="font-size:.75rem;">En FCFA</span>
            </div>
            <div class="card-body p-4">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- Commandes par statut --}}
    <div class="col-lg-4">
        <div class="card border-0 h-100" style="border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);">
            <div class="card-header bg-white py-3 px-4"
                 style="border-bottom:1px solid #f1f5f9;border-radius:16px 16px 0 0;">
                <h6 class="mb-0 fw-800 d-flex align-items-center gap-2">
                    <div style="width:8px;height:8px;border-radius:50%;background:#f59e0b;"></div>
                    Commandes par statut
                </h6>
            </div>
            <div class="card-body p-4 d-flex flex-column align-items-center">
                <canvas id="statusChart" style="max-height:200px;"></canvas>
                <div class="mt-3 w-100" id="statusLegend"></div>
            </div>
        </div>
    </div>
</div>

{{-- ── LIGNE 2 : Top produits + Paiements ── --}}
<div class="row g-4 mb-4">

    {{-- Top produits bar chart --}}
    <div class="col-lg-7">
        <div class="card border-0 h-100" style="border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);">
            <div class="card-header bg-white py-3 px-4"
                 style="border-bottom:1px solid #f1f5f9;border-radius:16px 16px 0 0;">
                <h6 class="mb-0 fw-800 d-flex align-items-center gap-2">
                    <div style="width:8px;height:8px;border-radius:50%;background:#16a34a;"></div>
                    Top 10 produits les plus vendus
                </h6>
            </div>
            <div class="card-body p-4">
                <canvas id="topProductsChart" height="160"></canvas>
            </div>
        </div>
    </div>

    {{-- Revenus par méthode de paiement --}}
    <div class="col-lg-5">
        <div class="card border-0 h-100" style="border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);">
            <div class="card-header bg-white py-3 px-4"
                 style="border-bottom:1px solid #f1f5f9;border-radius:16px 16px 0 0;">
                <h6 class="mb-0 fw-800 d-flex align-items-center gap-2">
                    <div style="width:8px;height:8px;border-radius:50%;background:#7c3aed;"></div>
                    Revenus par mode de paiement
                </h6>
            </div>
            <div class="card-body p-4">
                <canvas id="methodChart" height="160"></canvas>
                <div class="mt-3" id="methodDetails"></div>
            </div>
        </div>
    </div>
</div>

{{-- ── TABLEAU RAPPORT DE VENTES EXPORTABLE ── --}}
<div class="card border-0 mb-4" style="border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center"
         style="border-bottom:1px solid #f1f5f9;border-radius:16px 16px 0 0;">
        <h6 class="mb-0 fw-800 d-flex align-items-center gap-2">
            <div style="width:8px;height:8px;border-radius:50%;background:#0f172a;"></div>
            Rapport détaillé des ventes
            <span class="badge bg-primary ms-2" style="font-size:.65rem;">{{ $period }} jours</span>
        </h6>
        <a href="{{ route('admin.reports.export', ['period' => $period]) }}"
           class="btn btn-success btn-sm fw-700">
            <i class="bi bi-download me-1"></i>Télécharger CSV
        </a>
    </div>

    {{-- Résumé par jour --}}
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="salesTable">
            <thead style="background:#f8fafc;">
                <tr>
                    <th class="ps-4 py-3" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;">Date</th>
                    <th class="py-3" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;">Commandes</th>
                    <th class="py-3" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;">Revenus</th>
                    <th class="py-3 pe-4 text-end" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;">Revenu moyen/commande</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; $grandOrders = 0; @endphp
                @foreach($dailyRevenue as $day)
                @php
                    $grandTotal += $day['revenue'];
                    $grandOrders += $day['orders'];
                    $avg = $day['orders'] > 0 ? $day['revenue'] / $day['orders'] : 0;
                @endphp
                @if($day['orders'] > 0 || $day['revenue'] > 0)
                <tr>
                    <td class="ps-4 fw-600" style="font-size:.85rem;">{{ $day['date'] }}</td>
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:6px;font-size:.85rem;">
                            <i class="bi bi-bag-check" style="color:#2563eb;"></i>
                            {{ $day['orders'] }} commande{{ $day['orders'] > 1 ? 's' : '' }}
                        </span>
                    </td>
                    <td class="fw-800" style="color:#16a34a;font-size:.9rem;">
                        {{ number_format($day['revenue'],0,',',' ') }} FCFA
                    </td>
                    <td class="pe-4 text-end text-muted" style="font-size:.82rem;">
                        {{ $day['orders'] > 0 ? number_format($avg,0,',',' ').' FCFA' : '—' }}
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
            <tfoot style="background:#f0fdf4;border-top:2px solid #bbf7d0;">
                <tr>
                    <td class="ps-4 fw-800 py-3">TOTAL</td>
                    <td class="fw-800">{{ $grandOrders }} commande{{ $grandOrders > 1 ? 's' : '' }}</td>
                    <td class="fw-900" style="color:#16a34a;font-size:1rem;">{{ number_format($grandTotal,0,',',' ') }} FCFA</td>
                    <td class="pe-4 text-end fw-700 text-muted">
                        {{ $grandOrders > 0 ? number_format($grandTotal/$grandOrders,0,',',' ').' FCFA' : '—' }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- ── TABLEAU STATISTIQUES PRODUITS ── --}}
<div class="card border-0 mb-4" style="border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);">
    <div class="card-header bg-white py-3 px-4"
         style="border-bottom:1px solid #f1f5f9;border-radius:16px 16px 0 0;">
        <h6 class="mb-0 fw-800 d-flex align-items-center gap-2">
            <div style="width:8px;height:8px;border-radius:50%;background:#16a34a;"></div>
            Statistiques produits
        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead style="background:#f8fafc;">
                <tr>
                    <th class="ps-4 py-3" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;">#</th>
                    <th class="py-3" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;">Produit</th>
                    <th class="py-3" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;">Qté vendue</th>
                    <th class="py-3" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;">CA généré</th>
                    <th class="py-3 pe-4" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;">Part des ventes</th>
                </tr>
            </thead>
            <tbody>
                @php $maxQty = $topProducts->max('total_qty') ?: 1; $totalRev = $topProducts->sum('total_revenue') ?: 1; @endphp
                @forelse($topProducts as $i => $p)
                <tr>
                    <td class="ps-4 text-muted fw-600" style="font-size:.85rem;">{{ $i+1 }}</td>
                    <td class="fw-700" style="font-size:.88rem;">{{ $p->product_name }}</td>
                    <td>
                        <span class="fw-800" style="color:#2563eb;">{{ $p->total_qty }}</span>
                        <span class="text-muted ms-1" style="font-size:.78rem;">unités</span>
                    </td>
                    <td class="fw-700" style="color:#16a34a;">{{ number_format($p->total_revenue,0,',',' ') }} FCFA</td>
                    <td class="pe-4" style="min-width:160px;">
                        @php $pct = round(($p->total_revenue / $totalRev) * 100, 1); @endphp
                        <div class="d-flex align-items-center gap-2">
                            <div style="flex:1;height:8px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
                                <div style="width:{{ $pct }}%;height:100%;background:linear-gradient(90deg,#2563eb,#7c3aed);border-radius:4px;"></div>
                            </div>
                            <span class="fw-700" style="font-size:.78rem;min-width:36px;">{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-bar-chart fs-2 d-block mb-2"></i>
                        Aucune vente sur cette période.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('styles')
<style>
    @media print {
        .sidebar, .topbar, .btn, select, form { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        body { background: #fff !important; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#64748b';

// ── Données PHP → JS ─────────────────────────────────────────────
const dailyLabels  = @json(array_column($dailyRevenue, 'date'));
const dailyRevs    = @json(array_column($dailyRevenue, 'revenue'));
const dailyOrders  = @json(array_column($dailyRevenue, 'orders'));

@php
    $jsStatusLabels = $ordersByStatus->pluck('status')->map(fn($s) => \App\Models\Order::STATUS_LABELS[$s] ?? $s)->values();
    $jsMethodNames  = $revenueByMethod->pluck('payment_method')->map(function($m) {
        if ($m === 'cash_on_delivery') return 'Paiement livraison';
        if ($m === 'geniuspay')        return 'GeniusPay';
        return $m;
    })->values();
    $jsTopLabels = $topProducts->pluck('product_name')->map(fn($n) => strlen($n) > 22 ? substr($n, 0, 22).'…' : $n)->values();
@endphp

const statusLabels = @json($jsStatusLabels);
const statusCounts = @json($ordersByStatus->pluck('total'));

const methodNames  = @json($jsMethodNames);
const methodTotals = @json($revenueByMethod->pluck('total'));

const topLabels    = @json($jsTopLabels);
const topQtys      = @json($topProducts->pluck('total_qty'));

// ── Palette ──────────────────────────────────────────────────────
const palette = ['#2563eb','#16a34a','#f59e0b','#7c3aed','#dc2626','#0891b2','#db2777','#65a30d','#ea580c','#0d9488'];

// ── 1. Graphique revenus (line) ───────────────────────────────────
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: 'Revenus (FCFA)',
            data: dailyRevs,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,.08)',
            tension: .4,
            fill: true,
            pointBackgroundColor: '#2563eb',
            pointRadius: 3,
            pointHoverRadius: 6,
        }, {
            label: 'Commandes',
            data: dailyOrders,
            borderColor: '#f59e0b',
            backgroundColor: 'transparent',
            tension: .4,
            borderDash: [5,4],
            yAxisID: 'y2',
            pointBackgroundColor: '#f59e0b',
            pointRadius: 3,
        }]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { position: 'top' } },
        scales: {
            y:  { beginAtZero: true, grid: { color: '#f1f5f9' },
                  ticks: { callback: v => v.toLocaleString('fr-FR') + ' F' } },
            y2: { position: 'right', beginAtZero: true, grid: { display: false },
                  ticks: { stepSize: 1 } },
        }
    }
});

// ── 2. Commandes par statut (donut) ──────────────────────────────
const statusChart = new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{ data: statusCounts, backgroundColor: palette, borderWidth: 2, borderColor: '#fff' }]
    },
    options: {
        cutout: '65%',
        plugins: { legend: { display: false } }
    }
});
// Légende personnalisée
const legendEl = document.getElementById('statusLegend');
statusLabels.forEach((l, i) => {
    legendEl.innerHTML += `<div class="d-flex justify-content-between align-items-center mb-1" style="font-size:.78rem;">
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${palette[i]};margin-right:6px;"></span>${l}</span>
        <strong>${statusCounts[i]}</strong></div>`;
});

// ── 3. Top produits (bar horizontal) ─────────────────────────────
new Chart(document.getElementById('topProductsChart'), {
    type: 'bar',
    data: {
        labels: topLabels,
        datasets: [{
            label: 'Quantité vendue',
            data: topQtys,
            backgroundColor: topQtys.map((_, i) => palette[i % palette.length]),
            borderRadius: 6,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { stepSize: 1 } },
            y: { grid: { display: false } }
        }
    }
});

// ── 4. Revenus par méthode (bar vertical) ────────────────────────
new Chart(document.getElementById('methodChart'), {
    type: 'bar',
    data: {
        labels: methodNames,
        datasets: [{
            label: 'Revenus (FCFA)',
            data: methodTotals,
            backgroundColor: ['#2563eb','#16a34a','#f59e0b','#7c3aed'],
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' },
                 ticks: { callback: v => v.toLocaleString('fr-FR') + ' F' } },
            x: { grid: { display: false } }
        }
    }
});

// Détails paiements texte
const detailsEl = document.getElementById('methodDetails');
methodNames.forEach((m, i) => {
    detailsEl.innerHTML += `<div class="d-flex justify-content-between align-items-center mb-2" style="font-size:.8rem;">
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:${['#2563eb','#16a34a','#f59e0b','#7c3aed'][i]};margin-right:6px;"></span>${m}</span>
        <strong>${Number(methodTotals[i]).toLocaleString('fr-FR')} FCFA</strong></div>`;
});
</script>
@endpush
