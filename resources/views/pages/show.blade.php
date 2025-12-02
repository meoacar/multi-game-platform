@extends('layouts.app')

@section('title', $page->title)

@section('content')
<!-- PUBG Mobile Arka Plan -->
<div class="fixed inset-0 z-0 overflow-hidden" 
     style="background-image: url('/arkaplan/2.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    
    <!-- Karartma Katmanı -->
    <div class="absolute inset-0 bg-black/92"></div>
    
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-gray-900/50 via-transparent to-gray-900/80"></div>
    
    <!-- Animasyonlu Işık Efektleri -->
    <div class="absolute inset-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500/5 rounded-full blur-3xl animate-float-delayed"></div>
    </div>
</div>

<div class="relative z-10 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="mb-8 animate-fade-in">
            <ol class="flex items-center gap-2 text-sm">
                <li>
                    <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Ana Sayfa
                    </a>
                </li>
                <li class="text-gray-600">/</li>
                <li class="text-white font-medium">{{ $page->title }}</li>
            </ol>
        </nav>

        <!-- Sayfa İçeriği -->
        <article class="bg-gradient-to-br from-gray-900/90 via-gray-800/90 to-gray-900/90 backdrop-blur-xl rounded-3xl border border-gray-700/50 shadow-2xl overflow-hidden animate-scale-in">
            
            <!-- Header -->
            <header class="relative p-8 md:p-12 border-b border-gray-700/50">
                <!-- Glow Effect -->
                <div class="absolute top-0 left-0 w-full h-full pointer-events-none">
                    <div class="absolute top-0 left-1/4 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl"></div>
                </div>
                
                <div class="relative">
                    <h1 class="text-4xl md:text-5xl font-black mb-4">
                        <span class="bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                            {{ $page->title }}
                        </span>
                    </h1>
                    <div class="flex items-center gap-4 text-sm text-gray-400">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <time datetime="{{ $page->updated_at->toIso8601String() }}">
                                Son güncelleme: {{ $page->updated_at->format('d.m.Y') }}
                            </time>
                        </div>
                    </div>
                </div>
            </header>

            <!-- İçerik -->
            <div class="p-8 md:p-12">
                <div class="prose prose-invert prose-lg max-w-none">
                    {!! $page->content !!}
                </div>
            </div>

            <!-- Footer -->
            <footer class="p-8 md:p-12 border-t border-gray-700/50 bg-gray-900/50">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">
                    <div class="flex items-center gap-2 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Oluşturulma: {{ $page->created_at->format('d.m.Y') }}
                    </div>
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold rounded-xl transition-all transform hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Ana Sayfaya Dön
                    </a>
                </div>
            </footer>
        </article>

        <!-- Site Footer -->
        <footer class="mt-12 pb-8">
            <div class="bg-gradient-to-br from-gray-900/80 via-gray-800/80 to-gray-900/80 backdrop-blur-xl rounded-2xl border border-gray-700/50 p-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Logo & Açıklama -->
                    <div class="md:col-span-2">
                        <h3 class="text-2xl font-black mb-3">
                            <span class="bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent">
                                PUBG Mobile Topluluk
                            </span>
                        </h3>
                        <p class="text-gray-400 text-sm leading-relaxed mb-4">
                            Türkiye'nin en büyük PUBG Mobile topluluğu. Takım bul, klan kur, turnuvalara katıl!
                        </p>
                        <div class="flex gap-3">
                            <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-blue-600 rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-blue-400 rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-purple-600 rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295-.002 0-.003 0-.005 0l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.64-.203-.658-.64.135-.954l11.566-4.458c.538-.196 1.006.128.832.941z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-indigo-600 rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Hızlı Linkler -->
                    <div>
                        <h4 class="text-white font-bold mb-4">Hızlı Linkler</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ route('lfg.index') }}" class="text-gray-400 hover:text-white transition-colors">İlanlar</a></li>
                            <li><a href="{{ route('clans.index') }}" class="text-gray-400 hover:text-white transition-colors">Klanlar</a></li>
                            <li><a href="{{ route('guide.index') }}" class="text-gray-400 hover:text-white transition-colors">Rehberler</a></li>
                            <li><a href="{{ route('community.index') }}" class="text-gray-400 hover:text-white transition-colors">Topluluk</a></li>
                        </ul>
                    </div>

                    <!-- Yasal -->
                    <div>
                        <h4 class="text-white font-bold mb-4">Yasal</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ route('pages.show', 'hakkimizda') }}" class="text-gray-400 hover:text-white transition-colors">Hakkımızda</a></li>
                            <li><a href="{{ route('pages.show', 'kullanim-sartlari') }}" class="text-gray-400 hover:text-white transition-colors">Kullanım Şartları</a></li>
                            <li><a href="{{ route('pages.show', 'gizlilik-politikasi') }}" class="text-gray-400 hover:text-white transition-colors">Gizlilik Politikası</a></li>
                            <li><a href="{{ route('pages.show', 'iletisim') }}" class="text-gray-400 hover:text-white transition-colors">İletişim</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="mt-8 pt-6 border-t border-gray-700/50 text-center text-sm text-gray-400">
                    <p>&copy; {{ date('Y') }} PUBG Mobile Topluluk. Tüm hakları saklıdır.</p>
                </div>
            </div>
        </footer>
    </div>
