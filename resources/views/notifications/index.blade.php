@extends('layouts.app')
@section('title', 'Notifikasi')
@section('content')
<div class="container" style="max-width:700px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <h1 style="font-family:'Noto Serif',serif;font-size:1.75rem;font-weight:700;color:var(--primary);display:flex;align-items:center;gap:12px;"><span class="material-symbols-rounded" style="font-size:28px;color:var(--accent);">notifications</span> Notifikasi</h1>
        @if(auth()->user()->unreadNotifications->count() > 0)
        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button type="submit" class="btn-secondary btn-sm">Tandai Semua Dibaca</button>
        </form>
        @endif
    </div>

    @forelse($notifications as $notification)
    <div class="glass-card" style="padding:20px;margin-bottom:8px;{{ $notification->read_at ? 'opacity:0.6;' : '' }}">
        <div style="display:flex;justify-content:space-between;align-items:start;">
            <div>
                <p style="font-weight:600;font-size:14px;">{{ $notification->data['title'] ?? 'Notifikasi' }}</p>
                <p style="color:#94a3b8;font-size:13px;margin-top:4px;">{{ $notification->data['message'] ?? '' }}</p>
                <p style="color:#64748b;font-size:12px;margin-top:8px;">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            @if(!$notification->read_at)
            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                @csrf
                <button type="submit" class="btn-secondary btn-sm">Dibaca</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="glass-card" style="padding:80px 40px;text-align:center;">
        <span class="material-symbols-rounded" style="font-size:56px;color:var(--text-muted);opacity:0.3;display:block;margin-bottom:16px;">notifications_off</span>
        <p style="color:var(--text-muted);">Tidak ada notifikasi.</p>
    </div>
    @endforelse

    <div class="pagination">{{ $notifications->links('pagination.custom') }}</div>
</div>
@endsection
