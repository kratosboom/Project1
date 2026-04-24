<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_provider_id')->constrained('game_providers')->cascadeOnDelete();
            $table->string('name');
            $table->string('image_url')->nullable();
            $table->decimal('rtp', 5, 2)->default(96.5);
            $table->boolean('is_hot')->default(false);
            $table->boolean('is_best')->default(false);
            $table->string('jam_gacor', 32)->default('00.00 - 00.00');
            $table->json('pola')->nullable();
            $table->json('modal_data')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
