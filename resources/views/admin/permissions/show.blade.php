@extends('admin.layout')

@section('title', 'Yetki Detayı: ' . $permission->name)

@section('content')
<div class="container-fluid px-4">
    <!-- Başlık -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Yetkiler</a></li>
                    <li class="breadcrumb-item active">{{ $permission->name }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">{{ $permission->name }}</h1>
            <p class="text-muted mb-0">
                <span class="badge bg-secondary">{{ ucfirst($permission->group) }}</span>
            </p>
        </div>
        <div>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Listeye Dön
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Sol Kolon: Yetki Bilgileri -->
        <div class="col-lg-8">
            <!-- Temel Bilgiler -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Yetki Bilgileri</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width: 200px;">Yetki Adı:</td>
                                <td><strong>{{ $permission->name }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Slug:</td>
                                <td><code>{{ $permission->slug }}</code></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Grup:</td>
                                <td>
                                    <a href="{{ route('admin.permissions.group-detail', urlencode($permission->group)) }}" class="badge bg-secondary text-decoration-none">
                                        {{ ucfirst($permission->group) }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Açıklama:</td>
                                <td>{{ $permission->description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Oluşturulma:</td>
                                <td>{{ $permission->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Son Güncelleme:</td>
                                <td>{{ $permission->updated_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kullanan Roller -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Bu Yetkiye Sahip Roller</h5>
                        <span class="badge bg-primary">{{ $permission->roles_count }} rol</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($permission->roles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Rol</th>
                                        <th>Açıklama</th>
                                        <th>Kullanıcı Sayısı</th>
                                        <th>Durum</th>
                                        <th class="text-end">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permission->roles as $role)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-{{ $role->icon }} me-2" style="color: {{ $role->color }}"></i>
                                                    <strong>{{ $role->name }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ Str::limit($role->description ?? '-', 50) }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $role->users->count() }} kullanıcı</span>
                                            </td>
                                            <td>
                                                @if($role->is_system)
                                                    <span class="badge bg-warning">Sistem Rolü</span>
                                                @else
                                                    <span class="badge bg-success">Özel Rol</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Bu yetkiye sahip rol bulunmuyor</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sağ Kolon: İstatistikler -->
        <div class="col-lg-4">
            <!-- İstatistikler -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">İstatistikler</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Kullanan Rol:</span>
                            <strong class="text-primary">{{ $permission->roles_count }}</strong>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-primary" style="width: {{ min(($permission->roles_count / 10) * 100, 100) }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Etkilenen Kullanıcı:</span>
                            <strong class="text-success">{{ $userCount }}</strong>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-success" style="width: {{ min(($userCount / 100) * 100, 100) }}%"></div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Grup:</span>
                        <span class="badge bg-secondary">{{ ucfirst($permission->group) }}</span>
                    </div>
                </div>
            </div>

            <!-- Hızlı Linkler -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Hızlı Linkler</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.permissions.group-detail', urlencode($permission->group)) }}" class="btn btn-outline-primary">
                            <i class="fas fa-folder me-2"></i>Grup Detayı
                        </a>
                        <a href="{{ route('admin.permissions.groups') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-layer-group me-2"></i>Tüm Gruplar
                        </a>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-shield-alt me-2"></i>Rol Yönetimi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
