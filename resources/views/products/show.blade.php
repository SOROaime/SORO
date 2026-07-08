@extends('layouts.app')
@section('title', $product->name)
@section('seo_title', $product->name . ' — ShopCI')
@section('seo_description', Str::limit(strip_tags($product->description ?? $product->name . ' disponible sur ShopCI. Livraison gratuite en Côte d\'Ivoire.'), 155))
@section('og_type', 'product')
@section('og_image', $product->image_url)

@push('seo_schema')
<script type="application/ld+json">{!! $productSchema !!}</script>
@endpush

@section('content')

{{-- ─── HEADER ─── --}}
<div class="page-header-bar">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Catalogue</a></li>
                @if($product->category)
                    <li class="breadcrumb-item">
                        <a href="{{ route('products.index', ['category' => $product->category]) }}">
                            {{ $product->category }}
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active">{{ Str::limit($product->name, 36) }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">

        {{-- ─── COLONNE IMAGE ─── --}}
        <div class="col-lg-5 fade-in-up">

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

                {{-- Badge catégorie flottant --}}
                @if($product->category)
                    <div style="position:absolute;top:14px;left:14px;">
                        <span class="badge badge-category"
                              style="font-size:.72rem;padding:.35em .9em;backdrop-filter:blur(10px);">
                            <i class="bi bi-tag me-1"></i>{{ $product->category }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Badges réassurance --}}
            <div class="row g-2">
                @foreach([
                    ['bi-shield-check', '#22c55e', '#f0fdf4', '#dcfce7', 'Paiement sécurisé'],
                    ['bi-truck',        '#2563eb', '#eff6ff', '#dbeafe', 'Livraison gratuite'],
                    ['bi-arrow-repeat', '#f59e0b', '#fffbeb', '#fef3c7', 'Retour sous 30j'],
                    ['bi-headset',      '#7c3aed', '#f5f3ff', '#ede9fe', 'Support 7j/7'],
                ] as $r)
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2 p-2 rounded-3"
                         style="background:{{ $r[2] }};border:1px solid {{ $r[3] }};">
                        <i class="bi {{ $r[0] }}"
                           style="color:{{ $r[1] }};font-size:.95rem;flex-shrink:0;"></i>
                        <span style="font-size:.74rem;font-weight:600;color:var(--text);">
                            {{ $r[4] }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ─── COLONNE DÉTAILS ─── --}}
        <div class="col-lg-7 fade-in-up-1">

            {{-- Header produit --}}
            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div class="d-flex flex-wrap gap-2">
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
            <h1 class="fw-900 mb-3"
                style="font-size:1.9rem;letter-spacing:-.045em;line-height:1.08;color:var(--text);">
                {{ $product->name }}
            </h1>

            {{-- Prix principal --}}
            <div class="price-box mb-4">
                <div class="d-flex align-items-baseline gap-3">
                    <span class="fw-900"
                          style="font-size:2.6rem;color:var(--primary);letter-spacing:-.065em;">
                        {{ $product->formatted_price }}
                    </span>
                    <div>
                        <div style="font-size:.76rem;color:var(--text-muted);font-weight:500;">TTC</div>
                        <div style="font-size:.74rem;color:#22c55e;font-weight:600;">
                            <i class="bi bi-truck me-1"></i>Livraison offerte
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── STOCK INDICATOR ─── --}}
            @php
                $maxDisplay = 50;
                $pct        = $product->stock > 0 ? min(100, round($product->stock / $maxDisplay * 100)) : 0;
                $barColor   = $product->stock > 10 ? '#22c55e' : ($product->stock > 0 ? '#f59e0b' : '#ef4444');
            @endphp
            <div class="mb-4 p-3 rounded-3"
                 style="background:#f8fafc;border:1px solid var(--border);">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span style="font-size:.78rem;font-weight:700;color:var(--text-muted);
                                 text-transform:uppercase;letter-spacing:.06em;">
                        Disponibilité
                    </span>
                    @if($product->stock > 10)
                        <span style="font-size:.84rem;font-weight:700;color:#16a34a;
                                     display:flex;align-items:center;gap:5px;">
                            <i class="bi bi-check-circle-fill"></i>En stock
                            <span style="background:#dcfce7;color:#166534;padding:.15em .65em;
                                         border-radius:20px;font-size:.7rem;margin-left:2px;">
                                {{ $product->stock }} unités
                            </span>
                        </span>
                    @elseif($product->stock > 0)
                        <span style="font-size:.84rem;font-weight:700;color:#d97706;
                                     display:flex;align-items:center;gap:5px;">
                            <i class="bi bi-exclamation-triangle-fill"></i>Stock limité
                            <span style="background:#fef9c3;color:#854d0e;padding:.15em .65em;
                                         border-radius:20px;font-size:.7rem;margin-left:2px;">
                                {{ $product->stock }} restants
                            </span>
                        </span>
                    @else
                        <span style="font-size:.84rem;font-weight:700;color:#dc2626;
                                     display:flex;align-items:center;gap:5px;">
                            <i class="bi bi-x-circle-fill"></i>Rupture de stock
                        </span>
                    @endif
                </div>
                <div class="stock-bar">
                    <div class="stock-bar-fill"
                         style="width:{{ $pct }}%;background:{{ $barColor }};"></div>
                </div>
            </div>

            {{-- Description --}}
            @if($product->description)
            <div class="mb-4">
                <h6 style="font-size:.72rem;text-transform:uppercase;letter-spacing:.07em;
                            color:var(--text-muted);font-weight:700;margin-bottom:.55rem;">
                    Description
                </h6>
                <p style="line-height:1.85;color:var(--text);font-size:.9rem;">
                    {{ $product->description }}
                </p>
            </div>
            @endif

            <div class="sep mb-4"></div>

            {{-- ─── AJOUT AU PANIER ─── --}}
            @auth
                @if($product->isAvailable())
                    <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        {{-- Sélecteur quantité --}}
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-123 me-1 text-muted"></i>Quantité
                            </label>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="qty-stepper lg">
                                    <button type="button" id="qty-minus"
                                            onclick="changeQty(-1)" disabled>
                                        <i class="bi bi-dash-lg"></i>
                                    </button>
                                    <input type="number" name="quantity" id="quantity"
                                           value="1" min="1" max="{{ $product->stock }}"
                                           oninput="updatePrice()">
                                    <button type="button" id="qty-plus"
                                            onclick="changeQty(1)"
                                            {{ $product->stock <= 1 ? 'disabled' : '' }}>
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                                <div style="font-size:.82rem;color:var(--text-muted);">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Max <strong>{{ $product->stock }}</strong> disponible(s)
                                </div>
                            </div>
                        </div>

                        {{-- Aperçu prix total --}}
                        <div class="price-preview mb-4" id="pricePreviewBox">
                            <div class="d-flex justify-content-between align-items-center gap-3">
                                <div>
                                    <div style="font-size:.7rem;color:var(--text-muted);font-weight:600;
                                                text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;">
                                        Total de votre sélection
                                    </div>
                                    <div class="total-price" id="pricePreview">
                                        {{ $product->formatted_price }}
                                    </div>
                                    <div style="font-size:.72rem;color:var(--text-muted);margin-top:4px;"
                                         id="qtyInfo">
                                        1 × {{ $product->formatted_price }}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div style="font-size:.7rem;color:var(--text-muted);">
                                        Prix unitaire
                                    </div>
                                    <div style="font-weight:800;font-size:1.05rem;color:var(--text);">
                                        {{ $product->formatted_price }}
                                    </div>
                                    <div style="font-size:.7rem;color:#22c55e;font-weight:600;margin-top:3px;">
                                        <i class="bi bi-truck me-1"></i>Livraison offerte
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Boutons --}}
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1 fw-700"
                                    id="addToCartBtn">
                                <i class="bi bi-bag-plus"></i>Ajouter au panier
                            </button>
                            <a href="{{ route('cart.index') }}"
                               class="btn btn-outline-secondary btn-lg px-3" title="Voir mon panier">
                                <i class="bi bi-bag"></i>
                            </a>
                            {{-- Bouton Favori --}}
                            @php $isFavShow = \App\Models\Wishlist::isFavorite(auth()->id(), $product->id); @endphp
                            <button type="button"
                                    class="btn btn-lg px-3 btn-heart-show {{ $isFavShow ? 'active' : '' }}"
                                    id="showFavBtn"
                                    data-product-id="{{ $product->id }}"
                                    data-toggle-url="{{ route('wishlist.toggle', $product) }}"
                                    data-csrf="{{ csrf_token() }}"
                                    title="{{ $isFavShow ? 'Retirer des favoris' : 'Ajouter aux favoris' }}">
                                <i class="bi {{ $isFavShow ? 'bi-heart-fill' : 'bi-heart' }}"
                                   style="font-size:1.2rem;"></i>
                            </button>
                        </div>
                    </form>

                @else
                    <div class="p-4 rounded-3 text-center"
                         style="background:#fef2f2;border:1.5px solid #fecaca;">
                        <i class="bi bi-bag-x fs-2 mb-2" style="color:#dc2626;display:block;"></i>
                        <div class="fw-700 mb-1" style="color:#991b1b;">Produit indisponible</div>
                        <div class="small" style="color:#b91c1c;">
                            Ce produit n'est pas disponible à la vente.
                        </div>
                    </div>
                @endif

            @else
                <div class="p-4 rounded-3 text-center"
                     style="background:var(--primary-xl);border:1.5px solid var(--primary-l);">
                    <div style="width:52px;height:52px;background:var(--primary);border-radius:14px;
                                display:flex;align-items:center;justify-content:center;
                                margin:0 auto 1rem;">
                        <i class="bi bi-person-lock fs-4 text-white"></i>
                    </div>
                    <div class="fw-700 mb-2" style="font-size:1rem;">
                        Connectez-vous pour acheter
                    </div>
                    <p class="text-muted mb-3" style="font-size:.85rem;">
                        Créez un compte pour accéder au panier.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i>Connexion
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary">
                            <i class="bi bi-person-plus"></i>S'inscrire
                        </a>
                    </div>
                </div>
            @endauth

        </div>
    </div>

    {{-- ─── SECTION AVIS & NOTES ─── --}}
    <div class="mt-5 pt-4" style="border-top:1px solid var(--border);">
        @php
            $avgRating    = $reviews->avg('rating') ?? 0;
            $roundedAvg   = round($avgRating, 1);
            $reviewsCount = $reviews->count();
            $ratingDist   = [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
            foreach ($reviews as $r) $ratingDist[$r->rating]++;
        @endphp

        <div class="row g-5">

            {{-- ── Résumé des notes ── --}}
            <div class="col-lg-4">
                <h3 class="section-title mb-4">
                    <i class="bi bi-star-fill me-2" style="color:#f59e0b;"></i>Avis clients
                </h3>

                <div class="text-center p-4 rounded-3 mb-4"
                     style="background:linear-gradient(135deg,#fffbeb,#fef9c3);border:1.5px solid #fde68a;">
                    <div style="font-size:4rem;font-weight:900;color:#f59e0b;line-height:1;letter-spacing:-.04em;">
                        {{ $roundedAvg > 0 ? number_format($roundedAvg,1) : '—' }}
                    </div>
                    <div class="my-2" style="font-size:1.5rem;color:#f59e0b;letter-spacing:2px;">
                        @for($i=1;$i<=5;$i++)
                            @if($i <= round($roundedAvg))
                                <i class="bi bi-star-fill"></i>
                            @elseif($i - 0.5 <= $roundedAvg)
                                <i class="bi bi-star-half"></i>
                            @else
                                <i class="bi bi-star" style="opacity:.3;"></i>
                            @endif
                        @endfor
                    </div>
                    <div class="text-muted" style="font-size:.82rem;">
                        {{ $reviewsCount }} avis
                    </div>
                </div>

                {{-- Distribution des notes --}}
                @foreach([5,4,3,2,1] as $star)
                @php $cnt = $ratingDist[$star]; $pct = $reviewsCount > 0 ? round($cnt/$reviewsCount*100) : 0; @endphp
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span style="font-size:.75rem;font-weight:700;color:#64748b;min-width:12px;">{{ $star }}</span>
                    <i class="bi bi-star-fill" style="font-size:.7rem;color:#f59e0b;"></i>
                    <div style="flex:1;height:8px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
                        <div style="width:{{ $pct }}%;height:100%;background:#f59e0b;border-radius:4px;transition:width .5s;"></div>
                    </div>
                    <span class="text-muted" style="font-size:.72rem;min-width:20px;">{{ $cnt }}</span>
                </div>
                @endforeach
            </div>

            {{-- ── Formulaire + liste ── --}}
            <div class="col-lg-8">

                {{-- Formulaire d'avis --}}
                @auth
                <div class="card mb-4" style="border-radius:16px;border:1.5px solid var(--border);">
                    <div class="card-header bg-white py-3 px-4"
                         style="border-bottom:1px solid var(--border);border-radius:16px 16px 0 0;">
                        <h6 class="mb-0 fw-800">
                            {{ $userReview ? 'Modifier votre avis' : 'Laisser un avis' }}
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success py-2 mb-3" style="font-size:.85rem;">
                                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            </div>
                        @endif
                        <form action="{{ route('reviews.store', $product) }}" method="POST">
                            @csrf
                            {{-- Étoiles interactives --}}
                            <div class="mb-3">
                                <label class="form-label fw-600" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                    Votre note <span class="text-danger">*</span>
                                </label>
                                <div class="star-rating-input" id="starInput">
                                    @for($i=1;$i<=5;$i++)
                                    <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}"
                                           {{ ($userReview && $userReview->rating == $i) ? 'checked' : '' }} required>
                                    <label for="star{{ $i }}" title="{{ $i }} étoile{{ $i>1?'s':'' }}">
                                        <i class="bi bi-star-fill"></i>
                                    </label>
                                    @endfor
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                    Commentaire <span class="text-muted fw-400">(optionnel)</span>
                                </label>
                                <textarea name="comment" class="form-control" rows="3"
                                          placeholder="Partagez votre expérience avec ce produit..."
                                          maxlength="1000">{{ $userReview?->comment }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary fw-700">
                                <i class="bi bi-send me-2"></i>
                                {{ $userReview ? 'Mettre à jour' : 'Publier mon avis' }}
                            </button>

                            @if($userReview)
                            <form action="{{ route('reviews.destroy', $userReview) }}" method="POST" class="d-inline ms-2">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm fw-600"
                                        onclick="return confirm('Supprimer votre avis ?')">
                                    <i class="bi bi-trash"></i> Supprimer
                                </button>
                            </form>
                            @endif
                        </form>
                    </div>
                </div>
                @else
                <div class="p-3 rounded-3 mb-4 text-center"
                     style="background:#f8fafc;border:1.5px solid var(--border);font-size:.88rem;">
                    <a href="{{ route('login') }}" class="fw-700 text-decoration-none">Connectez-vous</a>
                    pour laisser un avis.
                </div>
                @endauth

                {{-- Liste des avis --}}
                @forelse($reviews as $review)
                <div class="d-flex gap-3 py-3" style="border-bottom:1px solid var(--border);">
                    {{-- Avatar --}}
                    <div style="width:40px;height:40px;border-radius:50%;flex-shrink:0;
                                background:linear-gradient(135deg,var(--primary),var(--primary-d));
                                display:flex;align-items:center;justify-content:center;
                                font-size:.85rem;font-weight:800;color:#fff;">
                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                            <div>
                                <span class="fw-700" style="font-size:.9rem;">{{ $review->user->name }}</span>
                                <span class="ms-2" style="color:#f59e0b;font-size:.85rem;">
                                    @for($i=1;$i<=5;$i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted" style="font-size:.75rem;">
                                    {{ $review->created_at->diffForHumans() }}
                                </span>
                                @if(auth()->check() && (auth()->id() === $review->user_id || auth()->user()->isAdmin()))
                                <form action="{{ route('reviews.destroy', $review) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link btn-sm text-danger p-0"
                                            style="font-size:.75rem;"
                                            onclick="return confirm('Supprimer cet avis ?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @if($review->comment)
                        <p class="mb-0 mt-1" style="font-size:.87rem;color:var(--text);line-height:1.7;">
                            {{ $review->comment }}
                        </p>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-square-text fs-2 d-block mb-2"></i>
                    Aucun avis pour l'instant. Soyez le premier !
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ─── PRODUITS SIMILAIRES ─── --}}
    @if($relatedProducts->isNotEmpty())
        <div class="mt-5 pt-4" style="border-top:1px solid var(--border);">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="section-title mb-0">
                    <i class="bi bi-grid me-2" style="color:var(--accent)"></i>
                    Produits similaires
                </h3>
                <a href="{{ route('products.index', ['category' => $product->category]) }}"
                   class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-arrow-right"></i>Voir plus
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
        display: block;
    }
    .product-img-card:hover img { transform: scale(1.04); }
    .inactive-overlay {
        position: absolute; inset: 0;
        background: rgba(15,23,42,.58);
        display: flex; align-items: center; justify-content: center;
    }
    .inactive-overlay span {
        background: #fee2e2; color: #991b1b;
        padding: .6rem 1.2rem; border-radius: 50px;
        font-size: .84rem; font-weight: 700;
    }
    .price-box {
        background: linear-gradient(135deg, var(--primary-xl) 0%, #f8fafc 100%);
        border: 1.5px solid rgba(37,99,235,.1);
        border-radius: 14px;
        padding: 1.15rem 1.4rem;
    }
    /* Bouton cœur page produit */
    .btn-heart-show {
        border: 1.5px solid var(--border);
        background: #f8fafc;
        color: #cbd5e1;
        border-radius: 12px;
        transition: all .25s cubic-bezier(.175,.885,.32,1.275);
        flex-shrink: 0;
    }
    .btn-heart-show:hover,
    .btn-heart-show.active {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #ef4444;
        transform: scale(1.08);
        box-shadow: 0 4px 16px rgba(239,68,68,.22);
    }
    .btn-heart-show.active i {
        animation: heartPop .35s ease;
    }
    @keyframes heartPop {
        0%   { transform: scale(1); }
        50%  { transform: scale(1.5); }
        100% { transform: scale(1); }
    }
    /* Toast notification */
    /* ── Étoiles interactives ── */
    .star-rating-input { display:flex; flex-direction:row-reverse; gap:4px; width:fit-content; }
    .star-rating-input input { display:none; }
    .star-rating-input label { font-size:2rem; color:#d1d5db; cursor:pointer; transition:color .15s; }
    .star-rating-input input:checked ~ label,
    .star-rating-input label:hover,
    .star-rating-input label:hover ~ label { color:#f59e0b; }

    .fav-toast {
        position: fixed;
        bottom: 1.5rem; right: 1.5rem;
        background: #1e293b;
        color: #fff;
        padding: .75rem 1.25rem;
        border-radius: 12px;
        font-size: .85rem;
        font-weight: 600;
        box-shadow: 0 8px 28px rgba(0,0,0,.25);
        z-index: 9999;
        display: flex; align-items: center; gap: .55rem;
        transform: translateY(80px);
        opacity: 0;
        transition: all .3s cubic-bezier(.175,.885,.32,1.275);
    }
    .fav-toast.show {
        transform: translateY(0);
        opacity: 1;
    }
</style>
@endpush

@push('scripts')
<script>
    const unitPrice = {{ $product->price }};
    const maxStock  = {{ $product->stock }};

    function formatFCFA(amount) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'decimal', minimumFractionDigits: 0, maximumFractionDigits: 0
        }).format(Math.round(amount)) + ' FCFA';
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

        // Calcul du total
        const total = unitPrice * qty;
        document.getElementById('pricePreview').textContent = formatFCFA(total);
        document.getElementById('qtyInfo').textContent =
            qty + ' × ' + formatFCFA(unitPrice);

        // Animation flash sur la box
        const box = document.getElementById('pricePreviewBox');
        box.style.transform = 'scale(1.015)';
        box.style.boxShadow = '0 6px 24px rgba(37,99,235,.18)';
        setTimeout(() => {
            box.style.transform = 'scale(1)';
            box.style.boxShadow = '';
        }, 180);
    }

    function changeQty(delta) {
        const input = document.getElementById('quantity');
        input.value = (parseInt(input.value) || 1) + delta;
        updatePrice();
    }

    // Init au chargement
    document.addEventListener('DOMContentLoaded', () => {
        updatePrice();
        initFavBtn();
    });

    /* ── Bouton Favori AJAX ── */
    function initFavBtn() {
        const btn = document.getElementById('showFavBtn');
        if (!btn) return;

        btn.addEventListener('click', function () {
            const url   = this.dataset.toggleUrl;
            const csrf  = this.dataset.csrf;
            const icon  = this.querySelector('i');
            const isNowFav = !this.classList.contains('active');

            // Toggle visuel immédiat
            this.classList.toggle('active', isNowFav);
            icon.className = isNowFav ? 'bi bi-heart-fill' : 'bi bi-heart';
            icon.style.fontSize = '1.2rem';
            this.title = isNowFav ? 'Retirer des favoris' : 'Ajouter aux favoris';

            // Requête AJAX
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(r => r.json())
            .then(data => {
                // Mise à jour du compteur dans la navbar
                const badge = document.getElementById('navFavCount');
                if (badge) {
                    badge.textContent = data.count;
                    badge.style.display = data.count > 0 ? 'flex' : 'none';
                }
                showFavToast(data.added
                    ? '❤️ Ajouté aux favoris'
                    : '🤍 Retiré des favoris');
            })
            .catch(() => {
                // Revert si erreur réseau
                this.classList.toggle('active', !isNowFav);
                icon.className = !isNowFav ? 'bi bi-heart-fill' : 'bi bi-heart';
            });
        });
    }

    function showFavToast(msg) {
        let toast = document.getElementById('favToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'favToast';
            toast.className = 'fav-toast';
            document.body.appendChild(toast);
        }
        toast.textContent = msg;
        toast.classList.add('show');
        clearTimeout(toast._timer);
        toast._timer = setTimeout(() => toast.classList.remove('show'), 2500);
    }
</script>
@endpush
@endsection
