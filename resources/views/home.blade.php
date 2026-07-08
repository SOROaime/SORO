@extends('layouts.app')
@section('title', 'Accueil')
@section('seo_title', 'ShopCI — Boutique en ligne Cote d\'Ivoire | Livraison gratuite')
@section('seo_description', 'Achetez en ligne en Cote d\'Ivoire sur ShopCI. Plus de 500 produits, livraison gratuite, paiement securise via Orange Money, MTN Money, Wave. Paiement en 2x, 3x, 4x sans frais.')

@section('content')

{{-- ══════════════════════════════════════
     HERO
══════════════════════════════════════ --}}
<section class="hero">
    {{-- Particules décoratives --}}
    <div class="hero-particles" aria-hidden="true">
        <div class="hero-particle" style="width:80px;height:80px;left:8%;animation-duration:18s;animation-delay:0s;"></div>
        <div class="hero-particle" style="width:50px;height:50px;left:22%;animation-duration:14s;animation-delay:3s;"></div>
        <div class="hero-particle" style="width:120px;height:120px;left:60%;animation-duration:22s;animation-delay:1s;"></div>
        <div class="hero-particle" style="width:40px;height:40px;left:80%;animation-duration:16s;animation-delay:5s;"></div>
        <div class="hero-particle" style="width:70px;height:70px;left:42%;animation-duration:20s;animation-delay:2s;"></div>
    </div>

    <div class="container position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">

                <div class="hero-badge mb-4 fade-in-up">
                    <div class="live-dot"></div>
                    Boutique en ligne · Livraison gratuite
                </div>

                <h1 class="fade-in-up-1 mb-4">
                    La boutique qui<br>
                    vous <span class="accent">simplifie</span><br>
                    la vie.
                </h1>

                <p class="fade-in-up-2 mb-5"
                   style="font-size:1.08rem;color:rgba(255,255,255,.65);
                          line-height:1.8;max-width:480px;">
                    Des milliers de produits de qualité, livrés rapidement.
                    Commandez en toute confiance avec notre protection acheteur.
                </p>

                <div class="d-flex gap-3 flex-wrap fade-in-up-3">
                    <a href="{{ route('products.index') }}"
                       class="btn btn-accent btn-lg fw-700">
                        <i class="bi bi-grid-3x3-gap"></i>Explorer le catalogue
                    </a>
                    @guest
                        <a href="{{ route('register') }}"
                           class="btn btn-lg fw-700"
                           style="background:rgba(255,255,255,.1);
                                  border:1.5px solid rgba(255,255,255,.2);
                                  color:#fff;border-radius:13px;">
                            <i class="bi bi-person-plus"></i>Créer un compte
                        </a>
                    @endguest
                </div>
            </div>

            <div class="col-lg-5 fade-in-up-2 d-none d-lg-flex justify-content-end">
                <div class="hero-visual">
                    {{-- Photo de deux personnes heureuses --}}
                    <img src="{{ asset('images/hero-people.jpg') }}"
                         alt="Clients satisfaits"
                         class="hero-people-img"
                         onerror="this.src='https://picsum.photos/380/480?grayscale=false&random=42'">
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row g-3 mt-5 pt-3 fade-in-up-4">
            @foreach([
                ['bi-box-seam',  '500+',  'Produits',         '#dbeafe', '#2563eb'],
                ['bi-people',    '10k+',  'Clients',          '#dcfce7', '#16a34a'],
                ['bi-star-fill', '4.9★',  'Note moyenne',     '#fef3c7', '#d97706'],
                ['bi-headset',   '7j/7',  'Support client',   '#ede9fe', '#7c3aed'],
            ] as $s)
            <div class="col-6 col-md-3">
                <div class="hero-stat">
                    <div class="hero-stat-icon" style="background:{{ $s[3] }};color:{{ $s[4] }};">
                        <i class="bi {{ $s[0] }}"></i>
                    </div>
                    <div class="hero-stat-val">{{ $s[1] }}</div>
                    <div class="hero-stat-lbl">{{ $s[2] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════
     CATÉGORIES
══════════════════════════════════════ --}}
@if(isset($categories) && $categories->isNotEmpty())
<section class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="section-label mb-1">Collections</div>
            <h2 class="section-title mb-0">Parcourir par catégorie</h2>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @php
        $catIcons = [
            'Électronique'=>'bi-cpu','Mode'=>'bi-bag','Maison'=>'bi-house',
            'Sport'=>'bi-bicycle','Beauté'=>'bi-stars','Livres'=>'bi-book',
            'Jouets'=>'bi-controller','Alimentation'=>'bi-basket',
        ];
        $catColors = ['#2563eb','#7c3aed','#16a34a','#dc2626','#d97706','#0891b2','#db2777','#059669'];
        @endphp
        @foreach($categories as $i => $cat)
            @php
                $icon  = $catIcons[$cat] ?? 'bi-tag';
                $color = $catColors[$i % count($catColors)];
            @endphp
            <a href="{{ route('products.index', ['category' => $cat]) }}"
               class="cat-pill">
                <i class="bi {{ $icon }}" style="color:{{ $color }};"></i>
                {{ $cat }}
            </a>
        @endforeach
    </div>
