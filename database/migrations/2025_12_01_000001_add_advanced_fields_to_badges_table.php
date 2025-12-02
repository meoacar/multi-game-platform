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
        Schema::table('badges', function (Blueprint $table) {
            $table->enum('category', [
                'gameplay',
                'social',
                'content',
                'special',
                'activity',
                'moderation'
            ])->default('special')->after('icon');
            
            $table->enum('rarity', [
                'common',
                'rare',
                'epic',
                'legendary'
            ])->default('common')->after('category');
            
            $table->boolean('is_hidden')->default(false)->after('rarity');
            $table->boolean('is_active')->default(true)->after('is_hidden');
            $table->integer('sort_order')->default(0)->after('is_active');
        });

        Schema::table('user_badges', function (Blueprint $table) {
            $table->integer('progress')->default(0)->after('unlocked_at');
            $table->integer('progress_max')->nullable()->after('progress');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->dropColumn(['category', 'rarity', 'is_hidden', 'is_active', 'sort_order']);
        });

        Schema::table('user_badges', function (Blueprint $table) {
            $table->dropColumn(['progress', 'progress_max']);
        });
    }
};
