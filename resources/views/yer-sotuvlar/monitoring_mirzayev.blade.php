@extends('layouts.app')

@section('title', 'Ойлик динамика мониторинги')

@section('content')
<div class="min-h-screen bg-gray-50 py-6 px-4">
    <div class="max-w-[98%] mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm mb-6 border-l-4 border-blue-600">
            <div class="px-6 py-5 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-900 text-center">
                    Сотилган ерлардан {{ $comparativeData['meta']['selected_year'] }} йилда пул тушириш динамикаси
                </h1>
                <p class="text-gray-600 text-center mt-1 text-sm font-medium">ОЙЛИК МАЪЛУМОТ</p>
            </div>

            <!-- Filters -->
            <div class="p-5 bg-gray-50">
                <form method="GET" action="{{ route('yer-sotuvlar.monitoring_mirzayev') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Йил:</label>
                        <select name="year" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ $filters['year'] == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ой:</label>
                        <select name="month" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            @foreach($months as $monthNum => $monthName)
                                <option value="{{ $monthNum }}" {{ $filters['month'] == $monthNum ? 'selected' : '' }}>
                                    {{ $monthName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Тўлов тури:</label>
                        <select name="tolov_turi" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            @foreach($tolovTuriOptions as $value => $label)
                                <option value="{{ $value }}" {{ $filters['tolov_turi'] == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded text-sm transition-colors shadow-sm">
                            Кўрсатиш
                        </button>
                    </div>

                    <div class="flex items-end">
                        <a href="{{ route('yer-sotuvlar.monitoring_mirzayev') }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded text-sm transition-colors text-center shadow-sm">
                            Тозалаш
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- UMUMIY JAMI (Combined Total) --}}
        @if($filters['tolov_turi'] == 'all')
            <div class="bg-white rounded-lg shadow-sm mb-6 border-t-4 border-blue-600">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-base font-bold text-gray-900 uppercase">Умумий жами (барча тўлов турлари)</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-3 gap-6">
                        <!-- Selected Month -->
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h3 class="text-sm font-bold text-gray-900 mb-3 pb-2 border-b border-gray-300">
                                {{ $comparativeData['meta']['selected_month_name'] }}
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">План:</span>
                                    <span class="font-semibold text-gray-900">{{ number_format($comparativeData['jami_umumiy']['selected_month']['plan'] / 1000000000, 2) }} млрд</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Факт:</span>
                                    <span class="font-semibold text-gray-900">{{ number_format($comparativeData['jami_umumiy']['selected_month']['fakt'] / 1000000000, 2) }} млрд</span>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-gray-300">
                                    <span class="text-gray-700 font-medium">Тўланганлик:</span>
                                    <span class="text-2xl font-bold text-gray-900">{{ $comparativeData['jami_umumiy']['selected_month']['percentage'] }}%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Year to Date -->
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h3 class="text-sm font-bold text-gray-900 mb-3 pb-2 border-b border-gray-300">
                                За {{ $comparativeData['meta']['selected_month'] }} мес.
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">План:</span>
                                    <span class="font-semibold text-gray-900">{{ number_format($comparativeData['jami_umumiy']['year_to_date']['plan'] / 1000000000, 2) }} млрд</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Факт:</span>
                                    <span class="font-semibold text-gray-900">{{ number_format($comparativeData['jami_umumiy']['year_to_date']['fakt'] / 1000000000, 2) }} млрд</span>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-gray-300">
                                    <span class="text-gray-700 font-medium">Тўланганлик:</span>
                                    <span class="text-2xl font-bold text-gray-900">{{ $comparativeData['jami_umumiy']['year_to_date']['percentage'] }}%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Full Year -->
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h3 class="text-sm font-bold text-gray-900 mb-3 pb-2 border-b border-gray-300">
                                {{ $comparativeData['meta']['selected_year'] }} йил
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">План:</span>
                                    <span class="font-semibold text-gray-900">{{ number_format($comparativeData['jami_umumiy']['full_year']['plan'] / 1000000000, 2) }} млрд</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Факт:</span>
                                    <span class="font-semibold text-gray-900">{{ number_format($comparativeData['jami_umumiy']['full_year']['fakt'] / 1000000000, 2) }} млрд</span>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-gray-300">
                                    <span class="text-gray-700 font-medium">Тўланганлик:</span>
                                    <span class="text-2xl font-bold text-gray-900">{{ $comparativeData['jami_umumiy']['full_year']['percentage'] }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- MUDDATLI (Installments) Table --}}
        @if($filters['tolov_turi'] == 'all' || $filters['tolov_turi'] == 'muddatli')
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-3 border-b border-gray-300 bg-gray-100">
                    <h2 class="text-base font-bold text-gray-900 uppercase">
                        Муддатли (бўлиб тўлаш) - График бўйича
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse" style="font-size: 11px;">
                        <thead>
                            <tr class="bg-gray-100">
                                <th rowspan="2" class="border border-gray-300 px-2 py-2.5 text-center font-bold text-gray-900" style="min-width: 40px;">№</th>
                                <th rowspan="2" class="border border-gray-300 px-2 py-2.5 text-center font-bold text-gray-900" style="min-width: 180px;">Ҳудудлар</th>

                                <th colspan="3" class="border border-gray-300 px-2 py-2 text-center font-bold text-gray-900">
                                    {{ $comparativeData['meta']['selected_month_name'] }}
                                </th>
                                <th colspan="3" class="border border-gray-300 px-2 py-2 text-center font-bold text-gray-900">
                                    За {{ $comparativeData['meta']['selected_month'] }} мес.
                                </th>
                                <th colspan="3" class="border border-gray-300 px-2 py-2 text-center font-bold text-gray-900">
                                    {{ $comparativeData['meta']['selected_year'] }}г.
                                </th>
                            </tr>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 80px;">План</th>
                                <th class="border border-gray-300 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 80px;">Факт</th>
                                <th class="border border-gray-300 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 60px;">%</th>
                                <th class="border border-gray-300 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 80px;">План</th>
                                <th class="border border-gray-300 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 80px;">Факт</th>
                                <th class="border border-gray-300 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 60px;">%</th>
                                <th class="border border-gray-300 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 80px;">План</th>
                                <th class="border border-gray-300 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 80px;">Факт</th>
                                <th class="border border-gray-300 px-2 py-2 text-center font-semibold text-gray-700" style="min-width: 60px;">%</th>
                            </tr>
                        </thead>

                        <tbody>
                            {{-- JAMI --}}
                            <tr class="bg-gray-200 font-bold">
                                <td colspan="2" class="border border-gray-300 px-3 py-2.5 text-left text-gray-900 uppercase">ЖАМИ:</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-right text-gray-900">{{ number_format($comparativeData['jami_muddatli']['selected_month']['plan'] / 1000000, 1) }}</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-right text-gray-900">{{ number_format($comparativeData['jami_muddatli']['selected_month']['fakt'] / 1000000, 1) }}</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-center text-gray-900">{{ $comparativeData['jami_muddatli']['selected_month']['percentage'] }}%</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-right text-gray-900">{{ number_format($comparativeData['jami_muddatli']['year_to_date']['plan'] / 1000000, 1) }}</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-right text-gray-900">{{ number_format($comparativeData['jami_muddatli']['year_to_date']['fakt'] / 1000000, 1) }}</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-center text-gray-900">{{ $comparativeData['jami_muddatli']['year_to_date']['percentage'] }}%</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-right text-gray-900">{{ number_format($comparativeData['jami_muddatli']['full_year']['plan'] / 1000000, 1) }}</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-right text-gray-900">{{ number_format($comparativeData['jami_muddatli']['full_year']['fakt'] / 1000000, 1) }}</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-center text-gray-900">{{ $comparativeData['jami_muddatli']['full_year']['percentage'] }}%</td>
                            </tr>

                            {{-- Tumanlar --}}
                            @foreach($comparativeData['tumanlar_muddatli'] as $index => $tuman)
                                <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50">
                                    <td class="border border-gray-300 px-2 py-2 text-center text-gray-700">{{ $index + 1 }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-gray-900">{{ $tuman['tuman'] }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right text-gray-700">{{ number_format($tuman['selected_month']['plan'] / 1000000, 1) }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right text-gray-900 font-medium">{{ number_format($tuman['selected_month']['fakt'] / 1000000, 1) }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-center text-gray-900 font-medium">{{ $tuman['selected_month']['percentage'] }}%</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right text-gray-700">{{ number_format($tuman['year_to_date']['plan'] / 1000000, 1) }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right text-gray-900 font-medium">{{ number_format($tuman['year_to_date']['fakt'] / 1000000, 1) }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-center text-gray-900 font-medium">{{ $tuman['year_to_date']['percentage'] }}%</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right text-gray-700">{{ number_format($tuman['full_year']['plan'] / 1000000, 1) }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right text-gray-900 font-medium">{{ number_format($tuman['full_year']['fakt'] / 1000000, 1) }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-center text-gray-900 font-medium">{{ $tuman['full_year']['percentage'] }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 bg-gray-50 border-t border-gray-300 text-xs text-gray-600">
                    <strong>Изоҳ:</strong> График бўйича тўловлар. План - график тўловлар, Факт - тўланган маблағлар. Барча маблағлар млн.сум бирлигида.
                </div>
            </div>
        @endif

        {{-- MUDDATLI EMAS (One-time) Table --}}
        @if($filters['tolov_turi'] == 'all' || $filters['tolov_turi'] == 'muddatli_emas')
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-3 border-b border-gray-300 bg-gray-100">
                    <h2 class="text-base font-bold text-gray-900 uppercase">
                        Муддатли эмас (бир йўла тўлаш) - График йўқ
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse" style="font-size: 11px;">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-2 py-2.5 text-center font-bold text-gray-900" style="min-width: 40px;">№</th>
                                <th class="border border-gray-300 px-2 py-2.5 text-center font-bold text-gray-900" style="min-width: 180px;">Ҳудудлар</th>
                                <th class="border border-gray-300 px-2 py-2 text-center font-bold text-gray-900" style="min-width: 120px;">{{ $comparativeData['meta']['selected_month_name'] }}<br><span class="text-xs font-normal">(Факт)</span></th>
                                <th class="border border-gray-300 px-2 py-2 text-center font-bold text-gray-900" style="min-width: 120px;">За {{ $comparativeData['meta']['selected_month'] }} мес.<br><span class="text-xs font-normal">(Факт)</span></th>
                                <th class="border border-gray-300 px-2 py-2 text-center font-bold text-gray-900" style="min-width: 120px;">{{ $comparativeData['meta']['selected_year'] }}г.<br><span class="text-xs font-normal">(Факт)</span></th>
                            </tr>
                        </thead>

                        <tbody>
                            {{-- JAMI --}}
                            <tr class="bg-gray-200 font-bold">
                                <td colspan="2" class="border border-gray-300 px-3 py-2.5 text-left text-gray-900 uppercase">ЖАМИ:</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-right text-gray-900">{{ number_format($comparativeData['jami_muddatli_emas']['selected_month']['fakt'] / 1000000, 1) }}</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-right text-gray-900">{{ number_format($comparativeData['jami_muddatli_emas']['year_to_date']['fakt'] / 1000000, 1) }}</td>
                                <td class="border border-gray-300 px-2 py-2.5 text-right text-gray-900">{{ number_format($comparativeData['jami_muddatli_emas']['full_year']['fakt'] / 1000000, 1) }}</td>
                            </tr>

                            {{-- Tumanlar --}}
                            @foreach($comparativeData['tumanlar_muddatli_emas'] as $index => $tuman)
                                <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50">
                                    <td class="border border-gray-300 px-2 py-2 text-center text-gray-700">{{ $index + 1 }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-gray-900">{{ $tuman['tuman'] }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right text-gray-900 font-medium">{{ number_format($tuman['selected_month']['fakt'] / 1000000, 1) }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right text-gray-900 font-medium">{{ number_format($tuman['year_to_date']['fakt'] / 1000000, 1) }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right text-gray-900 font-medium">{{ number_format($tuman['full_year']['fakt'] / 1000000, 1) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 bg-gray-50 border-t border-gray-300 text-xs text-gray-600">
                    <strong>Изоҳ:</strong> Бир йўла тўланган маблағлар. График йўқ, фақат факт тўловлар кўрсатилган. Барча маблағлар млн.сум бирлигида.
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
