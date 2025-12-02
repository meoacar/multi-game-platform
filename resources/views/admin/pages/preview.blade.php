<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $page->meta_description ?? $page->excerpt_text }}">
    <meta name="keywords" content="{{ $page->meta_keywords }}">
    <title>{{ $page->seo_title }} - Önizleme</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .preview-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            text-align: center;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .preview-info {
            background: #f3f4f6;
            border-left: 4px solid #667eea;
            padding: 1rem;
            margin: 2rem 0;
        }
        
        .preview-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        @media (max-width: 768px) {
            .preview-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Önizleme Banner -->
    <div class="preview-banner">
        🔍 ÖNİZLEME MODU - Bu sayfa henüz yayınlanmamıştır
    </div>

    <!-- Sayfa İçeriği -->
    <div class="preview-content">
        <!-- Sayfa Bilgileri -->
        <div class="preview-info rounded-lg">
            <h3 class="font-bold text-lg mb-2">📄 Sayfa Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <strong>Başlık:</strong> {{ $page->title }}
                </div>
                <div>
                    <strong>Slug:</strong> {{ $page->slug }}
                </div>
                <div>
                    <strong>Şablon:</strong> {{ $page->template }}
                </div>
                <div>
                    <strong>Durum:</strong> 
                    <span class="px-2 py-1 rounded {{ $page->is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $page->is_published ? 'Yayında' : 'Taslak' }}
                    </span>
                </div>
                @if($page->meta_title)
                <div class="md:col-span-2">
                    <strong>SEO Başlık:</strong> {{ $page->meta_title }}
                </div>
                @endif
                @if($page->meta_description)
                <div class="md:col-span-2">
                    <strong>SEO Açıklama:</strong> {{ $page->meta_description }}
                </div>
                @endif
                @if($page->meta_keywords)
                <div class="md:col-span-2">
                    <strong>Anahtar Kelimeler:</strong> {{ $page->meta_keywords }}
                </div>
                @endif
            </div>
        </div>

        <!-- Sayfa İçeriği -->
        <div class="bg-white rounded-lg shadow-lg p-8 mt-6">
            @if($page->template === 'full-width')
                <!-- Tam Genişlik Şablon -->
                <article class="prose prose-lg max-w-none">
                    <h1 class="text-4xl font-bold mb-6">{{ $page->title }}</h1>
                    @if($page->excerpt)
                        <div class="text-xl text-gray-600 mb-8 italic">
                            {{ $page->excerpt }}
                        </div>
                    @endif
                    <div class="content">
                        {!! $page->content !!}
                    </div>
                </article>
            
            @elseif($page->template === 'sidebar-left')
                <!-- Sol Kenar Çubuğu Şablon -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <aside class="md:col-span-1 bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-bold mb-4">Kenar Çubuğu</h3>
                        <p class="text-sm text-gray-600">Widget'lar buraya gelecek</p>
                    </aside>
                    <article class="md:col-span-3 prose prose-lg">
                        <h1 class="text-4xl font-bold mb-6">{{ $page->title }}</h1>
                        @if($page->excerpt)
                            <div class="text-xl text-gray-600 mb-8 italic">
                                {{ $page->excerpt }}
                            </div>
                        @endif
                        <div class="content">
                            {!! $page->content !!}
                        </div>
                    </article>
                </div>
            
            @elseif($page->template === 'sidebar-right')
                <!-- Sağ Kenar Çubuğu Şablon -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <article class="md:col-span-3 prose prose-lg">
                        <h1 class="text-4xl font-bold mb-6">{{ $page->title }}</h1>
                        @if($page->excerpt)
                            <div class="text-xl text-gray-600 mb-8 italic">
                                {{ $page->excerpt }}
                            </div>
                        @endif
                        <div class="content">
                            {!! $page->content !!}
                        </div>
                    </article>
                    <aside class="md:col-span-1 bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-bold mb-4">Kenar Çubuğu</h3>
                        <p class="text-sm text-gray-600">Widget'lar buraya gelecek</p>
                    </aside>
                </div>
            
            @elseif($page->template === 'landing')
                <!-- Landing Page Şablon -->
                <div class="text-center">
                    <h1 class="text-5xl font-bold mb-6">{{ $page->title }}</h1>
                    @if($page->excerpt)
                        <div class="text-2xl text-gray-600 mb-12">
                            {{ $page->excerpt }}
                        </div>
                    @endif
                    <div class="prose prose-lg max-w-none mx-auto">
                        {!! $page->content !!}
                    </div>
                </div>
            
            @else
                <!-- Varsayılan Şablon -->
                <article class="prose prose-lg max-w-4xl mx-auto">
                    <h1 class="text-4xl font-bold mb-6">{{ $page->title }}</h1>
                    @if($page->excerpt)
                        <div class="text-xl text-gray-600 mb-8 italic">
                            {{ $page->excerpt }}
                        </div>
                    @endif
                    <div class="content">
                        {!! $page->content !!}
                    </div>
                </article>
            @endif
        </div>

        <!-- Önizleme Kontrolleri -->
        <div class="mt-8 flex justify-center gap-4">
            <a href="{{ route('admin.pages.edit', $page) }}" 
               class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                ✏️ Düzenle
            </a>
            <a href="{{ route('admin.pages.index') }}" 
               class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                ← Geri Dön
            </a>
        </div>
    </div>
</body>
</html>
