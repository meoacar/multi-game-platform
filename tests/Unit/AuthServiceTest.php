<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    /** @test */
    public function register_creates_user_and_profile()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $result = $this->authService->register($data);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertDatabaseHas('profiles', ['user_id' => $result['user']->id]);
    }

    /** @test */
    public function login_returns_user_and_token_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $result = $this->authService->login([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertEquals($user->id, $result['user']->id);
    }

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $result = $this->authService->login([
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertNull($result);
    }

    /** @test */
    public function login_fails_for_banned_user()
    {
        User::factory()->create([
            'email' => 'banned@example.com',
            'password' => Hash::make('password123'),
            'status' => 'banned',
        ]);

        $result = $this->authService->login([
            'email' => 'banned@example.com',
            'password' => 'password123',
        ]);

        $this->assertNull($result);
    }

    /** @test */
    public function logout_deletes_user_tokens()
    {
        $user = User::factory()->create();
        $user->createToken('test-token');

        $this->assertCount(1, $user->tokens);

        $this->authService->logout($user);

        $this->assertCount(0, $user->fresh()->tokens);
    }
}
