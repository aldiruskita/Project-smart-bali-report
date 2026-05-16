@extends('layouts.app')
@section('title', $report->title)
@section('content')
<div class="container" style="max-width:960px;">
    {{-- Back Navigation --}}
    <a href="{{ route('reports.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:14px;display:inline-flex;align-items:center;gap:6px;margin-bottom:24px;font-weight:500;transition:color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">
        <span class="material-symbols-rounded" style="font-size:18px;">arrow_back</span> Kembali ke Laporan
    </a>

    {{-- Header Card --}}
    <div class="glass-card report-detail-header" style="padding:32px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
            <div style="width:44px;height:44px;border-radius:14px;background:rgba(212,175,55,0.1);display:flex;align-items:center;justify-content:center;">
                <span class="material-symbols-rounded" style="font-size:24px;color:var(--accent);">{{ $report->category->icon }}</span>
            </div>
            <span class="badge badge-{{ $report->status_badge }}" style="font-size:13px;padding:6px 16px;">{{ $report->status_label }}</span>
            @if($report->priority_score >= 8)
                <span class="badge badge-danger" style="display:flex;align-items:center;gap:4px;">
                    <span class="material-symbols-rounded" style="font-size:14px;">priority_high</span> Prioritas Tinggi
                </span>
            @endif
        </div>
        <h1 style="font-family:'Noto Serif',serif;font-size:1.5rem;font-weight:700;margin-bottom:16px;color:var(--primary);line-height:1.3;">{{ $report->title }}</h1>
        <div style="display:flex;gap:20px;font-size:13px;color:var(--text-muted);flex-wrap:wrap;">
            <span style="display:flex;align-items:center;gap:6px;">
                <span class="material-symbols-rounded" style="font-size:16px;">person</span> {{ $report->reporter_name }}
            </span>
            <span style="display:flex;align-items:center;gap:6px;">
                <span class="material-symbols-rounded" style="font-size:16px;">folder</span> {{ $report->category->name }}
            </span>
            <span style="display:flex;align-items:center;gap:6px;">
                <span class="material-symbols-rounded" style="font-size:16px;">calendar_today</span> {{ $report->created_at->format('d M Y, H:i') }}
            </span>
            <span style="display:flex;align-items:center;gap:6px;">
                <span class="material-symbols-rounded" style="font-size:16px;">star</span> Prioritas: {{ $report->priority_score }}
            </span>
        </div>
        @if($report->ai_detected)
        <div style="margin-top:16px;padding:14px 18px;background:rgba(99,102,241,0.06);border:1px solid rgba(99,102,241,0.15);border-radius:14px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <span class="material-symbols-rounded" style="font-size:22px;color:#818cf8;">smart_toy</span>
            <span style="font-size:13px;font-weight:600;color:var(--primary);">AI Detected</span>
            <span class="badge badge-info">{{ $report->ai_category }}</span>
            <span style="font-size:12px;color:var(--text-muted);">Confidence: {{ number_format($report->ai_confidence, 1) }}%</span>
        </div>
        @endif
    </div>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;">
        <div>
            {{-- Description --}}
            <div class="glass-card" style="padding:28px;margin-bottom:24px;">
                <h2 style="font-weight:700;margin-bottom:14px;font-size:1.05rem;color:var(--primary);display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">edit_note</span> Deskripsi
                </h2>
                <p style="line-height:1.8;color:var(--text-secondary);font-size:14px;">{{ $report->description }}</p>
            </div>

            {{-- Media --}}
            @if($report->media->count() > 0)
            <div class="glass-card" style="padding:28px;margin-bottom:24px;">
                <h2 style="font-weight:700;margin-bottom:14px;font-size:1.05rem;color:var(--primary);display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">photo_library</span> Media
                </h2>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
                    @foreach($report->media as $media)
                        @if($media->type === 'image')
                            <img src="{{ asset('storage/'.$media->file_path) }}" style="width:100%;border-radius:14px;cursor:zoom-in;transition:transform 0.3s;border:1px solid var(--glass-border);" onclick="openLightbox(this.src)" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'" loading="lazy" alt="Report media">
                        @else
                            <video controls style="width:100%;border-radius:14px;border:1px solid var(--glass-border);">
                                <source src="{{ asset('storage/'.$media->file_path) }}">
                            </video>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Map --}}
            @if($report->latitude && $report->longitude)
            <div class="glass-card" style="padding:28px;margin-bottom:24px;">
                <h2 style="font-weight:700;margin-bottom:14px;font-size:1.05rem;color:var(--primary);display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">location_on</span> Lokasi
                </h2>
                @if($report->address)
                    <p style="font-size:14px;color:var(--text-muted);margin-bottom:14px;display:flex;align-items:center;gap:6px;">
                        <span class="material-symbols-rounded" style="font-size:16px;">near_me</span> {{ $report->address }}
                    </p>
                @endif
                <div class="map-container">
                    <div id="map" style="height:300px;"></div>
                </div>
            </div>
            @endif

            {{-- Comments --}}
            <div class="glass-card" style="padding:28px;margin-bottom:24px;">
                <h2 style="font-weight:700;margin-bottom:20px;font-size:1.05rem;color:var(--primary);display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">forum</span> Komentar ({{ $report->comments->count() }})
                </h2>

                @auth
                <form method="POST" action="{{ route('comments.store', $report) }}" style="margin-bottom:24px;">
                    @csrf
                    <textarea name="comment" class="form-textarea" style="min-height:80px;" placeholder="Tulis komentar Anda..." required></textarea>
                    <button type="submit" class="btn-primary btn-sm" style="margin-top:10px;gap:6px;">
                        <span class="material-symbols-rounded" style="font-size:16px;">send</span> Kirim Komentar
                    </button>
                </form>
                @endauth

                @forelse($report->comments as $comment)
                <div style="padding:16px 0;border-bottom:1px solid var(--glass-border);">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <span style="font-weight:600;font-size:14px;color:var(--primary);display:flex;align-items:center;gap:6px;">
                            <span class="material-symbols-rounded" style="font-size:16px;">account_circle</span> {{ $comment->user->name }}
                        </span>
                        <span style="font-size:12px;color:var(--text-muted);">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p style="font-size:14px;color:var(--text-secondary);line-height:1.6;">{{ $comment->comment }}</p>
                    @auth
                        @if($comment->user_id === auth()->id() || auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('comments.destroy', $comment) }}" style="margin-top:8px;">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:var(--danger);font-size:12px;cursor:pointer;display:flex;align-items:center;gap:4px;font-family:inherit;font-weight:600;">
                                <span class="material-symbols-rounded" style="font-size:14px;">delete</span> Hapus
                            </button>
                        </form>
                        @endif
                    @endauth
                </div>
                @empty
                <div style="text-align:center;padding:24px 0;">
                    <span class="material-symbols-rounded" style="font-size:36px;color:var(--text-muted);opacity:0.3;display:block;margin-bottom:8px;">chat</span>
                    <p style="color:var(--text-muted);font-size:14px;">Belum ada komentar.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Sidebar --}}
        <div>
            {{-- Vote --}}
            <div class="glass-card" style="padding:24px;margin-bottom:16px;text-align:center;">
                @auth
                <button class="vote-btn {{ $report->hasVotedBy(auth()->id()) ? 'voted' : '' }}" id="voteBtn" onclick="toggleVote()" style="width:100%;justify-content:center;gap:10px;">
                    <span class="material-symbols-rounded" style="font-size:20px;">thumb_up</span>
                    <span id="voteCount">{{ $report->votes->count() }}</span> Vote
                </button>
                @else
                <div class="vote-btn" style="width:100%;justify-content:center;cursor:default;gap:10px;">
                    <span class="material-symbols-rounded" style="font-size:20px;">thumb_up</span>
                    {{ $report->votes->count() }} Vote
                </div>
                <p style="font-size:12px;color:var(--text-muted);margin-top:10px;">
                    <a href="{{ route('login') }}" style="color:var(--primary);font-weight:600;text-decoration:none;">Login</a> untuk vote
                </p>
                @endauth
            </div>

            {{-- Timeline --}}
            <div class="glass-card" style="padding:24px;margin-bottom:16px;">
                <h3 style="font-weight:700;margin-bottom:16px;font-size:1rem;color:var(--primary);display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">timeline</span> Timeline
                </h3>
                <div class="timeline">
                    @foreach($report->logs as $log)
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:{{ match($log->status) { 'pending'=>'#f59e0b','verified'=>'#3b82f6','process'=>'#6366f1','done'=>'#22c55e','rejected'=>'#ef4444',default=>'#6b7280' } }};"></div>
                        <div style="font-weight:600;font-size:14px;color:var(--primary);">{{ $log->status_label }}</div>
                        @if($log->note)<div style="font-size:13px;color:var(--text-muted);">{{ $log->note }}</div>@endif
                        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">{{ $log->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Task Progress --}}
            @if($report->task)
            <div class="glass-card" style="padding:24px;margin-bottom:16px;">
                <h3 style="font-weight:700;margin-bottom:14px;font-size:1rem;color:var(--primary);display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">engineering</span> Penanganan
                </h3>
                <div style="font-size:14px;margin-bottom:14px;">
                    <p style="font-weight:600;color:var(--primary);">{{ $report->task->officer->name }}</p>
                    <div style="display:flex;align-items:center;gap:6px;margin-top:6px;">
                        <span class="badge badge-{{ $report->task->status_badge }}">{{ $report->task->status_label }}</span>
                        <span style="font-size:12px;">{{ $report->task->priority_badge }}</span>
                    </div>
                </div>
                {{-- Progress Bar --}}
                <div style="margin-bottom:14px;">
                    <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--text-muted);margin-bottom:6px;">
                        <span style="font-weight:600;">Progress</span>
                        <span style="font-weight:700;color:{{ $report->task->progress >= 100 ? '#22c55e' : 'var(--primary)' }};">{{ $report->task->progress }}%</span>
                    </div>
                    <div style="width:100%;background:var(--glass-border);border-radius:10px;height:8px;overflow:hidden;">
                        <div style="width:{{ $report->task->progress }}%;background:linear-gradient(90deg,{{ $report->task->progress >= 100 ? '#22c55e,#16a34a' : 'var(--primary),#2d5c4f' }});height:100%;border-radius:10px;transition:width 1s;"></div>
                    </div>
                </div>
                @if($report->task->deadline)
                <p style="font-size:12px;color:{{ $report->task->isOverdue() ? 'var(--danger)' : 'var(--text-muted)' }};display:flex;align-items:center;gap:4px;">
                    <span class="material-symbols-rounded" style="font-size:14px;">event</span>
                    Deadline: {{ $report->task->deadline->format('d M Y') }}
                    @if($report->task->isOverdue())
                        <span class="material-symbols-rounded" style="font-size:14px;color:var(--danger);">warning</span> Overdue
                    @endif
                </p>
                @endif
            </div>

            {{-- Before/After Photos --}}
            @if($report->task->attachments->count() > 0)
            <div class="glass-card" style="padding:24px;margin-bottom:16px;">
                <h3 style="font-weight:700;margin-bottom:14px;font-size:1rem;color:var(--primary);display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">compare</span> Bukti Pengerjaan
                </h3>
                @if($report->task->attachments->where('type','before')->count() > 0)
                <p style="font-size:12px;font-weight:700;color:#f59e0b;margin-bottom:6px;display:flex;align-items:center;gap:4px;text-transform:uppercase;letter-spacing:0.05em;">
                    <span class="material-symbols-rounded" style="font-size:14px;">photo_camera</span> Sebelum
                </p>
                @foreach($report->task->attachments->where('type','before') as $att)
                    <img src="{{ asset('storage/'.$att->file_path) }}" style="width:100%;border-radius:12px;margin-bottom:10px;border:2px solid rgba(245,158,11,0.2);">
                @endforeach
                @endif
                @if($report->task->attachments->where('type','after')->count() > 0)
                <p style="font-size:12px;font-weight:700;color:#22c55e;margin-bottom:6px;display:flex;align-items:center;gap:4px;text-transform:uppercase;letter-spacing:0.05em;">
                    <span class="material-symbols-rounded" style="font-size:14px;">check_circle</span> Sesudah
                </p>
                @foreach($report->task->attachments->where('type','after') as $att)
                    <img src="{{ asset('storage/'.$att->file_path) }}" style="width:100%;border-radius:12px;margin-bottom:10px;border:2px solid rgba(34,197,94,0.2);">
                @endforeach
                @endif
            </div>
            @endif
            @elseif($report->assignment)
            <div class="glass-card" style="padding:24px;margin-bottom:16px;">
                <h3 style="font-weight:700;margin-bottom:14px;font-size:1rem;color:var(--primary);display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">engineering</span> Petugas
                </h3>
                <div style="font-size:14px;">
                    <p style="font-weight:600;color:var(--primary);">{{ $report->assignment->officer->name }}</p>
                    <p style="color:var(--text-muted);font-size:13px;margin-top:4px;">Ditugaskan: {{ $report->assignment->assigned_at->format('d M Y') }}</p>
                </div>
            </div>
            @endif

            {{-- Rating --}}
            @if($report->status === 'done')
            <div class="glass-card" style="padding:24px;">
                <h3 style="font-weight:700;margin-bottom:14px;font-size:1rem;color:var(--primary);display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">star</span> Rating
                </h3>
                @auth
                @php $myRating = $report->ratings->where('user_id', auth()->id())->first(); @endphp
                <form method="POST" action="{{ route('ratings.store', $report) }}">
                    @csrf
                    <div style="display:flex;gap:6px;margin-bottom:14px;">
                        @for($i=1;$i<=5;$i++)
                        <label style="cursor:pointer;">
                            <input type="radio" name="rating" value="{{ $i }}" style="display:none;" {{ ($myRating && $myRating->rating==$i)?'checked':'' }}>
                            <span class="material-symbols-rounded star-rating" data-val="{{ $i }}" style="font-size:28px;color:{{ ($myRating && $myRating->rating>=$i) ? 'var(--accent)' : 'var(--glass-border)' }};font-variation-settings:'FILL' {{ ($myRating && $myRating->rating>=$i) ? '1' : '0' }};cursor:pointer;transition:all 0.2s;">star</span>
                        </label>
                        @endfor
                    </div>
                    <textarea name="feedback" class="form-textarea" style="min-height:60px;" placeholder="Feedback (opsional)">{{ $myRating?->feedback }}</textarea>
                    <button type="submit" class="btn-primary btn-sm" style="margin-top:10px;width:100%;justify-content:center;gap:6px;">
                        <span class="material-symbols-rounded" style="font-size:16px;">send</span> Kirim Rating
                    </button>
                </form>
                @endauth

                @if($report->ratings->count() > 0)
                <div style="margin-top:14px;font-size:14px;color:var(--text-muted);display:flex;align-items:center;gap:6px;">
                    <span class="material-symbols-rounded" style="font-size:18px;color:var(--accent);font-variation-settings:'FILL' 1;">star</span>
                    {{ number_format($report->ratings->avg('rating'), 1) }} ({{ $report->ratings->count() }} rating)
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
@if($report->latitude && $report->longitude)
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('map').setView([{{ $report->latitude }}, {{ $report->longitude }}], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
    L.marker([{{ $report->latitude }}, {{ $report->longitude }}]).addTo(map)
        .bindPopup('<strong>{{ $report->title }}</strong>').openPopup();
});
@endif

