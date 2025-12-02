@extends('admin.layout')

@section('title', 'Yeni Menü Oluştur')

@section('content')
<div class="container-fluid px-4">
    <!-- Başlık -->
    <div class="mb-4">
        <h1 class="h3 mb-2">Yeni Menü Oluştur</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}">Menüler</a></li>
                <li class="breadcrumb-item active">Yeni Menü</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.menus.store') }}" method="POST">
                        @csrf

                        <!-- Menü Adı -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Menü Adı <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Örnek: Ana Menü, Footer Menü</small>
                        </div>

                        <!-- Slug -->
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" 
                                   class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" 
                                   name="slug" 
                                   value="{{ old('slug') }}">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Boş bırakılırsa otomatik oluşturulur</small>
                        </div>

                        <!-- Konum -->
                        <div class="mb-3">
                            <label for="location" class="form-label">Konum <span class="text-danger">*</span></label>
                            <select class="form-select @error('location') is-invalid @enderror" 
                                    id="location" 
                                    name="location" 
                                    required>
                                <option value="">Konum Seçin</option>
                                @foreach($locations as $key => $label)
                                    <option value="{{ $key }}" {{ old('location') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Açıklama -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Durum -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                            </div>
                        </div>

                        <!-- Butonlar -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Kaydet
                            </button>
                            <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>İptal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Yardım Paneli -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-info-circle me-2"></i>Yardım
                </div>
                <div class="card-body">
                    <h6>Menü Konumları</h6>
                    <ul class="small">
                        <li><strong>Header:</strong> Sitenin üst kısmında görünür</li>
                        <li><strong>Footer:</strong> Sitenin alt kısmında görünür</li>
                        <li><strong>Sidebar:</strong> Yan menüde görünür</li>
                        <li><strong>Mobile:</strong> Mobil menüde görünür</li>
                    </ul>

                    <hr>

                    <h6>Sonraki Adım</h6>
                    <p class="small">Menüyü oluşturduktan sonra menü öğelerini ekleyebilirsiniz.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Slug otomatik oluşturma
document.getElementById('name').addEventListener('input', function(e) {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value || slugInput.dataset.auto !== 'false') {
        slugInput.value = e.target.value
            .toLowerCase()
            .replace(/ğ/g, 'g')
            .replace(/ü/g, 'u')
            .replace(/ş/g, 's')
            .replace(/ı/g, 'i')
            .replace(/ö/g, 'o')
            .replace(/ç/g, 'c')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.dataset.auto = 'true';
    }
});

document.getElementById('slug').addEventListener('input', function() {
    this.dataset.auto = 'false';
});
</script>
@endpush
@endsection
