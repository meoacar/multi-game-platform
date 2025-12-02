/**
 * Lazy Loading Modülü
 * Resimlerin ve içeriklerin gecikmeli yüklenmesi için
 */

import LazyLoad from 'vanilla-lazyload';

// Lazy load instance'ı oluştur
const lazyLoadInstance = new LazyLoad({
    // Elementler
    elements_selector: '.lazy',
    
    // Threshold - viewport'a ne kadar yaklaşınca yüklensin (px)
    threshold: 300,
    
    // Callback'ler
    callback_loaded: (el) => {
        // Yüklendikten sonra fade-in animasyonu
        el.classList.add('loaded');
    },
    
    callback_error: (el) => {
        // Hata durumunda placeholder göster
        el.classList.add('error');
        console.error('Lazy load error:', el.dataset.src);
    },
    
    // Responsive images için
    use_native: false,
});

// Dinamik içerik eklendiğinde lazy load'u güncelle
export function updateLazyLoad() {
    if (lazyLoadInstance) {
        lazyLoadInstance.update();
    }
}

// Export
export default lazyLoadInstance;
