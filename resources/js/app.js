import './bootstrap';

// Alpine.js
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Lazy Loading
import lazyLoadInstance, { updateLazyLoad } from './lazyload';

// Global olarak erişilebilir yap
window.lazyLoad = lazyLoadInstance;
window.updateLazyLoad = updateLazyLoad;

// DOM hazır olduğunda lazy load'u başlat
document.addEventListener('DOMContentLoaded', () => {
    // Lazy load CSS ekle
    const style = document.createElement('style');
    style.textContent = `
        /* Lazy load için temel stiller */
        .lazy {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        .lazy.loaded {
            opacity: 1;
        }
        
        .lazy.error {
            opacity: 0.5;
            background-color: #f3f4f6;
        }
        
        /* Blur-up effect için */
        .lazy-blur {
            filter: blur(10px);
            transition: filter 0.3s ease-in-out;
        }
        
        .lazy-blur.loaded {
            filter: blur(0);
        }
    `;
    document.head.appendChild(style);
});
