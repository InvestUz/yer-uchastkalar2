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

                <!-- SEARCH AND FILTERS FORM -->
                <form method="GET" action="{{ route('yer-sotuvlar.list') }}"
                    class="bg-gray-50 px-6 py-4 border-b border-gray-200">

                    <!-- Global Search -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Умумий қидирув (барча устунлар бўйича)
                        </label>
                        <input type="text" name="search"
                            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-base"
                            value="{{ request('search') }}"
                            placeholder="Лот рақами, туман, манзил, ғолиб номи ёки бошқа маълумот киритинг...">
                    </div>

                    <!-- Advanced Filters Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                        <!-- Tuman Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Туман</label>
                            <select name="tuman"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Барчаси</option>
                                @foreach ($tumanlar as $tuman)
                                    <option value="{{ $tuman }}" {{ request('tuman') == $tuman ? 'selected' : '' }}>
                                        {{ $tuman }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Йил</label>
                            <select name="yil"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Барчаси</option>
                                @foreach ($yillar as $yil)
                                    <option value="{{ $yil }}" {{ request('yil') == $yil ? 'selected' : '' }}>
                                        {{ $yil }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Auksion Date From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Аукцион санаси (дан)</label>
                            <input type="date" name="auksion_sana_from"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ request('auksion_sana_from') }}">
                        </div>

                        <!-- Auksion Date To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Аукцион санаси (гача)</label>
                            <input type="date" name="auksion_sana_to"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ request('auksion_sana_to') }}">
                        </div>

                        <!-- Price From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Нарх (дан, сўм)</label>
                            <input type="number" name="narx_from"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ request('narx_from') }}" step="0.01" placeholder="Мин нарх">
                        </div>

                        <!-- Price To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Нарх (гача, сўм)</label>
                            <input type="number" name="narx_to"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ request('narx_to') }}" step="0.01" placeholder="Макс нарх">
                        </div>

                        <!-- Area From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Майдон (дан, га)</label>
                            <input type="number" name="maydoni_from"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ request('maydoni_from') }}" step="0.01" placeholder="Мин майдон">
                        </div>

                        <!-- Area To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Майдон (гача, га)</label>
                            <input type="number" name="maydoni_to"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ request('maydoni_to') }}" step="0.01" placeholder="Макс майдон">
                        </div>

                        <!-- Holat Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ҳолат</label>
                            <input type="text" name="holat"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ request('holat') }}" placeholder="Ҳолат қидириш">
                        </div>

                        <!-- Asos Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Асос</label>
                            <input type="text" name="asos"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ request('asos') }}" placeholder="Асос қидириш">
                        </div>

                        <!-- Tolov Turi Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Тўлов тури</label>
                            <select name="tolov_turi"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Барчаси</option>
                                <option value="муддатли" {{ request('tolov_turi') == 'муддатли' ? 'selected' : '' }}>
                                    Муддатли</option>
                                <option value="муддатли эмас"
                                    {{ request('tolov_turi') == 'муддатли эмас' ? 'selected' : '' }}>Муддатли эмас</option>
                            </select>
                        </div>

                        <!-- Sort Field -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Саралаш</label>
                            <select name="sort"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="auksion_sana" {{ request('sort') == 'auksion_sana' ? 'selected' : '' }}>
                                    Аукцион санаси</option>
                                <option value="sotilgan_narx" {{ request('sort') == 'sotilgan_narx' ? 'selected' : '' }}>
                                    Сотилган нарх</option>
                                <option value="boshlangich_narx"
                                    {{ request('sort') == 'boshlangich_narx' ? 'selected' : '' }}>Бошланғич нарх</option>
                                <option value="maydoni" {{ request('sort') == 'maydoni' ? 'selected' : '' }}>Майдон
                                </option>
                                <option value="tuman" {{ request('sort') == 'tuman' ? 'selected' : '' }}>Туман</option>
                                <option value="lot_raqami" {{ request('sort') == 'lot_raqami' ? 'selected' : '' }}>Лот
                                    рақами</option>
                            </select>
                        </div>

                        <!-- Sort Direction -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Тартиб</label>
                            <select name="direction"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Камайиш ↓
                                </option>
                                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ўсиш ↑
                                </option>
                            </select>
                        </div>

                        <!-- Search Button -->
                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Қидириш
                            </button>
                        </div>

                        <!-- Reset Button -->
                        <div class="flex items-end">
                            <a href="{{ route('yer-sotuvlar.list') }}"
                                class="w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Тозалаш
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Active Filters Display -->
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-wrap gap-3">
                        @if (request('search'))
                            <div
                                class="inline-flex items-center bg-blue-100 border border-blue-300 rounded-md px-3 py-2 text-sm">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span class="text-blue-600">Қидирув:</span>
                                <span class="ml-1 font-semibold text-blue-900">{{ request('search') }}</span>
                            </div>
                        @endif

                        @if (request('tuman'))
                            <div
                                class="inline-flex items-center bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                <span class="text-gray-600">Туман:</span>
                                <span class="ml-1 font-semibold text-gray-900">{{ request('tuman') }}</span>
                            </div>
                        @endif

                        @if (request('yil'))
                            <div
                                class="inline-flex items-center bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-gray-600">Йил:</span>
                                <span class="ml-1 font-semibold text-gray-900">{{ request('yil') }}</span>
                            </div>
                        @endif

                        @if (request('tolov_turi'))
                            <div
                                class="inline-flex items-center bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                <span class="text-gray-600">Тўлов:</span>
                                <span class="ml-1 font-semibold text-gray-900">{{ request('tolov_turi') }}</span>
                            </div>
                        @endif

                        @if (request('holat'))
                            <div
                                class="inline-flex items-center bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-gray-600">Ҳолат:</span>
                                <span
                                    class="ml-1 font-semibold text-gray-900">{{ Str::limit(request('holat'), 40) }}</span>
                            </div>
                        @endif

                        @if (request('asos'))
                            <div
                                class="inline-flex items-center bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-gray-600">Асос:</span>
                                <span class="ml-1 font-semibold text-gray-900">{{ request('asos') }}</span>
                            </div>
                        @endif

                        @if (request('auksion_sana_from') || request('auksion_sana_to'))
                            <div
                                class="inline-flex items-center bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-gray-600">Санаси:</span>
                                <span class="ml-1 font-semibold text-gray-900">
                                    {{ request('auksion_sana_from') ?? '...' }} -
                                    {{ request('auksion_sana_to') ?? '...' }}
                                </span>
                            </div>
                        @endif

                        @if (request('narx_from') || request('narx_to'))
                            <div
                                class="inline-flex items-center bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                <span class="text-gray-600">Нарх:</span>
                                <span class="ml-1 font-semibold text-gray-900">
                                    {{ request('narx_from') ? number_format(request('narx_from')) : '0' }} -
                                    {{ request('narx_to') ? number_format(request('narx_to')) : '∞' }}
                                </span>
                            </div>
                        @endif

                        @if (request('maydoni_from') || request('maydoni_to'))
                            <div
                                class="inline-flex items-center bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                                </svg>
                                <span class="text-gray-600">Майдон:</span>
                                <span class="ml-1 font-semibold text-gray-900">
                                    {{ request('maydoni_from') ?? '0' }} - {{ request('maydoni_to') ?? '∞' }} га
                                </span>
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
                            <div class="text-3xl font-bold text-gray-800">
                                {{ number_format($statistics['total_area'], 2) }}</div>
                            <div class="text-sm text-gray-600 mt-1">Жами майдон (га)</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="text-3xl font-bold text-gray-800">
                                {{ number_format($statistics['boshlangich_narx'] / 1000000000, 1) }}</div>
                            <div class="text-sm text-gray-600 mt-1">Бошланғич нарх (млрд сўм)</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="text-3xl font-bold text-gray-800">
                                {{ number_format($statistics['chegirma'] / 1000000000, 1) }}</div>
                            <div class="text-sm text-gray-600 mt-1">Чегирма суммаси (млрд сўм)</div>
                        </div>

                        <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="text-3xl font-bold text-gray-800">
                                {{ number_format($statistics['golib_tolagan'] / 1000000000, 1) }}</div>
                            <div class="text-sm text-gray-600 mt-1">Ғолиб аукционга тўлаган сумма (млрд сўм)</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="text-3xl font-bold text-gray-800">
                                {{ number_format($statistics['total_price'] / 1000000000, 1) }}</div>
                            <div class="text-sm text-gray-600 mt-1">Сотилган нарх (млрд сўм)</div>
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
                                <!-- Sortable Column Helper Function -->
                                @php
                                    function sortableColumn($field, $label)
                                    {
                                        $currentSort = request('sort', 'auksion_sana');
                                        $currentDirection = request('direction', 'desc');
                                        $newDirection =
                                            $currentSort === $field && $currentDirection === 'asc' ? 'desc' : 'asc';

                                        $queryParams = array_merge(request()->except(['sort', 'direction', 'page']), [
                                            'sort' => $field,
                                            'direction' => $newDirection,
                                        ]);

                                        $url = route('yer-sotuvlar.list', $queryParams);
                                        $isActive = $currentSort === $field;

                                        $arrow = '';
                                        if ($isActive) {
                                            $arrow = $currentDirection === 'asc' ? '↑' : '↓';
                                        }

                                        return [
                                            'url' => $url,
                                            'isActive' => $isActive,
                                            'arrow' => $arrow,
                                            'label' => $label,
                                        ];
                                    }
                                @endphp

                                <!-- Sortable Headers -->
                                @php
                                    $columns = [
                                        'lot_raqami' => '№ Лот',
                                        'tuman' => 'Туман',
                                        'manzil' => 'Манзил',
                                        'maydoni' => 'Майдон (га)',
                                        'boshlangich_narx' => 'Бошл. нарх',
                                        'auksion_sana' => 'Аукцион',
                                        'sotilgan_narx' => 'Сотил. нарх',
                                        'chegirma' => 'Чегирма қиммати',
                                        'golib_tolagan' => 'Ғолиб аукционга тўлаган сумма',
                                        'golib' => 'Ғолиб',
                                    ];
                                @endphp

                                @foreach ($columns as $field => $label)
                                    @php $col = sortableColumn($field, $label); @endphp
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider cursor-pointer hover:bg-gray-600 transition-colors">
                                        <a href="{{ $col['url'] }}" class="flex items-center justify-between group">
                                            <span>{{ $col['label'] }}</span>
                                            <span
                                                class="ml-2 {{ $col['isActive'] ? 'text-yellow-300' : 'text-gray-400 group-hover:text-gray-300' }}">
                                                {{ $col['arrow'] ?: '⇅' }}
                                            </span>
                                        </a>
                                    </th>
                                @endforeach


                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Тўлов тури</th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Ҳолат</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($yerlar as $yer)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <a href="{{ route('yer-sotuvlar.show', $yer->lot_raqami) }}"
                                            class="font-semibold text-gray-900 hover:text-gray-700 hover:underline">
                                            {{ $yer->lot_raqami }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $yer->tuman }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900 max-w-xs truncate"
                                        title="{{ $yer->manzil }}">
                                        {{ Str::limit($yer->manzil, 50) }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($yer->maydoni, 4) }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($yer->boshlangich_narx, 0, '.', ' ') }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $yer->auksion_sana ? $yer->auksion_sana->format('d.m.Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                        {{ number_format($yer->sotilgan_narx, 0, '.', ' ') }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                        {{ number_format($yer->chegirma, 0, '.', ' ') }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                        {{ number_format($yer->golib_tolagan, 0, '.', ' ') }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900 max-w-xs truncate"
                                        title="{{ $yer->golib_nomi }}">
                                        {{ Str::limit($yer->golib_nomi, 40) }}
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        @if ($yer->tolov_turi === 'муддатли')
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Муддатли
                                            </span>
                                        @elseif($yer->tolov_turi === 'муддатли эмас')
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                муддатли эмас
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600 max-w-sm truncate"
                                        title="{{ $yer->holat }}">
                                        {{ Str::limit($yer->holat, 60) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-8 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="mt-2 text-lg font-medium">Маълумот топилмади</p>
                                        <p class="mt-1 text-sm">Филтр параметрларини ўзгартириб кўринг</p>
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
