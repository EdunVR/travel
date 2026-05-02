<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupSuperAdmin extends Command
{
    protected $signature = 'setup:superadmin';
    protected $description = 'Setup Super Admin account with full access';

    public function handle()
    {
        $this->info('🚀 Setting up Super Admin account...');
        $this->newLine();

        // Run migrations
        $this->info('📦 Running migrations...');
        Artisan::call('migrate', ['--force' => true]);
        $this->line(Artisan::output());

        // Run role & permission seeder
        $this->info('🔐 Creating roles & permissions...');
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        $this->line(Artisan::output());

        // Run super admin seeder
        $this->info('👤 Creating Super Admin user...');
        Artisan::call('db:seed', ['--class' => 'SuperAdminSeeder', '--force' => true]);
        $this->line(Artisan::output());

        // Clear cache
        $this->info('🧹 Clearing cache...');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $this->line('✅ Cache cleared!');

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('🎉 Super Admin Setup Complete!');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
        
        $this->table(
            ['Field', 'Value'],
            [
                ['URL', 'http://localhost/login'],
                ['Email', 'superadmin@morra.com'],
                ['Password', 'SuperAdmin@123'],
            ]
        );

        $this->newLine();
        $this->info('✨ You can now login with the credentials above!');
        $this->newLine();

        return Command::SUCCESS;
    }
}
