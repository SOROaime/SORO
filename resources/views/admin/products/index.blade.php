@extends('layouts.admin')

@section('title', 'Produits')

@section('breadcrumb')
    <li class="breadcrumb-item active">Produits</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2 fw-bold mb-0">Gestion des produits</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Nouveau produit
    </a>
</div>

{{-- Filtres --}}
<div class="card stat-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.products.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Rechercher un produit..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary w-100">Réinitialiser</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body p-0">
        @if($products->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-box-seam display-4"></i>
                <p class="mt-3">Aucun produit trouvé.</p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Créer le premier produit</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Image</th>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Prix</th>
                            <th>Stock</th>
                            <th>Statut</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td class="ps-4">
                                    <img
                                        src="{{ $product->image_url }}"
                                        alt="{{ $product->name }}"
                                        class="rounded-2"
                                        style="width:45px;height:45px;object-fit:cover;"
                                        onerror="this.src='https://placehold.co/45x45/e2e8f0/94a3b8?text=?'"
                                    >
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ Str::limit($product->name, 40) }}</div>
                                    <div class="text-muted small">{{ Str::limit($product->description, 50) }}</div>
                                </td>
                                <td>
                                    @if($product->category)
                                        <span class="badge bg-primary-subtle text-primary">{{ $product->category }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-primary">{{ $product->formatted_price }}</td>
                                <td>
                                    @if($product->stock === 0)
                                        <span class="badge bg-danger">0 — Rupture</span>
                                    @elseif($product->stock <= 5)
                                        <span class="badge bg-warning text-dark">{{ $product->stock }} — Faible</span>
                                    @else
                                        <span class="badge bg-success">{{ $product->stock }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge bg-success-subtle text-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('products.show', $product) }}"
                                           class="btn btn-outline-secondary btn-sm"
                                           target="_blank" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                           class="btn btn-outline-primary btn-sm" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                              onsubmit="return confirm('Supprimer définitivement ce produit ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
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

            <div class="d-flex justify-content-between align-items-center p-4 border-top">
                <div class="text-muted small">
                    {{ $products->total() }} produit(s) au total
                </div>
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
