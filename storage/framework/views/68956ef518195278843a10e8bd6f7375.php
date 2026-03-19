

<?php $__env->startSection('title', 'Фин-ҳисобот деталлар'); ?>

<?php $__env->startSection('content'); ?>
<?php
$fmtBillion = function($amount) {
    return number_format(((float)$amount) / 1_000_000_000, 1, '.', ',');
};

$fmtRaw = function($amount) {
    return number_format((float)$amount, 2, '.', ',');
};

$filters = $filters ?? [
    'year' => null,
    'month' => null,
    'date_from' => null,
    'date_to' => null,
];
$activeFilterParams = $activeFilterParams ?? [];
$monthOptions = $monthOptions ?? [];
$listRouteParams = $listRouteParams ?? [];
$detailsMode = $detailsMode ?? 'financial';

$periodParts = [];
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
$activeFilterText = !empty($periodParts) ? implode(' | ', $periodParts) : 'Барча давр';
?>
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-6 px-4">
    <div class="max-w-[98%] mx-auto space-y-6">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-t-4 border-blue-600">
            <div class="px-6 py-5 bg-gradient-to-r from-blue-50 to-slate-50 border-b border-slate-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">ФИН-ҲИСОБОТ ДЕТАЛ РЎЙХАТИ</h1>
                        <p class="text-sm text-slate-600 mt-1">
                            Танланган фильтр:
                            <span class="font-semibold"><?php echo e($selectedDistrict); ?></span>
                            /
                            <span class="font-semibold"><?php echo e($selectedCategory); ?></span>
                        </p>
                        <p class="text-xs text-blue-700 mt-1">
                            Давр фильтри: <span class="font-semibold"><?php echo e($activeFilterText); ?></span>
                        </p>
                        <p class="text-xs text-slate-500 mt-1">Барча сумма кўрсаткичлари млрд сўмда.</p>
                    </div>
                    <div class="flex gap-2">
                        <?php if($detailsMode === 'monitoring'): ?>
                            <a href="<?php echo e(route('yer-sotuvlar.list', $listRouteParams)); ?>" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-semibold">
                                Мониторинг рўйхат
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('yer-sotuvlar.fin-xisobot', $activeFilterParams)); ?>" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            Орқага
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-1 gap-4 bg-white">
                <div class="rounded-lg border border-slate-200 p-4 bg-slate-50">
                    <p class="text-xs uppercase tracking-wide text-slate-500">
                        <?php echo e($detailsMode === 'monitoring' ? 'Жами тушган маблағ' : 'Жами сумма'); ?>

                    </p>
                    <p class="text-2xl font-bold text-slate-800"><?php echo e($fmtBillion($totalAmount ?? 0)); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-t-4 border-blue-600">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <?php if($detailsMode === 'monitoring'): ?>
                            <tr class="bg-blue-50">
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Т/р</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Лот</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Ҳудуд</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Манзил</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Аукцион сана</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Тўлов тури</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Ҳолат</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Тушадиган</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Тушган</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Қолдиқ</th>
                            </tr>
                        <?php else: ?>
                            <tr class="bg-blue-50">
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Т/р</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Сана</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Ҳужжат №</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Лот</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Ҳудуд</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Тоифа</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Олувчи</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Модда</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Сумма</th>
                                <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Детали</th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="border border-slate-200 px-3 py-2 text-center text-sm text-slate-700"><?php echo e($loop->iteration); ?></td>
                                <?php if($detailsMode === 'monitoring'): ?>
                                    <td class="border border-slate-200 px-3 py-2 text-center text-sm text-slate-700 whitespace-nowrap">
                                        <?php if(!empty($row['lot_raqami'])): ?>
                                            <a href="<?php echo e(route('yer-sotuvlar.show', ['lot_raqami' => $row['lot_raqami']])); ?>" class="text-blue-700 hover:text-blue-900 hover:underline font-semibold">
                                                <?php echo e($row['lot_raqami']); ?>

                                            </a>
                                        <?php else: ?>
                                            <span class="text-slate-300">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="border border-slate-200 px-3 py-2 text-sm text-slate-700"><?php echo e($row['district']); ?></td>
                                    <td class="border border-slate-200 px-3 py-2 text-sm text-slate-700"><?php echo e($row['address']); ?></td>
                                    <td class="border border-slate-200 px-3 py-2 text-center text-sm text-slate-700"><?php echo e($row['auction_date']); ?></td>
                                    <td class="border border-slate-200 px-3 py-2 text-center text-sm text-slate-700"><?php echo e($row['payment_type']); ?></td>
                                    <td class="border border-slate-200 px-3 py-2 text-sm text-slate-700"><?php echo e($row['status']); ?></td>
                                    <td class="border border-slate-200 px-3 py-2 text-right text-sm font-semibold text-slate-800"><?php echo e($fmtBillion($row['expected'])); ?></td>
                                    <td class="border border-slate-200 px-3 py-2 text-right text-sm font-semibold text-slate-800"><?php echo e($fmtBillion($row['received'])); ?></td>
                                    <td class="border border-slate-200 px-3 py-2 text-right text-sm font-semibold text-slate-800"><?php echo e($fmtBillion($row['qoldiq'])); ?></td>
                                <?php else: ?>
                                    <td class="border border-slate-200 px-3 py-2 text-center text-sm text-slate-700"><?php echo e($row['date'] ?? ''); ?></td>
                                    <td class="border border-slate-200 px-3 py-2 text-center text-sm text-slate-700"><?php echo e($row['doc_num'] ?? ''); ?></td>
                                    <td class="border border-slate-200 px-3 py-2 text-center text-sm text-slate-700 whitespace-nowrap">
                                        <?php if(!empty($row['lot_raqami'])): ?>
                                            <a href="<?php echo e(route('yer-sotuvlar.show', ['lot_raqami' => $row['lot_raqami']])); ?>" class="text-blue-700 hover:text-blue-900 hover:underline font-semibold">
                                                <?php echo e($row['lot_raqami']); ?>

                                            </a>
                                            <?php if(!empty($row['lot_match_source'])): ?>
                                                <div class="text-[10px] text-slate-400 mt-0.5"><?php echo e($row['lot_match_source']); ?></div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-slate-300">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="border border-slate-200 px-3 py-2 text-sm text-slate-700"><?php echo e($row['district'] ?? ''); ?></td>
                                    <td class="border border-slate-200 px-3 py-2 text-sm text-slate-700"><?php echo e($row['category'] ?? ''); ?></td>
                                    <td class="border border-slate-200 px-3 py-2 text-sm text-slate-700"><?php echo e($row['recipient'] ?? ''); ?></td>
                                    <td class="border border-slate-200 px-3 py-2 text-sm text-slate-700"><?php echo e($row['article'] ?? ''); ?></td>
                                    <td class="border border-slate-200 px-3 py-2 text-right text-sm font-semibold text-slate-800"><?php echo e($fmtRaw($row['amount'] ?? 0)); ?></td>
                                    <td class="border border-slate-200 px-3 py-2 text-xs text-slate-600 break-words min-w-[380px]"><?php echo e($row['details'] ?? ''); ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="10" class="border border-slate-300 px-4 py-6 text-center text-slate-600">
                                    Танланган фильтр бўйича маълумот топилмади.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\inves\OneDrive\Ishchi stol\yer-uchastkalar\resources\views/yer-sotuvlar/fin-xisobot-details.blade.php ENDPATH**/ ?>