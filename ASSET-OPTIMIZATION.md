# Asset Optimizasyon Kılavuzu

Bu dokümantasyon, projede uygulanan asset optimizasyon tekniklerini ve kullanımlarını açıklar.

## 📦 Kurulum

### 1. NPM Paketlerini Yükle

```bash
cd pubg-community
npm install
```

Yüklenen optimizasyon paketleri:
- `vite-plugin-compression` - Gzip ve Brotli compression
- `vite-plugin-imagemin` - Resim optimizasyonu
- `vanilla-lazyload` - Lazy loading

### 2. Production Build

```bash
npm run build
```

Bu komut:
- CSS ve JS dosyalarını minify eder
- Gzip ve Brotli compression uygular
- Resimleri optimize eder
- Chunk'lara böler (daha iyi caching)

## 🎨 CSS/JS Minification

### Otomatik Minification

Vite production build sırasında otomatik olarak:
- CSS dosyalarını minify eder (cssnano ile)
- JS dosyalarını minify eder (terser ile)
- Console.log'ları kaldırır
- Dead code elimination yapar
- Tree shaking uygular

### Manuel Kontrol

```bash
# Development build (minification yok)
npm run dev

# Production build (minification var)
npm run build
```

## 🖼️ Image Optimization

### Lazy Loading Kullanımı

#### Blade Component ile:

```blade
<x-lazy-image 
    src="/images/banner.jpg"
    alt="Banner"
    class="w-full h-64"
    :blur="true"
/>
```

#### Manuel HTML ile:

```html
<img 
    class="lazy optimized-img"
    data-src="/images/photo.jpg"
    src="/images/placeholder.jpg"
    alt="Photo"
    loading="lazy"
/>
```

### Responsive Images

```html
<picture>
    <source 
        data-srcset="/images/photo-large.webp" 
        media="(min-width: 1024px)" 
        type="image/webp"
    />
    <source 
        data-srcset="/images/photo-medium.webp" 
        media="(min-width: 768px)" 
        type="image/webp"
    />
    <img 
        class="lazy responsive-img"
        data-src="/images/photo-small.jpg"
        src="/images/placeholder.jpg"
        alt="Photo"
    />
</picture>
```

### Image Helper Kullanımı

```php
use App\Helpers\ImageOptimizer;

// Resmi optimize et ve farklı boyutlarda kaydet
$paths = ImageOptimizer::optimize('uploads/photo.jpg', [
    'thumbnail' => [150, 150],
    'medium' => [600, 600],
    'large' => [1200, 1200],
]);

// WebP formatına dönüştür
$webpPath = ImageOptimizer::convertToWebP('uploads/photo.jpg');

// Resim bilgilerini al
$info = ImageOptimizer::getImageInfo('uploads/photo.jpg');
```

## ⚡ Lazy Loading

### JavaScript API

```javascript
// Lazy load'u manuel güncelle (dinamik içerik eklendiğinde)
window.updateLazyLoad();

// Lazy load instance'ına erişim
window.lazyLoad.update();
```

### Alpine.js ile Kullanım

```html
<div x-data="{ loaded: false }">
    <img 
        class="lazy"
        data-src="/images/photo.jpg"
        @load="loaded = true"
        x-show="loaded"
    />
    <div x-show="!loaded" class="lazy-placeholder"></div>
</div>
```

## 🚀 Performans İpuçları

### 1. Resim Formatları

- **WebP** kullanın (30-50% daha küçük)
- **JPEG** fotoğraflar için
- **PNG** şeffaflık gereken yerler için
- **SVG** ikonlar ve logolar için

### 2. Resim Boyutları

```php
// Controller'da
$sizes = [
    'thumbnail' => [150, 150],   // Liste görünümü
    'small' => [300, 300],       // Kart görünümü
    'medium' => [600, 600],      // Detay sayfası
    'large' => [1200, 1200],     // Full screen
];
```

### 3. Lazy Loading Stratejisi

```javascript
// Threshold ayarı (viewport'a ne kadar yaklaşınca yüklensin)
const lazyLoadInstance = new LazyLoad({
    threshold: 300, // 300px önce yükle
});
```

### 4. CSS Optimizasyonu

```css
/* GPU acceleration */
.animated-element {
    transform: translateZ(0);
    will-change: transform;
}

/* Aspect ratio (layout shift önleme) */
.image-container {
    aspect-ratio: 16 / 9;
}
```

## 📊 Artisan Komutları

### Asset Optimizasyon Komutu

```bash
# Tüm asset'leri optimize et
php artisan assets:optimize --all

# Sadece resimleri optimize et
php artisan assets:optimize --images

# Sadece CSS'i optimize et
php artisan assets:optimize --css

# Sadece JS'i optimize et
php artisan assets:optimize --js
```

## 🔧 Yapılandırma

### Vite Config (vite.config.js)

```javascript
export default defineConfig({
    build: {
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true, // Console.log'ları kaldır
            },
        },
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['alpinejs'],
                    'utils': ['axios'],
                },
            },
        },
    },
});
```

### Tailwind Config (tailwind.config.js)

```javascript
export default {
    // Production'da kullanılmayan CSS'leri temizle
    purge: {
        enabled: process.env.NODE_ENV === 'production',
        content: ['./resources/**/*.blade.php'],
    },
};
```

## 📈 Performans Metrikleri

### Hedef Değerler

- **First Contentful Paint (FCP)**: < 1.8s
- **Largest Contentful Paint (LCP)**: < 2.5s
- **Time to Interactive (TTI)**: < 3.8s
- **Total Blocking Time (TBT)**: < 200ms
- **Cumulative Layout Shift (CLS)**: < 0.1

### Ölçüm Araçları

- Google PageSpeed Insights
- Lighthouse (Chrome DevTools)
- WebPageTest
- GTmetrix

## 🐛 Sorun Giderme

### Lazy Loading Çalışmıyor

```javascript
// Console'da kontrol et
console.log(window.lazyLoad);
console.log(window.updateLazyLoad);

// Manuel güncelle
window.updateLazyLoad();
```

### Resimler Yüklenmiyor

```html
<!-- data-src yerine src kullanıldığından emin ol -->
<img class="lazy" data-src="/images/photo.jpg" src="/images/placeholder.jpg" />
```

### Build Hataları

```bash
# Cache'i temizle
npm cache clean --force

# Node modules'u yeniden yükle
rm -rf node_modules
npm install

# Build'i tekrar dene
npm run build
```

## 📚 Kaynaklar

- [Vite Documentation](https://vitejs.dev/)
- [Lazy Loading Best Practices](https://web.dev/lazy-loading/)
- [Image Optimization Guide](https://web.dev/fast/#optimize-your-images)
- [Web Performance Optimization](https://web.dev/fast/)

## ✅ Checklist

Production'a çıkmadan önce:

- [ ] `npm run build` çalıştırıldı
- [ ] Tüm resimler lazy loading ile yükleniyor
- [ ] WebP formatı kullanılıyor
- [ ] Gzip/Brotli compression aktif
- [ ] Browser caching ayarlandı
- [ ] PageSpeed score > 90
- [ ] Lighthouse audit yapıldı
- [ ] Console.log'lar temizlendi

## 🎯 Sonuç

Bu optimizasyonlar ile:
- **%40-60** daha küçük dosya boyutları
- **%30-50** daha hızlı sayfa yükleme
- **%20-30** daha az bandwidth kullanımı
- Daha iyi SEO skorları
- Daha iyi kullanıcı deneyimi
