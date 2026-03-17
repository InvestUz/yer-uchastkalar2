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
        Schema::create('davaktiv_rasxods', function (Blueprint $table) {
            $table->id();
            $table->date('doc_date')->nullable()->comment('Дата документа');
            $table->string('month')->nullable()->comment('месяц');
            $table->string('doc_number')->nullable()->comment('№ документа');
            $table->string('recipient_name')->nullable()->comment('Наименование получателя');
            $table->string('article')->nullable()->comment('Статья');
            $table->string('account_number')->nullable()->comment('Счет получателя');
            $table->string('bank_code')->nullable()->comment('Код банка получателя');
            $table->decimal('amount', 18, 2)->nullable()->comment('Сумма');
            $table->longText('details')->nullable()->comment('Детали документа');
            $table->longText('by_articles')->nullable()->comment('По статьям');
            $table->timestamps();
            $table->index('doc_date');
            $table->index('recipient_name');
            $table->index('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('davaktiv_rasxods');
    }
};
