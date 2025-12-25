<?php $__env->startSection('title', 'Филтрланган маълумотлар'); ?>

<?php $__env->startSection('content'); ?>
    <div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8">

        <!-- Header Section with Search -->
        <div class="mx-auto mb-6">
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold text-slate-800 mb-2">
            Ер участкалар рўйхати
        </h1>
    </div>
    <div class="flex items-center space-x-3">
        
        <a href="<?php echo e(route('export.filtered', request()->query())); ?>"
           class="flex items-center space-x-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Excel юклаб олиш (<?php echo e($yerlar->total()); ?> та)</span>
        </a>
        <a href="<?php echo e(route('yer-sotuvlar.create')); ?>"
           class="flex items-center space-x-2 px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Янги қўшиш</span>
        </a>
    </div>
</div>
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
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
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
                <?php if(request()->hasAny([
                        'tuman',
                        'yil',
                        'tolov_turi',
                        'holat',
                        'asos',
                        'auksion_sana_from',
                        'auksion_sana_to',
                        'narx_from',
                        'narx_to',
                        'maydoni_from',
                        'maydoni_to',
                        'include_all',
                        'include_bekor',
                        'grafik_ortda',
                        'toliq_tolangan',
                        'nazoratda',
                        'qoldiq_qarz',
                        'auksonda_turgan',
                        'search',
                    ])): ?>
                    <div class="bg-white px-6 py-4 border-b border-gray-200">
                        <div class="flex flex-wrap gap-2">
                            <?php if(request('search')): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    Қидирув: <?php echo e(Str::limit(request('search'), 30)); ?>

                                </span>
                            <?php endif; ?>

                            <?php if(request('tuman')): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    <?php echo e(request('tuman')); ?>

                                </span>
                            <?php endif; ?>

                            <?php if(request('yil')): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <?php echo e(request('yil')); ?>

                                </span>
                            <?php endif; ?>

                            <?php if(request('tolov_turi')): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                    <?php echo e(request('tolov_turi')); ?>

                                </span>
                            <?php endif; ?>

                            <?php if(request('holat')): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    Ҳолат: <?php echo e(Str::limit(request('holat'), 30)); ?>

                                </span>
                            <?php endif; ?>

                            <?php if(request('asos')): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                    Асос: <?php echo e(request('asos')); ?>

                                </span>
                            <?php endif; ?>

                            <?php if(request('auksion_sana_from') || request('auksion_sana_to')): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-pink-100 text-pink-800">
                                    <?php echo e(request('auksion_sana_from') ?? '...'); ?> -
                                    <?php echo e(request('auksion_sana_to') ?? '...'); ?>

                                </span>
                            <?php endif; ?>

                            <?php if(request('narx_from') || request('narx_to')): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                    <?php echo e(request('narx_from') ? number_format(request('narx_from')) : '0'); ?> -
                                    <?php echo e(request('narx_to') ? number_format(request('narx_to')) : '∞'); ?>

                                </span>
                            <?php endif; ?>

                            <?php if(request('maydoni_from') || request('maydoni_to')): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-teal-100 text-teal-800">
                                    <?php echo e(request('maydoni_from') ?? '0'); ?> - <?php echo e(request('maydoni_to') ?? '∞'); ?> га
                                </span>
                            <?php endif; ?>

                            <?php if(request('include_all') === 'true'): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-cyan-100 text-cyan-800">
                                    Барча статуслар
                                </span>
                            <?php endif; ?>

                            <?php if(request('include_bekor') === 'true'): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    Бекор қилинганлар ҳам
                                </span>
                            <?php endif; ?>

                            <?php if(request('grafik_ortda') === 'true'): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    График ортда
                                </span>
                            <?php endif; ?>

                            <?php if(request('toliq_tolangan') === 'true'): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                    Тўлиқ тўланган
                                </span>
                            <?php endif; ?>

                            <?php if(request('nazoratda') === 'true'): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    Назоратда
                                </span>
                            <?php endif; ?>

                            <?php if(request('qoldiq_qarz') === 'true'): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                                    Қолдиқ қарз
                                </span>
                            <?php endif; ?>

                            <?php if(request('auksonda_turgan') === 'true'): ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-violet-100 text-violet-800">
                                    Аукционда турган
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Statistics Summary -->
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-white px-6 py-4">
                        <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                            <div>
                                <h1 class="text-xl font-bold text-gray-600">Умумий статистика</h1>
                                <p class="text-gray-600 text-sm mt-1">Филтрланган маълумотлар бўйича</p>
                            </div>
                        </div>
                    </div>

                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4 bg-gray-50 border-b border-gray-200">
                        <div class="text-center p-3 bg-white rounded border border-gray-200">
                            <div class="text-xs text-gray-600">Умумий лотлар сони</div>
                            <div class="text-lg font-bold text-gray-900"><?php echo e(number_format($statistics['total_lots'])); ?>

                            </div>
                        </div>
                        <div class="text-center p-3 bg-white rounded border border-gray-200">
                            <div class="text-xs text-gray-600">Умумий майдон</div>
                            <div class="text-lg font-bold text-gray-900"><?php echo e(number_format($statistics['total_area'], 2)); ?>

                                га</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded border border-gray-200">
                            <div class="text-xs text-gray-600">Бошланғич нархи</div>
                            <div class="text-lg font-bold text-gray-900">
                                <?php echo e(number_format($statistics['boshlangich_narx'] / 1000000000, 2)); ?> млрд</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded border border-gray-200">
                            <div class="text-xs text-gray-600">Сотилган нархи</div>
                            <div class="text-lg font-bold text-gray-900">
                                <?php echo e(number_format($statistics['total_price'] / 1000000000, 2)); ?> млрд</div>
                        </div>

                        
                        <div class="text-center p-3 bg-white rounded border border-blue-200">
                            <div class="text-xs text-gray-600">Тушадиган маблағ</div>
                            <div class="text-lg font-bold text-blue-600">
                                <?php echo e(number_format($statistics['total_expected'] / 1000000000, 2)); ?> млрд</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded border border-green-200">
                            <div class="text-xs text-gray-600">Тушган маблағ</div>
                            <div class="text-lg font-bold text-green-600">
                                <?php echo e(number_format($statistics['total_received'] / 1000000000, 2)); ?> млрд</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded border border-orange-200">
                            <div class="text-xs text-gray-600">Қолдиқ маблағ</div>
                            <div class="text-lg font-bold text-orange-600">
                                <?php echo e(number_format($statistics['total_qoldiq'] / 1000000000, 2)); ?> млрд</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded border border-red-200">
                            <div class="text-xs text-gray-600">Муддати ўтган қарздорлик</div>
                            <div class="text-lg font-bold text-red-700">
                                <?php echo e(number_format($statistics['total_muddati_utgan'] / 1000000000, 2)); ?> млрд</div>
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
                    $newDirection =
                        $currentSort === $field && $currentDirection === 'asc'
                            ? 'desc'
                            : 'asc';

                    $queryParams = array_merge(
                        request()->except(['sort', 'direction', 'page']),
                        [
                            'sort' => $field,
                            'direction' => $newDirection,
                        ],
                    );

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
                    'golib_tolagan' => 'Ғолиб аукционга тўлаган сумма',
                    'golib' => 'Ғолиб',
                ];
            ?>

            <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $col = sortableColumn($field, $label); ?>
                <th scope="col" style="text-align: center !important;"
                    class="px-3 py-3 text-center text-xs font-medium text-white uppercase tracking-wider cursor-pointer hover:bg-gray-600 transition-colors">
                    <a href="<?php echo e($col['url']); ?>">
                        <span><?php echo e($col['label']); ?></span>
                        <span
                            class="ml-2 <?php echo e($col['isActive'] ? 'text-yellow-300' : 'text-gray-400 group-hover:text-gray-300'); ?>">
                            <?php echo e($col['arrow']); ?>

                        </span>
                    </a>
                </th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
                <th scope="col"
                    class="px-3 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                    Тушадиган маблағ
                </th>
                <th scope="col"
                    class="px-3 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                    Тушган маблағ
                </th>
                <th scope="col"
                    class="px-3 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                    Қолдиқ маблағ
                </th>
                <th scope="col"
                    class="px-3 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                    Муддати ўтган қарздорлик
                </th>

            <th scope="col"
                class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                Тўлов тури
            </th>
            <th scope="col"
                class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                Ҳолат
            </th>
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
                <td class="px-3 py-3 text-sm text-gray-900 max-w-xs"
                    title="<?php echo e($yer->manzil); ?>">
                    <?php echo e(Str::limit($yer->manzil, 40)); ?>

                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                    <?php echo e(number_format($yer->maydoni, 4)); ?>

                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                    <?php echo e(number_format($yer->boshlangich_narx / 1000000, 1)); ?> млн
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900">
                    <?php echo e($yer->auksion_sana ? $yer->auksion_sana->format('d.m.Y') : '-'); ?>

                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm font-semibold text-green-600 text-center">
                    <?php echo e(number_format($yer->sotilgan_narx / 1000000, 1)); ?> млн
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                    <?php echo e(number_format($yer->chegirma / 1000000, 1)); ?> млн
                </td>
                <td class="px-3 py-3 whitespace-nowrap text-sm font-semibold text-blue-600 text-center">
                    <?php
                        $total_tolov = $yer->faktTolovlar->sum('tolov_summa');
                        $golib_total = $yer->golib_tolagan + $total_tolov;
                    ?>
                    <?php echo e(number_format($golib_total / 1000000, 1)); ?> млн
                </td>
                <td class="px-3 py-3 text-sm text-gray-900 max-w-xs"
                    title="<?php echo e($yer->golib_nomi); ?>">
                    <?php echo e(Str::limit($yer->golib_nomi, 30)); ?>

                </td>

                
                    <?php
                        // Calculate expected amount
                        $expected = ($yer->golib_tolagan ?? 0) + ($yer->shartnoma_summasi ?? 0) - ($yer->auksion_harajati ?? 0);

                        // Get received amount from fakt_tolovlar
                        $received = $yer->faktTolovlar->sum('tolov_summa');

                        // Calculate qoldiq
                        $qoldiq = $expected - $received;

                        // Calculate muddati utgan qarzdorlik (overdue debt)
                        $muddatiUtganQarz = 0;

                        if ($yer->tolov_turi === 'муддатли') {
                            // For muddatli: grafik up to last month - fakt (excluding auction payments)
                            $cutoffDate = now()->subMonth()->endOfMonth()->format('Y-m-01');

                            $grafikTushadigan = $yer->grafikTolovlar
                                ->filter(function($grafik) use ($cutoffDate) {
                                    $grafikDate = $grafik->yil . '-' . str_pad($grafik->oy, 2, '0', STR_PAD_LEFT) . '-01';
                                    return $grafikDate <= $cutoffDate;
                                })
                                ->sum('grafik_summa');

                            $grafikTushgan = $yer->faktTolovlar
                                ->filter(function($fakt) {
                                    $tolashNom = $fakt->tolash_nom ?? '';
                                    return !str_contains($tolashNom, 'ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH');
                                })
                                ->sum('tolov_summa');

                            $muddatiUtganQarz = max(0, $grafikTushadigan - $grafikTushgan);
                        } elseif ($yer->tolov_turi === 'муддатли эмас') {
                            // For muddatli emas: qoldiq if positive
                            $muddatiUtganQarz = max(0, $qoldiq);
                        }
                    ?>

                    
                    <td class="px-3 py-3 whitespace-nowrap text-sm font-semibold text-blue-600 text-center">
                        <?php echo e(number_format($expected / 1000000000, 2)); ?> млрд
                    </td>

                    
                    <td class="px-3 py-3 whitespace-nowrap text-sm font-semibold text-green-600 text-center ">
                        <?php echo e(number_format($received / 1000000000, 2)); ?> млрд
                    </td>

                    
                    <td class="px-3 py-3 whitespace-nowrap text-sm font-semibold text-center <?php echo e($qoldiq > 0 ? 'text-red-600' : 'text-gray-600'); ?>">
                        <?php echo e(number_format($qoldiq / 1000000000, 2)); ?> млрд
                    </td>

                    
                    <td class="px-3 py-3 whitespace-nowrap text-sm font-semibold text-center <?php echo e($muddatiUtganQarz > 0 ? 'text-red-700' : 'text-gray-600'); ?>">
                        <?php echo e(number_format($muddatiUtganQarz / 1000000000, 2)); ?> млрд
                    </td>

                <td class="px-3 py-3 whitespace-nowrap text-sm">
                    <?php if($yer->tolov_turi === 'муддатли'): ?>
                        <span
                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Муддатли
                        </span>
                    <?php elseif($yer->tolov_turi === 'муддатли эмас'): ?>
                        <span
                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Муддатли эмас
                        </span>
                    <?php else: ?>
                        <span class="text-gray-400">-</span>
                    <?php endif; ?>
                </td>
                <td class="px-3 py-3 text-sm text-gray-600 max-w-sm"
                    title="<?php echo e($yer->holat); ?>">
                    <?php echo e(Str::limit($yer->holat, 50)); ?>

                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="" class="px-4 py-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
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
                                            <option value="<?php echo e($tuman); ?>"
                                                <?php echo e(request('tuman') == $tuman ? 'selected' : ''); ?>>
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
                                            <option value="<?php echo e($yil); ?>"
                                                <?php echo e(request('yil') == $yil ? 'selected' : ''); ?>>
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
                                        <option value="муддатли"
                                            <?php echo e(request('tolov_turi') == 'муддатли' ? 'selected' : ''); ?>>
                                            Муддатли</option>
                                        <option value="муддатли эмас"
                                            <?php echo e(request('tolov_turi') == 'муддатли эмас' ? 'selected' : ''); ?>>
                                            Муддатли эмас</option>
                                    </select>
                                </div>

                                <!-- Sort Field -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Саралаш</label>
                                    <select name="sort"
                                        class="w-full px-3 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="auksion_sana"
                                            <?php echo e(request('sort') == 'auksion_sana' ? 'selected' : ''); ?>>
                                            Аукцион санаси</option>
                                        <option value="sotilgan_narx"
                                            <?php echo e(request('sort') == 'sotilgan_narx' ? 'selected' : ''); ?>>
                                            Сотилган нарх</option>
                                        <option value="boshlangich_narx"
                                            <?php echo e(request('sort') == 'boshlangich_narx' ? 'selected' : ''); ?>>
                                            Бошланғич нарх</option>
                                        <option value="maydoni" <?php echo e(request('sort') == 'maydoni' ? 'selected' : ''); ?>>
                                            Майдон</option>
                                        <option value="tuman" <?php echo e(request('sort') == 'tuman' ? 'selected' : ''); ?>>Туман
                                        </option>
                                        <option value="lot_raqami"
                                            <?php echo e(request('sort') == 'lot_raqami' ? 'selected' : ''); ?>>Лот рақами</option>
                                    </select>
                                </div>

                                <!-- Auksion Date From -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Аукцион санаси
                                        (дан)</label>
                                    <input type="date" name="auksion_sana_from"
                                        class="w-full px-3 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        value="<?php echo e(request('auksion_sana_from')); ?>">
                                </div>

                                <!-- Auksion Date To -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Аукцион санаси
                                        (гача)</label>
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
                                        <option value="desc" <?php echo e(request('direction') == 'desc' ? 'selected' : ''); ?>>
                                            Камайиш ↓</option>
                                        <option value="asc" <?php echo e(request('direction') == 'asc' ? 'selected' : ''); ?>>Ўсиш
                                            ↑</option>
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
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Қидириш
                                </button>

                                <a href="<?php echo e(route('yer-sotuvlar.list')); ?>"
                                    class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-3 px-6 rounded-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
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