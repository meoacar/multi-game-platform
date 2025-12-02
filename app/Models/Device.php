<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Device Model
 * 
 * Kullanıcı cihaz ve hassasiyet ayarları
 * 
 * İlişkiler:
 * - belongsTo: User
 */
class Device extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'device_name',
        'graphics_settings',
        'fps_setting',
        'gyro_enabled',
        'sensitivity_settings',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gyro_enabled' => 'boolean',
        'sensitivity_settings' => 'array', // JSON olarak saklanır
    ];

    /**
     * Cihazın sahibi kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hassasiyet ayarını al
     * 
     * @param string $type (general, ads, gyro, etc.)
     * @return int|null
     */
    public function getSensitivity(string $type): ?int
    {
        return $this->sensitivity_settings[$type] ?? null;
    }

    /**
     * Hassasiyet ayarını güncelle
     * 
     * @param string $type
     * @param int $value
     */
    public function setSensitivity(string $type, int $value): void
    {
        $settings = $this->sensitivity_settings ?? [];
        $settings[$type] = $value;
        $this->update(['sensitivity_settings' => $settings]);
    }

    /**
     * Tüm hassasiyet ayarlarını al (formatlanmış)
     */
    public function getFormattedSensitivityAttribute(): array
    {
        $settings = $this->sensitivity_settings ?? [];
        $formatted = [];

        $labels = [
            'general' => 'Genel',
            'ads' => 'Nişan',
            'scope_2x' => '2x Dürbün',
            'scope_4x' => '4x Dürbün',
            'scope_8x' => '8x Dürbün',
            'gyro' => 'Gyro',
        ];

        foreach ($settings as $key => $value) {
            $formatted[] = [
                'type' => $key,
                'label' => $labels[$key] ?? ucfirst($key),
                'value' => $value,
            ];
        }

        return $formatted;
    }
}
