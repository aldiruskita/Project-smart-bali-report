@extends('layouts.app')
@section('title', 'Detail Tugas')
@section('content')
<div class="container" style="max-width:1000px;">
    <a href="{{ route('officer.dashboard') }}" style="color:var(--text-muted);text-decoration:none;font-size:14px;display:inline-flex;align-items:center;gap:6px;margin-bottom:20px;font-weight:500;transition:color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'"><span class="material-symbols-rounded" style="font-size:18px;">arrow_back</span> Kembali ke Dashboard</a>

    {{-- Task Header --}}
    <div class="glass-card" style="padding:32px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
            <div style="width:48px;height:48px;border-radius:14px;background:rgba(212,175,55,0.1);display:flex;align-items:center;justify-content:center;"><span class="material-symbols-rounded" style="font-size:26px;color:var(--accent);">{{ $task->report->category->icon ?? 'description' }}</span></div>
            <span class="badge badge-{{ $task->status_badge }}" style="font-size:14px;padding:6px 16px;">{{ $task->status_label }}</span>
            <span style="font-size:13px;">{{ $task->priority_badge }}</span>
            @if($task->isOverdue())
                <span class="badge" style="background:rgba(239,68,68,0.2);color:#f87171;display:flex;align-items:center;gap:4px;"><span class="material-symbols-rounded" style="font-size:14px;">alarm</span> Overdue!</span>
            @endif
        </div>
        <h1 style="font-family:'Noto Serif',serif;font-size:1.5rem;font-weight:700;margin-bottom:12px;color:var(--primary);">{{ $task->title }}</h1>
        <div style="display:flex;gap:20px;font-size:13px;color:var(--text-muted);flex-wrap:wrap;">
            <span style="display:flex;align-items:center;gap:6px;"><span class="material-symbols-rounded" style="font-size:16px;">engineering</span> Petugas: {{ $task->officer->name }}</span>
            <span style="display:flex;align-items:center;gap:6px;"><span class="material-symbols-rounded" style="font-size:16px;">admin_panel_settings</span> Ditugaskan oleh: {{ $task->assigner->name }}</span>
            @if($task->deadline)<span style="display:flex;align-items:center;gap:6px;"><span class="material-symbols-rounded" style="font-size:16px;">event</span> Deadline: {{ $task->deadline->format('d M Y') }}</span>@endif
            <span style="display:flex;align-items:center;gap:6px;"><span class="material-symbols-rounded" style="font-size:16px;">calendar_today</span> Dibuat: {{ $task->created_at->format('d M Y, H:i') }}</span>
        </div>

        {{-- Progress Bar --}}
        <div style="margin-top:16px;">
            <div style="display:flex;justify-content:space-between;font-size:13px;color:#94a3b8;margin-bottom:6px;">
                <span style="display:flex;align-items:center;gap:4px;"><span class="material-symbols-rounded" style="font-size:16px;">bar_chart</span> Progress</span>
                <span style="font-weight:700;color:{{ $task->progress >= 100 ? '#22c55e' : 'var(--primary-light)' }};">{{ $task->progress }}%</span>
            </div>
            <div style="width:100%;background:rgba(255,255,255,0.1);border-radius:10px;height:12px;overflow:hidden;">
                <div style="width:{{ $task->progress }}%;background:linear-gradient(90deg,{{ $task->progress >= 100 ? '#22c55e,#16a34a' : '#6366f1,#8b5cf6' }});height:100%;border-radius:10px;transition:width 1s ease;"></div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;">
        <div>
            {{-- Description --}}
            <div class="glass-card" style="padding:24px;margin-bottom:24px;">
                <h2 style="font-weight:700;margin-bottom:12px;font-size:1.05rem;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">edit_note</span> Deskripsi Tugas</h2>
                <p style="line-height:1.7;color:#cbd5e1;">{{ $task->description ?: $task->report->description }}</p>
            </div>

            {{-- Report Media --}}
            @if($task->report->media->count() > 0)
            <div class="glass-card" style="padding:24px;margin-bottom:24px;">
                <h2 style="font-weight:700;margin-bottom:12px;font-size:1.05rem;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">photo_library</span> Foto Laporan</h2>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;">
                    @foreach($task->report->media as $media)
                        <img src="{{ asset('storage/'.$media->file_path) }}" style="width:100%;border-radius:12px;cursor:zoom-in;" onclick="openLightbox(this.src)" loading="lazy" alt="">
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Before/After Attachments --}}
            <div class="glass-card" style="padding:24px;margin-bottom:24px;">
                <h2 style="font-weight:700;margin-bottom:16px;font-size:1.05rem;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">compare</span> Bukti Pengerjaan ({{ $task->attachments->count() }})</h2>

                @if($task->attachments->count() > 0)
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    {{-- Before --}}
                    <div>
                        <h4 style="font-size:13px;font-weight:700;color:#f59e0b;margin-bottom:8px;display:flex;align-items:center;gap:4px;text-transform:uppercase;letter-spacing:0.05em;"><span class="material-symbols-rounded" style="font-size:14px;">photo_camera</span> Sebelum</h4>
                        @foreach($task->attachments->where('type', 'before') as $att)
                        <div style="margin-bottom:8px;">
                            <img src="{{ asset('storage/'.$att->file_path) }}" style="width:100%;border-radius:10px;border:2px solid rgba(245,158,11,0.3);">
                            @if($att->caption)<p style="font-size:11px;color:#94a3b8;margin-top:4px;">{{ $att->caption }}</p>@endif
                        </div>
                        @endforeach
                        @if($task->attachments->where('type','before')->count() == 0)
                            <p style="font-size:12px;color:#64748b;">Belum ada foto</p>
                        @endif
                    </div>
                    {{-- After --}}
                    <div>
                        <h4 style="font-size:13px;font-weight:700;color:#22c55e;margin-bottom:8px;display:flex;align-items:center;gap:4px;text-transform:uppercase;letter-spacing:0.05em;"><span class="material-symbols-rounded" style="font-size:14px;">check_circle</span> Sesudah</h4>
                        @foreach($task->attachments->where('type', 'after') as $att)
                        <div style="margin-bottom:8px;">
                            <img src="{{ asset('storage/'.$att->file_path) }}" style="width:100%;border-radius:10px;border:2px solid rgba(34,197,94,0.3);">
                            @if($att->caption)<p style="font-size:11px;color:#94a3b8;margin-top:4px;">{{ $att->caption }}</p>@endif
                        </div>
                        @endforeach
                        @if($task->attachments->where('type','after')->count() == 0)
                            <p style="font-size:12px;color:#64748b;">Belum ada foto</p>
                        @endif
                    </div>
                </div>

                {{-- Progress photos --}}
                @if($task->attachments->where('type','progress')->count() > 0)
                <h4 style="font-size:13px;font-weight:700;color:#6366f1;margin-bottom:8px;display:flex;align-items:center;gap:4px;text-transform:uppercase;letter-spacing:0.05em;"><span class="material-symbols-rounded" style="font-size:14px;">sync</span> Progress</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:8px;">
                    @foreach($task->attachments->where('type', 'progress') as $att)
                    <div>
                        <img src="{{ asset('storage/'.$att->file_path) }}" style="width:100%;border-radius:8px;">
                        @if($att->caption)<p style="font-size:10px;color:#64748b;">{{ $att->caption }}</p>@endif
                    </div>
                    @endforeach
                </div>
                @endif
                @else
                <p style="color:#64748b;font-size:14px;">Belum ada bukti diupload.</p>
                @endif

                {{-- Upload Form (officer only) --}}
                @if($task->assigned_to === auth()->id() && !in_array($task->status, ['verified', 'rejected']))
                <div style="margin-top:16px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.05);">
                    <form method="POST" action="{{ route('tasks.upload', $task) }}" enctype="multipart/form-data" style="display:flex;gap:8px;flex-wrap:wrap;align-items:end;">
                        @csrf
                        <div style="flex:1;min-width:150px;">
                            <input type="file" name="file" accept="image/*" class="form-input" style="padding:8px;" required>
                        </div>
                        <select name="type" class="form-select" style="width:120px;">
                            <option value="before">Sebelum</option>
                            <option value="after">Sesudah</option>
                            <option value="progress">Progress</option>
                        </select>
                        <input type="text" name="caption" class="form-input" placeholder="Keterangan..." style="width:160px;">
                        <button type="submit" class="btn-primary btn-sm" style="gap:6px;"><span class="material-symbols-rounded" style="font-size:16px;">upload</span> Upload</button>
                    </form>
                </div>
                @endif
            </div>

            {{-- Map --}}
            @if($task->report->latitude && $task->report->longitude)
            <div class="glass-card" style="padding:24px;margin-bottom:24px;">
                <h2 style="font-weight:700;margin-bottom:12px;font-size:1.05rem;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">location_on</span> Lokasi</h2>
                @if($task->report->address)<p style="font-size:14px;color:#94a3b8;margin-bottom:12px;">{{ $task->report->address }}</p>@endif
                <div class="map-container"><div id="map" style="height:300px;"></div></div>
            </div>
            @endif

            {{-- Comments --}}
            <div class="glass-card" style="padding:24px;">
                <h2 style="font-weight:700;margin-bottom:16px;font-size:1.05rem;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">forum</span> Komentar ({{ $task->comments->count() }})</h2>

                <form method="POST" action="{{ route('tasks.comment', $task) }}" style="margin-bottom:20px;">
                    @csrf
                    <textarea name="message" class="form-textarea" style="min-height:80px;" placeholder="Tulis komentar atau catatan..." required></textarea>
                    <button type="submit" class="btn-primary btn-sm" style="margin-top:8px;gap:6px;"><span class="material-symbols-rounded" style="font-size:16px;">send</span> Kirim</button>
                </form>

                @forelse($task->comments as $comment)
                <div style="padding:14px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-weight:600;font-size:14px;">{{ $comment->user->name }}</span>
                            <span class="badge" style="font-size:10px;padding:2px 8px;background:{{ $comment->user->isAdmin() ? 'rgba(245,158,11,0.2)' : ($comment->user->isPetugas() ? 'rgba(59,130,246,0.2)' : 'rgba(107,114,128,0.2)') }};color:{{ $comment->user->isAdmin() ? '#fbbf24' : ($comment->user->isPetugas() ? '#60a5fa' : '#9ca3af') }};">{{ $comment->user->role_label }}</span>
                        </div>
                        <span style="font-size:12px;color:#64748b;">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p style="font-size:14px;color:#cbd5e1;">{{ $comment->message }}</p>
                </div>
                @empty
                <p style="color:#64748b;font-size:14px;">Belum ada komentar.</p>
                @endforelse
            </div>
        </div>

        {{-- Sidebar --}}
        <div>
            {{-- Actions (Officer) --}}
            @if($task->assigned_to === auth()->id())
            <div class="glass-card" style="padding:24px;margin-bottom:16px;">
                <h3 style="font-weight:700;margin-bottom:16px;font-size:1rem;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">bolt</span> Aksi</h3>

                @if($task->status === 'assigned')
                <div style="display:flex;gap:8px;margin-bottom:12px;">
                    <form method="POST" action="{{ route('tasks.accept', $task) }}" style="flex:1;">
                        @csrf
                        <button type="submit" class="btn-primary" style="width:100%;justify-content:center;gap:6px;"><span class="material-symbols-rounded" style="font-size:18px;">check_circle</span> Terima</button>
                    </form>
                    <button onclick="document.getElementById('rejectForm').style.display='block'" class="btn-secondary" style="flex:1;justify-content:center;gap:6px;"><span class="material-symbols-rounded" style="font-size:18px;">cancel</span> Tolak</button>
                </div>
                <form method="POST" action="{{ route('tasks.reject', $task) }}" id="rejectForm" style="display:none;">
                    @csrf
                    <textarea name="rejection_reason" class="form-textarea" placeholder="Alasan penolakan..." required style="min-height:60px;margin-bottom:8px;"></textarea>
                    <button type="submit" class="btn-sm" style="background:rgba(239,68,68,0.2);color:#f87171;border:1px solid rgba(239,68,68,0.3);width:100%;padding:8px;border-radius:10px;cursor:pointer;">Kirim Penolakan</button>
                </form>
                @endif

                @if(in_array($task->status, ['accepted', 'in_progress']))
                <form method="POST" action="{{ route('tasks.progress', $task) }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Progress (%)</label>
                        <input type="range" name="progress" min="0" max="100" step="5" value="{{ $task->progress }}" id="progressRange"
                            oninput="document.getElementById('progressVal').textContent=this.value+'%'"
                            style="width:100%;accent-color:var(--primary);">
                        <div style="text-align:center;font-weight:700;font-size:1.2rem;color:var(--primary-light);" id="progressVal">{{ $task->progress }}%</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>Dikerjakan</option>
                            <option value="done">Selesai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="note" class="form-textarea" style="min-height:60px;" placeholder="Catatan progress..."></textarea>
                    </div>
                    <button type="submit" class="btn-primary" style="width:100%;justify-content:center;gap:6px;"><span class="material-symbols-rounded" style="font-size:18px;">save</span> Update Progress</button>
                </form>
                @endif

                @if($task->status === 'done')
                <div style="text-align:center;padding:16px;">
                    <span class="material-symbols-rounded" style="font-size:40px;color:#22c55e;margin-bottom:8px;display:block;">check_circle</span>
                    <p style="color:#22c55e;font-weight:600;">Tugas selesai!</p>
                    <p style="color:#94a3b8;font-size:12px;">Menunggu verifikasi admin</p>
                </div>
                @endif

                @if($task->status === 'verified')
                <div style="text-align:center;padding:16px;">
                    <span class="material-symbols-rounded" style="font-size:40px;color:#22c55e;margin-bottom:8px;display:block;">emoji_events</span>
                    <p style="color:#22c55e;font-weight:600;">Tugas terverifikasi!</p>
                </div>
                @endif
            </div>
            @endif

            {{-- Admin Verify --}}
            @if(auth()->user()->isAdmin() && $task->status === 'done')
            <div class="glass-card" style="padding:24px;margin-bottom:16px;">
                <h3 style="font-weight:700;margin-bottom:12px;font-size:1rem;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">admin_panel_settings</span> Verifikasi Admin</h3>
                <form method="POST" action="{{ route('tasks.verify', $task) }}">
                    @csrf
                    <button type="submit" class="btn-primary" style="width:100%;justify-content:center;gap:6px;background:linear-gradient(135deg,#22c55e,#16a34a);"><span class="material-symbols-rounded" style="font-size:18px;">verified</span> Verifikasi Selesai</button>
                </form>
            </div>
            @endif

            {{-- Report Info --}}
            <div class="glass-card" style="padding:24px;margin-bottom:16px;">
                <h3 style="font-weight:700;margin-bottom:12px;font-size:1rem;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">description</span> Info Laporan</h3>
                <div style="font-size:14px;">
                    <p style="margin-bottom:8px;"><span style="color:#64748b;">Judul:</span> {{ $task->report->title }}</p>
                    <p style="margin-bottom:8px;"><span style="color:#64748b;">Kategori:</span> <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;color:var(--accent);">{{ $task->report->category->icon }}</span> {{ $task->report->category->name }}</p>
                    <p style="margin-bottom:8px;"><span style="color:#64748b;">Pelapor:</span> {{ $task->report->reporter_name }}</p>
                    <p style="margin-bottom:8px;"><span style="color:#64748b;">Status Laporan:</span> <span class="badge badge-{{ $task->report->status_badge }}">{{ $task->report->status_label }}</span></p>
                    <a href="{{ route('reports.show', $task->report) }}" style="color:var(--primary-light);font-size:13px;">Lihat laporan →</a>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="glass-card" style="padding:24px;">
                <h3 style="font-weight:700;margin-bottom:16px;font-size:1rem;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">timeline</span> Timeline</h3>
                <div class="timeline">
                    @foreach($task->report->logs as $log)
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:{{ match($log->status) { 'pending'=>'#f59e0b','verified'=>'#3b82f6','process'=>'#6366f1','done'=>'#22c55e','rejected'=>'#ef4444',default=>'#6b7280' } }};"></div>
                        <div style="font-weight:600;font-size:14px;">{{ $log->status_label }}</div>
                        @if($log->note)<div style="font-size:13px;color:#94a3b8;">{{ $log->note }}</div>@endif
                        <div style="font-size:12px;color:#64748b;margin-top:4px;">{{ $log->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
@if($task->report->latitude && $task->report->longitude)
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('map').setView([{{ $task->report->latitude }}, {{ $task->report->longitude }}], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
    L.marker([{{ $task->report->latitude }}, {{ $task->report->longitude }}]).addTo(map)
        .bindPopup('<strong>{{ $task->report->title }}</strong>').openPopup();
});
@endif
</script>
@endpush
@endsection
