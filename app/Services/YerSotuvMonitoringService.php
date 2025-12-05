<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class YerSotuvMonitoringService
{
    protected $yerSotuvService;

    public function __construct(YerSotuvService $yerSotuvService)
    {
        $this->yerSotuvService = $yerSotuvService;
    }

    /**
     * Get list of all tumanlar
     * AUTOMATIC DISTRICT FILTERING: District users only see their district
     */
    public function getTumanlar(): array
    {
        // CRITICAL: If district user, return only their district
        if (Auth::check() && Auth::user()->isDistrict()) {
            return [Auth::user()->tuman];
        }

        // Super admin sees all districts
        return [
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
    }

    /**
     * Calculate comprehensive tuman statistics (used by both monitoring and yigmaMalumot)
     */
    public function calculateTumanStatistics(?array $tumanPatterns, array $dateFilters, bool $includeBekor = false): array
    {
        // ✅ Use qoldiq_qarz specific method for "Аукционда турган маблағ"
        $qoldiqQarzLots = $this->yerSotuvService->getQoldiqQarzLotlar($tumanPatterns, $dateFilters);

        // Calculate expected amount for qoldiq qarz lots
        $biryolaQoldiqData = ['tushadigan_mablagh' => 0, 'soni' => count($qoldiqQarzLots)];
        if (!empty($qoldiqQarzLots)) {
            $qoldiqSum = \DB::table('yer_sotuvlar')
                ->whereIn('lot_raqami', $qoldiqQarzLots)
                ->selectRaw('SUM(COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0) - COALESCE(auksion_harajati, 0)) as expected')
                ->first();
            $biryolaQoldiqData['tushadigan_mablagh'] = $qoldiqSum->expected ?? 0;
        }

        // Calculate received amount for qoldiq qarz lots
        $biryolaQoldiqFakt = 0;
        if (!empty($qoldiqQarzLots)) {
            $biryolaQoldiqFakt = \DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $qoldiqQarzLots)
                ->sum('tolov_summa');
        }
        $biryolaQoldiq = $biryolaQoldiqData['tushadigan_mablagh'] - $biryolaQoldiqFakt;

        // ✅ Keep original biryola data for other calculations
        $biryolaData = $this->yerSotuvService->getTumanData($tumanPatterns, 'муддатли эмас', $dateFilters);
        $biryolaFakt = $this->yerSotuvService->calculateBiryolaFakt($tumanPatterns, $dateFilters);

        // Bolib data
        $bolibData = $this->yerSotuvService->getTumanData($tumanPatterns, 'муддатли', $dateFilters);
        $bolibTushgan = $this->yerSotuvService->calculateBolibTushgan($tumanPatterns, $dateFilters);
        $bolibTushadigan = $this->yerSotuvService->calculateBolibTushadigan($tumanPatterns, $dateFilters);

        // Calculate bolib tushgan WITH ALL payments (for Амалда тушган маблағ)
        $bolibTushganAll = 0;
        $bolibLotsAll = $this->yerSotuvService->getBolibLotlar($tumanPatterns, $dateFilters);
        if (!empty($bolibLotsAll)) {
            $bolibTushganAll = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $bolibLotsAll)
                ->sum('tolov_summa');
        }

        // Calculate график bo'yicha data
        $grafikData = $this->calculateGrafikData($tumanPatterns, $dateFilters);

        // Calculate combined totals
        $jamiTushgan = $biryolaFakt + $bolibTushgan;
        $jamiQoldiq = $biryolaData['tushadigan_mablagh'] + $bolibTushadigan - $jamiTushgan;

        // Calculate JAMI муддати ўтган қарздорлик
        $biryolaMuddatiUtgan = max(0, $biryolaQoldiq);
        $jamiMuddatiUtgan = $biryolaMuddatiUtgan + $grafikData['muddati_utgan_qarz'];

        $result = [
            // BIR YOLA
            'biryola_soni' => $biryolaData['soni'],
            'biryola_tushadigan' => $biryolaData['tushadigan_mablagh'],
            'biryola_tushgan' => $biryolaFakt,
            'biryola_qoldiq' => $biryolaQoldiq,

            // BOLIB
            'bolib_soni' => $bolibData['soni'],
            'bolib_tushadigan' => $bolibTushadigan,
            'bolib_tushgan' => $bolibTushgan,
            'bolib_tushgan_all' => $bolibTushganAll,
            'bolib_qoldiq' => $bolibTushadigan - $bolibTushgan,
            'grafik_tushadigan' => $grafikData['grafik_tushadigan'],
            'grafik_tushgan' => $grafikData['grafik_tushgan'],
            'muddati_utgan_qarz' => $grafikData['muddati_utgan_qarz'],
            'grafik_foiz' => $grafikData['grafik_foiz'],

            // JAMI
            'jami_soni' => $biryolaData['soni'] + $bolibData['soni'],
            'jami_tushadigan' => $biryolaData['tushadigan_mablagh'] + $bolibTushadigan,
            'jami_tushgan' => $jamiTushgan,
            'jami_qoldiq' => $jamiQoldiq,
            'jami_muddati_utgan' => $jamiMuddatiUtgan,
        ];

        // Add bekor qilinganlar data if requested
        if ($includeBekor) {
            $bekorData = $this->calculateBekorData($tumanPatterns, $dateFilters);
            $result = array_merge($result, $bekorData);
            $result['jami_soni'] += $bekorData['bekor_soni'];
        }

        return $result;
    }

    /**
     * Calculate график bo'yicha data (up to last month)
     */
    private function calculateGrafikData(?array $tumanPatterns, array $dateFilters): array
    {
        $bolibLots = $this->yerSotuvService->getBolibLotlar($tumanPatterns, $dateFilters);
        $bugun = $this->yerSotuvService->getGrafikCutoffDate();

        $grafikTushadigan = 0;
        $grafikTushgan = 0;
        $muddatiUtganQarz = 0;

        if (!empty($bolibLots)) {
            foreach ($bolibLots as $lotRaqami) {
                // Get grafik for this lot
                $lotGrafikTushadigan = DB::table('grafik_tolovlar')
                    ->where('lot_raqami', $lotRaqami)
                    ->whereRaw('CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") <= ?', [$bugun])
                    ->sum('grafik_summa');

                // Get fakt for this lot (EXCLUDING auction org payments)
                $lotGrafikTushgan = DB::table('fakt_tolovlar')
                    ->where('lot_raqami', $lotRaqami)
                    ->where(function($q) {
                        $q->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZ%')
                          ->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH AJ%')
                          ->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZI%')
                          ->orWhereNull('tolash_nom');
                    })
                    ->sum('tolov_summa');

                // Calculate debt for this lot
                $lotDebt = $lotGrafikTushadigan - $lotGrafikTushgan;

                // Only add to total if debt is positive
                if ($lotDebt > 0) {
                    $muddatiUtganQarz += $lotDebt;
                }

                // Add to totals for display
                $grafikTushadigan += $lotGrafikTushadigan;
                $grafikTushgan += $lotGrafikTushgan;
            }
        }

        $grafikFoiz = $grafikTushadigan > 0 ? round(($grafikTushgan / $grafikTushadigan) * 100, 1) : 0;

        return [
            'grafik_tushadigan' => $grafikTushadigan,
            'grafik_tushgan' => $grafikTushgan,
            'muddati_utgan_qarz' => $muddatiUtganQarz,
            'grafik_foiz' => $grafikFoiz,
        ];
    }

    /**
     * Calculate bekor qilinganlar data
     */
    private function calculateBekorData(?array $tumanPatterns, array $dateFilters): array
    {
        $bekorQuery = \App\Models\YerSotuv::query();
        $this->yerSotuvService->applyTumanFilter($bekorQuery, $tumanPatterns);
        $bekorQuery->where('holat', 'Бекор қилинган');
        $this->yerSotuvService->applyDateFilters($bekorQuery, $dateFilters);

        $bekorSoni = $bekorQuery->count();
        $bekorPayments = $this->yerSotuvService->calculateBekorQilinganlarPayments($tumanPatterns, $dateFilters);

        return [
            'bekor_soni' => $bekorSoni,
            'tolangan_mablagh' => $bekorPayments,
            'qaytarilgan_mablagh' => $bekorPayments,
        ];
    }

    /**
     * Calculate totals across all tumans
     */
    public function calculateJamiTotals(array $statistics): array
    {
        $jami = [
            'jami_soni' => 0,
            'jami_tushadigan' => 0,
            'jami_tushgan' => 0,
            'jami_qoldiq' => 0,
            'jami_muddati_utgan' => 0,

            'biryola_soni' => 0,
            'biryola_tushadigan' => 0,
            'biryola_tushgan' => 0,
            'biryola_qoldiq' => 0,

            'bolib_soni' => 0,
            'bolib_tushadigan' => 0,
            'bolib_tushgan' => 0,
            'bolib_tushgan_all' => 0,
            'bolib_qoldiq' => 0,
            'grafik_tushadigan' => 0,
            'grafik_tushgan' => 0,
            'muddati_utgan_qarz' => 0,
        ];

        foreach ($statistics as $stat) {
            foreach ($jami as $key => $value) {
                if ($key !== 'grafik_foiz' && isset($stat[$key])) {
                    $jami[$key] += $stat[$key];
                }
            }
        }

        // Calculate overall grafik foiz
        $jami['grafik_foiz'] = $jami['grafik_tushadigan'] > 0
            ? round(($jami['grafik_tushgan'] / $jami['grafik_tushadigan']) * 100, 1)
            : 0;

        return $jami;
    }

    /**
     * Calculate totals with bekor qilinganlar
     */
    public function calculateJamiTotalsWithBekor(array $statistics): array
    {
        $jami = $this->calculateJamiTotals($statistics);

        // Add bekor fields
        $jami['bekor_soni'] = 0;
        $jami['tolangan_mablagh'] = 0;
        $jami['qaytarilgan_mablagh'] = 0;

        foreach ($statistics as $stat) {
            if (isset($stat['bekor_soni'])) {
                $jami['bekor_soni'] += $stat['bekor_soni'];
                $jami['tolangan_mablagh'] += $stat['tolangan_mablagh'];
                $jami['qaytarilgan_mablagh'] += $stat['qaytarilgan_mablagh'];
            }
        }

        return $jami;
    }
}
