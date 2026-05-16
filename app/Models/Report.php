<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'latitude',
        'longitude',
        'address',
        'status',
        'priority_score',
        'is_anonymous',
        'ai_category',
        'ai_confidence',
        'ai_detected',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_anonymous' => 'boolean',
            'priority_score' => 'integer',
            'ai_confidence' => 'decimal:2',
            'ai_detected' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ReportMedia::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ReportLog::class)->orderBy('created_at', 'desc');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class)->latest();
    }

    public function task(): HasOne
    {
        return $this->hasOne(Task::class)->latest();
    }

    // ─── Scopes ─────────────────────────────────────
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeNearby($query, float $lat, float $lng, float $radiusKm = 1)
    {
        $haversine = "(6371 * acos(cos(radians($lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians($lng)) + sin(radians($lat)) * sin(radians(latitude))))";
        return $query->selectRaw("*, $haversine AS distance")
                     ->havingRaw("distance < ?", [$radiusKm])
                     ->orderBy('distance');
    }

    // ─── Helpers ────────────────────────────────────
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'verified' => 'info',
            'process' => 'primary',
            'done' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'verified' => 'Terverifikasi',
            'process' => 'Diproses',
            'done' => 'Selesai',
            'rejected' => 'Ditolak',
            default => 'Unknown',
        };
    }

    public function getReporterNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Anonim';
        }
        return $this->user->name ?? 'Unknown';
    }

    public function calculatePriority(): int
    {
        $voteScore = $this->votes()->count() * 2;
        $urgencyScore = $this->category->urgency_level ?? 1;

        // Count similar reports nearby (same category within 500m)
        $similarCount = 0;
        if ($this->latitude && $this->longitude) {
            $similarCount = Report::where('category_id', $this->category_id)
                ->where('id', '!=', $this->id)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get()
                ->filter(function ($report) {
                    $distance = $this->haversineDistance(
                        $this->latitude, $this->longitude,
                        $report->latitude, $report->longitude
                    );
                    return $distance < 0.5; // 500 meters
                })
                ->count();
        }

        return $voteScore + $similarCount + $urgencyScore;
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

    public function hasVotedBy(?int $userId): bool
    {
        if (!$userId) return false;
        return $this->votes()->where('user_id', $userId)->exists();
    }
}
