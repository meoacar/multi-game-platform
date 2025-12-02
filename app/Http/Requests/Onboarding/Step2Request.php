<?php

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Step 2 Request - Oyun Tercihleri
 * 
 * Onboarding sürecinin ikinci adımında kullanıcının
 * oyun tercihlerini doğrular.
 */
class Step2Request extends FormRequest
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
            'favorite_mode' => 'required|in:TPP,FPP',
            'favorite_type' => 'required|in:Solo,Duo,Squad',
            'active_hours' => 'required|array|min:1',
            'active_hours.*' => 'in:morning,afternoon,evening,night',
            'language' => 'nullable|in:tr,en',
        ];
    }

    /**
     * Türkçe hata mesajları
     */
    public function messages(): array
    {
        return [
            // Favorite Mode
            'favorite_mode.required' => 'Favori oyun modu zorunludur',
            'favorite_mode.in' => 'Geçersiz oyun modu. TPP veya FPP seçmelisiniz',
            
            // Favorite Type
            'favorite_type.required' => 'Favori oyun tipi zorunludur',
            'favorite_type.in' => 'Geçersiz oyun tipi. Solo, Duo veya Squad seçmelisiniz',
            
            // Active Hours
            'active_hours.required' => 'Aktif oyun saatleri zorunludur',
            'active_hours.array' => 'Aktif oyun saatleri liste formatında olmalıdır',
            'active_hours.min' => 'En az bir zaman dilimi seçmelisiniz',
            'active_hours.*.in' => 'Geçersiz zaman dilimi seçimi',
            
            // Language
            'language.in' => 'Geçersiz dil seçimi. Türkçe veya İngilizce seçmelisiniz',
        ];
    }

    /**
     * Özel attribute isimleri
     */
    public function attributes(): array
    {
        return [
            'favorite_mode' => 'favori mod',
            'favorite_type' => 'favori oyun tipi',
            'active_hours' => 'aktif saatler',
            'language' => 'dil',
        ];
    }
}
