<?php

namespace App\Http\Controllers\Livescore;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function show(Fixture $fixture): View
    {
        $fixture->load(['league', 'home', 'away']);

        $headToHead = Fixture::query()
            ->with(['home', 'away', 'league'])
            ->whereKeyNot($fixture->id)
            ->where(fn ($q) => $q
                ->where(fn ($q) => $q->where('home_team_id', $fixture->home_team_id)->where('away_team_id', $fixture->away_team_id))
                ->orWhere(fn ($q) => $q->where('home_team_id', $fixture->away_team_id)->where('away_team_id', $fixture->home_team_id)))
            ->orderByDesc('kickoff_at')
            ->limit(5)
            ->get();

        return view('livescore.match', compact('fixture', 'headToHead'));
    }
}
