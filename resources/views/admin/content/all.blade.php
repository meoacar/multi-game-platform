@extends('admin.layout')

@section('title', 'Tüm İçerikler')

@section('content')
<div class="container-fluid px-4">
    <!-- Sayfa Başlığı -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Tüm İçerikler</h1>
            <p class="text-muted mb-0">Tüm içerik türlerini birleşik görüntüleyin</p>
        </div>
        <div>
            <a href="{{ route('admin.content.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Geri Dön
            </a>
        </div>
    </div>

    <!-- Filtreler -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filtreler
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.content.all') }}" class="row g-3">
                <!-- İçerik Türü -->
                <div class="col-md-3">
                    <label class="form-label">İçerik Türü</label>
                    <select name="type" class="form-select">
                        <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>Tümü</option>
                        <option value="lfg" {{ request('type') === 'lfg' ? 'selected' : '' }}>LFG İlanları</option>
                        <option value="clan" {{ request('type') === 'clan' ? 'selected' : '' }}>Klanlar</option>
                        <option value="guide" {{ request('type') === 'guide' ? 'selected' : '' }}>Rehberler</option>
                        <option value="community" {{ request('type') === 'community' ? 'selected' : '' }}>Topluluk Gönderileri</option>
                    </select>
                </div>

                <!-- Arama -->
                <div class="col-md-3">
                    <label class="form-label">Arama</label>
                    <input type="text" name="search" class="form-control" placeholder="Başlık veya içerik ara..." value="{{ request('search') }}">
                </div>

                <!-- Başlangıç Tarihi -->
                <div class="col-md-2">
                    <label class="form-label">Başlangıç</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <!-- Bitiş Tarihi -->
                <div class="col-md-2">
                    <label class="form-label">Bitiş</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <!-- Sıralama -->
                <div class="col-md-2">
                    <label class="form-label">Sıralama</label>
                    <select name="sort_by" class="form-select">
                        <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Tarih</option>
                        <option value="views_count" {{ request('sort_by') === 'views_count' ? 'selected' : '' }}>Görüntülenme</option>
                    </select>
                </div>

                <!-- Butonlar -->
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Filtrele
                    </button>
                    <a href="{{ route('admin.content.all') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>Sıfırla
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- İçerik Listesi -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                İçerik Listesi ({{ number_format($total) }} sonuç)
            </h6>
        </div>
        <div class="card-body">
            @if($contents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tür</th>
                                <th>Başlık</th>
                                <th>Kullanıcı</th>
                                <th>Durum</th>
                                <th>Görüntülenme</th>
                                <th>Tarih</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contents as $content)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $content['type'] === 'lfg' ? 'primary' : ($content['type'] === 'clan' ? 'success' : ($content['type'] === 'guide' ? 'info' : 'warning')) }}">
                                            {{ $content['type_label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($content['is_featured'])
                                                <i class="fas fa-star text-warning me-2" title="Öne Çıkan"></i>
                                            @endif
                                            <span>{{ \Str::limit($content['title'], 50) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($content['user']->profile && $content['user']->profile->avatar_path)
                                                <img src="{{ asset('storage/' . $content['user']->profile->avatar_path) }}" 
                                                     class="rounded-circle me-2" 
                                                     width="30" 
                                                     height="30"
                                                     alt="{{ $content['user']->name }}">
                                            @else
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 30px; height: 30px; font-size: 12px;">
                                                    {{ strtoupper(substr($content['user']->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <span>{{ $content['user']->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($content['type'] === 'lfg')
                                            <span class="badge bg-{{ $content['status'] === 'open' ? 'success' : 'secondary' }}">
                                                {{ $content['status'] === 'open' ? 'Açık' : 'Kapalı' }}
                                            </span>
                                        @elseif($content['type'] === 'clan')
                                            <span class="badge bg-{{ $content['status'] === 'verified' ? 'success' : 'warning' }}">
                                                {{ $content['status'] === 'verified' ? 'Doğrulanmış' : 'Bekliyor' }}
                                            </span>
                                        @elseif($content['type'] === 'guide')
                                            <span class="badge bg-{{ $content['status'] === 'published' ? 'success' : 'secondary' }}">
                                                {{ $content['status'] === 'published' ? 'Yayında' : 'Taslak' }}
                                            </span>
                                        @else
                                            <span class="badge bg-success">Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($content['views_count'] > 0)
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-eye me-1"></i>{{ number_format($content['views_count']) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $content['created_at']->format('d.m.Y H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($content['type'] === 'lfg')
                                            <a href="{{ route('admin.lfg-posts.show', $content['id']) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.lfg-posts.edit', $content['id']) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @elseif($content['type'] === 'clan')
                                            <a href="{{ route('admin.clans.show', $content['id']) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.clans.edit', $content['id']) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @elseif($content['type'] === 'guide')
                                            <a href="{{ route('admin.guides.show', $content['id']) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.guides.edit', $content['id']) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @elseif($content['type'] === 'community')
                                            <a href="{{ route('admin.community-posts.show', $content['id']) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.community-posts.edit', $content['id']) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($total > $perPage)
                    <div class="d-flex justify-content-center mt-4">
                        <nav>
                            <ul class="pagination">
                                @php
                                    $totalPages = ceil($total / $perPage);
                                    $currentPage = request('page', 1);
                                @endphp

                                <!-- Previous -->
                                @if($currentPage > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                <!-- Pages -->
                                @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                                    <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">
                                            {{ $i }}
                                        </a>
                                    </li>
                                @endfor

                                <!-- Next -->
                                @if($currentPage < $totalPages)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Hiçbir içerik bulunamadı</p>
                    <a href="{{ route('admin.content.all') }}" class="btn btn-primary">
                        Filtreleri Sıfırla
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

