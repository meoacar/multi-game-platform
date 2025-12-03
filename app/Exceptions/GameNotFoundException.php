<?php

namespace App\Exceptions;

use Exception;

/**
 * Game Not Found Exception
 * 
 * Oyun bulunamadığında fırlatılan özel exception sınıfı.
 */
class GameNotFoundException extends Exception
{
    /**
     * Slug ile oyun bulunamadı
     */
    public static function bySlug(string $slug): self
    {
        return new self("'{$slug}' slug'ına sahip oyun bulunamadı.");
    }

    /**
     * ID ile oyun bulunamadı
     */
    public static function byId(int $id): self
    {
        return new self("ID {$id} olan oyun bulunamadı.");
    }

    /**
     * Aktif oyun bulunamadı
     */
    public static function noActiveGames(): self
    {
        return new self('Şu anda aktif oyun bulunmamaktadır.');
    }
}
