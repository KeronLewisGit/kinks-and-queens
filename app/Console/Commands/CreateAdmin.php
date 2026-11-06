<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {--name=} {--email=} {--password=}';

    protected $description = 'Create (or reset) an owner login for the admin';

    public function handle(): int
    {
        $email = $this->option('email') ?: $this->ask('Email');
        $password = $this->option('password') ?: $this->secret('Password (min 10 characters)');
        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen((string) $password) < 10) {
            $this->error('A valid email and a password of at least 10 characters are required.');

            return self::FAILURE;
        }
        User::updateOrCreate(['email' => $email], [
            'name' => $this->option('name') ?: 'Owner',
            'password' => $password,
            'role' => 'owner',
        ]);
        $this->info("Owner login ready for {$email}.");

        return self::SUCCESS;
    }
}
