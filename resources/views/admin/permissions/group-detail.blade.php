@extends('admin.layout')

@section('title', 'Grup Detayı: ' . ucfirst($group))

@section('content')
<div class="container-fluid px-4">
    <!-- Başlık -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Yetkiler</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.permissions.groups') }}">Gruplar</a></li>
                    <li class="breadcrumb-item active">{{ ucfirst($group) }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">{{ ucfirst($group) }} Grubu</h1>
        </div>
        <div>
            <a href="{{ route('admin.permissions.groups') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Gruplara Dön
            </a>
        </div>
    </div>

    <!-- İstatistikler -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                                <i class="fas fa-key fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Toplam Yetki</h6>
                            <h3 class="mb-0">{{ $stats['total_permissions'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded p-3">
                                <i class="fas fa-shield-alt fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Kullanan Rol</h6>
                            <h3 class="mb-0">{{ $stats['total_roles'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 text-info rounded p-3">
                                <i class="fas fa-star fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">En Yaygın Rol</h6>
                            @if($stats['most_common_role'])
                                <h5 class="mb-0">{{ $stats['most_common_role']->name }}</h5>
                            @else
                                <h5 class="mb-0 text-muted">-</h5>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Yetki Listesi -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Grup Yetkileri</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Yetki Adı</th>
                            <th>Slug</th>
                            <th>Açıklama</th>
                            <th>Kullanan Roller</th>
                            <th class="text-end">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $permission)
                            <tr>
                                <td>
                                    <strong>{{ $permission->name }}</strong>
                                </td>
                                <td>
                                    <code class="text-muted">{{ $permission->slug }}</code>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $permission->description ?? '-' }}</small>
                                </td>
                                <td>
                                    @if($permission->roles->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($permission->roles as $role)
                                                <span class="badge" style="background-color: {{ $role->color }}">
                                                    <i class="fas fa-{{ $role->icon }} me-1"></i>
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">Hiçbir rol kullanmıyor</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.permissions.show', $permission->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Detay
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
