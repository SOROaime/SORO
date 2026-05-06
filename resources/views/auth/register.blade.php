@extends('layouts.app')
@section('title', 'Inscription')

@section('content')

<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center align-items-center"
             style="min-height:calc(100vh - var(--navbar-h) - 60px);">
            <div class="col-md-10 col-lg-9 col-xl-8">
                <div class="auth-grid fade-in-up">

                    {{-- ─── Panneau gauche ─── --}}
                    <div class="auth-left d-none d-md-flex">
                        <div class="auth-left-inner">
                            <div class="auth-brand mb-auto">
                                <div class="brand-icon-lg mb-3">
                                    <i class="bi bi-person-plus-fill"></i>
                                </div>
                                <div class="fw-900 mb-1"
                                     style="font-size:1.5rem;letter-spacing:-.05em;color:#fff;">
                                    Rejoignez-nous !
                                </div>
                                <div style="font-size:.82rem;color:rgba(255,255,255,.5);">
                                    Inscription 100% gratuite
                                </div>
                            </div>

                            <div class="auth-perks">
                                @foreach([
                                    ['bi-bag-check-fill',   '#60a5fa', 'Accès au catalogue complet'],
                                    ['bi-shield-check',     '#4ade80', 'Paiements 100% sécurisés'],
                                    ['bi-truck-front-fill', '#fbbf24', 'Livraison gratuite partout'],
                                    ['bi-clock-history',    '#a78bfa', 'Suivi de vos commandes'],
                                ] as $p)
                                <div class="auth-perk-item">
                                    <div class="perk-icon" style="color:{{ $p[1] }};">
                                        <i class="bi {{ $p[0] }}"></i>
                                    </div>
                                    <span>{{ $p[2] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- ─── Panneau droit (formulaire) ─── --}}
                    <div class="auth-right">
                        <div class="text-center mb-4">
                            <div class="auth-logo-sm d-flex d-md-none mx-auto mb-3">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>
                            <h2 class="fw-900 mb-1"
                                style="letter-spacing:-.045em;font-size:1.6rem;">
                                Créer un compte
                            </h2>
                            <p class="text-muted" style="font-size:.875rem;">
                                Rejoignez <strong>ShopCI</strong> gratuitement
                            </p>
                        </div>

                        <form action="{{ route('register') }}" method="POST" novalidate>
                            @csrf

                            @if($errors->any() && !$errors->has('name') && !$errors->has('email') && !$errors->has('password') && !$errors->has('admin_key'))
                                <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
                                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                                    <span>{{ $errors->first() }}</span>
                                </div>
                            @endif

                            {{-- Nom --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom complet</label>
                                <div class="input-icon-group">
                                    <i class="bi bi-person input-icon"></i>
                                    <input type="text" id="name" name="name"
                                           class="form-control form-control-lg ps-icon @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}"
                                           placeholder="Jean Dupont"
                                           autocomplete="name" autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label">Adresse email</label>
                                <div class="input-icon-group">
                                    <i class="bi bi-envelope input-icon"></i>
                                    <input type="email" id="email" name="email"
                                           class="form-control form-control-lg ps-icon @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}"
                                           placeholder="votre@email.com"
                                           autocomplete="email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Mot de passe --}}
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <div class="input-icon-group">
                                    <i class="bi bi-lock input-icon"></i>
                                    <input type="password" id="password" name="password"
                                           class="form-control form-control-lg ps-icon pe-icon @error('password') is-invalid @enderror"
                                           placeholder="Minimum 8 caractères"
                                           autocomplete="new-password">
                                    <button type="button" class="input-icon-right" id="togglePwd" tabindex="-1">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text" style="font-size:.76rem;margin-top:.3rem;">
                                    <i class="bi bi-info-circle me-1"></i>
                                    8 caractères min., avec majuscule et chiffre.
                                </div>
                            </div>

                            {{-- Confirmation --}}
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">
                                    Confirmer le mot de passe
                                </label>
                                <div class="input-icon-group">
                                    <i class="bi bi-lock-fill input-icon"></i>
                                    <input type="password" id="password_confirmation"
                                           name="password_confirmation"
                                           class="form-control form-control-lg ps-icon pe-icon"
                                           placeholder="Répétez votre mot de passe"
                                           autocomplete="new-password">
                                    <button type="button" class="input-icon-right" id="togglePwd2" tabindex="-1">
                                        <i class="bi bi-eye" id="eyeIcon2"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Séparateur --}}
                            <div class="divider-section mb-3">
                                <span>Options avancées</span>
                            </div>

                            {{-- Toggle admin --}}
                            <div class="admin-toggle mb-2" id="adminToggleBox">
                                <div class="form-check d-flex align-items-center gap-2 mb-0">
                                    <input class="form-check-input mt-0" type="checkbox"
                                           id="isAdmin" name="is_admin" value="1"
                                           {{ old('is_admin') ? 'checked' : '' }}>
                                    <label class="form-check-label mb-0 fw-600"
                                           for="isAdmin"
                                           style="font-size:.875rem;cursor:pointer;">
                                        <i class="bi bi-shield-lock me-1"
                                           style="color:var(--accent);"></i>
                                        Compte administrateur
                                    </label>
                                </div>
                            </div>

                            {{-- Clé admin --}}
                            <div id="adminKeySection"
                                 style="display:none;"
                                 class="mb-3">
                                <div class="admin-key-warning mb-2">
                                    <i class="bi bi-exclamation-triangle-fill me-2"
                                       style="color:var(--accent);"></i>
                                    Réservé aux responsables autorisés. Clé fournie par l'administrateur.
                                </div>
                                <div class="input-icon-group">
                                    <i class="bi bi-key input-icon"></i>
                                    <input type="password" id="admin_key" name="admin_key"
                                           class="form-control form-control-lg ps-icon pe-icon @error('admin_key') is-invalid @enderror"
                                           placeholder="Clé secrète"
                                           autocomplete="off">
                                    <button type="button" class="input-icon-right"
                                            id="toggleKey" tabindex="-1">
                                        <i class="bi bi-eye" id="eyeKeyIcon"></i>
                                    </button>
                                    @error('admin_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit"
                                    class="btn btn-primary btn-lg w-100 fw-700 mt-2 mb-3">
                                <i class="bi bi-person-check"></i>Créer mon compte
                            </button>
                        </form>

                        <p class="text-center text-muted" style="font-size:.875rem;">
                            Déjà un compte ?
                            <a href="{{ route('login') }}"
                               class="fw-700 text-decoration-none"
                               style="color:var(--primary);">
                                Se connecter
                            </a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    footer { margin-top: 0 !important; }

    .auth-wrapper {
        background: linear-gradient(160deg, #fffbeb 0%, #f8fafc 60%, var(--primary-xl) 100%);
        padding: 2rem 0;
    }

    .auth-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 8px 48px rgba(0,0,0,.12), 0 2px 8px rgba(0,0,0,.06);
        border: 1px solid var(--border);
        background: #fff;
    }
    @media (max-width: 767px) {
        .auth-grid { grid-template-columns: 1fr; }
    }

    .auth-left {
        background: linear-gradient(145deg, #1a1a2e 0%, #16213e 55%, #0f3460 100%);
        padding: 2.5rem;
        flex-direction: column;
        position: relative; overflow: hidden;
    }
    .auth-left::before {
        content: '';
        position: absolute;
        width: 280px; height: 280px;
        background: radial-gradient(circle, rgba(245,158,11,.18) 0%, transparent 70%);
        top: -80px; right: -60px;
        pointer-events: none;
    }
    .auth-left::after {
        content: '';
        position: absolute;
        width: 200px; height: 200px;
        background: radial-gradient(circle, rgba(37,99,235,.14) 0%, transparent 70%);
        bottom: -50px; left: -30px;
        pointer-events: none;
    }
    .auth-left-inner {
        display: flex; flex-direction: column;
        height: 100%; position: relative; z-index: 1;
    }
    .brand-icon-lg {
        width: 52px; height: 52px;
        background: linear-gradient(135deg, var(--accent), #fb923c);
        border-radius: 15px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1.5rem;
        box-shadow: 0 6px 20px rgba(245,158,11,.35);
    }
    .auth-perks {
        display: flex; flex-direction: column; gap: 10px;
        margin-top: auto;
    }
    .auth-perk-item {
        display: flex; align-items: center; gap: 10px;
        font-size: .84rem; font-weight: 500;
        color: rgba(255,255,255,.68);
        padding: .6rem .85rem;
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 10px;
    }
    .perk-icon {
        width: 26px; height: 26px;
        display: flex; align-items: center; justify-content: center;
        font-size: .95rem; flex-shrink: 0;
    }

    .auth-right {
        padding: 2.2rem 2.5rem 2rem;
        display: flex; flex-direction: column; justify-content: center;
    }
    @media (max-width: 575px) {
        .auth-right { padding: 1.75rem 1.5rem; }
    }

    .auth-logo-sm {
        width: 50px; height: 50px;
        background: linear-gradient(135deg, var(--accent), #fb923c);
        border-radius: 14px;
        align-items: center; justify-content: center;
        font-size: 1.4rem; color: #fff;
        box-shadow: 0 6px 20px rgba(245,158,11,.3);
    }

    /* Input icons */
    .input-icon-group { position: relative; }
    .input-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: var(--text-light); font-size: .95rem; pointer-events: none; z-index: 3;
    }
    .input-icon-right {
        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
        background: none; border: none; color: var(--text-light);
        cursor: pointer; padding: 4px; z-index: 3;
        transition: color .2s; font-size: .95rem; line-height: 1;
        display: flex; align-items: center;
    }
    .input-icon-right:hover { color: var(--primary); }
    .ps-icon { padding-left: 2.6rem !important; }
    .pe-icon { padding-right: 2.8rem !important; }

    /* Divider */
    .divider-section {
        display: flex; align-items: center; gap: 10px;
        color: var(--text-muted); font-size: .72rem;
        font-weight: 600; text-transform: uppercase; letter-spacing: .07em;
    }
    .divider-section::before, .divider-section::after {
        content: ''; flex: 1; height: 1px; background: var(--border);
    }

    /* Admin toggle */
    .admin-toggle {
        background: var(--accent-l);
        border: 1.5px solid rgba(245,158,11,.22);
        border-radius: 11px;
        padding: .85rem 1rem;
    }
    .admin-key-warning {
        background: #fffbeb;
        border: 1px solid rgba(245,158,11,.3);
        border-radius: 9px;
        padding: .65rem .9rem;
        font-size: .79rem; font-weight: 600;
        color: #92400e;
        display: flex; align-items: flex-start;
    }
</style>
@endpush

@push('scripts')
<script>
    /* Toggle mot de passe */
    function makeToggle(btnId, inputId, iconId) {
        document.getElementById(btnId).addEventListener('click', function () {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.className = input.type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }
    makeToggle('togglePwd',  'password', 'eyeIcon');
    makeToggle('togglePwd2', 'password_confirmation', 'eyeIcon2');
    makeToggle('toggleKey',  'admin_key', 'eyeKeyIcon');

    /* Section clé admin */
    const isAdminChk      = document.getElementById('isAdmin');
    const adminKeySection = document.getElementById('adminKeySection');
    const adminKeyInput   = document.getElementById('admin_key');

    function toggleAdminSection() {
        if (isAdminChk.checked) {
            adminKeySection.style.display  = 'block';
            adminKeySection.style.animation = 'fadeInUp .3s ease';
            adminKeyInput.focus();
        } else {
            adminKeySection.style.display = 'none';
            adminKeyInput.value = '';
        }
    }
    isAdminChk.addEventListener('change', toggleAdminSection);

    @if(old('is_admin'))
        isAdminChk.checked = true;
        adminKeySection.style.display = 'block';
    @endif
</script>
@endpush
@endsection
