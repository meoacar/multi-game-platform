@extends('layouts.app')

@section('title', 'Topluluk - PUBG Mobile')

@section('content')
<!-- PUBG Mobile Arka Plan -->
<div class="fixed inset-0 z-0 overflow-hidden bg-black">
    <div class="absolute inset-0" 
         style="background-image: url('/arkaplan/1.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.12;">
    </div>
    
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-purple-900/15 via-pink-900/15 to-red-900/15"></div>
    
    <!-- Animasyonlu Işık Efektleri -->
    <div class="absolute inset-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-500/5 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-pink-500/5 rounded-full blur-3xl animate-float-delayed"></div>
    </div>
</div>

<div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-black bg-gradient-to-r from-purple-500 to-pink-600 bg-clip-text text-transparent mb-2">💬 Topluluk</h1>
            <p class="text-gray-400 text-lg">Oyuncularla tanış, deneyimlerini paylaş ve sohbet et</p>
        </div>
        @auth
            <a href="{{ route('community.create') }}" class="bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 transition-all transform hover:scale-105 whitespace-nowrap">
                ✍️ Yeni Gönderi
            </a>
        @endauth
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sol Sidebar - Filtreler & İstatistikler -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Hızlı İstatistikler -->
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-6">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                    <span class="text-2xl mr-2">📊</span>
                    Topluluk İstatistikleri
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Toplam Gönderi</span>
                        <span class="text-white font-bold">{{ $posts->total() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Bugün Paylaşılan</span>
                        <span class="text-green-400 font-bold">{{ $todayPosts ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Aktif Üyeler</span>
                        <span class="text-purple-400 font-bold">{{ $activeUsers ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Kategori Filtreleri -->
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-6">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                    <span class="text-2xl mr-2">🏷️</span>
                    Kategoriler
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('community.index') }}" 
                       class="block px-4 py-3 rounded-xl transition-all {{ !request('type') ? 'bg-gradient-to-r from-purple-500 to-pink-600 text-white shadow-lg' : 'bg-white/5 text-gray-300 hover:bg-white/10' }}">
                        <span class="font-semibold">🌟 Tümü</span>
                    </a>
                    <a href="{{ route('community.index', ['type' => 'intro']) }}" 
                       class="block px-4 py-3 rounded-xl transition-all {{ request('type') == 'intro' ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white shadow-lg' : 'bg-white/5 text-gray-300 hover:bg-white/10' }}">
                        <span class="font-semibold">👋 Tanışma</span>
                    </a>
                    <a href="{{ route('community.index', ['type' => 'general']) }}" 
                       class="block px-4 py-3 rounded-xl transition-all {{ request('type') == 'general' ? 'bg-gradient-to-r from-blue-500 to-cyan-600 text-white shadow-lg' : 'bg-white/5 text-gray-300 hover:bg-white/10' }}">
                        <span class="font-semibold">💬 Genel Sohbet</span>
                    </a>
                    <a href="{{ route('community.index', ['type' => 'question']) }}" 
                       class="block px-4 py-3 rounded-xl transition-all {{ request('type') == 'question' ? 'bg-gradient-to-r from-orange-500 to-red-600 text-white shadow-lg' : 'bg-white/5 text-gray-300 hover:bg-white/10' }}">
                        <span class="font-semibold">❓ Soru & Cevap</span>
                    </a>
                    <a href="{{ route('community.index', ['type' => 'achievement']) }}" 
                       class="block px-4 py-3 rounded-xl transition-all {{ request('type') == 'achievement' ? 'bg-gradient-to-r from-yellow-500 to-amber-600 text-white shadow-lg' : 'bg-white/5 text-gray-300 hover:bg-white/10' }}">
                        <span class="font-semibold">🏆 Başarılar</span>
                    </a>
                </div>
            </div>

            <!-- Popüler Etiketler -->
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-6">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                    <span class="text-2xl mr-2">🔥</span>
                    Trend Konular
                </h3>
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1.5 bg-purple-500/20 text-purple-300 rounded-full text-sm font-semibold border border-purple-500/30">#pubgmobile</span>
                    <span class="px-3 py-1.5 bg-pink-500/20 text-pink-300 rounded-full text-sm font-semibold border border-pink-500/30">#takımarıyorum</span>
                    <span class="px-3 py-1.5 bg-blue-500/20 text-blue-300 rounded-full text-sm font-semibold border border-blue-500/30">#ipuçları</span>
                    <span class="px-3 py-1.5 bg-green-500/20 text-green-300 rounded-full text-sm font-semibold border border-green-500/30">#yeniyim</span>
                    <span class="px-3 py-1.5 bg-orange-500/20 text-orange-300 rounded-full text-sm font-semibold border border-orange-500/30">#turnuva</span>
                </div>
            </div>
        </div>

        <!-- Ana İçerik - Gönderiler -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Arama & Sıralama -->
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-4">
                <form method="GET" action="{{ route('community.index') }}" class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Gönderi ara..." class="w-full bg-white/10 border-2 border-white/20 text-white placeholder-gray-400 rounded-xl px-4 py-2.5 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-all">
                    </div>
                    <select name="sort" class="bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-2.5 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-all">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>🕐 En Yeni</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>🔥 Popüler</option>
                        <option value="most_commented" {{ request('sort') == 'most_commented' ? 'selected' : '' }}>💬 En Çok Yorumlanan</option>
                    </select>
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg transition-all transform hover:scale-105">
                        Ara
                    </button>
                </form>
            </div>

            <!-- Gönderi Listesi -->
            <div class="space-y-4">
                @forelse($posts as $post)
                    <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl hover:shadow-purple-500/20 transition-all duration-300 border border-white/10 hover:border-purple-500/50 overflow-hidden" x-data="{ liked: false }">
                        <!-- Gönderi Header -->
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('profile.show', $post->user->id) }}" class="relative group">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($post->user->name) }}&background=8B5CF6&color=fff" 
                                             class="w-12 h-12 rounded-xl ring-2 ring-purple-500/50 group-hover:ring-purple-500 transition-all">
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-gray-900"></div>
                                    </a>
                                    <div>
                                        <a href="{{ route('profile.show', $post->user->id) }}" class="font-bold text-white hover:text-purple-400 transition-colors">
                                            {{ $post->user->name }}
                                        </a>
                                        <div class="flex items-center space-x-2 text-xs text-gray-400">
                                            <span>{{ $post->created_at->diffForHumans() }}</span>
                                            @if($post->user->profile && $post->user->profile->pubg_id)
                                                <span>•</span>
                                                <span class="text-purple-400">Level {{ $post->user->level ?? 1 }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Kategori Badge -->
                                <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $post->type == 'intro' ? 'bg-gradient-to-r from-green-500 to-emerald-600' : ($post->type == 'question' ? 'bg-gradient-to-r from-orange-500 to-red-600' : ($post->type == 'achievement' ? 'bg-gradient-to-r from-yellow-500 to-amber-600' : 'bg-gradient-to-r from-blue-500 to-cyan-600')) }} text-white shadow-lg">
                                    {{ $post->type == 'intro' ? '👋 Tanışma' : ($post->type == 'question' ? '❓ Soru' : ($post->type == 'achievement' ? '🏆 Başarı' : '💬 Genel')) }}
                                </span>
                            </div>

                            <!-- İçerik -->
                            <div class="mb-4">
                                <p class="text-gray-300 text-base leading-relaxed">
                                    {{ Str::limit($post->content, 250) }}
                                </p>
                                @if(strlen($post->content) > 250)
                                    <a href="{{ route('community.show', $post->id) }}" class="text-purple-400 hover:text-purple-300 text-sm font-semibold mt-2 inline-block">
                                        Devamını oku →
                                    </a>
                                @endif
                            </div>

                            <!-- Etkileşim Butonları -->
                            <div class="flex items-center justify-between pt-4 border-t border-white/10">
                                <div class="flex items-center space-x-4">
                                    <!-- Beğeni -->
                                    <button @click="liked = !liked" class="flex items-center space-x-2 px-4 py-2 rounded-xl transition-all" :class="liked ? 'bg-red-500/20 text-red-400' : 'bg-white/5 text-gray-400 hover:bg-white/10'">
                                        <svg class="w-5 h-5" :class="liked ? 'fill-current' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                        <span class="font-bold" x-text="liked ? {{ $post->likes_count + 1 }} : {{ $post->likes_count }}"></span>
                                    </button>

                                    <!-- Yorum -->
                                    <a href="{{ route('community.show', $post->id) }}" class="flex items-center space-x-2 px-4 py-2 rounded-xl bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span class="font-bold">{{ $post->comments->count() }}</span>
                                    </a>

                                    <!-- Paylaş -->
                                    <button class="flex items-center space-x-2 px-4 py-2 rounded-xl bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                        </svg>
                                    </button>
                                </div>

                                <a href="{{ route('community.show', $post->id) }}" class="text-purple-400 hover:text-purple-300 font-bold text-sm flex items-center space-x-1 transition-colors">
                                    <span>Detaylar</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-12 text-center">
                        <div class="text-6xl mb-4">💬</div>
                        <h3 class="text-2xl font-black text-white mb-2">Henüz Gönderi Yok</h3>
                        <p class="text-gray-400 text-lg mb-6">İlk gönderiyi sen paylaş ve topluluğu canlandır!</p>
                        @auth
                            <a href="{{ route('community.create') }}" class="inline-block bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 transition-all transform hover:scale-105">
                                İlk Gönderiyi Paylaş!
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="inline-block bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 transition-all transform hover:scale-105">
                                Kayıt Ol ve Paylaş!
                            </a>
                        @endauth
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($posts->hasPages())
                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Animasyonlar */
@keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -30px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
}

@keyframes float-delayed {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(-30px, 30px) scale(1.1); }
    66% { transform: translate(20px, -20px) scale(0.9); }
}

.animate-float {
    animation: float 8s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 10s ease-in-out infinite;
}
</style>
@endsection
