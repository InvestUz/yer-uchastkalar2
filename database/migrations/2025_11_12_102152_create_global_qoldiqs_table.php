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
        Schema::create('global_qoldiq', function (Blueprint $table) {
            $table->id();
            $table->date('sana')->unique()->comment('Qoldiq sanasi (snapshot date)');
            $table->decimal('summa', 20, 2)->comment('Qoldiq summasi');
            $table->enum('tur', ['plus', 'minus'])->default('plus')->comment('Plus (+) yoki Minus (-)');
            $table->text('izoh')->nullable()->comment('Qoldiq haqida izoh');
            $table->timestamps();

            $table->index('sana');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_qoldiq');
    }
};
