@extends('layouts.app')

@section('title', 'Йиғма маълумот')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-6 px-4">
    <div class="max-w-[98%] mx-auto">
        <!-- Premium Government Header -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden mb-6 border-t-4 border-blue-600">
            <div class="bg-white px-8 py-6">
                <div class="flex items-center justify-center space-x-4">
                    <div class="text-center">
                        <h1 class="text-2xl md:text-3xl font-bold text-blue tracking-wide mb-1">
                            Тошкент шаҳрида аукцион савдоларида сотилган ер участкаларининг тўловлари тўғрисида
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
                                <th rowspan="4" class="sticky-col border border-slate-300 px-2 py-3 text-center align-middle font-bold text-slate-800" style="width: 50px;">Т/р</th>
                                <th rowspan="4" class="sticky-col-2 border border-slate-300 px-3 py-3 text-center align-middle font-bold text-slate-800" style="width: 180px;">Ҳудудлар</th>
                                <th colspan="4" class="border border-slate-300 px-2 py-2 text-center font-bold text-slate-800 text-sm">Сотилган ер участкалари</th>
                                <th colspan="17" class="border border-slate-300 px-2 py-2 text-center font-bold text-slate-800 text-sm">шундан</th>
                            </tr>

                            <!-- Row 2: Sub-section headers -->
                            <tr style="background:#eff6ff !important;">
                                <!-- Sotilgan yer - 4 columns -->
                                <th rowspan="3" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="width: 70px;">Сони</th>
                                <th colspan="2" rowspan="2" class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs">шундан</th>
                                <th rowspan="3" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="width: 100px;">қолдиқ маблағ<br>(млрд сўм)</th>

                                <!-- Bir yola tolash - 4 columns -->
                                <th colspan="4" class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs">Бир йўла тўлаш шарти билан сотилган</th>

                                <!-- Bolib tolash - 10 columns -->
                                <th colspan="10" class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs">Нархини бўлиб тўлаш шарти билан сотилган</th>

                                <!-- Bekor qilinganlar - 3 columns -->
                                <th colspan="3" class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs">Бекор қилинганлар</th>
                            </tr>

                            <!-- Row 3: More detailed sub-headers -->
                            <tr style="background:#eff6ff !important;">
                                <!-- Bir yola - 4 columns -->
                                <th colspan="4" rowspan="1" class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs">шундан</th>

                                <!-- Bolib - 10 columns -->
                                <th colspan="4" rowspan="1" class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs">шундан</th>
                                <th colspan="6" rowspan="1" class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs">{{ now()->format('d.m.Y') }} йил ҳолатига</th>

                                <!-- Bekor - 3 columns -->
                                <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="width: 70px;">сони</th>
                                <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="width: 90px;">Тўланған маблағ<br>(млрд сўм)</th>
                                <th rowspan="2" class="border border-slate-300 px-2 py-2 text-center align-middle font-semibold text-slate-700 text-xs" style="width: 90px;">Қайтарилган маблағ<br>(млрд сўм)</th>
                            </tr>

                            <!-- Row 4: Bottom level details -->
                            <tr style="background:#eff6ff !important;">
                                <!-- Under "shundan" of Sotilgan yer -->
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 100px;">жами тушган маблағ<br>(млрд сўм)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 100px;">қолдиқ маблағ<br>(млрд сўм)</th>

                                <!-- Bir yola details -->
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 70px;">Сони</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 100px;">Сотилган ер нархи бўйича тушадиган маблағ<br>(млрд сўм)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 90px;">тушган маблағ<br>(млрд сўм)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 90px;">қолдиқ маблағ<br>(млрд сўм)</th>

                                <!-- Bolib details -->
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 70px;">Сони</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 100px;">Сотилган ер нархи бўйича тушадиган маблағ<br>(млрд сўм)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 90px;">тушган маблағ<br>(млрд сўм)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 90px;">қолдиқ маблағ<br>(млрд сўм)</th>

                                <!-- Holatiga details - 6 columns -->
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 70px;">фоизда</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 100px;">График б-ча тушадиган маблағ<br>(млрд сўм)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 100px;">Амалда график б-ча тушган маблағ<br>(млрд сўм)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 110px;">Муддати ўтган қарздорлик<br>(млрд сўм)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 100px;">тушган маблағ<br>(млрд сўм)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="width: 100px;">қолдиқ маблағ қарздорлик<br>(млрд сўм)</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white">
                            <!-- JAMI row -->
                            <tr class="bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100 border-y-2 border-amber-400">
                                <td colspan="2" class="sticky-col sticky-col-2 border border-slate-300 px-4 py-4 text-center align-middle font-bold text-slate-900 text-base uppercase bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100">
                                    ЖАМИ:
                                </td>

                                <!-- Sotilgan yer - 4 columns -->
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ $jami['jami_soni'] }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['jami_tushgan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['jami_qoldiq'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['jami_qoldiq'] / 1000000000, 2) }}</td>

                                <!-- BIR YOLA - 4 columns -->
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">
                                    <a href="{{ route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли эмас']) }}" class="text-blue-700 hover:text-blue-900 hover:underline">{{ $jami['biryola_soni'] }}</a>
                                </td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['biryola_tushadigan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['biryola_tushgan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['biryola_qoldiq'] / 1000000000, 2) }}</td>

                                <!-- BOLIB - 10 columns -->
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">
                                    <a href="{{ route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли']) }}" class="text-blue-700 hover:text-blue-900 hover:underline">{{ $jami['bolib_soni'] }}</a>
                                </td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['bolib_tushadigan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['bolib_tushgan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['bolib_qoldiq'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['grafik_foiz'], 1) }}%</td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['grafik_tushadigan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['grafik_tushgan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['muddati_utgan_qarz'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['bolib_tushgan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['bolib_qoldiq'] / 1000000000, 2) }}</td>

                                <!-- BEKOR - 3 columns -->
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ $jami['bekor_soni'] }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['tolangan_mablagh'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right font-bold text-slate-900">{{ number_format($jami['qaytarilgan_mablagh'] / 1000000000, 2) }}</td>
                            </tr>

                            <!-- Tuman Rows -->
                            @foreach($statistics as $index => $stat)
                            <tr class="hover:bg-blue-50 transition-colors duration-150 {{ $index % 2 == 0 ? 'bg-white' : 'bg-slate-50' }}">
                                <td class="sticky-col border border-slate-300 px-2 py-2 text-center align-middle font-medium text-slate-700 {{ $index % 2 == 0 ? 'bg-white' : 'bg-slate-50' }}">{{ $index + 1 }}</td>
                                <td class="sticky-col-2 border border-slate-300 px-3 py-2 align-middle font-semibold text-slate-800 {{ $index % 2 == 0 ? 'bg-white' : 'bg-slate-50' }}">{{ $stat['tuman'] }}</td>

                                <!-- Sotilgan yer - 4 columns -->
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ $stat['jami_soni'] }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ number_format($stat['jami_tushgan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ number_format($stat['jami_qoldiq'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ number_format($stat['jami_qoldiq'] / 1000000000, 2) }}</td>

                                <!-- BIR YOLA - 4 columns -->
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">
                                    @if($stat['biryola_soni'] > 0)
                                        <a href="{{ route('yer-sotuvlar.list', ['tuman' => $stat['tuman'], 'tolov_turi' => 'муддатли эмас']) }}" class="text-blue-600 hover:text-blue-800 hover:underline">{{ $stat['biryola_soni'] }}</a>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ number_format($stat['biryola_tushadigan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ number_format($stat['biryola_tushgan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ number_format($stat['biryola_qoldiq'] / 1000000000, 2) }}</td>

                                <!-- BOLIB - 10 columns -->
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">
                                    @if($stat['bolib_soni'] > 0)
                                        <a href="{{ route('yer-sotuvlar.list', ['tuman' => $stat['tuman'], 'tolov_turi' => 'муддатли']) }}" class="text-blue-600 hover:text-blue-800 hover:underline">{{ $stat['bolib_soni'] }}</a>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ number_format($stat['bolib_tushadigan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ number_format($stat['bolib_tushgan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ number_format($stat['bolib_qoldiq'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700 {{ $stat['grafik_foiz'] >= 90 ? 'bg-green-100' : ($stat['grafik_foiz'] >= 70 ? 'bg-yellow-100' : 'bg-red-100') }}">
                                    {{ number_format($stat['grafik_foiz'], 1) }}%
                                </td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ number_format($stat['grafik_tushadigan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ number_format($stat['grafik_tushgan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700 {{ $stat['muddati_utgan_qarz'] > 0 ? 'bg-red-50 text-red-700 font-semibold' : '' }}">
                                    {{ number_format($stat['muddati_utgan_qarz'] / 1000000000, 2) }}
                                </td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ number_format($stat['bolib_tushgan'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ number_format($stat['bolib_qoldiq'] / 1000000000, 2) }}</td>

                                <!-- BEKOR - 3 columns -->
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ $stat['bekor_soni'] }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ number_format($stat['tolangan_mablagh'] / 1000000000, 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2 text-right text-slate-700">{{ number_format($stat['qaytarilgan_mablagh'] / 1000000000, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Premium Filter Section -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-t-4 border-blue-600">
            <div class="p-6 bg-gradient-to-br from-slate-50 to-blue-50">
                <form method="GET" action="{{ route('yer-sotuvlar.yigma') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Бошланғич санаси:</label>
                            <input type="date" name="auksion_sana_from" class="w-full px-4 py-3 border-2 border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" value="{{ $dateFilters['auksion_sana_from'] ?? '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Тугаш санаси:</label>
                            <input type="date" name="auksion_sana_to" class="w-full px-4 py-3 border-2 border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" value="{{ $dateFilters['auksion_sana_to'] ?? '' }}">
                        </div>
                    </div>
                    <div class="flex gap-4 mt-6">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Қидириш
                        </button>
                        <a href="{{ route('yer-sotuvlar.yigma') }}" class="flex-1 bg-gradient-to-r from-slate-500 to-slate-600 hover:from-slate-600 hover:to-slate-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Тозалаш
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .sticky-col {
        position: sticky;
        left: 0;
        z-index: 20;
        background-color: inherit;
    }
    .sticky-col-2 {
        position: sticky;
        left: 50px;
        z-index: 20;
        background-color: inherit;
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
        .sticky-col, .sticky-col-2 {
            position: static;
        }
        body {
            background: white;
        }
    }
</style>
@endsection
