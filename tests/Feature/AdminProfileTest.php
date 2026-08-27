<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_profile_and_password_validation_errors_use_separate_error_bags(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'current-password']);

        $this->actingAs($admin)
            ->from(route('admin.profile.edit'))
            ->put(route('admin.profile.update'), [
                'name' => 'Yeni ad',
                'email' => $admin->email,
                'current_password' => 'wrong-password',
            ])
            ->assertSessionHasErrorsIn('profile', 'current_password');

        $this->actingAs($admin)
            ->from(route('admin.profile.edit'))
            ->put(route('admin.profile.password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertSessionHasErrorsIn('password', ['current_password', 'password']);
    }

    public function test_admin_can_update_profile_and_password_with_current_password(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'current-password']);

        $this->actingAs($admin)
            ->put(route('admin.profile.update'), [
                'name' => 'Güncel yönetici',
                'email' => 'updated@example.test',
                'current_password' => 'current-password',
            ])
            ->assertRedirect(route('admin.profile.edit'));

        $this->actingAs($admin)
            ->put(route('admin.profile.password.update'), [
                'current_password' => 'current-password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertRedirect(route('admin.profile.edit'));

        $this->assertTrue(Hash::check('new-secure-password', $admin->fresh()->password));
    }
}
