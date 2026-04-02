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
        Schema::create('not_found_factpayments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('row_number')->nullable();
            $table->string('source_file')->default('fakt_tolovlar.csv');
            $table->string('lot_raqami')->nullable();
            $table->string('raw_lot_value')->nullable();
            $table->date('tolov_sana')->nullable();
            $table->string('hujjat_raqam')->nullable();
            $table->string('tolash_nom')->nullable();
            $table->string('tolash_hisob')->nullable();
            $table->string('tolash_inn')->nullable();
            $table->decimal('tolov_summa', 20, 2)->default(0);
            $table->text('detali')->nullable();
            $table->string('reason')->nullable();
            $table->longText('raw_row')->nullable();
            $table->timestamps();

            $table->index(['lot_raqami', 'tolov_sana']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('not_found_factpayments');
    }
};
