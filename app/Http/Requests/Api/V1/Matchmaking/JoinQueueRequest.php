<?php

namespace App\Http\Requests\Api\V1\Matchmaking;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Matchmaking kuyruğuna katılma isteği
 * 
 * Requirements: 2.1, 2.2, 2.3, 2.4, 2.5
 */
class JoinQueueRequest extends FormRequest
{
    /**
     * Kullanıcının bu isteği yapma yetkisi var mı?
     */
    public function authorize(): bool
    {
        return true; // Auth middleware tarafından kontrol ediliyor
    }

    /**
     * Validation kuralları
     */
    public function rules(): array
    {
        return [
            // Requirement 2.1: Oyun modu seçimi
            'mode' => 'required|in:squad,duo,solo',
            
            // Requirement 2.2: Rank aralığı
            'min_rank' => 'nullable|string|in:Bronze,Silver,Gold,Platinum,Diamond,Crown,Ace,Conqueror',
            'max_rank' => 'nullable|string|in:Bronze,Silver,Gold,Platinum,Diamond,Crown,Ace,Conqueror',
            
            // Requirement 2.3: Şehir filtresi
            'city' => 'nullable|string|max:100',
            
            // Requirement 2.4: Mikrofon zorunluluğu
            'microphone_required' => 'nullable|boolean',
            
            // Requirement 2.5: Oyun stili
            'play_style' => 'nullable|in:aggressive,balanced,defensive',
            
            // Oyun ID'si (zorunlu)
            'game_id' => ['required', new \App\Rules\ValidGameId()],
        ];
    }

    /**
     * Özel hata mesajları (Türkçe)
     */
    public function messages(): array
    {
        return [
            // Mode mesajları
            'mode.required' => 'Oyun modu seçimi zorunludur',
            'mode.in' => 'Geçersiz oyun modu. Squad, Duo veya Solo seçebilirsiniz',
            
            // Rank mesajları
            'min_rank.in' => 'Geçersiz minimum rank değeri',
            'max_rank.in' => 'Geçersiz maksimum rank değeri',
            
            // City mesajları
            'city.max' => 'Şehir adı en fazla 100 karakter olabilir',
            
            // Microphone mesajları
            'microphone_required.boolean' => 'Mikrofon zorunluluğu true veya false olmalıdır',
            
            // Play style mesajları
            'play_style.in' => 'Geçersiz oyun stili. Aggressive, Balanced veya Defensive seçebilirsiniz',
            
            // Game ID mesajları
            'game_id.required' => 'Oyun seçimi zorunludur',
            'game_id.exists' => 'Seçilen oyun bulunamadı',
        ];
    }

    /**
     * Validation'dan sonra ek kontroller
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Rank aralığı kontrolü: min_rank, max_rank'tan büyük olamaz
            if ($this->filled(['min_rank', 'max_rank'])) {
                $rankOrder = [
                    'Bronze' => 1,
                    'Silver' => 2,
                    'Gold' => 3,
                    'Platinum' => 4,
                    'Diamond' => 5,
                    'Crown' => 6,
                    'Ace' => 7,
                    'Conqueror' => 8,
                ];

                $minRankValue = $rankOrder[$this->min_rank] ?? 0;
                $maxRankValue = $rankOrder[$this->max_rank] ?? 0;

                if ($minRankValue > $maxRankValue) {
                    $validator->errors()->add(
                        'min_rank',
                        'Minimum rank, maksimum rank\'tan büyük olamaz'
                    );
                }
            }
        });
    }

    /**
     * Validated data'yı hazırla
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Boolean değerleri düzelt
        if (isset($validated['microphone_required'])) {
            $validated['microphone_required'] = filter_var(
                $validated['microphone_required'],
                FILTER_VALIDATE_BOOLEAN
            );
        }

        return $validated;
    }
}
