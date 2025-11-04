<?php $__env->startSection('title', 'Ер Участка Маълумотларини Таҳрирлаш - ' . $yer->lot_raqami); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 mb-2">
                    Ер Участка Маълумотларини Таҳрирлаш
                </h1>
                <p class="text-slate-600">Лот рақами: <span class="font-semibold text-blue-600"><?php echo e($yer->lot_raqami); ?></span></p>
            </div>
            <a href="<?php echo e(route('yer-sotuvlar.show', $yer->lot_raqami)); ?>"
               class="flex items-center space-x-2 px-5 py-2.5 bg-slate-600 hover:bg-slate-700 text-white rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Орқага</span>
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="<?php echo e(route('yer-sotuvlar.update', $yer->lot_raqami)); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <!-- Асосий маълумотлар -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center space-x-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Асосий маълумотлар</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Lot Raqami -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Лот рақами *</label>
                    <input type="text" name="lot_raqami" value="<?php echo e(old('lot_raqami', $yer->lot_raqami)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                           required readonly>
                </div>

                <!-- Tuman -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Туман</label>
                    <input type="text" name="tuman" value="<?php echo e(old('tuman', $yer->tuman)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- MFY -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">МФЙ</label>
                    <input type="text" name="mfy" value="<?php echo e(old('mfy', $yer->mfy)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Manzil -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Манзил</label>
                    <input type="text" name="manzil" value="<?php echo e(old('manzil', $yer->manzil)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Unikal Raqam -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Уникал рақам</label>
                    <input type="text" name="unikal_raqam" value="<?php echo e(old('unikal_raqam', $yer->unikal_raqam)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Zona -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Зона</label>
                    <input type="text" name="zona" value="<?php echo e(old('zona', $yer->zona)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Bosh Reja Zona -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Бош режа зонаси</label>
                    <input type="text" name="bosh_reja_zona" value="<?php echo e(old('bosh_reja_zona', $yer->bosh_reja_zona)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Yangi Ozbekiston -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Янги Ўзбекистон</label>
                    <input type="text" name="yangi_ozbekiston" value="<?php echo e(old('yangi_ozbekiston', $yer->yangi_ozbekiston)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Maydoni -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Майдони (га)</label>
                    <input type="number" step="0.0001" name="maydoni" value="<?php echo e(old('maydoni', $yer->maydoni)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Yil -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Йил</label>
                    <input type="number" name="yil" value="<?php echo e(old('yil', $yer->yil)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Lokatsiya -->
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Локация (Google Maps link)</label>
                    <textarea name="lokatsiya" rows="2"
                              class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"><?php echo e(old('lokatsiya', $yer->lokatsiya)); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Қурилиш маълумотлари -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center space-x-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Қурилиш маълумотлари</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Qurilish Turi 1 -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Қурилиш тури 1</label>
                    <input type="text" name="qurilish_turi_1" value="<?php echo e(old('qurilish_turi_1', $yer->qurilish_turi_1)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Qurilish Turi 2 -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Қурилиш тури 2</label>
                    <input type="text" name="qurilish_turi_2" value="<?php echo e(old('qurilish_turi_2', $yer->qurilish_turi_2)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Qurilish Maydoni -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Қурилиш майдони (кв.м)</label>
                    <input type="number" step="0.01" name="qurilish_maydoni" value="<?php echo e(old('qurilish_maydoni', $yer->qurilish_maydoni)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Investitsiya -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Инвестиция (АҚШ долл)</label>
                    <input type="number" step="0.01" name="investitsiya" value="<?php echo e(old('investitsiya', $yer->investitsiya)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>
            </div>
        </div>

        <!-- Аукцион маълумотлари -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center space-x-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Аукцион маълумотлари</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Boshlangich Narx -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Бошланғич нарх</label>
                    <input type="number" step="0.01" name="boshlangich_narx" value="<?php echo e(old('boshlangich_narx', $yer->boshlangich_narx)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Auksion Sana -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Аукцион санаси</label>
                    <input type="date" name="auksion_sana" value="<?php echo e(old('auksion_sana', $yer->auksion_sana?->format('Y-m-d'))); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Sotilgan Narx -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Сотилган нарх</label>
                    <input type="number" step="0.01" name="sotilgan_narx" value="<?php echo e(old('sotilgan_narx', $yer->sotilgan_narx)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Auksion Golibi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Аукцион ғолиби</label>
                    <input type="text" name="auksion_golibi" value="<?php echo e(old('auksion_golibi', $yer->auksion_golibi)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Golib Turi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ғолиб тури</label>
                    <select name="golib_turi"
                            class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                        <option value="">Танланг</option>
                        <option value="юр лицо" <?php echo e(old('golib_turi', $yer->golib_turi) == 'юр лицо' ? 'selected' : ''); ?>>юр лицо</option>
                        <option value="физ лицо" <?php echo e(old('golib_turi', $yer->golib_turi) == 'физ лицо' ? 'selected' : ''); ?>>физ лицо</option>
                    </select>
                </div>

                <!-- Golib Nomi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ғолиб номи</label>
                    <input type="text" name="golib_nomi" value="<?php echo e(old('golib_nomi', $yer->golib_nomi)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Telefon -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Телефон</label>
                    <input type="text" name="telefon" value="<?php echo e(old('telefon', $yer->telefon)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Tolov Turi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Тўлов тури</label>
                    <select name="tolov_turi"
                            class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                        <option value="">Танланг</option>
                        <option value="муддатли" <?php echo e(old('tolov_turi', $yer->tolov_turi) == 'муддатли' ? 'selected' : ''); ?>>муддатли</option>
                        <option value="муддатли эмас" <?php echo e(old('tolov_turi', $yer->tolov_turi) == 'муддатли эмас' ? 'selected' : ''); ?>>муддатли эмас</option>
                    </select>
                </div>

                <!-- Asos -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Асос</label>
                    <input type="text" name="asos" value="<?php echo e(old('asos', $yer->asos)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Auksion Turi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Аукцион тури</label>
                    <select name="auksion_turi"
                            class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                        <option value="">Танланг</option>
                        <option value="Очиқ" <?php echo e(old('auksion_turi', $yer->auksion_turi) == 'Очиқ аукцион' ? 'selected' : ''); ?>>Очиқ</option>
                    <option value="Ёпиқ" <?php echo e(old('auksion_turi', $yer->auksion_turi) == 'Ёпиқ аукцион' ? 'selected' : ''); ?>>Ёпиқ</option>
                    </select>
                </div>

                <!-- Holat -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ҳолат</label>
                    <input type="text" name="holat" value="<?php echo e(old('holat', $yer->holat)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>
            </div>
        </div>

        <!-- Шартнома маълумотлари -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center space-x-2">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Шартнома маълумотлари</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Shartnoma Holati -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Шартнома ҳолати</label>
                    <input type="text" name="shartnoma_holati" value="<?php echo e(old('shartnoma_holati', $yer->shartnoma_holati)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Shartnoma Sana -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Шартнома санаси</label>
                    <input type="date" name="shartnoma_sana" value="<?php echo e(old('shartnoma_sana', $yer->shartnoma_sana?->format('Y-m-d'))); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Shartnoma Raqam -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Шартнома рақами</label>
                    <input type="text" name="shartnoma_raqam" value="<?php echo e(old('shartnoma_raqam', $yer->shartnoma_raqam)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>
            </div>
        </div>

        <!-- Молиявий маълумотлар -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center space-x-2">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Молиявий маълумотлар</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Golib Tolagan -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ғолиб тўлаган</label>
                    <input type="number" step="0.01" name="golib_tolagan" value="<?php echo e(old('golib_tolagan', $yer->golib_tolagan)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Buyurtmachiga Otkazilgan -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Буюртмачига ўтказилган</label>
                    <input type="number" step="0.01" name="buyurtmachiga_otkazilgan" value="<?php echo e(old('buyurtmachiga_otkazilgan', $yer->buyurtmachiga_otkazilgan)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Chegirma -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Чегирма</label>
                    <input type="number" step="0.01" name="chegirma" value="<?php echo e(old('chegirma', $yer->chegirma)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Auksion Harajati -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Аукцион ҳаражати (1%)</label>
                    <input type="number" step="0.01" name="auksion_harajati" value="<?php echo e(old('auksion_harajati', $yer->auksion_harajati)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Tushadigan Mablagh -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Тушадиган маблағ</label>
                    <input type="number" step="0.01" name="tushadigan_mablagh" value="<?php echo e(old('tushadigan_mablagh', $yer->tushadigan_mablagh)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Davaktiv Jamgarmasi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Давактив жамғармаси</label>
                    <input type="number" step="0.01" name="davaktiv_jamgarmasi" value="<?php echo e(old('davaktiv_jamgarmasi', $yer->davaktiv_jamgarmasi)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Shartnoma Tushgan -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Шартнома бўйича тушган</label>
                    <input type="number" step="0.01" name="shartnoma_tushgan" value="<?php echo e(old('shartnoma_tushgan', $yer->shartnoma_tushgan)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Davaktivda Turgan -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Давактивда турган</label>
                    <input type="number" step="0.01" name="davaktivda_turgan" value="<?php echo e(old('davaktivda_turgan', $yer->davaktivda_turgan)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Yer Auksion Harajat -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ер аукцион ҳаражат</label>
                    <input type="number" step="0.01" name="yer_auksion_harajat" value="<?php echo e(old('yer_auksion_harajat', $yer->yer_auksion_harajat)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Shartnoma Summasi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Шартнома суммаси</label>
                    <input type="number" step="0.01" name="shartnoma_summasi" value="<?php echo e(old('shartnoma_summasi', $yer->shartnoma_summasi)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Farqi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Фарқи</label>
                    <input type="number" step="0.01" name="farqi" value="<?php echo e(old('farqi', $yer->farqi)); ?>"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>
            </div>
        </div>


        <!-- Form Actions -->
        <div class="flex items-center justify-between bg-slate-50 rounded-xl p-6 border-2 border-slate-200">
            <a href="<?php echo e(route('yer-sotuvlar.show', $yer->lot_raqami)); ?>"
               class="flex items-center space-x-2 px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-lg transition-all duration-300 font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>Бекор қилиш</span>
            </a>

            <button type="submit"
                    class="flex items-center space-x-2 px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Сақлаш</span>
            </button>
        </div>
    </form>
