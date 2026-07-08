@extends('layouts.app')
@section('title', 'Catalogue')
@section('seo_title', request('category') ? request('category') . ' — ShopCI' : 'Catalogue produits — ShopCI Côte d\'Ivoire')
@section('seo_description', request('search') ? 'Résultats pour "' . request('search') . '" sur ShopCI — boutique en ligne Côte d\'Ivoire.' : 'Découvrez notre catalogue de produits sur ShopCI. Livraison gratuite partout en Côte d\'Ivoire. Paiement sécurisé.')

@section('content')

{{-- ─── BOUTON FILTRES FLOTTANT MOBILE — connectés seulement ─── --}}
@auth
<div class="d-lg-none" id="mobileFilterBar">
    <div class="px-3 py-2">
        <button class="btn btn-outline-secondary w-100"
                type="button" data-bs-toggle="collapse" data-bs-target="#filtresPanelMobile"
                aria-expanded="false">
            <i class="bi bi-funnel me-2"></i>Filtres & Recherche
            @if(request('search') || request('category'))
                <span class="badge ms-1" style="background:var(--primary);color:#fff;font-size:.65rem;">actif</span>
            @endif
            <i class="bi bi-chevron-down ms-auto"></i>
        </button>
        <div class="collapse" id="filtresPanelMobile">
            <div class="card mt-2">
                <div class="card-body p-3">
                    <form action="{{ route('products.index') }}" method="GET">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control"
                                       placeholder="Nom, description…"
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        @if(isset($categories) && $categories->isNotEmpty())
                        <div class="mb-3">
                            <select name="category" class="form-select form-select-sm">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="mb-3">
                            <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="newest"     {{ request('sort','newest') === 'newest'    ? 'selected' : '' }}>Nouveautés</option>
                                <option value="price_asc"  {{ request('sort') === 'price_asc'          ? 'selected' : '' }}>Prix croissant</option>
                                <option value="price_desc" {{ request('sort') === 'price_desc'         ? 'selected' : '' }}>Prix décroissant</option>
                                <option value="name"       {{ request('sort') === 'name'               ? 'selected' : '' }}>Nom A→Z</option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                <i class="bi bi-search"></i>Filtrer
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endauth

{{-- ─── HEADER ─── --}}
<div class="page-header-bar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                        <li class="breadcrumb-item active">Catalogue</li>
                    </ol>
                </nav>
                <h1 class="section-title mb-0">
                    <i class="bi bi-grid-3x3-gap me-2" style="color:var(--accent)"></i>
                    Catalogue
                    <span class="badge ms-2"
                          style="background:var(--primary-l);color:var(--primary);
                                 font-size:.72rem;font-weight:700;border-radius:20px;
                                 vertical-align:middle;">
                        {{ $products->total() }} produit(s)
                    </span>
                </h1>
            </div>
            @auth @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.products.create') }}"
                   class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i>Nouveau produit
                </a>
            @endif @endauth
        </div>
    </div>
</div>

