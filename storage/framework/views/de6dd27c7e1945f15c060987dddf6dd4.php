<?php $__env->startSection('title', 'Глобал қолдиқлар'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-100 py-6 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow mb-6 border-t-4 border-blue-600">
            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-blue-800">
                        Глобал қолдиқлар бошқаруви
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Ҳисобот саналарига нисбатан қолдиқларни бошқариш
                    </p>
                </div>
                <a href="<?php echo e(route('qoldiq.create')); ?>"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded transition-colors">
                    + Янги қолдиқ қўшиш
                </a>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <!-- Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-900 mb-2">📘 Қолдиқ қандай ишлайди:</h3>
            <ul class="text-sm text-blue-800 space-y-1 ml-4">
                <li>• <strong>Plus (+)</strong> - Қолдиқ факт тўловларга қўшилади</li>
                <li>• <strong>Minus (-)</strong> - Қолдиқ факт тўловлардан айрилади</li>
                <li>• Мисол: 2024-01-01 учун 48,754,073,412.35 сўм (+) = барча факт тўловларга қўшилади</li>
                <li>• Мисол: 2025-11-01 учун 56,564,353,036.04 сўм (-) = барча факт тўловлардан айрилади</li>
            </ul>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <?php if($qoldiqlar->count() > 0): ?>
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                №
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Сана
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Сумма
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Тур
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Изоҳ
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Амаллар
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $qoldiqlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $qoldiq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e($index + 1); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo e($qoldiq->sana->format('d.m.Y')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">
                                    <?php echo e(number_format($qoldiq->summa, 2, '.', ' ')); ?> сўм
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?php if($qoldiq->tur === 'plus'): ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            + Қўшиш
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            - Айириш
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?php echo e($qoldiq->izoh ?? '-'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="<?php echo e(route('qoldiq.edit', $qoldiq->id)); ?>"
                                       class="text-blue-600 hover:text-blue-900 mr-3">
                                        Таҳрирлаш
                                    </a>
                                    <form action="<?php echo e(route('qoldiq.destroy', $qoldiq->id)); ?>"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Ўчиришга ишонchingizми?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Ўчириш
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center py-12">
                    <p class="text-gray-500 text-lg">Ҳали қолдиқлар қўшилмаган</p>
                    <a href="<?php echo e(route('qoldiq.create')); ?>"
                       class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded">
                        Биринчи қолдиқни қўшиш
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Back Button -->
        <div class="mt-6">
            <a href="<?php echo e(route('yer-sotuvlar.index')); ?>"
               class="inline-flex items-center text-blue-600 hover:text-blue-800">
                ← Асосий саҳифага қайтиш
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\inves\OneDrive\Ishchi stol\yer-uchastkalar\resources\views\qoldiq\index.blade.php ENDPATH**/ ?>