<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportLog extends Model
{
    protected $fillable = ['report_id', 'status', 'updated_by', 'note'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'verified' => 'Terverifikasi',
            'process' => 'Diproses',
            'done' => 'Selesai',
            'rejected' => 'Ditolak',
            default => $this->status,
        };
    }
}
