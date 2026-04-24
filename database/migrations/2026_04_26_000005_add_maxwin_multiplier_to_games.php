<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            if (! Schema::hasColumn('games', 'maxwin_multiplier')) {
                $table->unsignedSmallInteger('maxwin_multiplier')
                    ->nullable()
                    ->after('maxwin_difficulty_max')
                    ->comment('Prediksi kemenangan: kelipatan modal (50-300x)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            if (Schema::hasColumn('games', 'maxwin_multiplier')) {
                $table->dropColumn('maxwin_multiplier');
            }
        });
    }
};
