<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateFakeUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-fake-user {name?} {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a fake user';

    /**
     * Creates a fake user with the provided name, email, and password or defaults
     */
    public function handle(): void
    {
        $name = $this->argument('name') ?? 'John Doe';
        $email = $this->argument('email') ?? 'testuser@example.com';
        $password = $this->argument('password') ?? 'password123';

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info("User created with ID: {$user->id}, email: {$email}, password: {$password}");
    }
}
