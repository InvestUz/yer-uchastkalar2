

<?php $__env->startSection('title', 'Мониторинг ва Аналитика'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-[98%] mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800 mb-2">
            Мониторинг ва Аналитика
        </h1>
        <p class="text-slate-600">График ва факт тўловларнинг таққослаш ва умумий статистика</p>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" action="<?php echo e(route('yer-sotuvlar.monitoring')); ?>" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Аукцион санаси (дан)</label>
                <input type="date" name="auksion_sana_from" value="<?php echo e($dateFilters['auksion_sana_from'] ?? ''); ?>"
                       class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Аукцион санаси (гача)</label>
                <input type="date" name="auksion_sana_to" value="<?php echo e($dateFilters['auksion_sana_to'] ?? ''); ?>"
                       class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                Филтрлаш
            </button>
            <?php if(!empty($dateFilters['auksion_sana_from']) || !empty($dateFilters['auksion_sana_to'])): ?>
                <a href="<?php echo e(route('yer-sotuvlar.monitoring')); ?>" class="px-6 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-lg font-semibold transition-all duration-300">
                    Тозалаш
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Lots -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-700">Жами лотлар (боьлиб)</h3>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold" style="color: rgb(185, 28, 28);"><?php echo e(number_format($summary['total_lots'])); ?></p>
            <p class="text-xs text-slate-500 mt-1">Бўлиб тўлов қисм</p>
        </div>

        <!-- Expected Amount -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-700">Тушадиган маблағ</h3>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold" style="color: rgb(29, 78, 216);"><?php echo e(number_format($summary['expected_amount'] / 1000000000, 2)); ?></p>
            <p class="text-xs text-slate-500 mt-1">млрд сўм</p>
        </div>

        <!-- Received Amount -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-700">Тушган маблағ</h3>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold" style="color: rgb(29, 78, 216);"><?php echo e(number_format($summary['received_amount'] / 1000000000, 2)); ?></p>
            <p class="text-xs text-slate-500 mt-1">млрд сўм</p>
        </div>

        <!-- Payment Percentage -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-700">Тўлов фоизи</h3>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold" style="color: rgb(29, 78, 216);"><?php echo e(number_format($summary['payment_percentage'], 1)); ?>%</p>
            <p class="text-xs text-slate-500 mt-1">Умумий тўлов даражаси</p>
        </div>
    </div>

    <!-- Main Charts -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mb-8">
        <!-- Payment Status Distribution -->
        <div class="xl:col-span-4 bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
                Ойлик график vs факт тўловлар
            </h3>
            <div class="h-72">
                <canvas id="monthlyComparisonChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tuman Analysis -->
    <div class="grid grid-cols-1 gap-6 mb-8">
        <!-- Grafik vs Fakt by Tuman -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Туманлар бўйича график ва факт тўловлар таққослаш
            </h3>
            <div class="h-[32rem]">
                <canvas id="tumanComparisonChart"></canvas>
            </div>
        </div>
    </div>


    <!-- Top Performers and Needs Attention -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Top Performers -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                Энг яхши натижа (топ 6)
            </h3>
            <div class="space-y-2.5">
                <?php $__currentLoopData = collect($tumanStats)->sortByDesc('percentage')->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg hover:from-green-100 hover:to-emerald-100 transition-all duration-300 border border-green-200">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center font-bold text-sm shadow-md">
                            <?php echo e($index + 1); ?>

                        </div>
                        <span class="font-semibold text-slate-800 text-sm"><?php echo e($stat['tuman']); ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-20 bg-green-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full" style="width: <?php echo e(min($stat['percentage'], 100)); ?>%"></div>
                        </div>
                        <span class="text-base font-bold text-green-600 min-w-[60px] text-right"><?php echo e(number_format($stat['percentage'], 1)); ?>%</span>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Needs Attention -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Диққат талаб қилади (топ 6)
            </h3>
            <div class="space-y-2.5">
                <?php $__currentLoopData = collect($tumanStats)->sortBy('percentage')->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-red-50 to-rose-50 rounded-lg hover:from-red-100 hover:to-rose-100 transition-all duration-300 border border-red-200">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-full flex items-center justify-center font-bold text-sm shadow-md">
                            <?php echo e($index + 1); ?>

                        </div>
                        <span class="font-semibold text-slate-800 text-sm"><?php echo e($stat['tuman']); ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-20 bg-red-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-red-500 to-red-600 h-2 rounded-full" style="width: <?php echo e(min($stat['percentage'], 100)); ?>%"></div>
                        </div>
                        <span class="text-base font-bold text-red-600 min-w-[60px] text-right"><?php echo e(number_format($stat['percentage'], 1)); ?>%</span>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics Table -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Туманлар бўйича батафсил статистика</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase">Туман</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase">Лотлар</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">График (млрд)</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">Факт (млрд)</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase">Фарқ (млрд)</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase">Фоиз</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase">Ҳолат</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $__currentLoopData = $tumanStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-sm font-semibold text-slate-800"><?php echo e($stat['tuman']); ?></td>
                        <td class="px-4 py-3 text-center text-sm font-bold" style="color: rgb(185, 28, 28);"><?php echo e(number_format($stat['lots'])); ?></td>
                        <td class="px-4 py-3 text-right text-sm font-semibold" style="color: rgb(29, 78, 216);"><?php echo e(number_format($stat['grafik'] / 1000000000, 2)); ?></td>
                        <td class="px-4 py-3 text-right text-sm font-semibold" style="color: rgb(29, 78, 216);"><?php echo e(number_format($stat['fakt'] / 1000000000, 2)); ?></td>
                        <td class="px-4 py-3 text-right text-sm font-semibold <?php echo e($stat['difference'] > 0 ? 'text-red-600' : 'text-green-600'); ?>">
                            <?php echo e(number_format($stat['difference'] / 1000000000, 2)); ?>

                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-24 bg-slate-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500" 
                                         style="width: <?php echo e(min($stat['percentage'], 100)); ?>%"></div>
                                </div>
                                <span class="text-sm font-bold" style="color: rgb(29, 78, 216);"><?php echo e(number_format($stat['percentage'], 1)); ?>%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if($stat['percentage'] >= 90): ?>
                                <span class="px-3 py-1 text-xs font-bold text-green-700 bg-green-100 rounded-full">Яхши</span>
                            <?php elseif($stat['percentage'] >= 70): ?>
                                <span class="px-3 py-1 text-xs font-bold text-yellow-700 bg-yellow-100 rounded-full">Ўртача</span>
                            <?php else: ?>
                                <span class="px-3 py-1 text-xs font-bold text-red-700 bg-red-100 rounded-full">Паст</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot class="bg-slate-100 font-bold">
                    <tr>
                        <td class="px-4 py-3 text-sm text-slate-800">ЖАМИ</td>
                        <td class="px-4 py-3 text-center text-sm" style="color: rgb(185, 28, 28);"><?php echo e(number_format(collect($tumanStats)->sum('lots'))); ?></td>
                        <td class="px-4 py-3 text-right text-sm" style="color: rgb(29, 78, 216);"><?php echo e(number_format(collect($tumanStats)->sum('grafik') / 1000000000, 2)); ?></td>
                        <td class="px-4 py-3 text-right text-sm" style="color: rgb(29, 78, 216);"><?php echo e(number_format(collect($tumanStats)->sum('fakt') / 1000000000, 2)); ?></td>
                        <td class="px-4 py-3 text-right text-sm <?php echo e(collect($tumanStats)->sum('difference') > 0 ? 'text-red-600' : 'text-green-600'); ?>">
                            <?php echo e(number_format(collect($tumanStats)->sum('difference') / 1000000000, 2)); ?>

                        </td>
                        <td class="px-4 py-3 text-center text-sm" style="color: rgb(29, 78, 216);">
                            <?php echo e(number_format(collect($tumanStats)->sum('fakt') / collect($tumanStats)->sum('grafik') * 100, 1)); ?>%
                        </td>
                        <td class="px-4 py-3 text-center text-sm">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<script>
// Disable datalabels globally, enable only where needed
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
            backgroundColor: [
                'rgb(34, 197, 94)',
                'rgb(59, 130, 246)',
                'rgb(239, 68, 68)',
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
            legend: {
                position: 'bottom',
                labels: {
                    font: { size: 11, weight: 'bold' },
                    color: '#000',
                    padding: 12,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 12 },
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        let value = context.parsed || 0;
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                        return label + ': ' + value + ' дона (' + percentage + '%)';
                    }
                }
            }
        },
        cutout: '65%'
    }
});

