<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Superadmin — login: raizomaestro@example.com / monsterRTP
        User::query()->updateOrCreate(
            ['email' => 'raizomaestro@example.com'],
            [
                'name' => 'raizomaestro',
                'password' => Hash::make('monsterRTP'),
            ]
        );

        Page::query()->create([
            'title' => 'Selamat datang',
            'slug' => Page::uniqueSlug('selamat-datang'),
            'excerpt' => 'Halaman contoh yang dibuat seeder.',
            'body' => "Ini adalah isi halaman contoh.\n\nAnda bisa mengganti teks ini dari menu Kelola Halaman → Edit.\n\nSelamat berkarya.",
            'is_published' => true,
        ]);

    }
}
