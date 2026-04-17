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
        Schema::table('assessment_ofis', function (Blueprint $table) {
            $table->string('generation_source')->nullable()->after('gap_score');
            $table->string('generation_provider')->nullable()->after('generation_source');
            $table->string('generation_model')->nullable()->after('generation_provider');
            $table->string('prompt_version')->nullable()->after('generation_model');
            $table->boolean('fallback_used')->default(false)->after('prompt_version');

            $table->index(['type', 'generation_source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_ofis', function (Blueprint $table) {
            $table->dropIndex('assessment_ofis_type_generation_source_index');
            $table->dropColumn([
                'generation_source',
                'generation_provider',
                'generation_model',
                'prompt_version',
                'fallback_used',
            ]);
        });
    }
};
