<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ── SEO ── --}}
    @php
        $suffix    = ' — Boutique en ligne Cote d\'Ivoire';
        $pageTitle = View::hasSection('seo_title')
            ? html_entity_decode(View::yieldContent('seo_title'), ENT_QUOTES, 'UTF-8')
            : (View::hasSection('title')
                ? html_entity_decode(View::yieldContent('title'), ENT_QUOTES, 'UTF-8') . $suffix
                : 'ShopCI' . $suffix);
        $seoDesc = View::hasSection('seo_description')
            ? html_entity_decode(View::yieldContent('seo_description'), ENT_QUOTES, 'UTF-8')
            : 'ShopCI - Boutique en ligne en Cote d\'Ivoire. Livraison gratuite, paiement securise.';
        $ogType  = View::hasSection('og_type')  ? View::yieldContent('og_type')  : 'website';
        $ogImage = View::hasSection('og_image') ? View::yieldContent('og_image') : asset('images/og-default.jpg');
        $ldJson  = json_encode([
            '@context'     => 'https://schema.org',
            '@type'        => 'Organization',
            'name'         => 'ShopCI',
            'url'          => config('app.url'),
            'logo'         => asset('images/logo.png'),
            'description'  => 'Boutique en ligne en Cote d\'Ivoire - livraison gratuite, paiement securise.',
            'contactPoint' => [
                '@type'             => 'ContactPoint',
                'email'             => config('mail.from.address'),
                'contactType'       => 'customer service',
                'availableLanguage' => 'French',
            ],
            'areaServed' => 'CI',
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $seoDesc }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- ── Open Graph ── --}}
    <meta property="og:type"        content="{{ $ogType }}">
    <meta property="og:title"       content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $seoDesc }}">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:image"       content="{{ $ogImage }}">
    <meta property="og:site_name"   content="ShopCI">
    <meta property="og:locale"      content="fr_CI">

    {{-- ── Twitter Card ── --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $seoDesc }}">
    <meta name="twitter:image"       content="{{ $ogImage }}">

    {{-- ── JSON-LD Organisation ── --}}
    <script type="application/ld+json">{!! $ldJson !!}</script>

    @stack('seo_schema')

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ══════════════════════════════════════════════
           DESIGN TOKENS — palette inchangée
        ══════════════════════════════════════════════ */
        :root {
            --primary:      #2563eb;
            --primary-d:    #1d4ed8;
            --primary-l:    #dbeafe;
            --primary-xl:   #eff6ff;
            --accent:       #f59e0b;
            --accent-d:     #d97706;
            --accent-l:     #fef3c7;
            --dark:         #0f172a;
            --dark-2:       #1e293b;
            --dark-3:       #334155;
            --light-bg:     #f8fafc;
            --card-bg:      #ffffff;
            --border:       #e2e8f0;
            --border-2:     #f1f5f9;
            --text:         #0f172a;
            --text-muted:   #64748b;
            --text-light:   #94a3b8;
            --success:      #16a34a;
            --success-l:    #dcfce7;
            --danger:       #dc2626;
            --danger-l:     #fee2e2;
            --warning:      #d97706;
            --radius:       16px;
            --radius-sm:    10px;
            --radius-xs:    8px;
            --radius-lg:    20px;
            --radius-xl:    24px;
            --shadow-xs:    0 1px 2px rgba(0,0,0,.05);
            --shadow:       0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.07);
            --shadow-md:    0 4px 24px rgba(0,0,0,.09);
            --shadow-lg:    0 8px 40px rgba(0,0,0,.12);
            --shadow-blue:  0 8px 32px rgba(37,99,235,.20);
            --shadow-hover: 0 20px 48px rgba(37,99,235,.16);
            --transition:   all .22s cubic-bezier(.4,0,.2,1);
            --navbar-h:     68px;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            background: var(--light-bg);
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            color: var(--text);
            line-height: 1.65;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
        }

        /* ─── Scrollbar fine ─── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary); }

        /* ══════════════════════════════════════════════
           NAVBAR
        ══════════════════════════════════════════════ */
        .navbar {
            background: rgba(10,18,36,.96) !important;
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            box-shadow: 0 1px 0 rgba(255,255,255,.06), 0 4px 32px rgba(0,0,0,.28);
            padding: 0;
            height: var(--navbar-h);
            position: sticky;
            top: 0;
            z-index: 1030;
            transition: box-shadow .3s;
        }
        .navbar.scrolled {
            box-shadow: 0 1px 0 rgba(255,255,255,.07), 0 8px 40px rgba(0,0,0,.38);
        }
        .navbar .container { height: 100%; }

        .navbar-brand {
            font-weight: 900;
            font-size: 1.38rem;
            color: #fff !important;
            letter-spacing: -1.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: var(--transition);
        }
        .navbar-brand:hover { opacity: .85; }

        .brand-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--accent) 0%, #fb923c 100%);
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1rem;
            box-shadow: 0 4px 14px rgba(245,158,11,.38);
            flex-shrink: 0;
            transition: var(--transition);
        }
        .navbar-brand:hover .brand-icon { transform: rotate(-6deg) scale(1.05); }
        .navbar-brand .brand-text { color: #fff; }
        .navbar-brand .brand-text span { color: var(--accent); }

        /* Nav links */
        .navbar-nav .nav-link {
            color: rgba(255,255,255,.62) !important;
            font-weight: 500;
            font-size: .875rem;
            padding: .5rem .9rem !important;
            border-radius: var(--radius-xs);
            transition: var(--transition);
            display: flex; align-items: center; gap: 6px;
            position: relative;
        }
        .navbar-nav .nav-link:hover {
            color: #fff !important;
            background: rgba(255,255,255,.08);
        }
        .navbar-nav .nav-link.active {
            color: #fff !important;
            background: rgba(37,99,235,.22);
        }
        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 50%;
            transform: translateX(-50%);
            width: 18px; height: 2px;
            background: var(--primary);
            border-radius: 2px;
        }

        /* Cart bubble */
        .cart-btn {
            position: relative;
            display: flex; align-items: center; justify-content: center;
            width: 40px; height: 40px;
            border-radius: 11px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.1);
            color: rgba(255,255,255,.78) !important;
            transition: var(--transition);
            text-decoration: none;
        }
        .cart-btn:hover {
            background: rgba(37,99,235,.25) !important;
            border-color: rgba(37,99,235,.4);
            color: #fff !important;
            transform: scale(1.06);
        }
        .cart-count {
            position: absolute;
            top: -6px; right: -6px;
            background: var(--accent);
            color: #fff;
            font-size: .56rem;
            font-weight: 900;
            min-width: 18px; height: 18px;
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            padding: 0 4px;
            border: 2px solid rgba(10,18,36,.96);
            letter-spacing: 0;
            line-height: 1;
        }

        /* User avatar */
        .user-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, #6366f1 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: .78rem; font-weight: 800; color: #fff;
            flex-shrink: 0;
            border: 2px solid rgba(255,255,255,.15);
            transition: var(--transition);
        }
        .dropdown:hover .user-avatar { border-color: var(--accent); }

        /* Dropdown */
        .dropdown-toggle::after { display: none; }
        .dropdown-menu {
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0,0,0,.14), 0 2px 8px rgba(0,0,0,.07);
            padding: .5rem;
            min-width: 220px;
            margin-top: 8px !important;
            animation: dropIn .18s cubic-bezier(.4,0,.2,1);
        }
        @keyframes dropIn {
            from { opacity: 0; transform: translateY(-8px) scale(.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .dropdown-header-info {
            padding: .55rem .8rem .45rem;
            border-bottom: 1px solid var(--border-2);
            margin-bottom: .35rem;
        }
        .dropdown-item {
            border-radius: 9px;
            font-size: .875rem;
            padding: .55rem .85rem;
            transition: var(--transition);
            font-weight: 500;
            display: flex; align-items: center; gap: 10px;
            color: var(--text);
        }
        .dropdown-item:hover { background: var(--primary-xl); color: var(--primary); }
        .dropdown-item.text-danger:hover { background: #fef2f2; color: var(--danger); }
        .dropdown-item .di-icon {
            width: 28px; height: 28px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem; flex-shrink: 0;
        }
        .dropdown-divider { margin: .3rem 0; border-color: var(--border-2); opacity: 1; }

        /* Toggler mobile */
        .navbar-toggler {
            border: 1px solid rgba(255,255,255,.15) !important;
            border-radius: 10px !important;
            padding: 6px 10px !important;
            background: rgba(255,255,255,.06) !important;
            transition: var(--transition);
        }
        .navbar-toggler:hover { background: rgba(255,255,255,.12) !important; }
        .navbar-toggler:focus { box-shadow: none !important; }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255,255,255,.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        }

        /* ══════════════════════════════════════════════
           BUTTONS
        ══════════════════════════════════════════════ */
        .btn {
            font-weight: 600;
            border-radius: 10px;
            transition: var(--transition);
            letter-spacing: -.015em;
            display: inline-flex; align-items: center; gap: 7px;
            position: relative;
            overflow: hidden;
        }
        .btn::after {
            content: '';
            position: absolute; inset: 0;
            background: rgba(255,255,255,0);
            transition: background .2s;
        }
        .btn:hover::after { background: rgba(255,255,255,.07); }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 2px 8px rgba(37,99,235,.28);
        }
        .btn-primary:hover, .btn-primary:focus {
            background: var(--primary-d);
            border-color: var(--primary-d);
            color: #fff;
            box-shadow: var(--shadow-blue);
            transform: translateY(-1px);
        }
        .btn-primary:active { transform: translateY(0); }

        .btn-accent {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
            box-shadow: 0 2px 8px rgba(245,158,11,.28);
        }
        .btn-accent:hover {
            background: var(--accent-d);
            border-color: var(--accent-d);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(245,158,11,.35);
        }

        .btn-success {
            background: var(--success);
            border-color: var(--success);
            box-shadow: 0 2px 8px rgba(22,163,74,.25);
        }
        .btn-success:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(22,163,74,.35); }

        .btn-outline-primary { color: var(--primary); border-color: var(--primary); border-width: 1.5px; }
        .btn-outline-primary:hover { background: var(--primary); color: #fff; transform: translateY(-1px); }

        .btn-outline-secondary { color: var(--text-muted); border-color: var(--border); border-width: 1.5px; background: #fff; }
        .btn-outline-secondary:hover { border-color: var(--dark-3); color: var(--text); background: #fff; }

        .btn-lg { padding: .75rem 1.8rem; font-size: 1rem; border-radius: 13px; }
        .btn-sm { padding: .38rem .85rem; font-size: .8rem; border-radius: 8px; }

        /* ══════════════════════════════════════════════
           CARDS
        ══════════════════════════════════════════════ */
        .card {
            border: 1px solid var(--border) !important;
            border-radius: var(--radius) !important;
            box-shadow: var(--shadow);
            transition: var(--transition);
            background: var(--card-bg);
            overflow: hidden;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(37,99,235,.12) !important;
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
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(37,99,235,.15) !important;
        }
        .product-card .img-wrapper {
            overflow: hidden;
            position: relative;
            background: var(--border-2);
        }
        .product-card .card-img-top {
            height: 200px;
            object-fit: cover;
            transition: transform .48s cubic-bezier(.4,0,.2,1);
            width: 100%;
            display: block;
        }
        .product-card:hover .card-img-top { transform: scale(1.07); }
        .product-card .card-body { flex: 1; display: flex; flex-direction: column; padding: 1rem; }
        .product-card .price {
            font-size: 1.22rem;
            font-weight: 900;
            color: var(--primary);
            letter-spacing: -.04em;
        }

        /* Stock chips */
        .stock-chip {
            font-size: .66rem;
            font-weight: 700;
            padding: .25em .72em;
            border-radius: 20px;
            letter-spacing: .03em;
            display: inline-flex; align-items: center; gap: 3px;
        }
        .stock-ok    { background: var(--success-l); color: #166534; }
        .stock-low   { background: #fef9c3; color: #854d0e; }
        .stock-empty { background: var(--danger-l); color: #991b1b; }

        /* ══════════════════════════════════════════════
           HERO
        ══════════════════════════════════════════════ */
        .hero {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-2) 55%, #1a3060 100%);
            color: #fff;
            padding: 100px 0 84px;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            width: 800px; height: 800px;
            background: radial-gradient(circle, rgba(37,99,235,.14) 0%, transparent 68%);
            top: -280px; right: -180px;
            pointer-events: none;
        }
        .hero::after {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(245,158,11,.09) 0%, transparent 68%);
            bottom: -220px; left: -120px;
            pointer-events: none;
        }
        .hero-particles {
            position: absolute; inset: 0; pointer-events: none;
            overflow: hidden;
        }
        .hero-particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
            animation: floatUp linear infinite;
        }
        @keyframes floatUp {
            0%   { transform: translateY(100%) scale(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { transform: translateY(-100vh) scale(1); opacity: 0; }
        }
        .hero h1 {
            font-size: clamp(2.2rem, 5vw, 3.5rem);
            font-weight: 900;
            letter-spacing: -.055em;
            line-height: 1.06;
        }
        .hero .accent { color: var(--accent); }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.14);
            color: rgba(255,255,255,.85);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: .77rem; font-weight: 600;
            backdrop-filter: blur(10px);
        }

        /* ══════════════════════════════════════════════
           SECTIONS
        ══════════════════════════════════════════════ */
        .section-title {
            font-weight: 800;
            font-size: 1.65rem;
            color: var(--text);
            letter-spacing: -.04em;
        }
        .section-label {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--primary);
        }

        /* ══════════════════════════════════════════════
           BADGES
        ══════════════════════════════════════════════ */
        .badge { font-weight: 600; letter-spacing: .02em; }
        .badge-category {
            background: var(--primary-l);
            color: var(--primary);
            font-weight: 700;
        }
        .badge-admin {
            background: linear-gradient(135deg, var(--accent), #fb923c);
            color: #fff;
            font-size: .56rem;
            padding: .22em .65em;
        }

        /* ══════════════════════════════════════════════
           FORMS
        ══════════════════════════════════════════════ */
        .form-control, .form-select {
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: .65rem 1rem;
            font-size: .9rem;
            transition: var(--transition);
            background: #fff;
            color: var(--text);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3.5px rgba(37,99,235,.1);
            background: #fff;
            outline: none;
        }
        .form-control::placeholder { color: var(--text-light); }
        .form-control-lg { padding: .8rem 1.15rem; font-size: 1rem; border-radius: 12px; }
        .input-group-text {
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--border-2);
            color: var(--text-muted);
            font-weight: 600;
        }
        .form-label {
            font-weight: 600;
            font-size: .845rem;
            color: var(--dark-3);
            margin-bottom: .45rem;
        }
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        /* ══════════════════════════════════════════════
           ALERTS
        ══════════════════════════════════════════════ */
        .alert {
            border: none;
            border-radius: var(--radius-sm);
            font-size: .875rem;
            font-weight: 500;
        }
        .alert-success { background: #f0fdf4; color: #166534; border-left: 3px solid #22c55e; }
        .alert-danger  { background: #fef2f2; color: #991b1b; border-left: 3px solid #ef4444; }
        .alert-info    { background: var(--primary-xl); color: #1e40af; border-left: 3px solid var(--primary); }
        .alert-warning { background: #fffbeb; color: #854d0e; border-left: 3px solid var(--accent); }

        /* ══════════════════════════════════════════════
           BREADCRUMB
        ══════════════════════════════════════════════ */
        .breadcrumb { font-size: .8rem; margin-bottom: 0; }
        .breadcrumb-item a { color: var(--primary); text-decoration: none; font-weight: 500; }
        .breadcrumb-item a:hover { text-decoration: underline; }
        .breadcrumb-item.active { color: var(--text-muted); }
        .breadcrumb-item + .breadcrumb-item::before { color: #cbd5e1; }

        /* ══════════════════════════════════════════════
           QUANTITY STEPPER
        ══════════════════════════════════════════════ */
        .qty-stepper {
            display: inline-flex;
            align-items: center;
            border: 1.5px solid var(--border);
            border-radius: 13px;
            overflow: hidden;
            background: #fff;
            box-shadow: var(--shadow-xs);
            transition: border-color .2s;
        }
        .qty-stepper:focus-within { border-color: var(--primary); }
        .qty-stepper button {
            width: 42px; height: 42px;
            border: none; background: transparent;
            color: var(--primary); font-size: 1rem; font-weight: 800;
            cursor: pointer; transition: var(--transition);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .qty-stepper button:hover:not(:disabled) {
            background: var(--primary-l);
            color: var(--primary-d);
        }
        .qty-stepper button:active:not(:disabled) { background: var(--primary-xl); }
        .qty-stepper button:disabled { color: #cbd5e1; cursor: not-allowed; }
        .qty-stepper input {
            width: 54px; height: 42px;
            border: none;
            border-left: 1.5px solid var(--border);
            border-right: 1.5px solid var(--border);
            text-align: center;
            font-weight: 700; font-size: .95rem;
            color: var(--text);
            background: transparent;
            -moz-appearance: textfield;
        }
        .qty-stepper input:focus { outline: none; background: var(--primary-xl); }
        .qty-stepper input::-webkit-outer-spin-button,
        .qty-stepper input::-webkit-inner-spin-button { -webkit-appearance: none; }

        /* Stepper large */
        .qty-stepper.lg button { width: 48px; height: 48px; font-size: 1.1rem; }
        .qty-stepper.lg input  { width: 62px; height: 48px; font-size: 1.05rem; }

        /* ── STOCK BAR ── */
        .stock-bar { height: 6px; border-radius: 4px; background: var(--border); overflow: hidden; }
        .stock-bar-fill { height: 100%; border-radius: 4px; transition: width .8s cubic-bezier(.4,0,.2,1); }

        /* ── PRICE PREVIEW ── */
        .price-preview {
            background: linear-gradient(135deg, var(--primary-xl) 0%, var(--primary-l) 100%);
            border: 1.5px solid rgba(37,99,235,.16);
            border-radius: var(--radius-sm);
            padding: 1.15rem 1.4rem;
            transition: transform .15s ease, box-shadow .15s;
        }
        .price-preview:hover { box-shadow: 0 4px 20px rgba(37,99,235,.1); }
        .price-preview .total-price {
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--primary);
            letter-spacing: -.06em;
        }

        /* ══════════════════════════════════════════════
           PAGINATION
        ══════════════════════════════════════════════ */
        .pagination .page-link {
            border-radius: 9px !important;
            margin: 0 2px;
            border: 1.5px solid var(--border);
            color: var(--text);
            font-weight: 600;
            font-size: .875rem;
            padding: .46rem .78rem;
            transition: var(--transition);
        }
        .pagination .page-link:hover { background: var(--primary-l); border-color: var(--primary); color: var(--primary); }
        .pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 2px 10px rgba(37,99,235,.3);
        }
        .pagination .page-item.disabled .page-link { color: var(--text-light); }

        /* ══════════════════════════════════════════════
           FOOTER
        ══════════════════════════════════════════════ */
        footer {
            background: var(--dark);
            color: rgba(255,255,255,.5);
            padding: 72px 0 32px;
            margin-top: 100px;
            position: relative;
        }
        footer::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(37,99,235,.5), rgba(245,158,11,.5), transparent);
        }
        footer .footer-brand {
            font-size: 1.3rem; font-weight: 900;
            color: #fff; letter-spacing: -.04em;
            display: flex; align-items: center; gap: 9px;
            text-decoration: none;
        }
        .brand-icon-sm {
            width: 30px; height: 30px;
            background: linear-gradient(135deg, var(--accent), #fb923c);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; color: #fff;
        }
        footer .footer-brand span { color: var(--accent); }
        footer a { color: rgba(255,255,255,.44); text-decoration: none; transition: var(--transition); font-size: .87rem; }
        footer a:hover { color: #fff; padding-left: 3px; }
        footer .footer-title {
            color: #fff; font-weight: 700;
            font-size: .75rem; text-transform: uppercase;
            letter-spacing: .09em; margin-bottom: 1.1rem;
        }
        footer hr { border-color: rgba(255,255,255,.07); }
        footer .footer-link-list { list-style: none; padding: 0; margin: 0; }
        footer .footer-link-list li { margin-bottom: .5rem; }
        .footer-pay-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 7px;
            padding: .3rem .65rem;
            font-size: .72rem; font-weight: 600;
            color: rgba(255,255,255,.55);
        }

        /* ══════════════════════════════════════════════
           UTILS
        ══════════════════════════════════════════════ */
        .text-primary   { color: var(--primary) !important; }
        .text-accent    { color: var(--accent) !important; }
        .text-success   { color: var(--success) !important; }
        .text-danger    { color: var(--danger) !important; }
        .bg-primary     { background: var(--primary) !important; }
        .border-primary { border-color: var(--primary) !important; }
        .rounded-xl     { border-radius: var(--radius) !important; }
        .rounded-2xl    { border-radius: var(--radius-lg) !important; }
        .shadow-soft    { box-shadow: var(--shadow); }
        .shadow-md      { box-shadow: var(--shadow-md) !important; }
        .fw-700 { font-weight: 700; }
        .fw-800 { font-weight: 800; }
        .fw-900 { font-weight: 900; }
        .gap-icon { gap: 7px; }
        .text-xs { font-size: .75rem; }
        .text-sm { font-size: .875rem; }

        /* Page header bar */
        .page-header-bar {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 1rem 0;
        }

        /* Glass card */
        .glass-card {
            background: rgba(255,255,255,.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,.6);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
        }

        /* ══════════════════════════════════════════════
           FLASH TOASTS
        ══════════════════════════════════════════════ */
        .flash-container {
            position: fixed;
            top: calc(var(--navbar-h) + 14px);
            right: 18px;
            z-index: 1090;
            width: 340px;
            max-width: calc(100vw - 36px);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .flash-toast {
            border-radius: 14px;
            padding: .9rem 1.1rem;
            font-size: .875rem;
            font-weight: 500;
            display: flex; align-items: flex-start; gap: 11px;
            box-shadow: 0 8px 32px rgba(0,0,0,.13), 0 2px 8px rgba(0,0,0,.07);
            border: 1px solid transparent;
            animation: slideInRight .28s cubic-bezier(.4,0,.2,1);
            cursor: pointer;
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(24px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .flash-toast .toast-icon {
            width: 30px; height: 30px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem; flex-shrink: 0; margin-top: -1px;
        }
        .flash-toast.toast-success { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
        .flash-toast.toast-success .toast-icon { background: var(--success-l); color: var(--success); }
        .flash-toast.toast-error   { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
        .flash-toast.toast-error   .toast-icon { background: var(--danger-l); color: var(--danger); }
        .flash-toast.toast-info    { background: var(--primary-xl); color: #1e40af; border-color: var(--primary-l); }
        .flash-toast.toast-info    .toast-icon { background: var(--primary-l); color: var(--primary); }
        .flash-toast .toast-close {
            margin-left: auto; background: none; border: none;
            color: inherit; opacity: .5; cursor: pointer; padding: 0; line-height: 1;
            flex-shrink: 0;
        }
        .flash-toast .toast-close:hover { opacity: 1; }

        /* ══════════════════════════════════════════════
           ANIMATIONS
        ══════════════════════════════════════════════ */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up   { animation: fadeInUp .45s ease both; }
        .fade-in-up-1 { animation: fadeInUp .45s .07s ease both; }
        .fade-in-up-2 { animation: fadeInUp .45s .15s ease both; }
        .fade-in-up-3 { animation: fadeInUp .45s .23s ease both; }
        .fade-in-up-4 { animation: fadeInUp .45s .31s ease both; }

        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50%       { transform: scale(1.6); opacity: .6; }
        }
        .live-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #22c55e;
            animation: pulse-dot 1.8s ease-in-out infinite;
            display: inline-block;
            box-shadow: 0 0 6px rgba(34,197,94,.5);
            flex-shrink: 0;
        }

        /* Shimmer skeleton */
        @keyframes shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position:  200% 0; }
        }
        .shimmer {
            background: linear-gradient(90deg, #f1f5f9 25%, #e8edf5 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        /* Gradient separator */
        .sep {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border), transparent);
            margin: 0;
        }

        /* ══════════════════════════════════════════════
           TABLE (admin)
        ══════════════════════════════════════════════ */
        .table { font-size: .875rem; }
        .table thead th {
            font-weight: 700;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--text-muted);
            border-bottom: 2px solid var(--border);
            padding: .85rem 1rem;
            background: var(--border-2);
        }
        .table tbody td {
            padding: .95rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-2);
        }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr { transition: background .15s; }
        .table tbody tr:hover { background: var(--primary-xl); }

        /* ══════════════════════════════════════════════
           RESPONSIVE MOBILE
        ══════════════════════════════════════════════ */
        @media (max-width: 991.98px) {
            /* Navbar collapse : fond sombre, espacement */
            #navMenu {
                background: rgba(10,18,36,.98);
                border-radius: 16px;
                padding: 1rem;
                margin-top: .5rem;
                border: 1px solid rgba(255,255,255,.07);
            }
            .navbar-nav .nav-link {
                padding: .65rem .9rem !important;
                border-radius: 10px;
            }
            /* Icônes panier/favoris en ligne sur mobile */
            .navbar-nav.ms-auto {
                flex-direction: row !important;
                flex-wrap: wrap;
                gap: .5rem !important;
                align-items: center;
                padding-top: .5rem;
                border-top: 1px solid rgba(255,255,255,.07);
                margin-top: .5rem;
            }
            /* Bouton S'inscrire pleine largeur */
            .navbar-nav .btn-primary {
                width: 100%;
                justify-content: center;
                padding: .6rem 1rem !important;
                margin-top: .25rem;
            }
        }

        @media (max-width: 767.98px) {
            /* Hero moins de padding */
            .hero {
                padding: 60px 0 50px;
            }
            .hero h1 {
                font-size: clamp(1.7rem, 7vw, 2.4rem);
            }

            /* Section titles */
            .section-title { font-size: 1.35rem; }

            /* Product cards : 2 par ligne sur mobile */
            .product-card .card-img-top { height: 140px; }
            .product-card .card-body { padding: .65rem .7rem; }
            .product-card .price { font-size: .95rem; }
            .product-card .card-body h6 { font-size: .8rem; }
            .product-card .card-body p { display: none; }
            .product-card .d-flex.gap-2.mt-3 .btn { font-size: .72rem; padding: .32rem .5rem; }

            /* Footer : tout en colonne */
            footer { padding: 48px 0 24px; margin-top: 60px; }
            footer .row.g-5 { --bs-gutter-y: 2rem; }

            /* Toasts plus petits */
            .flash-container { width: calc(100vw - 32px); right: 16px; }
            .flash-toast { font-size: .82rem; padding: .75rem .9rem; }

            /* Tables : scroll horizontal */
            .table-responsive-stack { overflow-x: auto; -webkit-overflow-scrolling: touch; }

            /* Qty stepper plus compact */
            .qty-stepper button { width: 36px; height: 36px; }
            .qty-stepper input  { width: 44px; height: 36px; font-size: .88rem; }

            /* Checkout : résumé en bas sur mobile (déjà col-lg, ok) */
            .price-preview .total-price { font-size: 1.5rem; }

            /* Page padding */
            .container { padding-left: .75rem; padding-right: .75rem; }
        }

        @media (max-width: 400px) {
            /* Très petits écrans — cartes encore plus compactes */
            .product-card .card-img-top { height: 110px; }
            .product-card .card-body { padding: .5rem; }
            .product-card .price { font-size: .85rem; }
            .stock-chip { font-size: .58rem; padding: .2em .5em; }
        }

        @media (max-width: 480px) {
            /* Très petits écrans */
            .hero { padding: 44px 0 40px; }
            .hero h1 { font-size: 1.55rem; }

            /* Boutons héro empilés */
            .hero .d-flex.gap-3 {
                flex-direction: column !important;
                gap: .75rem !important;
            }
            .hero .btn { width: 100%; justify-content: center; }

            /* Navbar brand texte */
            .navbar-brand .brand-text { font-size: 1.15rem; }

            /* Chat widget */
            #chat-window {
                width: calc(100vw - 20px) !important;
                right: -4px !important;
                bottom: 64px !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ══════════════ FLASH TOASTS ══════════════ --}}
<div class="flash-container" id="flashContainer">
    @if(session('success'))
        <div class="flash-toast toast-success" onclick="this.remove()">
            <div class="toast-icon"><i class="bi bi-check-lg"></i></div>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button class="toast-close"><i class="bi bi-x"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="flash-toast toast-error" onclick="this.remove()">
            <div class="toast-icon"><i class="bi bi-exclamation-lg"></i></div>
            <div class="flex-grow-1">{{ session('error') }}</div>
            <button class="toast-close"><i class="bi bi-x"></i></button>
        </div>
    @endif
    @if(session('info'))
        <div class="flash-toast toast-info" onclick="this.remove()">
            <div class="toast-icon"><i class="bi bi-info-lg"></i></div>
            <div class="flex-grow-1">{{ session('info') }}</div>
            <button class="toast-close"><i class="bi bi-x"></i></button>
        </div>
    @endif
</div>

{{-- ══════════════ NAVBAR ══════════════ --}}
<nav class="navbar navbar-expand-lg navbar-dark" id="mainNavbar">
    <div class="container">

        {{-- Brand --}}
        <a class="navbar-brand" href="{{ route('home') }}">
            <div class="brand-icon"><i class="bi bi-bag-heart-fill"></i></div>
            <span class="brand-text">Shop<span>CI</span></span>
        </a>

        {{-- Mobile toggler --}}
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navMenu"
                aria-controls="navMenu" aria-expanded="false" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">

            {{-- Nav links --}}
            <ul class="navbar-nav me-auto ms-4 gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                       href="{{ route('home') }}">
                        <i class="bi bi-house"></i>Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                       href="{{ route('products.index') }}">
                        <i class="bi bi-grid-3x3-gap"></i>Catalogue
                    </a>
                </li>
            </ul>

            {{-- Right side --}}
            <ul class="navbar-nav ms-auto align-items-center gap-2">

                @auth
                    {{-- Favoris --}}
                    @php $favCount = \App\Models\Wishlist::where('user_id', auth()->id())->count(); @endphp
                    <li class="nav-item">
                        <a href="{{ route('wishlist.index') }}" class="cart-btn" id="navFavBtn"
                           title="Mes favoris"
                           style="{{ request()->routeIs('wishlist.*') ? 'background:rgba(239,68,68,.18);border-color:rgba(239,68,68,.35);color:#ef4444 !important;' : '' }}">
                            <i class="bi bi-heart{{ request()->routeIs('wishlist.*') ? '-fill' : '' }}"
                               style="font-size:1.05rem;{{ request()->routeIs('wishlist.*') ? 'color:#ef4444;' : '' }}"></i>
                            @if($favCount > 0)
                                <span class="cart-count"
                                      style="background:#ef4444;" id="navFavCount">
                                    {{ $favCount > 99 ? '99+' : $favCount }}
                                </span>
                            @else
                                <span class="cart-count" id="navFavCount"
                                      style="background:#ef4444;display:none;">0</span>
                            @endif
                        </a>
                    </li>

                    {{-- Panier --}}
                    <li class="nav-item">
                        <a href="{{ route('cart.index') }}" class="cart-btn" title="Mon panier">
                            <i class="bi bi-bag" style="font-size:1.05rem;"></i>
                            @php $cartCount = auth()->user()->activeCart?->total_items ?? 0; @endphp
                            @if($cartCount > 0)
                                <span class="cart-count" id="cartCount">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                            @endif
                        </a>
                    </li>

                    {{-- User dropdown --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle p-0 ms-1" href="#"
                           data-bs-toggle="dropdown" aria-expanded="false"
                           style="display:flex;align-items:center;gap:8px;">
                            <div class="user-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span style="font-size:.82rem;font-weight:600;color:rgba(255,255,255,.78);
                                         max-width:90px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                {{ auth()->user()->name }}
                            </span>
                            @if(auth()->user()->isAdmin())
                                <span class="badge badge-admin">Admin</span>
                            @endif
                            <i class="bi bi-chevron-down" style="font-size:.6rem;color:rgba(255,255,255,.4);"></i>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <div class="dropdown-header-info">
                                    <div class="fw-700" style="font-size:.85rem;color:var(--text);">
                                        {{ auth()->user()->name }}
                                    </div>
                                    <div style="font-size:.76rem;color:var(--text-muted);">
                                        {{ auth()->user()->email }}
                                    </div>
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('cart.index') }}">
                                    <div class="di-icon" style="background:#eff6ff;color:var(--primary);">
                                        <i class="bi bi-bag"></i>
                                    </div>
                                    Mon panier
                                    @if($cartCount > 0)
                                        <span class="badge ms-auto" id="cartCountMobile"
                                              style="background:var(--primary-l);color:var(--primary);font-size:.66rem;">
                                            {{ $cartCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('wishlist.index') }}">
                                    <div class="di-icon" style="background:#fef2f2;color:#ef4444;">
                                        <i class="bi bi-heart-fill"></i>
                                    </div>
                                    Mes Favoris
                                    @if($favCount > 0)
                                        <span class="badge ms-auto"
                                              style="background:#fee2e2;color:#dc2626;font-size:.66rem;">
                                            {{ $favCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <div class="di-icon" style="background:#eff6ff;color:var(--primary);">
                                        <i class="bi bi-person-circle"></i>
                                    </div>
                                    Mon profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('orders.index') }}">
                                    <div class="di-icon" style="background:#f0fdf4;color:var(--success);">
                                        <i class="bi bi-receipt"></i>
                                    </div>
                                    Mes commandes
                                </a>
                            </li>
                            @if(auth()->user()->isAdmin())
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <div class="di-icon"
                                             style="background:linear-gradient(135deg,#fef3c7,#fde68a);color:#92400e;">
                                            <i class="bi bi-speedometer2"></i>
                                        </div>
                                        Administration
                                        <i class="bi bi-arrow-up-right ms-auto"
                                           style="font-size:.65rem;opacity:.5;"></i>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.coupons.index') }}">
                                        <div class="di-icon"
                                             style="background:linear-gradient(135deg,#fce7f3,#fbcfe8);color:#9d174d;">
                                            <i class="bi bi-tag-fill"></i>
                                        </div>
                                        Coupons promo
                                    </a>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger w-100 border-0 bg-transparent">
                                        <div class="di-icon" style="background:#fef2f2;color:var(--danger);">
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
                        <a href="{{ route('login') }}" class="nav-link">
                            <i class="bi bi-person"></i>Connexion
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('register') }}"
                           class="btn btn-primary btn-sm"
                           style="border-radius:9px;padding:.42rem 1rem;">
                            <i class="bi bi-person-plus"></i>S'inscrire
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- ══════════════ CONTENT ══════════════ --}}
<main>
    @yield('content')
</main>

{{-- ══════════════ FOOTER ══════════════ --}}
<footer>
    <div class="container">
        <div class="row g-5 mb-5">

            {{-- Brand block --}}
            <div class="col-lg-4">
                <a href="{{ route('home') }}" class="footer-brand mb-3 d-inline-flex">
                    <div class="brand-icon-sm"><i class="bi bi-bag-heart-fill"></i></div>
                    Shop<span>CI</span>
                </a>
                <p style="font-size:.875rem;line-height:1.75;margin-top:.75rem;">
                    La boutique en ligne qui place le client au centre. Qualité, sécurité et rapidité pour chaque commande.
                </p>
                <div class="d-flex gap-2 mt-4 flex-wrap">
                    <span class="footer-pay-badge"><i class="bi bi-shield-lock"></i>SSL</span>
                    <span class="footer-pay-badge"><i class="bi bi-credit-card"></i>Paiement sécurisé</span>
                    <span class="footer-pay-badge"><i class="bi bi-truck"></i>Livraison gratuite</span>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="col-6 col-lg-2 offset-lg-1">
                <h6 class="footer-title">Navigation</h6>
                <ul class="footer-link-list">
                    <li><a href="{{ route('home') }}">Accueil</a></li>
                    <li><a href="{{ route('products.index') }}">Catalogue</a></li>
                    @auth
                        <li><a href="{{ route('cart.index') }}">Mon panier</a></li>
                        <li><a href="{{ route('orders.index') }}">Mes commandes</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Compte --}}
            <div class="col-6 col-lg-2">
                <h6 class="footer-title">Mon compte</h6>
                <ul class="footer-link-list">
                    @auth
                        <li><a href="{{ route('orders.index') }}">Historique</a></li>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf<li><button type="submit" style="background:none;border:none;padding:0;color:rgba(255,255,255,.44);font-size:.87rem;cursor:pointer;transition:color .2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.44)'">Déconnexion</button></li>
                        </form>
                    @else
                        <li><a href="{{ route('login') }}">Connexion</a></li>
                        <li><a href="{{ route('register') }}">Inscription</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Support --}}
            <div class="col-lg-3">
                <h6 class="footer-title">Support</h6>
                <ul class="footer-link-list">
                    <li>
                        <a href="#" style="display:flex;align-items:center;gap:7px;">
                            <div class="live-dot"></div>
                            Support en ligne 7j/7
                        </a>
                    </li>
                    <li><a href="{{ route('contact') }}"><i class="bi bi-envelope me-2" style="font-size:.75rem;"></i>Contact</a></li>
                    <li><a href="{{ route('cgv') }}"><i class="bi bi-file-text me-2" style="font-size:.75rem;"></i>CGV</a></li>
                    <li><a href="{{ route('contact') }}"><i class="bi bi-arrow-return-left me-2" style="font-size:.75rem;"></i>Retours</a></li>
                </ul>
            </div>
        </div>

        <hr>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-1">
            <span style="font-size:.8rem;">© {{ date('Y') }} ShopCI — Tous droits réservés &nbsp;·&nbsp; <a href="{{ route('cgv') }}" style="color:rgba(255,255,255,.44);text-decoration:none;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.44)'">CGV</a> &nbsp;·&nbsp; <a href="{{ route('contact') }}" style="color:rgba(255,255,255,.44);text-decoration:none;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.44)'">Contact</a></span>
            <span style="font-size:.76rem;display:flex;align-items:center;gap:6px;">
                <i class="bi bi-shield-check" style="color:var(--success);"></i>
                Site sécurisé SSL · Données protégées
            </span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Navbar scroll shadow
    const navbar = document.getElementById('mainNavbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 8);
        }, { passive: true });
    }

    // Auto-dismiss toasts
    document.querySelectorAll('.flash-toast').forEach(toast => {
        setTimeout(() => {
            toast.style.transition = 'opacity .4s, transform .4s';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(16px)';
            setTimeout(() => toast.remove(), 400);
        }, 4200);
    });
</script>

@stack('scripts')

@include('chat.widget')
</body>
</html>
