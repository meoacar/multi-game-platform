import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // PUBG Temalı Renkler
            colors: {
                'pubg': {
                    'orange': '#F77F00',
                    'yellow': '#FCBF49',
                    'dark': '#003049',
                    'blue': '#0077B6',
                    'light-blue': '#00B4D8',
                    'cyan': '#90E0EF',
                },
                'game': {
                    'primary': '#0077B6',
                    'secondary': '#F77F00',
                    'accent': '#FCBF49',
                    'dark': '#003049',
                    'success': '#06D6A0',
                    'danger': '#EF476F',
                    'warning': '#FFD166',
                },
            },
            animation: {
                'slide-down': 'slideDown 0.5s ease-out',
                'slide-up': 'slideUp 0.5s ease-out',
                'slide-left': 'slideLeft 0.5s ease-out',
                'slide-right': 'slideRight 0.5s ease-out',
                'fade-in': 'fadeIn 0.5s ease-out',
                'fade-out': 'fadeOut 0.5s ease-out',
                'scale-in': 'scaleIn 0.3s ease-out',
                'scale-out': 'scaleOut 0.3s ease-out',
                'bounce-in': 'bounceIn 0.6s ease-out',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'spin-slow': 'spin 3s linear infinite',
            },
            keyframes: {
                slideDown: {
                    '0%': { transform: 'translateY(-100%)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(100%)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideLeft: {
                    '0%': { transform: 'translateX(100%)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
                slideRight: {
                    '0%': { transform: 'translateX(-100%)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeOut: {
                    '0%': { opacity: '1' },
                    '100%': { opacity: '0' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(0.9)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                scaleOut: {
                    '0%': { transform: 'scale(1)', opacity: '1' },
                    '100%': { transform: 'scale(0.9)', opacity: '0' },
                },
                bounceIn: {
                    '0%': { transform: 'scale(0.3)', opacity: '0' },
                    '50%': { transform: 'scale(1.05)' },
                    '70%': { transform: 'scale(0.9)' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
            },
            // Touch-friendly boyutlar
            spacing: {
                'touch': '44px', // Minimum touch target size (Apple HIG)
                'touch-lg': '48px', // Büyük touch target
            },
            // Smooth transitions
            transitionDuration: {
                '400': '400ms',
                '600': '600ms',
            },
            transitionTimingFunction: {
                'smooth': 'cubic-bezier(0.4, 0, 0.2, 1)',
                'bounce-in': 'cubic-bezier(0.68, -0.55, 0.265, 1.55)',
            },
        },
    },
    plugins: [],
    // Safelist - dinamik class'lar için
    safelist: [
        // Alpine.js dinamik class'ları için
        'bg-green-50',
        'bg-red-50',
        'bg-yellow-50',
        'border-green-500',
        'border-red-500',
        'border-yellow-500',
        'text-green-800',
        'text-red-800',
        'text-yellow-800',
        'text-green-500',
        'text-red-500',
        'text-yellow-500',
    ],
};
