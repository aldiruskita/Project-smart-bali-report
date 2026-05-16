@extends('layouts.app')
@section('title', 'Beranda')
@section('content')

{{-- Hero Section --}}
<section class="hero-section" style="position:relative;height:85vh;min-height:600px;display:flex;align-items:center;overflow:hidden;">
    <div style="position:absolute;inset:0;z-index:0;">
        <img class="hero-bg-img" src="{{ asset('images/hero-bali.png') }}" style="width:100%;height:100%;object-fit:cover;" alt="Candi Bentar Bali">
        <div class="hero-gradient" style="position:absolute;inset:0;background:linear-gradient(to right, rgba(26,58,50,0.95), rgba(26,58,50,0.65));"></div>
        <div style="position:absolute;bottom:0;left:0;right:0;height:150px;background:linear-gradient(to top, var(--bg-light) 0%, rgba(251,249,244,0.3) 50%, transparent 100%);z-index:1;"></div>
    </div>
    <div class="hero-content" style="position:relative;z-index:10;max-width:1280px;margin:0 auto;padding:0 40px;width:100%;color:white;">
        <div style="max-width:640px;">
            <span class="hero-badge" style="font-size:12px;font-weight:700;letter-spacing:0.1em;color:#D4AF37;display:inline-block;margin-bottom:16px;text-transform:uppercase;background:rgba(212,175,55,0.15);padding:6px 16px;border-radius:20px;border:1px solid rgba(212,175,55,0.3);">TRI HITA KARANA</span>
            <h1 style="font-family:'Noto Serif',serif;font-size:2.5rem;font-weight:700;margin-bottom:24px;line-height:1.2;letter-spacing:-0.02em;">Harmonisasi Pelayanan Publik Berbasis Tradisi</h1>
            <p class="hero-desc" style="font-size:18px;color:rgba(255,255,255,0.8);margin-bottom:40px;line-height:1.6;max-width:520px;">
                Mewujudkan Bali yang bersih, aman, dan tertata melalui partisipasi aktif warga dalam menjaga fasilitas publik.
            </p>
            <div class="hero-cta" style="display:flex;flex-wrap:wrap;gap:16px;">
                @auth
                    @if(auth()->user()->isWarga())
                        <a href="{{ route('reports.create') }}" class="btn-primary" style="padding:14px 32px;font-size:14px;border-radius:14px;">
                            <span class="material-symbols-rounded" style="font-size:20px;">campaign</span> Mulai Lapor Sekarang
                        </a>
                        <a href="{{ route('reports.my') }}" style="background:rgba(255,255,255,0.1);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.2);color:white;padding:14px 32px;border-radius:14px;font-size:14px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px;">
                            <span class="material-symbols-rounded" style="font-size:20px;">explore</span> Laporan Saya
                        </a>
                    @elseif(auth()->user()->isPetugas())
                        @php $myPending = \App\Models\Task::where('assigned_to', auth()->id())->whereIn('status',['assigned','accepted','in_progress'])->count(); @endphp
                        <a href="{{ route('officer.dashboard') }}" class="btn-primary" style="padding:14px 32px;font-size:14px;border-radius:14px;">
                            <span class="material-symbols-rounded" style="font-size:20px;">engineering</span> Tugas Saya ({{ $myPending }})
                        </a>
                    @elseif(auth()->user()->isAdmin())
                        @php $pendingAdmin = \App\Models\Report::where('status','pending')->count(); @endphp
                        <a href="{{ route('admin.dashboard') }}" class="btn-primary" style="padding:14px 32px;font-size:14px;border-radius:14px;">
                            <span class="material-symbols-rounded" style="font-size:20px;">dashboard</span> Dashboard Admin
                        </a>
                        <a href="{{ route('admin.reports') }}" style="background:rgba(255,255,255,0.1);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.2);color:white;padding:14px 32px;border-radius:14px;font-size:14px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px;">
                            <span class="material-symbols-rounded" style="font-size:20px;">edit_note</span> Kelola ({{ $pendingAdmin }} menunggu)
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="btn-primary" style="padding:14px 32px;font-size:14px;border-radius:14px;">
                        <span class="material-symbols-rounded" style="font-size:20px;">campaign</span> Mulai Lapor Sekarang
                    </a>
                    <a href="{{ route('reports.index') }}" style="background:rgba(255,255,255,0.1);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.2);color:white;padding:14px 32px;border-radius:14px;font-size:14px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px;">
                        <span class="material-symbols-rounded" style="font-size:20px;">explore</span> Telusuri Laporan
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

