<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Her kullanıcı için profile_complete XP'sinden sadece ilkini tut, diğerlerini sil
        $duplicates = DB::table('xp_events')
            ->select('user_id', DB::raw('MIN(id) as keep_id'))
            ->where('type', 'profile_complete')
            ->groupBy('user_id')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            // İlk kayıt hariç diğerlerini sil
            $deletedEvents = DB::table('xp_events')
                ->where('user_id', $duplicate->user_id)
                ->where('type', 'profile_complete')
                ->where('id', '!=', $duplicate->keep_id)
                ->get();

            // Silinen XP'leri kullanıcının toplam XP'sinden düş
            $totalDeletedXp = $deletedEvents->sum('points');
            
            if ($totalDeletedXp > 0) {
                DB::table('users')
                    ->where('id', $duplicate->user_id)
                    ->decrement('xp_total', $totalDeletedXp);
            }

            // Duplicate kayıtları sil
            DB::table('xp_events')
                ->where('user_id', $duplicate->user_id)
                ->where('type', 'profile_complete')
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri alınamaz
    }
};
