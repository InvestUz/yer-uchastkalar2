<?php $__env->startSection('title', 'Мониторинг ва Аналитика'); ?>

<?php $__env->startSection('content'); ?>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-50 py-8">
        <div class="mx-auto px-6">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold mb-2" style="color: rgb(30, 41, 59);">
                    Тўловлар мониторинги ва аналитика
                </h1>
                <p class="text-slate-600 text-lg">
                    Ер сотув аукционлари бўйича тўловларни кузатиш ва таҳлил қилиш
                </p>
            </div>

            <!-- Period Filter Buttons -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-slate-800">Ҳисобот даври</h2>
                    <div class="flex items-center gap-3">
                        <!-- Clear Button -->
                        <?php if($periodInfo['period'] !== 'all'): ?>
                            <a href="<?php echo e(route('yer-sotuvlar.monitoring')); ?>"
                                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Тозалаш
                            </a>
                        <?php endif; ?>

                        <!-- Period Display -->
                        <span class="text-sm text-slate-500 bg-slate-100 px-4 py-2 rounded-lg font-semibold">
                            <?php if($periodInfo['period'] === 'month'): ?>
                                <?php echo e(\Carbon\Carbon::create($periodInfo['year'], $periodInfo['month'], 1)->locale('uz')->translatedFormat('F Y')); ?>

                                ойи ҳолатига
                            <?php elseif($periodInfo['period'] === 'quarter'): ?>
                                <?php echo e($periodInfo['quarter']); ?>-чорак ҳолатига <?php echo e($periodInfo['year']); ?> й
                            <?php elseif($periodInfo['period'] === 'year'): ?>
                                <?php echo e($periodInfo['year']); ?> йил ҳолатига
                            <?php else: ?>
                                Барча давр
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <!-- Main Period Filter -->
                <div class="flex gap-0 border border-gray-300 rounded-lg overflow-hidden mb-6">
                    <button onclick="changePeriod('month')"
                        class="flex-1 px-6 py-3 text-sm font-semibold period-filter-btn transition-all border-r border-gray-300 <?php echo e($periodInfo['period'] === 'month' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'); ?>"
                        id="btn-month">
                        Ойлик ҳисобот
                    </button>
                    <button onclick="changePeriod('quarter')"
                        class="flex-1 px-6 py-3 text-sm font-semibold period-filter-btn transition-all border-r border-gray-300 <?php echo e($periodInfo['period'] === 'quarter' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'); ?>"
                        id="btn-quarter">
                        Чораклик ҳисобот
                    </button>
                    <button onclick="changePeriod('year')"
                        class="flex-1 px-6 py-3 text-sm font-semibold period-filter-btn transition-all border-r border-gray-300 <?php echo e($periodInfo['period'] === 'year' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'); ?>"
                        id="btn-year">
                        Йиллик ҳисобот
                    </button>
                    <button onclick="changePeriod('all')"
                        class="flex-1 px-6 py-3 text-sm font-semibold period-filter-btn transition-all <?php echo e($periodInfo['period'] === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'); ?>"
                        id="btn-all">
                        Умумий ҳисобот
                    </button>
                </div>


            </div>
            <!-- Payment Type Tabs -->
            <div class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden">
                <div class="flex border-b border-gray-200">
                    <button onclick="switchTab('muddatli')"
                        class="flex-1 px-6 py-4 text-center font-bold transition-all duration-300 tab-button"
                        id="tab-muddatli"
                        style="background: linear-gradient(to right, rgb(37, 99, 235), rgb(29, 78, 216)); color: white;">
                        Муддатли тўлов (Бўлиб тўлаш)
                    </button>
                    <button onclick="switchTab('muddatli-emas')"
                        class="flex-1 px-6 py-4 text-center font-bold transition-all duration-300 tab-button"
                        id="tab-muddatli-emas" style="background: white; color: rgb(71, 85, 105);">
                        Муддатсиз тўлов (Бир йўла тўлаш)
                    </button>
                </div>
            </div>

            <!-- Муддатли Content -->
            <div id="content-muddatli" class="tab-content">
                <!-- Statistics Cards - Муддатли (7 cards with period info at BOTTOM) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                    <!-- Card 1: Жами лотлар сони - CLICKABLE -->
                    <a href="<?php echo e(route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли'], $dateFilters))); ?>"
                        class="block bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500 hover:shadow-2xl transition-all transform hover:-translate-y-1">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-slate-700">Жами лотлар сони</h3>
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-red-700 mb-1"><?php echo e(number_format($summaryMuddatli['total_lots'])); ?>

                            та</p>
                        <p class="text-xs text-slate-500 mb-3">Бўлиб тўлаш</p>

                        <!-- Period info at BOTTOM -->
                        <?php if($periodInfo['period'] !== 'all'): ?>
                            <div class="mt-auto pt-3 border-t border-slate-200">
                                <p class="text-xs text-blue-600 font-medium flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span><?php echo e($periodInfo['period'] === 'month'
                                        ? \Carbon\Carbon::create($periodInfo['year'], $periodInfo['month'], 1)->locale('uz')->translatedFormat('F Y')
                                        : ($periodInfo['period'] === 'quarter'
                                            ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year']
                                            : ($periodInfo['period'] === 'year'
                                                ? $periodInfo['year'] . ' йил'
                                                : ''))); ?></span>
                                </p>
                            </div>
                        <?php endif; ?>
                    </a>

                    <!-- Card 2: Тушадиган маблағ - CLICKABLE -->
                    <a href="<?php echo e(route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли', 'nazoratda' => 'true'], $dateFilters))); ?>"
                        class="block bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-2xl transition-all transform hover:-translate-y-1">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-slate-700">Тушадиган маблағ</h3>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-blue-700 mb-1">
                            <?php echo e(number_format($nazoratdagilar['tushadigan_mablagh'] / 1000000000, 2)); ?> млрд</p>
                        <p class="text-xs text-slate-500 mb-3">Назоратдагилар тушадиган маблағ</p>

                        <?php if($periodInfo['period'] !== 'all'): ?>
                            <div class="mt-auto pt-3 border-t border-slate-200">
                                <p class="text-xs text-blue-600 font-medium flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span><?php echo e($periodInfo['period'] === 'month'
                                        ? \Carbon\Carbon::create($periodInfo['year'], $periodInfo['month'], 1)->locale('uz')->translatedFormat('F Y')
                                        : ($periodInfo['period'] === 'quarter'
                                            ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year']
                                            : ($periodInfo['period'] === 'year'
                                                ? $periodInfo['year'] . ' йил'
                                                : ''))); ?></span>
                                </p>
                            </div>
                        <?php endif; ?>
                    </a>

                    <!-- Card 3: Амалда тушган маблағ - CLICKABLE -->
                    <a href="<?php echo e(route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли', 'nazoratda' => 'true'], $dateFilters))); ?>"
                        class="block bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-2xl transition-all transform hover:-translate-y-1">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-slate-700">Амалда тушган маблағ</h3>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-green-700 mb-1">
                            <?php echo e(number_format($nazoratdagilar['tushgan_summa'] / 1000000000, 2)); ?> млрд</p>
                        <p class="text-xs text-slate-500 mb-3">Фактик тўланган сумма</p>

                        <?php if($periodInfo['period'] !== 'all'): ?>
                            <div class="mt-auto pt-3 border-t border-slate-200">
                                <p class="text-xs text-blue-600 font-medium flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span><?php echo e($periodInfo['period'] === 'month'
                                        ? \Carbon\Carbon::create($periodInfo['year'], $periodInfo['month'], 1)->locale('uz')->translatedFormat('F Y')
                                        : ($periodInfo['period'] === 'quarter'
                                            ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year']
                                            : ($periodInfo['period'] === 'year'
                                                ? $periodInfo['year'] . ' йил'
                                                : ''))); ?></span>
                                </p>
                            </div>
                        <?php endif; ?>
                    </a>

                    <!-- Card 4: Қолдиқ маблағ - CLICKABLE -->
                    <?php
                        $qoldiqMablagh = $nazoratdagilar['tushadigan_mablagh'] - $nazoratdagilar['tushgan_summa'];
                        $qoldiqFoizi =
                            $nazoratdagilar['tushadigan_mablagh'] > 0
                                ? (($nazoratdagilar['tushadigan_mablagh'] - $nazoratdagilar['tushgan_summa']) /
                                        $nazoratdagilar['tushadigan_mablagh']) *
                                    100
                                : 0;
                    ?>
                    <a href="<?php echo e(route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли', 'nazoratda' => 'true'], $dateFilters))); ?>"
                        class="block bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500 hover:shadow-2xl transition-all transform hover:-translate-y-1">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-slate-700">Қолдиқ маблағ</h3>
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-red-700 mb-2">
                            <?php echo e(number_format($qoldiqMablagh / 1000000000, 2)); ?> млрд</p>
                        <div class="flex items-center mb-3">
                            <div class="flex-1 bg-gray-200 rounded-full h-2.5 mr-3">
                                <div class="bg-red-600 h-2.5 rounded-full transition-all duration-500"
                                    style="width: <?php echo e(100 - min(100, $qoldiqFoizi)); ?>%"></div>
                            </div>
                            <span
                                class="text-sm font-bold text-red-600"><?php echo e(number_format(100 - $qoldiqFoizi, 1)); ?>%</span>
                        </div>

                        <?php if($periodInfo['period'] !== 'all'): ?>
                            <div class="mt-auto pt-3 border-t border-slate-200">
                                <p class="text-xs text-blue-600 font-medium flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span><?php echo e($periodInfo['period'] === 'month'
                                        ? \Carbon\Carbon::create($periodInfo['year'], $periodInfo['month'], 1)->locale('uz')->translatedFormat('F Y')
                                        : ($periodInfo['period'] === 'quarter'
                                            ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year']
                                            : ($periodInfo['period'] === 'year'
                                                ? $periodInfo['year'] . ' йил'
                                                : ''))); ?></span>
                                </p>
                            </div>
                        <?php endif; ?>
                    </a>

                    <!-- Card 5: График б-ча тушадиган маблағ - CLICKABLE -->
                    <a href="<?php echo e(route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли'], $dateFilters))); ?>"
                        class="block bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-2xl transition-all transform hover:-translate-y-1">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-slate-700">График б-ча тушадиган маблағ</h3>
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-orange-700 mb-1">
                            <?php echo e(number_format($grafikTushadiganMuddatli / 1000000000, 2)); ?> млрд</p>
                        <p class="text-xs text-slate-500 mb-3">График бўйича тушадиган (охирги ой ҳолатига)</p>

                        <?php if($periodInfo['period'] !== 'all'): ?>
                            <div class="mt-auto pt-3 border-t border-slate-200">
                                <p class="text-xs text-blue-600 font-medium flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span><?php echo e($periodInfo['period'] === 'month'
                                        ? \Carbon\Carbon::create($periodInfo['year'], $periodInfo['month'], 1)->locale('uz')->translatedFormat('F Y')
                                        : ($periodInfo['period'] === 'quarter'
                                            ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year']
                                            : ($periodInfo['period'] === 'year'
                                                ? $periodInfo['year'] . ' йил'
                                                : ''))); ?></span>
                                </p>
                            </div>
                        <?php endif; ?>
                    </a>


