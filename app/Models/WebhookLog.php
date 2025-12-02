<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    public $timestamps = false; // Sadece created_at var

    protected $fillable = [
        'webhook_id',
        'event',
        'payload',
        'status_code',
        'response',
        'response_time',
        'success',
        'error_message',
        'attempt',
        'created_at',
    ];

    protected $casts = [
        'success' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * İlişki: Webhook
     */
    public function webhook()
    {
        return $this->belongsTo(Webhook::class);
    }
}
