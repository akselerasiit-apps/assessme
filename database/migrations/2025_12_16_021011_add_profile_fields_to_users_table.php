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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('password');
            $table->string('phone', 20)->nullable()->after('email');
            $table->text('bio')->nullable()->after('phone');
            $table->string('timezone', 50)->nullable()->after('bio');
            $table->string('language', 5)->default('id')->after('timezone');
            $table->json('preferences')->nullable()->after('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_path', 'phone', 'bio', 'timezone', 'language', 'preferences']);
        });
    }
};
