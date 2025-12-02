@extends('admin.layout')

@section('title', 'Asset Optimizasyon Test')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">🚀 Asset Optimizasyon Test Sayfası</h1>
        <p class="text-gray-600 mt-2">Bu sayfa asset optimizasyonlarını test etmek için oluşturulmuştur.</p>
    </div>

    <!-- Lazy Loading Test -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">📸 Lazy Loading Test</h2>
        <p class="text-gray-600 mb-6">Aşağıya scroll yapın, resimler viewport'a yaklaştıkça yüklenecek.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @for($i = 1; $i <= 12; $i++)
                <div class="bg-gray-100 rounded-lg overflow-hidden">
                    <x-optimized-image 
                        src="https://picsum.photos/400/300?random={{ $i }}"
                        alt="Test Image {{ $i }}"
                        aspectRatio="4-3"
                        :lazy="true"
                    />
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900">Test Resim {{ $i }}</h3>
                        <p class="text-sm text-gray-600">Lazy loading ile yükleniyor</p>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Aspect Ratio Test -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">📐 Aspect Ratio Test</h2>
        <p class="text-gray-600 mb-6">Farklı aspect ratio'lar ile layout shift önleme.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- 16:9 -->
            <div>
                <h3 class="font-semibold mb-2">16:9 (Video)</h3>
                <x-optimized-image 
                    src="https://picsum.photos/1600/900"
                    alt="16:9 Aspect Ratio"
                    aspectRatio="16-9"
                    :lazy="true"
                />
            </div>
            
            <!-- 4:3 -->
            <div>
                <h3 class="font-semibold mb-2">4:3 (Klasik)</h3>
                <x-optimized-image 
                    src="https://picsum.photos/800/600"
                    alt="4:3 Aspect Ratio"
                    aspectRatio="4-3"
                    :lazy="true"
                />
            </div>
            
            <!-- 1:1 -->
            <div>
                <h3 class="font-semibold mb-2">1:1 (Kare)</h3>
                <x-optimized-image 
                    src="https://picsum.photos/600/600"
                    alt="1:1 Aspect Ratio"
                    aspectRatio="1-1"
                    :lazy="true"
                />
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Performance Metrics</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="text-sm text-blue-600 font-semibold mb-1">Lazy Images</div>
                <div class="text-2xl font-bold text-blue-900" id="lazy-count">0</div>
            </div>
            
            <div class="bg-green-50 rounded-lg p-4">
                <div class="text-sm text-green-600 font-semibold mb-1">Loaded Images</div>
                <div class="text-2xl font-bold text-green-900" id="loaded-count">0</div>
            </div>
            
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="text-sm text-yellow-600 font-semibold mb-1">Page Load Time</div>
                <div class="text-2xl font-bold text-yellow-900" id="load-time">-</div>
            </div>
            
            <div class="bg-purple-50 rounded-lg p-4">
                <div class="text-sm text-purple-600 font-semibold mb-1">DOM Ready</div>
                <div class="text-2xl font-bold text-purple-900" id="dom-ready">-</div>
            </div>
        </div>
    </div>

    <!-- Optimization Tips -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <h2 class="text-2xl font-bold mb-4">💡 Optimizasyon İpuçları</h2>
        <ul class="space-y-2">
            <li class="flex items-start">
                <span class="mr-2">✅</span>
                <span>Lazy loading kullanarak sayfa yükleme süresini %30-50 azaltabilirsiniz</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2">✅</span>
                <span>WebP formatı kullanarak resim boyutlarını %30-40 küçültebilirsiniz</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2">✅</span>
                <span>Aspect ratio kullanarak layout shift'i önleyebilirsiniz</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2">✅</span>
                <span>Gzip/Brotli compression ile bandwidth kullanımını azaltabilirsiniz</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2">✅</span>
                <span>Browser caching ile tekrar ziyaretlerde hızlı yükleme sağlayabilirsiniz</span>
            </li>
        </ul>
    </div>
</div>

@push('scripts')
<script>
    // Performance metrics
    document.addEventListener('DOMContentLoaded', () => {
        // Lazy image sayısı
        const lazyImages = document.querySelectorAll('.lazy');
        document.getElementById('lazy-count').textContent = lazyImages.length;
        
        // Yüklenen resim sayısı
        let loadedCount = 0;
        lazyImages.forEach(img => {
            img.addEventListener('load', () => {
                loadedCount++;
                document.getElementById('loaded-count').textContent = loadedCount;
            });
        });
        
        // Page load time
        window.addEventListener('load', () => {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            document.getElementById('load-time').textContent = (loadTime / 1000).toFixed(2) + 's';
        });
        
        // DOM ready time
        const domReady = performance.timing.domContentLoadedEventEnd - performance.timing.navigationStart;
        document.getElementById('dom-ready').textContent = (domReady / 1000).toFixed(2) + 's';
        
        // Console'da detaylı bilgi
        console.log('🚀 Performance Metrics:');
        console.log('- Lazy Images:', lazyImages.length);
        console.log('- DOM Ready:', (domReady / 1000).toFixed(2) + 's');
    });
</script>
@endpush
@endsection
