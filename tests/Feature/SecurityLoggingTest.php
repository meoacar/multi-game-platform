<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use App\Models\Tournament;
use App\Services\SecurityLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * Security Logging Test
 * 
 * Güvenlik loglarının doğru şekilde kaydedildiğini test eder.
 * 
 * Requirements: 16.5, 18.3
 */
class SecurityLoggingTest extends TestCase
{
    use RefreshDatabase;

    protected SecurityLogService $securityLog;

    protected function setUp(): void
    {
        parent::setUp();
        $this->securityLog = app(SecurityLogService::class);
    }

    /** @test */
    public function it_logs_cross_game_access_attempts()
    {
        // Arrange
        $game1 = Game::factory()->create(['slug' => 'pubg']);
        $game2 = Game::factory()->create(['slug' => 'cod']);
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create(['game_id' => $game2->id]);

        // Session'da game1 var
        session(['game_id' => $game1->id]);

        // Log'u yakala
        Log::shouldReceive('warning')
            ->once()
            ->with('Security: Cross-game access attempt', \Mockery::on(function ($data) use ($tournament, $game1, $game2) {
                return $data['event_type'] === 'cross_game_access_attempt'
                    && $data['resource_type'] === get_class($tournament)
                    && $data['resource_game_id'] === $game2->id
                    && $data['current_game_id'] === $game1->id;
            }));

        // Act
        $this->securityLog->logCrossGameAccessAttempt($tournament, $game1->id, $user->id);

        // Assert - Mockery otomatik olarak kontrol eder
    }

    /** @test */
    public function it_logs_invalid_subdomain_access()
    {
        // Arrange
        $invalidSubdomain = 'invalid-game';

        // Log'u yakala
        Log::shouldReceive('warning')
            ->once()
            ->with('Security: Invalid subdomain access', \Mockery::on(function ($data) use ($invalidSubdomain) {
                return $data['event_type'] === 'invalid_subdomain_access'
                    && $data['subdomain'] === $invalidSubdomain;
            }));

        // Act
        $this->securityLog->logInvalidSubdomainAccess($invalidSubdomain);

        // Assert - Mockery otomatik olarak kontrol eder
    }

    /** @test */
    public function it_logs_inactive_game_access()
    {
        // Arrange
        $game = Game::factory()->create([
            'slug' => 'inactive-game',
            'status' => 'inactive',
        ]);

        // Log'u yakala
        Log::shouldReceive('info')
            ->once()
            ->with('Security: Inactive game access attempt', \Mockery::on(function ($data) use ($game) {
                return $data['event_type'] === 'inactive_game_access'
                    && $data['game_id'] === $game->id
                    && $data['game_slug'] === $game->slug;
            }));

        // Act
        $this->securityLog->logInactiveGameAccess($game->id, $game->slug, $game->name);

        // Assert - Mockery otomatik olarak kontrol eder
    }

    /** @test */
    public function it_logs_missing_game_context()
    {
        // Arrange
        $action = 'create_tournament';

        // Log'u yakala
        Log::shouldReceive('error')
            ->once()
            ->with('Game Error: Missing game context', \Mockery::on(function ($data) use ($action) {
                return $data['event_type'] === 'missing_game_context'
                    && $data['action'] === $action;
            }));

        // Act
        $this->securityLog->logMissingGameContext($action);

        // Assert - Mockery otomatik olarak kontrol eder
    }

    /** @test */
    public function it_logs_game_not_found_errors()
    {
        // Arrange
        $slug = 'nonexistent-game';

        // Log'u yakala
        Log::shouldReceive('error')
            ->once()
            ->with('Game Error: Game not found', \Mockery::on(function ($data) use ($slug) {
                return $data['event_type'] === 'game_not_found'
                    && $data['identifier'] === $slug
                    && $data['identifier_type'] === 'slug';
            }));

        // Act
        $this->securityLog->logGameNotFound($slug, 'slug');

        // Assert - Mockery otomatik olarak kontrol eder
    }

