<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - PUBG Topluluk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: true, mobileMenuOpen: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
            :class="{ '-translate-x-full': !mobileMenuOpen && !sidebarOpen, 'translate-x-0': mobileMenuOpen || sidebarOpen }"
            x-show="sidebarOpen || mobileMenuOpen"
            @click.away="mobileMenuOpen = false">
            
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-6 bg-gray-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                    <span class="text-2xl">🛡️</span>
                    <span class="text-xl font-bold">Admin Panel</span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:block text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-800' }}">
                    <span class="text-xl">📊</span>
                    <span class="font-semibold">Dashboard</span>
                </a>

                <!-- Kullanıcılar -->
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.profiles.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-800' }}">
                    <span class="text-xl">👥</span>
                    <span class="font-semibold">Kullanıcılar</span>
                </a>

                <!-- İçerik Yönetimi -->
                <div x-data="{ open: {{ request()->routeIs('admin.lfg-posts.*') || request()->routeIs('admin.clans.*') || request()->routeIs('admin.guides.*') || request()->routeIs('admin.community-posts.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 transition-all">
                        <div class="flex items-center space-x-3">
                            <span class="text-xl">📝</span>
                            <span class="font-semibold">İçerik Yönetimi</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('admin.lfg-posts.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.lfg-posts.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            İlanlar
                        </a>
                        <a href="{{ route('admin.clans.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.clans.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Klanlar
                        </a>
                        <a href="{{ route('admin.guides.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.guides.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Rehberler
                        </a>
                        <a href="{{ route('admin.community-posts.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.community-posts.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Topluluk
                        </a>
                    </div>
                </div>

                <!-- Raporlar -->
                <a href="{{ route('admin.reports.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-800' }}">
                    <span class="text-xl">⚠️</span>
                    <span class="font-semibold">Raporlar</span>
                </a>

                <!-- Analitik -->
                <div x-data="{ open: {{ request()->routeIs('admin.analytics.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 transition-all">
                        <div class="flex items-center space-x-3">
                            <span class="text-xl">📈</span>
                            <span class="font-semibold">Analitik</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('admin.analytics.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.analytics.index') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Genel Bakış
                        </a>
                        <a href="{{ route('admin.analytics.users') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.analytics.users') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Kullanıcı Analitiği
                        </a>
                        <a href="{{ route('admin.analytics.content') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.analytics.content') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            İçerik Analitiği
                        </a>
                        <a href="{{ route('admin.analytics.platform') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.analytics.platform') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Platform Analitiği
                        </a>
                    </div>
                </div>

                <!-- Divider -->
                <div class="my-4 border-t border-gray-800"></div>

                <!-- Ayarlar -->
                <div x-data="{ open: {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.games.*') || request()->routeIs('admin.xp.*') || request()->routeIs('admin.seo.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 transition-all">
                        <div class="flex items-center space-x-3">
                            <span class="text-xl">⚙️</span>
                            <span class="font-semibold">Ayarlar</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Site Ayarları
                        </a>
                        <a href="{{ route('admin.games.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.games.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Oyunlar
                        </a>
                        <a href="{{ route('admin.xp.badges') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.xp.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            XP & Rozetler
                        </a>
                        <a href="{{ route('admin.seo.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.seo.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            SEO
                        </a>
                    </div>
                </div>

                <!-- Sistem -->
                <div x-data="{ open: {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.security.*') || request()->routeIs('admin.logs.*') || request()->routeIs('admin.queue.*') || request()->routeIs('admin.backup.*') || request()->routeIs('admin.system.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 transition-all">
                        <div class="flex items-center space-x-3">
                            <span class="text-xl">🛡️</span>
                            <span class="font-semibold">Sistem</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('admin.roles.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.roles.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Rol Yönetimi
                        </a>
                        <a href="{{ route('admin.permissions.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.permissions.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Yetki Yönetimi
                        </a>
                        <a href="{{ route('admin.security.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.security.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Güvenlik
                        </a>
                        <a href="{{ route('admin.logs.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.logs.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Loglar
                        </a>
                        <a href="{{ route('admin.queue.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.queue.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Queue Yönetimi
                        </a>
                        <a href="{{ route('admin.system.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.system.index') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Sistem İzleme
                        </a>
                        <a href="{{ route('admin.backup.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.backup.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Yedekleme
                        </a>
                    </div>
                </div>

                <!-- CMS -->
                <div x-data="{ open: {{ request()->routeIs('admin.pages.*') || request()->routeIs('admin.menus.*') || request()->routeIs('admin.widgets.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 transition-all">
                        <div class="flex items-center space-x-3">
                            <span class="text-xl">📄</span>
                            <span class="font-semibold">CMS</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('admin.pages.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.pages.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Sayfalar
                        </a>
                        <a href="{{ route('admin.menus.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.menus.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Menüler
                        </a>
                        <a href="{{ route('admin.widgets.index') }}" class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.widgets.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Widget'lar
                        </a>
                    </div>
                </div>

                <!-- Push Bildirimler -->
                <a href="{{ route('admin.push-notifications.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.push-notifications.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-800' }}">
                    <span class="text-xl">🔔</span>
                    <span class="font-semibold">Push Bildirimler</span>
                </a>
            </nav>

            <!-- Sidebar Footer -->
            <div class="px-4 py-4 border-t border-gray-800">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800">
                    <span class="text-xl">🏠</span>
                    <span>Siteye Dön</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between h-16 px-6">
                    <!-- Mobile Menu Button & Breadcrumb -->
                    <div class="flex items-center space-x-4">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-gray-600 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        
                        <!-- Breadcrumb -->
                        <nav class="hidden md:flex items-center space-x-2 text-sm text-gray-600">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Admin</a>
                            @if(isset($breadcrumbs))
                                @foreach($breadcrumbs as $breadcrumb)
                                    <span>/</span>
                                    @if(isset($breadcrumb['url']))
                                        <a href="{{ $breadcrumb['url'] }}" class="hover:text-gray-900">{{ $breadcrumb['title'] }}</a>
                                    @else
                                        <span class="text-gray-900 font-semibold">{{ $breadcrumb['title'] }}</span>
                                    @endif
                                @endforeach
                            @else
                                <span>/</span>
                                <span class="text-gray-900 font-semibold">@yield('title', 'Dashboard')</span>
                            @endif
                        </nav>
                    </div>

                    <!-- Right Side: Notifications & User Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>
                            
                            <!-- Notifications Dropdown -->
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                                <div class="px-4 py-2 border-b border-gray-200">
                                    <h3 class="font-semibold text-gray-900">Bildirimler</h3>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer">
                                        <p class="text-sm text-gray-900">Yeni rapor alındı</p>
                                        <p class="text-xs text-gray-500 mt-1">5 dakika önce</p>
                                    </div>
                                    <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer">
                                        <p class="text-sm text-gray-900">Yeni kullanıcı kaydı</p>
                                        <p class="text-xs text-gray-500 mt-1">10 dakika önce</p>
                                    </div>
                                </div>
                                <div class="px-4 py-2 border-t border-gray-200">
                                    <a href="#" class="text-sm text-blue-600 hover:text-blue-800">Tümünü Gör</a>
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded-lg">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <span class="hidden md:block text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- User Dropdown -->
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                                <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    👤 Profilim
                                </a>
                                <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    ⚙️ Ayarlar
                                </a>
                                <div class="border-t border-gray-200 my-2"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        🚪 Çıkış Yap
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mx-6 mt-6">
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm" x-data="{ show: true }" x-show="show">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                                <button @click="show = false" class="text-green-500 hover:text-green-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mx-6 mt-6">
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm" x-data="{ show: true }" x-show="show">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                                <button @click="show = false" class="text-red-500 hover:text-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mx-6 mt-6">
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow-sm" x-data="{ show: true }" x-show="show">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <p class="text-sm font-medium text-yellow-800">{{ session('warning') }}</p>
                                </div>
                                <button @click="show = false" class="text-yellow-500 hover:text-yellow-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                <div class="p-6">
                    @yield('content')
                </div>

                <!-- Footer -->
                <footer class="bg-white border-t border-gray-200 mt-auto">
                    <div class="px-6 py-4">
                        <div class="flex flex-col md:flex-row items-center justify-between text-sm text-gray-600">
                            <p>&copy; {{ date('Y') }} PUBG Mobile Topluluk. Tüm hakları saklıdır.</p>
                            <div class="flex items-center space-x-4 mt-2 md:mt-0">
                                <span>Laravel {{ app()->version() }}</span>
                                <span>•</span>
                                <span>PHP {{ PHP_VERSION }}</span>
                                <span>•</span>
                                <span>Admin Panel v1.0</span>
                            </div>
                        </div>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div x-show="mobileMenuOpen" 
         @click="mobileMenuOpen = false"
         class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
