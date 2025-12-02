@extends('admin.layout')

@section('title', 'Topluluk Gönderileri')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Topluluk Gönderileri</h1>
        <p class="text-gray-600 mt-1">Tanışma ve genel gönderileri yönet</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-purple-100 text-sm mb-1">Toplam Gönderi</p>
            <p class="text-3xl font-bold">{{ number_format($stats['total']) }}</p>
            <p class="text-purple-100 text-xs mt-1">Tüm zamanlar</p>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-yellow-100 text-sm mb-1">Öne Çıkan</p>
            <p class="text-3xl font-bold">{{ number_format($stats['featured']) }}</p>
            <p class="text-yellow-100 text-xs mt-1">Featured</p>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-blue-100 text-sm mb-1">Tanışma</p>
            <p class="text-3xl font-bold">{{ number_format($stats['intro_posts']) }}</p>
            <p class="text-blue-100 text-xs mt-1">Intro posts</p>
        </div>

        <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-gray-100 text-sm mb-1">Genel</p>
            <p class="text-3xl font-bold">{{ number_format($stats['general_posts']) }}</p>
            <p class="text-gray-100 text-xs mt-1">General posts</p>
        </div>

        <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-pink-100 text-sm mb-1">Bugün</p>
            <p class="text-3xl font-bold">{{ number_format($stats['today']) }}</p>
            <p class="text-pink-100 text-xs mt-1">Yeni gönderi</p>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-gray-600 text-sm mb-1">Bu Hafta</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['this_week']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-gray-600 text-sm mb-1">Bu Ay</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['this_month']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-gray-600 text-sm mb-1">Toplam Beğeni</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_likes']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-gray-600 text-sm mb-1">Toplam Yorum</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_comments']) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ara..." class="px-3 py-2 border rounded-lg">
            <select name="type" class="px-3 py-2 border rounded-lg">
                <option value="">Tüm Tipler</option>
                <option value="intro" {{ request('type') == 'intro' ? 'selected' : '' }}>Tanışma</option>
                <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>Genel</option>
            </select>
            <select name="is_featured" class="px-3 py-2 border rounded-lg">
                <option value="">Tümü</option>
                <option value="yes" {{ request('is_featured') == 'yes' ? 'selected' : '' }}>Öne Çıkan</option>
                <option value="no" {{ request('is_featured') == 'no' ? 'selected' : '' }}>Normal</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">Filtrele</button>
        </form>
    </div>

    <!-- Bulk Actions -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4" x-data="{ selected: [] }">
        <div class="flex justify-between mb-4">
            <span class="text-sm text-gray-600"><span x-text="selected.length"></span> seçildi</span>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('admin.community-posts.bulk-feature') }}" class="inline" @submit.prevent="if(selected.length > 0) { $el.querySelector('input[name=ids]').value = JSON.stringify(selected); $el.submit(); }">
                    @csrf
                    <input type="hidden" name="ids">
                    <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">Toplu Öne Çıkar</button>
                </form>
                <form method="POST" action="{{ route('admin.community-posts.bulk-delete') }}" class="inline" @submit.prevent="if(selected.length > 0 && confirm('Silinecek?')) { $el.querySelector('input[name=ids]').value = JSON.stringify(selected); $el.submit(); }">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ids">
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Toplu Sil</button>
                </form>
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left"><input type="checkbox" @change="selected = $event.target.checked ? [...document.querySelectorAll('.post-checkbox')].map(cb => parseInt(cb.value)) : []" class="rounded"></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kullanıcı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İçerik</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tip</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Beğeni</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($posts as $post)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <input type="checkbox" value="{{ $post->id }}" class="post-checkbox rounded" @change="$event.target.checked ? selected.push({{ $post->id }}) : selected = selected.filter(id => id !== {{ $post->id }})">
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            @if($post->is_featured)<span class="text-yellow-500">⭐</span>@endif
                            <div>
                                <div class="text-sm font-medium">{{ $post->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $post->user->profile->nickname ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm max-w-md">{{ Str::limit($post->content, 80) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $post->type === 'intro' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $post->type === 'intro' ? 'Tanışma' : 'Genel' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $post->likes_count ?? 0 }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $post->created_at->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 text-sm space-x-2">
                        <a href="{{ route('admin.community-posts.show', $post->id) }}" class="text-blue-600 hover:text-blue-800">Detay</a>
                        <form action="{{ route('admin.community-posts.toggle-featured', $post->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-yellow-600 hover:text-yellow-800">{{ $post->is_featured ? '★' : '☆' }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">Gönderi bulunamadı</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($posts->hasPages())
    <div class="bg-white rounded-lg shadow-sm px-6 py-4">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
