@extends('admin.layout')

@section('title', 'İlan Detayı')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.lfg-posts.index') }}" class="hover:text-orange-600">İlanlar</a>
            <span>/</span>
            <span>Detay</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $post->title }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Post Details -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">İlan Bilgileri</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Açıklama</label>
                        <p class="mt-1 text-gray-900">{{ $post->description }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Oyun</label>
                            <p class="mt-1 text-gray-900">{{ $post->game->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Mod</label>
                            <p class="mt-1 text-gray-900">{{ $post->mode ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Rank Aralığı</label>
                            <p class="mt-1 text-gray-900">{{ $post->min_rank }} - {{ $post->max_rank }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Şehir</label>
                            <p class="mt-1 text-gray-900">{{ $post->city ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Oyun Stili</label>
                            <p class="mt-1 text-gray-900">{{ $post->play_style_tag ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Mikrofon</label>
                            <p class="mt-1 text-gray-900">{{ $post->microphone_required ? 'Gerekli' : 'Opsiyonel' }}</p>
                        </div>
                    </div>

                    @if($post->admin_notes)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <label class="text-sm font-medium text-yellow-800">Admin Notları</label>
                        <p class="mt-1 text-yellow-900">{{ $post->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Applications -->
            @if($post->applications->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Başvurular ({{ $post->applications->count() }})</h2>
                
                <div class="space-y-3">
                    @foreach($post->applications as $application)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
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
                        <p class="mt-2 text-sm text-gray-600">{{ $application->message }}</p>
                        @endif
                        <div class="mt-2 text-xs text-gray-400">
                            {{ $application->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Reports -->
            @if($post->reports && $post->reports->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4 text-red-600">Raporlar ({{ $post->reports->count() }})</h2>
                
                <div class="space-y-3">
                    @foreach($post->reports as $report)
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
            <!-- User Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">İlan Sahibi</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">İsim</label>
                        <p class="mt-1 text-gray-900">{{ $post->user->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-gray-900">{{ $post->user->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nickname</label>
                        <p class="mt-1 text-gray-900">{{ $post->user->profile->nickname ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Rank</label>
                        <p class="mt-1 text-gray-900">{{ $post->user->profile->rank ?? '-' }}</p>
                    </div>
                    <a href="{{ route('admin.users.show', $post->user_id) }}" 
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
                        <label class="text-sm font-medium text-gray-500">Durum</label>
                        <p class="mt-1">
                            @if($post->status === 'open')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Açık</span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Kapalı</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Öne Çıkan</label>
                        <p class="mt-1">
                            @if($post->is_featured)
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Evet ⭐</span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Hayır</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Oluşturulma</label>
                        <p class="mt-1 text-gray-900">{{ $post->created_at->format('d.m.Y H:i') }}</p>
                    </div>

                    <div class="pt-4 space-y-2">
                        <a href="{{ route('admin.lfg-posts.edit', $post->id) }}" 
                            class="block w-full text-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                            Düzenle
                        </a>

                        <form action="{{ route('admin.lfg-posts.toggle-featured', $post->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                                {{ $post->is_featured ? 'Öne Çıkarmadan Kaldır' : 'Öne Çıkar' }}
                            </button>
                        </form>

                        @if($post->status === 'open')
                        <form action="{{ route('admin.lfg-posts.close', $post->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                                İlanı Kapat
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('admin.lfg-posts.destroy', $post->id) }}" method="POST"
                            onsubmit="return confirm('İlan silinecek. Emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                İlanı Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
