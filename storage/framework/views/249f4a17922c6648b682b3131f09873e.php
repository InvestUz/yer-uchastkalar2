<?php $__env->startSection('title', 'Бўлиб тўлаш маълумоти'); ?>

<?php $__env->startSection('content'); ?>
    <?php
    $fmt = function($amount) {
        if ($amount >= 1_000_000_000_000) {
            return number_format($amount / 1_000_000_000_000, 2, '.', ',') . ' трлн';
        } elseif ($amount >= 1_000_000_000) {
            return number_format($amount / 1_000_000_000, 1, '.', ',') . ' млрд';
        } elseif ($amount >= 1_000_000) {
            return number_format($amount / 1_000_000, 0, '.', ',') . ' млн';
        }
        return number_format($amount, 0, '.', ',');
    };
    ?>
    <!-- Main Content -->
            <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-6 px-4">
        <div class="max-w-[98%] mx-auto">
            <!-- Premium Government Header -->
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden mb-6 border-t-4 border-blue-600">
                <div class="bg-white px-8 py-6">
                    <div class="flex items-center justify-center space-x-4">
                        <div class="text-center">
                            <h1 class="text-lg font-bold text-blue tracking-wide mb-1">
                                Тошкент шаҳрида аукцион савдоларида бўлиб тўлаш шарти билан сотилган ер участкалари
                                тўғрисида
                            </h1>
                            <h2 class="text-base font-semibold text-blue">
                                ЙИҒМА МАЪЛУМОТ
                            </h2>
                            <p class="text-xs text-slate-500 mt-1">
                                Сумма ёки сони устига босинг: тизим танланган ҳудуд/тоифа бўйича детал рўйхатни очади.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Statistics Table -->
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="border-collapse statistics-table">
                            <thead>
                                <tr style="background:#eff6ff !important;">
                                    <th rowspan="2" class="sticky-col border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800" style="width: 40px; min-width: 40px; max-width: 40px; font-size:11px;">
                                        Т/р
                                    </th>
                                    <th rowspan="2" class="sticky-col-2 border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800" style="width: 150px; min-width: 150px; max-width: 150px; font-size:11px;">
                                        Ҳудудлар
                                    </th>
                                    <th rowspan="2" class="total-amount-col border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800" style="font-size:11px;">
                                        Жами (сум)
                                    </th>

                                    <?php $__currentLoopData = $paymentCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th class="border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800" style="min-width: 100px; font-size:11px;">
                                            <?php echo e($category); ?>

                                        </th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>

                            <tbody class="bg-white">
                                <?php if(empty($districtData) || count($districtData) === 0): ?>
                                    <tr>
                                        <td colspan="<?php echo e(3 + count($paymentCategories)); ?>" class="border border-slate-300 px-4 py-6 text-center text-slate-700">
                                            Маълумотлар топилмади.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr class="bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100 border-y-2 border-amber-400">
                                        <td colspan="2" class="sticky-col-total border border-slate-300 px-3 py-2 text-center align-middle font-bold text-slate-900 text-xs uppercase bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100">
                                            ЖАМИ:
                                        </td>
                                        <td class="total-amount-col border border-slate-300 px-2 py-1 text-right font-bold text-slate-900">
                                            <a href="<?php echo e(route('yer-sotuvlar.fin-xisobot.details')); ?>" class="block text-blue-700 hover:text-blue-900 hover:underline">
                                                <span class="font-semibold"><?php echo e($fmt($totalAmount)); ?></span><br>
                                                <span class="text-slate-400"><?php echo e($transactionCount ?? 0); ?> та</span>
                                            </a>
                                        </td>

                                        <?php $__currentLoopData = $paymentCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td class="border border-slate-300 px-2 py-1 text-right font-bold text-slate-900">
                                                <?php $catTotal = $categoryTotals[$category] ?? 0; ?>
                                                <?php if($catTotal > 0): ?>
                                                    <a href="<?php echo e(route('yer-sotuvlar.fin-xisobot.details', ['category' => $category])); ?>" class="block text-blue-700 hover:text-blue-900 hover:underline text-right">
                                                        <span class="font-semibold"><?php echo e($fmt($catTotal)); ?></span><br>
                                                        <span class="text-slate-400"><?php echo e($categoryCounts[$category] ?? 0); ?> та</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-slate-300">—</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                                    <a href="<?php echo e(route('yer-sotuvlar.fin-xisobot.details', ['district' => $district])); ?>" class="block text-blue-700 hover:text-blue-900 hover:underline text-right">
                                                        <span class="font-semibold"><?php echo e($fmt($districtTotal)); ?></span><br>
                                                        <span class="text-slate-400"><?php echo e($districtCounts[$district] ?? 0); ?> та</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-slate-300">—</span>
                                                <?php endif; ?>
                                            </td>

                                            <?php $__currentLoopData = $paymentCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <td class="border border-slate-300 px-2 py-1 text-right text-slate-700">
                                                    <?php $cellAmount = $values[$category] ?? 0; ?>
                                                    <?php if($cellAmount > 0): ?>
                                                        <a href="<?php echo e(route('yer-sotuvlar.fin-xisobot.details', ['district' => $district, 'category' => $category])); ?>" class="block text-blue-700 hover:text-blue-900 hover:underline text-right">
                                                            <span class="font-semibold"><?php echo e($fmt($cellAmount)); ?></span><br>
                                                            <span class="text-slate-400"><?php echo e($districtCategoryCounts[$district][$category] ?? 0); ?> та</span>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-slate-300">—</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- Premium Filter Section -->
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-t-4 border-blue-600">

                <div class="p-6 bg-gradient-to-br from-slate-50 to-blue-50">
                    <form method="GET" action="http://127.0.0.1:8000/svod3">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">


                            <!-- Date From -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Бошланғич санаси:</label>
                                <input type="date" name="auksion_sana_from" class="w-full px-4 py-3 border-2 border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" value="">
                            </div>

                            <!-- Date To -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Тугаш санаси:</label>
                                <input type="date" name="auksion_sana_to" class="w-full px-4 py-3 border-2 border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" value="">
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-4 mt-6">
                                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Қидириш
                                </button>
                                <a href="http://127.0.0.1:8000/umumiy" class="flex-1 bg-gradient-to-r from-slate-500 to-slate-600 hover:from-slate-600 hover:to-slate-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Тозалаш
                                </a>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Sticky columns for horizontal scroll */
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

        .statistics-table td {
            font-size: 11px;
        }

        .statistics-table a:hover {
            background-color: rgba(219, 234, 254, 0.4);
            border-radius: 4px;
        }

        /* Smooth scrollbar */
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

        /* Print styles */
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


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\inves\OneDrive\Ishchi stol\yer-uchastkalar\resources\views/yer-sotuvlar/fin-xisobot.blade.php ENDPATH**/ ?>