@auth
function toggleVote() {
    fetch('{{ route("reports.vote", $report) }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('voteCount').textContent = data.count;
        document.getElementById('voteBtn').classList.toggle('voted', data.voted);
    });
}
@endauth

// Star rating hover effect
document.querySelectorAll('.star-rating').forEach(star => {
    star.addEventListener('mouseenter', function() {
        const val = parseInt(this.dataset.val);
        document.querySelectorAll('.star-rating').forEach(s => {
            const sv = parseInt(s.dataset.val);
            s.style.color = sv <= val ? 'var(--accent)' : 'var(--glass-border)';
            s.style.fontVariationSettings = sv <= val ? "'FILL' 1" : "'FILL' 0";
        });
    });
    star.addEventListener('click', function() {
        this.previousElementSibling.checked = true;
    });
});

const ratingContainer = document.querySelector('[style*="display:flex;gap:6px;margin-bottom:14px"]');
if (ratingContainer) {
    ratingContainer.addEventListener('mouseleave', function() {
        const checked = this.querySelector('input:checked');
        const checkedVal = checked ? parseInt(checked.value) : 0;
        document.querySelectorAll('.star-rating').forEach(s => {
            const sv = parseInt(s.dataset.val);
            s.style.color = sv <= checkedVal ? 'var(--accent)' : 'var(--glass-border)';
            s.style.fontVariationSettings = sv <= checkedVal ? "'FILL' 1" : "'FILL' 0";
        });
    });
}
</script>
@endpush
@endsection
