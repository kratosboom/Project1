<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            if (! Schema::hasColumn('games', 'click_count')) {
                $table->unsignedBigInteger('click_count')
                    ->default(0)
                    ->after('is_active')
                    ->comment('Jumlah klik user — dipakai untuk derive is_hot (top-N).');
            }
        });

        if (Schema::hasColumn('games', 'click_count')) {
            Schema::table('games', function (Blueprint $table) {
                $table->index('click_count', 'games_click_count_idx');
            });
        }

        DB::table('games')->update(['is_hot' => false]);
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            if (Schema::hasColumn('games', 'click_count')) {
                try {
                    $table->dropIndex('games_click_count_idx');
                } catch (\Throwable) {
                }
                $table->dropColumn('click_count');
            }
        });
    }
};
