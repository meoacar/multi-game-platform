<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Sıralama önemli:
     * 1. Önce oyunlar (games)
     * 2. Sonra kullanıcılar (users + profiles + devices)
     * 3. Badge, Setting, Page
     */
    public function run(): void
    {
        $this->call([
            GameSeeder::class,
            UserSeeder::class,
            BadgeSeeder::class,
            SettingSeeder::class,
            PageSeeder::class,
        ]);

        $this->command->info('🎉 Tüm seeder\'lar başarıyla çalıştırıldı!');
    }
}
