<?php

use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/halaman/{page}', [PageController::class, 'show'])->name('halaman.show');

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('admin', function () {
        $pageCount = \App\Models\Page::count();
        $publishedCount = \App\Models\Page::where('is_published', true)->count();
        $userCount = \App\Models\User::count();

        return view('admin.dashboard', compact('pageCount', 'publishedCount', 'userCount'));
    })->name('admin.dashboard');

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
