<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Widget;

class WidgetSeeder extends Seeder
{
    /**
     * Örnek widget'lar oluştur
     */
    public function run(): void
    {
        $widgets = [
            [
                'title' => 'Hoş Geldiniz',
                'slug' => 'hos-geldiniz',
                'type' => 'html',
                'location' => 'sidebar_right',
                'content' => '<div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-6 rounded-lg shadow-lg">
    <h3 class="text-xl font-bold mb-2">🎮 Hoş Geldiniz!</h3>
    <p class="text-sm">PUBG Mobile Türkiye topluluğuna hoş geldiniz. Takım arkadaşı bul, klan oluştur ve rehberlerle gelişin!</p>
</div>',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Topluluk İstatistikleri',
                'slug' => 'topluluk-istatistikleri',
                'type' => 'html',
                'location' => 'sidebar_right',
                'content' => '<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <h3 class="text-lg font-bold text-gray-900 mb-4">📊 Topluluk İstatistikleri</h3>
    <div class="space-y-3">
        <div class="flex justify-between items-center">
            <span class="text-gray-600">Toplam Üye</span>
            <span class="font-bold text-blue-600">1,234</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-600">Aktif Klan</span>
            <span class="font-bold text-green-600">56</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-600">LFG İlanı</span>
            <span class="font-bold text-purple-600">89</span>
        </div>
    </div>
</div>',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Discord Sunucumuz',
                'slug' => 'discord-sunucumuz',
                'type' => 'html',
                'location' => 'sidebar_right',
                'content' => '<div class="bg-indigo-600 text-white p-6 rounded-lg shadow-lg">
    <div class="flex items-center gap-3 mb-3">
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
            <path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
        </svg>
        <h3 class="text-lg font-bold">Discord\'a Katıl!</h3>
    </div>
    <p class="text-sm text-indigo-100 mb-4">Topluluğumuzla anlık iletişim kur, etkinliklere katıl!</p>
    <a href="#" class="block w-full text-center bg-white text-indigo-600 font-semibold py-2 px-4 rounded-lg hover:bg-indigo-50 transition">
        Katıl
    </a>
</div>',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Son Rehberler',
                'slug' => 'son-rehberler',
                'type' => 'recent_content',
                'location' => 'sidebar_right',
                'content' => null,
                'settings' => [
                    'limit' => 5,
                    'content_type' => 'guides',
                ],
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Hızlı Linkler',
                'slug' => 'hizli-linkler',
                'type' => 'html',
                'location' => 'footer_1',
                'content' => '<div>
    <h4 class="text-white font-bold mb-4">Hızlı Linkler</h4>
    <ul class="space-y-2 text-gray-300">
        <li><a href="#" class="hover:text-white transition">Ana Sayfa</a></li>
        <li><a href="#" class="hover:text-white transition">Takım Bul</a></li>
        <li><a href="#" class="hover:text-white transition">Klanlar</a></li>
        <li><a href="#" class="hover:text-white transition">Rehberler</a></li>
    </ul>
</div>',
                'order' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($widgets as $widget) {
            Widget::create($widget);
        }

        $this->command->info('✅ ' . count($widgets) . ' örnek widget oluşturuldu!');
    }
}
