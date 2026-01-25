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
        Schema::create('assessment_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained('gamo_questions')->cascadeOnDelete();
            $table->string('evidence_name');
            $table->text('evidence_description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('url')->nullable();
            $table->string('file_type', 50)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['assessment_id', 'activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_evidence');
    }
};
