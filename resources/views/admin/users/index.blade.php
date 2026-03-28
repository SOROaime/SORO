@extends('layouts.admin')

@section('title', 'Utilisateurs')

@section('breadcrumb')
    <li class="breadcrumb-item active">Utilisateurs</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2 fw-bold mb-0">Utilisateurs</h1>
    <div class="text-muted small">{{ $users->total() }} utilisateur(s)</div>
</div>

{{-- Filtres --}}
<div class="card stat-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.users.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Nom ou email..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body p-0">
        @if($users->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people display-4"></i>
                <p class="mt-3">Aucun utilisateur trouvé.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Utilisateur</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Commandes</th>
                            <th>Inscription</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                             style="width:36px;height:36px;">
                                            <i class="bi bi-person text-primary"></i>
                                        </div>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $user->email }}</td>
                                <td>
                                    @if($user->isAdmin())
                                        <span class="badge bg-warning text-dark">Admin</span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary">Client</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $user->orders_count }}</span>
                                </td>
                                <td class="text-muted small">{{ $user->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center p-4 border-top">
                <div class="text-muted small">{{ $users->total() }} utilisateur(s)</div>
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
