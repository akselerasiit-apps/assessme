<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AsesiRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Asesi role if not exists
        $asesiRole = Role::firstOrCreate(['name' => 'Asesi']);
        
        // Get all permissions that Viewer has plus evidence upload
        $permissions = [
            'view assessments',
            'view own assessments',
            'view companies',
            'upload evidence', // New permission for Asesi
        ];
        
        foreach ($permissions as $permission) {
            $perm = Permission::firstOrCreate(['name' => $permission]);
            $asesiRole->givePermissionTo($perm);
        }
        
        $this->command->info('Asesi role created with permissions: ' . implode(', ', $permissions));
    }
}
