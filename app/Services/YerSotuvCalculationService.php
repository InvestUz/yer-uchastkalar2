<?php

namespace App\Services;

use App\Models\YerSotuv;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class YerSotuvCalculationService
{
    protected $queryService;

    public function __construct(YerSotuvQueryService $queryService)
    {
        $this->queryService = $queryService;
    }

    /**
     * Calculate total grafik tushadigan (scheduled amount up to last month)
     * for муддатли payments with date filters
     */
    public function calculateGrafikTushadigan(?array $tumanPatterns = null, array $dateFilters = [], string $tolovTuri = 'муддатли'): float
    {
        $query = YerSotuv::query();

        // CRITICAL: Apply base filters
        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', $tolovTuri);
        $this->queryService->applyDateFilters($query, $dateFilters);

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
        $cutoffDate = $this->queryService->getGrafikCutoffDate();

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
     * Calculate biryola_fakt: ONLY actual payments from fakt_tolovlar for bir yo'la (excluding mulk qabul)
     * CRITICAL: Must use fakt_tolovlar table ONLY
     */
    public function calculateBiryolaFakt(?array $tumanPatterns = null, array $dateFilters = [], YerSotuvDataService $dataService): float
    {
        $biryolaLots = $dataService->getBiryolaLotlar($tumanPatterns, $dateFilters);

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
    public function calculateBolibTushgan(?array $tumanPatterns = null, array $dateFilters = [], YerSotuvDataService $dataService): float
    {
        $bolibLots = $dataService->getBolibLotlar($tumanPatterns, $dateFilters);

        if (empty($bolibLots)) {
            Log::info('BOLIB TUSHGAN Calculation', [
                'tuman_patterns' => $tumanPatterns,
                'date_filters' => $dateFilters,
                'lots_count' => 0,
                'result' => 0
            ]);
            return 0;
        }

        // Get ONLY from fakt_tolovlar, EXCLUDING auction org payments
        $faktQuery = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $bolibLots);

        // EXCLUDE auction organization payments
        $faktQuery->where(function($q) {
            $q->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZ%')
              ->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH AJ%')
              ->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZI%')
              ->where('tolash_nom', 'NOT LIKE', '%ГУП "ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZI"%');
        });

        $faktSum = $faktQuery->sum('tolov_summa');

        return $faktSum;
    }

    /**
     * Calculate bolib_tushadigan: expected amount for bo'lib to'lash
     */
    public function calculateBolibTushadigan(?array $tumanPatterns = null, array $dateFilters = []): float
    {
        $query = YerSotuv::query();

        // CRITICAL: Exclude canceled records
        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->queryService->applyDateFilters($query, $dateFilters);

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
     * Calculate total payments from fakt_tolovlar for bekor qilinganlar (canceled lots)
     */
    public function calculateBekorQilinganlarPayments(?array $tumanPatterns = null, array $dateFilters = []): float
    {
        $query = YerSotuv::query();

        $this->queryService->applyTumanFilter($query, $tumanPatterns);
        $query->where('holat', 'Бекор қилинган');
        $this->queryService->applyDateFilters($query, $dateFilters);

        $lotRaqamlari = $query->pluck('lot_raqami')->toArray();

        if (empty($lotRaqamlari)) {
            return 0;
        }

        // Get total payments from fakt_tolovlar for these canceled lots
        $faktSum = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->sum('tolov_summa');

        Log::info('BEKOR QILINGANLAR Payments Calculation', [
            'tuman_patterns' => $tumanPatterns,
            'date_filters' => $dateFilters,
            'lots_count' => count($lotRaqamlari),
            'lot_raqamlari' => $lotRaqamlari,
            'source' => 'fakt_tolovlar table',
            'result' => $faktSum
        ]);

        return $faktSum;
    }

    /**
     * Calculate fakt payments for a specific period
     * Returns actual payments made during the selected period ONLY
     */
    public function calculateFaktByPeriod(?array $tumanPatterns, array $dateFilters, string $tolovTuri): float
    {
        $query = YerSotuv::query();

        // Apply filters
        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', $tolovTuri);
        $this->queryService->applyDateFilters($query, $dateFilters);

        $lotRaqamlari = $query->pluck('lot_raqami')->toArray();

        if (empty($lotRaqamlari)) {
            return 0;
        }

        // Build fakt query with period filters
        $faktQuery = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari);

        // EXCLUDE auction organization payments
        $faktQuery->where(function($q) {
            $q->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZ%')
              ->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH AJ%')
              ->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZI%')
              ->where('tolash_nom', 'NOT LIKE', '%ГУП "ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZI"%');
        });

        // Apply date filters to tolov_sana
        if (!empty($dateFilters['auksion_sana_from'])) {
            $faktQuery->whereDate('tolov_sana', '>=', $dateFilters['auksion_sana_from']);
        }
        if (!empty($dateFilters['auksion_sana_to'])) {
            $faktQuery->whereDate('tolov_sana', '<=', $dateFilters['auksion_sana_to']);
        }

        $faktSumma = $faktQuery->sum('tolov_summa');

        \Log::info('Fakt BY PERIOD Calculation (Net of Auction Fees)', [
            'tuman_patterns' => $tumanPatterns,
            'tolov_turi' => $tolovTuri,
            'date_filters' => $dateFilters,
            'lots_count' => count($lotRaqamlari),
            'fakt_summa' => $faktSumma
        ]);

        return $faktSumma;
    }

    /**
     * Calculate grafik tushadigan for a specific period
     * Returns scheduled payments for the selected period ONLY
     */
    public function calculateGrafikTushadiganByPeriod(array $dateFilters, string $tolovTuri): float
    {
        // Get ALL lots of this payment type
        $query = YerSotuv::query();
        $this->queryService->applyBaseFilters($query);
        $query->where('tolov_turi', $tolovTuri);

        $lotRaqamlari = $query->pluck('lot_raqami')->toArray();

        if (empty($lotRaqamlari)) {
            return 0;
        }

        // Extract period from date filters
        if (empty($dateFilters['auksion_sana_from']) || empty($dateFilters['auksion_sana_to'])) {
            return 0; // No period specified
        }

        $dateFrom = \Carbon\Carbon::parse($dateFilters['auksion_sana_from']);
        $dateTo = \Carbon\Carbon::parse($dateFilters['auksion_sana_to']);

        // Build SQL query for grafik
        $grafikQuery = DB::table('grafik_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->where('yil', '>=', $dateFrom->year)
            ->where('yil', '<=', $dateTo->year);

        // If same year, filter by months
        if ($dateFrom->year === $dateTo->year) {
            $grafikQuery->where('oy', '>=', $dateFrom->month)
                        ->where('oy', '<=', $dateTo->month);
        }

        $grafikSumma = $grafikQuery->sum('grafik_summa');

        // NOW SUBTRACT auction organization payments
        $auksionOrgPayments = $this->getAuksionOrganizationPayments($lotRaqamlari, $dateFilters);

        $netGrafikSumma = $grafikSumma - $auksionOrgPayments;

        \Log::info('Grafik By Period (Net of Auction Fees)', [
            'lots' => count($lotRaqamlari),
            'from' => $dateFrom->format('Y-m'),
            'to' => $dateTo->format('Y-m'),
            'gross_grafik' => $grafikSumma,
            'auction_org_payments' => $auksionOrgPayments,
            'net_grafik_summa' => $netGrafikSumma
        ]);

        return max(0, $netGrafikSumma); // Don't return negative
    }

    /**
     * Get total payments made to auction organizations
     * These should be excluded from "tushadigan" calculations
     */
    private function getAuksionOrganizationPayments(array $lotRaqamlari, array $dateFilters = []): float
    {
        if (empty($lotRaqamlari)) {
            return 0;
        }

        $auksionOrganizations = [
            'ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZ',
            'ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH AJ',
            'ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZI'
        ];

        $query = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->where(function($q) use ($auksionOrganizations) {
                foreach ($auksionOrganizations as $org) {
                    $q->orWhere('tolash_nom', 'LIKE', '%' . $org . '%');
                }
            });

        // Apply date filters if provided
        if (!empty($dateFilters['auksion_sana_from'])) {
            $query->whereDate('tolov_sana', '>=', $dateFilters['auksion_sana_from']);
        }
        if (!empty($dateFilters['auksion_sana_to'])) {
            $query->whereDate('tolov_sana', '<=', $dateFilters['auksion_sana_to']);
        }

        $totalAuksionPayments = $query->sum('tolov_summa');

        \Log::info('Auction Organization Payments Excluded', [
            'lots_count' => count($lotRaqamlari),
            'date_filters' => $dateFilters,
            'organizations' => $auksionOrganizations,
            'total_excluded' => $totalAuksionPayments
        ]);

        return $totalAuksionPayments;
    }
}
