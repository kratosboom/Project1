<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\ThemePalette;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        $settings = array_merge(Setting::defaultKeys(), Setting::pluck('value', 'key')->all());
        $themePresets = ThemePalette::presets();

        return view('admin.pengaturan', compact('settings', 'themePresets'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'brand_name' => ['nullable', 'string', 'max:120'],
            'logo_url' => ['nullable', 'string', 'max:2000'],
            'favicon_url' => ['nullable', 'string', 'max:2000'],
            'hero_banner_url' => ['nullable', 'string', 'max:2000'],
            'footer_text' => ['nullable', 'string', 'max:5000'],
            'login_url' => ['nullable', 'string', 'max:2000'],
            'register_url' => ['nullable', 'string', 'max:2000'],
            'marquee_text' => ['nullable', 'string', 'max:1000'],
            'maxwin_modal_kapital_label' => ['nullable', 'string', 'max:160'],
            'maxwin_default_kapital' => ['nullable', 'string', 'max:14', 'regex:/^[0-9]*$/'],
            'maxwin_modal_footer_text' => ['nullable', 'string', 'max:500'],
            'main_sekarang_url' => ['nullable', 'string', 'max:2000'],
            'hajar_sekarang_url' => ['nullable', 'string', 'max:2000'],
        ]);

        if (($data['maxwin_default_kapital'] ?? '') === '' || $data['maxwin_default_kapital'] === '0') {
            $data['maxwin_default_kapital'] = null;
        }

        foreach ($data as $key => $value) {
            if ($value === null || $value === '') {
                Setting::query()->where('key', $key)->delete();
            } else {
                Setting::query()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        Setting::refreshCache();

        return redirect()->route('admin.pengaturan.edit')->with('ok', 'Pengaturan situs disimpan.');
    }
}
