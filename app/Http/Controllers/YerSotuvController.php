<?php

namespace App\Http\Controllers;

use App\Models\YerSotuv;
use App\Models\GrafikTolov;
use App\Models\FaktTolov;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class YerSotuvController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['tuman', 'yil', 'tolov_turi', 'holat', 'asos', 'auksonda_turgan']);

        // Debug mode
        if ($request->has('debug')) {
            return $this->debugMulkQabul();
        }

        // Agar filter bo'lsa, filtered data ko'rsatish
        if (!empty(array_filter($filters))) {
            return $this->showFilteredData($request, $filters);
        }

        // Aks holda statistics table ko'rsatish
        $statistics = $this->getDetailedStatistics();

        return view('yer-sotuvlar.statistics', compact('statistics'));
    }



    // SVOD 3 - Bo'lib to'lash jadvali
    public function svod3(Request $request)
    {
        $statistics = $this->getSvod3Statistics();

        return view('yer-sotuvlar.svod3', compact('statistics'));
    }

    private function getCalculationBreakdown($lotRaqamlari)
    {
        if (empty($lotRaqamlari)) {
            return [
                'golib_tolagan' => 0,
                'shartnoma_summasi' => 0,
                'T_total' => 0,
                'fakt_tolovlar' => 0,
                'auksion_harajati' => 0,
                'auksion_1_percent' => 0,
                'B_calculated' => 0
            ];
        }

        // Get base data from yer_sotuvlar
        $baseData = YerSotuv::whereIn('lot_raqami', $lotRaqamlari)
            ->selectRaw('
            SUM(COALESCE(golib_tolagan, 0)) as golib_tolagan,
            SUM(COALESCE(shartnoma_summasi, 0)) as shartnoma_summasi,
            SUM(COALESCE(auksion_harajati, 0)) as auksion_harajati
        ')
            ->first();

        // Get ALL fakt tolovlar
        $faktTolovlar = FaktTolov::whereIn('lot_raqami', $lotRaqamlari)
            ->sum('tolov_summa');

        // Calculate step by step
        $golib = $baseData->golib_tolagan ?? 0;
        $shartnoma = $baseData->shartnoma_summasi ?? 0;
        $T = $golib + $shartnoma;

        $fakt = $faktTolovlar;
        $auksion = $baseData->auksion_harajati ?? 0;
        $auksion1Percent = $auksion * 0.01;

        $B = $T - $fakt - $auksion1Percent;

        return [
            'golib_tolagan' => $golib,
            'shartnoma_summasi' => $shartnoma,
            'T_total' => $T,
            'fakt_tolovlar' => $fakt,
            'auksion_harajati' => $auksion,
            'auksion_1_percent' => $auksion1Percent,
            'B_calculated' => $B
        ];
    }


    private function getSvod3Statistics()
    {
        $tumanlar = [
            'Бектемир тумани',
            'Мирзо Улуғбек тумани',
            'Миробод тумани',
            'Олмазор тумани',
            'Сирғали тумани',
            'Учтепа тумани',
            'Чилонзор тумани',
            'Шайхонтоҳур тумани',
            'Юнусобод тумани',
            'Яккасарой тумани',
            'Янги ҳаёт тумани',
            'Яшнобод тумани'
        ];

        $result = [
            'jami' => $this->initializeSvod3Total(),
            'tumanlar' => []
        ];

        foreach ($tumanlar as $tuman) {
            $tumanPatterns = $this->getTumanPatterns($tuman);

            $stat = [
                'tuman' => $tuman,
                'narhini_bolib' => $this->getNarhiniBolib($tumanPatterns),
                'toliq_tolanganlar' => $this->getToliqTolanganlar($tumanPatterns),
                'nazoratdagilar' => $this->getNazoratdagilar($tumanPatterns),
                'grafik_ortda' => $this->getGrafikOrtda($tumanPatterns)
            ];

            $result['tumanlar'][] = $stat;
            $this->addToSvod3Total($result['jami'], $stat);
        }

        return $result;
    }

    /**
     * NARHINI BO'LIB - All lots sold with installment payment
     * Formula: T = golib_tolagan + shartnoma_summasi
     */
    private function getNarhiniBolib($tumanPatterns = null)
    {
        $query = YerSotuv::query();

        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        // Bo'lib to'lash (muddatli)
        $query->where('tolov_turi', 'муддатли');

        $data = $query->selectRaw('
        COUNT(*) as soni,
        SUM(maydoni) as maydoni,
        SUM(boshlangich_narx) as boshlangich_narx,
        SUM(sotilgan_narx) as sotilgan_narx,
        SUM(COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0)) as tushadigan_mablagh
    ')->first();

        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'boshlangich_narx' => $data->boshlangich_narx ?? 0,
            'sotilgan_narx' => $data->sotilgan_narx ?? 0,
            'tushadigan_mablagh' => $data->tushadigan_mablagh ?? 0
        ];
    }


    /**
     * TO'LIQ TO'LANGANLAR - Fully paid lots
     * Formula:
     * T = golib_tolagan + shartnoma_summasi
     * B = T - (fakt_tolovlar + auksion_harajati)
     * Fully paid when: B ≤ 0
     */
   private function getToliqTolanganlar($tumanPatterns = null)
{
    $query = YerSotuv::query();

    if ($tumanPatterns !== null && !empty($tumanPatterns)) {
        $query->where(function ($q) use ($tumanPatterns) {
            foreach ($tumanPatterns as $pattern) {
                $q->orWhere('tuman', 'like', '%' . $pattern . '%');
            }
        });
    }

    $query->where('tolov_turi', 'муддатли');

    // To'liq to'langan: B ≤ 0
    // B = (golib_tolagan + shartnoma_summasi) - (fakt_tolovlar + auksion_harajati) ≤ 0
    $query->whereRaw('lot_raqami IN (
        SELECT ys.lot_raqami
        FROM yer_sotuvlar ys
        LEFT JOIN (
            SELECT lot_raqami, SUM(tolov_summa) as jami_fakt
            FROM fakt_tolovlar
            GROUP BY lot_raqami
        ) f ON f.lot_raqami = ys.lot_raqami
        WHERE ys.tolov_turi = "муддатли"
        AND (
            (COALESCE(ys.golib_tolagan, 0) + COALESCE(ys.shartnoma_summasi, 0))
            - (COALESCE(f.jami_fakt, 0) + COALESCE(ys.auksion_harajati, 0))
        ) <= 0
        AND (COALESCE(ys.golib_tolagan, 0) + COALESCE(ys.shartnoma_summasi, 0)) > 0
    )');

    $data = $query->selectRaw('
        COUNT(*) as soni,
        SUM(maydoni) as maydoni,
        SUM(boshlangich_narx) as boshlangich_narx,
        SUM(sotilgan_narx) as sotilgan_narx,
        SUM(COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0)) as T_total
    ')->first();

    // For fully paid lots, tushgan_summa = tushadigan_mablagh (they paid everything)
    $tushganSumma = $data->T_total ?? 0;

    return [
        'soni' => $data->soni ?? 0,
        'maydoni' => $data->maydoni ?? 0,
        'boshlangich_narx' => $data->boshlangich_narx ?? 0,
        'sotilgan_narx' => $data->sotilgan_narx ?? 0,
        'tushadigan_mablagh' => $data->T_total ?? 0,
        'tushgan_summa' => $tushganSumma
    ];
}



    /**
     * NAZORATDAGILAR - Lots under monitoring (not fully paid)
     * Formula:
     * T = golib_tolagan + shartnoma_summasi
     * B = T - (fakt_tolovlar + auksion_harajati)
     * Under monitoring when: B > 0
     */
  private function getNazoratdagilar($tumanPatterns = null)
{
    $query = YerSotuv::query();

    if ($tumanPatterns !== null && !empty($tumanPatterns)) {
        $query->where(function ($q) use ($tumanPatterns) {
            foreach ($tumanPatterns as $pattern) {
                $q->orWhere('tuman', 'like', '%' . $pattern . '%');
            }
        });
    }

    $query->where('tolov_turi', 'муддатли');

    // Nazoratdagi: B > 0
    // B = (golib_tolagan + shartnoma_summasi) - (fakt_tolovlar + auksion_harajati) > 0
    $query->whereRaw('lot_raqami IN (
        SELECT ys.lot_raqami
        FROM yer_sotuvlar ys
        LEFT JOIN (
            SELECT lot_raqami, SUM(tolov_summa) as jami_fakt
            FROM fakt_tolovlar
            GROUP BY lot_raqami
        ) f ON f.lot_raqami = ys.lot_raqami
        WHERE ys.tolov_turi = "муддатли"
        AND (
            (COALESCE(ys.golib_tolagan, 0) + COALESCE(ys.shartnoma_summasi, 0))
            - (COALESCE(f.jami_fakt, 0) + COALESCE(ys.auksion_harajati, 0))
        ) > 0
    )');

    $data = $query->selectRaw('
        COUNT(*) as soni,
        SUM(maydoni) as maydoni,
        SUM(boshlangich_narx) as boshlangich_narx,
        SUM(sotilgan_narx) as sotilgan_narx,
        SUM(COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0)) as T_total
    ')->first();

    // Get lot raqamlari to calculate actual tushgan_summa
    $lotlar = YerSotuv::query();
    if ($tumanPatterns !== null && !empty($tumanPatterns)) {
        $lotlar->where(function ($q) use ($tumanPatterns) {
            foreach ($tumanPatterns as $pattern) {
                $q->orWhere('tuman', 'like', '%' . $pattern . '%');
            }
        });
    }
    $lotlar->where('tolov_turi', 'муддатли');
    $lotlar->whereRaw('lot_raqami IN (
        SELECT ys.lot_raqami
        FROM yer_sotuvlar ys
        LEFT JOIN (
            SELECT lot_raqami, SUM(tolov_summa) as jami_fakt
            FROM fakt_tolovlar
            GROUP BY lot_raqami
        ) f ON f.lot_raqami = ys.lot_raqami
        WHERE ys.tolov_turi = "муддатли"
        AND (
            (COALESCE(ys.golib_tolagan, 0) + COALESCE(ys.shartnoma_summasi, 0))
            - (COALESCE(f.jami_fakt, 0) + COALESCE(ys.auksion_harajati, 0))
        ) > 0
    )');

    $lotRaqamlari = $lotlar->pluck('lot_raqami')->toArray();

    // Calculate tushgan_summa: fakt_tolovlar + auksion_harajati
    $tushganData = DB::table('yer_sotuvlar as ys')
        ->leftJoin('fakt_tolovlar as f', 'f.lot_raqami', '=', 'ys.lot_raqami')
        ->whereIn('ys.lot_raqami', $lotRaqamlari)
        ->selectRaw('
            SUM(COALESCE(f.tolov_summa, 0)) as jami_fakt,
            SUM(COALESCE(ys.auksion_harajati, 0)) as jami_auksion
        ')
        ->first();

    $tushganSumma = ($tushganData->jami_fakt ?? 0) + ($tushganData->jami_auksion ?? 0);

    // For grafik and fakt sums (for display purposes)
    $tolovData = DB::table('yer_sotuvlar as ys')
        ->leftJoin('grafik_tolovlar as g', 'g.lot_raqami', '=', 'ys.lot_raqami')
        ->leftJoin('fakt_tolovlar as f', 'f.lot_raqami', '=', 'ys.lot_raqami')
        ->whereIn('ys.lot_raqami', $lotRaqamlari)
        ->selectRaw('
            SUM(COALESCE(g.grafik_summa, 0)) as jami_grafik,
            SUM(COALESCE(f.tolov_summa, 0)) as jami_fakt
        ')
        ->first();

    return [
        'soni' => $data->soni ?? 0,
        'maydoni' => $data->maydoni ?? 0,
        'boshlangich_narx' => $data->boshlangich_narx ?? 0,
        'sotilgan_narx' => $data->sotilgan_narx ?? 0,
        'tushadigan_mablagh' => $data->T_total ?? 0,
        'grafik_summa' => $tolovData->jami_grafik ?? 0,
        'fakt_summa' => $tolovData->jami_fakt  ?? 0,
        'tushgan_summa' => $tushganSumma
    ];
}

private function getGrafikOrtda($tumanPatterns = null)
{
    // Get the LAST day of the PREVIOUS month
    // This ensures we include all COMPLETED months only
    // Example: If today is Oct 30, 2025, bugun = 2025-09-30
    // Result: All months up to and including September are included
    $bugun = now()->subMonth()->endOfMonth()->format('Y-m-d');

    Log::info('=== GRAFIK ORTDA DEBUG START ===');
    Log::info('Cutoff Date (bugun): ' . $bugun);
    Log::info('Today\'s actual date: ' . now()->format('Y-m-d'));
    Log::info('Current Month: ' . now()->format('F Y'));
    Log::info('Previous Month: ' . now()->subMonth()->format('F Y'));
    Log::info('Tuman Patterns: ' . json_encode($tumanPatterns));

    $query = YerSotuv::query();

    if ($tumanPatterns !== null && !empty($tumanPatterns)) {
        $query->where(function ($q) use ($tumanPatterns) {
            foreach ($tumanPatterns as $pattern) {
                $q->orWhere('tuman', 'like', '%' . $pattern . '%');
            }
        });
    }

    $query->where('tolov_turi', 'муддатли');

    // Grafik bo'yicha ortda qolganlar (subset of nazoratdagilar)
    $query->whereRaw('lot_raqami IN (
        SELECT ys.lot_raqami
        FROM yer_sotuvlar ys
        LEFT JOIN (
            SELECT lot_raqami,
                   SUM(grafik_summa) as jami_grafik
            FROM grafik_tolovlar
            WHERE CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") <= ?
            GROUP BY lot_raqami
        ) g ON g.lot_raqami = ys.lot_raqami
        LEFT JOIN (
            SELECT lot_raqami, SUM(tolov_summa) as jami_fakt
            FROM fakt_tolovlar
            GROUP BY lot_raqami
        ) f ON f.lot_raqami = ys.lot_raqami
        WHERE ys.tolov_turi = "муддатли"
        AND (
            (COALESCE(ys.golib_tolagan, 0) + COALESCE(ys.shartnoma_summasi, 0))
            - (COALESCE(f.jami_fakt, 0) + COALESCE(ys.auksion_harajati, 0))
        ) > 0
        AND COALESCE(g.jami_grafik, 0) > COALESCE(f.jami_fakt, 0)
        AND COALESCE(g.jami_grafik, 0) > 0
    )', [$bugun]);

    $data = $query->selectRaw('
        COUNT(*) as soni,
        SUM(maydoni) as maydoni
    ')->first();

    Log::info('Found lots count (soni): ' . ($data->soni ?? 0));
    Log::info('Total area (maydoni): ' . ($data->maydoni ?? 0));

    // Get lot raqamlari for detailed calculation
    $lotlar = YerSotuv::query();
    if ($tumanPatterns !== null && !empty($tumanPatterns)) {
        $lotlar->where(function ($q) use ($tumanPatterns) {
            foreach ($tumanPatterns as $pattern) {
                $q->orWhere('tuman', 'like', '%' . $pattern . '%');
            }
        });
    }
    $lotlar->where('tolov_turi', 'муддатли');
    $lotlar->whereRaw('lot_raqami IN (
        SELECT ys.lot_raqami
        FROM yer_sotuvlar ys
        LEFT JOIN (
            SELECT lot_raqami,
                   SUM(grafik_summa) as jami_grafik
            FROM grafik_tolovlar
            WHERE CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") <= ?
            GROUP BY lot_raqami
        ) g ON g.lot_raqami = ys.lot_raqami
        LEFT JOIN (
            SELECT lot_raqami, SUM(tolov_summa) as jami_fakt
            FROM fakt_tolovlar
            GROUP BY lot_raqami
        ) f ON f.lot_raqami = ys.lot_raqami
        WHERE ys.tolov_turi = "муддатли"
        AND (
            (COALESCE(ys.golib_tolagan, 0) + COALESCE(ys.shartnoma_summasi, 0))
            - (COALESCE(f.jami_fakt, 0) + COALESCE(ys.auksion_harajati, 0))
        ) > 0
        AND COALESCE(g.jami_grafik, 0) > COALESCE(f.jami_fakt, 0)
        AND COALESCE(g.jami_grafik, 0) > 0
    )', [$bugun]);

    $lotRaqamlari = $lotlar->pluck('lot_raqami')->toArray();

    Log::info('Total Lot Raqamlari found: ' . count($lotRaqamlari));
    Log::info('Lot Raqamlari: ' . json_encode($lotRaqamlari));

    // Debug: Get all grafik months for these lots
    if (!empty($lotRaqamlari)) {
        $allGrafikMonths = DB::table('grafik_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->selectRaw('
                DISTINCT yil,
                oy,
                CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") as month_date,
                COUNT(*) as count,
                SUM(grafik_summa) as total_summa
            ')
            ->groupBy('yil', 'oy')
            ->orderBy('yil', 'asc')
            ->orderBy('oy', 'asc')
            ->get();

        Log::info('--- ALL GRAFIK MONTHS IN DATABASE ---');
        foreach ($allGrafikMonths as $month) {
            $monthDate = $month->month_date;
            $isIncluded = $monthDate <= $bugun ? '✅ INCLUDED' : '❌ EXCLUDED';
            Log::info(sprintf(
                '%s - Year: %s, Month: %02d, Lots: %d, Total: %s - %s',
                $monthDate,
                $month->yil,
                $month->oy,
                $month->count,
                number_format($month->total_summa, 2),
                $isIncluded
            ));
        }

        // Show filtered months (only those included in calculation)
        $includedMonths = DB::table('grafik_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->whereRaw('CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") <= ?', [$bugun])
            ->selectRaw('
                DISTINCT yil,
                oy,
                CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") as month_date,
                COUNT(*) as count,
                SUM(grafik_summa) as total_summa
            ')
            ->groupBy('yil', 'oy')
            ->orderBy('yil', 'asc')
            ->orderBy('oy', 'asc')
            ->get();

        Log::info('--- INCLUDED MONTHS (<= ' . $bugun . ') ---');
        $totalIncluded = 0;
        foreach ($includedMonths as $month) {
            Log::info(sprintf(
                '✅ %s - Year: %s, Month: %02d, Lots: %d, Total: %s',
                $month->month_date,
                $month->yil,
                $month->oy,
                $month->count,
                number_format($month->total_summa, 2)
            ));
            $totalIncluded += $month->total_summa;
        }
        Log::info('Total from INCLUDED months: ' . number_format($totalIncluded, 2));
    }

    // Main calculation with detailed logging
    $tolovData = DB::table('yer_sotuvlar as ys')
        ->leftJoin('grafik_tolovlar as g', function ($join) use ($bugun) {
            $join->on('g.lot_raqami', '=', 'ys.lot_raqami')
                ->whereRaw('CONCAT(g.yil, "-", LPAD(g.oy, 2, "0"), "-01") <= ?', [$bugun]);
        })
        ->leftJoin('fakt_tolovlar as f', 'f.lot_raqami', '=', 'ys.lot_raqami')
        ->whereIn('ys.lot_raqami', $lotRaqamlari)
        ->selectRaw('
            SUM(COALESCE(g.grafik_summa, 0)) as jami_grafik,
            SUM(COALESCE(f.tolov_summa, 0)) as jami_fakt,
            SUM(COALESCE(ys.golib_tolagan, 0)) as jami_golib,
            SUM(COALESCE(ys.auksion_harajati, 0)) as jami_auksion_harajati
        ')
        ->first();

    Log::info('--- PAYMENT TOTALS ---');
    Log::info('Jami Grafik (scheduled): ' . number_format($tolovData->jami_grafik ?? 0, 2));
    Log::info('Jami Fakt (actual payments): ' . number_format($tolovData->jami_fakt ?? 0, 2));
    Log::info('Jami Golib Tolagan: ' . number_format($tolovData->jami_golib ?? 0, 2));
    Log::info('Jami Auksion Harajati: ' . number_format($tolovData->jami_auksion_harajati ?? 0, 2));

    $grafikSumma = $tolovData->jami_grafik ?? 0;
    $faktSummaRaw = $tolovData->jami_fakt ?? 0;
    $golibTolagan = $tolovData->jami_golib ?? 0;
    $auksionHarajati = $tolovData->jami_auksion_harajati ?? 0;

    $faktSumma = $faktSummaRaw - $golibTolagan + $auksionHarajati;

    Log::info('--- CALCULATION BREAKDOWN ---');
    Log::info('Formula: faktSumma = jami_fakt - golib_tolagan + auksion_harajati');
    Log::info('faktSumma = ' . number_format($faktSummaRaw, 2) . ' - ' . number_format($golibTolagan, 2) . ' + ' . number_format($auksionHarajati, 2));
    Log::info('faktSumma = ' . number_format($faktSumma, 2));
    Log::info('Difference (grafik - fakt): ' . number_format($grafikSumma - $faktSumma, 2));

    $foiz = $grafikSumma > 0 ? round(($faktSumma / $grafikSumma) * 100, 1) : 0;
    Log::info('Percentage: ' . $foiz . '%');

    // Per-lot detailed breakdown (first 5 lots for sample)
    if (!empty($lotRaqamlari)) {
        $sampleLots = array_slice($lotRaqamlari, 0, 5);
        Log::info('--- DETAILED BREAKDOWN (First 5 Lots) ---');

        foreach ($sampleLots as $lotNo) {
            $lotDetail = DB::table('yer_sotuvlar as ys')
                ->leftJoin('grafik_tolovlar as g', function ($join) use ($bugun) {
                    $join->on('g.lot_raqami', '=', 'ys.lot_raqami')
                        ->whereRaw('CONCAT(g.yil, "-", LPAD(g.oy, 2, "0"), "-01") <= ?', [$bugun]);
                })
                ->leftJoin('fakt_tolovlar as f', 'f.lot_raqami', '=', 'ys.lot_raqami')
                ->where('ys.lot_raqami', $lotNo)
                ->selectRaw('
                    ys.lot_raqami,
                    ys.tuman,
                    SUM(COALESCE(g.grafik_summa, 0)) as grafik,
                    SUM(COALESCE(f.tolov_summa, 0)) as fakt,
                    ys.golib_tolagan,
                    ys.auksion_harajati
                ')
                ->groupBy('ys.lot_raqami', 'ys.tuman', 'ys.golib_tolagan', 'ys.auksion_harajati')
                ->first();

            if ($lotDetail) {
                $lotFakt = $lotDetail->fakt - $lotDetail->golib_tolagan + $lotDetail->auksion_harajati;
                Log::info(sprintf(
                    'Lot %s (%s): Grafik=%s, Fakt=%s, Adjusted=%s, Diff=%s',
                    $lotDetail->lot_raqami,
                    $lotDetail->tuman,
                    number_format($lotDetail->grafik, 2),
                    number_format($lotDetail->fakt, 2),
                    number_format($lotFakt, 2),
                    number_format($lotDetail->grafik - $lotFakt, 2)
                ));

                // Show months for this lot
                $lotMonths = DB::table('grafik_tolovlar')
                    ->where('lot_raqami', $lotNo)
                    ->selectRaw('
                        yil,
                        oy,
                        CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") as month_date,
                        grafik_summa
                    ')
                    ->orderBy('yil', 'asc')
                    ->orderBy('oy', 'asc')
                    ->get();

                $monthsInfo = $lotMonths->map(function($m) use ($bugun) {
                    $status = $m->month_date <= $bugun ? '✅' : '❌';
                    return sprintf('%s %s/%02d (%s)', $status, $m->yil, $m->oy, number_format($m->grafik_summa, 0));
                })->implode(', ');

                Log::info('  Months: ' . $monthsInfo);
            }
        }
    }

    $result = [
        'soni' => $data->soni ?? 0,
        'maydoni' => $data->maydoni ?? 0,
        'grafik_summa' => $grafikSumma,
        'fakt_summa' => $faktSumma,
        'farq_summa' => $grafikSumma - $faktSumma,
        'foiz' => $foiz
    ];

    Log::info('--- FINAL RESULT ---');
    Log::info(json_encode($result, JSON_PRETTY_PRINT));
    Log::info('=== GRAFIK ORTDA DEBUG END ===');

    return $result;
}

    private function initializeSvod3Total()
    {
        return [
            'narhini_bolib' => ['soni' => 0, 'maydoni' => 0, 'boshlangich_narx' => 0, 'sotilgan_narx' => 0, 'tushadigan_mablagh' => 0],
            'toliq_tolanganlar' => ['soni' => 0, 'maydoni' => 0, 'boshlangich_narx' => 0, 'sotilgan_narx' => 0, 'tushadigan_mablagh' => 0, 'tushgan_summa' => 0],
            'nazoratdagilar' => ['soni' => 0, 'maydoni' => 0, 'boshlangich_narx' => 0, 'sotilgan_narx' => 0, 'tushadigan_mablagh' => 0, 'grafik_summa' => 0, 'fakt_summa' => 0, 'tushgan_summa' => 0],
            'grafik_ortda' => ['soni' => 0, 'maydoni' => 0, 'grafik_summa' => 0, 'fakt_summa' => 0, 'foiz' => 0]
        ];
    }



     private function addToSvod3Total(&$jami, $stat)
    {
        foreach (['soni', 'maydoni', 'boshlangich_narx', 'sotilgan_narx', 'tushadigan_mablagh'] as $field) {
            $jami['narhini_bolib'][$field] += $stat['narhini_bolib'][$field];
        }

        foreach (['soni', 'maydoni', 'boshlangich_narx', 'sotilgan_narx', 'tushadigan_mablagh', 'tushgan_summa'] as $field) {
            $jami['toliq_tolanganlar'][$field] += $stat['toliq_tolanganlar'][$field];
        }

        foreach (['soni', 'maydoni', 'boshlangich_narx', 'sotilgan_narx', 'tushadigan_mablagh', 'grafik_summa', 'fakt_summa', 'tushgan_summa'] as $field) {
            $jami['nazoratdagilar'][$field] += $stat['nazoratdagilar'][$field];
        }

        foreach (['soni', 'maydoni', 'grafik_summa', 'fakt_summa'] as $field) {
            $jami['grafik_ortda'][$field] += $stat['grafik_ortda'][$field];
        }

        // Calculate percentage
        if ($jami['grafik_ortda']['grafik_summa'] > 0) {
            $jami['grafik_ortda']['foiz'] = round(($jami['grafik_ortda']['fakt_summa'] / $jami['grafik_ortda']['grafik_summa']) * 100, 1);
        }
    }


    // Qolgan metodlar (showFilteredData, getDetailedStatistics, va h.k.)

   public function list(Request $request)
    {
        $filters = [
            'tuman' => $request->tuman,
            'yil' => $request->yil,
            'tolov_turi' => $request->tolov_turi,
            'holat' => $request->holat,
            'asos' => $request->asos,
        ];

        return $this->showFilteredData($request, $filters);
    }
//    public function list(Request $request)
// {
//     $query = YerSotuv::query();

//     // Global search across multiple columns
//     if ($request->filled('search')) {
//         $searchTerm = $request->search;
//         $query->where(function($q) use ($searchTerm) {
//             $q->where('lot_raqami', 'LIKE', "%{$searchTerm}%")
//               ->orWhere('tuman', 'LIKE', "%{$searchTerm}%")
//               ->orWhere('manzil', 'LIKE', "%{$searchTerm}%")
//               ->orWhere('golib_nomi', 'LIKE', "%{$searchTerm}%")
//               ->orWhere('holat', 'LIKE', "%{$searchTerm}%")
//               ->orWhere('asos', 'LIKE', "%{$searchTerm}%");
//         });
//     }

//     // Tuman filter
//     if ($request->filled('tuman')) {
//         $query->where('tuman', $request->tuman);
//     }

//     // Year filter
//     if ($request->filled('yil')) {
//         $query->whereYear('auksion_sana', $request->yil);
//     }

//     // Date range filter
//     if ($request->filled('auksion_sana_from')) {
//         $query->whereDate('auksion_sana', '>=', $request->auksion_sana_from);
//     }
//     if ($request->filled('auksion_sana_to')) {
//         $query->whereDate('auksion_sana', '<=', $request->auksion_sana_to);
//     }

//     // Price range filter
//     if ($request->filled('narx_from')) {
//         $query->where('sotilgan_narx', '>=', $request->narx_from);
//     }
//     if ($request->filled('narx_to')) {
//         $query->where('sotilgan_narx', '<=', $request->narx_to);
//     }

//     // Area range filter
//     if ($request->filled('maydoni_from')) {
//         $query->where('maydoni', '>=', $request->maydoni_from);
//     }
//     if ($request->filled('maydoni_to')) {
//         $query->where('maydoni', '<=', $request->maydoni_to);
//     }

//     // Holat filter
//     if ($request->filled('holat')) {
//         $query->where('holat', 'LIKE', "%{$request->holat}%");
//     }

//     // Asos filter
//     if ($request->filled('asos')) {
//         $query->where('asos', 'LIKE', "%{$request->asos}%");
//     }

//     // Tolov turi filter
//     if ($request->filled('tolov_turi')) {
//         $query->where('tolov_turi', $request->tolov_turi);
//     }

//     // Sorting
//     $sortField = $request->get('sort', 'auksion_sana');
//     $sortDirection = $request->get('direction', 'desc');
//     $query->orderBy($sortField, $sortDirection);

//     // Get paginated results
//     $yerlar = $query->paginate(50)->appends($request->all());

//     // Calculate statistics
//     $statistics = [
//         'total_lots' => $query->count(),
//         'total_area' => $query->sum('maydoni'),
//         'boshlangich_narx' => $query->sum('boshlangich_narx'),
//         'chegirma' => $query->sum('chegirma'),
//         'golib_tolagan' => $query->sum('golib_tolagan'),
//         'total_price' => $query->sum('sotilgan_narx'),
//     ];

//     // Get unique values for filters
//     $tumanlar = YerSotuv::distinct()->pluck('tuman')->sort()->values();
//     $yillar = YerSotuv::distinct()
//         ->selectRaw('YEAR(auksion_sana) as year')
//         ->whereNotNull('auksion_sana')
//         ->orderBy('year', 'desc')
//         ->pluck('year');

//     return view('yer-sotuvlar.list', compact('yerlar', 'statistics', 'tumanlar', 'yillar'));
// }

     private function debugMulkQabul()
    {
        echo "<h1>DEBUG: Mulk Qabul Qilmagan Yerlar</h1>";

        echo "<h2>1. BARCHA HOLATLAR</h2>";
        $holatlar = YerSotuv::select('holat', DB::raw('COUNT(*) as count'))
            ->whereNotNull('holat')
            ->groupBy('holat')
            ->orderBy('count', 'desc')
            ->limit(20)
            ->get();

        echo "<table border='1' style='width: 100%; font-size: 12px;'>";
        echo "<tr><th>Holat</th><th>Count</th></tr>";
        foreach ($holatlar as $h) {
            $highlight = (strpos($h->holat, '34') !== false || strpos($h->holat, 'Ishtirokchi') !== false) ? 'background: yellow;' : '';
            echo "<tr style='{$highlight}'><td>{$h->holat}</td><td><strong>{$h->count}</strong></td></tr>";
        }
        echo "</table><br>";

        echo "<h2>2. BARCHA ASOSLAR</h2>";
        $asoslar = YerSotuv::select('asos', DB::raw('COUNT(*) as count'))
            ->whereNotNull('asos')
            ->groupBy('asos')
            ->orderBy('count', 'desc')
            ->get();

        echo "<table border='1' style='width: 100%; font-size: 12px;'>";
        echo "<tr><th>Asos</th><th>Count</th></tr>";
        foreach ($asoslar as $a) {
            $highlight = (strpos($a->asos, '135') !== false || strpos($a->asos, 'ПФ') !== false) ? 'background: yellow;' : '';
            echo "<tr style='{$highlight}'><td>{$a->asos}</td><td><strong>{$a->count}</strong></td></tr>";
        }
        echo "</table><br>";

        echo "<h2>3. TEST QUERIES</h2>";

        // Test 1: Aniq
        $test1 = YerSotuv::where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%')
            ->where('asos', 'like', '%ПФ-135%')
            ->count();
        echo "<p><strong>Test 1 (ANIQ):</strong> holat LIKE '%Ishtirokchi roziligini kutish jarayonida (34)%' AND asos LIKE '%ПФ-135%'</p>";
        echo "<p style='color: red; font-size: 20px;'>COUNT: <strong>{$test1}</strong></p>";

        // Test 2: Faqat (34)
        $test2 = YerSotuv::where('holat', 'like', '%34%')->count();
        echo "<p><strong>Test 2:</strong> holat LIKE '%34%'</p>";
        echo "<p style='color: blue; font-size: 20px;'>COUNT: <strong>{$test2}</strong></p>";

        // Test 3: Faqat 135
        $test3 = YerSotuv::where('asos', 'like', '%135%')->count();
        echo "<p><strong>Test 3:</strong> asos LIKE '%135%'</p>";
        echo "<p style='color: green; font-size: 20px;'>COUNT: <strong>{$test3}</strong></p>";

        // Test 4: Kombinatsiya (keng)
        $test4 = YerSotuv::where('holat', 'like', '%34%')
            ->where('asos', 'like', '%135%')
            ->count();
        echo "<p><strong>Test 4 (KENG):</strong> holat LIKE '%34%' AND asos LIKE '%135%'</p>";
        echo "<p style='color: purple; font-size: 20px;'>COUNT: <strong>{$test4}</strong></p>";

        echo "<h2>4. BIRINCHI 10 TA (asos LIKE '%135%')</h2>";
        $samples = YerSotuv::select('lot_raqami', 'tuman', 'holat', 'asos')
            ->where('asos', 'like', '%135%')
            ->limit(10)
            ->get();

        echo "<table border='1' style='width: 100%; font-size: 11px;'>";
        echo "<tr><th>Lot</th><th>Tuman</th><th>Holat</th><th>Asos</th></tr>";
        foreach ($samples as $s) {
            echo "<tr>";
            echo "<td>{$s->lot_raqami}</td>";
            echo "<td>{$s->tuman}</td>";
            echo "<td>" . substr($s->holat ?? '', 0, 60) . "...</td>";
            echo "<td>{$s->asos}</td>";
            echo "</tr>";
        }
        echo "</table>";

        die();
    }
     private function showFilteredData(Request $request, array $filters)
    {
        $query = YerSotuv::query();

        // **GLOBAL SEARCH** - Search across multiple columns
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('lot_raqami', 'like', '%' . $searchTerm . '%')
                    ->orWhere('tuman', 'like', '%' . $searchTerm . '%')
                    ->orWhere('mfy', 'like', '%' . $searchTerm . '%')
                    ->orWhere('manzil', 'like', '%' . $searchTerm . '%')
                    ->orWhere('unikal_raqam', 'like', '%' . $searchTerm . '%')
                    ->orWhere('zona', 'like', '%' . $searchTerm . '%')
                    ->orWhere('golib_nomi', 'like', '%' . $searchTerm . '%')
                    ->orWhere('auksion_golibi', 'like', '%' . $searchTerm . '%')
                    ->orWhere('telefon', 'like', '%' . $searchTerm . '%')
                    ->orWhere('holat', 'like', '%' . $searchTerm . '%')
                    ->orWhere('asos', 'like', '%' . $searchTerm . '%')
                    ->orWhere('shartnoma_raqam', 'like', '%' . $searchTerm . '%')
                    ->orWhere('tolov_turi', 'like', '%' . $searchTerm . '%');
            });
        }

        // Tuman filter
        if (!empty($filters['tuman'])) {
            $tumanPatterns = $this->getTumanPatterns($filters['tuman']);
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        // Yil filter
        if (!empty($filters['yil'])) {
            $query->where('yil', $filters['yil']);
        }

        // **Date Range Filter for Auksion Sana**
        if (!empty($filters['auksion_sana_from'])) {
            $query->whereDate('auksion_sana', '>=', $filters['auksion_sana_from']);
        }

        if (!empty($filters['auksion_sana_to'])) {
            $query->whereDate('auksion_sana', '<=', $filters['auksion_sana_to']);
        }

        // **Date Range Filter for Shartnoma Sana**
        if (!empty($filters['shartnoma_sana_from'])) {
            $query->whereDate('shartnoma_sana', '>=', $filters['shartnoma_sana_from']);
        }

        if (!empty($filters['shartnoma_sana_to'])) {
            $query->whereDate('shartnoma_sana', '<=', $filters['shartnoma_sana_to']);
        }

        // **Price Range Filter**
        if (!empty($filters['narx_from'])) {
            $query->where('sotilgan_narx', '>=', $filters['narx_from']);
        }

        if (!empty($filters['narx_to'])) {
            $query->where('sotilgan_narx', '<=', $filters['narx_to']);
        }

        // **Area Range Filter**
        if (!empty($filters['maydoni_from'])) {
            $query->where('maydoni', '>=', $filters['maydoni_from']);
        }

        if (!empty($filters['maydoni_to'])) {
            $query->where('maydoni', '<=', $filters['maydoni_to']);
        }

        // SPECIAL FILTERS - Priority order matters!

        // 1. Auksonda turgan
        if (!empty($filters['auksonda_turgan']) && $filters['auksonda_turgan'] === 'true') {
            $query->where(function ($q) {
                $q->where('tolov_turi', '!=', 'муддатли')
                    ->where('tolov_turi', '!=', 'муддатли эмас')
                    ->orWhereNull('tolov_turi');
            });
        }
        // 2. Toliq tolangan - UPDATED LOGIC
        elseif (!empty($request->toliq_tolangan) && $request->toliq_tolangan === 'true') {
            $query->where('tolov_turi', 'муддатли');
            $query->whereRaw('(
                (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0))
                - (
                    COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0)
                    + COALESCE(auksion_harajati, 0)
                )
            ) <= 0
            AND (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0)) > 0');
        }
        // 3. Nazoratda - UPDATED LOGIC
        elseif (!empty($request->nazoratda) && $request->nazoratda === 'true') {
            $query->where('tolov_turi', 'муддатли');
            $query->whereRaw('(
                (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0))
                - (
                    COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0)
                    + COALESCE(auksion_harajati, 0)
                )
            ) > 0');
        }
        // 4. Grafik ortda (subset of nazoratda)
    // 4. Grafik ortda (subset of nazoratda)
elseif (!empty($request->grafik_ortda) && $request->grafik_ortda === 'true') {
    // Use LAST day of PREVIOUS month (same as getGrafikOrtda)
    $bugun = now()->subMonth()->endOfMonth()->format('Y-m-d');

    $query->where('tolov_turi', 'муддатли');
    $query->whereRaw('lot_raqami IN (
        SELECT ys.lot_raqami
        FROM yer_sotuvlar ys
        LEFT JOIN (
            SELECT lot_raqami,
                   SUM(grafik_summa) as jami_grafik
            FROM grafik_tolovlar
            WHERE CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") <= ?
            GROUP BY lot_raqami
        ) g ON g.lot_raqami = ys.lot_raqami
        LEFT JOIN (
            SELECT lot_raqami, SUM(tolov_summa) as jami_fakt
            FROM fakt_tolovlar
            GROUP BY lot_raqami
        ) f ON f.lot_raqami = ys.lot_raqami
        WHERE ys.tolov_turi = "муддатли"
        AND (
            (COALESCE(ys.golib_tolagan, 0) + COALESCE(ys.shartnoma_summasi, 0))
            - (COALESCE(f.jami_fakt, 0) + COALESCE(ys.auksion_harajati, 0))
        ) > 0
        AND COALESCE(g.jami_grafik, 0) > COALESCE(f.jami_fakt, 0)
        AND COALESCE(g.jami_grafik, 0) > 0
    )', [$bugun]);
}
        // 5. Oddiy tolov turi filter
        elseif (!empty($filters['tolov_turi'])) {
            $query->where('tolov_turi', $filters['tolov_turi']);
        }

        // Holat filter
        if (!empty($filters['holat'])) {
            $query->where('holat', 'like', '%' . $filters['holat'] . '%');

            // Agar holat (34) bo'lsa, avtomatik ravishda asos=ПФ-135 qo'shish
            if (strpos($filters['holat'], '(34)') !== false) {
                $query->where('asos', 'ПФ-135');
            }
        }

        // Asos filter
        if (!empty($filters['asos'])) {
            $query->where('asos', 'like', '%' . $filters['asos'] . '%');
        }

        // MUHIM: Statistikani paginatsiyadan OLDIN hisoblash
        $statistics = [
            'total_lots' => $query->count(),
            'total_area' => $query->sum('maydoni'),
            'total_price' => $query->sum('sotilgan_narx'),
            'boshlangich_narx' => $query->sum('boshlangich_narx'),
            'chegirma' => $query->sum('chegirma'),
            'golib_tolagan' => $query->sum('golib_tolagan'),

        ];

        // **ENHANCED SORTING** with more options and proper NULL handling
        $sortField = $request->get('sort', 'auksion_sana');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSortFields = [
            'auksion_sana',
            'shartnoma_sana',
            'sotilgan_narx',
            'boshlangich_narx',
            'maydoni',
            'tuman',
            'lot_raqami',
            'yil',
            'manzil',
            'golib_nomi',
            'telefon',
            'tolov_turi',
            'holat',
            'asos'
        ];

        if (in_array($sortField, $allowedSortFields)) {
            // Handle NULL values for date and numeric fields
            if (in_array($sortField, ['auksion_sana', 'shartnoma_sana', 'sotilgan_narx', 'boshlangich_narx', 'maydoni'])) {
                // NULLs last for DESC, NULLs first for ASC
                if ($sortDirection === 'desc') {
                    $query->orderByRaw("CASE WHEN {$sortField} IS NULL THEN 1 ELSE 0 END");
                    $query->orderBy($sortField, 'desc');
                } else {
                    $query->orderByRaw("CASE WHEN {$sortField} IS NULL THEN 1 ELSE 0 END");
                    $query->orderBy($sortField, 'asc');
                }
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        }

        // Paginatsiya
        $yerlar = $query->paginate(50)->withQueryString();

        // Dropdown uchun ro'yxatlar
        $tumanlar = YerSotuv::select('tuman')
            ->distinct()
            ->whereNotNull('tuman')
            ->orderBy('tuman')
            ->pluck('tuman');

        $yillar = YerSotuv::select('yil')
            ->distinct()
            ->whereNotNull('yil')
            ->orderBy('yil', 'desc')
            ->pluck('yil');

        return view('yer-sotuvlar.list', compact('yerlar', 'tumanlar', 'yillar', 'filters', 'statistics'));
    }

   private function getDetailedStatistics()
    {
        $tumanlar = [
            'Бектемир тумани',
            'Мирзо Улуғбек тумани',
            'Миробод тумани',
            'Олмазор тумани',
            'Сирғали тумани',
            'Учтепа тумани',
            'Чилонзор тумани',
            'Шайхонтоҳур тумани',
            'Юнусобод тумани',
            'Яккасарой тумани',
            'Янги ҳаёт тумани',
            'Яшнобод тумани'
        ];

        $statistics = [];

        // Har bir tuman uchun statistika
        foreach ($tumanlar as $tuman) {
            $stat = $this->calculateTumanStatistics($tuman);
            $statistics[] = $stat;
        }

        // JAMI ni to'g'ridan-to'g'ri hisoblash (tuman filtersiz)
        $jami = [
            'jami' => $this->getTumanData(null),
            'bir_yola' => $this->getTumanData(null, 'муддатли эмас'),
            'bolib' => $this->getTumanData(null, 'муддатли'),
            'auksonda' => $this->getAuksondaTurgan(null),
            'mulk_qabul' => $this->getMulkQabulQilmagan(null)
        ];

        return [
            'tumanlar' => $statistics,
            'jami' => $jami
        ];
    }

  private function calculateTumanStatistics($tumanName)
    {
        // Tuman nomini olish (masalan: "Бектемир т." -> pattern matching uchun)
        $tumanPatterns = $this->getTumanPatterns($tumanName);

        // Jami sotilgan yerlar
        $jami = $this->getTumanData($tumanPatterns);

        // Bir yo'la to'lash
        $birYola = $this->getTumanData($tumanPatterns, 'муддатли эмас');

        // Bo'lib to'lash
        $bolib = $this->getTumanData($tumanPatterns, 'муддатли');

        // Auksonda turgan
        $auksonda = $this->getAuksondaTurgan($tumanPatterns);

        // Mulk qabul qilish tugmasi bosilmagan
        $mulkQabul = $this->getMulkQabulQilmagan($tumanPatterns);

        return [
            'tuman' => $tumanName,
            'tuman_patterns' => $tumanPatterns,
            'jami' => $jami,
            'bir_yola' => $birYola,
            'bolib' => $bolib,
            'auksonda' => $auksonda,
            'mulk_qabul' => $mulkQabul
        ];
    }

  private function getTumanPatterns($tumanName)
    {
        // Turli variantlarni yaratish
        $base = str_replace([' т.', ' тумани'], '', $tumanName);

        // Maxsus variantlar (masalan: Шайхонтоҳур va Шайхонтоҳур)
        $patterns = [
            $base,                          // Шайхонтоҳур
            $base . ' т.',                  // Шайхонтоҳур т.
            $base . ' тумани',              // Шайхонтоҳур тумани
        ];

        // О/о variant (masalan: Шайхонтоҳур <-> Шайхонтоҳур)
        if (mb_strpos($base, 'о') !== false) {
            $altBase = str_replace('о', 'ҳ', $base);
            $patterns[] = $altBase;
            $patterns[] = $altBase . ' т.';
            $patterns[] = $altBase . ' тумани';
        }

        if (mb_strpos($base, 'ҳ') !== false) {
            $altBase = str_replace('ҳ', 'о', $base);
            $patterns[] = $altBase;
            $patterns[] = $altBase . ' т.';
            $patterns[] = $altBase . ' тумани';
        }

        return array_unique($patterns);
    }

  private function getTumanData($tumanPatterns = null, $tolovTuri = null)
    {
        $query = YerSotuv::query();

        // Tuman filter (agar mavjud bo'lsa)
        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        // Tolov turi filter
        if ($tolovTuri) {
            $query->where('tolov_turi', $tolovTuri);
        }

        // Main data (including 'низоли')
        $data = $query->selectRaw('
        COUNT(*) as soni,
        SUM(maydoni) as maydoni,
        SUM(boshlangich_narx) as boshlangich_narx,
        SUM(sotilgan_narx) as sotilgan_narx,
        SUM(chegirma) as chegirma
    ')->first();

        // Separate query for tushadigan_mablagh (excluding 'низоли')
        // ✅ CHANGED: Use golib_tolagan instead of tushadigan_mablagh
        $queryTushadigan = YerSotuv::query();

        // Apply same tuman filter
        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $queryTushadigan->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        // Apply same tolov_turi filter
        if ($tolovTuri) {
            $queryTushadigan->where('tolov_turi', $tolovTuri);
        }

        // Exclude 'низоли' for tushadigan_mablagh only
        $queryTushadigan->where('tolov_turi', '!=', 'низоли');

        // ✅ CHANGED: Sum golib_tolagan instead of tushadigan_mablagh
        // SUM(golib_tolagan) as tushadigan_mablagh
        $tushadiganData = $queryTushadigan->selectRaw('
            SUM(COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0)) as tushadigan_mablagh

    ')->first();

        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'boshlangich_narx' => $data->boshlangich_narx ?? 0,
            'sotilgan_narx' => $data->sotilgan_narx ?? 0,
            'chegirma' => $data->chegirma ?? 0,
            'tushadigan_mablagh' => $tushadiganData->tushadigan_mablagh ?? 0
        ];
    }
    private function getAuksondaTurgan($tumanPatterns = null)
    {
        $query = YerSotuv::query();

        // Tuman filter (agar mavjud bo'lsa)
        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        $query->where(function ($q) {
            $q->where('tolov_turi', '!=', 'муддатли')
                ->where('tolov_turi', '!=', 'муддатли эмас')
                ->orWhereNull('tolov_turi');
        });

        $data = $query->selectRaw('
            COUNT(*) as soni,
            SUM(maydoni) as maydoni,
            SUM(boshlangich_narx) as boshlangich_narx,
            SUM(sotilgan_narx) as sotilgan_narx
        ')->first();

        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'boshlangich_narx' => $data->boshlangich_narx ?? 0,
            'sotilgan_narx' => $data->sotilgan_narx ?? 0,
            'chegirma' => $data->chegirma ?? 0,
            'golib_tolagan' => $data->golib_tolagan ?? 0,
        ];
    }

    private function getMulkQabulQilmagan($tumanPatterns = null)
    {
        // FAQAT HOLAT (34) BO'YICHA - 17 ta
        // Agar tuman pattern bo'lsa, faqat o'sha tuman uchun
        // Aks holda, barcha tumanlar uchun
        $query = YerSotuv::query();

        // Tuman filter (agar mavjud bo'lsa)
        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        // Holat filter
        $query->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%');

        // Asos filter - ПФ-135
        $query->where('asos', 'ПФ-135');

        // Agar davaktivda_turgan mavjud bo'lsa uni ishlatish, aks holda sotilgan_narx
        $data = $query->selectRaw('
        COUNT(*) as soni,
        SUM(CASE
            WHEN davaktivda_turgan IS NOT NULL AND davaktivda_turgan > 0
            THEN davaktivda_turgan
            ELSE sotilgan_narx
        END) as auksion_mablagh
    ')->first();

        return [
            'soni' => $data->soni ?? 0,
            'auksion_mablagh' => $data->auksion_mablagh ?? 0
        ];
    }
    private function initializeTotal()
    {
        return [
            'jami' => ['soni' => 0, 'maydoni' => 0, 'boshlangich_narx' => 0, 'sotilgan_narx' => 0, 'chegirma' => 0, 'tushadigan_mablagh' => 0],
            'bir_yola' => ['soni' => 0, 'maydoni' => 0, 'boshlangich_narx' => 0, 'sotilgan_narx' => 0, 'chegirma' => 0, 'tushadigan_mablagh' => 0],
            'bolib' => ['soni' => 0, 'maydoni' => 0, 'boshlangich_narx' => 0, 'sotilgan_narx' => 0, 'chegirma' => 0, 'tushadigan_mablagh' => 0],
            'auksonda' => ['soni' => 0, 'maydoni' => 0, 'boshlangich_narx' => 0, 'sotilgan_narx' => 0],
            'mulk_qabul' => ['soni' => 0, 'auksion_mablagh' => 0]
        ];
    }

     private function addToTotal(&$jami, $stat)
    {
        foreach (['jami', 'bir_yola', 'bolib'] as $key) {
            foreach ($stat[$key] as $field => $value) {
                $jami[$key][$field] += $value;
            }
        }

        foreach ($stat['auksonda'] as $field => $value) {
            $jami['auksonda'][$field] += $value;
        }

        foreach ($stat['mulk_qabul'] as $field => $value) {
            $jami['mulk_qabul'][$field] += $value;
        }
    }

    public function show($lot_raqami)
    {
        $yer = YerSotuv::where('lot_raqami', $lot_raqami)
            ->with([
                'grafikTolovlar' => function ($query) {
                    $query->orderBy('yil')->orderBy('oy');
                },
                'faktTolovlar' => function ($query) {
                    $query->orderByDesc('tolov_sana');
                }
            ])
            ->firstOrFail();

        // DEBUG: Ma'lumotlarni tekshirish
        Log::info('Lot raqami: ' . $lot_raqami);
        Log::info('Grafik to\'lovlar soni: ' . $yer->grafikTolovlar->count());
        Log::info('Fakt to\'lovlar soni: ' . $yer->faktTolovlar->count());

        // Agar grafik to'lovlar bo'lmasa
        if ($yer->grafikTolovlar->isEmpty()) {
            Log::warning('Bu lot uchun grafik to\'lovlar mavjud emas!');
        }

        $tolovTaqqoslash = $this->taqqoslashHisoblash($yer);

        return view('yer-sotuvlar.show', compact('yer', 'tolovTaqqoslash'));
    }
   private function taqqoslashHisoblash($yer)
{
    // Get all months that have either grafik OR fakt payments
    $grafikByMonth = $yer->grafikTolovlar->groupBy(function ($item) {
        return $item->yil . '-' . str_pad($item->oy, 2, '0', STR_PAD_LEFT);
    });

    $faktByMonth = $yer->faktTolovlar->groupBy(function ($item) {
        return $item->tolov_sana->format('Y-m');
    });

    $taqqoslash = [];
    $allMonths = [];

    // Collect all unique months from grafik
    foreach ($grafikByMonth as $key => $grafikItems) {
        $allMonths[$key] = [
            'yil' => $grafikItems->first()->yil,
            'oy' => $grafikItems->first()->oy,
            'oy_nomi' => $grafikItems->first()->oy_nomi,
            'grafik_summa' => $grafikItems->sum('grafik_summa'),
            'is_advance' => false,
            'payment_date' => null
        ];
    }

    // Add months from fakt that don't exist in grafik (ADVANCE PAYMENTS)
    foreach ($faktByMonth as $key => $faktItems) {
        if (!isset($allMonths[$key])) {
            // Get the actual payment date from first payment in this month
            $firstPayment = $faktItems->first();

            $allMonths[$key] = [
                'yil' => (int)$firstPayment->tolov_sana->format('Y'),
                'oy' => (int)$firstPayment->tolov_sana->format('m'),
                'oy_nomi' => 'Avvaldan to\'lagan summasi',  // Special label
                'grafik_summa' => 0,  // No grafik for this month
                'is_advance' => true,
                'payment_date' => $firstPayment->tolov_sana->format('d.m.Y')  // Actual date
            ];
        }
    }

    // Sort by year and month
    ksort($allMonths);

    // Build comparison table
    foreach ($allMonths as $key => $monthData) {
        $grafikSumma = $monthData['grafik_summa'];
        $faktSumma = $faktByMonth->get($key)?->sum('tolov_summa') ?? 0;
        $farq = $grafikSumma - $faktSumma;
        $foiz = $grafikSumma > 0 ? round(($faktSumma / $grafikSumma) * 100, 1) : 0;

        // Display name: either "oy_nomi yil" or "Avvaldan to'lagan (date)"
        $displayName = $monthData['is_advance']
            ? $monthData['oy_nomi'] . ' (' . $monthData['payment_date'] . ')'
            : $monthData['oy_nomi'] . ' ' . $monthData['yil'];

        $taqqoslash[] = [
            'yil' => $monthData['yil'],
            'oy' => $monthData['oy'],
            'oy_nomi' => $displayName,  // Enhanced display name
            'grafik' => $grafikSumma,
            'fakt' => $faktSumma,
            'farq' => $farq,
            'foiz' => $foiz,
            'is_advance' => $monthData['is_advance']  // Flag for styling
        ];
    }

    return $taqqoslash;
}


private function comparePayments($grafik, $fakt)
    {
        $result = [];

        foreach ($grafik as $g) {
            $faktSumma = $fakt->where('yil', $g->yil)
                ->where('oy', $g->oy)
                ->sum('summa');

            $result[] = [
                'yil' => $g->yil,
                'oy' => $g->oy,
                'oy_nomi' => $g->oy_nomi,
                'grafik' => $g->summa,
                'fakt' => $faktSumma,
                'farq' => $g->summa - $faktSumma,
                'foiz' => $g->summa > 0 ? round(($faktSumma / $g->summa) * 100, 1) : 0
            ];
        }

        return $result;
    }
}