</div>

<style>
/* Animasyonlar */
@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes scale-in {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -30px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
}

@keyframes float-delayed {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(-30px, 30px) scale(1.1); }
    66% { transform: translate(20px, -20px) scale(0.9); }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out forwards;
}

.animate-scale-in {
    animation: scale-in 0.6s ease-out forwards;
}

.animate-float {
    animation: float 8s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 10s ease-in-out infinite;
}

/* Prose (İçerik) Stilleri - Dark Theme */
.prose-invert {
    color: #d1d5db;
    line-height: 1.75;
}

.prose-invert h1 {
    font-size: 2.25em;
    font-weight: 800;
    margin-top: 0;
    margin-bottom: 0.8888889em;
    line-height: 1.1111111;
    color: #f9fafb;
}

.prose-invert h2 {
    font-size: 1.875em;
    font-weight: 700;
    margin-top: 2em;
    margin-bottom: 1em;
    line-height: 1.3333333;
    color: #f9fafb;
    padding-bottom: 0.5em;
    border-bottom: 1px solid rgba(75, 85, 99, 0.3);
}

.prose-invert h3 {
    font-size: 1.5em;
    font-weight: 600;
    margin-top: 1.6em;
    margin-bottom: 0.6em;
    line-height: 1.6;
    color: #f3f4f6;
}

.prose-invert h4 {
    font-size: 1.25em;
    font-weight: 600;
    margin-top: 1.5em;
    margin-bottom: 0.5em;
    line-height: 1.5;
    color: #e5e7eb;
}

.prose-invert p {
    margin-top: 1.25em;
    margin-bottom: 1.25em;
}

.prose-invert a {
    color: #60a5fa;
    text-decoration: underline;
    font-weight: 500;
    transition: color 0.2s;
}

.prose-invert a:hover {
    color: #93c5fd;
}

.prose-invert strong {
    color: #f9fafb;
    font-weight: 600;
}

.prose-invert ul, .prose-invert ol {
    margin-top: 1.25em;
    margin-bottom: 1.25em;
    padding-left: 1.625em;
}

.prose-invert li {
    margin-top: 0.5em;
    margin-bottom: 0.5em;
}

.prose-invert ul > li {
    padding-left: 0.375em;
}

.prose-invert ul > li::marker {
    color: #9ca3af;
}

.prose-invert ol > li::marker {
    color: #9ca3af;
    font-weight: 400;
}

.prose-invert blockquote {
    font-weight: 500;
    font-style: italic;
    color: #e5e7eb;
    border-left-width: 0.25rem;
    border-left-color: #3b82f6;
    quotes: "\201C""\201D""\2018""\2019";
    margin-top: 1.6em;
    margin-bottom: 1.6em;
    padding-left: 1em;
    background: rgba(59, 130, 246, 0.05);
    padding: 1em;
    border-radius: 0.5rem;
}

.prose-invert code {
    color: #fbbf24;
    font-weight: 600;
    font-size: 0.875em;
    background-color: rgba(31, 41, 55, 0.5);
    padding: 0.2em 0.4em;
    border-radius: 0.25rem;
    border: 1px solid rgba(75, 85, 99, 0.3);
}

.prose-invert pre {
    color: #e5e7eb;
    background-color: rgba(17, 24, 39, 0.8);
    overflow-x: auto;
    font-size: 0.875em;
    line-height: 1.7142857;
    margin-top: 1.7142857em;
    margin-bottom: 1.7142857em;
    border-radius: 0.75rem;
    padding: 1em 1.5em;
    border: 1px solid rgba(75, 85, 99, 0.3);
}

.prose-invert pre code {
    background-color: transparent;
    border-width: 0;
    border-radius: 0;
    padding: 0;
    font-weight: 400;
    color: inherit;
    font-size: inherit;
    line-height: inherit;
}

.prose-invert table {
    width: 100%;
    table-layout: auto;
    text-align: left;
    margin-top: 2em;
    margin-bottom: 2em;
    font-size: 0.875em;
    line-height: 1.7142857;
    border-radius: 0.5rem;
    overflow: hidden;
}

.prose-invert thead {
    background-color: rgba(31, 41, 55, 0.5);
    border-bottom-width: 1px;
    border-bottom-color: #4b5563;
}

.prose-invert thead th {
    color: #f9fafb;
    font-weight: 600;
    vertical-align: bottom;
    padding: 0.75em 1em;
}

.prose-invert tbody tr {
    border-bottom-width: 1px;
    border-bottom-color: rgba(75, 85, 99, 0.3);
}

.prose-invert tbody tr:hover {
    background-color: rgba(31, 41, 55, 0.3);
}

.prose-invert tbody td {
    vertical-align: baseline;
    padding: 0.75em 1em;
}

.prose-invert img {
    margin-top: 2em;
    margin-bottom: 2em;
    border-radius: 0.75rem;
    max-width: 100%;
    height: auto;
    border: 1px solid rgba(75, 85, 99, 0.3);
}

.prose-invert hr {
    border-color: rgba(75, 85, 99, 0.3);
    border-top-width: 1px;
    margin-top: 3em;
    margin-bottom: 3em;
}
</style>
@endsection
