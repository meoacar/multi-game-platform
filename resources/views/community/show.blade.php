@extends('layouts.app')

@section('title', 'Gönderi Detayı - Topluluk')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Geri Butonu -->
    <div class="mb-6">
        <a href="{{ route('community.index') }}" class="inline-flex items-center text-purple-400 hover:text-purple-300 font-semibold transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Topluluğa Dön
        </a>
    </div>

    <!-- Gönderi Kartı -->
    <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 overflow-hidden mb-8" x-data="{ liked: false }">
        <div class="p-8">
            <!-- Kullanıcı Bilgisi -->
            <div class="flex items-start justify-between mb-6 pb-6 border-b border-white/10">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.show', $post->user->id) }}" class="relative group">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($post->user->name) }}&background=8B5CF6&color=fff" 
                             class="w-16 h-16 rounded-xl ring-2 ring-purple-500/50 group-hover:ring-purple-500 transition-all">
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-gray-900"></div>
                    </a>
                    <div>
                        <a href="{{ route('profile.show', $post->user->id) }}" class="font-bold text-xl text-white hover:text-purple-400 transition-colors">
                            {{ $post->user->name }}
                        </a>
                        <div class="flex items-center space-x-2 text-sm text-gray-400 mt-1">
                            <span>{{ $post->created_at->format('d.m.Y H:i') }}</span>
                            @if($post->user->profile && $post->user->profile->pubg_id)
                                <span>•</span>
                                <span class="text-purple-400">Level {{ $post->user->level ?? 1 }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Kategori Badge -->
                <span class="px-4 py-2 rounded-xl text-sm font-bold {{ $post->type == 'intro' ? 'bg-gradient-to-r from-green-500 to-emerald-600' : ($post->type == 'question' ? 'bg-gradient-to-r from-orange-500 to-red-600' : ($post->type == 'achievement' ? 'bg-gradient-to-r from-yellow-500 to-amber-600' : 'bg-gradient-to-r from-blue-500 to-cyan-600')) }} text-white shadow-lg">
                    {{ $post->type == 'intro' ? '👋 Tanışma' : ($post->type == 'question' ? '❓ Soru' : ($post->type == 'achievement' ? '🏆 Başarı' : '💬 Genel')) }}
                </span>
            </div>

            <!-- İçerik -->
            <div class="mb-8">
                <p class="text-gray-200 text-lg leading-relaxed whitespace-pre-wrap">{{ $post->content }}</p>
            </div>

            <!-- Etkileşim Butonları -->
            <div class="flex items-center justify-between pt-6 border-t border-white/10">
                <div class="flex items-center space-x-4">
                    <!-- Beğeni -->
                    <button @click="liked = !liked" class="flex items-center space-x-2 px-6 py-3 rounded-xl transition-all" :class="liked ? 'bg-red-500/20 text-red-400' : 'bg-white/5 text-gray-400 hover:bg-white/10'">
                        <svg class="w-6 h-6" :class="liked ? 'fill-current' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span class="font-bold text-lg" x-text="liked ? {{ $post->likes_count + 1 }} : {{ $post->likes_count }}"></span>
                    </button>

                    <!-- Yorum Sayısı -->
                    <div class="flex items-center space-x-2 px-6 py-3 rounded-xl bg-white/5 text-gray-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span class="font-bold text-lg">{{ $post->comments->count() }}</span>
                    </div>

                    <!-- Paylaş -->
                    <button class="flex items-center space-x-2 px-6 py-3 rounded-xl bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                    </button>
                </div>

                <!-- Sil Butonu -->
                @if(auth()->check() && (auth()->id() == $post->user_id || auth()->user()->is_admin))
                    <form action="{{ route('community.destroy', $post->id) }}" method="POST" 
                          onsubmit="return confirm('Gönderiyi silmek istediğinize emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-3 rounded-xl bg-red-500/20 text-red-400 hover:bg-red-500/30 font-bold transition-all">
                            🗑️ Sil
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Yorumlar Bölümü -->
    <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-8">
        <h2 class="text-2xl font-black text-white mb-6 flex items-center">
            <span class="text-3xl mr-3">💬</span>
            Yorumlar ({{ $post->comments->count() }})
        </h2>
        
        @auth
            <form action="{{ route('comments.store') }}" method="POST" class="mb-8">
                @csrf
                <input type="hidden" name="commentable_type" value="App\Models\CommunityPost">
                <input type="hidden" name="commentable_id" value="{{ $post->id }}">
                
                <div class="flex items-start space-x-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=8B5CF6&color=fff" 
                         class="w-12 h-12 rounded-xl ring-2 ring-purple-500/50">
                    <div class="flex-1">
                        <textarea name="content" rows="3" 
                                  class="w-full bg-white/10 border-2 border-white/20 text-white placeholder-gray-500 rounded-xl px-4 py-3 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-all resize-none" 
                                  placeholder="Yorumunuzu yazın..."></textarea>
                        <button type="submit" class="mt-3 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg transition-all transform hover:scale-105">
                            💬 Yorum Yap
                        </button>
                    </div>
                </div>
            </form>
        @else
            <div class="bg-white/5 rounded-xl p-6 text-center mb-8">
                <p class="text-gray-400 mb-4">Yorum yapmak için giriş yapmalısınız</p>
                <a href="{{ route('login') }}" class="inline-block bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg transition-all transform hover:scale-105">
                    Giriş Yap
                </a>
            </div>
        @endauth

        <div class="space-y-4">
            @forelse($post->comments as $comment)
                <div class="bg-white/5 rounded-xl p-6 border-l-4 border-purple-500">
                    <div class="flex items-start space-x-3 mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->name) }}&background=8B5CF6&color=fff" 
                             class="w-10 h-10 rounded-lg">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-white">{{ $comment->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-300 leading-relaxed ml-13">{{ $comment->content }}</p>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">💭</div>
                    <p class="text-gray-400 text-lg">Henüz yorum yok</p>
                    <p class="text-gray-500 text-sm">İlk yorumu sen yap!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
