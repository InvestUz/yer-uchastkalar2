<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
          Schema::create('yer_sotuvlar', function (Blueprint $table) {
            $table->id();

            // Asosiy ma'lumotlar
            $table->string('lot_raqami')->unique();
            $table->string('tuman')->nullable();
            $table->string('mfy')->nullable();
            $table->string('manzil')->nullable();
            $table->string('unikal_raqam')->nullable();
            $table->string('zona')->nullable();
            $table->string('bosh_reja_zona')->nullable(); // Бош режа бўйича жойлашув зонаси
            $table->string('yangi_ozbekiston')->nullable(); // Янги Ўзбекистон
            $table->decimal('maydoni', 12, 4)->nullable();
            $table->text('lokatsiya')->nullable(); // Google Maps link

            // Qurilish ma'lumotlari
            $table->text('qurilish_turi_1')->nullable(); // Қурилишга рухсат берилган объект тури
            $table->text('qurilish_turi_2')->nullable(); // Қурилишга рухсат берилган объект тури (2)
            $table->decimal('qurilish_maydoni', 12, 2)->nullable(); // кв.м
            $table->decimal('investitsiya', 20, 2)->nullable(); // АҚШ долл

            // Auksion ma'lumotlari
            $table->decimal('boshlangich_narx', 20, 2)->nullable();
            $table->date('auksion_sana')->nullable();
            $table->decimal('sotilgan_narx', 20, 2)->nullable();
            $table->string('auksion_golibi')->nullable(); // G`olib / Golib yoq
            $table->string('golib_turi')->nullable(); // юр лицо / физ лицо
            $table->string('golib_nomi')->nullable();
            $table->string('telefon')->nullable();
            $table->string('tolov_turi')->nullable(); // муддатли / муддатли эмас
            $table->string('asos')->nullable(); // ПФ-33, ПФ-93
            $table->string('auksion_turi')->nullable(); // Очиқ / Ёпиқ аукцион
            $table->string('holat')->nullable(); // Лот ҳолати

            // Shartnoma ma'lumotlari
            $table->string('shartnoma_holati')->nullable(); // шартнома тузганлиги
            $table->date('shartnoma_sana')->nullable();
            $table->string('shartnoma_raqam')->nullable();

            // Moliyaviy ma'lumotlar
            $table->decimal('golib_tolagan', 20, 2)->nullable(); // Ғолиб аукционга тўлаган сумма
            $table->decimal('buyurtmachiga_otkazilgan', 20, 2)->nullable(); // Буюртмачига ўтказилган сумма
            $table->decimal('chegirma', 20, 2)->nullable(); // Чегирма
            $table->decimal('auksion_harajati', 20, 2)->nullable(); // Аукцион ҳаражати 1 фоиз
            $table->decimal('tushadigan_mablagh', 20, 2)->nullable(); // Тушадиган маблағ
            $table->decimal('davaktiv_jamgarmasi', 20, 2)->nullable(); // Давактив жамғармасига тушган маблағ
            $table->decimal('shartnoma_tushgan', 20, 2)->nullable(); // шартнома бўйича тушган маблағ
            $table->decimal('davaktivda_turgan', 20, 2)->nullable(); // Давактивда турган маблағ
            $table->decimal('yer_auksion_harajat', 20, 2)->nullable(); // Ерни аукционга чиқариш ва аукцион харажатлари

            // Taqsimot - tushadigan
            $table->decimal('mahalliy_byudjet_tushadigan', 20, 2)->nullable();
            $table->decimal('jamgarma_tushadigan', 20, 2)->nullable();
            $table->decimal('yangi_oz_direksiya_tushadigan', 20, 2)->nullable();
            $table->decimal('shayxontohur_tushadigan', 20, 2)->nullable();

            $table->decimal('yangi_hayot_industrial_park_tushadigan', 20, 2)->nullable();
            $table->decimal('ksz_direksiyalari_tushadigan', 20, 2)->nullable();
            $table->decimal('toshkent_city_direksiya_tushadigan', 20, 2)->nullable();
            $table->decimal('tuman_byudjeti_tushadigan', 20, 2)->nullable();


            // Taqsimot - taqsimlangan
            $table->decimal('mahalliy_byudjet_taqsimlangan', 20, 2)->nullable();
            $table->decimal('jamgarma_taqsimlangan', 20, 2)->nullable();
            $table->decimal('yangi_oz_direksiya_taqsimlangan', 20, 2)->nullable();
            $table->decimal('shayxontohur_taqsimlangan', 20, 2)->nullable();

          $table->decimal('yangi_hayot_industrial_park_taqsimlangan', 20, 2)->nullable();
            $table->decimal('ksz_direksiyalari_taqsimlangan', 20, 2)->nullable();
            $table->decimal('toshkent_city_direksiya_taqsimlangan', 20, 2)->nullable();
            $table->decimal('tuman_byudjeti_taqsimlangan', 20, 2)->nullable();

            // Qoldiq
            $table->decimal('qoldiq_mahalliy_byudjet', 20, 2)->nullable();
            $table->decimal('qoldiq_jamgarma', 20, 2)->nullable();
            $table->decimal('qoldiq_yangi_oz_direksiya', 20, 2)->nullable();
            $table->decimal('qoldiq_shayxontohur', 20, 2)->nullable();

            $table->decimal('farqi', 20, 2)->nullable();
            $table->decimal('shartnoma_summasi', 20, 2)->nullable(); // Шартнома бўйича тушадиган

            $table->integer('yil')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['lot_raqami', 'tuman']);
            $table->index('auksion_sana');
            $table->index('yil');
            $table->index('holat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yer_sotuvlar');
    }
};
