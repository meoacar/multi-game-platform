<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;

class ApiKeyAuth
{
    /**
     * API key doğrulama middleware'i
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        
        // API key'i al (header veya query parameter'dan)
        $apiKeyValue = $request->header('X-API-Key') ?? $request->query('api_key');

        if (!$apiKeyValue) {
            return response()->json([
                'error' => 'API key gerekli',
                'message' => 'X-API-Key header\'ı veya api_key query parameter\'ı göndermelisiniz'
            ], 401);
        }

        // API key'i doğrula
        $apiKey = ApiKey::validate($apiKeyValue);

        if (!$apiKey) {
            return response()->json([
                'error' => 'Geçersiz API key',
                'message' => 'API key bulunamadı veya süresi dolmuş'
            ], 401);
        }

        // IP whitelist kontrolü
        if (!$apiKey->isIpAllowed($request->ip())) {
            return response()->json([
                'error' => 'IP adresi izin listesinde değil',
                'message' => 'Bu IP adresinden API\'ye erişim izniniz yok'
            ], 403);
        }

        // Endpoint izin kontrolü
        $endpoint = $request->path();
        if (!$apiKey->hasPermission($endpoint)) {
            return response()->json([
                'error' => 'Bu endpoint\'e erişim izniniz yok',
                'message' => 'API key\'iniz bu endpoint için yetkilendirilmemiş'
            ], 403);
        }

        // Rate limit kontrolü
        if (!$apiKey->checkRateLimit()) {
            return response()->json([
                'error' => 'Rate limit aşıldı',
                'message' => "Dakika başına maksimum {$apiKey->rate_limit} istek yapabilirsiniz",
                'retry_after' => 60
            ], 429);
        }

        // API key'i request'e ekle (controller'da kullanmak için)
        $request->attributes->set('api_key', $apiKey);

        // İsteği işle
        $response = $next($request);

        // Response time hesapla
        $responseTime = (int) ((microtime(true) - $startTime) * 1000);

        // Log kaydet (asenkron)
        dispatch(function () use ($apiKey, $request, $response, $responseTime) {
            ApiLog::logRequest([
                'api_key_id' => $apiKey->id,
                'method' => $request->method(),
                'endpoint' => $request->path(),
                'status_code' => $response->status(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'request_body' => $request->getContent(),
                'response_body' => $response->getContent(),
                'response_time' => $responseTime,
            ]);
        })->afterResponse();

        return $response;
    }
}
