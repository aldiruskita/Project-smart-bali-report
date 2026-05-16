<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Category;
use App\Models\ReportMedia;
use App\Models\ReportLog;
use App\Services\ReportService;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    protected ReportService $reportService;
    protected AIService $aiService;

    public function __construct(ReportService $reportService, AIService $aiService)
    {
        $this->reportService = $reportService;
        $this->aiService = $aiService;
    }

    /**
     * Display a listing of reports.
     */
    public function index(Request $request)
    {
        $query = Report::with(['user', 'category', 'media', 'votes'])
            ->withCount('votes', 'comments');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Sort
        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'oldest' => $query->oldest(),
            'priority' => $query->orderBy('priority_score', 'desc'),
            'votes' => $query->orderBy('votes_count', 'desc'),
            default => $query->latest(),
        };

        $reports = $query->paginate(12);
        $categories = Category::all();

        return view('reports.index', compact('reports', 'categories'));
    }

    /**
     * Show the form for creating a new report.
     */
    public function create()
    {
        $categories = Category::all();
        return view('reports.create', compact('categories'));
    }

    /**
     * Store a newly created report.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'category_id' => 'nullable|exists:categories,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
            'is_anonymous' => 'nullable|boolean',
            'auto_detect' => 'nullable|boolean',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov|max:10240',
        ]);

        $aiData = ['ai_category' => null, 'ai_confidence' => null, 'ai_detected' => false];

        // 🤖 AI Smart Classification
        if ($request->boolean('auto_detect') || empty($validated['category_id'])) {
            $fullText = $validated['title'] . ' ' . $validated['description'];
            $aiResult = $this->aiService->smartClassify($fullText);

            if ($aiResult['category']) {
                $categoryId = $this->aiService->resolveCategoryId($aiResult['category']);
                if ($categoryId) {
                    $validated['category_id'] = $categoryId;
                    $aiData = [
                        'ai_category' => $aiResult['category'],
                        'ai_confidence' => $aiResult['confidence'],
                        'ai_detected' => true,
                    ];

                    Log::info('[AI] Auto-classified report', [
                        'title' => $validated['title'],
                        'detected' => $aiResult['category'],
                        'confidence' => $aiResult['confidence'],
                        'method' => $aiResult['method'],
                    ]);
                }
            }
        }

        // Fallback: if still no category, default to 'Lainnya'
        if (empty($validated['category_id'])) {
            $lainnya = Category::where('name', 'Lainnya')->first();
            $validated['category_id'] = $lainnya?->id ?? Category::first()->id;
        }

        $validated = array_merge($validated, $aiData);
        $report = $this->reportService->createReport($validated, Auth::user());

        $successMsg = 'Laporan berhasil dikirim!';
        if ($aiData['ai_detected']) {
            $successMsg .= ' 🤖 AI mendeteksi kategori: ' . $aiData['ai_category'] . ' (' . $aiData['ai_confidence'] . '% confidence)';
        }

        return redirect()->route('reports.show', $report)
            ->with('success', $successMsg);
    }

    /**
     * Display the specified report.
     */
    public function show(Report $report)
    {
        $report->load(['user', 'category', 'media', 'logs.updater', 'comments.user', 'votes', 'ratings', 'assignment.officer', 'task.officer', 'task.attachments']);

        return view('reports.show', compact('report'));
    }

    /**
     * Show current user's reports.
     */
    public function myReports()
    {
        $reports = Report::where('user_id', Auth::id())
            ->with(['category', 'media', 'task.officer'])
            ->withCount('votes', 'comments')
            ->latest()
            ->paginate(12);

        return view('reports.my-reports', compact('reports'));
    }

    /**
     * Get reports as JSON for map.
     */
    public function mapData(Request $request)
    {
        $query = Report::with(['category', 'user'])
            ->withCount('votes')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $reports = $query->get()->map(function ($report) {
            return [
                'id' => $report->id,
                'title' => $report->title,
                'description' => substr($report->description, 0, 100),
                'latitude' => (float) $report->latitude,
                'longitude' => (float) $report->longitude,
                'status' => $report->status,
                'status_label' => $report->status_label,
                'category' => $report->category->name,
                'category_icon' => $report->category->icon,
                'category_color' => $report->category->color,
                'votes_count' => $report->votes_count,
                'reporter' => $report->reporter_name,
                'created_at' => $report->created_at->diffForHumans(),
                'url' => route('reports.show', $report),
            ];
        });

        return response()->json($reports);
    }

    /**
     * AI Classification preview (AJAX endpoint).
     */
    public function classifyPreview(Request $request)
    {
        $request->validate(['text' => 'required|string|min:5']);
        $result = $this->aiService->classifyForPreview($request->text);
        return response()->json($result);
    }
}
