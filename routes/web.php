<?php

use App\Http\Controllers\Admin\LivescoreController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Livescore\LeagueController;
use App\Http\Controllers\Livescore\MatchController;
use App\Http\Controllers\Livescore\SitemapController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jadwal/{date}', [HomeController::class, 'date'])
    ->where('date', '\d{4}-\d{2}-\d{2}')
    ->name('jadwal');
Route::get('/liga/{league}', [LeagueController::class, 'show'])->name('liga.show');
Route::get('/pertandingan/{fixture}', [MatchController::class, 'show'])->name('pertandingan.show');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::get('/halaman/{page}', [PageController::class, 'show'])->name('halaman.show');

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('admin', function () {
        $pageCount = \App\Models\Page::count();
        $publishedCount = \App\Models\Page::where('is_published', true)->count();
        $userCount = \App\Models\User::count();
        $livescore = [
            'configured' => app(\App\Services\ApiSports\FootballClient::class)->isConfigured(),
            'quota' => app(\App\Services\ApiSports\QuotaGuard::class)->snapshot(),
            'lastSync' => \App\Services\Livescore\FootballSync::lastSyncedAt(),
            'fixtures' => \App\Models\Fixture::count(),
            'leagues' => count(config('livescore.leagues', [])),
        ];

        return view('admin.dashboard', compact('pageCount', 'publishedCount', 'userCount', 'livescore'));
    })->name('admin.dashboard');

    Route::post('admin/livescore/sync', [LivescoreController::class, 'sync'])
        ->middleware('throttle:2,10')
        ->name('admin.livescore.sync');

    Route::get('admin/pengaturan', [SiteSettingController::class, 'edit'])->name('admin.pengaturan.edit');
    Route::put('admin/pengaturan', [SiteSettingController::class, 'update'])->name('admin.pengaturan.update');
    Route::put('admin/pengaturan/tema', [ThemeController::class, 'update'])->name('admin.theme.update');

    Route::resource('admin/halaman', PageController::class)
        ->except(['show'])
        ->parameters(['halaman' => 'page'])
        ->names('admin.halaman');

    Route::resource('admin/users', UserController::class)
        ->except(['show'])
        ->names('admin.users');
});

require __DIR__.'/auth.php';
