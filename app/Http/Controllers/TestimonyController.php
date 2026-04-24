<?php

namespace App\Http\Controllers;

use App\Models\Testimony;
use Illuminate\View\View;

class TestimonyController extends Controller
{
    public function index(): View
    {
        $testimonies = Testimony::query()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('testimonies.index', compact('testimonies'));
    }

    public function buktiJackpot(): View
    {
        $testimonies = Testimony::query()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('testimonies.bukti-jackpot', compact('testimonies'));
    }
}
