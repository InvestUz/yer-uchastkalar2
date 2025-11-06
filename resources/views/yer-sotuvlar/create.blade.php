@extends('layouts.app')

@section('title', 'Янги Ер Участка Қўшиш')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 mb-2">
                    Янги Ер Участка Қўшиш
                </h1>
                <p class="text-slate-600">Барча керакли маълумотларни тўлдиринг</p>
            </div>
            <a href="{{ route('yer-sotuvlar.list') }}"
               class="flex items-center space-x-2 px-5 py-2.5 bg-slate-600 hover:bg-slate-700 text-white rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Орқага</span>
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('yer-sotuvlar.store') }}" method="POST" class="space-y-6" id="createForm">
        @csrf

        <!-- Асосий маълумотлар -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center space-x-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Асосий маълумотлар</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Lot Raqami -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Лот рақами *</label>
                    <input type="text" name="lot_raqami" value="{{ old('lot_raqami') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                           required>
                    @error('lot_raqami')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tuman -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Туман</label>
                    <select name="tuman" id="tuman"
                            class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                        <option value="">Танланг</option>
                        @foreach($tumanlar as $tuman)
                            <option value="{{ $tuman }}" {{ old('tuman') == $tuman ? 'selected' : '' }}>{{ $tuman }}</option>
                        @endforeach
                        <option value="__other__">Бошқа (ёзиш)</option>
                    </select>
                    <input type="text" id="tuman_other" name="tuman_other"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all mt-2 hidden"
                           placeholder="Туман номини киритинг">
                </div>

                <!-- MFY -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">МФЙ</label>
                    <select name="mfy" id="mfy"
                            class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                        <option value="">Танланг</option>
                        @foreach($mfylar as $mfy)
                            <option value="{{ $mfy }}" {{ old('mfy') == $mfy ? 'selected' : '' }}>{{ $mfy }}</option>
                        @endforeach
                        <option value="__other__">Бошқа (ёзиш)</option>
                    </select>
                    <input type="text" id="mfy_other" name="mfy_other"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all mt-2 hidden"
                           placeholder="МФЙ номини киритинг">
                </div>

                <!-- Manzil -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Манзил</label>
                    <input type="text" name="manzil" value="{{ old('manzil') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Unikal Raqam -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Уникал рақам</label>
                    <input type="text" name="unikal_raqam" value="{{ old('unikal_raqam') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Zona -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Зона</label>
                    <select name="zona" id="zona"
                            class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                        <option value="">Танланг</option>
                        @foreach($zonalar as $zona)
                            <option value="{{ $zona }}" {{ old('zona') == $zona ? 'selected' : '' }}>{{ $zona }}</option>
                        @endforeach
                        <option value="__other__">Бошқа (ёзиш)</option>
                    </select>
                    <input type="text" id="zona_other" name="zona_other"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all mt-2 hidden"
                           placeholder="Зона номини киритинг">
                </div>

                <!-- Bosh Reja Zona -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Бош режа зонаси</label>
                    <select name="bosh_reja_zona" id="bosh_reja_zona"
                            class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                        <option value="">Танланг</option>
                        @foreach($boshRejaZonalari as $brz)
                            <option value="{{ $brz }}" {{ old('bosh_reja_zona') == $brz ? 'selected' : '' }}>{{ $brz }}</option>
                        @endforeach
                        <option value="__other__">Бошқа (ёзиш)</option>
                    </select>
                    <input type="text" id="bosh_reja_zona_other" name="bosh_reja_zona_other"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all mt-2 hidden"
                           placeholder="Бош режа зонаси номини киритинг">
                </div>

                <!-- Yangi Ozbekiston -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Янги Ўзбекистон</label>
                    <select name="yangi_ozbekiston" id="yangi_ozbekiston"
                            class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                        <option value="">Танланг</option>
                        @foreach($yangiOzbekiston as $yo)
                            <option value="{{ $yo }}" {{ old('yangi_ozbekiston') == $yo ? 'selected' : '' }}>{{ $yo }}</option>
                        @endforeach
                        <option value="__other__">Бошқа (ёзиш)</option>
                    </select>
                    <input type="text" id="yangi_ozbekiston_other" name="yangi_ozbekiston_other"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all mt-2 hidden"
                           placeholder="Номини киритинг">
                </div>

                <!-- Maydoni -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Майдони (га)</label>
                    <input type="number" step="0.0001" name="maydoni" value="{{ old('maydoni') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Yil -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Йил</label>
                    <input type="number" name="yil" value="{{ old('yil', date('Y')) }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Lokatsiya -->
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Локация (Google Maps link)</label>
                    <textarea name="lokatsiya" rows="2"
                              class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">{{ old('lokatsiya') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Қурилиш маълумотлари -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center space-x-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Қурилиш маълумотлари</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Qurilish Turi 1 -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Қурилиш тури 1</label>
                    <select name="qurilish_turi_1" id="qurilish_turi_1"
                            class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                        <option value="">Танланг</option>
                        @foreach($qurilishTurlari1 as $qt1)
                            <option value="{{ $qt1 }}" {{ old('qurilish_turi_1') == $qt1 ? 'selected' : '' }}>{{ $qt1 }}</option>
                        @endforeach
                        <option value="__other__">Бошқа (ёзиш)</option>
                    </select>
                    <input type="text" id="qurilish_turi_1_other" name="qurilish_turi_1_other"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all mt-2 hidden"
                           placeholder="Қурилиш тури номини киритинг">
                </div>

                <!-- Qurilish Turi 2 -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Қурилиш тури 2</label>
                    <select name="qurilish_turi_2" id="qurilish_turi_2"
                            class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                        <option value="">Танланг</option>
                        @foreach($qurilishTurlari2 as $qt2)
                            <option value="{{ $qt2 }}" {{ old('qurilish_turi_2') == $qt2 ? 'selected' : '' }}>{{ $qt2 }}</option>
                        @endforeach
                        <option value="__other__">Бошқа (ёзиш)</option>
                    </select>
                    <input type="text" id="qurilish_turi_2_other" name="qurilish_turi_2_other"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all mt-2 hidden"
                           placeholder="Қурилиш тури номини киритинг">
                </div>

                <!-- Qurilish Maydoni -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Қурилиш майдони (кв.м)</label>
                    <input type="number" step="0.01" name="qurilish_maydoni" value="{{ old('qurilish_maydoni') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Investitsiya -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Инвестиция (АҚШ долл)</label>
                    <input type="number" step="0.01" name="investitsiya" value="{{ old('investitsiya') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>
            </div>
        </div>

        <!-- Аукцион маълумотлари -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center space-x-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Аукцион маълумотлари</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Boshlangich Narx -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Бошланғич нарх</label>
                    <input type="number" step="0.01" name="boshlangich_narx" value="{{ old('boshlangich_narx') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Auksion Sana -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Аукцион санаси</label>
                    <input type="date" name="auksion_sana" value="{{ old('auksion_sana') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Sotilgan Narx -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Сотилган нарх</label>
                    <input type="number" step="0.01" name="sotilgan_narx" value="{{ old('sotilgan_narx') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Auksion Golibi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Аукцион ғолиби</label>
                    <input type="text" name="auksion_golibi" value="{{ old('auksion_golibi') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Golib Turi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ғолиб тури</label>
                    <select name="golib_turi"
                            class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                        <option value="">Танланг</option>
                        <option value="юр лицо" {{ old('golib_turi') == 'юр лицо' ? 'selected' : '' }}>юр лицо</option>
                        <option value="физ лицо" {{ old('golib_turi') == 'физ лицо' ? 'selected' : '' }}>физ лицо</option>
                    </select>
                </div>

                <!-- Golib Nomi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ғолиб номи</label>
                    <input type="text" name="golib_nomi" value="{{ old('golib_nomi') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Telefon -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Телефон</label>
                    <input type="text" name="telefon" value="{{ old('telefon') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                           placeholder="+998 XX XXX XX XX">
                </div>

                <!-- Tolov Turi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Тўлов тури</label>
                    <select name="tolov_turi" id="tolov_turi"
                            class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                        <option value="">Танланг</option>
                        <option value="муддатли" {{ old('tolov_turi') == 'муддатли' ? 'selected' : '' }}>муддатли</option>
                        <option value="муддатли эмас" {{ old('tolov_turi') == 'муддатли эмас' ? 'selected' : '' }}>муддатли эмас</option>
                    </select>
                </div>

                <!-- Asos -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Асос</label>
                    <select name="asos" id="asos"
                            class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                        <option value="">Танланг</option>
                        @foreach($asoslar as $asos)
                            <option value="{{ $asos }}" {{ old('asos') == $asos ? 'selected' : '' }}>{{ $asos }}</option>
                        @endforeach
                        <option value="__other__">Бошқа (ёзиш)</option>
                    </select>
                    <input type="text" id="asos_other" name="asos_other"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all mt-2 hidden"
                           placeholder="Асос номини киритинг">
                </div>

                <!-- Auksion Turi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Аукцион тури</label>
                    <select name="auksion_turi"
                            class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                        <option value="">Танланг</option>
                        <option value="Очиқ" {{ old('auksion_turi') == 'Очиқ' ? 'selected' : '' }}>Очиқ</option>
                        <option value="Ёпиқ" {{ old('auksion_turi') == 'Ёпиқ' ? 'selected' : '' }}>Ёпиқ</option>
                    </select>
                </div>

                <!-- Holat -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ҳолат</label>
                    <select name="holat" id="holat"
                            class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                        <option value="">Танланг</option>
                        @foreach($holatlar as $holat)
                            <option value="{{ $holat }}" {{ old('holat') == $holat ? 'selected' : '' }}>{{ $holat }}</option>
                        @endforeach
                        <option value="__other__">Бошқа (ёзиш)</option>
                    </select>
                    <input type="text" id="holat_other" name="holat_other"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all mt-2 hidden"
                           placeholder="Ҳолат номини киритинг">
                </div>
            </div>
        </div>

        <!-- График тўловлар (only shows when tolov_turi is муддатли) -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500 hidden" id="grafikSection">
            <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center space-x-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>График тўловлар</span>
                <span id="grafikCount" class="ml-2 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold hidden">0 та</span>
            </h2>
            <p class="text-sm text-slate-600 mb-4">Ойлик тўлов режасини киритинг</p>

            <div id="grafikContainer" class="space-y-4 mb-4">
                <!-- Grafik rows will be added here dynamically -->
            </div>

            <div class="flex items-center justify-between">
                <button type="button" id="addGrafikRow"
                        class="flex items-center space-x-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Қўшиш</span>
                </button>
                <button type="button" id="clearAllGrafik"
                        class="flex items-center space-x-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-all hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Ҳаммасини ўчириш</span>
                </button>
            </div>
        </div>

        <!-- Шартнома маълумотлари -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center space-x-2">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Шартнома маълумотлари</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Shartnoma Holati -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Шартнома ҳолати</label>
                    <input type="text" name="shartnoma_holati" value="{{ old('shartnoma_holati') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Shartnoma Sana -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Шартнома санаси</label>
                    <input type="date" name="shartnoma_sana" value="{{ old('shartnoma_sana') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Shartnoma Raqam -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Шартнома рақами</label>
                    <input type="text" name="shartnoma_raqam" value="{{ old('shartnoma_raqam') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>
            </div>
        </div>

        <!-- Молиявий маълумотлар -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center space-x-2">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Молиявий маълумотлар</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Golib Tolagan -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ғолиб тўлаган</label>
                    <input type="number" step="0.01" name="golib_tolagan" value="{{ old('golib_tolagan') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Buyurtmachiga Otkazilgan -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Буюртмачига ўтказилган</label>
                    <input type="number" step="0.01" name="buyurtmachiga_otkazilgan" value="{{ old('buyurtmachiga_otkazilgan') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Chegirma -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Чегирма</label>
                    <input type="number" step="0.01" name="chegirma" value="{{ old('chegirma') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Auksion Harajati -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Аукцион ҳаражати (1%)</label>
                    <input type="number" step="0.01" name="auksion_harajati" value="{{ old('auksion_harajati') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Tushadigan Mablagh -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Тушадиган маблағ</label>
                    <input type="number" step="0.01" name="tushadigan_mablagh" value="{{ old('tushadigan_mablagh') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Davaktiv Jamgarmasi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Давактив жамғармаси</label>
                    <input type="number" step="0.01" name="davaktiv_jamgarmasi" value="{{ old('davaktiv_jamgarmasi') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Shartnoma Tushgan -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Шартнома бўйича тушган</label>
                    <input type="number" step="0.01" name="shartnoma_tushgan" value="{{ old('shartnoma_tushgan') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Davaktivda Turgan -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Давактивда турган</label>
                    <input type="number" step="0.01" name="davaktivda_turgan" value="{{ old('davaktivda_turgan') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Yer Auksion Harajat -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ер аукцион ҳаражат</label>
                    <input type="number" step="0.01" name="yer_auksion_harajat" value="{{ old('yer_auksion_harajat') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Shartnoma Summasi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Шартнома суммаси</label>
                    <input type="number" step="0.01" name="shartnoma_summasi" value="{{ old('shartnoma_summasi') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>

                <!-- Farqi -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Фарқи</label>
                    <input type="number" step="0.01" name="farqi" value="{{ old('farqi') }}"
                           class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between bg-slate-50 rounded-xl p-6 border-2 border-slate-200">
            <a href="{{ route('yer-sotuvlar.list') }}"
               class="flex items-center space-x-2 px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-lg transition-all duration-300 font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>Бекор қилиш</span>
            </a>

            <button type="submit"
                    class="flex items-center space-x-2 px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Сақлаш</span>
            </button>
        </div>
    </form>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="fixed bottom-8 right-8 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl animate-fadeIn z-50">
    <div class="flex items-center space-x-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
