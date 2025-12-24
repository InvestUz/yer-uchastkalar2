<?php

namespace App\Services;

use App\Models\YerSotuv;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class YerSotuvDataService
{
    protected $queryService;

    public function __construct(YerSotuvQueryService $queryService)
    {
        $this->queryService = $queryService;
    }

    /**
     * Get basic statistics (jami, bir_yola, bolib)
     */
    public function getTumanData(?array $tumanPatterns = null, ?string $tolovTuri = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        // CRITICAL: Exclude canceled records
        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);

        if ($tolovTuri) {
            $query->where('tolov_turi', $tolovTuri);
        }
        // ✅ When tolov_turi is null (JAMI count), include ALL lots (муддатли, муддатли эмас, AND auksonda turgan)

        $this->queryService->applyDateFilters($query, $dateFilters);

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
        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);

        $query->where(function ($q) {
            $q->where('tolov_turi', '!=', 'муддатли')
                ->where('tolov_turi', '!=', 'муддатли эмас')
                ->orWhereNull('tolov_turi');
        });

        $this->queryService->applyDateFilters($query, $dateFilters);

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
        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);

        $query->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida%')
            ->where('asos', 'ПФ-135');

        $this->queryService->applyDateFilters($query, $dateFilters);

        $results = $query->get(['tolov_turi', 'golib_tolagan', 'shartnoma_summasi', 'auksion_harajati', 'lot_raqami']);

        $totalAuksionMablagh = 0;
        $totalAuksionMablaghMuddatliEmas = 0;
        $itemCalculations = [];
        $countMuddatli = 0;
        $countMuddatliEmas = 0;

        foreach ($results as $result) {
            $golibTolagan = (float) $result->golib_tolagan;
            $shartnomaSummasi = (float) $result->shartnoma_summasi;
            $auksionHarajati = (float) $result->auksion_harajati;

            // ✅ SQL bilan bir xil formula: golib_tolagan - auksion_harajati
            $itemValue = $golibTolagan - $auksionHarajati;

            if ($result->tolov_turi === 'муддатли эмас') {
                $countMuddatliEmas++;
                $totalAuksionMablaghMuddatliEmas += $itemValue;
            } else {
                $countMuddatli++;
            }

            $totalAuksionMablagh += $itemValue;

            $itemCalculations[] = [
                'lot_raqami' => $result->lot_raqami,
                'tolov_turi' => $result->tolov_turi,
                'golib_tolagan' => $golibTolagan,
                'shartnoma_summasi' => $shartnomaSummasi,
                'auksion_harajati' => $auksionHarajati,
                'formula' => 'golib_tolagan - auksion_harajati', // ✅ Hamma uchun bir xil
                'calculated_value' => $itemValue
            ];
        }

        Log::info('MULK QABUL QILMAGAN Calculation', [
            'tuman_patterns' => $tumanPatterns,
            'date_filters' => $dateFilters,
            'total_records' => $results->count(),
            'count_muddatli' => $countMuddatli,
            'count_muddatli_emas' => $countMuddatliEmas,
            'item_calculations' => $itemCalculations,
            'total_auksion_mablagh' => $totalAuksionMablagh,
            'total_auksion_mablagh_muddatli_emas_only' => $totalAuksionMablaghMuddatliEmas
        ]);

        return [
            'total_records' => $results->count(),
            'total_records_muddatli_emas' => $countMuddatliEmas,
            'total_records_muddatli' => $countMuddatli,
            'total_auksion_mablagh' => $totalAuksionMablagh, // ✅ SQL bilan bir xil bo'ladi
            'total_auksion_mablagh_muddatli_emas' => $totalAuksionMablaghMuddatliEmas,
            'items' => $itemCalculations
        ];
    }

    /**
     * Get lot numbers for bo'lib to'lash by tuman
     */
    public function getBolibLotlar(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        // CRITICAL: Exclude canceled records
        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->queryService->applyDateFilters($query, $dateFilters);

        return $query->pluck('lot_raqami')->toArray();
    }

    /**
     * Get lot numbers for bir yo'la to'lash by tuman (ONLY qoldiq_qarz statuses)
     * ✅ Only returns lots where Қолдиқ маблағ > 0 (excluding fully paid lots)
     */
    public function getQoldiqQarzLotlar(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        // ✅ Do NOT apply base filters - we want to include "Бекор қилинған"
        $this->queryService->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли эмас');

        // ✅ Include only qoldiq_qarz statuses (no Лот якунланди)
        $query->whereNotNull('holat');
        $query->where(function ($q) {
            $q->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida%')
                ->orWhere('holat', 'like', '%G`olib shartnoma imzolashga rozilik bildirdi%')
                ->orWhere('holat', 'like', '%Ишл. кечикт. туф. мулкни қабул қил. тасдиқланмаған%')
                ->orWhere('holat', 'like', '%Бекор қилинған%');
        });

        // ✅ Only include lots where Қолдиқ маблағ > 0
        // Formula: expected - paid > 0.01
        $query->whereRaw('(
            (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0) - COALESCE(auksion_harajati, 0))
            - COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0)
            > 0.01
        )');

        // ✅ CRITICAL: Do NOT apply date filters for qoldiq_qarz calculation
        // This ensures ALL lots with remaining debt are included, regardless of auction date
        // $this->queryService->applyDateFilters($query, $dateFilters);

        return $query->pluck('lot_raqami')->toArray();
    }

    /**
     * Get lot numbers for bir yo'la to'lash by tuman (excluding mulk qabul)
     */
    public function getBiryolaLotlar(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        // CRITICAL: Exclude canceled records
        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли эмас');

        // Exclude mulk qabul qilmagan
        $query->where(function ($q) {
            $q->where('holat', 'not like', '%Ishtirokchi roziligini kutish jarayonida (34)%')
                ->orWhere('asos', '!=', 'ПФ-135')
                ->orWhereNull('holat')
                ->orWhereNull('asos');
        });

        $this->queryService->applyDateFilters($query, $dateFilters);

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
     * SVOD3: Get narhini bo'lib statistics
     */
    public function getNarhiniBolib(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        // CRITICAL: Exclude canceled records
        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->queryService->applyDateFilters($query, $dateFilters);

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
        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->queryService->applyDateFilters($query, $dateFilters);

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
        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->queryService->applyDateFilters($query, $dateFilters);

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
    public function getGrafikOrtda(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $bugun = $this->queryService->getGrafikCutoffDate();

        $query = YerSotuv::query();

        // CRITICAL: Exclude canceled records
        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->queryService->applyDateFilters($query, $dateFilters);

        // Get ALL муддатли lots (no complex subquery)
        $lotRaqamlari = $query->pluck('lot_raqami')->toArray();

        if (empty($lotRaqamlari)) {
            return [
                'soni' => 0,
                'maydoni' => 0,
                'grafik_summa' => 0,
                'fakt_summa' => 0,
                'muddati_utgan_qarz' => 0
            ];
        }

        // ✅ Calculate LOT-BY-LOT with auction org exclusion
        $soniWithDebt = 0;
        $totalMaydoni = 0;
        $grafikSumma = 0;
        $faktSumma = 0;
        $muddatiUtganQarz = 0;

        foreach ($lotRaqamlari as $lotRaqami) {
            // Get grafik for this lot
            $lotGrafikSumma = DB::table('grafik_tolovlar')
                ->where('lot_raqami', $lotRaqami)
                ->whereRaw('CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") <= ?', [$bugun])
                ->sum('grafik_summa');

            // Get fakt for this lot (EXCLUDING auction org payments)
            $lotFaktSumma = DB::table('fakt_tolovlar')
                ->where('lot_raqami', $lotRaqami)
                ->where(function($q) {
                    $q->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZ%')
                      ->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH AJ%')
                      ->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZI%')
                      ->orWhereNull('tolash_nom');
                })
                ->sum('tolov_summa');

            // Calculate debt for this lot
            $lotDebt = $lotGrafikSumma - $lotFaktSumma;

            // ✅ Only count lots with positive debt
            if ($lotDebt > 0) {
                $soniWithDebt++;
                $muddatiUtganQarz += $lotDebt;

                // Get maydoni for this lot
                $lotMaydoni = YerSotuv::where('lot_raqami', $lotRaqami)->value('maydoni');
                $totalMaydoni += $lotMaydoni ?? 0;
            }

            // Add to totals for display (all lots)
            $grafikSumma += $lotGrafikSumma;
            $faktSumma += $lotFaktSumma;
        }

        return [
            'soni' => $soniWithDebt, // ✅ Count of lots with debt > 0 only
            'maydoni' => $totalMaydoni,
            'grafik_summa' => $grafikSumma,
            'fakt_summa' => $faktSumma,
            'muddati_utgan_qarz' => $muddatiUtganQarz
        ];
    }

    /**
     * Get nazoratdagilar with PERIOD-SPECIFIC calculations
     */
    public function getNazoratdagilarByPeriod(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->queryService->applyDateFilters($query, $dateFilters);

        // Find lots with outstanding balance
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

        $lotRaqamlari = (clone $query)->pluck('lot_raqami')->toArray();

        $data = $query->selectRaw('
            COUNT(*) as soni,
            SUM(maydoni) as maydoni,
            SUM(boshlangich_narx) as boshlangich_narx,
            SUM(sotilgan_narx) as sotilgan_narx,
            SUM(COALESCE(golib_tolagan, 0)) as golib_tolagan,
            SUM(COALESCE(shartnoma_summasi, 0)) as shartnoma_summasi,
            SUM(COALESCE(auksion_harajati, 0)) as auksion_harajati
        ')->first();

        // Calculate tushadigan for SELECTED PERIOD from grafik_tolovlar
        $tushadiganMablagh = 0;
        if (!empty($lotRaqamlari)) {
            $grafikQuery = DB::table('grafik_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari);

            // Apply period filters
            if (!empty($dateFilters['auksion_sana_from']) && !empty($dateFilters['auksion_sana_to'])) {
                $dateFrom = \Carbon\Carbon::parse($dateFilters['auksion_sana_from']);
                $dateTo = \Carbon\Carbon::parse($dateFilters['auksion_sana_to']);

                $grafikQuery->where(function($q) use ($dateFrom, $dateTo) {
                    $q->where('yil', '>=', $dateFrom->year)
                      ->where('yil', '<=', $dateTo->year);

                    if ($dateFrom->year === $dateTo->year) {
                        $q->where('oy', '>=', $dateFrom->month)
                          ->where('oy', '<=', $dateTo->month);
                    }
                });
            }

            $tushadiganMablagh = $grafikQuery->sum('grafik_summa');
        }

        // Calculate tushgan for SELECTED PERIOD from fakt_tolovlar (EXCLUDING auction org)
        $tushganSumma = 0;
        if (!empty($lotRaqamlari)) {
            $faktQuery = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari);

            // EXCLUDE auction organization payments
            $faktQuery->where(function($q) {
                $q->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZ%')
                  ->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH AJ%')
                  ->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZI%')
                  ->where('tolash_nom', 'NOT LIKE', '%ГУП "ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZI"%');
            });

            // Apply date filters
            if (!empty($dateFilters['auksion_sana_from'])) {
                $faktQuery->whereDate('tolov_sana', '>=', $dateFilters['auksion_sana_from']);
            }
            if (!empty($dateFilters['auksion_sana_to'])) {
                $faktQuery->whereDate('tolov_sana', '<=', $dateFilters['auksion_sana_to']);
            }

            $tushganSumma = $faktQuery->sum('tolov_summa');
        }

        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'boshlangich_narx' => $data->boshlangich_narx ?? 0,
            'sotilgan_narx' => $data->sotilgan_narx ?? 0,
            'tushadigan_mablagh' => $tushadiganMablagh,
            'tushgan_summa' => $tushganSumma // NET of auction fees
        ];
    }

    /**
     * Get grafik ortda with PERIOD-SPECIFIC calculations
     */
    public function getGrafikOrtdaByPeriod(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->queryService->applyDateFilters($query, $dateFilters);

        // Find lots with outstanding balance
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

        $lotRaqamlari = (clone $query)->pluck('lot_raqami')->toArray();

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

        // Calculate grafik for SELECTED PERIOD
        $grafikQuery = DB::table('grafik_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari);

        if (!empty($dateFilters['auksion_sana_from']) && !empty($dateFilters['auksion_sana_to'])) {
            $dateFrom = \Carbon\Carbon::parse($dateFilters['auksion_sana_from']);
            $dateTo = \Carbon\Carbon::parse($dateFilters['auksion_sana_to']);

            $grafikQuery->where(function($q) use ($dateFrom, $dateTo) {
                $q->where('yil', '>=', $dateFrom->year)
                  ->where('yil', '<=', $dateTo->year);

                if ($dateFrom->year === $dateTo->year) {
                    $q->where('oy', '>=', $dateFrom->month)
                      ->where('oy', '<=', $dateTo->month);
                }
            });
        }

        $grafikSumma = $grafikQuery->sum('grafik_summa');

        // Calculate fakt for SELECTED PERIOD (EXCLUDING auction org)
        $faktQuery = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari);

        // EXCLUDE auction organization payments
        $faktQuery->where(function($q) {
            $q->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZ%')
              ->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH AJ%')
              ->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZI%')
              ->where('tolash_nom', 'NOT LIKE', '%ГУП "ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZI"%');
        });

        if (!empty($dateFilters['auksion_sana_from'])) {
            $faktQuery->whereDate('tolov_sana', '>=', $dateFilters['auksion_sana_from']);
        }
        if (!empty($dateFilters['auksion_sana_to'])) {
            $faktQuery->whereDate('tolov_sana', '<=', $dateFilters['auksion_sana_to']);
        }

        $faktSumma = $faktQuery->sum('tolov_summa');

        $muddatiUtganQarz = max(0, $grafikSumma - $faktSumma);

        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'grafik_summa' => $grafikSumma,
            'fakt_summa' => $faktSumma, // NET of auction fees
            'muddati_utgan_qarz' => $muddatiUtganQarz
        ];
    }

    /**
     * Get monitoring statistics for a specific category with period filters
     */
    public function getMonitoringCategoryData(string $category, array $dateFilters = []): array
    {
        $query = YerSotuv::query();

        // CRITICAL: Apply base filters
        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyDateFilters($query, $dateFilters);

        switch ($category) {
            case 'total_lots':
                $query->where('tolov_turi', 'муддатли');
                break;

            case 'nazoratdagilar':
                $query->where('tolov_turi', 'муддатли');
                $query->whereRaw('(
                    (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0))
                    - (
                        COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0)
                        + COALESCE(auksion_harajati, 0)
                    )
                ) > 0');
                break;

            case 'grafik_ortda':
                $bugun = $this->queryService->getGrafikCutoffDate();
                $query->where('tolov_turi', 'муддатли');
                $query->whereRaw('lot_raqami IN (
                    SELECT ys.lot_raqami
                    FROM yer_sotuvlar ys
                    LEFT JOIN (
                        SELECT lot_raqami, SUM(grafik_summa) as jami_grafik
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
                break;
        }

        return [
            'count' => $query->count(),
            'lot_raqamlari' => $query->pluck('lot_raqami')->toArray()
        ];
    }
}
