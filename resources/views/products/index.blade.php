@extends('layouts.app')

@section('title', 'Catalogue Produits')

@section('content')

{{-- ─── EN-TÊTE ─── --}}
<div style="background:#fff;border-bottom:1px solid var(--border);">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                        <li class="breadcrumb-item active">Catalogue</li>
                    </ol>
                </nav>
                <h1 class="section-title mb-1">
                    <i class="bi bi-grid-3x3-gap me-2" style="color:var(--accent)"></i>Catalogue
                </h1>
                <p class="text-muted mb-0" style="font-size:.85rem;">
                    <span class="fw-700" style="color:var(--primary);">{{ $products->total() }}</span> produit(s) trouvé(s)
                    @if(request('search')) &bull; Recherche : <em>"{{ request('search') }}"</em> @endif
                    @if(request('category')) &bull; Catégorie : <strong>{{ request('category') }}</strong> @endif
                </p>
            </div>
            @auth @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i>Nouveau produit
                </a>
            @endif @endauth
        </div>
    </div>
</div>

<div class="container py-5">

    {{-- ─── BARRE DE FILTRES ─── --}}
    <div class="card mb-5" style="border-radius:16px!important;">
        <div class="card-body p-4">
            <form action="{{ route('products.index') }}" method="GET" id="filterForm">
                <div class="row g-3 align-items-end">

                    {{-- Recherche --}}
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="bi bi-search me-1 text-muted"></i>Rechercher
                        </label>
                        <input type="text" name="search" class="form-control"
                               placeholder="Nom du produit..."
                               value="{{ request('search') }}">
                    </div>

                    {{-- Catégorie --}}
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-tag me-1 text-muted"></i>Catégorie
                        </label>
                        <select name="category" class="form-select">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tri --}}
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-sort-down me-1 text-muted"></i>Trier par
                        </label>
                        <select name="sort" class="form-select">
                            <option value="recent"     {{ request('sort','recent') === 'recent'     ? 'selected' : '' }}>Plus récents</option>
                            <option value="price_asc"  {{ request('sort') === 'price_asc'           ? 'selected' : '' }}>Prix croissant</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc'          ? 'selected' : '' }}>Prix décroissant</option>
                            <option value="name"       {{ request('sort') === 'name'                ? 'selected' : '' }}>Nom (A–Z)</option>
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-funnel-fill"></i>
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── GRILLE PRODUITS ─── --}}
    @if($products->isEmpty())
        <div class="text-center py-5">
            <div style="width:80px;height:80px;background:var(--primary-l);border-radius:20px;
                        display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
                <i class="bi bi-search fs-2 text-primary"></i>
            </div>
            <h4 class="fw-700 mb-2">Aucun produit trouvé</h4>
            <p class="text-muted mb-4">Essayez de modifier vos critères de recherche.</p>
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i>Voir tous les produits
            </a>
        </div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
            @foreach($products as $i => $product)
                <div class="col fade-in-up" style="animation-delay:{{ ($i % 8) * 0.05 }}s;">
                    @include('components.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-5 pt-2">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
