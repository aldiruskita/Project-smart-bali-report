<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Report;
use App\Models\Vote;
use App\Models\Rating;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ─── Statistics ─────────────────────────────
        $totalReports = Report::where('user_id', $user->id)->count();
        $completedReports = Report::where('user_id', $user->id)->where('status', 'done')->count();
        $pendingReports = Report::where('user_id', $user->id)->where('status', 'pending')->count();
        $processReports = Report::where('user_id', $user->id)->where('status', 'process')->count();

        // Total votes received on user's reports
        $totalVotesReceived = Vote::whereIn('report_id',
            Report::where('user_id', $user->id)->pluck('id')
        )->count();

        // Total votes given by user
        $totalVotesGiven = Vote::where('user_id', $user->id)->count();

        // Total comments
        $totalComments = Comment::where('user_id', $user->id)->count();

        // Average rating on user's completed reports
        $avgRating = Rating::whereIn('report_id',
            Report::where('user_id', $user->id)->where('status', 'done')->pluck('id')
        )->avg('rating') ?? 0;

        // AI-detected reports
        $aiReports = Report::where('user_id', $user->id)->where('ai_detected', true)->count();

        // Monthly report trend (last 6 months)
        $monthlyReports = Report::where('user_id', $user->id)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Recent reports
        $recentReports = Report::where('user_id', $user->id)
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        // ─── Badges ─────────────────────────────────
        $badges = [];
        if ($totalReports >= 1) $badges[] = ['icon' => '📝', 'name' => 'Pelapor', 'color' => '#3b82f6'];
        if ($totalReports >= 5) $badges[] = ['icon' => '🧠', 'name' => 'Kontributor', 'color' => '#8b5cf6'];
        if ($totalReports >= 10) $badges[] = ['icon' => '🔥', 'name' => 'Top Reporter', 'color' => '#ef4444'];
        if ($completedReports >= 3) $badges[] = ['icon' => '✅', 'name' => 'Problem Solver', 'color' => '#22c55e'];
        if ($totalVotesGiven >= 5) $badges[] = ['icon' => '👍', 'name' => 'Voter Aktif', 'color' => '#f59e0b'];
        if ($totalComments >= 5) $badges[] = ['icon' => '💬', 'name' => 'Komunikator', 'color' => '#06b6d4'];
        if ($aiReports >= 1) $badges[] = ['icon' => '🤖', 'name' => 'AI User', 'color' => '#6366f1'];

        // Contribution progress (based on reports, max 20 for 100%)
        $contributionProgress = min(100, ($totalReports / 20) * 100);

        return view('profile.index', compact(
            'user', 'totalReports', 'completedReports', 'pendingReports', 'processReports',
            'totalVotesReceived', 'totalVotesGiven', 'totalComments', 'avgRating',
            'aiReports', 'monthlyReports', 'recentReports', 'badges', 'contributionProgress'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $user->update($request->only('name', 'email', 'phone'));

        // Upload avatar
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
        }

        return back()->with('success', 'Profile berhasil diperbarui! 🎉');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!password_verify($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah']);
        }

        $user->update(['password' => bcrypt($request->password)]);

        return back()->with('success', 'Password berhasil diubah! 🔐');
    }
}
