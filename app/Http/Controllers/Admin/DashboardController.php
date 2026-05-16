<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_reports' => Report::count(),
            'pending' => Report::where('status', 'pending')->count(),
            'verified' => Report::where('status', 'verified')->count(),
            'process' => Report::where('status', 'process')->count(),
            'done' => Report::where('status', 'done')->count(),
            'rejected' => Report::where('status', 'rejected')->count(),
            'total_users' => User::count(),
            'total_officers' => User::where('role', 'petugas')->count(),
            'ai_detected' => Report::where('ai_detected', true)->count(),
            'ai_avg_confidence' => Report::where('ai_detected', true)->avg('ai_confidence') ?? 0,
        ];

        // Reports per category
        $categoryStats = Category::withCount('reports')
            ->orderBy('reports_count', 'desc')
            ->get();

        // Monthly reports (last 12 months)
        $monthlyReports = Report::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Recent reports
        $recentReports = Report::with(['user', 'category'])
            ->latest()
            ->take(10)
            ->get();

        // Top areas (most reports)
        $topAreas = Report::whereNotNull('address')
            ->select('address', DB::raw('COUNT(*) as report_count'))
            ->groupBy('address')
            ->orderBy('report_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'categoryStats', 'monthlyReports', 'recentReports', 'topAreas'
        ));
    }

    /**
     * API endpoint for chart data.
     */
    public function chartData(Request $request)
    {
        $type = $request->input('type', 'monthly');

        if ($type === 'monthly') {
            $data = Report::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return response()->json($data);
        }

        if ($type === 'category') {
            $data = Category::withCount('reports')
                ->orderBy('reports_count', 'desc')
                ->get()
                ->map(fn($cat) => [
                    'name' => $cat->name,
                    'count' => $cat->reports_count,
                    'color' => $cat->color,
                ]);

            return response()->json($data);
        }

        if ($type === 'status') {
            $data = Report::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get();

            return response()->json($data);
        }

        return response()->json([]);
    }
}
