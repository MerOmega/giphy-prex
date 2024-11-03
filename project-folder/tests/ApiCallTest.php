<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

abstract class ApiCallTest extends TestCase
{

    use RefreshDatabase;

    public $mockConsoleOutput = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--env' => 'testing', '--force' => true]);
        $this->artisan('passport:install', ['--no-interaction' => true]);
        $this->artisan('db:seed', ['--verbose' => true]);

        $user = User::where('email', 'testuser@example.com')->first();
        Passport::actingAs($user);
    }
}
