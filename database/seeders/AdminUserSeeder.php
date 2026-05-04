<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@ezytools.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'subscription_type' => 'pro',
                'subscription_expires_at' => now()->addYears(10),
                'email_verified_at' => now(),
            ]
        );

        // Add unlimited pro subscription
        \App\Models\Subscription::updateOrCreate(
            ['user_id' => $admin->id, 'status' => 'active'],
            [
                'plan' => 'lifetime',
                'amount' => 0,
                'currency' => 'BDT',
                'transaction_id' => 'TRX-ADMIN-LIFETIME',
                'payment_method' => 'system',
                'starts_at' => now(),
                'expires_at' => now()->addYears(10),
            ]
        );
    }
}