// Monthly Comparison Chart
const monthlyCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chartData['monthly']['labels']); ?>,
        datasets: [{
            label: 'График',
            data: <?php echo json_encode($chartData['monthly']['grafik']); ?>,
            borderColor: 'rgb(239, 68, 68)',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 6
        }, {
            label: 'Факт',
            data: <?php echo json_encode($chartData['monthly']['fakt']); ?>,
            borderColor: 'rgb(29, 78, 216)',
            backgroundColor: 'rgba(29, 78, 216, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                labels: {
                    font: { size: 13, weight: 'bold' },
                    color: '#000',
                    padding: 15,
                    usePointStyle: true
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 12 },
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' млрд';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toFixed(1) + ' млрд';
                    },
                    font: { weight: 'bold', size: 11 },
                    color: '#000'
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                ticks: {
                    font: { weight: 'bold', size: 10 },
                    color: '#000',
                    maxRotation: 45,
                    minRotation: 45
                },
                grid: {
                    display: false
                }
            }
        }
    }
});

// Tuman Comparison Chart
const tumanCtx = document.getElementById('tumanComparisonChart').getContext('2d');
new Chart(tumanCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($chartData['tuman']['labels']); ?>,
        datasets: [{
            label: 'График',
            data: <?php echo json_encode($chartData['tuman']['grafik']); ?>,
            backgroundColor: 'rgba(239, 68, 68, 0.8)',
            borderColor: 'rgb(239, 68, 68)',
            borderWidth: 2,
            borderRadius: 6,
            borderSkipped: false
        }, {
            label: 'Факт',
            data: <?php echo json_encode($chartData['tuman']['fakt']); ?>,
            backgroundColor: 'rgba(29, 78, 216, 0.8)',
            borderColor: 'rgb(29, 78, 216)',
            borderWidth: 2,
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                labels: {
                    font: { size: 13, weight: 'bold' },
                    color: '#000',
                    padding: 15,
                    usePointStyle: true
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 12 },
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' млрд';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toFixed(1) + ' млрд';
                    },
                    font: { weight: 'bold', size: 11 },
                    color: '#000'
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                ticks: {
                    font: { weight: 'bold', size: 9 },
                    color: '#000',
                    maxRotation: 45,
                    minRotation: 45
                },
                grid: {
                    display: false
                }
            }
        }
    }
});

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Администратор\Desktop\yer-uchastkalar2\resources\views/yer-sotuvlar/monitoring.blade.php ENDPATH**/ ?>