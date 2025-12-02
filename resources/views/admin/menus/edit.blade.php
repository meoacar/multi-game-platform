@extends('admin.layout')

@section('title', 'Menü Düzenle: ' . $menu->name)

@section('content')
<div class="container-fluid px-4" x-data="menuEditor()">
    <!-- Başlık -->
    <div class="mb-4">
        <h1 class="h3 mb-2">Menü Düzenle: {{ $menu->name }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}">Menüler</a></li>
                <li class="breadcrumb-item active">{{ $menu->name }}</li>
            </ol>
        </nav>
    </div>

    <!-- Başarı/Hata Mesajları -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Sol Panel: Menü Ayarları -->
        <div class="col-lg-4">
            <!-- Menü Bilgileri -->
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Menü Bilgileri</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.menus.update', $menu) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Menü Adı</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $menu->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" 
                                   class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" 
                                   name="slug" 
                                   value="{{ old('slug', $menu->slug) }}" 
                                   required>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Konum</label>
                            <select class="form-select @error('location') is-invalid @enderror" 
                                    id="location" 
                                    name="location" 
                                    required>
                                @foreach($locations as $key => $label)
                                    <option value="{{ $key }}" {{ old('location', $menu->location) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $menu->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $menu->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Menü Bilgilerini Güncelle
                        </button>
                    </form>
                </div>
            </div>

            <!-- Yeni Öğe Ekle -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Yeni Öğe Ekle</h5>
                </div>
                <div class="card-body">
                    <form @submit.prevent="addItem">
                        <div class="mb-3">
                            <label class="form-label">Başlık</label>
                            <input type="text" class="form-control" x-model="newItem.title" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tür</label>
                            <select class="form-select" x-model="newItem.type">
                                @foreach($itemTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3" x-show="newItem.type === 'url' || newItem.type === 'custom'">
                            <label class="form-label">URL</label>
                            <input type="text" class="form-control" x-model="newItem.url">
                        </div>

                        <div class="mb-3" x-show="newItem.type === 'page'">
                            <label class="form-label">Sayfa</label>
                            <select class="form-select" x-model="newItem.target_id">
                                <option value="">Sayfa Seçin</option>
                                @foreach($pages as $page)
                                    <option value="{{ $page->id }}">{{ $page->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3" x-show="newItem.type === 'route'">
                            <label class="form-label">Route</label>
                            <input type="text" class="form-control" x-model="newItem.route" placeholder="home">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Icon (opsiyonel)</label>
                            <input type="text" class="form-control" x-model="newItem.icon" placeholder="fas fa-home">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hedef</label>
                            <select class="form-select" x-model="newItem.target">
                                <option value="_self">Aynı Pencere</option>
                                <option value="_blank">Yeni Pencere</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" x-model="newItem.is_active">
                                <label class="form-check-label">Aktif</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-plus me-2"></i>Öğe Ekle
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sağ Panel: Menü Yapısı -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Menü Yapısı</h5>
                    <button @click="saveOrder" class="btn btn-sm btn-primary" :disabled="!hasChanges">
                        <i class="fas fa-save me-2"></i>Sıralamayı Kaydet
                    </button>
                </div>
                <div class="card-body">
                    @if($menu->items->count() > 0)
                        <div id="menu-items" class="menu-items-container">
                            @foreach($menu->items as $item)
                                @include('admin.menus.partials.menu-item', ['item' => $item, 'level' => 0])
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-list fa-3x mb-3"></i>
                            <p>Henüz menü öğesi eklenmemiş.</p>
                            <p class="small">Sol panelden yeni öğe ekleyebilirsiniz.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.menu-items-container {
    min-height: 200px;
}

.menu-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    cursor: move;
    transition: all 0.2s;
}

.menu-item:hover {
    background: #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.menu-item.dragging {
    opacity: 0.5;
}

.menu-item-children {
    margin-left: 2rem;
    margin-top: 0.5rem;
}

.menu-item-handle {
    cursor: grab;
    color: #6c757d;
}

.menu-item-handle:active {
    cursor: grabbing;
}

.menu-item-actions {
    display: flex;
    gap: 0.25rem;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
function menuEditor() {
    return {
        hasChanges: false,
        newItem: {
            title: '',
            type: 'custom',
            url: '',
            route: '',
            target_id: null,
            target_type: null,
            icon: '',
            target: '_self',
            is_active: true
        },

        init() {
            this.initSortable();
        },

        initSortable() {
            const container = document.getElementById('menu-items');
            if (!container) return;

            new Sortable(container, {
                animation: 150,
                handle: '.menu-item-handle',
                ghostClass: 'dragging',
                onEnd: () => {
                    this.hasChanges = true;
                }
            });

            // Alt öğeler için de sortable yap
            document.querySelectorAll('.menu-item-children').forEach(el => {
                new Sortable(el, {
                    animation: 150,
                    handle: '.menu-item-handle',
                    ghostClass: 'dragging',
                    onEnd: () => {
                        this.hasChanges = true;
                    }
                });
            });
        },

        async addItem() {
            try {
                const formData = { ...this.newItem };
                
                if (formData.type === 'page') {
                    formData.target_type = 'App\\Models\\Page';
                }

                const response = await fetch('{{ route("admin.menus.items.store", $menu) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Bir hata oluştu');
            }
        },

        async saveOrder() {
            const items = [];
            const container = document.getElementById('menu-items');
            
            // Ana öğeleri topla
            container.querySelectorAll(':scope > .menu-item').forEach((el, index) => {
                items.push({
                    id: parseInt(el.dataset.id),
                    parent_id: null,
                    order: index
                });

                // Alt öğeleri topla
                el.querySelectorAll('.menu-item-children > .menu-item').forEach((child, childIndex) => {
                    items.push({
                        id: parseInt(child.dataset.id),
                        parent_id: parseInt(el.dataset.id),
                        order: childIndex
                    });
                });
            });

            try {
                const response = await fetch('{{ route("admin.menus.items.reorder", $menu) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ items })
                });

                const data = await response.json();

                if (data.success) {
                    this.hasChanges = false;
                    alert('Sıralama başarıyla kaydedildi');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Bir hata oluştu');
            }
        },

        async deleteItem(itemId) {
            if (!confirm('Bu menü öğesini silmek istediğinizden emin misiniz?')) {
                return;
            }

            try {
                const response = await fetch(`/admin/menus/items/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Bir hata oluştu');
            }
        },

        async toggleStatus(itemId) {
            try {
                const response = await fetch(`/admin/menus/items/${itemId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Bir hata oluştu');
            }
        }
    }
}
</script>
@endpush
@endsection
