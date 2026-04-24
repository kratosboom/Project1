<?php

use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\GameProviderController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\TestimonyAdminController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\GameClickController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TestimonyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/testimoni', [TestimonyController::class, 'index'])->name('testimoni');
Route::redirect('/testimoni/v2', '/bukti-jackpot', 301);
Route::get('/bukti-jackpot', [TestimonyController::class, 'buktiJackpot'])->name('bukti_jackpot');

Route::get('/halaman/{page}', [PageController::class, 'show'])->name('halaman.show');

Route::post('/games/{game}/track-click', GameClickController::class)
    ->middleware('throttle:60,1')
    ->name('games.trackClick');

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('admin', function () {
        $pageCount = \App\Models\Page::count();
        $publishedCount = \App\Models\Page::where('is_published', true)->count();
        $gameCount = \Illuminate\Support\Facades\Schema::hasTable('games') ? \App\Models\Game::count() : 0;
        $providerCount = \Illuminate\Support\Facades\Schema::hasTable('game_providers') ? \App\Models\GameProvider::count() : 0;
        $testimonyCount = \App\Models\Testimony::count();

        return view('admin.dashboard', compact('pageCount', 'publishedCount', 'gameCount', 'providerCount', 'testimonyCount'));
    })->name('admin.dashboard');

    Route::get('admin/pengaturan', [SiteSettingController::class, 'edit'])->name('admin.pengaturan.edit');
    Route::put('admin/pengaturan', [SiteSettingController::class, 'update'])->name('admin.pengaturan.update');
    Route::put('admin/pengaturan/tema', [ThemeController::class, 'update'])->name('admin.theme.update');

    Route::post('admin/provider/{game_provider}/ambil-logo', [GameProviderController::class, 'fetchLogo'])->name('admin.provider.fetchLogo');
    Route::resource('admin/provider', GameProviderController::class)->names('admin.provider')->parameters(['provider' => 'game_provider'])->except(['show']);

    Route::post('admin/game/impor', [GameController::class, 'import'])->name('admin.game.import');
    Route::post('admin/game/pratinjau-html', [GameController::class, 'previewHtml'])->name('admin.game.previewHtml');
    Route::post('admin/game/impor-html', [GameController::class, 'importHtml'])->name('admin.game.importHtml');
    Route::post('admin/game/acak-semua', [GameController::class, 'randomizeAll'])->name('admin.game.randomizeAll');
    Route::post('admin/game/reset-klik', [GameController::class, 'resetClicks'])->name('admin.game.resetClicks');
    Route::post('admin/game/hitung-ulang-hot', [GameController::class, 'recomputeHot'])->name('admin.game.recomputeHot');
    Route::resource('admin/game', GameController::class)->names('admin.game')->except(['show']);

    Route::resource('admin/testimonies', TestimonyAdminController::class)
        ->names('admin.testimoni')
        ->except(['show'])
        ->parameters(['testimonies' => 'testimony']);

    Route::resource('admin/halaman', PageController::class)
        ->except(['show'])
        ->parameters(['halaman' => 'page'])
        ->names('admin.halaman');
});

require __DIR__.'/auth.php';
