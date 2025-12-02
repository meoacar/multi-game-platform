@extends('admin.layout')

@section('title', 'Rapor Detayı #' . $report->id)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-sm text-gray-600">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Admin</a>
            <span>/</span>
            <a href="{{ route('admin.reports.index') }}" class="hover:text-gray-900">Raporlar</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold">Rapor #{{ $report->id }}</span>
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ana İçerik -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Rapor Başlığı ve Durum -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $report->reason }}</h1>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                @if($report->priority === 'high') bg-red-100 text-red-800
                                @elseif($report->priority === 'medium') bg-orange-100 text-orange-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst($report->priority ?? 'medium') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">Rapor #{{ $report->id }}</p>
                    </div>
                    <span class="px-4 py-2 rounded-full text-sm font-semibold
                        @if($report->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($report->status === 'resolved') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        @if($report->status === 'pending') Bekliyor
                        @elseif($report->status === 'resolved') Çözüldü
                        @else Reddedildi
                        @endif
                    </span>
                </div>

                <!-- Öncelik Güncelleme -->
                @if($report->status === 'pending')
                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <form action="{{ route('admin.reports.update-priority', $report->id) }}" method="POST" class="flex items-center space-x-4">
                        @csrf
                        <label class="text-sm font-medium text-gray-700">Öncelik:</label>
                        <select name="priority" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="low" {{ $report->priority === 'low' ? 'selected' : '' }}>Düşük</option>
                            <option value="medium" {{ ($report->priority === 'medium' || !$report->priority) ? 'selected' : '' }}>Orta</option>
                            <option value="high" {{ $report->priority === 'high' ? 'selected' : '' }}>Yüksek</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                            Güncelle
                        </button>
                    </form>
                </div>
                @endif

                <!-- Açıklama -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Rapor Açıklaması</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $report->description }}</p>
                    </div>
                </div>

                <!-- Rapor Bilgileri -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Raporlayan</h4>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.users.show', $report->reporter->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                {{ $report->reporter->name }}
                            </a>
                        </div>
                        <p class="text-sm text-gray-600">{{ $report->reporter->email }}</p>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Rapor Tarihi</h4>
                        <p class="text-gray-900">{{ $report->created_at->format('d.m.Y H:i') }}</p>
                        <p class="text-sm text-gray-600">{{ $report->created_at->diffForHumans() }}</p>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">İçerik Türü</h4>
                        <p class="text-gray-900">{{ class_basename($report->reportable_type) }}</p>
                    </div>

                    @if($report->resolved_at)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Çözüm Tarihi</h4>
                        <p class="text-gray-900">{{ $report->resolved_at->format('d.m.Y H:i') }}</p>
                        @if($report->resolver)
                            <p class="text-sm text-gray-600">{{ $report->resolver->name }}</p>
                        @endif
                    </div>
                    @endif
                </div>

                @if($report->resolution_note)
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Çözüm Notu</h3>
                    <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                        <p class="text-gray-700">{{ $report->resolution_note }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Raporlanan İçerik -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Raporlanan İçerik</h2>
                
                @if($report->reportable)
                    <div class="bg-gray-50 border border-gray-200 p-6 rounded-lg">
                        @if($report->reportable_type === 'App\Models\User')
                            <div class="flex items-start space-x-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $report->reportable->name }}</h3>
                                    <p class="text-sm text-gray-600 mb-2">{{ $report->reportable->email }}</p>
                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                        <span>Kayıt: {{ $report->reportable->created_at->format('d.m.Y') }}</span>
                                        <span>•</span>
                                        <span>Durum: {{ $report->reportable->status }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('admin.users.show', $report->reportable->id) }}" 
                                   class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                                    Kullanıcıyı Görüntüle
                                </a>
                            </div>
                        @elseif($report->reportable_type === 'App\Models\LfgPost')
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $report->reportable->title }}</h3>
                            <p class="text-gray-700 mb-4">{{ Str::limit($report->reportable->description, 300) }}</p>
                            <a href="{{ route('admin.lfg-posts.show', $report->reportable->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                                İlanı Görüntüle
                            </a>
                        @elseif($report->reportable_type === 'App\Models\Clan')
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $report->reportable->name }}</h3>
                            <p class="text-gray-700 mb-4">{{ Str::limit($report->reportable->description, 300) }}</p>
                            <a href="{{ route('admin.clans.show', $report->reportable->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                                Klanı Görüntüle
                            </a>
                        @elseif($report->reportable_type === 'App\Models\GuidePost')
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $report->reportable->title }}</h3>
                            <p class="text-gray-700 mb-4">{{ Str::limit($report->reportable->content, 300) }}</p>
                            <a href="{{ route('admin.guides.show', $report->reportable->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                                Rehberi Görüntüle
                            </a>
                        @elseif($report->reportable_type === 'App\Models\CommunityPost')
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $report->reportable->title }}</h3>
                            <p class="text-gray-700 mb-4">{{ Str::limit($report->reportable->content, 300) }}</p>
                            <a href="{{ route('admin.community-posts.show', $report->reportable->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                                Gönderiyi Görüntüle
                            </a>
                        @endif
                    </div>
                @else
                    <div class="bg-red-50 border border-red-200 p-6 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <p class="text-red-800 font-medium">Raporlanan içerik silinmiş veya bulunamıyor</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- İşlem Butonları -->
            @if($report->status === 'pending')
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">İşlemler</h2>
                
                <form action="{{ route('admin.reports.resolve', $report->id) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">İşlem Seç</label>
                        <select name="action" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seçiniz...</option>
                            <option value="delete_content">İçeriği Sil</option>
                            <option value="ban_user">Kullanıcıyı Banla</option>
                            <option value="warn_user">Kullanıcıyı Uyar</option>
                            <option value="no_action">İşlem Yapma</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Çözüm Notu (Opsiyonel)</label>
                        <textarea name="resolution_note" rows="3" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="İşlem hakkında notlar..."></textarea>
                    </div>

                    <div class="flex space-x-3">
                        <button type="submit" 
                            onclick="return confirm('Bu raporu çözmek istediğinizden emin misiniz?')"
                            class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-semibold">
                            ✅ Raporu Çöz
                        </button>
                    </div>
                </form>

                <div class="mt-4 pt-4 border-t">
                    <form action="{{ route('admin.reports.reject', $report->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Red Nedeni (Opsiyonel)</label>
                            <textarea name="resolution_note" rows="2" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Red nedeni..."></textarea>
                        </div>
                        <button type="submit" 
                            onclick="return confirm('Bu raporu reddetmek istediğinizden emin misiniz?')"
                            class="w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-semibold">
                            ❌ Raporu Reddet
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Yan Panel -->
        <div class="space-y-6">
            <!-- İlgili Raporlar -->
            @if($relatedReports->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aynı İçerik İçin Diğer Raporlar</h3>
                <div class="space-y-3">
                    @foreach($relatedReports as $related)
                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between mb-2">
                                <span class="text-sm font-medium text-gray-900">{{ $related->reason }}</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($related->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($related->status === 'resolved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($related->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 mb-2">{{ Str::limit($related->description, 80) }}</p>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>{{ $related->reporter->name }}</span>
                                <a href="{{ route('admin.reports.show', $related->id) }}" class="text-blue-600 hover:text-blue-800">
                                    Görüntüle →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Raporlayan Kullanıcının Geçmişi -->
            @if($reporterHistory->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Raporlayan Kullanıcının Geçmiş Raporları</h3>
                <div class="space-y-3">
                    @foreach($reporterHistory as $history)
                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between mb-2">
                                <span class="text-sm font-medium text-gray-900">{{ $history->reason }}</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($history->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($history->status === 'resolved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($history->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 mb-2">{{ class_basename($history->reportable_type) }}</p>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>{{ $history->created_at->format('d.m.Y') }}</span>
                                <a href="{{ route('admin.reports.show', $history->id) }}" class="text-blue-600 hover:text-blue-800">
                                    Görüntüle →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Hızlı İşlemler -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hızlı İşlemler</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.users.show', $report->reporter->id) }}" 
                       class="block w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 text-center">
                        Raporlayan Kullanıcıyı Görüntüle
                    </a>
                    @if($report->reportable && $report->reportable_type === 'App\Models\User')
                        <a href="{{ route('admin.users.show', $report->reportable->id) }}" 
                           class="block w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 text-center">
                            Raporlanan Kullanıcıyı Görüntüle
                        </a>
                    @endif
                    <a href="{{ route('admin.reports.index', ['reporter_id' => $report->reporter_id]) }}" 
                       class="block w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 text-center">
                        Bu Kullanıcının Tüm Raporları
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
