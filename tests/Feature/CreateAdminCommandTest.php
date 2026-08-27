<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_short_password_does_not_create_an_admin(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Ad', 'Test Yönetici')
            ->expectsQuestion('E-posta', 'short-password@example.test')
            ->expectsQuestion('Parola', 'short')
            ->expectsQuestion('Parola doğrulaması', 'short')
            ->expectsOutput('Yönetici hesabı oluşturulamadı. Girilen bilgileri kontrol edin.')
            ->assertExitCode(1);

        $this->assertDatabaseMissing('users', ['email' => 'short-password@example.test']);
    }

    public function test_valid_password_creates_a_hashed_admin_account(): void
    {
        $password = 'safe-test-password';

        $this->artisan('admin:create')
            ->expectsQuestion('Ad', 'Test Yönetici')
            ->expectsQuestion('E-posta', 'valid-password@example.test')
            ->expectsQuestion('Parola', $password)
            ->expectsQuestion('Parola doğrulaması', $password)
            ->expectsOutput('Yönetici hesabı oluşturuldu.')
            ->assertExitCode(0);

        $admin = User::query()->where('email', 'valid-password@example.test')->firstOrFail();
        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check($password, $admin->password));
    }
}
