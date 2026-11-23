<?php

namespace App\Http\Controllers\Livescore;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect([['loc' => route('home'), 'lastmod' => now(), 'freq' => 'always', 'priority' => '1.0']]);

        foreach (League::query()->whereIn('api_id', config('livescore.leagues', []))->get() as $l) {
            $urls->push(['loc' => route('liga.show', $l), 'lastmod' => $l->updated_at, 'freq' => 'daily', 'priority' => '0.8']);
        }

        foreach (Fixture::query()->where('kickoff_at', '>=', now()->subDays(60))->orderByDesc('kickoff_at')->limit(5000)->get(['slug', 'updated_at']) as $f) {
            $urls->push(['loc' => route('pertandingan.show', $f), 'lastmod' => $f->updated_at, 'freq' => 'hourly', 'priority' => '0.6']);
        }

        foreach (Page::query()->where('is_published', true)->get(['slug', 'updated_at']) as $p) {
            $urls->push(['loc' => route('halaman.show', $p), 'lastmod' => $p->updated_at, 'freq' => 'weekly', 'priority' => '0.5']);
        }

        return response()->view('livescore.sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        return response("User-agent: *\nDisallow: /admin\nDisallow: /login\n\nSitemap: ".route('sitemap')."\n")
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
