@extends('layouts.app')

@section('title', 'Бўлиб тўлаш шарти билан сотилган ерлар')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h5 class="mb-0">Тошкент шаҳрида аукцион савдоларида бўлиб тўлаш шарти билан сотилган ер участкалари тўғрисида</h5>
            <h6 class="mb-0 mt-1">ЙИҒМА МАЪЛУМОТ</h6>
            <small>шундан, {{ now()->format('d.m.Y') }} ҳолатига</small>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0" style="font-size: 10px;">
                    <thead>
                        <!-- Row 1: Main headers -->
                        <tr class="table-secondary text-center align-middle" style="font-weight: bold;">
                            <th rowspan="3">Т/р</th>
                            <th rowspan="3">Ҳудудлар</th>
                            <th colspan="5">Нархини бўлиб тўлаш шарти билан сотилган</th>
                            <th colspan="5">шундан тўлиқ тўланганлар</th>
                            <th colspan="8">назоратдагилар</th>
                            <th colspan="6">шундан, гр.ортда қолганлар</th>
                        </tr>

                        <!-- Row 2: Sub headers -->
                        <tr class="table-secondary text-center align-middle" style="font-weight: bold;">
                            <!-- Narhini bolib tolash -->
                            <th rowspan="2">сони</th>
                            <th rowspan="2">майдони<br>(га)</th>
                            <th rowspan="2">бошланғич нархи<br>(млрд сўм)</th>
                            <th rowspan="2">сотилган нархи<br>(млрд сўм)</th>
                            <th rowspan="2">тушадиган қиймат<br>(млрд сўм)</th>

                            <!-- Toliq tolanganlar -->
                            <th rowspan="2">сони</th>
                            <th rowspan="2">майдони<br>(га)</th>
                            <th rowspan="2">бошланғич нархи<br>(млрд сўм)</th>
                            <th rowspan="2">сотилган нархи<br>(млрд сўм)</th>
                            <th rowspan="2">тушадиган қиймат<br>(млрд сўм)</th>

                            <!-- Nazoratdagilar -->
                            <th rowspan="2">сони</th>
                            <th rowspan="2">майдони<br>(га)</th>
                            <th rowspan="2">бошланғич нархи<br>(млрд сўм)</th>
                            <th rowspan="2">сотилган нархи<br>(млрд сўм)</th>
                            <th colspan="3">шундан</th>
                            <th rowspan="2">тушадиган қиймат<br>(млрд сўм)</th>

                            <!-- Grafik ortda -->
                            <th rowspan="2">сони</th>
                            <th rowspan="2">майдони<br>(га)</th>
                            <th colspan="3">график б-ча тўлов суммаси<br>(млрд сўм)</th>
                            <th rowspan="2">%</th>
                        </tr>

                        <!-- Row 3: Detailed headers -->
                        <tr class="table-secondary text-center align-middle" style="font-weight: bold;">
                            <!-- Nazoratdagilar - shundan -->
                            <th>назоратдагилар<br>(млрд сўм)</th>
                            <th>сотилган нархи<br>(млрд сўм)</th>
                            <th>тушган қиймат<br>(млрд сўм)</th>

                            <!-- Grafik ortda - grafik summa -->
                            <th>тушган<br>(млрд сўм)</th>
                            <th>график б-ча<br>(млрд сўм)</th>
                            <th>амалда тўлов<br>(млрд сўм)</th>
                        </tr>
                    </thead>

                    <tbody>
                        <!-- Jami row -->
                        <tr class="table-warning fw-bold">
                            <td class="text-center">жами:</td>
                            <td></td>

                            <!-- Narhini bolib tolash -->
                            <td class="text-end">
                                <a href="{{ route('yer-sotuvlar.list', ['tolov_turi' => 'муддатли']) }}" class="text-decoration-none text-dark">
                                    {{ $statistics['jami']['narhini_bolib']['soni'] }}
                                </a>
                            </td>
                            <td class="text-end">{{ number_format($statistics['jami']['narhini_bolib']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['narhini_bolib']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['narhini_bolib']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['narhini_bolib']['tushadigan_mablagh'] / 1000000000, 1) }}</td>

                            <!-- Toliq tolanganlar -->
                            <td class="text-end">{{ $statistics['jami']['toliq_tolanganlar']['soni'] }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['toliq_tolanganlar']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['toliq_tolanganlar']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['toliq_tolanganlar']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['toliq_tolanganlar']['tushadigan_mablagh'] / 1000000000, 1) }}</td>

                            <!-- Nazoratdagilar -->
                            <td class="text-end">{{ $statistics['jami']['nazoratdagilar']['soni'] }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['grafik_summa'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['tushgan_summa'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['nazoratdagilar']['tushadigan_mablagh'] / 1000000000, 1) }}</td>

                            <!-- Grafik ortda -->
                            <td class="text-end">{{ $statistics['jami']['grafik_ortda']['soni'] }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['grafik_ortda']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format(($statistics['jami']['grafik_ortda']['grafik_summa'] - $statistics['jami']['grafik_ortda']['fakt_summa']) / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['grafik_ortda']['grafik_summa'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['grafik_ortda']['fakt_summa'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['grafik_ortda']['foiz'], 1) }}</td>
                        </tr>

                        <!-- Tumanlar -->
                        @foreach($statistics['tumanlar'] as $index => $tuman)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $tuman['tuman'] }}</td>

                            <!-- Narhini bolib tolash -->
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

                            <!-- Toliq tolanganlar -->
                            <td class="text-end">{{ $tuman['toliq_tolanganlar']['soni'] }}</td>
                            <td class="text-end">{{ number_format($tuman['toliq_tolanganlar']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($tuman['toliq_tolanganlar']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['toliq_tolanganlar']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['toliq_tolanganlar']['tushadigan_mablagh'] / 1000000000, 1) }}</td>

                            <!-- Nazoratdagilar -->
                            <td class="text-end">{{ $tuman['nazoratdagilar']['soni'] }}</td>
                            <td class="text-end">{{ number_format($tuman['nazoratdagilar']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($tuman['nazoratdagilar']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['nazoratdagilar']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['nazoratdagilar']['grafik_summa'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['nazoratdagilar']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['nazoratdagilar']['tushgan_summa'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['nazoratdagilar']['tushadigan_mablagh'] / 1000000000, 1) }}</td>

                            <!-- Grafik ortda -->
                            <td class="text-end">{{ $tuman['grafik_ortda']['soni'] }}</td>
                            <td class="text-end">{{ number_format($tuman['grafik_ortda']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format(($tuman['grafik_ortda']['grafik_summa'] - $tuman['grafik_ortda']['fakt_summa']) / 1000000000, 1) }}</td>
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
        padding: 3px 5px !important;
    }

    .table thead th {
        background-color: #e9ecef;
        vertical-align: middle;
    }

    a {
        color: #0d6efd;
    }

    a:hover {
        text-decoration: underline !important;
        color: #0a58ca;
    }

    .align-middle {
        vertical-align: middle !important;
    }
</style>
@endsection
