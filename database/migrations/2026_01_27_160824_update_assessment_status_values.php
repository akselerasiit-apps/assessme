<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any 'reviewed' or 'approved' status to 'completed'
        DB::table('assessments')
            ->whereIn('status', ['reviewed', 'approved', 'archived'])
            ->update(['status' => 'completed']);
        
        // Alter column to remove 'reviewed', 'approved', 'archived' from enum
        DB::statement("ALTER TABLE assessments MODIFY COLUMN status ENUM('draft', 'in_progress', 'completed') DEFAULT 'draft'");
        
        // Remove reviewed_by and approved_by columns as they're no longer needed
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['reviewed_by', 'approved_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore enum values
        DB::statement("ALTER TABLE assessments MODIFY COLUMN status ENUM('draft', 'in_progress', 'completed', 'reviewed', 'approved', 'archived') DEFAULT 'draft'");
        
        // Restore columns
        Schema::table('assessments', function (Blueprint $table) {
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
        });
    }
};