</div>
@endif

@if(session('error'))
<div class="fixed bottom-8 right-8 bg-red-500 text-white px-6 py-4 rounded-lg shadow-2xl animate-fadeIn z-50">
    <div class="flex items-center space-x-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
</div>
@endif

@if($errors->any())
<div class="fixed bottom-8 right-8 bg-red-500 text-white px-6 py-4 rounded-lg shadow-2xl animate-fadeIn z-50 max-w-md">
    <div class="flex items-start space-x-3">
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="font-bold mb-2">Хатолар:</p>
            <ul class="list-disc list-inside space-y-1 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle "other" option for select fields
    const selectFields = [
        'tuman', 'mfy', 'zona', 'bosh_reja_zona', 'yangi_ozbekiston',
        'qurilish_turi_1', 'qurilish_turi_2', 'asos', 'holat'
    ];

    selectFields.forEach(field => {
        const select = document.getElementById(field);
        const otherInput = document.getElementById(field + '_other');

        if (select && otherInput) {
            select.addEventListener('change', function() {
                if (this.value === '__other__') {
                    otherInput.classList.remove('hidden');
                    otherInput.required = true;
                    otherInput.focus();
                } else {
                    otherInput.classList.add('hidden');
                    otherInput.required = false;
                    otherInput.value = '';
                }
            });
        }
    });

    // Handle tolov_turi change
    const tolovTuriSelect = document.getElementById('tolov_turi');
    const grafikSection = document.getElementById('grafikSection');

    if (tolovTuriSelect) {
        tolovTuriSelect.addEventListener('change', function() {
            if (this.value === 'муддатли') {
                grafikSection.classList.remove('hidden');
                // Auto add first row
                if (grafikContainer.children.length === 0) {
                    addGrafikBtn.click();
                }
            } else {
                grafikSection.classList.add('hidden');
            }
        });
    }

    // Grafik tolovlar functionality
    let grafikRowIndex = 0;
    const grafikContainer = document.getElementById('grafikContainer');
    const addGrafikBtn = document.getElementById('addGrafikRow');
    const clearAllBtn = document.getElementById('clearAllGrafik');
    const grafikCountBadge = document.getElementById('grafikCount');

    if (addGrafikBtn) {
        addGrafikBtn.addEventListener('click', function() {
            const row = createGrafikRow(grafikRowIndex);
            grafikContainer.appendChild(row);
            grafikRowIndex++;
            updateGrafikCount();
        });
    }

    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            if (confirm('Барча график тўловларни ўчирмоқчимисиз?')) {
                grafikContainer.innerHTML = '';
                updateGrafikCount();
            }
        });
    }

    function createGrafikRow(index) {
        const div = document.createElement('div');
        div.className = 'grid grid-cols-1 md:grid-cols-4 gap-4 items-end p-4 bg-slate-50 rounded-lg border-2 border-slate-200';
        div.innerHTML = `
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Йил</label>
                <input type="number" name="grafik_data[${index}][yil]"
                       class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                       placeholder="2024" value="${new Date().getFullYear()}">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Ой</label>
                <select name="grafik_data[${index}][oy]"
                        class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                    <option value="">Танланг</option>
                    <option value="1">1 - Январь</option>
                    <option value="2">2 - Февраль</option>
                    <option value="3">3 - Март</option>
                    <option value="4">4 - Апрель</option>
                    <option value="5">5 - Май</option>
                    <option value="6">6 - Июнь</option>
                    <option value="7">7 - Июль</option>
                    <option value="8">8 - Август</option>
                    <option value="9">9 - Сентябрь</option>
                    <option value="10">10 - Октябрь</option>
                    <option value="11">11 - Ноябрь</option>
                    <option value="12">12 - Декабрь</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Сумма (сўм)</label>
                <input type="number" step="0.01" name="grafik_data[${index}][summa]"
                       class="w-full px-4 py-2.5 border-2 border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                       placeholder="0.00">
            </div>
            <div>
                <button type="button" class="removeGrafikRow w-full px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all font-semibold">
                    <span class="flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Ўчириш</span>
                    </span>
                </button>
            </div>
        `;

        // Add remove functionality
        const removeBtn = div.querySelector('.removeGrafikRow');
        removeBtn.addEventListener('click', function() {
            div.remove();
            updateGrafikCount();
        });

        return div;
    }

    function updateGrafikCount() {
        const count = grafikContainer.children.length;

        if (count > 0) {
            grafikCountBadge.textContent = count + ' та';
            grafikCountBadge.classList.remove('hidden');
            clearAllBtn.classList.remove('hidden');
        } else {
            grafikCountBadge.classList.add('hidden');
            clearAllBtn.classList.add('hidden');
        }
    }

    // Before form submission, handle "other" values
    const form = document.getElementById('createForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Сақланмоқда...';

            selectFields.forEach(field => {
                const select = document.getElementById(field);
                const otherInput = document.getElementById(field + '_other');

                if (select && otherInput && select.value === '__other__' && otherInput.value) {
                    // Create hidden input with the actual value
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = field;
                    hidden.value = otherInput.value;
                    form.appendChild(hidden);
                }
            });
        });
    }
});
</script>

<style>
.animate-fadeIn {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
@endsection
