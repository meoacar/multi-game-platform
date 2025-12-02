@extends('admin.layout')

@section('title', 'Yorum Yönetimi')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 -m-6 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-black bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 bg-clip-text text-transparent mb-2">
                💬 YORUM YÖNETİMİ
            </h1>
            <p class="text-gray-400 text-lg">Tüm yorumları görüntüle ve yönet</p>
        </div>

        <!-- İstatistikler -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-black/40 backdrop-blur-xl rounded-2xl p-6 border border-orange-500/20">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-4xl">💬</div>
                    <div class="text-3xl font-black text-white">{{ number_format($stats['total']) }}</div>
                </div>
                <p class="text-orange-400 font-bold text-sm uppercase tracking-wider">Toplam Yorum</p>
            </div>

            <div class="bg-black/40 backdrop-blur-xl rounded-2xl p-6 border border-green-500/20">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-4xl">📅</div>
                    <div class="text-3xl font-black text-green-400">{{ number_format($stats['today']) }}</div>
                </div>
                <p class="text-green-400 font-bold text-sm uppercase tracking-wider">Bugün</p>
            </div>

            <div class="bg-black/40 backdrop-blur-xl rounded-2xl p-6 border border-blue-500/20">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-4xl">📊</div>
                    <div class="text-3xl font-black text-blue-400">{{ number_format($stats['this_week']) }}</div>
                </div>
                <p class="text-blue-400 font-bold text-sm uppercase tracking-wider">Bu Hafta</p>
            </div>

            <div class="bg-black/40 backdrop-blur-xl rounded-2xl p-6 border border-purple-500/20">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-4xl">📈</div>
                    <div class="text-3xl font-black text-purple-400">{{ number_format($stats['this_month']) }}</div>
                </div>
                <p class="text-purple-400 font-bold text-sm uppercase tracking-wider">Bu Ay</p>
            </div>
        </div>

        <!-- Filtreler -->
        <div class="bg-black/40 backdrop-blur-xl rounded-2xl p-6 mb-8 border border-orange-500/20">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-bold text-orange-400 mb-2 uppercase tracking-wider">🔍 Ara</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Yorum içeriği..."
                        class="w-full px-4 py-3 bg-gray-900/50 border-2 border-orange-500/30 text-white rounded-xl focus:ring-2 focus:ring-orange-500 placeholder-gray-500">
                </div>

                <div>
                    <label class="block text-sm font-bold text-orange-400 mb-2 uppercase tracking-wider">📝 İçerik Türü</label>
                    <select name="commentable_type" class="w-full px-4 py-3 bg-gray-900/50 border-2 border-orange-500/30 text-white rounded-xl focus:ring-2 focus:ring-orange-500">
                        <option value="">Tümü</option>
                        <option value="GuidePost" {{ request('commentable_type') == 'GuidePost' ? 'selected' : '' }}>Rehberler</option>
                        <option value="CommunityPost" {{ request('commentable_type') == 'CommunityPost' ? 'selected' : '' }}>Topluluk</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-xl font-bold uppercase shadow-lg shadow-orange-500/50 hover:from-orange-700 hover:to-red-700 transition-all">
                        🔍 Filtrele
                    </button>
                </div>

                <div class="flex items-end">
                    <a href="{{ route('admin.comments.index') }}" class="w-full px-6 py-3 bg-gray-800 text-gray-300 rounded-xl font-bold uppercase text-center border-2 border-gray-700 hover:bg-gray-700 transition-all">
                        ✖️ Temizle
                    </a>
                </div>
            </form>
        </div>

        <!-- Yorumlar Listesi -->
        <div class="bg-black/40 backdrop-blur-xl rounded-2xl overflow-hidden border border-orange-500/20">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-orange-600/20 to-red-600/20 border-b-2 border-orange-500/30">
                        <tr>
                            <th class="px-6 py-5 text-left text-xs font-black text-orange-400 uppercase tracking-wider">👤 Kullanıcı</th>
                            <th class="px-6 py-5 text-left text-xs font-black text-orange-400 uppercase tracking-wider">💬 Yorum</th>
                            <th class="px-6 py-5 text-left text-xs font-black text-orange-400 uppercase tracking-wider">📝 İçerik</th>
                            <th class="px-6 py-5 text-left text-xs font-black text-orange-400 uppercase tracking-wider">📅 Tarih</th>
                            <th class="px-6 py-5 text-left text-xs font-black text-orange-400 uppercase tracking-wider">⚙️ İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-orange-500/10">
                        @forelse($comments as $comment)
                        <tr class="hover:bg-orange-500/5 transition-all">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center text-white font-black shadow-lg shadow-orange-500/50">
                                        {{ substr($comment->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-white">{{ $comment->user->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $comment->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <p class="text-sm text-gray-300 line-clamp-2">{{ $comment->content }}</p>
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-sm">
                                    <span class="px-3 py-1 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg text-xs font-bold">
                                        {{ class_basename($comment->commentable_type) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-sm text-gray-400">{{ $comment->created_at->diffForHumans() }}</div>
                                <div class="text-xs text-gray-500">{{ $comment->created_at->format('d.m.Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.comments.show', $comment->id) }}" 
                                       class="px-3 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-lg text-xs font-bold uppercase shadow-lg shadow-blue-500/50 transition-all">
                                        👁️ Görüntüle
                                    </a>
                                    <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yorumu silmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-3 py-2 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white rounded-lg text-xs font-bold uppercase shadow-lg shadow-red-500/50 transition-all">
                                            🗑️ Sil
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="text-gray-500">
                                    <div class="text-8xl mb-6 opacity-50">💬</div>
                                    <p class="text-2xl font-black text-orange-400 mb-2">Yorum Bulunamadı</p>
                                    <p class="text-sm text-gray-400">Henüz hiç yorum yapılmamış</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($comments->hasPages())
            <div class="px-6 py-5 border-t-2 border-orange-500/30 bg-black/60">
                {{ $comments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
