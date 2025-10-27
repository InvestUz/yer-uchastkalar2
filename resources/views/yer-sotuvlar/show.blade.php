@extends('layouts.app')

@section('title', 'Лот ' . $yer->lot_raqami . ' - Батафсил')

@section('content')
<div class="space-y-6">
    {{-- Back Button --}}
    <div>
        <a href="{{ route('yer-sotuvlar.index') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Орқага
        </a>
    </div>

    {{-- Main Info --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Лот № {{ $yer->lot_raqami }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $yer->tuman }} - {{ $yer->mfy }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">Аукцион санаси</p>
                <p class="text-base font-semibold text-gray-900">{{ $yer->auksion_sana ? $yer->auksion_sana->format('d.m.Y') : '-' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="border-l-4 border-blue-500 pl-4">
                <p class="text-sm text-gray-600">Уникал рақам</p>
                <p class="text-base font-semibold text-gray-900 mt-1">{{ $yer->unikal_raqam }}</p>
            </div>
            <div class="border-l-4 border-green-500 pl-4">
                <p class="text-sm text-gray-600">Майдони</p>
                <p class="text-base font-semibold text-gray-900 mt-1">{{ number_format($yer->maydoni, 4) }} га</p>
            </div>
            <div class="border-l-4 border-purple-500 pl-4">
                <p class="text-sm text-gray-600">Зона</p>
                <p class="text-base font-semibold text-gray-900 mt-1">{{ $yer->zona }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div>
                <p class="text-sm text-gray-600">Ғолиб</p>
                <p class="text-base font-semibold text-gray-900 mt-1">{{ $yer->golib_nomi }}</p>
                <p class="text-sm text-gray-500">{{ $yer->golib_turi }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Телефон</p>
                <p class="text-base font-semibold text-gray-900 mt-1">{{ $yer->telefon ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">То'лов тури</p>
                <p class="text-base font-semibold text-gray-900 mt-1">{{ $yer->tolov_turi ?? '-' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 pt-6 border-t">
            <div>
                <p class="text-sm text-gray-600">Бошланғич нарх</p>
                <p class="text-lg font-bold text-gray-900 mt-1">{{ number_format($yer->boshlangich_narx, 0, ',', ' ') }} сўм</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Сотилган нарх</p>
                <p class="text-lg font-bold text-teal-700 mt-1">{{ number_format($yer->sotilgan_narx, 0, ',', ' ') }} сўм</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Шартнома суммаси</p>
                <p class="text-lg font-bold text-blue-700 mt-1">{{ number_format($yer->shartnoma_summasi, 0, ',', ' ') }} сўм</p>
            </div>
        </div>

        @if($yer->shartnoma_sana)
        <div class="mt-6 pt-6 border-t">
            <p class="text-sm text-gray-600">Шартнома</p>
            <p class="text-base font-semibold text-gray-900 mt-1">
                № {{ $yer->shartnoma_raqam }} / {{ $yer->shartnoma_sana->format('d.m.Y') }}
            </p>
        </div>
        @endif
    </div>

    {{-- Payment Summary --}}
    @php
        $grafikJami = $yer->grafikTolovlar->sum('grafik_summa');
        $faktJami = $yer->faktTolovlar->sum('tolov_summa');
        $qarzdorlik = $grafikJami - $faktJami;
        $foiz = $grafikJami > 0 ? round(($faktJami / $grafikJami) * 100, 1) : 0;
    @endphp

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">То'лов ҳолати</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                <p class="text-xs text-blue-600 font-medium">Графикда</p>
                <p class="text-xl font-bold text-blue-900 mt-1">{{ number_format($grafikJami, 0, ',', ' ') }}</p>
                <p class="text-xs text-blue-600 mt-1">сўм</p>
            </div>
            <div class="bg-teal-50 rounded-lg p-4 border border-teal-100">
                <p class="text-xs text-teal-600 font-medium">Тўланган</p>
                <p class="text-xl font-bold text-teal-900 mt-1">{{ number_format($faktJami, 0, ',', ' ') }}</p>
                <p class="text-xs text-teal-600 mt-1">сўм</p>
            </div>
            <div class="bg-orange-50 rounded-lg p-4 border border-orange-100">
                <p class="text-xs text-orange-600 font-medium">Қарздорлик</p>
                <p class="text-xl font-bold text-orange-900 mt-1">{{ number_format($qarzdorlik, 0, ',', ' ') }}</p>
                <p class="text-xs text-orange-600 mt-1">сўм</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                <p class="text-xs text-purple-600 font-medium">То'лов фоизи</p>
                <p class="text-xl font-bold text-purple-900 mt-1">{{ $foiz }}%</p>
                <p class="text-xs text-purple-600 mt-1">бажарилди</p>
            </div>
        </div>

        <div class="mt-4">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>График бажарилиши</span>
                <span>{{ number_format($faktJami, 0, ',', ' ') }} / {{ number_format($grafikJami, 0, ',', ' ') }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="h-4 rounded-full transition-all {{ $foiz >= 80 ? 'bg-green-600' : ($foiz >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                     style="width: {{ min($foiz, 100) }}%"></div>
            </div>
        </div>
    </div>

    {{-- Monthly Comparison --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Ойлик то'лов таққослаш</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Давр</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">График (сўм)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Факт (сўм)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Фарқ (сўм)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Фоиз</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ҳолат</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tolovTaqqoslash as $tolov)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                            {{ ucfirst($tolov['oy_nomi']) }} {{ $tolov['yil'] }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">
                            {{ number_format($tolov['grafik'], 0, ',', ' ') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <span class="font-medium {{ $tolov['fakt'] > 0 ? 'text-teal-700' : 'text-gray-400' }}">
                                {{ number_format($tolov['fakt'], 0, ',', ' ') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <span class="font-medium {{ $tolov['farq'] <= 0 ? 'text-green-700' : 'text-red-700' }}">
                                {{ $tolov['farq'] >= 0 ? '+' : '' }}{{ number_format($tolov['farq'], 0, ',', ' ') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $tolov['foiz'] >= 100 ? 'bg-green-100 text-green-800' : ($tolov['foiz'] >= 80 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $tolov['foiz'] }}%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($tolov['foiz'] >= 100)
                                <span class="text-green-600">✓</span>
                            @elseif($tolov['foiz'] >= 80)
                                <span class="text-yellow-600">~</span>
                            @else
                                <span class="text-red-600">✗</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                            График маълумотлари топилмади
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900">ЖАМИ</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">
                            {{ number_format(collect($tolovTaqqoslash)->sum('grafik'), 0, ',', ' ') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-teal-700 text-right">
                            {{ number_format(collect($tolovTaqqoslash)->sum('fakt'), 0, ',', ' ') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right {{ collect($tolovTaqqoslash)->sum('farq') <= 0 ? 'text-green-700' : 'text-red-700' }}">
                            {{ number_format(collect($tolovTaqqoslash)->sum('farq'), 0, ',', ' ') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            {{ $foiz }}%
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Actual Payments History --}}
    @if($yer->faktTolovlar->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">То'лов тарихи ({{ $yer->faktTolovlar->count() }} та)</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Сана</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ҳужжат №</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Тўловчи</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Сумма (сўм)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($yer->faktTolovlar->sortByDesc('tolov_sana') as $tolov)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $tolov->tolov_sana->format('d.m.Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $tolov->hujjat_raqam }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ Str::limit($tolov->tolash_nom, 40) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-teal-700 text-right font-medium">
                            {{ number_format($tolov->tolov_summa, 0, ',', ' ') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
