<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ------------------ Registration ------------------
    public function test_registration_validates_required_fields(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'different',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password',
            ]);
    }

    // ---registration not valid for dups emails
    public function test_registration_rejects_duplicate_emails(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => $user->email,
            'password' => 'P4ssword123.1',
            'password_confirmation' => 'P4ssword123.1',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    // ---registration valid
    public function test_user_can_register_successfully(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Joe Doe',
            'email' => 'joedoe@greencycle.com',
            'password' => 'P4ssword123.1',
            'password_confirmation' => 'P4ssword123.1',
            'coins' => 999,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('user.name', 'Joe Doe')
            ->assertJsonPath('user.coins', 0)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'coins'],
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'joedoe@greencycle.com',
            'coins' => 0,
        ]);

        $user = User::where('email', 'joedoe@greencycle.com')->firstOrFail();

        $this->assertTrue(
            Hash::check('P4ssword123.1', $user->password)
        );

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    // ------------------ Login ------------------

    // --rejects the login for the password
    public function test_login_rejects_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('P4ssword123.1'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'Password123.1',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    // --successful login
    public function test_user_can_login_successfully(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Login successful.')
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'coins'],
                'token',
            ]);

        $this->assertNotEmpty($response->json('token'));

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'name' => 'auth_token',
        ]);
    }

    // ------------------ Logout ------------------
    public function test_user_can_logout_successfully(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Logout successful.');

        $this->assertDatabaseCount('personal_access_tokens', 0);

        // Reset the authenticated user between requests.
        auth()->guard('sanctum')->forgetUser();

        // Verify that the revoked token is rejected.
        $this->withToken($token)
            ->postJson('/api/auth/logout')
            ->assertUnauthorized();
    }
}
