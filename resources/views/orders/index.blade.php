@extends('layouts.app')

@section('title', 'Mes Commandes')

@section('content')
<div class="container py-5">

    {{-- En-tête --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="section-title mb-1">
                <i class="bi bi-bag-check me-2" style="color:var(--accent)"></i>Mes Commandes
            </h1>
            <p class="text-muted mb-0" style="font-size:.9rem;">
                Retrouvez l'historique de toutes vos commandes
            </p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm fw-600">
            <i class="bi bi-grid me-2"></i>Continuer mes achats
        </a>
    </div>

    @if($orders->isEmpty())
        {{-- État vide --}}
        <div class="text-center py-5">
            <div style="width:96px;height:96px;background:linear-gradient(135deg,var(--primary-l),#dbeafe);
                        border-radius:24px;display:flex;align-items:center;justify-content:center;
                        margin:0 auto 1.5rem;font-size:2.5rem;color:var(--primary);">
                <i class="bi bi-bag-x"></i>
            </div>
            <h3 class="fw-800 mb-2" style="font-size:1.4rem;color:var(--dark);">Aucune commande pour l'instant</h3>
            <p class="text-muted mb-4">Vous n'avez pas encore passé de commande.<br>Découvrez nos produits !</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg fw-700 px-5">
                <i class="bi bi-bag-plus me-2"></i>Commencer mes achats
            </a>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($orders as $order)
            @php
                $sc = ['pending'=>'#f59e0b','paid'=>'#16a34a','processing'=>'#2563eb',
                       'shipped'=>'#7c3aed','delivered'=>'#16a34a','cancelled'=>'#dc2626','refunded'=>'#64748b'];
                $sb = ['pending'=>'#fef9c3','paid'=>'#dcfce7','processing'=>'#dbeafe',
                       'shipped'=>'#ede9fe','delivered'=>'#dcfce7','cancelled'=>'#fee2e2','refunded'=>'#f1f5f9'];
            @endphp
            <div class="card" style="border-radius:16px;border:1.5px solid var(--border);transition:box-shadow .2s,transform .2s;"
                 onmouseenter="this.style.boxShadow='0 8px 32px rgba(37,99,235,.1)';this.style.transform='translateY(-2px)'"
                 onmouseleave="this.style.boxShadow='';this.style.transform=''">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">

                        {{-- Numéro & date --}}
                        <div class="col-md-3">
                            <div class="fw-800 mb-1" style="color:var(--primary);font-size:1rem;">
                                {{ $order->order_number }}
                            </div>
                            <div class="text-muted" style="font-size:.8rem;">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $order->created_at->format('d/m/Y à H:i') }}
                            </div>
                        </div>

                        {{-- Articles --}}
                        <div class="col-md-2">
                            <div class="text-muted mb-1" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;font-weight:600;">Articles</div>
                            <div class="fw-700" style="font-size:.9rem;">
                                <i class="bi bi-box me-1 text-muted"></i>
                                {{ $order->items->count() }} produit(s)
                            </div>
                        </div>

                        {{-- Total --}}
                        <div class="col-md-2">
                            <div class="text-muted mb-1" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;font-weight:600;">Total</div>
                            <div class="fw-900" style="font-size:1.15rem;color:var(--primary);letter-spacing:-.03em;">
                                {{ $order->formatted_total }}
                            </div>
                        </div>

                        {{-- Statut --}}
                        <div class="col-md-3 d-flex align-items-center">
                            <span style="padding:.4em 1.1em;border-radius:30px;font-size:.78rem;font-weight:800;
                                         background:{{ $sb[$order->status] ?? '#f1f5f9' }};
                                         color:{{ $sc[$order->status] ?? '#64748b' }};">
                                {{ $order->status_label }}
                            </span>
                        </div>

                        {{-- Action --}}
                        <div class="col-md-2 text-end">
                            <a href="{{ route('orders.show', $order) }}"
                               class="btn btn-primary btn-sm fw-600 px-3">
                                <i class="bi bi-eye me-1"></i>Détails
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
