@extends('layouts.app')

@section('title', 'Commande ' . $order->order_number)

@section('content')
<div class="container py-5">

    {{-- En-tête --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <div class="d-flex align-items-center gap-3 mb-1">
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h1 class="section-title mb-0">{{ $order->order_number }}</h1>
            </div>
            <p class="text-muted mb-0 ms-5 ps-2" style="font-size:.88rem;">
                <i class="bi bi-calendar3 me-1"></i>
                Passée le {{ $order->created_at->format('d/m/Y à H:i') }}
            </p>
        </div>
        @php
            $sc = ['pending'=>'#f59e0b','paid'=>'#16a34a','processing'=>'#2563eb',
                   'shipped'=>'#7c3aed','delivered'=>'#16a34a','cancelled'=>'#dc2626','refunded'=>'#64748b'];
            $sb = ['pending'=>'#fef9c3','paid'=>'#dcfce7','processing'=>'#dbeafe',
                   'shipped'=>'#ede9fe','delivered'=>'#dcfce7','cancelled'=>'#fee2e2','refunded'=>'#f1f5f9'];
        @endphp
        <span style="padding:.5em 1.3em;border-radius:30px;font-size:.85rem;font-weight:800;
                     background:{{ $sb[$order->status] ?? '#f1f5f9' }};
                     color:{{ $sc[$order->status] ?? '#64748b' }};letter-spacing:.02em;">
            {{ $order->status_label }}
        </span>
    </div>

    <div class="row g-4">

        {{-- ─── COLONNE GAUCHE ─── --}}
        <div class="col-lg-8">

            {{-- Articles --}}
            <div class="card mb-4" style="border-radius:16px;border:1.5px solid var(--border);">
                <div class="card-header bg-white py-3 px-4"
                     style="border-bottom:1.5px solid var(--border);border-radius:16px 16px 0 0;">
                    <h5 class="mb-0 fw-800 d-flex align-items-center gap-2">
                        <div style="width:32px;height:32px;background:#dbeafe;border-radius:9px;
                                    display:flex;align-items:center;justify-content:center;color:#2563eb;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        Articles commandés
                    </h5>
                </div>
                <div class="card-body p-0">
                    @foreach($order->items as $item)
                    <div class="d-flex align-items-center gap-3 px-4 py-3"
                         style="border-bottom:1px solid var(--border);">
                        <div style="width:60px;height:60px;border-radius:12px;overflow:hidden;flex-shrink:0;background:#f1f5f9;">
                            @if($item->product)
                                <img src="{{ $item->product->image_url ?? '' }}"
                                     alt="{{ $item->product_name }}"
                                     style="width:100%;height:100%;object-fit:cover;"
                                     onerror="this.src='https://placehold.co/60x60/e2e8f0/94a3b8?text=?'">
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:1.3rem;">
                                    <i class="bi bi-box"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-700 mb-1">{{ $item->product_name }}</div>
                            <div class="text-muted" style="font-size:.8rem;">
                                Prix unitaire : <strong style="color:var(--text);">{{ $item->formatted_unit_price }}</strong>
                            </div>
                        </div>
                        <div style="background:#f1f5f9;border-radius:8px;padding:.3em .9em;font-size:.85rem;font-weight:700;color:#475569;flex-shrink:0;">
                            × {{ $item->quantity }}
                        </div>
                        <div class="fw-900 text-end flex-shrink-0" style="color:var(--primary);font-size:1rem;min-width:80px;">
                            {{ $item->formatted_subtotal }}
                        </div>
                    </div>
                    @endforeach

                    {{-- Total --}}
                    <div class="d-flex justify-content-between align-items-center px-4 py-3"
                         style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:0 0 14px 14px;">
                        <div>
                            <div class="fw-800" style="font-size:1rem;color:var(--dark);">Total</div>
                            <div class="text-muted" style="font-size:.78rem;">
                                Livraison <span style="color:#16a34a;font-weight:700;">gratuite</span> incluse
                            </div>
                        </div>
                        <span class="fw-900" style="font-size:1.5rem;color:var(--primary);letter-spacing:-.04em;">
                            {{ $order->formatted_total }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Plan de tranches --}}
            @if($order->hasInstallments())
            @php
                $nextInstallment = $order->installments->where('status', 'pending')->sortBy('installment_number')->first();
            @endphp
            <div class="card mb-4" style="border-radius:16px;border:1.5px solid var(--border);">
                <div class="card-header bg-white py-3 px-4"
                     style="border-bottom:1.5px solid var(--border);border-radius:16px 16px 0 0;">
                    <h5 class="mb-0 fw-800 d-flex align-items-center gap-2">
                        <div style="width:32px;height:32px;background:#fef9c3;border-radius:9px;
                                    display:flex;align-items:center;justify-content:center;color:#b45309;">
                            <i class="bi bi-calendar-week"></i>
                        </div>
                        Paiement par tranches
                        <span style="background:#f1f5f9;color:#64748b;padding:.15em .6em;border-radius:20px;font-size:.75rem;font-weight:700;">
                            {{ $order->paid_installments_count }}/{{ $order->installment_count }} payée(s)
                        </span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @foreach($order->installments as $inst)
                    @php
                        $isOverdue  = $inst->status === 'pending' && $inst->due_date->isPast();
                        $isNext     = $nextInstallment && $inst->id === $nextInstallment->id;
                        $bg    = $inst->status === 'paid' ? '#dcfce7' : ($isOverdue ? '#fee2e2' : ($isNext ? '#fffbeb' : '#f8fafc'));
                        $color = $inst->status === 'paid' ? '#16a34a' : ($isOverdue ? '#dc2626' : '#475569');
                        $icon  = $inst->status === 'paid' ? 'bi-check-circle-fill' : ($isOverdue ? 'bi-exclamation-circle-fill' : 'bi-clock');
                    @endphp
                    <div style="border-bottom:1px solid var(--border);background:{{ $bg }};">
                        <div class="d-flex align-items-center justify-content-between px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi {{ $icon }}" style="color:{{ $color }};font-size:1.1rem;"></i>
                                <div>
                                    <div class="fw-700" style="font-size:.88rem;color:{{ $color }};">
                                        Tranche {{ $inst->installment_number }}/{{ $order->installment_count }}
                                        @if($isNext)
                                            <span style="background:#fef9c3;color:#b45309;font-size:.7rem;padding:.1em .5em;border-radius:10px;margin-left:.3em;">
                                                À payer
                                            </span>
                                        @endif
                                    </div>
                                    <div style="font-size:.75rem;color:#64748b;">
                                        @if($inst->status === 'paid')
                                            Payée le {{ $inst->paid_at->format('d/m/Y') }}
                                        @elseif($isOverdue)
                                            En retard — échue le {{ $inst->due_date->format('d/m/Y') }}
                                        @else
                                            À régler avant le {{ $inst->due_date->format('d/m/Y') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-900" style="color:{{ $color }};font-size:.95rem;">
                                    {{ $inst->formatted_amount }}
                                </span>
                            </div>
                        </div>

                        {{-- Bouton payer (tranche suivante uniquement) --}}
                        @if($isNext && $order->payment && $order->payment->payment_method !== 'cash_on_delivery')
                        <div class="px-4 pb-3">
                            <form action="{{ route('installment.pay', [$order, $inst]) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="btn btn-sm fw-700 w-100"
                                        style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border-radius:10px;padding:.5em 1em;"
                                        onclick="return confirm('Payer la tranche {{ $inst->installment_number }} de {{ $inst->formatted_amount }} via GeniusPay ?')">
                                    <i class="bi bi-credit-card me-2"></i>
                                    Payer {{ $inst->formatted_amount }} maintenant
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                {{-- Message si paiement à la livraison --}}
                @if($nextInstallment && $order->payment && $order->payment->payment_method === 'cash_on_delivery')
                <div class="px-4 py-3" style="background:#f0fdf4;border-top:1px solid var(--border);border-radius:0 0 14px 14px;">
                    <div style="font-size:.82rem;color:#166534;">
                        <i class="bi bi-info-circle me-1"></i>
                        Paiement à la livraison — le vendeur enregistrera vos tranches suivantes.
                    </div>
                </div>
                @endif
            </div>
            @endif

            {{-- Adresse de livraison --}}
            @if($order->shipping_address)
            <div class="card" style="border-radius:16px;border:1.5px solid var(--border);">
                <div class="card-header bg-white py-3 px-4"
                     style="border-bottom:1.5px solid var(--border);border-radius:16px 16px 0 0;">
                    <h5 class="mb-0 fw-800 d-flex align-items-center gap-2">
                        <div style="width:32px;height:32px;background:#dcfce7;border-radius:9px;
                                    display:flex;align-items:center;justify-content:center;color:#16a34a;">
                            <i class="bi bi-truck"></i>
                        </div>
                        Adresse de livraison
                    </h5>
                </div>
                <div class="card-body px-4 py-4">
                    <div class="d-flex gap-3">
                        <i class="bi bi-geo-alt-fill mt-1 flex-shrink-0" style="color:var(--primary);font-size:1.1rem;"></i>
                        <div>
                            <div class="fw-700 mb-1">{{ $order->shipping_address }}</div>
                            <div class="text-muted">
                                {{ $order->shipping_quartier }}{{ $order->shipping_quartier ? ', ' : '' }}{{ $order->shipping_commune }}{{ $order->shipping_commune ? ' — ' : '' }}{{ $order->shipping_city }}
                            </div>
                            @if($order->notes)
                                <div class="mt-2 p-2 rounded-2" style="background:#fef9c3;font-size:.82rem;color:#854d0e;">
                                    <i class="bi bi-chat-quote me-1"></i><em>{{ $order->notes }}</em>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- ─── COLONNE DROITE ─── --}}
        <div class="col-lg-4 d-flex flex-column gap-4">

            {{-- Paiement --}}
            @if($order->payment)
            <div class="card" style="border-radius:16px;border:1.5px solid var(--border);">
                <div class="card-header bg-white py-3 px-4"
                     style="border-bottom:1.5px solid var(--border);border-radius:16px 16px 0 0;">
                    <h5 class="mb-0 fw-800 d-flex align-items-center gap-2">
                        <div style="width:32px;height:32px;background:#dcfce7;border-radius:9px;
                                    display:flex;align-items:center;justify-content:center;color:#16a34a;">
                            <i class="bi bi-credit-card"></i>
                        </div>
                        Paiement
                    </h5>
                </div>
                <div class="card-body px-4 py-4">
                    @php
                        $ps = $order->payment->status;
                        $ref = $order->payment->transaction_reference ?? '';
                        $canVerify = $ps === 'pending'
                            && $order->payment->payment_method !== 'cash_on_delivery'
                            && $ref && !str_starts_with($ref, 'PENDING-');
                    @endphp
                    <div class="mb-3">
                        <span style="padding:.3em .9em;border-radius:20px;font-size:.78rem;font-weight:800;
                                     background:{{ $ps === 'success' ? '#dcfce7' : ($ps === 'pending' ? '#fef9c3' : '#fee2e2') }};
                                     color:{{ $ps === 'success' ? '#16a34a' : ($ps === 'pending' ? '#b45309' : '#dc2626') }};">
                            <i class="bi bi-{{ $ps === 'success' ? 'check-circle-fill' : ($ps === 'pending' ? 'hourglass-split' : 'x-circle-fill') }} me-1"></i>
                            {{ $order->payment->status_label }}
                        </span>
                    </div>

                    @if($canVerify)
                    <a href="{{ route('payment.callback', $order) }}?gp_result=success"
                       class="btn btn-sm fw-700 w-100 mb-3"
                       style="background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;border-radius:10px;">
                        <i class="bi bi-arrow-repeat me-2"></i>Vérifier mon paiement
                    </a>
                    @endif
                    <div class="d-flex justify-content-between mb-2" style="font-size:.88rem;">
                        <span class="text-muted">Référence</span>
                    </div>
                    <code style="font-size:.75rem;display:block;background:#f8fafc;padding:.4em .7em;
                                 border-radius:8px;border:1px solid var(--border);margin-bottom:.75rem;
                                 word-break:break-all;color:#475569;">
                        {{ $order->payment->transaction_reference }}
                    </code>
                    @if($order->payment->card_last_four)
                    <div class="d-flex justify-content-between mb-2" style="font-size:.85rem;">
                        <span class="text-muted">Carte</span>
                        <span class="fw-600" style="font-family:monospace;">
                            •••• •••• •••• {{ $order->payment->card_last_four }}
                        </span>
                    </div>
                    @endif
                    @if($order->payment->paid_at)
                    <div class="d-flex justify-content-between" style="font-size:.85rem;">
                        <span class="text-muted">Payé le</span>
                        <span class="fw-600">{{ $order->payment->paid_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Actions --}}
            <div class="card" style="border-radius:16px;border:1.5px solid var(--border);">
                <div class="card-body px-4 py-4 d-grid gap-2">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary fw-600">
                        <i class="bi bi-arrow-left me-2"></i>Mes commandes
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary fw-600">
                        <i class="bi bi-grid me-2"></i>Continuer mes achats
                    </a>
                    @if($order->canBeCancelled())
                        <form action="{{ route('orders.cancel', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100 fw-600"
                                    onclick="return confirm('Annuler cette commande ?')">
                                <i class="bi bi-x-circle me-2"></i>Annuler la commande
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
