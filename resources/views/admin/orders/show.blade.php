@extends('layouts.admin')

@section('title', 'Commande ' . $order->order_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Commandes</a></li>
    <li class="breadcrumb-item active">{{ $order->order_number }}</li>
@endsection

@section('content')

{{-- En-tête --}}
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="fw-900 mb-1" style="font-size:1.8rem;letter-spacing:-.04em;">
            {{ $order->order_number }}
        </h1>
        <p class="text-muted mb-0" style="font-size:.88rem;">
            <i class="bi bi-calendar3 me-1"></i>
            {{ $order->created_at->format('d/m/Y à H:i') }}
        </p>
    </div>
    @php
        $sc = ['pending'=>'#f59e0b','paid'=>'#16a34a','processing'=>'#2563eb',
               'shipped'=>'#7c3aed','delivered'=>'#16a34a','cancelled'=>'#dc2626','refunded'=>'#64748b'];
        $sb = ['pending'=>'#fef9c3','paid'=>'#dcfce7','processing'=>'#dbeafe',
               'shipped'=>'#ede9fe','delivered'=>'#dcfce7','cancelled'=>'#fee2e2','refunded'=>'#f1f5f9'];
    @endphp
    <span style="padding:.5em 1.2em;border-radius:30px;font-size:.85rem;font-weight:800;
                 background:{{ $sb[$order->status] ?? '#f1f5f9' }};
                 color:{{ $sc[$order->status] ?? '#64748b' }};letter-spacing:.02em;">
        {{ $order->status_label }}
    </span>
</div>

<div class="row g-4">

    {{-- ═══ COLONNE GAUCHE ═══ --}}
    <div class="col-lg-8">

        {{-- Articles --}}
        <div class="card stat-card mb-4">
            <div class="card-header bg-white border-0 py-3 px-4" style="border-bottom:1px solid var(--border)!important;">
                <h5 class="mb-0 fw-700 d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:#dbeafe;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#2563eb;">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    Articles commandés
                    <span style="background:#f1f5f9;color:#64748b;padding:.15em .6em;border-radius:20px;font-size:.75rem;font-weight:700;">
                        {{ $order->items->count() }}
                    </span>
                </h5>
            </div>
            <div class="card-body p-0">
                @foreach($order->items as $item)
                <div class="d-flex align-items-center gap-3 px-4 py-3" style="border-bottom:1px solid var(--border);">
                    <div style="width:54px;height:54px;border-radius:12px;overflow:hidden;flex-shrink:0;background:#f1f5f9;">
                        @if($item->product)
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}"
                                 style="width:100%;height:100%;object-fit:cover;"
                                 onerror="this.src='https://placehold.co/54x54/e2e8f0/94a3b8?text=?'">
                        @else
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#94a3b8;">
                                <i class="bi bi-box"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-700" style="font-size:.9rem;">{{ $item->product_name }}</div>
                        <div class="text-muted" style="font-size:.78rem;">
                            Prix unitaire : <strong>{{ $item->formatted_unit_price }}</strong>
                        </div>
                    </div>
                    <div class="text-center" style="min-width:60px;">
                        <div style="background:#f1f5f9;border-radius:8px;padding:.3em .8em;font-size:.85rem;font-weight:700;color:#475569;">
                            × {{ $item->quantity }}
                        </div>
                    </div>
                    <div class="fw-800 text-end" style="color:var(--primary);min-width:80px;font-size:.95rem;">
                        {{ $item->formatted_subtotal }}
                    </div>
                </div>
                @endforeach

                {{-- Total --}}
                <div class="d-flex justify-content-between align-items-center px-4 py-3"
                     style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:0 0 16px 16px;">
                    <span class="fw-800" style="font-size:1rem;color:var(--dark);">Total de la commande</span>
                    <span class="fw-900" style="font-size:1.4rem;color:var(--primary);letter-spacing:-.04em;">
                        {{ $order->formatted_total }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Tranches de paiement --}}
        @if($order->hasInstallments())
        <div class="card stat-card mb-4">
            <div class="card-header bg-white border-0 py-3 px-4" style="border-bottom:1px solid var(--border)!important;">
                <h5 class="mb-0 fw-700 d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:#fef9c3;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#b45309;">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                    Paiement par tranches
                    <span style="background:#f1f5f9;color:#64748b;padding:.15em .6em;border-radius:20px;font-size:.75rem;font-weight:700;">
                        {{ $order->paid_installments_count }}/{{ $order->installment_count }}
                    </span>
                </h5>
            </div>
            <div class="card-body p-0">
                @foreach($order->installments as $inst)
                @php
                    $isOverdue = $inst->status === 'pending' && $inst->due_date->isPast();
                    $bg    = $inst->status === 'paid' ? '#dcfce7' : ($isOverdue ? '#fee2e2' : '#f8fafc');
                    $color = $inst->status === 'paid' ? '#16a34a' : ($isOverdue ? '#dc2626' : '#475569');
                    $icon  = $inst->status === 'paid' ? 'bi-check-circle-fill' : ($isOverdue ? 'bi-exclamation-circle-fill' : 'bi-clock');
                @endphp
                <div class="d-flex align-items-center justify-content-between px-4 py-3"
                     style="border-bottom:1px solid var(--border);background:{{ $bg }};">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi {{ $icon }}" style="color:{{ $color }};font-size:1.1rem;"></i>
                        <div>
                            <div class="fw-700" style="font-size:.88rem;color:{{ $color }};">
                                Tranche {{ $inst->installment_number }}/{{ $order->installment_count }}
                            </div>
                            <div style="font-size:.75rem;color:#64748b;">
                                @if($inst->status === 'paid')
                                    Payée le {{ $inst->paid_at->format('d/m/Y à H:i') }}
                                @elseif($isOverdue)
                                    En retard — échue le {{ $inst->due_date->format('d/m/Y') }}
                                @else
                                    Échéance : {{ $inst->due_date->format('d/m/Y') }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-900" style="color:{{ $color }};font-size:.95rem;">{{ $inst->formatted_amount }}</span>
                        @if($inst->status !== 'paid')
                        <form action="{{ route('admin.installments.pay', [$order, $inst]) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm fw-700"
                                    style="background:#16a34a;color:#fff;border-radius:8px;font-size:.78rem;"
                                    onclick="return confirm('Marquer cette tranche comme payée ?')">
                                <i class="bi bi-check2 me-1"></i>Marquer payée
                            </button>
                        </form>
                        @else
                        <span style="background:#dcfce7;color:#16a34a;padding:.25em .7em;border-radius:20px;font-size:.72rem;font-weight:800;">
                            <i class="bi bi-check-circle-fill me-1"></i>Payée
                        </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Livraison --}}
        @if($order->shipping_address)
        <div class="card stat-card">
            <div class="card-header bg-white border-0 py-3 px-4" style="border-bottom:1px solid var(--border)!important;">
                <h5 class="mb-0 fw-700 d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:#dcfce7;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#16a34a;">
                        <i class="bi bi-truck"></i>
                    </div>
                    Adresse de livraison
                </h5>
            </div>
            <div class="card-body px-4 py-4">
                <div class="d-flex gap-3">
                    <i class="bi bi-geo-alt-fill text-primary mt-1 flex-shrink-0" style="font-size:1.1rem;"></i>
                    <div>
                        <div class="fw-700 mb-1">{{ $order->shipping_address }}</div>
                        <div class="text-muted">
                            {{ $order->shipping_quartier }}{{ $order->shipping_quartier ? ', ' : '' }}{{ $order->shipping_commune }}{{ $order->shipping_commune ? ' — ' : '' }}{{ $order->shipping_city }}
                        </div>
                        @if($order->notes)
                            <div class="mt-2 p-2 rounded-2" style="background:#fef9c3;font-size:.82rem;color:#854d0e;">
                                <i class="bi bi-chat-quote me-1"></i>{{ $order->notes }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ═══ COLONNE DROITE ═══ --}}
    <div class="col-lg-4 d-flex flex-column gap-4">

        {{-- Modifier statut --}}
        <div class="card stat-card">
            <div class="card-header bg-white border-0 py-3 px-4" style="border-bottom:1px solid var(--border)!important;">
                <h5 class="mb-0 fw-700 d-flex align-items-center gap-2" style="font-size:.95rem;">
                    <i class="bi bi-pencil-square" style="color:var(--primary)"></i>Modifier le statut
                </h5>
            </div>
            <div class="card-body px-4 py-4">
                <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <select name="status" class="form-select fw-600">
                            @foreach(\App\Models\Order::STATUS_LABELS as $value => $label)
                                <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-700">
                        <i class="bi bi-check2 me-2"></i>Mettre à jour
                    </button>
                </form>
            </div>
        </div>

        {{-- Client --}}
        <div class="card stat-card">
            <div class="card-header bg-white border-0 py-3 px-4" style="border-bottom:1px solid var(--border)!important;">
                <h5 class="mb-0 fw-700 d-flex align-items-center gap-2" style="font-size:.95rem;">
                    <i class="bi bi-person-fill" style="color:#7c3aed"></i>Client
                </h5>
            </div>
            <div class="card-body px-4 py-4">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:50%;
                                background:linear-gradient(135deg,var(--primary),#7c3aed);
                                display:flex;align-items:center;justify-content:center;
                                font-size:1.1rem;font-weight:800;color:#fff;flex-shrink:0;">
                        {{ strtoupper(substr($order->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-700">{{ $order->user->name }}</div>
                        <div class="text-muted" style="font-size:.82rem;">{{ $order->user->email }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Paiement --}}
        @if($order->payment)
        <div class="card stat-card">
            <div class="card-header bg-white border-0 py-3 px-4" style="border-bottom:1px solid var(--border)!important;">
                <h5 class="mb-0 fw-700 d-flex align-items-center gap-2" style="font-size:.95rem;">
                    <i class="bi bi-credit-card-fill" style="color:#16a34a"></i>Paiement
                </h5>
            </div>
            <div class="card-body px-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted" style="font-size:.82rem;">Statut</span>
                    @php $ps = $order->payment->status; @endphp
                    <span style="padding:.25em .75em;border-radius:20px;font-size:.72rem;font-weight:800;
                                 background:{{ $ps === 'success' ? '#dcfce7' : '#fee2e2' }};
                                 color:{{ $ps === 'success' ? '#16a34a' : '#dc2626' }};">
                        {{ $order->payment->status_label }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted" style="font-size:.82rem;">Montant</span>
                    <span class="fw-800" style="color:var(--primary);">{{ $order->payment->formatted_amount }}</span>
                </div>
                <div class="mb-3">
                    <div class="text-muted mb-1" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;">Référence</div>
                    <code style="font-size:.78rem;background:#f8fafc;padding:.3em .6em;border-radius:6px;border:1px solid var(--border);">
                        {{ $order->payment->transaction_reference }}
                    </code>
                </div>
                @if($order->payment->card_last_four)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted" style="font-size:.82rem;">Carte</span>
                    <span class="fw-600" style="font-size:.85rem;font-family:monospace;">
                        •••• •••• •••• {{ $order->payment->card_last_four }}
                    </span>
                </div>
                @endif
                @if($order->payment->paid_at)
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size:.82rem;">Payé le</span>
                    <span class="fw-600" style="font-size:.82rem;">
                        {{ $order->payment->paid_at->format('d/m/Y H:i') }}
                    </span>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Retour --}}
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary fw-600">
            <i class="bi bi-arrow-left me-2"></i>Retour aux commandes
        </a>
    </div>
</div>
@endsection
