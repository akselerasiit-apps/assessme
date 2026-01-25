<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Evidence versions table for version control
        Schema::create('evidence_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_answer_id')->constrained('assessment_answers')->cascadeOnDelete();
            $table->integer('version_number')->default(1);
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type', 50);
            $table->bigInteger('file_size'); // in bytes
            $table->string('file_hash', 64)->nullable(); // SHA256 hash for integrity
            $table->boolean('is_encrypted')->default(true);
            $table->text('version_notes')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['assessment_answer_id', 'version_number']);
            $table->index('uploaded_at');
        });

        // Add columns to assessment_answers table for enhanced evidence management
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->integer('current_version')->default(1)->after('evidence_encrypted');
            $table->foreignId('current_version_id')->nullable()->after('current_version');
            $table->text('tags')->nullable()->after('notes');
            $table->timestamp('evidence_updated_at')->nullable()->after('tags');
            
            // Add index for better query performance
            $table->index('evidence_updated_at');
        });

        // Evidence access log for tracking who accessed what
        Schema::create('evidence_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_answer_id')->constrained('assessment_answers')->cascadeOnDelete();
            $table->foreignId('evidence_version_id')->nullable()->constrained('evidence_versions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('action', ['view', 'download', 'upload', 'delete', 'restore'])->default('view');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamp('accessed_at')->useCurrent();
            
            $table->index(['assessment_answer_id', 'accessed_at']);
            $table->index(['user_id', 'accessed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence_access_logs');
        
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->dropColumn(['current_version', 'current_version_id', 'tags', 'evidence_updated_at']);
        });
        
        Schema::dropIfExists('evidence_versions');
    }
};
