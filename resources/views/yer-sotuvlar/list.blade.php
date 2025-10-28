@extends('layouts.app')

@section('title', 'Филтрланган маълумотлар')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="mx-auto mb-6">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
                <!-- Header -->
                <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <h1 class="text-xl font-semibold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Филтрланган маълумотлар
                        </h1>
                        <a href="{{ route('yer-sotuvlar.index') }}"
                            class="bg-white text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-100 transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Статистикага қайтиш
                        </a>
                    </div>
                </div>

                <!-- Filters Display -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-wrap gap-3">
                        @if (!empty($filters['tuman']))
                            <div
                                class="inline-flex items-center bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-gray-600">Туман:</span>
                                <span class="ml-1 font-semibold text-gray-900">{{ $filters['tuman'] }}</span>
                            </div>
                        @endif

                        @if (!empty($filters['tolov_turi']))
                            <div
                                class="inline-flex items-center bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                <span class="text-gray-600">Тўлов:</span>
                                <span class="ml-1 font-semibold text-gray-900">{{ $filters['tolov_turi'] }}</span>
                            </div>
                        @endif

                        @if (!empty($filters['holat']))
                            <div
                                class="inline-flex items-center bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-gray-600">Ҳолат:</span>
                                <span
                                    class="ml-1 font-semibold text-gray-900">{{ Str::limit($filters['holat'], 40) }}</span>
                            </div>
                        @endif

                        @if (!empty($filters['asos']))
                            <div
                                class="inline-flex items-center bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-gray-600">Асос:</span>
                                <span class="ml-1 font-semibold text-gray-900">{{ $filters['asos'] }}</span>
                            </div>
                        @endif

                        @if (!empty($filters['yil']))
                            <div
                                class="inline-flex items-center bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-gray-600">Йил:</span>
                                <span class="ml-1 font-semibold text-gray-900">{{ $filters['yil'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Statistics Summary -->
                <div class="bg-white px-6 py-5">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="text-3xl font-bold text-gray-800">{{ number_format($statistics['total_lots']) }}
                            </div>
                            <div class="text-sm text-gray-600 mt-1">Жами лотлар</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="text-3xl font-bold text-gray-800">{{ number_format($statistics['total_area'], 2) }}
                            </div>
                            <div class="text-sm text-gray-600 mt-1">Жами майдон (га)</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="text-3xl font-bold text-gray-800">
                                {{ number_format($statistics['total_price'] / 1000000000, 1) }}</div>
                            <div class="text-sm text-gray-600 mt-1">Жами сумма (млрд)</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="text-3xl font-bold text-gray-800">
                                {{ $yerlar->currentPage() }}/{{ $yerlar->lastPage() }}</div>
                            <div class="text-sm text-gray-600 mt-1">Саҳифа</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="mx-auto">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-100 uppercase tracking-wider w-16">
                                    №</th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                    Лот рақами</th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                    Туман</th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                    МФЙ</th>
                                    <th scope="col"
                                                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                                                    Уникал рақами</th>
                                <th scope="col"
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                    Майдон (га)</th>
                                <th scope="col"
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                    Бошл. нарх</th>
                                <th scope="col"
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                    Сотилган нарх</th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                    Тўлов тури</th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                    Лот холати</th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                    Аукцион сана</th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-100 uppercase tracking-wider">
                                    Амаллар</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($yerlar as $index => $yer)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-center text-sm font-medium text-gray-900">
                                        {{ $yerlar->firstItem() + $index }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <a href="{{ route('yer-sotuvlar.show', $yer->lot_raqami) }}"
                                            class="font-semibold text-gray-900 hover:text-gray-700 hover:underline">
                                            {{ $yer->lot_raqami }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $yer->tuman }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $yer->mfy }}
                                    </td>
  <td class="px-4 py-3 text-sm text-right">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $yer->unikal_raqam }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ number_format($yer->maydoni, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-600">
                                        {{ number_format($yer->boshlangich_narx / 1000000, 1) }} млн
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">
                                        {{ number_format($yer->sotilgan_narx / 1000000, 1) }} млн
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        @if ($yer->tolov_turi == 'муддатли эмас')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-600 text-white">
                                                Бир йўла
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-400 text-white">
                                                Бўлиб
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ Str::limit($yer->holat, 50) }}
                                    </td>

                                    <td class="px-4 py-3 text-sm text-center text-gray-600">
                                        @if ($yer->auksion_sana)
                                            {{ $yer->auksion_sana->format('d.m.Y') }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <a href="{{ route('yer-sotuvlar.show', $yer->lot_raqami) }}"
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-4 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Маълумот топилмади</h3>
                                        <p class="mt-1 text-sm text-gray-500">Филтр шартларига мос келадиган маълумотлар
                                            йўқ</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($yerlar->hasPages())
                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Кўрсатилмоқда: <span class="font-semibold">{{ $yerlar->firstItem() }}</span> -
                                <span class="font-semibold">{{ $yerlar->lastItem() }}</span> /
                                <span class="font-semibold">{{ $yerlar->total() }}</span>
                            </div>
                            <div>
                                {{ $yerlar->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Custom scrollbar for table */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endsection
