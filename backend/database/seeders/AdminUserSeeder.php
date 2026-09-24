<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@betlink.et'],
            [
                'name'              => 'BetLink Admin',
                'phone'             => '+251911000000',
                'password'          => Hash::make('Admin@123456'),
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');
        UserProfile::firstOrCreate(['user_id' => $admin->id], ['is_verified' => true, 'verified_at' => now()]);

        // Demo Owner
        $owner = User::firstOrCreate(
            ['email' => 'owner@betlink.et'],
            [
                'name'              => 'Demo Owner',
                'phone'             => '+251911000001',
                'password'          => Hash::make('Owner@123456'),
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );
        $owner->assignRole('owner');
        UserProfile::firstOrCreate(['user_id' => $owner->id]);

        // Demo Buyer
        $buyer = User::firstOrCreate(
            ['email' => 'buyer@betlink.et'],
            [
                'name'              => 'Demo Buyer',
                'phone'             => '+251911000002',
                'password'          => Hash::make('Buyer@123456'),
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );
        $buyer->assignRole('buyer');
        UserProfile::firstOrCreate(['user_id' => $buyer->id]);

        // Demo Agent
        $agent = User::firstOrCreate(
            ['email' => 'agent@betlink.et'],
            [
                'name'              => 'Demo Agent',
                'phone'             => '+251911000003',
                'password'          => Hash::make('Agent@123456'),
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );
        $agent->assignRole('agent');
        UserProfile::firstOrCreate(['user_id' => $agent->id, 'is_verified' => true, 'verified_at' => now()]);

        $this->command->info('Admin, Owner, Buyer, Agent demo users seeded.');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@betlink.et', 'Admin@123456'],
                ['Owner', 'owner@betlink.et', 'Owner@123456'],
                ['Buyer', 'buyer@betlink.et', 'Buyer@123456'],
                ['Agent', 'agent@betlink.et', 'Agent@123456'],
            ]
        );
    }
}
