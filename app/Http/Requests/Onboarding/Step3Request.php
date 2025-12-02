<?php

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Step 3 Request - İlgi Alanları
 * 
 * Onboarding sürecinin üçüncü adımında kullanıcının
 * ilgi alanlarını doğrular.
 */
class Step3Request extends FormRequest
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
            'interests' => 'required|array|min:1',
            'interests.*' => 'in:lfg,clan,tournament,social',
        ];
    }

    /**
     * Türkçe hata mesajları
     */
    public function messages(): array
    {
        return [
            // Interests
            'interests.required' => 'İlgi alanları zorunludur',
            'interests.array' => 'İlgi alanları liste formatında olmalıdır',
            'interests.min' => 'En az bir ilgi alanı seçmelisiniz',
            'interests.*.in' => 'Geçersiz ilgi alanı seçimi',
        ];
    }

    /**
     * Özel attribute isimleri
     */
    public function attributes(): array
    {
        return [
            'interests' => 'ilgi alanları',
        ];
    }
}
