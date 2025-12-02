@extends('admin.layout')

@section('title', 'Test Push Notification')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.push-notifications.index') }}" 
           class="text-gray-600 hover:text-gray-900">
            ← Geri
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🧪 Test Push Notification</h1>
            <p class="text-gray-600 mt-1">Tek kullanıcıya test bildirimi gönder</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow">
        <form action="{{ route('admin.push-notifications.send-test') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Kullanıcı Seçimi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kullanıcı Seç <span class="text-red-500">*</span>
                </label>
                <select name="user_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Kullanıcı seçin...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }}) - {{ ucfirst($user->device_type) }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">
                    Sadece FCM token'ı olan kullanıcılar listeleniyor
                </p>
            </div>

            <!-- Başlık -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Başlık <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', '🔔 Test Bildirimi') }}" required maxlength="255"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Bildirim başlığı">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">Maksimum 255 karakter</p>
            </div>

            <!-- Mesaj -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Mesaj <span class="text-red-500">*</span>
                </label>
                <textarea name="body" rows="4" required maxlength="500"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Bildirim mesajı">{{ old('body', 'Bu bir test bildirimidir. Push notification sistemi çalışıyor!') }}</textarea>
                @error('body')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">Maksimum 500 karakter</p>
            </div>

            <!-- Görsel URL (Opsiyonel) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Görsel URL (Opsiyonel)
                </label>
                <input type="url" name="image" value="{{ old('image') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="https://example.com/image.jpg">
                @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">Bildirimde gösterilecek görsel (URL)</p>
            </div>

            <!-- Click Action (Opsiyonel) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Click Action (Opsiyonel)
                </label>
                <input type="text" name="click_action" value="{{ old('click_action') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="app://home">
                @error('click_action')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">Bildirime tıklandığında açılacak sayfa (deep link)</p>
            </div>

            <!-- Butonlar -->
            <div class="flex gap-3 pt-4 border-t border-gray-200">
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    🚀 Test Bildirimi Gönder
                </button>
                <a href="{{ route('admin.push-notifications.index') }}" 
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    İptal
                </a>
            </div>
        </form>
    </div>

    <!-- Bilgi Kutusu -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <span class="text-2xl">⚠️</span>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-yellow-900 mb-2">Önemli Notlar</h3>
                <ul class="text-sm text-yellow-800 space-y-1">
                    <li>• Test bildirimi seçilen kullanıcıya anında gönderilir</li>
                    <li>• Queue worker'ın çalıştığından emin ol: <code class="bg-yellow-100 px-2 py-1 rounded">php artisan queue:work</code></li>
                    <li>• Kullanıcının cihazında bildirim izinlerinin açık olması gerekir</li>
                    <li>• Log dosyasından gönderim durumunu kontrol edebilirsin</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
