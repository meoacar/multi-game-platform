<?php

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Step 1 Request - PUBG Profil Bilgileri
 * 
 * Onboarding sürecinin ilk adımında kullanıcının
 * PUBG oyun bilgilerini doğrular.
 */
class Step1Request extends FormRequest
{
    /**
     * Kullanıcının bu isteği yapma yetkisi var mı?
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Validasyon kuralları
     */
    public function rules(): array
    {
        return [
            'pubg_id' => 'required|string|max:50|min:3',
            'player_level' => 'required|integer|min:1|max:100',
            'player_tier' => 'required|in:Bronz,Gümüş,Altın,Platin,Elmas,Taç,As,As Ustası,As Hakimi,Fatih',
            'main_server' => 'required|in:Europe,Asia,America',
        ];
    }

    /**
     * Türkçe hata mesajları
     */
    public function messages(): array
    {
        return [
            // PUBG ID
            'pubg_id.required' => 'PUBG ID zorunludur',
            'pubg_id.string' => 'PUBG ID metin formatında olmalıdır',
            'pubg_id.max' => 'PUBG ID en fazla 50 karakter olabilir',
            'pubg_id.min' => 'PUBG ID en az 3 karakter olmalıdır',
            
            // Player Level
            'player_level.required' => 'Oyuncu seviyesi zorunludur',
            'player_level.integer' => 'Oyuncu seviyesi sayı olmalıdır',
            'player_level.min' => 'Oyuncu seviyesi en az 1 olmalıdır',
            'player_level.max' => 'Oyuncu seviyesi en fazla 100 olabilir',
            
            // Player Tier
            'player_tier.required' => 'Oyuncu tier\'i zorunludur',
            'player_tier.in' => 'Geçersiz tier seçimi. Lütfen listeden seçim yapın',
            
            // Main Server
            'main_server.required' => 'Ana sunucu zorunludur',
            'main_server.in' => 'Geçersiz sunucu seçimi. Lütfen listeden seçim yapın',
        ];
    }

    /**
     * Özel attribute isimleri (hata mesajlarında kullanılır)
     */
    public function attributes(): array
    {
        return [
            'pubg_id' => 'PUBG ID',
            'player_level' => 'oyuncu seviyesi',
            'player_tier' => 'tier',
            'main_server' => 'ana sunucu',
        ];
    }
}
