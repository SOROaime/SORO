@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('breadcrumb')
    <li class="breadcrumb-item active">Tableau de bord</li>
@endsection

@section('content')

{{-- TITRE --}}
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="fw-900 mb-1" style="font-size:1.8rem;letter-spacing:-.04em;">Tableau de bord</h1>
        <p class="text-muted mb-0" style="font-size:.88rem;">
            Bonjour, <strong style="color:var(--dark);">{{ auth()->user()->name }}</strong> —
            <span class="live-dot d-inline-block mx-1"></span>
            {{ now()->format('d/m/Y \à H:i') }}
        </p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary fw-600">
        <i class="bi bi-plus-lg me-2"></i>Nouveau produit
    </a>
</div>

{{-- ═══ CARTES STATS ═══ --}}
<div class="row g-4 mb-5">
    @php $statCards = [
        ['label'=>'Produits actifs',  'value'=> $stats['active_products'],
         'sub'  => $stats['total_products'].' au total',
         'icon' =>'bi-box-seam',      'color'=>'#2563eb', 'bg'=>'#dbeafe', 'trend'=>'+'],
        ['label'=>'Commandes',        'value'=> $stats['total_orders'],
         'sub'  => $stats['pending_orders'].' en attente',
         'icon' =>'bi-bag-check',     'color'=>'#f59e0b', 'bg'=>'#fef9c3', 'trend'=>'!'],
        ['label'=>'Revenus totaux',   'value'=> number_format($stats['total_revenue'],0,',',' ').' €',
         'sub'  => "Aujourd'hui : ".number_format($stats['today_revenue'],2,',',' ').' €',
         'icon' =>'bi-currency-euro', 'color'=>'#16a34a', 'bg'=>'#dcfce7', 'trend'=>'+'],
        ['label'=>'Utilisateurs',     'value'=> $stats['total_users'],
         'sub'  => 'Clients enregistrés',
         'icon' =>'bi-people',        'color'=>'#7c3aed', 'bg'=>'#ede9fe', 'trend'=>'+'],
    ]; @endphp

    @foreach($statCards as $card)
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100" style="transition:transform .2s,box-shadow .2s;"
             onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 28px rgba(0,0,0,.1)'"
             onmouseleave="this.style.transform='';this.style.boxShadow=''">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="text-muted fw-600 mb-2"
                             style="font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;">
                            {{ $card['label'] }}
                        </div>
                        <div class="fw-900 mb-1"
                             style="font-size:1.85rem;letter-spacing:-.05em;color:var(--dark);line-height:1;">
                            {{ $card['value'] }}
                        </div>
                        <div class="text-muted" style="font-size:.75rem;">{{ $card['sub'] }}</div>
                    </div>
                    <div style="width:48px;height:48px;border-radius:14px;
                                background:{{ $card['bg'] }};color:{{ $card['color'] }};
                                font-size:1.3rem;display:flex;align-items:center;justify-content:center;
                                flex-shrink:0;">
                        <i class="bi {{ $card['icon'] }}"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">

    {{-- ═══ DERNIÈRES COMMANDES ═══ --}}
    <div class="col-lg-8">
        <div class="card stat-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center py-3 px-4"
                 style="background:#fff;border-bottom:1px solid var(--border);">
                <h5 class="mb-0 fw-700 d-flex align-items-center gap-2">
                    <i class="bi bi-bag-check" style="color:var(--primary)"></i>
                    Dernières commandes
                </h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary btn-sm">
                    Voir tout <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentOrders->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bag fs-2 d-block mb-2"></i>
                        Aucune commande pour le moment.
                    </div>
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
                                @php
                                    $sc = ['pending'=>'#f59e0b','paid'=>'#16a34a','processing'=>'#2563eb',
                                           'shipped'=>'#7c3aed','delivered'=>'#16a34a','cancelled'=>'#dc2626'];
                                    $sb = ['pending'=>'#fef9c3','paid'=>'#dcfce7','processing'=>'#dbeafe',
                                           'shipped'=>'#ede9fe','delivered'=>'#dcfce7','cancelled'=>'#fee2e2'];
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                           class="fw-700 text-decoration-none"
                                           style="color:var(--primary);">{{ $order->order_number }}</a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:28px;height:28px;border-radius:50%;
                                                        background:linear-gradient(135deg,var(--primary),var(--primary-d));
                                                        display:flex;align-items:center;justify-content:center;
                                                        font-size:.68rem;font-weight:800;color:#fff;flex-shrink:0;">
                                                {{ strtoupper(substr($order->user->name,0,1)) }}
                                            </div>
                                            <span>{{ $order->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="fw-700" style="color:var(--primary);">{{ $order->formatted_total }}</td>
                                    <td>
                                        <span style="padding:.25em .8em;border-radius:20px;font-size:.72rem;font-weight:700;
                                                     background:{{ $sb[$order->status] ?? '#f1f5f9' }};
                                                     color:{{ $sc[$order->status] ?? '#64748b' }};">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-muted" style="font-size:.82rem;">
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

    {{-- ═══ COLONNE DROITE ═══ --}}
    <div class="col-lg-4 d-flex flex-column gap-4">

        {{-- Alertes stock --}}
        <div class="card stat-card">
            <div class="card-header d-flex justify-content-between align-items-center py-3 px-4"
                 style="background:#fff;border-bottom:1px solid var(--border);">
                <h5 class="mb-0 fw-700 d-flex align-items-center gap-2" style="font-size:.9rem;">
                    <i class="bi bi-exclamation-triangle-fill" style="color:#f59e0b"></i>
                    Alertes stock
                </h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm fw-600"
                   style="background:#fef9c3;color:#854d0e;border:none;font-size:.72rem;">
                    Gérer
                </a>
            </div>
            <div class="card-body p-0">
                @if($lowStockProducts->isEmpty())
                    <div class="text-center py-4" style="color:#16a34a;font-size:.85rem;">
                        <i class="bi bi-check-circle-fill d-block fs-3 mb-2"></i>
                        Tous les stocks sont OK !
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($lowStockProducts as $p)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $p->image_url }}" alt=""
                                     style="width:32px;height:32px;border-radius:8px;object-fit:cover;border:1px solid var(--border);"
                                     onerror="this.src='https://placehold.co/32x32/f1f5f9/94a3b8?text=?'">
                                <span class="fw-600" style="font-size:.82rem;">{{ Str::limit($p->name,22) }}</span>
                            </div>
                            <span style="padding:.2em .65em;border-radius:20px;font-size:.68rem;font-weight:800;
                                         background:{{ $p->stock===0 ? '#fee2e2' : '#fef9c3' }};
                                         color:{{ $p->stock===0 ? '#dc2626' : '#854d0e' }};">
                                {{ $p->stock === 0 ? 'Rupture' : $p->stock.' restant'.($p->stock>1?'s':'') }}
                            </span>
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Actions rapides --}}
        <div class="card stat-card">
            <div class="card-header py-3 px-4"
                 style="background:#fff;border-bottom:1px solid var(--border);">
                <h5 class="mb-0 fw-700 d-flex align-items-center gap-2" style="font-size:.9rem;">
                    <i class="bi bi-lightning-charge-fill" style="color:var(--accent)"></i>
                    Actions rapides
                </h5>
            </div>
            <div class="card-body p-3 d-grid gap-2">
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm fw-600">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau produit
                </a>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm fw-600"
                   style="background:#fef9c3;color:#854d0e;border:1px solid #fde68a;">
                    <i class="bi bi-clock me-2"></i>En attente ({{ $stats['pending_orders'] }})
                </a>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm fw-600">
                    <i class="bi bi-credit-card me-2"></i>Voir les paiements
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm fw-600">
                    <i class="bi bi-people me-2"></i>Gérer les utilisateurs
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm fw-600" target="_blank">
                    <i class="bi bi-shop me-2"></i>Voir la boutique
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
