<?php

namespace App\Http\Requests\Api\V1\Clan;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Klana Başvuru Form Request
 * Klana başvurmak için validasyon kuralları
 */
class ApplyClanRequest extends FormRequest
{
    /**
     * Kullanıcının bu isteği yapmaya yetkisi var mı?
     */
    public function authorize(): bool
    {
        return true; // Middleware ile kontrol ediliyor
    }

    /**
     * Validasyon kuralları
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Özel hata mesajları (Türkçe)
     */
    public function messages(): array
    {
        return [
            'message.max' => 'Mesaj en fazla 1000 karakter olabilir.',
        ];
    }

    /**
     * Alan isimlerini Türkçeleştir
     */
    public function attributes(): array
    {
        return [
            'message' => 'mesaj',
        ];
    }
}

