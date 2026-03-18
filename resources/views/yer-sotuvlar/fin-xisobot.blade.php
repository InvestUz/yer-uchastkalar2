@extends('layouts.app')

@section('title', 'Бўлиб тўлаш маълумоти')

@section('content')
    @php
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

    $filters = $filters ?? [
        'year' => null,
        'month' => null,
        'date_from' => null,
        'date_to' => null,
    ];
    $activeFilterParams = $activeFilterParams ?? [];
    $availableYears = $availableYears ?? [];
    $monthOptions = $monthOptions ?? [];

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
    $hasActiveFilters = !empty($periodParts);
    $activeFilterText = $hasActiveFilters ? implode(' | ', $periodParts) : 'Барча давр';
    @endphp
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
                            <p class="text-xs text-blue-700 mt-1">
                                Амалдаги фильтр: {{ $activeFilterText }}
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

                          @foreach($paymentCategories as $category => $value)
    <th
                                    class="border border-slate-300 px-2 py-2 text-center align-middle font-bold text-slate-800"
                                    style="min-width: 100px; font-size:11px;"
                                    title="{{ $category }}"
                                >
                                    {{ \Illuminate\Support\Str::limit($category, 15, '...') }}
                                </th>
                            @endforeach
                                </tr>
                            </thead>

                            <tbody class="bg-white">
                                @if(empty($districtData) || count($districtData) === 0)
                                    <tr>
                                        <td colspan="{{ 3 + count($paymentCategories) }}" class="border border-slate-300 px-4 py-6 text-center text-slate-700">
                                            Маълумотлар топилмади.
                                        </td>
                                    </tr>
                                @else
                                    <tr class="bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100 border-y-2 border-amber-400">
                                        <td colspan="2" class="sticky-col-total border border-slate-300 px-3 py-2 text-center align-middle font-bold text-slate-900 text-xs uppercase bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100">
                                            ЖАМИ:
                                        </td>
                                        <td class="total-amount-col border border-slate-300 px-2 py-1 text-right font-bold text-slate-900">
                                            <a href="{{ route('yer-sotuvlar.fin-xisobot.details', $activeFilterParams) }}" class="block text-blue-700 hover:text-blue-900 hover:underline">
                                                <span class="font-semibold">{{ $fmt($totalAmount) }}</span><br>
                                                <span class="text-slate-400">{{ $transactionCount ?? 0 }} та</span>
                                            </a>
                                        </td>

                                        @foreach($paymentCategories as $category => $value)
                                            <td class="border border-slate-300 px-2 py-1 text-right font-bold text-slate-900">
                                                @php $catTotal = $categoryTotals[$category] ?? 0; @endphp
                                                @if($catTotal > 0)
                                                    <a href="{{ route('yer-sotuvlar.fin-xisobot.details', array_merge($activeFilterParams, ['category' => $category])) }}" class="block text-blue-700 hover:text-blue-900 hover:underline text-right">
                                                        <span class="font-semibold">{{ $fmt($catTotal) }}</span><br>
                                                        <span class="text-slate-400">{{ $categoryCounts[$category] ?? 0 }} та</span>
                                                    </a>
                                                @else
                                                    <span class="text-slate-300">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>

                                    @foreach($districtData as $district => $values)
                                        <tr class="hover:bg-blue-50 transition-colors duration-150 bg-white">
                                            <td class="sticky-col border border-slate-300 px-2 py-1 text-center align-middle font-medium text-slate-700">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td class="sticky-col-2 border border-slate-300 px-2 py-1 align-middle font-semibold text-slate-800">
                                                {{ $district }}
                                            </td>
                                            <td class="total-amount-col border border-slate-300 px-2 py-1 text-right text-slate-700">
                                                @php $districtTotal = $values['Жами'] ?? 0; @endphp
                                                @if($districtTotal > 0)
                                                    <a href="{{ route('yer-sotuvlar.fin-xisobot.details', array_merge($activeFilterParams, ['district' => $district])) }}" class="block text-blue-700 hover:text-blue-900 hover:underline text-right">
                                                        <span class="font-semibold">{{ $fmt($districtTotal) }}</span><br>
                                                        <span class="text-slate-400">{{ $districtCounts[$district] ?? 0 }} та</span>
                                                    </a>
                                                @else
                                                    <span class="text-slate-300">—</span>
                                                @endif
                                            </td>

                                            @foreach($paymentCategories as $category => $value)
                                                <td class="border border-slate-300 px-2 py-1 text-right text-slate-700">
                                                    @php $cellAmount = $values[$category] ?? 0; @endphp
                                                    @if($cellAmount > 0)
                                                        <a href="{{ route('yer-sotuvlar.fin-xisobot.details', array_merge($activeFilterParams, ['district' => $district, 'category' => $category])) }}" class="block text-blue-700 hover:text-blue-900 hover:underline text-right">
                                                            <span class="font-semibold">{{ $fmt($cellAmount) }}</span><br>
                                                            <span class="text-slate-400">{{ $districtCategoryCounts[$district][$category] ?? 0 }} та</span>
                                                        </a>
                                                    @else
                                                        <span class="text-slate-300">—</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- Premium Filter Section -->
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-t-4 border-blue-600">

                <div class="p-6 bg-gradient-to-br from-slate-50 to-blue-50">
                    <form method="GET" action="{{ route('yer-sotuvlar.fin-xisobot') }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Йил:</label>
                                <select name="year" class="w-full px-4 py-3 border-2 border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                    <option value="">Барча йиллар</option>
                                    @foreach($availableYears as $yearOption)
                                        <option value="{{ $yearOption }}" @selected((int)($filters['year'] ?? 0) === (int)$yearOption)>
                                            {{ $yearOption }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Ой:</label>
                                <select name="month" class="w-full px-4 py-3 border-2 border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                    <option value="">Барча ойлар</option>
                                    @foreach($monthOptions as $monthNumber => $monthLabel)
                                        <option value="{{ $monthNumber }}" @selected((int)($filters['month'] ?? 0) === (int)$monthNumber)>
                                            {{ $monthLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date From -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Бошланғич санаси:</label>
                                <input type="date" name="date_from" class="w-full px-4 py-3 border-2 border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" value="{{ $filters['date_from'] ?? '' }}">
                            </div>

                            <!-- Date To -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Тугаш санаси:</label>
                                <input type="date" name="date_to" class="w-full px-4 py-3 border-2 border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" value="{{ $filters['date_to'] ?? '' }}">
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-4 mt-6">
                                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Қидириш
                                </button>
                                <a href="{{ route('yer-sotuvlar.fin-xisobot') }}" class="flex-1 bg-gradient-to-r from-slate-500 to-slate-600 hover:from-slate-600 hover:to-slate-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
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


@endsection

