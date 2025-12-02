@extends('layouts.app')

@section('title', $post->title . ' - İlan Detayı')

@section('content')
<!-- Hero Bölümü - Koyu Tema -->
<div class="relative bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 overflow-hidden">
    <!-- Animated Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>
    
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-transparent to-transparent"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Yol Haritası -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm text-gray-400">
                <li><a href="{{ route('home') }}" class="hover:text-blue-400 transition">🏠 Ana Sayfa</a></li>
                <li><span class="text-gray-600">/</span></li>
                <li><a href="{{ route('lfg.index') }}" class="hover:text-blue-400 transition">📋 İlanlar</a></li>
                <li><span class="text-gray-600">/</span></li>
                <li class="text-blue-400">{{ Str::limit($post->title, 30) }}</li>
            </ol>
        </nav>

        <!-- Başlık ve Durum -->
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 mb-8">
            <div class="flex-1">
                <h1 class="text-4xl lg:text-6xl font-black mb-6 leading-tight bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                    {{ $post->title }}
                </h1>
                <div class="flex flex-wrap items-center gap-4">
                    <!-- Görüntülenme -->
                    <div class="flex items-center px-4 py-2 bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-700/50">
                        <svg class="w-5 h-5 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-300 font-semibold">{{ $post->views_count }} görüntülenme</span>
                    </div>
                    <!-- Tarih -->
                    <div class="flex items-center px-4 py-2 bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-700/50">
                        <svg class="w-5 h-5 mr-2 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-300 font-semibold">{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                    <!-- Oyun -->
                    <div class="flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17z"/>
                            <path fill-rule="evenodd" d="M15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-bold">{{ $post->game->name }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Durum Rozeti -->
            <div class="flex-shrink-0">
                @if($post->status === 'open')
                    <div class="relative group">
                        <div class="absolute inset-0 bg-green-500 rounded-2xl blur-xl opacity-50 group-hover:opacity-75 transition"></div>
                        <div class="relative flex items-center px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl shadow-2xl border border-green-500/50">
                            <div class="w-3 h-3 bg-white rounded-full mr-3 animate-pulse"></div>
                            <span class="font-black text-xl">AÇIK İLAN</span>
                        </div>
                    </div>
                @else
                    <div class="flex items-center px-8 py-4 bg-gradient-to-r from-red-600 to-pink-600 rounded-2xl shadow-2xl border border-red-500/50">
                        <div class="w-3 h-3 bg-white rounded-full mr-3"></div>
                        <span class="font-black text-xl">KAPALI</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 pb-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Ana İçerik -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Açıklama Kartı -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl blur-xl opacity-20 group-hover:opacity-30 transition"></div>
                <div class="relative bg-gray-900/90 backdrop-blur-xl rounded-3xl border border-gray-800 overflow-hidden shadow-2xl">
                    <div class="bg-gradient-to-r from-blue-900/50 to-purple-900/50 px-8 py-6 border-b border-gray-800">
                        <h2 class="text-2xl font-black text-white flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            İlan Açıklaması
                        </h2>
                    </div>
                    <div class="px-8 py-8">
                        <p class="text-gray-300 text-lg leading-relaxed whitespace-pre-line">{{ $post->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Gereksinimler Tablosu -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-3xl blur-xl opacity-20 group-hover:opacity-30 transition"></div>
                <div class="relative bg-gray-900/90 backdrop-blur-xl rounded-3xl border border-gray-800 overflow-hidden shadow-2xl">
                    <div class="bg-gradient-to-r from-purple-900/50 to-pink-900/50 px-8 py-6 border-b border-gray-800">
                        <h2 class="text-2xl font-black text-white flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            Gereksinimler & Detaylar
                        </h2>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($post->min_rank || $post->max_rank)
                            <div class="group/item relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-yellow-600 to-orange-600 opacity-0 group-hover/item:opacity-10 transition"></div>
                                <div class="relative bg-gray-800/50 backdrop-blur-sm p-6 rounded-2xl border-2 border-yellow-600/30 hover:border-yellow-500 transition-all">
                                    <div class="flex items-center mb-4">
                                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center text-3xl shadow-lg group-hover/item:scale-110 transition-transform">
                                            🏆
                                        </div>
                                        <h3 class="ml-4 font-black text-white text-lg">Rütbe Gereksinimi</h3>
                                    </div>
                                    <p class="text-gray-300 font-bold text-xl">
                                        {{ $post->min_rank ?? 'Belirtilmemiş' }} 
                                        @if($post->max_rank) <span class="text-yellow-400">→</span> {{ $post->max_rank }} @endif
                                    </p>
                                </div>
                            </div>
                            @endif

                            @if($post->mode)
                            <div class="group/item relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-cyan-600 opacity-0 group-hover/item:opacity-10 transition"></div>
                                <div class="relative bg-gray-800/50 backdrop-blur-sm p-6 rounded-2xl border-2 border-blue-600/30 hover:border-blue-500 transition-all">
                                    <div class="flex items-center mb-4">
                                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center text-3xl shadow-lg group-hover/item:scale-110 transition-transform">
                                            🎮
                                        </div>
                                        <h3 class="ml-4 font-black text-white text-lg">Oyun Modu</h3>
                                    </div>
                                    <p class="text-gray-300 font-bold text-xl">{{ $post->mode }}</p>
                                </div>
                            </div>
                            @endif

                            @if($post->microphone_required !== null)
                            <div class="group/item relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-purple-600 to-pink-600 opacity-0 group-hover/item:opacity-10 transition"></div>
                                <div class="relative bg-gray-800/50 backdrop-blur-sm p-6 rounded-2xl border-2 border-purple-600/30 hover:border-purple-500 transition-all">
                                    <div class="flex items-center mb-4">
                                        <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center text-3xl shadow-lg group-hover/item:scale-110 transition-transform">
                                            🎤
                                        </div>
                                        <h3 class="ml-4 font-black text-white text-lg">Mikrofon</h3>
                                    </div>
                                    <p class="text-gray-300 font-bold text-xl">
                                        {{ $post->microphone_required ? '✅ Gerekli' : '⚪ İsteğe Bağlı' }}
                                    </p>
                                </div>
                            </div>
                            @endif

                            @if($post->play_style_tag)
                            <div class="group/item relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-green-600 to-emerald-600 opacity-0 group-hover/item:opacity-10 transition"></div>
                                <div class="relative bg-gray-800/50 backdrop-blur-sm p-6 rounded-2xl border-2 border-green-600/30 hover:border-green-500 transition-all">
                                    <div class="flex items-center mb-4">
                                        <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center text-3xl shadow-lg group-hover/item:scale-110 transition-transform">
                                            🎯
                                        </div>
                                        <h3 class="ml-4 font-black text-white text-lg">Oyun Tarzı</h3>
                                    </div>
                                    <p class="text-gray-300 font-bold text-xl capitalize">{{ str_replace('-', ' ', $post->play_style_tag) }}</p>
                                </div>
                            </div>
                            @endif

                            @if($post->city)
                            <div class="group/item relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-red-600 to-pink-600 opacity-0 group-hover/item:opacity-10 transition"></div>
                                <div class="relative bg-gray-800/50 backdrop-blur-sm p-6 rounded-2xl border-2 border-red-600/30 hover:border-red-500 transition-all">
                                    <div class="flex items-center mb-4">
                                        <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-pink-500 rounded-xl flex items-center justify-center text-3xl shadow-lg group-hover/item:scale-110 transition-transform">
                                            📍
                                        </div>
                                        <h3 class="ml-4 font-black text-white text-lg">Şehir</h3>
                                    </div>
                                    <p class="text-gray-300 font-bold text-xl">{{ $post->city }}</p>
                                </div>
                            </div>
                            @endif

                            @if($post->min_age_range || $post->max_age_range)
                            <div class="group/item relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 to-purple-600 opacity-0 group-hover/item:opacity-10 transition"></div>
                                <div class="relative bg-gray-800/50 backdrop-blur-sm p-6 rounded-2xl border-2 border-indigo-600/30 hover:border-indigo-500 transition-all">
                                    <div class="flex items-center mb-4">
                                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center text-3xl shadow-lg group-hover/item:scale-110 transition-transform">
                                            👤
                                        </div>
                                        <h3 class="ml-4 font-black text-white text-lg">Yaş Aralığı</h3>
                                    </div>
                                    <p class="text-gray-300 font-bold text-xl">
                                        {{ $post->min_age_range ?? '?' }} - {{ $post->max_age_range ?? '?' }}
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Başvuru Bölümü -->
            @auth
                @if($post->status === 'open' && $post->user_id !== auth()->id())
                    @if($userApplication)
                        <div class="relative group">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-3xl blur-2xl opacity-30 group-hover:opacity-40 transition"></div>
                            <div class="relative bg-gradient-to-r from-blue-900/90 to-cyan-900/90 backdrop-blur-xl rounded-3xl border border-blue-500/50 shadow-2xl overflow-hidden">
                                <div class="p-8">
                                    <div class="flex items-center mb-6">
                                        <div class="w-20 h-20 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center text-5xl mr-5 shadow-xl">
                                            ✅
                                        </div>
                                        <div>
                                            <h3 class="text-3xl font-black text-white">Başvurunuz Alındı!</h3>
                                            <p class="text-blue-200 text-lg mt-1">{{ $userApplication->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    @if($userApplication->status !== 'pending')
                                        <div class="p-6 bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10">
                                            <p class="text-sm font-bold text-blue-200 mb-2">Başvuru Durumu:</p>
                                            <p class="text-3xl font-black">
                                                @if($userApplication->status === 'accepted')
                                                    <span class="text-green-400">🎉 Kabul Edildi</span>
                                                @else
                                                    <span class="text-red-400">❌ Reddedildi</span>
                                                @endif
                                            </p>
                                        </div>
                                    @else
                                        <div class="p-6 bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10">
                                            <p class="text-sm font-bold text-blue-200 mb-2">Başvuru Durumu:</p>
                                            <p class="text-2xl font-black text-yellow-400 flex items-center">
                                                <span class="animate-pulse mr-3 text-3xl">⏳</span> Değerlendiriliyor
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="relative group">
                            <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-emerald-600 rounded-3xl blur-2xl opacity-30 group-hover:opacity-40 transition"></div>
                            <div class="relative bg-gray-900/90 backdrop-blur-xl rounded-3xl border border-gray-800 shadow-2xl overflow-hidden">
                                <div class="bg-gradient-to-r from-green-900/50 to-emerald-900/50 px-8 py-6 border-b border-gray-800">
                                    <h2 class="text-2xl font-black text-white flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center mr-3">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                                            </svg>
                                        </div>
                                        Bu İlana Başvur
                                    </h2>
                                </div>
                                <form action="{{ route('lfg.apply', $post->id) }}" method="POST" class="p-8">
                                    @csrf
                                    <div class="mb-6">
                                        <label class="block text-sm font-black text-gray-300 mb-3 uppercase tracking-wider">
                                            Mesajınız (İsteğe Bağlı)
                                        </label>
                                        <textarea name="message" rows="4" 
                                            class="w-full px-5 py-4 bg-gray-800/50 border-2 border-gray-700 rounded-xl focus:ring-4 focus:ring-green-500/30 focus:border-green-500 transition-all resize-none text-white placeholder-gray-500"
                                            placeholder="Kendinizi tanıtın, deneyimlerinizi paylaşın..."></textarea>
                                    </div>
                                    <button type="submit" 
                                        class="w-full relative group/btn overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl blur opacity-50 group-hover/btn:opacity-75 transition"></div>
                                        <div class="relative bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-5 rounded-xl font-black text-xl shadow-2xl transform group-hover/btn:scale-[1.02] transition-all flex items-center justify-center border border-green-500/50">
                                            <svg class="w-7 h-7 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                                            </svg>
                                            BAŞVURUYU GÖNDER
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endif

                @if($post->user_id === auth()->id())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <a href="{{ route('lfg.applications', $post->id) }}" 
                            class="relative group/btn overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl blur opacity-50 group-hover/btn:opacity-75 transition"></div>
                            <div class="relative bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-5 rounded-xl font-black text-xl shadow-2xl transform group-hover/btn:scale-[1.02] transition-all flex items-center justify-center border border-green-500/50">
                                <svg class="w-7 h-7 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                                BAŞVURULARI GÖRÜNTÜLE
                            </div>
                        </a>
                        <a href="{{ route('lfg.edit', $post->id) }}" 
                            class="relative group/btn overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-gray-700 to-gray-800 rounded-xl blur opacity-50 group-hover/btn:opacity-75 transition"></div>
                            <div class="relative bg-gradient-to-r from-gray-700 to-gray-800 px-8 py-5 rounded-xl font-black text-xl shadow-2xl transform group-hover/btn:scale-[1.02] transition-all flex items-center justify-center border border-gray-600/50">
                                <svg class="w-7 h-7 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                                İLANI DÜZENLE
                            </div>
                        </a>
                    </div>
                @endif
            @else
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-yellow-600 to-orange-600 rounded-3xl blur-2xl opacity-40 group-hover:opacity-50 transition"></div>
                    <div class="relative bg-gradient-to-r from-yellow-900/90 to-orange-900/90 backdrop-blur-xl rounded-3xl border border-yellow-500/50 shadow-2xl overflow-hidden">
                        <div class="p-10 text-center">
                            <div class="w-24 h-24 bg-white/10 backdrop-blur-sm rounded-full flex items-center justify-center text-5xl mx-auto mb-6 shadow-2xl">
                                🔐
                            </div>
                            <h3 class="text-3xl font-black mb-3">Giriş Yapın</h3>
                            <p class="text-yellow-100 text-lg mb-8">Bu ilana başvurmak için hesabınıza giriş yapmalısınız</p>
                            <a href="{{ route('login') }}" 
                                class="inline-block bg-white text-orange-600 px-10 py-4 rounded-xl font-black text-lg hover:bg-gray-100 transform hover:scale-105 transition-all shadow-2xl">
                                GİRİŞ YAP
                            </a>
                        </div>
                    </div>
                </div>
            @endauth
        </div>

        <!-- Yan Panel -->
        <div class="lg:col-span-1 space-y-8">
            <!-- İlan Sahibi Kartı -->
            <div class="relative group sticky top-6">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-purple-600 rounded-3xl blur-xl opacity-30 group-hover:opacity-40 transition"></div>
                <div class="relative bg-gray-900/90 backdrop-blur-xl rounded-3xl border border-gray-800 shadow-2xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-900/50 to-purple-900/50 px-6 py-5 border-b border-gray-800">
                        <h3 class="text-xl font-black text-white flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            İlan Sahibi
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center space-x-4 mb-6">
                            <div class="relative">
                                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-2xl flex items-center justify-center text-white font-black text-3xl shadow-2xl">
                                    {{ substr($post->user->name, 0, 1) }}
                                </div>
                                <div class="absolute -bottom-2 -right-2 w-7 h-7 bg-green-500 rounded-full border-4 border-gray-900 shadow-lg"></div>
                            </div>
                            <div class="flex-1">
                                <p class="font-black text-white text-xl mb-1">{{ $post->user->name }}</p>
                                @if($post->user->profile)
                                    <p class="text-sm text-gray-400 font-bold">
                                        {{ $post->user->profile->rank ?? 'Rütbe belirtilmemiş' }}
                                    </p>
                                @endif
                                @if($post->user->profile && $post->user->profile->level)
                                    <div class="flex items-center mt-2">
                                        <span class="text-xs font-black text-purple-400 bg-purple-900/50 px-3 py-1 rounded-full border border-purple-500/30">
                                            ⭐ Seviye {{ $post->user->profile->level }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @if($post->user->profile)
                            <div class="space-y-3 mb-6">
                                @if($post->user->profile->total_xp)
                                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-yellow-900/30 to-orange-900/30 rounded-xl border border-yellow-600/30">
                                    <span class="text-sm font-bold text-gray-300">Toplam XP</span>
                                    <span class="font-black text-yellow-400 text-lg">{{ number_format($post->user->profile->total_xp) }}</span>
                                </div>
                                @endif
                                
                                @if($post->user->profile->platform)
                                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-900/30 to-cyan-900/30 rounded-xl border border-blue-600/30">
                                    <span class="text-sm font-bold text-gray-300">Platform</span>
                                    <span class="font-black text-blue-400 text-lg">
                                        @if($post->user->profile->platform === 'mobile')
                                            📱 Mobil
                                        @elseif($post->user->profile->platform === 'pc')
                                            💻 Bilgisayar
                                        @elseif($post->user->profile->platform === 'console')
                                            🎮 Konsol
                                        @else
                                            {{ ucfirst($post->user->profile->platform) }}
                                        @endif
                                    </span>
                                </div>
                                @endif
                            </div>
                        @endif
                        
                        <a href="{{ route('profile.show', $post->user->id) }}" 
                            class="block relative group/btn overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl blur opacity-50 group-hover/btn:opacity-75 transition"></div>
                            <div class="relative bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4 rounded-xl text-center font-black shadow-2xl transform group-hover/btn:scale-[1.02] transition-all border border-blue-500/50">
                                PROFİLİ GÖRÜNTÜLE
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Son Tarih Kartı -->
            @if($post->expires_at)
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-br from-red-600 to-pink-600 rounded-3xl blur-xl opacity-30 group-hover:opacity-40 transition"></div>
                <div class="relative bg-gray-900/90 backdrop-blur-xl rounded-3xl border border-gray-800 shadow-2xl overflow-hidden">
                    <div class="bg-gradient-to-r from-red-900/50 to-pink-900/50 px-6 py-5 border-b border-gray-800">
                        <h3 class="text-xl font-black text-white flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-pink-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            Son Başvuru
                        </h3>
                    </div>
                    <div class="p-8">
                        <div class="text-center">
                            <p class="text-5xl font-black text-white mb-3">{{ $post->expires_at->format('d.m.Y') }}</p>
                            <p class="text-2xl text-gray-400 font-black mb-4">{{ $post->expires_at->format('H:i') }}</p>
                            <div class="inline-flex items-center px-5 py-3 bg-red-900/50 text-red-400 rounded-xl font-black border border-red-500/30">
                                <svg class="w-5 h-5 mr-2 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                {{ $post->expires_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Paylaş Kartı -->
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-br from-pink-600 via-purple-600 to-indigo-600 rounded-3xl blur-xl opacity-40 group-hover:opacity-50 transition"></div>
                <div class="relative bg-gradient-to-br from-pink-900/90 via-purple-900/90 to-indigo-900/90 backdrop-blur-xl rounded-3xl border border-pink-500/50 shadow-2xl overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-black text-white mb-5 flex items-center">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"/>
                                </svg>
                            </div>
                            Paylaş
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <button onclick="shareOnTwitter()" class="bg-white/10 backdrop-blur-sm hover:bg-white/20 px-4 py-4 rounded-xl font-black transition-all transform hover:scale-105 border border-white/20">
                                🐦 X
                            </button>
                            <button onclick="copyLink()" class="bg-white/10 backdrop-blur-sm hover:bg-white/20 px-4 py-4 rounded-xl font-black transition-all transform hover:scale-105 border border-white/20">
                                🔗 Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function shareOnTwitter() {
    const text = '{{ $post->title }} - PUBG Mobile Topluluk';
    const url = window.location.href;
    window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`, '_blank');
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        alert('Link kopyalandı! 🎉');
    });
}
</script>

@endsection
