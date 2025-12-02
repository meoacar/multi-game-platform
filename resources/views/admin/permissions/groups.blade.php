@extends('admin.layout')

@section('title', 'Yetki Grupları')

@section('content')
<div class="container-fluid px-4">
    <!-- Başlık -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Yetki Grupları</h1>
            <p class="text-muted mb-0">Yetkileri gruplara göre görüntüleyin</p>
        </div>
        <div>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>Liste Görünümü
            </a>
        </div>
    </div>

    <!-- Grup Kartları -->
    <div class="row">
        @foreach($permissionsGrouped as $group => $permissions)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-folder me-2"></i>
                                {{ ucfirst($group) }}
                            </h5>
                            <span class="badge bg-white text-primary">{{ $groupStats[$group]['count'] }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Grup İstatistikleri -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Yetki Sayısı:</span>
                                <strong>{{ $groupStats[$group]['count'] }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Kullanan Rol:</span>
                                <strong>{{ $groupStats[$group]['roles_count'] }}</strong>
                            </div>
                        </div>

                        <hr>

                        <!-- Yetki Listesi (İlk 5) -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Yetkiler:</h6>
                            <ul class="list-unstyled mb-0">
                                @foreach($permissions->take(5) as $permission)
                                    <li class="mb-2">
                                        <i class="fas fa-key text-primary me-2"></i>
                                        <small>{{ $permission->name }}</small>
                                        @if($permission->roles->count() > 0)
                                            <span class="badge bg-secondary ms-1">{{ $permission->roles->count() }}</span>
                                        @endif
                                    </li>
                                @endforeach
                                @if($permissions->count() > 5)
                                    <li class="text-muted">
                                        <small>+{{ $permissions->count() - 5 }} yetki daha...</small>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <!-- Detay Butonu -->
                        <a href="{{ route('admin.permissions.group-detail', urlencode($group)) }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-eye me-2"></i>Detayları Gör
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($permissionsGrouped->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Yetki grubu bulunamadı</h5>
            </div>
        </div>
    @endif
</div>
@endsection
