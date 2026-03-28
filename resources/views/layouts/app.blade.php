<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ShopLaravel') — Boutique en ligne</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary:    #2563eb;
            --primary-d:  #1d4ed8;
            --accent:     #f59e0b;
            --dark:       #1e293b;
            --light-bg:   #f8fafc;
        }

        body { background-color: var(--light-bg); font-family: 'Segoe UI', sans-serif; }

        /* ---- NAVBAR ---- */
        .navbar { background: var(--dark) !important; box-shadow: 0 2px 10px rgba(0,0,0,.3); }
        .navbar-brand { font-weight: 800; font-size: 1.5rem; color: #fff !important; letter-spacing: -0.5px; }
        .navbar-brand span { color: var(--accent); }
        .nav-link { color: rgba(255,255,255,.85) !important; font-weight: 500; transition: color .2s; }
        .nav-link:hover, .nav-link.active { color: #fff !important; }
        .cart-badge { background: var(--accent); color: #000; font-size: .65rem; font-weight: 700; }

        /* ---- BOUTONS ---- */
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-d); border-color: var(--primary-d); }
        .btn-outline-primary { color: var(--primary); border-color: var(--primary); }
        .btn-outline-primary:hover { background: var(--primary); color: #fff; }

        /* ---- CARDS PRODUITS ---- */
        .product-card {
            border: none; border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,.08);
            transition: transform .25s, box-shadow .25s;
            overflow: hidden;
        }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.14); }
        .product-card .card-img-top { height: 220px; object-fit: cover; }
        .product-card .price { font-size: 1.3rem; font-weight: 700; color: var(--primary); }
        .product-card .stock-badge { font-size: .75rem; }

        /* ---- SECTION HÉRO ---- */
        .hero {
            background: linear-gradient(135deg, var(--dark) 0%, #334155 100%);
            color: #fff; padding: 80px 0;
        }
        .hero h1 { font-size: 3rem; font-weight: 800; }
        .hero .accent { color: var(--accent); }

        /* ---- SIDEBAR ADMIN ---- */
        .admin-sidebar {
            background: var(--dark); min-height: 100vh;
            width: 260px; position: fixed; left: 0; top: 0; z-index: 1000;
            padding-top: 60px;
        }
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,.7) !important;
            padding: .6rem 1.5rem;
            border-radius: 0 24px 24px 0;
            margin: 2px 8px 2px 0;
            transition: all .2s;
        }
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            background: rgba(255,255,255,.1);
            color: #fff !important;
        }
        .admin-sidebar .nav-link i { width: 22px; }
        .admin-content { margin-left: 260px; padding: 80px 30px 30px; }

        /* ---- FOOTER ---- */
        footer { background: var(--dark); color: rgba(255,255,255,.7); padding: 40px 0 20px; margin-top: 80px; }
        footer a { color: rgba(255,255,255,.6); text-decoration: none; transition: color .2s; }
        footer a:hover { color: #fff; }

        /* ---- ALERTS ---- */
        .alert { border: none; border-radius: 10px; }

        /* ---- UTILITAIRES ---- */
        .section-title { font-weight: 700; font-size: 1.8rem; color: var(--dark); }
        .badge-category { background: #e0e7ff; color: var(--primary); font-weight: 600; }
    </style>

    @stack('styles')
</head>
<body>

{{-- ===================== NAVIGATION ===================== --}}
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-bag-heart-fill me-1"></i>Shop<span>Laravel</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house me-1"></i>Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                        <i class="bi bi-grid me-1"></i>Produits
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center gap-2">
                @auth
                    {{-- Panier --}}
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                            <i class="bi bi-cart3 fs-5"></i>
                            @php
                                $cartCount = \App\Models\Cart::getOrCreateActive(auth()->id())
                                    ->items()->sum('quantity');
                            @endphp
                            @if($cartCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill cart-badge">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>
                    </li>

                    {{-- Dropdown utilisateur --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                            @if(auth()->user()->isAdmin())
                                <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem">Admin</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if(auth()->user()->isAdmin())
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Tableau de bord
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li>
                                <a class="dropdown-item" href="{{ route('orders.index') }}">
                                    <i class="bi bi-bag-check me-2"></i>Mes commandes
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Connexion
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-warning btn-sm fw-bold px-3" href="{{ route('register') }}">
                            S'inscrire
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- ===================== FLASH MESSAGES ===================== --}}
<div class="container mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

{{-- ===================== CONTENU PRINCIPAL ===================== --}}
<main>
    @yield('content')
</main>

{{-- ===================== FOOTER ===================== --}}
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold text-white mb-3">
                    <i class="bi bi-bag-heart-fill me-2"></i>ShopLaravel
                </h5>
                <p class="small">Votre boutique en ligne moderne et sécurisée. Des milliers de produits livrés chez vous.</p>
            </div>
            <div class="col-md-4 mb-4">
                <h6 class="fw-bold text-white mb-3">Navigation</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('home') }}"><i class="bi bi-chevron-right me-1"></i>Accueil</a></li>
                    <li><a href="{{ route('products.index') }}"><i class="bi bi-chevron-right me-1"></i>Produits</a></li>
                    @auth
                        <li><a href="{{ route('orders.index') }}"><i class="bi bi-chevron-right me-1"></i>Mes commandes</a></li>
                    @endauth
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h6 class="fw-bold text-white mb-3">Paiement sécurisé</h6>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-secondary"><i class="bi bi-shield-lock me-1"></i>SSL</span>
                    <span class="badge bg-secondary"><i class="bi bi-credit-card me-1"></i>Carte</span>
                    <span class="badge bg-secondary"><i class="bi bi-lock me-1"></i>Sécurisé</span>
                </div>
            </div>
        </div>
        <hr class="border-secondary">
        <p class="text-center small mb-0">© {{ date('Y') }} ShopLaravel — Tous droits réservés.</p>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>
