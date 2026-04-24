<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('testimonies', function (Blueprint $table) {
            $table->json('proof_images')->nullable()->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('testimonies', function (Blueprint $table) {
            $table->dropColumn('proof_images');
        });
    }
};
