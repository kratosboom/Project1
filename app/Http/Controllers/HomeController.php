<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameProvider;
use App\Support\GameProviderBrandMatcher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $allProviders = GameProvider::query()
            ->withCount('games')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $providers = GameProviderBrandMatcher::dedupeForNavigation($allProviders);

        $requested = $request->query('provider');
        $requestedStr = is_string($requested) ? $requested : null;

        $slugForResolve = $requestedStr;
        if ($slugForResolve !== null && $slugForResolve !== '' && $slugForResolve !== 'all') {
            $aliases = config('game_providers.slug_aliases', []);
            if (isset($aliases[$slugForResolve])) {
                $slugForResolve = (string) $aliases[$slugForResolve];
            }
        }

        $gamesShowAll = $requestedStr === null || $requestedStr === '' || $requestedStr === 'all';

        $activeProvider = null;
        if (! $gamesShowAll && $slugForResolve !== null && $slugForResolve !== '' && $allProviders->isNotEmpty()) {
            $resolved = GameProviderBrandMatcher::resolveFromUrlSlug($allProviders, $slugForResolve);
            if ($resolved === null) {
                $gamesShowAll = true;
            } else {
                if ((string) $resolved->slug !== (string) $requestedStr) {
                    return redirect()->route('home', array_merge(
                        $request->query(),
                        ['provider' => $resolved->slug],
                    ));
                }
                $activeProvider = $resolved;
            }
        }

        $gamesQuery = Game::query()
            ->with('provider')
            ->orderBy('sort_order')
            ->orderBy('id');

        if (! $gamesShowAll && $activeProvider !== null) {
            if ($activeProvider->is_hot_games) {
                $gamesQuery->where(function (Builder $q) use ($activeProvider) {
                    $q->where('is_hot', true)
                        ->orWhere('game_provider_id', $activeProvider->id);
                });
            } else {
                $gamesQuery->where('game_provider_id', $activeProvider->id);
            }
        }

        // 6 baris × 6 kolom (breakpoint xl) = 36 game per halaman
        $games = $gamesQuery->paginate(36)->withQueryString();

        return view('home', compact('providers', 'games', 'activeProvider', 'gamesShowAll'));
    }
}
