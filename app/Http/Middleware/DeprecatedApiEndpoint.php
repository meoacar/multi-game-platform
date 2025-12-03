<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Deprecated API Endpoint Middleware
 * 
 * Bu middleware, eski API endpoint'lerinin kullanımını izler ve
 * deprecation warning'leri ekler.
 */
class DeprecatedApiEndpoint
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $newEndpoint  Yeni endpoint URL'i
     * @param  string|null  $deprecationDate  Deprecation tarihi (Y-m-d formatında)
     * @param  string|null  $sunsetDate  Endpoint'in tamamen kaldırılacağı tarih
     */
    public function handle(Request $request, Closure $next, ?string $newEndpoint = null, ?string $deprecationDate = null, ?string $sunsetDate = null): Response
    {
        // Deprecation bilgilerini logla
        Log::channel('daily')->warning('Deprecated API endpoint used', [
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'user_id' => auth('sanctum')->id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'new_endpoint' => $newEndpoint,
            'deprecation_date' => $deprecationDate,
            'sunset_date' => $sunsetDate,
        ]);

        // Request'i işle
        $response = $next($request);

        // Deprecation header'larını ekle
        $response->headers->set('X-API-Deprecated', 'true');
        
        if ($deprecationDate) {
            $response->headers->set('X-API-Deprecation-Date', $deprecationDate);
        }
        
        if ($sunsetDate) {
            $response->headers->set('X-API-Sunset-Date', $sunsetDate);
        }
        
        if ($newEndpoint) {
            $response->headers->set('X-API-New-Endpoint', $newEndpoint);
        }

        // Deprecation mesajını response body'ye ekle (JSON response için)
        if ($response->headers->get('Content-Type') === 'application/json') {
            $content = json_decode($response->getContent(), true);
            
            if (is_array($content)) {
                $content['_deprecated'] = [
                    'message' => 'Bu endpoint kullanımdan kaldırılmıştır.',
                    'deprecation_date' => $deprecationDate,
                    'sunset_date' => $sunsetDate,
                    'new_endpoint' => $newEndpoint,
                    'migration_guide' => url('/docs/api/migration-guide'),
                ];
                
                $response->setContent(json_encode($content));
            }
        }

        return $response;
    }
}
