<?php

namespace App\Services;

use App\Models\YerSotuv;
use Illuminate\Support\Facades\DB;

class YerSotuvService
{
    /**
     * Get the cutoff date for grafik calculations
     * Uses LAST day of PREVIOUS month
     */
    public function getGrafikCutoffDate(): string
    {
        return now()->subMonth()->endOfMonth()->format('Y-m-d');
    }

    /**
     * Get tuman search patterns for flexible matching
     */
    public function getTumanPatterns(string $tumanName): array
    {
        $base = str_replace([' т.', ' тумани'], '', $tumanName);

        $patterns = [
            $base,
            $base . ' т.',
            $base . ' тумани',
        ];

        // Handle о/ҳ variants
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

    /**
     * Apply tuman filter to query
     */
    public function applyTumanFilter($query, ?array $tumanPatterns)
    {
        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }
        return $query;
    }

    /**
     * Apply date filters to query
     */
    public function applyDateFilters($query, array $dateFilters)
    {
        if (!empty($dateFilters['auksion_sana_from'])) {
            $query->whereDate('auksion_sana', '>=', $dateFilters['auksion_sana_from']);
        }
        if (!empty($dateFilters['auksion_sana_to'])) {
            $query->whereDate('auksion_sana', '<=', $dateFilters['auksion_sana_to']);
        }
        return $query;
    }

    /**
     * Get basic statistics (jami, bir_yola, bolib)
     */
    public function getTumanData(?array $tumanPatterns = null, ?string $tolovTuri = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        $this->applyTumanFilter($query, $tumanPatterns);

        if ($tolovTuri) {
            $query->where('tolov_turi', $tolovTuri);
        }

        $this->applyDateFilters($query, $dateFilters);

        $data = $query->selectRaw('
            COUNT(*) as soni,
            SUM(maydoni) as maydoni,
            SUM(boshlangich_narx) as boshlangich_narx,
            SUM(sotilgan_narx) as sotilgan_narx,
            SUM(chegirma) as chegirma,
            SUM(COALESCE(golib_tolagan, 0)) as golib_tolagan,
            SUM(COALESCE(shartnoma_summasi, 0)) as shartnoma_summasi,
            SUM(COALESCE(auksion_harajati, 0)) as auksion_harajati
        ')->first();

        // CRITICAL: Tushadigan = golib_tolagan + shartnoma_summasi - auksion_harajati
        $tushadiganMablagh = ($data->golib_tolagan + $data->shartnoma_summasi) - $data->auksion_harajati;

        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'boshlangich_narx' => $data->boshlangich_narx ?? 0,
            'sotilgan_narx' => $data->sotilgan_narx ?? 0,
            'chegirma' => $data->chegirma ?? 0,
            'auksion_harajati' => $data->auksion_harajati ?? 0,
            'tushadigan_mablagh' => $tushadiganMablagh 
        ];
    }

    /**
     * Get auksonda turgan data
     */
    public function getAuksondaTurgan(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        $this->applyTumanFilter($query, $tumanPatterns);

        $query->where(function ($q) {
            $q->where('tolov_turi', '!=', 'муддатли')
                ->where('tolov_turi', '!=', 'муддатли эмас')
                ->orWhereNull('tolov_turi');
        });

        $this->applyDateFilters($query, $dateFilters);

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
        ];
    }

    /**
     * Get mulk qabul qilmagan data (for both муддатли and муддатли эмас)
     */
    public function getMulkQabulQilmagan(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        $this->applyTumanFilter($query, $tumanPatterns);

        $query->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%')
              ->where('asos', 'ПФ-135');

        $this->applyDateFilters($query, $dateFilters);

        $results = $query->get(['tolov_turi', 'golib_tolagan', 'auksion_harajati']);

        $auksionMablagh = 0;
        foreach ($results as $result) {
            $golibTolagan = floatval($result->golib_tolagan ?? 0);
            $auksionHarajati = floatval($result->auksion_harajati ?? 0);

            // Only subtract auksion_harajati for муддатли эмас
            if ($result->tolov_turi === 'муддатли эмас') {
                $auksionMablagh += ($golibTolagan - $auksionHarajati);
            } else {
                $auksionMablagh += $golibTolagan;
            }
        }
        return [
            'soni' => $results->count(),
            'auksion_mablagh' => $auksionMablagh
        ];
    }

    /**
     * Get lot numbers for bo'lib to'lash by tuman
     */
    public function getBolibLotlar(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        $this->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->applyDateFilters($query, $dateFilters);

        return $query->pluck('lot_raqami')->toArray();
    }

