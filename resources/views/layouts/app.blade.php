<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ShopCI') — Boutique en ligne</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary:      #2563eb;
            --primary-d:    #1d4ed8;
            --primary-l:    #dbeafe;
            --accent:       #f59e0b;
            --accent-d:     #d97706;
            --dark:         #0f172a;
            --dark-2:       #1e293b;
            --dark-3:       #334155;
            --light-bg:     #f8fafc;
            --card-bg:      #ffffff;
            --border:       #e2e8f0;
            --text:         #0f172a;
            --text-muted:   #64748b;
            --success:      #16a34a;
            --danger:       #dc2626;
            --radius:       16px;
            --radius-sm:    10px;
            --radius-xs:    8px;
            --shadow:       0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.08);
            --shadow-md:    0 4px 24px rgba(0,0,0,.10);
            --shadow-hover: 0 16px 48px rgba(37,99,235,.18);
            --transition:   all .22s cubic-bezier(.4,0,.2,1);
            --navbar-h:     68px;
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            background: var(--light-bg);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: var(--text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── SCROLLBAR ─── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary); }

        /* ═══════════════════════════════════════
           NAVBAR
        ═══════════════════════════════════════ */
        .navbar {
            background: rgba(15,23,42,.97) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 1px 0 rgba(255,255,255,.06), 0 4px 24px rgba(0,0,0,.3);
            padding: 0;
            height: var(--navbar-h);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .navbar .container { height: 100%; }

        .navbar-brand {
            font-weight: 900;
            font-size: 1.4rem;
            color: #fff !important;
            letter-spacing: -1.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            text-decoration: none;
        }
        .navbar-brand:hover { opacity: .88; }

        .brand-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--accent), #fb923c);
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1rem;
            box-shadow: 0 4px 12px rgba(245,158,11,.35);
            flex-shrink: 0;
        }
        .navbar-brand .brand-text { color: #fff; }
        .navbar-brand .brand-text span { color: var(--accent); }

        .nav-link {
            color: rgba(255,255,255,.68) !important;
            font-weight: 500;
            font-size: .875rem;
            padding: .45rem .85rem !important;
            border-radius: var(--radius-xs);
            transition: var(--transition);
            display: flex; align-items: center; gap: 5px;
        }
        .nav-link:hover, .nav-link.active {
            color: #fff !important;
            background: rgba(255,255,255,.09);
        }
        .nav-link.active { color: #fff !important; }

        /* Cart bubble */
        .cart-bubble {
            position: relative;
            display: flex; align-items: center; justify-content: center;
            width: 38px; height: 38px;
            border-radius: 10px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.1);
            color: rgba(255,255,255,.8) !important;
            transition: var(--transition);
        }
        .cart-bubble:hover { background: rgba(255,255,255,.14) !important; color: #fff !important; }
        .cart-count {
            position: absolute;
            top: -5px; right: -5px;
            background: var(--accent);
            color: var(--dark);
            font-size: .58rem;
            font-weight: 900;
            min-width: 17px; height: 17px;
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            padding: 0 4px;
            border: 2px solid rgba(15,23,42,.97);
        }

        /* Avatar */
        .user-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-d));
            display: flex; align-items: center; justify-content: center;
            font-size: .78rem; font-weight: 800; color: #fff;
            flex-shrink: 0;
        }

        /* Dropdown */
        .dropdown-menu {
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(0,0,0,.12), 0 2px 8px rgba(0,0,0,.06);
            padding: .5rem;
            min-width: 210px;
            animation: dropIn .18s ease;
        }
        @keyframes dropIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .dropdown-item {
            border-radius: 9px;
            font-size: .875rem;
            padding: .55rem .9rem;
            transition: var(--transition);
            font-weight: 500;
            display: flex; align-items: center; gap: 8px;
            color: var(--text);
        }
        .dropdown-item:hover { background: #f1f5f9; color: var(--primary); }
        .dropdown-item .di-icon {
            width: 26px; height: 26px;
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem;
            flex-shrink: 0;
        }
        .dropdown-divider { margin: .3rem 0; border-color: var(--border); }

        /* ═══════════════════════════════════════
           BUTTONS
        ═══════════════════════════════════════ */
        .btn {
            font-weight: 600;
            border-radius: 10px;
            transition: var(--transition);
            letter-spacing: -.01em;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(37,99,235,.25);
        }
        .btn-primary:hover, .btn-primary:focus {
            background: var(--primary-d);
            border-color: var(--primary-d);
            box-shadow: 0 4px 16px rgba(37,99,235,.4);
            transform: translateY(-1px);
        }
        .btn-warning {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff !important;
            box-shadow: 0 2px 8px rgba(245,158,11,.25);
            font-weight: 700;
        }
        .btn-warning:hover {
            background: var(--accent-d);
            border-color: var(--accent-d);
            color: #fff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(245,158,11,.35);
        }
        .btn-success {
            box-shadow: 0 2px 8px rgba(22,163,74,.25);
        }
        .btn-success:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(22,163,74,.35); }
        .btn-outline-primary { color: var(--primary); border-color: var(--primary); }
        .btn-outline-primary:hover { background: var(--primary); color: #fff; transform: translateY(-1px); }
        .btn-lg { padding: .72rem 1.7rem; font-size: 1rem; border-radius: 12px; }
        .btn-sm { padding: .36rem .8rem; font-size: .82rem; border-radius: 8px; }

        /* ═══════════════════════════════════════
           CARDS
        ═══════════════════════════════════════ */
        .card {
            border: 1px solid var(--border) !important;
            border-radius: var(--radius) !important;
            box-shadow: var(--shadow);
            transition: var(--transition);
            background: var(--card-bg);
        }

        /* ─── PRODUCT CARDS ─── */
        .product-card {
            border: 1px solid var(--border) !important;
            border-radius: var(--radius) !important;
            overflow: hidden;
            transition: var(--transition);
            box-shadow: var(--shadow);
            background: #fff;
            height: 100%;
            display: flex; flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(37,99,235,.15) !important;
        }
        .product-card .img-wrapper {
            overflow: hidden;
            position: relative;
            background: #f8fafc;
        }
        .product-card .card-img-top {
            height: 210px;
            object-fit: cover;
            transition: transform .45s cubic-bezier(.4,0,.2,1);
            width: 100%;
        }
        .product-card:hover .card-img-top { transform: scale(1.05); }
        .product-card .card-body { flex: 1; display: flex; flex-direction: column; }
        .product-card .price {
            font-size: 1.28rem;
            font-weight: 900;
            color: var(--primary);
            letter-spacing: -.04em;
        }

        /* Stock chips */
        .stock-chip {
            font-size: .68rem;
            font-weight: 700;
            padding: .22em .7em;
            border-radius: 20px;
            letter-spacing: .03em;
        }
        .stock-ok    { background: #dcfce7; color: #166534; }
        .stock-low   { background: #fef9c3; color: #854d0e; }
        .stock-empty { background: #fee2e2; color: #991b1b; }

        /* ═══════════════════════════════════════
           HERO
        ═══════════════════════════════════════ */
        .hero {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-2) 55%, #1e3a6e 100%);
            color: #fff;
            padding: 96px 0 80px;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(37,99,235,.15) 0%, transparent 70%);
            top: -250px; right: -150px;
            pointer-events: none;
        }
        .hero::after {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(245,158,11,.1) 0%, transparent 70%);
            bottom: -200px; left: -100px;
            pointer-events: none;
        }
        .hero h1 {
            font-size: clamp(2.2rem, 5vw, 3.4rem);
            font-weight: 900;
            letter-spacing: -.05em;
            line-height: 1.08;
        }
        .hero .accent { color: var(--accent); }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.16);
            color: rgba(255,255,255,.88);
            padding: 7px 16px;
            border-radius: 50px;
            font-size: .78rem; font-weight: 600;
            backdrop-filter: blur(10px);
        }

        /* ═══════════════════════════════════════
           SECTIONS & TYPOGRAPHY
        ═══════════════════════════════════════ */
        .section-title {
            font-weight: 800;
            font-size: 1.65rem;
            color: var(--text);
            letter-spacing: -.04em;
        }

        /* ═══════════════════════════════════════
           BADGES
        ═══════════════════════════════════════ */
        .badge { font-weight: 600; letter-spacing: .02em; }
        .badge-category {
            background: var(--primary-l);
            color: var(--primary);
            font-weight: 700;
        }
        .badge-admin {
            background: linear-gradient(135deg, var(--accent), #fb923c);
            color: #fff;
            font-size: .58rem;
            padding: .22em .6em;
        }

        /* ═══════════════════════════════════════
           FORMS
        ═══════════════════════════════════════ */
        .form-control, .form-select {
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: .62rem 1rem;
            font-size: .9rem;
            transition: var(--transition);
            background: #fff;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,.1);
            background: #fff;
        }
        .form-control-lg { padding: .78rem 1.1rem; font-size: 1rem; border-radius: 12px; }
        .input-group-text {
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            background: #f8fafc;
            color: var(--text-muted);
        }
        .form-label { font-weight: 600; font-size: .875rem; color: var(--dark-3); margin-bottom: .4rem; }

        /* ═══════════════════════════════════════
           ALERTS
        ═══════════════════════════════════════ */
        .alert {
            border: none;
            border-radius: var(--radius-sm);
            font-size: .88rem;
            font-weight: 500;
        }
        .alert-success { background: #f0fdf4; color: #166534; border-left: 4px solid #22c55e; }
        .alert-danger  { background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; }
        .alert-info    { background: #eff6ff; color: #1e40af; border-left: 4px solid #3b82f6; }
        .alert-warning { background: #fffbeb; color: #854d0e; border-left: 4px solid #f59e0b; }

        /* ═══════════════════════════════════════
           BREADCRUMB
        ═══════════════════════════════════════ */
        .breadcrumb { font-size: .82rem; margin-bottom: 0; }
        .breadcrumb-item a { color: var(--primary); text-decoration: none; font-weight: 500; }
        .breadcrumb-item a:hover { text-decoration: underline; }
        .breadcrumb-item.active { color: var(--text-muted); }
        .breadcrumb-item + .breadcrumb-item::before { color: #cbd5e1; }

        /* ═══════════════════════════════════════
           FOOTER
        ═══════════════════════════════════════ */
        footer {
            background: var(--dark);
            color: rgba(255,255,255,.55);
            padding: 72px 0 28px;
            margin-top: 100px;
            position: relative;
        }
        footer::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(37,99,235,.4), rgba(245,158,11,.4), transparent);
        }
        footer .footer-brand {
            font-size: 1.35rem; font-weight: 900;
            color: #fff; letter-spacing: -.04em;
            display: flex; align-items: center; gap: 8px;
        }
        footer .footer-brand .brand-icon-sm {
            width: 30px; height: 30px;
            background: linear-gradient(135deg, var(--accent), #fb923c);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; color: #fff;
        }
        footer .footer-brand span { color: var(--accent); }
        footer a { color: rgba(255,255,255,.48); text-decoration: none; transition: var(--transition); font-size: .875rem; }
        footer a:hover { color: #fff; }
        footer .footer-title { color: #fff; font-weight: 700; font-size: .8rem; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 1rem; }
        footer hr { border-color: rgba(255,255,255,.08); }
        footer .footer-link-list li { margin-bottom: .45rem; }

        /* ═══════════════════════════════════════
           QUANTITY STEPPER
        ═══════════════════════════════════════ */
        .qty-stepper {
            display: inline-flex;
            align-items: center;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }
        .qty-stepper button {
            width: 40px; height: 40px;
            border: none; background: transparent;
            color: var(--primary); font-size: 1.1rem; font-weight: 700;
            cursor: pointer; transition: var(--transition);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .qty-stepper button:hover:not(:disabled) { background: var(--primary-l); }
        .qty-stepper button:disabled { color: #cbd5e1; cursor: not-allowed; }
        .qty-stepper input {
            width: 52px; height: 40px;
            border: none; border-left: 1.5px solid var(--border); border-right: 1.5px solid var(--border);
            text-align: center; font-weight: 700; font-size: .95rem;
            color: var(--text);
            background: transparent;
            -moz-appearance: textfield;
        }
        .qty-stepper input:focus { outline: none; }
        .qty-stepper input::-webkit-outer-spin-button,
        .qty-stepper input::-webkit-inner-spin-button { -webkit-appearance: none; }

        /* ─── STOCK BAR ─── */
        .stock-bar { height: 5px; border-radius: 3px; background: var(--border); overflow: hidden; }
        .stock-bar-fill { height: 100%; border-radius: 3px; transition: width .8s ease; }

        /* ─── PRICE PREVIEW ─── */
        .price-preview {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border: 1.5px solid rgba(37,99,235,.18);
            border-radius: var(--radius-sm);
            padding: 1.1rem 1.3rem;
        }
        .price-preview .total-price {
            font-size: 1.7rem;
            font-weight: 900;
            color: var(--primary);
            letter-spacing: -.05em;
        }

        /* ═══════════════════════════════════════
           PAGINATION
        ═══════════════════════════════════════ */
        .pagination .page-link {
            border-radius: 9px !important;
            margin: 0 2px;
            border: 1.5px solid var(--border);
            color: var(--text);
            font-weight: 600;
            font-size: .875rem;
            padding: .45rem .75rem;
            transition: var(--transition);
        }
        .pagination .page-link:hover { background: var(--primary-l); border-color: var(--primary); color: var(--primary); }
        .pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(37,99,235,.3);
        }

        /* ═══════════════════════════════════════
           UTILS
        ═══════════════════════════════════════ */
        .text-primary { color: var(--primary) !important; }
        .bg-primary   { background: var(--primary) !important; }
        .border-primary { border-color: var(--primary) !important; }
        .rounded-xl  { border-radius: var(--radius) !important; }
        .rounded-2xl { border-radius: 20px !important; }
        .shadow-soft { box-shadow: var(--shadow); }
        .fw-700 { font-weight: 700; }
        .fw-800 { font-weight: 800; }
        .fw-900 { font-weight: 900; }

        /* ═══════════════════════════════════════
           ANIMATIONS
        ═══════════════════════════════════════ */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(22px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up   { animation: fadeInUp .5s ease forwards; }
        .fade-in-up-1 { animation: fadeInUp .5s .08s ease both; }
        .fade-in-up-2 { animation: fadeInUp .5s .18s ease both; }
        .fade-in-up-3 { animation: fadeInUp .5s .28s ease both; }

        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50%       { transform: scale(1.5); opacity: .65; }
        }
        .live-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #22c55e;
            animation: pulse-dot 1.8s ease-in-out infinite;
            display: inline-block;
            box-shadow: 0 0 6px rgba(34,197,94,.5);
        }

        @keyframes shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .shimmer {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        /* ═══════════════════════════════════════
           SECTION DIVIDER
        ═══════════════════════════════════════ */
        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border), transparent);
            margin: 0;
        }

        /* ═══════════════════════════════════════
           TOAST / FLASH
        ═══════════════════════════════════════ */
        .flash-container {
            position: fixed;
            top: calc(var(--navbar-h) + 12px);
            right: 16px;
            z-index: 1090;
            width: 340px;
            max-width: calc(100vw - 32px);
        }
        .flash-toast {
            border-radius: 12px;
            padding: .9rem 1.1rem;
            font-size: .875rem;
            font-weight: 500;
            display: flex; align-items: flex-start; gap: 10px;
            box-shadow: 0 8px 32px rgba(0,0,0,.14);
            border: none;
            animation: slideInRight .3s ease;
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(20px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .flash-toast .toast-icon {
            width: 28px; height: 28px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem; flex-shrink: 0; margin-top: -1px;
        }
        .flash-toast.success { background: #f0fdf4; color: #166534; }
        .flash-toast.success .toast-icon { background: #dcfce7; color: #16a34a; }
        .flash-toast.error   { background: #fef2f2; color: #991b1b; }
        .flash-toast.error   .toast-icon { background: #fee2e2; color: #dc2626; }
    </style>

    @stack('styles')
</head>
<body>

{{-- ═══════════════════ NAVIGATION ═══════════════════ --}}
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">

        {{-- Brand --}}
        <a class="navbar-brand" href="{{ route('home') }}">
            <div class="brand-icon">
                <i class="bi bi-bag-heart-fill"></i>
            </div>
            <span class="brand-text">Shop<span>CI</span></span>
        </a>

        {{-- Toggler mobile --}}
        <button class="navbar-toggler border-0 p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"
                style="background:rgba(255,255,255,.07);border-radius:9px;">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto ms-4 gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house"></i>Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                        <i class="bi bi-grid"></i>Produits
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center gap-2">
                @auth
                    {{-- Panier --}}
                    <li class="nav-item">
                        @php
                            $cartCount = \App\Models\Cart::getOrCreateActive(auth()->id())
                                ->items()->sum('quantity');
                        @endphp
                        <a class="nav-link cart-bubble" href="{{ route('cart.index') }}" title="Mon panier">
                            <i class="bi bi-bag fs-5"></i>
                            @if($cartCount > 0)
                                <span class="cart-count">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                            @endif
                        </a>
                    </li>

                    {{-- Dropdown utilisateur --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 px-2" href="#" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="d-none d-lg-inline fw-600" style="font-size:.875rem;color:#fff;">
                                {{ Str::limit(auth()->user()->name, 14) }}
                            </span>
                            @if(auth()->user()->isAdmin())
                                <span class="badge badge-admin rounded-pill">Admin</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end mt-2">
                            @if(auth()->user()->isAdmin())
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <div class="di-icon" style="background:#dbeafe;color:#2563eb;">
                                            <i class="bi bi-speedometer2"></i>
                                        </div>
                                        Tableau de bord
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li>
                                <a class="dropdown-item" href="{{ route('orders.index') }}">
                                    <div class="di-icon" style="background:#dcfce7;color:#16a34a;">
                                        <i class="bi bi-bag-check"></i>
                                    </div>
                                    Mes commandes
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item" style="color:#dc2626;">
                                        <div class="di-icon" style="background:#fee2e2;color:#dc2626;">
                                            <i class="bi bi-box-arrow-right"></i>
                                        </div>
                                        Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i>Connexion
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-warning btn-sm fw-700 px-4" href="{{ route('register') }}">
                            S'inscrire
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- ═══════════════════ FLASH MESSAGES (TOAST) ═══════════════════ --}}
<div class="flash-container">
    @if(session('success'))
        <div class="flash-toast success alert-dismissible fade show" role="alert">
            <div class="toast-icon"><i class="bi bi-check-lg"></i></div>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-sm ms-2" data-bs-dismiss="alert" style="font-size:.7rem;"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="flash-toast error alert-dismissible fade show" role="alert">
            <div class="toast-icon"><i class="bi bi-exclamation-lg"></i></div>
            <div class="flex-grow-1">{{ session('error') }}</div>
            <button type="button" class="btn-close btn-close-sm ms-2" data-bs-dismiss="alert" style="font-size:.7rem;"></button>
        </div>
    @endif
</div>

{{-- ═══════════════════ CONTENU PRINCIPAL ═══════════════════ --}}
<main>
    @yield('content')
</main>

{{-- ═══════════════════ FOOTER ═══════════════════ --}}
<footer>
    <div class="container">
        <div class="row g-5 mb-5">
            <div class="col-lg-4">
                <div class="footer-brand mb-3">
                    <div class="brand-icon-sm"><i class="bi bi-bag-heart-fill"></i></div>
                    Shop<span>CI</span>
                </div>
                <p class="small mb-4" style="line-height:1.9;color:rgba(255,255,255,.45);">
                    Votre boutique en ligne moderne et sécurisée.<br>
                    Des milliers de produits livrés directement chez vous,<br>
                    avec paiement 100% sécurisé.
                </p>
                <div class="d-flex align-items-center gap-2">
                    <span class="live-dot"></span>
                    <span style="font-size:.78rem;color:rgba(255,255,255,.4);">Boutique active · Paiement sécurisé</span>
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <div class="footer-title">Navigation</div>
                <ul class="list-unstyled footer-link-list">
                    <li><a href="{{ route('home') }}"><i class="bi bi-chevron-right me-1" style="font-size:.65rem;"></i>Accueil</a></li>
                    <li><a href="{{ route('products.index') }}"><i class="bi bi-chevron-right me-1" style="font-size:.65rem;"></i>Catalogue</a></li>
                    @auth
                        <li><a href="{{ route('orders.index') }}"><i class="bi bi-chevron-right me-1" style="font-size:.65rem;"></i>Mes commandes</a></li>
                        <li><a href="{{ route('cart.index') }}"><i class="bi bi-chevron-right me-1" style="font-size:.65rem;"></i>Mon panier</a></li>
                    @endauth
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <div class="footer-title">Mon compte</div>
                <ul class="list-unstyled footer-link-list">
                    @guest
                        <li><a href="{{ route('login') }}"><i class="bi bi-chevron-right me-1" style="font-size:.65rem;"></i>Se connecter</a></li>
                        <li><a href="{{ route('register') }}"><i class="bi bi-chevron-right me-1" style="font-size:.65rem;"></i>S'inscrire</a></li>
                    @endguest
                    @auth
                        @if(auth()->user()->isAdmin())
                            <li><a href="{{ route('admin.dashboard') }}"><i class="bi bi-chevron-right me-1" style="font-size:.65rem;"></i>Administration</a></li>
                        @endif
                        <li><a href="{{ route('orders.index') }}"><i class="bi bi-chevron-right me-1" style="font-size:.65rem;"></i>Commandes</a></li>
                    @endauth
                </ul>
            </div>

            <div class="col-lg-4">
                <div class="footer-title">Paiement sécurisé</div>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.75);padding:.4rem .9rem;border-radius:8px;font-size:.78rem;font-weight:600;">
                        <i class="bi bi-shield-lock me-1" style="color:#22c55e;"></i>SSL 256-bit
                    </span>
                    <span style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.75);padding:.4rem .9rem;border-radius:8px;font-size:.78rem;font-weight:600;">
                        <i class="bi bi-credit-card me-1" style="color:var(--accent);"></i>Carte bancaire
                    </span>
                    <span style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.75);padding:.4rem .9rem;border-radius:8px;font-size:.78rem;font-weight:600;">
                        <i class="bi bi-lock me-1" style="color:var(--primary);"></i>Données chiffrées
                    </span>
                </div>
                <p style="font-size:.78rem;color:rgba(255,255,255,.3);line-height:1.7;">
                    Vos données bancaires ne sont jamais stockées intégralement sur nos serveurs.
                </p>
            </div>
        </div>

        <hr>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 pt-1 pb-2">
            <p class="small mb-0" style="color:rgba(255,255,255,.28);">
                © {{ date('Y') }} <strong style="color:rgba(255,255,255,.5);">ShopCI</strong> — Tous droits réservés.
            </p>
            <p class="small mb-0" style="color:rgba(255,255,255,.22);">
                Fait avec <i class="bi bi-heart-fill" style="color:#ef4444;font-size:.7rem;"></i> Laravel
            </p>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Auto-dismiss flash toasts after 4s
    document.querySelectorAll('.flash-toast').forEach(el => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            if (bsAlert) bsAlert.close();
        }, 4500);
    });
</script>

@stack('scripts')
</body>
</html>
