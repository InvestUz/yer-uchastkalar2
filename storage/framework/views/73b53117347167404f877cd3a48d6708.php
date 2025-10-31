<?php $__env->startSection('title', 'Лот ' . $yer->lot_raqami . ' - Батафсил'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-4">

        
        <div>
            <a href="<?php echo e(url()->previous()); ?>"
                class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Орқага қайтиш
            </a>
        </div>

        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                    <div>
                        <h1 class="text-xl font-bold text-white">Лот № <?php echo e($yer->lot_raqami); ?></h1>
                        <p class="text-gray-300 text-sm mt-1"><?php echo e($yer->tuman); ?> • <?php echo e($yer->mfy); ?> •
                            <?php echo e($yer->unikal_raqam); ?></p>
                    </div>

                </div>
            </div>

            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4 bg-gray-50 border-b border-gray-200">
                <div class="text-center p-3 bg-white rounded border border-gray-200">
                    <div class="text-xs text-gray-600">ер майдони</div>

                    <div class="text-lg font-bold text-gray-900"><?php echo e(number_format($yer->maydoni, 2)); ?> га</div>
                </div>
                <div class="text-center p-3 bg-white rounded border border-gray-200">
                    <div class="text-xs text-gray-600">Бошланғич нархи</div>
                    <div class="text-lg font-bold text-gray-900"><?php echo e(number_format($yer->boshlangich_narx, 2)); ?>

                        сўм</div>
                </div>
                <div class="text-center p-3 bg-white rounded border border-gray-200">
                    <div class="text-xs text-gray-600">Сотилган нархи</div>
                    <div class="text-lg font-bold text-gray-900"><?php echo e(number_format($yer->sotilgan_narx, 2)); ?>

                        сўм</div>
                </div>

                <div class="text-center p-3 bg-gray-700 text-white rounded border border-gray-600">
                    <div class="text-xs">Аукцион хизмат ҳақи 1 фоиз</div>
                    <?php echo e(number_format($yer->auksion_harajati, 2)); ?> сўм
                </div>
                <div class="text-center p-3 bg-gray-700 text-white rounded border border-gray-600">
                    <div class="text-xs">Сотилган ер тўлови бўйича тушадиган қиймат</div>
                    <?php echo e(number_format($yer->shartnoma_summasi + $yer->golib_tolagan - $yer->auksion_harajati, 2)); ?> сўм
                </div>
                <div class="text-center p-3 bg-gray-700 text-white rounded border border-gray-600">
                    <div class="text-xs">Шартнома графиги б-ча тўлов</div>
                    <div class="text-lg font-bold"><?php echo e(number_format($yer->shartnoma_summasi, 2)); ?> сўм
                    </div>
                </div>

                <div class="text-center p-3 bg-gray-700 text-white rounded border border-gray-600">
                    <div class="text-xs">Амалда тўланган қиймат</div>
                    <div class="text-lg font-bold">
                        <?php echo e(number_format($yer->faktTolovlar->sum('tolov_summa'), 2)); ?>

                        сўм</div>
                </div>

                <div class="text-center p-3 bg-gray-700 text-white rounded border border-gray-600">

                    <div class="text-xs">Тўланиши лозим бўлган қолдик қиймат</div>
                    <?php echo e(number_format($yer->shartnoma_summasi + $yer->golib_tolagan - ($yer->faktTolovlar->sum('tolov_summa') + $yer->auksion_harajati), 2)); ?>

                    сўм
                </div>
            </div>
        </div>
    </div>


    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px overflow-x-auto">
                <button onclick="openTab(event, 'basic')"
                    class="tab-button active px-6 py-3 text-sm font-medium border-b-2 border-gray-700 text-gray-900">
                    Асосий маълумотлар
                </button>
                <button onclick="openTab(event, 'financial')"
                    class="tab-button px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-900">
                    Молиявий кўрсаткичлар
                </button>
                <button onclick="openTab(event, 'budget')"
                    class="tab-button px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-900">
                    Тақсимот
                </button>
                <button onclick="openTab(event, 'payment')"
                    class="tab-button px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-900">
                    Тўловлар
                </button>
            </nav>
        </div>

        
        <div id="basic" class="tab-content p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide pb-2 border-b border-gray-200">Ер
                        участкаси маълумотлари</h3>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="py-2 text-gray-600 w-40">Уникал рақам</td>
                                <td class="py-2 font-medium text-gray-900"><?php echo e($yer->unikal_raqam ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Зона</td>
                                <td class="py-2 font-medium text-gray-900"><?php echo e($yer->zona ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Бош режа зона</td>
                                <td class="py-2 font-medium text-gray-900"><?php echo e($yer->bosh_reja_zona ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Янги Ўзбекистон</td>
                                <td class="py-2 font-medium text-gray-900"><?php echo e($yer->yangi_ozbekiston ?? '-'); ?></td>
                            </tr>
                            <?php if($yer->manzil): ?>
                                <tr>
                                    <td class="py-2 text-gray-600">Манзил</td>
                                    <td class="py-2 text-gray-900"><?php echo e($yer->manzil); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if($yer->lokatsiya): ?>
                                <tr>
                                    <td class="py-2 text-gray-600">Локация</td>
                                    <td class="py-2">
                                        <a href="https://www.google.com/maps?q=<?php echo e($yer->lokatsiya); ?>" target="_blank"
                                            class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            Харитада кўриш
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <?php if($yer->qurilish_turi_1 || $yer->qurilish_maydoni): ?>
                        <h3
                            class="text-sm font-semibold text-gray-900 uppercase tracking-wide pt-4 pb-2 border-b border-gray-200">
                            Қурилишга рухсат берилган объект тури</h3>
                        <table class="min-w-full text-sm">
                            <tbody class="divide-y divide-gray-100">
                                <?php if($yer->qurilish_turi_1): ?>
                                    <tr>
                                        <td class="py-2 text-gray-600 w-40">Соҳаси </td>
                                        <td class="py-2 text-gray-900"><?php echo e($yer->qurilish_turi_1); ?></td>
                                    </tr>
                                <?php endif; ?>

                                
                                <?php if($yer->investitsiya): ?>
                                    <tr>
                                        <td class="py-2 text-gray-600">Киритиладиган инвестиция</td>
                                        <td class="py-2 font-medium text-gray-900">
                                            <?php echo e(number_format($yer->investitsiya, 1)); ?> АҚШ доллари</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide pb-2 border-b border-gray-200">
                        Аукцион маълумотлари</h3>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="py-2 text-gray-600 w-40">Аукцион санаси</td>
                                <td class="py-2 text-gray-900"><?php echo e($yer->auksion_sana ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600 w-40">Аукцион тури</td>
                                <td class="py-2 text-gray-900"><?php echo e($yer->auksion_turi ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Асос</td>
                                <td class="py-2 text-gray-900"><?php echo e($yer->asos ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Ҳолат</td>
                                <td class="py-2 text-gray-900"><?php echo e(Str::limit($yer->holat, 40) ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Ғолиб</td>
                                <td class="py-2 font-medium text-gray-900"><?php echo e($yer->golib_nomi ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Субъект тури</td>
                                <td class="py-2 text-gray-900">
                                    <?php if($yer->golib_turi == 'юр лицо'): ?>
                                        юридик шахс
                                    <?php else: ?>
                                        жисмоний шахс
                                    <?php endif; ?>

                                </td>
                            </tr>
                            <?php if($yer->telefon): ?>
                                <tr>
                                    <td class="py-2 text-gray-600">Телефон</td>
                                    <td class="py-2 text-gray-900">
                                        <a href="tel:<?php echo e($yer->telefon); ?>" class="text-blue-600 hover:text-blue-800">
                                            <?php echo e($yer->telefon); ?>

                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="py-2 text-gray-600">Тўлов тури</td>
                                <td class="py-2">
                                    <?php if($yer->tolov_turi == 'муддатли эмас'): ?>
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-700 text-white rounded">Бир
                                            йўла</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-400 text-white rounded">Бўлиб
                                            тўлаш</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <?php if($yer->shartnoma_sana || $yer->shartnoma_raqam): ?>
                        <h3
                            class="text-sm font-semibold text-gray-900 uppercase tracking-wide pt-4 pb-2 border-b border-gray-200">
                            Шартнома маълумотлари</h3>
                        <table class="min-w-full text-sm">
                            <tbody class="divide-y divide-gray-100">
                                <?php if($yer->shartnoma_holati): ?>
                                    <tr>
                                        <td class="py-2 text-gray-600 w-40">Ҳолати</td>
                                        <td class="py-2 text-gray-900"><?php echo e($yer->shartnoma_holati); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if($yer->shartnoma_raqam): ?>
                                    <tr>
                                        <td class="py-2 text-gray-600">Рақами</td>
                                        <td class="py-2 font-medium text-gray-900"><?php echo e($yer->shartnoma_raqam); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if($yer->shartnoma_sana): ?>
                                    <tr>
                                        <td class="py-2 text-gray-600">Санаси</td>
                                        <td class="py-2 font-medium text-gray-900">
                                            <?php echo e($yer->shartnoma_sana->format('d.m.Y')); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div id="financial" class="tab-content hidden p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3
                        class="text-sm font-semibold text-gray-900 uppercase tracking-wide pb-2 border-b border-gray-200 mb-3">
                        Асосий суммалар</h3>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="py-2 text-gray-600">Ғолиб тўлаган</td>
                                <td class="py-2 text-right font-medium text-gray-900">
                                    <?php echo e(number_format($yer->golib_tolagan, 1)); ?> cўм</td>
                            </tr>
                            
                            <tr>
                                <td class="py-2 text-gray-600">Чегирма</td>
                                <td class="py-2 text-right font-medium text-gray-900">
                                    <?php echo e(number_format($yer->chegirma, 1)); ?> cўм</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Аукцион харажати</td>
                                <td class="py-2 text-right font-medium text-gray-900">
                                    <?php echo e(number_format($yer->auksion_harajati, 1)); ?> cўм</td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>

                <div style="display: none">
                    <h3
                        class="text-sm font-semibold text-gray-900 uppercase tracking-wide pb-2 border-b border-gray-200 mb-3">
                        Бошқа маълумотлар</h3>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="py-2 text-gray-600">Тушадиган маблағ</td>
                                <td class="py-2 text-right font-medium text-gray-900">
                                    <?php echo e(number_format($yer->tushadigan_mablagh / 1000000, 1)); ?> млн</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Давактив жамғармаси</td>
                                <td class="py-2 text-right font-medium text-gray-900">
                                    <?php echo e(number_format($yer->davaktiv_jamgarmasi / 1000000, 1)); ?> млн</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Шартнома тушган</td>
                                <td class="py-2 text-right font-medium text-gray-900">
                                    <?php echo e(number_format($yer->shartnoma_tushgan / 1000000, 1)); ?> млн</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Давактивда турган</td>
                                <td class="py-2 text-right font-medium text-gray-900">
                                    <?php echo e(number_format($yer->davaktivda_turgan / 1000000, 1)); ?> млн</td>
                            </tr>
                            <?php if($yer->farqi): ?>
                                <tr class="bg-gray-50">
                                    <td class="py-2 text-gray-900 font-semibold">Фарқи</td>
                                    <td class="py-2 text-right font-bold text-gray-900">
                                        <?php echo e(number_format($yer->farqi / 1000000, 1)); ?> млн</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div id="budget" class="tab-content hidden p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Категория</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Тушадиган</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Тақсимланган
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Қолдиқ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">Тошкент шаҳар бюджети</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                <?php echo e(number_format($yer->mahalliy_byudjet_tushadigan, 1)); ?> сўм</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                <?php echo e(number_format($yer->mahalliy_byudjet_taqsimlangan, 1)); ?> сўм</td>
                            <td
                                class="px-4 py-3 text-right font-semibold <?php echo e($yer->qoldiq_mahalliy_byudjet > 0 ? 'text-red-700' : 'text-green-700'); ?>">
                                <?php echo e(number_format($yer->qoldiq_mahalliy_byudjet, 1)); ?> сўм
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">Жамғарма</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                <?php echo e(number_format($yer->jamgarma_tushadigan, 1)); ?> сўм</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                <?php echo e(number_format($yer->jamgarma_taqsimlangan, 1)); ?> сўм</td>
                            <td
                                class="px-4 py-3 text-right font-semibold <?php echo e($yer->qoldiq_jamgarma > 0 ? 'text-red-700' : 'text-green-700'); ?>">
                                <?php echo e(number_format($yer->qoldiq_jamgarma, 1)); ?> сўм
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">Янги Ўзбекистон</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                <?php echo e(number_format($yer->yangi_oz_direksiya_tushadigan, 1)); ?> сўм</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                <?php echo e(number_format($yer->yangi_oz_direksiya_taqsimlangan, 1)); ?> сўм</td>
                            <td
                                class="px-4 py-3 text-right font-semibold <?php echo e($yer->qoldiq_yangi_oz_direksiya > 0 ? 'text-red-700' : 'text-green-700'); ?>">
                                <?php echo e(number_format($yer->qoldiq_yangi_oz_direksiya, 1)); ?> сўм
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">Шайхонтоҳур тумани</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                сўм</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                сўм</td>
                            <td
                                class="px-4 py-3 text-right font-semibold <?php echo e($yer->qoldiq_shayxontohur > 0 ? 'text-red-700' : 'text-green-700'); ?>">
                                сўм
                            </td>
                        </tr>


                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">Янгиҳаёт индустриал технопаки</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                <?php echo e(number_format($yer->yangi_hayot_industrial_park_tushadigan, 1)); ?> сўм</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                <?php echo e(number_format($yer->yangi_hayot_industrial_park_taqsimlangan, 1)); ?> сўм</td>
                            <td
                                class="px-4 py-3 text-right font-semibold <?php echo e($yer->qoldiq_mahalliy_byudjet > 0 ? 'text-red-700' : 'text-green-700'); ?>">
                                <?php echo e(number_format($yer->qoldiq_mahalliy_byudjet, 1)); ?> сўм
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">КСЗ дирекциялари</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                <?php echo e(number_format($yer->ksz_direksiyalari_tushadigan, 1)); ?> сўм</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                <?php echo e(number_format($yer->ksz_direksiyalari_taqsimlangan, 1)); ?> сўм</td>
                            <td
                                class="px-4 py-3 text-right font-semibold <?php echo e($yer->qoldiq_jamgarma > 0 ? 'text-red-700' : 'text-green-700'); ?>">
                                <?php echo e(number_format($yer->qoldiq_jamgarma, 1)); ?> сўм
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">Тошкент сити дирекцияси</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                <?php echo e(number_format($yer->toshkent_city_direksiya_tushadigan, 1)); ?> сўм</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                <?php echo e(number_format($yer->toshkent_city_direksiya_taqsimlangan, 1)); ?> сўм</td>
                            <td
                                class="px-4 py-3 text-right font-semibold <?php echo e($yer->qoldiq_yangi_oz_direksiya > 0 ? 'text-red-700' : 'text-green-700'); ?>">
                                <?php echo e(number_format($yer->qoldiq_yangi_oz_direksiya, 1)); ?> сўм
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">Туманлар бюжети</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                <?php echo e(number_format($yer->tuman_byudjeti_tushadigan, 1)); ?> сўм</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                <?php echo e(number_format($yer->tuman_byudjeti_taqsimlangan, 1)); ?> сўм</td>
                            <td
                                class="px-4 py-3 text-right font-semibold <?php echo e($yer->qoldiq_shayxontohur > 0 ? 'text-red-700' : 'text-green-700'); ?>">
                                <?php echo e(number_format($yer->qoldiq_shayxontohur, 1)); ?> сўм
                            </td>
                        </tr>

                        <tr class="bg-gray-100 font-semibold">
                            <td class="px-4 py-3 text-gray-900">ЖАМИ</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                
                                сўм
                            </td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                
                                сўм
                            </td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                
                                сўм
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div id="payment" class="tab-content hidden p-6">
            <?php
                $grafikJami = $yer->grafikTolovlar->sum('grafik_summa');
                $faktJami = $yer->faktTolovlar->sum('tolov_summa');
                $qarzdorlik = $grafikJami - $faktJami;
                $foiz = $grafikJami > 0 ? round(($faktJami / $grafikJami) * 100, 1) : 0;
                $hasPaymentData = $yer->grafikTolovlar->count() > 0 || $yer->faktTolovlar->count() > 0;
            ?>

            <?php if(!$hasPaymentData): ?>
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="mt-3 text-sm text-gray-600">Тўлов маълумотлари мавжуд эмас</p>
                    <p class="text-xs text-gray-500 mt-1"><?php echo e($yer->holat ?? 'Маълум эмас'); ?></p>
                </div>
            <?php else: ?>
                
                <div class="space-y-2">
                    <?php if(isset($tolovTaqqoslash) && count($tolovTaqqoslash) > 0): ?>
                        <details class="group border border-gray-200 rounded">
                            <summary
                                class="cursor-pointer px-4 py-3 bg-gray-50 hover:bg-gray-100 flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-900">Ойлик таққослаш</span>
                                <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </summary>
                            <div class="p-4 border-t border-gray-200">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-xs">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left font-medium text-gray-700">Ой</th>
                                                <th class="px-3 py-2 text-right font-medium text-gray-700">График</th>
                                                <th class="px-3 py-2 text-right font-medium text-gray-700">Факт</th>
                                                <th class="px-3 py-2 text-center font-medium text-gray-700">%</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <?php
                                                $jamiGrafik = 0;
                                                $jamiFakt = 0;
                                            ?>

                                            <?php $__currentLoopData = $tolovTaqqoslash; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tolov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $jamiGrafik += $tolov['grafik'];
                                                    $jamiFakt += $tolov['fakt'];
                                                ?>
                                                <tr
                                                    class="hover:bg-gray-50 <?php echo e($tolov['is_advance'] ? 'bg-yellow-50' : ''); ?>">
                                                    <td class="px-3 py-2 text-gray-900">
                                                        <?php echo e($tolov['oy_nomi']); ?>


                                                    </td>
                                                    <td class="px-3 py-2 text-right text-gray-900">
                                                        <?php echo e(number_format($tolov['grafik'] / 1000000, 1)); ?>

                                                    </td>
                                                    <td
                                                        class="px-3 py-2 text-right <?php echo e($tolov['fakt'] > 0 ? 'text-gray-900 font-semibold' : 'text-gray-400'); ?>">
                                                        <?php echo e(number_format($tolov['fakt'] / 1000000, 1)); ?>

                                                    </td>
                                                    <td class="px-3 py-2 text-center">
                                                        <?php if($tolov['is_advance']): ?>
                                                            <span
                                                                class="px-2 py-1 rounded text-xs bg-yellow-500 text-white">
                                                                -
                                                            </span>
                                                        <?php else: ?>
                                                            <span
                                                                class="px-2 py-1 rounded text-xs <?php echo e($tolov['foiz'] >= 100 ? 'bg-gray-700 text-white' : 'bg-gray-200 text-gray-800'); ?>">
                                                                <?php echo e($tolov['foiz']); ?>%
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>

                                        
                                        <tfoot class="border-t-2 border-gray-300">
                                            <tr class="bg-gradient-to-r from-blue-50 to-indigo-50">
                                                <td class="px-3 py-3 text-sm font-bold text-gray-900">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1 text-blue-600" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Жами
                                                    </div>
                                                </td>
                                                <td class="px-3 py-3 text-right text-sm font-bold text-gray-900">
                                                    <?php echo e(number_format($jamiGrafik / 1000000, 1)); ?> млн
                                                </td>
                                                <td class="px-3 py-3 text-right text-sm font-bold text-gray-900">
                                                    <?php echo e(number_format($jamiFakt / 1000000, 1)); ?> млн
                                                </td>
                                                <td class="px-3 py-3 text-center">
                                                    <?php
                                                        $jamiFoiz =
                                                            $jamiGrafik > 0
                                                                ? round(($jamiFakt / $jamiGrafik) * 100)
                                                                : 0;
                                                    ?>
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold <?php echo e($jamiFoiz >= 100 ? 'bg-green-600 text-white' : ($jamiFoiz >= 50 ? 'bg-yellow-500 text-white' : 'bg-red-500 text-white')); ?>">
                                                        <?php echo e($jamiFoiz); ?>%
                                                    </span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </details>
                    <?php endif; ?>

                    <?php if($yer->faktTolovlar->count() > 0): ?>
                        <details class="group border border-gray-200 rounded">
                            <summary
                                class="cursor-pointer px-4 py-3 bg-gray-50 hover:bg-gray-100 flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-900">Тўлов тарихи
                                    (<?php echo e($yer->faktTolovlar->count()); ?>)</span>
                                <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </summary>
                            <div class="p-4 border-t border-gray-200">
                                <div class="space-y-2">
                                    <?php $__currentLoopData = $yer->faktTolovlar->sortByDesc('tolov_sana')->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tolov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo e($tolov->tolov_sana->format('d.m.Y')); ?></div>
                                                <div class="text-xs text-gray-500">
                                                    <?php echo e(Str::limit($tolov->tolash_nom, 30)); ?></div>
                                            </div>
                                            <div class="text-sm font-semibold text-gray-900">
                                                <?php echo e(number_format($tolov->tolov_summa / 1000000, 1)); ?> млн
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </details>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>

    <script>
        function openTab(evt, tabName) {
            // Hide all tab contents
            var tabContents = document.getElementsByClassName("tab-content");
            for (var i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.add("hidden");
            }

            // Remove active class from all buttons
            var tabButtons = document.getElementsByClassName("tab-button");
            for (var i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove("active", "border-gray-700", "text-gray-900");
                tabButtons[i].classList.add("border-transparent", "text-gray-600");
            }

            // Show current tab and mark button as active
            document.getElementById(tabName).classList.remove("hidden");
            evt.currentTarget.classList.add("active", "border-gray-700", "text-gray-900");
            evt.currentTarget.classList.remove("border-transparent", "text-gray-600");
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\inves\OneDrive\Ishchi stol\yer-uchastkalar\resources\views/yer-sotuvlar/show.blade.php ENDPATH**/ ?>