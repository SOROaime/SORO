@extends('layouts.app')

@section('title', 'Mon Panier')

@section('content')
<div class="container py-5">
    <h1 class="section-title mb-4">
        <i class="bi bi-cart3 me-2"></i>Mon Panier
    </h1>

    @if($cart->items->isEmpty())
        {{-- Panier vide --}}
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-3 text-muted"></i>
            <h3 class="mt-3 text-muted">Votre panier est vide</h3>
            <p class="text-muted">Ajoutez des produits pour commencer vos achats.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary mt-2">
                <i class="bi bi-grid me-2"></i>Parcourir les produits
            </a>
        </div>
    @else
        <div class="row g-4">
            {{-- Articles --}}
            <div class="col-lg-8">
                <div class="card border-0 rounded-4 shadow-sm">
                    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            {{ $cart->total_items }} article(s)
                        </h5>
                        {{-- Vider le panier --}}
                        <form action="{{ route('cart.clear') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Vider tout le panier ?')">
                                <i class="bi bi-trash me-1"></i>Vider
                            </button>
                        </form>
                    </div>

                    <div class="card-body p-0">
                        @foreach($cart->items as $item)
                            <div class="d-flex align-items-center gap-3 p-4 border-top" id="item-{{ $item->id }}">
                                {{-- Image --}}
                                <img
                                    src="{{ $item->product->image_url }}"
                                    alt="{{ $item->product->name }}"
                                    class="rounded-3"
                                    style="width: 80px; height: 80px; object-fit: cover;"
                                    onerror="this.src='https://placehold.co/80x80/e2e8f0/94a3b8?text=?'"
                                >

                                {{-- Infos produit --}}
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1">
                                        <a href="{{ route('products.show', $item->product) }}"
                                           class="text-dark text-decoration-none">
                                            {{ $item->product->name }}
                                        </a>
                                    </h6>
                                    <div class="text-muted small">
                                        Prix unitaire : {{ $item->product->formatted_price }}
                                    </div>
                                    @if($item->product->stock < $item->quantity)
                                        <span class="badge bg-danger-subtle text-danger small">
                                            <i class="bi bi-exclamation-triangle me-1"></i>Stock insuffisant
                                        </span>
                                    @endif
                                </div>

                                {{-- Quantité --}}
                                <form action="{{ route('cart.update', $item) }}" method="POST"
                                      class="d-flex align-items-center gap-1">
                                    @csrf
                                    @method('PATCH')
                                    <div class="input-group" style="width: 120px;">
                                        <button type="button" class="btn btn-outline-secondary btn-sm qty-minus"
                                                data-target="qty-{{ $item->id }}">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <input type="number"
                                               name="quantity"
                                               id="qty-{{ $item->id }}"
                                               class="form-control form-control-sm text-center"
                                               value="{{ $item->quantity }}"
                                               min="1"
                                               max="{{ $item->product->stock }}">
                                        <button type="button" class="btn btn-outline-secondary btn-sm qty-plus"
                                                data-target="qty-{{ $item->id }}"
                                                data-max="{{ $item->product->stock }}">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                    <button type="submit" class="btn btn-outline-primary btn-sm ms-1">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>

                                {{-- Sous-total --}}
                                <div class="text-end ms-2" style="min-width: 90px;">
                                    <div class="fw-bold text-primary">{{ $item->formatted_subtotal }}</div>
                                </div>

                                {{-- Supprimer --}}
                                <form action="{{ route('cart.remove', $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Supprimer cet article ?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Résumé --}}
            <div class="col-lg-4">
                <div class="card border-0 rounded-4 shadow-sm sticky-top" style="top: 80px;">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold">Résumé de la commande</h5>
                    </div>
                    <div class="card-body px-4">
                        {{-- Lignes de résumé --}}
                        @foreach($cart->items as $item)
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">{{ Str::limit($item->product->name, 25) }} × {{ $item->quantity }}</span>
                                <span>{{ $item->formatted_subtotal }}</span>
                            </div>
                        @endforeach

                        <hr>

                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Sous-total</span>
                            <span>{{ $cart->formatted_total }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Livraison</span>
                            <span class="text-success">Gratuite</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                            <span>Total</span>
                            <span class="text-primary">{{ $cart->formatted_total }}</span>
                        </div>

                        <a href="{{ route('orders.checkout') }}" class="btn btn-primary btn-lg w-100 fw-bold">
                            <i class="bi bi-credit-card me-2"></i>Passer la commande
                        </a>

                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="bi bi-arrow-left me-2"></i>Continuer mes achats
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Boutons +/-
    document.querySelectorAll('.qty-minus').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
        });
    });
    document.querySelectorAll('.qty-plus').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            const max   = parseInt(btn.dataset.max);
            if (parseInt(input.value) < max) input.value = parseInt(input.value) + 1;
        });
    });
</script>
@endpush
@endsection
