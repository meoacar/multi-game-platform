@extends('layouts.app')

@section('title', $page->title . ' - SquadBul')
@section('description', $page->meta_description ?? $page->title)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-black via-purple-950 to-black py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Breadcrumb -->
            <nav class="mb-8 text-sm">
                <ol class="flex items-center gap-2 text-gray-400">
                    <li><a href="/" class="hover:text-purple-400 transition-colors">Ana Sayfa</a></li>
                    <li>/</li>
                    <li class="text-white">{{ $page->title }}</li>
                </ol>
            </nav>

            <!-- Page Content -->
            <article class="bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-xl rounded-3xl border border-purple-500/20 p-8 md:p-12 shadow-2xl">
                <!-- Title -->
                <h1 class="text-4xl md:text-5xl font-black text-white mb-6 bg-gradient-to-r from-purple-400 via-pink-400 to-blue-400 bg-clip-text text-transparent">
                    {{ $page->title }}
                </h1>

                <!-- Content -->
                <div class="prose prose-invert prose-purple max-w-none">
                    <div class="text-gray-300 leading-relaxed space-y-4">
                        {!! $page->content !!}
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-12 pt-8 border-t border-white/10">
                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <span>Son güncelleme: {{ $page->updated_at->format('d.m.Y') }}</span>
                        <a href="/" class="text-purple-400 hover:text-purple-300 transition-colors font-semibold">
                            ← Ana Sayfaya Dön
                        </a>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>

<style>
    .prose h1 {
        @apply text-3xl font-black text-white mb-4 mt-8;
    }
    .prose h2 {
        @apply text-2xl font-bold text-white mb-3 mt-6;
    }
    .prose h3 {
        @apply text-xl font-bold text-gray-200 mb-2 mt-4;
    }
    .prose p {
        @apply text-gray-300 mb-4;
    }
    .prose ul, .prose ol {
        @apply text-gray-300 mb-4 ml-6;
    }
    .prose li {
        @apply mb-2;
    }
    .prose a {
        @apply text-purple-400 hover:text-purple-300 transition-colors underline;
    }
    .prose strong {
        @apply text-white font-bold;
    }
</style>
@endsection
