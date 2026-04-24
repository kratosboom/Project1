<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Testimony;
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

        $proof1 = [
            'https://placehold.co/640x400/1f2937/fb2323?text=Bukti+WD+jutaan',
            'https://placehold.co/640x400/111827/4ade80?text=Struk+berhasil',
        ];
        $proof2 = [
            'https://placehold.co/640x400/111827/38bdf8?text=History+pembayaran',
        ];
        $proof3 = [
            'https://placehold.co/640x400/1e293b/fbbf24?text=Screenshot+maxwin',
            'https://placehold.co/640x400/0f172a/94a3b8?text=Chat+CS+selesaikan',
        ];

        Testimony::query()->insert([
            [
                'author_name' => 'Dewi Lestari',
                'author_role' => 'Product Lead, Jakarta',
                'body' => 'Antarmuka gelapnya enak dipandang lama. CRUD halaman terasa ringan — pas buat prototipe cepat.',
                'proof_images' => json_encode($proof1),
                'rating' => 5,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'author_name' => 'Raka Pratama',
                'author_role' => 'Freelance Developer',
                'body' => 'Struktur routes-nya bersih. Saya suka slug otomatis dan daftar kelola halaman yang terbaca.',
                'proof_images' => json_encode($proof2),
                'rating' => 5,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'author_name' => 'Mira Anggraini',
                'author_role' => 'UI/UX Designer',
                'body' => 'Tipografi Fraunces + DM Sans memberi karakter. Bukan tampilan Laravel “default” yang membosankan.',
                'proof_images' => json_encode($proof3),
                'rating' => 4,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Page::query()->create([
            'title' => 'Selamat datang',
            'slug' => Page::uniqueSlug('selamat-datang'),
            'excerpt' => 'Halaman contoh yang dibuat seeder.',
            'body' => "Ini adalah isi halaman contoh.\n\nAnda bisa mengganti teks ini dari menu Kelola Halaman → Edit.\n\nSelamat berkarya.",
            'is_published' => true,
        ]);

        $this->call(SiteContentSeeder::class);
    }
}
