<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'Tableau de bord') | ShopCI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary:      #2563eb;
            --primary-d:    #1d4ed8;
            --primary-l:    #dbeafe;
            --primary-xl:   #eff6ff;
            --accent:       #f59e0b;
            --accent-d:     #d97706;
            --dark:         #0f172a;
            --dark-2:       #1e293b;
            --dark-3:       #334155;
            --light-bg:     #f1f5f9;
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
            --sidebar-w:    260px;
            --topbar-h:     64px;
            --radius:       14px;
            --shadow:       0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.07);
            --transition:   all .2s cubic-bezier(.4,0,.2,1);
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            background: var(--light-bg);
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            color: var(--text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ══════════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════════ */
        .admin-sidebar {
            background: var(--dark-2);
            width: var(--sidebar-w);
            position: fixed; left: 0; top: 0;
            height: 100vh;
            overflow-y: auto; overflow-x: hidden;
            z-index: 1040;
            display: flex; flex-direction: column;
            box-shadow: 4px 0 32px rgba(0,0,0,.22);
        }
        .admin-sidebar::-webkit-scrollbar { width: 4px; }
        .admin-sidebar::-webkit-scrollbar-track { background: transparent; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 2px; }

        /* Header sidebar */
        .sidebar-header {
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            flex-shrink: 0;
        }
        .sidebar-brand {
            display: flex; align-items: center; gap: 11px;
            text-decoration: none; transition: var(--transition);
        }
        .sidebar-brand:hover { opacity: .88; }
        .sidebar-brand .brand-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--accent), #fb923c);
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1rem;
            box-shadow: 0 4px 12px rgba(245,158,11,.32);
            flex-shrink: 0;
        }
        .sidebar-brand .brand-text {
            font-size: 1.2rem; font-weight: 900;
            color: #fff; letter-spacing: -.04em;
        }
        .sidebar-brand .brand-text span { color: var(--accent); }
        .sidebar-sub {
            font-size: .64rem; color: rgba(255,255,255,.32);
            font-weight: 600; letter-spacing: .1em;
            text-transform: uppercase; margin-top: 1px;
        }

        /* Nav */
        .sidebar-nav { padding: 10px 0; flex: 1; }
        .sidebar-section {
            font-size: .62rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .12em;
            color: rgba(255,255,255,.28);
            padding: 18px 20px 6px;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 11px;
            color: rgba(255,255,255,.58) !important;
            text-decoration: none;
            padding: 9px 20px;
            font-size: .865rem; font-weight: 500;
            transition: var(--transition);
            border-left: 3px solid transparent;
            margin: 1px 10px 1px 0;
            border-radius: 0 11px 11px 0;
            position: relative;
        }
        .sidebar-link i { font-size: .95rem; width: 20px; flex-shrink: 0; text-align: center; }
        .sidebar-link:hover {
            background: rgba(255,255,255,.07);
            color: #fff !important;
            border-left-color: rgba(255,255,255,.18);
        }
        .sidebar-link.active {
            background: rgba(37,99,235,.22);
            color: #fff !important;
            border-left-color: var(--accent);
            font-weight: 600;
        }
        .sidebar-link .badge-count {
            margin-left: auto;
            background: var(--accent);
            color: #fff;
            font-size: .6rem; font-weight: 800;
            padding: .18em .55em;
            border-radius: 10px;
            line-height: 1.4;
        }

        /* Footer sidebar */
        .sidebar-footer {
            padding: 16px 18px 20px;
            border-top: 1px solid rgba(255,255,255,.07);
            flex-shrink: 0;
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 12px;
        }
        .sidebar-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary), #6366f1);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .78rem; font-weight: 800; color: #fff;
            flex-shrink: 0;
            border: 2px solid rgba(255,255,255,.14);
        }
        .sidebar-user-info .name {
            font-size: .84rem; font-weight: 700; color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            max-width: 160px;
        }
        .sidebar-user-info .role {
            font-size: .66rem; color: rgba(255,255,255,.38); font-weight: 500;
            display: flex; align-items: center; gap: 5px;
        }

        /* ══════════════════════════════════════════
           TOPBAR
        ══════════════════════════════════════════ */
        .admin-topbar {
            position: fixed; top: 0;
            left: var(--sidebar-w); right: 0;
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid var(--border);
            z-index: 1030;
            display: flex; align-items: center;
            padding: 0 28px;
            justify-content: space-between;
            box-shadow: 0 1px 8px rgba(0,0,0,.04);
        }
        .topbar-breadcrumb .breadcrumb {
            margin: 0; font-size: .82rem;
        }
        .topbar-breadcrumb .breadcrumb-item a {
            color: var(--primary); text-decoration: none; font-weight: 500;
        }
        .topbar-breadcrumb .breadcrumb-item.active { color: var(--text-muted); }
        .topbar-breadcrumb .breadcrumb-item + .breadcrumb-item::before { color: #cbd5e1; }

        .topbar-right { display: flex; align-items: center; gap: 14px; }
        .topbar-time {
            font-size: .78rem; color: var(--text-muted); font-weight: 500;
            display: flex; align-items: center; gap: 5px;
        }
        .topbar-user {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 12px;
            background: var(--light-bg);
            border-radius: 10px;
            font-size: .82rem; font-weight: 600;
            border: 1px solid var(--border);
        }
        .topbar-avatar {
            width: 28px; height: 28px;
            background: linear-gradient(135deg, var(--primary), #6366f1);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .68rem; font-weight: 800; color: #fff;
        }
        .topbar-shop-btn {
            display: flex; align-items: center; gap: 6px;
            font-size: .8rem; font-weight: 600;
            color: var(--text-muted);
            padding: 6px 12px;
            border: 1.5px solid var(--border);
            border-radius: 9px;
            text-decoration: none;
            transition: var(--transition);
            background: #fff;
        }
        .topbar-shop-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-xl);
        }

        /* ══════════════════════════════════════════
           CONTENU PRINCIPAL
        ══════════════════════════════════════════ */
        .admin-content {
            margin-left: var(--sidebar-w);
            padding-top: calc(var(--topbar-h) + 28px);
            padding-right: 28px;
            padding-left: 28px;
            padding-bottom: 48px;
            min-height: 100vh;
        }

        /* ══════════════════════════════════════════
           STAT CARDS
        ══════════════════════════════════════════ */
        .stat-card {
            background: #fff;
            border: 1px solid var(--border) !important;
            border-radius: var(--radius) !important;
            box-shadow: var(--shadow);
            padding: 1.4rem 1.5rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.1); }
        .stat-card::after {
            content: '';
            position: absolute; top: 0; right: 0;
            width: 80px; height: 80px;
            border-radius: 0 0 0 80px;
            opacity: .06;
        }

        /* ══════════════════════════════════════════
           CARDS
        ══════════════════════════════════════════ */
        .card {
            border: 1px solid var(--border) !important;
            border-radius: var(--radius) !important;
            box-shadow: var(--shadow);
            background: #fff;
        }
        .card-header {
            background: #fff !important;
            border-bottom: 1px solid var(--border-2) !important;
            border-radius: var(--radius) var(--radius) 0 0 !important;
        }

        /* ══════════════════════════════════════════
           TABLE
        ══════════════════════════════════════════ */
        .table { font-size: .875rem; }
        .table thead th {
            font-weight: 700; font-size: .7rem;
            text-transform: uppercase; letter-spacing: .08em;
            color: var(--text-muted);
            background: var(--border-2);
            border-bottom: 2px solid var(--border);
            padding: .9rem 1rem;
        }
        .table tbody td {
            vertical-align: middle;
            border-color: var(--border-2);
            padding: .9rem 1rem;
        }
        .table tbody tr { transition: background .15s; }
        .table tbody tr:hover { background: var(--primary-xl); }

        /* ══════════════════════════════════════════
           FORMS
        ══════════════════════════════════════════ */
        .form-control, .form-select {
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: .9rem;
            transition: var(--transition);
            padding: .62rem 1rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3.5px rgba(37,99,235,.1);
            outline: none;
        }
        .input-group-text {
            border: 1.5px solid var(--border);
            background: var(--border-2);
            border-radius: 10px;
            color: var(--text-muted);
            font-weight: 600;
        }
        .form-label { font-weight: 600; font-size: .875rem; color: var(--dark-3); margin-bottom: .45rem; }

        /* ══════════════════════════════════════════
           BUTTONS
        ══════════════════════════════════════════ */
        .btn {
            font-weight: 600; border-radius: 10px;
            transition: var(--transition);
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-primary {
            background: var(--primary); border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(37,99,235,.25);
        }
        .btn-primary:hover {
            background: var(--primary-d); border-color: var(--primary-d);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(37,99,235,.35);
        }
        .btn-success { box-shadow: 0 2px 8px rgba(22,163,74,.2); }
        .btn-success:hover { transform: translateY(-1px); }
        .btn-danger { box-shadow: 0 2px 8px rgba(220,38,38,.15); }
        .btn-outline-primary { color: var(--primary); border-color: var(--primary); border-width: 1.5px; }
        .btn-outline-primary:hover { background: var(--primary); color: #fff; transform: translateY(-1px); }
        .btn-outline-secondary { border-width: 1.5px; }
        .btn-sm { padding: .36rem .8rem; font-size: .8rem; border-radius: 8px; }
        .btn-lg { padding: .72rem 1.6rem; font-size: 1rem; border-radius: 12px; }

        /* ══════════════════════════════════════════
           ALERTS
        ══════════════════════════════════════════ */
        .alert { border: none; border-radius: 12px; font-size: .875rem; font-weight: 500; }
        .alert-success { background: #f0fdf4; color: #166534; border-left: 3px solid #22c55e; }
        .alert-danger  { background: #fef2f2; color: #991b1b; border-left: 3px solid #ef4444; }
        .alert-warning { background: #fffbeb; color: #854d0e; border-left: 3px solid var(--accent); }
        .alert-info    { background: var(--primary-xl); color: #1e40af; border-left: 3px solid var(--primary); }

        /* ══════════════════════════════════════════
           BADGES / STATUS PILLS
        ══════════════════════════════════════════ */
        .badge { font-weight: 600; letter-spacing: .02em; }
        .status-pill {
            padding: .28em .78em;
            border-radius: 20px;
            font-size: .7rem; font-weight: 700;
            letter-spacing: .03em;
            display: inline-flex; align-items: center; gap: 4px;
        }

        /* ══════════════════════════════════════════
           PAGINATION
        ══════════════════════════════════════════ */
        .pagination .page-link {
            border-radius: 9px !important; margin: 0 2px;
            border: 1.5px solid var(--border);
            color: var(--primary); font-weight: 600; font-size: .85rem;
            padding: .42rem .75rem;
            transition: var(--transition);
        }
        .pagination .page-link:hover { background: var(--primary-l); border-color: var(--primary); }
        .pagination .page-item.active .page-link {
            background: var(--primary); border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(37,99,235,.25);
        }

        /* ══════════════════════════════════════════
           UTILS
        ══════════════════════════════════════════ */
        .fw-700 { font-weight: 700; }
        .fw-800 { font-weight: 800; }
        .fw-900 { font-weight: 900; }
        .text-primary { color: var(--primary) !important; }
        .text-accent  { color: var(--accent)  !important; }
        .rounded-xl   { border-radius: var(--radius) !important; }

        @keyframes pulse-dot {
            0%,100% { transform: scale(1); opacity: 1; }
            50%      { transform: scale(1.5); opacity: .65; }
        }
        .live-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #22c55e;
            animation: pulse-dot 1.8s infinite;
            display: inline-block;
            box-shadow: 0 0 5px rgba(34,197,94,.5);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp .4s ease both; }

        /* Page header */
        .admin-page-header {
            display: flex; align-items: center;
            justify-content: space-between;
            flex-wrap: wrap; gap: 1rem;
            margin-bottom: 1.75rem;
        }
        .admin-page-title {
            font-size: 1.5rem; font-weight: 800;
            color: var(--text); letter-spacing: -.04em;
            margin: 0;
        }

        /* Stock bar (admin) */
        .stock-bar { height: 6px; border-radius: 3px; background: var(--border); overflow: hidden; }
        .stock-bar-fill { height: 100%; border-radius: 3px; transition: width .6s ease; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ══════════════ SIDEBAR ══════════════ --}}
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-bag-heart-fill"></i></div>
            <div>
                <div class="brand-text">Shop<span>CI</span></div>
                <div class="sidebar-sub">Administration</div>
            </div>
        </a>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-section">Principal</div>
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>Tableau de bord
        </a>

        <div class="sidebar-section">Boutique</div>
        <a href="{{ route('admin.products.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>Produits
        </a>
        <a href="{{ route('admin.orders.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="bi bi-bag-check"></i>Commandes
            @php $pending = \App\Models\Order::where('status','pending')->count(); @endphp
            @if($pending > 0)
                <span class="badge-count">{{ $pending }}</span>
            @endif
        </a>
        <a href="{{ route('admin.payments.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card"></i>Paiements
        </a>

        <div class="sidebar-section">Gestion</div>
        <a href="{{ route('admin.users.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>Utilisateurs
        </a>
        <a href="{{ route('admin.products.create') }}" class="sidebar-link">
            <i class="bi bi-plus-circle"></i>Ajouter un produit
        </a>

        <div class="sidebar-section">Site</div>
        <a href="{{ route('home') }}" class="sidebar-link" target="_blank" rel="noopener">
            <i class="bi bi-shop"></i>Voir la boutique
            <i class="bi bi-box-arrow-up-right ms-auto" style="font-size:.65rem;opacity:.4;"></i>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="sidebar-user-info" style="min-width:0;">
                <div class="name">{{ auth()->user()->name }}</div>
                <div class="role">
                    <span class="live-dot"></span>Administrateur
                </div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm w-100"
                    style="background:rgba(255,255,255,.07);color:rgba(255,255,255,.62);
                           border:1px solid rgba(255,255,255,.1);font-size:.8rem;">
                <i class="bi bi-box-arrow-right"></i>Déconnexion
            </button>
        </form>
    </div>
</aside>

{{-- ══════════════ TOPBAR ══════════════ --}}
<header class="admin-topbar">
    <nav class="topbar-breadcrumb" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Admin</a>
            </li>
            @yield('breadcrumb')
        </ol>
    </nav>
    <div class="topbar-right">
        <a href="{{ route('home') }}" class="topbar-shop-btn d-none d-md-flex" target="_blank">
            <i class="bi bi-shop"></i>Boutique
        </a>
        <span class="topbar-time d-none d-lg-flex">
            <i class="bi bi-clock"></i>{{ now()->format('d/m/Y · H:i') }}
        </span>
        <div class="topbar-user">
            <div class="topbar-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <span class="d-none d-md-inline" style="color:var(--text);">
                {{ auth()->user()->name }}
            </span>
        </div>
    </div>
</header>

{{-- ══════════════ CONTENU ══════════════ --}}
<main class="admin-content">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-check-circle-fill fs-5 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
