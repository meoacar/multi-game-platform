@extends('layouts.admin')

@section('title', 'Rozet Yönetimi')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Rozet Yönetimi</h1>
            <p class="text-gray-600 mt-1">Toplam {{ $badges->total() }} rozet</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.badges.stats') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                📊 İstatistikler
            </a>
            <a href="{{ route('admin.badges.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                ➕ Yeni Rozet
            </a>
        </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.badges.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Arama -->
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Rozet ara..." 
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Kategori -->
            <div>
                <select name="category" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Tüm Kategoriler</option>
                    <option value="gameplay" {{ request('category') == 'gameplay' ? 'selected' : '' }}>Oyun</option>
                    <option value="social" {{ request('category') == 'social' ? 'selected' : '' }}>Sosyal</option>
                    <option value="content" {{ request('category') == 'content' ? 'selected' : '' }}>İçerik</option>
                    <option value="special" {{ request('category') == 'special' ? 'selected' : '' }}>Özel</option>
                    <option value="activity" {{ request('category') == 'activity' ? 'selected' : '' }}>Aktivite</option>
                    <option value="moderation" {{ request('category') == 'moderation' ? 'selected' : '' }}>Moderasyon</option>
                </select>
            </div>

            <!-- Nadirlik -->
            <div>
                <select name="rarity" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Tüm Nadirlikler</option>
                    <option value="common" {{ request('rarity') == 'common' ? 'selected' : '' }}>Yaygın</option>
                    <option value="rare" {{ request('rarity') == 'rare' ? 'selected' : '' }}>Nadir</option>
                    <option value="epic" {{ request('rarity') == 'epic' ? 'selected' : '' }}>Epik</option>
                    <option value="legendary" {{ request('rarity') == 'legendary' ? 'selected' : '' }}>Efsanevi</option>
                </select>
            </div>

            <!-- Durum -->
            <div>
                <select name="is_active" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Tüm Durumlar</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>

            <!-- Butonlar -->
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Filtrele
                </button>
                <a href="{{ route('admin.badges.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Temizle
                </a>
            </div>
        </form>
    </div>

    <!-- Rozet Listesi -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rozet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nadirlik</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">XP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sıra</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($badges as $badge)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">{{ $badge->icon }}</span>
                            <div>
                                <div class="font-medium text-gray-900">{{ $badge->name }}</div>
                                <div class="text-sm text-gray-500">{{ $badge->description }}</div>
                                @if($badge->is_hidden)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 mt-1">
                                    🔒 Gizli
                                </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $categoryColors = [
                                'gameplay' => 'bg-blue-100 text-blue-800',
                                'social' => 'bg-green-100 text-green-800',
                                'content' => 'bg-yellow-100 text-yellow-800',
                                'special' => 'bg-purple-100 text-purple-800',
                                'activity' => 'bg-red-100 text-red-800',
                                'moderation' => 'bg-gray-100 text-gray-800',
                            ];
                            $categoryNames = [
                                'gameplay' => 'Oyun',
                                'social' => 'Sosyal',
                                'content' => 'İçerik',
                                'special' => 'Özel',
                                'activity' => 'Aktivite',
                                'moderation' => 'Moderasyon',
                            ];
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $categoryColors[$badge->category] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $categoryNames[$badge->category] ?? $badge->category }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $rarityColors = [
                                'common' => 'bg-gray-100 text-gray-800',
                                'rare' => 'bg-blue-100 text-blue-800',
                                'epic' => 'bg-purple-100 text-purple-800',
                                'legendary' => 'bg-yellow-100 text-yellow-800',
                            ];
                            $rarityNames = [
                                'common' => 'Yaygın',
                                'rare' => 'Nadir',
                                'epic' => 'Epik',
                                'legendary' => 'Efsanevi',
                            ];
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $rarityColors[$badge->rarity] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $rarityNames[$badge->rarity] ?? $badge->rarity }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $badge->xp_required ?? 0 }} XP
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <form action="{{ route('admin.badges.toggle-active', $badge) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badge->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $badge->is_active ? 'Aktif' : 'Pasif' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $badge->sort_order }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.badges.edit', $badge) }}" class="text-blue-600 hover:text-blue-900">
                                Düzenle
                            </a>
                            <form action="{{ route('admin.badges.destroy', $badge) }}" method="POST" class="inline" onsubmit="return confirm('Bu rozeti silmek istediğinizden emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    Sil
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        Rozet bulunamadı.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $badges->links() }}
    </div>
</div>
@endsection
