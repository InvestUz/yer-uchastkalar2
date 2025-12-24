@extends('layouts.app')

@section('title', 'Мониторинг ва Аналитика')

@php
    // Define month names for reuse
    $monthNames = [
        1 => 'Январь',
        2 => 'Февраль',
        3 => 'Март',
        4 => 'Апрель',
        5 => 'Май',
        6 => 'Июнь',
        7 => 'Июль',
        8 => 'Август',
        9 => 'Сентябрь',
        10 => 'Октябрь',
        11 => 'Ноябрь',
        12 => 'Декабрь',
    ];
@endphp

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-50 py-8">
        <div class="mx-auto px-6">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold mb-2" style="color: rgb(30, 41, 59);">
                    Тўловлар мониторинги ва аналитика
                </h1>
                <p class="text-slate-600 text-lg">
                    Ер сотув аукционлари бўйича тўловларни кузатиш ва таҳлил қилиш
                </p>
            </div>

            <!-- Period Filter Buttons -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-slate-800">Ҳисобот даври</h2>
                    <div class="flex items-center gap-3">
                        <!-- Clear Button -->
                        @if ($periodInfo['period'] !== 'all')
                            <a href="{{ route('yer-sotuvlar.monitoring') }}"
                                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Тозалаш
                            </a>
                        @endif

                        <!-- Period Display -->
                        <span class="text-sm text-slate-500 bg-slate-100 px-4 py-2 rounded-lg font-semibold">
                            @if ($periodInfo['period'] === 'month')
                                {{ $monthNames[$periodInfo['month']] ?? '' }} {{ $periodInfo['year'] }} ойи ҳолатига
                            @elseif($periodInfo['period'] === 'quarter')
                                {{ $periodInfo['quarter'] }}-чорак ҳолатига {{ $periodInfo['year'] }} й
                            @elseif($periodInfo['period'] === 'year')
                                {{ $periodInfo['year'] }} йил ҳолатига
                            @else
                                Барча давр
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Main Period Filter -->
                <div class="flex gap-0 border border-gray-300 rounded-lg overflow-hidden mb-6">
                    <a href="{{ route('yer-sotuvlar.monitoring', ['period' => 'month', 'year' => $periodInfo['year'], 'month' => now()->subMonth()->month]) }}"
                        class="flex-1 px-6 py-3 text-sm font-semibold period-filter-btn transition-all border-r border-gray-300 {{ $periodInfo['period'] === 'month' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                        id="btn-month">
                        Ойлик ҳисобот
                    </a>
                    <a href="{{ route('yer-sotuvlar.monitoring', ['period' => 'quarter', 'year' => $periodInfo['year'], 'quarter' => ceil(now()->month / 3)]) }}"
                        class="flex-1 px-6 py-3 text-sm font-semibold period-filter-btn transition-all border-r border-gray-300 {{ $periodInfo['period'] === 'quarter' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                        id="btn-quarter">
                        Чораклик ҳисобот
                    </a>
                    <a href="{{ route('yer-sotuvlar.monitoring', ['period' => 'year', 'year' => now()->year]) }}"
                        class="flex-1 px-6 py-3 text-sm font-semibold period-filter-btn transition-all border-r border-gray-300 {{ $periodInfo['period'] === 'year' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                        id="btn-year">
                        Йиллик ҳисобот
                    </a>
                    <a href="{{ route('yer-sotuvlar.monitoring', ['period' => 'all']) }}"
                        class="flex-1 px-6 py-3 text-sm font-semibold period-filter-btn transition-all {{ $periodInfo['period'] === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                        id="btn-all">
                        Умумий ҳисобот
                    </a>
                </div>

                {{-- Period Filter Section --}}
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <form method="GET" action="{{ route('yer-sotuvlar.monitoring') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            {{-- Period Type Selector --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Давр тури
                                </label>
                                <select name="period" id="period"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="all" {{ $periodInfo['period'] === 'all' ? 'selected' : '' }}>
                                        Барчаси (умумий)
                                    </option>
                                    <option value="year" {{ $periodInfo['period'] === 'year' ? 'selected' : '' }}>
                                        Йил бўйича
                                    </option>
                                    <option value="quarter" {{ $periodInfo['period'] === 'quarter' ? 'selected' : '' }}>
                                        Чорак бўйича
                                    </option>
                                    <option value="month" {{ $periodInfo['period'] === 'month' ? 'selected' : '' }}>
                                        Ой бўйича
                                    </option>
                                </select>
                            </div>

                            {{-- Year Selector --}}
                            <div id="year-selector"
                                style="display: {{ in_array($periodInfo['period'], ['year', 'quarter', 'month']) ? 'block' : 'none' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Йил
                                </label>
                                <select name="year" id="year-select"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    @foreach ($availablePeriods['years'] as $year)
                                        <option value="{{ $year }}"
                                            {{ $periodInfo['year'] == $year ? 'selected' : '' }}>
                                            {{ $year }} йил
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Quarter Selector --}}
                            <div id="quarter-selector"
                                style="display: {{ $periodInfo['period'] === 'quarter' ? 'block' : 'none' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Чорак
                                </label>
                                <select name="quarter" id="quarter-select"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    @if ($periodInfo['period'] === 'quarter')
                                        @php
                                            $selectedYearQuarters = collect($availablePeriods['quarters'])
                                                ->where('yil', $periodInfo['year'])
                                                ->sortBy('chorak_raqam');
                                        @endphp
                                        @forelse($selectedYearQuarters as $quarter)
                                            <option value="{{ $quarter['chorak_raqam'] }}"
                                                {{ $periodInfo['quarter'] == $quarter['chorak_raqam'] ? 'selected' : '' }}>
                                                {{ $quarter['chorak_nomi'] }}
                                                ({{ number_format($quarter['summa'] / 1000000000, 2) }} млрд)
                                            </option>
                                        @empty
                                            <option value="">Маълумот йўқ</option>
                                        @endforelse
                                    @else
                                        <option value="1">1-чорак (Январь - Март)</option>
                                    @endif
                                </select>
                            </div>

                            {{-- Month Selector --}}
                            <div id="month-selector"
                                style="display: {{ $periodInfo['period'] === 'month' ? 'block' : 'none' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Ой
                                </label>
                                <select name="month" id="month-select"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    @if ($periodInfo['period'] === 'month')
                                        @php
                                            $selectedYearMonths = collect($availablePeriods['months'])
                                                ->where('yil', $periodInfo['year'])
                                                ->sortBy('oy');
                                        @endphp
                                        @forelse($selectedYearMonths as $month)
                                            <option value="{{ $month['oy'] }}"
                                                {{ $periodInfo['month'] == $month['oy'] ? 'selected' : '' }}>
                                                {{ $month['oy_nomi'] }}
                                                ({{ number_format($month['summa'] / 1000000000, 2) }} млрд)
                                            </option>
                                        @empty
                                            <option value="">Маълумот йўқ</option>
                                        @endforelse
                                    @else
                                        @php
                                            $oylar = [
                                                1 => 'Январь',
                                                2 => 'Февраль',
                                                3 => 'Март',
                                                4 => 'Апрель',
                                                5 => 'Май',
                                                6 => 'Июнь',
                                                7 => 'Июль',
                                                8 => 'Август',
                                                9 => 'Сентябрь',
                                                10 => 'Октябрь',
                                                11 => 'Ноябрь',
                                                12 => 'Декабрь',
                                            ];
                                        @endphp
                                        @foreach ($oylar as $oyRaqam => $oyNomi)
                                            <option value="{{ $oyRaqam }}">
                                                {{ $oyNomi }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('yer-sotuvlar.monitoring') }}"
                                class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                Тозалаш
                            </a>
                            <button type="submit" onclick="cleanFormBeforeSubmit(event)"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Қидириш
                            </button>
                        </div>
                    </form>
                </div>

                {{-- JavaScript for Dynamic Form --}}
                <script>
                    function cleanFormBeforeSubmit(event) {
                        const periodValue = document.getElementById('period').value;

                        if (periodValue === 'all') {
                            document.getElementById('year-select').disabled = true;
                            document.getElementById('quarter-select').disabled = true;
                            document.getElementById('month-select').disabled = true;
                        } else if (periodValue === 'year') {
                            document.getElementById('quarter-select').disabled = true;
                            document.getElementById('month-select').disabled = true;
                        } else if (periodValue === 'quarter') {
                            document.getElementById('month-select').disabled = true;
                        } else if (periodValue === 'month') {
                            document.getElementById('quarter-select').disabled = true;
                        }
                    }

                    document.addEventListener('DOMContentLoaded', function() {
                        const periodSelect = document.getElementById('period');
                        const yearSelector = document.getElementById('year-selector');
                        const quarterSelector = document.getElementById('quarter-selector');
                        const monthSelector = document.getElementById('month-selector');
                        const yearSelect = document.getElementById('year-select');
                        const quarterSelect = document.getElementById('quarter-select');
                        const monthSelect = document.getElementById('month-select');

                        let periodData = {
                            years: {!! json_encode($availablePeriods['years']) !!},
                            quarters: {!! json_encode($availablePeriods['quarters']) !!},
                            months: {!! json_encode($availablePeriods['months']) !!}
                        };

                        function updateSelectors() {
                            const periodValue = periodSelect.value;

                            yearSelector.style.display = 'none';
                            quarterSelector.style.display = 'none';
                            monthSelector.style.display = 'none';

                            if (periodValue === 'year') {
                                yearSelector.style.display = 'block';
                            } else if (periodValue === 'quarter') {
                                yearSelector.style.display = 'block';
                                quarterSelector.style.display = 'block';
                            } else if (periodValue === 'month') {
                                yearSelector.style.display = 'block';
                                monthSelector.style.display = 'block';
                            }
                        }

                        yearSelect.addEventListener('change', function() {
                            if (periodSelect.value === 'quarter') {
                                const selectedYear = parseInt(this.value);
                                const quartersForYear = periodData.quarters.filter(q => q.yil === selectedYear);

                                quarterSelect.innerHTML = '';

                                if (quartersForYear.length > 0) {
                                    quartersForYear.forEach(q => {
                                        const option = document.createElement('option');
                                        option.value = q.chorak_raqam;
                                        option.textContent = q.chorak_nomi + ' (' + (q.summa / 1000000000)
                                            .toFixed(2) + ' млрд)';
                                        quarterSelect.appendChild(option);
                                    });
                                } else {
                                    const option = document.createElement('option');
                                    option.value = '';
                                    option.textContent = 'Бу йил учун маълумот йўқ';
                                    quarterSelect.appendChild(option);
                                }
                            } else if (periodSelect.value === 'month') {
                                const selectedYear = parseInt(this.value);
                                const monthsForYear = periodData.months.filter(m => m.yil === selectedYear);

                                monthSelect.innerHTML = '';

                                if (monthsForYear.length > 0) {
                                    monthsForYear.forEach(m => {
                                        const option = document.createElement('option');
                                        option.value = m.oy;
                                        option.textContent = m.oy_nomi + ' (' + (m.summa / 1000000000).toFixed(
                                            2) + ' млрд)';
                                        monthSelect.appendChild(option);
                                    });
                                } else {
                                    const option = document.createElement('option');
                                    option.value = '';
                                    option.textContent = 'Бу йил учун маълумот йўқ';
                                    monthSelect.appendChild(option);
                                }
                            }
                        });

                        function initializeSelectors() {
                            updateSelectors();

                            if (periodSelect.value === 'quarter') {
                                const selectedYear = parseInt(yearSelect.value);
                                const quartersForYear = periodData.quarters.filter(q => q.yil === selectedYear);
                                const currentQuarter = {{ $periodInfo['quarter'] }};

                                quarterSelect.innerHTML = '';
                                quartersForYear.forEach(q => {
                                    const option = document.createElement('option');
                                    option.value = q.chorak_raqam;
                                    option.textContent = q.chorak_nomi + ' (' + (q.summa / 1000000000).toFixed(2) +
                                        ' млрд)';
                                    if (q.chorak_raqam === currentQuarter) {
                                        option.selected = true;
                                    }
                                    quarterSelect.appendChild(option);
                                });
                            }

                            if (periodSelect.value === 'month') {
                                const selectedYear = parseInt(yearSelect.value);
                                const monthsForYear = periodData.months.filter(m => m.yil === selectedYear);
                                const currentMonth = {{ $periodInfo['month'] }};

                                monthSelect.innerHTML = '';
                                monthsForYear.forEach(m => {
                                    const option = document.createElement('option');
                                    option.value = m.oy;
                                    option.textContent = m.oy_nomi + ' (' + (m.summa / 1000000000).toFixed(2) +
                                        ' млрд)';
                                    if (m.oy === currentMonth) {
                                        option.selected = true;
                                    }
                                    monthSelect.appendChild(option);
                                });
                            }
                        }

                        periodSelect.addEventListener('change', updateSelectors);
                        initializeSelectors();
                    });
                </script>
            </div>

            <!-- ROW 1: TOTAL (All Payment Types) -->
            <div class="bg-gradient-to-r from-slate-100 to-gray-100 rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    ЖАМИ (Барча тўлов турлари)
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Card 1: Жами лотлар сони -->
                    <a href="{{ route('yer-sotuvlar.list', array_merge(['include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                        class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1"
                        style="border-color: rgb(29 78 216);">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-slate-700" style="font-size: 22px">Жами лотлар сони</h3>
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                style="background-color: rgba(29, 78, 216, 0.1);">
                                <svg class="w-7 h-7" style="color: rgb(29 78 216);" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold mb-1" style="color: rgb(29 78 216);">
                            {{ number_format($summaryTotal['total_lots']) }} та</p>
                    </a>

                    <!-- Total Card 2: Тушадиган маблағ -->
                    <a href="{{ route('yer-sotuvlar.list', array_merge(['include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                        class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1"
                        style="border-color: rgb(29 78 216);">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-slate-700" style="font-size: 22px">Тушадиган маблағ</h3>
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                style="background-color: rgba(29, 78, 216, 0.1);">
                                <svg class="w-7 h-7" style="color: rgb(29 78 216);" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold mb-1" style="color: rgb(29 78 216);">
                            {{ number_format($summaryTotal['expected_amount'] / 1000000000, 2) }} млрд сўм</p>
                    </a>

                    <!-- Total Card 3: Амалда тушган маблағ -->
                    <a href="{{ route('yer-sotuvlar.list', array_merge(['include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                        class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1"
                        style="border-color: rgb(29 78 216);">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-slate-700" style="font-size: 22px">Амалда тушган маблағ</h3>
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                style="background-color: rgba(29, 78, 216, 0.1);">
                                <svg class="w-7 h-7" style="color: rgb(29 78 216);" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold mb-1" style="color: rgb(29 78 216);">
                            {{ number_format($summaryTotal['received_amount'] / 1000000000, 2) }} млрд сўм</p>
                    </a>

                    <!-- Total Card 4: Қолдиқ маблағ -->
                    @php
                        $totalQoldiq = $summaryTotal['expected_amount'] - $summaryTotal['received_amount'];
                        $totalQoldiqFoizi =
                            $summaryTotal['expected_amount'] > 0
                                ? ($totalQoldiq / $summaryTotal['expected_amount']) * 100
                                : 0;
                    @endphp
                    <a href="{{ route('yer-sotuvlar.list', array_merge(['include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                        class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1"
                        style="border-color: rgb(185 28 28);">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-slate-700" style="font-size: 22px">Қолдиқ маблағ</h3>
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                style="background-color: rgba(185, 28, 28, 0.1);">
                                <svg class="w-7 h-7" style="color: rgb(185 28 28);" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold mb-2" style="color: rgb(185 28 28);">
                            {{ number_format($totalQoldiq / 1000000000, 2) }} млрд сўм</p>
                        <div class="flex items-center mb-3">
                            <div class="flex-1 bg-gray-200 rounded-full h-2.5 mr-3">
                                <div class="h-2.5 rounded-full transition-all duration-500"
                                    style="width: {{ min(100, $totalQoldiqFoizi) }}%; background-color: rgb(185 28 28);">
                                </div>
                            </div>
                            <span class="text-sm font-bold"
                                style="color: rgb(185 28 28);">{{ number_format($totalQoldiqFoizi, 1) }}%</span>
                        </div>
                    </a>

                    <!-- Total Card 5: Муддати ўтган қарздорлик (JAMI - ALL) -->
                    @php
                        // Calculate muddati utgan for muddatli emas (auksonda turgan)
                        $muddatiOtganMuddatliEmas = max(
                            0,
                            $summaryMuddatliEmas['expected_amount'] - $summaryMuddatliEmas['received_amount'],
                        );
                        // Total muddati utgan = muddatli (grafik ortda) + muddatli emas (auksonda turgan)
                        $totalMuddatiUtganQarz = $muddatiUtganQarz + $muddatiOtganMuddatliEmas;
                    @endphp
                    <a href="{{ route('yer-sotuvlar.list', array_merge(['grafik_ortda' => 'true', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                        class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1 lg:col-start-4"
                        style="border-color: rgb(185 28 28);">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-slate-700" style="font-size: 22px">Муддати ўтган қарздорлик</h3>
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                style="background-color: rgba(185, 28, 28, 0.1);">
                                <svg class="w-7 h-7" style="color: rgb(185 28 28);" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold mb-2" style="color: rgb(185 28 28);">
                            {{ number_format($totalMuddatiUtganQarz / 1000000000, 2) }} млрд сўм
                        </p>
                        <div class="flex items-center mb-3">
                            <div class="flex-1 bg-gray-200 rounded-full h-2.5 mr-3">
                                <div class="h-2.5 rounded-full transition-all duration-500"
                                    style="width: {{ min(100, $totalQoldiqFoizi) }}%; background-color: rgb(185 28 28);">
                                </div>
                            </div>
                            <span class="text-sm font-bold"
                                style="color: rgb(185 28 28);">{{ number_format($totalQoldiqFoizi, 1) }}%</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- ROW 2: МУДДАТЛИ (Installment Payments) -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Муддатли тўлов (Бўлиб тўлаш)
                </h2>

                <!-- Муддатли Content -->
                <div id="content-muddatli" class="tab-content">
                    <!-- Statistics Cards - Муддатли -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                        <!-- Card 1: Жами лотлар сони -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                            class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1"
                            style="border-color: rgb(185 28 28);">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-slate-700" style="font-size: 22px">Жами лотлар сони</h3>
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(185, 28, 28, 0.1);">
                                    <svg class="w-7 h-7" style="color: rgb(185 28 28);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold mb-1" style="color: rgb(185 28 28);">
                                {{ number_format($summaryMuddatli['total_lots']) }} та</p>
                        </a>

                        <!-- Card 2: Тушадиган маблағ -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли', 'nazoratda' => 'true', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                            class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1"
                            style="border-color: rgb(29 78 216);">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-slate-700" style="font-size: 22px">Тушадиган маблағ</h3>
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(29, 78, 216, 0.1);">
                                    <svg class="w-7 h-7" style="color: rgb(29 78 216);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold mb-1" style="color: rgb(29 78 216);">
                                {{ number_format($nazoratdagilar['tushadigan_mablagh'] / 1000000000, 2) }} млрд сўм</p>
                        </a>

                        <!-- Card 3: Амалда тушган маблағ -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли', 'nazoratda' => 'true', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                            class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1"
                            style="border-color: rgb(29 78 216);">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-slate-700" style="font-size: 22px">Амалда тушган маблағ</h3>
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(29, 78, 216, 0.1);">
                                    <svg class="w-7 h-7" style="color: rgb(29 78 216);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold mb-1" style="color: rgb(29 78 216);">
                                {{ number_format($nazoratdagilar['tushgan_summa'] / 1000000000, 2) }} млрд сўм</p>
                        </a>

                        <!-- Card 4: Қолдиқ маблағ -->
                        @php
                            $qoldiqMablagh = $nazoratdagilar['tushadigan_mablagh'] - $nazoratdagilar['tushgan_summa'];
                            $qoldiqFoizi =
                                $nazoratdagilar['tushadigan_mablagh'] > 0
                                    ? (($nazoratdagilar['tushadigan_mablagh'] - $nazoratdagilar['tushgan_summa']) /
                                            $nazoratdagilar['tushadigan_mablagh']) *
                                        100
                                    : 0;
                        @endphp
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли', 'nazoratda' => 'true', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                            class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1"
                            style="border-color: rgb(185 28 28);">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-slate-700" style="font-size: 22px">Қолдиқ маблағ</h3>
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(185, 28, 28, 0.1);">
                                    <svg class="w-7 h-7" style="color: rgb(185 28 28);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold mb-2" style="color: rgb(185 28 28);">
                                {{ number_format($qoldiqMablagh / 1000000000, 2) }} млрд сўм</p>
                            <div class="flex items-center mb-3">
                                <div class="flex-1 bg-gray-200 rounded-full h-2.5 mr-3">
                                    <div class="h-2.5 rounded-full transition-all duration-500"
                                        style="width: {{ 100 - min(100, $qoldiqFoizi) }}%; background-color: rgb(185 28 28);">
                                    </div>
                                </div>
                                <span class="text-sm font-bold"
                                    style="color: rgb(185 28 28);">{{ number_format(100 - $qoldiqFoizi, 1) }}%</span>
                            </div>
                        </a>

                        <!-- Card 5: График б-ча тушадиган маблағ -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                            class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1 lg:col-start-2"
                            style="border-color: rgb(29 78 216);">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-slate-700" style="font-size: 22px">График б-ча тушадиган
                                    маблағ</h3>
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(29, 78, 216, 0.1);">
                                    <svg class="w-7 h-7" style="color: rgb(29 78 216);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold mb-1" style="color: rgb(29 78 216);">
                                {{ number_format($grafikTushadiganMuddatli / 1000000000, 2) }} млрд сўм</p>
                        </a>

                        <!-- Card 6: График бўйича тушган -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                            class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1 lg:col-start-3"
                            style="border-color: rgb(29 78 216);">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-slate-700" style="font-size: 22px">График бўйича тушган</h3>
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(29, 78, 216, 0.1);">
                                    <svg class="w-7 h-7" style="color: rgb(29 78 216);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold mb-1" style="color: rgb(29 78 216);">
                                {{ number_format($grafikBoyichaTushgan / 1000000000, 2) }} млрд сўм
                            </p>
                        </a>

                        <!-- Card 7: Муддати ўтган қарздорлик (Муддатли only) -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли', 'grafik_ortda' => 'true', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                            class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1 lg:col-start-4"
                            style="border-color: rgb(185 28 28);">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-slate-700" style="font-size: 22px">Муддати ўтган қарздорлик
                                </h3>
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(185, 28, 28, 0.1);">
                                    <svg class="w-7 h-7" style="color: rgb(185 28 28);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold mb-1" style="color: rgb(185 28 28);">
                                {{ number_format($muddatiUtganQarz / 1000000000, 2) }} млрд сўм
                            </p>
                        </a>
                    </div>
                </div>

                <!-- Payment Type Tabs -->
                <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Муддатсиз тўлов (Бир йўла тўлаш)
                </h2>

                <!-- Муддатли эмас Content -->
                <div id="content-muddatli-emas" class="tab-content">
                    <!-- Statistics Cards - Муддатли эмас -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- 1. Soni -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли эмас', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                            class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1"
                            style="border-color: rgb(185 28 28);">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-slate-700" style="font-size: 22px">Жами лотлар сони</h3>
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(185, 28, 28, 0.1);">
                                    <svg class="w-7 h-7" style="color: rgb(185 28 28);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold mb-1" style="color: rgb(185 28 28);">
                                {{ number_format($summaryMuddatliEmas['total_lots']) }} та</p>
                        </a>

                        <!-- 2. Tushadigan mablag' -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли эмас', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                            class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1"
                            style="border-color: rgb(29 78 216);">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-slate-700" style="font-size: 22px">Тушадиган маблағ</h3>
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(29, 78, 216, 0.1);">
                                    <svg class="w-7 h-7" style="color: rgb(29 78 216);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold mb-1" style="color: rgb(29 78 216);">
                                {{ number_format($summaryMuddatliEmas['expected_amount'] / 1000000000, 2) }} млрд сўм</p>
                        </a>

                        <!-- 4. Amalda to'langan -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли эмас', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                            class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1"
                            style="border-color: rgb(29 78 216);">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-slate-700" style="font-size: 22px">Амалда тўланган</h3>
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(29, 78, 216, 0.1);">
                                    <svg class="w-7 h-7" style="color: rgb(29 78 216);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold mb-2" style="color: rgb(29 78 216);">
                                {{ number_format($summaryMuddatliEmas['received_amount'] / 1000000000, 2) }} млрд сўм</p>
                            <div class="flex items-center">
                                <div class="flex-1 bg-gray-200 rounded-full h-2.5 mr-3">
                                    <div class="h-2.5 rounded-full transition-all duration-500"
                                        style="width: {{ min(100, $summaryMuddatliEmas['payment_percentage']) }}%; background-color: rgb(29 78 216);">
                                    </div>
                                </div>
                                <span class="text-sm font-bold"
                                    style="color: rgb(29 78 216);">{{ number_format($summaryMuddatliEmas['payment_percentage'], 1) }}%</span>
                            </div>
                        </a>

                        <!-- 5. Аукционда турган маблағ (Qoldiq qarz - to'lanmagan lotlar) -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли эмас', 'qoldiq_qarz' => 'true', 'include_all' => 'false', 'auksion_sana_from' => $dateFilters['auksion_sana_from'], 'auksion_sana_to' => $dateFilters['auksion_sana_to']], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
                            class="block bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-2xl transition-all transform hover:-translate-y-1"
                            style="border-color: rgb(185 28 28);">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-slate-700" style="font-size: 22px">Аукционда турган маблағ
                                </h3>
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(185, 28, 28, 0.1);">
                                    <svg class="w-7 h-7" style="color: rgb(185 28 28);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold mb-1" style="color: rgb(185 28 28);">
                                {{ number_format($qoldiqQarzData['qoldiq_amount'] / 1000000000, 2) }} млрд сўм
                            </p>
                            <p class="text-1xl text-slate-500">{{ $qoldiqQarzData['count'] }} та лот - мулкни қабул қилиш тасдиқланмаганлар</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .tab-button {
                position: relative;
                overflow: hidden;
            }

            .tab-button::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
                transform: translate(-50%, -50%);
                transition: width 0.6s, height 0.6s;
            }

            .tab-button:hover::before {
                width: 300px;
                height: 300px;
            }

            .period-filter-btn {
                position: relative;
                overflow: hidden;
            }

            .period-filter-btn::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                border-radius: 50%;
                background: rgba(59, 130, 246, 0.1);
                transform: translate(-50%, -50%);
                transition: width 0.6s, height 0.6s;
            }

            .period-filter-btn:hover::before {
                width: 300px;
                height: 300px;
            }

            .bg-white {
                transition: all 0.3s ease;
            }

            .hover\:shadow-xl:hover {
                transform: translateY(-2px);
            }

            @keyframes fadeInRow {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            tbody tr {
                animation: fadeInRow 0.3s ease-in-out;
            }
        </style>
    @endsection
