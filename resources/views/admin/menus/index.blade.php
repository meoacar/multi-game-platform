@extends('admin.layout')

@section('title', 'Menü Yönetimi')

@section('content')
<div class="container-fluid px-4">
    <!-- Başlık -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Menü Yönetimi</h1>
        <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Yeni Menü Oluştur
        </a>
    </div>

    <!-- Başarı Mesajı -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Menü Listesi -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if($menus->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Menü Adı</th>
                                <th>Slug</th>
                                <th>Konum</th>
                                <th>Öğe Sayısı</th>
                                <th>Durum</th>
                                <th>Oluşturma Tarihi</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menus as $menu)
                                <tr>
                                    <td>
                                        <strong>{{ $menu->name }}</strong>
                                        @if($menu->description)
                                            <br>
                                            <small class="text-muted">{{ Str::limit($menu->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $menu->slug }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $menu->location }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $menu->all_items_count }} öğe</span>
                                    </td>
                                    <td>
                                        @if($menu->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Pasif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $menu->created_at->format('d.m.Y H:i') }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.menus.edit', $menu) }}" 
                                               class="btn btn-outline-primary" 
                                               title="Düzenle">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.menus.destroy', $menu) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Bu menüyü silmek istediğinizden emin misiniz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger" 
                                                        title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $menus->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bars fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Henüz menü oluşturulmamış.</p>
                    <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>İlk Menüyü Oluştur
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
