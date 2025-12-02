<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('guide'));
    }

    public function rules(): array
    {
        return [
            'game_id' => 'nullable|exists:games,id',
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string|min:100',
            'is_published' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.max' => 'Başlık en fazla 255 karakter olabilir',
            'content.min' => 'İçerik en az 100 karakter olmalıdır',
            'game_id.exists' => 'Geçersiz oyun seçimi',
        ];
    }
}
