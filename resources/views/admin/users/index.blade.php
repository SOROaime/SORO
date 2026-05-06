@extends('layouts.admin')

@section('title', 'Utilisateurs')

@section('breadcrumb')
    <li class="breadcrumb-item active">Utilisateurs</li>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="fw-900 mb-1" style="font-size:1.8rem;letter-spacing:-.04em;">Utilisateurs</h1>
        <p class="text-muted mb-0" style="font-size:.88rem;">
            <span class="fw-700" style="color:var(--primary);">{{ $users->total() }}</span> utilisateur(s) enregistré(s)
        </p>
    </div>
</div>

{{-- Filtres --}}
<div class="card stat-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.users.index') }}" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text" style="background:#f8fafc;border:1.5px solid var(--border);">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Nom ou email..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-600">
                        <i class="bi bi-funnel me-1"></i>Filtrer
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body p-0">
        @if($users->isEmpty())
            <div class="text-center py-5 text-muted">
                <div style="width:72px;height:72px;background:#f1f5f9;border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="bi bi-people fs-2"></i>
                </div>
                <p class="mb-0 fw-600">Aucun utilisateur trouvé.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:.875rem;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th class="ps-4 fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;padding:.85rem .75rem;">Utilisateur</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Email</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Rôle</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Commandes</th>
                            <th class="fw-600 text-muted border-0 pe-4" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Inscription</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:38px;height:38px;border-radius:50%;flex-shrink:0;
                                                background:{{ $user->isAdmin() ? 'linear-gradient(135deg,#f59e0b,#d97706)' : 'linear-gradient(135deg,var(--primary),#7c3aed)' }};
                                                display:flex;align-items:center;justify-content:center;
                                                font-size:.85rem;font-weight:800;color:#fff;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-700">{{ $user->name }}</div>
                                        <div class="text-muted" style="font-size:.75rem;">
                                            ID #{{ $user->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>
                                @if($user->isAdmin())
                                    <span style="padding:.25em .75em;border-radius:20px;font-size:.72rem;font-weight:800;background:#fef9c3;color:#854d0e;">
                                        <i class="bi bi-shield-fill-check me-1"></i>Admin
                                    </span>
                                @else
                                    <span style="padding:.25em .75em;border-radius:20px;font-size:.72rem;font-weight:700;background:#dbeafe;color:#1d4ed8;">
                                        <i class="bi bi-person me-1"></i>Client
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span style="padding:.2em .7em;border-radius:20px;font-size:.75rem;font-weight:700;
                                             background:{{ $user->orders_count > 0 ? '#dcfce7' : '#f1f5f9' }};
                                             color:{{ $user->orders_count > 0 ? '#16a34a' : '#64748b' }};">
                                    {{ $user->orders_count }} commande(s)
                                </span>
                            </td>
                            <td class="text-muted pe-4" style="font-size:.82rem;">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3"
                 style="border-top:1px solid var(--border);">
                <div class="text-muted" style="font-size:.82rem;">
                    {{ $users->total() }} utilisateur(s) — Page {{ $users->currentPage() }} / {{ $users->lastPage() }}
                </div>
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
