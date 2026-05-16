<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function toggle(Report $report)
    {
        $user = Auth::user();
        $existing = Vote::where('report_id', $report->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $voted = false;
        } else {
            Vote::create([
                'report_id' => $report->id,
                'user_id' => $user->id,
            ]);
            $voted = true;
        }

        // Recalculate priority
        $report->priority_score = $report->calculatePriority();
        $report->save();

        return response()->json([
            'voted' => $voted,
            'count' => $report->votes()->count(),
        ]);
    }
}
