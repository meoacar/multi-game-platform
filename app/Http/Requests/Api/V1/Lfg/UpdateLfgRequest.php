<?php

namespace App\Http\Requests\Api\V1\Lfg;

use Illuminate\Foundation\Http\FormRequest;

/**
 * LFG İlan Güncelleme Form Request
 * Mevcut takım arama ilanını güncellemek için validasyon kuralları
 */
class UpdateLfgRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255|min:5',
            'description' => 'sometimes|string|min:10',
            'min_rank' => 'nullable|string',
            'max_rank' => 'nullable|string',
            'mode' => 'nullable|string|max:100',
            'microphone_required' => 'boolean',
            'city' => 'nullable|string|max:255',
            'play_style_tag' => 'nullable|string|in:try-hard,chill,fun-first,competitive,casual',
            'status' => 'sometimes|in:open,closed',
        ];
    }

    /**
     * Özel hata mesajları (Türkçe)
     */
    public function messages(): array
    {
        return [
            'title.min' => 'İlan başlığı en az 5 karakter olmalıdır.',
            'title.max' => 'İlan başlığı en fazla 255 karakter olabilir.',
            'description.min' => 'İlan açıklaması en az 10 karakter olmalıdır.',
            'mode.max' => 'Oyun modu en fazla 100 karakter olabilir.',
            'microphone_required.boolean' => 'Mikrofon gereksinimi geçerli bir değer olmalıdır.',
            'city.max' => 'Şehir en fazla 255 karakter olabilir.',
            'play_style_tag.in' => 'Geçersiz oyun stili seçimi.',
            'status.in' => 'Geçersiz durum seçimi.',
        ];
    }

    /**
     * Alan isimlerini Türkçeleştir
     */
    public function attributes(): array
    {
        return [
            'title' => 'başlık',
            'description' => 'açıklama',
            'min_rank' => 'minimum rütbe',
            'max_rank' => 'maksimum rütbe',
            'mode' => 'oyun modu',
            'microphone_required' => 'mikrofon gereksinimi',
            'city' => 'şehir',
            'play_style_tag' => 'oyun stili',
            'status' => 'durum',
        ];
    }
}
