<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Kullanıcı Giriş Form Request
 * Kullanıcı girişi için validasyon kuralları
 */
class LoginRequest extends FormRequest
{
    /**
     * Kullanıcının bu isteği yapmaya yetkisi var mı?
     * Giriş işlemi herkese açık olduğu için true
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validasyon kuralları
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    /**
     * Özel hata mesajları (Türkçe)
     */
    public function messages(): array
    {
        return [
            'email.required' => 'E-posta adresi zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'password.required' => 'Şifre zorunludur.',
        ];
    }

    /**
     * Alan isimlerini Türkçeleştir
     */
    public function attributes(): array
    {
        return [
            'email' => 'e-posta',
            'password' => 'şifre',
        ];
    }
}
