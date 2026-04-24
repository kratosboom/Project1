<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    protected $fillable = [
        'game_provider_id',
        'name',
        'image_url',
        'rtp',
        'is_hot',
        'is_best',
        'is_active',
        'jam_gacor',
        'pola',
        'modal_data',
        'maxwin_footer_text',
        'maxwin_difficulty_min',
        'maxwin_difficulty_max',
        'maxwin_multiplier',
        'click_count',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'rtp' => 'float',
            'is_hot' => 'boolean',
            'is_best' => 'boolean',
            'is_active' => 'boolean',
            'pola' => 'array',
            'modal_data' => 'array',
            'maxwin_difficulty_min' => 'integer',
            'maxwin_difficulty_max' => 'integer',
            'maxwin_multiplier' => 'integer',
            'click_count' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(GameProvider::class, 'game_provider_id');
    }
}
