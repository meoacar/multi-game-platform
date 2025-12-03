<?php

namespace Tests\Unit;

use Tests\TestCase;

class ConfigTest extends TestCase
{
    /**
     * Session domain konfigürasyonunun doğru ayarlandığını test eder
     *
     * @test
     */
    public function session_domain_is_configured_for_wildcard_subdomains()
    {
        // SESSION_DOMAIN .env'de ayarlanmış olmalı
        $sessionDomain = config('session.domain');
        
        // Wildcard domain formatında olmalı (.takimsistemi.test)
        $this->assertNotNull($sessionDomain);
        $this->assertStringStartsWith('.', $sessionDomain);
        
        // APP_DOMAIN ile uyumlu olmalı
        $appDomain = env('APP_DOMAIN', 'takimsistemi.test');
        $this->assertEquals('.' . $appDomain, $sessionDomain);
    }
    
    /**
     * Session path'in root olarak ayarlandığını test eder
     *
     * @test
     */
    public function session_path_is_set_to_root()
    {
        $sessionPath = config('session.path');
        
        $this->assertEquals('/', $sessionPath);
    }
    
    /**
     * Session same_site ayarının lax olduğunu test eder
     *
     * @test
     */
    public function session_same_site_is_lax()
    {
        $sameSite = config('session.same_site');
        
        $this->assertEquals('lax', $sameSite);
    }
    
    /**
     * Sanctum stateful domains'in wildcard subdomain içerdiğini test eder
     *
     * @test
     */
    public function sanctum_stateful_domains_includes_wildcard_subdomain()
    {
        $statefulDomains = config('sanctum.stateful');
        
        $this->assertIsArray($statefulDomains);
        
        // Ana domain ve wildcard subdomain içermeli
        $appDomain = env('APP_DOMAIN', 'takimsistemi.test');
        $wildcardDomain = '*.' . $appDomain;
        
        $this->assertContains($appDomain, $statefulDomains);
        $this->assertContains($wildcardDomain, $statefulDomains);
    }
    
    /**
     * Sanctum stateful domains'in localhost içerdiğini test eder
     *
     * @test
     */
    public function sanctum_stateful_domains_includes_localhost()
    {
        $statefulDomains = config('sanctum.stateful');
        
        $this->assertContains('localhost', $statefulDomains);
        $this->assertContains('127.0.0.1', $statefulDomains);
    }
    
    /**
     * Session driver'ın doğru ayarlandığını test eder
     * Test ortamında 'array', production'da 'database' olmalı
     *
     * @test
     */
    public function session_driver_is_configured()
    {
        $driver = config('session.driver');
        
        // Test ortamında array, production'da database
        $this->assertContains($driver, ['database', 'array', 'redis']);
    }
}
