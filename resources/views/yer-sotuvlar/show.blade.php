@extends('layouts.app')

@section('title', 'Лот ' . $yer->lot_raqami . ' - Батафсил')

@section('content')
<div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto space-y-6">
        
        {{-- Back Button --}}
        <div>
            <a href="{{ route('yer-sotuvlar.index') }}" 
               class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Орқага қайтиш
            </a>
        </div>

        {{-- Main Header --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-5 rounded-t-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-white">Лот № {{ $yer->lot_raqami }}</h1>
                        <p class="text-gray-300 mt-1">{{ $yer->tuman }} туман, {{ $yer->mfy }}</p>
                    </div>
                    <div class="text-right bg-white/10 rounded-lg px-4 py-3">
                        <p class="text-xs text-gray-300 uppercase tracking-wide">Аукцион санаси</p>
                        <p class="text-lg font-semibold text-white mt-1">
                            {{ $yer->auksion_sana ? $yer->auksion_sana->format('d.m.Y') : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Basic Info Grid --}}
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="border-l-4 border-gray-600 pl-4 bg-gray-50 py-3 rounded-r">
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Уникал рақам</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $yer->unikal_raqam }}</p>
                    </div>
                    <div class="border-l-4 border-gray-600 pl-4 bg-gray-50 py-3 rounded-r">
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Майдони</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ number_format($yer->maydoni, 4) }} га</p>
                    </div>
                    <div class="border-l-4 border-gray-600 pl-4 bg-gray-50 py-3 rounded-r">
                        <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Зона</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $yer->zona }}</p>
                    </div>
                </div>

                {{-- Winner Info --}}
                <div class="pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Ғолиб маълумотлари</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-xs text-gray-600 font-medium uppercase">Номи</p>
                            <p class="text-base font-semibold text-gray-900 mt-1">{{ $yer->golib_nomi }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $yer->golib_turi }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-medium uppercase">Телефон</p>
                            <p class="text-base font-semibold text-gray-900 mt-1">{{ $yer->telefon ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-medium uppercase">Тўлов тури</p>
                            <p class="text-base font-semibold text-gray-900 mt-1">
                                {{ $yer->tolov_turi ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Financial Info --}}
                <div class="pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Молиявий маълумотлар</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-xs text-gray-600 font-medium uppercase">Бошланғич нарх</p>
                            <p class="text-xl font-bold text-gray-900 mt-2">
                                {{ number_format($yer->boshlangich_narx, 0, ',', ' ') }}
                            </p>
                            <p class="text-xs text-gray-600 mt-1">сўм</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-xs text-gray-600 font-medium uppercase">Сотилган нарх</p>
                            <p class="text-xl font-bold text-gray-900 mt-2">
                                {{ number_format($yer->sotilgan_narx, 0, ',', ' ') }}
                            </p>
                            <p class="text-xs text-gray-600 mt-1">сўм</p>
                        </div>
                        <div class="bg-gray-700 rounded-lg p-4 border border-gray-600">
                            <p class="text-xs text-gray-300 font-medium uppercase">Шартнома суммаси</p>
                            <p class="text-xl font-bold text-white mt-2">
                                {{ number_format($yer->shartnoma_summasi, 0, ',', ' ') }}
                            </p>
                            <p class="text-xs text-gray-300 mt-1">сўм</p>
                        </div>
                    </div>
                </div>

                {{-- Contract Info --}}
                @if($yer->shartnoma_sana)
                <div class="pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Шартнома маълумотлари</h3>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-base font-semibold text-gray-900">
                            № {{ $yer->shartnoma_raqam }} / {{ $yer->shartnoma_sana->format('d.m.Y') }}
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Payment Summary --}}
        @php
            $grafikJami = $yer->grafikTolovlar->sum('grafik_summa');
            $faktJami = $yer->faktTolovlar->sum('tolov_summa');
            $qarzdorlik = $grafikJami - $faktJami;
            $foiz = $grafikJami > 0 ? round(($faktJami / $grafikJami) * 100, 1) : 0;
        @endphp

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Тўлов ҳолати
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-5 border-2 border-gray-200">
                    <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Графикда</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">
                        {{ number_format($grafikJami / 1000000, 1) }}
                    </p>
                    <p class="text-xs text-gray-600 mt-1">млн сўм</p>
                </div>
                <div class="bg-gray-700 rounded-lg p-5 border-2 border-gray-600">
                    <p class="text-xs text-gray-300 font-semibold uppercase tracking-wide">Тўланган</p>
                    <p class="text-2xl font-bold text-white mt-2">
                        {{ number_format($faktJami / 1000000, 1) }}
                    </p>
                    <p class="text-xs text-gray-300 mt-1">млн сўм</p>
                </div>
                <div class="bg-gray-100 rounded-lg p-5 border-2 border-gray-300">
                    <p class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Қарздорлик</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">
                        {{ number_format($qarzdorlik / 1000000, 1) }}
                    </p>
                    <p class="text-xs text-gray-600 mt-1">млн сўм</p>
                </div>
                <div class="bg-gradient-to-br from-gray-600 to-gray-700 rounded-lg p-5 border-2 border-gray-500">
                    <p class="text-xs text-gray-200 font-semibold uppercase tracking-wide">Тўлов фоизи</p>
                    <p class="text-2xl font-bold text-white mt-2">{{ $foiz }}%</p>
                    <p class="text-xs text-gray-200 mt-1">бажарилди</p>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="bg-gray-100 rounded-lg p-4 border border-gray-200">
                <div class="flex justify-between text-sm text-gray-700 font-medium mb-2">
                    <span>График бажарилиши</span>
                    <span>{{ number_format($faktJami, 0, ',', ' ') }} / {{ number_format($grafikJami, 0, ',', ' ') }} сўм</span>
                </div>
                <div class="w-full bg-gray-300 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full transition-all duration-500 {{ $foiz >= 80 ? 'bg-gray-700' : ($foiz >= 50 ? 'bg-gray-500' : 'bg-gray-400') }}"
                         style="width: {{ min($foiz, 100) }}%">
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-2 text-right">
                    @if($foiz >= 100)
                        График тўлиқ бажарилган
                    @elseif($foiz >= 80)
                        График деярли бажарилган
                    @else
                        График бажарилиши паст
                    @endif
                </p>
            </div>
        </div>

        {{-- Monthly Comparison Table --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-5 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Ойлик тўлов таққослаш
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                Давр
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                График
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                Факт
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                Фарқ
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                Фоиз
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                Ҳолат
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tolovTaqqoslash as $tolov)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ ucfirst($tolov['oy_nomi']) }} {{ $tolov['yil'] }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right font-medium">
                                {{ number_format($tolov['grafik'] / 1000000, 2) }} млн
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <span class="font-semibold {{ $tolov['fakt'] > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                                    {{ number_format($tolov['fakt'] / 1000000, 2) }} млн
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <span class="font-semibold {{ $tolov['farq'] <= 0 ? 'text-gray-700' : 'text-gray-500' }}">
                                    {{ $tolov['farq'] >= 0 ? '+' : '' }}{{ number_format($tolov['farq'] / 1000000, 2) }} млн
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold {{ $tolov['foiz'] >= 100 ? 'bg-gray-700 text-white' : ($tolov['foiz'] >= 80 ? 'bg-gray-400 text-white' : 'bg-gray-200 text-gray-800') }}">
                                    {{ $tolov['foiz'] }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($tolov['foiz'] >= 100)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-700 text-white">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                @elseif($tolov['foiz'] >= 50)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-400 text-white">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-300 text-gray-700">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">График маълумотлари топилмади</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($tolovTaqqoslash) > 0)
                    <tfoot class="bg-gray-100 font-semibold">
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 uppercase">ЖАМИ</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                {{ number_format(collect($tolovTaqqoslash)->sum('grafik') / 1000000, 2) }} млн
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                {{ number_format(collect($tolovTaqqoslash)->sum('fakt') / 1000000, 2) }} млн
                            </td>
                            <td class="px-6 py-4 text-sm text-right {{ collect($tolovTaqqoslash)->sum('farq') <= 0 ? 'text-gray-700' : 'text-gray-500' }}">
                                {{ number_format(collect($tolovTaqqoslash)->sum('farq') / 1000000, 2) }} млн
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                {{ $foiz }}%
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Payment History --}}
        @if($yer->faktTolovlar->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-5 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Тўлов тарихи
                    <span class="ml-2 px-2 py-1 text-xs font-semibold bg-gray-700 text-white rounded-full">
                        {{ $yer->faktTolovlar->count() }}
                    </span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                Сана
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                Ҳужжат №
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                Тўловчи
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                Сумма
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($yer->faktTolovlar as $tolov)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium whitespace-nowrap">
                                {{ $tolov->tolov_sana->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $tolov->hujjat_raqam }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="max-w-xs">
                                    <p class="font-medium truncate">{{ $tolov->tolash_nom }}</p>
                                    @if($tolov->tolash_inn)
                                    <p class="text-xs text-gray-500 mt-1">ИНН: {{ $tolov->tolash_inn }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right font-semibold whitespace-nowrap">
                                {{ number_format($tolov->tolov_summa, 0, ',', ' ') }} сўм
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-sm font-semibold text-gray-900 uppercase">
                                ЖАМИ ТЎЛАНГАН
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                {{ number_format($faktJami, 0, ',', ' ') }} сўм
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection