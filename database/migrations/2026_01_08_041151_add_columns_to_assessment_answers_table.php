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
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->enum('capability_rating', ['N/A', 'N', 'P', 'L', 'F'])->nullable()->after('capability_score');
            $table->text('translated_text')->nullable()->after('answer_text');
            $table->integer('level')->nullable()->after('maturity_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->dropColumn(['capability_rating', 'translated_text', 'level']);
        });
    }
};
