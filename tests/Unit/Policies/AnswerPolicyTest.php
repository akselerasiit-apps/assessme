<?php

namespace Tests\Unit\Policies;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\User;
use App\Policies\AnswerPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnswerPolicyTest extends TestCase
{
    use RefreshDatabase;

    private AnswerPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new AnswerPolicy();
    }

    /**
     * Test Assessor can create answer.
     */
    public function test_assessor_can_create_answer(): void
    {
        $user = $this->createUserWithRole('Assessor');

        $this->assertTrue($this->policy->create($user));
    }

    /**
     * Test Viewer cannot create answer.
     */
    public function test_viewer_cannot_create_answer(): void
    {
        $user = $this->createUserWithRole('Viewer');

        $this->assertFalse($this->policy->create($user));
    }

    /**
     * Test Admin can view any answer.
     */
    public function test_admin_can_view_any_answer(): void
    {
        $user = $this->createUserWithRole('Admin');

        $this->assertTrue($this->policy->viewAny($user));
    }

    /**
     * Test Assessor can update their own answer.
     */
    public function test_assessor_can_update_own_answer(): void
    {
        $user = $this->createUserWithRole('Assessor');
        $assessment = Assessment::factory()->draft()->create();
        $answer = AssessmentAnswer::factory()->create([
            'assessment_id' => $assessment->id,
            'answered_by' => $user->id
        ]);

        $this->assertTrue($this->policy->update($user, $answer));
    }

    /**
     * Test Assessor cannot update other's answer.
     */
    public function test_assessor_cannot_update_others_answer(): void
    {
        $user = $this->createUserWithRole('Assessor');
        $otherUser = $this->createUserWithRole('Assessor');
        $answer = AssessmentAnswer::factory()->create(['answered_by' => $otherUser->id]);

        $this->assertFalse($this->policy->update($user, $answer));
    }

    /**
     * Test Admin can delete any answer.
     */
    public function test_admin_can_delete_any_answer(): void
    {
        $user = $this->createUserWithRole('Admin');
        $assessment = Assessment::factory()->draft()->create();
        $answer = AssessmentAnswer::factory()->create([
            'assessment_id' => $assessment->id
        ]);

        $this->assertTrue($this->policy->delete($user, $answer));
    }

    /**
     * Test Assessor can upload evidence.
     */
    public function test_assessor_can_upload_evidence(): void
    {
        $user = $this->createUserWithRole('Assessor');
        $assessment = Assessment::factory()->draft()->create();
        $answer = AssessmentAnswer::factory()->create([
            'assessment_id' => $assessment->id,
            'answered_by' => $user->id
        ]);

        $this->assertTrue($this->policy->uploadEvidence($user, $answer));
    }

    /**
     * Test Viewer cannot upload evidence.
     */
    public function test_viewer_cannot_upload_evidence(): void
    {
        $user = $this->createUserWithRole('Viewer');
        $answer = AssessmentAnswer::factory()->create();

        $this->assertFalse($this->policy->uploadEvidence($user, $answer));
    }
}
