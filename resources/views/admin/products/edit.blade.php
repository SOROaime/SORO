@extends('layouts.admin')

@section('title', 'Modifier ' . $product->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Produits</a></li>
    <li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2 fw-bold mb-0">Modifier : {{ Str::limit($product->name, 40) }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary" target="_blank">
            <i class="bi bi-eye me-1"></i>Voir
        </a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Retour
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="card stat-card mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Informations générales</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom du produit *</label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $product->name) }}">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description *</label>
                        <textarea name="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catégorie</label>
                        <div class="row g-2">
                            <div class="col-7">
                                <select name="category" class="form-select">
                                    <option value="">Choisir ou saisir</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ old('category', $product->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-5">
                                <input type="text" class="form-control"
                                       value="{{ old('category', $product->category) }}"
                                       placeholder="Nouvelle catégorie...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card stat-card mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-currency-euro me-2"></i>Prix et stock</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prix (€) *</label>
                            <div class="input-group">
                                <input type="number" name="price" step="0.01" min="0"
                                       class="form-control @error('price') is-invalid @enderror"
                                       value="{{ old('price', $product->price) }}">
                                <span class="input-group-text">€</span>
                            </div>
                            @error('price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Stock *</label>
                            <input type="number" name="stock" min="0"
                                   class="form-control @error('stock') is-invalid @enderror"
                                   value="{{ old('stock', $product->stock) }}">
                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card stat-card mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-image me-2"></i>Image et visibilité</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    {{-- Image actuelle --}}
                    @if($product->image)
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">Image actuelle</label><br>
                            <img src="{{ $product->image_url }}" class="rounded-3"
                                 style="max-height: 150px; max-width: 250px;" alt="{{ $product->name }}">
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nouvelle image (laisser vide pour conserver)</label>
                        <input type="file" name="image" id="imageInput"
                               class="form-control @error('image') is-invalid @enderror"
                               accept="image/*">
                        <div class="form-text">JPEG, PNG, WebP — max 2 Mo</div>
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <img id="imagePreview" class="rounded-3 mt-2 d-none"
                             style="max-height: 150px;" alt="Aperçu">
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active"
                               id="isActive" value="1"
                               {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="isActive">
                            Produit visible sur la boutique
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold">
                    <i class="bi bi-check-circle me-2"></i>Enregistrer
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-lg">Annuler</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = () => { preview.src = reader.result; preview.classList.remove('d-none'); };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection
