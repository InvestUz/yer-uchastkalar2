@extends('layouts.app')

@section('title', 'Бўлиб тўлаш маълумоти')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-6 px-4">
    <div class="max-w-[98%] mx-auto">
        <!-- Premium Government Header -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden mb-6 border-t-4 border-blue-600">
            <div class="bg-white px-8 py-6">
                <div class="flex items-center justify-center space-x-4">
                    <div class="text-center">
                        <h1 class="text-2xl md:text-3xl font-bold text-blue tracking-wide mb-1">
                            Тошкент шаҳрида аукцион савдоларида бўлиб тўлаш шарти билан сотилган ер участкалари тўғрисида
                        </h1>
                        <h2 class="text-xl md:text-2xl font-semibold text-blue">
                            ЙИҒМА МАЪЛУМОТ
                        </h2>
                    </div>
                </div>
            </div>

            <!-- Statistics Table -->
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse statistics-table">
                        <thead>
                            <!-- Row 1: Main section headers -->
                            <tr style="background:#eff6ff !important;">
                                <th rowspan="4" class="sticky-col border border-slate-300 px-4 py-4 text-center align-middle font-bold text-slate-800" style="min-width: 60px;">
                                    Т/р
                                </th>
                                <th rowspan="4" class="sticky-col-2 border border-slate-300 px-4 py-4 text-center align-middle font-bold text-slate-800" style="min-width: 200px;">
                                    Ҳудудлар
                                </th>
                                <th colspan="5" class="border border-slate-300 px-4 py-3 text-center font-bold text-slate-800">
                                    Нархини бўлиб тўлаш шарти билан сотилган
                                </th>
                                <th colspan="11" class="border border-slate-300 px-4 py-3 text-center font-bold text-slate-800">
                                    шундан, {{ now()->format('d.m.Y') }} ҳолатига
                                </th>
                                <th colspan="5" class="border border-slate-300 px-4 py-3 text-center font-bold text-slate-800">
                                    шундан, графикда ортда қолганлар
                                </th>
                            </tr>

                            <!-- Row 2: Sub-section headers -->
                            <tr style="background:#eff6ff !important;">
                                <!-- Narhini bo'lib to'lash - 5 columns -->
                                <th rowspan="3" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 80px;">
                                    сони
                                </th>
                                <th rowspan="3" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 100px;">
                                    майдони<br>(га)
                                </th>
                                <th rowspan="3" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 120px;">
                                    бошланғич<br>нархи<br>(млрд сўм)
                                </th>
                                <th rowspan="3" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 120px;">
                                    сотилган<br>нархи<br>(млрд сўм)
                                </th>
                                <th colspan="1" class="border border-slate-300 px-3 py-3 text-center font-semibold text-slate-700 text-sm">
                                    шундан
                                </th>

                                <!-- Shundan holatiga - 11 columns -->
                                <th colspan="5" class="border border-slate-300 px-3 py-3 text-center font-semibold text-slate-700 text-sm">
                                    тўлиқ тўланганлар
                                </th>
                                <th colspan="6" class="border border-slate-300 px-3 py-3 text-center font-semibold text-slate-700 text-sm">
                                    назоратдагилар
                                </th>

                                <!-- Grafik ortda - 5 columns -->
                                <th rowspan="3" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 80px;">
                                    сони
                                </th>
                                <th rowspan="3" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 100px;">
                                    майдони<br>(га)
                                </th>
                                <th colspan="3" class="border border-slate-300 px-3 py-3 text-center font-semibold text-slate-700 text-sm">
                                    шундан
                                </th>
                            </tr>

                            <!-- Row 3: More detailed sub-headers -->
                            <tr style="background:#eff6ff !important;">
                                <!-- Under "shundan" of Narhini bo'lib -->
                                <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="min-width: 130px;">
                                    Сотилган ер тўлови бўйича тушадиган қиймат<br>(млрд сўм)
                                </th>

                                <!-- Toliq tolanganlar - 5 columns -->
                                <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="min-width: 70px;">сони</th>
                                <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="min-width: 90px;">майдони<br>(га)</th>
                                <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="min-width: 110px;">бошланғич нархи<br>(млрд сўм)</th>
                                <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="min-width: 110px;">сотилган нархи<br>(млрд сўм)</th>
                                <th colspan="1" class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs">шундан</th>

                                <!-- Nazoratdagilar - 6 columns -->
                                <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="min-width: 70px;">сони</th>
                                <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="min-width: 90px;">майдони<br>(га)</th>
                                <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="min-width: 110px;">бошланғич нархи<br>(млрд сўм)</th>
                                <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="min-width: 110px;">сотилган нархи<br>(млрд сўм)</th>
                                <th colspan="2" class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs">шундан</th>

                                <!-- Under "shundan" of Grafik ortda -->
                                <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="min-width: 120px;">график б-ча тўлов суммаси<br>(млрд сўм)</th>
                                <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="min-width: 120px;">амалда тўлов суммаси<br>(млрд сўм)</th>
                                <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="min-width: 80px;">%</th>
                            </tr>

                            <!-- Row 4: Bottom level details -->
                            <tr style="background:#eff6ff !important;">
                                <!-- Under "shundan" of Toliq tolanganlar -->
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="min-width: 120px;">
                                    тушган қиймат<br>(млрд сўм)
                                </th>

                                <!-- Under "shundan" of Nazoratdagilar -->
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="min-width: 130px;">
                                    Сотилган ер тўлови бўйича тушадиган қиймат<br>(млрд сўм)
                                </th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="min-width: 120px;">
                                    тушган қиймат<br>(млрд сўм)
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white">
                            <!-- Jami row -->
                            <tr class="bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100 border-y-2 border-amber-400">
                                <td colspan="2" class="sticky-col border border-slate-300 px-4 py-4 text-center align-middle font-bold text-slate-900 text-base uppercase bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100">
                                    ЖАМИ:
                                </td>

                                <!-- Narhini bolib tolash - 5 columns -->
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    <a href="{{ route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли']) }}" class="text-blue-700 hover:text-blue-900 hover:underline transition-all">
                                        {{ $statistics['jami']['narhini_bolib']['soni'] }}
                                    </a>
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    {{ number_format($statistics['jami']['narhini_bolib']['maydoni'], 2) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    {{ number_format($statistics['jami']['narhini_bolib']['boshlangich_narx'] / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    {{ number_format($statistics['jami']['narhini_bolib']['sotilgan_narx'] / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    {{ number_format($statistics['jami']['narhini_bolib']['tushadigan_mablagh'] / 1000000000, 1) }}
                                </td>

                                <!-- Toliq tolanganlar - 5 columns -->
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    <a href="{{ route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли', 'toliq_tolangan' => 'true']) }}" class="text-blue-700 hover:text-blue-900 hover:underline transition-all">
                                        {{ $statistics['jami']['toliq_tolanganlar']['soni'] }}
                                    </a>
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    {{ number_format($statistics['jami']['toliq_tolanganlar']['maydoni'], 2) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    {{ number_format($statistics['jami']['toliq_tolanganlar']['boshlangich_narx'] / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    {{ number_format($statistics['jami']['toliq_tolanganlar']['sotilgan_narx'] / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    @php
                                        $tushganJamiToliq = $statistics['jami']['toliq_tolanganlar']['tushgan_summa'] ?? 0;
                                    @endphp
                                    {{ $tushganJamiToliq > 0 ? number_format($tushganJamiToliq / 1000000000, 1) : '0.0' }}
                                </td>

                                <!-- Nazoratdagilar - 6 columns -->
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    <a href="{{ route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли', 'nazoratda' => 'true']) }}" class="text-blue-700 hover:text-blue-900 hover:underline transition-all">
                                        {{ $statistics['jami']['nazoratdagilar']['soni'] }}
                                    </a>
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    {{ number_format($statistics['jami']['nazoratdagilar']['maydoni'], 2) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    {{ number_format($statistics['jami']['nazoratdagilar']['boshlangich_narx'] / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    {{ number_format($statistics['jami']['nazoratdagilar']['sotilgan_narx'] / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    {{ number_format($statistics['jami']['nazoratdagilar']['tushadigan_mablagh'] / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    @php
                                        $tushganJamiNazorat = $statistics['jami']['nazoratdagilar']['tushgan_summa'] ?? 0;
                                    @endphp
                                    {{ $tushganJamiNazorat > 0 ? number_format($tushganJamiNazorat / 1000000000, 1) : '0.0' }}
                                </td>

                                <!-- Grafik ortda - 5 columns -->
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    <a href="{{ route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли', 'grafik_ortda' => 'true']) }}" class="text-blue-700 hover:text-blue-900 hover:underline transition-all">
                                        {{ $statistics['jami']['grafik_ortda']['soni'] }}
                                    </a>
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    {{ number_format($statistics['jami']['grafik_ortda']['maydoni'], 2) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    {{ number_format($statistics['jami']['grafik_ortda']['grafik_summa'] / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    {{ number_format($statistics['jami']['grafik_ortda']['fakt_summa'] / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    {{ number_format($statistics['jami']['grafik_ortda']['foiz'], 1) }}
                                </td>
                            </tr>

                            <!-- Tumanlar -->
                            @foreach($statistics['tumanlar'] as $index => $tuman)
                            <tr class="hover:bg-blue-50 transition-colors duration-150 {{ $index % 2 == 0 ? 'bg-white' : 'bg-slate-50' }}">
                                <td class="sticky-col border border-slate-300 px-3 py-3 text-center align-middle font-medium text-slate-700">
                                    {{ $index + 1 }}
                                </td>
                                <td class="sticky-col-2 border border-slate-300 px-3 py-3 align-middle font-semibold text-slate-800">
                                    {{ $tuman['tuman'] }}
                                </td>

                                <!-- Narhini bolib tolash - 5 columns -->
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    @if($tuman['narhini_bolib']['soni'] > 0)
                                        <a href="{{ route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли']) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition-all">
                                            {{ $tuman['narhini_bolib']['soni'] }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    {{ number_format($tuman['narhini_bolib']['maydoni'], 2) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    {{ number_format($tuman['narhini_bolib']['boshlangich_narx'] / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    {{ number_format($tuman['narhini_bolib']['sotilgan_narx'] / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    {{ number_format($tuman['narhini_bolib']['tushadigan_mablagh'] / 1000000000, 1) }}
                                </td>

                                <!-- Toliq tolanganlar - 5 columns -->
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    @if(($tuman['toliq_tolanganlar']['soni'] ?? 0) > 0)
                                        <a href="{{ route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли', 'toliq_tolangan' => 'true']) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition-all">
                                            {{ $tuman['toliq_tolanganlar']['soni'] }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">{{ $tuman['toliq_tolanganlar']['soni'] ?? 0 }}</span>
                                    @endif
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    {{ number_format($tuman['toliq_tolanganlar']['maydoni'] ?? 0, 2) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    {{ number_format(($tuman['toliq_tolanganlar']['boshlangich_narx'] ?? 0) / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    {{ number_format(($tuman['toliq_tolanganlar']['sotilgan_narx'] ?? 0) / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    @php
                                        $tushganToliq = $tuman['toliq_tolanganlar']['tushgan_summa'] ?? 0;
                                    @endphp
                                    {{ $tushganToliq > 0 ? number_format($tushganToliq / 1000000000, 1) : '0.0' }}
                                </td>

                                <!-- Nazoratdagilar - 6 columns -->
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    @if(($tuman['nazoratdagilar']['soni'] ?? 0) > 0)
                                        <a href="{{ route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли', 'nazoratda' => 'true']) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition-all">
                                            {{ $tuman['nazoratdagilar']['soni'] }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">{{ $tuman['nazoratdagilar']['soni'] ?? 0 }}</span>
                                    @endif
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    {{ number_format($tuman['nazoratdagilar']['maydoni'] ?? 0, 2) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    {{ number_format(($tuman['nazoratdagilar']['boshlangich_narx'] ?? 0) / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    {{ number_format(($tuman['nazoratdagilar']['sotilgan_narx'] ?? 0) / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    {{ number_format(($tuman['nazoratdagilar']['tushadigan_mablagh'] ?? 0) / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    @php
                                        $tushganNazorat = $tuman['nazoratdagilar']['tushgan_summa'] ?? 0;
                                    @endphp
                                    {{ $tushganNazorat > 0 ? number_format($tushganNazorat / 1000000000, 1) : '0.0' }}
                                </td>

                                <!-- Grafik ortda - 5 columns -->
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    @if(($tuman['grafik_ortda']['soni'] ?? 0) > 0)
                                        <a href="{{ route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли', 'grafik_ortda' => 'true']) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition-all">
                                            {{ $tuman['grafik_ortda']['soni'] }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">{{ $tuman['grafik_ortda']['soni'] ?? 0 }}</span>
                                    @endif
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    {{ number_format($tuman['grafik_ortda']['maydoni'] ?? 0, 2) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    {{ number_format(($tuman['grafik_ortda']['grafik_summa'] ?? 0) / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    {{ number_format(($tuman['grafik_ortda']['fakt_summa'] ?? 0) / 1000000000, 1) }}
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    {{ number_format($tuman['grafik_ortda']['foiz'] ?? 0, 1) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Sticky columns for horizontal scroll */
    .sticky-col {
        position: sticky;
        left: 0;
        z-index: 20;
        background-color: inherit;
    }

    .sticky-col-2 {
        position: sticky;
        left: 60px;
        z-index: 20;
        background-color: inherit;
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
        .sticky-col, .sticky-col-2 {
            position: static;
        }

        body {
            background: white;
        }
    }
</style>
@endsection
