<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'icon', 'color', 'urgency_level'];

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
