@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Üye Yönetimi</h1>
            <p class="text-gray-600 mt-1">{{ $squad->name }}</p>
        </div>
        <a href="{{ route('squads.show', $squad->slug) }}" class="text-blue-600 hover:text-blue-800">
            ← Takıma Dön
        </a>
    </div>

    <!-- Stats -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Mevcut Üye Sayısı</p>
                <p class="text-3xl font-bold text-gray-900">{{ $squad->members->count() }}/{{ $squad->max_members }}</p>
            </div>
            @if($squad->members->count() < $squad->max_members)
            <span class="px-4 py-2 rounded-full bg-green-100 text-green-800 font-medium">
                {{ $squad->max_members - $squad->members->count() }} Yer Mevcut
            </span>
            @else
            <span class="px-4 py-2 rounded-full bg-red-100 text-red-800 font-medium">
                Takım Dolu
            </span>
            @endif
        </div>
    </div>

    <!-- Add Member Form -->
    @if($squad->members->count() < $squad->max_members)
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Üye Ekle</h2>
        <form action="{{ route('squads.invite', $squad->slug) }}" method="POST" class="flex space-x-4">
            @csrf
            <div class="flex-1">
                <input type="number" name="user_id" placeholder="Kullanıcı ID" required
                    class="w-full border-gray-300 rounded-lg">
                <p class="mt-1 text-sm text-gray-500">Eklemek istediğiniz kullanıcının ID'sini girin</p>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Ekle
            </button>
        </form>
    </div>
    @endif

    <!-- Members List -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Takım Üyeleri</h2>
        <div class="space-y-3">
            @foreach($squad->members as $member)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <p class="font-medium text-gray-900">{{ $member->name }}</p>
                            @if($member->pivot->role === 'leader')
                            <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                Lider
                            </span>
                            @endif
                        </div>
                        @if($member->profile)
                        <p class="text-sm text-gray-600">{{ $member->profile->nickname ?? 'PUBG Nick yok' }}</p>
                        <div class="flex items-center space-x-3 mt-1 text-xs text-gray-500">
                            @if($member->profile->rank)
                            <span>Rank: {{ $member->profile->rank }}</span>
                            @endif
                            @if($member->profile->city)
                            <span>{{ $member->profile->city }}</span>
                            @endif
                        </div>
                        @endif
                        <p class="text-xs text-gray-500 mt-1">
                            Katılma: {{ \Carbon\Carbon::parse($member->pivot->joined_at)->format('d.m.Y') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <a href="{{ route('user.profile', $member->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        Profil
                    </a>
                    @if($member->id !== $squad->leader_id)
                    <form action="{{ route('squads.remove-member', [$squad->slug, $member->id]) }}" method="POST" onsubmit="return confirm('Bu üyeyi çıkarmak istediğinize emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                            Çıkar
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
