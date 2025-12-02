@extends('admin.layout')

@section('title', 'Oyun Yönetimi')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent mb-2">
                        🎮 Oyun Yönetimi
                    </h1>
                    <p class="text-gray-400">Platform oyunlarını yönetin ve yeni oyunlar ekleyin</p>
                </div>
                <button onclick="document.getElementById('createGameModal').classList.remove('hidden')" 
                    class="px-5 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Yeni Oyun
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-xl shadow-blue-500/20 p-6 text-white transform hover:scale-105 transition-all border border-blue-500/20">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-5xl">🎮</div>
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-bold mb-1">{{ $games->count() }}</p>
                <p class="text-blue-200 text-sm">Toplam Oyun</p>
            </div>

            <div class="bg-gradient-to-br from-green-600 to-emerald-700 rounded-2xl shadow-xl shadow-green-500/20 p-6 text-white transform hover:scale-105 transition-all border border-green-500/20">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-5xl">✅</div>
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-bold mb-1">{{ $games->where('is_active', true)->count() }}</p>
                <p class="text-green-200 text-sm">Aktif Oyun</p>
            </div>


            <div class="bg-gradient-to-br from-orange-600 to-red-700 rounded-2xl shadow-xl shadow-orange-500/20 p-6 text-white transform hover:scale-105 transition-all border border-orange-500/20">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-5xl">📢</div>
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-bold mb-1">{{ $games->sum('lfg_posts_count') }}</p>
                <p class="text-orange-200 text-sm">Toplam İlan</p>
            </div>

            <div class="bg-gradient-to-br from-purple-600 to-pink-700 rounded-2xl shadow-xl shadow-purple-500/20 p-6 text-white transform hover:scale-105 transition-all border border-purple-500/20">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-5xl">👑</div>
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-bold mb-1">{{ $games->sum('clans_count') }}</p>
                <p class="text-purple-200 text-sm">Toplam Klan</p>
            </div>
        </div>

        <!-- Games Table -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-2xl overflow-hidden border border-gray-700/50">
            <div class="p-6 border-b border-gray-700/50 bg-gradient-to-r from-gray-800/80 to-gray-900/80">
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    Oyun Listesi
                </h2>
            </div>

            @if($games->isEmpty())
                <div class="p-16 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-3xl mb-6 border-2 border-blue-500/30">
                        <div class="text-6xl">🎮</div>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Henüz oyun yok</h3>
                    <p class="text-gray-400 mb-6">İlk oyunu ekleyerek başlayın</p>
                    <button onclick="document.getElementById('createGameModal').classList.remove('hidden')" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all">
                        İlk Oyunu Ekle
                    </button>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Oyun</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">İlanlar</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Klanlar</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Rehberler</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Durum</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            @foreach($games as $game)
                            <tr class="hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                            {{ substr($game->name, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-white">{{ $game->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $game->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center border border-orange-500/30">
                                            <span class="text-lg">📢</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-white">{{ number_format($game->lfg_posts_count) }}</div>
                                            <div class="text-xs text-gray-400">ilan</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center border border-purple-500/30">
                                            <span class="text-lg">👑</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-white">{{ number_format($game->clans_count) }}</div>
                                            <div class="text-xs text-gray-400">klan</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center border border-blue-500/30">
                                            <span class="text-lg">📚</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-white">{{ number_format($game->guides_count) }}</div>
                                            <div class="text-xs text-gray-400">rehber</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($game->is_active)
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg bg-green-500/20 text-green-400 border border-green-500/30">
                                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-500/20 text-gray-400 border border-gray-500/30">
                                            <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                            Pasif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick="editGame({{ $game->id }}, '{{ $game->name }}', {{ $game->is_active ? 'true' : 'false' }})"
                                            class="px-3 py-2 bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 rounded-lg font-semibold transition-colors border border-blue-500/30">
                                            Düzenle
                                        </button>
                                        <form action="{{ route('admin.games.destroy', $game->id) }}" method="POST" class="inline" 
                                            onsubmit="return confirm('Bu oyunu silmek istediğinizden emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-400 rounded-lg font-semibold transition-colors border border-red-500/30">
                                                Sil
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>


<!-- Create Game Modal -->
<div id="createGameModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="if(event.target === this) this.classList.add('hidden')">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-2xl max-w-md w-full border border-gray-700 transform transition-all" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17zM15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">Yeni Oyun</h3>
                </div>
                <button onclick="document.getElementById('createGameModal').classList.add('hidden')" 
                    class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.games.store') }}" method="POST" class="p-6 space-y-5">
            @csrf
            
            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">Oyun Adı *</label>
                <input type="text" name="name" required 
                    class="w-full px-4 py-3 bg-gray-800 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                    placeholder="Örn: PUBG Mobile">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">Slug (URL) *</label>
                <input type="text" name="slug" required 
                    class="w-full px-4 py-3 bg-gray-800 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                    placeholder="Örn: pubg-mobile">
                <p class="text-xs text-gray-500 mt-2">Küçük harf, tire ile ayırın</p>
            </div>

            <div class="flex items-center gap-3 p-4 bg-gray-800/50 rounded-xl border border-gray-700">
                <input type="checkbox" name="is_active" id="create_is_active" value="1" checked
                    class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-2 focus:ring-blue-500">
                <label for="create_is_active" class="text-sm font-medium text-gray-300">Oyun Aktif</label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-700">
                <button type="button" onclick="document.getElementById('createGameModal').classList.add('hidden')"
                    class="px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-semibold transition-colors">
                    İptal
                </button>
                <button type="submit" 
                    class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-semibold shadow-lg transition-all">
                    Oyun Ekle
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Game Modal -->
<div id="editGameModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="if(event.target === this) this.classList.add('hidden')">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-2xl max-w-md w-full border border-gray-700 transform transition-all" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-600 to-red-600 p-6 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">Oyun Düzenle</h3>
                </div>
                <button onclick="document.getElementById('editGameModal').classList.add('hidden')" 
                    class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Form -->
        <form id="editGameForm" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">Oyun Adı *</label>
                <input type="text" name="name" id="edit_name" required 
                    class="w-full px-4 py-3 bg-gray-800 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all">
            </div>

            <div class="flex items-center gap-3 p-4 bg-gray-800/50 rounded-xl border border-gray-700">
                <input type="checkbox" name="is_active" id="edit_is_active" value="1" 
                    class="w-5 h-5 text-orange-600 bg-gray-700 border-gray-600 rounded focus:ring-2 focus:ring-orange-500">
                <label for="edit_is_active" class="text-sm font-medium text-gray-300">Oyun Aktif</label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-700">
                <button type="button" onclick="document.getElementById('editGameModal').classList.add('hidden')"
                    class="px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-semibold transition-colors">
                    İptal
                </button>
                <button type="submit" 
                    class="px-5 py-2.5 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white rounded-xl font-semibold shadow-lg transition-all">
                    Güncelle
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editGame(id, name, isActive) {
    document.getElementById('editGameForm').action = `/admin/games/${id}`;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_is_active').checked = isActive;
    document.getElementById('editGameModal').classList.remove('hidden');
}
</script>
@endsection
