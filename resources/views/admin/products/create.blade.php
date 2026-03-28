@extends('layouts.admin')

@section('title', 'Créer un produit')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Produits</a></li>
    <li class="breadcrumb-item active">Créer</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2 fw-bold mb-0">Créer un produit</h1>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Retour
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Erreurs globales --}}
            @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Informations générales --}}
            <div class="card stat-card mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Informations générales</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    {{-- Nom --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom du produit <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="Ex: iPhone 15 Pro Max">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                        <textarea name="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Description détaillée du produit...">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Catégorie --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catégorie</label>
                        <div class="row g-2">
                            <div class="col-7">
                                <select name="category" id="categorySelect"
                                        class="form-select @error('category') is-invalid @enderror">
                                    <option value="">Choisir ou saisir ci-dessous</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-5">
                                <input type="text" id="categoryNew" class="form-control"
                                       placeholder="Nouvelle catégorie...">
                            </div>
                        </div>
                        @error('category') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Prix & Stock --}}
            <div class="card stat-card mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-currency-euro me-2"></i>Prix et stock</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prix (€) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="price" step="0.01" min="0"
                                       class="form-control @error('price') is-invalid @enderror"
                                       value="{{ old('price') }}"
                                       placeholder="29.99">
                                <span class="input-group-text">€</span>
                                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
                            <input type="number" name="stock" min="0"
                                   class="form-control @error('stock') is-invalid @enderror"
                                   value="{{ old('stock', 0) }}"
                                   placeholder="100">
                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Image & Statut --}}
            <div class="card stat-card mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-image me-2"></i>Image et visibilité</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Image du produit</label>
                        <input type="file" name="image" id="imageInput"
                               class="form-control @error('image') is-invalid @enderror"
                               accept="image/*">
                        <div class="form-text">JPEG, PNG, WebP — max 2 Mo</div>
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="mt-2">
                            <img id="imagePreview" class="rounded-3 d-none"
                                 style="max-height: 150px; max-width: 250px;" alt="Aperçu">
                        </div>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active"
                               id="isActive" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="isActive">
                            Produit visible sur la boutique
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold">
                    <i class="bi bi-check-circle me-2"></i>Créer le produit
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-lg">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Aperçu de l'image
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = () => { preview.src = reader.result; preview.classList.remove('d-none'); };
            reader.readAsDataURL(file);
        }
    });

    // Catégorie : saisie manuelle override le select
    document.getElementById('categoryNew').addEventListener('input', function() {
        document.getElementById('categorySelect').value = '';
        document.getElementsByName('category')[0].value = this.value;
    });
    document.getElementById('categorySelect').addEventListener('change', function() {
        document.getElementById('categoryNew').value = '';
    });
</script>
@endpush
@endsection