<div class="container py-5" id="catalogueContainer">
    <div class="row g-4">

        {{-- ─── FILTRES (sidebar desktop) ─── --}}
        <div class="col-12 col-lg-3 fade-in-up d-none d-lg-block">
            <div class="card sticky-top" style="top:82px;">
                <div class="card-header py-3 px-4"
                     style="background:#fff;border-bottom:1px solid var(--border-2);">
                    <h6 class="fw-700 mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-funnel text-primary"></i>Filtres
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('products.index') }}" method="GET">

                        {{-- Recherche --}}
                        <div class="mb-4">
                            <label class="form-label"
                                   style="font-size:.72rem;text-transform:uppercase;
                                          letter-spacing:.07em;color:var(--text-muted);">
                                Recherche
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search"
                                       class="form-control"
                                       placeholder="Nom, description…"
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        {{-- Catégorie --}}
                        @if(isset($categories) && $categories->isNotEmpty())
                        <div class="mb-4">
                            <label class="form-label"
                                   style="font-size:.72rem;text-transform:uppercase;
                                          letter-spacing:.07em;color:var(--text-muted);">
                                Catégorie
                            </label>
                            <div class="d-flex flex-column gap-1">
                                <a href="{{ route('products.index', array_merge(request()->except('category','page'), [])) }}"
                                   class="cat-filter-btn {{ !request('category') ? 'active' : '' }}">
                                    <i class="bi bi-grid"></i>Toutes
                                </a>
                                @foreach($categories as $cat)
                                    <a href="{{ route('products.index', array_merge(request()->except('category','page'), ['category' => $cat])) }}"
                                       class="cat-filter-btn {{ request('category') === $cat ? 'active' : '' }}">
                                        <i class="bi bi-tag"></i>{{ $cat }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Tri --}}
                        <div class="mb-4">
                            <label class="form-label"
                                   style="font-size:.72rem;text-transform:uppercase;
                                          letter-spacing:.07em;color:var(--text-muted);">
                                Trier par
                            </label>
                            <select name="sort" class="form-select"
                                    onchange="this.form.submit()">
                                <option value="newest"  {{ request('sort','newest')  === 'newest'   ? 'selected' : '' }}>
                                    Nouveautés
                                </option>
                                <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected' : '' }}>
                                    Prix croissant
                                </option>
                                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>
                                    Prix décroissant
                                </option>
                                <option value="name"    {{ request('sort') === 'name'    ? 'selected' : '' }}>
                                    Nom A→Z
                                </option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                <i class="bi bi-search"></i>Filtrer
                            </button>
                            <a href="{{ route('products.index') }}"
                               class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ─── GRILLE PRODUITS ─── --}}
        <div class="col-12 col-lg-9">

            {{-- Filtres actifs --}}
            @if(request('search') || request('category'))
            <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
                <span class="text-muted" style="font-size:.82rem;">Filtres actifs :</span>
                @if(request('search'))
                    <span class="active-filter-tag">
                        <i class="bi bi-search"></i>{{ request('search') }}
                        <a href="{{ route('products.index', array_merge(request()->except('search','page'), [])) }}"
                           class="ms-1">&times;</a>
                    </span>
                @endif
                @if(request('category'))
                    <span class="active-filter-tag">
                        <i class="bi bi-tag"></i>{{ request('category') }}
                        <a href="{{ route('products.index', array_merge(request()->except('category','page'), [])) }}"
                           class="ms-1">&times;</a>
                    </span>
                @endif
            </div>
            @endif

            @if($products->isEmpty())
                <div class="text-center py-5 fade-in-up"
                     style="background:#fff;border-radius:var(--radius);
                            border:1.5px dashed var(--border);">
                    <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="fw-700 mb-2">Aucun produit trouvé</h5>
                    <p class="text-muted mb-3" style="font-size:.9rem;">
                        Essayez de modifier vos filtres de recherche.
                    </p>
                    <a href="{{ route('products.index') }}"
                       class="btn btn-primary btn-sm">
                        <i class="bi bi-x-circle"></i>Réinitialiser
                    </a>
                </div>
            @else
                <div class="row row-cols-2 row-cols-sm-2 row-cols-xl-3 g-2 g-sm-4">
                    @foreach($products as $product)
                        <div class="col fade-in-up">
                            @include('components.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($products->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-5">
                        <span style="font-size:.82rem;color:var(--text-muted);">
                            Affichage de <strong>{{ $products->firstItem() }}</strong>–<strong>{{ $products->lastItem() }}</strong>
                            sur <strong>{{ $products->total() }}</strong> produits
                        </span>
                        <nav aria-label="Pagination">
                            {{ $products->withQueryString()->links() }}
                        </nav>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    /* ── Bouton filtres flottant mobile ── */
    #mobileFilterBar {
        background: #fff;
        border-bottom: 1px solid var(--border);
        z-index: 1020;
    }
    #mobileFilterBar.is-sticky {
        position: fixed;
        top: var(--navbar-h);
        left: 0; right: 0;
        box-shadow: 0 4px 16px rgba(0,0,0,.10);
    }

    .cat-filter-btn {
        display: flex; align-items: center; gap: 7px;
        padding: .48rem .85rem;
        border-radius: 9px;
        font-size: .84rem; font-weight: 600;
        color: var(--text-muted);
        text-decoration: none;
        transition: var(--transition);
        border: 1.5px solid transparent;
    }
    .cat-filter-btn:hover {
        background: var(--primary-xl);
        color: var(--primary);
        border-color: var(--primary-l);
    }
    .cat-filter-btn.active {
        background: var(--primary-l);
        color: var(--primary);
        border-color: var(--primary-l);
        font-weight: 700;
    }

    .active-filter-tag {
        display: inline-flex; align-items: center; gap: 5px;
        background: var(--primary-l);
        color: var(--primary);
        padding: .28em .72em;
        border-radius: 20px;
        font-size: .8rem; font-weight: 600;
    }
    .active-filter-tag a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 800;
        opacity: .7;
    }
    .active-filter-tag a:hover { opacity: 1; }

</style>
@endpush

@push('scripts')
<script>
(function () {
    const bar = document.getElementById('mobileFilterBar');
    if (!bar) return; // non connecté → rien à faire

    // Placeholder pour éviter le saut de contenu quand la barre devient fixed
    const placeholder = document.createElement('div');
    bar.parentNode.insertBefore(placeholder, bar.nextSibling);

    const navH = parseInt(getComputedStyle(document.documentElement)
                     .getPropertyValue('--navbar-h')) || 68;
    let barTop = 0;

    function measure() {
        bar.classList.remove('is-sticky');
        placeholder.style.height = '';
        barTop = bar.getBoundingClientRect().top + window.scrollY;
    }

    function onScroll() {
        if (window.innerWidth >= 992) return; // desktop : sidebar normale
        if (window.scrollY > barTop - navH) {
            if (!bar.classList.contains('is-sticky')) {
                placeholder.style.height = bar.offsetHeight + 'px';
                bar.classList.add('is-sticky');
            }
        } else {
            bar.classList.remove('is-sticky');
            placeholder.style.height = '';
        }
    }

    measure();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', function () { measure(); onScroll(); }, { passive: true });
}());
</script>
@endpush
@endsection
