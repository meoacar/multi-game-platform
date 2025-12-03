<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

/**
 * Security Log Service
 * 
 * Multi-game platform için güvenlik loglarını merkezi olarak yöneten servis.
 * Şüpheli erişim denemeleri, oyunla ilgili hatalar ve güvenlik olaylarını loglar.
 * 
 * Requirements: 16.5, 18.3
 */
class SecurityLogService
{
    /**
     * Şüpheli cross-game erişim girişimini logla
     * 
     * @param mixed $resource Erişilmeye çalışılan kaynak
     * @param int $currentGameId Mevcut oyun ID
     * @param int|null $userId Kullanıcı ID
     * @return void
     */
    public function logCrossGameAccessAttempt($resource, int $currentGameId, ?int $userId = null): void
    {
        Log::warning('Security: Cross-game access attempt', [
            'event_type' => 'cross_game_access_attempt',
            'user_id' => $userId ?? auth()->id(),
            'user_email' => auth()->user()?->email,
            'resource_type' => get_class($resource),
            'resource_id' => $resource->id ?? null,
            'resource_game_id' => $resource->game_id ?? null,
            'current_game_id' => $currentGameId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Geçersiz subdomain erişimini logla
     * 
     * @param string $subdomain Geçersiz subdomain
     * @param Request|null $request HTTP request
     * @return void
     */
    public function logInvalidSubdomainAccess(string $subdomain, ?Request $request = null): void
    {
        $request = $request ?? request();
        
        Log::warning('Security: Invalid subdomain access', [
            'event_type' => 'invalid_subdomain_access',
            'subdomain' => $subdomain,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'referer' => $request->header('referer'),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * İnaktif oyun erişimini logla
     * 
     * @param int $gameId Oyun ID
     * @param string $gameSlug Oyun slug
     * @param string $gameName Oyun adı
     * @return void
     */
    public function logInactiveGameAccess(int $gameId, string $gameSlug, string $gameName): void
    {
        Log::info('Security: Inactive game access attempt', [
            'event_type' => 'inactive_game_access',
            'game_id' => $gameId,
            'game_slug' => $gameSlug,
            'game_name' => $gameName,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Oyun bağlamı eksikliği hatasını logla
     * 
     * @param string $action Yapılmaya çalışılan işlem
     * @param array $context Ek bağlam bilgisi
     * @return void
     */
    public function logMissingGameContext(string $action, array $context = []): void
    {
        Log::error('Game Error: Missing game context', array_merge([
            'event_type' => 'missing_game_context',
            'action' => $action,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'ip_address' => request()->ip(),
            'url' => request()->fullUrl(),
            'timestamp' => now()->toDateTimeString(),
        ], $context));
    }

    /**
     * Oyun bulunamadı hatasını logla
     * 
     * @param string $identifier Oyun tanımlayıcı (slug veya ID)
     * @param string $identifierType Tanımlayıcı tipi (slug, id)
     * @param array $context Ek bağlam bilgisi
     * @return void
     */
    public function logGameNotFound(string $identifier, string $identifierType = 'slug', array $context = []): void
    {
        Log::error('Game Error: Game not found', array_merge([
            'event_type' => 'game_not_found',
            'identifier' => $identifier,
            'identifier_type' => $identifierType,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'url' => request()->fullUrl(),
            'timestamp' => now()->toDateTimeString(),
        ], $context));
    }

    /**
     * Oyun veritabanı hatasını logla
     * 
     * @param \Exception $exception Exception instance
     * @param string $operation Yapılan işlem
     * @param array $context Ek bağlam bilgisi
     * @return void
     */
    public function logGameDatabaseError(\Exception $exception, string $operation, array $context = []): void
    {
        Log::error('Game Error: Database error', array_merge([
            'event_type' => 'game_database_error',
            'operation' => $operation,
            'error_message' => $exception->getMessage(),
            'error_code' => $exception->getCode(),
            'error_file' => $exception->getFile(),
            'error_line' => $exception->getLine(),
            'game_id' => session('game_id'),
            'user_id' => auth()->id(),
            'timestamp' => now()->toDateTimeString(),
        ], $context));
    }

    /**
     * Oyun yetkilendirme hatasını logla
     * 
     * @param string $resourceType Kaynak tipi
     * @param int|null $resourceId Kaynak ID
     * @param string $action Yapılmaya çalışılan işlem
     * @param array $context Ek bağlam bilgisi
     * @return void
     */
    public function logGameAuthorizationError(string $resourceType, ?int $resourceId, string $action, array $context = []): void
    {
        Log::warning('Game Error: Authorization failed', array_merge([
            'event_type' => 'game_authorization_error',
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'action' => $action,
            'game_id' => session('game_id'),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ], $context));
    }

    /**
     * Genel güvenlik olayını logla
     * 
     * @param string $eventType Olay tipi
     * @param string $message Olay mesajı
     * @param string $severity Önem derecesi (info, warning, error)
     * @param array $context Ek bağlam bilgisi
     * @return void
     */
    public function logSecurityEvent(string $eventType, string $message, string $severity = 'warning', array $context = []): void
    {
        $logData = array_merge([
            'event_type' => $eventType,
            'message' => $message,
            'game_id' => session('game_id'),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ], $context);

        match($severity) {
            'error' => Log::error($message, $logData),
            'warning' => Log::warning($message, $logData),
            'info' => Log::info($message, $logData),
            default => Log::warning($message, $logData),
        };
    }

    /**
     * Brute force girişimini logla
     * 
     * @param string $targetType Hedef tipi (login, api, etc.)
     * @param int $attemptCount Deneme sayısı
     * @return void
     */
    public function logBruteForceAttempt(string $targetType, int $attemptCount): void
    {
        Log::warning('Security: Potential brute force attempt', [
            'event_type' => 'brute_force_attempt',
            'target_type' => $targetType,
            'attempt_count' => $attemptCount,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Şüpheli IP adresini logla
     * 
     * @param string $reason Şüphe nedeni
     * @param array $context Ek bağlam bilgisi
     * @return void
     */
    public function logSuspiciousIp(string $reason, array $context = []): void
    {
        Log::warning('Security: Suspicious IP detected', array_merge([
            'event_type' => 'suspicious_ip',
            'reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ], $context));
    }
}