</div>

<!-- Success/Error Messages -->
<?php if(session('success')): ?>
<div class="fixed bottom-8 right-8 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl animate-fadeIn z-50">
    <div class="flex items-center space-x-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-semibold"><?php echo e(session('success')); ?></span>
    </div>
</div>
<?php endif; ?>

<?php if(session('error')): ?>
<div class="fixed bottom-8 right-8 bg-red-500 text-white px-6 py-4 rounded-lg shadow-2xl animate-fadeIn z-50">
    <div class="flex items-center space-x-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-semibold"><?php echo e(session('error')); ?></span>
    </div>
</div>
<?php endif; ?>

<?php if($errors->any()): ?>
<div class="fixed bottom-8 right-8 bg-red-500 text-white px-6 py-4 rounded-lg shadow-2xl animate-fadeIn z-50 max-w-md">
    <div class="flex items-start space-x-3">
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="font-bold mb-2">Хатолар:</p>
            <ul class="list-disc list-inside space-y-1 text-sm">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- <script>
    // Auto-hide success/error messages after 5 seconds
    setTimeout(function() {
        const messages = document.querySelectorAll('.animate-fadeIn');
        messages.forEach(function(message) {
            message.style.transition = 'opacity 0.5s ease';
            message.style.opacity = '0';
            setTimeout(function() {
                message.remove();
            }, 500);
        });
    }, 5000);
</script> -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\inves\OneDrive\Ishchi stol\yer-uchastkalar\resources\views/yer-sotuvlar/edit.blade.php ENDPATH**/ ?>