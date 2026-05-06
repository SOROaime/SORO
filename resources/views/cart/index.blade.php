@extends('layouts.app')

@section('title', 'Mon Panier')

@section('content')

{{-- ─── EN-TÊTE ─── --}}
<div style="background:#fff;border-bottom:1px solid var(--border);">
    <div class="container py-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                    <li class="breadcrumb-item active">Mon panier</li>
                </ol></nav>
                <h1 class="section-title mb-0">
                    <i class="bi bi-bag me-2" style="color:var(--accent)"></i>Mon Panier
                    @if($cart->items->isNotEmpty())
                        <span class="badge ms-2"
                              style="background:var(--primary-l);color:var(--primary);font-size:.75rem;font-weight:700;border-radius:20px;vertical-align:middle;">
                            {{ $cart->total_items }} article(s)
                        </span>
                    @endif
                </h1>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Continuer mes achats
            </a>
        </div>
    </div>
</div>

<div class="container py-5">

@if($cart->items->isEmpty())
    {{-- ─── PANIER VIDE ─── --}}
    <div class="text-center py-5">
        <div style="width:100px;height:100px;background:var(--primary-l);border-radius:26px;
                    display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;
                    box-shadow:0 8px 24px rgba(37,99,235,.12);">
            <i class="bi bi-bag fs-1 text-primary"></i>
        </div>
        <h3 class="fw-800 mb-2">Votre panier est vide</h3>
        <p class="text-muted mb-4" style="font-size:.92rem;">Découvrez nos produits et ajoutez-les à votre panier.</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-grid-3x3-gap"></i>Parcourir le catalogue
        </a>
    </div>

