@extends('layouts.admin')
@section('title', 'Conversation — ' . $conversation->display_name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.conversations.index') }}">Conversations</a></li>
    <li class="breadcrumb-item active">{{ $conversation->display_name }}</li>
@endsection

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">
            <i class="bi bi-chat-dots me-2" style="color:#b45309;"></i>{{ $conversation->display_name }}
        </h1>
        <p class="text-muted mb-0" style="font-size:.875rem;">
            {{ $conversation->messages->count() }} messages · Démarrée le {{ $conversation->created_at->format('d/m/Y à H:i') }}
        </p>
    </div>
    <a href="{{ route('admin.conversations.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<div class="card" style="max-width:800px;">
    <div class="card-body p-4">
        <div class="d-flex flex-column gap-3">
            @foreach($conversation->messages as $msg)
            @if($msg->role === 'user')
            <div class="d-flex justify-content-end">
                <div style="max-width:70%;background:#dbeafe;border-radius:16px 16px 4px 16px;
                            padding:.75em 1em;font-size:.88rem;color:#1e3a5f;">
                    {{ $msg->content }}
                    <div style="font-size:.7rem;color:#64748b;text-align:right;margin-top:.3em;">
                        {{ $msg->created_at->format('H:i') }}
                    </div>
                </div>
            </div>
            @else
            <div class="d-flex gap-2 align-items-start">
                <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#d97706);
                            display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;">
                    🤖
                </div>
                <div style="max-width:70%;background:#f8fafc;border:1.5px solid #e2e8f0;
                            border-radius:4px 16px 16px 16px;padding:.75em 1em;font-size:.88rem;color:#334155;">
                    {{ $msg->content }}
                    <div style="font-size:.7rem;color:#94a3b8;margin-top:.3em;">
                        {{ $msg->created_at->format('H:i') }}
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>
@endsection
