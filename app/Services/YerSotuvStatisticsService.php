<?php

namespace App\Services;

use App\Models\YerSotuv;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class YerSotuvStatisticsService
{
    protected $queryService;
    protected $dataService;
    protected $calculationService;

    public function __construct(
        YerSotuvQueryService $queryService,
        YerSotuvDataService $dataService,
        YerSotuvCalculationService $calculationService
    ) {
        $this->queryService = $queryService;
        $this->dataService = $dataService;
        $this->calculationService = $calculationService;
    }

    /**
     * Get complete statistics for main page (SVOD1)
     * AUTOMATIC DISTRICT FILTERING: District users only see their own data
     */
    public function getDetailedStatistics(array $dateFilters = []): array
    {
        Log::info('========== STARTING DETAILED STATISTICS CALCULATION (SVOD1) ==========', [
            'date_filters' => $dateFilters,
            'user' => Auth::user()->email ?? 'guest',
            'role' => Auth::user()->role ?? 'none'
        ]);

        // CRITICAL: If district user, only process their district
        if (Auth::check() && Auth::user()->isDistrict()) {
            $tumanlar = [Auth::user()->tuman];
            Log::info('DISTRICT USER: Filtering to single district', ['tuman' => Auth::user()->tuman]);
        } else {
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
        }

        $statistics = [];

        foreach ($tumanlar as $tuman) {
            Log::info("---------- Processing Tuman: {$tuman} ----------");

            $tumanPatterns = $this->queryService->getTumanPatterns($tuman);

            $stat = [
                'tuman' => $tuman,
                'jami' => $this->dataService->getTumanData($tumanPatterns, null, $dateFilters),
                'bir_yola' => $this->dataService->getTumanData($tumanPatterns, 'муддатли эмас', $dateFilters),
                'bolib' => $this->dataService->getTumanData($tumanPatterns, 'муддатли', $dateFilters),
                'auksonda' => $this->dataService->getAuksondaTurgan($tumanPatterns, $dateFilters),
                'mulk_qabul' => $this->dataService->getMulkQabulQilmagan($tumanPatterns, $dateFilters),
                'biryola_fakt' => $this->calculationService->calculateBiryolaFakt($tumanPatterns, $dateFilters, $this->dataService),
                'bolib_tushgan' => $this->calculationService->calculateBolibTushgan($tumanPatterns, $dateFilters, $this->dataService),
                'bolib_tushadigan' => $this->calculationService->calculateBolibTushadigan($tumanPatterns, $dateFilters),
            ];

            // Calculate bolib_tushgan_all (INCLUDING auction org payments)
            $bolibLotsAll = $this->dataService->getBolibLotlar($tumanPatterns, $dateFilters);
            $bolibTushganAll = 0;
            if (!empty($bolibLotsAll)) {
                $bolibTushganAll = DB::table('fakt_tolovlar')
                    ->whereIn('lot_raqami', $bolibLotsAll)
                    ->sum('tolov_summa');
            }
            $stat['bolib_tushgan_all'] = $bolibTushganAll;

            // CALCULATE JAMI TUSHADIGAN:
            // bir_yola_tushadigan + bolib_tushadigan + mulk_qabul
            $stat['jami']['tushadigan_mablagh'] =
                $stat['bir_yola']['tushadigan_mablagh'] +
                $stat['bolib_tushadigan'] +
                $stat['mulk_qabul']['total_auksion_mablagh'];

            $stat['jami_tushgan_yigindi'] = $stat['biryola_fakt'] + $stat['bolib_tushgan'];

            Log::info("JAMI TUSHADIGAN Calculation for {$tuman}", [
                'formula' => 'bir_yola_tushadigan + bolib_tushadigan + mulk_qabul',
                'calculation_steps' => [
                    'bir_yola_tushadigan' => $stat['bir_yola']['tushadigan_mablagh'],
                    'bolib_tushadigan' => $stat['bolib_tushadigan'],
                    'mulk_qabul' => $stat['mulk_qabul']['total_auksion_mablagh'],
                    'sum' => "{$stat['bir_yola']['tushadigan_mablagh']} + {$stat['bolib_tushadigan']} + {$stat['mulk_qabul']['total_auksion_mablagh']}",
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
            'jami' => $this->dataService->getTumanData(null, null, $dateFilters),
            'bir_yola' => $this->dataService->getTumanData(null, 'муддатли эмас', $dateFilters),
            'bolib' => $this->dataService->getTumanData(null, 'муддатли', $dateFilters),
            'auksonda' => $this->dataService->getAuksondaTurgan(null, $dateFilters),
            'mulk_qabul' => $this->dataService->getMulkQabulQilmagan(null, $dateFilters),
            'biryola_fakt' => $this->calculationService->calculateBiryolaFakt(null, $dateFilters, $this->dataService),
            'bolib_tushgan' => $this->calculationService->calculateBolibTushgan(null, $dateFilters, $this->dataService),
            'bolib_tushadigan' => $this->calculationService->calculateBolibTushadigan(null, $dateFilters),
        ];

        // Calculate bolib_tushgan_all (INCLUDING auction org payments) for JAMI
        $bolibLotsAll = $this->dataService->getBolibLotlar(null, $dateFilters);
        $bolibTushganAll = 0;
        if (!empty($bolibLotsAll)) {
            $bolibTushganAll = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $bolibLotsAll)
                ->sum('tolov_summa');
        }
        $jami['bolib_tushgan_all'] = $bolibTushganAll;

        // CALCULATE JAMI TUSHADIGAN:
        // bir_yola_tushadigan + bolib_tushadigan + mulk_qabul
        $jami['jami']['tushadigan_mablagh'] =
            $jami['bir_yola']['tushadigan_mablagh'] +
            $jami['bolib_tushadigan'] +
            $jami['mulk_qabul']['total_auksion_mablagh'];

        $jami['jami_tushgan_yigindi'] = $jami['biryola_fakt'] + $jami['bolib_tushgan'];

        Log::info("OVERALL JAMI TUSHADIGAN Calculation", [
            'formula' => 'bir_yola_tushadigan + bolib_tushadigan + mulk_qabul',
            'calculation_steps' => [
                'bir_yola_tushadigan' => $jami['bir_yola']['tushadigan_mablagh'],
                'bolib_tushadigan' => $jami['bolib_tushadigan'],
                'mulk_qabul' => $jami['mulk_qabul']['total_auksion_mablagh'],
                'sum' => "{$jami['bir_yola']['tushadigan_mablagh']} + {$jami['bolib_tushadigan']} + {$jami['mulk_qabul']['total_auksion_mablagh']}",
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
     * AUTOMATIC DISTRICT FILTERING: District users only see their own data
     */
    public function getSvod3Statistics(array $dateFilters = []): array
    {
        // CRITICAL: If district user, only process their district
        if (Auth::check() && Auth::user()->isDistrict()) {
            $tumanlar = [Auth::user()->tuman];
        } else {
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
        }

        $result = [
            'jami' => $this->initializeSvod3Total(),
            'tumanlar' => []
        ];

        foreach ($tumanlar as $tuman) {
            $tumanPatterns = $this->queryService->getTumanPatterns($tuman);

            $stat = [
                'tuman' => $tuman,
                'narhini_bolib' => $this->dataService->getNarhiniBolib($tumanPatterns, $dateFilters),
                'toliq_tolanganlar' => $this->dataService->getToliqTolanganlar($tumanPatterns, $dateFilters),
                'nazoratdagilar' => $this->dataService->getNazoratdagilar($tumanPatterns, $dateFilters),
                'grafik_ortda' => $this->dataService->getGrafikOrtda($tumanPatterns, $dateFilters)
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
     * AUTOMATIC DISTRICT FILTERING: District users only see their own data
     */
    public function getMonthlyComparativeData(array $filters = []): array
    {
        // CRITICAL: If district user, only process their district
        if (Auth::check() && Auth::user()->isDistrict()) {
            $tumanlar = [Auth::user()->tuman];
        } else {
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
        }

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
            $tumanPatterns = $this->queryService->getTumanPatterns($tuman);

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
        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);
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
        $this->queryService->applyBaseFilters($query);
        $this->queryService->applyTumanFilter($query, $tumanPatterns);
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

    /**
     * Get available years, quarters, and months from grafik_tolovlar
     * Optimized to use single query per type
     */
    public function getAvailablePeriods(): array
    {
        // Get available years
        $years = DB::table('grafik_tolovlar')
            ->select('yil')
            ->distinct()
            ->orderBy('yil', 'ASC')
            ->pluck('yil')
            ->toArray();

        // Get quarters with aggregated data using CASE statement
        $quarters = DB::table('grafik_tolovlar')
            ->selectRaw("
                yil,
                CASE
                    WHEN oy BETWEEN 1 AND 3 THEN 1
                    WHEN oy BETWEEN 4 AND 6 THEN 2
                    WHEN oy BETWEEN 7 AND 9 THEN 3
                    WHEN oy BETWEEN 10 AND 12 THEN 4
                END as chorak_raqam,
                CASE
                    WHEN oy BETWEEN 1 AND 3 THEN '1-чорак (Январь - Март)'
                    WHEN oy BETWEEN 4 AND 6 THEN '2-чорак (Апрель - Июнь)'
                    WHEN oy BETWEEN 7 AND 9 THEN '3-чорак (Июль - Сентябрь)'
                    WHEN oy BETWEEN 10 AND 12 THEN '4-чорак (Октябрь - Декабрь)'
                END as chorak_nomi,
                SUM(grafik_summa) as choraklik_summa,
                MIN(oy) as min_oy
            ")
            ->groupByRaw('yil, chorak_raqam, chorak_nomi')
            ->orderBy('yil', 'ASC')
            ->orderBy('min_oy', 'ASC')
            ->get()
            ->map(function ($item) {
                return [
                    'yil' => $item->yil,
                    'chorak_raqam' => $item->chorak_raqam,
                    'chorak_nomi' => $item->chorak_nomi,
                    'summa' => $item->choraklik_summa,
                    'display' => $item->yil . ' - ' . $item->chorak_nomi
                ];
            })
            ->toArray();

        // Get months with aggregated data
        $months = DB::table('grafik_tolovlar')
            ->select(
                'yil',
                'oy',
                'oy_nomi',
                DB::raw('SUM(grafik_summa) as oylik_summa')
            )
            ->groupBy('yil', 'oy', 'oy_nomi')
            ->orderBy('yil', 'ASC')
            ->orderBy('oy', 'ASC')
            ->get()
            ->map(function ($item) {
                return [
                    'yil' => $item->yil,
                    'oy' => $item->oy,
                    'oy_nomi' => $item->oy_nomi,
                    'summa' => $item->oylik_summa,
                    'display' => $item->oy_nomi . ' ' . $item->yil
                ];
            })
            ->toArray();

        return [
            'years' => $years,
            'quarters' => $quarters,
            'months' => $months
        ];
    }

    /**
     * Get month name in Uzbek
     */
    private function getMonthName(int $month): string
    {
        $months = [
            1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
            5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
            9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
        ];

        return $months[$month] ?? '';
    }

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

        // TUSHADIGAN MABLAGH (including mulk_qabul)
        $mulkQabulMablagh = $jami['mulk_qabul']['total_auksion_mablagh'] ?? 0;

        $logContent[] = sprintf(
            "║ %-50s │ %15s │ %15s │ %15s ║",
            "TUSHADIGAN MABLAGH (mln)",
            number_format($jami['bir_yola']['tushadigan_mablagh'] / 1000000, 1),
            number_format($jami['bolib_tushadigan'] / 1000000, 1),
            number_format($jami['jami']['tushadigan_mablagh'] / 1000000, 1)
        );

        // Add Mulk Qabul row if there's data
        if ($mulkQabulMablagh > 0) {
            $logContent[] = sprintf(
                "║ %-50s │ %15s │ %15s │ %15s ║",
                "  + Mulk qabul qilmagan (mln)",
                "-",
                "-",
                number_format($mulkQabulMablagh / 1000000, 1)
            );
        }

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
            $logContent[] = sprintf("│ TUMAN: %-87s │", $tumanStat['tuman'] ?? 'N/A');
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

            // Add Mulk Qabul for this tuman if there's data
            $tumanMulkQabul = $tumanStat['mulk_qabul']['total_auksion_mablagh'] ?? 0;
            if ($tumanMulkQabul > 0) {
                $logContent[] = sprintf(
                    "│ %-50s │ %15s │ %15s │ %15s │",
                    "  + Mulk qabul qilmagan (mln)",
                    "-",
                    "-",
                    number_format($tumanMulkQabul / 1000000, 1)
                );
            }

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
