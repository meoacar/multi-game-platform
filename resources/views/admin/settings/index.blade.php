@extends('admin.layout')

@section('title', 'Site Ayarları')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ activeTab: 'general', previewMode: false }">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                ⚙️ Site Ayarları
            </h1>
            <p class="text-gray-600 mt-2">Platform ayarlarını buradan yönetebilirsin</p>
        </div>
        <button @click="previewMode = !previewMode" type="button"
            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors flex items-center gap-2">
            <span x-show="!previewMode">👁️ Önizleme</span>
            <span x-show="previewMode">✏️ Düzenleme</span>
        </button>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-lg mb-6">
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex -mb-px min-w-max">
                <button @click="activeTab = 'general'" 
                    :class="activeTab === 'general' ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-6 py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    🌐 Genel
                </button>
                <button @click="activeTab = 'registration'" 
                    :class="activeTab === 'registration' ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-6 py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    👥 Kayıt
                </button>
                <button @click="activeTab = 'security'" 
                    :class="activeTab === 'security' ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-6 py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    🔒 Güvenlik
                </button>
                <button @click="activeTab = 'content'" 
                    :class="activeTab === 'content' ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-6 py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    📝 İçerik
                </button>
                <button @click="activeTab = 'notification'" 
                    :class="activeTab === 'notification' ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-6 py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    🔔 Bildirim
                </button>
                <button @click="activeTab = 'xp'" 
                    :class="activeTab === 'xp' ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-6 py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    ⭐ XP Sistemi
                </button>
                <button @click="activeTab = 'maintenance'" 
                    :class="activeTab === 'maintenance' ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-6 py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    🔧 Bakım
                </button>
                <button @click="activeTab = 'api'" 
                    :class="activeTab === 'api' ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-6 py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    🔌 API
                </button>
            </nav>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="p-8" x-show="!previewMode">
            @csrf
            @method('PUT')

            <!-- Genel Ayarlar -->
            <div x-show="activeTab === 'general'" class="space-y-6" x-cloak>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">🌐 Genel Ayarlar</h2>
                    
                    <!-- Site Adı -->
                    <div class="mb-6">
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Site Adı <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="site_name" id="site_name" 
                            value="{{ old('site_name', isset($settings['site_name']) ? $settings['site_name']->value : 'PUBG Mobile Topluluk') }}" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Sitenin başlığında ve logoda görünecek isim</p>
                    </div>

                    <!-- Site Sloganı -->
                    <div class="mb-6">
                        <label for="site_tagline" class="block text-sm font-medium text-gray-700 mb-2">
                            Site Sloganı
                        </label>
                        <input type="text" name="site_tagline" id="site_tagline" 
                            value="{{ old('site_tagline', isset($settings['site_tagline']) ? $settings['site_tagline']->value : 'Türkiye\'nin En Büyük PUBG Mobile Topluluğu') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Ana sayfada görünecek slogan</p>
                    </div>

                    <!-- Ana Sayfa Duyurusu -->
                    <div class="mb-6">
                        <label for="homepage_announcement" class="block text-sm font-medium text-gray-700 mb-2">
                            Ana Sayfa Duyurusu
                        </label>
                        <textarea name="homepage_announcement" id="homepage_announcement" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('homepage_announcement', isset($settings['homepage_announcement']) ? $settings['homepage_announcement']->value : '') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Ana sayfada gösterilecek önemli duyuru (boş bırakılabilir)</p>
                    </div>

                    <!-- Site Logosu URL -->
                    <div class="mb-6">
                        <label for="site_logo" class="block text-sm font-medium text-gray-700 mb-2">
                            Site Logo URL
                        </label>
                        <input type="url" name="site_logo" id="site_logo" 
                            value="{{ old('site_logo', isset($settings['site_logo']) ? $settings['site_logo']->value : '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Logo resmi URL'si (boş bırakılabilir)</p>
                    </div>

                    <!-- Site Favicon URL -->
                    <div class="mb-6">
                        <label for="site_favicon" class="block text-sm font-medium text-gray-700 mb-2">
                            Site Favicon URL
                        </label>
                        <input type="url" name="site_favicon" id="site_favicon" 
                            value="{{ old('site_favicon', isset($settings['site_favicon']) ? $settings['site_favicon']->value : '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Favicon resmi URL'si (boş bırakılabilir)</p>
                    </div>
                </div>
            </div>

            <!-- Kayıt Ayarları -->
            <div x-show="activeTab === 'registration'" class="space-y-6" x-cloak>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">👥 Kayıt Ayarları</h2>
                    
                    <!-- Kayıt Açık/Kapalı -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="registration_open" class="block text-sm font-medium text-gray-900">
                                    Yeni Kayıtlar
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Yeni kullanıcıların kayıt olmasına izin ver</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="registration_open" id="registration_open" value="1" 
                                    {{ old('registration_open', isset($settings['registration_open']) ? $settings['registration_open']->value : true) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Email Doğrulama Zorunlu -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="require_email_verification" class="block text-sm font-medium text-gray-900">
                                    Email Doğrulama Zorunlu
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Kullanıcılar email adreslerini doğrulamak zorunda</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="require_email_verification" id="require_email_verification" value="1" 
                                    {{ old('require_email_verification', isset($settings['require_email_verification']) ? $settings['require_email_verification']->value : true) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Minimum Yaş -->
                    <div class="mb-6">
                        <label for="minimum_age" class="block text-sm font-medium text-gray-700 mb-2">
                            Minimum Yaş
                        </label>
                        <input type="number" name="minimum_age" id="minimum_age" min="13" max="99"
                            value="{{ old('minimum_age', isset($settings['minimum_age']) ? $settings['minimum_age']->value : 13) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Kayıt olabilmek için minimum yaş sınırı</p>
                    </div>
                </div>
            </div>

            <!-- Güvenlik Ayarları -->
            <div x-show="activeTab === 'security'" class="space-y-6" x-cloak>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">🔒 Güvenlik Ayarları</h2>
                    
                    <!-- reCAPTCHA Aktif -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="recaptcha_enabled" class="block text-sm font-medium text-gray-900">
                                    reCAPTCHA Koruması
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Kayıt ve giriş formlarında reCAPTCHA kullan</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="recaptcha_enabled" id="recaptcha_enabled" value="1" 
                                    {{ old('recaptcha_enabled', isset($settings['recaptcha_enabled']) ? $settings['recaptcha_enabled']->value : false) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Şifre Minimum Uzunluk -->
                    <div class="mb-6">
                        <label for="password_min_length" class="block text-sm font-medium text-gray-700 mb-2">
                            Minimum Şifre Uzunluğu
                        </label>
                        <input type="number" name="password_min_length" id="password_min_length" min="6" max="32"
                            value="{{ old('password_min_length', isset($settings['password_min_length']) ? $settings['password_min_length']->value : 8) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Kullanıcı şifrelerinin minimum karakter sayısı</p>
                    </div>

                    <!-- Şifre Karmaşıklık Gereksinimleri -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Şifre Karmaşıklık Gereksinimleri</label>
                        <div class="space-y-3">
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <input type="checkbox" name="password_require_uppercase" id="password_require_uppercase" value="1"
                                    {{ old('password_require_uppercase', isset($settings['password_require_uppercase']) ? $settings['password_require_uppercase']->value : false) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                <label for="password_require_uppercase" class="ml-3 text-sm text-gray-700">
                                    Büyük harf zorunlu (A-Z)
                                </label>
                            </div>
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <input type="checkbox" name="password_require_lowercase" id="password_require_lowercase" value="1"
                                    {{ old('password_require_lowercase', isset($settings['password_require_lowercase']) ? $settings['password_require_lowercase']->value : false) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                <label for="password_require_lowercase" class="ml-3 text-sm text-gray-700">
                                    Küçük harf zorunlu (a-z)
                                </label>
                            </div>
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <input type="checkbox" name="password_require_numbers" id="password_require_numbers" value="1"
                                    {{ old('password_require_numbers', isset($settings['password_require_numbers']) ? $settings['password_require_numbers']->value : false) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                <label for="password_require_numbers" class="ml-3 text-sm text-gray-700">
                                    Rakam zorunlu (0-9)
                                </label>
                            </div>
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <input type="checkbox" name="password_require_special" id="password_require_special" value="1"
                                    {{ old('password_require_special', isset($settings['password_require_special']) ? $settings['password_require_special']->value : false) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                <label for="password_require_special" class="ml-3 text-sm text-gray-700">
                                    Özel karakter zorunlu (!@#$%^&*)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Maksimum Giriş Denemesi -->
                    <div class="mb-6">
                        <label for="max_login_attempts" class="block text-sm font-medium text-gray-700 mb-2">
                            Maksimum Giriş Denemesi
                        </label>
                        <input type="number" name="max_login_attempts" id="max_login_attempts" min="3" max="10"
                            value="{{ old('max_login_attempts', isset($settings['max_login_attempts']) ? $settings['max_login_attempts']->value : 5) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Başarısız giriş denemesi sonrası hesap geçici olarak kilitlenir</p>
                    </div>

                    <!-- Oturum Süresi (dakika) -->
                    <div class="mb-6">
                        <label for="session_lifetime" class="block text-sm font-medium text-gray-700 mb-2">
                            Oturum Süresi (dakika)
                        </label>
                        <input type="number" name="session_lifetime" id="session_lifetime" min="30" max="10080"
                            value="{{ old('session_lifetime', isset($settings['session_lifetime']) ? $settings['session_lifetime']->value : 120) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Kullanıcı oturumunun ne kadar süre aktif kalacağı</p>
                    </div>

                    <!-- 2FA Zorunlu (Adminler için) -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div>
                                <label for="require_2fa_admin" class="block text-sm font-medium text-gray-900">
                                    Admin 2FA Zorunlu
                                </label>
                                <p class="text-xs text-gray-600 mt-1">Admin kullanıcılar için iki faktörlü doğrulama zorunlu</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="require_2fa_admin" id="require_2fa_admin" value="1" 
                                    {{ old('require_2fa_admin', isset($settings['require_2fa_admin']) ? $settings['require_2fa_admin']->value : false) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-yellow-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- İçerik Ayarları -->
            <div x-show="activeTab === 'content'" class="space-y-6" x-cloak>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">📝 İçerik Ayarları</h2>
                    
                    <!-- İçerik Moderasyonu -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="content_moderation" class="block text-sm font-medium text-gray-900">
                                    İçerik Moderasyonu
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Yeni içerikler yayınlanmadan önce onay beklesin</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="content_moderation" id="content_moderation" value="1" 
                                    {{ old('content_moderation', isset($settings['content_moderation']) ? $settings['content_moderation']->value : false) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Maksimum Dosya Boyutu (MB) -->
                    <div class="mb-6">
                        <label for="max_upload_size" class="block text-sm font-medium text-gray-700 mb-2">
                            Maksimum Dosya Boyutu (MB)
                        </label>
                        <input type="number" name="max_upload_size" id="max_upload_size" min="1" max="50"
                            value="{{ old('max_upload_size', isset($settings['max_upload_size']) ? $settings['max_upload_size']->value : 5) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Kullanıcıların yükleyebileceği maksimum dosya boyutu</p>
                    </div>

                    <!-- Günlük İlan Limiti -->
                    <div class="mb-6">
                        <label for="daily_post_limit" class="block text-sm font-medium text-gray-700 mb-2">
                            Günlük İlan Limiti
                        </label>
                        <input type="number" name="daily_post_limit" id="daily_post_limit" min="1" max="100"
                            value="{{ old('daily_post_limit', isset($settings['daily_post_limit']) ? $settings['daily_post_limit']->value : 10) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Bir kullanıcının günde oluşturabileceği maksimum ilan sayısı</p>
                    </div>
                </div>
            </div>

            <!-- Bildirim Ayarları -->
            <div x-show="activeTab === 'notification'" class="space-y-6" x-cloak>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">🔔 Bildirim Ayarları</h2>
                    
                    <!-- Email Bildirimleri -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="email_notifications_enabled" class="block text-sm font-medium text-gray-900">
                                    Email Bildirimleri
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Kullanıcılara email bildirimleri gönder</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_notifications_enabled" id="email_notifications_enabled" value="1" 
                                    {{ old('email_notifications_enabled', isset($settings['email_notifications_enabled']) ? $settings['email_notifications_enabled']->value : true) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- SMS Bildirimleri -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="sms_notifications_enabled" class="block text-sm font-medium text-gray-900">
                                    SMS Bildirimleri
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Kullanıcılara SMS bildirimleri gönder</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="sms_notifications_enabled" id="sms_notifications_enabled" value="1" 
                                    {{ old('sms_notifications_enabled', isset($settings['sms_notifications_enabled']) ? $settings['sms_notifications_enabled']->value : false) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Push Bildirimleri -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="push_notifications_enabled" class="block text-sm font-medium text-gray-900">
                                    Push Bildirimleri
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Tarayıcı push bildirimleri gönder</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="push_notifications_enabled" id="push_notifications_enabled" value="1" 
                                    {{ old('push_notifications_enabled', isset($settings['push_notifications_enabled']) ? $settings['push_notifications_enabled']->value : false) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- SMTP Ayarları -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">📧 SMTP Ayarları</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="smtp_host" class="block text-sm font-medium text-gray-700 mb-2">
                                    SMTP Host
                                </label>
                                <input type="text" name="smtp_host" id="smtp_host" 
                                    value="{{ old('smtp_host', isset($settings['smtp_host']) ? $settings['smtp_host']->value : '') }}"
                                    placeholder="smtp.gmail.com"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="smtp_port" class="block text-sm font-medium text-gray-700 mb-2">
                                    SMTP Port
                                </label>
                                <input type="number" name="smtp_port" id="smtp_port" 
                                    value="{{ old('smtp_port', isset($settings['smtp_port']) ? $settings['smtp_port']->value : 587) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="smtp_username" class="block text-sm font-medium text-gray-700 mb-2">
                                    SMTP Kullanıcı Adı
                                </label>
                                <input type="text" name="smtp_username" id="smtp_username" 
                                    value="{{ old('smtp_username', isset($settings['smtp_username']) ? $settings['smtp_username']->value : '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="smtp_encryption" class="block text-sm font-medium text-gray-700 mb-2">
                                    SMTP Şifreleme
                                </label>
                                <select name="smtp_encryption" id="smtp_encryption"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="tls" {{ old('smtp_encryption', isset($settings['smtp_encryption']) ? $settings['smtp_encryption']->value : 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ old('smtp_encryption', isset($settings['smtp_encryption']) ? $settings['smtp_encryption']->value : 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="" {{ old('smtp_encryption', isset($settings['smtp_encryption']) ? $settings['smtp_encryption']->value : 'tls') == '' ? 'selected' : '' }}>Yok</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Bildirim Sıklığı -->
                    <div class="mb-6">
                        <label for="notification_frequency" class="block text-sm font-medium text-gray-700 mb-2">
                            Bildirim Sıklığı
                        </label>
                        <select name="notification_frequency" id="notification_frequency"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="instant" {{ old('notification_frequency', isset($settings['notification_frequency']) ? $settings['notification_frequency']->value : 'instant') == 'instant' ? 'selected' : '' }}>Anında</option>
                            <option value="hourly" {{ old('notification_frequency', isset($settings['notification_frequency']) ? $settings['notification_frequency']->value : 'instant') == 'hourly' ? 'selected' : '' }}>Saatlik Özet</option>
                            <option value="daily" {{ old('notification_frequency', isset($settings['notification_frequency']) ? $settings['notification_frequency']->value : 'instant') == 'daily' ? 'selected' : '' }}>Günlük Özet</option>
                            <option value="weekly" {{ old('notification_frequency', isset($settings['notification_frequency']) ? $settings['notification_frequency']->value : 'instant') == 'weekly' ? 'selected' : '' }}>Haftalık Özet</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Kullanıcılara bildirimlerin ne sıklıkla gönderileceği</p>
                    </div>
                </div>
            </div>

            <!-- XP Sistemi Ayarları -->
            <div x-show="activeTab === 'xp'" class="space-y-6" x-cloak>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">⭐ XP Sistemi Ayarları</h2>
                    
                    <!-- XP Sistemi Aktif -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="xp_system_enabled" class="block text-sm font-medium text-gray-900">
                                    XP Sistemi
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Kullanıcılar aktivitelerden XP kazansın</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="xp_system_enabled" id="xp_system_enabled" value="1" 
                                    {{ old('xp_system_enabled', isset($settings['xp_system_enabled']) ? $settings['xp_system_enabled']->value : true) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- XP Kazanma Kuralları -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">🎯 XP Kazanma Kuralları</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="xp_profile_complete" class="block text-sm font-medium text-gray-700 mb-2">
                                    Profil Tamamlama
                                </label>
                                <input type="number" name="xp_profile_complete" id="xp_profile_complete" min="0"
                                    value="{{ old('xp_profile_complete', isset($settings['xp_profile_complete']) ? $settings['xp_profile_complete']->value : 50) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="xp_post_create" class="block text-sm font-medium text-gray-700 mb-2">
                                    İlan Oluşturma
                                </label>
                                <input type="number" name="xp_post_create" id="xp_post_create" min="0"
                                    value="{{ old('xp_post_create', isset($settings['xp_post_create']) ? $settings['xp_post_create']->value : 10) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="xp_comment_create" class="block text-sm font-medium text-gray-700 mb-2">
                                    Yorum Yapma
                                </label>
                                <input type="number" name="xp_comment_create" id="xp_comment_create" min="0"
                                    value="{{ old('xp_comment_create', isset($settings['xp_comment_create']) ? $settings['xp_comment_create']->value : 5) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="xp_guide_create" class="block text-sm font-medium text-gray-700 mb-2">
                                    Rehber Oluşturma
                                </label>
                                <input type="number" name="xp_guide_create" id="xp_guide_create" min="0"
                                    value="{{ old('xp_guide_create', isset($settings['xp_guide_create']) ? $settings['xp_guide_create']->value : 25) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="xp_clan_create" class="block text-sm font-medium text-gray-700 mb-2">
                                    Klan Oluşturma
                                </label>
                                <input type="number" name="xp_clan_create" id="xp_clan_create" min="0"
                                    value="{{ old('xp_clan_create', isset($settings['xp_clan_create']) ? $settings['xp_clan_create']->value : 30) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="xp_daily_login" class="block text-sm font-medium text-gray-700 mb-2">
                                    Günlük Giriş
                                </label>
                                <input type="number" name="xp_daily_login" id="xp_daily_login" min="0"
                                    value="{{ old('xp_daily_login', isset($settings['xp_daily_login']) ? $settings['xp_daily_login']->value : 5) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Level Sistemi -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">📊 Level Sistemi</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="xp_per_level" class="block text-sm font-medium text-gray-700 mb-2">
                                    Level Başına Gereken XP
                                </label>
                                <input type="number" name="xp_per_level" id="xp_per_level" min="100"
                                    value="{{ old('xp_per_level', isset($settings['xp_per_level']) ? $settings['xp_per_level']->value : 100) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="mt-1 text-xs text-gray-500">Her level için gereken temel XP miktarı</p>
                            </div>
                            <div>
                                <label for="xp_level_multiplier" class="block text-sm font-medium text-gray-700 mb-2">
                                    Level Çarpanı
                                </label>
                                <input type="number" name="xp_level_multiplier" id="xp_level_multiplier" min="1" max="3" step="0.1"
                                    value="{{ old('xp_level_multiplier', isset($settings['xp_level_multiplier']) ? $settings['xp_level_multiplier']->value : 1.5) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="mt-1 text-xs text-gray-500">Her level için XP gereksinimi çarpanı</p>
                            </div>
                        </div>
                    </div>

                    <!-- Liderlik Tablosu -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="leaderboard_enabled" class="block text-sm font-medium text-gray-900">
                                    Liderlik Tablosu
                                </label>
                                <p class="text-xs text-gray-500 mt-1">XP liderlik tablosunu göster</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="leaderboard_enabled" id="leaderboard_enabled" value="1" 
                                    {{ old('leaderboard_enabled', isset($settings['leaderboard_enabled']) ? $settings['leaderboard_enabled']->value : true) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bakım Modu -->
            <div x-show="activeTab === 'maintenance'" class="space-y-6" x-cloak>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">🔧 Bakım Modu</h2>
                    
                    <!-- Bakım Modu Aktif -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div>
                                <label for="maintenance_mode" class="block text-sm font-medium text-gray-900">
                                    ⚠️ Bakım Modu
                                </label>
                                <p class="text-xs text-gray-600 mt-1">Aktif olduğunda sadece adminler siteye erişebilir</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1" 
                                    {{ old('maintenance_mode', isset($settings['maintenance_mode']) ? $settings['maintenance_mode']->value : false) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-yellow-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Bakım Mesajı -->
                    <div class="mb-6">
                        <label for="maintenance_message" class="block text-sm font-medium text-gray-700 mb-2">
                            Bakım Mesajı
                        </label>
                        <textarea name="maintenance_message" id="maintenance_message" rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('maintenance_message', isset($settings['maintenance_message']) ? $settings['maintenance_message']->value : 'Sitemiz şu anda bakımdadır. Lütfen daha sonra tekrar deneyin.') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Bakım modunda kullanıcılara gösterilecek mesaj</p>
                    </div>

                    <!-- Tahmini Süre -->
                    <div class="mb-6">
                        <label for="maintenance_eta" class="block text-sm font-medium text-gray-700 mb-2">
                            Tahmini Süre
                        </label>
                        <input type="text" name="maintenance_eta" id="maintenance_eta" 
                            value="{{ old('maintenance_eta', isset($settings['maintenance_eta']) ? $settings['maintenance_eta']->value : '') }}"
                            placeholder="Örn: 2 saat, 30 dakika"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Bakımın ne kadar süreceği (boş bırakılabilir)</p>
                    </div>
                </div>
            </div>

            <!-- API Ayarları -->
            <div x-show="activeTab === 'api'" class="space-y-6" x-cloak>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">🔌 API Ayarları</h2>
                    
                    <!-- API Key Yönetimi Linki -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-blue-900">🔑 API Key Yönetimi</h3>
                                <p class="text-xs text-blue-700 mt-1">API key'lerini oluştur, düzenle ve yönet</p>
                            </div>
                            <a href="{{ route('admin.api.keys.index') }}" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium">
                                Yönet →
                            </a>
                        </div>
                    </div>

                    <!-- Webhook Yönetimi Linki -->
                    <div class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-purple-900">🔗 Webhook Yönetimi</h3>
                                <p class="text-xs text-purple-700 mt-1">Webhook'ları oluştur ve yönet</p>
                            </div>
                            <a href="{{ route('admin.api.webhooks.index') }}" 
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors text-sm font-medium">
                                Yönet →
                            </a>
                        </div>
                    </div>
                    
                    <!-- API Aktif -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="api_enabled" class="block text-sm font-medium text-gray-900">
                                    API Erişimi
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Harici uygulamaların API'ye erişmesine izin ver</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="api_enabled" id="api_enabled" value="1" 
                                    {{ old('api_enabled', isset($settings['api_enabled']) ? $settings['api_enabled']->value : true) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Rate Limiting -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">⏱️ Rate Limiting</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="api_rate_limit" class="block text-sm font-medium text-gray-700 mb-2">
                                    Dakika Başına İstek Limiti
                                </label>
                                <input type="number" name="api_rate_limit" id="api_rate_limit" min="10" max="1000"
                                    value="{{ old('api_rate_limit', isset($settings['api_rate_limit']) ? $settings['api_rate_limit']->value : 60) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="mt-1 text-xs text-gray-500">Bir API key'in dakikada yapabileceği istek sayısı</p>
                            </div>
                            <div>
                                <label for="api_rate_limit_guest" class="block text-sm font-medium text-gray-700 mb-2">
                                    Misafir İstek Limiti
                                </label>
                                <input type="number" name="api_rate_limit_guest" id="api_rate_limit_guest" min="5" max="100"
                                    value="{{ old('api_rate_limit_guest', isset($settings['api_rate_limit_guest']) ? $settings['api_rate_limit_guest']->value : 20) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="mt-1 text-xs text-gray-500">API key olmadan yapılabilecek istek sayısı</p>
                            </div>
                        </div>
                    </div>

                    <!-- CORS Ayarları -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">🌐 CORS Ayarları</h3>
                        
                        <div class="mb-4">
                            <label for="api_cors_enabled" class="flex items-center cursor-pointer">
                                <input type="checkbox" name="api_cors_enabled" id="api_cors_enabled" value="1"
                                    {{ old('api_cors_enabled', isset($settings['api_cors_enabled']) ? $settings['api_cors_enabled']->value : true) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-3 text-sm text-gray-700">CORS'u Etkinleştir</span>
                            </label>
                        </div>
                        
                        <div>
                            <label for="api_cors_origins" class="block text-sm font-medium text-gray-700 mb-2">
                                İzin Verilen Origin'ler
                            </label>
                            <textarea name="api_cors_origins" id="api_cors_origins" rows="3"
                                placeholder="https://example.com&#10;https://app.example.com&#10;* (tümü için)"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('api_cors_origins', isset($settings['api_cors_origins']) ? $settings['api_cors_origins']->value : '*') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Her satıra bir origin yazın. * tüm origin'lere izin verir.</p>
                        </div>
                    </div>

                    <!-- Webhook Ayarları -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">🔗 Webhook Ayarları</h3>
                        
                        <div class="mb-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <label for="webhooks_enabled" class="block text-sm font-medium text-gray-900">
                                        Webhook'lar
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">Olaylar için webhook bildirimleri gönder</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="webhooks_enabled" id="webhooks_enabled" value="1" 
                                        {{ old('webhooks_enabled', isset($settings['webhooks_enabled']) ? $settings['webhooks_enabled']->value : false) ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                        
                        <div>
                            <label for="webhook_secret" class="block text-sm font-medium text-gray-700 mb-2">
                                Webhook Secret Key
                            </label>
                            <input type="text" name="webhook_secret" id="webhook_secret" 
                                value="{{ old('webhook_secret', isset($settings['webhook_secret']) ? $settings['webhook_secret']->value : '') }}"
                                placeholder="Webhook imzalama için gizli anahtar"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="mt-1 text-xs text-gray-500">Webhook isteklerini doğrulamak için kullanılır</p>
                        </div>
                    </div>

                    <!-- API Dokümantasyonu -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div>
                                <label for="api_docs_enabled" class="block text-sm font-medium text-gray-900">
                                    API Dokümantasyonu
                                </label>
                                <p class="text-xs text-gray-600 mt-1">Swagger/OpenAPI dokümantasyonunu göster</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="api_docs_enabled" id="api_docs_enabled" value="1" 
                                    {{ old('api_docs_enabled', isset($settings['api_docs_enabled']) ? $settings['api_docs_enabled']->value : true) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kaydet Butonu -->
            <div class="flex items-center justify-end pt-6 border-t">
                <button type="submit" 
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105 font-semibold shadow-lg">
                    💾 Ayarları Kaydet
                </button>
            </div>
        </form>

        <!-- Önizleme Modu -->
        <div x-show="previewMode" class="p-8" x-cloak>
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl p-8 border-2 border-blue-200">
                <div class="text-center">
                    <div class="text-6xl mb-4">👁️</div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Önizleme Modu</h3>
                    <p class="text-gray-600 mb-6">Ayarlarınızın nasıl görüneceğini burada görebilirsiniz</p>
                    
                    <!-- Önizleme İçeriği -->
                    <div class="bg-white rounded-lg p-6 shadow-md text-left max-w-2xl mx-auto">
                        <div x-show="activeTab === 'general'">
                            <h4 class="font-bold text-lg mb-3">🌐 Genel Ayarlar Önizlemesi</h4>
                            <div class="space-y-2 text-sm">
                                <p><strong>Site Adı:</strong> <span class="text-blue-600">{{ isset($settings['site_name']) ? $settings['site_name']->value : 'PUBG Mobile Topluluk' }}</span></p>
                                <p><strong>Slogan:</strong> {{ isset($settings['site_tagline']) ? $settings['site_tagline']->value : 'Türkiye\'nin En Büyük PUBG Mobile Topluluğu' }}</p>
                            </div>
                        </div>
                        
                        <div x-show="activeTab === 'registration'">
                            <h4 class="font-bold text-lg mb-3">👥 Kayıt Ayarları Önizlemesi</h4>
                            <div class="space-y-2 text-sm">
                                <p><strong>Yeni Kayıtlar:</strong> 
                                    <span class="px-2 py-1 rounded {{ (isset($settings['registration_open']) && $settings['registration_open']->value) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ (isset($settings['registration_open']) && $settings['registration_open']->value) ? 'Açık' : 'Kapalı' }}
                                    </span>
                                </p>
                                <p><strong>Email Doğrulama:</strong> 
                                    <span class="px-2 py-1 rounded {{ (isset($settings['require_email_verification']) && $settings['require_email_verification']->value) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ (isset($settings['require_email_verification']) && $settings['require_email_verification']->value) ? 'Zorunlu' : 'İsteğe Bağlı' }}
                                    </span>
                                </p>
                                <p><strong>Minimum Yaş:</strong> {{ isset($settings['minimum_age']) ? $settings['minimum_age']->value : 13 }} yaş</p>
                            </div>
                        </div>
                        
                        <div x-show="activeTab === 'security'">
                            <h4 class="font-bold text-lg mb-3">🔒 Güvenlik Ayarları Önizlemesi</h4>
                            <div class="space-y-2 text-sm">
                                <p><strong>Minimum Şifre Uzunluğu:</strong> {{ isset($settings['password_min_length']) ? $settings['password_min_length']->value : 8 }} karakter</p>
                                <p><strong>Maksimum Giriş Denemesi:</strong> {{ isset($settings['max_login_attempts']) ? $settings['max_login_attempts']->value : 5 }} deneme</p>
                                <p><strong>Oturum Süresi:</strong> {{ isset($settings['session_lifetime']) ? $settings['session_lifetime']->value : 120 }} dakika</p>
                            </div>
                        </div>
                        
                        <div x-show="activeTab === 'content'">
                            <h4 class="font-bold text-lg mb-3">📝 İçerik Ayarları Önizlemesi</h4>
                            <div class="space-y-2 text-sm">
                                <p><strong>İçerik Moderasyonu:</strong> 
                                    <span class="px-2 py-1 rounded {{ (isset($settings['content_moderation']) && $settings['content_moderation']->value) ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ (isset($settings['content_moderation']) && $settings['content_moderation']->value) ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </p>
                                <p><strong>Maksimum Dosya Boyutu:</strong> {{ isset($settings['max_upload_size']) ? $settings['max_upload_size']->value : 5 }} MB</p>
                                <p><strong>Günlük İlan Limiti:</strong> {{ isset($settings['daily_post_limit']) ? $settings['daily_post_limit']->value : 10 }} ilan</p>
                            </div>
                        </div>
                        
                        <div x-show="activeTab === 'notification'">
                            <h4 class="font-bold text-lg mb-3">🔔 Bildirim Ayarları Önizlemesi</h4>
                            <div class="space-y-2 text-sm">
                                <p><strong>Email Bildirimleri:</strong> {{ (isset($settings['email_notifications_enabled']) && $settings['email_notifications_enabled']->value) ? '✅ Aktif' : '❌ Pasif' }}</p>
                                <p><strong>SMS Bildirimleri:</strong> {{ (isset($settings['sms_notifications_enabled']) && $settings['sms_notifications_enabled']->value) ? '✅ Aktif' : '❌ Pasif' }}</p>
                                <p><strong>Push Bildirimleri:</strong> {{ (isset($settings['push_notifications_enabled']) && $settings['push_notifications_enabled']->value) ? '✅ Aktif' : '❌ Pasif' }}</p>
                            </div>
                        </div>
                        
                        <div x-show="activeTab === 'xp'">
                            <h4 class="font-bold text-lg mb-3">⭐ XP Sistemi Önizlemesi</h4>
                            <div class="space-y-2 text-sm">
                                <p><strong>XP Sistemi:</strong> {{ (isset($settings['xp_system_enabled']) && $settings['xp_system_enabled']->value) ? '✅ Aktif' : '❌ Pasif' }}</p>
                                <p><strong>İlan Oluşturma:</strong> +{{ isset($settings['xp_post_create']) ? $settings['xp_post_create']->value : 10 }} XP</p>
                                <p><strong>Rehber Oluşturma:</strong> +{{ isset($settings['xp_guide_create']) ? $settings['xp_guide_create']->value : 25 }} XP</p>
                                <p><strong>Level Başına XP:</strong> {{ isset($settings['xp_per_level']) ? $settings['xp_per_level']->value : 100 }} XP</p>
                            </div>
                        </div>
                        
                        <div x-show="activeTab === 'maintenance'">
                            <h4 class="font-bold text-lg mb-3">🔧 Bakım Modu Önizlemesi</h4>
                            <div class="space-y-2 text-sm">
                                <p><strong>Bakım Modu:</strong> 
                                    <span class="px-2 py-1 rounded {{ (isset($settings['maintenance_mode']) && $settings['maintenance_mode']->value) ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ (isset($settings['maintenance_mode']) && $settings['maintenance_mode']->value) ? '⚠️ Aktif' : '✅ Pasif' }}
                                    </span>
                                </p>
                                @if(isset($settings['maintenance_mode']) && $settings['maintenance_mode']->value)
                                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded">
                                    <p class="text-yellow-800">{{ isset($settings['maintenance_message']) ? $settings['maintenance_message']->value : 'Sitemiz şu anda bakımdadır.' }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div x-show="activeTab === 'api'">
                            <h4 class="font-bold text-lg mb-3">🔌 API Ayarları Önizlemesi</h4>
                            <div class="space-y-2 text-sm">
                                <p><strong>API Erişimi:</strong> {{ (isset($settings['api_enabled']) && $settings['api_enabled']->value) ? '✅ Aktif' : '❌ Pasif' }}</p>
                                <p><strong>Rate Limit:</strong> {{ isset($settings['api_rate_limit']) ? $settings['api_rate_limit']->value : 60 }} istek/dakika</p>
                                <p><strong>CORS:</strong> {{ (isset($settings['api_cors_enabled']) && $settings['api_cors_enabled']->value) ? '✅ Etkin' : '❌ Devre Dışı' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <button @click="previewMode = false" type="button"
                        class="mt-6 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        ✏️ Düzenlemeye Dön
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
