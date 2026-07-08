@extends('layouts.app')
@section('title', 'Mon profil')

@section('content')
<div class="container py-5" style="max-width:820px;">

    {{-- En-tête --}}
    <div class="d-flex align-items-center gap-3 mb-5">
        <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,var(--primary),#6366f1);
                    display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.4rem;font-weight:800;flex-shrink:0;">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h1 class="fw-800 mb-0" style="font-size:1.5rem;letter-spacing:-.04em;">Mon profil</h1>
            <p class="text-muted mb-0" style="font-size:.875rem;">Gérez vos informations personnelles et votre mot de passe</p>
        </div>
    </div>

    {{-- Alertes globales --}}
    @if(session('success'))
        <div class="alert d-flex align-items-center gap-2 mb-4"
             style="background:var(--success-l);border:1px solid #bbf7d0;color:#15803d;border-radius:12px;">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('success_password'))
        <div class="alert d-flex align-items-center gap-2 mb-4"
             style="background:var(--success-l);border:1px solid #bbf7d0;color:#15803d;border-radius:12px;">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            <span>{{ session('success_password') }}</span>
        </div>
    @endif

    {{-- ── Section 1 : Informations personnelles ── --}}
    <div class="card mb-4">
        <div class="card-body p-4">
            <h5 class="fw-700 mb-1" style="font-size:1rem;">Informations personnelles</h5>
            <p class="text-muted mb-4" style="font-size:.82rem;">Nom, adresse email et numéro de téléphone</p>

            @if($errors->has('name') || $errors->has('email') || $errors->has('phone'))
                <div class="alert d-flex align-items-center gap-2 mb-4"
                     style="background:var(--danger-l);border:1px solid #fecaca;color:var(--danger);border-radius:12px;">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" novalidate>
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600" style="font-size:.875rem;">Nom complet</label>
                        <div class="input-icon-group">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" name="name"
                                   class="form-control ps-icon @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600" style="font-size:.875rem;">Adresse email</label>
                        <div class="input-icon-group">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" name="email"
                                   class="form-control ps-icon @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600" style="font-size:.875rem;">Téléphone</label>
                        <div class="input-icon-group">
                            <i class="bi bi-telephone input-icon"></i>
                            <input type="text" name="phone"
                                   class="form-control ps-icon @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $user->phone) }}" placeholder="+225 07 00 00 00 00">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i>Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Section 2 : Changer le mot de passe ── --}}
    <div class="card">
        <div class="card-body p-4">
            <h5 class="fw-700 mb-1" style="font-size:1rem;">Changer le mot de passe</h5>
            <p class="text-muted mb-4" style="font-size:.82rem;">Pour votre sécurité, utilisez un mot de passe fort (8 caractères min., majuscule + chiffre)</p>

            @if($errors->has('current_password') || $errors->has('password'))
                <div class="alert d-flex align-items-center gap-2 mb-4"
                     style="background:var(--danger-l);border:1px solid #fecaca;color:var(--danger);border-radius:12px;">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                    <span>{{ $errors->first('current_password') ?: $errors->first('password') }}</span>
                </div>
            @endif

            <form action="{{ route('profile.password') }}" method="POST" novalidate>
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600" style="font-size:.875rem;">Mot de passe actuel</label>
                        <div class="input-icon-group">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" name="current_password"
                                   class="form-control ps-icon pe-icon @error('current_password') is-invalid @enderror"
                                   id="currentPwd" placeholder="••••••••">
                            <button type="button" class="input-icon-right" onclick="togglePwd('currentPwd','eyeCurrent')" tabindex="-1">
                                <i class="bi bi-eye" id="eyeCurrent"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600" style="font-size:.875rem;">Nouveau mot de passe</label>
                        <div class="input-icon-group">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <input type="password" name="password"
                                   class="form-control ps-icon pe-icon @error('password') is-invalid @enderror"
                                   id="newPwd" placeholder="••••••••">
                            <button type="button" class="input-icon-right" onclick="togglePwd('newPwd','eyeNew')" tabindex="-1">
                                <i class="bi bi-eye" id="eyeNew"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600" style="font-size:.875rem;">Confirmer le nouveau mot de passe</label>
                        <div class="input-icon-group">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <input type="password" name="password_confirmation"
                                   class="form-control ps-icon pe-icon"
                                   id="confirmPwd" placeholder="••••••••">
                            <button type="button" class="input-icon-right" onclick="togglePwd('confirmPwd','eyeConfirm')" tabindex="-1">
                                <i class="bi bi-eye" id="eyeConfirm"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-shield-lock"></i>Mettre à jour le mot de passe
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<style>
.input-icon-group { position: relative; }
.input-icon {
    position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
    color: var(--text-light); font-size: .95rem; pointer-events: none; z-index: 3;
}
.input-icon-right {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    background: none; border: none; color: var(--text-light);
    cursor: pointer; padding: 4px; z-index: 3;
    font-size: .95rem; line-height: 1;
    display: flex; align-items: center;
}
.input-icon-right:hover { color: var(--primary); }
.ps-icon { padding-left: 2.6rem !important; }
.pe-icon { padding-right: 2.8rem !important; }
</style>

<script>
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endsection
