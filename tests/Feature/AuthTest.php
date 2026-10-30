<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ravi Kumar',
            'email' => 'ravi@example.com',
            'password' => 'Secret123',
            'password_confirmation' => 'Secret123',
        ], $overrides);
    }

    public function test_customer_can_register(): void
    {
        $this->postJson('/api/v1/auth/register', $this->validPayload())
            ->assertStatus(201)
            ->assertJsonPath('data.email', 'ravi@example.com')
            ->assertJsonPath('data.role', 'customer');

        $this->assertDatabaseHas('users', ['email' => 'ravi@example.com']);
    }

    public function test_password_is_stored_hashed(): void
    {
        $this->postJson('/api/v1/auth/register', $this->validPayload());

        $this->assertNotSame('Secret123', User::first()->password);
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'ravi@example.com']);

        $this->postJson('/api/v1/auth/register', $this->validPayload())
            ->assertStatus(422)
            ->assertJsonValidationErrors('email', 'errors');
    }

    public function test_registration_rejects_weak_password(): void
    {
        $this->postJson('/api/v1/auth/register', $this->validPayload([
            'password' => 'abc',
            'password_confirmation' => 'abc',
        ]))->assertStatus(422);
    }

    public function test_client_cannot_choose_own_role(): void
    {
        $this->postJson('/api/v1/auth/register', $this->validPayload(['role' => 'admin']))
            ->assertStatus(201)
            ->assertJsonPath('data.role', 'customer');
    }

    public function test_user_can_login_and_receives_token(): void
    {
        User::factory()->create([
            'email' => 'ravi@example.com',
            'password' => 'Secret123',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'ravi@example.com',
            'password' => 'Secret123',
        ])
            ->assertOk()
            ->assertJsonStructure(['data' => ['token', 'token_type', 'expires_at', 'user']]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'ravi@example.com', 'password' => 'Secret123']);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'ravi@example.com',
            'password' => 'WrongPass1',
        ])->assertStatus(401);
    }

    public function test_suspended_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'ravi@example.com',
            'password' => 'Secret123',
            'status' => User::STATUS_SUSPENDED,
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'ravi@example.com',
            'password' => 'Secret123',
        ])->assertStatus(403);
    }

    public function test_me_returns_current_user_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }

    public function test_suspended_user_token_is_rejected(): void
    {
        $user = User::factory()->create(['status' => User::STATUS_SUSPENDED]);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/auth/me')
            ->assertStatus(403);
    }

    public function test_logout_deletes_the_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}