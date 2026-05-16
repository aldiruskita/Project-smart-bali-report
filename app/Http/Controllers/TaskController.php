<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskAttachment;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * 🧑‍🔧 Petugas Dashboard — list all assigned tasks
     */
    public function dashboard()
    {
        $user = Auth::user();

        $tasks = Task::where('assigned_to', $user->id)
            ->with(['report.category', 'report.media', 'attachments'])
            ->withCount('comments', 'attachments')
            ->latest()
            ->get();

        $stats = [
            'total' => $tasks->count(),
            'assigned' => $tasks->where('status', 'assigned')->count(),
            'accepted' => $tasks->where('status', 'accepted')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'done' => $tasks->where('status', 'done')->count(),
            'verified' => $tasks->where('status', 'verified')->count(),
            'overdue' => $tasks->filter(fn($t) => $t->isOverdue())->count(),
        ];

        return view('officer.dashboard', compact('tasks', 'stats'));
    }

    /**
     * 📋 Task detail page
     */
    public function show(Task $task)
    {
        $user = Auth::user();

        // Only the assigned officer or admin can view
        if ($task->assigned_to !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $task->load([
            'report.category', 'report.media', 'report.user', 'report.logs.updater',
            'officer', 'assigner', 'comments.user', 'attachments.uploader'
        ]);

        return view('officer.task-detail', compact('task'));
    }

    /**
     * ✅ Accept task
     */
    public function accept(Task $task)
    {
        $this->authorizeOfficer($task);
        $task->update(['status' => 'accepted', 'accepted_at' => now()]);
        $this->logTaskActivity($task, 'Tugas diterima oleh petugas');
        return back()->with('success', 'Tugas berhasil diterima! 💪');
    }

    /**
     * ❌ Reject task
     */
    public function reject(Request $request, Task $task)
    {
        $this->authorizeOfficer($task);
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        $task->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
        $this->logTaskActivity($task, 'Tugas ditolak: ' . $request->rejection_reason);
        return back()->with('success', 'Tugas ditolak.');
    }

    /**
     * 🔄 Update progress
     */
    public function updateProgress(Request $request, Task $task)
    {
        $this->authorizeOfficer($task);
        $request->validate([
            'progress' => 'required|integer|between:0,100',
            'status' => 'required|in:accepted,in_progress,done',
            'note' => 'nullable|string|max:500',
        ]);

        $data = ['progress' => $request->progress, 'status' => $request->status];
        if ($request->status === 'done') {
            $data['completed_at'] = now();
            $data['progress'] = 100;
        }

        $task->update($data);

        // Sync report status
        if ($request->status === 'done') {
            $this->reportService->updateStatus($task->report, 'done', Auth::user(), 'Petugas menyelesaikan tugas');
        } elseif ($request->status === 'in_progress' && $task->report->status !== 'process') {
            $this->reportService->updateStatus($task->report, 'process', Auth::user(), 'Petugas mulai mengerjakan');
        }

        $note = $request->note ?? 'Progress diupdate ke ' . $request->progress . '%';
        $this->logTaskActivity($task, $note);

        return back()->with('success', 'Progress berhasil diupdate! 📊');
    }

    /**
     * 💬 Add comment
     */
    public function addComment(Request $request, Task $task)
    {
        $request->validate(['message' => 'required|string|max:1000']);
        TaskComment::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);
        return back()->with('success', 'Komentar ditambahkan! 💬');
    }

    /**
     * 📎 Upload attachment
     */
    public function uploadAttachment(Request $request, Task $task)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4|max:10240',
            'type' => 'required|in:before,after,progress',
            'caption' => 'nullable|string|max:255',
        ]);

        $path = $request->file('file')->store('tasks/' . $task->id, 'public');

        TaskAttachment::create([
            'task_id' => $task->id,
            'uploaded_by' => Auth::id(),
            'file_path' => $path,
            'type' => $request->type,
            'caption' => $request->caption,
        ]);

        $this->logTaskActivity($task, 'Foto ' . $request->type . ' diupload');
        return back()->with('success', 'Bukti berhasil diupload! 📎');
    }

    /**
     * 👑 Admin: Verify completed task
     */
    public function verify(Task $task)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        $task->update(['status' => 'verified']);
        $this->reportService->updateStatus($task->report, 'done', Auth::user(), 'Tugas diverifikasi oleh admin');
        $this->logTaskActivity($task, 'Tugas diverifikasi oleh admin');
        return back()->with('success', 'Tugas terverifikasi! ✅');
    }

    // ─── Helpers ────────────────────────────────────
    private function authorizeOfficer(Task $task): void
    {
        if ($task->assigned_to !== Auth::id()) abort(403);
    }

    private function logTaskActivity(Task $task, string $note): void
    {
        Log::info('[Task] ' . $note, [
            'task_id' => $task->id,
            'report_id' => $task->report_id,
            'user' => Auth::user()->name,
        ]);
    }
}
