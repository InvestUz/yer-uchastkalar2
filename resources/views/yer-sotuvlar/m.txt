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
                                    {{-- Dynamically populated by JavaScript based on selected year --}}
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
                                    {{-- Dynamically populated by JavaScript based on selected year --}}
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
                    // Clean form before submit to remove unnecessary parameters
                    function cleanFormBeforeSubmit(event) {
                        const periodValue = document.getElementById('period').value;

                        // Disable inputs based on period type
                        if (periodValue === 'all') {
                            // Disable all period selectors
                            document.getElementById('year-select').disabled = true;
                            document.getElementById('quarter-select').disabled = true;
                            document.getElementById('month-select').disabled = true;
                        } else if (periodValue === 'year') {
                            // Only year is needed
                            document.getElementById('quarter-select').disabled = true;
                            document.getElementById('month-select').disabled = true;
                        } else if (periodValue === 'quarter') {
                            // Year and quarter are needed, disable month
                            document.getElementById('month-select').disabled = true;
                        } else if (periodValue === 'month') {
                            // Year and month are needed, disable quarter
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

                        // Store period data
                        let periodData = {
                            years: {!! json_encode($availablePeriods['years']) !!},
                            quarters: {!! json_encode($availablePeriods['quarters']) !!},
                            months: {!! json_encode($availablePeriods['months']) !!}
                        };

                        function updateSelectors() {
                            const periodValue = periodSelect.value;

                            // Hide all first
                            yearSelector.style.display = 'none';
                            quarterSelector.style.display = 'none';
                            monthSelector.style.display = 'none';

                            // Show based on selection
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

                        // Update quarters when year changes (for quarter selection)
                        yearSelect.addEventListener('change', function() {
                            if (periodSelect.value === 'quarter') {
                                const selectedYear = parseInt(this.value);
                                const quartersForYear = periodData.quarters.filter(q => q.yil === selectedYear);

                                // Rebuild quarter options
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

                                // Rebuild month options
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

                        // Initialize on page load
                        function initializeSelectors() {
                            updateSelectors();

                            // If quarter mode is active, populate quarters for selected year
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

                            // If month mode is active, populate months for selected year
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
                        initializeSelectors(); // Initial call with data population
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
                    <a href="{{ route('yer-sotuvlar.list', $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : []) }}"
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
                        @if ($periodInfo['period'] !== 'all')
                            <div class="mt-auto pt-3 border-t border-slate-200" style="display: none">
                                <p class="text-xs text-blue-600 font-medium flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span>{{ $periodInfo['period'] === 'month' ? ($monthNames[$periodInfo['month']] ?? '') . ' ' . $periodInfo['year'] : ($periodInfo['period'] === 'quarter' ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year'] : ($periodInfo['period'] === 'year' ? $periodInfo['year'] . ' йил' : '')) }}</span>
                                </p>
                            </div>
                        @endif
                    </a>

                    <!-- Total Card 2: Тушадиган маблағ -->
                    <a href="{{ route('yer-sotuvlar.list', $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : []) }}"
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
                        @if ($periodInfo['period'] !== 'all')
                            <div class="mt-auto pt-3 border-t border-slate-200" style="display: none">
                                <p class="text-xs text-blue-600 font-medium flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span>{{ $periodInfo['period'] === 'month' ? ($monthNames[$periodInfo['month']] ?? '') . ' ' . $periodInfo['year'] : ($periodInfo['period'] === 'quarter' ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year'] : ($periodInfo['period'] === 'year' ? $periodInfo['year'] . ' йил' : '')) }}</span>
                                </p>
                            </div>
                        @endif
                    </a>

                    <!-- Total Card 3: Амалда тушган маблағ -->
                    <a href="{{ route('yer-sotuvlar.list', $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : []) }}"
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
                        @if ($periodInfo['period'] !== 'all')
                            <div class="mt-auto pt-3 border-t border-slate-200" style="display: none">
                                <p class="text-xs text-blue-600 font-medium flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span>{{ $periodInfo['period'] === 'month' ? ($monthNames[$periodInfo['month']] ?? '') . ' ' . $periodInfo['year'] : ($periodInfo['period'] === 'quarter' ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year'] : ($periodInfo['period'] === 'year' ? $periodInfo['year'] . ' йил' : '')) }}</span>
                                </p>
                            </div>
                        @endif
                    </a>

                    <!-- Total Card 4: Қолдиқ маблағ -->
                    @php
                        $totalQoldiq = $summaryTotal['expected_amount'] - $summaryTotal['received_amount'];
                        $totalQoldiqFoizi =
                            $summaryTotal['expected_amount'] > 0
                                ? ($totalQoldiq / $summaryTotal['expected_amount']) * 100
                                : 0;
                    @endphp
                    <a href="{{ route('yer-sotuvlar.list', $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : []) }}"
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
                        @if ($periodInfo['period'] !== 'all')
                            <div class="mt-auto pt-3 border-t border-slate-200" style="display: none">
                                <p class="text-xs text-blue-600 font-medium flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span>{{ $periodInfo['period'] === 'month' ? ($monthNames[$periodInfo['month']] ?? '') . ' ' . $periodInfo['year'] : ($periodInfo['period'] === 'quarter' ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year'] : ($periodInfo['period'] === 'year' ? $periodInfo['year'] . ' йил' : '')) }}</span>
                                </p>
                            </div>
                        @endif
                    </a>

                    <!-- Total Card 5: Yigindi Қолдиқ маблағ -->
                    @php
                        $totalQoldiq = $summaryTotal['expected_amount'] - $summaryTotal['received_amount'];
                        $totalQoldiqFoizi =
                            $summaryTotal['expected_amount'] > 0
                                ? ($totalQoldiq / $summaryTotal['expected_amount']) * 100
                                : 0;
                    @endphp
                    <a href="{{ route('yer-sotuvlar.list', $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : []) }}"
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
                        @php
                            $muddatiOtganMuddatliEmas = max(
                                0,
                                $summaryMuddatliEmas['expected_amount'] - $summaryMuddatliEmas['received_amount'],
                            );
                        @endphp

                        <p class="text-3xl font-bold mb-2" style="color: rgb(185 28 28);">
                            {{ number_format(($muddatiOtganMuddatliEmas + $muddatiUtganQarz) / 1000000000, 2) }} млрд сўм


                            <div class="flex items-center mb-3">
                                <div class="flex-1 bg-gray-200 rounded-full h-2.5 mr-3">
                                    <div class="h-2.5 rounded-full transition-all duration-500"
                                        style="width: {{ min(100, $totalQoldiqFoizi) }}%; background-color: rgb(185 28 28);">
                                    </div>
                                </div>
                                <span class="text-sm font-bold"
                                    style="color: rgb(185 28 28);">{{ number_format($totalQoldiqFoizi, 1) }}%</span>
                            </div>
                            @if ($periodInfo['period'] !== 'all')
                                <div class="mt-auto pt-3 border-t border-slate-200" style="display: none">
                                    <p class="text-xs text-blue-600 font-medium flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span>{{ $periodInfo['period'] === 'month' ? ($monthNames[$periodInfo['month']] ?? '') . ' ' . $periodInfo['year'] : ($periodInfo['period'] === 'quarter' ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year'] : ($periodInfo['period'] === 'year' ? $periodInfo['year'] . ' йил' : '')) }}</span>
                                    </p>
                                </div>
                            @endif
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
                    <!-- Statistics Cards - Муддатли (7 cards with period info at BOTTOM) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                        <!-- Card 1: Жами лотлар сони - CLICKABLE -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли'], $periodInfo['period'] !== 'all' ? ['period' => $periodInfo['period'], 'year' => $periodInfo['year'], 'quarter' => $periodInfo['quarter'] ?? null, 'month' => $periodInfo['month'] ?? null] : [])) }}"
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
                                {{ number_format($summaryMuddatli['total_lots']) }}
                                та</p>

                            <!-- Period info at BOTTOM -->
                            @if ($periodInfo['period'] !== 'all')
                                <div class="mt-auto pt-3 border-t border-slate-200" style="display: none">
                                    <p class="text-xs text-blue-600 font-medium flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span>{{ $periodInfo['period'] === 'month'
                                            ? ($monthNames[$periodInfo['month']] ?? '') . ' ' . $periodInfo['year']
                                            : ($periodInfo['period'] === 'quarter'
                                                ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year']
                                                : ($periodInfo['period'] === 'year'
                                                    ? $periodInfo['year'] . ' йил'
                                                    : '')) }}</span>
                                    </p>
                                </div>
                            @endif
                        </a>

                        <!-- Card 2: Тушадиган маблағ - CLICKABLE -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли', 'nazoratda' => 'true'], $dateFilters)) }}"
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

                            @if ($periodInfo['period'] !== 'all')
                                <div class="mt-auto pt-3 border-t border-slate-200" style="display: none">
                                    <p class="text-xs text-blue-600 font-medium flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span>{{ $periodInfo['period'] === 'month'
                                            ? ($monthNames[$periodInfo['month']] ?? '') . ' ' . $periodInfo['year']
                                            : ($periodInfo['period'] === 'quarter'
                                                ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year']
                                                : ($periodInfo['period'] === 'year'
                                                    ? $periodInfo['year'] . ' йил'
                                                    : '')) }}</span>
                                    </p>
                                </div>
                            @endif
                        </a>

                        <!-- Card 3: Амалда тушган маблағ - CLICKABLE -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли', 'nazoratda' => 'true'], $dateFilters)) }}"
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

                            @if ($periodInfo['period'] !== 'all')
                                <div class="mt-auto pt-3 border-t border-slate-200" style="display: none">
                                    <p class="text-xs text-blue-600 font-medium flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span>{{ $periodInfo['period'] === 'month'
                                            ? ($monthNames[$periodInfo['month']] ?? '') . ' ' . $periodInfo['year']
                                            : ($periodInfo['period'] === 'quarter'
                                                ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year']
                                                : ($periodInfo['period'] === 'year'
                                                    ? $periodInfo['year'] . ' йил'
                                                    : '')) }}</span>
                                    </p>
                                </div>
                            @endif
                        </a>

                        <!-- Card 4: Қолдиқ маблағ - CLICKABLE -->
                        @php
                            $qoldiqMablagh = $nazoratdagilar['tushadigan_mablagh'] - $nazoratdagilar['tushgan_summa'];
                            $qoldiqFoizi =
                                $nazoratdagilar['tushadigan_mablagh'] > 0
                                    ? (($nazoratdagilar['tushadigan_mablagh'] - $nazoratdagilar['tushgan_summa']) /
                                            $nazoratdagilar['tushadigan_mablagh']) *
                                        100
                                    : 0;
                        @endphp
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли', 'nazoratda' => 'true'], $dateFilters)) }}"
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

                            @if ($periodInfo['period'] !== 'all')
                                <div class="mt-auto pt-3 border-t border-slate-200" style="display: none">
                                    <p class="text-xs text-blue-600 font-medium flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span>{{ $periodInfo['period'] === 'month'
                                            ? ($monthNames[$periodInfo['month']] ?? '') . ' ' . $periodInfo['year']
                                            : ($periodInfo['period'] === 'quarter'
                                                ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year']
                                                : ($periodInfo['period'] === 'year'
                                                    ? $periodInfo['year'] . ' йил'
                                                    : '')) }}</span>
                                    </p>
                                </div>
                            @endif
                        </a>

                        <!-- Card 5: График б-ча тушадиган маблағ - CLICKABLE -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли'], $dateFilters)) }}"
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

                            @if ($periodInfo['period'] !== 'all')
                                <div class="mt-auto pt-3 border-t border-slate-200" style="display: none">
                                    <p class="text-xs text-blue-600 font-medium flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span>{{ $periodInfo['period'] === 'month'
                                            ? ($monthNames[$periodInfo['month']] ?? '') . ' ' . $periodInfo['year']
                                            : ($periodInfo['period'] === 'quarter'
                                                ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year']
                                                : ($periodInfo['period'] === 'year'
                                                    ? $periodInfo['year'] . ' йил'
                                                    : '')) }}</span>
                                    </p>
                                </div>
                            @endif
                        </a>


                        <!-- Card 6: График бўйича тушган - CLICKABLE -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли'], $dateFilters)) }}"
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

                            @if ($periodInfo['period'] !== 'all')
                                <div class="mt-auto pt-3 border-t border-slate-200" style="display: none">
                                    <p class="text-xs text-blue-600 font-medium flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span>{{ $periodInfo['period'] === 'month'
                                            ? \Carbon\Carbon::create($periodInfo['year'], $periodInfo['month'], 1)->locale('uz')->translatedFormat('F Y')
                                            : ($periodInfo['period'] === 'quarter'
                                                ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year']
                                                : ($periodInfo['period'] === 'year'
                                                    ? $periodInfo['year'] . ' йил'
                                                    : '')) }}</span>
                                    </p>
                                </div>
                            @endif
                        </a>

                        <!-- Card 7: Муддати ўтган қарздорлик - CLICKABLE -->
                        <a href="{{ route('yer-sotuvlar.list', array_merge(['tolov_turi' => 'муддатли', 'grafik_ortda' => 'true'], $dateFilters)) }}"
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

                            @if ($periodInfo['period'] !== 'all')
                                <div class="mt-auto pt-3 border-t border-slate-200" style="display: none">
                                    <p class="text-xs text-blue-600 font-medium flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span>{{ $periodInfo['period'] === 'month'
                                            ? \Carbon\Carbon::create($periodInfo['year'], $periodInfo['month'], 1)->locale('uz')->translatedFormat('F Y')
                                            : ($periodInfo['period'] === 'quarter'
                                                ? $periodInfo['quarter'] . '-чорак ' . $periodInfo['year']
                                                : ($periodInfo['period'] === 'year'
                                                    ? $periodInfo['year'] . ' йил'
                                                    : '')) }}</span>
                                    </p>
                                </div>
                            @endif
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
                    <!-- Statistics Cards - Муддатли эмас (5 cards) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- 1. Soni -->
                        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-xl transition-shadow"
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
                        </div>

                        <!-- 2. Tushadigan mablag' -->
                        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-xl transition-shadow"
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
                        </div>

                        {{-- <!-- 3. Grafik = Expected (no schedule) -->
                        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-xl transition-shadow"
                            style="border-color: rgb(29 78 216);">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-slate-700" style="font-size: 22px">Графикда тушадиган</h3>
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
                                {{ number_format($summaryMuddatliEmas['expected_amount'] / 1000000000, 2) }} млрд сўм</p>
                            <p class="text-xs text-slate-500">Бир йўла тўлов (график йўқ)</p>
                        </div> --}}

                        <!-- 4. Amalda to'langan -->
                        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-xl transition-shadow"
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
                        </div>

                        <!-- 5. Muddati o'tgan -->

                        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 hover:shadow-xl transition-shadow"
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
                                {{ number_format($muddatiOtganMuddatliEmas / 1000000000, 2) }} млрд сўм</p>
                            <p class="text-1xl text-slate-500">мулкни қабул қилиш тасдиқланмаганлар</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js">
        </script>

        <script>
            // Tab switching functions
            function switchTab(tabName) {
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.style.display = 'none';
                });
                document.getElementById('content-' + tabName).style.display = 'block';

                document.querySelectorAll('.tab-button').forEach(button => {
                    button.style.background = 'white';
                    button.style.color = 'rgb(71, 85, 105)';
                });

                const activeTab = document.getElementById('tab-' + tabName);
                if (tabName === 'muddatli') {
                    activeTab.style.background = 'linear-gradient(to right, rgb(37, 99, 235), rgb(29, 78, 216))';
                } else {
                    activeTab.style.background = 'linear-gradient(to right, rgb(34, 197, 94), rgb(22, 163, 74))';
                }
                activeTab.style.color = 'white';
            }

            // Register datalabels plugin globally
            Chart.register(ChartDataLabels);
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
                            {{ $chartData['status']['completed'] }},
                            {{ $chartData['status']['under_control'] }},
                            {{ $chartData['status']['overdue'] }},
                            {{ $chartData['status']['auction'] }}
                        ],
                        backgroundColor: ['rgb(29, 78, 216)', 'rgb(29, 78, 216)', 'rgb(185, 28, 28)',
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
                        datalabels: {
                            display: true,
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 14
                            },
                            formatter: (value) => value
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 11,
                                    weight: 'bold'
                                },
                                color: '#000',
                                padding: 12,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + context.parsed + ' дона (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });

            // Monthly Comparison Chart - Муддатли
            const monthlyCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData['monthly_muddatli']['labels']) !!},
                    datasets: [{
                        label: 'График',
                        data: {!! json_encode($chartData['monthly_muddatli']['grafik']) !!},
                        borderColor: 'rgb(185, 28, 28)',
                        backgroundColor: 'rgba(185, 28, 28, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3
                    }, {
                        label: 'Факт',
                        data: {!! json_encode($chartData['monthly_muddatli']['fakt']) !!},
                        borderColor: 'rgb(29, 78, 216)',
                        backgroundColor: 'rgba(29, 78, 216, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            display: true,
                            align: 'top',
                            color: (context) => context.datasetIndex === 0 ? 'rgb(185, 28, 28)' : 'rgb(29, 78, 216)',
                            font: {
                                weight: 'bold',
                                size: 10
                            },
                            formatter: (value) => value.toFixed(2)
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => value.toFixed(1) + ' млрд'
                            }
                        }
                    }
                }
            });

            // Tuman Comparison Chart - Муддатли
            const tumanCtx = document.getElementById('tumanComparisonChart').getContext('2d');
            new Chart(tumanCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartData['tuman_muddatli']['labels']) !!},
                    datasets: [{
                        label: 'График',
                        data: {!! json_encode($chartData['tuman_muddatli']['grafik']) !!},
                        backgroundColor: 'rgba(185, 28, 28, 0.8)',
                        borderColor: 'rgb(185, 28, 28)',
                        borderWidth: 2,
                        borderRadius: 6
                    }, {
                        label: 'Факт',
                        data: {!! json_encode($chartData['tuman_muddatli']['fakt']) !!},
                        backgroundColor: 'rgba(29, 78, 216, 0.8)',
                        borderColor: 'rgb(29, 78, 216)',
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            display: true,
                            align: 'end',
                            anchor: 'end',
                            color: (context) => context.datasetIndex === 0 ? 'rgb(185, 28, 28)' : 'rgb(29, 78, 216)',
                            font: {
                                weight: 'bold',
                                size: 9
                            },
                            formatter: (value) => value.toFixed(2)
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => value.toFixed(1) + ' млрд'
                            }
                        }
                    }
                }
            });

            // Monthly Chart - Муддатли эмас
            const monthlyMuddatliEmasCtx = document.getElementById('monthlyMuddatliEmasChart').getContext('2d');
            new Chart(monthlyMuddatliEmasCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData['monthly_muddatli_emas']['labels']) !!},
                    datasets: [{
                        label: 'Тушган маблағ',
                        data: {!! json_encode($chartData['monthly_muddatli_emas']['received']) !!},
                        borderColor: 'rgb(29, 78, 216)',
                        backgroundColor: 'rgba(29, 78, 216, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            display: true,
                            align: 'top',
                            color: 'rgb(29, 78, 216)',
                            font: {
                                weight: 'bold',
                                size: 10
                            },
                            formatter: (value) => value.toFixed(2)
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => value.toFixed(1) + ' млрд'
                            }
                        }
                    }
                }
            });

            // Tuman Chart - Муддатли эмас
            const tumanMuddatliEmasCtx = document.getElementById('tumanMuddatliEmasChart').getContext('2d');
            new Chart(tumanMuddatliEmasCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartData['tuman_muddatli_emas']['labels']) !!},
                    datasets: [{
                        label: 'Тушадиган',
                        data: {!! json_encode($chartData['tuman_muddatli_emas']['expected']) !!},
                        backgroundColor: 'rgba(185, 28, 28, 0.8)',
                        borderColor: 'rgb(185, 28, 28)',
                        borderWidth: 2,
                        borderRadius: 6
                    }, {
                        label: 'Тушган',
                        data: {!! json_encode($chartData['tuman_muddatli_emas']['received']) !!},
                        backgroundColor: 'rgba(29, 78, 216, 0.8)',
                        borderColor: 'rgb(29, 78, 216)',
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            display: true,
                            align: 'end',
                            anchor: 'end',
                            color: (context) => context.datasetIndex === 0 ? 'rgb(185, 28, 28)' : 'rgb(29, 78, 216)',
                            font: {
                                weight: 'bold',
                                size: 9
                            },
                            formatter: (value) => value.toFixed(2)
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => value.toFixed(1) + ' млрд'
                            }
                        }
                    }
                }
            });
        </script>

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
