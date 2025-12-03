<?php

namespace App\Rules;

use App\Models\Game;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Game ID Validation Rule
 * 
 * Oyun ID'sinin geçerli ve aktif olduğunu doğrular.
 * Opsiyonel olarak mevcut game context ile eşleşmesini kontrol eder.
 */
class ValidGameId implements ValidationRule
{
    /**
     * Mevcut game context ile eşleşme kontrolü yapılsın mı?
     */
    protected bool $requireMatchContext;

    /**
     * Sadece aktif oyunlar kabul edilsin mi?
     */
    protected bool $requireActive;

    /**
     * Constructor
     * 
     * @param bool $requireMatchContext Session'daki game_id ile eşleşmeli mi?
     * @param bool $requireActive Sadece aktif oyunlar kabul edilsin mi?
     */
    public function __construct(
        bool $requireMatchContext = false,
        bool $requireActive = true
    ) {
        $this->requireMatchContext = $requireMatchContext;
        $this->requireActive = $requireActive;
    }

    /**
     * Validation logic
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Boş değer kontrolü (nullable ise başka rule ile kontrol edilir)
        if (empty($value)) {
            return;
        }

        // Numeric kontrolü
        if (!is_numeric($value)) {
            $fail('Geçersiz oyun ID formatı.');
            return;
        }

        // Oyunu bul
        $query = Game::where('id', $value);

        // Sadece aktif oyunlar
        if ($this->requireActive) {
            $query->where('status', 'active');
        }

        $game = $query->first();

        // Oyun bulunamadı
        if (!$game) {
            if ($this->requireActive) {
                $fail('Seçilen oyun bulunamadı veya aktif değil.');
            } else {
                $fail('Seçilen oyun bulunamadı.');
            }
            return;
        }

        // Game context kontrolü
        if ($this->requireMatchContext) {
            $sessionGameId = session('game_id');
            
            if ($sessionGameId && $game->id !== $sessionGameId) {
                $fail('Seçilen oyun, mevcut oyun bağlamı ile eşleşmiyor.');
                return;
            }
        }
    }

    /**
     * Static factory method - Aktif oyun kontrolü
     */
    public static function active(): self
    {
        return new self(requireActive: true);
    }

    /**
     * Static factory method - Context eşleşme kontrolü
     */
    public static function matchContext(): self
    {
        return new self(requireMatchContext: true, requireActive: true);
    }

    /**
     * Static factory method - Tüm oyunlar (aktif + inaktif)
     */
    public static function any(): self
    {
        return new self(requireActive: false);
    }
}
