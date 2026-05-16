<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request, Report $report)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'feedback' => 'nullable|string|max:500',
        ]);

        // Only allow rating on done reports
        if ($report->status !== 'done') {
            return back()->with('error', 'Hanya laporan selesai yang bisa diberi rating.');
        }

        // Check if already rated
        $existing = Rating::where('report_id', $report->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->update($validated);
        } else {
            Rating::create([
                'report_id' => $report->id,
                'user_id' => Auth::id(),
                ...$validated,
            ]);
        }

        return back()->with('success', 'Rating berhasil diberikan.');
    }
}