</section>
<div class="sep"></div>
@endif

{{-- ══════════════════════════════════════
     PRODUITS EN VEDETTE
══════════════════════════════════════ --}}
<section class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="section-label mb-1">Sélection</div>
            <h2 class="section-title mb-0">
                <i class="bi bi-star me-2" style="color:var(--accent)"></i>En vedette
            </h2>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-grid-3x3-gap"></i>Tout voir
        </a>
    </div>

    @if(isset($featuredProducts) && $featuredProducts->isNotEmpty())
        <div class="row row-cols-2 row-cols-sm-2 row-cols-lg-4 g-2 g-sm-4">
            @foreach($featuredProducts as $product)
                <div class="col fade-in-up">
                    @include('components.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5"
             style="background:#fff;border-radius:var(--radius);
                    border:1.5px dashed var(--border);">
            <i class="bi bi-box-seam fs-1 text-muted mb-3 d-block"></i>
            <p class="text-muted mb-3">Aucun produit en vedette pour le moment.</p>
            @auth @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i>Ajouter un produit
                </a>
            @endif @endauth
        </div>
    @endif
</section>

{{-- ══════════════════════════════════════
     RÉASSURANCE
══════════════════════════════════════ --}}
<section style="background:#fff;border-top:1px solid var(--border);
                border-bottom:1px solid var(--border);padding:3.5rem 0;">
    <div class="container">
        <div class="row g-4 text-center">
            @foreach([
                ['bi-shield-lock-fill','#2563eb','#eff6ff','Paiement sécurisé',  'Transactions protégées SSL'],
                ['bi-truck-front-fill','#16a34a','#f0fdf4','Livraison gratuite', 'Pour toutes les commandes'],
                ['bi-arrow-repeat',    '#7c3aed','#f5f3ff','Retour sous 30j',   'Satisfaction garantie'],
                ['bi-headset',         '#d97706','#fffbeb','Support 7j/7',       'Réponse en moins de 2h'],
            ] as $r)
            <div class="col-6 col-md-3">
                <div class="reassurance-item">
                    <div class="reassurance-icon" style="background:{{ $r[2] }};color:{{ $r[1] }};">
                        <i class="bi {{ $r[0] }}"></i>
                    </div>
                    <h6 class="fw-800 mb-1" style="font-size:.9rem;">{{ $r[3] }}</h6>
                    <p class="text-muted mb-0" style="font-size:.78rem;">{{ $r[4] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════
     CTA INSCRIPTION (guests)
══════════════════════════════════════ --}}
@guest
<section class="cta-section">
    <div class="container text-center">
        <div class="cta-badge mb-3">
            <i class="bi bi-stars me-2"></i>Rejoignez-nous
        </div>
        <h2 class="fw-900 mb-3"
            style="font-size:clamp(1.6rem,3.5vw,2.4rem);letter-spacing:-.045em;color:#fff;">
            Déjà 10 000+ clients nous font confiance
        </h2>
        <p class="mb-5" style="color:rgba(255,255,255,.65);font-size:1rem;max-width:440px;margin:0 auto 2rem;">
            Créez votre compte gratuitement et profitez de toutes nos offres.
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('register') }}" class="btn btn-accent btn-lg fw-700">
                <i class="bi bi-person-plus"></i>Créer un compte gratuit
            </a>
            <a href="{{ route('login') }}"
               class="btn btn-lg fw-700"
               style="background:rgba(255,255,255,.12);border:1.5px solid rgba(255,255,255,.2);
                      color:#fff;border-radius:13px;">
                <i class="bi bi-box-arrow-in-right"></i>Se connecter
            </a>
        </div>
    </div>
