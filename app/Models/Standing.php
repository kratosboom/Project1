<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Standing extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['groups' => 'array', 'synced_at' => 'datetime'];
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }
}
