@extends('layouts.app')
@section('title', 'Laporan Saya')
@section('content')
<div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-family:'Noto Serif',serif;font-size:1.75rem;font-weight:700;color:var(--primary);display:flex;align-items:center;gap:12px;">
                <span class="material-symbols-rounded" style="font-size:28px;color:var(--accent);">history</span>
                Laporan Saya
            </h1>
            <p style="color:var(--text-muted);font-size:14px;margin-top:4px;">Pantau status semua laporan yang Anda buat</p>
        </div>
        <a href="{{ route('reports.create') }}" class="btn-primary btn-sm" style="gap:6px;">
            <span class="material-symbols-rounded" style="font-size:16px;">add_circle</span> Buat Laporan
        </a>
    </div>

    {{-- Stats --}}
    @if($reports->total() > 0)
    <div class="grid-4" style="margin-bottom:28px;">
        <div class="stat-card" style="text-align:center;">
            <span class="material-symbols-rounded" style="font-size:28px;color:var(--primary);margin-bottom:8px;display:block;">description</span>
            <div class="stat-number">{{ $reports->total() }}</div>
            <div class="stat-label">Total</div>
        </div>
        <div class="stat-card" style="text-align:center;">
            <span class="material-symbols-rounded" style="font-size:28px;color:#f59e0b;margin-bottom:8px;display:block;">schedule</span>
            <div style="font-size:2rem;font-weight:800;color:#f59e0b;">{{ $reports->where('status','pending')->count() + $reports->where('status','verified')->count() }}</div>
            <div class="stat-label">Menunggu</div>
        </div>
        <div class="stat-card" style="text-align:center;">
            <span class="material-symbols-rounded" style="font-size:28px;color:#6366f1;margin-bottom:8px;display:block;">sync</span>
            <div style="font-size:2rem;font-weight:800;color:#6366f1;">{{ $reports->where('status','process')->count() }}</div>
            <div class="stat-label">Diproses</div>
        </div>
        <div class="stat-card" style="text-align:center;">
            <span class="material-symbols-rounded" style="font-size:28px;color:#22c55e;margin-bottom:8px;display:block;">check_circle</span>
            <div style="font-size:2rem;font-weight:800;color:#22c55e;">{{ $reports->where('status','done')->count() }}</div>
            <div class="stat-label">Selesai</div>
        </div>
    </div>
    @endif

    @if($reports->count() > 0)
    <div class="grid-3">
        @foreach($reports as $report)
        <a href="{{ route('reports.show', $report) }}" class="glass-card" style="padding:24px;text-decoration:none;color:inherit;display:block;border-radius:20px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;flex-wrap:wrap;">
                <span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">{{ $report->category->icon }}</span>
                <span class="badge badge-{{ $report->status_badge }}">{{ $report->status_label }}</span>
                @if($report->ai_detected)
                    <span class="badge badge-info" style="font-size:10px;gap:3px;">
                        <span class="material-symbols-rounded" style="font-size:12px;">smart_toy</span> AI
                    </span>
                @endif
            </div>
            <h3 style="font-family:'Noto Serif',serif;font-weight:600;margin-bottom:8px;font-size:15px;color:var(--primary);">{{ $report->title }}</h3>
            <p style="color:var(--text-secondary);font-size:13px;margin-bottom:12px;line-height:1.5;">{{ Str::limit($report->description, 80) }}</p>

            {{-- Task Progress --}}
            @if($report->task)
            <div style="margin-bottom:12px;">
                <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--text-muted);margin-bottom:4px;">
                    <span style="display:flex;align-items:center;gap:4px;">
                        <span class="material-symbols-rounded" style="font-size:14px;">engineering</span> {{ $report->task->officer->name ?? 'Petugas' }}
                    </span>
                    <span style="font-weight:700;color:{{ $report->task->progress >= 100 ? '#22c55e' : 'var(--primary)' }};">{{ $report->task->progress }}%</span>
                </div>
                <div style="width:100%;background:var(--glass-border);border-radius:10px;height:6px;overflow:hidden;">
                    <div style="width:{{ $report->task->progress }}%;background:{{ $report->task->progress >= 100 ? '#22c55e' : 'var(--primary)' }};height:100%;border-radius:10px;transition:width 0.5s;"></div>
                </div>
            </div>
            @endif

            <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--text-muted);border-top:1px solid var(--glass-border);padding-top:12px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="display:flex;align-items:center;gap:3px;"><span class="material-symbols-rounded" style="font-size:14px;">thumb_up</span> {{ $report->votes_count }}</span>
                    <span style="display:flex;align-items:center;gap:3px;"><span class="material-symbols-rounded" style="font-size:14px;">chat_bubble</span> {{ $report->comments_count }}</span>
                </div>
                <span style="font-weight:600;">{{ $report->created_at->diffForHumans() }}</span>
            </div>
        </a>
        @endforeach
    </div>
    <div class="pagination">{{ $reports->links('pagination.custom') }}</div>
    @else
    <div class="glass-card" style="padding:80px 40px;text-align:center;border-radius:20px;">
        <span class="material-symbols-rounded" style="font-size:56px;color:var(--text-muted);opacity:0.3;display:block;margin-bottom:16px;">inbox</span>
        <p style="color:var(--text-muted);font-size:15px;margin-bottom:20px;">Anda belum membuat laporan.</p>
        <a href="{{ route('reports.create') }}" class="btn-primary" style="gap:8px;">
            <span class="material-symbols-rounded" style="font-size:18px;">add_circle</span> Buat Laporan Pertama
        </a>
    </div>
    @endif
</div>
@endsection
