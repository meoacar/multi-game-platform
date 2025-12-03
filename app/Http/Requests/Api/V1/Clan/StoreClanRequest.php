<?php

namespace App\Http\Requests\Api\V1\Clan;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Klan Oluşturma Form Request
 * Yeni klan oluşturmak için validasyon kuralları
 */
class StoreClanRequest extends FormRequest
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
            'game_id' => ['required', new \App\Rules\ValidGameId()],
            'name' => 'required|string|max:255|min:3',
            'description' => 'required|string|min:10',
            'requirements' => 'nullable|string',
            'min_rank' => 'nullable|string',
            'max_rank' => 'nullable|string',
            'min_age_range' => 'nullable|string|max:50',
            'max_age_range' => 'nullable|string|max:50',
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
            'game_id.required' => 'Oyun seçimi zorunludur.',
            'game_id.exists' => 'Seçilen oyun geçerli değil.',
            'name.required' => 'Klan adı zorunludur.',
            'name.min' => 'Klan adı en az 3 karakter olmalıdır.',
            'name.max' => 'Klan adı en fazla 255 karakter olabilir.',
            'description.required' => 'Klan açıklaması zorunludur.',
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
            'game_id' => 'oyun',
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
