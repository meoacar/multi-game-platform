@extends('layouts.app')

@section('title', 'Mesajlar')

@section('content')
<!-- PUBG Mobile Arka Plan -->
<div class="fixed inset-0 z-0 overflow-hidden bg-black">
    <div class="absolute inset-0" 
         style="background-image: url('/arkaplan/2.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.12;">
    </div>
    <div class="absolute inset-0 bg-gradient-to-br from-orange-900/15 via-purple-900/15 to-pink-900/15"></div>
</div>

<style>

    .conversation-card {
        background: rgba(26, 31, 58, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 107, 0, 0.2);
        transition: all 0.3s ease;
    }

    .conversation-card:hover {
        border-color: rgba(255, 107, 0, 0.5);
        transform: translateX(5px);
        box-shadow: 0 10px 30px rgba(255, 107, 0, 0.2);
    }

    .unread-badge {
        background: linear-gradient(135deg, #ff6b00 0%, #ff8c00 100%);
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
</style>

<div class="min-h-screen py-8">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2">💬 Mesajlar</h1>
                    <p class="text-gray-400">Tüm konuşmalarınız burada</p>
                </div>
                <div class="conversation-card rounded-xl px-6 py-3">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-400">{{ $conversations->count() }}</div>
                        <div class="text-xs text-gray-400">Konuşma</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Konuşmalar -->
        <div class="space-y-3">
            @forelse($conversations as $conversation)
                <a href="{{ route('messages.show', $conversation['user']->id) }}" 
                   class="conversation-card flex items-center p-5 rounded-xl block">
                    <!-- Avatar -->
                    <div class="relative mr-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-orange-500/30">
                            {{ substr($conversation['user']->name, 0, 1) }}
                        </div>
                        @if($conversation['unread_count'] > 0)
                            <div class="absolute -top-1 -right-1 w-6 h-6 bg-green-400 rounded-full border-2 border-gray-900 flex items-center justify-center">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Kullanıcı Bilgisi -->
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start mb-1">
                            <div class="flex-1 min-w-0 mr-4">
                                <p class="font-bold text-xl text-white truncate">{{ $conversation['user']->name }}</p>
                                @if($conversation['user']->profile)
                                    <p class="text-sm text-gray-400 flex items-center gap-1">
                                        <span>🎮</span>
                                        <span class="truncate">{{ $conversation['user']->profile->pubg_id ?? 'PUBG ID yok' }}</span>
                                    </p>
                                @endif
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs text-gray-500 mb-2">
                                    {{ \Carbon\Carbon::parse($conversation['last_message_at'])->diffForHumans() }}
                                </p>
                                @if($conversation['unread_count'] > 0)
                                    <span class="unread-badge inline-flex items-center justify-center text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                        {{ $conversation['unread_count'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Son Mesaj Önizleme (opsiyonel) -->
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-sm text-gray-500">
                                Son mesaj: {{ \Carbon\Carbon::parse($conversation['last_message_at'])->format('d.m.Y H:i') }}
                            </p>
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @empty
                <div class="conversation-card rounded-2xl p-16 text-center">
                    <div class="text-8xl mb-6 opacity-20">📭</div>
                    <p class="text-gray-300 text-xl mb-3 font-semibold">Henüz mesajınız yok</p>
                    <p class="text-gray-500 mb-6">Kullanıcı profillerinden mesaj gönderebilirsiniz</p>
                    <a href="{{ route('home') }}" 
                       class="inline-block bg-gradient-to-r from-orange-500 to-red-600 text-white px-8 py-3 rounded-xl hover:shadow-lg hover:shadow-orange-500/30 transition-all hover:-translate-y-1 font-semibold">
                        🏠 Ana Sayfaya Dön
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Bilgi Kartı -->
        @if($conversations->count() > 0)
        <div class="mt-6 conversation-card rounded-xl p-4">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2 text-gray-400">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span>Mesajlarınız güvenli bir şekilde saklanır</span>
                </div>
                <div class="text-orange-400 text-xs font-semibold">
                    🔒 Şifreli İletişim
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
