<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bakım Modu - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Rajdhani', sans-serif;
        }
        
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        /* Full Screen Background */
        .bg-pubg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('/arkaplan/pubg-mobile-3840x2160-19041.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -2;
        }
        
        /* Dark Overlay */
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.85) 0%, rgba(20, 20, 30, 0.9) 50%, rgba(0, 0, 0, 0.85) 100%);
            z-index: -1;
        }
        
        /* Glowing Effect - Cyberpunk Blue/Purple */
        .glow-cyber {
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.5),
                        0 0 60px rgba(147, 51, 234, 0.4),
                        0 0 90px rgba(59, 130, 246, 0.3);
        }
        
        .glow-text {
            text-shadow: 0 0 15px rgba(59, 130, 246, 1),
                         0 0 30px rgba(147, 51, 234, 0.8),
                         0 0 45px rgba(59, 130, 246, 0.6),
                         0 0 60px rgba(147, 51, 234, 0.4);
        }
        
        /* Glass Effect */
        .glass-effect {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        
        /* Pulse Animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        
        .pulse-animation {
            animation: pulse 2s ease-in-out infinite;
        }
        
        /* Scan Line Effect */
        .scan-line {
            position: relative;
            overflow: hidden;
        }
        
        .scan-line::before {
            content: '';
            position: absolute;
            top: -100%;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(transparent, rgba(255, 165, 0, 0.1), transparent);
            animation: scan 3s ease-in-out infinite;
        }
        
        @keyframes scan {
            0% { top: -100%; }
            50% { top: 100%; }
            100% { top: 100%; }
        }
        
        /* Hexagon Pattern */
        .hex-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0l25.98 15v30L30 60 4.02 45V15z' fill='none' stroke='rgba(255,165,0,0.05)' stroke-width='1'/%3E%3C/svg%3E");
        }
        
        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(2deg); }
            66% { transform: translateY(-10px) rotate(-2deg); }
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        /* Glitch Effect */
        @keyframes glitch {
            0%, 100% { transform: translate(0); }
            20% { transform: translate(-2px, 2px); }
            40% { transform: translate(-2px, -2px); }
            60% { transform: translate(2px, 2px); }
            80% { transform: translate(2px, -2px); }
        }
        
        .glitch:hover {
            animation: glitch 0.3s infinite;
        }
        
        /* Animated Particles */
        @keyframes particleFloat {
            0%, 100% { 
                transform: translate(0, 0) rotate(0deg);
                opacity: 0.3;
            }
            25% { 
                transform: translate(10px, -20px) rotate(90deg);
                opacity: 0.6;
            }
            50% { 
                transform: translate(-10px, -40px) rotate(180deg);
                opacity: 0.3;
            }
            75% { 
                transform: translate(15px, -60px) rotate(270deg);
                opacity: 0.6;
            }
        }
        
        .particle {
            animation: particleFloat 8s ease-in-out infinite;
        }
        
        /* Cyberpunk Gradient */
        .cyber-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 50%, #3b82f6 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative">
    <!-- PUBG Background Image -->
    <div class="bg-pubg"></div>
    
    <!-- Dark Overlay -->
    <div class="bg-overlay"></div>
    <div class="max-w-4xl w-full relative z-10">
        <!-- Ana Kart -->
        <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden glow-cyber scan-line">
            <!-- Üst Kısım - Cyberpunk Header -->
            <div class="relative bg-gradient-to-r from-blue-600/30 via-purple-600/30 to-blue-600/30 p-8 md:p-12 text-center border-b border-blue-500/40">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <defs>
                            <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"/>
                            </pattern>
                        </defs>
                        <rect width="100" height="100" fill="url(#grid)" />
                    </svg>
                </div>
                
                <!-- Icon -->
                <div class="float-animation inline-block relative z-10">
                    <div class="text-8xl mb-4 pulse-animation">
                        <svg class="w-24 h-24 mx-auto text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6zm0-10c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Title -->
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-3 glow-text tracking-wider uppercase relative z-10">
                    BAKIM MODU
                </h1>
                <div class="flex items-center justify-center gap-2 mb-4">
                    <div class="h-px w-12 bg-gradient-to-r from-transparent to-blue-500"></div>
                    <p class="text-blue-400 text-xl font-semibold tracking-widest uppercase">SYSTEM UPGRADE</p>
                    <div class="h-px w-12 bg-gradient-to-l from-transparent to-blue-500"></div>
                </div>
                <p class="text-gray-300 text-lg relative z-10">Sunucularımız güncelleniyor</p>
            </div>
            
            <!-- İçerik -->
            <div class="p-8 md:p-12 relative space-y-8">
                <!-- Ana Mesaj Kartı -->
                <div class="relative overflow-hidden rounded-2xl">
                    <!-- Animated Border -->
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 via-purple-500 to-blue-500 opacity-50 blur-xl"></div>
                    
                    <div class="relative glass-effect border-2 border-blue-500/50 p-8 rounded-2xl">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center pulse-animation">
                                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-blue-400 mb-2 uppercase tracking-wide">Önemli Bilgilendirme</h3>
                                <p class="text-gray-100 text-lg leading-relaxed">
                                    {{ $message }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tahmini Süre -->
                @if($eta)
                <div class="glass-effect border border-blue-500/40 rounded-2xl p-6 hover:border-blue-500/60 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 border border-blue-500/40 flex items-center justify-center">
                                <svg class="w-7 h-7 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-blue-400 font-semibold uppercase tracking-widest mb-1">Tahmini Tamamlanma</p>
                                <p class="text-white text-3xl font-bold">{{ $eta }}</p>
                            </div>
                        </div>
                        <div class="hidden md:block">
                            <div class="w-20 h-20 rounded-full border-4 border-blue-500/30 border-t-blue-500 animate-spin"></div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Güncelleme Özellikleri -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Performans -->
                    <div class="group relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-transparent rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300"></div>
                        <div class="relative glass-effect border border-blue-500/30 rounded-2xl p-6 hover:border-blue-500/60 transition-all duration-300">
                            <div class="flex flex-col items-center text-center space-y-3">
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-500/30 to-cyan-500/30 border border-blue-500/50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-8 h-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-bold text-lg uppercase tracking-wide">Performans</h4>
                                    <p class="text-gray-400 text-sm mt-1">Hız İyileştirmeleri</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Yeni Özellikler -->
                    <div class="group relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/20 to-transparent rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300"></div>
                        <div class="relative glass-effect border border-purple-500/30 rounded-2xl p-6 hover:border-purple-500/60 transition-all duration-300">
                            <div class="flex flex-col items-center text-center space-y-3">
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-purple-500/30 to-pink-500/30 border border-purple-500/50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-8 h-8 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-bold text-lg uppercase tracking-wide">Özellikler</h4>
                                    <p class="text-gray-400 text-sm mt-1">Yeni Eklentiler</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Güvenlik -->
                    <div class="group relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/20 to-transparent rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300"></div>
                        <div class="relative glass-effect border border-cyan-500/30 rounded-2xl p-6 hover:border-cyan-500/60 transition-all duration-300">
                            <div class="flex flex-col items-center text-center space-y-3">
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-cyan-500/30 to-blue-500/30 border border-cyan-500/50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-8 h-8 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-bold text-lg uppercase tracking-wide">Güvenlik</h4>
                                    <p class="text-gray-400 text-sm mt-1">Sistem Güncellemeleri</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bilgilendirme ve Aksiyon -->
                <div class="glass-effect border border-blue-500/20 rounded-2xl p-8 text-center space-y-6">
                    <div class="space-y-2">
                        <p class="text-gray-300 text-lg font-medium">
                            Güncellemeler tamamlandığında sitemiz otomatik olarak açılacaktır.
                        </p>
                        <p class="text-gray-500 text-sm">
                            Sayfa her 30 saniyede bir otomatik yenileniyor
                        </p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <button onclick="location.reload()" 
                            class="group relative inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-xl hover:from-blue-500 hover:to-purple-500 transition-all transform hover:scale-105 shadow-lg hover:shadow-blue-500/50 uppercase tracking-wider">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-400 rounded-xl blur opacity-50 group-hover:opacity-75 transition-opacity"></div>
                            <svg class="relative w-6 h-6 mr-2 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span class="relative">Şimdi Yenile</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-black/40 px-8 py-6 text-center border-t border-blue-500/30">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <div class="h-px w-8 bg-gradient-to-r from-transparent to-blue-500/50"></div>
                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                    </svg>
                    <div class="h-px w-8 bg-gradient-to-l from-transparent to-blue-500/50"></div>
                </div>
                <p class="text-sm text-gray-400 font-medium">
                    © {{ date('Y') }} <span class="text-blue-500 font-bold">{{ \App\Models\Setting::where('key', 'site_name')->first()->value ?? config('app.name') }}</span>
                </p>
                <p class="text-xs text-gray-600 mt-1">Tüm hakları saklıdır.</p>
            </div>
        </div>
        
        <!-- Admin Giriş Linki -->
        <div class="text-center mt-8 relative z-10">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm text-gray-300 hover:text-blue-400 transition-colors duration-300 glass-effect hover:border-blue-500/60 px-6 py-3 rounded-lg">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span class="font-semibold uppercase tracking-wider">Admin Girişi</span>
            </a>
        </div>
    </div>
    
    <!-- Floating Particles -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute top-1/4 left-1/4 w-3 h-3 bg-blue-500 rounded-full opacity-40 particle"></div>
        <div class="absolute top-1/3 right-1/4 w-4 h-4 bg-purple-500 rounded-full opacity-30 particle" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-1/4 left-1/3 w-2 h-2 bg-cyan-400 rounded-full opacity-50 particle" style="animation-delay: 2s;"></div>
        <div class="absolute top-2/3 right-1/3 w-3 h-3 bg-blue-400 rounded-full opacity-40 particle" style="animation-delay: 3s;"></div>
        <div class="absolute bottom-1/3 right-1/4 w-4 h-4 bg-purple-500 rounded-full opacity-30 particle" style="animation-delay: 4s;"></div>
        <div class="absolute top-1/2 left-1/2 w-2 h-2 bg-cyan-600 rounded-full opacity-50 particle" style="animation-delay: 5s;"></div>
    </div>
    
    <!-- Otomatik Yenileme (Her 30 saniyede bir) -->
    <script>
        // Auto reload after 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
        
        // Countdown timer
        let countdown = 30;
        const timer = setInterval(function() {
            countdown--;
            if (countdown <= 0) {
                clearInterval(timer);
            }
        }, 1000);
        
        // Random particle animation
        function createParticle() {
            const particle = document.createElement('div');
            particle.className = 'fixed w-1 h-1 bg-orange-500 rounded-full pointer-events-none';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.opacity = Math.random() * 0.5;
            particle.style.animation = `float ${3 + Math.random() * 3}s ease-in-out infinite`;
            document.body.appendChild(particle);
            
            setTimeout(() => particle.remove(), 6000);
        }
        
        // Create particles periodically
        setInterval(createParticle, 2000);
    </script>
</body>
</html>
