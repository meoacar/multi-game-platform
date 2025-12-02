<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoşgeldin - PUBG Mobile Topluluk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 min-h-screen relative">
    <!-- Arkaplan Resmi -->
    <div class="fixed inset-0 z-0">
        <img src="{{ asset('arkaplan/12.jpg') }}" alt="Background" class="w-full h-full object-cover opacity-30">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900/80 via-blue-900/70 to-purple-900/80"></div>
    </div>
    
    <div class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8 relative z-10">
        <div class="w-full max-w-2xl">
            <!-- Logo & Başlık -->
            <div class="text-center mb-8 animate-fade-in">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-game-primary to-game-blue rounded-2xl shadow-2xl shadow-game-primary/30 mb-4 animate-bounce-in hw-accelerate">
                    <span class="text-white font-black text-4xl">P</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-white mb-2">
                    Hoşgeldin! 🎮
                </h1>
                <p class="text-gray-300 text-lg">
                    Profilini tamamla ve topluluğa katıl
                </p>
            </div>

            <!-- Progress Bar -->
            <x-onboarding.progress-bar 
                :current="$currentStep" 
                :total="$totalSteps" 
            />

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 bg-game-success/20 border-2 border-game-success text-green-100 px-6 py-4 rounded-xl flex items-center animate-slide-down">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-game-danger/20 border-2 border-game-danger text-red-100 px-6 py-4 rounded-xl flex items-center animate-slide-down">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 bg-game-primary/20 border-2 border-game-primary text-blue-100 px-6 py-4 rounded-xl flex items-center animate-slide-down">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            <!-- Content Card -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-2xl p-6 sm:p-8 border border-gray-700/50">
                @yield('content')
            </div>

            <!-- Navigation -->
            <x-onboarding.step-navigation 
                :current="$currentStep" 
                :total="$totalSteps"
            />

            <!-- Footer Info -->
            <div class="text-center mt-8 text-gray-400 text-sm">
                <p>Bilgilerini istediğin zaman güncelleyebilirsin</p>
            </div>
        </div>
    </div>

    <!-- Custom Animations -->
    <style>
        @keyframes slide-down {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slide-down {
            animation: slide-down 0.3s ease-out;
        }
    </style>
</body>
</html>
