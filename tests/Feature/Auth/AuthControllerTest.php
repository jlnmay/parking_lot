<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\Role;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function validUser(array $overrides = []): User
    {
        $roleId = $overrides['role_id']
        ?? Role::where('name', 'Attendant')->first()?->id
        ?? Role::factory()->attendant()->create()->id;

        unset($overrides['role_id']);

        return User::factory()->create(array_merge([
            'password' => Hash::make('correct-password'),
            'role_id' => $roleId,
        ], $overrides));
    }

    public function test_valid_login_succeeds_and_regenerates_session(): void
    {
        $user = $this->validUser(['email' => 'jordan@example.com']);

        $oldSessionId = session()->getId();

        $response = $this->postJson('/login', [
            'email' => 'jordan@example.com',
            'password' => 'correct-password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['user' => ['id', 'name', 'email', 'role']])
            ->assertJsonPath('user.email', 'jordan@example.com');

        $this->assertAuthenticatedAs($user);
        $this->assertNotEquals($oldSessionId, session()->getId());
    }

    public function test_invalid_login_returns_exact_error_message(): void
    {
        $this->validUser(['email' => 'jordan@example.com']);

        $response = $this->postJson('/login', [
            'email' => 'jordan@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.email.0', 'El usuario o la contraseña son incorrectos.');

        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_five_attempts(): void
    {
        $this->validUser(['email' => 'jordan@example.com']);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/login', [
                'email' => 'jordan@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->postJson('/login', [
            'email' => 'jordan@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
    }

    public function test_logout_invalidates_session(): void
    {
        $user = $this->validUser();
        $this->actingAs($user);

        $this->assertAuthenticated();

        $response = $this->postJson('/logout');
        $response->assertNoContent();

        $this->assertGuest();

        $this->getJson('/me')->assertStatus(401);
    }

    public function test_me_returns_401_when_unauthenticated(): void
    {
        $this->getJson('/me')->assertStatus(401);
    }

    public function test_me_returns_user_payload_when_authenticated(): void
    {
        $user = $this->validUser(['email' => 'jordan@example.com']);
        $this->actingAs($user);

        $response = $this->getJson('/me');

        $response->assertOk()
            ->assertJsonPath('user.email', 'jordan@example.com');
    }
}