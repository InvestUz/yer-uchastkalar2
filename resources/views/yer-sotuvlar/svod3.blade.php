@extends('layouts.app')

@section('title', 'DEBUG: Бўлиб тўлаш шарти билан сотилган ерлар')

@section('content')
<div class="container-fluid py-4">

    {{-- DEBUG SECTION START --}}
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">🔍 DEBUG INFORMATION - DETAILED CALCULATION LOG</h5>
        </div>
        <div class="card-body" style="font-family: monospace; font-size: 11px;">

            <h6 class="text-primary">═══════════════════════════════════════════════════════════</h6>
            <h6 class="text-primary">JAMI (TOTAL) VALUES</h6>
            <h6 class="text-primary">═══════════════════════════════════════════════════════════</h6>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-success mt-3">📊 NARHINI BO'LIB</h6>
                    <table class="table table-sm table-bordered">
                        <tr><td>soni</td><td class="text-end"><strong>{{ $statistics['jami']['narhini_bolib']['soni'] }}</strong></td></tr>
                        <tr><td>maydoni</td><td class="text-end">{{ number_format($statistics['jami']['narhini_bolib']['maydoni'], 4) }}</td></tr>
                        <tr><td>boshlangich_narx</td><td class="text-end">{{ number_format($statistics['jami']['narhini_bolib']['boshlangich_narx'], 2) }}</td></tr>
                        <tr><td>sotilgan_narx</td><td class="text-end">{{ number_format($statistics['jami']['narhini_bolib']['sotilgan_narx'], 2) }}</td></tr>
                        <tr class="table-warning">
                            <td><strong>tushadigan_mablagh (B)</strong></td>
                            <td class="text-end"><strong>{{ number_format($statistics['jami']['narhini_bolib']['tushadigan_mablagh'], 2) }}</strong></td>
                        </tr>
                        <tr class="table-info">
                            <td>млрд сўм</td>
                            <td class="text-end"><strong>{{ number_format($statistics['jami']['narhini_bolib']['tushadigan_mablagh'] / 1000000000, 1) }}</strong></td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h6 class="text-success mt-3">✅ TO'LIQ TO'LANGANLAR</h6>
                    <table class="table table-sm table-bordered">
                        <tr><td>soni</td><td class="text-end"><strong>{{ $statistics['jami']['toliq_tolanganlar']['soni'] }}</strong></td></tr>
                        <tr><td>maydoni</td><td class="text-end">{{ number_format($statistics['jami']['toliq_tolanganlar']['maydoni'], 4) }}</td></tr>
                        <tr><td>boshlangich_narx</td><td class="text-end">{{ number_format($statistics['jami']['toliq_tolanganlar']['boshlangich_narx'], 2) }}</td></tr>
                        <tr><td>sotilgan_narx</td><td class="text-end">{{ number_format($statistics['jami']['toliq_tolanganlar']['sotilgan_narx'], 2) }}</td></tr>
                        <tr class="table-primary">
                            <td><strong>tushadigan_mablagh (T)</strong></td>
                            <td class="text-end"><strong>{{ number_format($statistics['jami']['toliq_tolanganlar']['tushadigan_mablagh'], 2) }}</strong></td>
                        </tr>
                        <tr class="table-warning">
                            <td><strong>tushgan_summa (B)</strong></td>
                            <td class="text-end"><strong>{{ number_format($statistics['jami']['toliq_tolanganlar']['tushgan_summa'], 2) }}</strong></td>
                        </tr>
                        <tr class="table-info">
                            <td>млрд сўм (T)</td>
                            <td class="text-end">{{ number_format($statistics['jami']['toliq_tolanganlar']['tushadigan_mablagh'] / 1000000000, 1) }}</td>
                        </tr>
                        <tr class="table-info">
                            <td>млрд сўм (B)</td>
                            <td class="text-end"><strong>{{ number_format($statistics['jami']['toliq_tolanganlar']['tushgan_summa'] / 1000000000, 1) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-success mt-3">⏳ NAZORATDAGILAR</h6>
                    <table class="table table-sm table-bordered">
                        <tr><td>soni</td><td class="text-end"><strong>{{ $statistics['jami']['nazoratdagilar']['soni'] }}</strong></td></tr>
                        <tr><td>maydoni</td><td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['maydoni'], 4) }}</td></tr>
                        <tr><td>boshlangich_narx</td><td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['boshlangich_narx'], 2) }}</td></tr>
                        <tr><td>sotilgan_narx</td><td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['sotilgan_narx'], 2) }}</td></tr>
                        <tr class="table-primary">
                            <td><strong>tushadigan_mablagh (T)</strong></td>
                            <td class="text-end"><strong>{{ number_format($statistics['jami']['nazoratdagilar']['tushadigan_mablagh'], 2) }}</strong></td>
                        </tr>
                        <tr class="table-warning">
                            <td><strong>tushgan_summa (B)</strong></td>
                            <td class="text-end"><strong>{{ number_format($statistics['jami']['nazoratdagilar']['tushgan_summa'], 2) }}</strong></td>
                        </tr>
                        <tr><td>grafik_summa</td><td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['grafik_summa'], 2) }}</td></tr>
                        <tr><td>fakt_summa</td><td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['fakt_summa'], 2) }}</td></tr>
                        <tr class="table-info">
                            <td>млрд сўм (T)</td>
                            <td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['tushadigan_mablagh'] / 1000000000, 1) }}</td>
                        </tr>
                        <tr class="table-info">
                            <td>млрд сўм (B)</td>
                            <td class="text-end"><strong>{{ number_format($statistics['jami']['nazoratdagilar']['tushgan_summa'] / 1000000000, 1) }}</strong></td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h6 class="text-success mt-3">⚠️ GRAFIK ORTDA</h6>
                    <table class="table table-sm table-bordered">
                        <tr><td>soni</td><td class="text-end"><strong>{{ $statistics['jami']['grafik_ortda']['soni'] }}</strong></td></tr>
                        <tr><td>maydoni</td><td class="text-end">{{ number_format($statistics['jami']['grafik_ortda']['maydoni'], 4) }}</td></tr>
                        <tr><td>grafik_summa</td><td class="text-end">{{ number_format($statistics['jami']['grafik_ortda']['grafik_summa'], 2) }}</td></tr>
                        <tr><td>fakt_summa</td><td class="text-end">{{ number_format($statistics['jami']['grafik_ortda']['fakt_summa'], 2) }}</td></tr>
                        <tr><td>farq_summa</td><td class="text-end">{{ number_format($statistics['jami']['grafik_ortda']['grafik_summa'] - $statistics['jami']['grafik_ortda']['fakt_summa'], 2) }}</td></tr>
                        <tr><td>foiz</td><td class="text-end">{{ number_format($statistics['jami']['grafik_ortda']['foiz'], 1) }}%</td></tr>
                    </table>
                </div>
            </div>

            <h6 class="text-primary mt-4">═══════════════════════════════════════════════════════════</h6>
            <h6 class="text-primary">TUMANLAR (DISTRICTS) - FIRST 3 EXAMPLES</h6>
            <h6 class="text-primary">═══════════════════════════════════════════════════════════</h6>

            @foreach($statistics['tumanlar'] as $index => $tuman)
                @if($index < 3)
                <div class="card mb-3 border-primary">
                    <div class="card-header bg-primary text-white">
                        <strong>{{ $index + 1 }}. {{ $tuman['tuman'] }}</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h6 class="text-success">📊 NARHINI BO'LIB</h6>
                                <table class="table table-sm table-bordered">
                                    <tr><td>soni</td><td class="text-end">{{ $tuman['narhini_bolib']['soni'] }}</td></tr>
                                    <tr><td>maydoni</td><td class="text-end">{{ number_format($tuman['narhini_bolib']['maydoni'], 2) }}</td></tr>
                                    <tr><td>boshlangich_narx</td><td class="text-end">{{ number_format($tuman['narhini_bolib']['boshlangich_narx'], 2) }}</td></tr>
                                    <tr><td>sotilgan_narx</td><td class="text-end">{{ number_format($tuman['narhini_bolib']['sotilgan_narx'], 2) }}</td></tr>
                                    <tr class="table-warning">
                                        <td><strong>tushadigan (B)</strong></td>
                                        <td class="text-end"><strong>{{ number_format($tuman['narhini_bolib']['tushadigan_mablagh'], 2) }}</strong></td>
                                    </tr>
                                    <tr class="table-info">
                                        <td>млрд</td>
                                        <td class="text-end"><strong>{{ number_format($tuman['narhini_bolib']['tushadigan_mablagh'] / 1000000000, 1) }}</strong></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-3">
                                <h6 class="text-success">✅ TO'LIQ</h6>
                                <table class="table table-sm table-bordered">
                                    <tr><td>soni</td><td class="text-end">{{ $tuman['toliq_tolanganlar']['soni'] }}</td></tr>
                                    <tr><td>maydoni</td><td class="text-end">{{ number_format($tuman['toliq_tolanganlar']['maydoni'], 2) }}</td></tr>
                                    <tr><td>boshlangich</td><td class="text-end">{{ number_format($tuman['toliq_tolanganlar']['boshlangich_narx'], 2) }}</td></tr>
                                    <tr><td>sotilgan</td><td class="text-end">{{ number_format($tuman['toliq_tolanganlar']['sotilgan_narx'], 2) }}</td></tr>
                                    <tr class="table-primary">
                                        <td><strong>tushadigan (T)</strong></td>
                                        <td class="text-end"><strong>{{ number_format($tuman['toliq_tolanganlar']['tushadigan_mablagh'], 2) }}</strong></td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td><strong>tushgan (B)</strong></td>
                                        <td class="text-end"><strong>{{ number_format($tuman['toliq_tolanganlar']['tushgan_summa'], 2) }}</strong></td>
                                    </tr>
                                    <tr class="table-info">
                                        <td>млрд (B)</td>
                                        <td class="text-end"><strong>{{ number_format($tuman['toliq_tolanganlar']['tushgan_summa'] / 1000000000, 1) }}</strong></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-3">
                                <h6 class="text-success">⏳ NAZORAT</h6>
                                <table class="table table-sm table-bordered">
                                    <tr><td>soni</td><td class="text-end">{{ $tuman['nazoratdagilar']['soni'] }}</td></tr>
                                    <tr><td>maydoni</td><td class="text-end">{{ number_format($tuman['nazoratdagilar']['maydoni'], 2) }}</td></tr>
                                    <tr><td>boshlangich</td><td class="text-end">{{ number_format($tuman['nazoratdagilar']['boshlangich_narx'], 2) }}</td></tr>
                                    <tr><td>sotilgan</td><td class="text-end">{{ number_format($tuman['nazoratdagilar']['sotilgan_narx'], 2) }}</td></tr>
                                    <tr class="table-primary">
                                        <td><strong>tushadigan (T)</strong></td>
                                        <td class="text-end"><strong>{{ number_format($tuman['nazoratdagilar']['tushadigan_mablagh'], 2) }}</strong></td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td><strong>tushgan (B)</strong></td>
                                        <td class="text-end"><strong>{{ number_format($tuman['nazoratdagilar']['tushgan_summa'], 2) }}</strong></td>
                                    </tr>
                                    <tr><td>grafik</td><td class="text-end">{{ number_format($tuman['nazoratdagilar']['grafik_summa'], 2) }}</td></tr>
                                    <tr><td>fakt</td><td class="text-end">{{ number_format($tuman['nazoratdagilar']['fakt_summa'], 2) }}</td></tr>
                                    <tr class="table-info">
                                        <td>млрд (B)</td>
                                        <td class="text-end"><strong>{{ number_format($tuman['nazoratdagilar']['tushgan_summa'] / 1000000000, 1) }}</strong></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-3">
                                <h6 class="text-success">⚠️ GRAFIK ORTDA</h6>
                                <table class="table table-sm table-bordered">
                                    <tr><td>soni</td><td class="text-end">{{ $tuman['grafik_ortda']['soni'] }}</td></tr>
                                    <tr><td>maydoni</td><td class="text-end">{{ number_format($tuman['grafik_ortda']['maydoni'], 2) }}</td></tr>
                                    <tr><td>grafik</td><td class="text-end">{{ number_format($tuman['grafik_ortda']['grafik_summa'], 2) }}</td></tr>
                                    <tr><td>fakt</td><td class="text-end">{{ number_format($tuman['grafik_ortda']['fakt_summa'], 2) }}</td></tr>
                                    <tr><td>foiz</td><td class="text-end">{{ number_format($tuman['grafik_ortda']['foiz'], 1) }}%</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach

            <div class="alert alert-info mt-3">
                <h6>📝 FORMULA EXPLANATION (CORRECTED):</h6>
                <ul>
                    <li><strong>T</strong> = Тушадиган қиймат = Ғолиб бошланғич аукционга тўлаган сумма (golib_tolagan) + Шартнома бўйича тушадиган (shartnoma_summasi)</li>
                    <li><strong>B</strong> = Тушган қиймат (To'liq to'lov summasi) = T - ВПР Бахтиёр ака (total_fakt_tolovlar) - Аукцион ҳаражати 1 фоиз (auksion_harajati × 0.01)</li>
                    <li><strong>total_fakt_tolovlar</strong> = BARCHA to'lovlar yig'indisi (SUM of ALL payments from fakt_tolovlar table)</li>
                    <li><strong>auksion_harajati × 0.01</strong> = Аукцион ҳаражатининг 1 фоизи (1% of auction cost)</li>
                </ul>
                <div class="mt-3 p-3 bg-light">
                    <strong>FORMULA:</strong><br>
                    <code style="font-size: 14px;">
                        B = T - Fakt - (Auksion_harajati × 0.01)<br>
                        B = (golib_tolagan + shartnoma_summasi) - SUM(fakt_tolovlar) - (auksion_harajati × 1%)
                    </code>
                </div>
            </div>
        </div>
    </div>
    {{-- DEBUG SECTION END --}}

    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h5 class="mb-0 text-dark" style="color: #000; font-weight: bold;">Тошкент шаҳрида аукцион савдоларида бўлиб тўлаш шарти билан сотилган ер участкалари тўғрисида</h5>
            <h6 class="mb-0 mt-1" style="color: #000; font-weight: bold; margin-bottom: 20px;">ЙИҒМА МАЪЛУМОТ</h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0" style="font-size: 9px;">
                    <thead>
                        <!-- Row 1: Main section headers -->
                        <tr class="table-secondary text-center align-middle" style="font-weight: bold;">
                            <th rowspan="4">Т/р</th>
                            <th rowspan="4">Ҳудудлар</th>
                            <th colspan="5">Нархини бўлиб тўлаш шарти билан сотилган</th>
                            <th colspan="11">шундан, {{ now()->format('d.m.Y') }} ҳолатига</th>
                            <th colspan="5">шундан, гр.ортда қолганлар</th>
                        </tr>

                        <!-- Row 2: Sub-section headers -->
                        <tr class="table-secondary text-center align-middle" style="font-weight: bold;">
                            <!-- Narhini bo'lib to'lash - 5 columns -->
                            <th rowspan="3">сони</th>
                            <th rowspan="3">майдони<br>(га)</th>
                            <th rowspan="3">бошланғич нархи<br>(млрд сўм)</th>
                            <th rowspan="3">сотилган нархи<br>(млрд сўм)</th>
                            <th colspan="1">шундан</th>

                            <!-- Shundan holatiga - 11 columns -->
                            <th colspan="5">тўлиқ тўланганлар</th>
                            <th colspan="6">назоратдагилар</th>

                            <!-- Grafik ortda - 5 columns -->
                            <th rowspan="3">сони</th>
                            <th rowspan="3">майдони<br>(га)</th>
                            <th colspan="3">шундан</th>
                        </tr>

                        <!-- Row 3: More detailed sub-headers -->
                        <tr class="table-secondary text-center align-middle" style="font-weight: bold;">
                            <!-- Under "shundan" of Narhini bo'lib -->
                            <th rowspan="2">тушадиган қиймат<br>(млрд сўм)</th>

                            <!-- Toliq tolanganlar - 5 columns -->
                            <th rowspan="2">сони</th>
                            <th rowspan="2">майдони<br>(га)</th>
                            <th rowspan="2">бошланғич нархи<br>(млрд сўм)</th>
                            <th rowspan="2">сотилган нархи<br>(млрд сўм)</th>
                            <th colspan="1">шундан</th>

                            <!-- Nazoratdagilar - 6 columns -->
                            <th rowspan="2">сони</th>
                            <th rowspan="2">майдони<br>(га)</th>
                            <th rowspan="2">бошланғич нархи<br>(млрд сўм)</th>
                            <th rowspan="2">сотилган нархи<br>(млрд сўм)</th>
                            <th colspan="2">шундан</th>

                            <!-- Under "shundan" of Grafik ortda -->
                            <th rowspan="2">график б-ча<br>тўлов суммаси<br>(млрд сўм)</th>
                            <th rowspan="2">амалда тўлов<br>суммаси<br>(млрд сўм)</th>
                            <th rowspan="2">%</th>
                        </tr>

                        <!-- Row 4: Bottom level details -->
                        <tr class="table-secondary text-center align-middle" style="font-weight: bold;">
                            <!-- Under "shundan" of Toliq tolanganlar -->
                            <th>тушган қиймат<br>(млрд сўм)</th>

                            <!-- Under "shundan" of Nazoratdagilar -->
                            <th>тушадиган қиймат<br>(млрд сўм)</th>
                            <th>тушган қиймат<br>(млрд сўм)</th>
                        </tr>
                    </thead>

                    <tbody>
                        <!-- Jami row -->
                        <tr class="table-warning fw-bold" style="font-weight: bold">
                            <td class="text-center" colspan="2">жами:</td>

                            <!-- Narhini bolib tolash - 5 columns -->
                            <td class="text-end">
                                <a href="{{ route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли']) }}" class="text-decoration-none text-dark">
                                    {{ $statistics['jami']['narhini_bolib']['soni'] }}
                                </a>
                            </td>
                            <td class="text-end">{{ number_format($statistics['jami']['narhini_bolib']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['narhini_bolib']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['narhini_bolib']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['narhini_bolib']['tushadigan_mablagh'] / 1000000000, 1) }}</td>

                            <!-- Toliq tolanganlar - 5 columns -->
                            <td class="text-end">
                                <a href="{{ route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли', 'toliq_tolangan' => 'true']) }}" class="text-decoration-none text-dark">
                                    {{ $statistics['jami']['toliq_tolanganlar']['soni'] }}
                                </a>
                            </td>
                            <td class="text-end">{{ number_format($statistics['jami']['toliq_tolanganlar']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['toliq_tolanganlar']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['toliq_tolanganlar']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format(($statistics['jami']['toliq_tolanganlar']['tushgan_summa'] ?? 0) / 1000000000, 1) }}</td>

                            <!-- Nazoratdagilar - 6 columns -->
                            <td class="text-end">
                                <a href="{{ route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли', 'nazoratda' => 'true']) }}" class="text-decoration-none text-dark">
                                    {{ $statistics['jami']['nazoratdagilar']['soni'] }}
                                </a>
                            </td>
                            <td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['tushadigan_mablagh'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format(($statistics['jami']['nazoratdagilar']['tushgan_summa'] ?? 0) / 1000000000, 1) }}</td>

                            <!-- Grafik ortda - 5 columns -->
                            <td class="text-end">
                                <a href="{{ route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли', 'grafik_ortda' => 'true']) }}" class="text-decoration-none text-dark">
                                    {{ $statistics['jami']['grafik_ortda']['soni'] }}
                                </a>
                            </td>
                            <td class="text-end">{{ number_format($statistics['jami']['grafik_ortda']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['grafik_ortda']['grafik_summa'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['grafik_ortda']['fakt_summa'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['grafik_ortda']['foiz'], 1) }}</td>
                        </tr>

                        <!-- Tumanlar -->
                        @foreach($statistics['tumanlar'] as $index => $tuman)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $tuman['tuman'] }}</td>

                            <!-- Narhini bolib tolash - 5 columns -->
                            <td class="text-end">
                                @if($tuman['narhini_bolib']['soni'] > 0)
                                    <a href="{{ route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли']) }}" class="text-decoration-none">
                                        {{ $tuman['narhini_bolib']['soni'] }}
                                    </a>
                                @else
                                    0
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($tuman['narhini_bolib']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($tuman['narhini_bolib']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['narhini_bolib']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['narhini_bolib']['tushadigan_mablagh'] / 1000000000, 1) }}</td>

                            <!-- Toliq tolanganlar - 5 columns -->
                            <td class="text-end">
                                @if($tuman['toliq_tolanganlar']['soni'] > 0)
                                    <a href="{{ route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли', 'toliq_tolangan' => 'true']) }}" class="text-decoration-none">
                                        {{ $tuman['toliq_tolanganlar']['soni'] }}
                                    </a>
                                @else
                                    0
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($tuman['toliq_tolanganlar']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($tuman['toliq_tolanganlar']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['toliq_tolanganlar']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format(($tuman['toliq_tolanganlar']['tushgan_summa'] ?? 0) / 1000000000, 1) }}</td>

                            <!-- Nazoratdagilar - 6 columns -->
                            <td class="text-end">
                                @if($tuman['nazoratdagilar']['soni'] > 0)
                                    <a href="{{ route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли', 'nazoratda' => 'true']) }}" class="text-decoration-none">
                                        {{ $tuman['nazoratdagilar']['soni'] }}
                                    </a>
                                @else
                                    0
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($tuman['nazoratdagilar']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($tuman['nazoratdagilar']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['nazoratdagilar']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['nazoratdagilar']['tushadigan_mablagh'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format(($tuman['nazoratdagilar']['tushgan_summa'] ?? 0) / 1000000000, 1) }}</td>

                            <!-- Grafik ortda - 5 columns -->
                            <td class="text-end">
                                @if($tuman['grafik_ortda']['soni'] > 0)
                                    <a href="{{ route('yer-sotuvlar.list', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли', 'grafik_ortda' => 'true']) }}" class="text-decoration-none">
                                        {{ $tuman['grafik_ortda']['soni'] }}
                                    </a>
                                @else
                                    0
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($tuman['grafik_ortda']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($tuman['grafik_ortda']['grafik_summa'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['grafik_ortda']['fakt_summa'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['grafik_ortda']['foiz'], 1) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #000 !important;
        padding: 4px 6px !important;
    }

    .table thead th {
        background-color: #e9ecef;
    }

    a {
        color: #0d6efd;
    }

    a:hover {
        text-decoration: underline !important;
        color: #0a58ca;
    }
</style>
@endsection
