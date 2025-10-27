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
           Schema::create('grafik_tolovlar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('yer_sotuv_id')->constrained('yer_sotuvlar')->onDelete('cascade');
            $table->string('lot_raqami');
            $table->integer('yil');
            $table->integer('oy');
            $table->string('oy_nomi');
            $table->decimal('grafik_summa', 20, 2);
            $table->timestamps();

            $table->index(['lot_raqami', 'yil', 'oy']);
            $table->foreign('lot_raqami')->references('lot_raqami')->on('yer_sotuvlar')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grafik_tolovs');
    }
};
