<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Create sample users for each role
     */
    public function run(): void
    {
        // Check if roles exist
        if (Role::count() === 0) {
            $this->command->warn('⚠️  No roles found. Running RolePermissionSeeder first...');
            $this->call(RolePermissionSeeder::class);
        }

        // Default password for all test users
        $password = Hash::make('Password123!');

        // 1. Super Admin User
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@assessme.com'],
            [
                'name' => 'Super Administrator',
                'password' => $password,
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // 2. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@assessme.com'],
            [
                'name' => 'System Administrator',
                'password' => $password,
            ]
        );
        $admin->assignRole('Admin');

        // 3. Manager User
        $manager = User::firstOrCreate(
            ['email' => 'manager@assessme.com'],
            [
                'name' => 'Assessment Manager',
                'password' => $password,
            ]
        );
        $manager->assignRole('Manager');

        // 4. Assessor User
        $assessor = User::firstOrCreate(
            ['email' => 'assessor@assessme.com'],
            [
                'name' => 'IT Assessor',
                'password' => $password,
            ]
        );
        $assessor->assignRole('Assessor');

        // 5. Viewer User
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@assessme.com'],
            [
                'name' => 'Report Viewer',
                'password' => $password,
            ]
        );
        $viewer->assignRole('Viewer');

        $this->command->info('✅ Sample users created successfully!');
        $this->command->info('');
        $this->command->info('📧 Login credentials (all users):');
        $this->command->info('Password: Password123!');
        $this->command->info('');
        $this->command->table(
            ['Email', 'Role', 'Name'],
            [
                ['superadmin@assessme.com', 'Super Admin', 'Super Administrator'],
                ['admin@assessme.com', 'Admin', 'System Administrator'],
                ['manager@assessme.com', 'Manager', 'Assessment Manager'],
                ['assessor@assessme.com', 'Assessor', 'IT Assessor'],
                ['viewer@assessme.com', 'Viewer', 'Report Viewer'],
            ]
        );
    }
}
