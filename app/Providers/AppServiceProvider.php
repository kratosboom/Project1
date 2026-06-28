<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Setting;
use App\Support\ThemePalette;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(
            [
                'layouts.tokyo99',
                'layouts.admin',
                'layouts.app',
                'home',
                'testimonies.index',
                'testimonies.bukti-jackpot',
            ],
            function ($view) {
                $site = Setting::asArray();
                $view->with('site', $site);
                $view->with('theme', ThemePalette::resolve($site['theme_preset'] ?? null));

                $navPages = Schema::hasTable('pages')
                    ? Page::query()
                        ->where('is_published', true)
                        ->orderByDesc('updated_at')
                        ->get(['id', 'title', 'slug'])
                    : collect();
                $view->with('navPages', $navPages);
            }
        );
    }
}
