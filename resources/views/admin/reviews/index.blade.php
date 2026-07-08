@extends('layouts.admin')
@section('title', 'Avis clients')

@section('breadcrumb')
    <li class="breadcrumb-item active">Avis clients</li>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 class="fw-900 mb-1" style="font-size:1.8rem;letter-spacing:-.04em;">
            <i class="bi bi-star-fill me-2" style="color:#f59e0b;"></i>Avis clients
        </h1>
        <p class="text-muted mb-0" style="font-size:.88rem;">Modérez les avis laissés sur vos produits</p>
    </div>
</div>

{{-- KPI --}}
<div class="row g-3 mb-4">
    @php $kpis = [
        ['label'=>'Total avis',       'value'=>$stats['total'],              'icon'=>'bi-chat-square-text','color'=>'#2563eb','bg'=>'#dbeafe'],
        ['label'=>'Note moyenne',     'value'=>$stats['avg'].' / 5',         'icon'=>'bi-star-fill',       'color'=>'#f59e0b','bg'=>'#fef9c3'],
        ['label'=>'Avis 5 étoiles',   'value'=>$stats['five'],               'icon'=>'bi-star-fill',       'color'=>'#16a34a','bg'=>'#dcfce7'],
        ['label'=>'Avis négatifs',    'value'=>$stats['one_two'],            'icon'=>'bi-exclamation-triangle-fill','color'=>'#dc2626','bg'=>'#fee2e2'],
    ]; @endphp
    @foreach($kpis as $k)
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0" style="border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:14px;background:{{ $k['bg'] }};
                            color:{{ $k['color'] }};font-size:1.3rem;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi {{ $k['icon'] }}"></i>
                </div>
                <div>
                    <div class="text-muted fw-600" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;">{{ $k['label'] }}</div>
                    <div class="fw-900" style="font-size:1.6rem;letter-spacing:-.04em;color:#0f172a;line-height:1.1;">{{ $k['value'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filtres --}}
<div class="card border-0 mb-4" style="border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);">
    <div class="card-body p-4">
        <form method="GET" class="d-flex gap-3 flex-wrap align-items-end">
            <div>
                <label class="form-label fw-600" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Recherche produit</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control form-control-sm" placeholder="Nom du produit..." style="min-width:220px;">
            </div>
            <div>
                <label class="form-label fw-600" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Note</label>
                <select name="rating" class="form-select form-select-sm" style="min-width:140px;">
                    <option value="">Toutes les notes</option>
                    @foreach([5,4,3,2,1] as $r)
                    <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>
                        {{ $r }} étoile{{ $r > 1 ? 's' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm fw-700">
                <i class="bi bi-funnel me-1"></i>Filtrer
            </button>
            @if(request()->anyFilled(['search','rating']))
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary btn-sm fw-600">
                <i class="bi bi-x me-1"></i>Réinitialiser
            </a>
            @endif
        </form>
    </div>
</div>

{{-- Tableau des avis --}}
<div class="card border-0" style="border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);">
    <div class="card-header bg-white py-3 px-4"
         style="border-bottom:1px solid #f1f5f9;border-radius:16px 16px 0 0;">
        <h6 class="mb-0 fw-800">{{ $reviews->total() }} avis trouvés</h6>
    </div>

    @if($reviews->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-chat-square-text fs-2 d-block mb-2"></i>
            Aucun avis pour le moment.
        </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead style="background:#f8fafc;">
                <tr>
                    <th class="ps-4 py-3" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;">Client</th>
                    <th class="py-3" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;">Produit</th>
                    <th class="py-3" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;">Note</th>
                    <th class="py-3" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;">Commentaire</th>
                    <th class="py-3" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;">Date</th>
                    <th class="py-3 pe-4" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $review)
                @php
                    $starColor = $review->rating >= 4 ? '#16a34a' : ($review->rating == 3 ? '#f59e0b' : '#dc2626');
                    $starBg    = $review->rating >= 4 ? '#dcfce7' : ($review->rating == 3 ? '#fef9c3' : '#fee2e2');
                @endphp
                <tr>
                    {{-- Client --}}
                    <td class="ps-4 py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;border-radius:50%;flex-shrink:0;
                                        background:linear-gradient(135deg,var(--primary),#7c3aed);
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:.75rem;font-weight:800;color:#fff;">
                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-700" style="font-size:.85rem;">{{ $review->user->name }}</div>
                                <div class="text-muted" style="font-size:.72rem;">{{ $review->user->email }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Produit --}}
                    <td class="py-3">
                        <a href="{{ route('products.show', $review->product) }}"
                           class="fw-600 text-decoration-none" style="font-size:.85rem;color:var(--primary);"
                           target="_blank">
                            {{ Str::limit($review->product->name, 30) }}
                            <i class="bi bi-box-arrow-up-right ms-1" style="font-size:.65rem;"></i>
                        </a>
                    </td>

                    {{-- Note --}}
                    <td class="py-3">
                        <span style="display:inline-flex;align-items:center;gap:5px;
                                     background:{{ $starBg }};color:{{ $starColor }};
                                     padding:.25em .75em;border-radius:20px;font-size:.78rem;font-weight:800;">
                            <i class="bi bi-star-fill"></i> {{ $review->rating }}/5
                        </span>
                        <div style="font-size:.7rem;color:#f59e0b;margin-top:3px;">
                            @for($i=1;$i<=5;$i++)
                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                            @endfor
                        </div>
                    </td>

                    {{-- Commentaire --}}
                    <td class="py-3" style="max-width:280px;">
                        @if($review->comment)
                            <span style="font-size:.82rem;color:var(--text);line-height:1.5;">
                                {{ Str::limit($review->comment, 100) }}
                            </span>
                        @else
                            <span class="text-muted" style="font-size:.78rem;font-style:italic;">Pas de commentaire</span>
                        @endif
                    </td>

                    {{-- Date --}}
                    <td class="py-3 text-muted" style="font-size:.78rem;white-space:nowrap;">
                        {{ $review->created_at->format('d/m/Y') }}<br>
                        <span style="font-size:.72rem;">{{ $review->created_at->diffForHumans() }}</span>
                    </td>

                    {{-- Action --}}
                    <td class="py-3 pe-4">
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm fw-600"
                                    style="background:#fee2e2;color:#dc2626;border:1px solid #fecaca;font-size:.75rem;"
                                    onclick="return confirm('Supprimer cet avis définitivement ?')">
                                <i class="bi bi-trash me-1"></i>Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($reviews->hasPages())
    <div class="px-4 py-3" style="border-top:1px solid #f1f5f9;">
        {{ $reviews->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
