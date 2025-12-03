<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;

class GameRanksSeeder extends Seeder
{
    /**
     * Oyunlara özel rütbe sistemlerini ekle
     */
    public function run(): void
    {
        $this->command->info('🎮 Oyunlara özel rütbe sistemleri ekleniyor...');

        // PUBG Mobile Rütbeleri
        $pubg = Game::where('slug', 'pubg')->first();
        if ($pubg) {
            $settings = is_array($pubg->settings) ? $pubg->settings : json_decode($pubg->settings, true) ?? [];
            $settings['ranks'] = [
                'Bronze V', 'Bronze IV', 'Bronze III', 'Bronze II', 'Bronze I',
                'Silver V', 'Silver IV', 'Silver III', 'Silver II', 'Silver I',
                'Gold V', 'Gold IV', 'Gold III', 'Gold II', 'Gold I',
                'Platinum V', 'Platinum IV', 'Platinum III', 'Platinum II', 'Platinum I',
                'Diamond V', 'Diamond IV', 'Diamond III', 'Diamond II', 'Diamond I',
                'Crown V', 'Crown IV', 'Crown III', 'Crown II', 'Crown I',
                'Ace', 'Ace Master', 'Ace Dominator',
                'Conqueror'
            ];
            $settings['game_modes'] = ['Classic', 'Arena', 'TDM', 'Payload'];
            $settings['maps'] = ['Erangel', 'Miramar', 'Sanhok', 'Vikendi', 'Livik'];
            $pubg->settings = $settings;
            $pubg->save();
            $this->command->info('✅ PUBG Mobile rütbeleri eklendi');
        }

        // Call of Duty Mobile Rütbeleri
        $cod = Game::where('slug', 'cod')->first();
        if ($cod) {
            $settings = is_array($cod->settings) ? $cod->settings : json_decode($cod->settings, true) ?? [];
            $settings['ranks'] = [
                'Rookie I', 'Rookie II', 'Rookie III', 'Rookie IV', 'Rookie V',
                'Veteran I', 'Veteran II', 'Veteran III', 'Veteran IV', 'Veteran V',
                'Elite I', 'Elite II', 'Elite III', 'Elite IV', 'Elite V',
                'Pro I', 'Pro II', 'Pro III', 'Pro IV', 'Pro V',
                'Master I', 'Master II', 'Master III', 'Master IV', 'Master V',
                'Grand Master I', 'Grand Master II', 'Grand Master III', 'Grand Master IV', 'Grand Master V',
                'Legendary'
            ];
            $settings['game_modes'] = ['Multiplayer', 'Battle Royale', 'Zombies'];
            $settings['maps'] = ['Nuketown', 'Crash', 'Standoff', 'Crossfire', 'Isolated', 'Blackout'];
            $cod->settings = $settings;
            $cod->save();
            $this->command->info('✅ COD Mobile rütbeleri eklendi');
        }

        // Valorant Rütbeleri
        $valorant = Game::where('slug', 'valorant')->first();
        if ($valorant) {
            $settings = is_array($valorant->settings) ? $valorant->settings : json_decode($valorant->settings, true) ?? [];
            $settings['ranks'] = [
                'Iron 1', 'Iron 2', 'Iron 3',
                'Bronze 1', 'Bronze 2', 'Bronze 3',
                'Silver 1', 'Silver 2', 'Silver 3',
                'Gold 1', 'Gold 2', 'Gold 3',
                'Platinum 1', 'Platinum 2', 'Platinum 3',
                'Diamond 1', 'Diamond 2', 'Diamond 3',
                'Ascendant 1', 'Ascendant 2', 'Ascendant 3',
                'Immortal 1', 'Immortal 2', 'Immortal 3',
                'Radiant'
            ];
            $settings['game_modes'] = ['Unrated', 'Competitive', 'Spike Rush', 'Deathmatch'];
            $settings['maps'] = ['Bind', 'Haven', 'Split', 'Ascent', 'Icebox', 'Breeze', 'Fracture', 'Pearl', 'Lotus'];
            $settings['agents'] = ['Jett', 'Phoenix', 'Sage', 'Sova', 'Viper', 'Cypher', 'Reyna', 'Killjoy', 'Breach', 'Omen'];
            $valorant->settings = $settings;
            $valorant->save();
            $this->command->info('✅ Valorant rütbeleri eklendi');
        }

        // League of Legends Rütbeleri
        $lol = Game::where('slug', 'lol')->first();
        if ($lol) {
            $settings = is_array($lol->settings) ? $lol->settings : json_decode($lol->settings, true) ?? [];
            $settings['ranks'] = [
                'Iron IV', 'Iron III', 'Iron II', 'Iron I',
                'Bronze IV', 'Bronze III', 'Bronze II', 'Bronze I',
                'Silver IV', 'Silver III', 'Silver II', 'Silver I',
                'Gold IV', 'Gold III', 'Gold II', 'Gold I',
                'Platinum IV', 'Platinum III', 'Platinum II', 'Platinum I',
                'Diamond IV', 'Diamond III', 'Diamond II', 'Diamond I',
                'Master',
                'Grandmaster',
                'Challenger'
            ];
            $settings['game_modes'] = ['Summoner\'s Rift', 'ARAM', 'TFT', 'Arena'];
            $settings['roles'] = ['Top', 'Jungle', 'Mid', 'ADC', 'Support'];
            $lol->settings = $settings;
            $lol->save();
            $this->command->info('✅ League of Legends rütbeleri eklendi');
        }

        // CS:GO Rütbeleri
        $csgo = Game::where('slug', 'csgo')->first();
        if ($csgo) {
            $settings = is_array($csgo->settings) ? $csgo->settings : json_decode($csgo->settings, true) ?? [];
            $settings['ranks'] = [
                'Silver I', 'Silver II', 'Silver III', 'Silver IV', 'Silver Elite', 'Silver Elite Master',
                'Gold Nova I', 'Gold Nova II', 'Gold Nova III', 'Gold Nova Master',
                'Master Guardian I', 'Master Guardian II', 'Master Guardian Elite', 'Distinguished Master Guardian',
                'Legendary Eagle', 'Legendary Eagle Master',
                'Supreme Master First Class',
                'The Global Elite'
            ];
            $settings['game_modes'] = ['Competitive', 'Casual', 'Deathmatch', 'Arms Race', 'Wingman'];
            $settings['maps'] = ['Dust II', 'Mirage', 'Inferno', 'Nuke', 'Overpass', 'Vertigo', 'Ancient'];
            $csgo->settings = $settings;
            $csgo->save();
            $this->command->info('✅ CS:GO rütbeleri eklendi');
        }

        $this->command->info('🎉 Tüm oyunların rütbe sistemleri başarıyla eklendi!');
    }
}
