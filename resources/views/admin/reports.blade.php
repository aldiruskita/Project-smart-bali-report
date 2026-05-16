@extends('layouts.admin')
@section('title', 'Kelola Laporan')
@section('admin-content')

{{-- Header with stats summary --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <h1 style="font-family:'Noto Serif',serif;font-size:1.5rem;font-weight:700;color:var(--primary);">Kelola Laporan</h1>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <div style="background:rgba(245,158,11,0.1);color:#b45309;padding:6px 16px;border-radius:10px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:6px;">
            <span class="material-symbols-rounded" style="font-size:16px;">schedule</span>
            {{ $reports->where('status','pending')->count() }} Menunggu
        </div>
        <div style="background:rgba(59,130,246,0.1);color:#1d4ed8;padding:6px 16px;border-radius:10px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:6px;">
            <span class="material-symbols-rounded" style="font-size:16px;">sync</span>
            {{ $reports->where('status','process')->count() }} Diproses
        </div>
        <div style="background:rgba(22,163,74,0.1);color:#15803d;padding:6px 16px;border-radius:10px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:6px;">
            <span class="material-symbols-rounded" style="font-size:16px;">check_circle</span>
            {{ $reports->where('status','done')->count() }} Selesai
        </div>
    </div>
</div>

{{-- Filters --}}
<div style="background:white;border:1px solid var(--glass-border);border-radius:16px;padding:16px;margin-bottom:24px;">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;">
        <div style="flex:1;min-width:160px;">
            <label style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;display:block;margin-bottom:4px;">Cari Laporan</label>
            <input type="text" name="search" class="form-input" placeholder="Judul, deskripsi, pelapor..." value="{{ request('search') }}">
        </div>
        <div>
            <label style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;display:block;margin-bottom:4px;">Status</label>
            <select name="status" class="form-select" style="width:auto;">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Menunggu</option>
                <option value="verified" {{ request('status')=='verified'?'selected':'' }}>Terverifikasi</option>
                <option value="process" {{ request('status')=='process'?'selected':'' }}>Diproses</option>
                <option value="done" {{ request('status')=='done'?'selected':'' }}>Selesai</option>
                <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Ditolak</option>
            </select>
        </div>
        <div>
            <label style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;display:block;margin-bottom:4px;">Kategori</label>
            <select name="category" class="form-select" style="width:auto;">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category')==$cat->id?'selected':'' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;display:block;margin-bottom:4px;">Urutkan</label>
            <select name="sort" class="form-select" style="width:auto;">
                <option value="latest" {{ request('sort','latest')=='latest'?'selected':'' }}>Terbaru</option>
                <option value="oldest" {{ request('sort')=='oldest'?'selected':'' }}>Terlama</option>
                <option value="priority" {{ request('sort')=='priority'?'selected':'' }}>Prioritas Tertinggi</option>
            </select>
        </div>
        <button type="submit" class="btn-primary btn-sm" style="display:flex;align-items:center;gap:6px;">
            <span class="material-symbols-rounded" style="font-size:16px;">search</span>
            Filter
        </button>
        @if(request()->hasAny(['search','status','category','sort']))
            <a href="{{ route('admin.reports') }}" style="color:var(--text-muted);font-size:12px;text-decoration:none;padding:8px;display:flex;align-items:center;gap:4px;">
                <span class="material-symbols-rounded" style="font-size:16px;">close</span>
                Reset
            </a>
        @endif
    </form>
</div>

{{-- Reports Cards --}}
@forelse($reports as $report)
<div style="background:white;border:1px solid var(--glass-border);border-radius:16px;padding:20px;margin-bottom:12px;transition:box-shadow 0.2s;" class="report-card-item">

    {{-- Row 1: Info + Quick Actions --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;">
        {{-- Left: Report Info --}}
        <div style="flex:1;min-width:280px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap;">
                <span style="color:var(--text-muted);font-size:12px;font-weight:600;">#{{ $report->id }}</span>
                <span class="badge badge-{{ $report->status_badge }}">{{ $report->status_label }}</span>
                <span style="background:rgba(0,0,0,0.04);padding:2px 8px;border-radius:6px;font-size:11px;color:var(--text-muted);display:inline-flex;align-items:center;gap:4px;"><span class="material-symbols-rounded" style="font-size:14px;">{{ $report->category->icon }}</span> {{ $report->category->name }}</span>
                @if($report->priority_score >= 8)
                    <span style="background:rgba(239,68,68,0.1);color:#dc2626;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                        <span class="material-symbols-rounded" style="font-size:14px;">priority_high</span>
                        Prioritas Tinggi
                    </span>
                @endif
            </div>
            <a href="{{ route('reports.show', $report) }}" style="color:var(--primary);text-decoration:none;font-weight:700;font-size:15px;line-height:1.4;">
                {{ $report->title }}
            </a>
            <div style="font-size:12px;color:var(--text-muted);margin-top:4px;display:flex;gap:12px;flex-wrap:wrap;">
                <span style="display:flex;align-items:center;gap:4px;">
                    <span class="material-symbols-rounded" style="font-size:14px;">person</span>
                    {{ $report->reporter_name }}
                </span>
                <span style="display:flex;align-items:center;gap:4px;">
                    <span class="material-symbols-rounded" style="font-size:14px;">location_on</span>
                    {{ $report->address ? Str::limit($report->address, 25) : '-' }}
                </span>
                <span style="display:flex;align-items:center;gap:4px;">
                    <span class="material-symbols-rounded" style="font-size:14px;">schedule</span>
                    {{ $report->created_at->format('d M Y, H:i') }}
                </span>
                <span style="display:flex;align-items:center;gap:4px;">
                    <span class="material-symbols-rounded" style="font-size:14px;">thumb_up</span>
                    {{ $report->votes_count }}
                    <span class="material-symbols-rounded" style="font-size:14px;margin-left:4px;">comment</span>
                    {{ $report->comments_count }}
                </span>
            </div>
            @if($report->assignment)
                <div style="margin-top:6px;font-size:12px;color:#1d4ed8;font-weight:600;display:flex;align-items:center;gap:4px;">
                    <span class="material-symbols-rounded" style="font-size:14px;">engineering</span>
                    Ditugaskan ke: {{ $report->assignment->officer->name ?? '-' }}
                    @if($report->task)
                        <span style="color:var(--text-muted);font-weight:400;">· Progress: {{ $report->task->progress }}%</span>
                    @endif
                </div>
            @endif
        </div>

        {{-- Right: Status + Delete --}}
        <div style="display:flex;gap:6px;align-items:center;flex-shrink:0;">
            <form method="POST" action="{{ route('admin.reports.status', $report) }}" style="display:flex;gap:4px;align-items:center;">
                @csrf @method('PATCH')
                <select name="status" class="form-select" style="width:auto;padding:6px 10px;font-size:12px;border-radius:8px;">
                    <option value="pending" {{ $report->status=='pending'?'selected':'' }}>Menunggu</option>
                    <option value="verified" {{ $report->status=='verified'?'selected':'' }}>Verifikasi</option>
                    <option value="process" {{ $report->status=='process'?'selected':'' }}>Proses</option>
                    <option value="done" {{ $report->status=='done'?'selected':'' }}>Selesai</option>
                    <option value="rejected" {{ $report->status=='rejected'?'selected':'' }}>Tolak</option>
                </select>
                <button type="submit" class="btn-primary btn-sm" style="padding:6px 12px;display:flex;align-items:center;">
                    <span class="material-symbols-rounded" style="font-size:16px;">check</span>
                </button>
            </form>
            <a href="{{ route('reports.show', $report) }}" style="background:var(--primary);color:white;border:none;border-radius:8px;padding:6px 12px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                <span class="material-symbols-rounded" style="font-size:16px;">visibility</span>
            </a>
            <button type="button" class="btn-hapus-laporan" data-id="{{ $report->id }}" style="background:#ef4444;color:white;border:none;border-radius:8px;padding:6px 12px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;">
                <span class="material-symbols-rounded" style="font-size:16px;">delete</span>
            </button>
        </div>
    </div>

    {{-- Row 2: Assign (collapsible) --}}
    @if(!$report->assignment || in_array($report->status, ['pending', 'verified']))
    <div style="margin-top:12px;padding-top:12px;border-top:1px dashed rgba(0,0,0,0.08);">
        <form method="POST" action="{{ route('admin.reports.assign', $report) }}" style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
            @csrf
            <span style="font-size:12px;color:var(--text-muted);font-weight:700;display:flex;align-items:center;gap:4px;">
                <span class="material-symbols-rounded" style="font-size:14px;">assignment_ind</span>
                Tugaskan:
            </span>
            <select name="officer_id" class="form-select" style="width:auto;padding:6px 10px;font-size:12px;border-radius:8px;" required>
                <option value="">Pilih Petugas</option>
                @foreach($officers as $officer)
                    <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                @endforeach
            </select>
            <select name="priority" class="form-select" style="width:auto;padding:6px 10px;font-size:12px;border-radius:8px;">
                <option value="medium">Sedang</option>
                <option value="low">Rendah</option>
                <option value="high">Tinggi</option>
                <option value="urgent">Darurat</option>
            </select>
            <input type="date" name="deadline" class="form-input" style="width:auto;padding:6px 10px;font-size:12px;border-radius:8px;">
            <button type="submit" class="btn-secondary btn-sm" style="display:flex;align-items:center;gap:4px;">
                <span class="material-symbols-rounded" style="font-size:14px;">send</span>
                Tugaskan
            </button>
        </form>
    </div>
    @endif
</div>
@empty
<div style="background:white;border:1px solid var(--glass-border);border-radius:16px;padding:48px;text-align:center;">
    <span class="material-symbols-rounded" style="font-size:48px;color:var(--text-muted);margin-bottom:12px;">inbox</span>
    <div style="font-family:'Noto Serif',serif;font-size:1.1rem;color:var(--text-muted);">Tidak ada laporan ditemukan.</div>
    <p style="color:var(--text-muted);font-size:13px;margin-top:8px;">Coba ubah filter pencarian Anda.</p>
</div>
@endforelse

{{-- Hidden delete forms --}}
@foreach($reports as $report)
<form id="form-delete-{{ $report->id }}" method="POST" action="{{ route('admin.reports.destroy', $report) }}" style="display:none;">
    @csrf
    <input type="hidden" name="_method" value="DELETE">
</form>
@endforeach

<div class="pagination" style="margin-top:20px;">{{ $reports->withQueryString()->links('pagination.custom') }}</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete buttons
    document.querySelectorAll('.btn-hapus-laporan').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            if (confirm('Yakin hapus laporan #' + id + '? Data tidak bisa dikembalikan.')) {
                document.getElementById('form-delete-' + id).submit();
            }
        });
    });
});
</script>
@endpush
