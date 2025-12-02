@extends('layouts.app')

@section('title', $guide->title)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-lg shadow p-8">
        <!-- Başlık -->
        <h1 class="text-4xl font-bold mb-4">{{ $guide->title }}</h1>
        
        <!-- Yazar Bilgisi -->
        <div class="flex items-center justify-between mb-6 pb-6 border-b">
            <div class="flex items-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($guide->user->name) }}" 
                     class="w-12 h-12 rounded-full mr-3">
                <div>
                    <p class="font-semibold">{{ $guide->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $guide->created_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>
            
            <div class="flex gap-4 text-sm text-gray-500">
                <span>👁️ {{ $guide->views_count }}</span>
                <span>❤️ {{ $guide->likes_count }}</span>
            </div>
        </div>

        @if($guide->game)
            <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded mb-6">
                {{ $guide->game->name }}
            </span>
        @endif

        <!-- İçerik -->
        <div class="prose max-w-none mb-8">
            {!! nl2br(e($guide->content)) !!}
        </div>

        <!-- Beğen Butonu -->
        @auth
            <button onclick="likeGuide({{ $guide->id }})" 
                    class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600">
                ❤️ Beğen
            </button>
        @endauth

        <!-- Düzenle/Sil Butonları -->
        @can('update', $guide)
            <div class="mt-6 pt-6 border-t flex gap-4">
                <a href="{{ route('guides.edit', $guide->id) }}" 
                   class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                    Düzenle
                </a>
                <form action="{{ route('guides.destroy', $guide->id) }}" method="POST" 
                      onsubmit="return confirm('Rehberi silmek istediğinize emin misiniz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        Sil
                    </button>
                </form>
            </div>
        @endcan
    </div>

    <!-- Yorumlar -->
    <div class="mt-8 bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold mb-6">Yorumlar</h2>
        
        @auth
            <form action="{{ route('comments.store') }}" method="POST" class="mb-8">
                @csrf
                <input type="hidden" name="commentable_type" value="App\Models\GuidePost">
                <input type="hidden" name="commentable_id" value="{{ $guide->id }}">
                <textarea name="content" rows="3" 
                          class="w-full border rounded px-3 py-2 mb-2" 
                          placeholder="Yorumunuzu yazın..."></textarea>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Yorum Yap
                </button>
            </form>
        @endauth

        <div class="space-y-4">
            @forelse($guide->comments as $comment)
                <div class="border-l-4 border-gray-200 pl-4">
                    <div class="flex items-center mb-2">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->name) }}" 
                             class="w-8 h-8 rounded-full mr-2">
                        <div>
                            <p class="font-semibold">{{ $comment->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <p class="text-gray-700">{{ $comment->content }}</p>
                </div>
            @empty
                <p class="text-gray-500">Henüz yorum yok</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
function likeGuide(id) {
    fetch(`/api/v1/guides/${id}/like`, {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        alert('Beğenildi!');
        location.reload();
    });
}
</script>
@endpush
@endsection
