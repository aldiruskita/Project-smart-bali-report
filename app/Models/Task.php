<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'report_id', 'assigned_to', 'assigned_by',
        'title', 'description', 'status', 'priority',
        'deadline', 'progress', 'rejection_reason',
        'accepted_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'accepted_at' => 'datetime',
            'completed_at' => 'datetime',
            'progress' => 'integer',
        ];
    }

    // ─── Relationships ──────────────────────────────
    public function report(): BelongsTo { return $this->belongsTo(Report::class); }
    public function officer(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function assigner(): BelongsTo { return $this->belongsTo(User::class, 'assigned_by'); }
    public function comments(): HasMany { return $this->hasMany(TaskComment::class)->orderBy('created_at', 'desc'); }
    public function attachments(): HasMany { return $this->hasMany(TaskAttachment::class); }

    // ─── Helpers ────────────────────────────────────
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'assigned' => 'Ditugaskan',
            'accepted' => 'Diterima',
            'in_progress' => 'Dikerjakan',
            'done' => 'Selesai',
            'rejected' => 'Ditolak',
            'verified' => 'Terverifikasi',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'assigned' => 'warning',
            'accepted' => 'info',
            'in_progress' => 'primary',
            'done' => 'success',
            'rejected' => 'danger',
            'verified' => 'success',
            default => 'secondary',
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'low' => '🟢 Rendah',
            'medium' => '🟡 Sedang',
            'high' => '🟠 Tinggi',
            'urgent' => '🔴 Darurat',
            default => '⚪ -',
        };
    }

    public function isOverdue(): bool
    {
        return $this->deadline && $this->deadline->isPast() && !in_array($this->status, ['done', 'verified']);
    }
}
