<?php

namespace Tests\Feature\Assessment;

use App\Models\Assessment;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin can create assessment.
     */
    public function test_admin_can_create_assessment(): void
    {
        $this->authenticateAs('Admin');
        $company = Company::factory()->create();
        $user = User::factory()->create();

        $response = $this->postJson('/api/assessments', [
            'code' => 'TEST-001',
            'title' => 'New Assessment',
            'description' => 'Assessment description',
            'company_id' => $company->id,
            'assessment_type' => 'initial',
            'scope_type' => 'tailored',
            'assessment_period_start' => now()->addDays(1)->format('Y-m-d'),
            'assessment_period_end' => now()->addDays(30)->format('Y-m-d'),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'code',
                    'title',
                    'company_id',
                    'status',
                ]
            ]);

        $this->assertDatabaseHas('assessments', [
            'title' => 'New Assessment',
            'company_id' => $company->id,
        ]);
    }

    /**
     * Test viewer cannot create assessment.
     */
    public function test_viewer_cannot_create_assessment(): void
    {
        $this->authenticateAs('Viewer');
        $company = Company::factory()->create();

        $response = $this->postJson('/api/assessments', [
            'code' => 'TEST-002',
            'title' => 'New Assessment',
            'company_id' => $company->id,
            'assessment_type' => 'initial',
            'assessment_period_start' => now()->addDays(1)->format('Y-m-d'),
            'assessment_period_end' => now()->addDays(30)->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test admin can view all assessments.
     */
    public function test_admin_can_view_all_assessments(): void
    {
        $this->authenticateAs('Admin');
        Assessment::factory()->count(5)->create();

        $response = $this->getJson('/api/assessments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'code', 'title', 'status']
                ]
            ]);
    }

    /**
     * Test admin can update assessment.
     */
    public function test_admin_can_update_assessment(): void
    {
        $this->authenticateAs('Admin');
        $assessment = Assessment::factory()->create([
            'title' => 'Original Title'
        ]);

        $response = $this->putJson("/api/assessments/{$assessment->id}", [
            'code' => $assessment->code,
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'company_id' => $assessment->company_id,
            'assessment_type' => $assessment->assessment_type,
            'assessment_period_start' => $assessment->assessment_period_start->format('Y-m-d'),
            'assessment_period_end' => $assessment->assessment_period_end->format('Y-m-d'),
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('assessments', [
            'id' => $assessment->id,
            'title' => 'Updated Title',
        ]);
    }

    /**
     * Test admin can delete assessment.
     */
    public function test_admin_can_delete_assessment(): void
    {
        $this->authenticateAs('Super Admin');
        $assessment = Assessment::factory()->create();

        $response = $this->deleteJson("/api/assessments/{$assessment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('assessments', [
            'id' => $assessment->id,
        ]);
    }

    /**
     * Test validation errors for missing fields.
     */
    public function test_validation_errors_for_missing_fields(): void
    {
        $this->authenticateAs('Admin');

        $response = $this->postJson('/api/assessments', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'company_id', 'assessment_period_start', 'assessment_period_end']);
    }

    /**
     * Test assessment status can be updated.
     */
    public function test_assessment_status_can_be_updated(): void
    {
        $this->authenticateAs('Admin');
        $assessment = Assessment::factory()->draft()->create();

        $response = $this->patchJson("/api/assessments/{$assessment->id}/status", [
            'status' => 'in_progress'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('assessments', [
            'id' => $assessment->id,
            'status' => 'in_progress',
        ]);
    }
}
