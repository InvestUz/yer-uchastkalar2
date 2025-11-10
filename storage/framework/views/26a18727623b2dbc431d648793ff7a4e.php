<?php $__env->startSection('title', 'Ойлик динамика мониторинги'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-100 py-6 px-4">
    <div class="max-w-[98%] mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow mb-6 border-t-4 border-blue-600">
            <div class="px-6 py-5 border-b border-gray-200">
                <h1 class="text-xl font-bold text-blue-800 text-center">
                    Сотилган ерлардан <?php echo e($comparativeData['meta']['selected_year']); ?> йилда пул тушириш динамикаси
                </h1>
                <p class="text-blue-600 text-center mt-1 text-sm font-semibold">ОЙЛИК МАЪЛУМОТ</p>
            </div>

            <!-- Filters -->
            <div class="p-5 bg-gray-50">
                <form method="GET" action="<?php echo e(route('yer-sotuvlar.monitoring_mirzayev')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Йил:</label>
                        <select name="year" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500">
                            <?php $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($year); ?>" <?php echo e($filters['year'] == $year ? 'selected' : ''); ?>>
                                    <?php echo e($year); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ой:</label>
                        <select name="month" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500">
                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthNum => $monthName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($monthNum); ?>" <?php echo e($filters['month'] == $monthNum ? 'selected' : ''); ?>>
                                    <?php echo e($monthName); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded text-sm transition-colors">
                            Кўрсатиш
                        </button>
                    </div>

                    <div class="flex items-end">
                        <a href="<?php echo e(route('yer-sotuvlar.monitoring_mirzayev')); ?>" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded text-sm transition-colors text-center">
                            Тозалаш
                        </a>
                    </div>
                </form>
            </div>
        </div>

    

        <!-- Main Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse" style="font-size: 11px;">
                    <thead>
                        <!-- Main Title Row -->
                        <tr style="background-color: #EFF6FF;">
                            <th rowspan="2" class="border border-gray-400 px-2 py-2.5 text-center align-middle font-bold text-gray-800" style="min-width: 40px;">т/р</th>
                            <th rowspan="2" class="border border-gray-400 px-2 py-2.5 text-center align-middle font-bold text-gray-800" style="min-width: 180px;">Ҳудудлар</th>
                            
                            <!-- Selected Month -->
                            <th colspan="3" class="border border-gray-400 px-2 py-2 text-center font-bold text-gray-800">
                                <?php echo e($comparativeData['meta']['selected_month_name']); ?>

                            </th>
                            
                            <!-- Year to Date -->
                            <th colspan="3" class="border border-gray-400 px-2 py-2 text-center font-bold text-gray-800">
                                За <?php echo e($comparativeData['meta']['selected_month']); ?> мес.
                            </th>
                            
                            <!-- Full Year -->
                            <th colspan="3" class="border border-gray-400 px-2 py-2 text-center font-bold text-gray-800">
                                <?php echo e($comparativeData['meta']['selected_year']); ?>г.
                            </th>
                        </tr>

                        <!-- Sub Headers -->
                        <tr style="background-color: #EFF6FF;">
                            <!-- Selected Month -->
                            <th class="border border-gray-400 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 80px;">План</th>
                            <th class="border border-gray-400 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 80px;">Факт</th>
                            <th class="border border-gray-400 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 60px;">+/-</th>

                            <!-- Year to Date -->
                            <th class="border border-gray-400 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 80px;">План</th>
                            <th class="border border-gray-400 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 80px;">Факт</th>
                            <th class="border border-gray-400 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 60px;">+/-</th>

                            <!-- Full Year -->
                            <th class="border border-gray-400 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 80px;">План</th>
                            <th class="border border-gray-400 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 80px;">Факт</th>
                            <th class="border border-gray-400 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 60px;">+/-</th>
                        </tr>
                    </thead>

                    <tbody>
                        <!-- JAMI (Total) Row - First -->
                        <tr style="background-color: #FEF3C7;">
                            <td colspan="2" class="border border-gray-400 px-3 py-2.5 text-left font-bold text-gray-900 uppercase">
                                ЖАМИ:
                            </td>

                            <!-- Selected Month Total -->
                            <td class="border border-gray-400 px-2 py-2.5 text-right font-bold text-gray-900">
                                <?php echo e(number_format($comparativeData['jami']['selected_month']['plan'] / 1000000, 1)); ?>

                            </td>
                            <td class="border border-gray-400 px-2 py-2.5 text-right font-bold text-red-700">
                                <?php echo e(number_format($comparativeData['jami']['selected_month']['fakt'] / 1000000, 1)); ?>

                            </td>
                            <td class="border border-gray-400 px-2 py-2.5 text-center font-bold text-gray-900">
                                <?php echo e($comparativeData['jami']['selected_month']['percentage']); ?>%
                            </td>

                            <!-- Year to Date Total -->
                            <td class="border border-gray-400 px-2 py-2.5 text-right font-bold text-gray-900">
                                <?php echo e(number_format($comparativeData['jami']['year_to_date']['plan'] / 1000000, 1)); ?>

                            </td>
                            <td class="border border-gray-400 px-2 py-2.5 text-right font-bold text-red-700">
                                <?php echo e(number_format($comparativeData['jami']['year_to_date']['fakt'] / 1000000, 1)); ?>

                            </td>
                            <td class="border border-gray-400 px-2 py-2.5 text-center font-bold text-gray-900">
                                <?php echo e($comparativeData['jami']['year_to_date']['percentage']); ?>%
                            </td>

                            <!-- Full Year Total -->
                            <td class="border border-gray-400 px-2 py-2.5 text-right font-bold text-gray-900">
                                <?php echo e(number_format($comparativeData['jami']['full_year']['plan'] / 1000000, 1)); ?>

                            </td>
                            <td class="border border-gray-400 px-2 py-2.5 text-right font-bold text-red-700">
                                <?php echo e(number_format($comparativeData['jami']['full_year']['fakt'] / 1000000, 1)); ?>

                            </td>
                            <td class="border border-gray-400 px-2 py-2.5 text-center font-bold text-gray-900">
                                <?php echo e($comparativeData['jami']['full_year']['percentage']); ?>%
                            </td>
                        </tr>

                        <!-- Tuman Rows -->
                        <?php $__currentLoopData = $comparativeData['tumanlar']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tumanData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="<?php echo e($index % 2 == 0 ? 'bg-white' : 'bg-gray-50'); ?> hover:bg-blue-50">
                                <!-- Row Number -->
                                <td class="border border-gray-400 px-2 py-2 text-center font-medium text-gray-700"><?php echo e($index + 1); ?></td>
                                
                                <!-- Tuman Name -->
                                <td class="border border-gray-400 px-2 py-2 font-medium text-gray-800"><?php echo e($tumanData['tuman']); ?></td>

                                <!-- Selected Month -->
                                <td class="border border-gray-400 px-2 py-2 text-right text-gray-700">
                                    <?php echo e(number_format($tumanData['selected_month']['plan'] / 1000000, 1)); ?>

                                </td>
                                <td class="border border-gray-400 px-2 py-2 text-right font-semibold <?php echo e($tumanData['selected_month']['fakt'] > 0 ? 'text-blue-700' : 'text-gray-700'); ?>">
                                    <?php echo e(number_format($tumanData['selected_month']['fakt'] / 1000000, 1)); ?>

                                </td>
                                <td class="border border-gray-400 px-2 py-2 text-center font-semibold text-gray-800">
                                    <?php echo e($tumanData['selected_month']['percentage']); ?>%
                                </td>

                                <!-- Year to Date -->
                                <td class="border border-gray-400 px-2 py-2 text-right text-gray-700">
                                    <?php echo e(number_format($tumanData['year_to_date']['plan'] / 1000000, 1)); ?>

                                </td>
                                <td class="border border-gray-400 px-2 py-2 text-right font-semibold <?php echo e($tumanData['year_to_date']['fakt'] > 0 ? 'text-blue-700' : 'text-gray-700'); ?>">
                                    <?php echo e(number_format($tumanData['year_to_date']['fakt'] / 1000000, 1)); ?>

                                </td>
                                <td class="border border-gray-400 px-2 py-2 text-center font-semibold text-gray-800">
                                    <?php echo e($tumanData['year_to_date']['percentage']); ?>%
                                </td>

                                <!-- Full Year -->
                                <td class="border border-gray-400 px-2 py-2 text-right text-gray-700">
                                    <?php echo e(number_format($tumanData['full_year']['plan'] / 1000000, 1)); ?>

                                </td>
                                <td class="border border-gray-400 px-2 py-2 text-right font-semibold <?php echo e($tumanData['full_year']['fakt'] > 0 ? 'text-blue-700' : 'text-gray-700'); ?>">
                                    <?php echo e(number_format($tumanData['full_year']['fakt'] / 1000000, 1)); ?>

                                </td>
                                <td class="border border-gray-400 px-2 py-2 text-center font-semibold text-gray-800">
                                    <?php echo e($tumanData['full_year']['percentage']); ?>%
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Note -->
            <div class="p-3 bg-gray-50 border-t border-gray-300">
                <p class="text-xs text-gray-600">
                    <strong>Маълумот:</strong> млн.сум (миллион сўм) бирлигида кўрсатилган. 
                    План - График тўловлар, Факт - Тўланган маблағлар.
                </p>
            </div>
        </div>

            <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <!-- Selected Month Card -->
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <h3 class="text-sm font-bold text-gray-700 mb-3 pb-2 border-b border-gray-200">
                    <?php echo e($comparativeData['meta']['selected_month_name']); ?>

                </h3>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-600">План:</span>
                        <span class="font-semibold text-gray-800"><?php echo e(number_format($comparativeData['jami']['selected_month']['plan'] / 1000000000, 2)); ?> млрд</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Факт:</span>
                        <span class="font-semibold text-blue-700"><?php echo e(number_format($comparativeData['jami']['selected_month']['fakt'] / 1000000000, 2)); ?> млрд</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                        <span class="text-gray-600">Тўланганлик:</span>
                        <span class="text-lg font-bold text-gray-800">
                            <?php echo e($comparativeData['jami']['selected_month']['percentage']); ?>%
                        </span>
                    </div>
                </div>
            </div>

            <!-- Year to Date Card -->
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <h3 class="text-sm font-bold text-gray-700 mb-3 pb-2 border-b border-gray-200">
                    <?php echo e($comparativeData['meta']['selected_month']); ?> ой якуни
                </h3>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-600">План:</span>
                        <span class="font-semibold text-gray-800"><?php echo e(number_format($comparativeData['jami']['year_to_date']['plan'] / 1000000000, 2)); ?> млрд</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Факт:</span>
                        <span class="font-semibold text-blue-700"><?php echo e(number_format($comparativeData['jami']['year_to_date']['fakt'] / 1000000000, 2)); ?> млрд</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                        <span class="text-gray-600">Тўланганлик:</span>
                        <span class="text-lg font-bold text-gray-800">
                            <?php echo e($comparativeData['jami']['year_to_date']['percentage']); ?>%
                        </span>
                    </div>
                </div>
            </div>

            <!-- Full Year Card -->
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <h3 class="text-sm font-bold text-gray-700 mb-3 pb-2 border-b border-gray-200">
                    <?php echo e($comparativeData['meta']['selected_year']); ?> йил
                </h3>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-600">План:</span>
                        <span class="font-semibold text-gray-800"><?php echo e(number_format($comparativeData['jami']['full_year']['plan'] / 1000000000, 2)); ?> млрд</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Факт:</span>
                        <span class="font-semibold text-blue-700"><?php echo e(number_format($comparativeData['jami']['full_year']['fakt'] / 1000000000, 2)); ?> млрд</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                        <span class="text-gray-600">Тўланганлик:</span>
                        <span class="text-lg font-bold text-gray-800">
                            <?php echo e($comparativeData['jami']['full_year']['percentage']); ?>%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\inves\OneDrive\Ishchi stol\yer-uchastkalar\resources\views/yer-sotuvlar/monitoring_mirzayev.blade.php ENDPATH**/ ?>