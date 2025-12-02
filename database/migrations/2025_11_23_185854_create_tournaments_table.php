<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('rules')->nullable();
            $table->string('prize_pool')->nullable();
            $table->integer('max_teams');
            $table->integer('team_size')->default(4);
            $table->timestamp('registration_starts_at')->nullable();
            $table->timestamp('registration_ends_at')->nullable();
            $table->timestamp('tournament_starts_at')->nullable();
            $table->timestamp('tournament_ends_at')->nullable();
            $table->enum('status', ['upcoming', 'registration_open', 'in_progress', 'completed', 'cancelled'])->default('upcoming');
            $table->json('bracket_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
