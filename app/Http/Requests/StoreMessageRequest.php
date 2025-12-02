<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receiver_id' => 'required|exists:users,id|different:' . auth()->id(),
            'content' => 'required|string|max:1000|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'receiver_id.required' => 'Alıcı seçilmelidir',
            'receiver_id.exists' => 'Geçersiz kullanıcı',
            'receiver_id.different' => 'Kendinize mesaj gönderemezsiniz',
            'content.required' => 'Mesaj içeriği zorunludur',
            'content.max' => 'Mesaj en fazla 1000 karakter olabilir',
        ];
    }
}
