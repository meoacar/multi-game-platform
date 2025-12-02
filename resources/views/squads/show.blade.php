@extends('layouts.app')

@section('title', $squad->name)

@section('content')
<!-- Hero Banner -->
<div class="relative bg-gradient-to-r from-green-600 via-emerald-600 to-green-700 text-white overflow-hidden">
    <div class="absolute inset-0 bg-black/20"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-5xl font-black mb-2">{{ $squad->name }}</h1>
                <p class="text-xl text-white/90">Takım Detayları</p>
            </div>
            <a href="{{ route('squads.index') }}" 
                class="bg-white/20 hover:bg-white/30 text-white px-6 py-3 rounded-xl font-bold transition-all">
                ← Geri Dön
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ana İçerik -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Takım Bilgileri -->
            <div class="bg-gradient-to-br from-gray-900/40 to-black/40 backdrop-blur-sm rounded-2xl border-2 border-green-500/30 overflow-hidden shadow-xl">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4 border-b-2 border-green-500/50">
                    <h2 class="text-2xl font-black text-white flex items-center gap-3">
                        <span class="text-3xl">ℹ️</span>
                        TAKIM BİLGİLERİ
                    </h2>
                </div>

                <div class="p-6">
                    @if($squad->description)
                    <div class="mb-6">
                        <h3 class="text-white font-bold mb-2">Açıklama</h3>
                        <p class="text-gray-300 leading-relaxed">{{ $squad->description }}</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-black/30 p-4 rounded-xl border border-green-500/20">
                            <p class="text-gray-400 text-sm mb-1">Maksimum Üye</p>
                            <p class="text-white font-bold text-lg">{{ $squad->max_members }} Kişi</p>
                        </div>

                        @if($squad->game_mode)
                        <div class="bg-black/30 p-4 rounded-xl border border-green-500/20">
                            <p class="text-gray-400 text-sm mb-1">Oyun Modu</p>
                            <p class="text-white font-bold text-lg">{{ $squad->game_mode }}</p>
                        </div>
                        @endif

                        @if($squad->rank_requirement)
                        <div class="bg-black/30 p-4 rounded-xl border border-green-500/20">
                            <p class="text-gray-400 text-sm mb-1">Rütbe Gereksinimi</p>
                            <p class="text-white font-bold text-lg">{{ $squad->rank_requirement }}</p>
                        </div>
                        @endif

                        @if($squad->region)
                        <div class="bg-black/30 p-4 rounded-xl border border-green-500/20">
                            <p class="text-gray-400 text-sm mb-1">Bölge</p>
                            <p class="text-white font-bold text-lg">{{ $squad->region }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Üyeler -->
            <div class="bg-gradient-to-br from-gray-900/40 to-black/40 backdrop-blur-sm rounded-2xl border-2 border-green-500/30 overflow-hidden shadow-xl">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4 border-b-2 border-green-500/50">
                    <h2 class="text-2xl font-black text-white flex items-center gap-3">
                        <span class="text-3xl">👥</span>
                        ÜYELER ({{ $squad->members->count() }}/{{ $squad->max_members }})
                    </h2>
                </div>

                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($squad->members as $member)
                        <div class="bg-black/30 backdrop-blur-sm rounded-xl border border-green-500/20 p-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl flex items-center justify-center text-white text-xl font-bold">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-white font-bold text-lg">{{ $member->name }}</p>
                                    <p class="text-gray-400 text-sm">
                                        @if($member->pivot->role === 'leader')
                                            👑 Lider
                                        @else
                                            Üye
                                        @endif
                                        • {{ \Carbon\Carbon::parse($member->pivot->joined_at)->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('profile.show', $member->id) }}" 
                                class="bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30 text-blue-400 px-4 py-2 rounded-lg font-bold transition-all">
                                Profil →
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Lider Kartı -->
            <div class="bg-gradient-to-br from-orange-900/40 to-red-900/40 backdrop-blur-sm rounded-2xl border-2 border-orange-500/30 overflow-hidden shadow-xl">
                <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-4 border-b-2 border-orange-500/50">
                    <h3 class="text-xl font-black text-white flex items-center gap-2">
                        👑 LİDER
                    </h3>
                </div>
                <div class="p-6 text-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center text-white text-4xl font-bold mx-auto mb-4 shadow-2xl">
                        {{ substr($squad->leader->name, 0, 1) }}
                    </div>
                    <h4 class="text-white font-bold text-xl mb-2">{{ $squad->leader->name }}</h4>
                    <p class="text-gray-400 text-sm mb-4">{{ $squad->leader->email }}</p>
                    <a href="{{ route('profile.show', $squad->leader->id) }}" 
                        class="inline-block bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg">
                        Profili Görüntüle
                    </a>
                </div>
            </div>

            <!-- Durum -->
            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl border-2 border-gray-700 overflow-hidden shadow-xl">
                <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4 border-b-2 border-gray-600">
                    <h3 class="text-xl font-black text-white flex items-center gap-2">
                        📊 DURUM
                    </h3>
                </div>
                <div class="p-6 text-center">
                    @if($squad->isFull())
                        <div class="text-6xl mb-4">🔒</div>
                        <h4 class="text-2xl font-black text-red-400 mb-2">TAKIM DOLU</h4>
                        <p class="text-gray-400">Tüm pozisyonlar dolu</p>
                    @else
                        <div class="text-6xl mb-4">✅</div>
                        <h4 class="text-2xl font-black text-green-400 mb-2">YER VAR</h4>
                        <p class="text-gray-400 mb-4">{{ $squad->max_members - $squad->members->count() }} pozisyon açık</p>
                        @auth
                            @if(!$squad->hasMember(auth()->id()))
                                <button class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-black py-3 rounded-xl transition-all shadow-lg">
                                    TAKIMA KATIL
                                </button>
                            @else
                                <div class="bg-blue-500/20 border border-blue-500/30 rounded-xl px-4 py-3">
                                    <span class="text-blue-400 font-bold">✓ Zaten Üyesin</span>
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" 
                                class="block w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold py-3 rounded-xl transition-all text-center">
                                Katılmak İçin Giriş Yap
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
