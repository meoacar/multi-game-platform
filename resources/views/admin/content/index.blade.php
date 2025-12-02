@extends('admin.layout')

@section('title', 'İçerik Yönetimi')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 -m-6 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 bg-clip-text text-transparent mb-2">
                        📝 İÇERİK YÖNETİMİ
                    </h1>
                    <p class="text-gray-400 text-lg">Tüm içerik türlerini tek yerden yönet</p>
                </div>
            </div>
        </div>

        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- LFG İlanları -->
            <div class="bg-black/40 backdrop-blur-xl rounded-2xl p-6 border border-orange-500/20 hover:border-orange-500/40 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-4xl">📢</div>
                    <div class="px-3 py-1 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-lg text-xs font-bold">
                        {{ number_format($stats['lfg_posts']['total']) }}
                    </div>
                </div>
                <h3 class="text-orange-400 font-black text-sm uppercase tracking-wider mb-3">LFG İlanları</h3>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Açık:</span>
                        <span class="text-green-400 font-bold">{{ $stats['lfg_posts']['open'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Kapalı:</span>
                        <span class="text-red-400 font-bold">{{ $stats['lfg_posts']['closed'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">⭐ Öne Çıkan:</span>
                        <span class="text-yellow-400 font-bold">{{ $stats['lfg_posts']['featured'] }}</span>
                    </div>
                </div>
                <a href="{{ route('admin.lfg-posts.index') }}" 
                   class="block w-full px-4 py-2 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white rounded-lg text-center font-bold text-sm uppercase transition-all shadow-lg shadow-orange-500/50">
                    Yönet →
                </a>
            </div>

            <!-- Klanlar -->
            <div class="bg-black/40 backdrop-blur-xl rounded-2xl p-6 border border-orange-500/20 hover:border-orange-500/40 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-4xl">👑</div>
                    <div class="px-3 py-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg text-xs font-bold">
                        {{ number_format($stats['clans']['total']) }}
                    </div>
                </div>
                <h3 class="text-orange-400 font-black text-sm uppercase tracking-wider mb-3">Klanlar</h3>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">✓ Doğrulanmış:</span>
                        <span class="text-blue-400 font-bold">{{ $stats['clans']['verified'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Aktif:</span>
                        <span class="text-green-400 font-bold">{{ $stats['clans']['active'] ?? $stats['clans']['total'] }}</span>
                    </div>
                </div>
                <a href="{{ route('admin.clans.index') }}" 
                   class="block w-full px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg text-center font-bold text-sm uppercase transition-all shadow-lg shadow-purple-500/50">
                    Yönet →
                </a>
            </div>

            <!-- Rehberler -->
            <div class="bg-black/40 backdrop-blur-xl rounded-2xl p-6 border border-orange-500/20 hover:border-orange-500/40 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-4xl">📚</div>
                    <div class="px-3 py-1 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg text-xs font-bold">
                        {{ number_format($stats['guides']['total']) }}
                    </div>
                </div>
                <h3 class="text-orange-400 font-black text-sm uppercase tracking-wider mb-3">Rehberler</h3>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Yayınlanan:</span>
                        <span class="text-green-400 font-bold">{{ $stats['guides']['published'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">⭐ Öne Çıkan:</span>
                        <span class="text-yellow-400 font-bold">{{ $stats['guides']['featured'] }}</span>
                    </div>
                </div>
                <a href="{{ route('admin.guides.index') }}" 
                   class="block w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-lg text-center font-bold text-sm uppercase transition-all shadow-lg shadow-blue-500/50">
                    Yönet →
                </a>
            </div>

            <!-- Topluluk Gönderileri -->
            <div class="bg-black/40 backdrop-blur-xl rounded-2xl p-6 border border-orange-500/20 hover:border-orange-500/40 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-4xl">💬</div>
                    <div class="px-3 py-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg text-xs font-bold">
                        {{ number_format($stats['community_posts']['total']) }}
                    </div>
                </div>
                <h3 class="text-orange-400 font-black text-sm uppercase tracking-wider mb-3">Topluluk</h3>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">⭐ Öne Çıkan:</span>
                        <span class="text-yellow-400 font-bold">{{ $stats['community_posts']['featured'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Toplam:</span>
                        <span class="text-gray-300 font-bold">{{ $stats['community_posts']['total'] }}</span>
                    </div>
                </div>
                <a href="{{ route('admin.community-posts.index') }}" 
                   class="block w-full px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg text-center font-bold text-sm uppercase transition-all shadow-lg shadow-green-500/50">
                    Yönet →
                </a>
            </div>
        </div>

        <!-- Son İçerikler -->
        <div class="bg-black/40 backdrop-blur-xl rounded-2xl p-6 border border-orange-500/20">
            <h2 class="text-2xl font-black text-orange-400 mb-6 uppercase tracking-wider">📋 Son İçerikler</h2>
            
            <div class="space-y-4">
                <p class="text-gray-400 text-center py-8">İçerik listesi yakında eklenecek...</p>
            </div>
        </div>
    </div>
</div>
@endsection
