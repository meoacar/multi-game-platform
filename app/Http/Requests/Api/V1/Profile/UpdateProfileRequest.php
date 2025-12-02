<?php

namespace App\Http\Requests\Api\V1\Profile;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Profil Güncelleme Form Request
 * Kullanıcı profil bilgilerini güncellemek için validasyon kuralları
 */
class UpdateProfileRequest extends FormRequest
{
    /**
     * Kullanıcının bu isteği yapmaya yetkisi var mı?
     * Sadece kendi profilini güncelleyebilir
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
            'nickname' => 'nullable|string|max:255|min:3',
            'pubg_id' => 'nullable|string|max:255',
            'rank' => 'nullable|string|in:Bronze,Silver,Gold,Platinum,Diamond,Crown,Ace,Conqueror',
            'server_region' => 'nullable|string|in:EU,MENA,ASIA,NA,SA',
            'city' => 'nullable|string|max:255',
            'age_range' => 'nullable|string|max:50',
            'gender' => 'nullable|string|in:male,female,other',
            'play_style' => 'nullable|string|in:try-hard,chill,fun-first,competitive,casual',
            'bio' => 'nullable|string|max:1000',
            'twitch_username' => 'nullable|string|max:255',
            'youtube_channel' => 'nullable|string|max:255',
            'discord_username' => 'nullable|string|max:255',
        ];
    }

    /**
     * Özel hata mesajları (Türkçe)
     */
    public function messages(): array
    {
        return [
            'nickname.min' => 'Takma ad en az 3 karakter olmalıdır.',
            'nickname.max' => 'Takma ad en fazla 255 karakter olabilir.',
            'pubg_id.max' => 'PUBG ID en fazla 255 karakter olabilir.',
            'rank.in' => 'Geçersiz rütbe seçimi.',
            'server_region.in' => 'Geçersiz sunucu bölgesi seçimi.',
            'city.max' => 'Şehir en fazla 255 karakter olabilir.',
            'age_range.max' => 'Yaş aralığı en fazla 50 karakter olabilir.',
            'gender.in' => 'Geçersiz cinsiyet seçimi.',
            'play_style.in' => 'Geçersiz oyun stili seçimi.',
            'bio.max' => 'Biyografi en fazla 1000 karakter olabilir.',
            'twitch_username.max' => 'Twitch kullanıcı adı en fazla 255 karakter olabilir.',
            'youtube_channel.max' => 'YouTube kanal adı en fazla 255 karakter olabilir.',
            'discord_username.max' => 'Discord kullanıcı adı en fazla 255 karakter olabilir.',
        ];
    }

    /**
     * Alan isimlerini Türkçeleştir
     */
    public function attributes(): array
    {
        return [
            'nickname' => 'takma ad',
            'pubg_id' => 'PUBG ID',
            'rank' => 'rütbe',
            'server_region' => 'sunucu bölgesi',
            'city' => 'şehir',
            'age_range' => 'yaş aralığı',
            'gender' => 'cinsiyet',
            'play_style' => 'oyun stili',
            'bio' => 'biyografi',
            'twitch_username' => 'Twitch kullanıcı adı',
            'youtube_channel' => 'YouTube kanalı',
            'discord_username' => 'Discord kullanıcı adı',
        ];
    }
}
