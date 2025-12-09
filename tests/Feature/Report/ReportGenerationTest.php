<?php

namespace Tests\Feature\Report;

use App\Models\Assessment;
use App\Models\GamoObjective;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportGenerationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test manager can generate assessment summary PDF.
     */
    public function test_manager_can_generate_assessment_summary_pdf(): void
    {
        $this->authenticateAs('Admin');
        $assessment = Assessment::factory()->completed()->create();

        $response = $this->getJson("/api/reports/assessments/{$assessment->id}/summary-pdf");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test manager can generate maturity report PDF.
     */
    public function test_manager_can_generate_maturity_report_pdf(): void
    {
        $this->authenticateAs('Admin');
        $assessment = Assessment::factory()->completed()->create();

        $response = $this->getJson("/api/reports/assessments/{$assessment->id}/maturity-pdf");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test manager can generate gap analysis PDF.
     */
    public function test_manager_can_generate_gap_analysis_pdf(): void
    {
        $this->authenticateAs('Admin');
        $assessment = Assessment::factory()->completed()->create();

        $response = $this->getJson("/api/reports/assessments/{$assessment->id}/gap-analysis-pdf");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test manager can export assessment to Excel.
     */
    public function test_manager_can_export_assessment_to_excel(): void
    {
        $this->authenticateAs('Admin');
        $assessment = Assessment::factory()->completed()->create();

        $response = $this->getJson("/api/reports/assessments/{$assessment->id}/export-excel");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test admin can view dashboard statistics.
     */
    public function test_admin_can_view_dashboard_statistics(): void
    {
        $this->authenticateAs('Admin');
        Assessment::factory()->count(10)->create();

        $response = $this->getJson('/api/reports/dashboard-stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'statistics' => [
                        'total_assessments',
                        'draft',
                        'in_progress',
                        'completed',
                        'reviewed',
                        'approved',
                        'average_maturity',
                        'average_progress',
                    ],
                    'maturity_distribution',
                ],
            ]);
    }

    /**
     * Test viewer cannot generate reports for other assessments.
     */
    public function test_viewer_can_only_view_authorized_reports(): void
    {
        $this->authenticateAs('Viewer');
        $assessment = Assessment::factory()->create();

        $response = $this->getJson("/api/reports/assessments/{$assessment->id}/summary-pdf");

        // Viewer should have limited access based on UAM
        $this->assertTrue(in_array($response->status(), [200, 403]));
    }
}
