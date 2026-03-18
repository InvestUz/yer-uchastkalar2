@extends('layouts.app')

@section('title', 'Фин-ҳисобот деталлар')

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
$activeFilterText = !empty($periodParts) ? implode(' | ', $periodParts) : 'Барча давр';
@endphp
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-6 px-4">
    <div class="max-w-[98%] mx-auto space-y-6">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-t-4 border-blue-600">
            <div class="px-6 py-5 bg-gradient-to-r from-blue-50 to-slate-50 border-b border-slate-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">ФИН-ҲИСОБОТ ДЕТАЛ РЎЙХАТИ</h1>
                        <p class="text-sm text-slate-600 mt-1">
                            Танланган фильтр:
                            <span class="font-semibold">{{ $selectedDistrict }}</span>
                            /
                            <span class="font-semibold">{{ $selectedCategory }}</span>
                        </p>
                        <p class="text-xs text-blue-700 mt-1">
                            Давр фильтри: <span class="font-semibold">{{ $activeFilterText }}</span>
                        </p>
                    </div>
                    <a href="{{ route('yer-sotuvlar.fin-xisobot', $activeFilterParams) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        Орқага
                    </a>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4 bg-white">
                <div class="rounded-lg border border-slate-200 p-4 bg-slate-50">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Ёзувлар сони</p>
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($recordCount ?? 0, 0, '.', ',') }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 p-4 bg-slate-50">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Жами сумма</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $fmt($totalAmount ?? 0) }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ number_format($totalAmount ?? 0, 2, '.', ',') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-t-4 border-blue-600">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-blue-50">
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Т/р</th>
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Сана</th>
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Ҳужжат №</th>
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Лот</th>
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Ҳудуд</th>
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Тоифа</th>
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Олувчи</th>
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Модда</th>
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Сумма</th>
                            <th class="border border-slate-300 px-3 py-3 text-center text-xs font-bold text-slate-700">Детали</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="border border-slate-200 px-3 py-2 text-center text-sm text-slate-700">{{ $loop->iteration }}</td>
                                <td class="border border-slate-200 px-3 py-2 text-center text-sm text-slate-700">{{ $row['date'] }}</td>
                                <td class="border border-slate-200 px-3 py-2 text-center text-sm text-slate-700">{{ $row['doc_num'] }}</td>
                                <td class="border border-slate-200 px-3 py-2 text-center text-sm text-slate-700 whitespace-nowrap">
                                    @if(!empty($row['lot_raqami']))
                                        <a href="{{ route('yer-sotuvlar.show', ['lot_raqami' => $row['lot_raqami']]) }}" class="text-blue-700 hover:text-blue-900 hover:underline font-semibold">
                                            {{ $row['lot_raqami'] }}
                                        </a>
                                        @if(!empty($row['lot_match_source']))
                                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $row['lot_match_source'] }}</div>
                                        @endif
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="border border-slate-200 px-3 py-2 text-sm text-slate-700">{{ $row['district'] }}</td>
                                <td class="border border-slate-200 px-3 py-2 text-sm text-slate-700">{{ $row['category'] }}</td>
                                <td class="border border-slate-200 px-3 py-2 text-sm text-slate-700">{{ $row['recipient'] }}</td>
                                <td class="border border-slate-200 px-3 py-2 text-sm text-slate-700">{{ $row['article'] }}</td>
                                <td class="border border-slate-200 px-3 py-2 text-right text-sm font-semibold text-slate-800">{{ number_format($row['amount'], 2, '.', ',') }}</td>
                                <td class="border border-slate-200 px-3 py-2 text-xs text-slate-600 break-words min-w-[380px]">{{ $row['details'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="border border-slate-300 px-4 py-6 text-center text-slate-600">
                                    Танланган фильтр бўйича маълумот топилмади.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
