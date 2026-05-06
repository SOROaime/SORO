@extends('layouts.admin')

@section('title', 'Créer un produit')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Produits</a></li>
    <li class="breadcrumb-item active">Créer</li>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="fw-900 mb-1" style="font-size:1.8rem;letter-spacing:-.04em;">Nouveau produit</h1>
        <p class="text-muted mb-0" style="font-size:.88rem;">Ajoutez un nouveau produit à la boutique</p>
    </div>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger mb-4 d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                    <ul class="mb-0 ps-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            {{-- Informations générales --}}
            <div class="card stat-card mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="mb-0 fw-700 d-flex align-items-center gap-2">
                        <div style="width:32px;height:32px;background:#eff6ff;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:.9rem;">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>
                        Informations générales
                    </h5>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <div class="mb-3">
                        <label class="form-label">Nom du produit <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control form-control-lg @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="Ex : iPhone 15 Pro Max">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Description détaillée du produit...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label">Catégorie</label>

                        {{-- Champ caché qui sera réellement soumis --}}
                        <input type="hidden" name="category" id="categoryValue" value="{{ old('category') }}">

                        {{-- Onglets Existante / Nouvelle --}}
                        <div class="mb-2 d-flex gap-2">
                            <button type="button" id="tabExisting"
                                    class="btn btn-sm fw-600"
                                    style="background:var(--primary);color:#fff;border-radius:8px;">
                                <i class="bi bi-list-ul me-1"></i>Existante
                            </button>
                            <button type="button" id="tabNew"
                                    class="btn btn-sm btn-outline-secondary fw-600"
                                    style="border-radius:8px;">
                                <i class="bi bi-plus-circle me-1"></i>Nouvelle
                            </button>
                        </div>

                        {{-- Panneau : sélection existante --}}
                        <div id="panelExisting">
                            <select id="categorySelect"
                                    class="form-select @error('category') is-invalid @enderror">
                                <option value="">— Choisir une catégorie —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Panneau : nouvelle catégorie --}}
                        <div id="panelNew" class="d-none">
                            <input type="text" id="categoryNew"
                                   class="form-control"
                                   placeholder="Ex : Électronique, Vêtements..."
                                   value="">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Cette catégorie sera créée et disponible immédiatement dans la boutique.
                            </div>
                        </div>

                        @error('category')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Prix & Stock --}}
            <div class="card stat-card mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="mb-0 fw-700 d-flex align-items-center gap-2">
                        <div style="width:32px;height:32px;background:#dcfce7;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#16a34a;font-size:.9rem;">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        Prix et stock
                        <span class="badge ms-2" style="background:#fef9c3;color:#854d0e;font-size:.68rem;font-weight:600;border-radius:6px;">
                            <i class="bi bi-info-circle me-1"></i>Le stock se décrémente automatiquement à chaque commande
                        </span>
                    </h5>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Prix (FCFA) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text fw-700" style="font-size:.78rem;">FCFA</span>
                                <input type="number" name="price" step="0.01" min="0"
                                       class="form-control form-control-lg @error('price') is-invalid @enderror"
                                       value="{{ old('price') }}" placeholder="29.99">
                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock initial <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-boxes"></i></span>
                                <input type="number" name="stock" min="0"
                                       class="form-control form-control-lg @error('stock') is-invalid @enderror"
                                       value="{{ old('stock', 0) }}" placeholder="100" id="stockInput">
                                @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <div class="flex-grow-1" style="height:5px;background:#e2e8f0;border-radius:3px;overflow:hidden;">
                                    <div id="stockBar" style="height:100%;border-radius:3px;transition:width .3s,background .3s;width:0%;background:#22c55e;"></div>
                                </div>
                                <span id="stockLabel" style="font-size:.72rem;font-weight:700;white-space:nowrap;color:#64748b;">—</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 p-3 rounded-3 d-flex gap-2 align-items-start"
                         style="background:#f8fafc;border:1px solid var(--border);font-size:.8rem;color:var(--text-muted);">
                        <i class="bi bi-arrow-repeat text-primary mt-1 flex-shrink-0"></i>
                        <span>
                            Le stock est <strong>décrémenté automatiquement</strong> lors d'une commande validée,
                            et <strong>restitué</strong> si le paiement échoue ou si la commande est annulée.
                        </span>
                    </div>
                </div>
            </div>

            {{-- Image & Statut --}}
            <div class="card stat-card mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="mb-0 fw-700 d-flex align-items-center gap-2">
                        <div style="width:32px;height:32px;background:#f5f3ff;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#7c3aed;font-size:.9rem;">
                            <i class="bi bi-image-fill"></i>
                        </div>
                        Image et visibilité
                    </h5>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <div class="mb-4">
                        <label class="form-label">Image du produit</label>
                        <input type="file" name="image" id="imageInput"
                               class="form-control @error('image') is-invalid @enderror"
                               accept="image/*">
                        <div class="form-text"><i class="bi bi-info-circle me-1"></i>JPEG, PNG, WebP — max 2 Mo</div>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="mt-2 d-none" id="previewWrap">
                            <img id="imagePreview" class="rounded-3"
                                 style="max-height:140px;max-width:240px;object-fit:cover;box-shadow:0 4px 12px rgba(0,0,0,.1);" alt="Aperçu">
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 p-3 rounded-3"
                         style="background:#f8fafc;border:1px solid var(--border);">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_active"
                                   id="isActive" value="1" style="width:2.5rem;height:1.3rem;"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                        </div>
                        <div>
                            <label class="form-check-label fw-700 mb-0" for="isActive" style="cursor:pointer;">
                                Produit visible sur la boutique
                            </label>
                            <div class="text-muted" style="font-size:.78rem;">
                                Si désactivé, le produit ne sera pas visible par les clients.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary btn-lg px-5 fw-700">
                    <i class="bi bi-check-circle-fill"></i>Créer le produit
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-lg">
                    Annuler
                </a>
            </div>
        </form>
    </div>

    {{-- Panneau latéral --}}
    <div class="col-lg-4">
        <div class="card stat-card" style="position:sticky;top:90px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="mb-0 fw-700 d-flex align-items-center gap-2" style="font-size:.95rem;">
                    <i class="bi bi-lightbulb-fill" style="color:var(--accent);"></i>Conseils
                </h5>
            </div>
            <div class="card-body px-4 pb-4 pt-3">
                @foreach([
                    ['bi-type','var(--primary)','#eff6ff',        'Nom clair','Soyez précis : marque, modèle, caractéristiques clés.'],
                    ['bi-card-text','#7c3aed','#f5f3ff',          'Description','Décrivez les avantages et spécifications.'],
                    ['bi-image','#16a34a','#dcfce7',              'Image','Utilisez une image nette en 1:1 pour un rendu optimal.'],
                    ['bi-boxes','#f59e0b','#fef9c3',              'Stock','Renseignez un stock précis : il sera décrémenté à chaque commande validée.'],
                ] as $tip)
                <div class="d-flex gap-2 mb-3" style="font-size:.82rem;">
                    <div style="width:28px;height:28px;border-radius:8px;background:{{ $tip[2] }};color:{{ $tip[1] }};
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.8rem;">
                        <i class="bi {{ $tip[0] }}"></i>
                    </div>
                    <div>
                        <div class="fw-700 mb-0" style="color:var(--text);">{{ $tip[3] }}</div>
                        <div class="text-muted" style="line-height:1.4;">{{ $tip[4] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = r => {
                const img = document.getElementById('imagePreview');
                img.src = r.result;
                document.getElementById('previewWrap').classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    // ── Gestion onglets catégorie ──
    const tabExisting  = document.getElementById('tabExisting');
    const tabNew       = document.getElementById('tabNew');
    const panelExisting= document.getElementById('panelExisting');
    const panelNew     = document.getElementById('panelNew');
    const catValue     = document.getElementById('categoryValue');
    const catSelect    = document.getElementById('categorySelect');
    const catNew       = document.getElementById('categoryNew');

    function setTab(mode) {
        if (mode === 'existing') {
            panelExisting.classList.remove('d-none');
            panelNew.classList.add('d-none');
            tabExisting.style.background = 'var(--primary)'; tabExisting.style.color = '#fff';
            tabNew.style.background = ''; tabNew.style.color = '';
            tabNew.className = 'btn btn-sm btn-outline-secondary fw-600';
            catValue.value = catSelect.value;
        } else {
            panelNew.classList.remove('d-none');
            panelExisting.classList.add('d-none');
            tabNew.style.background = 'var(--primary)'; tabNew.style.color = '#fff';
            tabNew.className = 'btn btn-sm fw-600';
            tabExisting.style.background = ''; tabExisting.style.color = '';
            tabExisting.className = 'btn btn-sm btn-outline-secondary fw-600';
            catValue.value = catNew.value.trim();
        }
    }

    tabExisting.addEventListener('click', () => setTab('existing'));
    tabNew.addEventListener('click',      () => setTab('new'));

    // Sync en temps réel
    catSelect.addEventListener('change', () => { catValue.value = catSelect.value; });
    catNew.addEventListener('input',     () => { catValue.value = catNew.value.trim(); });

    // Init : si old('category') est une valeur non listée → mode nouvelle
    (function() {
        const oldVal = catValue.value;
        if (!oldVal) return;
        const opts = Array.from(catSelect.options).map(o => o.value);
        if (opts.includes(oldVal)) {
            catSelect.value = oldVal;
            setTab('existing');
        } else {
            catNew.value = oldVal;
            setTab('new');
        }
    })();

    const stockInput = document.getElementById('stockInput');
    const stockBar   = document.getElementById('stockBar');
    const stockLabel = document.getElementById('stockLabel');

    function updateStockIndicator() {
        const v = parseInt(stockInput.value) || 0;
        let pct, color, label;
        if (v === 0)      { pct = 2;   color = '#ef4444'; label = 'Rupture'; }
        else if (v <= 5)  { pct = 15;  color = '#f59e0b'; label = 'Très faible'; }
        else if (v <= 20) { pct = 40;  color = '#f59e0b'; label = 'Faible'; }
        else if (v <= 50) { pct = 70;  color = '#22c55e'; label = 'Bon'; }
        else              { pct = 100; color = '#16a34a'; label = 'Excellent'; }
        stockBar.style.width      = pct + '%';
        stockBar.style.background = color;
        stockLabel.textContent    = v + ' — ' + label;
        stockLabel.style.color    = color;
    }
    stockInput.addEventListener('input', updateStockIndicator);
    updateStockIndicator();
</script>
@endpush
@endsection
