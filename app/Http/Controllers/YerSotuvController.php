<?php

namespace App\Http\Controllers;

use App\Models\GrafikTolov;
use App\Models\YerSotuv;
use App\Services\YerSotuvService;
use App\Services\YerSotuvFilterService;
use App\Services\YerSotuvMonitoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YerSotuvController extends Controller
{
    protected $yerSotuvService;
    protected $filterService;
    protected $monitoringService;

    public function __construct(
        YerSotuvService $yerSotuvService,
        YerSotuvFilterService $filterService,
        YerSotuvMonitoringService $monitoringService
    ) {
        $this->yerSotuvService = $yerSotuvService;
        $this->filterService = $filterService;
        $this->monitoringService = $monitoringService;
    }

    /**
     * Display main statistics page (SVOD1)
     */
    public function index(Request $request)
    {
        // DEFAULT: From 01.01.2024 to today
        $dateFilters = [
            'auksion_sana_from' => $request->auksion_sana_from ?? '2024-01-01',
            'auksion_sana_to' => $request->auksion_sana_to ?? now()->toDateString(),
        ];

        $statistics = $this->yerSotuvService->getDetailedStatistics($dateFilters);
        $this->yerSotuvService->logDetailedStatisticsToFile($statistics);

        return view('yer-sotuvlar.statistics', compact('statistics', 'dateFilters'));
    }
    /**
     * Display SVOD3 statistics page (Bo'lib to'lash)
     */
    public function svod3(Request $request)
    {
        // DEFAULT: From 01.01.2024 to today
        $dateFilters = [
            'auksion_sana_from' => $request->auksion_sana_from ?? '2024-01-01',
            'auksion_sana_to' => $request->auksion_sana_to ?? now()->toDateString(),
        ];

        $statistics = $this->yerSotuvService->getSvod3Statistics($dateFilters);

        return view('yer-sotuvlar.svod3', compact('statistics', 'dateFilters'));
    }

    /**
     * Display filtered list of land sales
     * DEFAULT: Show ALL statuses from 2024-01-01 to today (NO status/type filters)
     * FILTERED: When clicking from other pages, preserve their filters
     */
    public function list(Request $request)
    {
        // Check if period parameters are passed (from monitoring cards)
        if ($request->has('period') && $request->period !== 'all') {
            // Convert period to date filters for grafik-based filtering
            $periodFilters = $this->processPeriodFilter($request);

            // For муддатли: filter by lots that have grafik in this period
            if ($request->tolov_turi === 'муддатли') {
                return $this->listByGrafikPeriod($request, $periodFilters);
            }
        }

        // ✅ Check if qoldiq_qarz filter is active
        $isQoldiqQarzFilter = !empty($request->qoldiq_qarz) && $request->qoldiq_qarz === 'true';

        // ✅ DEFAULT: Show ALL statuses with ONLY date filter (2024-01-01 to today)
        // ✅ When coming from other pages, preserve their specific filters
        $filters = [
            'search' => $request->search,
            'tuman' => $request->tuman,
            'yil' => $request->yil,
            'tolov_turi' => $request->tolov_turi,
            'holat' => $request->holat,
            'asos' => $request->asos,
            // ✅ DEFAULT DATE FILTERS: 2024-01-01 to today
            'auksion_sana_from' => $request->auksion_sana_from ?? ($isQoldiqQarzFilter ? null : '2024-01-01'),
            'auksion_sana_to' => $request->auksion_sana_to ?? ($isQoldiqQarzFilter ? null : now()->toDateString()),
            'shartnoma_sana_from' => $request->shartnoma_sana_from,
            'shartnoma_sana_to' => $request->shartnoma_sana_to,
            'narx_from' => $request->narx_from,
            'narx_to' => $request->narx_to,
            'maydoni_from' => $request->maydoni_from,
            'maydoni_to' => $request->maydoni_to,
            'auksonda_turgan' => $request->auksonda_turgan,
            'grafik_ortda' => $request->grafik_ortda,
            'toliq_tolangan' => $request->toliq_tolangan,
            'nazoratda' => $request->nazoratda,
            'qoldiq_qarz' => $request->qoldiq_qarz,
            // ✅ include_auksonda: Include auksonda turgan lots (without affecting cancelled lot exclusion)
            'include_auksonda' => $request->include_auksonda,
            // ✅ DEFAULT: Show ALL statuses ONLY if not using include_bekor or include_auksonda
            // If include_bekor or include_auksonda is set, don't default to include_all
            'include_all' => $request->include_all ?? ((!empty($request->include_bekor) || !empty($request->include_auksonda)) ? null : 'true'),
            'include_bekor' => $isQoldiqQarzFilter ? 'true' : $request->include_bekor,
        ];

        \Log::info('List Filters Applied', [
            'qoldiq_qarz' => $filters['qoldiq_qarz'],
            'include_bekor' => $filters['include_bekor'],
            'include_all' => $filters['include_all'],
            'tolov_turi' => $filters['tolov_turi'],
            'auksion_sana_from' => $filters['auksion_sana_from'],
            'auksion_sana_to' => $filters['auksion_sana_to'],
            'request_include_bekor' => $request->include_bekor,
            'request_include_all' => $request->include_all
        ]);

        return $this->showFilteredData($request, $filters);
    }

    /**
     * List lots by grafik period (for monitoring card clicks)
     */
    private function listByGrafikPeriod(Request $request, array $periodFilters)
    {
        $dateFrom = \Carbon\Carbon::parse($periodFilters['auksion_sana_from']);
        $dateTo = \Carbon\Carbon::parse($periodFilters['auksion_sana_to']);

        // Get distinct lots with grafik in this period (matching monitoring logic)
        $lotsQuery = DB::table('grafik_tolovlar as gt')
            ->join('yer_sotuvlar as ys', 'gt.lot_raqami', '=', 'ys.lot_raqami')
            ->where('ys.tolov_turi', $request->tolov_turi)
            ->where('ys.holat', '!=', 'Бекор қилинган')
            ->whereNotNull('ys.holat')
            ->distinct();

        // Apply year and month filters to grafik data
        if ($dateFrom->year === $dateTo->year) {
            $lotsQuery->where('gt.yil', $dateFrom->year)
                ->whereBetween('gt.oy', [$dateFrom->month, $dateTo->month]);
        } else {
            $lotsQuery->where(function ($q) use ($dateFrom, $dateTo) {
                $q->where(function ($y1) use ($dateFrom) {
                    $y1->where('gt.yil', $dateFrom->year)
                        ->where('gt.oy', '>=', $dateFrom->month);
                });

                if ($dateTo->year - $dateFrom->year > 1) {
                    $q->orWhere(function ($ym) use ($dateFrom, $dateTo) {
                        $ym->whereBetween('gt.yil', [$dateFrom->year + 1, $dateTo->year - 1]);
                    });
                }

                $q->orWhere(function ($y2) use ($dateTo) {
                    $y2->where('gt.yil', $dateTo->year)
                        ->where('gt.oy', '<=', $dateTo->month);
                });
            });
        }

        $lotRaqamlari = $lotsQuery->pluck('gt.lot_raqami')->unique()->toArray();

        \Log::info('List By Grafik Period', [
            'period' => $request->period,
            'year' => $request->year,
            'quarter' => $request->quarter,
            'month' => $request->month,
            'date_range' => $dateFrom->format('Y-m') . ' to ' . $dateTo->format('Y-m'),
            'lots_count' => count($lotRaqamlari),
            'sample_lots' => array_slice($lotRaqamlari, 0, 5)
        ]);

        // Build filters for showFilteredData
        $filters = [
            'tolov_turi' => $request->tolov_turi,
            'lot_raqamlari' => $lotRaqamlari, // Pass specific lots to filter by
        ];

        return $this->showFilteredData($request, $filters);
    }

    /**
     * Show detailed information for a specific lot
     */
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

        $tolovTaqqoslash = $this->yerSotuvService->calculateTolovTaqqoslash($yer);

        return view('yer-sotuvlar.show', compact('yer', 'tolovTaqqoslash'));
    }

    /**
     * Show edit form
     */
    public function edit($lot_raqami)
    {
        $yer = YerSotuv::where('lot_raqami', $lot_raqami)->firstOrFail();
        return view('yer-sotuvlar.edit', compact('yer'));
    }

    /**
     * Update yer sotuv data
     */


    public function update(Request $request, $lot_raqami)
    {
        $yer = YerSotuv::where('lot_raqami', $lot_raqami)->firstOrFail();

        $oldLot = $yer->lot_raqami;   // eski lot raqami
        $yer->update($request->all());
        $newLot = $yer->lot_raqami;   // yangilangan lot raqami

        // Agar lot raqami o'zgarsa, listga qaytar
        if ($oldLot !== $newLot) {
            return redirect()->route('yer-sotuvlar.list')
                ->with('success', 'Маълумотлар муваффақиятли янгиланди!');
        }

        // Aks holda show pagega redirect
        return redirect()->route('yer-sotuvlar.show', $newLot)
            ->with('success', 'Маълумотлар муваффақиятли янгиланди!');
    }


    /**
     * Calculate grafik tushadigan for muddatli with date filters
     */
    private function calculateGrafikTushadigan(array $dateFilters, string $tolovTuri): float
    {
        $query = YerSotuv::query();

        // CRITICAL: Apply base filters and date filters
        $this->yerSotuvService->applyBaseFilters($query);
        $query->where('tolov_turi', $tolovTuri);
        $this->yerSotuvService->applyDateFilters($query, $dateFilters);

        $lotRaqamlari = $query->pluck('lot_raqami')->toArray();

        if (empty($lotRaqamlari)) {
            return 0;
        }

        // Use last month's end date as cutoff
        $cutoffDate = now()->subMonth()->endOfMonth()->format('Y-m-01');

        $grafikSumma = DB::table('grafik_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->whereRaw('CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") <= ?', [$cutoffDate])
            ->sum('grafik_summa');

        \Log::info('Grafik Tushadigan Calculation', [
            'tolov_turi' => $tolovTuri,
            'date_filters' => $dateFilters,
            'lots_count' => count($lotRaqamlari),
            'cutoff_date' => $cutoffDate,
            'grafik_summa' => $grafikSumma
        ]);

        return $grafikSumma;
    }

    /**
     * Process period filter into date range
     */
    private function processPeriodFilter(Request $request): array
    {
        $period = $request->period ?? 'all';
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        $quarter = $request->quarter ?? ceil(now()->month / 3);

        $dateFilters = [
            'auksion_sana_from' => null,
            'auksion_sana_to' => null,
        ];

        switch ($period) {
            case 'month':
                // Filter by specific month
                $dateFilters['auksion_sana_from'] = date('Y-m-01', strtotime("{$year}-{$month}-01"));
                $dateFilters['auksion_sana_to'] = date('Y-m-t', strtotime("{$year}-{$month}-01"));
                break;

            case 'quarter':
                // Filter by quarter
                $quarterMonths = [
                    1 => [1, 3],
                    2 => [4, 6],
                    3 => [7, 9],
                    4 => [10, 12]
                ];

                $startMonth = $quarterMonths[$quarter][0];
                $endMonth = $quarterMonths[$quarter][1];

                $dateFilters['auksion_sana_from'] = date('Y-m-01', strtotime("{$year}-{$startMonth}-01"));
                $dateFilters['auksion_sana_to'] = date('Y-m-t', strtotime("{$year}-{$endMonth}-01"));
                break;

            case 'year':
                // Filter by year
                $dateFilters['auksion_sana_from'] = "{$year}-01-01";
                $dateFilters['auksion_sana_to'] = "{$year}-12-31";
                break;

            case 'all':
            default:
                // DEFAULT: From 01.01.2024 to today
                $dateFilters['auksion_sana_from'] = '2024-01-01';
                $dateFilters['auksion_sana_to'] = now()->toDateString();
                break;
        }

        \Log::info('Period Filter Processed', [
            'period' => $period,
            'year' => $year,
            'month' => $month,
            'quarter' => $quarter,
            'date_filters' => $dateFilters
        ]);

        return $dateFilters;
    }

    /**
     * Display monitoring and analytics page
     * UPDATED VERSION with period filter support
     */
    // In YerSotuvController.php - update the monitoring() method

    /**
     * Display monitoring and analytics page
     * UPDATED VERSION with period filter support and correct grafik calculations
     */
public function monitoring(Request $request)
{
    // Process period filter to convert to date range
    $dateFilters = $this->processPeriodFilter($request);

    // Determine if we're using period-specific filtering
    $periodInfo = [
        'period' => $request->period ?? 'all',
        'year' => $request->year ?? now()->year,
        'month' => $request->month ?? now()->month,
        'quarter' => $request->quarter ?? ceil(now()->month / 3)
    ];

    $isPeriodFiltered = $periodInfo['period'] !== 'all';

    // Get all tumanlar
    $tumanlar = $this->monitoringService->getTumanlar();

    // Calculate statistics for each tuman using MonitoringService
    $monitoringStatistics = [];
    foreach ($tumanlar as $tuman) {
        $tumanPatterns = $this->yerSotuvService->getTumanPatterns($tuman);
        $stat = $this->monitoringService->calculateTumanStatistics($tumanPatterns, $dateFilters, false);
        $stat['tuman'] = $tuman;
        $monitoringStatistics[] = $stat;
    }

    // Calculate JAMI totals
    $jami = $this->monitoringService->calculateJamiTotals($monitoringStatistics);

    // Map data to monitoring page variables for backward compatibility
    $summaryTotal = [
        'total_lots' => $jami['jami_soni'],
        'expected_amount' => $jami['jami_tushadigan'],
        'received_amount' => $jami['jami_tushgan'],
    ];

    $summaryMuddatli = [
        'total_lots' => $jami['bolib_soni'],
        'expected_amount' => $jami['bolib_tushadigan'],
        'received_amount' => $jami['bolib_tushgan'],
        'payment_percentage' => $jami['bolib_tushadigan'] > 0 ? ($jami['bolib_tushgan'] / $jami['bolib_tushadigan']) * 100 : 0
    ];

    $summaryMuddatliEmas = [
        'total_lots' => $jami['biryola_soni'],
        'expected_amount' => $jami['biryola_tushadigan'],
        'received_amount' => $jami['biryola_tushgan'],
        'payment_percentage' => $jami['biryola_tushadigan'] > 0 ? ($jami['biryola_tushgan'] / $jami['biryola_tushadigan']) * 100 : 0
    ];

    $grafikTushadiganMuddatli = $jami['grafik_tushadigan'];
    $nazoratdagilar = [
        'tushadigan_mablagh' => $jami['bolib_tushadigan'],
        'tushgan_summa' => $jami['bolib_tushgan_all'], // ✅ ALL payments INCLUDING auction org (859.54)
        'soni' => $jami['bolib_soni']
    ];

    $grafikBoyichaTushgan = $jami['grafik_tushgan'];
    $muddatiUtganQarz = $jami['muddati_utgan_qarz'];

    // ✅ Calculate qoldiq_qarz specific data (for "Аукционда турган маблағ" card)
    $qoldiqQarzData = $this->calculateQoldiqQarzData($dateFilters);

    // Get tuman statistics with period-aware calculations (existing functionality)
    $tumanStatsMuddatli = [];
    foreach ($tumanlar as $tuman) {
        $tumanPatterns = $this->yerSotuvService->getTumanPatterns($tuman);

        // Use period-aware method
        if ($isPeriodFiltered) {
            $stats = $this->calculateTumanMonitoringByPeriod($tumanPatterns, $dateFilters, 'муддатли');
        } else {
            $stats = $this->calculateTumanMonitoring($tumanPatterns, $dateFilters, 'муддатли');
        }

        if ($stats['lots'] > 0) {
            $tumanStatsMuddatli[] = [
                'tuman' => $tuman,
                'lots' => $stats['lots'],
                'grafik' => $stats['grafik'],
                'fakt' => $stats['fakt'],
                'difference' => $stats['difference'],
                'percentage' => $stats['percentage']
            ];
        }
    }

    // Get tuman statistics for муддатли эмас with period-aware calculations
    $tumanStatsMuddatliEmas = [];
    foreach ($tumanlar as $tuman) {
        $tumanPatterns = $this->yerSotuvService->getTumanPatterns($tuman);

        // Use period-aware method
        if ($isPeriodFiltered) {
            $stats = $this->calculateTumanMonitoringByPeriod($tumanPatterns, $dateFilters, 'муддатли эмас');
        } else {
            $stats = $this->calculateTumanMonitoring($tumanPatterns, $dateFilters, 'муддатли эмас');
        }

        if ($stats['lots'] > 0) {
            $tumanStatsMuddatliEmas[] = [
                'tuman' => $tuman,
                'lots' => $stats['lots'],
                'expected' => $stats['expected'],
                'received' => $stats['received'],
                'difference' => $stats['difference'],
                'percentage' => $stats['percentage']
            ];
        }
    }

    $availablePeriods = $this->yerSotuvService->getAvailablePeriods();

    // Prepare chart data with period filters
    $chartData = $this->prepareChartData($tumanStatsMuddatli, $tumanStatsMuddatliEmas, $dateFilters);

    return view('yer-sotuvlar.monitoring', compact(
        'summaryTotal',
        'summaryMuddatli',
        'summaryMuddatliEmas',
        'tumanStatsMuddatli',
        'tumanStatsMuddatliEmas',
        'chartData',
        'dateFilters',
        'periodInfo',
        'grafikTushadiganMuddatli',
        'nazoratdagilar',
        'grafikBoyichaTushgan',
        'muddatiUtganQarz',
        'availablePeriods',
        'qoldiqQarzData'
    ));
}

    /**
     * Calculate monitoring summary BY PERIOD (lots with grafik/fakt in period)
     */
    private function calculateMonitoringSummaryByPeriod(array $dateFilters, ?string $tolovTuri): array
    {
        if ($tolovTuri === 'муддатли' || $tolovTuri === null) {
            // For period filtering: Count DISTINCT lots from grafik_tolovlar in this period
            // This matches the SQL query logic
            $dateFrom = \Carbon\Carbon::parse($dateFilters['auksion_sana_from']);
            $dateTo = \Carbon\Carbon::parse($dateFilters['auksion_sana_to']);

            // Build query to count distinct lots with grafik in the period
            $lotsQuery = DB::table('grafik_tolovlar as gt')
                ->join('yer_sotuvlar as ys', 'gt.lot_raqami', '=', 'ys.lot_raqami')
                ->where('ys.holat', '!=', 'Бекор қилинган')
                ->whereNotNull('ys.holat')
                ->distinct();

            // Apply payment type filter if specified
            if ($tolovTuri !== null) {
                $lotsQuery->where('ys.tolov_turi', $tolovTuri);
            }

            // Apply year and month filters to grafik data
            if ($dateFrom->year === $dateTo->year) {
                // Same year - simple month range
                $lotsQuery->where('gt.yil', $dateFrom->year)
                    ->whereBetween('gt.oy', [$dateFrom->month, $dateTo->month]);
            } else {
                // Multiple years - complex logic
                $lotsQuery->where(function ($q) use ($dateFrom, $dateTo) {
                    // First year: from start month to December
                    $q->where(function ($y1) use ($dateFrom) {
                        $y1->where('gt.yil', $dateFrom->year)
                            ->where('gt.oy', '>=', $dateFrom->month);
                    });

                    // Middle years: all months
                    if ($dateTo->year - $dateFrom->year > 1) {
                        $q->orWhere(function ($ym) use ($dateFrom, $dateTo) {
                            $ym->whereBetween('gt.yil', [$dateFrom->year + 1, $dateTo->year - 1]);
                        });
                    }

                    // Last year: January to end month
                    $q->orWhere(function ($y2) use ($dateTo) {
                        $y2->where('gt.yil', $dateTo->year)
                            ->where('gt.oy', '<=', $dateTo->month);
                    });
                });
            }

            $lotsInPeriod = $lotsQuery->pluck('gt.lot_raqami')->unique()->toArray();
            $totalLots = count($lotsInPeriod);

            \Log::info('Period Summary Calculation', [
                'tolov_turi' => $tolovTuri,
                'period' => $dateFrom->format('Y-m') . ' to ' . $dateTo->format('Y-m'),
                'lots_in_period' => $totalLots,
                'sample_lots' => array_slice($lotsInPeriod, 0, 5)
            ]);

            if ($totalLots === 0) {
                return [
                    'total_lots' => 0,
                    'expected_amount' => 0,
                    'received_amount' => 0,
                    'payment_percentage' => 0
                ];
            }

            // Calculate expected amount for these lots
            $data = YerSotuv::whereIn('lot_raqami', $lotsInPeriod)
                ->selectRaw('
                    SUM(COALESCE(golib_tolagan, 0)) as golib_tolagan,
                    SUM(COALESCE(shartnoma_summasi, 0)) as shartnoma_summasi,
                    SUM(COALESCE(auksion_harajati, 0)) as auksion_harajati
                ')->first();

            $expectedAmount = ($data->golib_tolagan + $data->shartnoma_summasi) - $data->auksion_harajati;

            // Calculate received amount (all-time for these lots)
            $receivedAmount = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $lotsInPeriod)
                ->sum('tolov_summa');

            $paymentPercentage = $expectedAmount > 0 ? ($receivedAmount / $expectedAmount) * 100 : 0;

            return [
                'total_lots' => $totalLots,
                'expected_amount' => $expectedAmount,
                'received_amount' => $receivedAmount,
                'payment_percentage' => $paymentPercentage
            ];
        } else {
            // For муддатли эмас - similar logic
            $query = YerSotuv::query();
            $this->yerSotuvService->applyBaseFilters($query);
            $query->where('tolov_turi', $tolovTuri);

            $allLots = $query->pluck('lot_raqami')->toArray();

            if (empty($allLots)) {
                return [
                    'total_lots' => 0,
                    'expected_amount' => 0,
                    'received_amount' => 0,
                    'payment_percentage' => 0
                ];
            }

            // Find lots that have fakt payments in this period
            $faktQuery = DB::table('fakt_tolovlar')
                ->select('lot_raqami')
                ->whereIn('lot_raqami', $allLots)
                ->distinct();

            if (!empty($dateFilters['auksion_sana_from'])) {
                $faktQuery->whereDate('tolov_sana', '>=', $dateFilters['auksion_sana_from']);
            }
            if (!empty($dateFilters['auksion_sana_to'])) {
                $faktQuery->whereDate('tolov_sana', '<=', $dateFilters['auksion_sana_to']);
            }

            $lotsInPeriod = $faktQuery->pluck('lot_raqami')->toArray();
            $totalLots = count($lotsInPeriod);

            // Calculate expected amount for these lots
            $data = YerSotuv::whereIn('lot_raqami', $lotsInPeriod)
                ->selectRaw('
                    SUM(COALESCE(golib_tolagan, 0)) as golib_tolagan,
                    SUM(COALESCE(shartnoma_summasi, 0)) as shartnoma_summasi,
                    SUM(COALESCE(auksion_harajati, 0)) as auksion_harajati
                ')->first();

            $expectedAmount = ($data->golib_tolagan + $data->shartnoma_summasi) - $data->auksion_harajati;

            // Calculate received amount IN PERIOD
            $receivedQuery = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $lotsInPeriod);

            if (!empty($dateFilters['auksion_sana_from'])) {
                $receivedQuery->whereDate('tolov_sana', '>=', $dateFilters['auksion_sana_from']);
            }
            if (!empty($dateFilters['auksion_sana_to'])) {
                $receivedQuery->whereDate('tolov_sana', '<=', $dateFilters['auksion_sana_to']);
            }

            $receivedAmount = $receivedQuery->sum('tolov_summa');
            $paymentPercentage = $expectedAmount > 0 ? ($receivedAmount / $expectedAmount) * 100 : 0;

            return [
                'total_lots' => $totalLots,
                'expected_amount' => $expectedAmount,
                'received_amount' => $receivedAmount,
                'payment_percentage' => $paymentPercentage
            ];
        }
    }

    /**
     * Calculate график фактик summa for specific period
     */
    private function calculateGrafikFaktByPeriod(array $dateFilters): float
    {
        $query = YerSotuv::query();
        $query->where('tolov_turi', 'муддатли');
        $this->yerSotuvService->applyDateFilters($query, $dateFilters);

        $lotRaqamlari = $query->pluck('lot_raqami')->toArray();

        if (empty($lotRaqamlari)) {
            return 0;
        }

        // Get fakt payments within the period
        $faktQuery = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari);

        if (!empty($dateFilters['auksion_sana_from'])) {
            $faktQuery->whereDate('tolov_sana', '>=', $dateFilters['auksion_sana_from']);
        }
        if (!empty($dateFilters['auksion_sana_to'])) {
            $faktQuery->whereDate('tolov_sana', '<=', $dateFilters['auksion_sana_to']);
        }

        return $faktQuery->sum('tolov_summa');
    }

    /**
     * Get available period options (years, quarters, months) via AJAX
     */
    public function getPeriodOptions()
    {
        $periods = $this->yerSotuvService->getAvailablePeriods();
        return response()->json($periods);
    }
    private function calculateTumanMonitoringByPeriod(?array $tumanPatterns, array $dateFilters, string $tolovTuri): array
    {
        $query = YerSotuv::query();

        $this->yerSotuvService->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', $tolovTuri);
        $this->yerSotuvService->applyDateFilters($query, $dateFilters);

        $lots = $query->count();

        if ($lots === 0) {
            return [
                'lots' => 0,
                'grafik' => 0,
                'fakt' => 0,
                'expected' => 0,
                'received' => 0,
                'difference' => 0,
                'percentage' => 0
            ];
        }

        $lotRaqamlari = $query->pluck('lot_raqami')->toArray();

        if ($tolovTuri === 'муддатли') {
            // Get grafik for PERIOD
            $grafikQuery = DB::table('grafik_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari);

            if (!empty($dateFilters['auksion_sana_from']) && !empty($dateFilters['auksion_sana_to'])) {
                $dateFrom = \Carbon\Carbon::parse($dateFilters['auksion_sana_from']);
                $dateTo = \Carbon\Carbon::parse($dateFilters['auksion_sana_to']);

                $grafikQuery->where(function ($q) use ($dateFrom, $dateTo) {
                    $q->where('yil', '>=', $dateFrom->year)
                        ->where('yil', '<=', $dateTo->year);

                    if ($dateFrom->year === $dateTo->year) {
                        $q->where('oy', '>=', $dateFrom->month)
                            ->where('oy', '<=', $dateTo->month);
                    }
                });
            }

            $grafikSumma = $grafikQuery->sum('grafik_summa');

            // Get fakt for PERIOD
            $faktQuery = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari);

            if (!empty($dateFilters['auksion_sana_from'])) {
                $faktQuery->whereDate('tolov_sana', '>=', $dateFilters['auksion_sana_from']);
            }
            if (!empty($dateFilters['auksion_sana_to'])) {
                $faktQuery->whereDate('tolov_sana', '<=', $dateFilters['auksion_sana_to']);
            }

            $faktSumma = $faktQuery->sum('tolov_summa');

            $difference = $grafikSumma - $faktSumma;
            $percentage = $grafikSumma > 0 ? ($faktSumma / $grafikSumma) * 100 : 0;

            return [
                'lots' => $lots,
                'grafik' => $grafikSumma,
                'fakt' => $faktSumma,
                'difference' => $difference,
                'percentage' => $percentage
            ];
        } else {
            // For муддатли эмас - similar period filtering for fakt
            $faktQuery = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari);

            if (!empty($dateFilters['auksion_sana_from'])) {
                $faktQuery->whereDate('tolov_sana', '>=', $dateFilters['auksion_sana_from']);
            }
            if (!empty($dateFilters['auksion_sana_to'])) {
                $faktQuery->whereDate('tolov_sana', '<=', $dateFilters['auksion_sana_to']);
            }

            $receivedAmount = $faktQuery->sum('tolov_summa');

            // Expected is total contract amount
            $data = YerSotuv::whereIn('lot_raqami', $lotRaqamlari)
                ->selectRaw('
                SUM(COALESCE(golib_tolagan, 0)) as golib_tolagan,
                SUM(COALESCE(shartnoma_summasi, 0)) as shartnoma_summasi,
                SUM(COALESCE(auksion_harajati, 0)) as auksion_harajati
            ')->first();

            $expectedAmount = ($data->golib_tolagan + $data->shartnoma_summasi) - $data->auksion_harajati;

            $difference = $expectedAmount - $receivedAmount;
            $percentage = $expectedAmount > 0 ? ($receivedAmount / $expectedAmount) * 100 : 0;

            return [
                'lots' => $lots,
                'expected' => $expectedAmount,
                'received' => $receivedAmount,
                'difference' => $difference,
                'percentage' => $percentage
            ];
        }
    }
    /**
     * Calculate monitoring summary
     */
    private function calculateMonitoringSummary(array $dateFilters, ?string $tolovTuri): array
    {
        $query = YerSotuv::query();

        // Apply payment type filter only if specified
        if ($tolovTuri !== null) {
            $query->where('tolov_turi', $tolovTuri);
        }
        $this->yerSotuvService->applyDateFilters($query, $dateFilters);

        $totalLots = $query->count();

        // Get lot numbers FIRST before modifying query
        $lotRaqamlari = (clone $query)->pluck('lot_raqami')->toArray();

        // Calculate expected amount
        $data = $query->selectRaw('
        SUM(COALESCE(golib_tolagan, 0)) as golib_tolagan,
        SUM(COALESCE(shartnoma_summasi, 0)) as shartnoma_summasi,
        SUM(COALESCE(auksion_harajati, 0)) as auksion_harajati
    ')->first();

        $expectedAmount = ($data->golib_tolagan + $data->shartnoma_summasi) - $data->auksion_harajati;

        // Calculate received amount
        $receivedAmount = 0;
        if (!empty($lotRaqamlari)) {
            $receivedAmount = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari)
                ->sum('tolov_summa');
        }

        $paymentPercentage = $expectedAmount > 0 ? ($receivedAmount / $expectedAmount) * 100 : 0;

        return [
            'total_lots' => $totalLots,
            'expected_amount' => $expectedAmount,
            'received_amount' => $receivedAmount,
            'payment_percentage' => $paymentPercentage
        ];
    }
    /**
     * Calculate tuman monitoring statistics
     */
    private function calculateTumanMonitoring(?array $tumanPatterns, array $dateFilters, string $tolovTuri): array
    {
        $query = YerSotuv::query();

        $this->yerSotuvService->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', $tolovTuri);
        $this->yerSotuvService->applyDateFilters($query, $dateFilters);

        $lots = $query->count();

        if ($lots === 0) {
            return [
                'lots' => 0,
                'grafik' => 0,
                'fakt' => 0,
                'expected' => 0,
                'received' => 0,
                'difference' => 0,
                'percentage' => 0
            ];
        }

        $lotRaqamlari = $query->pluck('lot_raqami')->toArray();

        if ($tolovTuri === 'муддатли') {
            // Get grafik summa (up to last month)
            $bugun = $this->yerSotuvService->getGrafikCutoffDate();

            $grafikSumma = DB::table('grafik_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari)
                ->whereRaw('CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") <= ?', [$bugun])
                ->sum('grafik_summa');

            // Get fakt summa
            $faktSumma = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari)
                ->sum('tolov_summa');

            $difference = $grafikSumma - $faktSumma;
            $percentage = $grafikSumma > 0 ? ($faktSumma / $grafikSumma) * 100 : 0;

            return [
                'lots' => $lots,
                'grafik' => $grafikSumma,
                'fakt' => $faktSumma,
                'difference' => $difference,
                'percentage' => $percentage
            ];
        } else {
            // For муддатли эмас - calculate expected vs received
            $data = YerSotuv::whereIn('lot_raqami', $lotRaqamlari)
                ->selectRaw('
                SUM(COALESCE(golib_tolagan, 0)) as golib_tolagan,
                SUM(COALESCE(shartnoma_summasi, 0)) as shartnoma_summasi,
                SUM(COALESCE(auksion_harajati, 0)) as auksion_harajati
            ')->first();

            $expectedAmount = ($data->golib_tolagan + $data->shartnoma_summasi) - $data->auksion_harajati;

            $receivedAmount = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari)
                ->sum('tolov_summa');

            $difference = $expectedAmount - $receivedAmount;
            $percentage = $expectedAmount > 0 ? ($receivedAmount / $expectedAmount) * 100 : 0;

            return [
                'lots' => $lots,
                'expected' => $expectedAmount,
                'received' => $receivedAmount,
                'difference' => $difference,
                'percentage' => $percentage
            ];
        }
    }

    /**
     * Calculate qoldiq_qarz specific data for monitoring page
     * ✅ SYNCHRONIZED with YerSotuvFilterService::applyQoldiqQarzFilter
     */
    private function calculateQoldiqQarzData(array $dateFilters): array
    {
        // Get qoldiq_qarz lots using the same logic as FilterService
        $qoldiqQarzLots = DB::table('yer_sotuvlar')
            ->where('tolov_turi', 'муддатли эмас')
            ->whereNotNull('holat')
            ->where('holat', '!=', 'Бекор қилинган')
            ->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida%')
                        ->orWhere('holat', 'like', '%G`olib shartnoma imzolashga rozilik bildirdi%')
                        ->orWhere('holat', 'like', '%Ишл. кечикт. туф. мулкни қабул қил. тасдиқланмаған%')
                        ->orWhere('holat', 'like', '%Бекор қилинған%');
                })
                // Include specific "Лот якунланди" lots (same as FilterService)
                ->orWhereIn('lot_raqami', ['19092338', '19227515']);
            })
            // Same condition as FilterService (>= ... - 0.01)
            ->whereRaw('(
                (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0) - COALESCE(auksion_harajati, 0))
                >= COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0) - 0.01
            )')
            ->pluck('lot_raqami')
            ->toArray();

        $count = count($qoldiqQarzLots);
        $expectedAmount = 0;
        $receivedAmount = 0;

        if (!empty($qoldiqQarzLots)) {
            $data = DB::table('yer_sotuvlar')
                ->whereIn('lot_raqami', $qoldiqQarzLots)
                ->selectRaw('
                    SUM(COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0) - COALESCE(auksion_harajati, 0)) as expected
                ')
                ->first();

            $expectedAmount = $data->expected ?? 0;

            $receivedAmount = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $qoldiqQarzLots)
                ->sum('tolov_summa');
        }

        $qoldiqAmount = max(0, $expectedAmount - $receivedAmount);

        return [
            'count' => $count,
            'expected_amount' => $expectedAmount,
            'received_amount' => $receivedAmount,
            'qoldiq_amount' => $qoldiqAmount,
            'lot_raqamlari' => $qoldiqQarzLots
        ];
    }

    /**
     * Prepare chart data
     */
    private function prepareChartData(array $tumanStatsMuddatli, array $tumanStatsMuddatliEmas, array $dateFilters): array
    {
        // Payment status distribution for муддатли
        $toliqTolanganlar = $this->yerSotuvService->getToliqTolanganlar(null, $dateFilters);
        $nazoratdagilar = $this->yerSotuvService->getNazoratdagilar(null, $dateFilters);
        $grafikOrtda = $this->yerSotuvService->getGrafikOrtda(null, $dateFilters);
        $auksonda = $this->yerSotuvService->getAuksondaTurgan(null, $dateFilters);

        // Monthly comparison data for муддатли
        $monthlyDataMuddatli = $this->getMonthlyComparisonData($dateFilters, 'муддатли');

        // Monthly comparison data for муддатли эмас
        $monthlyDataMuddatliEmas = $this->getMonthlyComparisonData($dateFilters, 'муддатли эмас');

        // Tuman comparison data for муддатли
        $tumanLabelsMuddatli = array_column($tumanStatsMuddatli, 'tuman');
        $tumanGrafikMuddatli = array_map(function ($val) {
            return $val / 1000000000;
        }, array_column($tumanStatsMuddatli, 'grafik'));
        $tumanFaktMuddatli = array_map(function ($val) {
            return $val / 1000000000;
        }, array_column($tumanStatsMuddatli, 'fakt'));

        // Tuman comparison data for муддатли эмас
        $tumanLabelsMuddatliEmas = array_column($tumanStatsMuddatliEmas, 'tuman');
        $tumanExpectedMuddatliEmas = array_map(function ($val) {
            return $val / 1000000000;
        }, array_column($tumanStatsMuddatliEmas, 'expected'));
        $tumanReceivedMuddatliEmas = array_map(function ($val) {
            return $val / 1000000000;
        }, array_column($tumanStatsMuddatliEmas, 'received'));

        return [
            'status' => [
                'completed' => $toliqTolanganlar['soni'],
                'under_control' => $nazoratdagilar['soni'],
                'overdue' => $grafikOrtda['soni'],
                'auction' => $auksonda['soni']
            ],
            'monthly_muddatli' => $monthlyDataMuddatli,
            'monthly_muddatli_emas' => $monthlyDataMuddatliEmas,
            'tuman_muddatli' => [
                'labels' => $tumanLabelsMuddatli,
                'grafik' => $tumanGrafikMuddatli,
                'fakt' => $tumanFaktMuddatli
            ],
            'tuman_muddatli_emas' => [
                'labels' => $tumanLabelsMuddatliEmas,
                'expected' => $tumanExpectedMuddatliEmas,
                'received' => $tumanReceivedMuddatliEmas
            ]
        ];
    }

    /**
     * Get monthly comparison data for charts
     */
    private function getMonthlyComparisonData(array $dateFilters, string $tolovTuri): array
    {
        $query = YerSotuv::query();
        $query->where('tolov_turi', $tolovTuri);
        $this->yerSotuvService->applyDateFilters($query, $dateFilters);

        $lotRaqamlari = $query->pluck('lot_raqami')->toArray();

        if (empty($lotRaqamlari)) {
            return [
                'labels' => [],
                'grafik' => [],
                'fakt' => [],
                'expected' => [],
                'received' => []
            ];
        }

        $months = [];
        $grafikData = [];
        $faktData = [];
        $expectedData = [];
        $receivedData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $year = $date->year;
            $month = $date->month;

            $monthLabel = $date->locale('uz')->translatedFormat('M Y');
            $months[] = $monthLabel;

            if ($tolovTuri === 'муддатли') {
                // Get grafik for this month
                $grafikSum = DB::table('grafik_tolovlar')
                    ->whereIn('lot_raqami', $lotRaqamlari)
                    ->where('yil', $year)
                    ->where('oy', $month)
                    ->sum('grafik_summa');

                // Get fakt for this month
                $faktSum = DB::table('fakt_tolovlar')
                    ->whereIn('lot_raqami', $lotRaqamlari)
                    ->whereYear('tolov_sana', $year)
                    ->whereMonth('tolov_sana', $month)
                    ->sum('tolov_summa');

                $grafikData[] = round($grafikSum / 1000000000, 2);
                $faktData[] = round($faktSum / 1000000000, 2);
            } else {
                // For муддатли эмас, only track received payments
                $receivedSum = DB::table('fakt_tolovlar')
                    ->whereIn('lot_raqami', $lotRaqamlari)
                    ->whereYear('tolov_sana', $year)
                    ->whereMonth('tolov_sana', $month)
                    ->sum('tolov_summa');

                $receivedData[] = round($receivedSum / 1000000000, 2);
            }
        }

        if ($tolovTuri === 'муддатли') {
            return [
                'labels' => $months,
                'grafik' => $grafikData,
                'fakt' => $faktData
            ];
        } else {
            return [
                'labels' => $months,
                'received' => $receivedData
            ];
        }
    }



    /**
     * Show filtered data with pagination
     */
    private function showFilteredData(Request $request, array $filters)
    {
        $query = YerSotuv::query();

        // ✅ Use FilterService for all filtering logic (optimized)
        $this->filterService->applyFilters($query, $filters);

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

        // ✅ DEBUG: Log the final query for qoldiq_qarz
        if (!empty($filters['qoldiq_qarz']) && $filters['qoldiq_qarz'] === 'true') {
            $sql = $query->toSql();
            $bindings = $query->getBindings();
            \Log::info('Qoldiq Qarz Final Query', ['sql' => $sql, 'bindings' => $bindings]);
        }

        // Calculate statistics using service
        $statistics = $this->yerSotuvService->getListStatistics(clone $query);

        // ✅ Calculate additional statistics for new cards
        $yerlarForStats = (clone $query)->with(['faktTolovlar', 'grafikTolovlar'])->get();

        $totalExpected = 0;
        $totalReceived = 0;
        $totalQoldiq = 0;
        $totalMuddatiUtgan = 0;

        foreach ($yerlarForStats as $yer) {
            // Calculate expected amount
            $expected = ($yer->golib_tolagan ?? 0) + ($yer->shartnoma_summasi ?? 0) - ($yer->auksion_harajati ?? 0);

            // Get received amount
            $received = $yer->faktTolovlar->sum('tolov_summa');

            // Calculate qoldiq
            $qoldiq = $expected - $received;

            // Calculate muddati utgan qarzdorlik
            $muddatiUtganQarz = 0;

            if ($yer->tolov_turi === 'муддатли') {
                // For muddatli: grafik up to last month - fakt (excluding auction payments)
                $cutoffDate = now()->subMonth()->endOfMonth()->format('Y-m-01');

                $grafikTushadigan = $yer->grafikTolovlar
                    ->filter(function($grafik) use ($cutoffDate) {
                        $grafikDate = $grafik->yil . '-' . str_pad($grafik->oy, 2, '0', STR_PAD_LEFT) . '-01';
                        return $grafikDate <= $cutoffDate;
                    })
                    ->sum('grafik_summa');

                $grafikTushgan = $yer->faktTolovlar
                    ->filter(function($fakt) {
                        $tolashNom = $fakt->tolash_nom ?? '';
                        return !str_contains($tolashNom, 'ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH');
                    })
                    ->sum('tolov_summa');

                $muddatiUtganQarz = max(0, $grafikTushadigan - $grafikTushgan);
            } elseif ($yer->tolov_turi === 'муддатли эмас') {
                // For muddatli emas: qoldiq if positive
                $muddatiUtganQarz = max(0, $qoldiq);
            }

            $totalExpected += $expected;
            $totalReceived += $received;
            $totalQoldiq += $qoldiq;
            $totalMuddatiUtgan += $muddatiUtganQarz;
        }

        // Add to statistics array
        $statistics['total_expected'] = $totalExpected;
        $statistics['total_received'] = $totalReceived;
        $statistics['total_qoldiq'] = $totalQoldiq;
        $statistics['total_muddati_utgan'] = $totalMuddatiUtgan;

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

        // ✅ FIX: Explicitly select all columns to ensure shartnoma_summasi and auksion_harajati are loaded
        $query->select('yer_sotuvlar.*');

        // Paginate results
        $yerlar = $query->with('faktTolovlar')->paginate(8)->withQueryString();

        // Get dropdown options
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

    /**
     * Show create form
     */
    public function create()
    {
        // Get distinct values for select options
        $tumanlar = YerSotuv::select('tuman')
            ->distinct()
            ->whereNotNull('tuman')
            ->orderBy('tuman')
            ->pluck('tuman');

        $mfylar = YerSotuv::select('mfy')
            ->distinct()
            ->whereNotNull('mfy')
            ->orderBy('mfy')
            ->pluck('mfy');

        $zonalar = YerSotuv::select('zona')
            ->distinct()
            ->whereNotNull('zona')
            ->orderBy('zona')
            ->pluck('zona');

        $boshRejaZonalari = YerSotuv::select('bosh_reja_zona')
            ->distinct()
            ->whereNotNull('bosh_reja_zona')
            ->orderBy('bosh_reja_zona')
            ->pluck('bosh_reja_zona');

        $yangiOzbekiston = YerSotuv::select('yangi_ozbekiston')
            ->distinct()
            ->whereNotNull('yangi_ozbekiston')
            ->orderBy('yangi_ozbekiston')
            ->pluck('yangi_ozbekiston');

        $qurilishTurlari1 = YerSotuv::select('qurilish_turi_1')
            ->distinct()
            ->whereNotNull('qurilish_turi_1')
            ->orderBy('qurilish_turi_1')
            ->pluck('qurilish_turi_1');

        $qurilishTurlari2 = YerSotuv::select('qurilish_turi_2')
            ->distinct()
            ->whereNotNull('qurilish_turi_2')
            ->orderBy('qurilish_turi_2')
            ->pluck('qurilish_turi_2');

        $asoslar = YerSotuv::select('asos')
            ->distinct()
            ->whereNotNull('asos')
            ->orderBy('asos')
            ->pluck('asos');

        $holatlar = YerSotuv::select('holat')
            ->distinct()
            ->whereNotNull('holat')
            ->orderBy('holat')
            ->pluck('holat');

        return view('yer-sotuvlar.create', compact(
            'tumanlar',
            'mfylar',
            'zonalar',
            'boshRejaZonalari',
            'yangiOzbekiston',
            'qurilishTurlari1',
            'qurilishTurlari2',
            'asoslar',
            'holatlar'
        ));
    }

    /**
     * Store new yer sotuv
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lot_raqami' => 'required|string|unique:yer_sotuvlar,lot_raqami',
            'tuman' => 'nullable|string',
            'mfy' => 'nullable|string',
            'manzil' => 'nullable|string',
            'unikal_raqam' => 'nullable|string',
            'zona' => 'nullable|string',
            'bosh_reja_zona' => 'nullable|string',
            'yangi_ozbekiston' => 'nullable|string',
            'maydoni' => 'nullable|numeric',
            'yil' => 'nullable|integer',
            'lokatsiya' => 'nullable|string',
            'qurilish_turi_1' => 'nullable|string',
            'qurilish_turi_2' => 'nullable|string',
            'qurilish_maydoni' => 'nullable|numeric',
            'investitsiya' => 'nullable|numeric',
            'boshlangich_narx' => 'nullable|numeric',
            'auksion_sana' => 'nullable|date',
            'sotilgan_narx' => 'nullable|numeric',
            'auksion_golibi' => 'nullable|string',
            'golib_turi' => 'nullable|string',
            'golib_nomi' => 'nullable|string',
            'telefon' => 'nullable|string',
            'tolov_turi' => 'nullable|string',
            'asos' => 'nullable|string',
            'auksion_turi' => 'nullable|string',
            'holat' => 'nullable|string',
            'shartnoma_holati' => 'nullable|string',
            'shartnoma_sana' => 'nullable|date',
            'shartnoma_raqam' => 'nullable|string',
            'golib_tolagan' => 'nullable|numeric',
            'buyurtmachiga_otkazilgan' => 'nullable|numeric',
            'chegirma' => 'nullable|numeric',
            'auksion_harajati' => 'nullable|numeric',
            'tushadigan_mablagh' => 'nullable|numeric',
            'davaktiv_jamgarmasi' => 'nullable|numeric',
            'shartnoma_tushgan' => 'nullable|numeric',
            'davaktivda_turgan' => 'nullable|numeric',
            'yer_auksion_harajat' => 'nullable|numeric',
            'shartnoma_summasi' => 'nullable|numeric',
            'farqi' => 'nullable|numeric',
            'grafik_data' => 'nullable|array',
            'grafik_data.*.yil' => 'nullable|integer',
            'grafik_data.*.oy' => 'nullable|integer|min:1|max:12',
            'grafik_data.*.summa' => 'nullable|numeric',
        ]);

        DB::beginTransaction();

        try {
            // Create yer sotuv record
            $yer = YerSotuv::create($validated);

            // If tolov_turi is "муддатли" AND grafik_data exists, create grafik tolovlar
            if ($request->tolov_turi === 'муддатли' && $request->has('grafik_data') && is_array($request->grafik_data)) {
                $this->createGrafikTolovlar($yer, $request->grafik_data);
            }

            DB::commit();

            return redirect()->route('yer-sotuvlar.show', $yer->lot_raqami)
                ->with('success', 'Ер участка муваффақиятли қўшилди!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating yer sotuv: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Хатолик юз берди: ' . $e->getMessage());
        }
    }

    /**
     * Create grafik tolovlar for a yer sotuv
     */
    private function createGrafikTolovlar(YerSotuv $yer, array $grafikData)
    {
        $oyNomlari = [
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

        foreach ($grafikData as $item) {
            // Skip empty rows
            if (empty($item['yil']) || empty($item['oy']) || empty($item['summa'])) {
                continue;
            }

            GrafikTolov::create([
                'yer_sotuv_id' => $yer->id,
                'lot_raqami' => $yer->lot_raqami,
                'yil' => $item['yil'],
                'oy' => $item['oy'],
                'oy_nomi' => $oyNomlari[$item['oy']] ?? '',
                'grafik_summa' => $item['summa'],
            ]);
        }
    }

    /**
     * Display monthly comparative monitoring page
     */
    public function monitoring_mirzayev(Request $request)
    {
        // Get last month's last day as default
        $lastMonth = now()->subMonth()->endOfMonth();

        // Filters
        $filters = [
            'year' => $request->get('year', $lastMonth->year),
            'month' => $request->get('month', $lastMonth->month),
            'tolov_turi' => $request->get('tolov_turi', 'all'), // 'all', 'muddatli', 'muddatli_emas'
        ];

        // Get comparative data
        $comparativeData = $this->yerSotuvService->getMonthlyComparativeData($filters);

        // Get available years from grafik_tolovlar
        $availableYears = DB::table('grafik_tolovlar')
            ->select('yil')
            ->distinct()
            ->orderBy('yil', 'desc')
            ->pluck('yil');

        // All months dictionary
        $allMonths = [
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

        // Determine which months to show based on selected year
        $selectedYear = $filters['year'];
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $months = [];

        if ($selectedYear < $currentYear) {
            // Past years - show all 12 months
            $months = $allMonths;
        } elseif ($selectedYear == $currentYear) {
            // Current year - show only up to last completed month
            $lastCompletedMonth = $currentMonth - 1;
            if ($lastCompletedMonth < 1) {
                $lastCompletedMonth = 12;
            }

            for ($i = 1; $i <= $lastCompletedMonth; $i++) {
                $months[$i] = $allMonths[$i];
            }
        }

        // Tolov turi options
        $tolovTuriOptions = [
            'all' => 'Барчаси',
            'muddatli' => 'Муддатли (бўлиб тўлаш)',
            'muddatli_emas' => 'Муддатли эмас (бир йўла)'
        ];

        return view('yer-sotuvlar.monitoring_mirzayev', compact(
            'comparativeData',
            'availableYears',
            'months',
            'filters',
            'tolovTuriOptions'
        ));
    }

    /**
     * Display Yigma Malumot (Comprehensive Summary) page
     * This combines both муддатли and муддатли эмас data with additional calculations
     */
    public function yigmaMalumot(Request $request)
    {
        // DEFAULT: From 01.01.2024 to today
        $dateFilters = [
            'auksion_sana_from' => $request->auksion_sana_from ?? '2024-01-01',
            'auksion_sana_to' => $request->auksion_sana_to ?? now()->toDateString(),
        ];

        $tumanlar = $this->monitoringService->getTumanlar();
        $statistics = [];

        foreach ($tumanlar as $tuman) {
            $tumanPatterns = $this->yerSotuvService->getTumanPatterns($tuman);
            $stat = $this->monitoringService->calculateTumanStatistics($tumanPatterns, $dateFilters, true);
            $stat['tuman'] = $tuman;
            $statistics[] = $stat;
        }

        // Calculate JAMI totals with bekor qilinganlar
        $jami = $this->monitoringService->calculateJamiTotalsWithBekor($statistics);

        return view('yer-sotuvlar.yigma', compact('statistics', 'jami', 'dateFilters'));
    }
}
