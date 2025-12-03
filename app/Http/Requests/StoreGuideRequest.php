<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'game_id' => ['nullable', new \App\Rules\ValidGameId()],
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:100',
            'is_published' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Başlık alanı zorunludur',
            'title.max' => 'Başlık en fazla 255 karakter olabilir',
            'content.required' => 'İçerik alanı zorunludur',
            'content.min' => 'İçerik en az 100 karakter olmalıdır',
            'game_id.exists' => 'Geçersiz oyun seçimi',
        ];
    }
}
