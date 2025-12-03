<?php

namespace App\Traits;

use App\Exceptions\GameContextException;
use App\Services\SecurityLogService;

/**
 * Has Game Context Trait
 * 
 * Policy'lerde oyun bağlamı kontrolü için yardımcı metodlar sağlar.
 * Bu trait, cross-game erişimi önlemek ve güvenlik loglarını tutmak için kullanılır.
 */
trait HasGameContext
{
    /**
     * Kaynağın oyun bağlamını kontrol et
     * 
     * @param mixed $resource Kontrol edilecek kaynak (model instance)
     * @param int|null $currentGameId Mevcut oyun ID (null ise session'dan alınır)
     * @return bool
     * @throws GameContextException
     */
    protected function checkGameContext($resource, ?int $currentGameId = null): bool
    {
        // Mevcut oyun ID'sini al
        $currentGameId = $currentGameId ?? session('game_id');

        // Oyun bağlamı yoksa hata fırlat
        if (!$currentGameId) {
            // Eksik game context'i logla
            app(SecurityLogService::class)->logMissingGameContext(
                'Policy check: ' . get_class($this),
                ['resource_type' => get_class($resource)]
            );
            
            throw GameContextException::missing();
        }

        // Kaynak game_id'ye sahip değilse (cross-game kaynak), kontrol yapma
        if (!isset($resource->game_id)) {
            return true;
        }

        // Kaynak başka bir oyuna aitse
        if ($resource->game_id !== $currentGameId) {
            // Şüpheli erişim girişimini logla
            $this->logSuspiciousAccess($resource, $currentGameId);
            
            throw GameContextException::crossGameAccess(
                class_basename($resource),
                $resource->game_id,
                $currentGameId
            );
        }

        return true;
    }

    /**
     * Şüpheli erişim girişimini logla
     * 
     * @param mixed $resource
     * @param int $currentGameId
     * @return void
     */
    protected function logSuspiciousAccess($resource, int $currentGameId): void
    {
        app(SecurityLogService::class)->logCrossGameAccessAttempt(
            $resource,
            $currentGameId,
            auth()->id()
        );
    }

    /**
     * Kullanıcının admin olup olmadığını kontrol et
     * Admin kullanıcılar game context kontrolünden muaf tutulabilir
     * 
     * @param \App\Models\User|null $user
     * @return bool
     */
    protected function isAdmin($user): bool
    {
        return $user && method_exists($user, 'isAdmin') && $user->isAdmin();
    }

    /**
     * Game context kontrolünü atla (sadece admin için)
     * 
     * @param \App\Models\User|null $user
     * @return bool
     */
    protected function canBypassGameContext($user): bool
    {
        return $this->isAdmin($user);
    }
}
