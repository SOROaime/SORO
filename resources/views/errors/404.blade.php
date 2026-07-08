@extends('layouts.app')
@section('title', 'Page introuvable')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6 text-center py-5">

            {{-- Illustration --}}
            <div style="font-size:6rem;line-height:1;margin-bottom:1.5rem;
                        background:linear-gradient(135deg,var(--primary),#6366f1);
                        -webkit-background-clip:text;-webkit-text-fill-color:transparent;
                        font-weight:900;letter-spacing:-.05em;">
                404
            </div>

            <div style="width:80px;height:4px;background:linear-gradient(90deg,var(--primary),var(--accent));
                        border-radius:2px;margin:0 auto 2rem;"></div>

            <h1 class="fw-900 mb-3" style="font-size:1.6rem;letter-spacing:-.04em;">
                Page introuvable
            </h1>
            <p class="text-muted mb-5" style="font-size:.95rem;line-height:1.8;max-width:420px;margin:0 auto 2rem;">
                Oups ! La page que vous cherchez n'existe pas ou a été déplacée.
                Pas de panique, explorez notre catalogue ou retournez à l'accueil.
            </p>

            <div class="d-flex gap-3 justify-content-center flex-wrap mb-5">
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg fw-700" style="border-radius:13px;">
                    <i class="bi bi-house me-2"></i>Retour à l'accueil
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg fw-700" style="border-radius:13px;">
                    <i class="bi bi-grid-3x3-gap me-2"></i>Voir le catalogue
                </a>
            </div>

            {{-- Suggestions rapides --}}
            <div class="d-flex gap-2 justify-content-center flex-wrap">
                @foreach([
                    ['contact',        'bi-envelope',    'Contact'],
                    ['cgv',            'bi-file-text',   'CGV'],
                    ['orders.index',   'bi-bag',         'Mes commandes'],
                    ['wishlist.index', 'bi-heart',       'Mes favoris'],
                ] as [$route, $icon, $label])
                    @auth
                        <a href="{{ route($route) }}"
                           class="btn btn-sm btn-outline-secondary fw-600"
                           style="border-radius:50px;font-size:.8rem;">
                            <i class="bi {{ $icon }} me-1"></i>{{ $label }}
                        </a>
                    @else
                        @if(in_array($route, ['contact', 'cgv']))
                        <a href="{{ route($route) }}"
                           class="btn btn-sm btn-outline-secondary fw-600"
                           style="border-radius:50px;font-size:.8rem;">
                            <i class="bi {{ $icon }} me-1"></i>{{ $label }}
                        </a>
                        @endif
                    @endauth
                @endforeach
            </div>

        </div>
    </div>
</div>
@endsection
