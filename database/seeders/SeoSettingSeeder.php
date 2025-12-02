<?php

namespace Database\Seeders;

use App\Models\SeoSetting;
use Illuminate\Database\Seeder;

class SeoSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seoSettings = [
            [
                'page_type' => 'home',
                'title' => 'PUBG Mobile Topluluk Platformu - Takım Bul, Klan Kur',
                'description' => 'Türkiye\'nin en büyük PUBG Mobile topluluğu. Takım ara, klan kur, turnuvalara katıl. Binlerce oyuncu ile tanış ve PUBG Mobile deneyimini zirveye taşı!',
                'keywords' => 'pubg mobile, pubg mobile türkiye, pubg klan, pubg takım, lfg pubg, pubg mobile topluluk, pubg turnuva',
                'og_title' => 'PUBG Mobile Topluluk Platformu - Türkiye\'nin En Büyük Topluluğu',
                'og_description' => 'Binlerce oyuncu ile takım kur, klan oluştur, turnuvalara katıl!',
                'og_image' => 'https://example.com/images/og-home.jpg',
                'twitter_title' => 'PUBG Mobile Topluluk Platformu',
                'twitter_description' => 'Türkiye\'nin en büyük PUBG Mobile topluluğu!',
                'twitter_image' => 'https://example.com/images/twitter-home.jpg',
                'is_active' => true,
            ],
            [
                'page_type' => 'clans',
                'title' => 'Klanlar - PUBG Mobile Topluluk',
                'description' => 'PUBG Mobile için aktif klanlar. Klan bul, başvur veya kendi klanını oluştur. Doğrulanmış klanlar, güçlü takımlar ve rekabetçi oyuncular seni bekliyor!',
                'keywords' => 'pubg mobile klan, pubg klan ara, pubg klan kur, pubg mobile türkiye klan, pubg klan başvuru',
                'og_title' => 'PUBG Mobile Klanlar - Aktif Klanlar',
                'og_description' => 'Türkiye\'nin en büyük PUBG Mobile klan platformu. Senin için en uygun klanı bul!',
                'og_image' => 'https://example.com/images/og-clans.jpg',
                'twitter_title' => 'PUBG Mobile Klanlar',
                'twitter_description' => 'Aktif PUBG Mobile klanları keşfet!',
                'twitter_image' => 'https://example.com/images/twitter-clans.jpg',
                'is_active' => true,
            ],
            [
                'page_type' => 'lfg',
                'title' => 'İlanlar - PUBG Mobile Takım Ara',
                'description' => 'PUBG Mobile için takım arkadaşı ara. Aktif LFG ilanları, seviyene uygun oyuncular, hemen takım kur ve oyuna başla!',
                'keywords' => 'pubg mobile lfg, pubg takım ara, pubg mobile takım, pubg squad, pubg duo',
                'og_title' => 'PUBG Mobile İlanlar - Takım Arkadaşı Bul',
                'og_description' => 'Seviyene uygun takım arkadaşları bul, hemen oyuna başla!',
                'og_image' => 'https://example.com/images/og-lfg.jpg',
                'twitter_title' => 'PUBG Mobile İlanlar',
                'twitter_description' => 'Takım arkadaşı bul, oyuna başla!',
                'twitter_image' => 'https://example.com/images/twitter-lfg.jpg',
                'is_active' => true,
            ],
        ];

        foreach ($seoSettings as $setting) {
            SeoSetting::updateOrCreate(
                ['page_type' => $setting['page_type']],
                $setting
            );
        }
    }
}
