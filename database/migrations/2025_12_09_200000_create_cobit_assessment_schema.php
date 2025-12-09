<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // COMPANIES TABLE
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('industry', 100)->nullable();
            $table->enum('size', ['startup', 'sme', 'enterprise'])->default('sme');
            $table->integer('established_year')->nullable();
            $table->timestamps();
        });

        // DESIGN FACTORS TABLE
        Schema::create('design_factors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->integer('factor_order')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('code');
            $table->index('is_active');
        });

        // GAMO OBJECTIVES TABLE
        Schema::create('gamo_objectives', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('name_id')->nullable();
            $table->longText('description');
            $table->longText('description_id')->nullable();
            $table->enum('category', ['EDM', 'APO', 'BAI', 'DSS', 'MEA']);
            $table->integer('objective_order')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('code');
            $table->index('category');
            $table->index('is_active');
        });

        // ASSESSMENTS TABLE (MUST COME BEFORE DEPENDENT TABLES)
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('company_id')->constrained('companies');
            $table->enum('assessment_type', ['initial', 'periodic', 'specific'])->default('initial');
            $table->enum('scope_type', ['full', 'tailored'])->default('tailored');
            $table->enum('status', ['draft', 'in_progress', 'completed', 'reviewed', 'approved', 'archived'])->default('draft');
            $table->date('assessment_period_start')->nullable();
            $table->date('assessment_period_end')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->integer('progress_percentage')->default(0);
            $table->decimal('overall_maturity_level', 3, 2)->nullable();
            $table->boolean('is_encrypted')->default(true);
            $table->timestamps();
            $table->index('status');
            $table->index('company_id');
            $table->index('created_at');
        });

        // ASSESSMENT DESIGN FACTORS TABLE
        Schema::create('assessment_design_factors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('design_factor_id')->constrained('design_factors');
            $table->string('selected_value', 500)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['assessment_id', 'design_factor_id'], 'adf_assessment_df_unique');
        });

        // GAMO QUESTIONS TABLE
        Schema::create('gamo_questions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->foreignId('gamo_objective_id')->constrained('gamo_objectives');
            $table->longText('question_text');
            $table->text('guidance')->nullable();
            $table->text('evidence_requirement')->nullable();
            $table->enum('question_type', ['text', 'rating', 'multiple_choice', 'yes_no', 'evidence'])->default('text');
            $table->integer('maturity_level')->default(1);
            $table->boolean('required')->default(true);
            $table->integer('question_order')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('gamo_objective_id');
            $table->index('is_active');
        });

        // ASSESSMENT GAMO SELECTIONS TABLE
        Schema::create('assessment_gamo_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('gamo_objective_id')->constrained('gamo_objectives');
            $table->boolean('is_selected')->default(true);
            $table->text('selection_reason')->nullable();
            $table->timestamp('selected_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['assessment_id', 'gamo_objective_id'], 'ags_assessment_gamo_unique');
        });

        // ASSESSMENT ANSWERS TABLE
        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('gamo_questions');
            $table->foreignId('gamo_objective_id')->constrained('gamo_objectives');
            $table->longText('answer_text')->nullable();
            $table->json('answer_json')->nullable();
            $table->integer('maturity_level')->default(0);
            $table->decimal('capability_score', 5, 2)->nullable();
            $table->boolean('is_encrypted')->default(true);
            $table->string('evidence_file')->nullable();
            $table->boolean('evidence_encrypted')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('answered_by')->constrained('users');
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
            $table->unique(['assessment_id', 'question_id'], 'aa_assessment_question_unique');
            $table->index('gamo_objective_id');
        });

        // GAMO SCORES TABLE
        Schema::create('gamo_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('gamo_objective_id')->constrained('gamo_objectives');
            $table->decimal('current_maturity_level', 3, 2)->default(0);
            $table->decimal('target_maturity_level', 3, 2)->default(3);
            $table->decimal('capability_score', 5, 2)->nullable();
            $table->decimal('capability_level', 3, 2)->nullable();
            $table->integer('percentage_complete')->default(0);
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->timestamps();
            $table->unique(['assessment_id', 'gamo_objective_id'], 'gs_assessment_gamo_unique');
            $table->index('current_maturity_level');
        });

        // ASSESSMENT GAMO TARGET LEVELS TABLE
        Schema::create('assessment_gamo_target_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('gamo_objective_id')->constrained('gamo_objectives');
            $table->decimal('current_maturity_level', 3, 2)->default(0);
            $table->decimal('target_maturity_level', 3, 2)->default(3);
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])->default('MEDIUM');
            $table->string('estimated_effort')->nullable();
            $table->date('target_achievement_date')->nullable();
            $table->text('gap_analysis')->nullable();
            $table->text('recommended_actions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['assessment_id', 'gamo_objective_id'], 'agtl_assessment_gamo_unique');
        });

        // GAMO CAPABILITY DEFINITIONS TABLE
        Schema::create('gamo_capability_definitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gamo_objective_id')->constrained('gamo_objectives');
            $table->integer('level');
            $table->string('level_name', 100)->nullable();
            $table->decimal('compliance_score', 5, 2)->nullable();
            $table->integer('weight')->default(1);
            $table->integer('min_questions')->nullable();
            $table->integer('max_questions')->nullable();
            $table->integer('required_evidence_count')->nullable();
            $table->integer('required_compliance_percentage')->nullable();
            $table->longText('additional_requirements')->nullable();
            $table->longText('guidance_text')->nullable();
            $table->longText('examples')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['gamo_objective_id', 'level'], 'gcd_gamo_level_unique');
        });

        // ASSESSMENT ANSWER CAPABILITY SCORES TABLE
        Schema::create('assessment_answer_capability_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_answer_id')->constrained('assessment_answers')->cascadeOnDelete();
            $table->integer('level');
            $table->decimal('compliance_score', 5, 2)->nullable();
            $table->integer('compliance_percentage')->nullable();
            $table->enum('achievement_status', ['NOT_ACHIEVED', 'PARTIALLY_ACHIEVED', 'FULLY_ACHIEVED'])->nullable();
            $table->boolean('evidence_provided')->default(false);
            $table->integer('evidence_count')->default(0);
            $table->text('assessment_notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['assessment_answer_id', 'level'], 'aacs_answer_level_unique');
        });

        // ASSESSMENT BANDINGS TABLE
        Schema::create('assessment_bandings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('gamo_objective_id')->constrained('gamo_objectives');
            $table->integer('banding_round')->default(1);
            $table->foreignId('initiated_by')->constrained('users');
            $table->string('banding_reason');
            $table->longText('banding_description')->nullable();
            $table->decimal('old_maturity_level', 3, 2)->nullable();
            $table->decimal('new_maturity_level', 3, 2)->nullable();
            $table->integer('old_evidence_count')->nullable();
            $table->integer('new_evidence_count')->nullable();
            $table->string('additional_evidence_files', 500)->nullable();
            $table->longText('revised_answers')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('approval_notes')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('banding_round');
        });

        // LOGIN ATTEMPTS TABLE
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('email');
            $table->string('ip_address', 45);
            $table->enum('status', ['SUCCESS', 'FAILED'])->default('FAILED');
            $table->string('failure_reason')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('attempted_at')->useCurrent();
            $table->index(['email', 'ip_address']);
            $table->index('attempted_at');
        });

        // AUDIT LOGS TABLE
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action', 100);
            $table->string('module', 50)->nullable();
            $table->string('entity_type', 100)->nullable();
            $table->bigInteger('entity_id')->nullable();
            $table->integer('status_code')->nullable();
            $table->longText('old_values')->nullable();
            $table->longText('new_values')->nullable();
            $table->boolean('sensitive_data_accessed')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->boolean('is_encrypted')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
            $table->index('sensitive_data_accessed');
        });

        // USER TOKENS TABLE
        Schema::create('user_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('token_type', ['access', 'refresh', 'api'])->default('access');
            $table->string('token_hash');
            $table->json('device_info')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->boolean('is_encrypted')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->index('user_id');
            $table->index('expires_at');
            $table->unique('token_hash');
        });

        // ENCRYPTION KEYS LOG TABLE
        Schema::create('encryption_keys_log', function (Blueprint $table) {
            $table->id();
            $table->integer('key_version');
            $table->string('key_algorithm', 100)->nullable();
            $table->integer('key_size')->nullable();
            $table->timestamp('rotation_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'compromised'])->default('active');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        // Drop tables in reverse order due to foreign keys
        Schema::dropIfExists('assessment_bandings');
        Schema::dropIfExists('assessment_answer_capability_scores');
        Schema::dropIfExists('gamo_capability_definitions');
        Schema::dropIfExists('assessment_gamo_target_levels');
        Schema::dropIfExists('gamo_scores');
        Schema::dropIfExists('assessment_answers');
        Schema::dropIfExists('assessment_gamo_selections');
        Schema::dropIfExists('gamo_questions');
        Schema::dropIfExists('assessment_design_factors');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('gamo_objectives');
        Schema::dropIfExists('design_factors');
        Schema::dropIfExists('encryption_keys_log');
        Schema::dropIfExists('user_tokens');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('companies');
    }
};
