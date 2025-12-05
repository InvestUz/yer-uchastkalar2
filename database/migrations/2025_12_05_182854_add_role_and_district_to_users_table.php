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
            $table->string('role')->default('district'); // super_admin or district
            $table->string('tuman')->nullable(); // District name for district users
            $table->boolean('can_edit')->default(false); // Edit permissions
            $table->boolean('is_active')->default(true); // Account status
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'tuman', 'can_edit', 'is_active']);
        });
    }
};
