@extends('layouts.app')
@section('title', 'Semua Laporan')
@section('content')
<div class="container">
    {{-- Page Header --}}
    <div class="report-page-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-family:'Noto Serif',serif;font-size:1.75rem;font-weight:700;color:var(--primary);display:flex;align-items:center;gap:12px;">
                <span class="material-symbols-rounded" style="font-size:28px;color:var(--accent);">description</span>
                Semua Laporan
            </h1>
            <p style="color:var(--text-muted);font-size:14px;margin-top:4px;">Pantau dan telusuri laporan dari warga</p>
        </div>
        @auth
            <a href="{{ route('reports.create') }}" class="btn-primary" style="gap:8px;">
                <span class="material-symbols-rounded" style="font-size:18px;">add_circle</span> Buat Laporan
            </a>
        @endauth
    </div>

    {{-- Filters --}}
    <div class="glass-card" style="padding:24px;margin-bottom:28px;">
        <form method="GET" style="display:flex;gap:14px;flex-wrap:wrap;align-items:end;">
            <div style="flex:1;min-width:200px;">
                <label class="form-label" style="display:flex;align-items:center;gap:6px;">
                    <span class="material-symbols-rounded" style="font-size:16px;">search</span> Cari
                </label>
                <input type="text" name="search" class="form-input" placeholder="Cari judul laporan..." value="{{ request('search') }}">
            </div>
            <div style="min-width:160px;">
                <label class="form-label" style="display:flex;align-items:center;gap:6px;">
                    <span class="material-symbols-rounded" style="font-size:16px;">filter_list</span> Status
                </label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Menunggu</option>
                    <option value="verified" {{ request('status')=='verified'?'selected':'' }}>Terverifikasi</option>
                    <option value="process" {{ request('status')=='process'?'selected':'' }}>Diproses</option>
                    <option value="done" {{ request('status')=='done'?'selected':'' }}>Selesai</option>
                    <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Ditolak</option>
                </select>
            </div>
            <div style="min-width:160px;">
                <label class="form-label" style="display:flex;align-items:center;gap:6px;">
                    <span class="material-symbols-rounded" style="font-size:16px;">category</span> Kategori
                </label>
                <select name="category" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:140px;">
                <label class="form-label" style="display:flex;align-items:center;gap:6px;">
                    <span class="material-symbols-rounded" style="font-size:16px;">swap_vert</span> Urutkan
                </label>
                <select name="sort" class="form-select">
                    <option value="latest" {{ request('sort')=='latest'?'selected':'' }}>Terbaru</option>
                    <option value="oldest" {{ request('sort')=='oldest'?'selected':'' }}>Terlama</option>
                    <option value="priority" {{ request('sort')=='priority'?'selected':'' }}>Prioritas</option>
                    <option value="votes" {{ request('sort')=='votes'?'selected':'' }}>Terbanyak Vote</option>
                </select>
            </div>
            <button type="submit" class="btn-primary btn-sm" style="gap:6px;padding:11px 20px;">
                <span class="material-symbols-rounded" style="font-size:16px;">search</span> Filter
            </button>
        </form>
    </div>

    {{-- Report Grid --}}
    @if($reports->count() > 0)
    <div class="grid-3">
        @foreach($reports as $report)
        <a href="{{ route('reports.show', $report) }}" class="glass-card report-card" style="text-decoration:none;color:inherit;display:block;border-radius:20px;overflow:hidden;">
            @if($report->media->first())
                <div style="height:180px;position:relative;overflow:hidden;background:var(--bg-surface);">
                    <img src="{{ asset('storage/'.$report->media->first()->file_path) }}" style="width:100%;height:100%;object-fit:cover;transition:transform 0.4s ease;" alt="">
                    @php
                        $statusColors = ['pending'=>'background:var(--secondary);color:white;','verified'=>'background:#3b82f6;color:white;','process'=>'background:#D4AF37;color:var(--primary);','done'=>'background:#16a34a;color:white;','rejected'=>'background:#ef4444;color:white;'];
                        $statusLabels = ['pending'=>'MENUNGGU','verified'=>'TERVERIFIKASI','process'=>'SEDANG DIPROSES','done'=>'SELESAI','rejected'=>'DITOLAK'];
                    @endphp
                    <div style="position:absolute;top:12px;left:12px;{{ $statusColors[$report->status] ?? '' }}font-size:10px;font-weight:700;letter-spacing:0.08em;padding:5px 12px;border-radius:20px;backdrop-filter:blur(4px);">
                        {{ $statusLabels[$report->status] ?? strtoupper($report->status) }}
                    </div>
                </div>
            @endif
            <div style="padding:20px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;flex-wrap:wrap;">
                    <span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">{{ $report->category->icon }}</span>
                    @if(!$report->media->first())
                        <span class="badge badge-{{ $report->status_badge }}">{{ $report->status_label }}</span>
                    @endif
                    @if($report->priority_score >= 8)
                        <span class="badge badge-danger" style="gap:4px;">
                            <span class="material-symbols-rounded" style="font-size:12px;">priority_high</span> Prioritas
                        </span>
                    @endif
                    @if($report->ai_detected)
                        <span class="badge badge-info" style="gap:4px;">
                            <span class="material-symbols-rounded" style="font-size:12px;">smart_toy</span> AI
                        </span>
                    @endif
                </div>
                <h3 style="font-family:'Noto Serif',serif;font-weight:600;margin-bottom:8px;font-size:15px;color:var(--primary);line-height:1.4;">{{ $report->title }}</h3>
                <p style="color:var(--text-secondary);font-size:13px;margin-bottom:12px;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ Str::limit($report->description, 100) }}</p>
                @if($report->address)
                    <p style="font-size:12px;color:var(--text-muted);margin-bottom:12px;display:flex;align-items:center;gap:4px;">
                        <span class="material-symbols-rounded" style="font-size:14px;color:var(--accent);">location_on</span>
                        {{ Str::limit($report->address, 40) }}
                    </p>
                @endif
                <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--text-muted);border-top:1px solid var(--glass-border);padding-top:12px;margin-top:4px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <span style="display:flex;align-items:center;gap:4px;">
                            <span class="material-symbols-rounded" style="font-size:14px;">thumb_up</span> {{ $report->votes_count }}
                        </span>
                        <span style="display:flex;align-items:center;gap:4px;">
                            <span class="material-symbols-rounded" style="font-size:14px;">chat_bubble</span> {{ $report->comments_count }}
                        </span>
                    </div>
                    <span style="font-weight:600;letter-spacing:0.02em;">{{ $report->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div class="pagination">
        {{ $reports->withQueryString()->links('pagination.custom') }}
    </div>
    @else
    <div class="glass-card" style="padding:80px 40px;text-align:center;">
        <span class="material-symbols-rounded" style="font-size:56px;color:var(--text-muted);opacity:0.4;display:block;margin-bottom:16px;">inbox</span>
        <p style="color:var(--text-muted);font-size:15px;">Tidak ada laporan ditemukan.</p>
    </div>
    @endif
</div>
@endsection
