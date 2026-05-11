<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $adminRole = Role::findOrCreate('admin');
        $userRole = Role::findOrCreate('user');

        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPassword = env('ADMIN_PASSWORD', 'password');

        $admin = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => env('ADMIN_NAME', 'System Admin'),
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
            ]
        );

        if (! $admin->hasRole($adminRole)) {
            $admin->assignRole($adminRole);
        }

        $userEmail = env('USER_EMAIL', 'user@example.com');
        $userPassword = env('USER_PASSWORD', 'password');

        $user = User::firstOrCreate(
            ['email' => $userEmail],
            [
                'name' => env('USER_NAME', 'Sample User'),
                'password' => Hash::make($userPassword),
                'email_verified_at' => now(),
            ]
        );

        if (! $user->hasRole($userRole)) {
            $user->assignRole($userRole);
        }
    }
}
