@extends('layouts.app')

@section('title', 'Йиғма маълумот')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0" style="color: #000; font-weight: bold;">Тошкент шаҳрида аукцион савдоларида сотилган ер участкалари тўғрисида</h4>
            <h5 class="mb-0 mt-1" style="color: #000; font-weight: bold; margin-bottom: 20px;">ЙИҒМА МАЪЛУМОТ</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0" style="font-size: 11px;">
                    <thead>
                        <!-- Row 1: Main headers -->
                        <tr class="table-secondary text-center" style="font-weight: bold;">
                            <th rowspan="3" style="vertical-align: middle;">Т/р</th>
                            <th rowspan="3" style="vertical-align: middle;">Ҳудудлар</th>
                            <th colspan="6">Сотилган ер участкалар</th>
                            <th colspan="12">шундан</th>
                            <th colspan="4">Аукционда сотилган ва савдо натижасини расмийлаштишда турган ерлар</th>
                            <th colspan="2">Мулкни қабул қилиб олиш тугмаси босилмаган ерлар</th>
                        </tr>

                        <!-- Row 2: Sub headers -->
                        <tr class="table-secondary text-center" style="font-weight: bold;">
                            <!-- Sotilgan yer uchastkalar -->
                            <th rowspan="2" style="vertical-align: middle;">сони</th>
                            <th rowspan="2" style="vertical-align: middle;">майдони<br>(га)</th>
                            <th rowspan="2" style="vertical-align: middle;">бошланғич нархи<br>(млрд сўм)</th>
                            <th rowspan="2" style="vertical-align: middle;">сотилган нархи<br>(млрд сўм)</th>
                            <th colspan="2">шундан</th>

                            <!-- Bir yo'la to'lash -->
                            <th colspan="6">Бир йўла тўлаш шарти билан сотилган</th>

                            <!-- Bo'lib to'lash -->
                            <th colspan="6">Нархини бўлиб тўлаш шарти билан сотилган</th>

                            <!-- Auksonda turgan -->
                            <th rowspan="2" style="vertical-align: middle;">сони</th>
                            <th rowspan="2" style="vertical-align: middle;">майдони<br>(га)</th>
                            <th rowspan="2" style="vertical-align: middle;">бошланғич нархи<br>(млрд сўм)</th>
                            <th rowspan="2" style="vertical-align: middle;">сотилган нархи<br>(млрд сўм)</th>

                            <!-- Mulk qabul qilmagan -->
                            <th rowspan="2" style="vertical-align: middle;">сони</th>
                            <th rowspan="2" style="vertical-align: middle;">Аукционда турган маблағ<br>(млрд сўм)</th>
                        </tr>

                        <!-- Row 3: Detailed headers -->
                        <tr class="table-secondary text-center" style="font-weight: bold;">
                            <!-- Sotilgan - shundan -->
                            <th>Чегирма қиймати<br>(млрд сўм)</th>
                            <th>Сотилган ер тўлови бўйича тушадиган қиймат<br>(млрд сўм)</th>

                            <!-- Bir yo'la -->
                            <th>сони</th>
                            <th>майдони<br>(га)</th>
                            <th>бошланғич нархи<br>(млрд сўм)</th>
                            <th>сотилган нархи<br>(млрд сўм)</th>
                            <th>Чегирма қиймати<br>(млрд сўм)</th>
                            <th>Сотилган ер тўлови бўйича тушадиган қиймат<br>(млрд сўм)</th>

                            <!-- Bo'lib to'lash -->
                            <th>сони</th>
                            <th>майдони<br>(га)</th>
                            <th>бошланғич нархи<br>(млрд сўм)</th>
                            <th>сотилган нархи<br>(млрд сўм)</th>
                            <th>Чегирма қиймати<br>(млрд сўм)</th>
                            <th>Сотилган ер тўлови бўйича тушадиган қиймат<br>(млрд сўм)</th>
                        </tr>
                    </thead>

                    <tbody>
                        <!-- Jami row -->
                        <tr class="table-warning fw-bold"  style="font-weight: bold">
                                                   <td class="text-center" colspan="2">жами:</td>


                            <!-- Jami sotilgan -->
                            <td class="text-end">
                                <a href="{{ route('yer-sotuvlar.index') }}" class="text-decoration-none text-dark">
                                    {{ $statistics['jami']['jami']['soni'] }}
                                </a>
                            </td>
                            <td class="text-end">{{ number_format($statistics['jami']['jami']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['jami']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['jami']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['jami']['chegirma'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['jami']['tushadigan_mablagh'] / 1000000000, 1) }}</td>

                            <!-- Bir yo'la -->
                            <td class="text-end">
                                <a href="{{ route('yer-sotuvlar.index', ['tolov_turi' => 'муддатли эмас']) }}" class="text-decoration-none text-dark">
                                    {{ $statistics['jami']['bir_yola']['soni'] }}
                                </a>
                            </td>
                            <td class="text-end">{{ number_format($statistics['jami']['bir_yola']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['bir_yola']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['bir_yola']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['bir_yola']['chegirma'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['bir_yola']['tushadigan_mablagh'] / 1000000000, 1) }}</td>

                            <!-- Bo'lib to'lash -->
                            <td class="text-end">
                                <a href="{{ route('yer-sotuvlar.index', ['tolov_turi' => 'муддатли']) }}" class="text-decoration-none text-dark">
                                    {{ $statistics['jami']['bolib']['soni'] }}
                                </a>
                            </td>
                            <td class="text-end">{{ number_format($statistics['jami']['bolib']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['bolib']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['bolib']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['bolib']['chegirma'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['bolib']['tushadigan_mablagh'] / 1000000000, 1) }}</td>

                            <!-- Auksonda turgan -->
                            <td class="text-end">
                                <a href="{{ route('yer-sotuvlar.index', ['auksonda_turgan' => 'true']) }}" class="text-decoration-none text-dark">
                                    {{ $statistics['jami']['auksonda']['soni'] }}
                                </a>
                            </td>
                            <td class="text-end">{{ number_format($statistics['jami']['auksonda']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['auksonda']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($statistics['jami']['auksonda']['sotilgan_narx'] / 1000000000, 1) }}</td>

                            <!-- Mulk qabul qilmagan -->
                            <td class="text-end">
                                <a href="{{ route('yer-sotuvlar.index', ['holat' => 'Ishtirokchi roziligini kutish jarayonida (34)']) }}" class="text-decoration-none text-dark">
                                    {{ $statistics['jami']['mulk_qabul']['soni'] }}
                                </a>
                            </td>
                            <td class="text-end">{{ number_format($statistics['jami']['mulk_qabul']['auksion_mablagh'] / 1000000000, 1) }}</td>
                        </tr>

                        <!-- Tumanlar -->
                        @foreach($statistics['tumanlar'] as $index => $tuman)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $tuman['tuman'] }}</td>

                            <!-- Jami sotilgan -->
                            <td class="text-end">
                                @if($tuman['jami']['soni'] > 0)
                                    <a href="{{ route('yer-sotuvlar.index', ['tuman' => $tuman['tuman']]) }}" class="text-decoration-none">
                                        {{ $tuman['jami']['soni'] }}
                                    </a>
                                @else
                                    0
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($tuman['jami']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($tuman['jami']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['jami']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['jami']['chegirma'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['jami']['tushadigan_mablagh'] / 1000000000, 1) }}</td>

                            <!-- Bir yo'la -->
                            <td class="text-end">
                                @if($tuman['bir_yola']['soni'] > 0)
                                    <a href="{{ route('yer-sotuvlar.index', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли эмас']) }}" class="text-decoration-none">
                                        {{ $tuman['bir_yola']['soni'] }}
                                    </a>
                                @else
                                    0
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($tuman['bir_yola']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($tuman['bir_yola']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['bir_yola']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['bir_yola']['chegirma'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['bir_yola']['tushadigan_mablagh'] / 1000000000, 1) }}</td>

                            <!-- Bo'lib to'lash -->
                            <td class="text-end">
                                @if($tuman['bolib']['soni'] > 0)
                                    <a href="{{ route('yer-sotuvlar.index', ['tuman' => $tuman['tuman'], 'tolov_turi' => 'муддатли']) }}" class="text-decoration-none">
                                        {{ $tuman['bolib']['soni'] }}
                                    </a>
                                @else
                                    0
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($tuman['bolib']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($tuman['bolib']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['bolib']['sotilgan_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['bolib']['chegirma'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['bolib']['tushadigan_mablagh'] / 1000000000, 1) }}</td>

                            <!-- Auksonda turgan -->
                            <td class="text-end">
                                @if($tuman['auksonda']['soni'] > 0)
                                    <a href="{{ route('yer-sotuvlar.index', ['tuman' => $tuman['tuman'], 'auksonda_turgan' => 'true']) }}" class="text-decoration-none">
                                        {{ $tuman['auksonda']['soni'] }}
                                    </a>
                                @else
                                    0
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($tuman['auksonda']['maydoni'], 2) }}</td>
                            <td class="text-end">{{ number_format($tuman['auksonda']['boshlangich_narx'] / 1000000000, 1) }}</td>
                            <td class="text-end">{{ number_format($tuman['auksonda']['sotilgan_narx'] / 1000000000, 1) }}</td>

                            <!-- Mulk qabul qilmagan -->
                            <td class="text-end">
                                @if($tuman['mulk_qabul']['soni'] > 0)
                                    <a href="{{ route('yer-sotuvlar.index', ['tuman' => $tuman['tuman'], 'holat' => 'Ishtirokchi roziligini kutish jarayonida (34)']) }}" class="text-decoration-none">
                                        {{ $tuman['mulk_qabul']['soni'] }}
                                    </a>
                                @else
                                    0
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($tuman['mulk_qabul']['auksion_mablagh'] / 1000000000, 1) }}</td>
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
        background-color: #edf7f9;
    }
.table tbody{
    background-color: #fff !important;
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
