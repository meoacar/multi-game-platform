@extends('admin.layout')

@section('title', 'Yetki Yönetimi')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1 fw-bold">
                <i class="fas fa-shield-alt text-primary me-2"></i>Yetki Yönetimi
            </h1>
            <p class="text-muted small mb-0">Toplam {{ $stats['total'] }} yetki, {{ $stats['groups'] }} grup</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.permissions.groups') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-layer-group me-1"></i>Gruplar
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.permissions.index') }}" id="filterForm">
                <div class="row g-2">
                    <div class="col-md-3">
                        <select name="group" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">📁 Tüm Gruplar</option>
                            @foreach($groups as $group)
                                <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>
                                    {{ ucfirst($group) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm" 
                               placeholder="🔍 Ara..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="view" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="list" {{ request('view') != 'grouped' ? 'selected' : '' }}>📋 Liste</option>
                            <option value="grouped" {{ request('view') == 'grouped' ? 'selected' : '' }}>📂 Gruplu</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-1">
                            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-filter"></i> Filtrele
                            </button>
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request('view') === 'grouped' && $permissions)
        <!-- Grouped View -->
        @foreach($permissions as $group => $groupPermissions)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white py-2">
                    <strong><i class="fas fa-folder me-2"></i>{{ ucfirst($group) }}</strong>
                    <span class="badge bg-white text-primary float-end">{{ $groupPermissions->count() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="30%">Yetki</th>
                                <th width="25%">Slug</th>
                                <th width="30%">Açıklama</th>
                                <th width="10%">Roller</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupPermissions as $permission)
                                <tr>
                                    <td><strong class="small">{{ $permission->name }}</strong></td>
                                    <td><code class="small">{{ $permission->slug }}</code></td>
                                    <td><small class="text-muted">{{ Str::limit($permission->description ?? '-', 40) }}</small></td>
                                    <td>
                                        @if(isset($permission->roles) && $permission->roles->count() > 0)
                                            <span class="badge bg-primary small">{{ $permission->roles->count() }}</span>
                                        @else
                                            <span class="text-muted small">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.permissions.show', $permission->id) }}" 
                                           class="btn btn-xs btn-outline-primary" title="Detay">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <!-- List View -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" id="permissionsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="12%">Grup</th>
                            <th width="25%">Yetki Adı</th>
                            <th width="23%">Slug</th>
                            <th width="30%">Açıklama</th>
                            <th width="5%" class="text-center">Roller</th>
                            <th width="5%" class="text-center">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginated as $permission)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary small">{{ ucfirst($permission->group) }}</span>
                                </td>
                                <td>
                                    <strong class="small">{{ $permission->name }}</strong>
                                </td>
                                <td>
                                    <code class="small text-muted">{{ $permission->slug }}</code>
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($permission->description ?? '-', 45) }}</small>
                                </td>
                                <td class="text-center">
                                    @if(isset($permission->roles) && $permission->roles->count() > 0)
                                        <span class="badge bg-primary small">{{ $permission->roles->count() }}</span>
                                    @else
                                        <span class="text-muted small">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.permissions.show', $permission->id) }}" 
                                       class="btn btn-xs btn-outline-primary" title="Detay">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2 d-block"></i>
                                    <span class="text-muted">Yetki bulunamadı</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($paginated && $paginated->hasPages())
                <div class="card-footer bg-light py-2">
                    {{ $paginated->links() }}
                </div>
            @endif
        </div>
    @endif
</div>

<style>
.btn-xs {
    padding: 0.15rem 0.4rem;
    font-size: 0.7rem;
    line-height: 1.2;
}

.table-sm th,
.table-sm td {
    padding: 0.4rem;
    vertical-align: middle;
    font-size: 0.875rem;
}

.table-sm thead th {
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge.small {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

code {
    font-size: 0.8rem;
}

.card {
    transition: box-shadow 0.2s ease;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}
</style>
@endsection
