@extends('layouts.app')

@section('title', 'Йиғма маълумот')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-6 px-4">
    <div class="max-w-[98%] mx-auto">
        <!-- Premium Government Header -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden mb-6 border-t-4 border-blue-600">
            <div class="bg-white px-8 py-6">
                <div class="flex items-center justify-center space-x-4">
                    <!-- Government Emblem -->

                    <div class="text-center">
                        <h1 class="text-2xl md:text-3xl font-bold text-blue tracking-wide mb-1">
                            Тошкент шаҳрида аукцион савдоларида сотилган ер участкалари тўғрисида
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
                        <thead >

                            <!-- Row 1: Main headers -->
                            <tr style="background: #eff6ff !important;">
                                <th rowspan="3" class="sticky-col border border-slate-300 px-4 py-4 text-center align-middle font-bold text-slate-800" style="min-width: 60px;">
                                    Т/р
                                </th>
                                <th rowspan="3" class="sticky-col-2 border border-slate-300 px-4 py-4 text-center align-middle font-bold text-slate-800" style="min-width: 200px;">
                                    Ҳудудлар
                                </th>
                                <th colspan="6" class="border border-slate-300 px-4 py-3 text-center font-bold text-slate-800">
                                    Сотилган ер участкалар
                                </th>
                                <th colspan="12" class="border border-slate-300 px-4 py-3 text-center font-bold text-slate-800">
                                    шундан
                                </th>
                                <th colspan="4" class="border border-slate-300 px-4 py-3 text-center font-bold text-slate-800">
                                    Аукционда сотилган ва савдо натижасини расмийлаштришда турган ерлар
                                </th>
                                <th colspan="2" class="border border-slate-300 px-4 py-3 text-center font-bold text-slate-800">
                                    Мулкни қабул қилиб олиш тугмаси босилмаган ерлар
                                </th>
                            </tr>

                            <!-- Row 2: Sub headers -->
                            <tr style="background:#eff6ff !important;">
                                <th rowspan="2" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 80px;">
                                    сони
                                </th>
                                <th rowspan="2" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 100px;">
                                    майдони<br>(га)
                                </th>
                                <th rowspan="2" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 120px;">
                                    бошланғич<br>нархи<br>(млрд сўм)
                                </th>
                                <th rowspan="2" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 120px;">
                                    сотилган<br>нархи<br>(млрд сўм)
                                </th>
                                <th colspan="2" class="border border-slate-300 px-3 py-3 text-center font-semibold text-slate-700 text-sm">
                                    шундан
                                </th>
                                <th colspan="6" class="border border-slate-300 px-3 py-3 text-center font-semibold text-slate-700 text-sm">
                                    Бир йўла тўлаш шарти билан сотилган
                                </th>
                                <th colspan="6" class="border border-slate-300 px-3 py-3 text-center font-semibold text-slate-700 text-sm">
                                    Нархини бўлиб тўлаш шарти билан сотилган
                                </th>
                                <th rowspan="2" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 80px;">
                                    сони
                                </th>
                                <th rowspan="2" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 100px;">
                                    майдони<br>(га)
                                </th>
                                <th rowspan="2" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 120px;">
                                    бошланғич<br>нархи<br>(млрд сўм)
                                </th>
                                <th rowspan="2" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 120px;">
                                    сотилган<br>нархи<br>(млрд сўм)
                                </th>
                                <th rowspan="2" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 80px;">
                                    сони
                                </th>
                                <th rowspan="2" class="border border-slate-300 px-3 py-3 text-center align-middle font-semibold text-slate-700 text-sm" style="min-width: 130px;">
                                    Аукционда<br>турган маблағ<br>(млрд сўм)
                                </th>
                            </tr>

                            <!-- Row 3: Detailed headers -->
                            <tr style="background:#eff6ff !important;">
                                <th class="border border-slate-300 px-3 py-3 text-center font-semibold text-slate-700 text-xs" style="min-width: 120px;">
                                    Чегирма<br>қиймати<br>(млрд сўм)
                                </th>
                                <th class="border border-slate-300 px-3 py-3 text-center font-semibold text-slate-700 text-xs" style="min-width: 140px;">
                                    Сотилган ер<br>тўлови бўйича<br>тушадиган қиймат<br>(млрд сўм)
                                </th>
                                <!-- Bir yo'la -->
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="min-width: 70px;">сони</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="min-width: 90px;">майдони<br>(га)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="min-width: 110px;">бошланғич<br>нархи<br>(млрд сўм)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="min-width: 110px;">сотилган<br>нархи<br>(млрд сўм)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="min-width: 110px;">Чегирма<br>қиймати<br>(млрд сўм)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="min-width: 130px;">Сотилган ер<br>тўлови бўйича<br>тушадиган қиймат<br>(млрд сўм)</th>
                                <!-- Bo'lib to'lash -->
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="min-width: 70px;">сони</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="min-width: 90px;">майдони<br>(га)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="min-width: 110px;">бошланғич<br>нархи<br>(млрд сўм)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="min-width: 110px;">сотилган<br>нархи<br>(млрд сўм)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="min-width: 110px;">Чегирма<br>қиймати<br>(млрд сўм)</th>
                                <th class="border border-slate-300 px-2 py-2 text-center font-semibold text-slate-700 text-xs" style="min-width: 130px;">Сотилган ер<br>тўлови бўйича<br>тушадиган қиймат<br>(млрд сўм)</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white">
                            <!-- Jami row -->
                            <tr class="bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100 border-y-2 border-amber-400">
                                <td colspan="2" class="sticky-col border border-slate-300 px-4 py-4 text-center align-middle font-bold text-slate-900 text-base uppercase bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100">
                                    ЖАМИ:
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    <a href="{{ route('yer-sotuvlar.list') }}" class="text-blue-700 hover:text-blue-900 hover:underline transition-all">
                                        {{ $statistics['jami']['jami']['soni'] }}
                                    </a>
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['jami']['maydoni'], 2) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['jami']['boshlangich_narx'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['jami']['sotilgan_narx'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['jami']['chegirma'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['jami']['tushadigan_mablagh'] / 1000000000, 1) }}</td>
                                <!-- Bir yo'la -->
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    <a href="{{ route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли эмас']) }}" class="text-blue-700 hover:text-blue-900 hover:underline transition-all">
                                        {{ $statistics['jami']['bir_yola']['soni'] }}
                                    </a>
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['bir_yola']['maydoni'], 2) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['bir_yola']['boshlangich_narx'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['bir_yola']['sotilgan_narx'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['bir_yola']['chegirma'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['bir_yola']['tushadigan_mablagh'] / 1000000000, 1) }}</td>
                                <!-- Bo'lib to'lash -->
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    <a href="{{ route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли']) }}" class="text-blue-700 hover:text-blue-900 hover:underline transition-all">
                                        {{ $statistics['jami']['bolib']['soni'] }}
                                    </a>
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['bolib']['maydoni'], 2) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['bolib']['boshlangich_narx'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['bolib']['sotilgan_narx'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['bolib']['chegirma'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['bolib']['tushadigan_mablagh'] / 1000000000, 1) }}</td>
                                <!-- Auksonda turgan -->
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    <a href="{{ route('yer-sotuvlar.list', ['auksonda_turgan' => 'true']) }}" class="text-blue-700 hover:text-blue-900 hover:underline transition-all">
                                        {{ $statistics['jami']['auksonda']['soni'] }}
                                    </a>
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['auksonda']['maydoni'], 2) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['auksonda']['boshlangich_narx'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['auksonda']['sotilgan_narx'] / 1000000000, 1) }}</td>
                                <!-- Mulk qabul qilmagan -->
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">
                                    <a href="{{ route('yer-sotuvlar.list', ['holat' => 'Ishtirokchi roziligini kutish jarayonida (34)']) }}" class="text-blue-700 hover:text-blue-900 hover:underline transition-all">
                                        {{ $statistics['jami']['mulk_qabul']['soni'] }}
                                    </a>
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-bold text-slate-900">{{ number_format($statistics['jami']['mulk_qabul']['auksion_mablagh'] / 1000000000, 1) }}</td>
                            </tr>

                            <!-- Tumanlar -->
                            @foreach($statistics['tumanlar'] as $index => $tuman)
                            <tr class="hover:bg-blue-50 transition-colors duration-150 {{ $index % 2 == 0 ? 'bg-white' : 'bg-slate-50' }}">
                                <td class="sticky-col border border-slate-300 px-3 py-3 text-center align-middle font-medium text-slate-700">{{ $index + 1 }}</td>
                                <td class="sticky-col-2 border border-slate-300 px-3 py-3 align-middle font-semibold text-slate-800">{{ $tuman['tuman'] }}</td>
                                <!-- Jami sotilgan -->
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    @if($tuman['jami']['soni'] > 0)
                                        <a href="{{ route('yer-sotuvlar.list', ['tuman' => $tuman['tuman']]) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition-all">
                                            {{ $tuman['jami']['soni'] }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['jami']['maydoni'], 2) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['jami']['boshlangich_narx'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['jami']['sotilgan_narx'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['jami']['chegirma'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['jami']['tushadigan_mablagh'] / 1000000000, 1) }}</td>
                                <!-- Bir yo'la -->
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    @if($tuman['bir_yola']['soni'] > 0)
                                        <a href="{{ route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли эмас']) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition-all">
                                            {{ $tuman['bir_yola']['soni'] }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['bir_yola']['maydoni'], 2) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['bir_yola']['boshlangich_narx'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['bir_yola']['sotilgan_narx'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['bir_yola']['chegirma'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['bir_yola']['tushadigan_mablagh'] / 1000000000, 1) }}</td>
                                <!-- Bo'lib to'lash -->
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    @if($tuman['bolib']['soni'] > 0)
                                        <a href="{{ route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли']) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition-all">
                                            {{ $tuman['bolib']['soni'] }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['bolib']['maydoni'], 2) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['bolib']['boshlangich_narx'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['bolib']['sotilgan_narx'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['bolib']['chegirma'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['bolib']['tushadigan_mablagh'] / 1000000000, 1) }}</td>
                                <!-- Auksonda turgan -->
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    @if($tuman['auksonda']['soni'] > 0)
                                        <a href="{{ route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'auksonda_turgan' => 'true']) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition-all">
                                            {{ $tuman['auksonda']['soni'] }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['auksonda']['maydoni'], 2) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['auksonda']['boshlangich_narx'] / 1000000000, 1) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['auksonda']['sotilgan_narx'] / 1000000000, 1) }}</td>
                                <!-- Mulk qabul qilmagan -->
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">
                                    @if($tuman['mulk_qabul']['soni'] > 0)
                                        <a href="{{ route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'holat' => 'Ishtirokchi roziligini kutish jarayonida (34)']) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium transition-all">
                                            {{ $tuman['mulk_qabul']['soni'] }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right text-slate-700">{{ number_format($tuman['mulk_qabul']['auksion_mablagh'] / 1000000000, 1) }}</td>
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
                <form method="GET" action="{{ route('yer-sotuvlar.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">


                        <!-- Date From -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Бошланғич санаси:</label>
                            <input type="date" name="auksion_sana_from" class="w-full px-4 py-3 border-2 border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" value="{{ request('auksion_sana_from') }}">
                        </div>

                        <!-- Date To -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Тугаш санаси:</label>
                            <input type="date" name="auksion_sana_to" class="w-full px-4 py-3 border-2 border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" value="{{ request('auksion_sana_to') }}">
                        </div>

  <!-- Action Buttons -->
                    <div class="flex gap-4 mt-6">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Қидириш
                        </button>
                        <a href="{{ route('yer-sotuvlar.index') }}" class="flex-1 bg-gradient-to-r from-slate-500 to-slate-600 hover:from-slate-600 hover:to-slate-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
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
