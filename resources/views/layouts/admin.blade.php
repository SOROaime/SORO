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
            --dark:      #1e293b;
            --dark-2:    #334155;
            --accent:    #f59e0b;
            --primary:   #2563eb;
            --primary-d: #1d4ed8;
            --primary-l: #dbeafe;
            --border:    #e2e8f0;
            --light-bg:  #f1f5f9;
            --text:      #0f172a;
            --text-muted:#64748b;
            --sidebar-w: 256px;
            --topbar-h:  62px;
        }

        * { box-sizing: border-box; }

        body {
            background: var(--light-bg);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: var(--text);
            line-height: 1.6;
        }

        /* ─── SIDEBAR ─── */
        .admin-sidebar {
            background: var(--dark);
            width: var(--sidebar-w);
            position: fixed; left: 0; top: 0;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1040;
            display: flex; flex-direction: column;
            box-shadow: 4px 0 24px rgba(0,0,0,.2);
        }
        .sidebar-header {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            flex-shrink: 0;
        }
        .sidebar-brand {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none;
        }
        .sidebar-brand .brand-icon {
            width: 36px; height: 36px;
            background: var(--accent);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: var(--dark); font-size: .95rem;
            flex-shrink: 0;
        }
        .sidebar-brand .brand-text {
            font-size: 1.25rem; font-weight: 900;
            color: #fff; letter-spacing: -.03em;
        }
        .sidebar-brand .brand-text span { color: var(--accent); }
        .sidebar-sub {
            font-size: .68rem; color: rgba(255,255,255,.35);
            font-weight: 500; letter-spacing: .08em;
            text-transform: uppercase; margin-top: 2px;
        }

        .sidebar-nav { padding: 12px 0; flex: 1; }
        .sidebar-section {
            font-size: .65rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .12em;
            color: rgba(255,255,255,.3);
            padding: 16px 20px 6px;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            color: rgba(255,255,255,.65) !important;
            text-decoration: none;
            padding: 9px 16px 9px 20px;
            font-size: .875rem; font-weight: 500;
            transition: all .2s;
            border-left: 3px solid transparent;
            margin: 1px 8px 1px 0;
            border-radius: 0 10px 10px 0;
        }
        .sidebar-link i { font-size: 1rem; width: 20px; flex-shrink: 0; }
        .sidebar-link:hover {
            background: rgba(255,255,255,.07);
            color: #fff !important;
            border-left-color: rgba(255,255,255,.2);
        }
        .sidebar-link.active {
            background: rgba(37,99,235,.25);
            color: #fff !important;
            border-left-color: var(--accent);
        }
        .sidebar-link .badge-count {
            margin-left: auto;
            background: var(--accent);
            color: var(--dark);
            font-size: .65rem; font-weight: 800;
            padding: .15em .5em;
            border-radius: 10px;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,.08);
            flex-shrink: 0;
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 10px;
        }
        .sidebar-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--primary), var(--primary-d));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; font-weight: 800; color: #fff;
            flex-shrink: 0;
        }
        .sidebar-user-info .name {
            font-size: .85rem; font-weight: 700; color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sidebar-user-info .role {
            font-size: .68rem; color: rgba(255,255,255,.4); font-weight: 500;
        }

        /* ─── TOPBAR ─── */
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
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .topbar-time {
            font-size: .78rem; color: var(--text-muted); font-weight: 500;
        }
        .topbar-user {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 12px;
            background: var(--light-bg);
            border-radius: 10px;
            font-size: .82rem; font-weight: 600;
        }
        .topbar-avatar {
            width: 28px; height: 28px;
            background: linear-gradient(135deg, var(--primary), var(--primary-d));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .7rem; font-weight: 800; color: #fff;
        }

        /* ─── CONTENT ─── */
        .admin-content {
            margin-left: var(--sidebar-w);
            padding-top: calc(var(--topbar-h) + 28px);
            padding-right: 28px; padding-left: 28px; padding-bottom: 40px;
            min-height: 100vh;
        }

        /* ─── CARDS ─── */
        .stat-card {
            border: 1px solid var(--border) !important;
            border-radius: 16px !important;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
            background: #fff;
        }

        /* ─── TABLE ─── */
        .table { font-size: .875rem; }
        .table thead th {
            font-weight: 700; font-size: .72rem;
            text-transform: uppercase; letter-spacing: .06em;
            color: var(--text-muted);
            background: #f8fafc;
            border: none;
            padding: .85rem .75rem;
        }
        .table tbody td { vertical-align: middle; border-color: var(--border); }
        .table-hover tbody tr:hover { background: #fafbfc; }

        /* ─── FORMS ─── */
        .form-control, .form-select {
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: .9rem;
            transition: all .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }
        .input-group-text {
            border: 1.5px solid var(--border);
            background: #f8fafc;
            border-radius: 10px;
        }

        /* ─── BUTTONS ─── */
        .btn { font-weight: 600; border-radius: 10px; transition: all .2s; }
        .btn-primary { background: var(--primary); border-color: var(--primary); box-shadow: 0 4px 12px rgba(37,99,235,.25); }
        .btn-primary:hover { background: var(--primary-d); border-color: var(--primary-d); transform: translateY(-1px); }
        .btn-sm { padding: .35rem .75rem; font-size: .82rem; border-radius: 8px; }

        /* ─── ALERTS ─── */
        .alert { border: none; border-radius: 12px; font-size: .9rem; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-danger  { background: #fee2e2; color: #991b1b; }

        /* ─── BADGES STATUTS ─── */
        .status-pill {
            padding: .28em .75em;
            border-radius: 20px;
            font-size: .72rem; font-weight: 700;
            letter-spacing: .02em;
        }

        /* ─── PAGINATION ─── */
        .pagination .page-link {
            border-radius: 8px !important; margin: 0 2px;
            border: 1.5px solid var(--border);
            color: var(--primary); font-weight: 500; font-size: .85rem;
        }
        .pagination .page-item.active .page-link {
            background: var(--primary); border-color: var(--primary);
        }

        /* ─── LIVE DOT ─── */
        @keyframes pulse-dot {
            0%,100% { transform: scale(1); opacity: 1; }
            50%      { transform: scale(1.4); opacity: .7; }
        }
        .live-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #16a34a;
            animation: pulse-dot 1.5s infinite;
            display: inline-block;
        }

        /* Scrollbar sidebar */
        .admin-sidebar::-webkit-scrollbar { width: 4px; }
        .admin-sidebar::-webkit-scrollbar-track { background: transparent; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 2px; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══════════════════ SIDEBAR ═══════════════════ --}}
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
            <i class="bi bi-speedometer2"></i>
            Tableau de bord
        </a>

        <div class="sidebar-section">Boutique</div>
        <a href="{{ route('admin.products.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>
            Produits
        </a>
        <a href="{{ route('admin.orders.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="bi bi-bag-check"></i>
            Commandes
            @php $pending = \App\Models\Order::where('status','pending')->count(); @endphp
            @if($pending > 0)
                <span class="badge-count">{{ $pending }}</span>
            @endif
        </a>
        <a href="{{ route('admin.payments.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card"></i>
            Paiements
        </a>

        <div class="sidebar-section">Gestion</div>
        <a href="{{ route('admin.users.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            Utilisateurs
        </a>
        <a href="{{ route('admin.products.create') }}"
           class="sidebar-link">
            <i class="bi bi-plus-circle"></i>
            Nouveau produit
        </a>

        <div class="sidebar-section">Navigation</div>
        <a href="{{ route('home') }}" class="sidebar-link" target="_blank">
            <i class="bi bi-shop"></i>
            Voir la boutique
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="sidebar-user-info">
                <div class="name">{{ auth()->user()->name }}</div>
                <div class="role"><span class="live-dot me-1"></span>Administrateur</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="btn btn-sm w-100"
                    style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.7);border:1px solid rgba(255,255,255,.12);font-size:.8rem;">
                <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
            </button>
        </form>
    </div>
</aside>

{{-- ═══════════════════ TOPBAR ═══════════════════ --}}
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
        <span class="topbar-time d-none d-md-block">
            <i class="bi bi-clock me-1"></i>{{ now()->format('d/m/Y H:i') }}
        </span>
        <div class="topbar-user">
            <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
        </div>
    </div>
</header>

{{-- ═══════════════════ CONTENU ═══════════════════ --}}
<main class="admin-content">
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