</section>
@endguest

@push('styles')
<style>
    /* ── Hero Visual ── */
    .hero-visual {
        position: relative;
        z-index: 1;
        width: 380px;
        height: 480px;
    }
    .hero-people-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 28px;
        box-shadow: 0 24px 60px rgba(0,0,0,.35);
        display: block;
        animation: floatPhoto 4s ease-in-out infinite;
    }
    @keyframes floatPhoto {
        0%   { transform: translateY(0px); }
        50%  { transform: translateY(-14px); }
        100% { transform: translateY(0px); }
    }

    /* ── Hero Stats ── */
    .hero-stat {
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 14px;
        padding: 1rem 1.1rem;
        display: flex; flex-direction: column; align-items: center; gap: 4px;
        text-align: center;
        backdrop-filter: blur(10px);
        transition: transform .2s;
    }
    .hero-stat:hover { transform: translateY(-3px); }
    .hero-stat-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: .95rem; margin-bottom: 4px;
    }
    .hero-stat-val {
        font-size: 1.35rem; font-weight: 900;
        color: #fff; letter-spacing: -.04em; line-height: 1;
    }
    .hero-stat-lbl {
        font-size: .73rem; color: rgba(255,255,255,.55); font-weight: 500;
    }

    /* ── Category pills ── */
    .cat-pill {
        display: inline-flex; align-items: center; gap: 7px;
        background: #fff;
        border: 1.5px solid var(--border);
        border-radius: 50px;
        padding: .52rem 1.1rem;
        font-size: .84rem; font-weight: 600;
        color: var(--text);
        text-decoration: none;
        transition: var(--transition);
        white-space: nowrap;
    }
    .cat-pill:hover {
        border-color: var(--primary);
        background: var(--primary-xl);
        color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(37,99,235,.12);
    }

    /* ── Réassurance ── */
    .reassurance-item {
        padding: 1rem 0.5rem;
        transition: transform .2s;
    }
    .reassurance-item:hover { transform: translateY(-4px); }
    .reassurance-icon {
        width: 56px; height: 56px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.35rem;
        margin: 0 auto 1rem;
        transition: transform .2s;
    }
    .reassurance-item:hover .reassurance-icon { transform: scale(1.08); }

    /* ── CTA section ── */
    .cta-section {
        background: linear-gradient(135deg, var(--dark) 0%, var(--dark-2) 55%, #1a3060 100%);
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }
    .cta-section::before {
        content: '';
        position: absolute;
        width: 600px; height: 600px;
        background: radial-gradient(circle, rgba(37,99,235,.15) 0%, transparent 70%);
        top: -200px; right: -100px;
        pointer-events: none;
    }
    .cta-badge {
        display: inline-flex; align-items: center;
        background: rgba(245,158,11,.15);
        border: 1px solid rgba(245,158,11,.3);
        color: var(--accent);
        padding: .45rem 1rem;
        border-radius: 50px;
        font-size: .78rem; font-weight: 700;
        letter-spacing: .04em;
    }
</style>
@endpush
@endsection
