<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_regular_user_is_forbidden_from_admin_panel(): void
    {
        $this->actingAs(User::factory()->create())->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_can_login_and_logout(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'yonetici@example.test', 'password' => 'correct-password']);

        $this->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'correct-password'])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_invalid_login_attempts_are_rate_limited(): void
    {
        RateLimiter::clear('yanlis@example.test|127.0.0.1');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from(route('admin.login'))->post(route('admin.login.store'), ['email' => 'yanlis@example.test', 'password' => 'wrong-password'])->assertRedirect(route('admin.login'));
        }

        $this->from(route('admin.login'))->post(route('admin.login.store'), ['email' => 'yanlis@example.test', 'password' => 'wrong-password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_public_registration_route_is_not_present(): void
    {
        $this->assertFalse(Route::has('register'));
    }
}
