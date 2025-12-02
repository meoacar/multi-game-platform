<?php

namespace App\Http\Requests\Api\V1\Clan;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Klan Güncelleme Form Request
 * Mevcut klanı güncellemek için validasyon kuralları
 */
class UpdateClanRequest extends FormRequest
{
    /**
     * Kullanıcının bu isteği yapmaya yetkisi var mı?
     */
    public function authorize(): bool
    {
        return true; // Controller'da kontrol ediliyor
    }

    /**
     * Validasyon kuralları
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255|min:3',
            'description' => 'sometimes|string|min:10',
            'requirements' => 'nullable|string',
            'min_rank' => 'nullable|string',
            'max_rank' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'max_members' => 'nullable|integer|min:5|max:100',
            'discord_invite' => 'nullable|url',
        ];
    }

    /**
     * Özel hata mesajları (Türkçe)
     */
    public function messages(): array
    {
        return [
            'name.min' => 'Klan adı en az 3 karakter olmalıdır.',
            'name.max' => 'Klan adı en fazla 255 karakter olabilir.',
            'description.min' => 'Klan açıklaması en az 10 karakter olmalıdır.',
            'city.max' => 'Şehir en fazla 255 karakter olabilir.',
            'max_members.integer' => 'Maksimum üye sayısı sayı olmalıdır.',
            'max_members.min' => 'Maksimum üye sayısı en az 5 olmalıdır.',
            'max_members.max' => 'Maksimum üye sayısı en fazla 100 olabilir.',
            'discord_invite.url' => 'Geçerli bir Discord davet linki giriniz.',
        ];
    }

    /**
     * Alan isimlerini Türkçeleştir
     */
    public function attributes(): array
    {
        return [
            'name' => 'klan adı',
            'description' => 'açıklama',
            'requirements' => 'gereksinimler',
            'min_rank' => 'minimum rütbe',
            'max_rank' => 'maksimum rütbe',
            'city' => 'şehir',
            'max_members' => 'maksimum üye sayısı',
            'discord_invite' => 'Discord davet linki',
        ];
    }
}
