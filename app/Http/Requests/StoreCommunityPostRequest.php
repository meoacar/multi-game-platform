<?php

namespace App\Http\Requests;

use App\Rules\ValidGameId;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommunityPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'game_id' => ['required', new ValidGameId()],
            'type' => 'required|in:intro,general',
            'content' => 'required|string|max:1000|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'game_id.required' => 'Oyun seçimi zorunludur',
            'type.required' => 'Post tipi zorunludur',
            'type.in' => 'Geçersiz post tipi',
            'content.required' => 'İçerik alanı zorunludur',
            'content.max' => 'İçerik en fazla 1000 karakter olabilir',
            'content.min' => 'İçerik en az 10 karakter olmalıdır',
        ];
    }

    public function attributes(): array
    {
        return [
            'game_id' => 'oyun',
            'type' => 'post tipi',
            'content' => 'içerik',
        ];
    }
}