{{-- Stats Bento --}}
<section class="stats-bento" style="position:relative;z-index:20;margin-top:-60px;padding:0 40px;">
    <div style="max-width:1280px;margin:0 auto;" class="grid-bento grid-3">
        <div class="stat-card bento-full" style="border-top:4px solid var(--primary);">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
                <span class="material-symbols-rounded" style="font-size:36px;color:var(--primary);font-variation-settings:'FILL' 1;">description</span>
                <div style="display:flex;align-items:center;color:#16a34a;font-weight:700;font-size:13px;background:rgba(22,163,74,0.08);padding:4px 8px;border-radius:6px;">
                    <span class="material-symbols-rounded" style="font-size:16px;margin-right:2px;">trending_up</span> +12%
                </div>
            </div>
            <div class="stat-label">Total Laporan</div>
            <div class="stat-number">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="stat-card" style="border-top:4px solid #D4AF37;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
                <span class="material-symbols-rounded" style="font-size:36px;color:#D4AF37;font-variation-settings:'FILL' 1;">sync</span>
                <div style="display:flex;align-items:center;color:#b45309;font-weight:700;font-size:13px;background:rgba(245,158,11,0.08);padding:4px 8px;border-radius:6px;">
                    <span class="material-symbols-rounded" style="font-size:16px;margin-right:2px;">timer</span> 48h Avg
                </div>
            </div>
            <div class="stat-label">Sedang Diproses</div>
            <div class="stat-number">{{ number_format($stats['process']) }}</div>
        </div>
        <div class="stat-card" style="border-top:4px solid var(--secondary);">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
                <span class="material-symbols-rounded" style="font-size:36px;color:var(--secondary);font-variation-settings:'FILL' 1;">task_alt</span>
                <div style="display:flex;align-items:center;color:#16a34a;font-weight:700;font-size:13px;background:rgba(22,163,74,0.08);padding:4px 8px;border-radius:6px;">
                    <span class="material-symbols-rounded" style="font-size:16px;margin-right:2px;">verified</span> {{ $stats['total'] > 0 ? round($stats['done']/$stats['total']*100) : 0 }}%
                </div>
            </div>
            <div class="stat-label">Laporan Selesai</div>
            <div class="stat-number">{{ number_format($stats['done']) }}</div>
        </div>
    </div>
</section>

{{-- Categories Grid --}}
<section class="categories-section" style="padding:64px 40px;max-width:1280px;margin:0 auto;">
    <div style="text-align:center;margin-bottom:48px;">
        <h2 style="font-family:'Noto Serif',serif;font-size:2rem;font-weight:600;color:var(--primary);margin-bottom:8px;">Kategori Pengaduan</h2>
        <div style="height:3px;width:80px;background:linear-gradient(to right,transparent,#D4AF37,transparent);margin:0 auto 16px;"></div>
        <p class="cat-desc" style="color:var(--text-secondary);font-family:'Noto Serif',serif;font-style:italic;max-width:500px;margin:0 auto;">Pilih kategori laporan Anda untuk mempercepat koordinasi dengan tim teknis daerah.</p>
    </div>
    <div class="categories-grid grid-4">
        @foreach($categories as $cat)
        @php
            $colors = ['#a23e23','#16a34a','#D4AF37','#3b82f6','#8b5cf6','#ec4899','#f59e0b','#6b7280'];
            $color = $colors[$loop->index % count($colors)];
        @endphp
        <div class="glass-card cat-item" style="padding:32px 20px;text-align:center;cursor:pointer;border-bottom:4px solid {{ $color }};border-radius:24px;" onclick="window.location='{{ route('reports.index', ['category' => $cat->id]) }}'">
            <div class="cat-icon" style="width:64px;height:64px;background:{{ $color }}12;border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <span class="material-symbols-rounded" style="font-size:28px;color:{{ $color }};font-variation-settings:'FILL' 0,'wght' 300;">{{ $cat->icon }}</span>
            </div>
            <div class="cat-name" style="font-family:'Noto Serif',serif;font-weight:600;color:var(--primary);font-size:15px;margin-bottom:4px;">{{ $cat->name }}</div>
            <div class="cat-count" style="color:var(--text-muted);font-size:12px;">{{ $cat->reports_count }} laporan</div>
        </div>
        @endforeach
    </div>
</section>

