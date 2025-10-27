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
        Schema::create('fakt_tolovlar', function (Blueprint $table) {
            $table->id();
            $table->string('lot_raqami');
            $table->date('tolov_sana');
            $table->string('hujjat_raqam')->nullable();
            $table->string('tolash_nom')->nullable();
            $table->string('tolash_hisob')->nullable();
            $table->string('tolash_inn')->nullable();
            $table->decimal('tolov_summa', 20, 2);
            $table->text('detali')->nullable();
            $table->timestamps();

            $table->index(['lot_raqami', 'tolov_sana']);
            $table->foreign('lot_raqami')->references('lot_raqami')->on('yer_sotuvlar')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fakt_tolovs');
    }
};
