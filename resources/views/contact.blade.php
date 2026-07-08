@extends('layouts.app')
@section('title', 'Contact')
@section('seo_title', 'Contactez ShopCI — Support client Côte d\'Ivoire')
@section('seo_description', 'Contactez l\'équipe ShopCI. Nous répondons à toutes vos questions sur vos commandes, livraisons et paiements dans les 24h.')

@section('content')

<div class="page-header-bar">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                <li class="breadcrumb-item active">Contact</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5 justify-content-center">

        {{-- ─── COLONNE GAUCHE : Infos ─── --}}
        <div class="col-lg-4">
            <div class="mb-4">
                <div class="section-label mb-1">Support</div>
                <h1 class="section-title mb-3">Contactez-nous</h1>
                <p class="text-muted" style="font-size:.92rem;line-height:1.8;">
                    Notre équipe est disponible 7j/7 pour répondre à vos questions sur vos commandes, livraisons ou paiements.
                </p>
            </div>

            <div class="d-flex flex-column gap-3">
                @foreach([
                    ['bi-envelope-fill',  '#2563eb', '#eff6ff', '#dbeafe', 'Email',          config('mail.from.address'),  null],
                    ['bi-clock-fill',     '#16a34a', '#f0fdf4', '#dcfce7', 'Heures',          'Lun – Sam : 8h – 20h',       null],
                    ['bi-geo-alt-fill',   '#d97706', '#fffbeb', '#fef3c7', 'Zone de livraison','Partout en Côte d\'Ivoire', null],
                    ['bi-headset',        '#7c3aed', '#f5f3ff', '#ede9fe', 'Délai de réponse','Moins de 24h ouvrées',       null],
                ] as $info)
                <div class="d-flex align-items-start gap-3 p-3 rounded-3"
                     style="background:{{ $info[2] }};border:1px solid {{ $info[3] }};">
                    <div style="width:38px;height:38px;border-radius:10px;background:{{ $info[3] }};
                                display:flex;align-items:center;justify-content:center;
                                color:{{ $info[1] }};font-size:1rem;flex-shrink:0;">
                        <i class="bi {{ $info[0] }}"></i>
                    </div>
                    <div>
                        <div class="fw-700" style="font-size:.82rem;color:{{ $info[1] }};text-transform:uppercase;letter-spacing:.05em;">
                            {{ $info[4] }}
                        </div>
                        <div style="font-size:.88rem;color:var(--text);">{{ $info[5] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ─── COLONNE DROITE : Formulaire ─── --}}
        <div class="col-lg-7">
            <div class="card" style="border-radius:20px;border:1.5px solid var(--border);box-shadow:var(--shadow-md);">
                <div class="card-body p-4 p-md-5">

                    @if(session('success'))
                        <div class="alert d-flex align-items-center gap-2 mb-4 fade-in-up"
                             style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;border-radius:12px;">
                            <i class="bi bi-check-circle-fill fs-5 flex-shrink-0"></i>
                            <div>
                                <div class="fw-700">Message envoyé !</div>
                                <div style="font-size:.87rem;">{{ session('success') }}</div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert d-flex align-items-center gap-2 mb-4"
                             style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:12px;">
                            <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <h2 class="fw-800 mb-1" style="font-size:1.3rem;letter-spacing:-.04em;">
                        Envoyer un message
                    </h2>
                    <p class="text-muted mb-4" style="font-size:.85rem;">Tous les champs marqués * sont obligatoires.</p>

                    <form action="{{ route('contact.send') }}" method="POST" novalidate>
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-600" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                    Nom complet <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', auth()->user()?->name) }}"
                                       placeholder="Jean Kouassi">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', auth()->user()?->email) }}"
                                       placeholder="votre@email.com">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-600" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                    Téléphone <span class="text-muted fw-400">(optionnel)</span>
                                </label>
                                <input type="tel" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', auth()->user()?->phone) }}"
                                       placeholder="07 XX XX XX XX">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                    Sujet <span class="text-danger">*</span>
                                </label>
                                <select name="subject" class="form-select @error('subject') is-invalid @enderror">
                                    <option value="">-- Choisir --</option>
                                    @foreach([
                                        'Suivi de commande',
                                        'Problème de paiement',
                                        'Livraison',
                                        'Retour / Remboursement',
                                        'Question sur un produit',
                                        'Problème technique',
                                        'Autre',
                                    ] as $s)
                                        <option value="{{ $s }}" {{ old('subject') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-600" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">
                                Message <span class="text-danger">*</span>
                            </label>
                            <textarea name="message" rows="5"
                                      class="form-control @error('message') is-invalid @enderror"
                                      placeholder="Décrivez votre demande en détail...">{{ old('message') }}</textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg fw-700 w-100" style="border-radius:12px;">
                            <i class="bi bi-send me-2"></i>Envoyer le message
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
