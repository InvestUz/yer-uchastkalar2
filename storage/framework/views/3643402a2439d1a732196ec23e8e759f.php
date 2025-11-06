<?php $__env->startSection('title', 'Филтрланган маълумотлар'); ?>

<?php $__env->startSection('content'); ?>
    <div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8">

        <!-- Header Section with Search -->
        <div class="mx-auto mb-6">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
                <!-- Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <div>
                            <h1 class="text-xl font-bold text-gray-600 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Филтрланган маълумотлар
                            </h1>
                            <p class="text-gray-600 text-sm mt-1">Барча ер участкалари рўйхати</p>
                        </div>
                        <a href="<?php echo e(route('yer-sotuvlar.index')); ?>"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Статистикага қайтиш
                        </a>
                    </div>
                </div>

                <!-- Global Search Bar -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <form method="GET" action="<?php echo e(route('yer-sotuvlar.list')); ?>" class="w-full">
                        <!-- Preserve existing filters -->
                        <?php $__currentLoopData = request()->except(['search', 'page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <div class="flex gap-3">
                            <div class="flex-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    value="<?php echo e(request('search')); ?>"
                                    placeholder="Лот рақами, туман, манзил, ғолиб номи ёки бошқа маълумот қидириш...">
                            </div>
                            <button type="submit"
                                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                            <?php if(request('search')): ?>
                            <a href="<?php echo e(route('yer-sotuvlar.list', request()->except(['search', 'page']))); ?>"
                                class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors">
                                Тозалаш
                            </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Active Filters Display -->
                <?php if(request()->hasAny(['tuman', 'yil', 'tolov_turi', 'holat', 'asos', 'auksion_sana_from', 'auksion_sana_to', 'narx_from', 'narx_to', 'maydoni_from', 'maydoni_to'])): ?>
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-wrap gap-2">
                        <?php if(request('tuman')): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                <?php echo e(request('tuman')); ?>

                            </span>
                        <?php endif; ?>

                        <?php if(request('yil')): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <?php echo e(request('yil')); ?>

                            </span>
                        <?php endif; ?>

                        <?php if(request('tolov_turi')): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                <?php echo e(request('tolov_turi')); ?>

                            </span>
                        <?php endif; ?>

                        <?php if(request('holat')): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                Ҳолат: <?php echo e(Str::limit(request('holat'), 30)); ?>

                            </span>
                        <?php endif; ?>

                        <?php if(request('asos')): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                Асос: <?php echo e(request('asos')); ?>

                            </span>
                        <?php endif; ?>

                        <?php if(request('auksion_sana_from') || request('auksion_sana_to')): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-pink-100 text-pink-800">
                                📅 <?php echo e(request('auksion_sana_from') ?? '...'); ?> - <?php echo e(request('auksion_sana_to') ?? '...'); ?>

                            </span>
                        <?php endif; ?>

                        <?php if(request('narx_from') || request('narx_to')): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                💰 <?php echo e(request('narx_from') ? number_format(request('narx_from')) : '0'); ?> -
                                <?php echo e(request('narx_to') ? number_format(request('narx_to')) : '∞'); ?>

                            </span>
                        <?php endif; ?>

                        <?php if(request('maydoni_from') || request('maydoni_to')): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-teal-100 text-teal-800">
                                📏 <?php echo e(request('maydoni_from') ?? '0'); ?> - <?php echo e(request('maydoni_to') ?? '∞'); ?> га
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

            <!-- Statistics Summary -->
<div class="bg-gray-50 px-6 py-5">
    <?php
        // Calculate all values upfront for clarity
        $totalLots = $statistics['total_lots'] ?? 0;
        $totalArea = $statistics['total_area'] ?? 0;
        $boshlangichNarx = $statistics['boshlangich_narx'] ?? 0;
        $sotilganNarx = $statistics['total_price'] ?? 0;
        $chegirma = $statistics['chegirma'] ?? 0;
        
        // Financial calculations
        $golibTolagan = $statistics['golib_tolagan'] ?? 0;
        $shartnomaSummasi = $statistics['shartnoma_summasi'] ?? 0;
        $auksionHarajati = $statistics['auksion_harajati'] ?? 0;
        $faktTolangan = $statistics['fakt_tolangan'] ?? 0;
        
        // Derived calculations
        $jamiTushishi = $golibTolagan + $shartnomaSummasi; // Total amount that should come in
        $jamiTushgan = $faktTolangan + $auksionHarajati; // Total amount received
        $qoldiqTolash = $jamiTushishi - $jamiTushgan; // Remaining to be paid
        $tushadigan = $jamiTushishi - $auksionHarajati; // Amount to be received (excluding service fee)
    ?>

    <!-- Primary Statistics: 4 columns on large screens -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <!-- Total Lots -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Жами лотлар</div>
                    <svg class="w-5 h-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-red-700"><?php echo e(number_format($totalLots)); ?></div>
                <div class="text-xs text-gray-500 mt-1">сони</div>
            </div>
        </div>

        <!-- Total Area -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Ер майдони</div>
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900"><?php echo e(number_format($totalArea, 2)); ?></div>
                <div class="text-xs text-gray-500 mt-1">гектар</div>
            </div>
        </div>

        <!-- Initial Price -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Бошланғич нархи</div>
                    <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-blue-700"><?php echo e(number_format($boshlangichNarx, 0)); ?></div>
                <div class="text-xs text-gray-500 mt-1">сўм</div>
            </div>
        </div>

        <!-- Sold Price -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Сотилган нархи</div>
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-green-600"><?php echo e(number_format($sotilganNarx, 0)); ?></div>
                <div class="text-xs text-gray-500 mt-1">сўм</div>
            </div>
        </div>
    </div>

    <!-- Secondary Statistics: Discount -->
    <div class="grid grid-cols-1 mb-4">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Чегирма қиймати</div>
                        <div class="text-2xl font-bold text-blue-700"><?php echo e(number_format($chegirma, 0)); ?> <span class="text-sm text-gray-500">сўм</span></div>
                    </div>
                    <?php if($boshlangichNarx > 0): ?>
                    <div class="text-right">
                        <div class="text-xs text-gray-500 mb-1">Фоиз</div>
                        <div class="text-xl font-semibold text-blue-600">
                            <?php echo e(number_format(($chegirma / $boshlangichNarx) * 100, 1)); ?>%
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Details: 4 columns -->
    <div class="bg-white rounded-lg border-2 border-blue-100 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            Молиявий кўрсаткичлар
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Auction Service Fee (1%) -->
            <div class="bg-gradient-to-br from-blue-50 to-white p-4 rounded-lg border border-blue-200">
                <div class="text-xs font-medium text-gray-600 mb-2">Аукцион хизмат ҳақи (1%)</div>
                <div class="text-xl font-bold text-blue-700"><?php echo e(number_format($auksionHarajati, 0)); ?></div>
                <div class="text-xs text-gray-500 mt-1">сўм</div>
            </div>

            <!-- Expected Amount (excluding service fee) -->
            <div class="bg-gradient-to-br from-purple-50 to-white p-4 rounded-lg border border-purple-200">
                <div class="text-xs font-medium text-gray-600 mb-2">Тушадиган қиймат</div>
                <div class="text-xl font-bold text-purple-700"><?php echo e(number_format($tushadigan, 0)); ?></div>
                <div class="text-xs text-gray-500 mt-1">сўм (хизмат ҳақисиз)</div>
            </div>

            <!-- Contract Payment Amount -->
            <div class="bg-gradient-to-br from-indigo-50 to-white p-4 rounded-lg border border-indigo-200">
                <div class="text-xs font-medium text-gray-600 mb-2">Шартнома бўйича тўлов</div>
                <div class="text-xl font-bold text-indigo-700"><?php echo e(number_format($shartnomaSummasi, 0)); ?></div>
                <div class="text-xs text-gray-500 mt-1">сўм</div>
            </div>

            <!-- Winner's Initial Payment -->
            <div class="bg-gradient-to-br from-teal-50 to-white p-4 rounded-lg border border-teal-200">
                <div class="text-xs font-medium text-gray-600 mb-2">Ғолиб тўлаган</div>
                <div class="text-xl font-bold text-teal-700"><?php echo e(number_format($golibTolagan, 0)); ?></div>
                <div class="text-xs text-gray-500 mt-1">сўм</div>
            </div>
        </div>
    </div>

    <!-- Payment Summary: 3 columns with emphasis -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
        <!-- Total Expected -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs font-medium uppercase tracking-wider opacity-90">Жами тушиши керак</div>
                <svg class="w-6 h-6 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold"><?php echo e(number_format($jamiTushishi, 0)); ?></div>
            <div class="text-xs opacity-75 mt-1">сўм</div>
        </div>

        <!-- Total Received -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs font-medium uppercase tracking-wider opacity-90">Жами тушган</div>
                <svg class="w-6 h-6 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold"><?php echo e(number_format($jamiTushgan, 0)); ?></div>
            <div class="text-xs opacity-75 mt-1">сўм (факт + хизмат ҳақи)</div>
        </div>

        <!-- Remaining Balance -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-lg shadow-lg p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs font-medium uppercase tracking-wider opacity-90">Қолдиқ тўлаш</div>
                <svg class="w-6 h-6 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold"><?php echo e(number_format($qoldiqTolash, 0)); ?></div>
            <div class="text-xs opacity-75 mt-1">сўм</div>
        </div>
    </div>

    <!-- Formula Explanation (Optional - can be hidden) -->
    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <details class="cursor-pointer">
            <summary class="text-sm font-medium text-blue-900 select-none">Ҳисоблаш формуласи</summary>
            <div class="mt-3 text-xs text-gray-700 space-y-2">
                <div class="flex items-center">
                    <span class="font-semibold min-w-[200px]">Жами тушиши керак:</span>
                    <span class="font-mono bg-white px-2 py-1 rounded">Ғолиб тўлаган + Шартнома суммаси</span>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold min-w-[200px]">Жами тушган:</span>
                    <span class="font-mono bg-white px-2 py-1 rounded">Факт тўлов + Аукцион ҳаражати</span>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold min-w-[200px]">Қолдиқ:</span>
                    <span class="font-mono bg-white px-2 py-1 rounded">Жами тушиши керак - Жами тушган</span>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold min-w-[200px]">Тушадиган қиймат:</span>
                    <span class="font-mono bg-white px-2 py-1 rounded">Жами тушиши керак - Аукцион ҳаражати</span>
                </div>
            </div>
        </details>
    </div>
</div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="mx-auto">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-700">
                            <tr>
                                <?php
                                    function sortableColumn($field, $label)
                                    {
                                        $currentSort = request('sort', 'auksion_sana');
                                        $currentDirection = request('direction', 'desc');
                                        $newDirection = $currentSort === $field && $currentDirection === 'asc' ? 'desc' : 'asc';

                                        $queryParams = array_merge(request()->except(['sort', 'direction', 'page']), [
                                            'sort' => $field,
                                            'direction' => $newDirection,
                                        ]);

                                        $url = route('yer-sotuvlar.list', $queryParams);
                                        $isActive = $currentSort === $field;
                                        $arrow = $isActive ? ($currentDirection === 'asc' ? '↑' : '↓') : '⇅';

                                        return [
                                            'url' => $url,
                                            'isActive' => $isActive,
                                            'arrow' => $arrow,
                                            'label' => $label,
                                        ];
                                    }

                                    $columns = [
                                        'lot_raqami' => '№ Лот',
                                        'tuman' => 'Туман',
                                        'manzil' => 'Манзил',
                                        'maydoni' => 'Майдон (га)',
                                        'boshlangich_narx' => 'Бошл. нарх',
                                        'auksion_sana' => 'Аукцион',
                                        'sotilgan_narx' => 'Сотил. нарх',
                                        'chegirma' => 'Чегирма',
                                        'golib_tolagan' => 'Ғолиб тўлаган',
                                        'golib' => 'Ғолиб',
                                    ];
                                ?>

                                <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $col = sortableColumn($field, $label); ?>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider cursor-pointer hover:bg-gray-600 transition-colors">
                                        <a href="<?php echo e($col['url']); ?>" class="flex items-center justify-between group">
                                            <span><?php echo e($col['label']); ?></span>
                                            <span class="ml-2 <?php echo e($col['isActive'] ? 'text-yellow-300' : 'text-gray-400 group-hover:text-gray-300'); ?>">
                                                <?php echo e($col['arrow']); ?>

                                            </span>
                                        </a>
                                    </th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Тўлов тури</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Ҳолат</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $yerlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-3 py-3 whitespace-nowrap text-sm font-medium">
                                        <a href="<?php echo e(route('yer-sotuvlar.show', $yer->lot_raqami)); ?>"
                                            class="font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                            <?php echo e($yer->lot_raqami); ?>

                                        </a>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e($yer->tuman); ?>

                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-900 max-w-xs" title="<?php echo e($yer->manzil); ?>">
                                        <?php echo e(Str::limit($yer->manzil, 40)); ?>

                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-right">
                                        <?php echo e(number_format($yer->maydoni, 4)); ?>

                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-right">
                                        <?php echo e(number_format($yer->boshlangich_narx / 1000000, 1)); ?> млн
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e($yer->auksion_sana ? $yer->auksion_sana->format('d.m.Y') : '-'); ?>

                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm font-semibold text-green-600 text-right">
                                        <?php echo e(number_format($yer->sotilgan_narx / 1000000, 1)); ?> млн
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-right">
                                        <?php echo e(number_format($yer->chegirma / 1000000, 1)); ?> млн
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm font-semibold text-blue-600 text-right">
                                        <?php
                                            $total_tolov = $yer->faktTolovlar->sum('tolov_summa');
                                            $golib_total = $yer->golib_tolagan + $total_tolov;
                                        ?>
                                        <?php echo e(number_format($golib_total / 1000000, 1)); ?> млн
                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-900 max-w-xs" title="<?php echo e($yer->golib_nomi); ?>">
                                        <?php echo e(Str::limit($yer->golib_nomi, 30)); ?>

                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm">
                                        <?php if($yer->tolov_turi === 'муддатли'): ?>
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Муддатли
                                            </span>
                                        <?php elseif($yer->tolov_turi === 'муддатли эмас'): ?>
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Муддатли эмас
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-600 max-w-sm" title="<?php echo e($yer->holat); ?>">
                                        <?php echo e(Str::limit($yer->holat, 50)); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="12" class="px-4 py-8 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="mt-2 text-lg font-medium">Маълумот топилмади</p>
                                        <p class="mt-1 text-sm">Филтр параметрларини ўзгартириб кўринг</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if($yerlar->hasPages()): ?>
                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Кўрсатилмоқда: <span class="font-semibold"><?php echo e($yerlar->firstItem()); ?></span> -
                                <span class="font-semibold"><?php echo e($yerlar->lastItem()); ?></span> /
                                <span class="font-semibold"><?php echo e($yerlar->total()); ?></span>
                            </div>
                            <div>
                                <?php echo e($yerlar->links()); ?>

                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- FILTERS SECTION - MOVED TO BOTTOM -->
        <div class="mx-auto mt-6">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
                <div class="bg-gray-700 px-6 py-3">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Қўшимча филтрлар
                    </h2>
                </div>

                <form method="GET" action="<?php echo e(route('yer-sotuvlar.list')); ?>" class="bg-gray-50 px-6 py-5">

                    <!-- Advanced Filters Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">

                        <!-- Tuman Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Туман</label>
                            <select name="tuman"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Барчаси</option>
                                <?php $__currentLoopData = $tumanlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tuman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($tuman); ?>" <?php echo e(request('tuman') == $tuman ? 'selected' : ''); ?>>
                                        <?php echo e($tuman); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Йил</label>
                            <select name="yil"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Барчаси</option>
                                <?php $__currentLoopData = $yillar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yil): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($yil); ?>" <?php echo e(request('yil') == $yil ? 'selected' : ''); ?>>
                                        <?php echo e($yil); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Tolov Turi Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Тўлов тури</label>
                            <select name="tolov_turi"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Барчаси</option>
                                <option value="муддатли" <?php echo e(request('tolov_turi') == 'муддатли' ? 'selected' : ''); ?>>
                                    Муддатли</option>
                                <option value="муддатли эмас" <?php echo e(request('tolov_turi') == 'муддатли эмас' ? 'selected' : ''); ?>>
                                    Муддатли эмас</option>
                            </select>
                        </div>

                        <!-- Sort Field -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Саралаш</label>
                            <select name="sort"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="auksion_sana" <?php echo e(request('sort') == 'auksion_sana' ? 'selected' : ''); ?>>
                                    Аукцион санаси</option>
                                <option value="sotilgan_narx" <?php echo e(request('sort') == 'sotilgan_narx' ? 'selected' : ''); ?>>
                                    Сотилган нарх</option>
                                <option value="boshlangich_narx" <?php echo e(request('sort') == 'boshlangich_narx' ? 'selected' : ''); ?>>
                                    Бошланғич нарх</option>
                                <option value="maydoni" <?php echo e(request('sort') == 'maydoni' ? 'selected' : ''); ?>>Майдон</option>
                                <option value="tuman" <?php echo e(request('sort') == 'tuman' ? 'selected' : ''); ?>>Туман</option>
                                <option value="lot_raqami" <?php echo e(request('sort') == 'lot_raqami' ? 'selected' : ''); ?>>Лот рақами</option>
                            </select>
                        </div>

                        <!-- Auksion Date From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Аукцион санаси (дан)</label>
                            <input type="date" name="auksion_sana_from"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="<?php echo e(request('auksion_sana_from')); ?>">
                        </div>

                        <!-- Auksion Date To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Аукцион санаси (гача)</label>
                            <input type="date" name="auksion_sana_to"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="<?php echo e(request('auksion_sana_to')); ?>">
                        </div>

                        <!-- Holat Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ҳолат</label>
                            <input type="text" name="holat"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="<?php echo e(request('holat')); ?>" placeholder="Ҳолат қидириш">
                        </div>

                        <!-- Asos Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Асос</label>
                            <input type="text" name="asos"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="<?php echo e(request('asos')); ?>" placeholder="Асос қидириш">
                        </div>

                        <!-- Sort Direction -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Тартиб</label>
                            <select name="direction"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="desc" <?php echo e(request('direction') == 'desc' ? 'selected' : ''); ?>>Камайиш ↓</option>
                                <option value="asc" <?php echo e(request('direction') == 'asc' ? 'selected' : ''); ?>>Ўсиш ↑</option>
                            </select>
                        </div>

                        <!-- Price From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Нарх (дан)</label>
                            <input type="number" name="narx_from"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="<?php echo e(request('narx_from')); ?>" placeholder="0">
                        </div>

                        <!-- Price To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Нарх (гача)</label>
                            <input type="number" name="narx_to"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="<?php echo e(request('narx_to')); ?>" placeholder="∞">
                        </div>

                        <!-- Area From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Майдон (дан) га</label>
                            <input type="number" step="0.01" name="maydoni_from"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="<?php echo e(request('maydoni_from')); ?>" placeholder="0">
                        </div>

                        <!-- Area To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Майдон (гача) га</label>
                            <input type="number" step="0.01" name="maydoni_to"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="<?php echo e(request('maydoni_to')); ?>" placeholder="∞">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Қидириш
                        </button>

                        <a href="<?php echo e(route('yer-sotuvlar.list')); ?>"
                            class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-3 px-6 rounded-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <style>
        /* Custom scrollbar for table */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\inves\OneDrive\Ishchi stol\yer-uchastkalar\resources\views/yer-sotuvlar/list.blade.php ENDPATH**/ ?>