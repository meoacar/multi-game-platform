<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Database Exception
 * 
 * Veritabanı hatalarını sanitize eden ve güvenli mesajlar döndüren exception sınıfı.
 * Hassas bilgileri (tablo adları, SQL sorguları) kullanıcıdan gizler.
 */
class DatabaseException extends Exception
{
    protected string $sanitizedMessage;
    protected string $originalMessage;
    protected bool $isAdmin;

    /**
     * Constructor
     */
    public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        $this->originalMessage = $message;
        $this->isAdmin = auth()->check() && auth()->user()->is_admin;
        $this->sanitizedMessage = $this->sanitizeMessage($message);
        
        parent::__construct($this->sanitizedMessage, $code, $previous);
    }

    /**
     * Veritabanı hata mesajını sanitize et
     * Hassas bilgileri kaldır
     */
    protected function sanitizeMessage(string $message): string
    {
        // Admin kullanıcılar için orijinal mesajı göster
        if ($this->isAdmin) {
            return $message;
        }

        // Kullanıcılar için genel mesaj
        return 'Bir veritabanı hatası oluştu. Lütfen daha sonra tekrar deneyin.';
    }

    /**
     * Orijinal mesajı al (loglama için)
     */
    public function getOriginalMessage(): string
    {
        return $this->originalMessage;
    }

    /**
     * Sanitize edilmiş mesajı al
     */
    public function getSanitizedMessage(): string
    {
        return $this->sanitizedMessage;
    }

    /**
     * Kullanıcıya gösterilecek mesajı al
     */
    public function getUserMessage(): string
    {
        if ($this->isAdmin) {
            return "Veritabanı Hatası: {$this->originalMessage}";
        }

        return $this->sanitizedMessage;
    }

    /**
     * Veritabanı hatası oluştur ve logla
     */
    public static function create(\Throwable $e): self
    {
        // Hatayı logla
        Log::error('Database error occurred', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'game_id' => session('game_id'),
            'user_id' => auth()->id(),
            'trace' => $e->getTraceAsString(),
        ]);

        return new self($e->getMessage(), $e->getCode(), $e);
    }

    /**
     * Foreign key constraint hatası
     */
    public static function foreignKeyConstraint(string $table = null): self
    {
        $message = $table 
            ? "'{$table}' tablosunda ilişkili kayıtlar bulunduğu için işlem yapılamadı."
            : 'İlişkili kayıtlar bulunduğu için işlem yapılamadı.';

        $exception = new self($message);
        // Admin olmayan kullanıcılar için mesajı override et
        if (!auth()->check() || !auth()->user()->is_admin) {
            $exception->sanitizedMessage = 'İlişkili kayıtlar bulunduğu için işlem yapılamadı.';
        }
        return $exception;
    }

    /**
     * Unique constraint hatası
     */
    public static function uniqueConstraint(string $field = null): self
    {
        $message = $field
            ? "'{$field}' alanı için bu değer zaten kullanılıyor."
            : 'Bu değer zaten kullanılıyor.';

        $exception = new self($message);
        // Admin olmayan kullanıcılar için mesajı override et
        if (!auth()->check() || !auth()->user()->is_admin) {
            $exception->sanitizedMessage = 'Bu değer zaten kullanılıyor.';
        }
        return $exception;
    }

    /**
     * Connection hatası
     */
    public static function connectionFailed(): self
    {
        return new self('Veritabanı bağlantısı kurulamadı. Lütfen daha sonra tekrar deneyin.');
    }
}
