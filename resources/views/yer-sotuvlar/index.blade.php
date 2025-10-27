@extends('layouts.app')

@section('title', 'Ер Участкалари То\'лов Мониторинг Тизими')

@section('content')
<div class="space-y-6">
    {{-- Main Statistics --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Умумий кўрсаткичлар</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                <p class="text-xs text-blue-600 font-medium">Жами участкалар</p>
                <p class="text-2xl font-bold text-blue-900 mt-1">{{ number_format($statistics['umumiy']->jami_soni) }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                <p class="text-xs text-green-600 font-medium">Майдони (га)</p>
                <p class="text-2xl font-bold text-green-900 mt-1">{{ number_format($statistics['umumiy']->jami_maydoni, 2) }}</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                <p class="text-xs text-purple-600 font-medium">Графикда (млрд)</p>
                <p class="text-2xl font-bold text-purple-900 mt-1">{{ number_format($statistics['umumiy']->grafik_jami / 1000000000, 2) }}</p>
            </div>
            <div class="bg-teal-50 rounded-lg p-4 border border-teal-100">
                <p class="text-xs text-teal-600 font-medium">Тўланган (млрд)</p>
                <p class="text-2xl font-bold text-teal-900 mt-1">{{ number_format($statistics['umumiy']->fakt_jami / 1000000000, 2) }}</p>
            </div>
            <div class="bg-orange-50 rounded-lg p-4 border border-orange-100">
                <p class="text-xs text-orange-600 font-medium">То'лов фоизи</p>
                <p class="text-2xl font-bold text-orange-900 mt-1">{{ $statistics['umumiy']->tolov_foizi }}%</p>
            </div>
        </div>

        {{-- Payment Progress Bar --}}
        <div class="mt-4">
            <div class="flex justify-between text-sm text-gray-600 mb-1">
                <span>То'лов ҳолати</span>
                <span>Қарздорлик: {{ number_format($statistics['umumiy']->qarzdorlik / 1000000000, 2) }} млрд</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-teal-600 h-3 rounded-full transition-all" style="width: {{ min($statistics['umumiy']->tolov_foizi, 100) }}%"></div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('yer-sotuvlar.index') }}" class="space-y-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Филтрлаш</h3>
                <a href="{{ route('yer-sotuvlar.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Тозалаш</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Қидириш</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Лот, туман, ғолиб..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Туман</label>
                    <select name="tuman" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Барчаси</option>
                        @foreach($tumanlar as $tuman)
                            <option value="{{ $tuman }}" {{ request('tuman') == $tuman ? 'selected' : '' }}>{{ $tuman }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Йил</label>
                    <select name="yil" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Барчаси</option>
                        @foreach($yillar as $yil)
                            <option value="{{ $yil }}" {{ request('yil') == $yil ? 'selected' : '' }}>{{ $yil }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Қидириш
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- District Statistics --}}
    @if(count($statistics['tumanlar']) > 0 && !request('tuman'))
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Туманлар кесимида то'лов ҳолати</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Туман</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Сони</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">График (млн)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Тўланган (млн)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Қарздорлик (млн)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Фоиз</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($statistics['tumanlar'] as $tuman)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $tuman->tuman }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($tuman->soni) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($tuman->grafik / 1000000, 1) }}</td>
                        <td class="px-4 py-3 text-sm text-teal-700 text-right font-medium">{{ number_format($tuman->fakt / 1000000, 1) }}</td>
                        <td class="px-4 py-3 text-sm text-right {{ $tuman->qarzdorlik > 0 ? 'text-orange-700' : 'text-green-700' }}">
                            {{ number_format($tuman->qarzdorlik / 1000000, 1) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $tuman->foiz >= 80 ? 'bg-green-100 text-green-800' : ($tuman->foiz >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $tuman->foiz }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">
                Сотилган ер участкалари
                <span class="text-sm font-normal text-gray-500">({{ $yerlar->total() }} та)</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">№</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Лот</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Туман</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ғолиб</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Майдон (га)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">График (млн)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Тўланган (млн)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Фоиз</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Амал</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($yerlar as $index => $yer)
                    @php
                        $grafik = $yer->grafikTolovlar->sum('grafik_summa');
                        $fakt = $yer->faktTolovlar->sum('tolov_summa');
                        $foiz = $grafik > 0 ? round(($fakt / $grafik) * 100, 1) : 0;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $yerlar->firstItem() + $index }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-blue-600">
                            <a href="{{ route('yer-sotuvlar.show', $yer->lot_raqami) }}" class="hover:underline">
                                {{ $yer->lot_raqami }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $yer->tuman }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ Str::limit($yer->golib_nomi, 25) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($yer->maydoni, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($grafik / 1000000, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right">
                            <span class="font-medium {{ $fakt > 0 ? 'text-teal-700' : 'text-gray-400' }}">
                                {{ number_format($fakt / 1000000, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $foiz >= 80 ? 'bg-green-100 text-green-800' : ($foiz >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $foiz }}%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('yer-sotuvlar.show', $yer->lot_raqami) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                Батафсил
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">
                            Маълумот топилмади
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($yerlar->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-600">
                    {{ $yerlar->firstItem() }}-{{ $yerlar->lastItem() }} / {{ $yerlar->total() }} та
                </div>

                <div class="flex items-center space-x-2">
                    @if($yerlar->onFirstPage())
                        <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">Олдинги</span>
                    @else
                        <a href="{{ $yerlar->previousPageUrl() }}" class="px-3 py-1 text-sm text-blue-600 bg-white border border-gray-300 rounded hover:bg-gray-50">Олдинги</a>
                    @endif

                    <div class="flex space-x-1">
                        @for($i = 1; $i <= min(5, $yerlar->lastPage()); $i++)
                            @if($i == $yerlar->currentPage())
                                <span class="px-3 py-1 text-sm text-white bg-blue-600 rounded">{{ $i }}</span>
                            @else
                                <a href="{{ $yerlar->url($i) }}" class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $i }}</a>
                            @endif
                        @endfor

                        @if($yerlar->lastPage() > 5)
                            <span class="px-3 py-1 text-sm text-gray-500">...</span>
                            <a href="{{ $yerlar->url($yerlar->lastPage()) }}" class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $yerlar->lastPage() }}</a>
                        @endif
                    </div>

                    @if($yerlar->hasMorePages())
                        <a href="{{ $yerlar->nextPageUrl() }}" class="px-3 py-1 text-sm text-blue-600 bg-white border border-gray-300 rounded hover:bg-gray-50">Кейинги</a>
                    @else
                        <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">Кейинги</span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
