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
    /**
     * Helper method to get consistent cutoff date for grafik calculations
     * Uses LAST day of PREVIOUS month to include only COMPLETED months
     */
    private function getGrafikCutoffDate()
    {
        // Get LAST day of PREVIOUS month
        // Example: If today is Oct 30, 2025 → returns 2025-09-30
        return now()->subMonth()->endOfMonth()->format('Y-m-d');
    }

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

        // For fully paid lots, tushgan_summa = tushadigan_mablagh
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

        // Get lot raqamlari
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

        // Calculate tushgan_summa
        $tushganData = DB::table('yer_sotuvlar as ys')
            ->leftJoin('fakt_tolovlar as f', 'f.lot_raqami', '=', 'ys.lot_raqami')
            ->whereIn('ys.lot_raqami', $lotRaqamlari)
            ->selectRaw('
                SUM(COALESCE(f.tolov_summa, 0)) as jami_fakt,
                SUM(COALESCE(ys.auksion_harajati, 0)) as jami_auksion
            ')
            ->first();

        $tushganSumma = ($tushganData->jami_fakt ?? 0) + ($tushganData->jami_auksion ?? 0);

        // For grafik and fakt sums
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
            'fakt_summa' => $tolovData->jami_fakt ?? 0,
            'tushgan_summa' => $tushganSumma
        ];
    }

    /**
     * GRAFIK ORTDA - Lots behind schedule
     * Uses consistent cutoff date from getGrafikCutoffDate()
     */
/**
 * Get detailed month-by-month breakdown for "grafik ortda" lots
 */
