<?php

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Step 4 Request - Bildirim Tercihleri
 * 
 * Onboarding sürecinin dördüncü ve son adımında kullanıcının
 * bildirim tercihlerini doğrular.
 */
class Step4Request extends FormRequest
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
            'push_enabled' => 'nullable|boolean',
            'email_notifications' => 'nullable|boolean',
        ];
    }

    /**
     * Türkçe hata mesajları
     */
    public function messages(): array
    {
        return [
            'push_enabled.boolean' => 'Push bildirim tercihi geçerli bir değer olmalıdır',
            'email_notifications.boolean' => 'Email bildirim tercihi geçerli bir değer olmalıdır',
        ];
    }

    /**
     * Özel attribute isimleri
     */
    public function attributes(): array
    {
        return [
            'push_enabled' => 'push bildirimleri',
            'email_notifications' => 'email bildirimleri',
        ];
    }

    /**
     * Validasyon öncesi veri hazırlama
     * 
     * Checkbox'lardan gelen "on" değerlerini boolean'a çevir
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('push_enabled')) {
            $data['push_enabled'] = filter_var($this->push_enabled, FILTER_VALIDATE_BOOLEAN);
        }

        if ($this->has('email_notifications')) {
            $data['email_notifications'] = filter_var($this->email_notifications, FILTER_VALIDATE_BOOLEAN);
        }

        $this->merge($data);
    }
}
