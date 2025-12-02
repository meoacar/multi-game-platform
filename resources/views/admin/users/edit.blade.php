@extends('admin.layout')

@section('title', 'Kullanıcı Düzenle')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kullanıcı Düzenle</h1>
                <p class="text-gray-600 mt-1">{{ $user->name }} - {{ $user->email }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                ← Geri Dön
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Temel Bilgiler -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">İsim</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="banned" {{ old('status', $user->status) === 'banned' ? 'selected' : '' }}>Banlı</option>
                        <option value="frozen" {{ old('status', $user->status) === 'frozen' ? 'selected' : '' }}>Dondurulmuş</option>
                    </select>
                    @error('status')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_admin" value="1" 
                               {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Admin Yetkisi</span>
                    </label>
                    @error('is_admin')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Admin Rolleri (sadece admin ise) -->
                @if($user->is_admin)
                <div class="border-t pt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Admin Rolleri</label>
                    <div class="space-y-2">
                        @foreach($roles as $role)
                        <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                   {{ $user->adminRoles->contains($role->id) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div class="ml-3">
                                <span class="font-medium text-gray-900">{{ $role->name }}</span>
                                <p class="text-sm text-gray-500">{{ $role->description }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- XP -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Toplam XP</label>
                    <input type="number" name="xp_total" value="{{ old('xp_total', $user->xp_total) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           min="0">
                    @error('xp_total')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Şifre Değiştir (Opsiyonel) -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Şifre Değiştir (Opsiyonel)</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Yeni Şifre</label>
                            <input type="password" name="password" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Boş bırakılırsa değişmez">
                            @error('password')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Şifre Tekrar</label>
                            <input type="password" name="password_confirmation" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Boş bırakılırsa değişmez">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Butonlar -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    İptal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    💾 Kaydet
                </button>
            </div>
        </form>
    </div>

    <!-- Kullanıcı İstatistikleri -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Kayıt Tarihi</p>
            <p class="text-lg font-bold text-gray-900">{{ $user->created_at->format('d.m.Y') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Son Giriş</p>
            <p class="text-lg font-bold text-gray-900">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Hiç' }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Email Doğrulama</p>
            <p class="text-lg font-bold {{ $user->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                {{ $user->email_verified_at ? '✓ Doğrulandı' : '✗ Doğrulanmadı' }}
            </p>
        </div>
    </div>
</div>
@endsection
