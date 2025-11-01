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
                            <!-- HEADER ROW 1: Main Section Headers -->
                            <tr id="header_row_1" style="background: #eff6ff !important;">
                                <!-- Column 0: Т/р -->
                                <th id="th_col_tr"
                                    name="col_tr"
                                    rowspan="5"
                                    class="sticky-col border border-slate-400 px-4 py-4 text-center align-middle font-bold text-slate-800"
                                    style="min-width: 60px;">
                                    Т/р
                                </th>

                                <!-- Column 1: Ҳудудлар -->
                                <th id="th_col_hudud"
                                    name="col_hudud"
                                    rowspan="5"
                                    class="sticky-col-2 border border-slate-400 px-4 py-4 text-center align-middle font-bold text-slate-800"
                                    style="min-width: 200px;">
                                    Ҳудудлар
                                </th>

                                <!-- Columns 2-8: Сотилган ер участкалари -->
                                <th id="th_section_sotilgan"
                                    name="section_sotilgan_yer"
                                    colspan="7"
                                    class="border border-slate-400 px-4 py-3 text-center font-bold text-slate-800">
                                    Сотилган ер участкалари
                                </th>

                                <!-- Columns 9-22: шундан (Бир йўла + Бўлиб тўлаш) -->
                                <th id="th_section_shundan"
                                    name="section_shundan"
                                    colspan="14"
                                    class="border border-slate-400 px-4 py-3 text-center font-bold text-slate-800">
                                    шундан
                                </th>

                                <!-- Columns 23-26: Аукционда турган ерлар -->
                                <th id="th_section_auksion"
                                    name="section_auksion_turgan"
                                    colspan="4"
                                    class="border border-slate-400 px-4 py-3 text-center font-bold text-slate-800">
                                    Аукционда сотилган ва савдо натижасини расмийлаштишда турган ерлар
                                </th>

                                <!-- Columns 27-28: Мулк қабул қилмаган -->
                                <th id="th_section_mulk"
                                    name="section_mulk_qabul"
                                    colspan="2"
                                    class="border border-slate-400 px-4 py-3 text-center font-bold text-slate-800">
                                    Мулкни қабул қилиб олиш тугмаси босилмаган ерлар
                                </th>
                            </tr>

                            <!-- HEADER ROW 2: Sub Section Headers -->
                            <tr id="header_row_2" style="background: #eff6ff !important;">
                                <!-- Columns 2-5: Basic info under Sotilgan yer -->
                                <th id="th_col_jami_soni"
                                    name="col_jami_soni"
                                    rowspan="4"
                                    class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                    style="min-width: 70px;">
                                    Сони
                                </th>
                                <th id="th_col_jami_maydoni"
                                    name="col_jami_maydoni"
                                    rowspan="4"
                                    class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                    style="min-width: 90px;">
                                    Майдони<br>(га)
                                </th>
                                <th id="th_col_jami_boshlangich"
                                    name="col_jami_boshlangich"
                                    rowspan="4"
                                    class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                    style="min-width: 110px;">
                                    Бошланғич<br>нархи<br>(млрд сўм)
                                </th>
                                <th id="th_col_jami_sotilgan"
                                    name="col_jami_sotilgan"
                                    rowspan="4"
                                    class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                    style="min-width: 110px;">
                                    Сотилган<br>нархи<br>(млрд сўм)
                                </th>

                                <!-- Columns 6-7: шундан under Sotilgan yer -->
                                <th id="th_subsection_jami_shundan"
                                    name="subsection_jami_shundan"
                                    colspan="2"
                                    rowspan="2"
                                    class="border border-slate-400 px-3 py-3 text-center font-semibold text-slate-700 text-sm">
                                    шундан
                                </th>

                                <!-- Column 8: Жами тушган -->
                                <th id="th_col_jami_tushgan"
                                    name="col_jami_tushgan"
                                    rowspan="4"
                                    class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                    style="min-width: 130px;">
                                    Жами тушган маблағ<br>(млрд сўм)
                                </th>

                                <!-- Columns 9-15: Бир йўла тўлаш -->
                                <th id="th_subsection_biryola"
                                    name="subsection_biryola_tolash"
                                    colspan="7"
                                    class="border border-slate-400 px-3 py-3 text-center font-semibold text-slate-700 text-sm">
                                    Бир йўла тўлаш шарти билан сотилган
                                </th>

                                <!-- Columns 16-22: Бўлиб тўлаш -->
                                <th id="th_subsection_bolib"
                                    name="subsection_bolib_tolash"
                                    colspan="7"
                                    class="border border-slate-400 px-3 py-3 text-center font-semibold text-slate-700 text-sm">
                                    Нархини бўлиб тўлаш шарти билан сотилган
                                </th>

                                <!-- Columns 23-26: Auksion details -->
                                <th id="th_col_auksion_soni"
                                    name="col_auksion_soni"
                                    rowspan="4"
                                    class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                    style="min-width: 70px;">
                                    сони
                                </th>
                                <th id="th_col_auksion_maydoni"
                                    name="col_auksion_maydoni"
                                    rowspan="4"
                                    class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                    style="min-width: 90px;">
                                    майдони<br>(га)
                                </th>
                                <th id="th_col_auksion_boshlangich"
                                    name="col_auksion_boshlangich"
                                    rowspan="4"
                                    class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                    style="min-width: 110px;">
                                    бошланғич<br>нархи<br>(млрд сўм)
                                </th>
                                <th id="th_col_auksion_sotilgan"
                                    name="col_auksion_sotilgan"
                                    rowspan="4"
                                    class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                    style="min-width: 110px;">
                                    сотилган<br>нархи<br>(млрд сўм)
                                </th>

                                <!-- Columns 27-28: Mulk qabul details -->
                                <th id="th_col_mulk_soni"
                                    name="col_mulk_soni"
                                    rowspan="4"
                                    class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                    style="min-width: 70px;">
                                    сони
                                </th>
                                <th id="th_col_mulk_mablagh"
                                    name="col_mulk_mablagh"
                                    rowspan="4"
                                    class="border border-slate-400 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm"
                                    style="min-width: 130px;">
                                    аукционда турган маблағ<br>(млрд сўм)
                                </th>
                            </tr>

                            <!-- HEADER ROW 3: Detail Headers -->
                            <tr id="header_row_3" style="background: #eff6ff !important;">
                                <!-- Columns 9-12: Bir yo'la basic fields -->
                                <th id="th_col_biryola_soni"
                                    name="col_biryola_soni"
                                    rowspan="3"
                                    class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                    style="min-width: 60px;">
                                    сони
                                </th>
                                <th id="th_col_biryola_maydoni"
                                    name="col_biryola_maydoni"
                                    rowspan="3"
                                    class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                    style="min-width: 80px;">
                                    майдони<br>(га)
                                </th>
                                <th id="th_col_biryola_boshlangich"
                                    name="col_biryola_boshlangich"
                                    rowspan="3"
                                    class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                    style="min-width: 100px;">
                                    бошланғич<br>нархи<br>(млрд сўм)
                                </th>
                                <th id="th_col_biryola_sotilgan"
                                    name="col_biryola_sotilgan"
                                    rowspan="3"
                                    class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                    style="min-width: 100px;">
                                    сотилган<br>нархи<br>(млрд сўм)
                                </th>

                                <!-- Columns 13-15: шундан under Bir yo'la -->
                                <th id="th_subsection_biryola_shundan"
                                    name="subsection_biryola_shundan"
                                    colspan="3"
                                    class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs">
                                    шундан
                                </th>

                                <!-- Columns 16-19: Bo'lib basic fields -->
                                <th id="th_col_bolib_soni"
                                    name="col_bolib_soni"
                                    rowspan="3"
                                    class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                    style="min-width: 60px;">
                                    сони
                                </th>
                                <th id="th_col_bolib_maydoni"
                                    name="col_bolib_maydoni"
                                    rowspan="3"
                                    class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                    style="min-width: 80px;">
                                    майдони<br>(га)
                                </th>
                                <th id="th_col_bolib_boshlangich"
                                    name="col_bolib_boshlangich"
                                    rowspan="3"
                                    class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                    style="min-width: 100px;">
                                    бошланғич<br>нархи<br>(млрд сўм)
                                </th>
                                <th id="th_col_bolib_sotilgan"
                                    name="col_bolib_sotilgan"
                                    rowspan="3"
                                    class="border border-slate-400 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs"
                                    style="min-width: 100px;">
                                    сотилган<br>нархи<br>(млрд сўм)
                                </th>

                                <!-- Columns 20-22: шундан under Bo'lib -->
                                <th id="th_subsection_bolib_shundan"
                                    name="subsection_bolib_shundan"
                                    colspan="3"
                                    class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs">
                                    шундан
                                </th>
                            </tr>

                            <!-- HEADER ROW 4: Final Detail Level 1 -->
                            <tr id="header_row_4" style="background: #eff6ff !important;">
                                <!-- Columns 6-7: Details under Jami shundan -->
                                <th id="th_col_jami_chegirma"
                                    name="col_jami_chegirma"
                                    class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                    style="min-width: 100px;">
                                    Чегирма<br>қиймати<br>(млрд сўм)
                                </th>
                                <th id="th_col_jami_tushadigan"
                                    name="col_jami_tushadigan"
                                    class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                    style="min-width: 120px;">
                                    Сотилган ер тўлови бўйича<br>тушадиган маблағ<br>(млрд сўм)
                                </th>

                                <!-- Columns 13-15: Details under Bir yo'la shundan -->
                                <th id="th_col_biryola_chegirma"
                                    name="col_biryola_chegirma"
                                    class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                    style="min-width: 90px;">
                                    чегирма<br>қиймати<br>(млрд сўм)
                                </th>
                                <th id="th_col_biryola_tushadigan"
                                    name="col_biryola_tushadigan"
                                    class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                    style="min-width: 120px;">
                                    сотилган ер<br>тўлови бўйича<br>тушадиган<br>маблағ<br>(млрд сўм)
                                </th>
                                <th id="th_col_biryola_tushgan"
                                    name="col_biryola_tushgan"
                                    class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                    style="min-width: 100px;">
                                    тушган<br>маблағ<br>(млрд сўм)
                                </th>

                                <!-- Columns 20-22: Details under Bo'lib shundan -->
                                <th id="th_col_bolib_chegirma"
                                    name="col_bolib_chegirma"
                                    class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                    style="min-width: 90px;">
                                    чегирма<br>қиймати<br>(млрд сўм)
                                </th>
                                <th id="th_col_bolib_tushadigan"
                                    name="col_bolib_tushadigan"
                                    class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                    style="min-width: 120px;">
                                    сотилган ер<br>тўлови бўйича<br>тушадиган<br>маблағ<br>(млрд сўм)
                                </th>
                                <th id="th_col_bolib_tushgan"
                                    name="col_bolib_tushgan"
                                    class="border border-slate-400 px-2 py-2 text-center font-semibold text-slate-700 text-xs"
                                    style="min-width: 100px;">
                                    тушган<br>маблағ<br>(млрд сўм)
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white">
                            <!-- JAMI ROW -->
                            <tr id="row_jami" name="row_total" class="bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100 border-y-2 border-amber-400">
                                <!-- Column 0-1: Label -->
                                <td id="td_jami_label"
                                    name="td_jami_label"
                                    colspan="2"
                                    class="sticky-col border border-slate-400 px-4 py-4 text-center align-middle font-bold text-slate-900 text-base uppercase bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100">
                                    ЖАМИ:
                                </td>

                                <!-- Column 2: Jami soni -->
                                <td id="td_jami_soni"
                                    name="td_jami_soni"
                                    data-column="col_jami_soni"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <a href="<?php echo e(route('yer-sotuvlar.list')); ?>" class="text-blue-700 hover:text-blue-900 hover:underline transition-all">
                                        <?php echo e($statistics['jami']['jami']['soni']); ?>

                                    </a>
                                </td>

                                <!-- Column 3: Jami maydoni -->
                                <td id="td_jami_maydoni"
                                    name="td_jami_maydoni"
                                    data-column="col_jami_maydoni"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['jami']['maydoni'], 2)); ?>

                                </td>

                                <!-- Column 4: Jami boshlangich narx -->
                                <td id="td_jami_boshlangich"
                                    name="td_jami_boshlangich"
                                    data-column="col_jami_boshlangich"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['jami']['boshlangich_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 5: Jami sotilgan narx -->
                                <td id="td_jami_sotilgan"
                                    name="td_jami_sotilgan"
                                    data-column="col_jami_sotilgan"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['jami']['sotilgan_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 6: Jami chegirma -->
                                <td id="td_jami_chegirma"
                                    name="td_jami_chegirma"
                                    data-column="col_jami_chegirma"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['jami']['chegirma'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 7: Jami tushadigan mablagh -->
                                <td id="td_jami_tushadigan"
                                    name="td_jami_tushadigan"
                                    data-column="col_jami_tushadigan"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['jami']['tushadigan_mablagh'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 8: Jami tushgan mablagh -->
                                <td id="td_jami_tushgan"
                                    name="td_jami_tushgan"
                                    data-column="col_jami_tushgan"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-blue-900 bg-blue-50">
                                    <?php echo e(number_format($statistics['jami']['jami_tushgan_yigindi'] / 1000000000, 1)); ?>

                                </td>
                                <!-- Column 9: Bir yo'la soni -->
                                <td id="td_biryola_soni"
                                    name="td_biryola_soni"
                                    data-column="col_biryola_soni"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <a href="<?php echo e(route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли эмас'])); ?>" class="text-blue-700 hover:text-blue-900 hover:underline transition-all">
                                        <?php echo e($statistics['jami']['bir_yola']['soni']); ?>

                                    </a>
                                </td>

                                <!-- Column 10: Bir yo'la maydoni -->
                                <td id="td_biryola_maydoni"
                                    name="td_biryola_maydoni"
                                    data-column="col_biryola_maydoni"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['bir_yola']['maydoni'], 2)); ?>

                                </td>

                                <!-- Column 11: Bir yo'la boshlangich -->
                                <td id="td_biryola_boshlangich"
                                    name="td_biryola_boshlangich"
                                    data-column="col_biryola_boshlangich"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['bir_yola']['boshlangich_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 12: Bir yo'la sotilgan -->
                                <td id="td_biryola_sotilgan"
                                    name="td_biryola_sotilgan"
                                    data-column="col_biryola_sotilgan"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['bir_yola']['sotilgan_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 13: Bir yo'la chegirma -->
                                <td id="td_biryola_chegirma"
                                    name="td_biryola_chegirma"
                                    data-column="col_biryola_chegirma"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['bir_yola']['chegirma'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 14: Bir yo'la tushadigan -->
                                <td id="td_biryola_tushadigan"
                                    name="td_biryola_tushadigan"
                                    data-column="col_biryola_tushadigan"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['bir_yola']['tushadigan_mablagh'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 15: Bir yo'la tushgan -->
                                <td id="td_biryola_tushgan"
                                    name="td_biryola_tushgan"
                                    data-column="col_biryola_tushgan"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-blue-900 bg-blue-50">
                                    <?php echo e(number_format($statistics['jami']['biryola_fakt'] / 1000000000, 1)); ?>

                                </td>


                                <!-- Column 16: Bo'lib soni -->
                                <td id="td_bolib_soni"
                                    name="td_bolib_soni"
                                    data-column="col_bolib_soni"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <a href="<?php echo e(route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли'])); ?>" class="text-blue-700 hover:text-blue-900 hover:underline transition-all">
                                        <?php echo e($statistics['jami']['bolib']['soni']); ?>

                                    </a>
                                </td>

                                <!-- Column 17: Bo'lib maydoni -->
                                <td id="td_bolib_maydoni"
                                    name="td_bolib_maydoni"
                                    data-column="col_bolib_maydoni"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['bolib']['maydoni'], 2)); ?>

                                </td>

                                <!-- Column 18: Bo'lib boshlangich -->
                                <td id="td_bolib_boshlangich"
                                    name="td_bolib_boshlangich"
                                    data-column="col_bolib_boshlangich"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['bolib']['boshlangich_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 19: Bo'lib sotilgan -->
                                <td id="td_bolib_sotilgan"
                                    name="td_bolib_sotilgan"
                                    data-column="col_bolib_sotilgan"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['bolib']['sotilgan_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 20: Bo'lib chegirma -->
                                <td id="td_bolib_chegirma"
                                    name="td_bolib_chegirma"
                                    data-column="col_bolib_chegirma"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['bolib']['chegirma'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 21: Bo'lib tushadigan -->
                                <td id="td_bolib_tushadigan"
                                    name="td_bolib_tushadigan"
                                    data-column="col_bolib_tushadigan"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['bolib_tushadigan'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 22: Bo'lib tushgan -->
                                <td id="td_bolib_tushgan"
                                    name="td_bolib_tushgan"
                                    data-column="col_bolib_tushgan"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-blue-900 bg-blue-50">
                                    <?php echo e(number_format($statistics['jami']['bolib_tushgan'] / 1000000000, 1)); ?>

                                </td>


                                <!-- Column 23: Auksion soni -->
                                <td id="td_auksion_soni"
                                    name="td_auksion_soni"
                                    data-column="col_auksion_soni"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <a href="<?php echo e(route('yer-sotuvlar.list', ['auksonda_turgan' => 'true'])); ?>" class="text-blue-700 hover:text-blue-900 hover:underline transition-all">
                                        <?php echo e($statistics['jami']['auksonda']['soni']); ?>

                                    </a>
                                </td>

                                <!-- Column 24: Auksion maydoni -->
                                <td id="td_auksion_maydoni"
                                    name="td_auksion_maydoni"
                                    data-column="col_auksion_maydoni"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['auksonda']['maydoni'], 2)); ?>

                                </td>

                                <!-- Column 25: Auksion boshlangich -->
                                <td id="td_auksion_boshlangich"
                                    name="td_auksion_boshlangich"
                                    data-column="col_auksion_boshlangich"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['auksonda']['boshlangich_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 26: Auksion sotilgan -->
                                <td id="td_auksion_sotilgan"
                                    name="td_auksion_sotilgan"
                                    data-column="col_auksion_sotilgan"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['auksonda']['sotilgan_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 27: Mulk qabul soni -->
                                <td id="td_mulk_soni"
                                    name="td_mulk_soni"
                                    data-column="col_mulk_soni"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <a href="<?php echo e(route('yer-sotuvlar.list', ['holat' => 'Ishtirokchi roziligini kutish jarayonida (34)'])); ?>" class="text-blue-700 hover:text-blue-900 hover:underline transition-all">
                                        <?php echo e($statistics['jami']['mulk_qabul']['soni']); ?>

                                    </a>
                                </td>

                                <!-- Column 28: Mulk qabul mablagh -->
                                <td id="td_mulk_mablagh"
                                    name="td_mulk_mablagh"
                                    data-column="col_mulk_mablagh"
                                    class="border border-slate-400 px-3 py-3 text-right font-bold text-slate-900">
                                    <?php echo e(number_format($statistics['jami']['mulk_qabul']['auksion_mablagh'] / 1000000000, 1)); ?> zzz
                                </td>
                            </tr>

                            <!-- TUMANLAR ROWS -->
                            <?php $__currentLoopData = $statistics['tumanlar']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tuman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr id="row_tuman_<?php echo e($index); ?>"
                                name="row_tuman_<?php echo e(Str::slug($tuman['tuman'])); ?>"
                                data-tuman="<?php echo e($tuman['tuman']); ?>"
                                class="hover:bg-blue-50 transition-colors duration-150 <?php echo e($index % 2 == 0 ? 'bg-white' : 'bg-slate-50'); ?>">

                                <!-- Column 0: T/r -->
                                <td id="td_tuman_<?php echo e($index); ?>_tr"
                                    name="td_tuman_<?php echo e($index); ?>_tr"
                                    data-column="col_tr"
                                    class="sticky-col border border-slate-400 px-3 py-3 text-center align-middle font-medium text-slate-700">
                                    <?php echo e($index + 1); ?>

                                </td>

                                <!-- Column 1: Hudud -->
                                <td id="td_tuman_<?php echo e($index); ?>_hudud"
                                    name="td_tuman_<?php echo e($index); ?>_hudud"
                                    data-column="col_hudud"
                                    class="sticky-col-2 border border-slate-400 px-3 py-3 align-middle font-semibold text-slate-800">
                                    <?php echo e($tuman['tuman']); ?>

                                </td>

                                <!-- Column 2: Jami soni -->
                                <td id="td_tuman_<?php echo e($index); ?>_jami_soni"
                                    name="td_tuman_<?php echo e($index); ?>_jami_soni"
                                    data-column="col_jami_soni"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php if($tuman['jami']['soni'] > 0): ?>
                                    <a href="<?php echo e(route('yer-sotuvlar.list', ['tuman' => $tuman['tuman']])); ?>" class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition-all">
                                        <?php echo e($tuman['jami']['soni']); ?>

                                    </a>
                                    <?php else: ?>
                                    0
                                    <?php endif; ?>
                                </td>

                                <!-- Column 3: Jami maydoni -->
                                <td id="td_tuman_<?php echo e($index); ?>_jami_maydoni"
                                    name="td_tuman_<?php echo e($index); ?>_jami_maydoni"
                                    data-column="col_jami_maydoni"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['jami']['maydoni'], 2)); ?>

                                </td>

                                <!-- Column 4: Jami boshlangich -->
                                <td id="td_tuman_<?php echo e($index); ?>_jami_boshlangich"
                                    name="td_tuman_<?php echo e($index); ?>_jami_boshlangich"
                                    data-column="col_jami_boshlangich"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['jami']['boshlangich_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 5: Jami sotilgan -->
                                <td id="td_tuman_<?php echo e($index); ?>_jami_sotilgan"
                                    name="td_tuman_<?php echo e($index); ?>_jami_sotilgan"
                                    data-column="col_jami_sotilgan"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['jami']['sotilgan_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 6: Jami chegirma -->
                                <td id="td_tuman_<?php echo e($index); ?>_jami_chegirma"
                                    name="td_tuman_<?php echo e($index); ?>_jami_chegirma"
                                    data-column="col_jami_chegirma"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['jami']['chegirma'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 7: Jami tushadigan -->
                                <td id="td_tuman_<?php echo e($index); ?>_jami_tushadigan"
                                    name="td_tuman_<?php echo e($index); ?>_jami_tushadigan"
                                    data-column="col_jami_tushadigan"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['jami']['tushadigan_mablagh'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 8: Jami tushgan -->
                                <td id="td_tuman_<?php echo e($index); ?>_jami_tushgan"
                                    name="td_tuman_<?php echo e($index); ?>_jami_tushgan"
                                    data-column="col_jami_tushgan"
                                    class="border border-slate-400 px-3 py-3 text-right text-blue-700 font-medium bg-blue-50">
                                    <?php echo e(number_format($tuman['jami_tushgan_yigindi'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 9: Bir yo'la soni -->
                                <td id="td_tuman_<?php echo e($index); ?>_biryola_soni"
                                    name="td_tuman_<?php echo e($index); ?>_biryola_soni"
                                    data-column="col_biryola_soni"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php if($tuman['bir_yola']['soni'] > 0): ?>
                                    <a href="<?php echo e(route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли эмас'])); ?>" class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition-all">
                                        <?php echo e($tuman['bir_yola']['soni']); ?>

                                    </a>
                                    <?php else: ?>
                                    0
                                    <?php endif; ?>
                                </td>

                                <!-- Column 10: Bir yo'la maydoni -->
                                <td id="td_tuman_<?php echo e($index); ?>_biryola_maydoni"
                                    name="td_tuman_<?php echo e($index); ?>_biryola_maydoni"
                                    data-column="col_biryola_maydoni"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['bir_yola']['maydoni'], 2)); ?>

                                </td>

                                <!-- Column 11: Bir yo'la boshlangich -->
                                <td id="td_tuman_<?php echo e($index); ?>_biryola_boshlangich"
                                    name="td_tuman_<?php echo e($index); ?>_biryola_boshlangich"
                                    data-column="col_biryola_boshlangich"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['bir_yola']['boshlangich_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 12: Bir yo'la sotilgan -->
                                <td id="td_tuman_<?php echo e($index); ?>_biryola_sotilgan"
                                    name="td_tuman_<?php echo e($index); ?>_biryola_sotilgan"
                                    data-column="col_biryola_sotilgan"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['bir_yola']['sotilgan_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 13: Bir yo'la chegirma -->
                                <td id="td_tuman_<?php echo e($index); ?>_biryola_chegirma"
                                    name="td_tuman_<?php echo e($index); ?>_biryola_chegirma"
                                    data-column="col_biryola_chegirma"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['bir_yola']['chegirma'] / 1000000000, 1)); ?>

                                </td>


                                <!-- Column 14: Bir yo'la tushadigan -->
                                <td id="td_tuman_<?php echo e($index); ?>_biryola_tushadigan"
                                    name="td_tuman_<?php echo e($index); ?>_biryola_tushadigan"
                                    data-column="col_biryola_tushadigan"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['bir_yola']['tushadigan_mablagh'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 15: Bir yo'la tushgan -->
                                <td id="td_tuman_<?php echo e($index); ?>_biryola_tushgan"
                                    name="td_tuman_<?php echo e($index); ?>_biryola_tushgan"
                                    data-column="col_biryola_tushgan"
                                    class="border border-slate-400 px-3 py-3 text-right text-blue-700 font-medium bg-blue-50">
                                    <?php echo e(number_format($tuman['biryola_fakt'] / 1000000000, 1)); ?>

                                </td>


                                <!-- Column 16: Bo'lib soni -->
                                <td id="td_tuman_<?php echo e($index); ?>_bolib_soni"
                                    name="td_tuman_<?php echo e($index); ?>_bolib_soni"
                                    data-column="col_bolib_soni"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php if($tuman['bolib']['soni'] > 0): ?>
                                    <a href="<?php echo e(route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли'])); ?>" class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition-all">
                                        <?php echo e($tuman['bolib']['soni']); ?>

                                    </a>
                                    <?php else: ?>
                                    0
                                    <?php endif; ?>
                                </td>

                                <!-- Column 17: Bo'lib maydoni -->
                                <td id="td_tuman_<?php echo e($index); ?>_bolib_maydoni"
                                    name="td_tuman_<?php echo e($index); ?>_bolib_maydoni"
                                    data-column="col_bolib_maydoni"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['bolib']['maydoni'], 2)); ?>

                                </td>

                                <!-- Column 18: Bo'lib boshlangich -->
                                <td id="td_tuman_<?php echo e($index); ?>_bolib_boshlangich"
                                    name="td_tuman_<?php echo e($index); ?>_bolib_boshlangich"
                                    data-column="col_bolib_boshlangich"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['bolib']['boshlangich_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 19: Bo'lib sotilgan -->
                                <td id="td_tuman_<?php echo e($index); ?>_bolib_sotilgan"
                                    name="td_tuman_<?php echo e($index); ?>_bolib_sotilgan"
                                    data-column="col_bolib_sotilgan"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['bolib']['sotilgan_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 20: Bo'lib chegirma -->
                                <td id="td_tuman_<?php echo e($index); ?>_bolib_chegirma"
                                    name="td_tuman_<?php echo e($index); ?>_bolib_chegirma"
                                    data-column="col_bolib_chegirma"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['bolib']['chegirma'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 21: Bo'lib tushadigan -->
                                <td id="td_tuman_<?php echo e($index); ?>_bolib_tushadigan"
                                    name="td_tuman_<?php echo e($index); ?>_bolib_tushadigan"
                                    data-column="col_bolib_tushadigan"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['bolib_tushadigan'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 22: Bo'lib tushgan -->
                                <td id="td_tuman_<?php echo e($index); ?>_bolib_tushgan"
                                    name="td_tuman_<?php echo e($index); ?>_bolib_tushgan"
                                    data-column="col_bolib_tushgan"
                                    class="border border-slate-400 px-3 py-3 text-right text-blue-700 font-medium bg-blue-50">
                                    <?php echo e(number_format($tuman['bolib_tushgan'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 23: Auksion soni -->
                                <td id="td_tuman_<?php echo e($index); ?>_auksion_soni"
                                    name="td_tuman_<?php echo e($index); ?>_auksion_soni"
                                    data-column="col_auksion_soni"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php if($tuman['auksonda']['soni'] > 0): ?>
                                    <a href="<?php echo e(route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'auksonda_turgan' => 'true'])); ?>" class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition-all">
                                        <?php echo e($tuman['auksonda']['soni']); ?>

                                    </a>
                                    <?php else: ?>
                                    0
                                    <?php endif; ?>
                                </td>

                                <!-- Column 24: Auksion maydoni -->
                                <td id="td_tuman_<?php echo e($index); ?>_auksion_maydoni"
                                    name="td_tuman_<?php echo e($index); ?>_auksion_maydoni"
                                    data-column="col_auksion_maydoni"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['auksonda']['maydoni'], 2)); ?>

                                </td>

                                <!-- Column 25: Auksion boshlangich -->
                                <td id="td_tuman_<?php echo e($index); ?>_auksion_boshlangich"
                                    name="td_tuman_<?php echo e($index); ?>_auksion_boshlangich"
                                    data-column="col_auksion_boshlangich"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['auksonda']['boshlangich_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 26: Auksion sotilgan -->
                                <td id="td_tuman_<?php echo e($index); ?>_auksion_sotilgan"
                                    name="td_tuman_<?php echo e($index); ?>_auksion_sotilgan"
                                    data-column="col_auksion_sotilgan"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['auksonda']['sotilgan_narx'] / 1000000000, 1)); ?>

                                </td>

                                <!-- Column 27: Mulk qabul soni -->
                                <td id="td_tuman_<?php echo e($index); ?>_mulk_soni"
                                    name="td_tuman_<?php echo e($index); ?>_mulk_soni"
                                    data-column="col_mulk_soni"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php if($tuman['mulk_qabul']['soni'] > 0): ?>
                                    <a href="<?php echo e(route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'holat' => 'Ishtirokchi roziligini kutish jarayonida (34)'])); ?>" class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition-all">
                                        <?php echo e($tuman['mulk_qabul']['soni']); ?>

                                    </a>
                                    <?php else: ?>
                                    0
                                    <?php endif; ?>
                                </td>

                                <!-- Column 28: Mulk qabul mablagh -->
                                <td id="td_tuman_<?php echo e($index); ?>_mulk_mablagh"
                                    name="td_tuman_<?php echo e($index); ?>_mulk_mablagh"
                                    data-column="col_mulk_mablagh"
                                    class="border border-slate-400 px-3 py-3 text-right text-slate-700">
                                    <?php echo e(number_format($tuman['mulk_qabul']['auksion_mablagh'] / 1000000000, 1)); ?>

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
                            <input type="date" name="auksion_sana_from" class="w-full px-4 py-3 border-2 border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" value="<?php echo e(request('auksion_sana_from')); ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Тугаш санаси:</label>
                            <input type="date" name="auksion_sana_to" class="w-full px-4 py-3 border-2 border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" value="<?php echo e(request('auksion_sana_to')); ?>">
                        </div>
                    </div>
                    <div class="flex gap-4 mt-6">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Қидириш
                        </button>
                        <a href="<?php echo e(route('yer-sotuvlar.index')); ?>" class="flex-1 bg-gradient-to-r from-slate-500 to-slate-600 hover:from-slate-600 hover:to-slate-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
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