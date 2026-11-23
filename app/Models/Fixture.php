<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Fixture extends Model
{
    /** Kode status API-Football. */
    public const LIVE = ['1H', 'HT', '2H', 'ET', 'BT', 'P', 'SUSP', 'INT', 'LIVE'];

    public const FINISHED = ['FT', 'AET', 'PEN'];

    public const UPCOMING = ['TBD', 'NS'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'kickoff_at' => 'datetime',
            'score' => 'array',
            'events' => 'array',
            'lineups' => 'array',
            'statistics' => 'array',
            'detail_synced_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function home(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function away(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function scopeLive(Builder $q): Builder
    {
        return $q->whereIn('status_short', self::LIVE);
    }

    /** Pertandingan pada tanggal lokal (zona waktu livescore). */
    public function scopeOnLocalDate(Builder $q, CarbonInterface $date): Builder
    {
        $tz = config('livescore.timezone');
        $start = Carbon::parse($date->toDateString(), $tz)->startOfDay()->utc();

        return $q->whereBetween('kickoff_at', [$start, $start->copy()->addDay()->subSecond()]);
    }

    public function localKickoff(): Carbon
    {
        return $this->kickoff_at->copy()->timezone(config('livescore.timezone'));
    }

    public function isLive(): bool
    {
        return in_array($this->status_short, self::LIVE, true);
    }

    public function isFinished(): bool
    {
        return in_array($this->status_short, self::FINISHED, true);
    }

    public function isUpcoming(): bool
    {
        return in_array($this->status_short, self::UPCOMING, true);
    }

    public function hasScore(): bool
    {
        return $this->goals_home !== null && $this->goals_away !== null;
    }

    /** Label singkat di kartu: menit, "HT", "FT", jam kickoff, dll. */
    public function statusLabel(): string
    {
        return match (true) {
            $this->status_short === 'HT' => 'HT',
            $this->status_short === 'BT' => 'Jeda',
            $this->status_short === 'P' => 'Adu penalti',
            $this->isLive() => ($this->elapsed ?? 0).($this->elapsed_extra ? '+'.$this->elapsed_extra : '')."'",
            $this->status_short === 'FT' => 'FT',
            $this->status_short === 'AET' => 'AET',
            $this->status_short === 'PEN' => 'Pen.',
            $this->status_short === 'PST' => 'Ditunda',
            $this->status_short === 'CANC' => 'Batal',
            $this->status_short === 'ABD' => 'Dihentikan',
            $this->status_short === 'TBD' => 'TBD',
            default => $this->localKickoff()->format('H:i'),
        };
    }

    public function title(): string
    {
        return $this->home->name.' vs '.$this->away->name;
    }

    /** @return array<string, mixed>|null  data penalti adu tendangan */
    public function penalty(): ?array
    {
        $p = $this->score['penalty'] ?? null;

        return ($p['home'] ?? null) !== null ? $p : null;
    }
}
