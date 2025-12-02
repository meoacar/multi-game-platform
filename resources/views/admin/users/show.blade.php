@extends('admin.layout')

@section('title', 'Kullanıcı Detayı - ' . $user->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="userDetail()">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kullanıcı Listesine Dön
        </a>
        
        <div class="flex gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Düzenle
            </a>
            <a href="{{ route('profile.show', $user->id) }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Profili Görüntüle
            </a>
        </div>
    </div>

    <!-- Kullanıcı Profil Kartı -->
    <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl shadow-2xl p-8 mb-6 text-white">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-4xl font-bold border-4 border-white/30">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-3xl font-bold mb-2">{{ $user->name }}</h1>
                    <p class="text-lg opacity-90 mb-3">{{ $user->email }}</p>
                    <div class="flex flex-wrap gap-2">
                        @if($user->status === 'active')
                            <span class="px-3 py-1 bg-green-400 text-green-900 text-sm font-semibold rounded-full">✅ Aktif</span>
                        @elseif($user->status === 'banned')
                            <span class="px-3 py-1 bg-red-400 text-red-900 text-sm font-semibold rounded-full">🚫 Banlı</span>
                        @elseif($user->status === 'frozen')
                            <span class="px-3 py-1 bg-blue-400 text-blue-900 text-sm font-semibold rounded-full">❄️ Dondurulmuş</span>
                        @endif
                        
                        @if($user->is_admin)
                            <span class="px-3 py-1 bg-yellow-400 text-yellow-900 text-sm font-semibold rounded-full">👑 Admin</span>
                        @endif
                        
                        @if($user->email_verified_at)
                            <span class="px-3 py-1 bg-white/20 text-white text-sm font-semibold rounded-full">✉️ Email Doğrulandı</span>
                        @else
                            <span class="px-3 py-1 bg-red-400 text-red-900 text-sm font-semibold rounded-full">❌ Email Doğrulanmadı</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="text-right">
                <p class="text-sm opacity-75">Kullanıcı ID</p>
                <p class="text-2xl font-bold">#{{ $user->id }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sol Kolon: Ana Bilgiler -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Temel Bilgiler Kartı -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Temel Bilgiler
                </h2>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">📅 Kayıt Tarihi</p>
                        <p class="font-semibold text-gray-900">{{ $user->created_at->format('d.m.Y') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">🕐 Son Giriş</p>
                        <p class="font-semibold text-gray-900">
                            {{ $user->last_login_at ? $user->last_login_at->format('d.m.Y') : 'Hiç' }}
                        </p>
                        @if($user->last_login_at)
                            <p class="text-xs text-gray-500 mt-1">{{ $user->last_login_at->diffForHumans() }}</p>
                        @endif
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">⭐ Toplam XP</p>
                        <p class="font-semibold text-gray-900">{{ number_format($user->xp_total ?? 0) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Level {{ $user->getLevel() }}</p>
                    </div>
                    
                    @if($user->profile)
                        @if($user->profile->pubg_id)
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg">
                            <p class="text-xs text-gray-600 mb-1">🎮 PUBG ID</p>
                            <p class="font-semibold text-gray-900">{{ $user->profile->pubg_id }}</p>
                        </div>
                        @endif
                        
                        @if($user->profile->rank)
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 rounded-lg">
                            <p class="text-xs text-gray-600 mb-1">🏆 Rütbe</p>
                            <p class="font-semibold text-gray-900">{{ $user->profile->rank }}</p>
                        </div>
                        @endif
                        
                        @if($user->profile->city)
                        <div class="bg-gradient-to-br from-pink-50 to-pink-100 p-4 rounded-lg">
                            <p class="text-xs text-gray-600 mb-1">🏙️ Şehir</p>
                            <p class="font-semibold text-gray-900">{{ $user->profile->city }}</p>
                        </div>
                        @endif
                    @endif
                </div>
                
                @if($user->profile && $user->profile->bio)
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm text-gray-600 mb-2">📝 Biyografi</p>
                    <p class="text-gray-900">{{ $user->profile->bio }}</p>
                </div>
                @endif
            </div>

            <!-- XP ve Level Grafiği -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    XP ve Level İlerlemesi
                </h2>
                
                @php
                    $levelProgress = $user->getLevelProgress();
                    $currentLevel = $user->getLevel();
                    $progressPercent = $levelProgress['progress_percent'] ?? 0;
                @endphp
                
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Level {{ $currentLevel }}</span>
                        <span class="text-sm font-medium text-gray-700">Level {{ $currentLevel + 1 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-500 to-blue-500 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold transition-all duration-500"
                            style="width: {{ $progressPercent }}%">
                            {{ round($progressPercent) }}%
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-2 text-xs text-gray-600">
                        <span>{{ number_format($levelProgress['current_level_xp'] ?? 0) }} XP</span>
                        <span>{{ number_format($levelProgress['next_level_xp'] ?? 0) }} XP</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <p class="text-2xl font-bold text-purple-600">{{ $currentLevel }}</p>
                        <p class="text-xs text-gray-600 mt-1">Mevcut Level</p>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($user->xp_total ?? 0) }}</p>
                        <p class="text-xs text-gray-600 mt-1">Toplam XP</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">{{ number_format($levelProgress['remaining_xp'] ?? 0) }}</p>
                        <p class="text-xs text-gray-600 mt-1">Kalan XP</p>
                    </div>
                </div>
            </div>

            <!-- İçerik İstatistikleri -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    İçerik İstatistikleri
                </h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl text-white">
                        <p class="text-3xl font-bold">{{ $user->lfgPosts->count() }}</p>
                        <p class="text-sm opacity-90 mt-1">🎯 LFG İlanı</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-green-500 to-green-600 rounded-xl text-white">
                        <p class="text-3xl font-bold">{{ $user->ownedClans->count() }}</p>
                        <p class="text-sm opacity-90 mt-1">🛡️ Klan</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl text-white">
                        <p class="text-3xl font-bold">{{ $user->guidePosts->count() }}</p>
                        <p class="text-sm opacity-90 mt-1">📚 Rehber</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl text-white">
                        <p class="text-3xl font-bold">{{ $user->communityPosts->count() }}</p>
                        <p class="text-sm opacity-90 mt-1">💬 Gönderi</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4">
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-xl font-bold text-gray-900">{{ $user->comments->count() }}</p>
                        <p class="text-xs text-gray-600 mt-1">💭 Yorum</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-xl font-bold text-gray-900">{{ $user->badges->count() }}</p>
                        <p class="text-xs text-gray-600 mt-1">🏅 Rozet</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-xl font-bold text-gray-900">{{ $user->friendships->count() }}</p>
                        <p class="text-xs text-gray-600 mt-1">👥 Arkadaş</p>
                    </div>
                </div>
            </div>


            <!-- Tab Menüsü -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button @click="activeTab = 'activity'" 
                            :class="activeTab === 'activity' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="px-6 py-4 border-b-2 font-medium text-sm transition-colors">
                            📊 Aktivite Timeline
                        </button>
                        <button @click="activeTab = 'content'" 
                            :class="activeTab === 'content' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="px-6 py-4 border-b-2 font-medium text-sm transition-colors">
                            📝 İçerikler
                        </button>
                        <button @click="activeTab = 'bans'" 
                            :class="activeTab === 'bans' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="px-6 py-4 border-b-2 font-medium text-sm transition-colors">
                            🚫 Ban Geçmişi
                        </button>
                        <button @click="activeTab = 'reports'" 
                            :class="activeTab === 'reports' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="px-6 py-4 border-b-2 font-medium text-sm transition-colors">
                            ⚠️ Raporlar
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <!-- Aktivite Timeline Tab -->
                    <div x-show="activeTab === 'activity'" class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Son Aktiviteler</h3>
                        
                        @php
                            // Son aktiviteleri birleştir (basit örnek)
                            $activities = collect();
                            
                            // XP eventleri
                            foreach($user->xpEvents()->latest()->take(10)->get() as $event) {
                                $activities->push([
                                    'type' => 'xp',
                                    'icon' => '⭐',
                                    'color' => 'purple',
                                    'title' => 'XP Kazandı',
                                    'description' => $event->description . ' (+' . $event->amount . ' XP)',
                                    'date' => $event->created_at
                                ]);
                            }
                            
                            // LFG ilanları
                            foreach($user->lfgPosts()->latest()->take(5)->get() as $post) {
                                $activities->push([
                                    'type' => 'lfg',
                                    'icon' => '🎯',
                                    'color' => 'blue',
                                    'title' => 'LFG İlanı Oluşturdu',
                                    'description' => $post->title,
                                    'date' => $post->created_at
                                ]);
                            }
                            
                            // Yorumlar
                            foreach($user->comments()->latest()->take(5)->get() as $comment) {
                                $activities->push([
                                    'type' => 'comment',
                                    'icon' => '💬',
                                    'color' => 'green',
                                    'title' => 'Yorum Yaptı',
                                    'description' => \Str::limit($comment->content, 50),
                                    'date' => $comment->created_at
                                ]);
                            }
                            
                            $activities = $activities->sortByDesc('date')->take(20);
                        @endphp
                        
                        @if($activities->count() > 0)
                            <div class="space-y-3">
                                @foreach($activities as $activity)
                                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="w-10 h-10 bg-{{ $activity['color'] }}-100 rounded-full flex items-center justify-center text-xl flex-shrink-0">
                                        {{ $activity['icon'] }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900">{{ $activity['title'] }}</p>
                                        <p class="text-sm text-gray-600 truncate">{{ $activity['description'] }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $activity['date']->diffForHumans() }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Henüz aktivite yok</p>
                            </div>
                        @endif
                    </div>

                    <!-- İçerikler Tab -->
                    <div x-show="activeTab === 'content'" class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Kullanıcının İçerikleri</h3>
                        
                        <!-- LFG İlanları -->
                        @if($user->lfgPosts->count() > 0)
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-900 mb-3">🎯 LFG İlanları ({{ $user->lfgPosts->count() }})</h4>
                            <div class="space-y-2">
                                @foreach($user->lfgPosts()->latest()->take(5)->get() as $post)
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate">{{ $post->title }}</p>
                                        <p class="text-xs text-gray-600">{{ $post->created_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                    <a href="{{ route('lfg.show', $post->id) }}" target="_blank"
                                        class="ml-4 px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                        Görüntüle
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <!-- Klanlar -->
                        @if($user->ownedClans->count() > 0)
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-900 mb-3">🛡️ Klanlar ({{ $user->ownedClans->count() }})</h4>
                            <div class="space-y-2">
                                @foreach($user->ownedClans as $clan)
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate">{{ $clan->name }}</p>
                                        <p class="text-xs text-gray-600">{{ $clan->members_count ?? 0 }} üye</p>
                                    </div>
                                    <a href="{{ route('clans.show', $clan->slug) }}" target="_blank"
                                        class="ml-4 px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                        Görüntüle
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <!-- Rehberler -->
                        @if($user->guidePosts->count() > 0)
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-900 mb-3">📚 Rehberler ({{ $user->guidePosts->count() }})</h4>
                            <div class="space-y-2">
                                @foreach($user->guidePosts()->latest()->take(5)->get() as $guide)
                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate">{{ $guide->title }}</p>
                                        <p class="text-xs text-gray-600">{{ $guide->created_at->format('d.m.Y') }}</p>
                                    </div>
                                    <a href="{{ route('guide.show', $guide->id) }}" target="_blank"
                                        class="ml-4 px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                                        Görüntüle
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        @if($user->lfgPosts->count() == 0 && $user->ownedClans->count() == 0 && $user->guidePosts->count() == 0)
                        <div class="text-center py-12 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p>Henüz içerik yok</p>
                        </div>
                        @endif
                    </div>

                    <!-- Ban Geçmişi Tab -->
                    <div x-show="activeTab === 'bans'">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Ban Geçmişi</h3>
                        
                        @php
                            // Admin activity log'lardan ban geçmişini al
                            $banHistory = \App\Models\AdminActivityLog::where('target_type', 'User')
                                ->where('target_id', $user->id)
                                ->whereIn('action', ['ban_user', 'unban_user', 'bulk_ban_user', 'bulk_unban_user'])
                                ->with('admin')
                                ->latest()
                                ->take(20)
                                ->get();
                        @endphp
                        
                        @if($banHistory->count() > 0)
                            <div class="space-y-3">
                                @foreach($banHistory as $log)
                                <div class="flex items-start gap-4 p-4 rounded-lg {{ str_contains($log->action, 'ban_user') && !str_contains($log->action, 'unban') ? 'bg-red-50' : 'bg-green-50' }}">
                                    <div class="w-10 h-10 {{ str_contains($log->action, 'ban_user') && !str_contains($log->action, 'unban') ? 'bg-red-100' : 'bg-green-100' }} rounded-full flex items-center justify-center text-xl flex-shrink-0">
                                        {{ str_contains($log->action, 'ban_user') && !str_contains($log->action, 'unban') ? '🚫' : '✅' }}
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">
                                            {{ str_contains($log->action, 'ban_user') && !str_contains($log->action, 'unban') ? 'Banlandı' : 'Ban Kaldırıldı' }}
                                        </p>
                                        <p class="text-sm text-gray-600">{{ $log->details }}</p>
                                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                            <span>👤 {{ $log->admin->name ?? 'Sistem' }}</span>
                                            <span>📅 {{ $log->created_at->format('d.m.Y H:i') }}</span>
                                            <span>🕐 {{ $log->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Ban geçmişi yok</p>
                            </div>
                        @endif
                    </div>

                    <!-- Raporlar Tab -->
                    <div x-show="activeTab === 'reports'">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Rapor Geçmişi</h3>
                        
                        @php
                            // Kullanıcının yaptığı raporlar
                            $userReports = $user->reports()->with('reportable')->latest()->take(10)->get();
                            // Kullanıcı hakkında yapılan raporlar
                            $reportsAboutUser = \App\Models\Report::where('reportable_type', 'User')
                                ->where('reportable_id', $user->id)
                                ->with('reporter')
                                ->latest()
                                ->take(10)
                                ->get();
                        @endphp
                        
                        <div class="space-y-6">
                            <!-- Kullanıcı Hakkında Yapılan Raporlar -->
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-3">⚠️ Kullanıcı Hakkında Raporlar ({{ $reportsAboutUser->count() }})</h4>
                                @if($reportsAboutUser->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($reportsAboutUser as $report)
                                        <div class="p-4 bg-red-50 rounded-lg">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <p class="font-semibold text-gray-900">{{ $report->reason }}</p>
                                                    <p class="text-sm text-gray-600 mt-1">{{ $report->description }}</p>
                                                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                                        <span>👤 {{ $report->reporter->name ?? 'Anonim' }}</span>
                                                        <span>📅 {{ $report->created_at->format('d.m.Y H:i') }}</span>
                                                        <span class="px-2 py-1 rounded {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($report->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                                            {{ $report->status }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 italic">Kullanıcı hakkında rapor yok</p>
                                @endif
                            </div>
                            
                            <!-- Kullanıcının Yaptığı Raporlar -->
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-3">📝 Kullanıcının Yaptığı Raporlar ({{ $userReports->count() }})</h4>
                                @if($userReports->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($userReports as $report)
                                        <div class="p-4 bg-blue-50 rounded-lg">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <p class="font-semibold text-gray-900">{{ $report->reason }}</p>
                                                    <p class="text-sm text-gray-600 mt-1">{{ $report->description }}</p>
                                                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                                        <span>📅 {{ $report->created_at->format('d.m.Y H:i') }}</span>
                                                        <span class="px-2 py-1 rounded {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($report->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                                            {{ $report->status }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 italic">Kullanıcı henüz rapor yapmamış</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Sağ Kolon: İşlemler -->
        <div class="lg:col-span-1 space-y-6">
            <!-- İşlemler Kartı -->
            <div class="bg-white rounded-xl shadow-lg p-6 sticky top-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    İşlemler
                </h3>
                
                @if($user->id !== auth()->id())
                <div class="space-y-3">
                    <!-- Ban/Unban -->
                    @if($user->status === 'active')
                        <form action="{{ route('admin.users.ban', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                onclick="return confirm('Bu kullanıcıyı banlamak istediğinizden emin misiniz?')"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                Kullanıcıyı Banla
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.users.unban', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Banı Kaldır
                            </button>
                        </form>
                    @endif

                    <!-- Admin Yap/Kaldır -->
                    @if($user->is_admin)
                        <form action="{{ route('admin.users.remove-admin', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                onclick="return confirm('Admin yetkisini kaldırmak istediğinizden emin misiniz?')"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-medium">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                Admin Yetkisini Kaldır
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.users.make-admin', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                onclick="return confirm('Bu kullanıcıyı admin yapmak istediğinizden emin misiniz?')"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                Admin Yap
                            </button>
                        </form>
                    @endif

                    <!-- Sil -->
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            onclick="return confirm('Bu kullanıcıyı kalıcı olarak silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Kullanıcıyı Sil
                        </button>
                    </form>
                </div>
                @else
                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4 text-center">
                        <svg class="w-12 h-12 mx-auto mb-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="text-sm font-medium text-yellow-800">
                            Kendi hesabınızda işlem yapamazsınız
                        </p>
                    </div>
                @endif
            </div>

            <!-- Roller Kartı -->
            @if($user->adminRoles->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    Admin Rolleri
                </h3>
                
                <div class="space-y-2">
                    @foreach($user->adminRoles as $role)
                    <div class="flex items-center gap-3 p-3 bg-purple-50 rounded-lg">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-xl" style="background-color: {{ $role->color }}20">
                            {{ $role->icon }}
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $role->name }}</p>
                            @if($role->description)
                                <p class="text-xs text-gray-600">{{ $role->description }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Rozetler Kartı -->
            @if($user->badges->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    Rozetler ({{ $user->badges->count() }})
                </h3>
                
                <div class="grid grid-cols-3 gap-3">
                    @foreach($user->badges->take(9) as $badge)
                    <div class="text-center p-3 bg-yellow-50 rounded-lg" title="{{ $badge->name }}">
                        <div class="text-3xl mb-1">{{ $badge->icon }}</div>
                        <p class="text-xs text-gray-600 truncate">{{ $badge->name }}</p>
                    </div>
                    @endforeach
                </div>
                
                @if($user->badges->count() > 9)
                <p class="text-center text-sm text-gray-500 mt-3">
                    +{{ $user->badges->count() - 9 }} rozet daha
                </p>
                @endif
            </div>
            @endif

            <!-- Hızlı İstatistikler -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <h3 class="text-lg font-bold mb-4">📊 Hızlı İstatistikler</h3>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm opacity-90">Hesap Yaşı</span>
                        <span class="font-bold">{{ $user->created_at->diffInDays(now()) }} gün</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm opacity-90">Toplam İçerik</span>
                        <span class="font-bold">
                            {{ $user->lfgPosts->count() + $user->ownedClans->count() + $user->guidePosts->count() + $user->communityPosts->count() }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm opacity-90">Toplam Yorum</span>
                        <span class="font-bold">{{ $user->comments->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm opacity-90">Arkadaş Sayısı</span>
                        <span class="font-bold">{{ $user->friendships->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function userDetail() {
    return {
        activeTab: 'activity',
    }
}
</script>
@endpush

@endsection
