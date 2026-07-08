<form action="{{ $action }}" method="POST">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-3">
        <label class="form-label fw-700" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
            Code promo <span class="text-danger">*</span>
        </label>
        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
               value="{{ old('code', $coupon?->code) }}"
               placeholder="ex: BIENVENUE10"
               style="text-transform:uppercase;font-family:monospace;letter-spacing:.08em;font-weight:700;">
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6">
            <label class="form-label fw-700" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                Type <span class="text-danger">*</span>
            </label>
            <select name="type" id="couponType" class="form-select @error('type') is-invalid @enderror">
                <option value="percent" {{ old('type', $coupon?->type) === 'percent' ? 'selected' : '' }}>% Pourcentage</option>
                <option value="fixed"   {{ old('type', $coupon?->type) === 'fixed'   ? 'selected' : '' }}>Montant fixe (FCFA)</option>
            </select>
            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-6">
            <label class="form-label fw-700" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                Valeur <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <input type="number" name="value" step="0.01" min="0.01"
                       class="form-control @error('value') is-invalid @enderror"
                       value="{{ old('value', $coupon?->value) }}" placeholder="10">
                <span class="input-group-text" id="valueUnit">%</span>
            </div>
            @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6">
            <label class="form-label fw-700" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                Commande minimum (FCFA)
            </label>
            <input type="number" name="min_order_amount" min="0" step="100"
                   class="form-control @error('min_order_amount') is-invalid @enderror"
                   value="{{ old('min_order_amount', $coupon?->min_order_amount ?? 0) }}"
                   placeholder="0">
            @error('min_order_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-6">
            <label class="form-label fw-700" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                Nb. utilisations max
            </label>
            <input type="number" name="max_uses" min="1"
                   class="form-control @error('max_uses') is-invalid @enderror"
                   value="{{ old('max_uses', $coupon?->max_uses) }}"
                   placeholder="Illimité">
            @error('max_uses') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-700" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
            Date d'expiration
        </label>
        <input type="datetime-local" name="expires_at"
               class="form-control @error('expires_at') is-invalid @enderror"
               value="{{ old('expires_at', $coupon?->expires_at?->format('Y-m-d\TH:i')) }}">
        @error('expires_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-4">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                   {{ old('is_active', $coupon?->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-600" for="isActive">Coupon actif</label>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary fw-700">
            <i class="bi bi-check-circle me-1"></i>
            {{ $coupon ? 'Mettre à jour' : 'Créer le coupon' }}
        </button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>

<script>
document.getElementById('couponType').addEventListener('change', function () {
    document.getElementById('valueUnit').textContent = this.value === 'percent' ? '%' : 'FCFA';
});
// Init
document.getElementById('valueUnit').textContent =
    document.getElementById('couponType').value === 'percent' ? '%' : 'FCFA';
</script>
