<?php $__env->startSection('title', 'Йиғма маълумот'); ?>
<?php $__env->startSection('content'); ?>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-6 px-4">
        <div class="max-w-[98%] mx-auto">
            <!-- Premium Government Header -->
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden mb-6 border-t-4 border-blue-600">
                <div class="bg-white px-8 py-6">
                    <div class="flex items-center justify-center space-x-4">
                        <div class="text-center">
                            <h1 class="text-2xl md:text-3xl font-bold text-blue-900 tracking-wide mb-1">
                                Тошкент шаҳрида аукцион савдоларида сотилган ер участкалари тўғрисида
                            </h1>
                            <h2 class="text-xl md:text-2xl font-semibold text-blue-700">
                                ЙИҒМА МАЪЛУМОТ
                            </h2>
                        </div>
                    </div>
                </div>
                <!-- Statistics Table -->
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse statistics-table">
                            <thead>
                                <!-- HEADER ROW 1: Excel Row 3 (EMPTY ROW) - Only T/p and Hududlar -->
                                <tr style="background: #eff6ff !important;">
                                    <th rowspan="6"
                                        class="sticky-col border border-slate-400 px-4 py-4 text-center align-middle font-bold text-slate-800"
                                        style="min-width: 60px;">Т/р</th>
                                    <th rowspan="6"
                                        class="sticky-col-2 border border-slate-400 px-4 py-4 text-center align-middle font-bold text-slate-800"
                                        style="min-width: 200px;">Ҳудудлар</th>
                                </tr>

                                <!-- HEADER ROW 2: Excel Row 4 - Top level section headers -->
                                <tr style="background: #eff6ff !important;">
                                    <!-- Cols 2-11: Main section -->
                                    <th colspan="10"
                                        class="border border-slate-400 px-4 py-3 text-center font-bold text-slate-800">
                                        Сотилган ер участкалари</th>

                                    <!-- Cols 12-31: "шундан" section -->
                                    <th colspan="20"
                                        class="border border-slate-400 px-4 py-3 text-center font-bold text-slate-800">
                                        шундан</th>

                                    <!-- Cols 32-35: Auction pending -->
                                    <th colspan="4" rowspan="3"
                                        class="border border-slate-400 px-4 py-3 text-center align-middle font-bold text-slate-800">
                                        Аукционда сотилган ва савдо натижасини расмийлаштишда турган ерлар</th>

                                    <!-- Cols 36-37: Property not accepted -->
                                    <th colspan="2" rowspan="3"
                                        class="border border-slate-400 px-4 py-3 text-center align-middle font-bold text-slate-800">
                                        Мулкни қабул қилиб олиш тугмаси босилмаган ерлар</th>
                                </tr>

                                <!-- HEADER ROW 3: Excel Row 5 - Second level -->
                                <tr style="background: #eff6ff !important;">
                                    <!-- Cols 2-5: Basic metrics -->
                                    <th rowspan="4"
                                        class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                        style="min-width: 70px;">Сони</th>
                                    <th rowspan="4"
                                        class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                        style="min-width: 90px;">Майдони<br>(га)</th>
                                    <th rowspan="4"
                                        class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                        style="min-width: 110px;">Бошланғич<br>нархи<br>(млрд сўм)</th>
                                    <th rowspan="4"
                                        class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                        style="min-width: 110px;">Сотилган<br>нархи<br>(млрд сўм)</th>

                                    <!-- Cols 6-7: "шундан" (NO rowspan, continues to next row) -->
                                    <th colspan="2"
                                        class="border border-slate-400 px-3 py-3 text-center font-semibold text-slate-700 text-sm">
                                        шундан</th>

                                    <!-- Col 8: Tushadigan mablagh -->
                                    <th rowspan="4"
                                        class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                        style="min-width: 130px;">Сотилган ер<br>нархи
                                        бўйича<br>тушадиган<br>маблағ<br>(млрд сўм)</th>

                                    <!-- Cols 9-11: "шундан" for payments (NO rowspan, continues to next row) -->
                                    <th colspan="3"
                                        class="border border-slate-400 px-3 py-3 text-center font-semibold text-slate-700 text-sm">
                                        шундан</th>

                                    <!-- Cols 12-21: Bir yola section -->
                                    <th colspan="10" rowspan="2"
                                        class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm">
                                        Бир йўла тўлаш шарти билан сотилган</th>

                                    <!-- Cols 22-31: Bolib section -->
                                    <th colspan="10" rowspan="2"
                                        class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm">
                                        Нархини бўлиб тўлаш шарти билан сотилган</th>
                                </tr>

                                <!-- HEADER ROW 4: Excel Row 6 - Third level -->
                                <tr style="background: #eff6ff !important;">
                                    <!-- Col 6: Chegirma -->
                                    <th rowspan="3"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 100px;">Чегирма<br>қиймати<br>(млрд сўм)</th>

                                    <!-- Col 7: Auksion harajat -->
                                    <th rowspan="3"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 110px;">Аукцион<br>(1 фоиз)<br>харажати<br>(млрд сўм)</th>

                                    <!-- Col 9: Jami tushgan -->
                                    <th rowspan="3"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 100px;">жами тушган<br>маблағ<br>(млрд сўм)</th>

                                    <!-- Col 10: Qoldiq -->
                                    <th rowspan="3"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 100px;">қолдиқ<br>маблағ<br>(млрд сўм)</th>

                                    <!-- Col 11: Foizda -->
                                    <th rowspan="3"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 80px;">фоизда</th>
                                </tr>

                                <!-- HEADER ROW 5: Excel Row 7 - Fourth level (Biryola and Bolib sections) -->
                                <tr style="background: #eff6ff !important;">
                                    <!-- Cols 12-15: Biryola basic -->
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 60px;">Сони</th>
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 80px;">Майдони<br>(га)</th>
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 100px;">Бошланғич<br>нархи<br>(млрд сўм)</th>
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 100px;">Сотилган<br>нархи<br>(млрд сўм)</th>

                                    <!-- Cols 16-17: Biryola "шундан" -->
                                    <th colspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs">
                                        шундан</th>

                                    <!-- Col 18: Biryola tushadigan -->
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 120px;">Сотилган ер<br>нархи
                                        бўйича<br>тушадиган<br>маблағ<br>(млрд сўм)</th>

                                    <!-- Cols 19-21: Biryola "шундан" payments -->
                                    <th colspan="3"
                                        class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs">
                                        шундан</th>

                                    <!-- Cols 22-25: Bolib basic -->
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 60px;">Сони</th>
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 80px;">Майдони<br>(га)</th>
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 100px;">Бошланғич<br>нархи<br>(млрд сўм)</th>
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 100px;">Сотилган<br>нархи<br>(млрд сўм)</th>

                                    <!-- Cols 26-27: Bolib "шундан" -->
                                    <th colspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs">
                                        шундан</th>

                                    <!-- Col 28: Bolib tushadigan -->
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 120px;">Сотилган ер<br>нархи
                                        бўйича<br>тушадиган<br>маблағ<br>(млрд сўм)</th>

                                    <!-- Cols 29-31: Bolib "шундан" payments -->
                                    <th colspan="3"
                                        class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs">
                                        шундан</th>

                                    <!-- Cols 32-35: Auksonda section details -->
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 70px;">Сони</th>
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 90px;">Майдони<br>(га)</th>
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 110px;">Бошланғич<br>нархи<br>(млрд сўм)</th>
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 110px;">Сотилган<br>нархи<br>(млрд сўм)</th>

                                    <!-- Cols 36-37: Mulk qabul section details -->
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 70px;">Сони</th>
                                    <th rowspan="2"
                                        class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                        style="min-width: 130px;">Аукционда<br>турган маблағ<br>(млрд сўм)</th>
                                </tr>

                                <!-- HEADER ROW 6: Excel Row 8 - Fifth level (Final details) -->
                                <tr style="background: #eff6ff !important;">
                                    <!-- Col 16: Biryola chegirma -->
                                    <th class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                        style="min-width: 90px;">чегирма<br>қиймати<br>(млрд сўм)</th>

                                    <!-- Col 17: Biryola auksion -->
                                    <th class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                        style="min-width: 100px;">аукцион<br>(1 фоиз)<br>харажати<br>(млрд сўм)</th>

                                    <!-- Col 19: Biryola tushgan -->
                                    <th class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                        style="min-width: 100px;">тушган<br>маблағ<br>(млрд сўм)</th>

                                    <!-- Col 20: Biryola qoldiq -->
                                    <th class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                        style="min-width: 90px;">қолдиқ<br>маблағ<br>(млрд сўм)</th>

                                    <!-- Col 21: Biryola foiz -->
                                    <th class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                        style="min-width: 70px;">фоизда</th>

                                    <!-- Col 26: Bolib chegirma -->
                                    <th class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                        style="min-width: 90px;">чегирма<br>қиймати<br>(млрд сўм)</th>

                                    <!-- Col 27: Bolib auksion -->
                                    <th class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                        style="min-width: 100px;">аукцион<br>(1 фоиз)<br>харажати<br>(млрд сўм)</th>

                                    <!-- Col 29: Bolib tushgan -->
                                    <th class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                        style="min-width: 100px;">тушган<br>маблағ<br>(млрд сўм)</th>

                                    <!-- Col 30: Bolib qoldiq -->
                                    <th class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                        style="min-width: 90px;">қолдиқ<br>маблағ<br>(млрд сўм)</th>

                                    <!-- Col 31: Bolib foiz -->
                                    <th class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                        style="min-width: 70px;">фоизда</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white">
                                <!-- JAMI ROW (Excel Row 8) -->
                                <tr
                                    class="bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100 border-y-2 border-amber-400">
                                    <!-- Col 0-1: Label -->
                                    <td colspan="2"
                                        class="sticky-col border border-slate-400 px-4 py-4 text-center align-middle font-bold text-slate-900 text-base uppercase bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100">
                                        ЖАМИ:</td>

                                    <!-- Col 2: Soni -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <a href="<?php echo e(route('yer-sotuvlar.list', ['include_auksonda' => 'true', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']])); ?>"
                                            class="text-blue-700 hover:text-blue-900 hover:underline"><?php echo e($statistics['jami']['jami']['soni']); ?></a>
                                    </td>

                                    <!-- Col 3: Maydoni -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['jami']['maydoni'], 2)); ?></td>

                                    <!-- Col 4: Boshlangich narxi -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['jami']['boshlangich_narx'] / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 5: Sotilgan narxi -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['jami']['sotilgan_narx'] / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 6: Chegirma -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['jami']['chegirma'] / 1000000000, 1)); ?></td>

                                    <!-- Col 7: Auksion harajati -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['jami']['auksion_harajati'] / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 8: Tushadigan mablagh -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['jami']['tushadigan_mablagh'] / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 9: Jami tushgan -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-blue-900">
                                        <?php echo e(number_format(($statistics['jami']['biryola_fakt'] + ($statistics['jami']['bolib_tushgan_all'] ?? 0)) / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 10: Qoldiq -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-red-700">
                                        <?php echo e(number_format(($statistics['jami']['jami']['tushadigan_mablagh'] - ($statistics['jami']['biryola_fakt'] + ($statistics['jami']['bolib_tushgan_all'] ?? 0))) / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 11: Foizda -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e($statistics['jami']['jami']['tushadigan_mablagh'] > 0 ? number_format((($statistics['jami']['biryola_fakt'] + ($statistics['jami']['bolib_tushgan_all'] ?? 0)) / $statistics['jami']['jami']['tushadigan_mablagh']) * 100, 1) : 0); ?>%
                                    </td>

                                    <!-- Col 12: Biryola soni -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <a href="<?php echo e(route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли эмас', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']])); ?>"
                                            class="text-blue-700 hover:text-blue-900 hover:underline"><?php echo e($statistics['jami']['bir_yola']['soni']); ?></a>
                                    </td>

                                    <!-- Col 13: Biryola maydoni -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['bir_yola']['maydoni'], 2)); ?></td>

                                    <!-- Col 14: Biryola boshlangich -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['bir_yola']['boshlangich_narx'] / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 15: Biryola sotilgan -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['bir_yola']['sotilgan_narx'] / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 16: Biryola chegirma -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['bir_yola']['chegirma'] / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 17: Biryola auksion harajat -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['bir_yola']['auksion_harajati'] / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 18: Biryola tushadigan -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['bir_yola']['tushadigan_mablagh'] / 1000000000, decimals: 1)); ?>

                                    </td>

                                    <!-- Col 19: Biryola tushgan -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-blue-900">
                                        <?php echo e(number_format($statistics['jami']['biryola_fakt'] / 1000000000, 1)); ?></td>

                                    <!-- Col 20: Biryola qoldiq -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-red-700">
                                        <?php echo e(number_format(($statistics['jami']['bir_yola']['tushadigan_mablagh'] - $statistics['jami']['biryola_fakt']) / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 21: Biryola foiz -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e($statistics['jami']['bir_yola']['tushadigan_mablagh'] > 0 ? number_format(($statistics['jami']['biryola_fakt'] / $statistics['jami']['bir_yola']['tushadigan_mablagh']) * 100, 1) : 0); ?>%
                                    </td>

                                    <!-- Col 22: Bolib soni -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <a href="<?php echo e(route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']])); ?>"
                                            class="text-blue-700 hover:text-blue-900 hover:underline"><?php echo e($statistics['jami']['bolib']['soni']); ?></a>
                                    </td>

                                    <!-- Col 23: Bolib maydoni -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['bolib']['maydoni'], 2)); ?></td>

                                    <!-- Col 24: Bolib boshlangich -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['bolib']['boshlangich_narx'] / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 25: Bolib sotilgan -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['bolib']['sotilgan_narx'] / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 26: Bolib chegirma -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['bolib']['chegirma'] / 1000000000, 1)); ?></td>

                                    <!-- Col 27: Bolib auksion harajat -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['bolib']['auksion_harajati'] / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 28: Bolib tushadigan -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['bolib_tushadigan'] / 1000000000, 1)); ?></td>

                                    <!-- Col 29: Bolib tushgan -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-blue-900">
                                        <?php echo e(number_format(($statistics['jami']['bolib_tushgan_all'] ?? 0) / 1000000000, 1)); ?></td>

                                    <!-- Col 30: Bolib qoldiq -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-red-700">
                                        <?php echo e(number_format((($statistics['jami']['bolib_tushadigan'] ?? 0) - ($statistics['jami']['bolib_tushgan_all'] ?? 0)) / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 31: Bolib foiz -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(($statistics['jami']['bolib_tushadigan'] ?? 0) > 0 ? number_format((($statistics['jami']['bolib_tushgan_all'] ?? 0) / $statistics['jami']['bolib_tushadigan']) * 100, 1) : 0); ?>%
                                    </td>

                                    <!-- Col 32: Auksonda soni -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <a href="<?php echo e(route('yer-sotuvlar.list', ['auksonda_turgan' => 'true', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']])); ?>"
                                            class="text-blue-700 hover:text-blue-900 hover:underline"><?php echo e($statistics['jami']['auksonda']['soni']); ?></a>
                                    </td>

                                    <!-- Col 33: Auksonda maydoni -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['auksonda']['maydoni'], 2)); ?></td>

                                    <!-- Col 34: Auksonda boshlangich -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['auksonda']['boshlangich_narx'] / 1000000000, 1)); ?>

                                    </td>

                                    <!-- Col 35: Auksonda sotilgan -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format($statistics['jami']['auksonda']['sotilgan_narx'] / 1000000000, 1)); ?>

                                    </td>
                                    <!-- JAMI - Col 36: Mulk qabul soni (ALL lots) -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <a href="<?php echo e(route('yer-sotuvlar.list', ['holat' => 'Ishtirokchi roziligini kutish jarayonida', 'asos' => 'ПФ-135', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']])); ?>"
                                            class="text-blue-700 hover:text-blue-900 hover:underline">
                                            <?php echo e($statistics['jami']['mulk_qabul']['total_records'] ?? 0); ?>

                                        </a>
                                    </td>

                                    <!-- JAMI - Col 37: Mulk qabul mablagh (only муддатли эмас amounts) -->
                                    <td class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                        <?php echo e(number_format(($statistics['jami']['mulk_qabul']['total_auksion_mablagh'] ?? 0) / 1000000000, 1)); ?>

                                    </td>
                                </tr>

                                <!-- TUMANLAR ROWS -->
                                <?php $__currentLoopData = $statistics['tumanlar']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tuman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr
                                        class="hover:bg-blue-50 transition-colors duration-150 <?php echo e($index % 2 == 0 ? 'bg-white' : 'bg-slate-50'); ?>">
                                        <!-- Col 0: Row number -->
                                        <td
                                            class="sticky-col border border-slate-400 px-3 py-3 text-center align-middle font-medium text-slate-700">
                                            <?php echo e($index + 1); ?></td>

                                        <!-- Col 1: Tuman name -->
                                        <td
                                            class="sticky-col-2 border border-slate-400 px-3 py-3 align-middle font-semibold text-slate-800">
                                            <?php echo e($tuman['tuman']); ?></td>

                                        <!-- Col 2: Jami soni -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php if($tuman['jami']['soni'] > 0): ?>
                                                <a href="<?php echo e(route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'include_auksonda' => 'true', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']])); ?>"
                                                    class="text-blue-600 hover:text-blue-800 hover:underline font-medium"><?php echo e($tuman['jami']['soni']); ?></a>
                                            <?php else: ?>
                                                0
                                            <?php endif; ?>
                                        </td>

                                        <!-- Col 3: Jami maydoni -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['jami']['maydoni'], 2)); ?></td>

                                        <!-- Col 4: Jami boshlangich -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['jami']['boshlangich_narx'] / 1000000000, 1)); ?></td>

                                        <!-- Col 5: Jami sotilgan -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['jami']['sotilgan_narx'] / 1000000000, 1)); ?></td>

                                        <!-- Col 6: Jami chegirma -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['jami']['chegirma'] / 1000000000, 1)); ?></td>

                                        <!-- Col 7: Jami auksion harajat -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['jami']['auksion_harajati'] / 1000000000, 1)); ?></td>

                                        <!-- Col 8: Jami tushadigan -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['jami']['tushadigan_mablagh'] / 1000000000, 1)); ?></td>

                                        <!-- Col 9: Jami tushgan -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-blue-700 font-medium">
                                            <?php echo e(number_format(($tuman['biryola_fakt'] + ($tuman['bolib_tushgan_all'] ?? 0)) / 1000000000, 1)); ?></td>

                                        <!-- Col 10: Jami qoldiq -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-red-700 font-medium">
                                            <?php echo e(number_format(($tuman['jami']['tushadigan_mablagh'] - ($tuman['biryola_fakt'] + ($tuman['bolib_tushgan_all'] ?? 0))) / 1000000000, 1)); ?>

                                        </td>

                                        <!-- Col 11: Jami foiz -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e($tuman['jami']['tushadigan_mablagh'] > 0 ? number_format((($tuman['biryola_fakt'] + ($tuman['bolib_tushgan_all'] ?? 0)) / $tuman['jami']['tushadigan_mablagh']) * 100, 1) : 0); ?>%
                                        </td>

                                        <!-- Col 12: Biryola soni -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php if($tuman['bir_yola']['soni'] > 0): ?>
                                                <a href="<?php echo e(route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли эмас', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']])); ?>"
                                                    class="text-blue-600 hover:text-blue-800 hover:underline font-medium"><?php echo e($tuman['bir_yola']['soni']); ?></a>
                                            <?php else: ?>
                                                0
                                            <?php endif; ?>
                                        </td>

                                        <!-- Col 13: Biryola maydoni -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['bir_yola']['maydoni'], 2)); ?></td>

                                        <!-- Col 14: Biryola boshlangich -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['bir_yola']['boshlangich_narx'] / 1000000000, 1)); ?>

                                        </td>

                                        <!-- Col 15: Biryola sotilgan -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['bir_yola']['sotilgan_narx'] / 1000000000, 1)); ?></td>

                                        <!-- Col 16: Biryola chegirma -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['bir_yola']['chegirma'] / 1000000000, 1)); ?></td>

                                        <!-- Col 17: Biryola auksion harajat -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['bir_yola']['auksion_harajati'] / 1000000000, 1)); ?>

                                        </td>

                                        <!-- Col 18: Biryola tushadigan -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['bir_yola']['tushadigan_mablagh'] / 1000000000, 1)); ?>

                                        </td>

                                        <!-- Col 19: Biryola tushgan -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-blue-700 font-medium">
                                            <?php echo e(number_format($tuman['biryola_fakt'] / 1000000000, 1)); ?></td>

                                        <!-- Col 20: Biryola qoldiq -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-red-700 font-medium">
                                            <?php echo e(number_format(($tuman['bir_yola']['tushadigan_mablagh'] - $tuman['biryola_fakt']) / 1000000000, 1)); ?>

                                        </td>

                                        <!-- Col 21: Biryola foiz -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e($tuman['bir_yola']['tushadigan_mablagh'] > 0 ? number_format(($tuman['biryola_fakt'] / $tuman['bir_yola']['tushadigan_mablagh']) * 100, 1) : 0); ?>%
                                        </td>

                                        <!-- Col 22: Bolib soni -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php if($tuman['bolib']['soni'] > 0): ?>
                                                <a href="<?php echo e(route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']])); ?>"
                                                    class="text-blue-600 hover:text-blue-800 hover:underline font-medium"><?php echo e($tuman['bolib']['soni']); ?></a>
                                            <?php else: ?>
                                                0
                                            <?php endif; ?>
                                        </td>

                                        <!-- Col 23: Bolib maydoni -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['bolib']['maydoni'], 2)); ?></td>

                                        <!-- Col 24: Bolib boshlangich -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['bolib']['boshlangich_narx'] / 1000000000, 1)); ?></td>
                                        <!-- Col 25: Bolib sotilgan -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['bolib']['sotilgan_narx'] / 1000000000, 1)); ?></td>

                                        <!-- Col 26: Bolib chegirma -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['bolib']['chegirma'] / 1000000000, 1)); ?></td>

                                        <!-- Col 27: Bolib auksion harajat -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['bolib']['auksion_harajati'] / 1000000000, 1)); ?></td>

                                        <!-- Col 28: Bolib tushadigan -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['bolib_tushadigan'] / 1000000000, 1)); ?></td>

                                        <!-- Col 29: Bolib tushgan -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-blue-700 font-medium">
                                            <?php echo e(number_format(($tuman['bolib_tushgan_all'] ?? 0) / 1000000000, 1)); ?></td>

                                        <!-- Col 30: Bolib qoldiq -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-red-700 font-medium">
                                            <?php echo e(number_format((($tuman['bolib_tushadigan'] ?? 0) - ($tuman['bolib_tushgan_all'] ?? 0)) / 1000000000, 1)); ?>

                                        </td>

                                        <!-- Col 31: Bolib foiz -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(($tuman['bolib_tushadigan'] ?? 0) > 0 ? number_format((($tuman['bolib_tushgan_all'] ?? 0) / $tuman['bolib_tushadigan']) * 100, 1) : 0); ?>%
                                        </td>

                                        <!-- Col 32: Auksonda soni -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php if($tuman['auksonda']['soni'] > 0): ?>
                                                <a href="<?php echo e(route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'auksonda_turgan' => 'true', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']])); ?>"
                                                    class="text-blue-600 hover:text-blue-800 hover:underline font-medium"><?php echo e($tuman['auksonda']['soni']); ?></a>
                                            <?php else: ?>
                                                0
                                            <?php endif; ?>
                                        </td>

                                        <!-- Col 33: Auksonda maydoni -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['auksonda']['maydoni'], 2)); ?></td>

                                        <!-- Col 34: Auksonda boshlangich -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['auksonda']['boshlangich_narx'] / 1000000000, 1)); ?>

                                        </td>

                                        <!-- Col 35: Auksonda sotilgan -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format($tuman['auksonda']['sotilgan_narx'] / 1000000000, 1)); ?></td>

                                        <!-- Col 36: Mulk qabul soni -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php if(($tuman['mulk_qabul']['total_records'] ?? 0) > 0): ?>
                                                <a href="<?php echo e(route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'holat' => 'Ishtirokchi roziligini kutish jarayonida', 'asos' => 'ПФ-135', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']])); ?>"
                                                    class="text-blue-600 hover:text-blue-800 hover:underline font-medium"><?php echo e($tuman['mulk_qabul']['total_records']); ?></a>
                                            <?php else: ?>
                                                0
                                            <?php endif; ?>
                                        </td>

                                        <!-- Col 37: Mulk qabul mablagh -->
                                        <td class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                            <?php echo e(number_format(($tuman['mulk_qabul']['total_auksion_mablagh'] ?? 0) / 1000000000, 1)); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-t-4 border-blue-600 mt-6">
                <div class="p-6 bg-gradient-to-br from-slate-50 to-blue-50">
                    <form method="GET" action="<?php echo e(route('yer-sotuvlar.index')); ?>">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Бошланғич санаси:</label>
                                <input type="date" name="auksion_sana_from"
                                    class="w-full px-4 py-3 border-2 border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                    value="<?php echo e(request('auksion_sana_from')); ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Тугаш санаси:</label>
                                <input type="date" name="auksion_sana_to"
                                    class="w-full px-4 py-3 border-2 border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                    value="<?php echo e(request('auksion_sana_to')); ?>">
                            </div>
                        </div>
                        <div class="flex gap-4 mt-6">
                            <button type="submit"
                                class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Қидириш
                            </button>
                            <a href="<?php echo e(route('yer-sotuvlar.index')); ?>"
                                class="flex-1 bg-gradient-to-r from-slate-500 to-slate-600 hover:from-slate-600 hover:to-slate-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Тозалаш
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .sticky-col {
            position: sticky;
            left: 0;
            z-index: 20;
            background-color: inherit;
        }

        .sticky-col-2 {
            position: sticky;
            left: 60px;
            z-index: 20;
            background-color: inherit;
        }

        .overflow-x-auto::-webkit-scrollbar {
            height: 12px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 6px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: linear-gradient(to right, #64748b, #475569);
            border-radius: 6px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to right, #475569, #334155);
        }

        @media print {

            .sticky-col,
            .sticky-col-2 {
                position: static;
            }

            body {
                background: white;
            }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\inves\OneDrive\Ishchi stol\yer-uchastkalar\resources\views/yer-sotuvlar/statistics.blade.php ENDPATH**/ ?>