/**
 * Get statistics for filtered list
 */
public function getListStatistics($query): array
{
    // Clone query to avoid modifying the original
    $statsQuery = clone $query;

    // Get lot numbers for fakt_tolangan calculation
    $lotRaqamlari = (clone $statsQuery)->pluck('lot_raqami')->toArray();

    // Calculate fakt_tolangan
    $faktTolangan = 0;
    if (!empty($lotRaqamlari)) {
        $faktTolangan = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->sum('tolov_summa');
    }

    // Calculate aggregate statistics
    $data = $statsQuery->selectRaw('
        COUNT(*) as total_lots,
        SUM(maydoni) as total_area,
        SUM(sotilgan_narx) as total_price,
        SUM(boshlangich_narx) as boshlangich_narx,
        SUM(chegirma) as chegirma,
        SUM(COALESCE(golib_tolagan, 0)) as golib_tolagan,
        SUM(COALESCE(shartnoma_summasi, 0)) as shartnoma_summasi,
        SUM(COALESCE(auksion_harajati, 0)) as auksion_harajati
    ')->first();

    return [
        'total_lots' => $data->total_lots ?? 0,
        'total_area' => $data->total_area ?? 0,
        'total_price' => $data->total_price ?? 0,
        'boshlangich_narx' => $data->boshlangich_narx ?? 0,
        'chegirma' => $data->chegirma ?? 0,
        'golib_tolagan' => $data->golib_tolagan ?? 0,
        'shartnoma_summasi' => $data->shartnoma_summasi ?? 0,
        'auksion_harajati' => $data->auksion_harajati ?? 0,
        'fakt_tolangan' => $faktTolangan,
    ];
}
    /**
     * Calculate biryola_fakt: actual payments received for bir yo'la minus mulk qabul
     */
    public function calculateBiryolaFakt(?array $tumanPatterns = null, array $dateFilters = []): float
    {
        $birYola = $this->getTumanData($tumanPatterns, 'муддатли эмас', $dateFilters);
        $mulkQabul = $this->getMulkQabulQilmagan($tumanPatterns, $dateFilters);

        return $birYola['tushadigan_mablagh'] - $mulkQabul['auksion_mablagh'] ;
    }

    /**
     * Calculate bolib_tushgan: actual payments received for bo'lib to'lash
     */
    public function calculateBolibTushgan(?array $tumanPatterns = null, array $dateFilters = []): float
    {
        $bolibLots = $this->getBolibLotlar($tumanPatterns, $dateFilters);

        if (empty($bolibLots)) {
            return 0;
        }

        return DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $bolibLots)
            ->sum('tolov_summa');
    }

    /**
     * Calculate bolib_tushadigan: expected amount for bo'lib to'lash
     */
    public function calculateBolibTushadigan(?array $tumanPatterns = null, array $dateFilters = []): float
    {
        $query = YerSotuv::query();

        $this->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->applyDateFilters($query, $dateFilters);

        $data = $query->selectRaw('
            SUM(COALESCE(golib_tolagan, 0)) as golib_tolagan,
            SUM(COALESCE(shartnoma_summasi, 0)) as shartnoma_summasi,
            SUM(COALESCE(auksion_harajati, 0)) as auksion_harajati
        ')->first();

        return ($data->golib_tolagan + $data->shartnoma_summasi) - $data->auksion_harajati;
    }

    /**
     * SVOD3: Get narhini bo'lib statistics
     */
    public function getNarhiniBolib(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        $this->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->applyDateFilters($query, $dateFilters);

        $data = $query->selectRaw('
            COUNT(*) as soni,
            SUM(maydoni) as maydoni,
            SUM(boshlangich_narx) as boshlangich_narx,
            SUM(sotilgan_narx) as sotilgan_narx,
            SUM(COALESCE(golib_tolagan, 0)) as golib_tolagan,
            SUM(COALESCE(shartnoma_summasi, 0)) as shartnoma_summasi,
            SUM(COALESCE(auksion_harajati, 0)) as auksion_harajati
        ')->first();

        $tushadiganMablagh = ($data->golib_tolagan + $data->shartnoma_summasi) - $data->auksion_harajati;

        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'boshlangich_narx' => $data->boshlangich_narx ?? 0,
            'sotilgan_narx' => $data->sotilgan_narx ?? 0,
            'tushadigan_mablagh' => $tushadiganMablagh 
        ];
    }

    /**
     * SVOD3: Get to'liq to'langanlar statistics
     */
    public function getToliqTolanganlar(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        $this->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->applyDateFilters($query, $dateFilters);

        // Find lots where payment is complete: T - (Fakt + AuksionHarajati) <= 0
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
            SUM(COALESCE(golib_tolagan, 0)) as golib_tolagan,
            SUM(COALESCE(shartnoma_summasi, 0)) as shartnoma_summasi,
            SUM(COALESCE(auksion_harajati, 0)) as auksion_harajati
        ')->first();

        $tushadiganMablagh = ($data->golib_tolagan + $data->shartnoma_summasi) - $data->auksion_harajati;

        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'boshlangich_narx' => $data->boshlangich_narx ?? 0,
            'sotilgan_narx' => $data->sotilgan_narx ?? 0,
            'tushadigan_mablagh' => $tushadiganMablagh ,
            'tushgan_summa' => $tushadiganMablagh // For completed payments, tushgan = tushadigan
        ];
    }

    /**
     * SVOD3: Get nazoratdagilar statistics
     */
    public function getNazoratdagilar(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        $this->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->applyDateFilters($query, $dateFilters);

        // Find lots with outstanding balance: T - (Fakt + AuksionHarajati) > 0
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

        // Get lot numbers BEFORE aggregation
        $lotRaqamlari = (clone $query)->pluck('lot_raqami')->toArray();

        // Now get aggregated data
        $data = $query->selectRaw('
            COUNT(*) as soni,
            SUM(maydoni) as maydoni,
            SUM(boshlangich_narx) as boshlangich_narx,
            SUM(sotilgan_narx) as sotilgan_narx,
            SUM(COALESCE(golib_tolagan, 0)) as golib_tolagan,
            SUM(COALESCE(shartnoma_summasi, 0)) as shartnoma_summasi,
            SUM(COALESCE(auksion_harajati, 0)) as auksion_harajati
        ')->first();

        $tushadiganMablagh = ($data->golib_tolagan + $data->shartnoma_summasi) - $data->auksion_harajati;

        // Calculate tushgan summa
        $tushganSumma = 0;
        if (!empty($lotRaqamlari)) {
            $tushganSumma = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari)
                ->sum('tolov_summa');
        }

        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'boshlangich_narx' => $data->boshlangich_narx ?? 0,
            'sotilgan_narx' => $data->sotilgan_narx ?? 0,
            'tushadigan_mablagh' => $tushadiganMablagh ,
            'tushgan_summa' => $tushganSumma
        ];
    }

    /**
     * SVOD3: Get grafik ortda statistics
     */
    public function getGrafikOrtda(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $bugun = $this->getGrafikCutoffDate();

        $query = YerSotuv::query();

        $this->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->applyDateFilters($query, $dateFilters);

        // Find lots where: outstanding balance AND grafik > fakt
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

        // Get lot numbers BEFORE aggregation
        $lotRaqamlari = (clone $query)->pluck('lot_raqami')->toArray();

        // Now get aggregated data
        $data = $query->selectRaw('
            COUNT(*) as soni,
            SUM(maydoni) as maydoni
        ')->first();

        if (empty($lotRaqamlari)) {
            return [
                'soni' => 0,
                'maydoni' => 0,
                'grafik_summa' => 0,
                'fakt_summa' => 0,
                'foiz' => 0
            ];
        }

        $grafikSumma = DB::table('grafik_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->whereRaw('CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") <= ?', [$bugun])
            ->sum('grafik_summa');

        $faktSumma = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->sum('tolov_summa');

        $foiz = $grafikSumma > 0 ? round(($faktSumma / $grafikSumma) * 100, 1) : 0;

        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'grafik_summa' => $grafikSumma,
            'fakt_summa' => $faktSumma,
            'foiz' => $foiz
        ];
    }

    /**
 * Get complete statistics for main page (SVOD1)
 */
public function getDetailedStatistics(array $dateFilters = []): array
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
        $tumanPatterns = $this->getTumanPatterns($tuman);

        $stat = [
            'tuman' => $tuman,
            'jami' => $this->getTumanData($tumanPatterns, null, $dateFilters),
            'bir_yola' => $this->getTumanData($tumanPatterns, 'муддатли эмас', $dateFilters),
            'bolib' => $this->getTumanData($tumanPatterns, 'муддатли', $dateFilters),
            'auksonda' => $this->getAuksondaTurgan($tumanPatterns, $dateFilters),
            'mulk_qabul' => $this->getMulkQabulQilmagan($tumanPatterns, $dateFilters),
            'biryola_fakt' => $this->calculateBiryolaFakt($tumanPatterns, $dateFilters),
            'bolib_tushgan' => $this->calculateBolibTushgan($tumanPatterns, $dateFilters),
            'bolib_tushadigan' => $this->calculateBolibTushadigan($tumanPatterns, $dateFilters),
        ];

        // RECALCULATE JAMI TUSHADIGAN: xx + yy + vv
        $stat['jami']['tushadigan_mablagh'] = 
            $stat['bir_yola']['tushadigan_mablagh'] +  // xx
            $stat['bolib_tushadigan'] +                 // yy
            $stat['mulk_qabul']['auksion_mablagh'];    // vv
        
        $stat['jami_tushgan_yigindi'] = $stat['biryola_fakt'] + $stat['bolib_tushgan'];

        $statistics[] = $stat;
    }

    // Calculate JAMI totals
    $jami = [
        'jami' => $this->getTumanData(null, null, $dateFilters),
        'bir_yola' => $this->getTumanData(null, 'муддатли эмас', $dateFilters),
        'bolib' => $this->getTumanData(null, 'муддатли', $dateFilters),
        'auksonda' => $this->getAuksondaTurgan(null, $dateFilters),
        'mulk_qabul' => $this->getMulkQabulQilmagan(null, $dateFilters),
        'biryola_fakt' => $this->calculateBiryolaFakt(null, $dateFilters),
        'bolib_tushgan' => $this->calculateBolibTushgan(null, $dateFilters),
        'bolib_tushadigan' => $this->calculateBolibTushadigan(null, $dateFilters),
    ];

    // RECALCULATE JAMI TUSHADIGAN: xx + yy + vv
    $jami['jami']['tushadigan_mablagh'] = 
        $jami['bir_yola']['tushadigan_mablagh'] +  // xx
        $jami['bolib_tushadigan'] +                 // yy
        $jami['mulk_qabul']['auksion_mablagh'];    // vv

    $jami['jami_tushgan_yigindi'] = $jami['biryola_fakt'] + $jami['bolib_tushgan'];

    return [
        'tumanlar' => $statistics,
        'jami' => $jami
    ];
}

    /**
     * Get complete statistics for SVOD3 page
     */
    public function getSvod3Statistics(array $dateFilters = []): array
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
                'narhini_bolib' => $this->getNarhiniBolib($tumanPatterns, $dateFilters),
                'toliq_tolanganlar' => $this->getToliqTolanganlar($tumanPatterns, $dateFilters),
                'nazoratdagilar' => $this->getNazoratdagilar($tumanPatterns, $dateFilters),
                'grafik_ortda' => $this->getGrafikOrtda($tumanPatterns, $dateFilters)
            ];

            $result['tumanlar'][] = $stat;
            $this->addToSvod3Total($result['jami'], $stat);
        }

        return $result;
    }

    /**
     * Initialize SVOD3 total structure
     */
    private function initializeSvod3Total(): array
    {
        return [
            'narhini_bolib' => [
                'soni' => 0,
                'maydoni' => 0,
                'boshlangich_narx' => 0,
                'sotilgan_narx' => 0,
                'tushadigan_mablagh' => 0
            ],
            'toliq_tolanganlar' => [
                'soni' => 0,
                'maydoni' => 0,
                'boshlangich_narx' => 0,
                'sotilgan_narx' => 0,
                'tushadigan_mablagh' => 0,
                'tushgan_summa' => 0
            ],
            'nazoratdagilar' => [
                'soni' => 0,
                'maydoni' => 0,
                'boshlangich_narx' => 0,
                'sotilgan_narx' => 0,
                'tushadigan_mablagh' => 0,
                'tushgan_summa' => 0
            ],
            'grafik_ortda' => [
                'soni' => 0,
                'maydoni' => 0,
                'grafik_summa' => 0,
                'fakt_summa' => 0,
                'foiz' => 0
            ]
        ];
    }

    /**
     * Add tuman statistics to SVOD3 total
     */
    private function addToSvod3Total(array &$jami, array $stat): void
    {
        foreach (['soni', 'maydoni', 'boshlangich_narx', 'sotilgan_narx', 'tushadigan_mablagh'] as $field) {
            $jami['narhini_bolib'][$field] += $stat['narhini_bolib'][$field];
        }

        foreach (['soni', 'maydoni', 'boshlangich_narx', 'sotilgan_narx', 'tushadigan_mablagh', 'tushgan_summa'] as $field) {
            $jami['toliq_tolanganlar'][$field] += $stat['toliq_tolanganlar'][$field];
        }

        foreach (['soni', 'maydoni', 'boshlangich_narx', 'sotilgan_narx', 'tushadigan_mablagh', 'tushgan_summa'] as $field) {
            $jami['nazoratdagilar'][$field] += $stat['nazoratdagilar'][$field];
        }

        foreach (['soni', 'maydoni', 'grafik_summa', 'fakt_summa'] as $field) {
            $jami['grafik_ortda'][$field] += $stat['grafik_ortda'][$field];
        }

        if ($jami['grafik_ortda']['grafik_summa'] > 0) {
            $jami['grafik_ortda']['foiz'] = round(
                ($jami['grafik_ortda']['fakt_summa'] / $jami['grafik_ortda']['grafik_summa']) * 100,
                1
            );
        }
    }

    /**
     * Calculate payment comparison for detail page
     */
    public function calculateTolovTaqqoslash(YerSotuv $yer): array
    {
        $grafikByMonth = $yer->grafikTolovlar->groupBy(function ($item) {
            return $item->yil . '-' . str_pad($item->oy, 2, '0', STR_PAD_LEFT);
        });

        $faktByMonth = $yer->faktTolovlar->groupBy(function ($item) {
            return $item->tolov_sana->format('Y-m');
        });

        $allMonths = [];

        // Add grafik months
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

        // Add advance payment months (not in grafik)
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

        $taqqoslash = [];
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

     /**
     * Get monthly comparative data for monitoring_mirzayev
     */
    public function getMonthlyComparativeData(array $filters = []): array
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

        $currentYear = now()->year;
        $currentMonth = now()->month;
        
        // Determine selected month (default to current month)
        $selectedMonth = $filters['month'] ?? $currentMonth;
        $selectedYear = $filters['year'] ?? $currentYear;

        $result = [
            'tumanlar' => [],
            'jami' => [
                'selected_month' => [
                    'plan' => 0,
                    'fakt' => 0,
                    'percentage' => 0
                ],
                'year_to_date' => [
                    'plan' => 0,
                    'fakt' => 0,
                    'percentage' => 0
                ],
                'full_year' => [
                    'plan' => 0,
                    'fakt' => 0,
                    'percentage' => 0
                ]
            ]
        ];

        foreach ($tumanlar as $tuman) {
            $tumanPatterns = $this->getTumanPatterns($tuman);
            
            // Get lots for this tuman (муддатли only)
            $query = YerSotuv::query();
            $this->applyTumanFilter($query, $tumanPatterns);
            $query->where('tolov_turi', 'муддатли');
            
            $lotRaqamlari = $query->pluck('lot_raqami')->toArray();

            if (empty($lotRaqamlari)) {
                continue;
            }

            // SELECTED MONTH
            $selectedMonthPlan = DB::table('grafik_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari)
                ->where('yil', $selectedYear)
                ->where('oy', $selectedMonth)
                ->sum('grafik_summa');

            $selectedMonthFakt = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari)
                ->whereYear('tolov_sana', $selectedYear)
                ->whereMonth('tolov_sana', $selectedMonth)
                ->sum('tolov_summa');

            $selectedMonthPercentage = $selectedMonthPlan > 0 
                ? round(($selectedMonthFakt / $selectedMonthPlan) * 100) 
                : 0;

            // YEAR TO DATE (January to selected month)
            $ytdPlan = DB::table('grafik_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari)
                ->where('yil', $selectedYear)
                ->where('oy', '<=', $selectedMonth)
                ->sum('grafik_summa');

            $ytdFakt = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari)
                ->whereYear('tolov_sana', $selectedYear)
                ->whereMonth('tolov_sana', '<=', $selectedMonth)
                ->sum('tolov_summa');

            $ytdPercentage = $ytdPlan > 0 
                ? round(($ytdFakt / $ytdPlan) * 100) 
                : 0;

            // FULL YEAR (all 12 months)
            $fullYearPlan = DB::table('grafik_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari)
                ->where('yil', $selectedYear)
                ->sum('grafik_summa');

            $fullYearFakt = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari)
                ->whereYear('tolov_sana', $selectedYear)
                ->sum('tolov_summa');

            $fullYearPercentage = $fullYearPlan > 0 
                ? round(($fullYearFakt / $fullYearPlan) * 100) 
                : 0;

            // Add to result
            $result['tumanlar'][] = [
                'tuman' => $tuman,
                'selected_month' => [
                    'plan' => $selectedMonthPlan,
                    'fakt' => $selectedMonthFakt,
                    'percentage' => $selectedMonthPercentage
                ],
                'year_to_date' => [
                    'plan' => $ytdPlan,
                    'fakt' => $ytdFakt,
                    'percentage' => $ytdPercentage
                ],
                'full_year' => [
                    'plan' => $fullYearPlan,
                    'fakt' => $fullYearFakt,
                    'percentage' => $fullYearPercentage
                ]
            ];

            // Add to totals
            $result['jami']['selected_month']['plan'] += $selectedMonthPlan;
            $result['jami']['selected_month']['fakt'] += $selectedMonthFakt;
            
            $result['jami']['year_to_date']['plan'] += $ytdPlan;
            $result['jami']['year_to_date']['fakt'] += $ytdFakt;
            
            $result['jami']['full_year']['plan'] += $fullYearPlan;
            $result['jami']['full_year']['fakt'] += $fullYearFakt;
        }

        // Calculate total percentages
        $result['jami']['selected_month']['percentage'] = $result['jami']['selected_month']['plan'] > 0
            ? round(($result['jami']['selected_month']['fakt'] / $result['jami']['selected_month']['plan']) * 100)
            : 0;

        $result['jami']['year_to_date']['percentage'] = $result['jami']['year_to_date']['plan'] > 0
            ? round(($result['jami']['year_to_date']['fakt'] / $result['jami']['year_to_date']['plan']) * 100)
            : 0;

        $result['jami']['full_year']['percentage'] = $result['jami']['full_year']['plan'] > 0
            ? round(($result['jami']['full_year']['fakt'] / $result['jami']['full_year']['plan']) * 100)
            : 0;

        // Add meta information
        $result['meta'] = [
            'selected_month' => $selectedMonth,
            'selected_month_name' => $this->getMonthName($selectedMonth),
            'selected_year' => $selectedYear,
            'current_month' => $currentMonth,
            'current_year' => $currentYear
        ];

        return $result;
    }
private function getMonthName(int $month): string
{
    $months = [
        1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
        5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
        9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
    ];
    return $months[$month] ?? 'Unknown';
}
}