<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

class LivescoreController extends Controller
{
    /** Sinkron skor hari ini sekarang juga (1 request API, dibatasi throttle di route). */
    public function sync(): RedirectResponse
    {
        $code = Artisan::call('livescore:sync', ['task' => 'today']);
        $out = trim(Artisan::output());

        return redirect()->route('admin.dashboard')->with('ok', $code === 0
            ? 'Sinkron selesai. '.str_replace("\n", ' · ', $out)
            : 'Sinkron gagal: '.$out);
    }
}
