@extends('layouts.admin')
@section('title', 'Conversations chatbot')

@section('breadcrumb')
    <li class="breadcrumb-item active">Conversations</li>
@endsection

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">
            <i class="bi bi-chat-dots me-2" style="color:#b45309;"></i>Conversations chatbot
        </h1>
        <p class="text-muted mb-0" style="font-size:.875rem;">Toutes les conversations avec Sara</p>
    </div>
</div>

<div class="card">
    <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-700" style="font-size:.95rem;">
            <i class="bi bi-list-ul me-2" style="color:#b45309;"></i>
            Conversations
            <span style="background:#f1f5f9;color:#64748b;padding:.15em .55em;border-radius:20px;font-size:.72rem;font-weight:700;">
                {{ $conversations->total() }}
            </span>
        </h5>
    </div>

    @if($conversations->isEmpty())
    <div class="card-body text-center py-5">
        <div style="font-size:2.5rem;margin-bottom:1rem;">💬</div>
        <p class="text-muted fw-600 mb-0">Aucune conversation pour le moment.</p>
    </div>
    @else
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Messages</th>
                    <th>Dernière activité</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($conversations as $conv)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#dbeafe,#bfdbfe);
                                        display:flex;align-items:center;justify-content:center;font-size:.9rem;font-weight:800;color:#1d4ed8;flex-shrink:0;">
                                {{ strtoupper(substr($conv->display_name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-700" style="font-size:.875rem;">{{ $conv->display_name }}</div>
                                @if($conv->user)
                                <div class="text-muted" style="font-size:.75rem;">{{ $conv->user->email }}</div>
                                @else
                                <div style="font-size:.75rem;color:#94a3b8;">Visiteur non connecté</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="background:#f1f5f9;color:#475569;padding:.25em .7em;border-radius:20px;font-size:.8rem;font-weight:700;">
                            {{ $conv->messages_count }} msg
                        </span>
                    </td>
                    <td style="font-size:.85rem;color:#64748b;">
                        {{ $conv->updated_at->diffForHumans() }}
                    </td>
                    <td>
                        <a href="{{ route('admin.conversations.show', $conv) }}"
                           class="btn btn-sm fw-600"
                           style="background:#dbeafe;color:#1d4ed8;border-radius:8px;font-size:.78rem;">
                            <i class="bi bi-eye me-1"></i>Voir
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($conversations->hasPages())
    <div class="card-body border-top d-flex justify-content-center py-3">
        {{ $conversations->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
