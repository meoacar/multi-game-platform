@extends('admin.layout')

@section('title', 'Rehber Detayı')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.guides.index') }}" class="hover:text-orange-600">Rehberler</a>
            <span>/</span>
            <span>Detay</span>
        </div>
        <div class="flex items-center gap-2">
            <h1 class="text-2xl font-bold text-gray-900">{{ $guide->title }}</h1>
            @if($guide->is_featured)
                <span class="text-yellow-500 text-2xl" title="Öne Çıkan">⭐</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Guide Content -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">İçerik</h2>
                
                <div class="prose max-w-none">
                    {!! nl2br(e($guide->content)) !!}
                </div>
            </div>

            <!-- Comments -->
            @if($guide->comments && $guide->comments->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Yorumlar ({{ $guide->comments->count() }})</h2>
                
                <div class="space-y-3">
                    @foreach($guide->comments as $comment)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <div class="font-medium text-gray-900">{{ $comment->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $comment->user->profile->nickname ?? '-' }}</div>
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $comment->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <p class="text-sm text-gray-600">{{ $comment->content }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Reports -->
            @if($guide->reports && $guide->reports->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4 text-red-600">Raporlar ({{ $guide->reports->count() }})</h2>
                
                <div class="space-y-3">
                    @foreach($guide->reports as $report)
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
            <!-- Author Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Yazar</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">İsim</label>
                        <p class="mt-1 text-gray-900">{{ $guide->user->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-gray-900">{{ $guide->user->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nickname</label>
                        <p class="mt-1 text-gray-900">{{ $guide->user->profile->nickname ?? '-' }}</p>
                    </div>
                    <a href="{{ route('admin.users.show', $guide->user_id) }}" 
                        class="block w-full text-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Kullanıcı Detayı
                    </a>
                </div>
            </div>

            <!-- Guide Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Rehber Bilgileri</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Oyun</label>
                        <p class="mt-1 text-gray-900">{{ $guide->game->name ?? 'Genel' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Görüntülenme</label>
                        <p class="mt-1 text-gray-900">{{ $guide->views_count ?? 0 }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Beğeni</label>
                        <p class="mt-1 text-gray-900">{{ $guide->likes_count ?? 0 }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Oluşturulma</label>
                        <p class="mt-1 text-gray-900">{{ $guide->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Status & Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Durum & İşlemler</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Yayın Durumu</label>
                        <p class="mt-1">
                            @if($guide->is_published)
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Yayında</span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Taslak</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Öne Çıkan</label>
                        <p class="mt-1">
                            @if($guide->is_featured)
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Evet ⭐</span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Hayır</span>
                            @endif
                        </p>
                    </div>

                    <div class="pt-4 space-y-2">
                        <a href="{{ route('admin.guides.edit', $guide->id) }}" 
                            class="block w-full text-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                            Düzenle
                        </a>

                        <form action="{{ route('admin.guides.toggle-published', $guide->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                {{ $guide->is_published ? 'Yayından Kaldır' : 'Yayınla' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.guides.toggle-featured', $guide->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                                {{ $guide->is_featured ? 'Öne Çıkarmadan Kaldır' : 'Öne Çıkar' }}
                            </button>
                        </form>

                        <a href="{{ route('guides.show', $guide->slug) }}" target="_blank"
                            class="block w-full text-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Önizle
                        </a>

                        <form action="{{ route('admin.guides.destroy', $guide->id) }}" method="POST"
                            onsubmit="return confirm('Rehber silinecek. Emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                Rehberi Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
