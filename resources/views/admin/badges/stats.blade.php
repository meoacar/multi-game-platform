@extends('layouts.admin')

@section('title', 'Rozet İstatistikleri')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Rozet İstatistikleri</h1>
            <p class="text-gray-600 mt-1">Rozet kullanım ve dağılım istatistikleri</p>
        </div>
        <a href="{{ route('admin.badges.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            ← Geri Dön
        </a>
    </div>

    <!-- Genel İstatistikler -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-600 mb-1">Toplam Rozet</div>
            <div class="text-3xl font-bold text-gray-900">{{ $totalBadges }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-600 mb-1">Aktif Rozet</div>
            <div class="text-3xl font-bold text-green-600">{{ $activeBadges }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-sm text-gray-600 mb-1">Gizli Rozet</div>
            <div class="text-3xl font-bold text-purple-600">{{ $hiddenBadges }}</div>
        </div>
    </div>

    <!-- Kategoriye Göre Dağılım -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Kategoriye Göre Dağılım</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @php
                $categoryNames = [
                    'gameplay' => 'Oyun',
                    'social' => 'Sosyal',
                    'content' => 'İçerik',
                    'special' => 'Özel',
                    'activity' => 'Aktivite',
                    'moderation' => 'Moderasyon',
                ];
                $categoryIcons = [
                    'gameplay' => '🎮',
                    'social' => '👥',
                    'content' => '📝',
                    'special' => '⭐',
                    'activity' => '🔥',
                    'moderation' => '🛡️',
                ];
            @endphp
            @foreach($byCategory as $cat)
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-2xl mb-1">{{ $categoryIcons[$cat->category] ?? '📌' }}</div>
                        <div class="text-sm text-gray-600">{{ $categoryNames[$cat->category] ?? $cat->category }}</div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $cat->count }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Nadirliğe Göre Dağılım -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Nadirliğe Göre Dağılım</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $rarityNames = [
                    'common' => 'Yaygın',
                    'rare' => 'Nadir',
                    'epic' => 'Epik',
                    'legendary' => 'Efsanevi',
                ];
                $rarityColors = [
                    'common' => 'text-gray-600',
                    'rare' => 'text-blue-600',
                    'epic' => 'text-purple-600',
                    'legendary' => 'text-yellow-600',
                ];
            @endphp
            @foreach($byRarity as $rar)
            <div class="border rounded-lg p-4">
                <div class="text-sm text-gray-600 mb-1">{{ $rarityNames[$rar->rarity] ?? $rar->rarity }}</div>
                <div class="text-3xl font-bold {{ $rarityColors[$rar->rarity] ?? 'text-gray-900' }}">{{ $rar->count }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- En Çok Açılan Rozetler -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">En Çok Açılan Rozetler (Top 10)</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sıra</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rozet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nadirlik</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Açan Kullanıcı</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($mostUnlocked as $index => $badge)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">{{ $badge->icon }}</span>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $badge->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $badge->description }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $categoryNames[$badge->category] ?? $badge->category }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $rarityNames[$badge->rarity] ?? $badge->rarity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-green-600">
                            {{ $badge->users_count }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- En Az Açılan Rozetler -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">En Az Açılan Rozetler (Top 10)</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sıra</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rozet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nadirlik</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Açan Kullanıcı</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($leastUnlocked as $index => $badge)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">{{ $badge->icon }}</span>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $badge->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $badge->description }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $categoryNames[$badge->category] ?? $badge->category }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $rarityNames[$badge->rarity] ?? $badge->rarity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-red-600">
                            {{ $badge->users_count }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
