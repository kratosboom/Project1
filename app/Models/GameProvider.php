<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameProvider extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo_url',
        'website_url',
        'icon_class',
        'is_hot_games',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_hot_games' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    public function navLabel(): string
    {
        if ($this->is_hot_games) {
            return 'HOT GAMES';
        }

        return strtoupper($this->name);
    }
}
