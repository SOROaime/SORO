@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container py-5">

    {{-- Fil d'Ariane --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produits</a></li>
            @if($product->category)
                <li class="breadcrumb-item">
                    <a href="{{ route('products.index', ['category' => $product->category]) }}">
                        {{ $product->category }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active">{{ Str::limit($product->name, 30) }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        {{-- Image --}}
        <div class="col-lg-5">
            <div class="card border-0 rounded-4 overflow-hidden shadow-sm">
                <img
                    src="{{ $product->image_url }}"
                    alt="{{ $product->name }}"
                    class="img-fluid"
                    style="max-height: 450px; object-fit: cover; width: 100%;"
                    onerror="this.src='https://placehold.co/600x450/e2e8f0/94a3b8?text={{ urlencode($product->name) }}'"
                >
            </div>
        </div>

        {{-- Détails --}}
        <div class="col-lg-7">
            {{-- Badge catégorie --}}
            @if($product->category)
                <span class="badge bg-primary-subtle text-primary mb-2">{{ $product->category }}</span>
            @endif

            <h1 class="fw-bold mb-2" style="font-size: 2rem;">{{ $product->name }}</h1>

            {{-- Prix --}}
            <div class="my-3">
                <span class="fs-1 fw-bold text-primary">{{ $product->formatted_price }}</span>
                <span class="text-muted ms-2 small">TTC</span>
            </div>

            {{-- Stock --}}
            <div class="mb-3">
                @if($product->stock > 10)
                    <span class="badge bg-success fs-6 px-3 py-2">
                        <i class="bi bi-check-circle me-1"></i>En stock ({{ $product->stock }} disponibles)
                    </span>
                @elseif($product->stock > 0)
                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>Stock limité ({{ $product->stock }} restants)
                    </span>
                @else
                    <span class="badge bg-danger fs-6 px-3 py-2">
                        <i class="bi bi-x-circle me-1"></i>Rupture de stock
                    </span>
                @endif
            </div>

            <hr>

            {{-- Description --}}
            <h5 class="fw-semibold mb-2">Description</h5>
            <p class="text-muted lh-lg">{{ $product->description }}</p>

            <hr>

            {{-- Formulaire ajout panier --}}
            @auth
                @if($product->isAvailable())
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="row g-3 align-items-end">
                            <div class="col-auto">
                                <label for="quantity" class="form-label fw-semibold">Quantité</label>
                                <div class="input-group" style="width: 130px;">
                                    <button type="button" class="btn btn-outline-secondary" id="qty-minus">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" name="quantity" id="quantity"
                                           class="form-control text-center"
                                           value="1" min="1" max="{{ $product->stock }}">
                                    <button type="button" class="btn btn-outline-secondary" id="qty-plus">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                                    <i class="bi bi-cart-plus me-2"></i>Ajouter au panier
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <button class="btn btn-secondary btn-lg w-100" disabled>
                        <i class="bi bi-cart-x me-2"></i>Indisponible
                    </button>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100 fw-bold">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Connectez-vous pour acheter
                </a>
            @endauth

            {{-- Réassurance --}}
            <div class="row g-3 mt-3">
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <i class="bi bi-shield-check text-success fs-5"></i>
                        Paiement sécurisé
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <i class="bi bi-truck text-primary fs-5"></i>
                        Livraison rapide
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Produits similaires --}}
    @if($relatedProducts->isNotEmpty())
        <div class="mt-5 pt-4 border-top">
            <h3 class="section-title mb-4">
                <i class="bi bi-grid me-2"></i>Produits similaires
            </h3>
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

@push('scripts')
<script>
    // Gestion des boutons +/- quantité (uniquement si l'input existe, i.e. utilisateur connecté)
    const input = document.getElementById('quantity');
    if (input) {
        const max = parseInt(input.max);
        document.getElementById('qty-minus').addEventListener('click', () => {
            if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
        });
        document.getElementById('qty-plus').addEventListener('click', () => {
            if (parseInt(input.value) < max) input.value = parseInt(input.value) + 1;
        });
    }
</script>
@endpush
@endsection
