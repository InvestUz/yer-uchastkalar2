<?php $__env->startSection('title', 'Бўлиб тўлаш маълумоти'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $fmt = function ($amount) {
            return number_format(((float)$amount) / 1_000_000_000, 1, '.', ',') . ' млрд';
        };

        $headerLabel = function ($label) {
            $map = [
                'Т/р' => 'Т/<br>р',
                'Ҳудудлар' => 'Ҳудуд-<br>лар',
                'Жами (млрд сўм)' => 'Жами<br>(млрд сўм)',
                'Чегирма' => 'Чегир-<br>ма',
                'Харидорларга қайтарилган маблағлар' => 'Харидорларга<br>қайтарилган<br>маблағлар',
                'Тошкент ш. қурилиш бошкармасига (1%)' => 'Тошкент ш.<br>қурилиш<br>бошкармасига<br>(1%)',
                'Давлат кадастрлар палатасига' => 'Давлат<br>кадастрлар<br>палатасига',
                'Геоахборот шахарсозлик кадастрига' => 'Геоахборот<br>шахарсозлик<br>кадастрига',
                'Солиқ қўмитаси хузуридаги Кадастр агентлигига' => 'Солиқ қўмитаси<br>хузуридаги<br>Кадастр<br>агентлигига',
                'Тошкент шахар махаллий бюджетига' => 'Тошкент шахар<br>махаллий<br>бюджетига',
                'Жамғармага' => 'Жамғар-<br>мага',
                'Туманга' => 'Туман-<br>га',
                'ЯнгиХаёт индустриал технопарки дирекциясига' => 'ЯнгиХаёт<br>индустриал<br>технопарки<br>дирекциясига',
                'Шайҳонтохур туманига' => 'Шайҳонтохур<br>туманига',
                'Тошкент сити дирекциясига' => 'Тошкент сити<br>дирекциясига',
                'Қолдиқ (млрд сўм)' => 'Қолдиқ<br>(млрд сўм)',
            ];

            return $map[$label] ?? e($label);
        };

        $filters = $filters ?? [
            'year' => null,
            'month' => null,
            'date_from' => null,
            'date_to' => null,
        ];
        $activeFilterParams = $activeFilterParams ?? [];
        $availableYears = $availableYears ?? [];
        $monthOptions = $monthOptions ?? [];
        $districtRestrict = $filters['district_restrict'] ?? null;
        $proportionalCategoryLookup = $proportionalCategoryLookup ?? [];

        $yearSelectOptions = [];
        if (!empty($availableYears)) {
            $minYear = (int)min($availableYears);
            $maxYear = (int)max($availableYears);
            if ($minYear <= $maxYear) {
                $yearSelectOptions = range($maxYear, $minYear);
            }
        }

        $periodParts = [];
        if ($districtRestrict) {
            $periodParts[] = 'Ҳудуд: ' . $districtRestrict;
        }
        if (!empty($filters['year'])) {
            $periodParts[] = 'Йил: ' . $filters['year'];
        }
        if (!empty($filters['month'])) {
            $monthNo = (int)$filters['month'];
            $periodParts[] = 'Ой: ' . ($monthOptions[$monthNo] ?? $monthNo);
        }
        if (!empty($filters['date_from'])) {
            $periodParts[] = 'Санадан: ' . $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $periodParts[] = 'Санага: ' . $filters['date_to'];
        }
        $hasActiveFilters = !empty($periodParts);
        $activeFilterText = $hasActiveFilters ? implode(' | ', $periodParts) : 'Барча давр';
    ?>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-6 px-4">
        <div class="max-w-[98%] mx-auto">

            <div class="bg-white rounded-xl shadow-2xl overflow-hidden mb-6 border-t-4 border-blue-600">
                <div class="bg-white px-8 py-6">
                    <div class="text-center">
                        <h1 class="text-lg font-bold text-blue tracking-wide mb-1">
                            Тошкент шаҳрида аукцион савдоларида бўлиб тўлаш шарти билан сотилган ер участкалари тўғрисида
                        </h1>
                        <h2 class="text-base font-semibold text-blue">ЙИҒМА МАЪЛУМОТ</h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Сумма ёки сони устига босинг: тизим танланган ҳудуд/тоифа бўйича детал рўйхатни очади.
                        </p>
                        <p class="text-xs text-blue-700 mt-1">Амалдаги фильтр: <?php echo e($activeFilterText); ?></p>
                        <?php if($districtRestrict): ?>
                            <p class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-300 rounded px-3 py-1 mt-2 inline-block">
                                Сиз фақат <strong><?php echo e($districtRestrict); ?></strong> бўйича маълумотларни кўряпсиз
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="border-collapse statistics-table">
                            <thead>
                                <tr style="background:#eff6ff !important;">
                                    <th rowspan="2" class="sticky-col border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800" style="width: 40px; min-width: 40px; max-width: 40px; font-size:11px;">
                                        <?php echo $headerLabel('Т/р'); ?>

                                    </th>
                                    <th rowspan="2" class="sticky-col-2 border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800" style="width: 150px; min-width: 150px; max-width: 150px; font-size:11px;">
                                        <?php echo $headerLabel('Ҳудудлар'); ?>

                                    </th>
                                    <th rowspan="2" class="total-amount-col border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800" style="font-size:11px;">
                                        <?php echo $headerLabel('Жами (млрд сўм)'); ?>

                                    </th>
                                    <?php $__currentLoopData = $paymentCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th
                                            class="border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800"
                                            style="min-width: 100px; font-size:11px;"
                                            title="<?php echo e($category); ?>"
                                        ><?php echo $headerLabel($category); ?>

                                        </th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <th class="border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800"
                                        style="min-width: 120px; font-size:11px;">
                                        <?php echo $headerLabel('Қолдиқ (млрд сўм)'); ?>

                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white">
                                <?php if(empty($districtData) || count($districtData) === 0): ?>
                                    <tr>
                                        <td colspan="<?php echo e(4 + count($paymentCategories)); ?>" class="border border-slate-300 px-4 py-6 text-center text-slate-700">
                                            Маълумотлар топилмади.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr class="bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100 border-y-2 border-amber-400">
                                        <td colspan="2" class="sticky-col-total border border-slate-300 px-3 py-2 text-center align-middle font-bold text-slate-900 text-xs uppercase bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100">
                                            ЖАМИ:
                                        </td>
                                        <td class="total-amount-col border border-slate-300 px-2 py-1 text-right font-bold text-slate-900">
                                            <a href="<?php echo e(route('yer-sotuvlar.fin-xisobot.details', $activeFilterParams)); ?>" class="block text-blue-700 hover:text-blue-900 hover:underline">
                                                <span class="font-semibold"><?php echo e($fmt($totalAmount)); ?></span><br>
                                                <span class="text-slate-400"><?php echo e($transactionCount ?? 0); ?> та</span>
                                            </a>
                                        </td>

                                        <?php $__currentLoopData = $paymentCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td class="border border-slate-300 px-2 py-1 text-right font-bold text-slate-900">
                                                <?php
                                                    $catTotal = $categoryTotals[$category] ?? 0;
                                                    $isSyntheticTotalCell = !empty($districtRestrict) && !empty($proportionalCategoryLookup[$category]);
                                                ?>
                                                <?php if($catTotal > 0): ?>
                                                    <?php if($isSyntheticTotalCell): ?>
                                                        <span class="block text-right text-slate-700" title="Пропорция бўйича ҳисобланган">
                                                            <span class="font-semibold"><?php echo e($fmt($catTotal)); ?></span><br>
                                                            <span class="text-slate-400"><?php echo e($categoryCounts[$category] ?? 0); ?> та</span>
                                                        </span>
                                                    <?php else: ?>
                                                        <a href="<?php echo e(route('yer-sotuvlar.fin-xisobot.details', array_merge($activeFilterParams, ['category' => $category]))); ?>" class="block text-blue-700 hover:text-blue-900 hover:underline text-right">
                                                            <span class="font-semibold"><?php echo e($fmt($catTotal)); ?></span><br>
                                                            <span class="text-slate-400"><?php echo e($categoryCounts[$category] ?? 0); ?> та</span>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-slate-300">—</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        <?php
                                            $jamiCategorySum = 0.0;
                                            foreach ($paymentCategories as $categoryName => $categoryValue) {
                                                $jamiCategorySum += (float)($categoryTotals[$categoryName] ?? 0);
                                            }
                                            $jamiQoldiq = (float)$totalAmount - $jamiCategorySum;
                                            if (abs($jamiQoldiq) < 0.01) {
                                                $jamiQoldiq = 0.0;
                                            }
                                        ?>
                                        <td class="border border-slate-300 px-2 py-1 text-right font-bold text-slate-900">
                                            <?php if($jamiQoldiq > 0): ?>
                                                <span class="font-semibold text-rose-700"><?php echo e($fmt($jamiQoldiq)); ?></span>
                                            <?php else: ?>
                                                <span class="text-slate-300">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <?php $__currentLoopData = $districtData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-blue-50 transition-colors duration-150 bg-white">
                                            <td class="sticky-col border border-slate-300 px-2 py-1 text-center align-middle font-medium text-slate-700">
                                                <?php echo e($loop->iteration); ?>

                                            </td>
                                            <td class="sticky-col-2 border border-slate-300 px-2 py-1 align-middle font-semibold text-slate-800">
                                                <?php echo e($district); ?>

                                            </td>
                                            <td class="total-amount-col border border-slate-300 px-2 py-1 text-right text-slate-700">
                                                <?php $districtTotal = $values['Жами'] ?? 0; ?>
                                                <?php if($districtTotal > 0): ?>
                                                    <a href="<?php echo e(route('yer-sotuvlar.fin-xisobot.details', array_merge($activeFilterParams, ['district' => $district]))); ?>" class="block text-blue-700 hover:text-blue-900 hover:underline text-right">
                                                        <span class="font-semibold"><?php echo e($fmt($districtTotal)); ?></span><br>
                                                        <span class="text-slate-400"><?php echo e($districtCounts[$district] ?? 0); ?> та</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-slate-300">—</span>
                                                <?php endif; ?>
                                            </td>

                                            <?php $__currentLoopData = $paymentCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <td class="border border-slate-300 px-2 py-1 text-right text-slate-700">
                                                    <?php
                                                        $cellAmount = $values[$category] ?? 0;
                                                        $isSyntheticDistrictCell = !empty($proportionalCategoryLookup[$category]);
                                                    ?>
                                                    <?php if($cellAmount > 0): ?>
                                                        <?php if($isSyntheticDistrictCell): ?>
                                                            <span class="block text-right text-slate-700" title="Пропорция бўйича ҳисобланган">
                                                                <span class="font-semibold"><?php echo e($fmt($cellAmount)); ?></span><br>
                                                                <span class="text-slate-400"><?php echo e($districtCategoryCounts[$district][$category] ?? 0); ?> та</span>
                                                            </span>
                                                        <?php else: ?>
                                                            <a href="<?php echo e(route('yer-sotuvlar.fin-xisobot.details', array_merge($activeFilterParams, ['district' => $district, 'category' => $category]))); ?>" class="block text-blue-700 hover:text-blue-900 hover:underline text-right">
                                                                <span class="font-semibold"><?php echo e($fmt($cellAmount)); ?></span><br>
                                                                <span class="text-slate-400"><?php echo e($districtCategoryCounts[$district][$category] ?? 0); ?> та</span>
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-slate-300">—</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                            <?php
                                                $districtCategorySum = 0.0;
                                                foreach ($paymentCategories as $categoryName => $categoryValue) {
                                                    $districtCategorySum += (float)($values[$categoryName] ?? 0);
                                                }
                                                $districtQoldiq = (float)$districtTotal - $districtCategorySum;
                                                if (abs($districtQoldiq) < 0.01) {
                                                    $districtQoldiq = 0.0;
                                                }
                                            ?>
                                            <td class="border border-slate-300 px-2 py-1 text-right text-slate-700">
                                                <?php if($districtQoldiq > 0): ?>
                                                    <span class="font-semibold text-rose-700"><?php echo e($fmt($districtQoldiq)); ?></span>
                                                <?php else: ?>
                                                    <span class="text-slate-300">—</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-t-4 border-blue-600">
                <div class="p-5 bg-gradient-to-br from-slate-50 to-blue-50">
                    <form method="GET" action="<?php echo e(route('yer-sotuvlar.fin-xisobot')); ?>" id="fin-filter-form" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Йил</label>
                                <select
                                    name="year"
                                    onchange="this.form.submit()"
                                    class="w-full px-3 py-2 border-2 border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                >
                                    <option value="">Барча йиллар</option>
                                    <?php $__currentLoopData = $yearSelectOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yearOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($yearOption); ?>" <?php if((int)($filters['year'] ?? 0) === (int)$yearOption): echo 'selected'; endif; ?>>
                                            <?php echo e($yearOption); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ой</label>
                                <?php if(!empty($filters['year'])): ?>
                                    <select
                                        name="month"
                                        onchange="this.form.submit()"
                                        class="w-full px-3 py-2 border-2 border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                    >
                                        <option value="">Барча ойлар</option>
                                        <?php $__currentLoopData = $monthOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthNumber => $monthLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($monthNumber); ?>" <?php if((int)($filters['month'] ?? 0) === (int)$monthNumber): echo 'selected'; endif; ?>>
                                                <?php echo e($monthLabel); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                <?php else: ?>
                                    <div class="w-full px-3 py-2 border-2 border-dashed border-slate-300 rounded-lg text-sm text-slate-500 bg-white">
                                        Ой фильтри йил танлангандан кейин чиқади
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="flex gap-2">
                                <button
                                    type="submit"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2 px-3 rounded-lg shadow transition-all"
                                >
                                    Қўллаш
                                </button>
                                <a
                                    href="<?php echo e(route('yer-sotuvlar.fin-xisobot')); ?>"
                                    class="flex-1 bg-slate-500 hover:bg-slate-600 text-white text-sm font-bold py-2 px-3 rounded-lg shadow transition-all text-center"
                                >
                                    Тозалаш
                                </a>
                            </div>
                        </div>

                        <details class="group pt-2" <?php echo e((!empty($filters['date_from']) || !empty($filters['date_to'])) ? 'open' : ''); ?>>
                            <summary class="flex items-center gap-2 text-xs font-bold text-slate-500 cursor-pointer select-none hover:text-blue-600 transition-colors list-none">
                                <svg class="w-3.5 h-3.5 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                                <span class="uppercase tracking-wide">Батафсил фильтр: бошланғич ва тугаш сана</span>
                                <?php if(!empty($filters['date_from']) || !empty($filters['date_to'])): ?>
                                    <span class="ml-1 px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full font-bold">Амалдаги</span>
                                <?php endif; ?>
                            </summary>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Бошланғич сана</label>
                                    <input
                                        type="date"
                                        name="date_from"
                                        value="<?php echo e($filters['date_from'] ?? ''); ?>"
                                        class="w-full px-3 py-2 border-2 border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                    >
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Тугаш сана</label>
                                    <input
                                        type="date"
                                        name="date_to"
                                        value="<?php echo e($filters['date_to'] ?? ''); ?>"
                                        class="w-full px-3 py-2 border-2 border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                    >
                                </div>

                                <div class="flex items-end">
                                    <button
                                        type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded-lg shadow transition-all"
                                    >
                                        Сана фильтрини қўллаш
                                    </button>
                                </div>
                            </div>
                        </details>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .statistics-table {
            --index-col-width: 40px;
            --district-col-width: 150px;
            --sticky-total-width: calc(var(--index-col-width) + var(--district-col-width));
            width: max-content;
            min-width: 100%;
        }

        .statistics-table th,
        .statistics-table td {
            box-sizing: border-box;
            font-size: 11px;
        }

        .statistics-table th {
            white-space: normal;
            line-height: 1.25;
            word-break: break-word;
        }

        .sticky-col {
            position: sticky;
            left: 0;
            z-index: 20;
            background-color: inherit;
        }

        .sticky-col-2 {
            position: sticky;
            left: var(--index-col-width);
            z-index: 20;
            background-color: inherit;
        }

        .sticky-col-total {
            position: sticky;
            left: 0;
            z-index: 21;
            width: var(--sticky-total-width);
            min-width: var(--sticky-total-width);
            max-width: var(--sticky-total-width);
            background-color: inherit;
        }

        .statistics-table th.sticky-col,
        .statistics-table td.sticky-col {
            width: var(--index-col-width);
            min-width: var(--index-col-width);
            max-width: var(--index-col-width);
        }

        .statistics-table th.sticky-col-2,
        .statistics-table td.sticky-col-2 {
            width: var(--district-col-width);
            min-width: var(--district-col-width);
            max-width: var(--district-col-width);
        }

        .statistics-table thead .sticky-col,
        .statistics-table thead .sticky-col-2,
        .statistics-table thead .sticky-col-total {
            z-index: 30;
        }

        .sticky-col,
        .sticky-col-2,
        .sticky-col-total {
            box-shadow: 1px 0 0 #cbd5e1;
        }

        .total-amount-col {
            min-width: 120px;
            white-space: nowrap;
        }

        .statistics-table a:hover {
            background-color: rgba(219, 234, 254, 0.4);
            border-radius: 4px;
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
            .sticky-col-2,
            .sticky-col-total {
                position: static;
            }

            body {
                background: white;
            }
        }
    </style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\inves\OneDrive\Ishchi stol\yer-uchastkalar\resources\views\yer-sotuvlar\fin-xisobot.blade.php ENDPATH**/ ?>