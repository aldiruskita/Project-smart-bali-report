@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('admin-content')
<h1 style="font-family:'Noto Serif',serif;font-size:1.5rem;font-weight:700;margin-bottom:24px;color:var(--primary);display:flex;align-items:center;gap:12px;"><span class="material-symbols-rounded" style="font-size:28px;color:var(--accent);">dashboard</span> Dashboard</h1>

{{-- Stats Grid --}}
<div class="grid-4" style="margin-bottom:32px;">
    <div class="stat-card">
        <div class="stat-number">{{ $stats['total_reports'] }}</div>
        <div class="stat-label">Total Laporan</div>
    </div>
    <div class="stat-card">
        <div style="font-size:2rem;font-weight:800;color:#f59e0b;">{{ $stats['pending'] }}</div>
        <div class="stat-label">Menunggu Verifikasi</div>
    </div>
    <div class="stat-card">
        <div style="font-size:2rem;font-weight:800;color:#6366f1;">{{ $stats['process'] }}</div>
        <div class="stat-label">Sedang Diproses</div>
    </div>
    <div class="stat-card">
        <div style="font-size:2rem;font-weight:800;color:#22c55e;">{{ $stats['done'] }}</div>
        <div class="stat-label">Selesai</div>
    </div>
</div>

<div class="grid-4" style="margin-bottom:32px;">
    <div class="stat-card">
        <div style="font-size:2rem;font-weight:800;color:#3b82f6;">{{ $stats['total_users'] }}</div>
        <div class="stat-label">Total User</div>
    </div>
    <div class="stat-card">
        <div style="font-size:2rem;font-weight:800;color:#8b5cf6;">{{ $stats['total_officers'] }}</div>
        <div class="stat-label">Total Petugas</div>
    </div>
    <div class="stat-card" style="border-color:rgba(99,102,241,0.3);">
        <div style="font-size:2rem;font-weight:800;color:#6366f1;">{{ $stats['ai_detected'] }}</div>
        <div class="stat-label"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">smart_toy</span> AI Detected</div>
    </div>
    <div class="stat-card" style="border-color:rgba(99,102,241,0.3);">
        <div style="font-size:2rem;font-weight:800;color:#a5b4fc;">{{ number_format($stats['ai_avg_confidence'], 1) }}%</div>
        <div class="stat-label"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">track_changes</span> AI Confidence</div>
    </div>
</div>

{{-- Charts --}}
<div class="grid-2" style="margin-bottom:32px;">
    <div class="glass-card" style="padding:24px;">
        <h3 style="font-weight:700;margin-bottom:16px;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">trending_up</span> Laporan Bulanan</h3>
        <canvas id="monthlyChart" height="200"></canvas>
    </div>
    <div class="glass-card" style="padding:24px;">
        <h3 style="font-weight:700;margin-bottom:16px;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">folder</span> Per Kategori</h3>
        <canvas id="categoryChart" height="200"></canvas>
    </div>
</div>

{{-- Category Breakdown --}}
<div class="glass-card" style="padding:24px;margin-bottom:32px;">
    <h3 style="font-weight:700;margin-bottom:16px;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">pie_chart</span> Kategori Laporan</h3>
    @foreach($categoryStats as $cat)
    <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
        <span class="material-symbols-rounded" style="font-size:20px;width:32px;text-align:center;color:{{ $cat->color }};">{{ $cat->icon }}</span>
        <span style="flex:1;font-weight:500;">{{ $cat->name }}</span>
        <div style="width:200px;background:rgba(255,255,255,0.1);border-radius:10px;height:8px;overflow:hidden;">
            <div style="width:{{ $stats['total_reports'] > 0 ? ($cat->reports_count / $stats['total_reports'] * 100) : 0 }}%;background:{{ $cat->color }};height:100%;border-radius:10px;transition:width 1s ease;"></div>
        </div>
        <span style="font-weight:700;width:40px;text-align:right;">{{ $cat->reports_count }}</span>
    </div>
    @endforeach
</div>

{{-- Recent Reports --}}
<div class="glass-card" style="padding:24px;">
    <h3 style="font-weight:700;margin-bottom:16px;color:var(--primary);display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">description</span> Laporan Terbaru</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Laporan</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentReports as $report)
            <tr style="cursor:pointer;" onclick="window.location='{{ route('reports.show', $report) }}'">
                <td>
                    <div style="font-weight:600;">{{ $report->title }}</div>
                    <div style="font-size:12px;color:#64748b;">{{ $report->reporter_name }}</div>
                </td>
                <td><span class="material-symbols-rounded" style="font-size:16px;color:{{ $report->category->color ?? 'var(--accent)' }};vertical-align:middle;">{{ $report->category->icon }}</span> {{ $report->category->name }}</td>
                <td><span class="badge badge-{{ $report->status_badge }}">{{ $report->status_label }}</span></td>
                <td style="color:#94a3b8;font-size:13px;">{{ $report->created_at->diffForHumans() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly chart
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
    const monthlyData = @json($monthlyReports);
    const monthlyLabels = monthlyData.map(d => months[d.month - 1] + ' ' + d.year);
    const monthlyCounts = monthlyData.map(d => d.count);

    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthlyLabels.length ? monthlyLabels : ['Tidak ada data'],
            datasets: [{
                label: 'Laporan',
                data: monthlyCounts.length ? monthlyCounts : [0],
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#6366f1',
                pointRadius: 5,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
            }
        }
    });

    // Category chart
    const catData = @json($categoryStats);
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: catData.map(c => c.name),
            datasets: [{
                data: catData.map(c => c.reports_count),
                backgroundColor: catData.map(c => c.color),
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#94a3b8', padding: 16, usePointStyle: true } }
            }
        }
    });
});
</script>
@endpush
@endsection
