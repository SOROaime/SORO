@extends('layouts.app')
@section('title', 'Nouveau coupon')
@section('content')
<div class="container py-5" style="max-width:600px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="section-title mb-0"><i class="bi bi-plus-circle me-2" style="color:var(--accent)"></i>Nouveau coupon</h1>
    </div>

    <div class="card" style="border-radius:16px;border:1.5px solid var(--border);">
        <div class="card-body p-4">
            @include('admin.coupons._form', ['coupon' => null, 'action' => route('admin.coupons.store'), 'method' => 'POST'])
        </div>
    </div>
</div>
@endsection
