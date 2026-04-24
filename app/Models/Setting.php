<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        if (! Schema::hasTable('settings')) {
            return $default;
        }
        $row = static::query()->where('key', $key)->value('value');

        return $row !== null && $row !== '' ? $row : $default;
    }

    public static function set(string $key, ?string $value): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        Cache::forget('settings_array');
    }

    /** @return array<string, string|null> */
    public static function asArray(): array
    {
        if (! Schema::hasTable('settings')) {
            return self::defaultKeys();
        }

        return Cache::rememberForever('settings_array', function () {
            $defaults = self::defaultKeys();
            $fromDb = static::query()->pluck('value', 'key')->all();

            return array_merge($defaults, $fromDb);
        });
    }

    public static function refreshCache(): void
    {
        if (Schema::hasTable('settings')) {
            Cache::forget('settings_array');
        }
    }

    /** @return array<string, string|null> */
    public static function defaultKeys(): array
    {
        return [
            'brand_name' => null,
            'logo_url' => null,
            'favicon_url' => null,
            'hero_banner_url' => null,
            'footer_text' => null,
            'login_url' => null,
            'register_url' => null,
            'marquee_text' => null,
            'theme_preset' => 'crimson',
            'maxwin_modal_kapital_label' => null,
            'maxwin_default_kapital' => null,
            'maxwin_modal_footer_text' => null,
            'main_sekarang_url' => null,
            'hajar_sekarang_url' => null,
        ];
    }
}
