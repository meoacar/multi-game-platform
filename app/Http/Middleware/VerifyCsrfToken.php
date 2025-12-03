<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken as Middleware;

/**
 * Cross-Subdomain CSRF Token Doğrulama Middleware
 * 
 * Bu middleware, çoklu oyun platformunda subdomain'ler arası
 * CSRF token doğrulamasını yönetir.
 * 
 * Özellikler:
 * - Tüm subdomain'lerde aynı CSRF token'ı kullanır
 * - Session domain konfigürasyonu ile uyumlu çalışır
 * - API endpoint'lerini CSRF kontrolünden muaf tutar
 * - Webhook endpoint'lerini muaf tutar
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * CSRF doğrulamasından muaf tutulacak URI'ler
     *
     * @var array<int, string>
     */
    protected $except = [
        // API endpoint'leri (Sanctum token ile korunur)
        'api/*',
        
        // Webhook endpoint'leri
        'webhooks/*',
        
        // FCM push notification callback'leri
        'fcm/callback',
        
        // Ödeme gateway callback'leri (gelecekte eklenebilir)
        // 'payment/callback/*',
    ];

    /**
     * CSRF token'ın geçerli olup olmadığını kontrol et
     * 
     * Cross-subdomain senaryolarında token doğrulaması için
     * özel mantık eklenebilir.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        // Parent class'ın token kontrolünü kullan
        // Session domain konfigürasyonu sayesinde
        // tüm subdomain'lerde aynı token geçerli olacak
        return parent::tokensMatch($request);
    }

    /**
     * İsteğin CSRF korumasından muaf olup olmadığını belirle
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        // Parent class'ın except kontrolünü kullan
        return parent::inExceptArray($request);
    }

    /**
     * CSRF token mismatch durumunda özel hata mesajı
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpKernel\Exception\HttpException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function buildException($request)
    {
        return parent::buildException($request);
    }
}
