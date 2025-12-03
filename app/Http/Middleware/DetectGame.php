<?php

namespace App\Http\Middleware;

use App\Exceptions\GameNotFoundException;
use App\Models\Game;
use App\Services\GameService;
use App\Services\SecurityLogService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * DetectGame Middleware
 * 
 * Multi-game platform için subdomain'den oyun algılama middleware'i
 * 
 * Sorumluluklar:
 * - Subdomain'den oyun slug'ını çıkarma
 * - Oyunu veritabanından bulma ve doğrulama
 * - Session'a game_id ve game bilgisini kaydetme
 * - View'lara currentGame değişkenini paylaşma
 * - Geçersiz subdomain durumlarını yönetme
 * - Kullanıcının son ziyaret ettiği oyunu kaydetme
 * 
 * Requirements: 3.1, 3.2, 3.3, 11.1, 11.2, 11.3, 11.4, 11.5
 */
class DetectGame
{
    /**
     * GameService instance
     */
    protected GameService $gameService;

    /**
     * SecurityLogService instance
     */
    protected SecurityLogService $securityLog;

    /**
     * Constructor
     */
    public function __construct(GameService $gameService, SecurityLogService $securityLog)
    {
        $this->gameService = $gameService;
        $this->securityLog = $securityLog;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Onboarding route'larında oyun algılamayı atla (kullanıcı oyun seçecek)
        if ($request->is('onboarding') || $request->is('onboarding/*')) {
            // Sadece step0 için game context'i temizle
            if ($request->is('onboarding') && auth()->check() && !auth()->user()->game_id) {
                $this->clearGameContext();
            }
            return $next($request);
        }
        
        // Subdomain'i al
        $host = $request->getHost();
        $subdomain = $this->extractSubdomain($host);
        
        // Ana domain kontrolü (subdomain yok)
        if (!$subdomain) {
            // Ana domain - game context'i temizle
            $this->clearGameContext();
            return $next($request);
        }
        
        // Oyunu bul
        $game = $this->findGameBySubdomain($subdomain);
        
        // Geçersiz subdomain kontrolü
        if (!$game) {
            throw GameNotFoundException::bySlug($subdomain);
        }
        
        // Oyun aktif mi kontrol et
        if ($game->status !== 'active') {
            return $this->handleInactiveGame($request, $game);
        }
        
        // Game context'i ayarla
        $this->setGameContext($game);
        
        // View'a paylaş
        view()->share('currentGame', $game);
        
        // Kullanıcının son ziyaret ettiği oyunu kaydet
        if (auth()->check()) {
            $this->gameService->saveLastVisitedGame(auth()->id(), $game->id);
        }
        
        return $next($request);
    }

    /**
     * Subdomain'i host'tan çıkar
     * 
     * @param string $host Host adı (örn: pubg.takimsistemi.com)
     * @return string|null Subdomain (örn: pubg) veya null
     */
    protected function extractSubdomain(string $host): ?string
    {
        // Host'u parçalara ayır
        $parts = explode('.', $host);
        
        // Minimum 3 parça olmalı (subdomain.domain.tld)
        if (count($parts) < 3) {
            return null;
        }
        
        // İlk parça subdomain'dir
        $subdomain = $parts[0];
        
        // 'www' subdomain'ini yoksay
        if ($subdomain === 'www') {
            return null;
        }
        
        // Localhost kontrolü
        if ($host === 'localhost' || str_starts_with($host, '127.0.0.1') || str_starts_with($host, '192.168.')) {
            return null;
        }
        
        return $subdomain;
    }

    /**
     * Subdomain'e göre oyunu bul
     * 
     * @param string $subdomain Subdomain slug'ı
     * @return Game|null Oyun modeli veya null
     */
    protected function findGameBySubdomain(string $subdomain): ?Game
    {
        try {
            // Cache'den veya veritabanından oyunu bul
            return Game::bySlug($subdomain)->first();
        } catch (\Exception $e) {
            // Veritabanı hatasını logla
            $this->securityLog->logGameDatabaseError(
                $e,
                'find_game_by_subdomain',
                ['subdomain' => $subdomain]
            );
            
            return null;
        }
    }

    /**
     * Game context'i session'a kaydet
     * 
     * @param Game $game Oyun modeli
     * @return void
     */
    protected function setGameContext(Game $game): void
    {
        session([
            'game_id' => $game->id,
            'game' => $game,
            'game_slug' => $game->slug,
        ]);
    }

    /**
     * Game context'i temizle
     * 
     * @return void
     */
    protected function clearGameContext(): void
    {
        session()->forget(['game_id', 'game', 'game_slug']);
    }

    /**
     * Geçersiz subdomain durumunu yönet
     * 
     * @param Request $request HTTP request
     * @param string $subdomain Geçersiz subdomain
     * @return Response HTTP response
     */
    protected function handleInvalidSubdomain(Request $request, string $subdomain): Response
    {
        // Şüpheli erişim girişimini logla
        $this->securityLog->logInvalidSubdomainAccess($subdomain, $request);
        
        // Game context'i temizle
        $this->clearGameContext();
        
        // API isteği mi kontrol et
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => 'Geçersiz oyun seçimi',
                'message' => 'Belirtilen oyun bulunamadı veya aktif değil.',
                'subdomain' => $subdomain,
            ], 404);
        }
        
        // Web isteği için ana sayfaya yönlendir
        return redirect()
            ->to($this->getMainDomainUrl())
            ->with('error', 'Geçersiz oyun seçimi. Lütfen geçerli bir oyun seçin.');
    }

    /**
     * İnaktif oyun durumunu yönet
     * 
     * @param Request $request HTTP request
     * @param Game $game İnaktif oyun
     * @return Response HTTP response
     */
    protected function handleInactiveGame(Request $request, Game $game): Response
    {
        // İnaktif oyun erişimini logla
        $this->securityLog->logInactiveGameAccess(
            $game->id,
            $game->slug,
            $game->name
        );
        
        // Game context'i temizle
        $this->clearGameContext();
        
        // API isteği mi kontrol et
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => 'Oyun aktif değil',
                'message' => "{$game->name} şu anda aktif değil.",
                'game_slug' => $game->slug,
            ], 403);
        }
        
        // Web isteği için ana sayfaya yönlendir
        return redirect()
            ->to($this->getMainDomainUrl())
            ->with('warning', "{$game->name} şu anda aktif değil. Lütfen başka bir oyun seçin.");
    }

    /**
     * Ana domain URL'ini al
     * 
     * @return string Ana domain URL'i
     */
    protected function getMainDomainUrl(): string
    {
        $domain = config('app.domain', 'takimsistemi.com');
        $protocol = config('app.url_protocol', 'https');
        
        // Localhost kontrolü
        if (app()->environment('local')) {
            return config('app.url', 'http://localhost');
        }
        
        return "{$protocol}://{$domain}";
    }
}
