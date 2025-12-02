<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|integer',
            'parent_id' => 'nullable|exists:comments,id',
            'content' => 'required|string|max:500|min:2',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Yorum içeriği zorunludur',
            'content.max' => 'Yorum en fazla 500 karakter olabilir',
            'content.min' => 'Yorum en az 2 karakter olmalıdır',
            'parent_id.exists' => 'Geçersiz üst yorum',
        ];
    }
}
