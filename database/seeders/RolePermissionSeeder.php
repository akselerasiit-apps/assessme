<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeding 5 roles dengan permissions sesuai UAM boilerplate
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions based on boilerplate UAM
        $permissions = [
            // User Management
            'user.create',
            'user.read',
            'user.update',
            'user.delete',
            'user.reset_password',

            // Role & Permission Management
            'role.manage',
            'permission.manage',

            // Assessment Management
            'assessment.create',
            'assessment.read',
            'assessment.update',
            'assessment.delete',
            'assessment.review',
            'assessment.approve',
            'assessment.archive',
            'assessment.assign_assessor',

            // Design Factor Management
            'design_factor.manage',
            'design_factor.read',

            // GAMO Objective Management
            'gamo_objective.manage',
            'gamo_objective.read',

            // Question Management
            'question.create',
            'question.read',
            'question.update',
            'question.delete',
            'question.bulk_import',

            // Answer Management
            'answer.create',
            'answer.read',
            'answer.update',
            'answer.delete',
            'answer.edit',

            // Evidence Management
            'evidence.upload',
            'evidence.delete',

            // Report Management
            'report.generate',
            'report.export',
            'report.view',
            'report.custom',

            // Audit Log
            'audit.view',
            'audit.export',

            // System Configuration
            'system.configure',
            'system.backup',
            'system.restore',
            'encryption.manage_keys',
            'security.configure',

            // Company Management
            'company.manage',

            // Dashboard
            'dashboard.view',

            // 2FA
            '2fa.bypass',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions based on UAM matrix

        // 1. Super Admin - Full Access (Level 4)
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // 2. Assessor - Conduct Assessment (Level 2)
        $assessor = Role::firstOrCreate(['name' => 'Assessor', 'guard_name' => 'web']);
        $assessor->syncPermissions([
            'assessment.read',
            'answer.create', 'answer.read', 'answer.update',
            'evidence.upload',
            'report.view',
            'dashboard.view',
        ]);

        // 3. Viewer - View-only Access (Level 1)
        $viewer = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions([
            'assessment.read',
            'report.view',
            'dashboard.view',
            'answer.read',
        ]);

        $this->command->info('✅ Roles and Permissions seeded successfully!');
        $this->command->info('Created 3 roles: Super Admin, Assessor, Viewer');
        $this->command->info('Created ' . count($permissions) . ' permissions');
    }
}
