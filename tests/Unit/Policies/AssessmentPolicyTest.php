<?php

namespace Tests\Unit\Policies;

use App\Models\Assessment;
use App\Models\User;
use App\Policies\AssessmentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentPolicyTest extends TestCase
{
    use RefreshDatabase;

    private AssessmentPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new AssessmentPolicy();
    }

    /**
     * Test Super Admin can view any assessment.
     */
    public function test_super_admin_can_view_any_assessment(): void
    {
        $user = $this->createUserWithRole('Super Admin');

        $this->assertTrue($this->policy->viewAny($user));
    }

    /**
     * Test Viewer can view assessments.
     */
    public function test_viewer_can_view_assessments(): void
    {
        $user = $this->createUserWithRole('Viewer');

        $this->assertTrue($this->policy->viewAny($user));
    }

    /**
     * Test Admin can create assessment.
     */
    public function test_admin_can_create_assessment(): void
    {
        $user = $this->createUserWithRole('Admin');

        $this->assertTrue($this->policy->create($user));
    }

    /**
     * Test Viewer cannot create assessment.
     */
    public function test_viewer_cannot_create_assessment(): void
    {
        $user = $this->createUserWithRole('Viewer');

        $this->assertFalse($this->policy->create($user));
    }

    /**
     * Test Admin can update assessment.
     */
    public function test_admin_can_update_assessment(): void
    {
        $user = $this->createUserWithRole('Admin');
        $assessment = Assessment::factory()->create();

        $this->assertTrue($this->policy->update($user, $assessment));
    }

    /**
     * Test Assessor cannot update assessment.
     */
    public function test_assessor_cannot_update_assessment(): void
    {
        $user = $this->createUserWithRole('Assessor');
        $assessment = Assessment::factory()->create();

        $this->assertFalse($this->policy->update($user, $assessment));
    }

    /**
     * Test Super Admin can delete assessment.
     */
    public function test_super_admin_can_delete_assessment(): void
    {
        $user = $this->createUserWithRole('Super Admin');
        $assessment = Assessment::factory()->create();

        $this->assertTrue($this->policy->delete($user, $assessment));
    }

    /**
     * Test Manager cannot delete assessment.
     */
    public function test_manager_cannot_delete_assessment(): void
    {
        $user = $this->createUserWithRole('Manager');
        $assessment = Assessment::factory()->create();

        $this->assertFalse($this->policy->delete($user, $assessment));
    }

    /**
     * Test Manager can review assessment.
     */
    public function test_manager_can_review_assessment(): void
    {
        $user = $this->createUserWithRole('Manager');
        $assessment = Assessment::factory()->create(['status' => 'completed']);

        $this->assertTrue($this->policy->review($user, $assessment));
    }

    /**
     * Test Super Admin can approve assessment.
     */
    public function test_super_admin_can_approve_assessment(): void
    {
        $user = $this->createUserWithRole('Super Admin');
        $assessment = Assessment::factory()->create(['status' => 'reviewed']);

        $this->assertTrue($this->policy->approve($user, $assessment));
    }

    /**
     * Test Admin cannot approve assessment (only Super Admin).
     */
    public function test_admin_cannot_approve_assessment(): void
    {
        $user = $this->createUserWithRole('Admin');
        $assessment = Assessment::factory()->create(['status' => 'reviewed']);

        $this->assertFalse($this->policy->approve($user, $assessment));
    }
}
