@extends('admin.layout')

@section('title', 'Oyun İstatistikleri Düzenle')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
        <a href="{{ route('admin.profiles.index') }}" class="hover:text-orange-600">Profiller</a>
        <span>/</span>
        <a href="{{ route('admin.profiles.edit', $profile->id) }}" class="hover:text-orange-600">{{ $profile->user->name }}</a>
        <span>/</span>
        <span>Oyun İstatistikleri</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Oyun İstatistikleri Düzenle</h1>
    <p class="text-gray-600 mt-1">{{ $profile->user->name }} - {{ $profile->nickname ?? 'Nickname yok' }}</p>
</div>

<!-- Mevcut İstatistikler -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white">
        <div class="text-sm opacity-90">K/D Oranı</div>
        <div class="text-3xl font-bold mt-1">{{ number_format($profile->kd_ratio, 2) }}</div>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-4 text-white">
        <div class="text-sm opacity-90">Win Rate</div>
        <div class="text-3xl font-bold mt-1">{{ number_format($profile->win_rate, 2) }}%</div>
    </div>
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-4 text-white">
        <div class="text-sm opacity-90">Headshot Rate</div>
        <div class="text-3xl font-bold mt-1">{{ number_format($profile->headshot_rate, 2) }}%</div>
    </div>
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-4 text-white">
        <div class="text-sm opacity-90">Toplam Maç</div>
        <div class="text-3xl font-bold mt-1">{{ number_format($profile->matches_played) }}</div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('admin.profiles.statistics.update', $profile->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Maç İstatistikleri</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Matches Played -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Oynanan Maç Sayısı
                    </label>
                    <input type="number" name="matches_played" value="{{ old('matches_played', $profile->matches_played) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('matches_played') border-red-500 @enderror">
                    @error('matches_played')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Wins -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kazanılan Maç
                    </label>
                    <input type="number" name="wins" value="{{ old('wins', $profile->wins) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('wins') border-red-500 @enderror">
                    @error('wins')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Win Rate otomatik hesaplanacak</p>
                </div>

                <!-- Top 10 Finishes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        İlk 10'a Girme
                    </label>
                    <input type="number" name="top_10_finishes" value="{{ old('top_10_finishes', $profile->top_10_finishes) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('top_10_finishes') border-red-500 @enderror">
                    @error('top_10_finishes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">⚔️ Savaş İstatistikleri</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Kills -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Toplam Öldürme
                    </label>
                    <input type="number" name="kills" value="{{ old('kills', $profile->kills) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('kills') border-red-500 @enderror">
                    @error('kills')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deaths -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Toplam Ölüm
                    </label>
                    <input type="number" name="deaths" value="{{ old('deaths', $profile->deaths) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('deaths') border-red-500 @enderror">
                    @error('deaths')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">K/D oranı otomatik hesaplanacak</p>
                </div>

                <!-- Headshots -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kafa Vuruşu
                    </label>
                    <input type="number" name="headshots" value="{{ old('headshots', $profile->headshots) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('headshots') border-red-500 @enderror">
                    @error('headshots')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Headshot rate otomatik hesaplanacak</p>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Diğer İstatistikler</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Damage Dealt -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Verilen Hasar
                    </label>
                    <input type="number" name="damage_dealt" value="{{ old('damage_dealt', $profile->damage_dealt) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('damage_dealt') border-red-500 @enderror">
                    @error('damage_dealt')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Survival Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Hayatta Kalma Süresi (dakika)
                    </label>
                    <input type="number" name="survival_time" value="{{ old('survival_time', $profile->survival_time) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('survival_time') border-red-500 @enderror">
                    @error('survival_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Longest Kill -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        En Uzak Öldürme (metre)
                    </label>
                    <input type="number" name="longest_kill" value="{{ old('longest_kill', $profile->longest_kill) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('longest_kill') border-red-500 @enderror">
                    @error('longest_kill')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Bilgilendirme -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-medium mb-1">Otomatik Hesaplama</p>
                    <ul class="list-disc list-inside space-y-1 text-blue-700">
                        <li><strong>K/D Oranı:</strong> Kills ÷ Deaths (ölüm 0 ise K/D = Kills)</li>
                        <li><strong>Win Rate:</strong> (Wins ÷ Matches Played) × 100</li>
                        <li><strong>Headshot Rate:</strong> (Headshots ÷ Kills) × 100</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Butonlar -->
        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg hover:from-orange-600 hover:to-red-600 transition-all">
                💾 İstatistikleri Güncelle
            </button>
            <a href="{{ route('admin.profiles.edit', $profile->id) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                ← Geri Dön
            </a>
        </div>
    </form>
</div>
@endsection
