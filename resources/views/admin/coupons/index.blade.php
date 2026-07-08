@extends('layouts.app')
@section('title', 'Coupons promo')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="section-title mb-0"><i class="bi bi-tag-fill me-2" style="color:var(--accent)"></i>Coupons promotionnels</h1>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary fw-700">
            <i class="bi bi-plus-circle me-1"></i>Nouveau coupon
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
        </div>
    @endif

    <div class="card" style="border-radius:16px;border:1.5px solid var(--border);">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f8fafc;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                    <tr>
                        <th class="px-4 py-3">Code</th>
                        <th class="py-3">Type</th>
                        <th class="py-3">Valeur</th>
                        <th class="py-3">Min. commande</th>
                        <th class="py-3">Utilisations</th>
                        <th class="py-3">Expiration</th>
                        <th class="py-3">Statut</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size:.88rem;">
                    @forelse($coupons as $coupon)
                    <tr>
                        <td class="px-4 py-3 fw-800" style="font-family:monospace;letter-spacing:.05em;color:var(--primary);">
                            {{ $coupon->code }}
                        </td>
                        <td class="py-3">
                            @if($coupon->type === 'percent')
                                <span class="badge" style="background:#ede9fe;color:#7c3aed;">% Pourcentage</span>
                            @else
                                <span class="badge" style="background:#dcfce7;color:#16a34a;">Montant fixe</span>
                            @endif
                        </td>
                        <td class="py-3 fw-700">{{ $coupon->getTypeLabel() }}</td>
                        <td class="py-3">
                            {{ $coupon->min_order_amount > 0 ? number_format($coupon->min_order_amount, 0, ',', ' ') . ' FCFA' : '—' }}
                        </td>
                        <td class="py-3">
                            {{ $coupon->used_count }}{{ $coupon->max_uses ? ' / ' . $coupon->max_uses : '' }}
                        </td>
                        <td class="py-3">
                            @if($coupon->expires_at)
                                <span class="{{ $coupon->expires_at->isPast() ? 'text-danger' : 'text-muted' }}">
                                    {{ $coupon->expires_at->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-muted">Sans limite</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($coupon->isValid())
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-secondary">Inactif</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                   class="btn btn-sm btn-outline-primary" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer ce coupon ?')" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-tag fs-2 d-block mb-2"></i>
                            Aucun coupon créé. <a href="{{ route('admin.coupons.create') }}">Créer le premier</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
        <div class="px-4 py-3">{{ $coupons->links() }}</div>
        @endif
    </div>
</div>
@endsection
