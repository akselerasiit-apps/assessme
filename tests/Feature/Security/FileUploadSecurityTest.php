<?php

namespace Tests\Feature\Security;

use App\Models\Assessment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test malicious file upload is rejected.
     */
    public function test_malicious_file_upload_is_rejected(): void
    {
        Storage::fake('private');
        $this->authenticateAs('Admin');

        $assessment = Assessment::factory()->draft()->create();
        $question = \App\Models\GamoQuestion::factory()->create();
        $answer = \App\Models\AssessmentAnswer::factory()->create([
            'assessment_id' => $assessment->id,
            'question_id' => $question->id,
        ]);
        $maliciousFile = UploadedFile::fake()->create('malware.exe', 1024);

        $response = $this->postJson('/api/evidence/upload', [
            'assessment_id' => $assessment->id,
            'answer_id' => $answer->id,
            'file' => $maliciousFile,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    /**
     * Test file size limit is enforced.
     */
    public function test_file_size_limit_is_enforced(): void
    {
        Storage::fake('private');
        $this->authenticateAs('Admin');

        $assessment = Assessment::factory()->draft()->create();
        $question = \App\Models\GamoQuestion::factory()->create();
        $answer = \App\Models\AssessmentAnswer::factory()->create([
            'assessment_id' => $assessment->id,
            'question_id' => $question->id,
        ]);
        $largeFile = UploadedFile::fake()->create('large.pdf', 15000); // 15MB

        $response = $this->postJson('/api/evidence/upload', [
            'assessment_id' => $assessment->id,
            'answer_id' => $answer->id,
            'file' => $largeFile,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    /**
     * Test only allowed file types can be uploaded.
     */
    public function test_only_allowed_file_types_can_be_uploaded(): void
    {
        Storage::fake('private');
        $this->authenticateAs('Admin');

        $assessment = Assessment::factory()->draft()->create();
        $question = \App\Models\GamoQuestion::factory()->create();
        $answer = \App\Models\AssessmentAnswer::factory()->create([
            'assessment_id' => $assessment->id,
            'question_id' => $question->id,
        ]);
        $validFile = UploadedFile::fake()->create('evidence.pdf', 1024);

        $response = $this->postJson('/api/evidence/upload', [
            'assessment_id' => $assessment->id,
            'answer_id' => $answer->id,
            'file' => $validFile,
        ]);

        // Should succeed
        $response->assertStatus(201);
    }

    /**
     * Test file is stored outside public directory.
     */
    public function test_file_is_stored_securely(): void
    {
        Storage::fake('private');
        $user = $this->authenticateAs('Assessor');

        $assessment = Assessment::factory()->create([
            'created_by' => $user->id,
            'status' => 'in_progress',
        ]);
        $question = \App\Models\GamoQuestion::factory()->create();
        $answer = \App\Models\AssessmentAnswer::factory()->create([
            'assessment_id' => $assessment->id,
            'question_id' => $question->id,
            'answered_by' => $user->id,
        ]);
        $file = UploadedFile::fake()->create('secure.pdf', 1024);

        $response = $this->postJson('/api/evidence/upload', [
            'assessment_id' => $assessment->id,
            'answer_id' => $answer->id,
            'file' => $file,
        ]);

        // Assert response is successful
        $response->assertStatus(201);
        
        // Verify file is stored in encrypted directory
        $answer->refresh();
        $this->assertNotNull($answer->evidence_file);
        $this->assertTrue($answer->evidence_encrypted);
        
        // File should be in private storage with encrypted path
        Storage::disk('private')->assertExists($answer->evidence_file);
    }

    /**
     * Test path traversal attack is prevented.
     */
    public function test_path_traversal_attack_is_prevented(): void
    {
        Storage::fake('private');
        $this->authenticateAs('Admin');

        $assessment = Assessment::factory()->draft()->create();
        $question = \App\Models\GamoQuestion::factory()->create();
        $answer = \App\Models\AssessmentAnswer::factory()->create([
            'assessment_id' => $assessment->id,
            'question_id' => $question->id,
        ]);
        $file = UploadedFile::fake()->create('../../../etc/passwd', 100);

        $response = $this->postJson('/api/evidence/upload', [
            'assessment_id' => $assessment->id,
            'answer_id' => $answer->id,
            'file' => $file,
        ]);

        $response->assertStatus(422);
    }
}
