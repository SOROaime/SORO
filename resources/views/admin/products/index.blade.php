@extends('layouts.admin')

@section('title', 'Produits')

@section('breadcrumb')
    <li class="breadcrumb-item active">Produits</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="fw-900 mb-1" style="font-size:1.8rem;letter-spacing:-.04em;">Gestion des produits</h1>
        <p class="text-muted mb-0" style="font-size:.88rem;">
            <span class="fw-700 text-primary">{{ $products->total() }}</span> produit(s) au total
        </p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary fw-600">
        <i class="bi bi-plus-lg me-2"></i>Nouveau produit
    </a>
</div>

{{-- Filtres --}}
<div class="card stat-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.products.index') }}" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text" style="background:#f8fafc;border:1.5px solid var(--border);">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Rechercher un produit..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 fw-600">
                        <i class="bi bi-funnel me-1"></i>Filtrer
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body p-0">
        @if($products->isEmpty())
            <div class="text-center py-5 text-muted">
                <div style="width:72px;height:72px;background:#f1f5f9;border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="bi bi-box-seam fs-2"></i>
                </div>
                <p class="mb-2 fw-600">Aucun produit trouvé.</p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus me-1"></i>Créer le premier produit
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:.88rem;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th class="ps-4 fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;padding:.85rem .75rem;">Photo</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Produit</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Catégorie</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Prix</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Stock</th>
                            <th class="fw-600 text-muted border-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Statut</th>
                            <th class="fw-600 text-muted border-0 text-end pe-4" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td class="ps-4">
                                <img src="{{ $product->image_url }}"
                                     alt="{{ $product->name }}"
                                     class="rounded-3"
                                     style="width:48px;height:48px;object-fit:cover;"
                                     onerror="this.src='https://placehold.co/48x48/e2e8f0/94a3b8?text=?'">
                            </td>
                            <td>
                                <div class="fw-700">{{ Str::limit($product->name, 38) }}</div>
                                <div class="text-muted" style="font-size:.78rem;">{{ Str::limit($product->description, 48) }}</div>
                            </td>
                            <td>
                                @if($product->category)
                                    <span style="padding:.25em .7em;border-radius:20px;font-size:.72rem;font-weight:700;
                                                 background:#dbeafe;color:#1d4ed8;">
                                        {{ $product->category }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="fw-800" style="color:var(--primary);">{{ $product->formatted_price }}</td>
                            <td>
                                @if($product->stock === 0)
                                    <span style="padding:.25em .7em;border-radius:20px;font-size:.72rem;font-weight:800;background:#fee2e2;color:#dc2626;">
                                        <i class="bi bi-x-circle me-1"></i>Rupture
                                    </span>
                                @elseif($product->stock <= 5)
                                    <span style="padding:.25em .7em;border-radius:20px;font-size:.72rem;font-weight:800;background:#fef9c3;color:#854d0e;">
                                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $product->stock }} — Faible
                                    </span>
                                @else
                                    <span style="padding:.25em .7em;border-radius:20px;font-size:.72rem;font-weight:800;background:#dcfce7;color:#16a34a;">
                                        <i class="bi bi-check-circle me-1"></i>{{ $product->stock }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_active)
                                    <span style="padding:.25em .7em;border-radius:20px;font-size:.72rem;font-weight:700;background:#dcfce7;color:#166534;">
                                        <i class="bi bi-circle-fill me-1" style="font-size:.4rem;vertical-align:middle;"></i>Actif
                                    </span>
                                @else
                                    <span style="padding:.25em .7em;border-radius:20px;font-size:.72rem;font-weight:700;background:#f1f5f9;color:#64748b;">
                                        <i class="bi bi-circle me-1" style="font-size:.4rem;vertical-align:middle;"></i>Inactif
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('products.show', $product) }}"
                                       class="btn btn-sm"
                                       style="background:#f1f5f9;border:none;color:var(--dark);width:32px;height:32px;padding:0;border-radius:8px;display:flex;align-items:center;justify-content:center;"
                                       target="_blank" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="btn btn-sm"
                                       style="background:#dbeafe;border:none;color:var(--primary);width:32px;height:32px;padding:0;border-radius:8px;display:flex;align-items:center;justify-content:center;"
                                       title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                          onsubmit="return confirm('Supprimer définitivement {{ addslashes($product->name) }} ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm"
                                                style="background:#fee2e2;border:none;color:#dc2626;width:32px;height:32px;padding:0;border-radius:8px;display:flex;align-items:center;justify-content:center;"
                                                title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center px-4 py-3"
                 style="border-top:1px solid var(--border);">
                <div class="text-muted" style="font-size:.82rem;">
                    {{ $products->total() }} produit(s) — Page {{ $products->currentPage() }} / {{ $products->lastPage() }}
                </div>
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
