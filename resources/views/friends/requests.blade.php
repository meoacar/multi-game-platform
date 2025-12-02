@extends('layouts.app')

@section('content')
<!-- Arka Plan -->
<div class="fixed inset-0 -z-10">
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-purple-900/95 via-gray-900/98 to-pink-900/95"></div>
    
    <!-- Arka Plan Resmi -->
    <div class="absolute inset-0 opacity-20">
        <img src="{{ asset('arkaplan/' . rand(1, 14) . '.jpg') }}" 
             alt="Background" 
             class="w-full h-full object-cover">
    </div>
    
    <!-- Animated Gradient Circles -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-pink-500/30 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
</div>

<div class="py-12 relative" x-data="{ activeTab: 'incoming' }">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl mb-6 shadow-lg shadow-purple-500/50">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-3">
                Arkadaşlık İstekleri
            </h1>
            <p class="text-gray-300 text-lg">Gelen ve giden arkadaşlık isteklerini yönet 📬</p>
            
            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('friends.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-700/50 hover:bg-gray-700 border-2 border-gray-600 hover:border-gray-500 text-white rounded-xl font-semibold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Arkadaşlarıma Dön
                </a>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-purple-500/10 p-2 border-2 border-purple-500/20 mb-8">
            <div class="grid grid-cols-2 gap-2">
                <button @click="activeTab = 'incoming'" 
                    :class="activeTab === 'incoming' ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg shadow-purple-500/50' : 'text-gray-400 hover:text-white hover:bg-gray-700/50'"
                    class="px-6 py-4 rounded-xl font-bold transition-all duration-300 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                    Gelen İstekler
                    @if($incomingRequests->count() > 0)
                    <span class="px-2.5 py-1 bg-red-500 text-white text-xs rounded-full font-bold animate-pulse">{{ $incomingRequests->count() }}</span>
                    @endif
                </button>
                <button @click="activeTab = 'outgoing'"
                    :class="activeTab === 'outgoing' ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg shadow-purple-500/50' : 'text-gray-400 hover:text-white hover:bg-gray-700/50'"
                    class="px-6 py-4 rounded-xl font-bold transition-all duration-300 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                    Giden İstekler
                    @if($outgoingRequests->count() > 0)
                    <span class="px-2.5 py-1 bg-blue-500 text-white text-xs rounded-full font-bold">{{ $outgoingRequests->count() }}</span>
                    @endif
                </button>
            </div>
        </div>

        <!-- Incoming Requests -->
        <div x-show="activeTab === 'incoming'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            @if($incomingRequests->count() > 0)
            <div class="space-y-4">
                @foreach($incomingRequests as $request)
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-purple-500/10 border-2 border-gray-700 hover:border-purple-500/50 transition-all duration-300 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                            <!-- User Info -->
                            <div class="flex items-center gap-4 flex-1">
                                <!-- Avatar -->
                                <div class="relative">
                                    <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center text-white text-3xl font-bold shadow-lg shadow-purple-500/50">
                                        {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                    </div>
                                    <div class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center animate-pulse">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Info -->
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-white mb-1">{{ $request->user->name }}</h3>
                                    @if($request->user->profile)
                                    <p class="text-sm text-gray-400 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ $request->user->profile->nickname ?? 'PUBG Nick yok' }}
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        @if($request->user->profile->rank)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-full bg-blue-600/30 text-blue-300 border border-blue-500/50 font-semibold">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            {{ $request->user->profile->rank }}
                                        </span>
                                        @endif
                                        @if($request->user->profile->city)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-full bg-green-600/30 text-green-300 border border-green-500/50 font-semibold">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $request->user->profile->city }}
                                        </span>
                                        @endif
                                    </div>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $request->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col sm:flex-row gap-2 lg:w-auto w-full">
                                <form action="{{ route('friends.accept', $request->id) }}" method="POST" class="flex-1 lg:flex-none">
                                    @csrf
                                    <button type="submit" class="w-full lg:w-auto flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-green-500/30 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Kabul Et
                                    </button>
                                </form>
                                <form action="{{ route('friends.reject', $request->id) }}" method="POST" class="flex-1 lg:flex-none">
                                    @csrf
                                    <button type="submit" class="w-full lg:w-auto flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-red-500/30 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Reddet
                                    </button>
                                </form>
                                <a href="{{ route('profile.show', $request->user->id) }}" class="flex-1 lg:flex-none flex items-center justify-center gap-2 bg-gray-700/50 hover:bg-gray-700 border-2 border-gray-600 hover:border-gray-500 text-white px-6 py-3 rounded-xl font-bold transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <!-- Empty State -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-purple-500/10 p-12 border-2 border-gray-700 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-700/50 rounded-2xl mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Gelen istek yok</h3>
                <p class="text-gray-400">Henüz size arkadaşlık isteği gönderen olmadı.</p>
            </div>
            @endif
        </div>

        <!-- Outgoing Requests -->
        <div x-show="activeTab === 'outgoing'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            @if($outgoingRequests->count() > 0)
            <div class="space-y-4">
                @foreach($outgoingRequests as $request)
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-blue-500/10 border-2 border-gray-700 hover:border-blue-500/50 transition-all duration-300 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                            <!-- User Info -->
                            <div class="flex items-center gap-4 flex-1">
                                <!-- Avatar -->
                                <div class="relative">
                                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center text-white text-3xl font-bold shadow-lg shadow-blue-500/50">
                                        {{ strtoupper(substr($request->friend->name, 0, 1)) }}
                                    </div>
                                </div>

                                <!-- Info -->
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-white mb-1">{{ $request->friend->name }}</h3>
                                    @if($request->friend->profile)
                                    <p class="text-sm text-gray-400 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ $request->friend->profile->nickname ?? 'PUBG Nick yok' }}
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        @if($request->friend->profile->rank)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-full bg-blue-600/30 text-blue-300 border border-blue-500/50 font-semibold">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            {{ $request->friend->profile->rank }}
                                        </span>
                                        @endif
                                        @if($request->friend->profile->city)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-full bg-green-600/30 text-green-300 border border-green-500/50 font-semibold">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $request->friend->profile->city }}
                                        </span>
                                        @endif
                                    </div>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $request->created_at->diffForHumans() }} gönderildi
                                    </p>
                                </div>
                            </div>

                            <!-- Status & Actions -->
                            <div class="flex flex-col sm:flex-row gap-2 lg:w-auto w-full items-center">
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-yellow-600/30 text-yellow-300 border border-yellow-500/50 font-bold">
                                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Bekliyor
                                </span>
                                <a href="{{ route('profile.show', $request->friend->id) }}" class="flex-1 lg:flex-none flex items-center justify-center gap-2 bg-gray-700/50 hover:bg-gray-700 border-2 border-gray-600 hover:border-gray-500 text-white px-6 py-3 rounded-xl font-bold transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <!-- Empty State -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-blue-500/10 p-12 border-2 border-gray-700 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-700/50 rounded-2xl mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Giden istek yok</h3>
                <p class="text-gray-400">Henüz kimseye arkadaşlık isteği göndermediniz.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
