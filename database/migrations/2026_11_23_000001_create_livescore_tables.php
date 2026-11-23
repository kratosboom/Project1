<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('api_id')->unique();
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('country')->nullable();
            $table->string('country_flag')->nullable();
            $table->string('logo')->nullable();
            $table->unsignedSmallInteger('season')->nullable();
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('sort_order')->default(999);
            $table->timestamps();
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('api_id')->unique();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('fixtures', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('api_id')->unique();
            $table->foreignId('league_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('season')->nullable();
            $table->string('round')->nullable();
            $table->foreignId('home_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('away_team_id')->constrained('teams')->cascadeOnDelete();
            $table->dateTime('kickoff_at')->index(); // UTC
            $table->string('status_short', 8)->index();
            $table->string('status_long')->nullable();
            $table->unsignedSmallInteger('elapsed')->nullable();
            $table->unsignedSmallInteger('elapsed_extra')->nullable();
            $table->unsignedTinyInteger('goals_home')->nullable();
            $table->unsignedTinyInteger('goals_away')->nullable();
            $table->json('score')->nullable();
            $table->string('venue_name')->nullable();
            $table->string('venue_city')->nullable();
            $table->string('referee')->nullable();
            $table->json('events')->nullable();
            $table->json('lineups')->nullable();
            $table->json('statistics')->nullable();
            $table->timestamp('detail_synced_at')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('standings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('season');
            $table->json('groups'); // array of groups, tiap group = array baris klasemen
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->unique(['league_id', 'season']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standings');
        Schema::dropIfExists('fixtures');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('leagues');
    }
};
