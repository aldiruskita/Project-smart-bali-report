<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportLog;
use App\Models\ReportMedia;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    /**
     * Create a new report with media and initial log.
     */
    public function createReport(array $data, User $user): Report
    {
        return DB::transaction(function () use ($data, $user) {
            // Create the report
            $report = Report::create([
                'user_id' => $user->id,
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'address' => $data['address'] ?? null,
                'is_anonymous' => $data['is_anonymous'] ?? false,
                'status' => 'pending',
                'priority_score' => 0,
                'ai_category' => $data['ai_category'] ?? null,
                'ai_confidence' => $data['ai_confidence'] ?? null,
                'ai_detected' => $data['ai_detected'] ?? false,
            ]);

            // Upload media files
            if (isset($data['media'])) {
                foreach (request()->file('media', []) as $file) {
                    $path = $file->store('reports/' . $report->id, 'public');
                    $type = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';

                    ReportMedia::create([
                        'report_id' => $report->id,
                        'file_path' => $path,
                        'type' => $type,
                    ]);
                }
            }

            // Create initial log
            ReportLog::create([
                'report_id' => $report->id,
                'status' => 'pending',
                'updated_by' => $user->id,
                'note' => 'Laporan dibuat',
            ]);

            // Calculate initial priority
            $report->priority_score = $report->calculatePriority();
            $report->save();

            return $report;
        });
    }

    /**
     * Update report status with log entry.
     */
    public function updateStatus(Report $report, string $status, User $updatedBy, ?string $note = null): Report
    {
        return DB::transaction(function () use ($report, $status, $updatedBy, $note) {
            $report->update(['status' => $status]);

            ReportLog::create([
                'report_id' => $report->id,
                'status' => $status,
                'updated_by' => $updatedBy->id,
                'note' => $note ?? 'Status diubah ke ' . $status,
            ]);

            // Recalculate priority
            $report->priority_score = $report->calculatePriority();
            $report->save();

            return $report;
        });
    }

    /**
     * Check for duplicate reports (same category within 500m radius).
     */
    public function findDuplicates(float $lat, float $lng, int $categoryId): \Illuminate\Support\Collection
    {
        return Report::where('category_id', $categoryId)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('status', '!=', 'done')
            ->where('status', '!=', 'rejected')
            ->get()
            ->filter(function ($report) use ($lat, $lng) {
                $distance = $this->haversineDistance($lat, $lng, $report->latitude, $report->longitude);
                return $distance < 0.5; // 500 meters
            });
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
