<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Services\Livescore\FootballSync;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class HomeController extends Controller
{
    /** Beranda: skor live + pertandingan hari ini. */
    public function index(): View
    {
        return $this->day(Carbon::now(config('livescore.timezone'))->startOfDay(), true);
    }

    /** /jadwal/{date}: jadwal & hasil pada tanggal tertentu. */
    public function date(string $date): View|RedirectResponse
    {
        $tz = config('livescore.timezone');
        [$y, $m, $d] = array_map('intval', explode('-', $date));
        abort_unless(checkdate($m, $d, $y), 404);
        $day = Carbon::createFromFormat('Y-m-d', $date, $tz)->startOfDay();

        if ($day->isSameDay(Carbon::now($tz))) {
            return redirect()->route('home', status: 301);
        }

        return $this->day($day, false);
    }

    private function day(Carbon $day, bool $isToday): View
    {
        $fixtures = Fixture::query()
            ->with(['league', 'home', 'away'])
            ->whereHas('league', fn ($q) => $q->whereIn('api_id', config('livescore.leagues', [])))
            ->onLocalDate($day)
            ->orderBy('kickoff_at')
            ->get();

        $groups = $fixtures
            ->groupBy('league_id')
            ->sortBy(fn ($items) => [$items->first()->league->sort_order, $items->first()->league->name]);

        $live = $isToday ? $fixtures->filter->isLive()->values() : collect();

        return view('livescore.day', [
            'day' => $day,
            'isToday' => $isToday,
            'groups' => $groups,
            'live' => $live,
            'total' => $fixtures->count(),
            'finished' => $fixtures->filter->isFinished()->count(),
            'lastSynced' => FootballSync::lastSyncedAt(),
        ]);
    }
}
