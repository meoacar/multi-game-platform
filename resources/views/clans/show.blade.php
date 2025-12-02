@extends('layouts.app')

@section('title', $clan->name . ' - Klan Detayı')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ana İçerik -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Başlık ve Doğrulama -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $clan->name }}</h1>
                        @if($clan->is_verified)
                            <span class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                ✓ Doğrulanmış Klan
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Oyun ve Bilgiler -->
                <div class="flex items-center space-x-4 text-sm text-gray-600 mb-6">
                    <span class="font-semibold text-blue-600">{{ $clan->game->name }}</span>
                    <span>•</span>
                    <span>👥 {{ $clan->member_count }}/{{ $clan->max_members ?? 50 }} üye</span>
                    <span>•</span>
                    <span>📅 {{ $clan->created_at->format('d.m.Y') }}</span>
                </div>

                <!-- Açıklama -->
                <div class="prose max-w-none mb-6">
                    <h3 class="text-lg font-semibold mb-2">Hakkında</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $clan->description }}</p>
                </div>

                <!-- Gereksinimler -->
                @if($clan->requirements)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">📋 Gereksinimler</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 whitespace-pre-line">{{ $clan->requirements }}</p>
                    </div>
                </div>
                @endif

                <!-- Detaylar -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    @if($clan->min_rank || $clan->max_rank)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">🏆 Rütbe</h4>
                        <p class="text-gray-700">
                            {{ $clan->min_rank ?? 'Belirtilmemiş' }} 
                            @if($clan->max_rank) - {{ $clan->max_rank }} @endif
                        </p>
                    </div>
                    @endif

                    @if($clan->city)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">📍 Şehir</h4>
                        <p class="text-gray-700">{{ $clan->city }}</p>
                    </div>
                    @endif

                    @if($clan->min_age_range || $clan->max_age_range)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">👤 Yaş Aralığı</h4>
                        <p class="text-gray-700">
                            {{ $clan->min_age_range ?? '?' }} - {{ $clan->max_age_range ?? '?' }}
                        </p>
                    </div>
                    @endif

                    @if($clan->discord_invite)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">💬 Discord</h4>
                        <a href="{{ $clan->discord_invite }}" target="_blank" 
                            class="text-blue-600 hover:underline">
                            Discord Sunucusu
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Başvuru Formu -->
                @auth
                    @if(!$isMember && $clan->user_id !== auth()->id())
                        @if($userApplication)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-blue-800 font-semibold">
                                    ✅ Bu klana başvurdunuz
                                    <span class="text-sm font-normal">
                                        ({{ $userApplication->created_at->diffForHumans() }})
                                    </span>
                                </p>
                                @if($userApplication->status !== 'pending')
                                    <p class="text-sm mt-2">
                                        Durum: 
                                        <span class="font-semibold {{ $userApplication->status === 'accepted' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $userApplication->status === 'accepted' ? 'Kabul Edildi' : 'Reddedildi' }}
                                        </span>
                                    </p>
                                @endif
                            </div>
                        @elseif(!$clan->isFull())
                            <form action="{{ route('clans.apply', $clan->slug) }}" method="POST" class="bg-gray-50 p-6 rounded-lg">
                                @csrf
                                <h3 class="text-lg font-semibold mb-4">Bu Klana Başvur</h3>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Mesajınız (Opsiyonel)
                                    </label>
                                    <textarea name="message" rows="3" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Kendinizi tanıtın..."></textarea>
                                </div>
                                <button type="submit" 
                                    class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                                    📝 Başvur
                                </button>
                            </form>
                        @else
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                                <p class="text-red-800 font-semibold">Klan dolu</p>
                            </div>
                        @endif
                    @endif

                    @if($isMember)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                            <p class="text-green-800 font-semibold">✅ Bu klanın üyesisiniz</p>
                        </div>
                    @endif

                    @if($clan->user_id === auth()->id())
                        <div class="flex space-x-4">
                            <a href="{{ route('clans.applications', $clan->slug) }}" 
                                class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 text-center font-semibold">
                                📋 Başvuruları Görüntüle
                            </a>
                            <a href="{{ route('clans.edit', $clan->slug) }}" 
                                class="flex-1 bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 text-center font-semibold">
                                ✏️ Düzenle
                            </a>
                        </div>
                    @endif
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                        <p class="text-yellow-800">
                            Bu klana başvurmak için <a href="#" class="font-semibold underline">giriş yapın</a>
                        </p>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Klan Lideri -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">👑 Klan Lideri</h3>
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr($clan->leader->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-semibold">{{ $clan->leader->name }}</p>
                        @if($clan->leader->profile)
                            <p class="text-sm text-gray-600">{{ $clan->leader->profile->rank ?? 'Rütbe belirtilmemiş' }}</p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('profile.show', $clan->leader->id) }}" 
                    class="block w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 text-center">
                    Profili Görüntüle
                </a>
            </div>

            <!-- Üyeler -->
            @if($clan->members->isNotEmpty())
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">👥 Üyeler ({{ $clan->member_count }})</h3>
                <div class="space-y-3">
                    @foreach($clan->members->take(10) as $member)
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center text-white text-sm font-bold">
                            {{ substr($member->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold">{{ $member->name }}</p>
                        </div>
                    </div>
                    @endforeach
                    @if($clan->member_count > 10)
                        <p class="text-sm text-gray-500 text-center mt-2">
                            +{{ $clan->member_count - 10 }} üye daha
                        </p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
