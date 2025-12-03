<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Profile;
use App\Models\Tournament;
use App\Models\Clan;
use App\Models\LfgPost;
use App\Models\CommunityPost;
use App\Models\GuidePost;
use App\Models\Badge;
use App\Models\Friendship;
use App\Models\Message;

/**
 * Multi-game migration sonrası veri bütünlüğünü kontrol eder
 * 
 * Bu command şunları doğrular:
 * 1. Tüm kullanıcı verileri korunmuş mu?
 * 2. İlişkiler hala çalışıyor mu?
 * 3. Game_id atamaları doğru yapılmış mı?
 * 4. Cross-game özellikler (friendships, messages) korunmuş mu?
 */
class VerifyDataIntegrity extends Command
{
    /**
     * Command signature
     */
    protected $signature = 'multi-game:verify-data
                          {--detailed : Detaylı rapor göster}
                          {--fix : Tespit edilen sorunları otomatik düzelt}';

    /**
     * Command description
     */
    protected $description = 'Multi-game migration sonrası veri bütünlüğünü kontrol eder';

    /**
     * Sorun sayacı
     */
    private int $issuesFound = 0;

    /**
     * Düzeltilen sorun sayacı
     */
    private int $issuesFixed = 0;

    /**
     * Execute the command
     */
    public function handle(): int
    {
        $this->info('🔍 Multi-Game Platform - Veri Bütünlüğü Kontrolü Başlıyor...');
        $this->newLine();

        // 1. Kullanıcı verilerini kontrol et
        $this->checkUserData();

        // 2. İlişkileri kontrol et
        $this->checkRelationships();

        // 3. Game-specific verileri kontrol et
        $this->checkGameSpecificData();

        // 4. Cross-game özellikleri kontrol et
        $this->checkCrossGameFeatures();

        // 5. Orphaned records kontrol et
        $this->checkOrphanedRecords();

        // Sonuç raporu
        $this->newLine();
        $this->displaySummary();

        return $this->issuesFound > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Kullanıcı verilerini kontrol et
     */
    private function checkUserData(): void
    {
        $this->info('📊 1. Kullanıcı Verileri Kontrolü');

        // Kullanıcı sayısı
        $userCount = User::count();
        $this->line("   ✓ Toplam kullanıcı: {$userCount}");

        // Profile ilişkisi
        $usersWithoutProfile = User::doesntHave('profile')->count();
        if ($usersWithoutProfile > 0) {
            $this->warn("   ⚠ Profile'ı olmayan kullanıcı: {$usersWithoutProfile}");
            $this->issuesFound++;

            if ($this->option('fix')) {
                $this->fixUsersWithoutProfile();
            }
        } else {
            $this->line("   ✓ Tüm kullanıcıların profile'ı var");
        }

        // Email uniqueness
        $duplicateEmails = DB::table('users')
            ->select('email', DB::raw('COUNT(*) as count'))
            ->groupBy('email')
            ->having('count', '>', 1)
            ->count();

        if ($duplicateEmails > 0) {
            $this->error("   ✗ Duplicate email adresleri bulundu: {$duplicateEmails}");
            $this->issuesFound++;
        } else {
            $this->line("   ✓ Email adresleri unique");
        }

        $this->newLine();
    }

    /**
     * İlişkileri kontrol et
     */
    private function checkRelationships(): void
    {
        $this->info('🔗 2. İlişki Kontrolü');

        // User -> Profile ilişkisi
        $profileCount = Profile::count();
        $this->line("   ✓ Toplam profile: {$profileCount}");

        // Profile -> User ilişkisi (orphaned profiles)
        $orphanedProfiles = Profile::whereNotIn('user_id', User::pluck('id'))->count();
        if ($orphanedProfiles > 0) {
            $this->warn("   ⚠ Sahibi olmayan profile: {$orphanedProfiles}");
            $this->issuesFound++;

            if ($this->option('fix')) {
                $this->fixOrphanedProfiles();
            }
        } else {
            $this->line("   ✓ Tüm profile'ların sahibi var");
        }

        // Tournament -> User ilişkisi
        $orphanedTournaments = Tournament::whereNotIn('organizer_id', User::pluck('id'))->count();
        if ($orphanedTournaments > 0) {
            $this->warn("   ⚠ Organizatörü olmayan turnuva: {$orphanedTournaments}");
            $this->issuesFound++;
        } else {
            $this->line("   ✓ Tüm turnuvaların organizatörü var");
        }

        // Clan -> User ilişkisi
        $orphanedClans = Clan::whereNotIn('user_id', User::pluck('id'))->count();
        if ($orphanedClans > 0) {
            $this->warn("   ⚠ Sahibi olmayan klan: {$orphanedClans}");
            $this->issuesFound++;
        } else {
            $this->line("   ✓ Tüm klanların sahibi var");
        }

        $this->newLine();
    }

    /**
     * Game-specific verileri kontrol et
     */
    private function checkGameSpecificData(): void
    {
        $this->info('🎮 3. Oyuna Özel Veri Kontrolü');

        // Tournaments
        $tournamentsWithoutGame = Tournament::whereNull('game_id')->count();
        if ($tournamentsWithoutGame > 0) {
            $this->error("   ✗ Game_id'si olmayan turnuva: {$tournamentsWithoutGame}");
            $this->issuesFound++;

            if ($this->option('fix')) {
                $this->fixMissingGameIds('tournaments');
            }
        } else {
            $tournamentCount = Tournament::count();
            $this->line("   ✓ Tüm turnuvaların game_id'si var ({$tournamentCount})");
        }

        // Clans
        $clansWithoutGame = Clan::whereNull('game_id')->count();
        if ($clansWithoutGame > 0) {
            $this->error("   ✗ Game_id'si olmayan klan: {$clansWithoutGame}");
            $this->issuesFound++;

            if ($this->option('fix')) {
                $this->fixMissingGameIds('clans');
            }
        } else {
            $clanCount = Clan::count();
            $this->line("   ✓ Tüm klanların game_id'si var ({$clanCount})");
        }

        // LFG Posts
        $lfgWithoutGame = LfgPost::whereNull('game_id')->count();
        if ($lfgWithoutGame > 0) {
            $this->error("   ✗ Game_id'si olmayan LFG ilanı: {$lfgWithoutGame}");
            $this->issuesFound++;

            if ($this->option('fix')) {
                $this->fixMissingGameIds('lfg_posts');
            }
        } else {
            $lfgCount = LfgPost::count();
            $this->line("   ✓ Tüm LFG ilanlarının game_id'si var ({$lfgCount})");
        }

        // Community Posts
        $communityWithoutGame = CommunityPost::whereNull('game_id')->count();
        if ($communityWithoutGame > 0) {
            $this->error("   ✗ Game_id'si olmayan topluluk gönderisi: {$communityWithoutGame}");
            $this->issuesFound++;

            if ($this->option('fix')) {
                $this->fixMissingGameIds('community_posts');
            }
        } else {
            $communityCount = CommunityPost::count();
            $this->line("   ✓ Tüm topluluk gönderilerinin game_id'si var ({$communityCount})");
        }

        // Guide Posts
        $guideWithoutGame = GuidePost::whereNull('game_id')->count();
        if ($guideWithoutGame > 0) {
            $this->error("   ✗ Game_id'si olmayan rehber: {$guideWithoutGame}");
            $this->issuesFound++;

            if ($this->option('fix')) {
                $this->fixMissingGameIds('guide_posts');
            }
        } else {
            $guideCount = GuidePost::count();
            $this->line("   ✓ Tüm rehberlerin game_id'si var ({$guideCount})");
        }

        $this->newLine();
    }

    /**
     * Cross-game özellikleri kontrol et
     */
    private function checkCrossGameFeatures(): void
    {
        $this->info('🌐 4. Cross-Game Özellik Kontrolü');

        // Friendships (game_id olmamalı - tablo yapısını kontrol et)
        $friendshipCount = Friendship::count();
        $friendshipColumns = DB::getSchemaBuilder()->getColumnListing('friendships');
        
        if (in_array('game_id', $friendshipColumns)) {
            $friendshipsWithGame = DB::table('friendships')
                ->whereNotNull('game_id')
                ->count();

            if ($friendshipsWithGame > 0) {
                $this->warn("   ⚠ Game_id'si olan arkadaşlık: {$friendshipsWithGame} (olmamalı)");
                $this->issuesFound++;
            } else {
                $this->line("   ✓ Arkadaşlıklar cross-game ({$friendshipCount})");
            }
        } else {
            $this->line("   ✓ Arkadaşlıklar cross-game - game_id kolonu yok ({$friendshipCount})");
        }

        // Messages (game_id olmamalı - tablo yapısını kontrol et)
        $messageCount = Message::count();
        $messageColumns = DB::getSchemaBuilder()->getColumnListing('messages');
        
        if (in_array('game_id', $messageColumns)) {
            $messagesWithGame = DB::table('messages')
                ->whereNotNull('game_id')
                ->count();

            if ($messagesWithGame > 0) {
                $this->warn("   ⚠ Game_id'si olan mesaj: {$messagesWithGame} (olmamalı)");
                $this->issuesFound++;
            } else {
                $this->line("   ✓ Mesajlar cross-game ({$messageCount})");
            }
        } else {
            $this->line("   ✓ Mesajlar cross-game - game_id kolonu yok ({$messageCount})");
        }

        // Users (game_id olmamalı - tablo yapısını kontrol et)
        $userColumns = DB::getSchemaBuilder()->getColumnListing('users');
        
        if (in_array('game_id', $userColumns)) {
            $usersWithGame = DB::table('users')
                ->whereNotNull('game_id')
                ->count();

            if ($usersWithGame > 0) {
                $this->warn("   ⚠ Game_id'si olan kullanıcı: {$usersWithGame} (olmamalı)");
                $this->issuesFound++;
            } else {
                $userCount = User::count();
                $this->line("   ✓ Kullanıcılar cross-game ({$userCount})");
            }
        } else {
            $userCount = User::count();
            $this->line("   ✓ Kullanıcılar cross-game - game_id kolonu yok ({$userCount})");
        }

        $this->newLine();
    }

    /**
     * Orphaned records kontrol et
     */
    private function checkOrphanedRecords(): void
    {
        $this->info('🗑️  5. Orphaned Record Kontrolü');

        // Invalid game_id'leri kontrol et
        $validGameIds = DB::table('games')->pluck('id')->toArray();

        $tables = [
            'tournaments' => Tournament::class,
            'clans' => Clan::class,
            'lfg_posts' => LfgPost::class,
            'community_posts' => CommunityPost::class,
            'guide_posts' => GuidePost::class,
        ];

        foreach ($tables as $table => $model) {
            $invalidRecords = DB::table($table)
                ->whereNotNull('game_id')
                ->whereNotIn('game_id', $validGameIds)
                ->count();

            if ($invalidRecords > 0) {
                $this->error("   ✗ {$table}: Geçersiz game_id'li kayıt: {$invalidRecords}");
                $this->issuesFound++;
            } else {
                $count = DB::table($table)->count();
                $this->line("   ✓ {$table}: Tüm game_id'ler geçerli ({$count})");
            }
        }

        $this->newLine();
    }

    /**
     * Profile'ı olmayan kullanıcıları düzelt
     */
    private function fixUsersWithoutProfile(): void
    {
        $users = User::doesntHave('profile')->get();

        foreach ($users as $user) {
            Profile::create([
                'user_id' => $user->id,
                'bio' => '',
                'player_id' => null,
                'player_tier' => 'Bronze',
                'favorite_mode' => 'Classic',
            ]);
            $this->issuesFixed++;
        }

        $this->info("   ✓ {$this->issuesFixed} kullanıcı için profile oluşturuldu");
    }

    /**
     * Orphaned profile'ları düzelt
     */
    private function fixOrphanedProfiles(): void
    {
        $deleted = Profile::whereNotIn('user_id', User::pluck('id'))->delete();
        $this->issuesFixed += $deleted;
        $this->info("   ✓ {$deleted} orphaned profile silindi");
    }

    /**
     * Eksik game_id'leri düzelt (default: game_id=1 PUBG)
     */
    private function fixMissingGameIds(string $table): void
    {
        $updated = DB::table($table)
            ->whereNull('game_id')
            ->update(['game_id' => 1]);

        $this->issuesFixed += $updated;
        $this->info("   ✓ {$table}: {$updated} kayıt game_id=1 ile güncellendi");
    }

    /**
     * Özet raporu göster
     */
    private function displaySummary(): void
    {
        $this->info('📋 Özet Rapor');
        $this->line('─────────────────────────────────────');

        if ($this->issuesFound === 0) {
            $this->info('✅ Hiçbir sorun bulunamadı! Veri bütünlüğü korunmuş.');
        } else {
            $this->warn("⚠️  Toplam {$this->issuesFound} sorun bulundu.");

            if ($this->issuesFixed > 0) {
                $this->info("✓ {$this->issuesFixed} sorun otomatik düzeltildi.");
            }

            if ($this->issuesFound > $this->issuesFixed) {
                $this->newLine();
                $this->warn('💡 Kalan sorunları düzeltmek için:');
                $this->line('   php artisan multi-game:verify-data --fix');
            }
        }

        $this->newLine();
    }
}
