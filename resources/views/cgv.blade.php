@extends('layouts.app')
@section('title', 'Conditions Générales de Vente')
@section('seo_title', 'CGV — Conditions Générales de Vente ShopCI')
@section('seo_description', 'Consultez les Conditions Générales de Vente de ShopCI, votre boutique en ligne en Côte d\'Ivoire. Commandes, paiements, livraisons, retours et protection des données.')

@section('content')

<div class="page-header-bar">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                <li class="breadcrumb-item active">CGV</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5" style="max-width:860px;">

    <div class="mb-5">
        <div class="section-label mb-1">Légal</div>
        <h1 class="section-title mb-2">Conditions Générales de Vente</h1>
        <p class="text-muted" style="font-size:.85rem;">
            <i class="bi bi-calendar3 me-1"></i>Dernière mise à jour : {{ date('d/m/Y') }}
        </p>
    </div>

    @php
    $sections = [
        [
            'icon' => 'bi-shop', 'color' => '#2563eb', 'bg' => '#eff6ff',
            'title' => '1. Identification du vendeur',
            'body'  => '<p>Le site <strong>ShopCI</strong> est exploité par :</p>
                        <ul>
                            <li><strong>Raison sociale :</strong> ShopCI</li>
                            <li><strong>Activité :</strong> Commerce en ligne de produits divers</li>
                            <li><strong>Zone de livraison :</strong> Côte d\'Ivoire</li>
                            <li><strong>Email :</strong> ' . config('mail.from.address') . '</li>
                        </ul>',
        ],
        [
            'icon' => 'bi-bag-check', 'color' => '#16a34a', 'bg' => '#f0fdf4',
            'title' => '2. Commandes',
            'body'  => '<p>Toute commande passée sur ShopCI implique l\'acceptation sans réserve des présentes CGV.</p>
                        <ul>
                            <li>Les commandes sont validées après confirmation du paiement ou accord de paiement à la livraison.</li>
                            <li>ShopCI se réserve le droit d\'annuler toute commande en cas de rupture de stock ou d\'information erronée.</li>
                            <li>Un email de confirmation vous est envoyé dès validation de votre commande.</li>
                            <li>Vous pouvez annuler une commande avant qu\'elle soit expédiée depuis votre espace "Mes commandes".</li>
                        </ul>',
        ],
        [
            'icon' => 'bi-credit-card', 'color' => '#7c3aed', 'bg' => '#f5f3ff',
            'title' => '3. Prix et paiement',
            'body'  => '<p>Les prix affichés sont en <strong>Francs CFA (XOF)</strong>, toutes taxes comprises.</p>
                        <ul>
                            <li><strong>Paiement en ligne</strong> sécurisé via GeniusPay (Orange Money, MTN Money, Wave, carte bancaire).</li>
                            <li><strong>Paiement à la livraison</strong> en espèces disponible.</li>
                            <li><strong>Paiement échelonné</strong> : possibilité de payer en 2x, 3x ou 4x sans frais supplémentaires.</li>
                            <li>Tous les paiements en ligne sont protégés par un code OTP à usage unique envoyé par email.</li>
                            <li>ShopCI ne stocke aucune donnée bancaire sur ses serveurs.</li>
                        </ul>',
        ],
        [
            'icon' => 'bi-truck-front', 'color' => '#d97706', 'bg' => '#fffbeb',
            'title' => '4. Livraison',
            'body'  => '<ul>
                            <li><strong>Livraison gratuite</strong> partout en Côte d\'Ivoire.</li>
                            <li>Délai de livraison estimé : <strong>24 à 72 heures</strong> selon la localisation.</li>
                            <li>Le livreur vous contacte par téléphone avant la livraison.</li>
                            <li>En cas d\'absence, une nouvelle tentative de livraison sera organisée.</li>
                            <li>ShopCI ne saurait être tenu responsable des retards liés à des événements de force majeure.</li>
                        </ul>',
        ],
        [
            'icon' => 'bi-arrow-repeat', 'color' => '#0891b2', 'bg' => '#ecfeff',
            'title' => '5. Retours et remboursements',
            'body'  => '<ul>
                            <li>Vous disposez de <strong>30 jours</strong> à compter de la réception pour retourner un produit.</li>
                            <li>Le produit doit être retourné dans son état d\'origine, non utilisé, dans son emballage d\'origine.</li>
                            <li>Pour initier un retour, contactez-nous à <strong>' . config('mail.from.address') . '</strong> avec votre numéro de commande.</li>
                            <li>Le remboursement est effectué dans les <strong>5 à 10 jours ouvrés</strong> après réception et vérification du produit.</li>
                            <li>Les frais de retour sont à la charge du client sauf en cas de produit défectueux.</li>
                        </ul>',
        ],
        [
            'icon' => 'bi-shield-lock', 'color' => '#dc2626', 'bg' => '#fef2f2',
            'title' => '6. Protection des données personnelles',
            'body'  => '<p>Conformément à la réglementation en vigueur, vous disposez des droits suivants :</p>
                        <ul>
                            <li>Les données collectées (nom, email, téléphone, adresse) sont utilisées uniquement pour le traitement de vos commandes et communications ShopCI.</li>
                            <li>Vos données ne sont jamais vendues à des tiers.</li>
                            <li>Vous pouvez demander la suppression de votre compte et de vos données en nous contactant.</li>
                            <li>Les mots de passe sont chiffrés (bcrypt) et les sessions sécurisées.</li>
                        </ul>',
        ],
        [
            'icon' => 'bi-gavel', 'color' => '#475569', 'bg' => '#f8fafc',
            'title' => '7. Droit applicable et litiges',
            'body'  => '<ul>
                            <li>Les présentes CGV sont soumises au droit ivoirien.</li>
                            <li>En cas de litige, une solution amiable sera recherchée en priorité.</li>
                            <li>À défaut d\'accord amiable, les tribunaux compétents d\'Abidjan (Côte d\'Ivoire) seront saisis.</li>
                            <li>Pour tout litige, contactez-nous d\'abord à <strong>' . config('mail.from.address') . '</strong>.</li>
                        </ul>',
        ],
    ];
    @endphp

    @foreach($sections as $s)
    <div class="card mb-4" style="border-radius:16px;border:1.5px solid var(--border);">
        <div class="card-header bg-white py-3 px-4"
             style="border-bottom:1px solid var(--border);border-radius:16px 16px 0 0;">
            <h2 class="fw-800 mb-0 d-flex align-items-center gap-2" style="font-size:1rem;">
                <div style="width:34px;height:34px;border-radius:9px;background:{{ $s['bg'] }};
                            display:flex;align-items:center;justify-content:center;
                            color:{{ $s['color'] }};font-size:.9rem;flex-shrink:0;">
                    <i class="bi {{ $s['icon'] }}"></i>
                </div>
                {{ $s['title'] }}
            </h2>
        </div>
        <div class="card-body px-4 py-3" style="font-size:.9rem;line-height:1.8;color:var(--text);">
            {!! $s['body'] !!}
        </div>
    </div>
    @endforeach

    <div class="text-center mt-5 pt-2">
        <p class="text-muted" style="font-size:.83rem;">
            Pour toute question, contactez-nous via notre
            <a href="{{ route('contact') }}" class="fw-700 text-decoration-none">page de contact</a>
            ou par email à <strong>{{ config('mail.from.address') }}</strong>.
        </p>
    </div>
</div>
@endsection