@else
    <div class="row g-4">

        {{-- ─── LISTE DES ARTICLES ─── --}}
        <div class="col-lg-8">
            <div class="card">
                {{-- Header --}}
                <div class="card-header d-flex justify-content-between align-items-center py-3 px-4"
                     style="background:#fff;border-bottom:1px solid var(--border);">
                    <h5 class="mb-0 fw-700 d-flex align-items-center gap-2">
                        <i class="bi bi-bag-check text-primary"></i>Articles
                        <span class="badge" style="background:#f1f5f9;color:var(--text-muted);font-weight:600;font-size:.72rem;">
                            {{ $cart->total_items }}
                        </span>
                    </h5>
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="btn btn-sm fw-600"
                                style="color:#dc2626;background:#fef2f2;border:1px solid #fecaca;border-radius:9px;"
                                onclick="return confirm('Vider tout le panier ?')">
                            <i class="bi bi-trash3"></i>Vider le panier
                        </button>
                    </form>
                </div>

                {{-- Articles --}}
                <div class="card-body p-0">
                    @foreach($cart->items as $item)
                    <div class="cart-item-row" id="item-row-{{ $item->id }}">
                        <div class="d-flex align-items-center gap-3">

                            {{-- Image produit --}}
                            <a href="{{ route('products.show', $item->product) }}" class="flex-shrink-0">
                                <img src="{{ $item->product->image_url }}"
                                     alt="{{ $item->product->name }}"
                                     class="cart-item-img"
                                     onerror="this.src='https://placehold.co/80x80/f1f5f9/94a3b8?text=?'">
                            </a>

                            {{-- Infos produit --}}
                            <div class="flex-grow-1 min-width-0">
                                <h6 class="fw-700 mb-1" style="font-size:.92rem;line-height:1.3;">
                                    <a href="{{ route('products.show', $item->product) }}"
                                       class="text-decoration-none" style="color:var(--text);">
                                        {{ $item->product->name }}
                                    </a>
                                </h6>

                                @if($item->product->category)
                                    <span class="badge badge-category mb-1" style="font-size:.65rem;">
                                        {{ $item->product->category }}
                                    </span>
                                @endif

                                <div style="font-size:.8rem;color:var(--text-muted);">
                                    Prix unitaire :
                                    <strong style="color:var(--primary);">{{ $item->product->formatted_price }}</strong>
                                </div>

                                {{-- Alertes stock --}}
                                @if($item->product->stock < $item->quantity)
                                    <div class="mt-1" style="font-size:.74rem;color:#dc2626;font-weight:600;">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        Stock insuffisant — max {{ $item->product->stock }} dispo.
                                    </div>
                                @elseif($item->product->stock <= 5)
                                    <div class="mt-1" style="font-size:.74rem;color:#d97706;font-weight:600;">
                                        <i class="bi bi-exclamation-circle-fill me-1"></i>
                                        Plus que {{ $item->product->stock }} en stock
                                    </div>
                                @endif
                            </div>

                            {{-- Stepper quantité --}}
                            <div class="flex-shrink-0">
                                <form action="{{ route('cart.update', $item) }}" method="POST"
                                      id="form-{{ $item->id }}">
                                    @csrf @method('PATCH')
                                    <div class="qty-stepper">
                                        <button type="button"
                                                onclick="stepCart({{ $item->id }}, -1, {{ $item->product->stock }})"
                                                {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                            <i class="bi bi-dash-lg"></i>
                                        </button>
                                        <input type="number" name="quantity"
                                               id="cart-qty-{{ $item->id }}"
                                               value="{{ $item->quantity }}"
                                               min="1" max="{{ $item->product->stock }}"
                                               onchange="cartQtyChanged({{ $item->id }}, {{ $item->product->stock }}, {{ $item->product->price }})">
                                        <button type="button"
                                                onclick="stepCart({{ $item->id }}, 1, {{ $item->product->stock }})"
                                                {{ $item->quantity >= $item->product->stock ? 'disabled' : '' }}>
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                    <button type="submit" class="d-none" id="submit-{{ $item->id }}"></button>
                                </form>
                            </div>

                            {{-- Sous-total ligne --}}
                            <div class="flex-shrink-0 text-end" style="min-width:92px;">
                                <div class="fw-900" style="font-size:1.05rem;color:var(--primary);"
                                     id="subtotal-{{ $item->id }}">
                                    {{ $item->formatted_subtotal }}
                                </div>
                                <div style="font-size:.7rem;color:var(--text-muted);" id="qty-label-{{ $item->id }}">
                                    {{ $item->quantity }} × {{ $item->product->formatted_price }}
                                </div>
                            </div>

                            {{-- Supprimer --}}
                            <div class="flex-shrink-0">
                                <form action="{{ route('cart.remove', $item) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="cart-remove-btn" title="Supprimer">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ─── RÉSUMÉ COMMANDE ─── --}}
        <div class="col-lg-4">
            <div class="card sticky-top" style="top:80px;">
                <div class="card-header py-3 px-4"
                     style="background:#fff;border-bottom:1px solid var(--border);">
                    <h5 class="mb-0 fw-700 d-flex align-items-center gap-2">
                        <i class="bi bi-receipt text-primary"></i>Résumé
                    </h5>
                </div>
                <div class="card-body p-4">

                    {{-- Lignes articles --}}
                    <div class="mb-3" style="max-height:220px;overflow-y:auto;padding-right:4px;">
                        @foreach($cart->items as $item)
                            <div class="d-flex justify-content-between align-items-start mb-2"
                                 style="font-size:.84rem;gap:.5rem;">
                                <span class="text-muted flex-grow-1" style="line-height:1.4;">
                                    {{ Str::limit($item->product->name, 20) }}
                                    <span class="fw-600 text-dark">
                                        × <span id="sum-qty-{{ $item->id }}">{{ $item->quantity }}</span>
                                    </span>
                                </span>
                                <span class="fw-700 flex-shrink-0"
                                      id="sum-sub-{{ $item->id }}">{{ $item->formatted_subtotal }}</span>
                            </div>
                        @endforeach
                    </div>

                    <hr style="border-color:var(--border);margin:.8rem 0;">

                    <div class="d-flex justify-content-between mb-2" style="font-size:.88rem;">
                        <span class="text-muted">Sous-total</span>
                        <span class="fw-600" id="cart-subtotal">{{ $cart->formatted_total }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4" style="font-size:.88rem;">
                        <span class="text-muted">Livraison</span>
                        <span class="fw-700" style="color:#16a34a;">
                            <i class="bi bi-check-circle-fill me-1"></i>Gratuite
                        </span>
                    </div>

                    <div class="p-3 rounded-3 mb-4"
                         style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1.5px solid rgba(37,99,235,.15);">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-700" style="font-size:.95rem;">Total à payer</span>
                            <span class="fw-900" style="font-size:1.5rem;color:var(--primary);letter-spacing:-.04em;"
                                  id="cart-total">{{ $cart->formatted_total }}</span>
                        </div>
                    </div>

                    <a href="{{ route('orders.checkout') }}" class="btn btn-primary btn-lg w-100 fw-700 mb-3">
                        <i class="bi bi-credit-card"></i>Passer la commande
                    </a>

                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 btn-sm">
                        <i class="bi bi-arrow-left"></i>Continuer mes achats
                    </a>

                    <div class="text-center mt-3" style="font-size:.72rem;color:var(--text-muted);">
                        <i class="bi bi-shield-lock me-1"></i>Paiement 100% sécurisé SSL
                    </div>
                </div>
            </div>
        </div>

    </div>
@endif
</div>

@push('styles')
<style>
    .cart-item-row {
        padding: 1.2rem 1.3rem;
        border-bottom: 1px solid var(--border);
        transition: background .2s;
    }
    .cart-item-row:last-child { border-bottom: none; }
    .cart-item-row:hover { background: #fafbfc; }
    .cart-item-img {
        width: 76px; height: 76px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid var(--border);
    }
    .cart-remove-btn {
        width: 34px; height: 34px; padding: 0;
        border-radius: 9px;
        background: #fef2f2; border: 1px solid #fecaca;
        color: #dc2626;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all .2s;
        font-size: .85rem;
    }
    .cart-remove-btn:hover { background: #fee2e2; transform: scale(1.05); }
</style>
@endpush

@push('scripts')
<script>
    const unitPrices = {
        @foreach($cart->items as $item)
        {{ $item->id }}: {{ $item->product->price }},
        @endforeach
    };

    function formatEuro(amount) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency', currency: 'EUR', minimumFractionDigits: 2
        }).format(amount);
    }

    function stepCart(itemId, delta, maxStock) {
        const input = document.getElementById('cart-qty-' + itemId);
        let qty = parseInt(input.value) + delta;
        if (qty < 1) qty = 1;
        if (qty > maxStock) qty = maxStock;
        input.value = qty;
        cartQtyChanged(itemId, maxStock, unitPrices[itemId]);
    }

    function cartQtyChanged(itemId, maxStock, unitPrice) {
        const input = document.getElementById('cart-qty-' + itemId);
        let qty = parseInt(input.value) || 1;
        if (qty < 1) qty = 1;
        if (qty > maxStock) qty = maxStock;
        input.value = qty;

        // Boutons stepper
        const stepper = input.closest('.qty-stepper');
        if (stepper) {
            stepper.querySelectorAll('button')[0].disabled = (qty <= 1);
            stepper.querySelectorAll('button')[1].disabled = (qty >= maxStock);
        }

        // Sous-total de la ligne
        const sub = unitPrices[itemId] * qty;
        const subEl = document.getElementById('subtotal-' + itemId);
        if (subEl) subEl.textContent = formatEuro(sub);

        // Label qty × prix
        const qtyLabel = document.getElementById('qty-label-' + itemId);
        if (qtyLabel) qtyLabel.textContent = qty + ' × ' + formatEuro(unitPrices[itemId]);

        // Résumé latéral
        const sumQtyEl = document.getElementById('sum-qty-' + itemId);
        if (sumQtyEl) sumQtyEl.textContent = qty;
        const sumSubEl = document.getElementById('sum-sub-' + itemId);
        if (sumSubEl) sumSubEl.textContent = formatEuro(sub);

        recalcTotal();

        // Debounce — soumet le formulaire après 750ms d'inactivité
        clearTimeout(window['debounce_' + itemId]);
        window['debounce_' + itemId] = setTimeout(() => {
            document.getElementById('submit-' + itemId).click();
        }, 750);
    }

    function recalcTotal() {
        let total = 0;
        Object.keys(unitPrices).forEach(id => {
            const input = document.getElementById('cart-qty-' + id);
            if (input) total += unitPrices[id] * (parseInt(input.value) || 1);
        });
        const totalEl    = document.getElementById('cart-total');
        const subtotalEl = document.getElementById('cart-subtotal');
        if (totalEl)    totalEl.textContent    = formatEuro(total);
        if (subtotalEl) subtotalEl.textContent = formatEuro(total);
    }
</script>
@endpush
@endsection
