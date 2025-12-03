<?php

namespace App\Http\Requests\Api\V1\Lfg;

use Illuminate\Foundation\Http\FormRequest;

/**
 * LFG İlan Oluşturma Form Request
 * Yeni takım arama ilanı oluşturmak için validasyon kuralları
 */
class StoreLfgRequest extends FormRequest
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
            'title' => 'required|string|max:255|min:5',
            'description' => 'required|string|min:10',
            'min_rank' => 'nullable|string',
            'max_rank' => 'nullable|string',
            'mode' => 'nullable|string|max:100',
            'microphone_required' => 'boolean',
            'min_age_range' => 'nullable|string|max:50',
            'max_age_range' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:255',
            'play_style_tag' => 'nullable|string|in:try-hard,chill,fun-first,competitive,casual',
            'expires_at' => 'nullable|date|after:now',
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
            'title.required' => 'İlan başlığı zorunludur.',
            'title.min' => 'İlan başlığı en az 5 karakter olmalıdır.',
            'title.max' => 'İlan başlığı en fazla 255 karakter olabilir.',
            'description.required' => 'İlan açıklaması zorunludur.',
            'description.min' => 'İlan açıklaması en az 10 karakter olmalıdır.',
            'mode.max' => 'Oyun modu en fazla 100 karakter olabilir.',
            'microphone_required.boolean' => 'Mikrofon gereksinimi geçerli bir değer olmalıdır.',
            'city.max' => 'Şehir en fazla 255 karakter olabilir.',
            'play_style_tag.in' => 'Geçersiz oyun stili seçimi.',
            'expires_at.date' => 'Geçerli bir tarih giriniz.',
            'expires_at.after' => 'Bitiş tarihi gelecekte olmalıdır.',
        ];
    }

    /**
     * Alan isimlerini Türkçeleştir
     */
    public function attributes(): array
    {
        return [
            'game_id' => 'oyun',
            'title' => 'başlık',
            'description' => 'açıklama',
            'min_rank' => 'minimum rütbe',
            'max_rank' => 'maksimum rütbe',
            'mode' => 'oyun modu',
            'microphone_required' => 'mikrofon gereksinimi',
            'city' => 'şehir',
            'play_style_tag' => 'oyun stili',
            'expires_at' => 'bitiş tarihi',
        ];
    }
}
