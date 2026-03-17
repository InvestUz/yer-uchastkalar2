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
        Schema::table('davaktiv_rasxods', function (Blueprint $table) {
                $table->string('district')->nullable()->after('by_articles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('davaktiv_rasxods', function (Blueprint $table) {
                $table->dropColumn('district');
        });
    }
};
