<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentGamoSelection;
use App\Models\Company;
use App\Models\GamoObjective;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestAssessmentSeeder extends Seeder
{
    /**
     * Create test assessment for interface testing
     */
    public function run(): void
    {
        $this->command->info('Creating test assessment...');
        
        // Get or create demo company
        $company = Company::first() ?? Company::create([
            'name' => 'Demo Company',
            'code' => 'DEMO001',
            'industry' => 'Technology',
            'is_active' => true,
        ]);
        
        // Get first user
        $user = User::first();
        
        if (!$user) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }
        
        // Create assessment
        $assessment = Assessment::create([
            'company_id' => $company->id,
            'code' => 'ASM-TEST-' . date('YmdHis'),
            'title' => 'COBIT 2019 Test Assessment',
            'description' => 'Testing assessment with COBIT 2019 activities',
            'assessment_period_start' => now(),
            'assessment_period_end' => now()->addDays(30),
            'status' => 'in_progress',
            'created_by' => $user->id,
            'progress_percentage' => 0,
        ]);
        
        // Select 5 GAMO objectives (1 from each domain)
        $gamoCodes = ['EDM01', 'APO01', 'BAI01', 'DSS01', 'MEA01'];
        $gamos = GamoObjective::whereIn('code', $gamoCodes)->get();
        
        foreach ($gamos as $gamo) {
            AssessmentGamoSelection::create([
                'assessment_id' => $assessment->id,
                'gamo_objective_id' => $gamo->id,
                'is_selected' => true,
            ]);
        }
        
        $this->command->info('');
        $this->command->info('✓ Test Assessment Created Successfully!');
        $this->command->info('  ════════════════════════════════════════');
        $this->command->info("  ID: {$assessment->id}");
        $this->command->info("  Code: {$assessment->code}");
        $this->command->info("  Title: {$assessment->title}");
        $this->command->info("  Company: {$company->name}");
        $this->command->info("  Selected GAMOs: " . $gamos->pluck('code')->implode(', '));
        $this->command->info("  Total Activities: " . ($gamos->count() * 3) . " (5 GAMOs × 3 activities each)");
        $this->command->info('  ════════════════════════════════════════');
        $this->command->info('  📱 Test URLs:');
        $this->command->info("     Answer (New): http://127.0.0.1:8001/assessments/{$assessment->id}/answer-new");
        $this->command->info("     Answer (Take): http://127.0.0.1:8001/assessments/{$assessment->id}/take");
        $this->command->info("     Detail: http://127.0.0.1:8001/assessments/{$assessment->id}");
        $this->command->info('');
    }
}
