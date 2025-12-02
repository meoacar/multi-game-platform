@extends('admin.layout')

@section('title', 'Klan Detayı')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.clans.index') }}" class="hover:text-orange-600">Klanlar</a>
            <span>/</span>
            <span>Detay</span>
        </div>
        <div class="flex items-center gap-2">
            <h1 class="text-2xl font-bold text-gray-900">{{ $clan->name }}</h1>
            @if($clan->is_verified)
                <span class="text-blue-500 text-2xl" title="Doğrulanmış Klan">✓</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Clan Details -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Klan Bilgileri</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Açıklama</label>
                        <p class="mt-1 text-gray-900">{{ $clan->description }}</p>
                    </div>

                    @if($clan->requirements)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Gereksinimler</label>
                        <p class="mt-1 text-gray-900">{{ $clan->requirements }}</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Oyun</label>
                            <p class="mt-1 text-gray-900">{{ $clan->game->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Şehir</label>
                            <p class="mt-1 text-gray-900">{{ $clan->city ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Rank Aralığı</label>
                            <p class="mt-1 text-gray-900">{{ $clan->min_rank ?? '-' }} - {{ $clan->max_rank ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Yaş Aralığı</label>
                            <p class="mt-1 text-gray-900">{{ $clan->min_age_range ?? '-' }} - {{ $clan->max_age_range ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Üye Sayısı</label>
                            <p class="mt-1 text-gray-900">{{ $clan->member_count ?? 1 }} / {{ $clan->max_members ?? 50 }}</p>
                        </div>
                        @if($clan->discord_invite)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Discord</label>
                            <p class="mt-1 text-gray-900">{{ $clan->discord_invite }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Members -->
            @if($clan->members && $clan->members->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Üyeler ({{ $clan->members->count() }})</h2>
                
                <div class="space-y-3">
                    @foreach($clan->members as $member)
                    <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <div class="font-medium text-gray-900">{{ $member->name }}</div>
                            <div class="text-sm text-gray-500">{{ $member->profile->nickname ?? '-' }}</div>
                            @if($member->profile && $member->profile->rank)
                                <span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 rounded-full">
                                    {{ $member->profile->rank }}
                                </span>
                            @endif
                        </div>
                        <form action="{{ route('admin.clans.remove-member', $clan->id) }}" method="POST"
                            onsubmit="return confirm('Üye klandan çıkarılacak. Emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="user_id" value="{{ $member->id }}">
                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">
                                Çıkar
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Applications -->
            @if($clan->applications && $clan->applications->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Başvurular ({{ $clan->applications->count() }})</h2>
                
                <div class="space-y-3">
                    @foreach($clan->applications as $application)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <div class="font-medium text-gray-900">{{ $application->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $application->user->profile->nickname ?? '-' }}</div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $application->status === 'accepted' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ ucfirst($application->status) }}
                            </span>
                        </div>
                        @if($application->message)
                        <p class="text-sm text-gray-600 mb-2">{{ $application->message }}</p>
                        @endif
                        <div class="flex items-center justify-between">
                            <div class="text-xs text-gray-400">
                                {{ $application->created_at->diffForHumans() }}
                            </div>
                            <form action="{{ route('admin.clans.update-application-status', $application->id) }}" method="POST" class="flex gap-2">
                                @csrf
                                <select name="status" class="text-xs px-2 py-1 border border-gray-300 rounded">
                                    <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>Bekliyor</option>
                                    <option value="accepted" {{ $application->status === 'accepted' ? 'selected' : '' }}>Kabul</option>
                                    <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Red</option>
                                </select>
                                <button type="submit" class="text-xs px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    Güncelle
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Reports -->
            @if($clan->reports && $clan->reports->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4 text-red-600">Raporlar ({{ $clan->reports->count() }})</h2>
                
                <div class="space-y-3">
                    @foreach($clan->reports as $report)
                    <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-900">{{ $report->reporter->name }}</div>
                                <div class="text-sm text-gray-600">{{ $report->reason }}</div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                {{ $report->status }}
                            </span>
                        </div>
                        @if($report->description)
                        <p class="mt-2 text-sm text-gray-700">{{ $report->description }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Leader Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Klan Lideri</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">İsim</label>
                        <p class="mt-1 text-gray-900">{{ $clan->leader->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-gray-900">{{ $clan->leader->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nickname</label>
                        <p class="mt-1 text-gray-900">{{ $clan->leader->profile->nickname ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Rank</label>
                        <p class="mt-1 text-gray-900">{{ $clan->leader->profile->rank ?? '-' }}</p>
                    </div>
                    <a href="{{ route('admin.users.show', $clan->user_id) }}" 
                        class="block w-full text-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Kullanıcı Detayı
                    </a>
                </div>
            </div>

            <!-- Status & Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Durum & İşlemler</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Doğrulanmış</label>
                        <p class="mt-1">
                            @if($clan->is_verified)
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Evet ✓</span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Hayır</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Oluşturulma</label>
                        <p class="mt-1 text-gray-900">{{ $clan->created_at->format('d.m.Y H:i') }}</p>
                    </div>

                    <div class="pt-4 space-y-2">
                        <a href="{{ route('admin.clans.edit', $clan->id) }}" 
                            class="block w-full text-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                            Düzenle
                        </a>

                        <form action="{{ route('admin.clans.toggle-verified', $clan->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                {{ $clan->is_verified ? 'Doğrulamayı Kaldır' : 'Doğrula' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.clans.destroy', $clan->id) }}" method="POST"
                            onsubmit="return confirm('Klan silinecek. Emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                Klanı Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
