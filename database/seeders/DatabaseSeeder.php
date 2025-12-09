<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * 
     * Seeding order:
     * 1. CompanySeeder - Create sample company (required by users)
     * 2. DesignFactorSeeder - Create 10 COBIT design factors
     * 3. GamoObjectiveSeeder - Create 23 GAMO objectives
     * 4. RolePermissionSeeder - Create roles & permissions (required by users)
     * 5. UserSeeder - Create sample users with roles
     * 6. GamoQuestionSeeder - Create sample assessment questions
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        $this->command->info('');

        // Call individual seeders in correct order
        $this->call([
            CompanySeeder::class,           // 1. Company first (users need company_id)
            DesignFactorSeeder::class,      // 2. Design Factors
            GamoObjectiveSeeder::class,     // 3. GAMO Objectives
            RolePermissionSeeder::class,    // 4. Roles & Permissions (users need roles)
            UserSeeder::class,              // 5. Sample Users
            GamoQuestionSeeder::class,      // 6. Sample Questions
        ]);

        $this->command->info('');
        $this->command->info('✅ Database seeding completed successfully!');
    }
}