{{-- How It Works --}}
<section style="padding:64px 40px;background:var(--bg-surface);">
    <div style="max-width:1280px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:48px;">
            <h2 style="font-family:'Noto Serif',serif;font-size:2rem;font-weight:600;color:var(--primary);margin-bottom:8px;">Bagaimana Cara Kerjanya?</h2>
            <div style="height:3px;width:80px;background:linear-gradient(to right,transparent,#D4AF37,transparent);margin:0 auto 16px;"></div>
            <p style="color:var(--text-secondary);max-width:500px;margin:0 auto;">Tiga langkah mudah untuk melaporkan masalah di lingkungan Anda</p>
        </div>
        <div class="grid-3" style="gap:32px;">
            <div class="glass-card" style="padding:0;text-align:center;border-radius:24px;overflow:visible;border-top:4px solid var(--secondary);">
                <div style="padding:36px 28px;">
                    <div style="width:44px;height:44px;border-radius:50%;background:var(--secondary);color:white;font-weight:800;font-size:18px;display:flex;align-items:center;justify-content:center;margin:-58px auto 16px;box-shadow:0 4px 16px rgba(162,62,35,0.3);border:4px solid var(--bg-surface);">1</div>
                    <span class="material-symbols-rounded" style="font-size:40px;color:var(--primary);margin-bottom:16px;display:block;">campaign</span>
                    <h3 style="font-family:'Noto Serif',serif;font-weight:600;color:var(--primary);margin-bottom:8px;font-size:1.05rem;">Buat Laporan</h3>
                    <p style="color:var(--text-muted);font-size:13px;line-height:1.6;">Isi formulir dengan deskripsi masalah, foto, dan lokasi. AI akan membantu klasifikasi otomatis.</p>
                </div>
            </div>
            <div class="glass-card" style="padding:0;text-align:center;border-radius:24px;overflow:visible;border-top:4px solid #D4AF37;">
                <div style="padding:36px 28px;">
                    <div style="width:44px;height:44px;border-radius:50%;background:#D4AF37;color:var(--primary);font-weight:800;font-size:18px;display:flex;align-items:center;justify-content:center;margin:-58px auto 16px;box-shadow:0 4px 16px rgba(212,175,55,0.3);border:4px solid var(--bg-surface);">2</div>
                    <span class="material-symbols-rounded" style="font-size:40px;color:var(--primary);margin-bottom:16px;display:block;">engineering</span>
                    <h3 style="font-family:'Noto Serif',serif;font-weight:600;color:var(--primary);margin-bottom:8px;font-size:1.05rem;">Tim Tindak Lanjut</h3>
                    <p style="color:var(--text-muted);font-size:13px;line-height:1.6;">Admin memverifikasi dan menugaskan petugas lapangan untuk menangani masalah Anda.</p>
                </div>
            </div>
            <div class="glass-card" style="padding:0;text-align:center;border-radius:24px;overflow:visible;border-top:4px solid var(--success);">
                <div style="padding:36px 28px;">
                    <div style="width:44px;height:44px;border-radius:50%;background:var(--success);color:white;font-weight:800;font-size:18px;display:flex;align-items:center;justify-content:center;margin:-58px auto 16px;box-shadow:0 4px 16px rgba(22,163,74,0.3);border:4px solid var(--bg-surface);">3</div>
                    <span class="material-symbols-rounded" style="font-size:40px;color:var(--primary);margin-bottom:16px;display:block;">verified</span>
                    <h3 style="font-family:'Noto Serif',serif;font-weight:600;color:var(--primary);margin-bottom:8px;font-size:1.05rem;">Masalah Terselesaikan</h3>
                    <p style="color:var(--text-muted);font-size:13px;line-height:1.6;">Pantau progress secara real-time dan berikan rating setelah masalah selesai ditangani.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Map Section --}}
<section class="map-section" style="padding:64px 0;background:var(--primary);position:relative;overflow:hidden;">
    <div style="max-width:1280px;margin:0 auto;padding:0 40px;position:relative;z-index:10;">
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;margin-bottom:32px;gap:24px;">
            <div style="color:white;">
                <h2 style="font-family:'Noto Serif',serif;font-size:2rem;font-weight:600;margin-bottom:8px;">Sebaran Wilayah Laporan</h2>
                <p class="map-desc" style="color:rgba(255,255,255,0.7);font-size:16px;">Pantau titik laporan warga secara real-time di seluruh wilayah.</p>
            </div>
        </div>
        <div class="map-container" style="border:4px solid rgba(255,255,255,0.1);border-radius:16px;">
            <div id="map"></div>
        </div>
        <div class="map-legend" style="display:flex;gap:24px;margin-top:16px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:12px;height:12px;border-radius:50%;background:var(--secondary);"></div>
                <span style="font-size:12px;color:rgba(255,255,255,0.7);font-weight:600;letter-spacing:0.05em;text-transform:uppercase;">Mendesak</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:12px;height:12px;border-radius:50%;background:#D4AF37;"></div>
                <span style="font-size:12px;color:rgba(255,255,255,0.7);font-weight:600;letter-spacing:0.05em;text-transform:uppercase;">Dalam Proses</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:12px;height:12px;border-radius:50%;background:#16a34a;"></div>
                <span style="font-size:12px;color:rgba(255,255,255,0.7);font-weight:600;letter-spacing:0.05em;text-transform:uppercase;">Selesai</span>
            </div>
        </div>
    </div>
