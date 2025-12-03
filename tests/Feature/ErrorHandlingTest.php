<?php

namespace Tests\Feature;

use App\Exceptions\DatabaseException;
use App\Exceptions\GameContextException;
use App\Exceptions\GameNotFoundException;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Error Handling Test
 * 
 * Multi-game platform error handling özelliklerini test eder
 * 
 * Test Coverage:
 * - GameNotFoundException handling
 * - GameContextException handling
 * - DatabaseException handling ve sanitization
 * - Role-based error messages
 * - Error page rendering
 */
class ErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: GameNotFoundException oluşturma ve mesajları
     */
    public function test_game_not_found_exception_by_slug(): void
    {
        $exception = GameNotFoundException::bySlug('invalid-game');
        
        $this->assertStringContainsString('invalid-game', $exception->getMessage());
        $this->assertStringContainsString('bulunamadı', $exception->getMessage());
    }

    public function test_game_not_found_exception_by_id(): void
    {
        $exception = GameNotFoundException::byId(999);
        
        $this->assertStringContainsString('999', $exception->getMessage());
        $this->assertStringContainsString('bulunamadı', $exception->getMessage());
    }

    public function test_game_not_found_exception_no_active_games(): void
    {
        $exception = GameNotFoundException::noActiveGames();
        
        $this->assertStringContainsString('aktif oyun', $exception->getMessage());
    }

    /**
     * Test: GameContextException oluşturma ve mesajları
     */
    public function test_game_context_exception_missing(): void
    {
        $exception = GameContextException::missing();
        
        $this->assertStringContainsString('bağlamı bulunamadı', $exception->getMessage());
    }

    public function test_game_context_exception_invalid(): void
    {
        $exception = GameContextException::invalid();
        
        $this->assertStringContainsString('Geçersiz', $exception->getMessage());
    }

    public function test_game_context_exception_cross_game_access(): void
    {
        $exception = GameContextException::crossGameAccess('Tournament', 1, 2);
        
        $this->assertStringContainsString('Tournament', $exception->getMessage());
        $this->assertStringContainsString('1', $exception->getMessage());
        $this->assertStringContainsString('2', $exception->getMessage());
    }

    /**
     * Test: DatabaseException sanitization - Normal kullanıcı
     */
    public function test_database_exception_sanitizes_message_for_regular_users(): void
    {
        // Normal kullanıcı olarak giriş yap
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);
        
        $exception = new DatabaseException('SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row');
        
        // Kullanıcıya gösterilen mesaj sanitize edilmiş olmalı
        $this->assertStringNotContainsString('SQLSTATE', $exception->getUserMessage());
        $this->assertStringNotContainsString('constraint', $exception->getUserMessage());
        $this->assertStringContainsString('veritabanı hatası', $exception->getUserMessage());
    }

    /**
     * Test: DatabaseException - Admin kullanıcı için detaylı mesaj
     */
    public function test_database_exception_shows_details_for_admin_users(): void
    {
        // Admin kullanıcı olarak giriş yap
        $user = User::factory()->create(['is_admin' => true]);
        $this->actingAs($user);
        
        $originalMessage = 'SQLSTATE[23000]: Integrity constraint violation';
        $exception = new DatabaseException($originalMessage);
        
        // Admin için orijinal mesaj gösterilmeli
        $this->assertStringContainsString($originalMessage, $exception->getUserMessage());
    }

    /**
     * Test: DatabaseException factory metodları
     */
    public function test_database_exception_foreign_key_constraint(): void
    {
        // Admin kullanıcı olarak test et
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        
        $exception = DatabaseException::foreignKeyConstraint('tournaments');
        
        $this->assertStringContainsString('tournaments', $exception->getOriginalMessage());
        $this->assertStringContainsString('ilişkili', $exception->getMessage());
    }

    public function test_database_exception_unique_constraint(): void
    {
        // Admin kullanıcı olarak test et
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        
        $exception = DatabaseException::uniqueConstraint('email');
        
        $this->assertStringContainsString('email', $exception->getOriginalMessage());
        $this->assertStringContainsString('zaten kullanılıyor', $exception->getMessage());
    }

    public function test_database_exception_connection_failed(): void
    {
        $exception = DatabaseException::connectionFailed();
        
        // Connection failed mesajı her zaman aynı (sanitize edilmemiş)
        $this->assertStringContainsString('veritabanı', strtolower($exception->getMessage()));
    }

    /**
     * Test: GameNotFoundException fırlatma
     */
    public function test_game_not_found_exception_can_be_thrown(): void
    {
        $this->expectException(GameNotFoundException::class);
        $this->expectExceptionMessage('bulunamadı');
        
        throw GameNotFoundException::bySlug('invalid-game');
    }

    /**
     * Test: GameContextException fırlatma
     */
    public function test_game_context_exception_can_be_thrown(): void
    {
        $this->expectException(GameContextException::class);
        $this->expectExceptionMessage('bağlamı bulunamadı');
        
        throw GameContextException::missing();
    }

    /**
     * Test: Role-based error messages
     */
    public function test_error_messages_differ_by_user_role(): void
    {
        $originalMessage = 'SELECT * FROM users WHERE id = 1';
        
        // Normal kullanıcı
        $regularUser = User::factory()->create(['is_admin' => false]);
        $this->actingAs($regularUser);
        
        $regularException = new DatabaseException($originalMessage);
        $regularMessage = $regularException->getUserMessage();
        
        // Admin kullanıcı
        $adminUser = User::factory()->create(['is_admin' => true]);
        $this->actingAs($adminUser);
        
        $adminException = new DatabaseException($originalMessage);
        $adminMessage = $adminException->getUserMessage();
        
        // Mesajlar farklı olmalı
        $this->assertNotEquals($regularMessage, $adminMessage);
        
        // Admin mesajı orijinal mesajı içermeli
        $this->assertStringContainsString($originalMessage, $adminMessage);
        
        // Normal kullanıcı mesajı orijinal mesajı içermemeli
        $this->assertStringNotContainsString($originalMessage, $regularMessage);
    }

    /**
     * Test: Database error sanitization - SQL injection attempt
     */
    public function test_database_error_sanitizes_sql_injection_attempts(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);
        
        $maliciousQuery = "SELECT * FROM users WHERE email = 'admin@test.com' OR '1'='1'";
        $exception = new DatabaseException($maliciousQuery);
        
        // Kullanıcıya SQL sorgusu gösterilmemeli
        $this->assertStringNotContainsString('SELECT', $exception->getUserMessage());
        $this->assertStringNotContainsString('FROM', $exception->getUserMessage());
        $this->assertStringNotContainsString('WHERE', $exception->getUserMessage());
    }

    /**
     * Test: Original message preservation for logging
     */
    public function test_database_exception_preserves_original_message_for_logging(): void
    {
        $originalMessage = 'SQLSTATE[HY000]: General error: 1364 Field \'name\' doesn\'t have a default value';
        $exception = new DatabaseException($originalMessage);
        
        // Orijinal mesaj korunmalı (loglama için)
        $this->assertEquals($originalMessage, $exception->getOriginalMessage());
        
        // Sanitize edilmiş mesaj farklı olmalı
        $this->assertNotEquals($originalMessage, $exception->getSanitizedMessage());
    }
}
