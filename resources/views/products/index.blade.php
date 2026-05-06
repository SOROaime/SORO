@extends('layouts.app')
@section('title', 'Catalogue')

@section('content')

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

<div class="container py-5">
    <div class="row g-4">

        {{-- ─── FILTRES (sidebar) ─── --}}
        <div class="col-lg-3 fade-in-up">
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
        <div class="col-lg-9">

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
                <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">
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
@endsection
