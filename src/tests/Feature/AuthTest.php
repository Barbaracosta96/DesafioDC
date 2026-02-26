<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesPermissionsSeeder::class);
    }

    // ─── LOGIN ──────────────────────────────────────────────────────────────

    public function test_login_page_is_accessible_to_guests(): void
    {
        $this->get(route('login'))->assertStatus(200);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);
        $user->assignRole('user');

        $this->post(route('login'), [
            'email'    => $user->email,
            'password' => 'secret123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correct-password')]);

        $response = $this->post(route('login'), [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_cannot_login_with_nonexistent_email(): void
    {
        $response = $this->post(route('login'), [
            'email'    => 'ghost@example.com',
            'password' => 'any-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // ─── REGISTER ───────────────────────────────────────────────────────────

    public function test_register_page_is_accessible_to_guests(): void
    {
        $this->get(route('register'))->assertStatus(200);
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->post(route('register'), [
            'name'                  => 'New User',
            'email'                 => 'newuser@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);

        // Newly registered user should receive the 'user' role
        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertTrue($user->hasRole('user'));
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post(route('register'), [
            'name'                  => 'Duplicate',
            'email'                 => 'taken@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_fails_when_passwords_do_not_match(): void
    {
        $response = $this->post(route('register'), [
            'name'                  => 'Test',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    // ─── LOGOUT ─────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    // ─── PASSWORD RESET ─────────────────────────────────────────────────────

    public function test_forgot_password_page_is_accessible(): void
    {
        $this->get(route('password.request'))->assertStatus(200);
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post(route('password.email'), [
            'email' => $user->email,
        ]);

        // Should not expose whether the e-mail exists (security)
        $response->assertSessionHasNoErrors();
    }

    public function test_reset_password_page_is_accessible_with_valid_token(): void
    {
        $user  = User::factory()->create();
        $token = Password::createToken($user);

        $this->get(route('password.reset', ['token' => $token]))
            ->assertStatus(200);
    }

    public function test_authenticated_users_cannot_access_login_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect();
    }

    public function test_unauthenticated_users_are_redirected_from_dashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }
}