<!-- Card 6: График бўйича тушган - CLICKABLE -->
<a href="<?php echo e(route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли'], $dateFilters))); ?>"
    class="block bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-2xl transition-all transform hover:-translate-y-1">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-slate-700">График бўйича тушган</h3>
        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
            <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
    </div>
    <p class="text-3xl font-bold text-orange-700 mb-1">
        
        <?php echo e(number_format($grafikBoyichaTushgan / 1000000000, 2)); ?> млрд
    </p>
    <p class="text-xs text-slate-500 mb-3">Амалда график бўйича</p>

    <?php if($periodInfo['period'] !== 'all'): ?>
        <div class="mt-auto pt-3 border-t border-slate-200">
            <p class="text-xs text-blue-600 font-medium flex items-center">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
                <span><?php echo e($periodInfo['period'] === 'month'
                    ? \Carbon\Carbon::create($periodInfo['year'], $periodInfo['month'], 1)->locale('uz')->translatedFormat('F Y')
                    : ($periodInfo['period'] === 'quarter'
                        ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year']
                        : ($periodInfo['period'] === 'year'
                            ? $periodInfo['year'] . ' йил'
                            : ''))); ?></span>
            </p>
        </div>
    <?php endif; ?>
</a>

<!-- Card 7: Муддати ўтган қарздорлик - CLICKABLE -->
<a href="<?php echo e(route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли', 'grafik_ortda' => 'true'], $dateFilters))); ?>"
    class="block bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500 hover:shadow-2xl transition-all transform hover:-translate-y-1">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-slate-700">Муддати ўтган қарздорлик</h3>
        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                </path>
            </svg>
        </div>
    </div>
    <p class="text-3xl font-bold text-red-700 mb-1">
        
        <?php echo e(number_format($muddatiUtganQarz / 1000000000, 2)); ?> млрд
    </p>
    <p class="text-xs text-slate-500 mb-3">Графикдан ортда қолган</p>

    <?php if($periodInfo['period'] !== 'all'): ?>
        <div class="mt-auto pt-3 border-t border-slate-200">
            <p class="text-xs text-blue-600 font-medium flex items-center">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
                <span><?php echo e($periodInfo['period'] === 'month'
                    ? \Carbon\Carbon::create($periodInfo['year'], $periodInfo['month'], 1)->locale('uz')->translatedFormat('F Y')
                    : ($periodInfo['period'] === 'quarter'
                        ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year']
                        : ($periodInfo['period'] === 'year'
                            ? $periodInfo['year'] . ' йил'
                            : ''))); ?></span>
            </p>
        </div>
    <?php endif; ?>
