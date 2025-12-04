@extends('layouts.app')

@section('title', $page->title . ' - SquadBul')
@section('description', $page->meta_description ?? $page->title)

@section('content')
<div class="relative min-h-screen overflow-hidden bg-black py-16">
    <!-- Background Image -->
    <div class="absolute inset-0">
        @php
            $backgroundImages = [
                'hakkimizda' => 'hakkımızda.jpeg',
                'iletisim' => 'anasayfaarka.jpg', // İletişim için anasayfa arka planı
                'sss' => 'anasayfaarka.jpg', // SSS için anasayfa arka planı
            ];
            $bgImage = $backgroundImages[$page->slug] ?? null;
        @endphp
        
        @if($bgImage && file_exists(public_path('images/analogolar/' . $bgImage)))
            <img src="{{ asset('images/analogolar/' . $bgImage) }}" 
                 alt="Background" 
                 class="w-full h-full object-cover opacity-20">
        @endif
        
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-black/90 via-purple-900/70 to-black/90"></div>
    </div>
    
    <!-- Animated Effects -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-20 right-20 w-96 h-96 bg-purple-600/10 rounded-full filter blur-3xl animate-pulse-slow"></div>
        <div class="absolute bottom-20 left-20 w-96 h-96 bg-pink-600/10 rounded-full filter blur-3xl animate-pulse-slow" style="animation-delay: 1s;"></div>
    </div>

    <div class="relative z-10 container mx-auto px-4">
        <div class="max-w-5xl mx-auto">
            <!-- Breadcrumb -->
            <nav class="mb-8 text-sm animate-fade-in">
                <ol class="flex items-center gap-2 text-gray-400">
                    <li><a href="/" class="hover:text-purple-400 transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                        Ana Sayfa
                    </a></li>
                    <li class="text-gray-600">/</li>
                    <li class="text-white font-semibold">{{ $page->title }}</li>
                </ol>
            </nav>

            <!-- Page Content -->
            <article class="bg-gradient-to-br from-gray-900/95 to-gray-800/95 backdrop-blur-2xl rounded-3xl border border-purple-500/30 shadow-2xl overflow-hidden animate-fade-in" style="animation-delay: 0.1s;">
                <!-- Header -->
                <div class="relative p-8 md:p-12 border-b border-white/10">
                    <!-- Decorative Elements -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-purple-500/10 to-pink-500/10 rounded-full filter blur-3xl"></div>
                    
                    <div class="relative">
                        <!-- Badge -->
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-purple-500/20 border border-purple-500/30 rounded-full mb-6">
                            <span class="w-2 h-2 bg-purple-400 rounded-full animate-pulse"></span>
                            <span class="text-purple-300 text-sm font-bold uppercase tracking-wider">{{ $page->slug }}</span>
                        </div>
                        
                        <!-- Title -->
                        <h1 class="text-5xl md:text-6xl font-black text-white mb-4 leading-tight">
                            <span class="bg-gradient-to-r from-purple-400 via-pink-400 to-blue-400 bg-clip-text text-transparent">
                                {{ $page->title }}
                            </span>
                        </h1>
                        
                        @if($page->meta_description)
                            <p class="text-xl text-gray-400 font-light">
                                {{ $page->meta_description }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8 md:p-12">
                    <div class="prose prose-invert prose-purple prose-lg max-w-none">
                        <div class="text-gray-300 leading-relaxed space-y-6">
                            {!! $page->content !!}
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-8 md:p-12 bg-gradient-to-r from-purple-900/20 to-pink-900/20 border-t border-white/10">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3 text-sm text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Son güncelleme: <strong class="text-white">{{ $page->updated_at->format('d.m.Y') }}</strong></span>
                        </div>
                        <a href="/" class="group flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-xl font-bold transition-all transform hover:scale-105 shadow-lg shadow-purple-500/30">
                            <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Ana Sayfaya Dön
                        </a>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>

<style>
    .prose h1 {
        @apply text-4xl font-black text-white mb-6 mt-10;
        background: linear-gradient(to right, #a855f7, #ec4899, #3b82f6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .prose h2 {
        @apply text-3xl font-bold text-white mb-4 mt-8 relative pl-6;
    }
    .prose h2::before {
        content: '';
        @apply absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-gradient-to-b from-purple-500 to-pink-500 rounded-full;
    }
    .prose h3 {
        @apply text-2xl font-bold text-gray-200 mb-3 mt-6;
    }
    .prose p {
        @apply text-gray-300 mb-5 text-lg leading-relaxed;
    }
    .prose ul, .prose ol {
        @apply text-gray-300 mb-6 ml-8 space-y-3;
    }
    .prose li {
        @apply relative pl-2;
    }
    .prose ul li::before {
        content: '→';
        @apply absolute -left-6 text-purple-400 font-bold;
    }
    .prose a {
        @apply text-purple-400 hover:text-purple-300 transition-colors font-semibold relative;
        text-decoration: none;
    }
    .prose a::after {
        content: '';
        @apply absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-purple-400 to-pink-400 transition-all;
    }
    .prose a:hover::after {
        @apply w-full;
    }
    .prose strong {
        @apply text-white font-bold;
    }
    
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fade-in 0.6s ease-out;
    }
</style>
@endsection
