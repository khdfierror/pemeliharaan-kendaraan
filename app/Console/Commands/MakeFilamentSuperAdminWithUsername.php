<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Filament\Facades\Filament;
use BezhanSalleh\FilamentShield\Support\Utils;

class MakeFilamentSuperAdminWithUsername extends Command
{
    protected $signature = 'make:filament-superadmin-username';
    protected $description = 'Create a new Filament Super Admin user with username support';

    public function handle(): int
    {
        $username = $this->ask('Username');
        $name = $this->ask('Name');
        $email = $this->ask('Email address');
        $password = $this->secret('Password');

        // Create user
        $user = User::create([
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info("User {$user->email} created successfully!");

        // Assign super-admin role (Shield)
        if (Utils::isRolePolicyEnabled()) {
            $user->assignRole(Utils::getSuperAdminName());
            $this->info('Super Admin role assigned!');
        } else {
            $this->warn('Shield Role Policy not enabled, cannot assign Super Admin role automatically.');
        }

        $this->info('All done! You can now login with your username.');

        return static::SUCCESS;
    }
}
