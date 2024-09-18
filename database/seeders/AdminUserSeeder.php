<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if the admin user already exists
        $existingAdmin = User::where('email', 'admin@example.com')->first();

        if ($existingAdmin) {
            $this->command->info('An admin user already exists.');
            return;
        }

        // Generate a random password
        $randomPassword = Str::random(12);

        // Create the admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make($randomPassword), // Store the hashed password
            'role' => User::ROLES['admin'],
        ]);
        
        // Output the password in the console
        $this->command->info('Admin user created.');
        $this->command->info('Email: ' . $admin->email);
        $this->command->info('Password: ' . $randomPassword);
    }
}
