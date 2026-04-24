<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Game;
use App\Services\ClickTrackerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameClickController extends Controller
{
    public function __invoke(Request $request, Game $game, ClickTrackerService $tracker): JsonResponse
    {
        $tracker->record($game);

        return response()->json([
            'ok' => true,
            'game_id' => $game->id,
            'click_count' => $game->fresh()->click_count,
        ]);
    }
}
