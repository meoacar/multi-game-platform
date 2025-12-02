@extends('layouts.app')

@section('title', 'Mesajlaşma')

@section('content')
<!-- PUBG Mobile Arka Plan -->
<div class="fixed inset-0 z-0 overflow-hidden bg-black">
    <div class="absolute inset-0" 
         style="background-image: url('/arkaplan/2.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.12;">
    </div>
    <div class="absolute inset-0 bg-gradient-to-br from-orange-900/15 via-purple-900/15 to-pink-900/15"></div>
</div>

<style>

    .message-container {
        background: rgba(26, 31, 58, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 107, 0, 0.2);
    }

    .message-bubble-sent {
        background: linear-gradient(135deg, #ff6b00 0%, #ff8c00 100%);
        box-shadow: 0 4px 15px rgba(255, 107, 0, 0.3);
    }

    .message-bubble-received {
        background: rgba(20, 25, 45, 0.8);
        border: 1px solid rgba(255, 107, 0, 0.2);
    }

    .message-input {
        background: rgba(20, 25, 45, 0.6);
        border: 1px solid rgba(255, 107, 0, 0.3);
        color: white;
    }

    .message-input:focus {
        outline: none;
        border-color: rgba(255, 107, 0, 0.6);
        box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1);
    }

    .btn-send {
        background: linear-gradient(135deg, #ff6b00 0%, #ff8c00 100%);
        transition: all 0.3s ease;
    }

    .btn-send:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(255, 107, 0, 0.4);
    }

    .messages-scroll::-webkit-scrollbar {
        width: 8px;
    }

    .messages-scroll::-webkit-scrollbar-track {
        background: rgba(20, 25, 45, 0.4);
        border-radius: 10px;
    }

    .messages-scroll::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #ff6b00 0%, #ff8c00 100%);
        border-radius: 10px;
    }

    .messages-scroll::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #ff8c00 0%, #ffa500 100%);
    }
</style>

<div class="min-h-screen py-8">
    <div class="max-w-5xl mx-auto px-4">
        <div class="message-container rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="border-b border-orange-900/30 p-6 bg-gradient-to-r from-gray-900/50 to-gray-800/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('messages.index') }}" 
                           class="text-orange-400 hover:text-orange-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        
                        <div class="relative">
                            <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-orange-500/30">
                                {{ substr($otherUser->name, 0, 1) }}
                            </div>
                            <div class="absolute bottom-0 right-0 w-4 h-4 bg-green-400 rounded-full border-2 border-gray-900"></div>
                        </div>
                        
                        <div>
                            <p class="font-bold text-xl text-white">{{ $otherUser->name }}</p>
                            @if($otherUser->profile)
                                <p class="text-sm text-gray-400">
                                    🎮 {{ $otherUser->profile->pubg_id ?? 'PUBG ID yok' }}
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    <a href="{{ route('profile.show', $otherUser->id) }}" 
                       class="bg-gradient-to-r from-orange-500 to-red-600 text-white px-6 py-3 rounded-xl hover:shadow-lg hover:shadow-orange-500/30 transition-all hover:-translate-y-1 font-semibold text-sm">
                        👤 Profili Gör
                    </a>
                </div>
            </div>

            <!-- Mesajlar -->
            <div class="p-6 h-[500px] overflow-y-auto messages-scroll bg-gradient-to-b from-gray-900/20 to-gray-900/40" id="messages-container">
                @forelse($messages as $message)
                    <div class="mb-6 {{ $message->sender_id == auth()->id() ? 'text-right' : 'text-left' }}">
                        <div class="inline-block max-w-md">
                            <div class="{{ $message->sender_id == auth()->id() ? 'message-bubble-sent' : 'message-bubble-received' }} rounded-2xl px-5 py-3 {{ $message->sender_id == auth()->id() ? 'rounded-tr-sm' : 'rounded-tl-sm' }}">
                                <p class="text-white leading-relaxed">{{ $message->content }}</p>
                            </div>
                            <div class="flex items-center gap-2 mt-2 {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                                <p class="text-xs text-gray-500">
                                    {{ $message->created_at->format('H:i') }}
                                </p>
                                @if($message->sender_id == auth()->id())
                                    <span class="text-xs text-orange-400">
                                        {{ $message->is_read ? '✓✓' : '✓' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-center">
                        <div class="text-8xl mb-6 opacity-20">💬</div>
                        <p class="text-gray-400 text-lg mb-2">Henüz mesaj yok</p>
                        <p class="text-gray-500 text-sm">İlk mesajı sen gönder ve sohbeti başlat!</p>
                    </div>
                @endforelse
            </div>

            <!-- Mesaj Gönderme Formu -->
            <div class="border-t border-orange-900/30 p-6 bg-gradient-to-r from-gray-900/50 to-gray-800/50">
                <form action="{{ route('messages.store') }}" method="POST" class="flex gap-3">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
                    
                    <div class="flex-1 relative">
                        <textarea name="content" rows="2" 
                                  class="message-input w-full rounded-xl px-5 py-3 resize-none placeholder-gray-500" 
                                  placeholder="💬 Mesajınızı yazın..."
                                  required></textarea>
                    </div>
                    
                    <button type="submit" 
                            class="btn-send text-white px-8 py-3 rounded-xl font-bold shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Gönder
                    </button>
                </form>
            </div>
        </div>

        <!-- Bilgi Kartı -->
        <div class="mt-6 message-container rounded-xl p-4">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2 text-gray-400">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span>Mesajlar gerçek zamanlı güncellenir</span>
                </div>
                <div class="text-orange-400 text-xs">
                    🔒 Güvenli Mesajlaşma
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Mesaj container'ını en alta kaydır
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
});

// Enter tuşu ile mesaj gönderme (Shift+Enter ile yeni satır)
document.querySelector('textarea[name="content"]')?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        this.closest('form').submit();
    }
});
</script>
@endsection
