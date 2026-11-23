<?php

namespace App\Http\Controllers\Livescore;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Standing;
use Illuminate\View\View;

class LeagueController extends Controller
{
    public function show(League $league): View
    {
        $base = Fixture::query()->with(['home', 'away', 'league'])->where('league_id', $league->id);

        return view('livescore.league', [
            'league' => $league,
            'standing' => Standing::query()->where('league_id', $league->id)->where('season', $league->season)->first(),
            'live' => (clone $base)->live()->orderBy('kickoff_at')->get(),
            'results' => (clone $base)->whereIn('status_short', Fixture::FINISHED)->orderByDesc('kickoff_at')->limit(12)->get(),
            'upcoming' => (clone $base)->whereIn('status_short', Fixture::UPCOMING)->where('kickoff_at', '>=', now()->subHours(3))->orderBy('kickoff_at')->limit(12)->get(),
        ]);
    }
}
