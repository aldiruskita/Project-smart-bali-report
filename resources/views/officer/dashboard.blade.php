@extends('layouts.app')
@section('title', 'Dashboard Petugas')
@section('content')
<div class="container">
    <h1 style="font-family:'Noto Serif',serif;font-size:1.75rem;font-weight:700;margin-bottom:24px;color:var(--primary);display:flex;align-items:center;gap:12px;"><span class="material-symbols-rounded" style="font-size:28px;color:var(--accent);">engineering</span> Dashboard Petugas</h1>

    {{-- Stats --}}
    <div class="grid-4" style="margin-bottom:24px;">
        <div class="stat-card">
            <div class="stat-number">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Tugas</div>
        </div>
        <div class="stat-card">
            <div style="font-size:2rem;font-weight:800;color:#f59e0b;">{{ $stats['assigned'] + $stats['accepted'] }}</div>
            <div class="stat-label"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">schedule</span> Menunggu</div>
        </div>
        <div class="stat-card">
            <div style="font-size:2rem;font-weight:800;color:#6366f1;">{{ $stats['in_progress'] }}</div>
            <div class="stat-label"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">sync</span> Dikerjakan</div>
        </div>
        <div class="stat-card">
            <div style="font-size:2rem;font-weight:800;color:#22c55e;">{{ $stats['done'] + $stats['verified'] }}</div>
            <div class="stat-label"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">check_circle</span> Selesai</div>
        </div>
    </div>

    @if($stats['overdue'] > 0)
    <div class="alert alert-error" style="margin-bottom:24px;display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:18px;">warning</span> Kamu punya {{ $stats['overdue'] }} tugas yang melewati deadline!</div>
    @endif

    {{-- Filter Tabs --}}
    <div class="glass-card" style="padding:16px;margin-bottom:24px;display:flex;gap:8px;flex-wrap:wrap;">
        <button class="btn-sm filter-tab active" data-filter="all" onclick="filterTasks('all', this)"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">list</span> Semua ({{ $stats['total'] }})</button>
        <button class="btn-sm filter-tab" data-filter="assigned" onclick="filterTasks('assigned', this)"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">fiber_new</span> Baru ({{ $stats['assigned'] }})</button>
        <button class="btn-sm filter-tab" data-filter="in_progress" onclick="filterTasks('in_progress', this)"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">sync</span> Dikerjakan ({{ $stats['in_progress'] }})</button>
        <button class="btn-sm filter-tab" data-filter="done" onclick="filterTasks('done', this)"><span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">check_circle</span> Selesai ({{ $stats['done'] }})</button>
    </div>

    {{-- Task List --}}
    @if($tasks->count() > 0)
    <div id="taskList">
        @foreach($tasks as $task)
        <div class="glass-card task-item" data-status="{{ $task->status }}" style="padding:24px;margin-bottom:16px;{{ $task->isOverdue() ? 'border-color:rgba(239,68,68,0.5);' : '' }}">
            <div style="display:flex;justify-content:space-between;align-items:start;gap:16px;flex-wrap:wrap;">
                <div style="flex:1;min-width:200px;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap;">
                        <span class="material-symbols-rounded" style="font-size:20px;color:var(--accent);">{{ $task->report->category->icon ?? 'description' }}</span>
                        <span class="badge badge-{{ $task->status_badge }}">{{ $task->status_label }}</span>
                        <span style="font-size:12px;">{{ $task->priority_badge }}</span>
                        @if($task->isOverdue())
                            <span class="badge" style="background:rgba(239,68,68,0.2);color:#f87171;display:flex;align-items:center;gap:4px;"><span class="material-symbols-rounded" style="font-size:14px;">alarm</span> Overdue</span>
                        @endif
                    </div>
                    <h3 style="font-weight:700;font-size:1.1rem;margin-bottom:6px;">
                        <a href="{{ route('tasks.show', $task) }}" style="color:inherit;text-decoration:none;">{{ $task->title }}</a>
                    </h3>
                    <p style="color:#94a3b8;font-size:13px;margin-bottom:8px;">{{ Str::limit($task->description, 120) }}</p>
                    <div style="display:flex;gap:12px;font-size:12px;color:#64748b;flex-wrap:wrap;">
                        @if($task->deadline)
                            <span style="display:flex;align-items:center;gap:4px;"><span class="material-symbols-rounded" style="font-size:14px;">event</span> Deadline: {{ $task->deadline->format('d M Y') }}</span>
                        @endif
                        <span style="display:flex;align-items:center;gap:4px;"><span class="material-symbols-rounded" style="font-size:14px;">chat_bubble</span> {{ $task->comments_count }} komentar</span>
                        <span style="display:flex;align-items:center;gap:4px;"><span class="material-symbols-rounded" style="font-size:14px;">attach_file</span> {{ $task->attachments_count }} bukti</span>
                        <span style="display:flex;align-items:center;gap:4px;"><span class="material-symbols-rounded" style="font-size:14px;">schedule</span> {{ $task->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;align-items:end;gap:8px;min-width:160px;">
                    {{-- Progress Bar --}}
                    <div style="width:100%;">
                        <div style="display:flex;justify-content:space-between;font-size:12px;color:#94a3b8;margin-bottom:4px;">
                            <span>Progress</span>
                            <span>{{ $task->progress }}%</span>
                        </div>
                        <div style="width:100%;background:rgba(255,255,255,0.1);border-radius:10px;height:8px;overflow:hidden;">
                            <div style="width:{{ $task->progress }}%;background:{{ $task->progress >= 100 ? '#22c55e' : ($task->progress >= 50 ? '#6366f1' : '#f59e0b') }};height:100%;border-radius:10px;transition:width 0.5s;"></div>
                        </div>
                    </div>

                    <a href="{{ route('tasks.show', $task) }}" class="btn-primary btn-sm" style="width:100%;justify-content:center;gap:6px;"><span class="material-symbols-rounded" style="font-size:16px;">visibility</span> Detail</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="glass-card" style="padding:80px 40px;text-align:center;">
        <span class="material-symbols-rounded" style="font-size:56px;color:var(--text-muted);opacity:0.3;display:block;margin-bottom:16px;">inbox</span>
        <p style="color:var(--text-muted);">Belum ada tugas yang diberikan.</p>
    </div>
    @endif
</div>

@push('scripts')
<script>
function filterTasks(status, btn) {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.task-item').forEach(item => {
        if (status === 'all') { item.style.display = ''; }
        else {
            const s = item.dataset.status;
            item.style.display = (status === 'in_progress' && (s === 'in_progress' || s === 'accepted')) ||
                                 (status === 'done' && (s === 'done' || s === 'verified')) ||
                                 s === status ? '' : 'none';
        }
    });
}
</script>
@endpush
@endsection
