import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import viteCompression from 'vite-plugin-compression';
import viteImagemin from 'vite-plugin-imagemin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        // Gzip compression
        viteCompression({
            algorithm: 'gzip',
            ext: '.gz',
            threshold: 10240, // 10kb üzeri dosyalar için
            deleteOriginFile: false,
        }),
        // Brotli compression (daha iyi sıkıştırma)
        viteCompression({
            algorithm: 'brotliCompress',
            ext: '.br',
            threshold: 10240,
            deleteOriginFile: false,
        }),
        // Image optimization
        viteImagemin({
            gifsicle: {
                optimizationLevel: 7,
                interlaced: false,
            },
            optipng: {
                optimizationLevel: 7,
            },
            mozjpeg: {
                quality: 80,
            },
            pngquant: {
                quality: [0.8, 0.9],
                speed: 4,
            },
            svgo: {
                plugins: [
                    {
                        name: 'removeViewBox',
                        active: false,
                    },
                    {
                        name: 'removeEmptyAttrs',
                        active: true,
                    },
                ],
            },
        }),
    ],
    build: {
        // CSS ve JS minification
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true, // Production'da console.log'ları kaldır
                drop_debugger: true,
            },
        },
        // Chunk stratejisi - daha iyi caching için
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['alpinejs'],
                    'utils': ['axios'],
                },
            },
        },
        // CSS code splitting
        cssCodeSplit: true,
        // Source map sadece development'ta
        sourcemap: process.env.NODE_ENV === 'development',
        // Chunk boyutu uyarı limiti
        chunkSizeWarningLimit: 1000,
    },
    // CSS optimizasyonu
    css: {
        devSourcemap: true,
    },
});
