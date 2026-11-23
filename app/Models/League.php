<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class League extends Model
{
    protected $guarded = ['id'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    public function standing(): HasOne
    {
        return $this->hasOne(Standing::class)->where('season', $this->season);
    }

    /** Nama tampilan berbahasa Indonesia (config livescore.league_names), fallback nama API. */
    public function displayName(): string
    {
        return config('livescore.league_names.'.$this->api_id, $this->name);
    }
}
