<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Properties
            'properties.view', 'properties.create', 'properties.update',
            'properties.delete', 'properties.approve', 'properties.feature',
            // Users
            'users.view', 'users.create', 'users.update', 'users.delete',
            'users.suspend', 'users.ban',
            // Appointments
            'appointments.view', 'appointments.create',
            'appointments.confirm', 'appointments.cancel',
            // Reviews
            'reviews.view', 'reviews.create', 'reviews.delete', 'reviews.approve',
            // Reports
            'reports.view', 'reports.manage',
            // Admin
            'admin.access', 'admin.analytics', 'admin.settings',
            // Blogs
            'blogs.view', 'blogs.create', 'blogs.update', 'blogs.delete',
            // Verifications
            'verifications.view', 'verifications.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'sanctum']);
        }

        // Admin — all permissions
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $admin->syncPermissions(Permission::where('guard_name', 'sanctum')->get());

        // Owner
        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'sanctum']);
        $owner->syncPermissions([
            'properties.view', 'properties.create', 'properties.update', 'properties.delete',
            'appointments.view', 'appointments.confirm', 'appointments.cancel',
            'reviews.view', 'reviews.create',
            'blogs.view',
        ]);

        // Buyer
        $buyer = Role::firstOrCreate(['name' => 'buyer', 'guard_name' => 'sanctum']);
        $buyer->syncPermissions([
            'properties.view',
            'appointments.view', 'appointments.create', 'appointments.cancel',
            'reviews.view', 'reviews.create',
            'blogs.view',
        ]);

        // Agent
        $agent = Role::firstOrCreate(['name' => 'agent', 'guard_name' => 'sanctum']);
        $agent->syncPermissions([
            'properties.view', 'properties.create', 'properties.update',
            'appointments.view', 'appointments.create', 'appointments.confirm', 'appointments.cancel',
            'reviews.view', 'reviews.create',
        ]);

        $this->command->info('Roles and permissions seeded.');
    }
}
