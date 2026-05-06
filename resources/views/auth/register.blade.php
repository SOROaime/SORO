@extends('layouts.app')

@section('title', 'Inscription')

@section('content')

<div class="auth-wrapper">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                {{-- Logo & titre --}}
                <div class="text-center mb-4 fade-in-up">
                    <div class="auth-logo mx-auto mb-3" style="background:linear-gradient(135deg,var(--accent),#fb923c);">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h2 class="fw-900 mb-1" style="letter-spacing:-.04em;">Créer un compte</h2>
                    <p class="text-muted" style="font-size:.9rem;">Rejoignez <strong>ShopCI</strong> gratuitement</p>
                </div>

                {{-- Card --}}
                <div class="auth-card card fade-in-up-1">
                    <div class="card-body p-4 p-sm-5">

                        <form action="{{ route('register') }}" method="POST" novalidate>
                            @csrf

                            {{-- Nom --}}
                            <div class="mb-4">
                                <label for="name" class="form-label">
                                    <i class="bi bi-person me-1 text-muted"></i>Nom complet
                                </label>
                                <input type="text"
                                       class="form-control form-control-lg @error('name') is-invalid @enderror"
                                       id="name" name="name"
                                       value="{{ old('name') }}"
                                       placeholder="Jean Dupont"
                                       autocomplete="name" autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-1 text-muted"></i>Adresse email
                                </label>
                                <input type="email"
                                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                                       id="email" name="email"
                                       value="{{ old('email') }}"
                                       placeholder="votre@email.com"
                                       autocomplete="email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Mot de passe --}}
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-1 text-muted"></i>Mot de passe
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control form-control-lg @error('password') is-invalid @enderror"
                                           id="password" name="password"
                                           placeholder="Minimum 8 caractères"
                                           autocomplete="new-password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePwd"
                                            style="border-left:0;border-radius:0 10px 10px 0;">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text mt-1" style="font-size:.78rem;">
                                    <i class="bi bi-info-circle me-1"></i>8 caractères minimum, avec majuscule et chiffre.
                                </div>
                            </div>

                            {{-- Confirmation --}}
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">
                                    <i class="bi bi-lock-fill me-1 text-muted"></i>Confirmer le mot de passe
                                </label>
                                <input type="password"
                                       class="form-control form-control-lg"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       placeholder="Répétez votre mot de passe"
                                       autocomplete="new-password">
                            </div>

                            {{-- Séparateur --}}
                            <div class="divider-section mb-4">
                                <span>Options avancées</span>
                            </div>

                            {{-- Case à cocher Admin --}}
                            <div class="admin-toggle mb-3" id="adminToggleBox">
                                <div class="form-check d-flex align-items-center gap-2 mb-0">
                                    <input class="form-check-input mt-0" type="checkbox"
                                           id="isAdmin" name="is_admin" value="1"
                                           {{ old('is_admin') ? 'checked' : '' }}>
                                    <label class="form-check-label mb-0" for="isAdmin"
                                           style="font-size:.875rem;font-weight:600;cursor:pointer;">
                                        <i class="bi bi-shield-lock me-1" style="color:var(--accent);"></i>
                                        Créer un compte administrateur
                                    </label>
                                </div>
                            </div>

                            {{-- Champ clé secrète -- masqué par défaut --}}
                            <div id="adminKeySection" style="display:none;" class="mb-4">
                                <div class="admin-key-warning mb-2">
                                    <i class="bi bi-exclamation-triangle-fill me-2" style="color:#f59e0b;"></i>
                                    Réservé aux responsables autorisés. Saisissez la clé fournie par l'administrateur principal.
                                </div>
                                <label for="admin_key" class="form-label">
                                    <i class="bi bi-key me-1 text-muted"></i>Clé secrète d'administration
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control form-control-lg @error('admin_key') is-invalid @enderror"
                                           id="admin_key" name="admin_key"
                                           placeholder="Clé secrète"
                                           autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleKey"
                                            style="border-left:0;border-radius:0 10px 10px 0;">
                                        <i class="bi bi-eye" id="eyeKeyIcon"></i>
                                    </button>
                                    @error('admin_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-700">
                                <i class="bi bi-person-check"></i>Créer mon compte
                            </button>
                        </form>
                    </div>
                </div>

                <p class="text-center text-muted mt-4" style="font-size:.9rem;">
                    Déjà un compte ?
                    <a href="{{ route('login') }}" class="fw-700 text-decoration-none" style="color:var(--primary);">
                        Se connecter
                    </a>
                </p>

            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .auth-wrapper {
        min-height: calc(100vh - 68px - 420px);
        background: linear-gradient(160deg, #f8fafc 60%, #fffbeb 100%);
    }
    .auth-logo {
        width: 64px; height: 64px;
        border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem; color: #fff;
        box-shadow: 0 8px 24px rgba(245,158,11,.3);
    }
    .auth-card {
        border-radius: 20px !important;
        box-shadow: 0 4px 32px rgba(0,0,0,.09), 0 1px 3px rgba(0,0,0,.05) !important;
        border: 1px solid var(--border) !important;
    }
    .divider-section {
        display: flex; align-items: center; gap: 12px;
        color: var(--text-muted); font-size: .78rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em;
    }
    .divider-section::before, .divider-section::after {
        content: ''; flex: 1; height: 1px; background: var(--border);
    }
    .admin-toggle {
        background: #fffbeb;
        border: 1.5px solid rgba(245,158,11,.25);
        border-radius: 12px;
        padding: .9rem 1.1rem;
    }
    .admin-key-warning {
        background: #fffbeb;
        border: 1px solid rgba(245,158,11,.3);
        border-radius: 10px;
        padding: .7rem .9rem;
        font-size: .8rem;
        font-weight: 600;
        color: #92400e;
    }
</style>
@endpush

@push('scripts')
<script>
    /* Toggle mot de passe */
    document.getElementById('togglePwd').addEventListener('click', function () {
        const pwd  = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        pwd.type = pwd.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    });

    /* Toggle clé secrète */
    document.getElementById('toggleKey').addEventListener('click', function () {
        const key  = document.getElementById('admin_key');
        const icon = document.getElementById('eyeKeyIcon');
        key.type = key.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    });

    /* Afficher/masquer section admin */
    const isAdminChk     = document.getElementById('isAdmin');
    const adminKeySection = document.getElementById('adminKeySection');
    const adminKeyInput   = document.getElementById('admin_key');

    function toggleAdminSection() {
        if (isAdminChk.checked) {
            adminKeySection.style.display = 'block';
            adminKeySection.style.animation = 'fadeInUp .3s ease';
            adminKeyInput.focus();
        } else {
            adminKeySection.style.display = 'none';
            adminKeyInput.value = '';
        }
    }

    isAdminChk.addEventListener('change', toggleAdminSection);

    /* Restaurer état après erreur de validation */
    @if(old('is_admin'))
        isAdminChk.checked = true;
        adminKeySection.style.display = 'block';
    @endif
</script>
@endpush
@endsection
