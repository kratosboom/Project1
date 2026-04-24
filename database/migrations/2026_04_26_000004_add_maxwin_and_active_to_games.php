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
            if (! Schema::hasColumn('games', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_best');
            }
            if (! Schema::hasColumn('games', 'maxwin_footer_text')) {
                $table->string('maxwin_footer_text', 280)->nullable()->after('modal_data');
            }
            if (! Schema::hasColumn('games', 'maxwin_difficulty_min')) {
                $table->unsignedTinyInteger('maxwin_difficulty_min')->nullable()->after('maxwin_footer_text');
            }
            if (! Schema::hasColumn('games', 'maxwin_difficulty_max')) {
                $table->unsignedTinyInteger('maxwin_difficulty_max')->nullable()->after('maxwin_difficulty_min');
            }
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            foreach (['is_active', 'maxwin_footer_text', 'maxwin_difficulty_min', 'maxwin_difficulty_max'] as $col) {
                if (Schema::hasColumn('games', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
