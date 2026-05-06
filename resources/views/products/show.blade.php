@extends('layouts.app')

@section('title', $product->name)

@section('content')

{{-- ─── EN-TÊTE PAGE ─── --}}
<div style="background:#fff;border-bottom:1px solid var(--border);">
    <div class="container py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Catalogue</a></li>
                @if($product->category)
                    <li class="breadcrumb-item">
                        <a href="{{ route('products.index', ['category' => $product->category]) }}">{{ $product->category }}</a>
                    </li>
                @endif
                <li class="breadcrumb-item active">{{ Str::limit($product->name, 40) }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">

        {{-- ─── COLONNE IMAGE ─── --}}
        <div class="col-lg-5">
            {{-- Image principale --}}
            <div class="product-img-card mb-4">
                <img src="{{ $product->image_url }}"
                     alt="{{ $product->name }}"
                     class="img-fluid w-100"
                     id="mainProductImg"
                     onerror="this.src='https://placehold.co/600x480/f1f5f9/94a3b8?text={{ urlencode($product->name) }}'">
                @if(!$product->is_active)
                    <div class="inactive-overlay">
                        <span><i class="bi bi-eye-slash me-2"></i>Produit inactif</span>
                    </div>
                @endif
            </div>

            {{-- Badges réassurance --}}
            <div class="row g-2">
                @foreach([
                    ['bi-shield-check',       '#22c55e','#f0fdf4', 'Paiement sécurisé'],
                    ['bi-truck',              '#2563eb','#eff6ff', 'Livraison gratuite'],
                    ['bi-box-arrow-in-left',  '#f59e0b','#fffbeb', 'Retour sous 30j'],
                    ['bi-headset',            '#7c3aed','#f5f3ff', 'Support 7j/7'],
                ] as $r)
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2 p-2 rounded-3"
                         style="background:{{ $r[2] }};border:1px solid {{ $r[2] }};">
                        <i class="bi {{ $r[0] }}" style="color:{{ $r[1] }};font-size:1rem;flex-shrink:0;"></i>
                        <span style="font-size:.76rem;font-weight:600;color:var(--text);">{{ $r[3] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ─── COLONNE DÉTAILS ─── --}}
        <div class="col-lg-7">

            {{-- En-tête produit --}}
            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div class="d-flex flex-wrap gap-2">
                    @if($product->category)
                        <span class="badge badge-category px-3 py-2" style="font-size:.75rem;">
                            <i class="bi bi-tag me-1"></i>{{ $product->category }}
                        </span>
                    @endif
                    @if(!$product->is_active)
                        <span class="badge bg-danger">Inactif</span>
                    @endif
                </div>
                @auth @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.products.edit', $product) }}"
                       class="btn btn-outline-secondary btn-sm flex-shrink-0">
                        <i class="bi bi-pencil"></i>Modifier
                    </a>
                @endif @endauth
            </div>

            {{-- Nom --}}
            <h1 class="fw-900 mb-4" style="font-size:1.9rem;letter-spacing:-.04em;line-height:1.1;color:var(--text);">
                {{ $product->name }}
            </h1>

            {{-- Prix --}}
            <div class="price-box mb-4">
                <div class="d-flex align-items-baseline gap-3">
                    <span class="fw-900" id="unitPriceDisplay"
                          style="font-size:2.5rem;color:var(--primary);letter-spacing:-.06em;">
                        {{ $product->formatted_price }}
                    </span>
                    <div>
                        <div style="font-size:.78rem;color:var(--text-muted);font-weight:500;">TTC</div>
                        <div style="font-size:.75rem;color:#22c55e;font-weight:600;">
                            <i class="bi bi-truck me-1"></i>Livraison gratuite
                        </div>
                    </div>
                </div>
            </div>

            {{-- Indicateur de stock ─── --}}
            <div class="mb-4 p-3 rounded-3" style="background:#f8fafc;border:1px solid var(--border);">
                @php
                    $maxDisplay = 50;
                    $pct        = $product->stock > 0 ? min(100, round($product->stock / $maxDisplay * 100)) : 0;
                    $barColor   = $product->stock > 10 ? '#22c55e' : ($product->stock > 0 ? '#f59e0b' : '#ef4444');
                @endphp
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span style="font-size:.82rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em;">
                        Disponibilité
                    </span>
                    @if($product->stock > 10)
                        <span style="font-size:.85rem;font-weight:700;color:#16a34a;">
                            <i class="bi bi-check-circle-fill me-1"></i>En stock
                            <span style="background:#dcfce7;color:#166534;padding:.15em .6em;border-radius:20px;font-size:.72rem;margin-left:4px;">
                                {{ $product->stock }} unités
                            </span>
                        </span>
                    @elseif($product->stock > 0)
                        <span style="font-size:.85rem;font-weight:700;color:#d97706;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Stock limité
                            <span style="background:#fef9c3;color:#854d0e;padding:.15em .6em;border-radius:20px;font-size:.72rem;margin-left:4px;">
                                {{ $product->stock }} restants
                            </span>
                        </span>
                    @else
                        <span style="font-size:.85rem;font-weight:700;color:#dc2626;">
                            <i class="bi bi-x-circle-fill me-1"></i>Rupture de stock
                        </span>
                    @endif
                </div>
                <div class="stock-bar">
                    <div class="stock-bar-fill" style="width:{{ $pct }}%;background:{{ $barColor }};"></div>
                </div>
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <h6 style="font-size:.74rem;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);font-weight:700;margin-bottom:.6rem;">
                    Description
                </h6>
                <p style="line-height:1.8;color:var(--text);font-size:.92rem;">{{ $product->description }}</p>
            </div>

            <hr style="border-color:var(--border);margin:1.5rem 0;">

            {{-- ─── FORMULAIRE AJOUT PANIER ─── --}}
            @auth
                @if($product->isAvailable())
                    <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        {{-- Sélecteur de quantité --}}
                        <div class="mb-4">
                            <label class="form-label mb-3">
                                <i class="bi bi-123 me-1 text-muted"></i>Quantité
                            </label>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="qty-stepper" style="transform:scale(1.05);">
                                    <button type="button" id="qty-minus" onclick="changeQty(-1)" disabled>
                                        <i class="bi bi-dash-lg"></i>
                                    </button>
                                    <input type="number" name="quantity" id="quantity"
                                           value="1" min="1" max="{{ $product->stock }}"
                                           oninput="updatePrice()">
                                    <button type="button" id="qty-plus" onclick="changeQty(1)"
                                            {{ $product->stock <= 1 ? 'disabled' : '' }}>
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                                <div style="font-size:.82rem;color:var(--text-muted);">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Maximum <strong>{{ $product->stock }}</strong> unité(s) disponible(s)
                                </div>
                            </div>
                        </div>

                        {{-- Aperçu du prix total --}}
                        <div class="price-preview mb-4" id="pricePreviewBox">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div style="font-size:.72rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">
                                        Total pour votre sélection
                                    </div>
                                    <div class="total-price" id="pricePreview">{{ $product->formatted_price }}</div>
                                    <div style="font-size:.72rem;color:var(--text-muted);margin-top:3px;" id="qtyInfo">
                                        1 × {{ $product->formatted_price }}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div style="font-size:.72rem;color:var(--text-muted);">Prix unitaire</div>
                                    <div style="font-weight:800;font-size:1rem;color:var(--text);" id="unitPriceLabel">
                                        {{ $product->formatted_price }}
                                    </div>
                                    <div style="font-size:.7rem;color:#22c55e;font-weight:600;margin-top:2px;">
                                        <i class="bi bi-truck me-1"></i>Livraison offerte
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bouton ajout panier --}}
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1 fw-700" id="addToCartBtn">
                                <i class="bi bi-bag-plus"></i>Ajouter au panier
                            </button>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary btn-lg px-3" title="Voir mon panier">
                                <i class="bi bi-bag"></i>
                            </a>
                        </div>
                    </form>

                @else
                    <div class="p-4 rounded-3 text-center" style="background:#fef2f2;border:1.5px solid #fecaca;">
                        <i class="bi bi-bag-x fs-2 mb-2" style="color:#dc2626;"></i>
                        <div class="fw-700 mb-1" style="color:#991b1b;">Produit indisponible</div>
                        <div class="small" style="color:#b91c1c;">Ce produit n'est actuellement pas disponible à la vente.</div>
                    </div>
                @endif

            @else
                {{-- Non connecté --}}
                <div class="p-4 rounded-3 text-center" style="background:var(--primary-l);border:1.5px solid rgba(37,99,235,.18);">
                    <div style="width:52px;height:52px;background:var(--primary);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <i class="bi bi-person-lock fs-4 text-white"></i>
                    </div>
                    <div class="fw-700 mb-2" style="font-size:1rem;">Connectez-vous pour acheter</div>
                    <p class="text-muted mb-3" style="font-size:.85rem;">Créez un compte pour accéder au panier et finaliser vos achats.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i>Se connecter
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary">
                            <i class="bi bi-person-plus"></i>S'inscrire
                        </a>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    {{-- ─── PRODUITS SIMILAIRES ─── --}}
    @if($relatedProducts->isNotEmpty())
        <div class="mt-5 pt-5" style="border-top:1px solid var(--border);">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="section-title mb-0">
                    <i class="bi bi-grid me-2" style="color:var(--accent)"></i>Produits similaires
                </h3>
                <a href="{{ route('products.index', ['category' => $product->category]) }}"
                   class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-arrow-right me-1"></i>Voir plus
                </a>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                @foreach($relatedProducts as $related)
                    <div class="col">
                        @include('components.product-card', ['product' => $related])
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .product-img-card {
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-md);
        background: #f8fafc;
        position: relative;
        aspect-ratio: 4/3;
    }
    .product-img-card img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform .5s ease;
    }
    .product-img-card:hover img { transform: scale(1.03); }
    .inactive-overlay {
        position: absolute; inset: 0;
        background: rgba(15,23,42,.6);
        display: flex; align-items: center; justify-content: center;
    }
    .inactive-overlay span {
        background: #fee2e2; color: #991b1b;
        padding: .6rem 1.2rem; border-radius: 50px;
        font-size: .85rem; font-weight: 700;
    }
    .price-box {
        background: linear-gradient(135deg, #eff6ff, #f8fafc);
        border: 1.5px solid rgba(37,99,235,.12);
        border-radius: 14px;
        padding: 1.1rem 1.3rem;
    }
</style>
@endpush

@push('scripts')
<script>
    const unitPrice = {{ $product->price }};
    const maxStock  = {{ $product->stock }};

    function formatEuro(amount) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency', currency: 'EUR', minimumFractionDigits: 2
        }).format(amount);
    }

    function updatePrice() {
        const input = document.getElementById('quantity');
        let qty = parseInt(input.value) || 1;
        if (qty < 1)        qty = 1;
        if (qty > maxStock) qty = maxStock;
        input.value = qty;

        // Boutons stepper
        document.getElementById('qty-minus').disabled = (qty <= 1);
        document.getElementById('qty-plus').disabled  = (qty >= maxStock);

        // Afficher le prix total
        const total = unitPrice * qty;
        document.getElementById('pricePreview').textContent = formatEuro(total);
        document.getElementById('qtyInfo').textContent = qty + ' × ' + formatEuro(unitPrice);

        // Animation sur le prix
        const box = document.getElementById('pricePreviewBox');
        box.style.transform = 'scale(1.01)';
        setTimeout(() => box.style.transform = 'scale(1)', 150);
    }

    function changeQty(delta) {
        const input = document.getElementById('quantity');
        input.value = (parseInt(input.value) || 1) + delta;
        updatePrice();
    }

    // Init
    updatePrice();
</script>
@endpush
@endsection
