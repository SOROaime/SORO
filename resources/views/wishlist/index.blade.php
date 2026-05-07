@extends('layouts.app')
@section('title', 'Mes Favoris')

@section('content')

<div class="page-header-bar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                        <li class="breadcrumb-item active">Mes Favoris</li>
                    </ol>
                </nav>
                <h1 class="section-title mb-0">
                    <i class="bi bi-heart-fill me-2" style="color:#ef4444;"></i>
                    Mes Favoris
                    @if($favorites->isNotEmpty())
                        <span class="badge ms-2"
                              style="background:#fee2e2;color:#dc2626;font-size:.72rem;
                                     font-weight:700;border-radius:20px;vertical-align:middle;">
                            {{ $favorites->count() }} produit(s)
                        </span>
                    @endif
                </h1>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-grid me-1"></i>Catalogue
            </a>
        </div>
    </div>
</div>

<div class="container py-5">

    @if($favorites->isEmpty())
        {{-- Aucun favori --}}
        <div class="text-center py-5 fade-in-up">
            <div style="width:100px;height:100px;background:#fee2e2;border-radius:28px;
                        display:flex;align-items:center;justify-content:center;
                        margin:0 auto 1.5rem;box-shadow:0 8px 28px rgba(239,68,68,.15);">
                <i class="bi bi-heart fs-1" style="color:#ef4444;"></i>
            </div>
            <h3 class="fw-800 mb-2">Aucun favori pour le moment</h3>
            <p class="text-muted mb-4" style="font-size:.92rem;">
                Cliquez sur le <i class="bi bi-heart" style="color:#ef4444;"></i> sur un produit pour l'ajouter ici.
            </p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg px-5">
                <i class="bi bi-grid-3x3-gap me-2"></i>Parcourir le catalogue
            </a>
        </div>

    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
            @foreach($favorites as $fav)
                <div class="col fade-in-up" id="fav-col-{{ $fav->product->id }}">
                    @include('components.product-card', ['product' => $fav->product])
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
