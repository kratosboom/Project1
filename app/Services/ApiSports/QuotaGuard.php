<?php

namespace App\Services\ApiSports;

use Illuminate\Support\Facades\Cache;

/**
 * Menyimpan sisa kuota harian API-Sports (dibaca dari header respons) dan
 * memutuskan apakah sebuah sinkronisasi boleh memakai request.
 * Kuota API-Sports reset tiap 00:00 UTC.
 */
class QuotaGuard
{
    private const KEY = 'livescore:quota';

    public function record(?int $remaining, ?int $limit): void
    {
        if ($remaining === null) {
            return;
        }

        Cache::forever(self::KEY, [
            'remaining' => $remaining,
            'limit' => $limit ?? (int) config('livescore.quota.daily_limit'),
            'day' => now('UTC')->toDateString(),
            'at' => now()->toIso8601String(),
        ]);
    }

    /** @return array{remaining:int,limit:int,day:string,at:string}|null */
    public function snapshot(): ?array
    {
        $s = Cache::get(self::KEY);

        if (! is_array($s) || ($s['day'] ?? null) !== now('UTC')->toDateString()) {
            return null; // belum ada data hari ini (kuota sudah reset)
        }

        return $s;
    }

    public function remaining(): ?int
    {
        return $this->snapshot()['remaining'] ?? null;
    }

    /**
     * $essential = true untuk sinkron skor hari ini: boleh memakai cadangan.
     */
    public function allows(bool $essential = false): bool
    {
        $remaining = $this->remaining();

        if ($remaining === null) {
            return true;
        }

        return $essential ? $remaining > 0 : $remaining > (int) config('livescore.quota.reserve');
    }
}
