<?php

namespace App\Http\Requests\Api\V1\Device;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Cihaz Oluşturma/Güncelleme Form Request
 * Kullanıcı cihaz ve hassasiyet ayarları için validasyon kuralları
 */
class StoreUpdateDeviceRequest extends FormRequest
{
    /**
     * Kullanıcının bu isteği yapmaya yetkisi var mı?
     * Sadece kendi cihaz bilgisini oluşturabilir/güncelleyebilir
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
            'device_name' => 'required|string|max:255',
            'graphics_settings' => 'nullable|string|max:255',
            'fps_setting' => 'nullable|string|max:50',
            'gyro_enabled' => 'boolean',
            'sensitivity_settings' => 'nullable|array',
            'sensitivity_settings.general' => 'nullable|integer|min:0|max:500',
            'sensitivity_settings.ads' => 'nullable|integer|min:0|max:500',
            'sensitivity_settings.scope_2x' => 'nullable|integer|min:0|max:500',
            'sensitivity_settings.scope_4x' => 'nullable|integer|min:0|max:500',
            'sensitivity_settings.scope_6x' => 'nullable|integer|min:0|max:500',
            'sensitivity_settings.scope_8x' => 'nullable|integer|min:0|max:500',
            'sensitivity_settings.gyro' => 'nullable|integer|min:0|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Özel hata mesajları (Türkçe)
     */
    public function messages(): array
    {
        return [
            'device_name.required' => 'Cihaz adı zorunludur.',
            'device_name.max' => 'Cihaz adı en fazla 255 karakter olabilir.',
            'graphics_settings.max' => 'Grafik ayarları en fazla 255 karakter olabilir.',
            'fps_setting.max' => 'FPS ayarı en fazla 50 karakter olabilir.',
            'gyro_enabled.boolean' => 'Gyro durumu geçerli bir değer olmalıdır.',
            'sensitivity_settings.array' => 'Hassasiyet ayarları geçerli bir format olmalıdır.',
            'sensitivity_settings.*.integer' => 'Hassasiyet değeri sayı olmalıdır.',
            'sensitivity_settings.*.min' => 'Hassasiyet değeri en az 0 olmalıdır.',
            'sensitivity_settings.*.max' => 'Hassasiyet değeri en fazla 500 olabilir.',
            'notes.max' => 'Notlar en fazla 1000 karakter olabilir.',
        ];
    }

    /**
     * Alan isimlerini Türkçeleştir
     */
    public function attributes(): array
    {
        return [
            'device_name' => 'cihaz adı',
            'graphics_settings' => 'grafik ayarları',
            'fps_setting' => 'FPS ayarı',
            'gyro_enabled' => 'gyro durumu',
            'sensitivity_settings' => 'hassasiyet ayarları',
            'notes' => 'notlar',
        ];
    }
}
