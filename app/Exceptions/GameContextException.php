<?php

namespace App\Exceptions;

use Exception;

/**
 * Game Context Exception
 * 
 * Oyun bağlamı (game context) ile ilgili hatalar için özel exception sınıfı.
 * Bu exception, kullanıcı yanlış oyun bağlamında bir kaynağa erişmeye çalıştığında fırlatılır.
 */
class GameContextException extends Exception
{
    /**
     * Oyun bağlamı bulunamadı hatası
     */
    public static function missing(): self
    {
        return new self('Oyun bağlamı bulunamadı. Lütfen bir oyun seçin.');
    }

    /**
     * Geçersiz oyun bağlamı hatası
     */
    public static function invalid(): self
    {
        return new self('Geçersiz oyun bağlamı.');
    }

    /**
     * Cross-game erişim hatası
     */
    public static function crossGameAccess(string $resourceType, int $resourceGameId, int $currentGameId): self
    {
        return new self(
            "Bu {$resourceType} başka bir oyuna ait. " .
            "Kaynak oyun ID: {$resourceGameId}, Mevcut oyun ID: {$currentGameId}"
        );
    }

    /**
     * Yetkisiz erişim hatası
     */
    public static function unauthorized(): self
    {
        return new self('Bu kaynağa erişim yetkiniz yok.');
    }
}
