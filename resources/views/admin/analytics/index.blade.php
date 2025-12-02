@extends('admin.layout')

@section('title', $pageTitle)

@section('content')
<div class="container mx-auto">
    <!-- Başlık -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $pageTitle }}</h1>
        <p class="text-gray-600 mt-1">Kullanıcı, içerik ve platform analitiği raporlarına hızlı erişim</p>
    </div>

    <!-- Analitik Modülleri -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Kullanıcı Analitiği -->
        <a href="{{ route('admin.analytics.users') }}" 
           class="group bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-4xl">
                    👥
                </div>
                <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Kullanıcı Analitiği</h3>
            <p class="text-blue-100 text-sm">
                Kayıt trendi, aktif kullanıcılar, churn rate, retention rate ve kullanıcı segmentasyonu
            </p>
            <div class="mt-4 pt-4 border-t border-blue-400 border-opacity-30">
                <div class="flex items-center text-white text-sm">
                    <span class="font-medium">Detaylı raporu görüntüle</span>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </div>
        </a>

        <!-- İçerik Analitiği -->
        <a href="{{ route('admin.analytics.content') }}" 
           class="group bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-4xl">
                    📝
                </div>
                <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">İçerik Analitiği</h3>
            <p class="text-green-100 text-sm">
                İçerik oluşturma trendi, popüler türler, engagement rate ve içerik kalitesi
            </p>
            <div class="mt-4 pt-4 border-t border-green-400 border-opacity-30">
                <div class="flex items-center text-white text-sm">
                    <span class="font-medium">Detaylı raporu görüntüle</span>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </div>
        </a>

        <!-- Platform Analitiği -->
        <a href="{{ route('admin.analytics.platform') }}" 
           class="group bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-4xl">
                    🌐
                </div>
                <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Platform Analitiği</h3>
            <p class="text-purple-100 text-sm">
                Sayfa görüntülenme, bounce rate, oturum süresi, trafik kaynakları ve cihaz dağılımı
            </p>
            <div class="mt-4 pt-4 border-t border-purple-400 border-opacity-30">
                <div class="flex items-center text-white text-sm">
                    <span class="font-medium">Detaylı raporu görüntüle</span>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Hızlı İstatistikler -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Hızlı Bakış</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-3xl mb-2">📊</div>
                <p class="text-sm text-gray-600 mb-1">Toplam Rapor</p>
                <p class="text-2xl font-bold text-gray-900">3</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-3xl mb-2">📈</div>
                <p class="text-sm text-gray-600 mb-1">Aktif Metrik</p>
                <p class="text-2xl font-bold text-gray-900">15+</p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <div class="text-3xl mb-2">🎯</div>
                <p class="text-sm text-gray-600 mb-1">Veri Kaynağı</p>
                <p class="text-2xl font-bold text-gray-900">Gerçek Zamanlı</p>
            </div>
            <div class="text-center p-4 bg-orange-50 rounded-lg">
                <div class="text-3xl mb-2">📅</div>
                <p class="text-sm text-gray-600 mb-1">Güncelleme</p>
                <p class="text-2xl font-bold text-gray-900">Anlık</p>
            </div>
        </div>
    </div>

    <!-- Özellikler -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Kullanıcı Analitiği Özellikleri -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-2xl">
                    👥
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Kullanıcı Analitiği</h3>
            </div>
            <ul class="space-y-2">
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Kayıt trendi ve büyüme analizi</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Aktif kullanıcı takibi</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Churn ve retention rate hesaplama</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Kullanıcı segmentasyonu (yeni, aktif, pasif, kayıp)</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Demografik analiz (şehir, yaş, cinsiyet)</span>
                </li>
            </ul>
        </div>

        <!-- İçerik Analitiği Özellikleri -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-2xl">
                    📝
                </div>
                <h3 class="text-lg font-semibold text-gray-900">İçerik Analitiği</h3>
            </div>
            <ul class="space-y-2">
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">İçerik oluşturma trendi (LFG, Klan, Rehber, Topluluk)</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Popüler içerik türleri dağılımı</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Engagement rate (etkileşim oranı)</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">İçerik kalitesi skorlaması</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">En aktif içerik üreticileri</span>
                </li>
            </ul>
        </div>

        <!-- Platform Analitiği Özellikleri -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-2xl">
                    🌐
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Platform Analitiği</h3>
            </div>
            <ul class="space-y-2">
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Sayfa görüntülenme istatistikleri</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Bounce rate (hemen çıkma oranı)</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Ortalama oturum süresi</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Trafik kaynakları analizi</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Cihaz dağılımı (mobil, tablet, desktop)</span>
                </li>
            </ul>
        </div>

        <!-- Export ve Raporlama -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center text-2xl">
                    📊
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Export ve Raporlama</h3>
            </div>
            <ul class="space-y-2">
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">PDF export desteği</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Excel export desteği</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Tarih aralığı filtreleme</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Özel tarih seçimi</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-orange-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-gray-700">Zamanlanmış raporlar (yakında)</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Bilgi Notu -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Analitik Sistemi Hakkında</h3>
                <p class="text-blue-800 mb-3">
                    Admin panel analitik sistemi, platformunuzun performansını detaylı bir şekilde izlemenizi sağlar. 
                    Tüm veriler gerçek zamanlı olarak hesaplanır ve grafiklerle görselleştirilir.
                </p>
                <ul class="list-disc list-inside text-blue-800 space-y-1">
                    <li>Kullanıcı davranışlarını anlayın ve büyüme stratejileri geliştirin</li>
                    <li>İçerik performansını ölçün ve optimize edin</li>
                    <li>Platform kullanımını izleyin ve iyileştirmeler yapın</li>
                    <li>Veri odaklı kararlar alın</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
