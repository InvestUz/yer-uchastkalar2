@extends('layouts.app')

@section('title', 'Бўлиб тўлаш маълумоти')

@section('content')
    @php
        $fmt = function ($amount) {
            return number_format(((float) $amount) / 1_000_000_000, 1, '.', ',');
        };

        $headerLabel = function ($label) {
            $map = [
                'Т/р' => 'Т/р',
                'Ҳудудлар' => 'Ҳудудлар',
                'Жами' => 'Жами',
                'Чегирма' => 'Чегирма',
                'Харидорларга қайтарилган маблағлар' => 'Харидорларга<br>қайтарилган маблағлар',
                'Тошкент ш. қурилиш бошкармасига (1%)' => 'Тошкент ш.<br>қурилиш бошкармасига<br>(1%)',
                'Давлат кадастрлар палатасига' => 'Давлат кадастрлар<br>палатасига',
                'Геоахборот шахарсозлик кадастрига' => 'Геоахборот<br>шахарсозлик кадастрига',
                'Солиқ қўмитаси хузуридаги Кадастр агентлигига' => 'Солиқ қўмитаси хузуридаги<br>Кадастр агентлигига',
                'Тошкент шахар махаллий бюджетига' => 'Тошкент шахар<br>махаллий бюджетига',
                'Жамғармага' => 'Жамғармага',
                'Туманга' => 'Туманга',
                'ЯнгиХаёт индустриал технопарки дирекциясига' => 'ЯнгиХаёт индустриал<br>технопарки дирекциясига',
                'Шайҳонтохур туманига' => 'Шайҳонтохур<br>туманига',
                'Тошкент сити дирекциясига' => 'Тошкент сити<br>дирекциясига',
                'Қолдиқ' => 'Қолдиқ',
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
            $minYear = (int) min($availableYears);
            $maxYear = (int) max($availableYears);
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
            $monthNo = (int) $filters['month'];
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
        $valueUnitLabel = 'млрд сўм';

        $rowOnlyCategories = [
            'ЯнгиХаёт индустриал технопарки дирекциясига',
            'Шайҳонтохур туманига',
            'Тошкент сити дирекциясига',
        ];
        $columnCategories = array_filter(
            $paymentCategories,
            static function ($value, $category) use ($rowOnlyCategories) {
                return !in_array($category, $rowOnlyCategories, true);
            },
            ARRAY_FILTER_USE_BOTH
        );

        $extraDistrictRows = [
            [
                'label' => 'ЯнгиХаёт индустриал технопарки дирекциясига',
                'category' => 'ЯнгиХаёт индустриал технопарки дирекциясига',
            ],
            [
                'label' => 'Шайҳонтохур туманига',
                'category' => 'Шайҳонтохур туманига',
            ],
            [
                'label' => 'Тошкент сити дирекциясига',
                'category' => 'Тошкент сити дирекциясига',
            ],
        ];

        $orderedDistrictData = $districtData ?? [];
        $unknownDistrictKey = 'Номалум';
        if (is_array($orderedDistrictData) && array_key_exists($unknownDistrictKey, $orderedDistrictData)) {
            $unknownDistrictValues = $orderedDistrictData[$unknownDistrictKey];
            unset($orderedDistrictData[$unknownDistrictKey]);

            if (is_array($unknownDistrictValues)) {
                $unknownDistrictValues['Жами'] = 0;
            }

            $orderedDistrictData[$unknownDistrictKey] = $unknownDistrictValues;
        }
    @endphp

    <div class="min-h-screen bg-slate-100 py-6 px-4">
        <div class="mx-auto max-w-[98%]">
            <div class="report-card overflow-hidden bg-white">
                <div class="report-header px-8 py-6">
                    <div class="text-center">
                        <p class="report-kicker">Расмий ҳисобот шакли</p>
                        <h1 class="mx-auto mt-2 max-w-4xl text-xl font-bold leading-snug tracking-wide text-slate-900">
                            Тошкент шаҳрида аукцион савдоларида бўлиб тўлаш шарти билан сотилган ер участкалари тўғрисида
                        </h1>
                        <h2 class="mt-1 text-sm font-semibold tracking-[0.18em] text-slate-600">ЙИҒМА МАЪЛУМОТ</h2>

                        @if($districtRestrict)
                            <p class="report-notice mt-4 inline-block px-4 py-2 text-left text-xs font-semibold text-amber-800">
                                Сиз фақат <strong>{{ $districtRestrict }}</strong> бўйича маълумотларни кўряпсиз
                            </p>
                        @endif
                    </div>
                </div>

                <div class="p-0">
                    <div class="table-meta-strip">
                        <div>
                            <p class="table-meta-title">Ҳисобот жадвали</p>
                            <p class="table-meta-subtitle">Қизил рақамлар: тақсимланмаган қолдиқ. Кўк рақамлар: детал саҳифасига ўтиш.</p>
                            <p class="table-meta-status">Амалдаги фильтр: {{ $activeFilterText }}</p>
                        </div>
                        <div class="table-meta-actions">
                            <div class="table-meta-unit">Маълумот бирлиги: {{ $valueUnitLabel }}</div>
                            <button
                                type="button"
                                id="fin-filter-trigger"
                                class="filter-trigger"
                                onclick="openFinFilterModal()"
                                aria-haspopup="dialog"
                                aria-controls="fin-filter-modal"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5h18l-7 8v5l-4 2v-7L3 5z" />
                                </svg>
                                <span>Фильтр</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto bg-white">
                        <table class="border-collapse statistics-table">
                            <thead>
                                <tr class="table-head-row">
                                    <th rowspan="2" class="sticky-col border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800" style="width: 40px; min-width: 40px; max-width: 40px; font-size:11px;">
                                        {!! $headerLabel('Т/р') !!}
                                    </th>
                                    <th rowspan="2" class="sticky-col-2 border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800" style="width: 150px; min-width: 150px; max-width: 150px; font-size:11px;">
                                        {!! $headerLabel('Ҳудудлар') !!}
                                    </th>
                                    <th rowspan="2" class="total-amount-col border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800" style="font-size:11px;">
                                        {!! $headerLabel('Жами') !!}
                                    </th>
                                    <th colspan="{{ count($columnCategories) }}" class="border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800" style="font-size:11px;">
                                        Жумладан
                                    </th>
                                    <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800" style="min-width: 120px; font-size:11px;">
                                        {!! $headerLabel('Қолдиқ') !!}
                                    </th>
                                </tr>
                                <tr class="table-subhead-row">
                                    @foreach($columnCategories as $category => $value)
                                        <th
                                            class="border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800"
                                            style="min-width: 100px; font-size:11px;"
                                            title="{{ $category }}"
                                        >{!! $headerLabel($category) !!}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody class="bg-white">
                                @if(empty($orderedDistrictData) || count($orderedDistrictData) === 0)
                                    <tr>
                                        <td colspan="{{ 4 + count($columnCategories) }}" class="border border-slate-300 px-4 py-6 text-center text-slate-700">
                                            Маълумотлар топилмади.
                                        </td>
                                    </tr>
                                @else
                                    <tr class="summary-row">
                                        <td colspan="2" class="sticky-col-total border border-slate-300 px-3 py-2 text-center align-middle text-xs font-bold uppercase text-slate-900 bg-slate-100">
                                            ЖАМИ:
                                        </td>
                                        <td class="total-amount-col border border-slate-300 px-2 py-1 text-right font-bold text-slate-900">
                                            <a href="{{ route('yer-sotuvlar.fin-xisobot.details', $activeFilterParams) }}" class="metric-link text-right">
                                                <span class="metric-value">{{ $fmt($totalAmount) }}</span>
                                            </a>
                                        </td>

                                        @foreach($columnCategories as $category => $value)
                                            <td class="border border-slate-300 px-2 py-1 text-right font-bold text-slate-900">
                                                @php
                                                    $catTotal = $categoryTotals[$category] ?? 0;
                                                    $isSyntheticTotalCell = !empty($districtRestrict) && !empty($proportionalCategoryLookup[$category]);
                                                @endphp
                                                @if($catTotal > 0)
                                                    @if($isSyntheticTotalCell)
                                                        <span class="metric-static text-right text-slate-700" title="Пропорция бўйича ҳисобланган">
                                                            <span class="metric-value text-slate-800">{{ $fmt($catTotal) }}</span>
                                                        </span>
                                                    @else
                                                        <a href="{{ route('yer-sotuvlar.fin-xisobot.details', array_merge($activeFilterParams, ['category' => $category])) }}" class="metric-link text-right">
                                                            <span class="metric-value">{{ $fmt($catTotal) }}</span>
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="dash-value">—</span>
                                                @endif
                                            </td>
                                        @endforeach

                                        @php
                                            $jamiCategorySum = 0.0;
                                            foreach ($columnCategories as $categoryName => $categoryValue) {
                                                $jamiCategorySum += (float) ($categoryTotals[$categoryName] ?? 0);
                                            }
                                            $jamiQoldiq = (float) $totalAmount - $jamiCategorySum;
                                            if (abs($jamiQoldiq) < 0.01) {
                                                $jamiQoldiq = 0.0;
                                            }
                                        @endphp
                                        <td class="border border-slate-300 px-2 py-1 text-right font-bold text-slate-900">
                                            @if($jamiQoldiq > 0)
                                                <span class="residual-value">{{ $fmt($jamiQoldiq) }}</span>
                                            @else
                                                <span class="dash-value">—</span>
                                            @endif
                                        </td>
                                    </tr>

                                    @foreach($orderedDistrictData as $district => $values)
                                        <tr class="bg-white transition-colors duration-150 hover:bg-slate-50">
                                            <td class="sticky-col border border-slate-300 px-2 py-1 text-center align-middle font-medium text-slate-700">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td class="sticky-col-2 border border-slate-300 px-2 py-1 align-middle font-semibold text-slate-800">
                                                {{ $district }}
                                            </td>
                                            <td class="total-amount-col border border-slate-300 px-2 py-1 text-right text-slate-700">
                                                @php $districtTotal = $values['Жами'] ?? 0; @endphp
                                                @if($districtTotal > 0)
                                                    <a href="{{ route('yer-sotuvlar.fin-xisobot.details', array_merge($activeFilterParams, ['district' => $district])) }}" class="metric-link text-right">
                                                        <span @class([
                                                            'metric-value',
                                                            'text-rose-700' => in_array($district, ['Номалум', 'ЯнгиХаёт индустриал технопарки дирекциясига', 'Шайҳонтохур туманига', 'Тошкент сити дирекциясига'], true),
                                                        ])>{{ $fmt($districtTotal) }}</span>
                                                    </a>
                                                @else
                                                    <span class="dash-value">—</span>
                                                @endif
                                            </td>

                                            @foreach($columnCategories as $category => $value)
                                                <td class="border border-slate-300 px-2 py-1 text-right text-slate-700">
                                                    @php
                                                        $cellAmount = $values[$category] ?? 0;
                                                        $isSyntheticDistrictCell = !empty($proportionalCategoryLookup[$category]);
                                                    @endphp
                                                    @if($cellAmount > 0)
                                                        @if($isSyntheticDistrictCell)
                                                            <span class="metric-static text-right text-slate-700" title="Пропорция бўйича ҳисобланган">
                                                                <span class="metric-value text-slate-800">{{ $fmt($cellAmount) }}</span>
                                                            </span>
                                                        @else
                                                            <a href="{{ route('yer-sotuvlar.fin-xisobot.details', array_merge($activeFilterParams, ['district' => $district, 'category' => $category])) }}" class="metric-link text-right">
                                                                <span class="metric-value">{{ $fmt($cellAmount) }}</span>
                                                            </a>
                                                        @endif
                                                    @else
                                                        <span class="dash-value">—</span>
                                                    @endif
                                                </td>
                                            @endforeach

                                            @php
                                                $districtCategorySum = 0.0;
                                                foreach ($columnCategories as $categoryName => $categoryValue) {
                                                    $districtCategorySum += (float) ($values[$categoryName] ?? 0);
                                                }
                                                $districtQoldiq = (float) $districtTotal - $districtCategorySum;
                                                if (abs($districtQoldiq) < 0.01) {
                                                    $districtQoldiq = 0.0;
                                                }
                                            @endphp
                                            <td class="border border-slate-300 px-2 py-1 text-right text-slate-700">
                                                @if($districtQoldiq > 0)
                                                    <span class="residual-value">{{ $fmt($districtQoldiq) }}</span>
                                                @else
                                                    <span class="dash-value">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    @php
                                        $districtRowNumber = count($orderedDistrictData);
                                    @endphp
                                    @foreach($extraDistrictRows as $extraRow)
                                        @php
                                            $districtRowNumber++;
                                            $extraCategory = $extraRow['category'];
                                            $extraTotal = (float) ($categoryTotals[$extraCategory] ?? 0);
                                        @endphp
                                        <tr class="supplement-row bg-white transition-colors duration-150 hover:bg-slate-50">
                                            <td class="sticky-col border border-slate-300 px-2 py-1 text-center align-middle font-medium text-slate-700">
                                                {{ $districtRowNumber }}
                                            </td>
                                            <td class="sticky-col-2 border border-slate-300 px-2 py-1 align-middle font-semibold text-slate-800">
                                                {{ $extraRow['label'] }}
                                            </td>
                                            <td class="total-amount-col border border-slate-300 px-2 py-1 text-right text-slate-700">
                                                @if($extraTotal > 0)
                                                    <a href="{{ route('yer-sotuvlar.fin-xisobot.details', array_merge($activeFilterParams, ['category' => $extraCategory])) }}" class="metric-link text-right">
                                                        <span @class([
                                                            'metric-value',
                                                            'text-rose-700' => in_array($extraCategory, ['ЯнгиХаёт индустриал технопарки дирекциясига', 'Шайҳонтохур туманига', 'Тошкент сити дирекциясига'], true),
                                                        ])>{{ $fmt($extraTotal) }}</span>
                                                    </a>
                                                @else
                                                    <span class="dash-value">—</span>
                                                @endif
                                            </td>

                                            @foreach($columnCategories as $category => $value)
                                                <td class="border border-slate-300 px-2 py-1 text-right text-slate-700">
                                                    <span class="dash-value">—</span>
                                                </td>
                                            @endforeach

                                            <td class="border border-slate-300 px-2 py-1 text-right text-slate-700">
                                                <span class="dash-value">—</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="fin-filter-modal" class="filter-modal" hidden onclick="handleFinFilterBackdrop(event)">
        <div
            class="filter-modal-panel"
            role="dialog"
            aria-modal="true"
            aria-labelledby="fin-filter-modal-title"
            onclick="event.stopPropagation()"
        >
            <div class="filter-modal-header">
                <div>
                    <p class="filter-modal-kicker">Ҳисобот фильтрлари</p>
                    <h3 id="fin-filter-modal-title" class="filter-modal-title">Фильтрни танлаш</h3>
                    <p class="filter-modal-subtitle">Йил, ой ва сана бўйича ҳисобот кўринишини янгиланг.</p>
                </div>
                <button type="button" class="filter-modal-close" onclick="closeFinFilterModal()" aria-label="Ёпиш">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>
            </div>

            <div class="filter-modal-body">
                <form method="GET" action="{{ route('yer-sotuvlar.fin-xisobot') }}" id="fin-filter-form" class="space-y-4">
                    <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Йил</label>
                            <select
                                name="year"
                                class="w-full rounded-md border border-slate-400 px-3 py-2 text-sm transition-all focus:border-slate-600 focus:ring-2 focus:ring-slate-200"
                            >
                                <option value="">Барча йиллар</option>
                                @foreach($yearSelectOptions as $yearOption)
                                    <option value="{{ $yearOption }}" @selected((int)($filters['year'] ?? 0) === (int)$yearOption)>
                                        {{ $yearOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Ой</label>
                            @if(!empty($filters['year']))
                                <select
                                    name="month"
                                    class="w-full rounded-md border border-slate-400 px-3 py-2 text-sm transition-all focus:border-slate-600 focus:ring-2 focus:ring-slate-200"
                                >
                                    <option value="">Барча ойлар</option>
                                    @foreach($monthOptions as $monthNumber => $monthLabel)
                                        <option value="{{ $monthNumber }}" @selected((int)($filters['month'] ?? 0) === (int)$monthNumber)>
                                            {{ $monthLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="w-full rounded-md border border-dashed border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-500">
                                    Ой фильтри йил танлангандан кейин чиқади
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-500">Бошланғич сана</label>
                            <input
                                type="date"
                                name="date_from"
                                value="{{ $filters['date_from'] ?? '' }}"
                                class="w-full rounded-md border border-slate-400 px-3 py-2 text-sm transition-all focus:border-slate-600 focus:ring-2 focus:ring-slate-200"
                            >
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-500">Тугаш сана</label>
                            <input
                                type="date"
                                name="date_to"
                                value="{{ $filters['date_to'] ?? '' }}"
                                class="w-full rounded-md border border-slate-400 px-3 py-2 text-sm transition-all focus:border-slate-600 focus:ring-2 focus:ring-slate-200"
                            >
                        </div>
                    </div>

                    <div class="filter-modal-actions">
                        <a
                            href="{{ route('yer-sotuvlar.fin-xisobot') }}"
                            class="rounded-md border border-slate-400 bg-white px-4 py-2 text-center text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50"
                        >
                            Тозалаш
                        </a>
                        <button
                            type="button"
                            class="rounded-md border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-200"
                            onclick="closeFinFilterModal()"
                        >
                            Бекор қилиш
                        </button>
                        <button
                            type="submit"
                            class="rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-slate-900"
                        >
                            Қўллаш
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.getElementById('fin-filter-modal');
            const trigger = document.getElementById('fin-filter-trigger');

            if (!modal) {
                return;
            }

            window.openFinFilterModal = function () {
                modal.hidden = false;
                document.body.classList.add('modal-open');

                const firstField = modal.querySelector('select, input, button, a');
                if (firstField) {
                    firstField.focus();
                }
            };

            window.closeFinFilterModal = function () {
                modal.hidden = true;
                document.body.classList.remove('modal-open');

                if (trigger) {
                    trigger.focus();
                }
            };

            window.handleFinFilterBackdrop = function (event) {
                if (event.target === modal) {
                    window.closeFinFilterModal();
                }
            };

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !modal.hidden) {
                    window.closeFinFilterModal();
                }
            });
        })();
    </script>

    <style>
        .report-card {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        }

        .report-header {
            border-bottom: 1px solid #dbe2ea;
            background: #ffffff;
        }

        .report-kicker {
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .report-notice {
            border: 1px solid #fcd34d;
            border-left-width: 4px;
            border-radius: 8px;
            background: #fffbeb;
        }

        .statistics-table {
            --index-col-width: 40px;
            --district-col-width: 150px;
            --sticky-total-width: calc(var(--index-col-width) + var(--district-col-width));
            width: max-content;
            min-width: 100%;
            border-spacing: 0;
            border-top: 1px solid #cbd5e1;
        }

        .statistics-table th,
        .statistics-table td {
            box-sizing: border-box;
            font-size: 12.5px;
            border-color: #cbd5e1;
        }

        .statistics-table th {
            white-space: normal;
            line-height: 1.5;
            word-break: normal;
            overflow-wrap: break-word;
            hyphens: none;
        }

        .statistics-table thead th {
            background: #e2e8f0;
            color: #0f172a;
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .table-subhead-row th {
            background: #f1f5f9;
        }

        .statistics-table tbody td {
            font-variant-numeric: tabular-nums;
            line-height: 1.45;
        }

        .statistics-table tbody tr:not(.summary-row):nth-child(even) td {
            background-color: #f8fafc;
        }

        .table-head-row th {
            border-bottom-width: 1px;
            border-bottom-color: #94a3b8;
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

        .summary-row td {
            background: #f1f5f9;
            border-top: 2px solid #64748b;
            border-bottom: 1px solid #cbd5e1;
            box-shadow: none;
        }

        .supplement-row td {
            background-color: #f8fafc;
        }

        .metric-link,
        .metric-static {
            display: block;
            margin: -2px -6px;
            padding: 2px 6px;
            border-radius: 8px;
        }

        .metric-link {
            color: #2563eb;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .metric-link:hover {
            background-color: #eff6ff;
            color: #1d4ed8;
            text-decoration: none;
        }

        .metric-value {
            font-size: 13.5px;
            font-weight: 700;
            letter-spacing: -0.01em;
            font-variant-numeric: tabular-nums;
        }

        .dash-value {
            color: #cbd5e1;
            font-weight: 600;
        }

        .residual-value {
            color: #b91c1c;
            font-size: 13.5px;
            font-weight: 700;
            letter-spacing: -0.01em;
            font-variant-numeric: tabular-nums;
        }

        .table-meta-strip {
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 0.85rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .table-meta-title {
            color: #0f172a;
            font-size: 13px;
            font-weight: 700;
        }

        .table-meta-subtitle {
            margin-top: 0.15rem;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
        }

        .table-meta-status {
            margin-top: 0.35rem;
            color: #334155;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.55;
        }

        .table-meta-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .table-meta-unit {
            flex-shrink: 0;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #ffffff;
            color: #334155;
            font-size: 11px;
            font-weight: 700;
            padding: 0.55rem 0.85rem;
        }

        .filter-trigger {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid #0f172a;
            border-radius: 6px;
            background: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            line-height: 1;
            padding: 0.7rem 0.95rem;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }

        .filter-trigger:hover {
            background: #1e293b;
            border-color: #1e293b;
        }

        .filter-trigger svg,
        .filter-modal-close svg {
            width: 16px;
            height: 16px;
        }

        .filter-modal {
            position: fixed;
            inset: 0;
            z-index: 80;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(3px);
        }

        .filter-modal[hidden] {
            display: none !important;
        }

        .filter-modal-panel {
            width: min(760px, 100%);
            max-height: calc(100vh - 3rem);
            overflow: auto;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 28px 80px rgba(15, 23, 42, 0.25);
        }

        .filter-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 1.1rem 1.25rem;
        }

        .filter-modal-kicker {
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .filter-modal-title {
            margin-top: 0.25rem;
            color: #0f172a;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.35;
        }

        .filter-modal-subtitle {
            margin-top: 0.25rem;
            color: #64748b;
            font-size: 12px;
            line-height: 1.5;
        }

        .filter-modal-close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #cbd5e1;
            border-radius: 9999px;
            background: #ffffff;
            color: #475569;
            padding: 0.6rem;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .filter-modal-close:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .filter-modal-body {
            padding: 1.25rem;
        }

        .filter-modal-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 0.75rem;
            padding-top: 0.5rem;
        }

        body.modal-open {
            overflow: hidden;
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

            .filter-trigger,
            #fin-filter-modal {
                display: none !important;
            }

            body {
                background: white;
            }
        }

        @media (max-width: 768px) {
            .table-meta-strip {
                flex-direction: column;
                align-items: flex-start;
            }

            .table-meta-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .filter-modal {
                padding: 0.75rem;
            }

            .filter-modal-header {
                padding: 1rem;
            }

            .filter-modal-body {
                padding: 1rem;
            }

            .filter-modal-actions {
                justify-content: stretch;
            }

            .filter-modal-actions > * {
                width: 100%;
            }
        }
    </style>
@endsection
