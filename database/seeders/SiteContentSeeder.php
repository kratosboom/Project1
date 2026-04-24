<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameProvider;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
            return;
        }

        $defaults = [
            'brand_name' => config('app.name'),
            'marquee_text' => __('Selamat datang di tampilan demo RTP. Jam & pola hanya contoh tata letak. Kelola lewat admin pengaturan.'),
        ];
        foreach ($defaults as $k => $v) {
            Setting::query()->updateOrCreate(
                ['key' => $k],
                ['value' => $v]
            );
        }
        Setting::refreshCache();

        if (! \Illuminate\Support\Facades\Schema::hasTable('game_providers')) {
            return;
        }

        GameProvider::query()->updateOrCreate(
            ['slug' => 'hot-games'],
            [
                'name' => 'Hot Games',
                'logo_url' => null,
                'website_url' => null,
                'icon_class' => 'fa-fire-alt',
                'is_hot_games' => true,
                'sort_order' => 0,
            ]
        );

        $pragmatic = GameProvider::query()->updateOrCreate(
            ['slug' => 'pragmatic'],
            [
                'name' => 'PRAGMATIC',
                'logo_url' => null,
                'website_url' => 'https://www.pragmaticplay.com/',
                'icon_class' => 'fa-crown',
                'is_hot_games' => false,
                'sort_order' => 1,
            ]
        );

        $others = [
            ['name' => 'PGSOFT', 'slug' => 'pgsoft', 'icon' => 'fa-gamepad', 'o' => 2],
            ['name' => 'PLAYTECH', 'slug' => 'playtech', 'icon' => 'fa-dice', 'o' => 3],
            ['name' => 'JILI', 'slug' => 'jili', 'icon' => 'fa-star', 'o' => 4],
            ['name' => 'HABANERO', 'slug' => 'habanero', 'icon' => 'fa-pepper-hot', 'o' => 5],
            ['name' => 'MICROGAMING', 'slug' => 'microgaming', 'icon' => 'fa-chess-king', 'o' => 6],
            ['name' => 'SPADEGAMING', 'slug' => 'spadegaming', 'icon' => 'fa-spade', 'o' => 7],
        ];
        foreach ($others as $row) {
            GameProvider::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'icon_class' => $row['icon'],
                    'is_hot_games' => false,
                    'sort_order' => $row['o'],
                ]
            );
        }

        if (! \Illuminate\Support\Facades\Schema::hasTable('games') || Game::query()->exists()) {
            return;
        }

        $data = [
            ['name' => 'Gates of Olympus 1000', 'rtp' => 96.97, 'hot' => true, 'best' => false, 'jam' => '10.15 - 13.45', 'pola' => ['turbo' => '20X', 'auto' => '50X', 'manual' => '100X'], 'img' => 'https://cdn.databerjalan.com/cdn-cgi/image/width=400,quality=75,fit=contain,format=auto/assets/images/games/pragmatic/vs20olympx.png'],
            ['name' => 'Sweet Bonanza 1000', 'rtp' => 96.54, 'hot' => true, 'best' => false, 'jam' => '14.00 - 16.20', 'pola' => ['turbo' => '15X', 'auto' => '30X', 'manual' => '80X'], 'img' => 'https://cdn.databerjalan.com/cdn-cgi/image/width=400,quality=75,fit=contain,format=auto/assets/images/games/pragmatic/vs20sbxmas.png'],
            ['name' => 'Starlight Princess 1000', 'rtp' => 97.92, 'hot' => false, 'best' => true, 'jam' => '17.15 - 20.45', 'pola' => ['turbo' => '20X', 'auto' => '50X', 'manual' => '100X'], 'img' => 'https://cdn.databerjalan.com/cdn-cgi/image/width=400,quality=75,fit=contain,format=auto/assets/images/games/pragmatic/vs20starpr.png'],
            ['name' => 'Mahjong Wins 3', 'rtp' => 95.53, 'hot' => false, 'best' => false, 'jam' => '20.00 - 22.00', 'pola' => ['turbo' => '10X', 'auto' => '25X', 'manual' => '60X'], 'img' => 'https://cdn.databerjalan.com/cdn-cgi/image/width=400,quality=75,fit=contain,format=auto/assets/images/games/pragmatic/vs20mahjwin.png'],
            ['name' => 'Fortune of Olympus', 'rtp' => 95.51, 'hot' => false, 'best' => false, 'jam' => '12.00 - 15.00', 'pola' => ['turbo' => '12X', 'auto' => '40X', 'manual' => '90X'], 'img' => 'https://cdn.databerjalan.com/cdn-cgi/image/width=400,quality=75,fit=contain,format=auto/assets/images/games/pragmatic/vs20olymp.png'],
            ['name' => 'Wild West Gold', 'rtp' => 94.81, 'hot' => false, 'best' => false, 'jam' => '18.30 - 21.00', 'pola' => ['turbo' => '18X', 'auto' => '45X', 'manual' => '120X'], 'img' => 'https://cdn.databerjalan.com/cdn-cgi/image/width=400,quality=75,fit=contain,format=auto/assets/images/games/pragmatic/vs25wildwest.png'],
        ];
        $sort = 0;
        foreach ($data as $row) {
            Game::query()->create([
                'game_provider_id' => $pragmatic->id,
                'name' => $row['name'],
                'image_url' => $row['img'],
                'rtp' => $row['rtp'],
                'is_hot' => $row['hot'],
                'is_best' => $row['best'],
                'jam_gacor' => $row['jam'],
                'pola' => $row['pola'],
                'sort_order' => $sort++,
            ]);
        }
    }
}
