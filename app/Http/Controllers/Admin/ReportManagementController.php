<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use App\Models\Assignment;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportManagementController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $query = Report::with(['user', 'category', 'assignment.officer', 'task'])->withCount('votes', 'comments');

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('category')) $query->where('category_id', $request->category);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                  ->orWhere('description', 'like', '%'.$request->search.'%')
                  ->orWhere('reporter_name', 'like', '%'.$request->search.'%')
                  ->orWhere('address', 'like', '%'.$request->search.'%');
            });
        }

        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'oldest' => $query->oldest(),
            'priority' => $query->orderBy('priority_score', 'desc'),
            default => $query->latest(),
        };

        $reports = $query->paginate(15);
        $officers = User::where('role', 'petugas')->get();
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('admin.reports', compact('reports', 'officers', 'categories'));
    }

    public function updateStatus(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,verified,process,done,rejected',
            'note' => 'nullable|string|max:500',
        ]);

        $this->reportService->updateStatus($report, $validated['status'], Auth::user(), $validated['note'] ?? null);
        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function assign(Request $request, Report $report)
    {
        $validated = $request->validate([
            'officer_id' => 'required|exists:users,id',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'deadline' => 'nullable|date|after:today',
            'task_description' => 'nullable|string|max:1000',
        ]);
        $officer = User::findOrFail($validated['officer_id']);

        // Create/update assignment
        Assignment::updateOrCreate(
            ['report_id' => $report->id],
            ['officer_id' => $validated['officer_id'], 'assigned_by' => Auth::id(), 'assigned_at' => now()]
        );

        // Create task
        \App\Models\Task::updateOrCreate(
            ['report_id' => $report->id],
            [
                'assigned_to' => $validated['officer_id'],
                'assigned_by' => Auth::id(),
                'title' => $report->title,
                'description' => $validated['task_description'] ?? $report->description,
                'status' => 'assigned',
                'priority' => $validated['priority'] ?? 'medium',
                'deadline' => $validated['deadline'] ?? null,
                'progress' => 0,
            ]
        );

        if (in_array($report->status, ['pending', 'verified'])) {
            $this->reportService->updateStatus($report, 'process', Auth::user(), 'Ditugaskan ke petugas: '.$officer->name);
        }
        return back()->with('success', 'Tugas berhasil diberikan ke ' . $officer->name . '! 🧑‍🔧');
    }

    public function destroy(Request $request, Report $report)
    {
        try {
            // Delete task and its related data first
            if ($report->task) {
                $report->task->comments()->delete();
                $report->task->attachments()->each(function ($att) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($att->file_path);
                    $att->delete();
                });
                $report->task->delete();
            }

            // Delete assignment
            $report->assignment()->delete();

            // Delete media files from storage
            foreach ($report->media as $media) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($media->file_path);
            }
            $report->media()->delete();

            // Delete other related data
            $report->logs()->delete();
            $report->comments()->delete();
            $report->votes()->delete();
            $report->ratings()->delete();

            // Now delete the report
            $report->delete();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Laporan berhasil dihapus.']);
            }
            return back()->with('success', 'Laporan berhasil dihapus.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal menghapus laporan: ' . $e->getMessage());
        }
    }
}