private function getGrafikOrtda($tumanPatterns = null)
{
    $bugun = $this->getGrafikCutoffDate();

    Log::info('=== GRAFIK ORTDA STATISTICS DEBUG START ===');
    Log::info('Cutoff Date: ' . $bugun);
    Log::info('Tuman Patterns: ' . json_encode($tumanPatterns));

    // Build the base query with tuman filter
    $query = YerSotuv::query();

    if ($tumanPatterns !== null && !empty($tumanPatterns)) {
        $query->where(function ($q) use ($tumanPatterns) {
            foreach ($tumanPatterns as $pattern) {
                $q->orWhere('tuman', 'like', '%' . $pattern . '%');
            }
        });
    }

    $query->where('tolov_turi', 'муддатли');

    // Subquery to find lots behind schedule
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

    Log::info('Found lots count: ' . ($data->soni ?? 0));
    Log::info('Total area: ' . ($data->maydoni ?? 0));

    // Create fresh query to get lot numbers
    $lotlarQuery = YerSotuv::query();

    if ($tumanPatterns !== null && !empty($tumanPatterns)) {
        $lotlarQuery->where(function ($q) use ($tumanPatterns) {
            foreach ($tumanPatterns as $pattern) {
                $q->orWhere('tuman', 'like', '%' . $pattern . '%');
            }
        });
    }

    $lotlarQuery->where('tolov_turi', 'муддатли');
    $lotlarQuery->whereRaw('lot_raqami IN (
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

    $lotRaqamlari = $lotlarQuery->pluck('lot_raqami')->toArray();

    Log::info('Lot Raqamlari: ' . json_encode($lotRaqamlari));

    if (empty($lotRaqamlari)) {
        Log::info('=== GRAFIK ORTDA STATISTICS DEBUG END (NO LOTS) ===');
        return [
            'soni' => 0,
            'maydoni' => 0,
            'grafik_summa' => 0,
            'fakt_summa' => 0,
            'farq_summa' => 0,
            'foiz' => 0
        ];
    }

    // ✅ FIXED: Only get months UP TO cutoff date
    $grafikSumma = DB::table('grafik_tolovlar')
        ->whereIn('lot_raqami', $lotRaqamlari)
        ->whereRaw('CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") <= ?', [$bugun])
        ->sum('grafik_summa');

    $faktSumma = DB::table('fakt_tolovlar')
        ->whereIn('lot_raqami', $lotRaqamlari)
        ->sum('tolov_summa');

    Log::info('--- PAYMENT CALCULATION (FIXED WITH PROPER DATE FILTER) ---');
    Log::info('Cutoff Date Applied: ' . $bugun);
    Log::info('Grafik Summa (scheduled up to cutoff): ' . number_format($grafikSumma, 2));
    Log::info('Fakt Summa (all actual payments): ' . number_format($faktSumma, 2));
    Log::info('Difference (grafik - fakt): ' . number_format($grafikSumma - $faktSumma, 2));

    $foiz = $grafikSumma > 0 ? round(($faktSumma / $grafikSumma) * 100, 1) : 0;
    Log::info('Percentage: ' . $foiz . '%');

    // ✅ DEBUG: Show monthly breakdown to verify
    $monthlyDetails = DB::table('grafik_tolovlar')
        ->whereIn('lot_raqami', $lotRaqamlari)
        ->whereRaw('CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") <= ?', [$bugun])
        ->select('lot_raqami', 'yil', 'oy', 'grafik_summa')
        ->orderBy('lot_raqami')
        ->orderBy('yil')
        ->orderBy('oy')
        ->get();

    Log::info('--- MONTHLY GRAFIK BREAKDOWN (ONLY PAST DUE) ---');
    $currentLot = null;
    $lotTotal = 0;

    foreach ($monthlyDetails as $month) {
        if ($currentLot !== $month->lot_raqami) {
            if ($currentLot !== null) {
                Log::info(sprintf('  LOT %s TOTAL: %.2f млрд', $currentLot, $lotTotal / 1_000_000_000));
                Log::info('  ---');
            }
            $currentLot = $month->lot_raqami;
            $lotTotal = 0;
            Log::info('LOT ' . $currentLot . ':');
        }

        $lotTotal += $month->grafik_summa;
        Log::info(sprintf('  %d-%02d: %.2f млн', $month->yil, $month->oy, $month->grafik_summa / 1_000_000));
    }

    if ($currentLot !== null) {
        Log::info(sprintf('  LOT %s TOTAL: %.2f млрд', $currentLot, $lotTotal / 1_000_000_000));
    }

    Log::info('--- VERIFICATION ---');
    Log::info('Sum of monthly grafik: ' . number_format($monthlyDetails->sum('grafik_summa'), 2));
    Log::info('Direct query grafik: ' . number_format($grafikSumma, 2));
    Log::info('Match: ' . ($monthlyDetails->sum('grafik_summa') == $grafikSumma ? '✅ YES' : '❌ NO'));

    $result = [
        'soni' => $data->soni ?? 0,
        'maydoni' => $data->maydoni ?? 0,
        'grafik_summa' => $grafikSumma,
        'fakt_summa' => $faktSumma,
        'farq_summa' => $grafikSumma - $faktSumma,
        'foiz' => $foiz
    ];

    Log::info('=== GRAFIK ORTDA STATISTICS DEBUG END ===');
    Log::info(json_encode($result, JSON_PRETTY_PRINT));

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

    public function list(Request $request)
    {
        // ✅ FIX: Include ALL filters including grafik_ortda
        $filters = [
            'search' => $request->search,
            'tuman' => $request->tuman,
            'yil' => $request->yil,
            'tolov_turi' => $request->tolov_turi,
            'holat' => $request->holat,
            'asos' => $request->asos,
            'auksion_sana_from' => $request->auksion_sana_from,
            'auksion_sana_to' => $request->auksion_sana_to,
            'shartnoma_sana_from' => $request->shartnoma_sana_from,
            'shartnoma_sana_to' => $request->shartnoma_sana_to,
            'narx_from' => $request->narx_from,
            'narx_to' => $request->narx_to,
            'maydoni_from' => $request->maydoni_from,
            'maydoni_to' => $request->maydoni_to,
            'auksonda_turgan' => $request->auksonda_turgan,
            'grafik_ortda' => $request->grafik_ortda,  // ✅ ADDED THIS!
            'toliq_tolangan' => $request->toliq_tolangan,  // ✅ ADDED THIS TOO!
            'nazoratda' => $request->nazoratda,  // ✅ AND THIS!
        ];

        return $this->showFilteredData($request, $filters);
    }
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

    /**
     * ✅ FIXED: showFilteredData now uses consistent cutoff date
     */
    private function showFilteredData(Request $request, array $filters)
    {
        $query = YerSotuv::query();

        // Global Search
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

        // Date Range Filters
        if (!empty($filters['auksion_sana_from'])) {
            $query->whereDate('auksion_sana', '>=', $filters['auksion_sana_from']);
        }
        if (!empty($filters['auksion_sana_to'])) {
            $query->whereDate('auksion_sana', '<=', $filters['auksion_sana_to']);
        }
        if (!empty($filters['shartnoma_sana_from'])) {
            $query->whereDate('shartnoma_sana', '>=', $filters['shartnoma_sana_from']);
        }
        if (!empty($filters['shartnoma_sana_to'])) {
            $query->whereDate('shartnoma_sana', '<=', $filters['shartnoma_sana_to']);
        }

        // Price Range Filter
        if (!empty($filters['narx_from'])) {
            $query->where('sotilgan_narx', '>=', $filters['narx_from']);
        }
        if (!empty($filters['narx_to'])) {
            $query->where('sotilgan_narx', '<=', $filters['narx_to']);
        }

        // Area Range Filter
        if (!empty($filters['maydoni_from'])) {
            $query->where('maydoni', '>=', $filters['maydoni_from']);
        }
        if (!empty($filters['maydoni_to'])) {
            $query->where('maydoni', '<=', $filters['maydoni_to']);
        }

        // ✅ FIX: Check filters array instead of request object
        // SPECIAL FILTERS - Priority order matters!

        // 1. Auksonda turgan
        if (!empty($filters['auksonda_turgan']) && $filters['auksonda_turgan'] === 'true') {
            $query->where(function ($q) {
                $q->where('tolov_turi', '!=', 'муддатли')
                    ->where('tolov_turi', '!=', 'муддатли эмас')
                    ->orWhereNull('tolov_turi');
            });
        }
        // 2. Toliq tolangan
        elseif (!empty($filters['toliq_tolangan']) && $filters['toliq_tolangan'] === 'true') {
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
        // 3. Nazoratda
        elseif (!empty($filters['nazoratda']) && $filters['nazoratda'] === 'true') {
            $query->where('tolov_turi', 'муддатли');
            $query->whereRaw('(
            (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0))
            - (
                COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0)
                + COALESCE(auksion_harajati, 0)
            )
        ) > 0');
        }
        // 4. Grafik ortda - ✅ FIXED: Uses consistent cutoff date
        elseif (!empty($filters['grafik_ortda']) && $filters['grafik_ortda'] === 'true') {
            $bugun = $this->getGrafikCutoffDate();

            Log::info('=== GRAFIK ORTDA LIST PAGE DEBUG ===');
            Log::info('Cutoff Date: ' . $bugun);
            Log::info('Tuman Filter: ' . ($filters['tuman'] ?? 'ALL'));

            $query->where('tolov_turi', 'муддатли');

            // ✅ FIX: Subquery matches statistics logic exactly
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

            // ✅ ADDED: Debug what we found
            $foundLots = $query->select('lot_raqami', 'tuman', 'maydoni')->get();
            Log::info('Found ' . $foundLots->count() . ' lots on list page');
            foreach ($foundLots as $lot) {
                Log::info("- Lot {$lot->lot_raqami}: {$lot->tuman}, {$lot->maydoni} га");
            }
        }
        // 5. Oddiy tolov turi filter
        elseif (!empty($filters['tolov_turi'])) {
            $query->where('tolov_turi', $filters['tolov_turi']);
        }

        // Holat filter
        if (!empty($filters['holat'])) {
            $query->where('holat', 'like', '%' . $filters['holat'] . '%');
            if (strpos($filters['holat'], '(34)') !== false) {
                $query->where('asos', 'ПФ-135');
            }
        }

        // Asos filter
        if (!empty($filters['asos'])) {
            $query->where('asos', 'like', '%' . $filters['asos'] . '%');
        }

        // Calculate statistics BEFORE pagination
        $statistics = [
            'total_lots' => $query->count(),
            'total_area' => $query->sum('maydoni'),
            'total_price' => $query->sum('sotilgan_narx'),
            'boshlangich_narx' => $query->sum('boshlangich_narx'),
            'chegirma' => $query->sum('chegirma'),
            'golib_tolagan' => $query->sum('golib_tolagan'),
        ];

        // Sorting
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
            if (in_array($sortField, ['auksion_sana', 'shartnoma_sana', 'sotilgan_narx', 'boshlangich_narx', 'maydoni'])) {
                $query->orderByRaw("CASE WHEN {$sortField} IS NULL THEN 1 ELSE 0 END");
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        }

        // Pagination
        $yerlar = $query->paginate(50)->withQueryString();

        // Dropdown lists
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

        foreach ($tumanlar as $tuman) {
            $stat = $this->calculateTumanStatistics($tuman);
            $statistics[] = $stat;
        }

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
        $tumanPatterns = $this->getTumanPatterns($tumanName);

        $jami = $this->getTumanData($tumanPatterns);
        $birYola = $this->getTumanData($tumanPatterns, 'муддатли эмас');
        $bolib = $this->getTumanData($tumanPatterns, 'муддатли');
        $auksonda = $this->getAuksondaTurgan($tumanPatterns);
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
        $base = str_replace([' т.', ' тумани'], '', $tumanName);

        $patterns = [
            $base,
            $base . ' т.',
            $base . ' тумани',
        ];

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

        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        if ($tolovTuri) {
            $query->where('tolov_turi', $tolovTuri);
        }

        $data = $query->selectRaw('
            COUNT(*) as soni,
            SUM(maydoni) as maydoni,
            SUM(boshlangich_narx) as boshlangich_narx,
            SUM(sotilgan_narx) as sotilgan_narx,
            SUM(chegirma) as chegirma
        ')->first();

        $queryTushadigan = YerSotuv::query();

        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $queryTushadigan->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        if ($tolovTuri) {
            $queryTushadigan->where('tolov_turi', $tolovTuri);
        }

        $queryTushadigan->where('tolov_turi', '!=', 'низоли');

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
        $query = YerSotuv::query();

        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        $query->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%');
        $query->where('asos', 'ПФ-135');

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

        Log::info('Lot raqami: ' . $lot_raqami);
        Log::info('Grafik to\'lovlar soni: ' . $yer->grafikTolovlar->count());
        Log::info('Fakt to\'lovlar soni: ' . $yer->faktTolovlar->count());

        if ($yer->grafikTolovlar->isEmpty()) {
            Log::warning('Bu lot uchun grafik to\'lovlar mavjud emas!');
        }

        $tolovTaqqoslash = $this->taqqoslashHisoblash($yer);

        return view('yer-sotuvlar.show', compact('yer', 'tolovTaqqoslash'));
    }

    private function taqqoslashHisoblash($yer)
    {
        $grafikByMonth = $yer->grafikTolovlar->groupBy(function ($item) {
            return $item->yil . '-' . str_pad($item->oy, 2, '0', STR_PAD_LEFT);
        });

        $faktByMonth = $yer->faktTolovlar->groupBy(function ($item) {
            return $item->tolov_sana->format('Y-m');
        });

        $taqqoslash = [];
        $allMonths = [];

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

        foreach ($faktByMonth as $key => $faktItems) {
            if (!isset($allMonths[$key])) {
                $firstPayment = $faktItems->first();

                $allMonths[$key] = [
                    'yil' => (int)$firstPayment->tolov_sana->format('Y'),
                    'oy' => (int)$firstPayment->tolov_sana->format('m'),
                    'oy_nomi' => 'Avvaldan to\'lagan summasi',
                    'grafik_summa' => 0,
                    'is_advance' => true,
                    'payment_date' => $firstPayment->tolov_sana->format('d.m.Y')
                ];
            }
        }

        ksort($allMonths);

        foreach ($allMonths as $key => $monthData) {
            $grafikSumma = $monthData['grafik_summa'];
            $faktSumma = $faktByMonth->get($key)?->sum('tolov_summa') ?? 0;
            $farq = $grafikSumma - $faktSumma;
            $foiz = $grafikSumma > 0 ? round(($faktSumma / $grafikSumma) * 100, 1) : 0;

            $displayName = $monthData['is_advance']
                ? $monthData['oy_nomi'] . ' (' . $monthData['payment_date'] . ')'
                : $monthData['oy_nomi'] . ' ' . $monthData['yil'];

            $taqqoslash[] = [
                'yil' => $monthData['yil'],
                'oy' => $monthData['oy'],
                'oy_nomi' => $displayName,
                'grafik' => $grafikSumma,
                'fakt' => $faktSumma,
                'farq' => $farq,
                'foiz' => $foiz,
                'is_advance' => $monthData['is_advance']
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
