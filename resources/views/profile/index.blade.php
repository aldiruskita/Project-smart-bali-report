@extends('layouts.app')
@section('title', 'Profile')
@section('content')
<div class="container" style="max-width:1000px;">

    {{-- Profile Header --}}
    <div class="glass-card" style="padding:0;overflow:hidden;margin-bottom:24px;">
        <div style="height:120px;background:linear-gradient(135deg,var(--primary),#8b5cf6,#ec4899);position:relative;">
            <div style="position:absolute;bottom:-50px;left:32px;">
                <div style="width:100px;height:100px;border-radius:50%;border:4px solid var(--glass-bg);overflow:hidden;background:#1e293b;">
                    <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=6366f1&color=fff&size=200' }}"
                         style="width:100%;height:100%;object-fit:cover;" alt="Avatar" id="avatarPreview">
                </div>
            </div>
        </div>
        <div style="padding:60px 32px 24px;">
            <div style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:12px;">
                <div>
                    <h1 style="font-size:1.5rem;font-weight:800;">{{ $user->name }}</h1>
                    <p style="color:#94a3b8;font-size:14px;">{{ $user->email }}</p>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:8px;">
                        <span class="badge" style="background:{{ match($user->role) { 'super_admin'=>'rgba(239,68,68,0.2)', 'admin_desa'=>'rgba(245,158,11,0.2)', 'petugas'=>'rgba(59,130,246,0.2)', default=>'rgba(107,114,128,0.2)' } }};color:{{ match($user->role) { 'super_admin'=>'#f87171', 'admin_desa'=>'#fbbf24', 'petugas'=>'#60a5fa', default=>'#9ca3af' } }};">
                            {{ $user->role_label }}
                        </span>
                        @if($user->phone)
                            <span style="font-size:13px;color:#64748b;display:flex;align-items:center;gap:4px;"><span class="material-symbols-rounded" style="font-size:16px;">phone</span> {{ $user->phone }}</span>
                        @endif
                        <span style="font-size:13px;color:#64748b;display:flex;align-items:center;gap:4px;"><span class="material-symbols-rounded" style="font-size:16px;">calendar_today</span> Bergabung {{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Badges --}}
    @if(count($badges) > 0)
    <div class="glass-card" style="padding:24px;margin-bottom:24px;">
        <h2 style="font-weight:700;margin-bottom:16px;font-size:1.1rem;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">emoji_events</span> Badge & Penghargaan</h2>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            @foreach($badges as $badge)
            <div style="display:flex;align-items:center;gap:8px;padding:10px 16px;background:{{ $badge['color'] }}15;border:1px solid {{ $badge['color'] }}40;border-radius:12px;">
                <span style="font-size:1.3rem;">{{ $badge['icon'] }}</span>
                <span style="font-weight:600;font-size:13px;color:{{ $badge['color'] }};">{{ $badge['name'] }}</span>
            </div>
            @endforeach
        </div>

        {{-- Contribution Progress --}}
        <div style="margin-top:20px;">
            <div style="display:flex;justify-content:space-between;font-size:13px;color:#94a3b8;margin-bottom:6px;">
                <span style="display:flex;align-items:center;gap:4px;"><span class="material-symbols-rounded" style="font-size:14px;">trending_up</span> Progress Kontribusi</span>
                <span>{{ number_format($contributionProgress, 0) }}%</span>
            </div>
            <div style="width:100%;background:rgba(255,255,255,0.1);border-radius:10px;height:8px;overflow:hidden;">
                <div style="width:{{ $contributionProgress }}%;background:linear-gradient(90deg,var(--primary),#8b5cf6);height:100%;border-radius:10px;transition:width 1s ease;"></div>
            </div>
            <p style="font-size:11px;color:#64748b;margin-top:4px;">Buat 20 laporan untuk mencapai 100%</p>
        </div>
    </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">
        {{-- Stats Grid --}}
        <div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="stat-card">
                    <div style="font-size:2rem;font-weight:800;color:var(--primary);">{{ $totalReports }}</div>
                    <div class="stat-label">Total Laporan</div>
                </div>
                <div class="stat-card">
                    <div style="font-size:2rem;font-weight:800;color:#22c55e;">{{ $completedReports }}</div>
                    <div class="stat-label">Selesai</div>
                </div>
                <div class="stat-card">
                    <div style="font-size:2rem;font-weight:800;color:#f59e0b;">{{ $pendingReports }}</div>
                    <div class="stat-label">Menunggu</div>
                </div>
                <div class="stat-card">
                    <div style="font-size:2rem;font-weight:800;color:#6366f1;">{{ $processReports }}</div>
                    <div class="stat-label">Diproses</div>
                </div>
                <div class="stat-card">
                    <div style="font-size:2rem;font-weight:800;color:#ec4899;">{{ $totalVotesReceived }}</div>
                    <div class="stat-label"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">thumb_up</span> Vote Diterima</div>
                </div>
                <div class="stat-card">
                    <div style="font-size:2rem;font-weight:800;color:#06b6d4;">{{ $totalComments }}</div>
                    <div class="stat-label"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">chat_bubble</span> Komentar</div>
                </div>
                <div class="stat-card">
                    <div style="font-size:2rem;font-weight:800;color:#fbbf24;">{{ number_format($avgRating, 1) }}</div>
                    <div class="stat-label"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">star</span> Rating Rata-rata</div>
                </div>
                <div class="stat-card" style="border-color:rgba(99,102,241,0.3);">
                    <div style="font-size:2rem;font-weight:800;color:#a5b4fc;">{{ $aiReports }}</div>
                    <div class="stat-label"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">smart_toy</span> AI Detected</div>
                </div>
            </div>
        </div>

        {{-- Monthly Chart --}}
        <div class="glass-card" style="padding:24px;">
            <h3 style="font-weight:700;margin-bottom:16px;font-size:1rem;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">trending_up</span> Laporan Bulanan</h3>
            <canvas id="profileChart" height="220"></canvas>
        </div>
    </div>

    {{-- Recent Reports --}}
    @if($recentReports->count() > 0)
    <div class="glass-card" style="padding:24px;margin-bottom:24px;">
        <h2 style="font-weight:700;margin-bottom:16px;font-size:1.1rem;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">description</span> Laporan Terakhir</h2>
        <table class="data-table">
            <thead>
                <tr><th>Laporan</th><th>Kategori</th><th>Status</th><th>Waktu</th></tr>
            </thead>
            <tbody>
                @foreach($recentReports as $report)
                <tr style="cursor:pointer;" onclick="window.location='{{ route('reports.show', $report) }}'">
                    <td>
                        <div style="font-weight:600;">{{ $report->title }}</div>
                        @if($report->ai_detected)
                            <span style="font-size:11px;color:#a5b4fc;display:flex;align-items:center;gap:3px;"><span class="material-symbols-rounded" style="font-size:12px;">smart_toy</span> AI Classified</span>
                        @endif
                    </td>
                    <td><span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;color:var(--accent);">{{ $report->category->icon }}</span> {{ $report->category->name }}</td>
                    <td><span class="badge badge-{{ $report->status_badge }}">{{ $report->status_label }}</span></td>
                    <td style="color:#94a3b8;font-size:13px;">{{ $report->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Edit Profile & Change Password --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">
        {{-- Edit Profile --}}
        <div class="glass-card" style="padding:32px;">
            <h2 style="font-weight:700;margin-bottom:20px;font-size:1.1rem;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">edit</span> Edit Profile</h2>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                    @error('name')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                    @error('email')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label class="form-label">Foto Profile</label>
                    <input type="file" name="avatar" accept="image/*" class="form-input" style="padding:10px;" onchange="previewAvatar(this)">
                    <p style="font-size:11px;color:#64748b;margin-top:4px;">JPG, PNG, GIF. Maks 2MB</p>
                </div>
                <button type="submit" class="btn-primary" style="width:100%;justify-content:center;gap:6px;"><span class="material-symbols-rounded" style="font-size:18px;">save</span> Simpan Perubahan</button>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="glass-card" style="padding:32px;">
            <h2 style="font-weight:700;margin-bottom:20px;font-size:1.1rem;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">lock</span> Ubah Password</h2>
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Password Lama</label>
                    <input type="password" name="current_password" class="form-input" required>
                    @error('current_password')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-input" required minlength="6">
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>
                <button type="submit" class="btn-secondary" style="width:100%;justify-content:center;gap:6px;"><span class="material-symbols-rounded" style="font-size:18px;">key</span> Ubah Password</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Avatar preview
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Monthly chart
document.addEventListener('DOMContentLoaded', function() {
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
    const data = @json($monthlyReports);
    const labels = data.map(d => months[d.month - 1]);
    const counts = data.map(d => d.count);

    new Chart(document.getElementById('profileChart'), {
        type: 'bar',
        data: {
            labels: labels.length ? labels : ['Belum ada'],
            datasets: [{
                label: 'Laporan',
                data: counts.length ? counts : [0],
                backgroundColor: 'rgba(99,102,241,0.6)',
                borderColor: '#6366f1',
                borderWidth: 1,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8', stepSize: 1 } },
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
            }
        }
    });
});
</script>
@endpush
@endsection
