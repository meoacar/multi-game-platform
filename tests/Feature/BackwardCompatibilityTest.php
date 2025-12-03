<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BackwardCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Test için PUBG oyununu oluştur
        Game::factory()->create([
            'id' => 1,
            'name' => 'PUBG Mobile',
            'slug' => 'pubg',
            'status' => 'active',
        ]);
    }

    /**
     * Test: Eski URL'ler yeni URL'lere redirect edilmeli
     * 
     * @test
     */
    public function test_legacy_urls_redirect_to_new_format()
    {
        // Eski format: takimsistemi.com/turnuvalar
        // Yeni format: pubg.takimsistemi.com/turnuvalar
        
        $legacyRoutes = [
            '/ilanlar',
            '/klanlar',
            '/rehber',
            '/topluluk',
            '/turnuvalar',
            '/takimlar',
        ];
        
        foreach ($legacyRoutes as $route) {
            $response = $this->get($route);
            
            // 301 Permanent Redirect bekliyoruz
            $response->assertStatus(301);
            
            // Yeni URL'e yönlendirme yapılmalı
            $expectedUrl = 'http://pubg.' . config('app.domain', 'takimsistemi.com') . $route;
            $response->assertRedirect($expectedUrl);
        }
    }

    /**
     * Test: Ana sayfa redirect edilmemeli
     * 
     * @test
     */
    public function test_main_page_not_redirected()
    {
        $response = $this->get('/');
        
        // Ana sayfa redirect edilmemeli, 200 OK dönmeli
        $response->assertStatus(200);
    }

    /**
     * Test: Subdomain'li URL'ler redirect edilmemeli
     * 
     * @test
     */
    public function test_subdomain_urls_not_redirected()
    {
        // Subdomain'li URL'ler zaten yeni format, redirect edilmemeli
        $response = $this->get('http://pubg.' . config('app.domain', 'takimsistemi.com') . '/turnuvalar');
        
        // Redirect olmamalı (200 veya başka bir valid status)
        $this->assertNotEquals(301, $response->status());
        $this->assertNotEquals(302, $response->status());
    }

    /**
     * Test: Query string'ler korunmalı
     * 
     * @test
     */
    public function test_query_strings_preserved_in_redirect()
    {
        $response = $this->get('/turnuvalar?status=active&page=2');
        
        $response->assertStatus(301);
        
        // Query string korunmalı
        $expectedUrl = 'http://pubg.' . config('app.domain', 'takimsistemi.com') . '/turnuvalar?status=active&page=2';
        $response->assertRedirect($expectedUrl);
    }

    /**
     * Test: Nested route'lar redirect edilmeli
     * 
     * @test
     */
    public function test_nested_routes_redirected()
    {
        $response = $this->get('/turnuvalar/123');
        
        $response->assertStatus(301);
        
        $expectedUrl = 'http://pubg.' . config('app.domain', 'takimsistemi.com') . '/turnuvalar/123';
        $response->assertRedirect($expectedUrl);
    }

    /**
     * Test: Auth route'ları redirect edilmemeli
     * 
     * @test
     */
    public function test_auth_routes_not_redirected()
    {
        // Auth route'ları game-specific değil, redirect edilmemeli
        $authRoutes = [
            '/giris',
            '/kayit',
            '/sifremi-unuttum',
        ];
        
        foreach ($authRoutes as $route) {
            $response = $this->get($route);
            
            // Redirect olmamalı (200 veya başka bir valid status)
            $this->assertNotEquals(301, $response->status());
        }
    }

    /**
     * Test: API route'ları redirect edilmemeli
     * 
     * @test
     */
    public function test_api_routes_not_redirected()
    {
        $response = $this->get('/api/stats');
        
        // API route'ları redirect edilmemeli
        $this->assertNotEquals(301, $response->status());
    }

    /**
     * Test: Admin route'ları redirect edilmemeli
     * 
     * @test
     */
    public function test_admin_routes_not_redirected()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        
        // Admin route'ları redirect edilmemeli
        $this->assertNotEquals(301, $response->status());
    }

    /**
     * Test: Profil route'ları redirect edilmeli
     * 
     * @test
     */
    public function test_profile_routes_redirected()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/profilim');
        
        $response->assertStatus(301);
        
        $expectedUrl = 'http://pubg.' . config('app.domain', 'takimsistemi.com') . '/profilim';
        $response->assertRedirect($expectedUrl);
    }

    /**
     * Test: Ayarlar route'ları redirect edilmeli
     * 
     * @test
     */
    public function test_settings_routes_redirected()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/ayarlar');
        
        $response->assertStatus(301);
        
        $expectedUrl = 'http://pubg.' . config('app.domain', 'takimsistemi.com') . '/ayarlar';
        $response->assertRedirect($expectedUrl);
    }

    /**
     * Test: Mesajlaşma route'ları redirect edilmeli
     * 
     * @test
     */
    public function test_messaging_routes_redirected()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/mesajlar');
        
        $response->assertStatus(301);
        
        $expectedUrl = 'http://pubg.' . config('app.domain', 'takimsistemi.com') . '/mesajlar';
        $response->assertRedirect($expectedUrl);
    }

    /**
     * Test: Bildirim route'ları redirect edilmeli
     * 
     * @test
     */
    public function test_notification_routes_redirected()
    {
        $user = User::factory()->create();
        
        // Türkçe route
        $response = $this->actingAs($user)->get('/bildirimler');
        $response->assertStatus(301);
        
        // İngilizce alias
        $response = $this->actingAs($user)->get('/notifications');
        $response->assertStatus(301);
    }

    /**
     * Test: Default game yoksa redirect yapılmamalı
     * 
     * @test
     */
    public function test_no_redirect_when_no_default_game()
    {
        // Tüm oyunları sil
        Game::query()->delete();
        
        // Cache'i temizle
        \Illuminate\Support\Facades\Cache::forget('default_game');
        
        $response = $this->get('/turnuvalar');
        
        // Default game yoksa redirect yapılmamalı
        $this->assertNotEquals(301, $response->status());
    }

    /**
     * Test: Inactive game redirect yapmamalı
     * 
     * @test
     */
    public function test_no_redirect_when_game_inactive()
    {
        // Oyunu inactive yap
        Game::where('id', 1)->update(['status' => 'inactive']);
        
        // Cache'i temizle
        \Illuminate\Support\Facades\Cache::forget('default_game');
        
        $response = $this->get('/turnuvalar');
        
        // Inactive game varsa redirect yapılmamalı
        $this->assertNotEquals(301, $response->status());
    }

    /**
     * Test: HTTPS redirect'leri doğru çalışmalı
     * 
     * @test
     */
    public function test_https_redirects_work_correctly()
    {
        // HTTPS isteği simüle et
        $response = $this->get('https://' . config('app.domain', 'takimsistemi.com') . '/turnuvalar');
        
        $response->assertStatus(301);
        
        // HTTPS korunmalı
        $expectedUrl = 'https://pubg.' . config('app.domain', 'takimsistemi.com') . '/turnuvalar';
        $response->assertRedirect($expectedUrl);
    }
}
