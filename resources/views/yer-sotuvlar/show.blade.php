@extends('layouts.app')

@section('title', 'Лот ' . $yer->lot_raqami . ' - Батафсил')

@section('content')
<div class="space-y-4">

    {{-- Back Button --}}
    <div>
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Орқага қайтиш
        </a>
    </div>

    {{-- Main Header Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                <div>
                    <h1 class="text-xl font-bold text-white">Лот № {{ $yer->lot_raqami }}</h1>
                    <p class="text-gray-300 text-sm mt-1">{{ $yer->tuman }} • {{ $yer->mfy }}</p>
                </div>
                <div class="mt-2 md:mt-0 flex items-center space-x-4 text-sm text-gray-300">
                    <span>{{ $yer->auksion_sana ? $yer->auksion_sana->format('d.m.Y') : '-' }}</span>
                    <span class="px-2 py-1 bg-white/20 rounded text-xs">{{ $yer->yil }}</span>
                </div>
            </div>
        </div>

        {{-- Quick Stats Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4 bg-gray-50 border-b border-gray-200">
            <div class="text-center p-3 bg-white rounded border border-gray-200">

                <div class="text-lg font-bold text-gray-900">{{ number_format($yer->maydoni, 2) }}</div>
                <div class="text-xs text-gray-600">га</div>
            </div>
            <div class="text-center p-3 bg-white rounded border border-gray-200">
                <div class="text-lg font-bold text-gray-900">{{ number_format($yer->boshlangich_narx / 1000000000, 2) }}</div>
                <div class="text-xs text-gray-600">млрд (бошл.)</div>
            </div>
            <div class="text-center p-3 bg-white rounded border border-gray-200">
                <div class="text-lg font-bold text-gray-900">{{ number_format($yer->sotilgan_narx / 1000000000, 2) }}</div>
                <div class="text-xs text-gray-600">млрд (сотилган)</div>
            </div>
            <div class="text-center p-3 bg-gray-700 text-white rounded border border-gray-600">
                <div class="text-lg font-bold">{{ number_format($yer->shartnoma_summasi / 1000000000, 2) }}</div>
                <div class="text-xs">млрд (шартнома)</div>
            </div>
        </div>
    </div>

    {{-- Tabbed Content --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px overflow-x-auto">
                <button onclick="openTab(event, 'basic')" class="tab-button active px-6 py-3 text-sm font-medium border-b-2 border-gray-700 text-gray-900">
                    Асосий
                </button>
                <button onclick="openTab(event, 'financial')" class="tab-button px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-900">
                    Молиявий кўрсаткичлар
                </button>
                <button onclick="openTab(event, 'budget')" class="tab-button px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-900">
                    Тақсимот
                </button>
                <button onclick="openTab(event, 'payment')" class="tab-button px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-900">
                    Тўловлар
                </button>
            </nav>
        </div>

        {{-- Tab Content: Basic Info --}}
        <div id="basic" class="tab-content p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Left Column --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide pb-2 border-b border-gray-200">Ер участка</h3>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="py-2 text-gray-600 w-40">Уникал рақам</td>
                                <td class="py-2 font-medium text-gray-900">{{ $yer->unikal_raqam ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Зона</td>
                                <td class="py-2 font-medium text-gray-900">{{ $yer->zona ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Бош режа зона</td>
                                <td class="py-2 font-medium text-gray-900">{{ $yer->bosh_reja_zona ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Янги Ўзбекистон</td>
                                <td class="py-2 font-medium text-gray-900">{{ $yer->yangi_ozbekiston ?? '-' }}</td>
                            </tr>
                            @if($yer->manzil)
                            <tr>
                                <td class="py-2 text-gray-600">Манзил</td>
                                <td class="py-2 text-gray-900">{{ $yer->manzil }}</td>
                            </tr>
                            @endif
                            @if($yer->lokatsiya)
                            <tr>
                                <td class="py-2 text-gray-600">Локация</td>
                                <td class="py-2">
                                    <a href="https://www.google.com/maps?q={{ $yer->lokatsiya }}" target="_blank"
                                       class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        </svg>
                                        Харитада кўриш
                                    </a>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                    @if($yer->qurilish_turi_1 || $yer->qurilish_maydoni)
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide pt-4 pb-2 border-b border-gray-200">Қурилиш</h3>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @if($yer->qurilish_turi_1)
                            <tr>
                                <td class="py-2 text-gray-600 w-40">Тури 1</td>
                                <td class="py-2 text-gray-900">{{ $yer->qurilish_turi_1 }}</td>
                            </tr>
                            @endif

                            @if($yer->qurilish_maydoni)
                            <tr>
                                <td class="py-2 text-gray-600">Майдони</td>
                                <td class="py-2 font-medium text-gray-900">{{ number_format($yer->qurilish_maydoni, 0) }} м²</td>
                            </tr>
                            @endif
                            @if($yer->investitsiya)
                            <tr>
                                <td class="py-2 text-gray-600">Инвестиция</td>
                                <td class="py-2 font-medium text-gray-900">{{ number_format($yer->investitsiya / 1000000, 1) }} млн</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    @endif
                </div>

                {{-- Right Column --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide pb-2 border-b border-gray-200">Аукцион ва Ғолиб</h3>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="py-2 text-gray-600 w-40">Аукцион тури</td>
                                <td class="py-2 text-gray-900">{{ $yer->auksion_turi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Асос</td>
                                <td class="py-2 text-gray-900">{{ $yer->asos ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Ҳолат</td>
                                <td class="py-2 text-gray-900">{{ Str::limit($yer->holat, 40) ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Ғолиб</td>
                                <td class="py-2 font-medium text-gray-900">{{ $yer->golib_nomi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Ғолиб тури</td>
                                <td class="py-2 text-gray-900">{{ $yer->golib_turi ?? '-' }}</td>
                            </tr>
                            @if($yer->telefon)
                            <tr>
                                <td class="py-2 text-gray-600">Телефон</td>
                                <td class="py-2 text-gray-900">
                                    <a href="tel:{{ $yer->telefon }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $yer->telefon }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td class="py-2 text-gray-600">Тўлов тури</td>
                                <td class="py-2">
                                    @if($yer->tolov_turi == 'муддатли эмас')
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-700 text-white rounded">Бир йўла</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-400 text-white rounded">Бўлиб тўлаш</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    @if($yer->shartnoma_sana || $yer->shartnoma_raqam)
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide pt-4 pb-2 border-b border-gray-200">Шартнома</h3>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @if($yer->shartnoma_holati)
                            <tr>
                                <td class="py-2 text-gray-600 w-40">Ҳолати</td>
                                <td class="py-2 text-gray-900">{{ $yer->shartnoma_holati }}</td>
                            </tr>
                            @endif
                            @if($yer->shartnoma_raqam)
                            <tr>
                                <td class="py-2 text-gray-600">Рақами</td>
                                <td class="py-2 font-medium text-gray-900">{{ $yer->shartnoma_raqam }}</td>
                            </tr>
                            @endif
                            @if($yer->shartnoma_sana)
                            <tr>
                                <td class="py-2 text-gray-600">Санаси</td>
                                <td class="py-2 font-medium text-gray-900">{{ $yer->shartnoma_sana->format('d.m.Y') }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tab Content: Financial --}}
        <div id="financial" class="tab-content hidden p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide pb-2 border-b border-gray-200 mb-3">Асосий суммалар</h3>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="py-2 text-gray-600">Ғолиб тўлаган</td>
                                <td class="py-2 text-right font-medium text-gray-900">{{ number_format($yer->golib_tolagan / 1000000, 1) }} млн</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Буюртмачига ўтказилган</td>
                                <td class="py-2 text-right font-medium text-gray-900">{{ number_format($yer->buyurtmachiga_otkazilgan / 1000000, 1) }} млн</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Чегирма</td>
                                <td class="py-2 text-right font-medium text-gray-900">{{ number_format($yer->chegirma / 1000000, 1) }} млн</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Аукцион харажати</td>
                                <td class="py-2 text-right font-medium text-gray-900">{{ number_format($yer->auksion_harajati / 1000000, 1) }} млн</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Ер аукцион харажат</td>
                                <td class="py-2 text-right font-medium text-gray-900">{{ number_format($yer->yer_auksion_harajat / 1000000, 1) }} млн</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide pb-2 border-b border-gray-200 mb-3">Бошқа маълумотлар</h3>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="py-2 text-gray-600">Тушадиган маблағ</td>
                                <td class="py-2 text-right font-medium text-gray-900">{{ number_format($yer->tushadigan_mablagh / 1000000, 1) }} млн</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Давактив жамғармаси</td>
                                <td class="py-2 text-right font-medium text-gray-900">{{ number_format($yer->davaktiv_jamgarmasi / 1000000, 1) }} млн</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Шартнома тушган</td>
                                <td class="py-2 text-right font-medium text-gray-900">{{ number_format($yer->shartnoma_tushgan / 1000000, 1) }} млн</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">Давактивда турган</td>
                                <td class="py-2 text-right font-medium text-gray-900">{{ number_format($yer->davaktivda_turgan / 1000000, 1) }} млн</td>
                            </tr>
                            @if($yer->farqi)
                            <tr class="bg-gray-50">
                                <td class="py-2 text-gray-900 font-semibold">Фарқи</td>
                                <td class="py-2 text-right font-bold text-gray-900">{{ number_format($yer->farqi / 1000000, 1) }} млн</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab Content: Budget Distribution --}}
        <div id="budget" class="tab-content hidden p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Категория</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Тушадиган</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Тақсимланган</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Қолдиқ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">Тошкент шаҳар бюджети</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->mahalliy_byudjet_tushadigan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->mahalliy_byudjet_taqsimlangan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $yer->qoldiq_mahalliy_byudjet > 0 ? 'text-red-700' : 'text-green-700' }}">
                                {{ number_format($yer->qoldiq_mahalliy_byudjet / 1000000, 1) }} млн
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">Жамғарма</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->jamgarma_tushadigan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->jamgarma_taqsimlangan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $yer->qoldiq_jamgarma > 0 ? 'text-red-700' : 'text-green-700' }}">
                                {{ number_format($yer->qoldiq_jamgarma / 1000000, 1) }} млн
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">Янги Ўзбекистон</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->yangi_oz_direksiya_tushadigan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->yangi_oz_direksiya_taqsimlangan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $yer->qoldiq_yangi_oz_direksiya > 0 ? 'text-red-700' : 'text-green-700' }}">
                                {{ number_format($yer->qoldiq_yangi_oz_direksiya / 1000000, 1) }} млн
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">Шайхонтоҳур тумани</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->shayxontohur_tushadigan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->shayxontohur_taqsimlangan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $yer->qoldiq_shayxontohur > 0 ? 'text-red-700' : 'text-green-700' }}">
                                {{ number_format($yer->qoldiq_shayxontohur / 1000000, 1) }} млн
                            </td>
                        </tr>


                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">Янгиҳаёт индустриал технопаки</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->yangi_hayot_industrial_park_tushadigan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->yangi_hayot_industrial_park_taqsimlangan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $yer->qoldiq_mahalliy_byudjet > 0 ? 'text-red-700' : 'text-green-700' }}">
                                {{ number_format($yer->qoldiq_mahalliy_byudjet / 1000000, 1) }} млн
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">КСЗ дирекциялари</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->ksz_direksiyalari_tushadigan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->ksz_direksiyalari_taqsimlangan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $yer->qoldiq_jamgarma > 0 ? 'text-red-700' : 'text-green-700' }}">
                                {{ number_format($yer->qoldiq_jamgarma / 1000000, 1) }} млн
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">Тошкент сити дирекцияси</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->toshkent_city_direksiya_tushadigan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->toshkent_city_direksiya_taqsimlangan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $yer->qoldiq_yangi_oz_direksiya > 0 ? 'text-red-700' : 'text-green-700' }}">
                                {{ number_format($yer->qoldiq_yangi_oz_direksiya / 1000000, 1) }} млн
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">Туманлар бюжети</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->tuman_byudjeti_tushadigan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ number_format($yer->tuman_byudjeti_taqsimlangan / 1000000, 1) }} млн</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $yer->qoldiq_shayxontohur > 0 ? 'text-red-700' : 'text-green-700' }}">
                                {{ number_format($yer->qoldiq_shayxontohur / 1000000, 1) }} млн
                            </td>
                        </tr>

                        <tr class="bg-gray-100 font-semibold">
                            <td class="px-4 py-3 text-gray-900">ЖАМИ</td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                {{ number_format(($yer->mahalliy_byudjet_tushadigan + $yer->jamgarma_tushadigan + $yer->yangi_oz_direksiya_tushadigan + $yer->shayxontohur_tushadigan) / 1000000, 1) }} млн
                            </td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                {{ number_format(($yer->mahalliy_byudjet_taqsimlangan + $yer->jamgarma_taqsimlangan + $yer->yangi_oz_direksiya_taqsimlangan + $yer->shayxontohur_taqsimlangan) / 1000000, 1) }} млн
                            </td>
                            <td class="px-4 py-3 text-right text-gray-900">
                                {{ number_format(($yer->qoldiq_mahalliy_byudjet + $yer->qoldiq_jamgarma + $yer->qoldiq_yangi_oz_direksiya + $yer->qoldiq_shayxontohur) / 1000000, 1) }} млн
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab Content: Payments --}}
        <div id="payment" class="tab-content hidden p-6">
            @php
                $grafikJami = $yer->grafikTolovlar->sum('grafik_summa');
                $faktJami = $yer->faktTolovlar->sum('tolov_summa');
                $qarzdorlik = $grafikJami - $faktJami;
                $foiz = $grafikJami > 0 ? round(($faktJami / $grafikJami) * 100, 1) : 0;
                $hasPaymentData = $yer->grafikTolovlar->count() > 0 || $yer->faktTolovlar->count() > 0;
            @endphp

            @if(!$hasPaymentData)
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="mt-3 text-sm text-gray-600">Тўлов маълумотлари мавжуд эмас</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $yer->holat ?? 'Маълум эмас' }}</p>
                </div>
            @else
                {{-- Payment Summary --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    <div class="bg-gray-50 rounded p-3 border border-gray-200 text-center">
                        <div class="text-xs text-gray-600 mb-1">Графикда</div>
                        <div class="text-lg font-bold text-gray-900">{{ number_format($grafikJami / 1000000, 1) }}</div>
                        <div class="text-xs text-gray-500">млн</div>
                    </div>
                    <div class="bg-gray-700 rounded p-3 text-center text-white">
                        <div class="text-xs mb-1">Тўланган</div>
                        <div class="text-lg font-bold">{{ number_format($faktJami / 1000000, 1) }}</div>
                        <div class="text-xs">млн</div>
                    </div>
                    <div class="bg-gray-50 rounded p-3 border border-gray-200 text-center">
                        <div class="text-xs text-gray-600 mb-1">Қарздорлик</div>
                        <div class="text-lg font-bold text-gray-900">{{ number_format($qarzdorlik / 1000000, 1) }}</div>
                        <div class="text-xs text-gray-500">млн</div>
                    </div>
                    <div class="bg-gray-800 rounded p-3 text-center text-white">
                        <div class="text-xs mb-1">Фоиз</div>
                        <div class="text-lg font-bold">{{ $foiz }}%</div>
                        <div class="text-xs">бажарилди</div>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="mb-4 bg-gray-100 rounded p-3">
                    <div class="flex justify-between text-xs text-gray-600 mb-2">
                        <span>График бажарилиши</span>
                        <span>{{ $foiz }}%</span>
                    </div>
                    <div class="w-full bg-gray-300 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $foiz >= 80 ? 'bg-gray-700' : 'bg-gray-500' }}" style="width: {{ min($foiz, 100) }}%"></div>
                    </div>
                </div>

                {{-- Collapsible Sections --}}
                <div class="space-y-2">
                    @if(isset($tolovTaqqoslash) && count($tolovTaqqoslash) > 0)
                    <details class="group border border-gray-200 rounded">
                        <summary class="cursor-pointer px-4 py-3 bg-gray-50 hover:bg-gray-100 flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900">Ойлик таққослаш</span>
                            <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </summary>
                        <div class="p-4 border-t border-gray-200">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-xs">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left font-medium text-gray-700">Ой</th>
                                            <th class="px-3 py-2 text-right font-medium text-gray-700">График</th>
                                            <th class="px-3 py-2 text-right font-medium text-gray-700">Факт</th>
                                            <th class="px-3 py-2 text-center font-medium text-gray-700">%</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($tolovTaqqoslash as $tolov)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2 text-gray-900">{{ $tolov['oy_nomi'] }} {{ $tolov['yil'] }}</td>
                                            <td class="px-3 py-2 text-right text-gray-900">{{ number_format($tolov['grafik'] / 1000000, 1) }}</td>
                                            <td class="px-3 py-2 text-right {{ $tolov['fakt'] > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                                                {{ number_format($tolov['fakt'] / 1000000, 1) }}
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <span class="px-2 py-1 rounded text-xs {{ $tolov['foiz'] >= 100 ? 'bg-gray-700 text-white' : 'bg-gray-200 text-gray-800' }}">
                                                    {{ $tolov['foiz'] }}%
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </details>
                    @endif

                    @if($yer->faktTolovlar->count() > 0)
                    <details class="group border border-gray-200 rounded">
                        <summary class="cursor-pointer px-4 py-3 bg-gray-50 hover:bg-gray-100 flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900">Тўлов тарихи ({{ $yer->faktTolovlar->count() }})</span>
                            <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </summary>
                        <div class="p-4 border-t border-gray-200">
                            <div class="space-y-2">
                                @foreach($yer->faktTolovlar->sortByDesc('tolov_sana')->take(10) as $tolov)
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $tolov->tolov_sana->format('d.m.Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($tolov->tolash_nom, 30) }}</div>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ number_format($tolov->tolov_summa / 1000000, 1) }} млн
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </details>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function openTab(evt, tabName) {
    // Hide all tab contents
    var tabContents = document.getElementsByClassName("tab-content");
    for (var i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.add("hidden");
    }

    // Remove active class from all buttons
    var tabButtons = document.getElementsByClassName("tab-button");
    for (var i = 0; i < tabButtons.length; i++) {
        tabButtons[i].classList.remove("active", "border-gray-700", "text-gray-900");
        tabButtons[i].classList.add("border-transparent", "text-gray-600");
    }

    // Show current tab and mark button as active
    document.getElementById(tabName).classList.remove("hidden");
    evt.currentTarget.classList.add("active", "border-gray-700", "text-gray-900");
    evt.currentTarget.classList.remove("border-transparent", "text-gray-600");
}
</script>
@endsection
