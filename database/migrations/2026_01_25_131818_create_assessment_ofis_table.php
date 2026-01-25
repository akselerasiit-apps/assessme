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
        Schema::create('assessment_ofis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->foreignId('gamo_objective_id')->constrained('gamo_objectives')->onDelete('cascade');
            $table->string('title');
            $table->text('description'); // WYSIWYG content (HTML)
            $table->enum('type', ['auto', 'manual'])->default('manual'); // auto-generated or manual
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->string('category')->nullable(); // Process/People/Technology/Governance
            $table->text('recommended_action')->nullable();
            $table->date('target_date')->nullable();
            $table->integer('current_level')->nullable(); // For auto-generated: current capability level
            $table->integer('target_level')->nullable(); // For auto-generated: target level
            $table->decimal('gap_score', 5, 2)->nullable(); // Gap between current and target
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['assessment_id', 'gamo_objective_id']);
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_ofis');
    }
};
