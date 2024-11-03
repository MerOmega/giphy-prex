<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command as CommandAlias;

class SetUpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations, seeders, and passport install';

    /**
     * Execute the console command that set ups the entire environment
     */
    public function handle()
    {
        $this->info('Starting application setup...');

        $this->info('Generating Key');
        Artisan::call('key:generate');

        $this->info('Running migrations...');
        Artisan::call('migrate');
        $this->info(Artisan::output());

        $this->info('Seeding the database...');
        Artisan::call('db:seed', ['--force' => true]);
        $this->info(Artisan::output());

        $this->info('Installing Passport...');
        Artisan::call('passport:install', ['--no-interaction' => true]);
        $this->info(Artisan::output());

        $this->info('Application setup completed successfully!');
        return CommandAlias::SUCCESS;
    }
}
