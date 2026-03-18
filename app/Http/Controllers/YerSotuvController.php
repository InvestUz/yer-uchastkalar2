<?php

namespace App\Http\Controllers;

use App\Models\GrafikTolov;
use App\Models\YerSotuv;
use App\Models\FaktTolov;
use App\Models\DavaktivRasxod;
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
    public function show(Request $request, $lot_raqami)
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

        // Date filter: default to today if not specified
        $dateFilters = [
            'date_to' => $request->date_to ?? now()->toDateString(),
        ];

        $tolovTaqqoslash = $this->yerSotuvService->calculateTolovTaqqoslash($yer);

        return view('yer-sotuvlar.show', compact('yer', 'tolovTaqqoslash', 'dateFilters'));
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

    // ✅ Handle tuman filter (Admin dropdown OR District user auto-filter)
    $selectedTuman = null;
    $filteredTumanlar = $tumanlar;

    if (auth()->check()) {
        if (auth()->user()->isSuperAdmin() && $request->filled('tuman')) {
            // Admin selected a specific tuman from dropdown
            $selectedTuman = $request->tuman;
            $filteredTumanlar = [$selectedTuman];
        } elseif (auth()->user()->isDistrict()) {
            // District user: auto-filter by their tuman
            $selectedTuman = auth()->user()->tuman;
            if ($selectedTuman) {
                $filteredTumanlar = [$selectedTuman];
            }
        }
    }

    // Calculate statistics for each tuman using MonitoringService
    $monitoringStatistics = [];
    foreach ($filteredTumanlar as $tuman) {
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
    // Pass tuman patterns if admin selected a tuman
    $qoldiqTumanPatterns = null;
    if ($selectedTuman) {
        $qoldiqTumanPatterns = $this->yerSotuvService->getTumanPatterns($selectedTuman);
    }
    $qoldiqQarzData = $this->calculateQoldiqQarzData($dateFilters, $qoldiqTumanPatterns);

    // Get tuman statistics with period-aware calculations (existing functionality)
    $tumanStatsMuddatli = [];
    foreach ($filteredTumanlar as $tuman) {
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
    foreach ($filteredTumanlar as $tuman) {
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
        'qoldiqQarzData',
        'tumanlar',
        'selectedTuman'
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
     * ✅ SUPPORTS TUMAN FILTERING: Both admin filter and district user auto-filter
     */
    private function calculateQoldiqQarzData(array $dateFilters, ?array $tumanPatterns = null): array
    {
        // Get qoldiq_qarz lots using the EXACT same logic as FilterService
        $query = DB::table('yer_sotuvlar')
            ->where('tolov_turi', 'муддатли эмас')
            ->whereNotNull('holat')
            ->where(function ($q) {
                $q->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida%')
                    ->orWhere('holat', 'like', '%G`olib shartnoma imzolashga rozilik bildirdi%')
                    ->orWhere('holat', 'like', '%Ишл. кечикт. туф. мулкни қабул қил. тасдиқланмаған%')
                    ->orWhere('holat', 'like', '%Бекор қилинған%');
            })
            // ✅ Only include lots where Қолдиқ маблағ > 0
            // Formula: expected - paid > 0.01 (same as FilterService)
            ->whereRaw('(
                (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0) - COALESCE(auksion_harajati, 0))
                - COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0)
                > 0.01
            )');

        // ✅ TUMAN FILTERING: Apply if tuman patterns provided (admin filter or district user)
        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        } elseif (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->isDistrict()) {
            // Fallback: Auto-filter for district users if no explicit filter provided
            $userDistrict = \Illuminate\Support\Facades\Auth::user()->tuman;
            if ($userDistrict) {
                $districtPatterns = $this->yerSotuvService->getTumanPatterns($userDistrict);
                $query->where(function ($q) use ($districtPatterns) {
                    foreach ($districtPatterns as $pattern) {
                        $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                    }
                });
            }
        }

        $qoldiqQarzLots = $query->pluck('lot_raqami')->toArray();

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

            // Calculate muddati utgan qarzdorlik - ONLY for муддатли
            $muddatiUtganQarz = 0;

            if ($yer->tolov_turi === 'муддатли') {
                // Formula: Шартнома графиги б-ча тўлов - Ғолиб тўлаган маблағ (ALL payments)
                $cutoffDate = now()->subMonth()->endOfMonth()->format('Y-m-01');

                $grafikTushadigan = $yer->grafikTolovlar
                    ->filter(function($grafik) use ($cutoffDate) {
                        $grafikDate = $grafik->yil . '-' . str_pad($grafik->oy, 2, '0', STR_PAD_LEFT) . '-01';
                        return $grafikDate <= $cutoffDate;
                    })
                    ->sum('grafik_summa');

                // Get fakt payments EXCLUDING auction org (only if name STARTS with ELEKTRON pattern)
                $grafikTushgan = $yer->faktTolovlar
                    ->filter(function($fakt) {
                        $tolashNom = $fakt->tolash_nom ?? '';
                        return !str_starts_with($tolashNom, 'ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZ')
                            && !str_starts_with($tolashNom, 'ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH AJ')
                            && !str_starts_with($tolashNom, 'ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZI')
                            && !str_starts_with($tolashNom, 'ГУП');
                    })
                    ->sum('tolov_summa');

                // Allow negative (overpaid), but only add positive debt to total
                // 5-cent threshold: treat small debts as fully paid
                $lotDiff = $grafikTushadigan - $grafikTushgan;
                if ($lotDiff > 0.05) {
                    $muddatiUtganQarz = $lotDiff;
                }
            }
            // Note: муддатли эмас does not have overdue debt calculation

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

    /**
     * Get detailed grafik payments (API endpoint)
     */
    public function getGrafikDetail(Request $request)
    {
        $dateFrom = $request->date_from ?? '2024-01-01';
        $dateTo = $request->date_to ?? now()->toDateString();
        $tuman = $request->tuman ?? null;

        $dateFilters = [
            'auksion_sana_from' => $dateFrom,
            'auksion_sana_to' => $dateTo
        ];

        // Get tuman patterns if tuman specified
        $tumanPatterns = null;
        if ($tuman) {
            $tumanPatterns = $this->yerSotuvService->getTumanPatterns($tuman);
        }

        // Use same logic as monitoring service
        $lots = $this->yerSotuvService->getBolibLotlar($tumanPatterns, $dateFilters);
        $bugun = now()->format('Y-m') . '-01';

        $payments = [];
        $total = 0;

        foreach ($lots as $lotRaqami) {
            // Get fakt payments EXCLUDING ELEKTRON
            $faktSum = DB::table('fakt_tolovlar')
                ->where('lot_raqami', $lotRaqami)
                ->where(function($q) {
                    $q->where('tolash_nom', 'NOT LIKE', 'ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZ%')
                      ->where('tolash_nom', 'NOT LIKE', 'ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH AJ%')
                      ->where('tolash_nom', 'NOT LIKE', 'ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZI%')
                      ->where('tolash_nom', 'NOT LIKE', 'ГУП%ELEKTRON ONLAYN-AUKSIONLARNI%')
                      ->orWhereNull('tolash_nom');
                })
                ->sum('tolov_summa');

            $total += $faktSum;

            // Get payer name
            $tolashNom = DB::table('fakt_tolovlar')
                ->where('lot_raqami', $lotRaqami)
                ->where(function($q) {
                    $q->where('tolash_nom', 'NOT LIKE', 'ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZ%')
                      ->where('tolash_nom', 'NOT LIKE', 'ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH AJ%')
                      ->where('tolash_nom', 'NOT LIKE', 'ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZI%')
                      ->where('tolash_nom', 'NOT LIKE', 'ГУП%ELEKTRON ONLAYN-AUKSIONLARNI%')
                      ->whereNotNull('tolash_nom');
                })
                ->value('tolash_nom');

            if ($faktSum > 0) {
                $payments[] = [
                    'lot_raqami' => $lotRaqami,
                    'tolash_nom' => $tolashNom ?? '-',
                    'tolov_summa' => $faktSum
                ];
            }
        }

        // Sort by amount descending
        usort($payments, function($a, $b) {
            return $b['tolov_summa'] <=> $a['tolov_summa'];
        });

        return response()->json([
            'total' => $total,
            'payments' => $payments
        ]);
    }

    /**
     * Display financial report (Fin-Xisobot) with data from davaktiv_rasxod table
     */
    public function finXisobot(Request $request)
    {
        try {
            $filters = $this->normalizeFinXisobotFilters($request);
            // District users automatically see only their own district
            if (auth()->check() && auth()->user()->isDistrict() && auth()->user()->tuman) {
                $filters['district_restrict'] = auth()->user()->tuman;
            }
            $summary = $this->buildFinXisobotSummary($filters);

            return view('yer-sotuvlar.fin-xisobot', array_merge($summary, [
                'filters' => $filters,
                'activeFilterParams' => $this->getFinXisobotActiveFilterParams($filters),
                'availableYears' => $this->getFinXisobotAvailableYears(),
                'monthOptions' => $this->getFinXisobotMonthOptions(),
            ]));
        } catch (\Exception $e) {
            \Log::error('Error reading financial data: ' . $e->getMessage());
            return view('yer-sotuvlar.fin-xisobot', [
                'financialData' => [],
                'recipients' => [],
                'districts' => [],
                'districtData' => [],
                'categoryTotals' => [],
                'categoryCounts' => [],
                'districtCounts' => [],
                'districtCategoryCounts' => [],
                'paymentCategories' => [],
                'totalAmount' => 0,
                'transactionCount' => 0,
                'filters' => [
                    'year' => null,
                    'month' => null,
                    'date_from' => null,
                    'date_to' => null,
                ],
                'activeFilterParams' => [],
                'availableYears' => $this->getFinXisobotAvailableYears(),
                'monthOptions' => $this->getFinXisobotMonthOptions(),
                'error' => 'Маълумотларни ўқишда хато: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display detail list for selected district/category from Fin-Xisobot table.
     */
    public function finXisobotDetails(Request $request)
    {
        try {
            $filters = $this->normalizeFinXisobotFilters($request);
            // District users automatically see only their own district
            if (auth()->check() && auth()->user()->isDistrict() && auth()->user()->tuman) {
                $filters['district_restrict'] = auth()->user()->tuman;
            }
            $summary = $this->buildFinXisobotSummary($filters);

            $district = trim((string)$request->query('district', ''));
            $category = trim((string)$request->query('category', ''));

            $rows = $summary['financialData'];

            if ($district !== '') {
                $rows = array_values(array_filter($rows, static function ($row) use ($district) {
                    if ($district === 'Бошқа' || $district === 'Номалум') {
                        return ($row['district_original'] ?? $row['district']) === $district;
                    }

                    return $row['district'] === $district;
                }));
            }

            if ($category !== '') {
                $rows = array_values(array_filter($rows, static function ($row) use ($category) {
                    return $row['category'] === $category;
                }));
            }

            usort($rows, static function ($a, $b) {
                return $b['amount'] <=> $a['amount'];
            });

            $detailTotal = 0;
            foreach ($rows as $row) {
                $detailTotal += (float)$row['amount'];
            }

            return view('yer-sotuvlar.fin-xisobot-details', [
                'rows' => $rows,
                'rawDistrict' => $district,
                'rawCategory' => $category,
                'selectedDistrict' => $district !== '' ? $district : 'Барча ҳудудлар',
                'selectedCategory' => $category !== '' ? $category : 'Барча тоифалар',
                'recordCount' => count($rows),
                'totalAmount' => $detailTotal,
                'filters' => $filters,
                'activeFilterParams' => $this->getFinXisobotActiveFilterParams($filters),
                'monthOptions' => $this->getFinXisobotMonthOptions(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error reading fin-xisobot details: ' . $e->getMessage());
            return redirect()->route('yer-sotuvlar.fin-xisobot')
                ->with('error', 'Детал маълумотларини очишда хато: ' . $e->getMessage());
        }
    }

    /**
     * Build summary data for Fin-Xisobot and keep one source of truth
     * for category and district classification.
     */
    private function buildFinXisobotSummary(array $filters = []): array
    {
        $summaryFilters = $filters;
        $districtRestrict = trim((string)($summaryFilters['district_restrict'] ?? ''));
        unset($summaryFilters['district_restrict']);

        $records = DavaktivRasxod::all();

        $paymentCategories = $this->getFinXisobotPaymentCategories();
        $proportionalCategoryLookup = $this->getFinXisobotProportionalCategoryLookup();

        if ($records->isEmpty()) {
            return [
                'financialData' => [],
                'recipients' => [],
                'districts' => [],
                'districtData' => [],
                'categoryTotals' => $paymentCategories,
                'categoryCounts' => array_fill_keys(array_keys($paymentCategories), 0),
                'districtCounts' => [],
                'districtCategoryCounts' => [],
                'paymentCategories' => $paymentCategories,
                'proportionalCategoryLookup' => $proportionalCategoryLookup,
                'totalAmount' => 0,
                'transactionCount' => 0,
            ];
        }

        $districtPatterns = $this->getFinXisobotDistrictPatterns();
        $categoryDistrictFallbacks = $this->getFinXisobotCategoryDistrictFallbacks();
        $lotLookup = $this->buildFinXisobotLotLookup();

        $districtData = [];
        $recipientTotals = [];
        $categoryTotals = $paymentCategories;
        $categoryCounts = array_fill_keys(array_keys($paymentCategories), 0);
        $districtCounts = [];
        $districtCategoryCounts = [];
        $totalAmount = 0;
        $financialData = [];

        foreach ($records as $record) {
            if (!$this->passesFinXisobotFilters($record, $summaryFilters)) {
                continue;
            }

            $recipient = $record->recipient_name ?? 'Белгисиз';
            $articleName = $record->article ?? 'Тошкент шахар махаллий бюджетига';
            $amount = (float)($record->amount ?? 0);

            if ($amount <= 0) {
                continue;
            }

            $category = $this->resolveFinXisobotCategory($articleName, $paymentCategories);
            $district = $this->resolveFinXisobotDistrict($record, $category, $districtPatterns, $categoryDistrictFallbacks);

            $totalAmount += $amount;
            $recipientTotals[$recipient] = ($recipientTotals[$recipient] ?? 0) + $amount;
            $categoryTotals[$category] = ($categoryTotals[$category] ?? 0) + $amount;
            $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;

            $lotMatch = $this->resolveFinXisobotLotForRecord($record, $lotLookup);

            $financialData[] = [
                'id' => $record->id,
                'date' => $record->doc_date ? $record->doc_date->format('d.m.Y') : '',
                'doc_num' => $record->doc_number ?? '',
                'recipient' => $recipient,
                'article' => $articleName,
                'amount' => $amount,
                'details' => $record->details ?? '',
                'district' => $district,
                'district_original' => $district,
                'category' => $category,
                'lot_raqami' => $lotMatch['lot_raqami'],
                'lot_match_source' => $lotMatch['source'],
            ];

            $this->appendFinXisobotDistrictTotals(
                $district,
                $category,
                $amount,
                $districtData,
                $districtCounts,
                $districtCategoryCounts,
                $paymentCategories
            );
        }

        $activeDistrictData = [];
        foreach ($districtData as $dist => $data) {
            if ($data['Жами'] > 0) {
                $activeDistrictData[$dist] = $data;
            }
        }

        uasort($activeDistrictData, static function ($a, $b) {
            return $b['Жами'] <=> $a['Жами'];
        });

        $summary = [
            'financialData' => $financialData,
            'recipients' => $recipientTotals,
            'districts' => array_keys($activeDistrictData),
            'districtData' => $activeDistrictData,
            'categoryTotals' => $categoryTotals,
            'categoryCounts' => $categoryCounts,
            'districtCounts' => $districtCounts,
            'districtCategoryCounts' => $districtCategoryCounts,
            'paymentCategories' => $paymentCategories,
            'proportionalCategoryLookup' => $proportionalCategoryLookup,
            'totalAmount' => $totalAmount,
            'transactionCount' => count($financialData),
        ];

        $summary = $this->applyFinXisobotUmumiyTotals(
            $summary,
            $summaryFilters,
            $districtPatterns,
            $paymentCategories
        );

        $summary = $this->applyFinXisobotProportionalCategoryDistribution($summary, $paymentCategories);

        if ($districtRestrict !== '') {
            $summary = $this->filterFinXisobotSummaryByDistrict(
                $summary,
                $districtRestrict,
                $districtPatterns,
                $paymentCategories
            );
        }

        return $summary;
    }

    private function normalizeFinXisobotFilters(Request $request): array
    {
        $yearInput = trim((string)$request->query('year', ''));
        $monthInput = trim((string)$request->query('month', ''));
        $dateFromInput = trim((string)$request->query('date_from', ''));
        $dateToInput = trim((string)$request->query('date_to', ''));

        $year = null;
        if ($yearInput !== '' && ctype_digit($yearInput)) {
            $parsedYear = (int)$yearInput;
            if ($parsedYear >= 2000 && $parsedYear <= 2100) {
                $year = $parsedYear;
            }
        }

        $month = null;
        if ($monthInput !== '' && ctype_digit($monthInput)) {
            $parsedMonth = (int)$monthInput;
            if ($parsedMonth >= 1 && $parsedMonth <= 12) {
                $month = $parsedMonth;
            }
        }

        $dateFrom = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFromInput) ? $dateFromInput : null;
        $dateTo = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateToInput) ? $dateToInput : null;

        if ($dateFrom !== null && $dateTo !== null && $dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        return [
            'year' => $year,
            'month' => $month,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ];
    }

    private function getFinXisobotActiveFilterParams(array $filters): array
    {
        // district_restrict is derived from auth, never exposed in URLs
        return array_filter([
            'year' => $filters['year'] ?? null,
            'month' => $filters['month'] ?? null,
            'date_from' => $filters['date_from'] ?? null,
            'date_to' => $filters['date_to'] ?? null,
        ], static function ($value) {
            return $value !== null && $value !== '';
        });
    }

    private function getFinXisobotAvailableYears(): array
    {
        $years = [];

        DavaktivRasxod::query()
            ->select(['doc_date', 'month'])
            ->chunk(1000, function ($rows) use (&$years) {
                foreach ($rows as $row) {
                    $effectiveDate = $this->getFinXisobotRecordDate($row);
                    if ($effectiveDate !== null) {
                        $years[(int)$effectiveDate->year] = true;
                    }
                }
            });

        $availableYears = array_map('intval', array_keys($years));
        rsort($availableYears, SORT_NUMERIC);

        return array_values($availableYears);
    }

    private function getFinXisobotMonthOptions(): array
    {
        return [
            1 => 'Январ',
            2 => 'Феврал',
            3 => 'Март',
            4 => 'Апрел',
            5 => 'Май',
            6 => 'Июн',
            7 => 'Июл',
            8 => 'Август',
            9 => 'Сентябр',
            10 => 'Октябр',
            11 => 'Ноябр',
            12 => 'Декабр',
        ];
    }

    private function getFinXisobotRecordDate(DavaktivRasxod $record): ?\Carbon\Carbon
    {
        if ($record->doc_date) {
            return $record->doc_date instanceof \Carbon\Carbon
                ? $record->doc_date->copy()->startOfDay()
                : \Carbon\Carbon::parse($record->doc_date)->startOfDay();
        }

        $monthValue = trim((string)($record->month ?? ''));
        if ($monthValue === '') {
            return null;
        }

        foreach (['d.m.Y', 'd,m,Y', 'Y-m-d'] as $format) {
            try {
                return \Carbon\Carbon::createFromFormat($format, $monthValue)->startOfDay();
            } catch (\Exception $e) {
                // Try next format.
            }
        }

        try {
            return \Carbon\Carbon::parse($monthValue)->startOfDay();
        } catch (\Exception $e) {
            return null;
        }
    }

    private function passesFinXisobotFilters(DavaktivRasxod $record, array $filters): bool
    {
        $effectiveDate = $this->getFinXisobotRecordDate($record);

        if (!empty($filters['year'])) {
            if ($effectiveDate === null || (int)$effectiveDate->year !== (int)$filters['year']) {
                return false;
            }
        }

        if (!empty($filters['month'])) {
            if ($effectiveDate === null || (int)$effectiveDate->month !== (int)$filters['month']) {
                return false;
            }
        }

        if (!empty($filters['date_from'])) {
            $fromDate = \Carbon\Carbon::createFromFormat('Y-m-d', $filters['date_from'])->startOfDay();
            if ($effectiveDate === null || $effectiveDate->lt($fromDate)) {
                return false;
            }
        }

        if (!empty($filters['date_to'])) {
            $toDate = \Carbon\Carbon::createFromFormat('Y-m-d', $filters['date_to'])->endOfDay();
            if ($effectiveDate === null || $effectiveDate->gt($toDate)) {
                return false;
            }
        }

        return true;
    }

    private function getFinXisobotCanonicalDistricts(array $districtPatterns): array
    {
        $districts = [];
        foreach ($districtPatterns as $districtName) {
            if (!in_array($districtName, $districts, true)) {
                $districts[] = $districtName;
            }
        }

        return $districts;
    }

    private function appendFinXisobotDistrictTotals(
        string $district,
        string $category,
        float $amount,
        array &$districtData,
        array &$districtCounts,
        array &$districtCategoryCounts,
        array $paymentCategories
    ): void {
        if (!isset($districtData[$district])) {
            $districtData[$district] = array_merge(['Жами' => 0], $paymentCategories);
        }
        $districtData[$district]['Жами'] += $amount;
        if (isset($districtData[$district][$category])) {
            $districtData[$district][$category] += $amount;
        }

        $districtCounts[$district] = ($districtCounts[$district] ?? 0) + 1;
        if (!isset($districtCategoryCounts[$district])) {
            $districtCategoryCounts[$district] = array_fill_keys(array_keys($paymentCategories), 0);
        }
        $districtCategoryCounts[$district][$category] = ($districtCategoryCounts[$district][$category] ?? 0) + 1;
    }

    private function filterFinXisobotSummaryByDistrict(
        array $summary,
        string $districtRestrict,
        array $districtPatterns,
        array $paymentCategories
    ): array {
        $canonicalRestrict = $this->resolveFinXisobotDistrictRestrictCanonical($districtRestrict, $districtPatterns);
        $normalizedRestrict = $this->normalizeFinXisobotDistrictKey($districtRestrict);

        $filteredRows = array_values(array_filter(
            $summary['financialData'] ?? [],
            function ($row) use ($canonicalRestrict, $normalizedRestrict) {
                $rowDistrict = (string)($row['district'] ?? '');

                if ($canonicalRestrict !== null) {
                    return $rowDistrict === $canonicalRestrict;
                }

                return $normalizedRestrict !== ''
                    && $this->normalizeFinXisobotDistrictKey($rowDistrict) === $normalizedRestrict;
            }
        ));

        $recipientTotals = [];

        foreach ($filteredRows as $row) {
            $recipient = (string)($row['recipient'] ?? 'Белгисиз');
            $amount = (float)($row['amount'] ?? 0);
            $recipientTotals[$recipient] = ($recipientTotals[$recipient] ?? 0) + $amount;
        }

        $matchedDistrict = null;
        foreach (array_keys($summary['districtData'] ?? []) as $districtName) {
            if ($canonicalRestrict !== null && $districtName === $canonicalRestrict) {
                $matchedDistrict = $districtName;
                break;
            }

            if ($normalizedRestrict !== '' && $this->normalizeFinXisobotDistrictKey($districtName) === $normalizedRestrict) {
                $matchedDistrict = $districtName;
                break;
            }
        }

        $categoryTotals = $paymentCategories;
        $categoryCounts = array_fill_keys(array_keys($paymentCategories), 0);
        $districtData = [];
        $districtCounts = [];
        $districtCategoryCounts = [];
        $totalAmount = 0.0;
        $transactionCount = 0;

        if ($matchedDistrict !== null) {
            $districtRow = $summary['districtData'][$matchedDistrict] ?? array_merge(['Жами' => 0], $paymentCategories);
            $districtCount = (int)($summary['districtCounts'][$matchedDistrict] ?? count($filteredRows));
            $districtCategoryRow = $summary['districtCategoryCounts'][$matchedDistrict] ?? array_fill_keys(array_keys($paymentCategories), 0);

            foreach ($paymentCategories as $category => $value) {
                $categoryTotals[$category] = (float)($districtRow[$category] ?? 0);
                $categoryCounts[$category] = (int)($districtCategoryRow[$category] ?? 0);
            }

            $districtData[$matchedDistrict] = $districtRow;
            $districtCounts[$matchedDistrict] = $districtCount;
            $districtCategoryCounts[$matchedDistrict] = $districtCategoryRow;
            $totalAmount = (float)($districtRow['Жами'] ?? 0);
            $transactionCount = $districtCount;
        }

        return [
            'financialData' => $filteredRows,
            'recipients' => $recipientTotals,
            'districts' => array_keys($districtData),
            'districtData' => $districtData,
            'categoryTotals' => $categoryTotals,
            'categoryCounts' => $categoryCounts,
            'districtCounts' => $districtCounts,
            'districtCategoryCounts' => $districtCategoryCounts,
            'paymentCategories' => $paymentCategories,
            'proportionalCategoryLookup' => $summary['proportionalCategoryLookup'] ?? $this->getFinXisobotProportionalCategoryLookup(),
            'totalAmount' => $totalAmount,
            'transactionCount' => $transactionCount,
        ];
    }

    private function applyFinXisobotUmumiyTotals(
        array $summary,
        array $filters,
        array $districtPatterns,
        array $paymentCategories
    ): array {
        $receivedTotals = $this->getFinXisobotUmumiyReceivedTotals($filters, $districtPatterns);

        if ($receivedTotals['total'] !== null) {
            $summary['totalAmount'] = (float)$receivedTotals['total'];
        }

        foreach ($receivedTotals['district_totals'] as $district => $receivedAmount) {
            if (!isset($summary['districtData'][$district])) {
                $summary['districtData'][$district] = array_merge(['Жами' => 0], $paymentCategories);
            }

            $summary['districtData'][$district]['Жами'] = (float)$receivedAmount;

            if (!isset($summary['districtCounts'][$district])) {
                $summary['districtCounts'][$district] = 0;
            }

            if (!isset($summary['districtCategoryCounts'][$district])) {
                $summary['districtCategoryCounts'][$district] = array_fill_keys(array_keys($paymentCategories), 0);
            }
        }

        $activeDistrictData = [];
        foreach (($summary['districtData'] ?? []) as $district => $values) {
            $jami = (float)($values['Жами'] ?? 0);
            $count = (int)($summary['districtCounts'][$district] ?? 0);
            if ($jami > 0 || $count > 0) {
                $activeDistrictData[$district] = $values;
            }
        }

        uasort($activeDistrictData, static function ($left, $right) {
            return ((float)($right['Жами'] ?? 0)) <=> ((float)($left['Жами'] ?? 0));
        });

        $summary['districtData'] = $activeDistrictData;
        $summary['districts'] = array_keys($activeDistrictData);

        return $summary;
    }

    private function applyFinXisobotProportionalCategoryDistribution(array $summary, array $paymentCategories): array
    {
        $proportionalCategoryLookup = $summary['proportionalCategoryLookup'] ?? $this->getFinXisobotProportionalCategoryLookup();
        $proportionalCategories = array_values(array_intersect(array_keys($paymentCategories), array_keys($proportionalCategoryLookup)));

        if (empty($proportionalCategories) || empty($summary['districtData'])) {
            return $summary;
        }

        $overallAmount = max(0.0, (float)($summary['totalAmount'] ?? 0));
        $overallCount = max(0, (int)($summary['transactionCount'] ?? 0));

        foreach (array_keys($summary['districtData']) as $district) {
            if (!isset($summary['districtCategoryCounts'][$district])) {
                $summary['districtCategoryCounts'][$district] = array_fill_keys(array_keys($paymentCategories), 0);
            }
        }

        foreach ($proportionalCategories as $category) {
            $categoryTotal = (float)($summary['categoryTotals'][$category] ?? 0);
            $categoryCount = (int)($summary['categoryCounts'][$category] ?? 0);

            foreach (array_keys($summary['districtData']) as $district) {
                $districtTotal = max(0.0, (float)($summary['districtData'][$district]['Жами'] ?? 0));
                $districtCount = max(0, (int)($summary['districtCounts'][$district] ?? 0));

                $summary['districtData'][$district][$category] = $overallAmount > 0
                    ? ($categoryTotal * ($districtTotal / $overallAmount))
                    : 0.0;

                $summary['districtCategoryCounts'][$district][$category] = $overallCount > 0
                    ? (int)round($categoryCount * ($districtCount / $overallCount))
                    : 0;
            }
        }

        return $summary;
    }

    private function getFinXisobotUmumiyReceivedTotals(array $filters, array $districtPatterns): array
    {
        try {
            $dateFilters = $this->buildFinXisobotUmumiyDateFilters($filters);
            $statistics = $this->yerSotuvService->getDetailedStatistics($dateFilters);

            $overallReceived = (float)(
                (float)($statistics['jami']['biryola_fakt'] ?? 0)
                + (float)($statistics['jami']['bolib_tushgan_all'] ?? 0)
            );

            $districtTotals = [];
            foreach (($statistics['tumanlar'] ?? []) as $tumanData) {
                $tumanName = trim((string)($tumanData['tuman'] ?? ''));
                if ($tumanName === '') {
                    continue;
                }

                $canonicalDistrict = $this->resolveFinXisobotDistrictRestrictCanonical($tumanName, $districtPatterns);
                if ($canonicalDistrict === null) {
                    $canonicalDistrict = 'Номалум';
                }

                $received = (float)(
                    (float)($tumanData['biryola_fakt'] ?? 0)
                    + (float)($tumanData['bolib_tushgan_all'] ?? 0)
                );

                $districtTotals[$canonicalDistrict] = ($districtTotals[$canonicalDistrict] ?? 0) + $received;
            }

            return [
                'total' => $overallReceived,
                'district_totals' => $districtTotals,
            ];
        } catch (\Throwable $e) {
            \Log::warning('Fin-Xisobot umumiy totals fallback: ' . $e->getMessage());

            return [
                'total' => null,
                'district_totals' => [],
            ];
        }
    }

    private function buildFinXisobotUmumiyDateFilters(array $filters): array
    {
        $from = '2024-01-01';
        $to = now()->toDateString();

        if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
            $from = $filters['date_from'] ?? $from;
            $to = $filters['date_to'] ?? $to;
        } elseif (!empty($filters['year']) && !empty($filters['month'])) {
            $year = (int)$filters['year'];
            $month = (int)$filters['month'];
            if ($year >= 2000 && $year <= 2100 && $month >= 1 && $month <= 12) {
                $start = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $end = $start->copy()->endOfMonth();
                $from = $start->format('Y-m-d');
                $to = $end->format('Y-m-d');
            }
        } elseif (!empty($filters['year'])) {
            $year = (int)$filters['year'];
            if ($year >= 2000 && $year <= 2100) {
                $from = sprintf('%04d-01-01', $year);
                $to = sprintf('%04d-12-31', $year);
            }
        }

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        return [
            'auksion_sana_from' => $from,
            'auksion_sana_to' => $to,
        ];
    }

    private function distributeUnresolvedFinXisobotRows(
        array &$financialData,
        array $unresolvedIndexes,
        array &$districtData,
        array &$districtCounts,
        array &$districtCategoryCounts,
        array $paymentCategories,
        array $canonicalDistricts
    ): void {
        if (empty($canonicalDistricts)) {
            return;
        }

        $overallReference = [];
        foreach ($canonicalDistricts as $district) {
            $overallReference[$district] = (float)($districtData[$district]['Жами'] ?? 0);
        }

        $overallReferenceSum = array_sum($overallReference);
        if ($overallReferenceSum <= 0) {
            $overallReference = array_fill_keys($canonicalDistricts, 1.0);
            $overallReferenceSum = (float)count($canonicalDistricts);
        }

        $categoryUnresolved = [];
        foreach ($unresolvedIndexes as $index) {
            $category = (string)($financialData[$index]['category'] ?? '');
            $categoryUnresolved[$category][] = $index;
        }

        foreach ($categoryUnresolved as $category => $indexes) {
            usort($indexes, function ($leftIndex, $rightIndex) use ($financialData) {
                $leftAmount = (float)($financialData[$leftIndex]['amount'] ?? 0);
                $rightAmount = (float)($financialData[$rightIndex]['amount'] ?? 0);

                if ($leftAmount === $rightAmount) {
                    return ($financialData[$leftIndex]['id'] ?? 0) <=> ($financialData[$rightIndex]['id'] ?? 0);
                }

                return $rightAmount <=> $leftAmount;
            });

            $reference = [];
            foreach ($canonicalDistricts as $district) {
                $reference[$district] = (float)($districtData[$district][$category] ?? 0);
            }

            $referenceSum = array_sum($reference);
            if ($referenceSum <= 0) {
                $reference = $overallReference;
                $referenceSum = $overallReferenceSum;
            }

            if ($referenceSum <= 0) {
                $reference = array_fill_keys($canonicalDistricts, 1.0);
                $referenceSum = (float)count($canonicalDistricts);
            }

            $unresolvedTotal = 0.0;
            foreach ($indexes as $index) {
                $unresolvedTotal += (float)($financialData[$index]['amount'] ?? 0);
            }

            $remainingTargets = [];
            foreach ($canonicalDistricts as $district) {
                $remainingTargets[$district] = $unresolvedTotal * ($reference[$district] / $referenceSum);
            }

            foreach ($indexes as $index) {
                $selectedDistrict = $this->pickFinXisobotDistrictByRemainingTarget(
                    $remainingTargets,
                    $reference,
                    $canonicalDistricts
                );

                if ($selectedDistrict === null) {
                    continue;
                }

                $amount = (float)($financialData[$index]['amount'] ?? 0);
                $financialData[$index]['district'] = $selectedDistrict;

                $this->appendFinXisobotDistrictTotals(
                    $selectedDistrict,
                    $category,
                    $amount,
                    $districtData,
                    $districtCounts,
                    $districtCategoryCounts,
                    $paymentCategories
                );

                $remainingTargets[$selectedDistrict] -= $amount;
            }
        }
    }

    private function pickFinXisobotDistrictByRemainingTarget(
        array $remainingTargets,
        array $reference,
        array $canonicalDistricts
    ): ?string {
        $selectedDistrict = null;
        $bestRemaining = -INF;
        $bestReference = -INF;

        foreach ($canonicalDistricts as $district) {
            $currentRemaining = (float)($remainingTargets[$district] ?? 0);
            $currentReference = (float)($reference[$district] ?? 0);

            if ($currentRemaining > $bestRemaining) {
                $bestRemaining = $currentRemaining;
                $bestReference = $currentReference;
                $selectedDistrict = $district;
                continue;
            }

            if ($currentRemaining === $bestRemaining) {
                if ($currentReference > $bestReference) {
                    $bestReference = $currentReference;
                    $selectedDistrict = $district;
                    continue;
                }

                if ($currentReference === $bestReference && $selectedDistrict !== null && strcmp($district, $selectedDistrict) < 0) {
                    $selectedDistrict = $district;
                }
            }
        }

        return $selectedDistrict;
    }

    private function buildFinXisobotLotLookup(): array
    {
        $lookup = [
            'lot' => [],
            'doc' => [],
            'contract' => [],
            'amount' => [],
            'amount_date' => [],
        ];

        FaktTolov::query()
            ->with('yerSotuv:lot_raqami,shartnoma_raqam')
            ->select(['lot_raqami', 'tolov_sana', 'hujjat_raqam', 'tolov_summa'])
            ->chunk(1000, function ($payments) use (&$lookup) {
                foreach ($payments as $payment) {
                    $lotRaqami = trim((string)$payment->lot_raqami);
                    if ($lotRaqami === '') {
                        continue;
                    }

                    $lotToken = preg_replace('/\D+/u', '', $lotRaqami) ?? '';
                    if ($lotToken !== '' && strlen($lotToken) >= 6 && strlen($lotToken) <= 8) {
                        $lookup['lot'][$lotToken][$lotRaqami] = true;
                    }

                    $docToken = preg_replace('/\D+/u', '', (string)$payment->hujjat_raqam) ?? '';
                    if ($docToken !== '' && strlen($docToken) >= 6) {
                        $lookup['doc'][$docToken][$lotRaqami] = true;
                    }

                    $contractToken = $this->normalizeFinXisobotText($payment->yerSotuv?->shartnoma_raqam ?? '');
                    if ($contractToken !== '' && mb_strlen($contractToken, 'UTF-8') >= 3) {
                        $lookup['contract'][$contractToken][$lotRaqami] = true;
                    }

                    $amountCents = (int)round(((float)$payment->tolov_summa) * 100);
                    if ($amountCents <= 0) {
                        continue;
                    }

                    $lookup['amount'][$amountCents][$lotRaqami] = true;

                    if ($payment->tolov_sana) {
                        $paymentDate = $payment->tolov_sana->format('Y-m-d');
                        $lookup['amount_date'][$paymentDate . '|' . $amountCents][$lotRaqami] = true;
                    }
                }
            });

        return $lookup;
    }

    private function resolveFinXisobotLotForRecord(DavaktivRasxod $record, array $lotLookup): array
    {
        $detailsText = (string)($record->details ?? '');
        $detailsNormalized = $this->normalizeFinXisobotText($detailsText);

        // 1) Lot tokens in details text
        $lotCandidates = [];
        preg_match_all('/\d{6,8}/u', $detailsText, $lotMatches);
        foreach (array_unique($lotMatches[0] ?? []) as $token) {
            if (isset($lotLookup['lot'][$token])) {
                $this->addFinXisobotLotCandidates($lotCandidates, $lotLookup['lot'][$token]);
            }
        }
        $lotFromToken = $this->resolveUniqueFinXisobotLot($lotCandidates);
        if ($lotFromToken !== null) {
            return ['lot_raqami' => $lotFromToken, 'source' => 'details_lot'];
        }

        // 2) Direct document number -> FaktTolov.hujjat_raqam
        $docNumberToken = preg_replace('/\D+/u', '', (string)($record->doc_number ?? '')) ?? '';
        if ($docNumberToken !== '' && isset($lotLookup['doc'][$docNumberToken])) {
            $lotFromDoc = $this->resolveUniqueFinXisobotLot($lotLookup['doc'][$docNumberToken]);
            if ($lotFromDoc !== null) {
                return ['lot_raqami' => $lotFromDoc, 'source' => 'doc_number'];
            }
        }

        // 3) Numeric tokens in details -> FaktTolov.hujjat_raqam
        $docCandidates = [];
        preg_match_all('/\d{6,12}/u', $detailsText, $docMatches);
        foreach (array_unique($docMatches[0] ?? []) as $token) {
            if (isset($lotLookup['doc'][$token])) {
                $this->addFinXisobotLotCandidates($docCandidates, $lotLookup['doc'][$token]);
            }
        }
        $lotFromDetailsDoc = $this->resolveUniqueFinXisobotLot($docCandidates);
        if ($lotFromDetailsDoc !== null) {
            return ['lot_raqami' => $lotFromDetailsDoc, 'source' => 'details_doc'];
        }

        // 4) Contract number token in details -> YerSotuv.shartnoma_raqam
        if ($detailsNormalized !== '') {
            $contractCandidates = [];
            foreach ($lotLookup['contract'] as $contractToken => $lotSet) {
                if (strpos($detailsNormalized, $contractToken) !== false) {
                    $this->addFinXisobotLotCandidates($contractCandidates, $lotSet);
                }
            }
            $lotFromContract = $this->resolveUniqueFinXisobotLot($contractCandidates);
            if ($lotFromContract !== null) {
                return ['lot_raqami' => $lotFromContract, 'source' => 'contract'];
            }
        }

        // 5) Exact amount (and date when available)
        $amountCents = (int)round(((float)($record->amount ?? 0)) * 100);
        if ($amountCents > 0) {
            if ($record->doc_date) {
                $amountDateKey = $record->doc_date->format('Y-m-d') . '|' . $amountCents;
                if (isset($lotLookup['amount_date'][$amountDateKey])) {
                    $lotFromAmountDate = $this->resolveUniqueFinXisobotLot($lotLookup['amount_date'][$amountDateKey]);
                    if ($lotFromAmountDate !== null) {
                        return ['lot_raqami' => $lotFromAmountDate, 'source' => 'amount_date'];
                    }
                }
            }

            if (isset($lotLookup['amount'][$amountCents])) {
                $lotFromAmount = $this->resolveUniqueFinXisobotLot($lotLookup['amount'][$amountCents]);
                if ($lotFromAmount !== null) {
                    return ['lot_raqami' => $lotFromAmount, 'source' => 'amount'];
                }
            }
        }

        return ['lot_raqami' => null, 'source' => null];
    }

    private function addFinXisobotLotCandidates(array &$target, array $lotSet): void
    {
        foreach (array_keys($lotSet) as $lotRaqami) {
            $target[$lotRaqami] = true;
        }
    }

    private function resolveUniqueFinXisobotLot(array $lotSet): ?string
    {
        if (count($lotSet) !== 1) {
            return null;
        }

        return array_key_first($lotSet);
    }

    private function getFinXisobotPaymentCategories(): array
    {
        return [
            'Чегирма' => 0,
            'Харидорларга қайтарилган маблағлар' => 0,
            'Тошкент ш. қурилиш бошкармасига (1%)' => 0,
            'Давлат кадастрлар палатасига' => 0,
            'Геоахборот шахарсозлик кадастрига' => 0,
            'Солиқ қўмитаси хузуридаги Кадастр агентлигига' => 0,
            'Тошкент шахар махаллий бюджетига' => 0,
            'Жамғармага' => 0,
            'Туманга' => 0,
            'ЯнгиХаёт индустриал технопарки дирекциясига' => 0,
            'Шайҳонтохур туманига' => 0,
            'Тошкент сити дирекциясига' => 0,
        ];
    }

    private function getFinXisobotProportionalCategories(): array
    {
        return [
            'Чегирма',
            'Харидорларга қайтарилган маблағлар',
            'Тошкент ш. қурилиш бошкармасига (1%)',
            'Давлат кадастрлар палатасига',
            'Геоахборот шахарсозлик кадастрига',
            'Солиқ қўмитаси хузуридаги Кадастр агентлигига',
            'Тошкент шахар махаллий бюджетига',
            'Жамғармага',
        ];
    }

    private function getFinXisobotProportionalCategoryLookup(): array
    {
        return array_fill_keys($this->getFinXisobotProportionalCategories(), true);
    }

    private function getFinXisobotDistrictPatterns(): array
    {
        return [
            'бектемир' => 'Бектемир',
            'миробод' => 'Миробод',
            'олмазор' => 'Олмазор',
            'сергели' => 'Сергели',
            'сирғали' => 'Сергели',
            'сиргали' => 'Сергели',
            'учтепа' => 'Учтепа',
            'шайҳонтохур' => 'Шайҳонтохур',
            'шайхонтохур' => 'Шайҳонтохур',
            'шайхонтахур' => 'Шайҳонтохур',
            'шайхонтаур' => 'Шайҳонтохур',
            'юнусобод' => 'Юнусобод',
            'яккасарой' => 'Яккасарой',
            'чилонзор' => 'Чилонзор',
            'мирзоулугбек' => 'Мирзо Улугбек',
            'мирзоулубек' => 'Мирзо Улугбек',
            'мирзоулуғбек' => 'Мирзо Улугбек',
            'яшнобод' => 'Яшнобод',
            'янгихаёт' => 'Янги Хаёт',
            'янгиҳаёт' => 'Янги Хаёт',
            'янгихает' => 'Янги Хаёт',
        ];
    }

    private function getFinXisobotCategoryDistrictFallbacks(): array
    {
        return [
            'ЯнгиХаёт индустриал технопарки дирекциясига' => 'Янги Хаёт',
            'Шайҳонтохур туманига' => 'Шайҳонтохур',
        ];
    }

    private function resolveFinXisobotCategory(string $articleName, array $paymentCategories): string
    {
        if (isset($paymentCategories[$articleName])) {
            return $articleName;
        }

        foreach (array_keys($paymentCategories) as $catKey) {
            if (stripos($articleName, $catKey) !== false || stripos($catKey, $articleName) !== false) {
                return $catKey;
            }
        }

        return 'Тошкент шахар махаллий бюджетига';
    }

    private function resolveFinXisobotDistrict(
        DavaktivRasxod $record,
        string $category,
        array $districtPatterns,
        array $categoryDistrictFallbacks
    ): string {
        if (!empty($record->district)) {
            return $record->district;
        }

        $normalizedDetails = $this->normalizeFinXisobotText($record->details ?? '');
        foreach ($districtPatterns as $pattern => $districtName) {
            if ($normalizedDetails !== '' && strpos($normalizedDetails, $pattern) !== false) {
                return $districtName;
            }
        }

        if (isset($categoryDistrictFallbacks[$category])) {
            return $categoryDistrictFallbacks[$category];
        }

        return 'Номалум';
    }

    private function resolveFinXisobotDistrictRestrictCanonical(string $districtRestrict, array $districtPatterns): ?string
    {
        $normalizedRestrict = $this->normalizeFinXisobotDistrictKey($districtRestrict);
        if ($normalizedRestrict === '') {
            return null;
        }

        foreach ($districtPatterns as $pattern => $districtName) {
            $normalizedPattern = $this->normalizeFinXisobotDistrictKey((string)$pattern);
            if ($normalizedPattern === '') {
                continue;
            }

            if (
                str_contains($normalizedRestrict, $normalizedPattern) ||
                str_contains($normalizedPattern, $normalizedRestrict)
            ) {
                return $districtName;
            }
        }

        $canonicalDistricts = array_values(array_unique(array_values($districtPatterns)));
        foreach ($canonicalDistricts as $canonicalDistrict) {
            if ($this->normalizeFinXisobotDistrictKey((string)$canonicalDistrict) === $normalizedRestrict) {
                return (string)$canonicalDistrict;
            }
        }

        return null;
    }

    private function normalizeFinXisobotDistrictKey(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $lower = mb_strtolower($text, 'UTF-8');
        // Normalize common district suffixes.
        $lower = preg_replace('/\bтумани\b/u', '', $lower) ?? $lower;
        $lower = preg_replace('/\bтуман\b/u', '', $lower) ?? $lower;

        // Normalize common orthographic variants used in user and source data.
        $lower = str_replace(['ҳ', 'ғ', 'қ', 'ў'], ['х', 'г', 'к', 'у'], $lower);

        $clean = preg_replace('/[^\p{L}\p{N}]+/u', '', $lower);

        return trim((string)($clean ?? $lower));
    }

    private function normalizeFinXisobotText(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $lower = mb_strtolower($text, 'UTF-8');
        $clean = preg_replace('/[^\p{L}\p{N}]+/u', '', $lower);

        return $clean ?? $lower;
    }
}
