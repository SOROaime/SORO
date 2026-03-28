<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'Tableau de bord') | ShopLaravel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --dark: #1e293b; --accent: #f59e0b; --primary: #2563eb; }
        body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; }

        /* Sidebar */
        .admin-sidebar {
            background: var(--dark); min-height: 100vh;
            width: 250px; position: fixed; left: 0; top: 0; z-index: 1000;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 20px 20px 15px;
            font-size: 1.3rem; font-weight: 800; color: #fff;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .sidebar-brand span { color: var(--accent); }
        .sidebar-menu { padding: 10px 0; }
        .sidebar-section { 
            font-size: .7rem; text-transform: uppercase; letter-spacing: 1px;
            color: rgba(255,255,255,.4); padding: 16px 20px 6px;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            color: rgba(255,255,255,.7) !important; text-decoration: none;
            padding: 10px 20px; font-size: .9rem; font-weight: 500;
            transition: all .2s; border-left: 3px solid transparent;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255,255,255,.08);
            color: #fff !important;
            border-left-color: var(--accent);
        }
        .sidebar-link i { font-size: 1.1rem; width: 20px; }

        /* Topbar */
        .admin-topbar {
            background: #fff; padding: 12px 30px;
            border-bottom: 1px solid #e2e8f0;
            position: fixed; top: 0; left: 250px; right: 0; z-index: 999;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }

        /* Content */
        .admin-content { margin-left: 250px; padding: 80px 30px 30px; }

        /* Cards statistiques */
        .stat-card { 
            border: none; border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
        }
        .stat-icon { 
            width: 50px; height: 50px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }

        /* Tables */
        .table { font-size: .9rem; }
        .table th { font-weight: 600; background: #f8fafc; }

        /* Badges statuts */
        .status-badge { font-size: .75rem; padding: .35em .7em; border-radius: 20px; }
    </style>
    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<div class="admin-sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-bag-heart-fill me-2"></i>Shop<span>Laravel</span>
        <div style="font-size:.7rem; color:rgba(255,255,255,.4); font-weight:400">Administration</div>
    </div>

    <div class="sidebar-menu">
        <div class="sidebar-section">Principal</div>
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Tableau de bord
        </a>

        <div class="sidebar-section">Boutique</div>
        <a href="{{ route('admin.products.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Produits
        </a>
        <a href="{{ route('admin.orders.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="bi bi-bag-check"></i> Commandes
        </a>
        <a href="{{ route('admin.payments.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card"></i> Paiements
        </a>

        <div class="sidebar-section">Utilisateurs</div>
        <a href="{{ route('admin.users.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Utilisateurs
        </a>

        <div class="sidebar-section">Boutique</div>
        <a href="{{ route('home') }}" class="sidebar-link">
            <i class="bi bi-shop"></i> Voir la boutique
        </a>

        <div style="padding: 30px 20px 20px;">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Déconnexion
                </button>
            </form>
        </div>
    </div>
</div>

{{-- TOPBAR --}}
<div class="admin-topbar">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Admin</a>
                </li>
                @yield('breadcrumb')
            </ol>
        </nav>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="text-muted small">
            <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
        </span>
    </div>
</div>

{{-- CONTENU --}}
<div class="admin-content">
    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