</section>

{{-- Latest Reports --}}
<section class="reports-section" style="padding:64px 40px;max-width:1280px;margin:0 auto;">
    <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:40px;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-family:'Noto Serif',serif;font-size:2rem;font-weight:600;color:var(--primary);margin-bottom:8px;">Laporan Terkini</h2>
            <p style="color:var(--text-secondary);">Terbuka dan transparan untuk kenyamanan bersama.</p>
        </div>
        <a href="{{ route('reports.index') }}" style="color:var(--secondary);font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;border-bottom:1px solid var(--secondary);padding-bottom:4px;">Lihat Semua Laporan</a>
    </div>
    <div class="grid-3">
        @foreach($reports as $report)
        <a href="{{ route('reports.show', $report) }}" class="glass-card report-card" style="text-decoration:none;color:inherit;display:block;border-radius:20px;overflow:hidden;">
            @if($report->media->first())
            <div class="report-img" style="height:192px;position:relative;overflow:hidden;">
                <img src="{{ asset('storage/'.$report->media->first()->file_path) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                @php
                    $statusColors = ['pending'=>'background:var(--secondary);color:white;','verified'=>'background:#3b82f6;color:white;','process'=>'background:#D4AF37;color:var(--primary);','done'=>'background:#16a34a;color:white;','rejected'=>'background:#ef4444;color:white;'];
                    $statusLabels = ['pending'=>'MENUNGGU','verified'=>'TERVERIFIKASI','process'=>'SEDANG DIPROSES','done'=>'SELESAI','rejected'=>'DITOLAK'];
                @endphp
                <div style="position:absolute;top:12px;left:12px;{{ $statusColors[$report->status] ?? '' }}font-size:10px;font-weight:700;letter-spacing:0.1em;padding:4px 12px;border-radius:20px;backdrop-filter:blur(4px);">
                    {{ $statusLabels[$report->status] ?? strtoupper($report->status) }}
                </div>
            </div>
            @endif
            <div class="report-body" style="padding:20px;">
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:12px;">
                    <span class="material-symbols-rounded" style="font-size:16px;color:#D4AF37;">location_on</span>
                    <span style="font-size:11px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--text-muted);">{{ $report->address ? Str::limit($report->address, 30) : $report->category->name }}</span>
                </div>
                <h3 style="font-family:'Noto Serif',serif;font-weight:600;color:var(--primary);font-size:1.05rem;margin-bottom:8px;line-height:1.4;">{{ $report->title }}</h3>
                <p style="color:var(--text-secondary);font-size:14px;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:16px;">{{ Str::limit($report->description, 100) }}</p>
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid rgba(0,0,0,0.06);">
                    <div style="display:flex;align-items:center;gap:12px;color:var(--text-muted);">
                        <div style="display:flex;align-items:center;gap:4px;">
                            <span class="material-symbols-rounded" style="font-size:16px;">thumb_up</span>
                            <span style="font-size:12px;">{{ $report->votes_count }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:4px;">
                            <span class="material-symbols-rounded" style="font-size:16px;">chat_bubble</span>
                            <span style="font-size:12px;">{{ $report->comments_count }}</span>
                        </div>
                    </div>
                    <span style="font-size:10px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--text-muted);">{{ $report->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('map').setView([-8.4095, 115.1889], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const markers = L.markerClusterGroup();

    fetch('{{ route("api.reports.map") }}')
        .then(r => r.json())
        .then(data => {
            data.forEach(report => {
                const colors = { pending:'#a23e23', verified:'#3b82f6', process:'#D4AF37', done:'#16a34a', rejected:'#ef4444' };
                const color = colors[report.status] || '#6b7280';
                const icon = L.divIcon({
                    html: `<div style="background:${color};width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid white;box-shadow:0 2px 10px rgba(0,0,0,0.2);"><span class="material-symbols-rounded" style="font-size:16px;color:white;font-variation-settings:'FILL' 0,'wght' 300;">${report.category_icon}</span></div>`,
                    className: '', iconSize: [32, 32], iconAnchor: [16, 16]
                });
                const marker = L.marker([report.latitude, report.longitude], { icon });
                marker.bindPopup(`
                    <div style="min-width:200px;font-family:'Public Sans',sans-serif;">
                        <strong style="color:#1A3A32;">${report.title}</strong><br>
                        <small style="color:#717975;">${report.category} · ${report.status_label}</small><br>
                        <small>${report.votes_count} votes · ${report.created_at}</small><br>
                        <a href="${report.url}" style="color:#a23e23;font-weight:600;">Lihat Detail →</a>
                    </div>
                `);
                markers.addLayer(marker);
            });
            map.addLayer(markers);
        });
});
</script>
@endpush
@endsection
