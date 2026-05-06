@extends('layouts.app')

@section('title', 'Accueil')

@section('content')

{{-- ═══════════════════ HERO ═══════════════════ --}}
<section class="hero">
    <div class="container position-relative" style="z-index:1;">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="hero-badge mb-4 fade-in-up">
                    <span class="live-dot"></span>
                    Boutique en ligne — Livraison rapide &amp; gratuite
                </div>
                <h1 class="mb-4 fade-in-up-1">
                    Découvrez notre<br>
                    sélection <span class="accent">premium</span>
                </h1>
                <p class="mb-5 fade-in-up-2" style="font-size:1.05rem;color:rgba(255,255,255,.65);max-width:480px;line-height:1.8;">
                    Des milliers de produits de qualité, paiement sécurisé SSL et gestion de stock en temps réel. Une expérience d'achat sans compromis.
                </p>
                <div class="d-flex gap-3 flex-wrap fade-in-up-3">
                    <a href="{{ route('products.index') }}" class="btn btn-warning btn-lg px-5 fw-700">
                        <i class="bi bi-grid-3x3-gap"></i>Explorer le catalogue
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-lg px-5 fw-600"
                           style="background:rgba(255,255,255,.1);color:#fff;border:1.5px solid rgba(255,255,255,.22);backdrop-filter:blur(10px);">
                            <i class="bi bi-person-plus"></i>Créer un compte
                        </a>
                    @endguest
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-flex justify-content-center">
                <div style="position:relative;width:340px;height:340px;">
                    {{-- Glow --}}
                    <div style="position:absolute;inset:-40px;background:radial-gradient(circle,rgba(37,99,235,.2),transparent 70%);border-radius:50%;"></div>
                    {{-- Card principale --}}
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
                                width:250px;height:250px;
                                background:rgba(255,255,255,.07);
                                border:1.5px solid rgba(255,255,255,.14);
                                border-radius:28px;
                                backdrop-filter:blur(20px);
                                display:flex;align-items:center;justify-content:center;flex-direction:column;gap:14px;">
                        <i class="bi bi-bag-heart-fill" style="font-size:5rem;color:var(--accent);filter:drop-shadow(0 8px 16px rgba(245,158,11,.4));"></i>
                        <span style="color:#fff;font-weight:900;font-size:1.2rem;letter-spacing:-.04em;">Shop<span style="color:var(--accent);">CI</span></span>
                    </div>
                    {{-- Badges flottants --}}
                    <div style="position:absolute;top:12px;right:-10px;background:#fff;border-radius:14px;padding:9px 14px;box-shadow:0 8px 28px rgba(0,0,0,.18);font-size:.78rem;font-weight:700;color:var(--primary);">
                        <i class="bi bi-shield-check me-1" style="color:#22c55e;"></i>Paiement SSL
                    </div>
                    <div style="position:absolute;bottom:24px;left:-10px;background:#fff;border-radius:14px;padding:9px 14px;box-shadow:0 8px 28px rgba(0,0,0,.18);font-size:.78rem;font-weight:700;color:var(--dark);">
                        <i class="bi bi-truck me-1" style="color:var(--primary);"></i>Livraison gratuite
                    </div>
                    <div style="position:absolute;top:50%;right:-24px;transform:translateY(-50%);background:linear-gradient(135deg,var(--primary),var(--primary-d));border-radius:12px;padding:10px 13px;box-shadow:0 6px 20px rgba(37,99,235,.35);font-size:.78rem;font-weight:700;color:#fff;text-align:center;">
                        <div style="font-size:1.2rem;font-weight:900;">⚡</div>
                        Stock live
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats rapides --}}
        <div class="row g-3 mt-5 pt-4" style="border-top:1px solid rgba(255,255,255,.1);">
            @php $heroStats = [
                ['icon'=>'bi-box-seam',  'val'=>'500+', 'label'=>'Produits'],
                ['icon'=>'bi-people',    'val'=>'10k+', 'label'=>'Clients'],
                ['icon'=>'bi-star-fill', 'val'=>'4.9★', 'label'=>'Note moyenne'],
                ['icon'=>'bi-headset',   'val'=>'7j/7', 'label'=>'Support'],
            ]; @endphp
            @foreach($heroStats as $s)
                <div class="col-6 col-md-3 text-center">
                    <i class="bi {{ $s['icon'] }}" style="font-size:1.2rem;color:var(--accent);opacity:.85;"></i>
                    <div style="font-size:1.5rem;font-weight:900;color:#fff;letter-spacing:-.04em;margin:.2rem 0;">{{ $s['val'] }}</div>
                    <div style="font-size:.76rem;color:rgba(255,255,255,.42);font-weight:500;">{{ $s['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════ CATÉGORIES ═══════════════════ --}}
@if($categories->isNotEmpty())
<section class="py-5" style="background:#fff;">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="section-title mb-0">
                <i class="bi bi-tags me-2" style="color:var(--accent)"></i>Catégories
            </h2>
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-right me-1"></i>Voir tout
            </a>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @php
                $catIcons = [
                    'Électronique'=>'bi-cpu','Mode'=>'bi-bag','Maison'=>'bi-house-heart',
                    'Sport'=>'bi-trophy','Beauté'=>'bi-stars','Livres'=>'bi-book','Jouets'=>'bi-controller'
                ];
                $catColors = [
                    'Électronique'=>'#2563eb','Mode'=>'#7c3aed','Maison'=>'#16a34a',
                    'Sport'=>'#f59e0b','Beauté'=>'#ec4899','Livres'=>'#0891b2','Jouets'=>'#ea580c'
                ];
            @endphp
            @foreach($categories as $category)
                @php $col = $catColors[$category] ?? '#2563eb'; @endphp
                <a href="{{ route('products.index', ['category' => $category]) }}"
                   class="cat-pill"
                   style="--cat-color:{{ $col }};">
                    <i class="bi {{ $catIcons[$category] ?? 'bi-tag' }}"></i>
                    {{ $category }}
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════ PRODUITS EN VEDETTE ═══════════════════ --}}
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-1">
                    <i class="bi bi-star-fill me-2" style="color:var(--accent)"></i>En vedette
                </h2>
                <p class="text-muted mb-0" style="font-size:.85rem;">Nos meilleures sélections du moment</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-grid me-1"></i>Voir tout
            </a>
        </div>

        @if($featuredProducts->isEmpty())
            <div class="text-center py-5">
                <div style="width:72px;height:72px;background:var(--primary-l);border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="bi bi-box-seam fs-2 text-primary"></i>
                </div>
                <p class="text-muted">Aucun produit disponible pour le moment.</p>
                @auth @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus"></i>Ajouter des produits
                    </a>
                @endif @endauth
            </div>
        @else
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
                @foreach($featuredProducts as $i => $product)
                    <div class="col fade-in-up" style="animation-delay:{{ $i * 0.06 }}s;">
                        @include('components.product-card', ['product' => $product])
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ═══════════════════ BANDE DE RÉASSURANCE ═══════════════════ --}}
<section class="py-5" style="background:#fff;border-top:1px solid var(--border);border-bottom:1px solid var(--border);">
    <div class="container">
        <div class="row g-4 text-center">
            @php $features = [
                ['icon'=>'bi-shield-check','color'=>'#22c55e','bg'=>'#f0fdf4','title'=>'Paiement sécurisé',   'desc'=>'Transactions chiffrées SSL 256-bit'],
                ['icon'=>'bi-truck',       'color'=>'#2563eb','bg'=>'#eff6ff','title'=>'Livraison gratuite',   'desc'=>'Sur toutes vos commandes'],
                ['icon'=>'bi-box-arrow-in-left','color'=>'#f59e0b','bg'=>'#fffbeb','title'=>'Retour facile',   'desc'=>'30 jours pour changer d\'avis'],
                ['icon'=>'bi-headset',     'color'=>'#7c3aed','bg'=>'#f5f3ff','title'=>'Support 7j/7',        'desc'=>'Une équipe toujours disponible'],
            ]; @endphp
            @foreach($features as $f)
                <div class="col-6 col-md-3">
                    <div class="reassurance-card p-4">
                        <div class="reas-icon mb-3" style="background:{{ $f['bg'] }};color:{{ $f['color'] }};">
                            <i class="bi {{ $f['icon'] }}"></i>
                        </div>
                        <div class="fw-700 mb-1" style="font-size:.92rem;color:var(--text);">{{ $f['title'] }}</div>
                        <div class="text-muted" style="font-size:.79rem;line-height:1.5;">{{ $f['desc'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════ CTA INSCRIPTION ═══════════════════ --}}
@guest
<section class="py-5">
    <div class="container">
        <div class="cta-banner text-center text-white">
            <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 60% 50%,rgba(245,158,11,.15),transparent 70%);border-radius:inherit;pointer-events:none;"></div>
            <div class="position-relative" style="z-index:1;">
                <div class="mb-3">
                    <span style="background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);color:var(--accent);padding:6px 16px;border-radius:50px;font-size:.78rem;font-weight:700;">
                        🎉 Rejoignez des milliers de clients satisfaits
                    </span>
                </div>
                <h2 class="fw-900 mb-3" style="font-size:2rem;letter-spacing:-.04em;">Prêt à commencer vos achats ?</h2>
                <p class="mb-4" style="opacity:.7;max-width:440px;margin:0 auto 1.5rem;font-size:.95rem;line-height:1.7;">
                    Créez votre compte gratuitement et profitez d'une expérience d'achat premium avec suivi de commandes en temps réel.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('register') }}" class="btn btn-warning btn-lg px-5 fw-700">
                        <i class="bi bi-person-plus"></i>Créer mon compte
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-lg px-5"
                       style="background:rgba(255,255,255,.1);color:#fff;border:1.5px solid rgba(255,255,255,.22);">
                        <i class="bi bi-grid"></i>Voir les produits
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endguest

@endsection

@push('styles')
<style>
    .cat-pill {
        display: inline-flex; align-items: center; gap: 7px;
        padding: .5rem 1.1rem;
        border-radius: 50px;
        border: 1.5px solid var(--border);
        background: #fff;
        color: var(--text);
        font-size: .85rem;
        font-weight: 600;
        text-decoration: none;
        transition: all .2s ease;
    }
    .cat-pill i { color: var(--cat-color); font-size: .9rem; }
    .cat-pill:hover {
        border-color: var(--cat-color);
        color: var(--cat-color);
        background: color-mix(in srgb, var(--cat-color) 8%, white);
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(0,0,0,.08);
    }

    .reassurance-card {
        border-radius: 16px;
        transition: all .22s ease;
    }
    .reassurance-card:hover { background: #f8fafc; transform: translateY(-3px); }
    .reas-icon {
        width: 56px; height: 56px; border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; margin: 0 auto;
    }

    .cta-banner {
        background: linear-gradient(135deg, var(--dark) 0%, var(--dark-2) 60%, #1e3a6e 100%);
        border-radius: 24px;
        padding: 72px 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(15,23,42,.3);
    }
</style>
@endpush
