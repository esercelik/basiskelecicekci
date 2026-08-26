<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

#[Signature('admin:create')]
#[Description('İlk yönetici hesabını güvenle oluşturur')]
class CreateAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = (string) $this->ask('Ad');
        $email = (string) $this->ask('E-posta');
        $password = (string) $this->secret('Parola');
        $passwordConfirmation = (string) $this->secret('Parola doğrulaması');

        $validator = Validator::make(compact('name', 'email', 'password', 'passwordConfirmation'), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'same:passwordConfirmation'],
        ]);

        if ($validator->fails()) {
            $this->error('Yönetici hesabı oluşturulamadı. Girilen bilgileri kontrol edin.');

            return self::FAILURE;
        }

        User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);

        $this->info('Yönetici hesabı oluşturuldu.');

        return self::SUCCESS;
    }
}