    /** @test */
    public function it_logs_game_database_errors()
    {
        // Arrange
        $exception = new \Exception('Database connection failed');
        $operation = 'find_game_by_slug';

        // Log'u yakala
        Log::shouldReceive('error')
            ->once()
            ->with('Game Error: Database error', \Mockery::on(function ($data) use ($operation) {
                return $data['event_type'] === 'game_database_error'
                    && $data['operation'] === $operation
                    && $data['error_message'] === 'Database connection failed';
            }));

        // Act
        $this->securityLog->logGameDatabaseError($exception, $operation);

        // Assert - Mockery otomatik olarak kontrol eder
    }

    /** @test */
    public function it_logs_game_authorization_errors()
    {
        // Arrange
        $resourceType = 'Tournament';
        $resourceId = 123;
        $action = 'update';

        // Log'u yakala
        Log::shouldReceive('warning')
            ->once()
            ->with('Game Error: Authorization failed', \Mockery::on(function ($data) use ($resourceType, $resourceId, $action) {
                return $data['event_type'] === 'game_authorization_error'
                    && $data['resource_type'] === $resourceType
                    && $data['resource_id'] === $resourceId
                    && $data['action'] === $action;
            }));

        // Act
        $this->securityLog->logGameAuthorizationError($resourceType, $resourceId, $action);

        // Assert - Mockery otomatik olarak kontrol eder
    }

    /** @test */
    public function it_logs_security_events_with_different_severities()
    {
        // Test warning severity
        Log::shouldReceive('warning')
            ->once()
            ->with('Test warning event', \Mockery::on(function ($data) {
                return $data['event_type'] === 'test_event';
            }));

        $this->securityLog->logSecurityEvent('test_event', 'Test warning event', 'warning');

        // Test error severity
        Log::shouldReceive('error')
            ->once()
            ->with('Test error event', \Mockery::on(function ($data) {
                return $data['event_type'] === 'test_error';
            }));

        $this->securityLog->logSecurityEvent('test_error', 'Test error event', 'error');

        // Test info severity
        Log::shouldReceive('info')
            ->once()
            ->with('Test info event', \Mockery::on(function ($data) {
                return $data['event_type'] === 'test_info';
            }));

        $this->securityLog->logSecurityEvent('test_info', 'Test info event', 'info');
    }

    /** @test */
    public function it_logs_brute_force_attempts()
    {
        // Arrange
        $targetType = 'login';
        $attemptCount = 5;

        // Log'u yakala
        Log::shouldReceive('warning')
            ->once()
            ->with('Security: Potential brute force attempt', \Mockery::on(function ($data) use ($targetType, $attemptCount) {
                return $data['event_type'] === 'brute_force_attempt'
                    && $data['target_type'] === $targetType
                    && $data['attempt_count'] === $attemptCount;
            }));

        // Act
        $this->securityLog->logBruteForceAttempt($targetType, $attemptCount);

        // Assert - Mockery otomatik olarak kontrol eder
    }

    /** @test */
    public function it_logs_suspicious_ip_addresses()
    {
        // Arrange
        $reason = 'Multiple failed login attempts';

        // Log'u yakala
        Log::shouldReceive('warning')
            ->once()
            ->with('Security: Suspicious IP detected', \Mockery::on(function ($data) use ($reason) {
                return $data['event_type'] === 'suspicious_ip'
                    && $data['reason'] === $reason;
            }));

        // Act
        $this->securityLog->logSuspiciousIp($reason);

        // Assert - Mockery otomatik olarak kontrol eder
    }

    /** @test */
    public function it_includes_user_context_in_logs_when_authenticated()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Log'u yakala
        Log::shouldReceive('warning')
            ->once()
            ->with('Security: Invalid subdomain access', \Mockery::on(function ($data) use ($user) {
                return $data['user_id'] === $user->id
                    && $data['user_email'] === $user->email;
            }));

        // Act
        $this->securityLog->logInvalidSubdomainAccess('test');

        // Assert - Mockery otomatik olarak kontrol eder
    }

    /** @test */
    public function it_includes_request_context_in_logs()
    {
        // Arrange
        $this->get('/test-url');

        // Log'u yakala
        Log::shouldReceive('warning')
            ->once()
            ->with('Security: Invalid subdomain access', \Mockery::on(function ($data) {
                return isset($data['ip_address'])
                    && isset($data['user_agent'])
                    && isset($data['url']);
            }));

        // Act
        $this->securityLog->logInvalidSubdomainAccess('test');

        // Assert - Mockery otomatik olarak kontrol eder
    }
}
