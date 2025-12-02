@extends('admin.layout')

@section('title', 'Yeni Rol Oluştur')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.roles.index') }}" class="hover:text-blue-600">Rol Yönetimi</a>
            <span>/</span>
            <span>Yeni Rol</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Yeni Rol Oluştur</h1>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sol Kolon - Temel Bilgiler -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Temel Bilgiler</h2>

                    <!-- Rol Adı -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Rol Adı <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                            Slug (Otomatik oluşturulur)
                        </label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Boş bırakılırsa otomatik oluşturulur</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Açıklama -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Açıklama
                        </label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Renk -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-1">
                            Renk
                        </label>
                        <div class="flex gap-2">
                            <input type="color" name="color" id="color" value="{{ old('color', '#3B82F6') }}"
                                class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                            <input type="text" id="color-text" value="{{ old('color', '#3B82F6') }}" readonly
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                        </div>
                        @error('color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- İkon -->
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">
                            İkon
                        </label>
                        <input type="text" name="icon" id="icon" value="{{ old('icon', 'shield') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('icon') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Heroicons icon adı (örn: shield, user, cog)</p>
                        @error('icon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sağ Kolon - Yetkiler -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Yetkiler</h2>
                        <div class="flex gap-2">
                            <button type="button" onclick="selectAllPermissions()" class="text-sm text-blue-600 hover:text-blue-800">
                                Tümünü Seç
                            </button>
                            <span class="text-gray-300">|</span>
                            <button type="button" onclick="deselectAllPermissions()" class="text-sm text-blue-600 hover:text-blue-800">
                                Tümünü Kaldır
                            </button>
                        </div>
                    </div>

                    @if($permissions->isEmpty())
                        <p class="text-gray-500 text-center py-8">Henüz yetki tanımlanmamış</p>
                    @else
                        <div class="space-y-6">
                            @foreach($permissions as $group => $groupPermissions)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="font-semibold text-gray-900 capitalize">{{ $group }}</h3>
                                        <button type="button" onclick="toggleGroup('{{ $group }}')" 
                                            class="text-sm text-blue-600 hover:text-blue-800">
                                            Grup Seç/Kaldır
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($groupPermissions as $permission)
                                            <label class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer group-{{ $group }}">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                                    class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 permission-checkbox group-checkbox-{{ $group }}">
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium text-gray-900">{{ $permission->name }}</div>
                                                    @if($permission->description)
                                                        <div class="text-xs text-gray-500 mt-1">{{ $permission->description }}</div>
                                                    @endif
                                                    <div class="text-xs text-gray-400 mt-1">{{ $permission->slug }}</div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @error('permissions')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Butonlar -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.roles.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                İptal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                Rol Oluştur
            </button>
        </div>
    </form>
</div>

<script>
// Renk seçici senkronizasyonu
document.getElementById('color').addEventListener('input', function(e) {
    document.getElementById('color-text').value = e.target.value;
});

// Tüm yetkileri seç
function selectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

// Tüm yetkileri kaldır
function deselectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Grup seç/kaldır
function toggleGroup(group) {
    const checkboxes = document.querySelectorAll('.group-checkbox-' + group);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
}

// Slug otomatik oluştur
document.getElementById('name').addEventListener('input', function(e) {
    const slug = e.target.value
        .toLowerCase()
        .replace(/ğ/g, 'g')
        .replace(/ü/g, 'u')
        .replace(/ş/g, 's')
        .replace(/ı/g, 'i')
        .replace(/ö/g, 'o')
        .replace(/ç/g, 'c')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    
    document.getElementById('slug').value = slug;
});
</script>
@endsection
