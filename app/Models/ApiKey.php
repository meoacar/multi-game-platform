<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'key',
        'prefix',
        'permissions',
        'rate_limit',
        'ip_whitelist',
        'last_used_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'key', // Gerçek key'i asla gösterme
    ];

    /**
     * İlişki: API key'in sahibi
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * İlişki: API logları
     */
    public function logs()
    {
        return $this->hasMany(ApiLog::class);
    }

    /**
     * Yeni API key oluştur
     */
    public static function generate(array $data): self
    {
        // Rastgele key oluştur
        $rawKey = 'pk_' . Str::random(32);
        
        // Key'i hash'le (veritabanında saklamak için)
        $hashedKey = hash('sha256', $rawKey);
        
        // Prefix oluştur (kullanıcıya gösterilecek)
        $prefix = substr($rawKey, 0, 8);
        
        $apiKey = self::create([
            'user_id' => $data['user_id'] ?? null,
            'name' => $data['name'],
            'key' => $hashedKey,
            'prefix' => $prefix,
            'permissions' => $data['permissions'] ?? [],
            'rate_limit' => $data['rate_limit'] ?? 60,
            'ip_whitelist' => $data['ip_whitelist'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'is_active' => true,
        ]);
        
        // Ham key'i sadece bir kez döndür (bir daha gösterilmeyecek)
        $apiKey->raw_key = $rawKey;
        
        return $apiKey;
    }

    /**
     * API key'i doğrula
     */
    public static function validate(string $key): ?self
    {
        $hashedKey = hash('sha256', $key);
        
        $apiKey = self::where('key', $hashedKey)
            ->where('is_active', true)
            ->first();
        
        if (!$apiKey) {
            return null;
        }
        
        // Süresi dolmuş mu kontrol et
        if ($apiKey->expires_at && $apiKey->expires_at->isPast()) {
            return null;
        }
        
        // Son kullanım tarihini güncelle
        $apiKey->update(['last_used_at' => now()]);
        
        return $apiKey;
    }

    /**
     * IP whitelist kontrolü
     */
    public function isIpAllowed(string $ip): bool
    {
        if (!$this->ip_whitelist) {
            return true; // Whitelist yoksa tüm IP'lere izin ver
        }
        
        $allowedIps = explode(',', $this->ip_whitelist);
        $allowedIps = array_map('trim', $allowedIps);
        
        return in_array($ip, $allowedIps);
    }

    /**
     * Endpoint'e erişim izni var mı?
     */
    public function hasPermission(string $endpoint): bool
    {
        if (!$this->permissions || empty($this->permissions)) {
            return true; // İzin listesi yoksa tüm endpoint'lere izin ver
        }
        
        foreach ($this->permissions as $pattern) {
            // Wildcard desteği (* ile)
            $pattern = str_replace('*', '.*', $pattern);
            if (preg_match("#^{$pattern}$#", $endpoint)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Rate limit kontrolü
     */
    public function checkRateLimit(): bool
    {
        $key = "api_rate_limit:{$this->id}";
        $requests = cache()->get($key, 0);
        
        if ($requests >= $this->rate_limit) {
            return false; // Limit aşıldı
        }
        
        // İstek sayısını artır
        cache()->put($key, $requests + 1, now()->addMinute());
        
        return true;
    }

    /**
     * Maskelenmiş key göster (güvenlik için)
     */
    public function getMaskedKeyAttribute(): string
    {
        return $this->prefix . '****' . substr($this->key, -4);
    }
}
