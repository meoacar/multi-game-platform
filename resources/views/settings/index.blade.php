@extends('layouts.app')

@section('title', 'Ayarlar - PUBG Mobile Topluluk')

@section('content')
<!-- Arka Plan -->
<div class="fixed inset-0 -z-10">
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-orange-900/95 via-gray-900/98 to-red-900/95"></div>
    
    <!-- Arka Plan Resmi -->
    <div class="absolute inset-0 opacity-20">
        <img src="{{ asset('arkaplan/' . rand(1, 14) . '.jpg') }}" 
             alt="Background" 
             class="w-full h-full object-cover">
    </div>
    
    <!-- Animated Gradient Circles -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-orange-500/30 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-red-500/30 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-yellow-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative">
    <h1 class="text-4xl font-black bg-gradient-to-r from-orange-500 to-red-600 bg-clip-text text-transparent mb-8">⚙️ Ayarlar</h1>
    
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500/50 text-green-400 px-6 py-4 rounded-xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-500/20 border border-red-500/50 text-red-400 px-6 py-4 rounded-xl mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profil Ayarları -->
    <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-8 mb-6">
        <h2 class="text-2xl font-bold text-white mb-6">👤 Profil Bilgileri</h2>
        <form action="{{ route('settings.update.profile') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">Kullanıcı Adı</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/50 transition-all">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">E-posta</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/50 transition-all">
            </div>
            <button type="submit" class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 transition-all transform hover:scale-105">
                Güncelle
            </button>
        </form>
    </div>

    <!-- Şifre Değiştir -->
    <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-8 mb-6">
        <h2 class="text-2xl font-bold text-white mb-6">🔒 Şifre Değiştir</h2>
        <form action="{{ route('settings.update.password') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">Mevcut Şifre</label>
                <input type="password" name="current_password" required class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/50 transition-all">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">Yeni Şifre</label>
                <input type="password" name="password" required class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/50 transition-all">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">Yeni Şifre Tekrar</label>
                <input type="password" name="password_confirmation" required class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/50 transition-all">
            </div>
            <button type="submit" class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 transition-all transform hover:scale-105">
                Şifreyi Değiştir
            </button>
        </form>
    </div>

    <!-- Bildirim Tercihleri -->
    <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-8 mb-6">
        <h2 class="text-2xl font-bold text-white mb-6">🔔 Bildirim Tercihleri</h2>
        <form action="{{ route('settings.update.notifications') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <label class="flex items-center justify-between cursor-pointer">
                <span class="text-gray-300">E-posta bildirimleri</span>
                <input type="checkbox" name="email_notifications" value="1" {{ ($settings['email_notifications'] ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded border-2 border-white/20 bg-white/10 text-orange-600 focus:ring-orange-500">
            </label>
            <label class="flex items-center justify-between cursor-pointer">
                <span class="text-gray-300">Yeni ilan bildirimleri</span>
                <input type="checkbox" name="lfg_notifications" value="1" {{ ($settings['lfg_notifications'] ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded border-2 border-white/20 bg-white/10 text-orange-600 focus:ring-orange-500">
            </label>
            <label class="flex items-center justify-between cursor-pointer">
                <span class="text-gray-300">Klan davetleri</span>
                <input type="checkbox" name="clan_invites" value="1" {{ ($settings['clan_invites'] ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded border-2 border-white/20 bg-white/10 text-orange-600 focus:ring-orange-500">
            </label>
            <label class="flex items-center justify-between cursor-pointer">
                <span class="text-gray-300">Mesaj bildirimleri</span>
                <input type="checkbox" name="message_notifications" value="1" {{ ($settings['message_notifications'] ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded border-2 border-white/20 bg-white/10 text-orange-600 focus:ring-orange-500">
            </label>
            <button type="submit" class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 transition-all transform hover:scale-105">
                Kaydet
            </button>
        </form>
    </div>

    <!-- Gizlilik -->
    <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-8 mb-6">
        <h2 class="text-2xl font-bold text-white mb-6">🔐 Gizlilik</h2>
        <form action="{{ route('settings.update.privacy') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <label class="flex items-center justify-between cursor-pointer">
                <span class="text-gray-300">Profilimi herkese göster</span>
                <input type="checkbox" name="profile_public" value="1" {{ ($settings['profile_public'] ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded border-2 border-white/20 bg-white/10 text-orange-600 focus:ring-orange-500">
            </label>
            <label class="flex items-center justify-between cursor-pointer">
                <span class="text-gray-300">İstatistiklerimi göster</span>
                <input type="checkbox" name="show_stats" value="1" {{ ($settings['show_stats'] ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded border-2 border-white/20 bg-white/10 text-orange-600 focus:ring-orange-500">
            </label>
            <label class="flex items-center justify-between cursor-pointer">
                <span class="text-gray-300">Çevrimiçi durumumu göster</span>
                <input type="checkbox" name="show_online_status" value="1" {{ ($settings['show_online_status'] ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded border-2 border-white/20 bg-white/10 text-orange-600 focus:ring-orange-500">
            </label>
            <button type="submit" class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 transition-all transform hover:scale-105">
                Kaydet
            </button>
        </form>
    </div>

    <!-- Tehlikeli Bölge -->
    <div class="bg-red-500/10 backdrop-blur-sm rounded-2xl shadow-xl border border-red-500/50 p-8">
        <h2 class="text-2xl font-bold text-red-400 mb-6">⚠️ Tehlikeli Bölge</h2>
        <p class="text-gray-400 mb-4">Bu işlemler geri alınamaz!</p>
        <div class="space-y-3">
            <form action="{{ route('settings.freeze') }}" method="POST" onsubmit="return confirm('Hesabınızı dondurmak istediğinizden emin misiniz?')">
                @csrf
                <button type="submit" class="w-full bg-red-500/20 hover:bg-red-500/30 text-red-400 font-bold py-3 px-6 rounded-xl border border-red-500/50 transition-all">
                    Hesabı Dondur
                </button>
            </form>
            
            <!-- Hesap Silme Modal Trigger -->
            <button onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="w-full bg-red-600/20 hover:bg-red-600/30 text-red-400 font-bold py-3 px-6 rounded-xl border border-red-600/50 transition-all">
                Hesabı Sil
            </button>
        </div>
    </div>

    <!-- Hesap Silme Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-2xl border border-red-500/50 p-8 max-w-md w-full">
            <h3 class="text-2xl font-bold text-red-400 mb-4">⚠️ Hesabı Sil</h3>
            <p class="text-gray-300 mb-6">Bu işlem geri alınamaz! Hesabınızı silmek için şifrenizi girin.</p>
            
            <form action="{{ route('settings.delete') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-300 mb-2">Şifreniz</label>
                    <input type="password" name="password" required class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-red-500 focus:ring-2 focus:ring-red-500/50 transition-all">
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-xl transition-all">
                        İptal
                    </button>
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl transition-all">
                        Hesabı Sil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