</a>
                    
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <form method="GET" action="<?php echo e(route('yer-sotuvlar.monitoring')); ?>" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Давр тури
                                    </label>
                                    <select name="period" id="period"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="all" <?php echo e($periodInfo['period'] === 'all' ? 'selected' : ''); ?>>
                                            Барчаси (умумий)
                                        </option>
                                        <option value="year" <?php echo e($periodInfo['period'] === 'year' ? 'selected' : ''); ?>>
                                            Йил бўйича
                                        </option>
                                        <option value="quarter"
                                            <?php echo e($periodInfo['period'] === 'quarter' ? 'selected' : ''); ?>>
                                            Чорак бўйича
                                        </option>
                                        <option value="month" <?php echo e($periodInfo['period'] === 'month' ? 'selected' : ''); ?>>
                                            Ой бўйича
                                        </option>
                                    </select>
                                </div>

                                
                                <div id="year-selector"
                                    style="display: <?php echo e(in_array($periodInfo['period'], ['year', 'quarter', 'month']) ? 'block' : 'none'); ?>">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Йил
                                    </label>
                                    <select name="year"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <?php $__currentLoopData = $availablePeriods['years']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($year); ?>"
                                                <?php echo e($periodInfo['year'] == $year ? 'selected' : ''); ?>>
                                                <?php echo e($year); ?> йил
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                
                                <div id="quarter-selector"
                                    style="display: <?php echo e($periodInfo['period'] === 'quarter' ? 'block' : 'none'); ?>">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Чорак
                                    </label>
                                    <select name="quarter"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="1" <?php echo e($periodInfo['quarter'] == 1 ? 'selected' : ''); ?>>
                                            1-чорак (Январь - Март)
                                        </option>
                                        <option value="2" <?php echo e($periodInfo['quarter'] == 2 ? 'selected' : ''); ?>>
                                            2-чорак (Апрель - Июнь)
                                        </option>
                                        <option value="3" <?php echo e($periodInfo['quarter'] == 3 ? 'selected' : ''); ?>>
                                            3-чорак (Июль - Сентябрь)
                                        </option>
                                        <option value="4" <?php echo e($periodInfo['quarter'] == 4 ? 'selected' : ''); ?>>
                                            4-чорак (Октябрь - Декабрь)
                                        </option>
                                    </select>
                                </div>

                                
                                <div id="month-selector"
                                    style="display: <?php echo e($periodInfo['period'] === 'month' ? 'block' : 'none'); ?>">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Ой
                                    </label>
                                    <select name="month"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <?php
                                            $oylar = [
                                                1 => 'Январь',
                                                2 => 'Февраль',
                                                3 => 'Март',
                                                4 => 'Апрель',
                                                5 => 'Май',
                                                6 => 'Июнь',
                                                7 => 'Июль',
                                                8 => 'Август',
                                                9 => 'Сентябрь',
                                                10 => 'Октябрь',
                                                11 => 'Ноябрь',
                                                12 => 'Декабрь',
                                            ];
                                        ?>
                                        <?php $__currentLoopData = $oylar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oyRaqam => $oyNomi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($oyRaqam); ?>"
                                                <?php echo e($periodInfo['month'] == $oyRaqam ? 'selected' : ''); ?>>
                                                <?php echo e($oyNomi); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            
                            <div class="flex justify-end space-x-3">
                                <a href="<?php echo e(route('yer-sotuvlar.monitoring')); ?>"
                                    class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                    Тозалаш
                                </a>
                                <button type="submit"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Қидириш
                                </button>
                            </div>
                        </form>
                    </div>

                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const periodSelect = document.getElementById('period');
                            const yearSelector = document.getElementById('year-selector');
                            const quarterSelector = document.getElementById('quarter-selector');
                            const monthSelector = document.getElementById('month-selector');

                            function updateSelectors() {
                                const periodValue = periodSelect.value;

                                // Hide all first
                                yearSelector.style.display = 'none';
                                quarterSelector.style.display = 'none';
                                monthSelector.style.display = 'none';

                                // Show based on selection
                                if (periodValue === 'year') {
                                    yearSelector.style.display = 'block';
                                } else if (periodValue === 'quarter') {
                                    yearSelector.style.display = 'block';
                                    quarterSelector.style.display = 'block';
                                } else if (periodValue === 'month') {
                                    yearSelector.style.display = 'block';
                                    monthSelector.style.display = 'block';
                                }
                            }

                            periodSelect.addEventListener('change', updateSelectors);
                            updateSelectors(); // Initial call
                        });
                    </script>
                </div>

                <!-- Charts - Муддатли -->
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mb-8">
                    <!-- Payment Status Distribution -->
                    <div class="xl:col-span-4 bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                            Тўлов ҳолати бўйича тақсимот
                        </h3>
                        <div class="h-72">
                            <canvas id="paymentStatusChart"></canvas>
                        </div>
                    </div>

                    <!-- Monthly Payment Comparison -->
                    <div class="xl:col-span-8 bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                            Тўловлар динамикаси (ойлар кесимида)
                        </h3>
                        <div class="h-72">
                            <canvas id="monthlyComparisonChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Tuman Analysis - Муддатли -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Тўловлар динамикаси (туманлар кесимида)
                    </h3>
                    <div class="h-[32rem]">
                        <canvas id="tumanComparisonChart"></canvas>
                    </div>
                </div>

                <!-- Detailed Statistics Table - Муддатли -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-6">Туманлар бўйича батафсил статистика</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-blue-50 border-b-2 border-blue-200">
                                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase">Ҳудуд номи
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase">Лотлар
                                        сони</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">График б-ча
                                        тўлов</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">Амалдаги
                                        тўлов</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">Фарқи</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase">Фоизда
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <?php $__currentLoopData = $tumanStatsMuddatli; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-blue-50 transition-colors">
                                        <td class="px-4 py-3 text-sm font-semibold text-slate-800"><?php echo e($stat['tuman']); ?>

                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                                <?php echo e(number_format($stat['lots'])); ?>

                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold text-blue-600">
                                            <?php echo e(number_format($stat['grafik'] / 1000000000, 2)); ?></td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold text-green-600">
                                            <?php echo e(number_format($stat['fakt'] / 1000000000, 2)); ?></td>
                                        <td
                                            class="px-4 py-3 text-right text-sm font-semibold <?php echo e($stat['difference'] > 0 ? 'text-red-600' : 'text-green-600'); ?>">
                                            <?php echo e(number_format($stat['difference'] / 1000000000, 2)); ?>

                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="w-24 bg-slate-200 rounded-full h-2">
                                                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500"
                                                        style="width: <?php echo e(min($stat['percentage'], 100)); ?>%"></div>
                                                </div>
                                                <span
                                                    class="text-sm font-bold text-blue-600"><?php echo e(number_format($stat['percentage'], 1)); ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot
                                class="bg-gradient-to-r from-yellow-50 to-yellow-100 border-t-2 border-yellow-300 font-bold">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-slate-800">ЖАМИ</td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-200 text-red-900">
                                            <?php echo e(number_format(collect($tumanStatsMuddatli)->sum('lots'))); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-blue-700">
                                        <?php echo e(number_format(collect($tumanStatsMuddatli)->sum('grafik') / 1000000000, 2)); ?>

                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-green-700">
                                        <?php echo e(number_format(collect($tumanStatsMuddatli)->sum('fakt') / 1000000000, 2)); ?></td>
                                    <td
                                        class="px-4 py-3 text-right text-sm <?php echo e(collect($tumanStatsMuddatli)->sum('difference') > 0 ? 'text-red-600' : 'text-green-600'); ?>">
                                        <?php echo e(number_format(collect($tumanStatsMuddatli)->sum('difference') / 1000000000, 2)); ?>

                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-blue-700">
                                        <?php
                                            $totalGrafik = collect($tumanStatsMuddatli)->sum('grafik');
                                            $totalFakt = collect($tumanStatsMuddatli)->sum('fakt');
                                            $percentage = $totalGrafik > 0 ? ($totalFakt / $totalGrafik) * 100 : 0;
                                        ?>
                                        <?php echo e(number_format($percentage, 1)); ?>%
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Муддатли эмас Content -->
            <div id="content-muddatli-emas" class="tab-content" style="display: none;">
                <!-- Statistics Cards - Муддатли эмас (5 cards) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                    <!-- 1. Soni -->
                    <div
                        class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500 hover:shadow-xl transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-slate-700">Жами лотлар сони</h3>
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-red-700 mb-1">
                            <?php echo e(number_format($summaryMuddatliEmas['total_lots'])); ?> та</p>
                        <p class="text-xs text-slate-500">Бир йўла тўлаш</p>
                    </div>

                    <!-- 2. Tushadigan mablag' -->
                    <div
                        class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-slate-700">Тушадиган маблағ</h3>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-blue-700 mb-1">
                            <?php echo e(number_format($summaryMuddatliEmas['expected_amount'] / 1000000000, 2)); ?> млрд</p>
                        <p class="text-xs text-slate-500">Жами шартнома суммаси</p>
                    </div>

                    <!-- 3. Grafik = Expected (no schedule) -->
                    <div
                        class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-slate-700">Графикда тушадиган</h3>
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-purple-700 mb-1">
                            <?php echo e(number_format($summaryMuddatliEmas['expected_amount'] / 1000000000, 2)); ?> млрд</p>
                        <p class="text-xs text-slate-500">Бир йўла тўлов (график йўқ)</p>
                    </div>

                    <!-- 4. Amalda to'langan -->
                    <div
                        class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-slate-700">Амалда тўланган</h3>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-green-700 mb-2">
                            <?php echo e(number_format($summaryMuddatliEmas['received_amount'] / 1000000000, 2)); ?> млрд</p>
                        <div class="flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-2.5 mr-3">
                                <div class="bg-green-600 h-2.5 rounded-full transition-all duration-500"
                                    style="width: <?php echo e(min(100, $summaryMuddatliEmas['payment_percentage'])); ?>%"></div>
                            </div>
                            <span
                                class="text-sm font-bold text-green-600"><?php echo e(number_format($summaryMuddatliEmas['payment_percentage'], 1)); ?>%</span>
                        </div>
                    </div>

                    <!-- 5. Muddati o'tgan -->
                    <?php
                        $muddatiOtganMuddatliEmas = max(
                            0,
                            $summaryMuddatliEmas['expected_amount'] - $summaryMuddatliEmas['received_amount'],
                        );
                    ?>
                    <div
                        class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-xl transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-slate-700">Муддати ўтган</h3>
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-orange-700 mb-1">
                            <?php echo e(number_format($muddatiOtganMuddatliEmas / 1000000000, 2)); ?> млрд</p>
                        <p class="text-xs text-slate-500">Тўланмаган маблағ</p>
                    </div>
                </div>

                <!-- Charts - Муддатли эмас -->
                <div class="grid grid-cols-1 gap-6 mb-8">
                    <!-- Monthly Payment Trend -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                            Тўловлар динамикаси (ойлар кесимида)
                        </h3>
                        <div class="h-72">
                            <canvas id="monthlyMuddatliEmasChart"></canvas>
                        </div>
                    </div>

                    <!-- Tuman Comparison -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Тўловлар динамикаси (туманлар кесимида)
                        </h3>
                        <div class="h-[32rem]">
                            <canvas id="tumanMuddatliEmasChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Detailed Statistics Table - Муддатли эмас -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-6">Туманлар бўйича батафсил статистика</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-green-50 border-b-2 border-green-200">
                                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase">Ҳудуд номи
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase">Лотлар
                                        сони</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">Тушадиган
                                        маблағ</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">Тушган
                                        маблағ</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">Фарқи</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase">Фоизда
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <?php $__currentLoopData = $tumanStatsMuddatliEmas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-green-50 transition-colors">
                                        <td class="px-4 py-3 text-sm font-semibold text-slate-800"><?php echo e($stat['tuman']); ?>

                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                                <?php echo e(number_format($stat['lots'])); ?>

                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold text-blue-600">
                                            <?php echo e(number_format($stat['expected'] / 1000000000, 2)); ?></td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold text-green-600">
                                            <?php echo e(number_format($stat['received'] / 1000000000, 2)); ?></td>
                                        <td
                                            class="px-4 py-3 text-right text-sm font-semibold <?php echo e($stat['difference'] > 0 ? 'text-red-600' : 'text-green-600'); ?>">
                                            <?php echo e(number_format($stat['difference'] / 1000000000, 2)); ?>

                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="w-24 bg-slate-200 rounded-full h-2">
                                                    <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-500"
                                                        style="width: <?php echo e(min($stat['percentage'], 100)); ?>%"></div>
                                                </div>
                                                <span
                                                    class="text-sm font-bold text-green-600"><?php echo e(number_format($stat['percentage'], 1)); ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot
                                class="bg-gradient-to-r from-yellow-50 to-yellow-100 border-t-2 border-yellow-300 font-bold">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-slate-800">ЖАМИ</td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-200 text-red-900">
                                            <?php echo e(number_format(collect($tumanStatsMuddatliEmas)->sum('lots'))); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-blue-700">
                                        <?php echo e(number_format(collect($tumanStatsMuddatliEmas)->sum('expected') / 1000000000, 2)); ?>

                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-green-700">
                                        <?php echo e(number_format(collect($tumanStatsMuddatliEmas)->sum('received') / 1000000000, 2)); ?>

                                    </td>
                                    <td
                                        class="px-4 py-3 text-right text-sm <?php echo e(collect($tumanStatsMuddatliEmas)->sum('difference') > 0 ? 'text-red-600' : 'text-green-600'); ?>">
                                        <?php echo e(number_format(collect($tumanStatsMuddatliEmas)->sum('difference') / 1000000000, 2)); ?>

                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-green-700">
                                        <?php
                                            $totalExpectedFinal = collect($tumanStatsMuddatliEmas)->sum('expected');
                                            $totalReceivedFinal = collect($tumanStatsMuddatliEmas)->sum('received');
                                            $percentageFinal =
                                                $totalExpectedFinal > 0
                                                    ? ($totalReceivedFinal / $totalExpectedFinal) * 100
                                                    : 0;
                                        ?>
                                        <?php echo e(number_format($percentageFinal, 1)); ?>%
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js">
    </script>

    <script>
        // Period filter functions
        function changePeriod(period) {
            document.getElementById('periodInput').value = period;
            const detailFilters = document.getElementById('detailFilters');
            if (period === 'all') {
                detailFilters.style.display = 'none';
            } else {
                detailFilters.style.display = 'grid';
            }
            document.getElementById('filterForm').submit();
        }

        // Tab switching functions
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });
            document.getElementById('content-' + tabName).style.display = 'block';

            document.querySelectorAll('.tab-button').forEach(button => {
                button.style.background = 'white';
                button.style.color = 'rgb(71, 85, 105)';
            });

            const activeTab = document.getElementById('tab-' + tabName);
            if (tabName === 'muddatli') {
                activeTab.style.background = 'linear-gradient(to right, rgb(37, 99, 235), rgb(29, 78, 216))';
            } else {
                activeTab.style.background = 'linear-gradient(to right, rgb(34, 197, 94), rgb(22, 163, 74))';
            }
            activeTab.style.color = 'white';
        }

        // Register datalabels plugin globally
        Chart.register(ChartDataLabels);
        Chart.defaults.set('plugins.datalabels', {
            display: false
        });

        // Payment Status Distribution Chart
        const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
        new Chart(paymentStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Тўлиқ тўланган', 'Назоратда', 'График ортда', 'Аукционда'],
                datasets: [{
                    data: [
                        <?php echo e($chartData['status']['completed']); ?>,
                        <?php echo e($chartData['status']['under_control']); ?>,
                        <?php echo e($chartData['status']['overdue']); ?>,
                        <?php echo e($chartData['status']['auction']); ?>

                    ],
                    backgroundColor: ['rgb(34, 197, 94)', 'rgb(59, 130, 246)', 'rgb(239, 68, 68)',
                        'rgb(156, 163, 175)'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        display: true,
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 14
                        },
                        formatter: (value) => value
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 11,
                                weight: 'bold'
                            },
                            color: '#000',
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + context.parsed + ' дона (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // Monthly Comparison Chart - Муддатли
        const monthlyCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chartData['monthly_muddatli']['labels']); ?>,
                datasets: [{
                    label: 'График',
                    data: <?php echo json_encode($chartData['monthly_muddatli']['grafik']); ?>,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3
                }, {
                    label: 'Факт',
                    data: <?php echo json_encode($chartData['monthly_muddatli']['fakt']); ?>,
                    borderColor: 'rgb(29, 78, 216)',
                    backgroundColor: 'rgba(29, 78, 216, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        display: true,
                        align: 'top',
                        color: (context) => context.datasetIndex === 0 ? 'rgb(239, 68, 68)' : 'rgb(29, 78, 216)',
                        font: {
                            weight: 'bold',
                            size: 10
                        },
                        formatter: (value) => value.toFixed(2)
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => value.toFixed(1) + ' млрд'
                        }
                    }
                }
            }
        });

        // Tuman Comparison Chart - Муддатли
        const tumanCtx = document.getElementById('tumanComparisonChart').getContext('2d');
        new Chart(tumanCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chartData['tuman_muddatli']['labels']); ?>,
                datasets: [{
                    label: 'График',
                    data: <?php echo json_encode($chartData['tuman_muddatli']['grafik']); ?>,
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 2,
                    borderRadius: 6
                }, {
                    label: 'Факт',
                    data: <?php echo json_encode($chartData['tuman_muddatli']['fakt']); ?>,
                    backgroundColor: 'rgba(29, 78, 216, 0.8)',
                    borderColor: 'rgb(29, 78, 216)',
                    borderWidth: 2,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        display: true,
                        align: 'end',
                        anchor: 'end',
                        color: (context) => context.datasetIndex === 0 ? 'rgb(239, 68, 68)' : 'rgb(29, 78, 216)',
                        font: {
                            weight: 'bold',
                            size: 9
                        },
                        formatter: (value) => value.toFixed(2)
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => value.toFixed(1) + ' млрд'
                        }
                    }
                }
            }
        });

        // Monthly Chart - Муддатли эмас
        const monthlyMuddatliEmasCtx = document.getElementById('monthlyMuddatliEmasChart').getContext('2d');
        new Chart(monthlyMuddatliEmasCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chartData['monthly_muddatli_emas']['labels']); ?>,
                datasets: [{
                    label: 'Тушган маблағ',
                    data: <?php echo json_encode($chartData['monthly_muddatli_emas']['received']); ?>,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        display: true,
                        align: 'top',
                        color: 'rgb(34, 197, 94)',
                        font: {
                            weight: 'bold',
                            size: 10
                        },
                        formatter: (value) => value.toFixed(2)
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => value.toFixed(1) + ' млрд'
                        }
                    }
                }
            }
        });

        // Tuman Chart - Муддатли эмас
        const tumanMuddatliEmasCtx = document.getElementById('tumanMuddatliEmasChart').getContext('2d');
        new Chart(tumanMuddatliEmasCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chartData['tuman_muddatli_emas']['labels']); ?>,
                datasets: [{
                    label: 'Тушадиган',
                    data: <?php echo json_encode($chartData['tuman_muddatli_emas']['expected']); ?>,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 2,
                    borderRadius: 6
                }, {
                    label: 'Тушган',
                    data: <?php echo json_encode($chartData['tuman_muddatli_emas']['received']); ?>,
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 2,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        display: true,
                        align: 'end',
                        anchor: 'end',
                        color: (context) => context.datasetIndex === 0 ? 'rgb(59, 130, 246)' : 'rgb(34, 197, 94)',
                        font: {
                            weight: 'bold',
                            size: 9
                        },
                        formatter: (value) => value.toFixed(2)
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => value.toFixed(1) + ' млрд'
                        }
                    }
                }
            }
        });
    </script>

    <style>
        .tab-button {
            position: relative;
            overflow: hidden;
        }

        .tab-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .tab-button:hover::before {
            width: 300px;
            height: 300px;
        }

        .period-filter-btn {
            position: relative;
            overflow: hidden;
        }

        .period-filter-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.1);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .period-filter-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .bg-white {
            transition: all 0.3s ease;
        }

        .hover\:shadow-xl:hover {
            transform: translateY(-2px);
        }

        @keyframes fadeInRow {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        tbody tr {
            animation: fadeInRow 0.3s ease-in-out;
        }
    </style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\inves\OneDrive\Ishchi stol\yer-uchastkalar\resources\views/yer-sotuvlar/monitoring.blade.php ENDPATH**/ ?>