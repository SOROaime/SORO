@extends('layouts.app')

@section('title', 'Catalogue Produits')

@section('content')
<div class="container py-5">

    {{-- En-tête --}}
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="section-title">
                <i class="bi bi-grid me-2"></i>Catalogue
            </h1>
            <p class="text-muted">{{ $products->total() }} produit(s) trouvé(s)</p>
        </div>
    </div>

    {{-- Barre de filtres --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3">
        <div class="card-body p-3">
            <form action="{{ route('products.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    {{-- Recherche --}}
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted">Rechercher</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Nom du produit..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Catégorie --}}
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted">Catégorie</label>
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
                        <label class="form-label small fw-semibold text-muted">Trier par</label>
                        <select name="sort" class="form-select">
                            <option value="recent" {{ request('sort') === 'recent' ? 'selected' : '' }}>Plus récents</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nom (A-Z)</option>
                        </select>
                    </div>

                    {{-- Boutons --}}
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-funnel"></i>
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Grille produits --}}
    @if($products->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-search display-4 text-muted"></i>
            <p class="text-muted mt-3 fs-5">Aucun produit trouvé.</p>
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                Voir tous les produits
            </a>
        </div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach($products as $product)
                <div class="col">
                    @include('components.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-5">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
