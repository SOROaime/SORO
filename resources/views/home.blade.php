@extends('layouts.app')

@section('title', 'Accueil')

@section('content')

{{-- ===================== SECTION HÉRO ===================== --}}
<section class="hero">
    <div class="container text-center">
        <h1 class="mb-3">
            La boutique <span class="accent">en ligne</span><br>qu'il vous faut
        </h1>
        <p class="lead mb-4 text-white-50">
            Des milliers de produits de qualité, livrés directement chez vous.<br>
            Paiement sécurisé et retours facilités.
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('products.index') }}" class="btn btn-warning btn-lg px-5 fw-bold">
                <i class="bi bi-grid me-2"></i>Voir les produits
            </a>
            @guest
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-5">
                    <i class="bi bi-person-plus me-2"></i>S'inscrire gratuitement
                </a>
            @endguest
        </div>

        {{-- Badges de réassurance --}}
        <div class="d-flex justify-content-center gap-4 mt-5 flex-wrap">
            <div class="text-center">
                <i class="bi bi-shield-check fs-2 text-warning"></i>
                <div class="small mt-1">Paiement sécurisé</div>
            </div>
            <div class="text-center">
                <i class="bi bi-truck fs-2 text-warning"></i>
                <div class="small mt-1">Livraison rapide</div>
            </div>
            <div class="text-center">
                <i class="bi bi-arrow-return-left fs-2 text-warning"></i>
                <div class="small mt-1">Retour facile</div>
            </div>
            <div class="text-center">
                <i class="bi bi-headset fs-2 text-warning"></i>
                <div class="small mt-1">Support 7j/7</div>
            </div>
        </div>
    </div>
</section>

{{-- ===================== PRODUITS EN VEDETTE ===================== --}}
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">
                <i class="bi bi-star-fill text-warning me-2"></i>Produits en vedette
            </h2>
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                Voir tout <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        @if($featuredProducts->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-box-seam display-4 text-muted"></i>
                <p class="text-muted mt-3">Aucun produit disponible pour le moment.</p>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-plus me-1"></i>Ajouter des produits
                        </a>
                    @endif
                @endauth
            </div>
        @else
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                @foreach($featuredProducts as $product)
                    <div class="col">
                        @include('components.product-card', ['product' => $product])
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ===================== CATÉGORIES ===================== --}}
@if($categories->isNotEmpty())
<section class="py-4 bg-white">
    <div class="container">
        <h2 class="section-title mb-4">
            <i class="bi bi-tags me-2"></i>Catégories
        </h2>
        <div class="d-flex flex-wrap gap-2">
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category]) }}"
                   class="btn btn-outline-secondary btn-sm px-4 py-2 rounded-pill">
                    <i class="bi bi-tag me-1"></i>{{ $category }}
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===================== SECTION CTA ===================== --}}
@guest
<section class="py-5" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
    <div class="container text-center text-white">
        <h2 class="fw-bold mb-3">Prêt à commencer vos achats ?</h2>
        <p class="mb-4 opacity-75">Créez votre compte gratuitement et profitez de toutes nos offres.</p>
        <a href="{{ route('register') }}" class="btn btn-warning btn-lg px-5 fw-bold">
            <i class="bi bi-person-plus me-2"></i>Créer mon compte
        </a>
    </div>
</section>
@endguest

@endsection
