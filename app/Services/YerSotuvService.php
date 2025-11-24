<?php

namespace App\Services;

use App\Models\YerSotuv;
use App\Models\GlobalQoldiq;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class YerSotuvService
{
    /**
     * Apply base filters to exclude canceled records
     * CRITICAL: Must be applied to ALL queries
     */
    public function applyBaseFilters($query)
    {
        return $query->where('holat', '!=', 'Бекор қилинган')
            ->whereNotNull('holat');
    }

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
     * Calculate total grafik tushadigan (scheduled amount up to last month)
     * for муддатли payments with date filters
     */
    public function calculateGrafikTushadigan(?array $tumanPatterns = null, array $dateFilters = [], string $tolovTuri = 'муддатли'): float
    {
        $query = YerSotuv::query();

        // CRITICAL: Apply base filters
        $this->applyBaseFilters($query);
        $this->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', $tolovTuri);
        $this->applyDateFilters($query, $dateFilters);

        $lotRaqamlari = $query->pluck('lot_raqami')->toArray();

        if (empty($lotRaqamlari)) {
            Log::info('Grafik Tushadigan Calculation - No lots found', [
                'tuman_patterns' => $tumanPatterns,
                'tolov_turi' => $tolovTuri,
                'date_filters' => $dateFilters,
            ]);
            return 0;
        }

        // Use last month's end date as cutoff
        $cutoffDate = $this->getGrafikCutoffDate();

        $grafikSumma = DB::table('grafik_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->whereRaw('CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") <= ?', [$cutoffDate])
            ->sum('grafik_summa');

        Log::info('Grafik Tushadigan Calculation', [
            'tuman_patterns' => $tumanPatterns,
            'tolov_turi' => $tolovTuri,
            'date_filters' => $dateFilters,
            'lots_count' => count($lotRaqamlari),
            'lot_raqamlari_sample' => array_slice($lotRaqamlari, 0, 5),
            'cutoff_date' => $cutoffDate,
            'grafik_summa' => $grafikSumma
        ]);

        return $grafikSumma;
    }


    /**
     * Get basic statistics (jami, bir_yola, bolib)
     */
    public function getTumanData(?array $tumanPatterns = null, ?string $tolovTuri = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        // CRITICAL: Exclude canceled records
        $this->applyBaseFilters($query);
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

        // CRITICAL: Different formulas based on tolov_turi
        $logContext = [
            'tuman_patterns' => $tumanPatterns,
            'tolov_turi' => $tolovTuri,
            'date_filters' => $dateFilters,
            'raw_data' => [
                'soni' => $data->soni ?? 0,
                'golib_tolagan' => $data->golib_tolagan ?? 0,
                'shartnoma_summasi' => $data->shartnoma_summasi ?? 0,
                'auksion_harajati' => $data->auksion_harajati ?? 0,
            ]
        ];

        if ($tolovTuri === 'муддатли эмас') {
            // TM1: BIR YO'LA
            $tushadiganMablagh = $data->golib_tolagan - $data->auksion_harajati;

            Log::info('TM1 Calculation (BIR YO\'LA - муддатли эмас)', array_merge($logContext, [
                'formula' => 'golib_tolagan - auksion_harajati',
                'calculation_steps' => [
                    'step_1' => "golib_tolagan = {$data->golib_tolagan}",
                    'step_2' => "auksion_harajati = {$data->auksion_harajati}",
                    'step_3' => "tushadigan_mablagh = {$data->golib_tolagan} - {$data->auksion_harajati}",
                ],
                'result' => $tushadiganMablagh
            ]));
        } elseif ($tolovTuri === 'муддатли') {
            // TM2: BO'LIB
            $tushadiganMablagh = ($data->golib_tolagan + $data->shartnoma_summasi) - $data->auksion_harajati;

            Log::info('TM2 Calculation (BO\'LIB - муддатли)', array_merge($logContext, [
                'formula' => '(golib_tolagan + shartnoma_summasi) - auksion_harajati',
                'calculation_steps' => [
                    'step_1' => "golib_tolagan = {$data->golib_tolagan}",
                    'step_2' => "shartnoma_summasi = {$data->shartnoma_summasi}",
                    'step_3' => "sum_golib_shartnoma = {$data->golib_tolagan} + {$data->shartnoma_summasi} = " . ($data->golib_tolagan + $data->shartnoma_summasi),
                    'step_4' => "auksion_harajati = {$data->auksion_harajati}",
                    'step_5' => "tushadigan_mablagh = " . ($data->golib_tolagan + $data->shartnoma_summasi) . " - {$data->auksion_harajati}",
                ],
                'result' => $tushadiganMablagh
            ]));
        } else {
            // JAMI: Sum of individual calculations (will be recalculated in getDetailedStatistics)
            $tushadiganMablagh = 0; // Placeholder

            Log::info('JAMI Calculation (tolov_turi = null)', array_merge($logContext, [
                'note' => 'Placeholder - will be recalculated as sum of TM1 + TM2 + mulk_qabul',
                'result' => $tushadiganMablagh
            ]));
        }

        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'boshlangich_narx' => $data->boshlangich_narx ?? 0,
            'sotilgan_narx' => $data->sotilgan_narx ?? 0,
            'chegirma' => $data->chegirma ?? 0,
            'auksion_harajati' => $data->auksion_harajati ?? 0,
            'tushadigan_mablagh' => $tushadiganMablagh,
            'golib_tolagan' => $data->golib_tolagan ?? 0,
            'shartnoma_summasi' => $data->shartnoma_summasi ?? 0,
        ];
    }


    /**
     * Get auksonda turgan data
     */
    public function getAuksondaTurgan(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        // CRITICAL: Exclude canceled records
        $this->applyBaseFilters($query);
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

        // CRITICAL: Exclude canceled records
        $this->applyBaseFilters($query);
        $this->applyTumanFilter($query, $tumanPatterns);

        $query->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%')
            ->where('asos', 'ПФ-135');

        $this->applyDateFilters($query, $dateFilters);

        $results = $query->get(['tolov_turi', 'golib_tolagan', 'auksion_harajati', 'lot_raqami']);

        $auksionMablagh = 0;
        $calculationLog = [];

        foreach ($results as $result) {
            $golibTolagan = floatval($result->golib_tolagan ?? 0);
            $auksionHarajati = floatval($result->auksion_harajati ?? 0);
            $itemValue = 0;

            // Only subtract auksion_harajati for муддатли эмас
            if ($result->tolov_turi === 'муддатли эмас') {
                $itemValue = $golibTolagan - $auksionHarajati;
                $calculationLog[] = [
                    'lot_raqami' => $result->lot_raqami,
                    'tolov_turi' => $result->tolov_turi,
                    'formula' => 'golib_tolagan - auksion_harajati',
                    'golib_tolagan' => $golibTolagan,
                    'auksion_harajati' => $auksionHarajati,
                    'item_value' => $itemValue
                ];
            } else {
                $itemValue = $golibTolagan;
                $calculationLog[] = [
                    'lot_raqami' => $result->lot_raqami,
                    'tolov_turi' => $result->tolov_turi,
                    'formula' => 'golib_tolagan (no subtraction)',
                    'golib_tolagan' => $golibTolagan,
                    'auksion_harajati' => $auksionHarajati,
                    'item_value' => $itemValue
                ];
            }

            $auksionMablagh += $itemValue;
        }

        Log::info('MULK QABUL QILMAGAN Calculation', [
            'tuman_patterns' => $tumanPatterns,
            'date_filters' => $dateFilters,
            'total_records' => $results->count(),
            'item_calculations' => $calculationLog,
            'total_auksion_mablagh' => $auksionMablagh
        ]);

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

        // CRITICAL: Exclude canceled records
        $this->applyBaseFilters($query);
        $this->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->applyDateFilters($query, $dateFilters);

        return $query->pluck('lot_raqami')->toArray();
    }

    /**
     * Get lot numbers for bir yo'la to'lash by tuman (excluding mulk qabul)
     */
    public function getBiryolaLotlar(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        // CRITICAL: Exclude canceled records
        $this->applyBaseFilters($query);
        $this->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли эмас');

        // Exclude mulk qabul qilmagan
        $query->where(function ($q) {
            $q->where('holat', 'not like', '%Ishtirokchi roziligini kutish jarayonida (34)%')
                ->orWhere('asos', '!=', 'ПФ-135')
                ->orWhereNull('holat')
                ->orWhereNull('asos');
        });

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

        // Calculate fakt_tolangan FROM fakt_tolovlar ONLY
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
     * Calculate biryola_fakt: ONLY actual payments from fakt_tolovlar for bir yo'la (excluding mulk qabul)
     * CRITICAL: Must use fakt_tolovlar table ONLY
     */
    public function calculateBiryolaFakt(?array $tumanPatterns = null, array $dateFilters = []): float
    {
        $biryolaLots = $this->getBiryolaLotlar($tumanPatterns, $dateFilters);

        if (empty($biryolaLots)) {
            Log::info('BIRYOLA FAKT Calculation', [
                'tuman_patterns' => $tumanPatterns,
                'date_filters' => $dateFilters,
                'lots_count' => 0,
                'result' => 0
            ]);
            return 0;
        }

        // Get ONLY from fakt_tolovlar
        $faktSum = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $biryolaLots)
            ->sum('tolov_summa');

        Log::info('BIRYOLA FAKT Calculation', [
            'tuman_patterns' => $tumanPatterns,
            'date_filters' => $dateFilters,
            'lots_count' => count($biryolaLots),
            'lot_raqamlari' => $biryolaLots,
            'source' => 'fakt_tolovlar table ONLY',
            'result' => $faktSum
        ]);

        return $faktSum;
    }

    /**
     * Calculate bolib_tushgan: ONLY actual payments from fakt_tolovlar for bo'lib to'lash
     * CRITICAL: Must use fakt_tolovlar table ONLY
     */
    public function calculateBolibTushgan(?array $tumanPatterns = null, array $dateFilters = []): float
    {
        $bolibLots = $this->getBolibLotlar($tumanPatterns, $dateFilters);

        if (empty($bolibLots)) {
            Log::info('BOLIB TUSHGAN Calculation', [
                'tuman_patterns' => $tumanPatterns,
                'date_filters' => $dateFilters,
                'lots_count' => 0,
                'result' => 0
            ]);
            return 0;
        }

        // Get ONLY from fakt_tolovlar
        $faktSum = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $bolibLots)
            ->sum('tolov_summa');

        Log::info('BOLIB TUSHGAN Calculation', [
            'tuman_patterns' => $tumanPatterns,
            'date_filters' => $dateFilters,
            'lots_count' => count($bolibLots),
            'lot_raqamlari' => $bolibLots,
            'source' => 'fakt_tolovlar table ONLY',
            'result' => $faktSum
        ]);

        return $faktSum;
    }

    /**
     * Calculate bolib_tushadigan: expected amount for bo'lib to'lash
     */
    public function calculateBolibTushadigan(?array $tumanPatterns = null, array $dateFilters = []): float
    {
        $query = YerSotuv::query();

        // CRITICAL: Exclude canceled records
        $this->applyBaseFilters($query);
        $this->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->applyDateFilters($query, $dateFilters);

        $data = $query->selectRaw('
        SUM(COALESCE(golib_tolagan, 0)) as golib_tolagan,
        SUM(COALESCE(shartnoma_summasi, 0)) as shartnoma_summasi,
        SUM(COALESCE(auksion_harajati, 0)) as auksion_harajati
    ')->first();

        // BO'LIB TUSHADIGAN: Golib_tolagan + Shartnoma_summasi - Auksion_harajati
        $result = ($data->golib_tolagan + $data->shartnoma_summasi) - $data->auksion_harajati;

        Log::info('BOLIB TUSHADIGAN Calculation (Expected amount for bo\'lib to\'lash)', [
            'tuman_patterns' => $tumanPatterns,
            'date_filters' => $dateFilters,
            'formula' => '(golib_tolagan + shartnoma_summasi) - auksion_harajati',
            'calculation_steps' => [
                'step_1' => "golib_tolagan = {$data->golib_tolagan}",
                'step_2' => "shartnoma_summasi = {$data->shartnoma_summasi}",
                'step_3' => "sum = {$data->golib_tolagan} + {$data->shartnoma_summasi} = " . ($data->golib_tolagan + $data->shartnoma_summasi),
                'step_4' => "auksion_harajati = {$data->auksion_harajati}",
                'step_5' => "result = " . ($data->golib_tolagan + $data->shartnoma_summasi) . " - {$data->auksion_harajati}",
            ],
            'result' => $result
        ]);

        return $result;
    }

    /**
     * SVOD3: Get narhini bo'lib statistics
     */
    public function getNarhiniBolib(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        // CRITICAL: Exclude canceled records
        $this->applyBaseFilters($query);
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

        // CRITICAL: Exclude canceled records
        $this->applyBaseFilters($query);
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
            AND ys.holat != "Бекор қилинган"
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
            'tushadigan_mablagh' => $tushadiganMablagh,
            'tushgan_summa' => $tushadiganMablagh // For completed payments, tushgan = tushadigan
        ];
    }

    /**
     * SVOD3: Get nazoratdagilar statistics
     */
    public function getNazoratdagilar(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        // CRITICAL: Exclude canceled records
        $this->applyBaseFilters($query);
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
            AND ys.holat != "Бекор қилинган"
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

        // Calculate tushgan summa FROM fakt_tolovlar ONLY
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
            'tushadigan_mablagh' => $tushadiganMablagh,
            'tushgan_summa' => $tushganSumma
        ];
    }

    /**
     * SVOD3: Get grafik ortda statistics
     */
    /**
     * SVOD3: Get grafik ortda statistics
     */
    public function getGrafikOrtda(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $bugun = $this->getGrafikCutoffDate();

        $query = YerSotuv::query();

        // CRITICAL: Exclude canceled records
        $this->applyBaseFilters($query);
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
        AND ys.holat != "Бекор қилинган"
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
                'muddati_utgan_qarz' => 0
            ];
        }

        $grafikSumma = DB::table('grafik_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->whereRaw('CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") <= ?', [$bugun])
            ->sum('grafik_summa');

        // Get FROM fakt_tolovlar ONLY
        $faktSumma = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->sum('tolov_summa');

        // Calculate overdue debt (график - факт)
        $muddatiUtganQarz = $grafikSumma - $faktSumma;
        // Ensure it's not negative
        $muddatiUtganQarz = max(0, $muddatiUtganQarz);

        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'grafik_summa' => $grafikSumma,
            'fakt_summa' => $faktSumma,
            'muddati_utgan_qarz' => $muddatiUtganQarz
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

        foreach (['soni', 'maydoni', 'grafik_summa', 'fakt_summa', 'muddati_utgan_qarz'] as $field) {
            $jami['grafik_ortda'][$field] += $stat['grafik_ortda'][$field];
        }
    }

    /**
     * Get complete statistics for main page (SVOD1)
     */
    public function getDetailedStatistics(array $dateFilters = []): array
    {
        Log::info('========== STARTING DETAILED STATISTICS CALCULATION (SVOD1) ==========', [
            'date_filters' => $dateFilters
        ]);

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
            Log::info("---------- Processing Tuman: {$tuman} ----------");

            $tumanPatterns = $this->getTumanPatterns($tuman);

            $stat = [
                'tuman' => $tuman,
                'jami' => $this->getTumanData($tumanPatterns, null, $dateFilters),
                'bir_yola' => $this->getTumanData($tumanPatterns, 'муддатли эмас', $dateFilters), // golib_tolagan - auksion_harajati
                'bolib' => $this->getTumanData($tumanPatterns, 'муддатли', $dateFilters), // golib_tolagan + shartnoma - auksion
                'auksonda' => $this->getAuksondaTurgan($tumanPatterns, $dateFilters),
                'mulk_qabul' => $this->getMulkQabulQilmagan($tumanPatterns, $dateFilters),
                'biryola_fakt' => $this->calculateBiryolaFakt($tumanPatterns, $dateFilters),
                'bolib_tushgan' => $this->calculateBolibTushgan($tumanPatterns, $dateFilters),
                'bolib_tushadigan' => $this->calculateBolibTushadigan($tumanPatterns, $dateFilters), // golib_tolagan + shartnoma - auksion
            ];

            // CALCULATE JAMI TUSHADIGAN:
            // bir_yola_tushadigan + bolib_tushadigan + mulk_qabul
            $stat['jami']['tushadigan_mablagh'] =
                $stat['bir_yola']['tushadigan_mablagh'] +  // golib_tolagan - auksion (муддатли эмас)
                $stat['bolib_tushadigan'] +                 // golib_tolagan + shartnoma - auksion (муддатли)
                $stat['mulk_qabul']['auksion_mablagh'];    // mulk qabul amount

            $stat['jami_tushgan_yigindi'] = $stat['biryola_fakt'] + $stat['bolib_tushgan'];

            Log::info("JAMI TUSHADIGAN Calculation for {$tuman}", [
                'formula' => 'bir_yola_tushadigan + bolib_tushadigan + mulk_qabul',
                'calculation_steps' => [
                    'bir_yola_tushadigan' => $stat['bir_yola']['tushadigan_mablagh'],
                    'bolib_tushadigan' => $stat['bolib_tushadigan'],
                    'mulk_qabul' => $stat['mulk_qabul']['auksion_mablagh'],
                    'sum' => "{$stat['bir_yola']['tushadigan_mablagh']} + {$stat['bolib_tushadigan']} + {$stat['mulk_qabul']['auksion_mablagh']}",
                ],
                'result' => $stat['jami']['tushadigan_mablagh']
            ]);

            Log::info("JAMI TUSHGAN YIGINDI for {$tuman}", [
                'formula' => 'biryola_fakt + bolib_tushgan',
                'biryola_fakt' => $stat['biryola_fakt'],
                'bolib_tushgan' => $stat['bolib_tushgan'],
                'result' => $stat['jami_tushgan_yigindi']
            ]);

            $statistics[] = $stat;
        }

        // Calculate JAMI totals
        Log::info("========== Calculating OVERALL JAMI TOTALS ==========");

        $jami = [
            'jami' => $this->getTumanData(null, null, $dateFilters),
            'bir_yola' => $this->getTumanData(null, 'муддатли эмас', $dateFilters), // golib_tolagan - auksion_harajati
            'bolib' => $this->getTumanData(null, 'муддатли', $dateFilters), // golib_tolagan + shartnoma - auksion
            'auksonda' => $this->getAuksondaTurgan(null, $dateFilters),
            'mulk_qabul' => $this->getMulkQabulQilmagan(null, $dateFilters),
            'biryola_fakt' => $this->calculateBiryolaFakt(null, $dateFilters),
            'bolib_tushgan' => $this->calculateBolibTushgan(null, $dateFilters),
            'bolib_tushadigan' => $this->calculateBolibTushadigan(null, $dateFilters), // golib_tolagan + shartnoma - auksion
        ];

        // CALCULATE JAMI TUSHADIGAN:
        // bir_yola_tushadigan + bolib_tushadigan + mulk_qabul
        $jami['jami']['tushadigan_mablagh'] =
            $jami['bir_yola']['tushadigan_mablagh'] +  // golib_tolagan - auksion (муддатли эмас)
            $jami['bolib_tushadigan'] +                 // golib_tolagan + shartnoma - auksion (муддатли)
            $jami['mulk_qabul']['auksion_mablagh'];    // mulk qabul amount

        $jami['jami_tushgan_yigindi'] = $jami['biryola_fakt'] + $jami['bolib_tushgan'];

        Log::info("OVERALL JAMI TUSHADIGAN Calculation", [
            'formula' => 'bir_yola_tushadigan + bolib_tushadigan + mulk_qabul',
            'calculation_steps' => [
                'bir_yola_tushadigan' => $jami['bir_yola']['tushadigan_mablagh'],
                'bolib_tushadigan' => $jami['bolib_tushadigan'],
                'mulk_qabul' => $jami['mulk_qabul']['auksion_mablagh'],
                'sum' => "{$jami['bir_yola']['tushadigan_mablagh']} + {$jami['bolib_tushadigan']} + {$jami['mulk_qabul']['auksion_mablagh']}",
            ],
            'result' => $jami['jami']['tushadigan_mablagh']
        ]);

        Log::info("OVERALL JAMI TUSHGAN YIGINDI", [
            'formula' => 'biryola_fakt + bolib_tushgan',
            'biryola_fakt' => $jami['biryola_fakt'],
            'bolib_tushgan' => $jami['bolib_tushgan'],
            'result' => $jami['jami_tushgan_yigindi']
        ]);

        Log::info('========== DETAILED STATISTICS CALCULATION COMPLETED ==========');

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
                'muddati_utgan_qarz' => 0
            ]
        ];
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
     * Uses fakt_tolovlar ONLY for actual payment calculations
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

        // Use last completed month by default
        $lastMonth = now()->subMonth()->endOfMonth();
        $selectedMonth = $filters['month'] ?? $lastMonth->month;
        $selectedYear = $filters['year'] ?? $lastMonth->year;
        $tolovTuriFilter = $filters['tolov_turi'] ?? 'all'; // 'all', 'muddatli', 'muddatli_emas'

        $result = [
            'tumanlar_muddatli' => [],
            'tumanlar_muddatli_emas' => [],
            'jami_muddatli' => [
                'selected_month' => ['plan' => 0, 'fakt' => 0, 'percentage' => 0],
                'year_to_date' => ['plan' => 0, 'fakt' => 0, 'percentage' => 0],
                'full_year' => ['plan' => 0, 'fakt' => 0, 'percentage' => 0]
            ],
            'jami_muddatli_emas' => [
                'selected_month' => ['fakt' => 0],
                'year_to_date' => ['fakt' => 0],
                'full_year' => ['fakt' => 0]
            ],
            'jami_umumiy' => [
                'selected_month' => ['plan' => 0, 'fakt' => 0, 'percentage' => 0],
                'year_to_date' => ['plan' => 0, 'fakt' => 0, 'percentage' => 0],
                'full_year' => ['plan' => 0, 'fakt' => 0, 'percentage' => 0]
            ]
        ];

        foreach ($tumanlar as $tuman) {
            $tumanPatterns = $this->getTumanPatterns($tuman);

            // ============ MUDDATLI (BO'LIB TO'LASH) ============
            if ($tolovTuriFilter === 'all' || $tolovTuriFilter === 'muddatli') {
                $muddatliData = $this->calculateMuddatliData(
                    $tumanPatterns,
                    $selectedYear,
                    $selectedMonth
                );

                if ($muddatliData['has_data']) {
                    $result['tumanlar_muddatli'][] = array_merge(
                        ['tuman' => $tuman],
                        $muddatliData['data']
                    );

                    // Add to totals
                    $this->addToMuddatliTotals($result['jami_muddatli'], $muddatliData['data']);
                }
            }

            // ============ MUDDATLI EMAS (BIR YO'LA TO'LASH) ============
            if ($tolovTuriFilter === 'all' || $tolovTuriFilter === 'muddatli_emas') {
                $muddatliEmasData = $this->calculateMuddatliEmasData(
                    $tumanPatterns,
                    $selectedYear,
                    $selectedMonth
                );

                if ($muddatliEmasData['has_data']) {
                    $result['tumanlar_muddatli_emas'][] = array_merge(
                        ['tuman' => $tuman],
                        $muddatliEmasData['data']
                    );

                    // Add to totals
                    $this->addToMuddatliEmasTotals($result['jami_muddatli_emas'], $muddatliEmasData['data']);
                }
            }
        }

        // Calculate percentages for muddatli
        $this->calculatePercentages($result['jami_muddatli']);

        // Calculate UMUMIY (combined totals)
        $result['jami_umumiy'] = [
            'selected_month' => [
                'plan' => $result['jami_muddatli']['selected_month']['plan'],
                'fakt' => $result['jami_muddatli']['selected_month']['fakt'] +
                    $result['jami_muddatli_emas']['selected_month']['fakt'],
                'percentage' => 0
            ],
            'year_to_date' => [
                'plan' => $result['jami_muddatli']['year_to_date']['plan'],
                'fakt' => $result['jami_muddatli']['year_to_date']['fakt'] +
                    $result['jami_muddatli_emas']['year_to_date']['fakt'],
                'percentage' => 0
            ],
            'full_year' => [
                'plan' => $result['jami_muddatli']['full_year']['plan'],
                'fakt' => $result['jami_muddatli']['full_year']['fakt'] +
                    $result['jami_muddatli_emas']['full_year']['fakt'],
                'percentage' => 0
            ]
        ];

        // Calculate umumiy percentages (Plan faqat muddatli uchun)
        $this->calculatePercentages($result['jami_umumiy']);

        // Apply global qoldiq adjustment if exists
        $qoldiq = \App\Models\GlobalQoldiq::getQoldiqForDate("{$selectedYear}-{$selectedMonth}-01");
        if ($qoldiq) {
            $result['qoldiq_info'] = [
                'sana' => $qoldiq->sana->format('d.m.Y'),
                'summa' => $qoldiq->summa,
                'tur' => $qoldiq->tur,
                'izoh' => $qoldiq->izoh
            ];
        }

        // Add meta information
        $result['meta'] = [
            'selected_month' => $selectedMonth,
            'selected_month_name' => $this->getMonthName($selectedMonth),
            'selected_year' => $selectedYear,
            'current_month' => now()->month,
            'current_year' => now()->year,
            'tolov_turi_filter' => $tolovTuriFilter
        ];

        return $result;
    }

    /**
     * Calculate data for MUDDATLI payments
     * CRITICAL: Uses fakt_tolovlar ONLY for fakt calculations
     */
    private function calculateMuddatliData(?array $tumanPatterns, int $year, int $month): array
    {
        $query = YerSotuv::query();

        // CRITICAL: Exclude canceled records
        $this->applyBaseFilters($query);
        $this->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');

        $lotRaqamlari = $query->pluck('lot_raqami')->toArray();

        if (empty($lotRaqamlari)) {
            return ['has_data' => false];
        }

        // Selected Month
        $selectedMonthPlan = DB::table('grafik_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->where('yil', $year)
            ->where('oy', $month)
            ->sum('grafik_summa');

        // FAKT from fakt_tolovlar ONLY
        $selectedMonthFakt = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->whereYear('tolov_sana', $year)
            ->whereMonth('tolov_sana', $month)
            ->sum('tolov_summa');

        // Year to Date
        $ytdPlan = DB::table('grafik_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->where('yil', $year)
            ->where('oy', '<=', $month)
            ->sum('grafik_summa');

        // FAKT from fakt_tolovlar ONLY
        $ytdFakt = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->whereYear('tolov_sana', $year)
            ->whereMonth('tolov_sana', '<=', $month)
            ->sum('tolov_summa');

        // Full Year
        $fullYearPlan = DB::table('grafik_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->where('yil', $year)
            ->sum('grafik_summa');

        // FAKT from fakt_tolovlar ONLY
        $fullYearFakt = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->whereYear('tolov_sana', $year)
            ->sum('tolov_summa');

        return [
            'has_data' => true,
            'data' => [
                'selected_month' => [
                    'plan' => $selectedMonthPlan,
                    'fakt' => $selectedMonthFakt,
                    'percentage' => $selectedMonthPlan > 0 ? round(($selectedMonthFakt / $selectedMonthPlan) * 100) : 0
                ],
                'year_to_date' => [
                    'plan' => $ytdPlan,
                    'fakt' => $ytdFakt,
                    'percentage' => $ytdPlan > 0 ? round(($ytdFakt / $ytdPlan) * 100) : 0
                ],
                'full_year' => [
                    'plan' => $fullYearPlan,
                    'fakt' => $fullYearFakt,
                    'percentage' => $fullYearPlan > 0 ? round(($fullYearFakt / $fullYearPlan) * 100) : 0
                ]
            ]
        ];
    }

    /**
     * Calculate data for MUDDATLI EMAS (one-time) payments
     * CRITICAL: Uses fakt_tolovlar ONLY
     */
    private function calculateMuddatliEmasData(?array $tumanPatterns, int $year, int $month): array
    {
        $query = YerSotuv::query();

        // CRITICAL: Exclude canceled records
        $this->applyBaseFilters($query);
        $this->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли эмас');

        $lotRaqamlari = $query->pluck('lot_raqami')->toArray();

        if (empty($lotRaqamlari)) {
            return ['has_data' => false];
        }

        // Selected Month - FAKT from fakt_tolovlar ONLY
        $selectedMonthFakt = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->whereYear('tolov_sana', $year)
            ->whereMonth('tolov_sana', $month)
            ->sum('tolov_summa');

        // Year to Date - FAKT from fakt_tolovlar ONLY
        $ytdFakt = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->whereYear('tolov_sana', $year)
            ->whereMonth('tolov_sana', '<=', $month)
            ->sum('tolov_summa');

        // Full Year - FAKT from fakt_tolovlar ONLY
        $fullYearFakt = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->whereYear('tolov_sana', $year)
            ->sum('tolov_summa');

        return [
            'has_data' => true,
            'data' => [
                'selected_month' => ['fakt' => $selectedMonthFakt],
                'year_to_date' => ['fakt' => $ytdFakt],
                'full_year' => ['fakt' => $fullYearFakt]
            ]
        ];
    }

    /**
     * Add tuman data to muddatli totals
     */
    private function addToMuddatliTotals(array &$totals, array $data): void
    {
        $totals['selected_month']['plan'] += $data['selected_month']['plan'];
        $totals['selected_month']['fakt'] += $data['selected_month']['fakt'];

        $totals['year_to_date']['plan'] += $data['year_to_date']['plan'];
        $totals['year_to_date']['fakt'] += $data['year_to_date']['fakt'];

        $totals['full_year']['plan'] += $data['full_year']['plan'];
        $totals['full_year']['fakt'] += $data['full_year']['fakt'];
    }

    /**
     * Add tuman data to muddatli emas totals
     */
    private function addToMuddatliEmasTotals(array &$totals, array $data): void
    {
        $totals['selected_month']['fakt'] += $data['selected_month']['fakt'];
        $totals['year_to_date']['fakt'] += $data['year_to_date']['fakt'];
        $totals['full_year']['fakt'] += $data['full_year']['fakt'];
    }

    /**
     * Calculate percentages for totals
     */
    private function calculatePercentages(array &$totals): void
    {
        $totals['selected_month']['percentage'] = $totals['selected_month']['plan'] > 0
            ? round(($totals['selected_month']['fakt'] / $totals['selected_month']['plan']) * 100)
            : 0;

        $totals['year_to_date']['percentage'] = $totals['year_to_date']['plan'] > 0
            ? round(($totals['year_to_date']['fakt'] / $totals['year_to_date']['plan']) * 100)
            : 0;

        $totals['full_year']['percentage'] = $totals['full_year']['plan'] > 0
            ? round(($totals['full_year']['fakt'] / $totals['full_year']['plan']) * 100)
            : 0;
    }

    private function getMonthName(int $month): string
    {
        $months = [
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь'
        ];
        return $months[$month] ?? 'Unknown';
    }
    /**
     * Write comprehensive statistics to log file
     * Call this after getDetailedStatistics() to log T1 and T2 breakdown
     */
    /**
     * Write comprehensive statistics to Laravel log
     * Call this after getDetailedStatistics() to log T1 and T2 breakdown
     */
    /**
     * Write comprehensive statistics to a formatted text file
     */
    public function logDetailedStatisticsToFile(array $statistics): void
    {
        $logContent = [];

        $logContent[] = "";
        $logContent[] = str_repeat("=", 100);
        $logContent[] = "=== SVOD1 STATISTIKA (T1 va T2 bo'yicha) ===";
        $logContent[] = "Sana: " . now()->format('Y-m-d H:i:s');
        $logContent[] = str_repeat("=", 100);

        // JAMI UMUMIY STATISTIKA
        $logContent[] = "";
        $logContent[] = "╔═══════════════════════════════════════════════════════════════════════════════════════════════════╗";
        $logContent[] = "║                                    UMUMIY STATISTIKA                                              ║";
        $logContent[] = "╠═══════════════════════════════════════════════════════════════════════════════════════════════════╣";
        $logContent[] = sprintf(
            "║ %-50s │ %15s │ %15s │ %15s ║",
            "KO'RSATKICH",
            "T1 (bir yo'la)",
            "T2 (bo'lib)",
            "JAMI"
        );
        $logContent[] = "╠═══════════════════════════════════════════════════════════════════════════════════════════════════╣";

        $jami = $statistics['jami'];

        // LOT soni
        $logContent[] = sprintf(
            "║ %-50s │ %15s │ %15s │ %15s ║",
            "LOT soni",
            number_format($jami['bir_yola']['soni']),
            number_format($jami['bolib']['soni']),
            number_format($jami['jami']['soni'])
        );

        // Maydoni
        $logContent[] = sprintf(
            "║ %-50s │ %15s │ %15s │ %15s ║",
            "Maydoni (gektar)",
            number_format($jami['bir_yola']['maydoni'], 2),
            number_format($jami['bolib']['maydoni'], 2),
            number_format($jami['jami']['maydoni'], 2)
        );

        // Boshlangich narx
        $logContent[] = sprintf(
            "║ %-50s │ %15s │ %15s │ %15s ║",
            "Boshlangich narx (mln)",
            number_format($jami['bir_yola']['boshlangich_narx'] / 1000000, 1),
            number_format($jami['bolib']['boshlangich_narx'] / 1000000, 1),
            number_format($jami['jami']['boshlangich_narx'] / 1000000, 1)
        );

        // Sotilgan narx
        $logContent[] = sprintf(
            "║ %-50s │ %15s │ %15s │ %15s ║",
            "Sotilgan narx (mln)",
            number_format($jami['bir_yola']['sotilgan_narx'] / 1000000, 1),
            number_format($jami['bolib']['sotilgan_narx'] / 1000000, 1),
            number_format($jami['jami']['sotilgan_narx'] / 1000000, 1)
        );

        // Golib to'lagan
        $logContent[] = sprintf(
            "║ %-50s │ %15s │ %15s │ %15s ║",
            "G'olib to'lagan (mln)",
            number_format($jami['bir_yola']['golib_tolagan'] / 1000000, 1),
            number_format($jami['bolib']['golib_tolagan'] / 1000000, 1),
            number_format($jami['jami']['golib_tolagan'] / 1000000, 1)
        );

        // Shartnoma summasi
        $logContent[] = sprintf(
            "║ %-50s │ %15s │ %15s │ %15s ║",
            "Shartnoma summasi (mln)",
            number_format($jami['bir_yola']['shartnoma_summasi'] / 1000000, 1),
            number_format($jami['bolib']['shartnoma_summasi'] / 1000000, 1),
            number_format($jami['jami']['shartnoma_summasi'] / 1000000, 1)
        );

        // Auksion harajati
        $logContent[] = sprintf(
            "║ %-50s │ %15s │ %15s │ %15s ║",
            "Auksion harajati (mln)",
            number_format($jami['bir_yola']['auksion_harajati'] / 1000000, 1),
            number_format($jami['bolib']['auksion_harajati'] / 1000000, 1),
            number_format($jami['jami']['auksion_harajati'] / 1000000, 1)
        );

        $logContent[] = "╠═══════════════════════════════════════════════════════════════════════════════════════════════════╣";

        // TUSHADIGAN MABLAGH
        $logContent[] = sprintf(
            "║ %-50s │ %15s │ %15s │ %15s ║",
            "TUSHADIGAN MABLAGH (mln)",
            number_format($jami['bir_yola']['tushadigan_mablagh'] / 1000000, 1),
            number_format($jami['bolib_tushadigan'] / 1000000, 1),
            number_format($jami['jami']['tushadigan_mablagh'] / 1000000, 1)
        );

        // FAKT TUSHGAN
        $logContent[] = sprintf(
            "║ %-50s │ %15s │ %15s │ %15s ║",
            "FAKT TUSHGAN (mln)",
            number_format($jami['biryola_fakt'] / 1000000, 1),
            number_format($jami['bolib_tushgan'] / 1000000, 1),
            number_format($jami['jami_tushgan_yigindi'] / 1000000, 1)
        );

        $logContent[] = "╚═══════════════════════════════════════════════════════════════════════════════════════════════════╝";

        // TUMAN BO'YICHA BATAFSIL
        $logContent[] = "";
        $logContent[] = "╔═══════════════════════════════════════════════════════════════════════════════════════════════════╗";
        $logContent[] = "║                                TUMAN BO'YICHA STATISTIKA                                          ║";
        $logContent[] = "╚═══════════════════════════════════════════════════════════════════════════════════════════════════╝";

        foreach ($statistics['tumanlar'] as $tumanStat) {
            $logContent[] = "";
            $logContent[] = "┌─────────────────────────────────────────────────────────────────────────────────────────────────┐";
            $logContent[] = sprintf("│ TUMAN: %-87s │", $tumanStat['tuman']);
            $logContent[] = "├─────────────────────────────────────────────────────────────────────────────────────────────────┤";
            $logContent[] = sprintf(
                "│ %-50s │ %15s │ %15s │ %15s │",
                "Ko'rsatkich",
                "T1 (bir yo'la)",
                "T2 (bo'lib)",
                "JAMI"
            );
            $logContent[] = "├─────────────────────────────────────────────────────────────────────────────────────────────────┤";

            // LOT soni
            $logContent[] = sprintf(
                "│ %-50s │ %15s │ %15s │ %15s │",
                "LOT soni",
                number_format($tumanStat['bir_yola']['soni']),
                number_format($tumanStat['bolib']['soni']),
                number_format($tumanStat['jami']['soni'])
            );

            // Tushadigan mablagh
            $logContent[] = sprintf(
                "│ %-50s │ %15s │ %15s │ %15s │",
                "Tushadigan (mln)",
                number_format($tumanStat['bir_yola']['tushadigan_mablagh'] / 1000000, 1),
                number_format($tumanStat['bolib_tushadigan'] / 1000000, 1),
                number_format($tumanStat['jami']['tushadigan_mablagh'] / 1000000, 1)
            );

            // Fakt tushgan
            $logContent[] = sprintf(
                "│ %-50s │ %15s │ %15s │ %15s │",
                "Fakt tushgan (mln)",
                number_format($tumanStat['biryola_fakt'] / 1000000, 1),
                number_format($tumanStat['bolib_tushgan'] / 1000000, 1),
                number_format($tumanStat['jami_tushgan_yigindi'] / 1000000, 1)
            );

            $logContent[] = "└─────────────────────────────────────────────────────────────────────────────────────────────────┘";
        }

        $logContent[] = "";
        $logContent[] = str_repeat("=", 100);
        $logContent[] = "Yakunlandi: " . now()->format('Y-m-d H:i:s');
        $logContent[] = str_repeat("=", 100);

        // Write to storage/app/statistics directory
        $filename = 'statistics/svod1_' . now()->format('Y-m-d_His') . '.txt';
        Storage::put($filename, implode("\n", $logContent));

        Log::info("Statistics report written to: " . storage_path('app/' . $filename));
    }
}
