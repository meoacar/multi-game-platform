<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kayıt Ol - PUBG Mobile Topluluk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white min-h-screen overflow-x-hidden">
    
    <!-- PUBG Mobile Arka Plan -->
    <div class="fixed inset-0 z-0 overflow-hidden bg-black">
        <div class="absolute inset-0" 
             style="background-image: url('/arkaplan/1.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.15;">
        </div>
        <div class="absolute inset-0 bg-gradient-to-br from-orange-900/20 via-red-900/20 to-pink-900/20"></div>
    </div>

    <!-- Navbar -->
    <div class="relative z-20">
        <x-navbar />
    </div>

    <!-- Main Content -->
    <div class="relative z-10 min-h-screen flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-md space-y-8">
            
            <!-- Header -->
            <div class="text-center space-y-3">
                <h1 class="text-5xl md:text-6xl font-black">
                    <span class="bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent">
                        Aramıza Katıl
                    </span>
                </h1>
                <p class="text-gray-400 text-sm">
                    Türkiye'nin en büyük PUBG Mobile topluluğu
                </p>
            </div>

            <!-- Form Card -->
            <div class="bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-800 shadow-2xl">
                
                @if ($errors->any())
                    <div class="mb-6 bg-red-500/10 border border-red-500/30 rounded-xl p-4">
                        <ul class="text-red-400 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST" class="space-y-5" x-data="{ showPassword: false, showPasswordConfirm: false }">
                    @csrf
                    
                    <!-- Name -->
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-semibold text-gray-300">
                            Kullanıcı Adı
                        </label>
                        <input 
                            id="name" 
                            name="name" 
                            type="text" 
                            required 
                            value="{{ old('name') }}"
                            class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all"
                            placeholder="Kullanıcı adını gir">
                    </div>
                    
                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-semibold text-gray-300">
                            Email
                        </label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all"
                            placeholder="ornek@email.com">
                    </div>
                    
                    <!-- Password -->
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-semibold text-gray-300">
                            Şifre
                        </label>
                        <div class="relative">
                            <input 
                                id="password" 
                                name="password" 
                                :type="showPassword ? 'text' : 'password'" 
                                required
                                class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all pr-12"
                                placeholder="En az 8 karakter">
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-400 transition-colors">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Password Confirmation -->
                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-300">
                            Şifre Tekrar
                        </label>
                        <div class="relative">
                            <input 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                :type="showPasswordConfirm ? 'text' : 'password'" 
                                required
                                class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all pr-12"
                                placeholder="Şifreni tekrar gir">
                            <button 
                                type="button" 
                                @click="showPasswordConfirm = !showPasswordConfirm"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-400 transition-colors">
                                <svg x-show="!showPasswordConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showPasswordConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start gap-3">
                        <input 
                            type="checkbox" 
                            name="terms" 
                            id="terms"
                            required
                            class="mt-1 w-4 h-4 rounded border-gray-600 bg-gray-700 text-orange-500 focus:ring-orange-500 focus:ring-offset-0 cursor-pointer">
                        <label for="terms" class="text-sm text-gray-400 leading-relaxed">
                            <a href="{{ route('pages.show', 'kullanim-sartlari') }}" target="_blank" class="text-orange-400 hover:text-orange-300 font-medium underline">Kullanım koşullarını</a> ve 
                            <a href="{{ route('pages.show', 'gizlilik-politikasi') }}" target="_blank" class="text-orange-400 hover:text-orange-300 font-medium underline">gizlilik politikasını</a> kabul ediyorum
                        </label>
                    </div>

                    <!-- Submit -->
                    <button 
                        type="submit"
                        class="w-full py-3.5 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-orange-500/25">
                        Kayıt Ol
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-800"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-2 bg-gray-900 text-gray-500">veya</span>
                    </div>
                </div>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-400">
                        Zaten hesabın var mı?
                        <a href="{{ route('login') }}" class="text-orange-400 hover:text-orange-300 font-semibold ml-1">
                            Giriş yap
                        </a>
                    </p>
                </div>
            </div>

            <!-- Back Home -->
            <div class="text-center">
                <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-400 inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Ana sayfaya dön
                </a>
            </div>
        </div>
    </div>
</body>
</html>
