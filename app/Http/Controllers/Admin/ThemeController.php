<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\ThemePalette;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ThemeController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'theme_preset' => ['required', 'string', Rule::in(array_keys(ThemePalette::presets()))],
        ]);

        Setting::query()->updateOrCreate(
            ['key' => 'theme_preset'],
            ['value' => $data['theme_preset']]
        );
        Setting::refreshCache();

        return redirect()->route('admin.pengaturan.edit')->with('ok', 'Tema warna disimpan.');
    }
}

