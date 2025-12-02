<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    public $timestamps = false; // Sadece created_at var

    protected $fillable = [
        'api_key_id',
        'method',
        'endpoint',
        'status_code',
        'ip_address',
        'user_agent',
        'request_body',
        'response_body',
        'response_time',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * İlişki: API key
     */
    public function apiKey()
    {
        return $this->belongsTo(ApiKey::class);
    }

    /**
     * Log kaydı oluştur
     */
    public static function logRequest(array $data): void
    {
        self::create([
            'api_key_id' => $data['api_key_id'] ?? null,
            'method' => $data['method'],
            'endpoint' => $data['endpoint'],
            'status_code' => $data['status_code'],
            'ip_address' => $data['ip_address'],
            'user_agent' => $data['user_agent'] ?? null,
            'request_body' => $data['request_body'] ?? null,
            'response_body' => $data['response_body'] ?? null,
            'response_time' => $data['response_time'] ?? null,
            'created_at' => now(),
        ]);
    }
}
