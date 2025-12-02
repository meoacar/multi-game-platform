<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Widget Önizleme - {{ $widget->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .widget-shadow {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body class="min-h-screen gradient-bg p-4 md:p-8" x-data="{ showDetails: false }">
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header Card -->
        <div class="glass-effect rounded-2xl p-6 widget-shadow animate-fade-in-up">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Widget Önizleme</h1>
                        <p class="text-sm text-gray-600">{{ $widget->title }}</p>
                    </div>
                </div>
                <button @click="window.close()" 
                        class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 transition flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Badges -->
            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                    {{ \App\Models\Widget::getAvailableTypes()[$widget->type] }}
                </span>
                <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">
                    {{ \App\Models\Widget::getAvailableLocations()[$widget->location] }}
                </span>
                <span class="px-3 py-1 {{ $widget->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }} text-xs font-semibold rounded-full">
                    {{ $widget->is_active ? '✓ Aktif' : '✗ Pasif' }}
                </span>
                <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">
                    Sıra: {{ $widget->order }}
                </span>
            </div>
        </div>

        <!-- Widget Preview -->
        <div class="glass-effect rounded-2xl p-8 widget-shadow animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="mb-4 pb-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Canlı Önizleme
                </h2>
            </div>
            
            <div class="widget-content">
                {!! $renderedContent !!}
            </div>
        </div>

        <!-- Details Toggle -->
        <div class="glass-effect rounded-2xl overflow-hidden widget-shadow animate-fade-in-up" style="animation-delay: 0.2s;">
            <button @click="showDetails = !showDetails" 
                    class="w-full px-6 py-4 flex items-center justify-between hover:bg-white/50 transition">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold text-gray-900">Widget Detayları</span>
                </div>
                <svg class="w-5 h-5 text-gray-600 transition-transform" :class="{ 'rotate-180': showDetails }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <div x-show="showDetails" x-collapse class="px-6 pb-6">
                <div class="space-y-3 pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm font-medium text-gray-600">Widget ID</span>
                        <span class="text-sm font-semibold text-gray-900">#{{ $widget->id }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm font-medium text-gray-600">Slug</span>
                        <span class="text-sm font-mono text-gray-900 bg-gray-100 px-2 py-1 rounded">{{ $widget->slug }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm font-medium text-gray-600">Oluşturma</span>
                        <span class="text-sm text-gray-900">{{ $widget->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm font-medium text-gray-600">Güncelleme</span>
                        <span class="text-sm text-gray-900">{{ $widget->updated_at->format('d.m.Y H:i') }}</span>
                    </div>
                    
                    @if($widget->settings)
                    <div class="pt-3 border-t border-gray-200">
                        <span class="text-sm font-medium text-gray-600 block mb-2">Ayarlar</span>
                        <pre class="text-xs bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto font-mono">{{ json_encode($widget->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                    @endif
                    
                    @if($widget->content && in_array($widget->type, ['html', 'ad']))
                    <div class="pt-3 border-t border-gray-200">
                        <span class="text-sm font-medium text-gray-600 block mb-2">HTML Kodu</span>
                        <pre class="text-xs bg-gray-900 text-blue-400 p-4 rounded-lg overflow-x-auto font-mono max-h-64">{{ $widget->content }}</pre>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-2 gap-4 animate-fade-in-up" style="animation-delay: 0.3s;">
            <a href="{{ route('admin.widgets.edit', $widget) }}" 
               class="glass-effect rounded-xl p-4 hover:bg-white/50 transition flex items-center justify-center gap-2 font-semibold text-gray-900 widget-shadow">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Düzenle
            </a>
            <button onclick="window.close()" 
                    class="glass-effect rounded-xl p-4 hover:bg-white/50 transition flex items-center justify-center gap-2 font-semibold text-gray-900 widget-shadow">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Kapat
            </button>
        </div>

        <!-- Footer Info -->
        <div class="text-center text-white/80 text-sm animate-fade-in-up" style="animation-delay: 0.4s;">
            <p>PUBG Mobile Topluluk - Widget Yönetim Sistemi</p>
        </div>
    </div>
</body>
</html>
