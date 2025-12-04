<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class GameSeeder extends Seeder
{
    /**
     * Multi-game platform için oyun seeder'ı
     * 
     * Bu seeder:
     * 1. PUBG Mobile oyununu detaylı ayarlarla oluşturur
     * 2. Mevcut verileri game_id=1 ile günceller
     * 3. Data integrity kontrolü yapar
     * 
     * Requirements: 2.2, 13.2, 13.3
     */
    public function run(): void
    {
        $this->command->info('🎮 GameSeeder başlatılıyor...');
        
        // 1. PUBG Mobile oyununu oluştur
        $this->createPubgGame();
        
        // 2. Diğer oyunları oluştur
        $this->createOtherGames();
        
        // 3. Mevcut verileri game_id=1 ile güncelle
        $this->updateExistingData();
        
        // 4. Data integrity kontrolü
        $this->verifyDataIntegrity();
        
        $this->command->info('✅ GameSeeder tamamlandı!');
    }
    
    /**
     * PUBG Mobile oyununu detaylı ayarlarla oluştur
     */
    private function createPubgGame(): void
    {
        $this->command->info('📝 PUBG Mobile oyunu oluşturuluyor...');
        
        // Önce ID 1'deki kaydı kontrol et (eski PUBG kaydı)
        $existingPubg = Game::find(1);
        
        // Eğer ID 1 yoksa, slug'a göre ara
        if (!$existingPubg) {
            $existingPubg = Game::where('slug', 'pubg-mobile')->orWhere('slug', 'pubg')->first();
        }
        
        if ($existingPubg) {
            $this->command->info("⚠️  Mevcut PUBG kaydı bulundu (ID: {$existingPubg->id}), güncelleniyor...");
            
            // Mevcut kaydı güncelle
            $existingPubg->update([
                'name' => 'PUBG Mobile',
                'slug' => 'pubg',
                'logo' => null, // Logo daha sonra eklenecek
                'icon' => null,
                'description' => 'PUBG Mobile - Battle Royale oyunu. 100 oyuncu, tek kazanan!',
                'status' => 'active',
                'is_active' => true,
                'order' => 1,
                'settings' => [
                    // Tema renkleri
                    'theme_color' => '#FF6B00',
                    'secondary_color' => '#FFB800',
                    
                    // Oyun özellikleri
                    'max_team_size' => 4,
                    'platforms' => ['Android', 'iOS'],
                    
                    // Özellik bayrakları
                    'features' => [
                        'tournaments' => true,
                        'clans' => true,
                        'lfg' => true,
                        'matchmaking' => true,
                        'guides' => true,
                        'community_posts' => true,
                    ],
                    
                    // Oyun modları
                    'game_modes' => [
                        'Classic',
                        'Arcade',
                        'EvoGround',
                        'Arena',
                    ],
                    
                    // Haritalar
                    'maps' => [
                        'Erangel',
                        'Miramar',
                        'Sanhok',
                        'Vikendi',
                        'Livik',
                        'Karakin',
                    ],
                    
                    // Rank sistemi
                    'ranks' => [
                        'Bronze',
                        'Silver',
                        'Gold',
                        'Platinum',
                        'Diamond',
                        'Crown',
                        'Ace',
                        'Conqueror',
                    ],
                ],
            ]);
            
            $pubg = $existingPubg;
            $this->command->info("✅ PUBG Mobile oyunu güncellendi (ID: {$pubg->id})");
        } else {
            // Yeni kayıt oluştur
            $pubg = Game::create([
                'name' => 'PUBG Mobile',
                'slug' => 'pubg',
                'logo' => null,
                'icon' => null,
                'description' => 'PUBG Mobile - Battle Royale oyunu. 100 oyuncu, tek kazanan!',
                'status' => 'active',
                'is_active' => true,
                'order' => 1,
                'settings' => [
                    'theme_color' => '#FF6B00',
                    'secondary_color' => '#FFB800',
                    'max_team_size' => 4,
                    'platforms' => ['Android', 'iOS'],
                    'features' => [
                        'tournaments' => true,
                        'clans' => true,
                        'lfg' => true,
                        'matchmaking' => true,
                        'guides' => true,
                        'community_posts' => true,
                    ],
                    'game_modes' => ['Classic', 'Arcade', 'EvoGround', 'Arena'],
                    'maps' => ['Erangel', 'Miramar', 'Sanhok', 'Vikendi', 'Livik', 'Karakin'],
                    'ranks' => ['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Crown', 'Ace', 'Conqueror'],
                ],
            ]);
            
            $this->command->info("✅ PUBG Mobile oyunu oluşturuldu (ID: {$pubg->id})");
        }
    }
    
    /**
     * Diğer oyunları oluştur (Valorant, COD, LOL, CS:GO)
     */
    private function createOtherGames(): void
    {
        $this->command->info('📝 Diğer oyunlar oluşturuluyor...');
        
        $games = [
            [
                'name' => 'Valorant',
                'slug' => 'valorant',
                'description' => 'Valorant - 5v5 taktiksel FPS oyunu. Karakterler, yetenekler ve strateji!',
                'order' => 2,
                'settings' => [
                    'theme_color' => '#FF4655',
                    'secondary_color' => '#FD4556',
                    'max_team_size' => 5,
                    'platforms' => ['PC'],
                    'features' => [
                        'tournaments' => true,
                        'clans' => true,
                        'lfg' => true,
                        'matchmaking' => true,
                        'guides' => true,
                        'community_posts' => true,
                    ],
                    'game_modes' => ['Unrated', 'Competitive', 'Spike Rush', 'Deathmatch', 'Escalation'],
                    'maps' => ['Bind', 'Haven', 'Split', 'Ascent', 'Icebox', 'Breeze', 'Fracture', 'Pearl', 'Lotus'],
                    'ranks' => ['Iron', 'Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Immortal', 'Radiant'],
                ],
            ],
            [
                'name' => 'Call of Duty Mobile',
                'slug' => 'cod',
                'description' => 'Call of Duty Mobile - Efsanevi FPS serisi mobilde. Multiplayer ve Battle Royale!',
                'order' => 3,
                'settings' => [
                    'theme_color' => '#5C8727',
                    'secondary_color' => '#8BC34A',
                    'max_team_size' => 5,
                    'platforms' => ['Android', 'iOS'],
                    'features' => [
                        'tournaments' => true,
                        'clans' => true,
                        'lfg' => true,
                        'matchmaking' => true,
                        'guides' => true,
                        'community_posts' => true,
                    ],
                    'game_modes' => ['Team Deathmatch', 'Domination', 'Search & Destroy', 'Battle Royale', 'Hardpoint'],
                    'maps' => ['Nuketown', 'Crash', 'Standoff', 'Crossfire', 'Firing Range', 'Summit'],
                    'ranks' => ['Rookie', 'Veteran', 'Elite', 'Pro', 'Master', 'Grandmaster', 'Legendary'],
                ],
            ],
            [
                'name' => 'League of Legends',
                'slug' => 'lol',
                'description' => 'League of Legends - Dünyanın en popüler MOBA oyunu. 5v5 stratejik savaşlar!',
                'order' => 4,
                'settings' => [
                    'theme_color' => '#C89B3C',
                    'secondary_color' => '#0AC8B9',
                    'max_team_size' => 5,
                    'platforms' => ['PC'],
                    'features' => [
                        'tournaments' => true,
                        'clans' => true,
                        'lfg' => true,
                        'matchmaking' => true,
                        'guides' => true,
                        'community_posts' => true,
                    ],
                    'game_modes' => ['Summoner\'s Rift', 'ARAM', 'URF', 'Nexus Blitz', 'Arena'],
                    'maps' => ['Summoner\'s Rift', 'Howling Abyss', 'Twisted Treeline'],
                    'ranks' => ['Iron', 'Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Master', 'Grandmaster', 'Challenger'],
                ],
            ],
            [
                'name' => 'Counter-Strike: Global Offensive',
                'slug' => 'csgo',
                'description' => 'CS:GO - Klasik FPS oyunu. 5v5 taktiksel bomba defüzü ve rekabetçi maçlar!',
                'order' => 5,
                'settings' => [
                    'theme_color' => '#F7931E',
                    'secondary_color' => '#00A8E8',
                    'max_team_size' => 5,
                    'platforms' => ['PC'],
                    'features' => [
                        'tournaments' => true,
                        'clans' => true,
                        'lfg' => true,
                        'matchmaking' => true,
                        'guides' => true,
                        'community_posts' => true,
                    ],
                    'game_modes' => ['Competitive', 'Casual', 'Deathmatch', 'Arms Race', 'Wingman'],
                    'maps' => ['Dust 2', 'Mirage', 'Inferno', 'Nuke', 'Overpass', 'Vertigo', 'Ancient'],
                    'ranks' => ['Silver', 'Gold Nova', 'Master Guardian', 'Distinguished Master Guardian', 'Legendary Eagle', 'Supreme', 'Global Elite'],
                ],
            ],
        ];
        
        foreach ($games as $gameData) {
            $existing = Game::where('slug', $gameData['slug'])->first();
            
            if ($existing) {
                $existing->update($gameData);
                $this->command->info("✅ {$gameData['name']} güncellendi (ID: {$existing->id})");
            } else {
                $game = Game::create($gameData + [
                    'logo' => null,
                    'icon' => null,
                    'status' => 'active',
                    'is_active' => true,
                ]);
                $this->command->info("✅ {$gameData['name']} oluşturuldu (ID: {$game->id})");
            }
        }
    }
    
    /**
     * Mevcut verileri game_id=1 ile güncelle
     * 
     * Bu metod, migration öncesi var olan verileri
     * PUBG Mobile oyununa (game_id=1) atar
     */
    private function updateExistingData(): void
    {
        $this->command->info('🔄 Mevcut veriler güncelleniyor...');
        
        DB::beginTransaction();
        
        try {
            $updates = [];
            
            // Tournaments tablosu
            $tournamentsUpdated = DB::table('tournaments')
                ->whereNull('game_id')
                ->update(['game_id' => 1]);
            $updates['tournaments'] = $tournamentsUpdated;
            
            // Clans tablosu
            $clansUpdated = DB::table('clans')
                ->whereNull('game_id')
                ->update(['game_id' => 1]);
            $updates['clans'] = $clansUpdated;
            
            // LFG Posts tablosu
            $lfgUpdated = DB::table('lfg_posts')
                ->whereNull('game_id')
                ->update(['game_id' => 1]);
            $updates['lfg_posts'] = $lfgUpdated;
            
            // Guide Posts tablosu (nullable)
            $guidesUpdated = DB::table('guide_posts')
                ->whereNull('game_id')
                ->update(['game_id' => 1]);
            $updates['guide_posts'] = $guidesUpdated;
            
            // Community Posts tablosu
            if (Schema::hasColumn('community_posts', 'game_id')) {
                $communityUpdated = DB::table('community_posts')
                    ->whereNull('game_id')
                    ->update(['game_id' => 1]);
                $updates['community_posts'] = $communityUpdated;
            }
            
            // Badges tablosu (nullable - sadece PUBG'ye özel olanlar)
            if (Schema::hasColumn('badges', 'game_id')) {
                $badgesUpdated = DB::table('badges')
                    ->whereNull('game_id')
                    ->where('name', 'LIKE', '%PUBG%')
                    ->orWhere('name', 'LIKE', '%Chicken%')
                    ->orWhere('name', 'LIKE', '%Winner%')
                    ->update(['game_id' => 1]);
                $updates['badges'] = $badgesUpdated;
            }
            
            DB::commit();
            
            // Sonuçları göster
            $this->command->info('✅ Veri güncelleme tamamlandı:');
            foreach ($updates as $table => $count) {
                if ($count > 0) {
                    $this->command->info("   - {$table}: {$count} kayıt güncellendi");
                }
            }
            
            // Log kaydet
            Log::info('GameSeeder: Mevcut veriler game_id=1 ile güncellendi', $updates);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Veri güncelleme hatası: ' . $e->getMessage());
            Log::error('GameSeeder: Veri güncelleme hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
    
    /**
     * Data integrity kontrolü
     * 
     * Migration sonrası veri bütünlüğünü kontrol eder:
     * - Tüm game-specific tablolarda game_id olmalı
     * - Foreign key ilişkileri doğru olmalı
     * - Orphan kayıt olmamalı
     */
    private function verifyDataIntegrity(): void
    {
        $this->command->info('🔍 Data integrity kontrolü yapılıyor...');
        
        $issues = [];
        
        // 1. PUBG oyununun var olduğunu kontrol et
        $pubgGame = Game::where('slug', 'pubg')->first();
        if (!$pubgGame) {
            $issues[] = 'PUBG oyunu bulunamadı!';
        } else {
            $this->command->info("✅ PUBG oyunu mevcut (ID: {$pubgGame->id})");
        }
        
        // 2. Tournaments - game_id NULL kontrolü
        $nullTournaments = DB::table('tournaments')->whereNull('game_id')->count();
        if ($nullTournaments > 0) {
            $issues[] = "Tournaments: {$nullTournaments} kayıtta game_id NULL";
        } else {
            $totalTournaments = DB::table('tournaments')->count();
            $this->command->info("✅ Tournaments: {$totalTournaments} kayıt, hepsi game_id'ye sahip");
        }
        
        // 3. Clans - game_id NULL kontrolü
        $nullClans = DB::table('clans')->whereNull('game_id')->count();
        if ($nullClans > 0) {
            $issues[] = "Clans: {$nullClans} kayıtta game_id NULL";
        } else {
            $totalClans = DB::table('clans')->count();
            $this->command->info("✅ Clans: {$totalClans} kayıt, hepsi game_id'ye sahip");
        }
        
        // 4. LFG Posts - game_id NULL kontrolü
        $nullLfg = DB::table('lfg_posts')->whereNull('game_id')->count();
        if ($nullLfg > 0) {
            $issues[] = "LFG Posts: {$nullLfg} kayıtta game_id NULL";
        } else {
            $totalLfg = DB::table('lfg_posts')->count();
            $this->command->info("✅ LFG Posts: {$totalLfg} kayıt, hepsi game_id'ye sahip");
        }
        
        // 5. Guide Posts - game_id kontrolü (nullable olabilir)
        $totalGuides = DB::table('guide_posts')->count();
        $guidesWithGame = DB::table('guide_posts')->whereNotNull('game_id')->count();
        $this->command->info("✅ Guide Posts: {$totalGuides} kayıt, {$guidesWithGame} tanesi game_id'ye sahip");
        
        // 6. Community Posts - game_id NULL kontrolü (eğer kolon varsa)
        if (Schema::hasColumn('community_posts', 'game_id')) {
            $nullCommunity = DB::table('community_posts')->whereNull('game_id')->count();
            if ($nullCommunity > 0) {
                $issues[] = "Community Posts: {$nullCommunity} kayıtta game_id NULL";
            } else {
                $totalCommunity = DB::table('community_posts')->count();
                $this->command->info("✅ Community Posts: {$totalCommunity} kayıt, hepsi game_id'ye sahip");
            }
        }
        
        // 7. Foreign key ilişkilerini kontrol et
        if ($pubgGame) {
            $orphanTournaments = DB::table('tournaments')
                ->whereNotNull('game_id')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('games')
                        ->whereColumn('games.id', 'tournaments.game_id');
                })
                ->count();
            
            if ($orphanTournaments > 0) {
                $issues[] = "Orphan tournaments: {$orphanTournaments} kayıt geçersiz game_id'ye sahip";
            }
        }
        
        // Sonuçları göster
        if (empty($issues)) {
            $this->command->info('✅ Data integrity kontrolü başarılı! Tüm veriler tutarlı.');
            Log::info('GameSeeder: Data integrity kontrolü başarılı');
        } else {
            $this->command->error('❌ Data integrity sorunları bulundu:');
            foreach ($issues as $issue) {
                $this->command->error("   - {$issue}");
            }
            Log::warning('GameSeeder: Data integrity sorunları', ['issues' => $issues]);
        }
    }
}
