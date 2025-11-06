<?php

namespace App\Http\Controllers;

use App\Models\YerSotuv;
use App\Services\YerSotuvService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YerSotuvController extends Controller
{
    protected $yerSotuvService;

    public function __construct(YerSotuvService $yerSotuvService)
    {
        $this->yerSotuvService = $yerSotuvService;
    }

    /**
     * Display main statistics page (SVOD1)
     */
    public function index(Request $request)
    {
        $dateFilters = [
            'auksion_sana_from' => $request->auksion_sana_from,
            'auksion_sana_to' => $request->auksion_sana_to,
        ];

        $statistics = $this->yerSotuvService->getDetailedStatistics($dateFilters);

        return view('yer-sotuvlar.statistics', compact('statistics', 'dateFilters'));
    }

    /**
     * Display SVOD3 statistics page (Bo'lib to'lash)
     */
    public function svod3(Request $request)
    {
        $dateFilters = [
            'auksion_sana_from' => $request->auksion_sana_from,
            'auksion_sana_to' => $request->auksion_sana_to,
        ];

        $statistics = $this->yerSotuvService->getSvod3Statistics($dateFilters);

        return view('yer-sotuvlar.svod3', compact('statistics', 'dateFilters'));
    }

    /**
     * Display filtered list of land sales
     */
    public function list(Request $request)
    {
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
            'grafik_ortda' => $request->grafik_ortda,
            'toliq_tolangan' => $request->toliq_tolangan,
            'nazoratda' => $request->nazoratda,
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
     * Display monitoring and analytics page
     */
    public function monitoring(Request $request)
    {
        $dateFilters = [
            'auksion_sana_from' => $request->auksion_sana_from,
            'auksion_sana_to' => $request->auksion_sana_to,
        ];

        // Get all tumanlar
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

        // Calculate summary
        $summary = $this->calculateMonitoringSummary($dateFilters);

        // Get tuman statistics
        $tumanStats = [];
        foreach ($tumanlar as $tuman) {
            $tumanPatterns = $this->yerSotuvService->getTumanPatterns($tuman);
            $stats = $this->calculateTumanMonitoring($tumanPatterns, $dateFilters);

            if ($stats['lots'] > 0) {
                $tumanStats[] = [
                    'tuman' => $tuman,
                    'lots' => $stats['lots'],
                    'grafik' => $stats['grafik'],
                    'fakt' => $stats['fakt'],
                    'difference' => $stats['difference'],
                    'percentage' => $stats['percentage']
                ];
            }
        }

        // Prepare chart data
        $chartData = $this->prepareChartData($tumanStats, $dateFilters);

        return view('yer-sotuvlar.monitoring', compact('summary', 'tumanStats', 'chartData', 'dateFilters'));
    }

    /**
     * Calculate monitoring summary
     */
    private function calculateMonitoringSummary(array $dateFilters): array
    {
        $query = YerSotuv::query();

        $query->where('tolov_turi', 'муддатли');
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
            $statistics['fakt_tolangan'] = DB::table('fakt_tolovlar')
                ->whereIn('lot_raqami', $lotRaqamlari)
                ->sum('tolov_summa');
        } else {
            $statistics['fakt_tolangan'] = 0;
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
    private function calculateTumanMonitoring(?array $tumanPatterns, array $dateFilters): array
    {
        $query = YerSotuv::query();

        $this->yerSotuvService->applyTumanFilter($query, $tumanPatterns);
        $query->where('tolov_turi', 'муддатли');
        $this->yerSotuvService->applyDateFilters($query, $dateFilters);

        $lots = $query->count();

        if ($lots === 0) {
            return [
                'lots' => 0,
                'grafik' => 0,
                'fakt' => 0,
                'difference' => 0,
                'percentage' => 0
            ];
        }

        $lotRaqamlari = $query->pluck('lot_raqami')->toArray();

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
    }

    /**
     * Prepare chart data
     */
    private function prepareChartData(array $tumanStats, array $dateFilters): array
    {
        // Payment status distribution
        $toliqTolanganlar = $this->yerSotuvService->getToliqTolanganlar(null, $dateFilters);
        $nazoratdagilar = $this->yerSotuvService->getNazoratdagilar(null, $dateFilters);
        $grafikOrtda = $this->yerSotuvService->getGrafikOrtda(null, $dateFilters);
        $auksonda = $this->yerSotuvService->getAuksondaTurgan(null, $dateFilters);

        // Monthly comparison data
        $monthlyData = $this->getMonthlyComparisonData($dateFilters);

        // Tuman comparison data
        $tumanLabels = array_column($tumanStats, 'tuman');
        $tumanGrafik = array_map(function ($val) {
            return $val / 1000000000;
        }, array_column($tumanStats, 'grafik'));
        $tumanFakt = array_map(function ($val) {
            return $val / 1000000000;
        }, array_column($tumanStats, 'fakt'));

        // Overdue amounts by tuman
        $overdueLabels = [];
        $overdueAmounts = [];
        foreach ($tumanStats as $stat) {
            if ($stat['difference'] > 0) {
                $overdueLabels[] = $stat['tuman'];
                $overdueAmounts[] = round($stat['difference'] / 1000000000, 2);
            }
        }

        return [
            'status' => [
                'completed' => $toliqTolanganlar['soni'],
                'under_control' => $nazoratdagilar['soni'],
                'overdue' => $grafikOrtda['soni'],
                'auction' => $auksonda['soni']
            ],
            'monthly' => $monthlyData,
            'tuman' => [
                'labels' => $tumanLabels,
                'grafik' => $tumanGrafik,
                'fakt' => $tumanFakt
            ],
            'overdue' => [
                'labels' => $overdueLabels,
                'amounts' => $overdueAmounts
            ]
        ];
    }

    /**
     * Get monthly comparison data for charts
     */
    private function getMonthlyComparisonData(array $dateFilters): array
    {
        $query = YerSotuv::query();
        $query->where('tolov_turi', 'муддатли');
        $this->yerSotuvService->applyDateFilters($query, $dateFilters);

        $lotRaqamlari = $query->pluck('lot_raqami')->toArray();

        if (empty($lotRaqamlari)) {
            return [
                'labels' => [],
                'grafik' => [],
                'fakt' => []
            ];
        }

        // Get last 12 months
        $months = [];
        $grafikData = [];
        $faktData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $year = $date->year;
            $month = $date->month;

            $monthLabel = $date->locale('uz')->translatedFormat('M Y');
            $months[] = $monthLabel;

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
        }

        return [
            'labels' => $months,
            'grafik' => $grafikData,
            'fakt' => $faktData
        ];
    }

    /**
     * Show filtered data with pagination
     */
    private function showFilteredData(Request $request, array $filters)
    {
        $query = YerSotuv::query();

        // Search filter
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
            $tumanPatterns = $this->yerSotuvService->getTumanPatterns($filters['tuman']);
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        // Year filter
        if (!empty($filters['yil'])) {
            $query->where('yil', $filters['yil']);
        }

        // Date filters
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

        // Price range filter
        if (!empty($filters['narx_from'])) {
            $query->where('sotilgan_narx', '>=', $filters['narx_from']);
        }
        if (!empty($filters['narx_to'])) {
            $query->where('sotilgan_narx', '<=', $filters['narx_to']);
        }

        // Area range filter
        if (!empty($filters['maydoni_from'])) {
            $query->where('maydoni', '>=', $filters['maydoni_from']);
        }
        if (!empty($filters['maydoni_to'])) {
            $query->where('maydoni', '<=', $filters['maydoni_to']);
        }

        // Special status filters
        if (!empty($filters['auksonda_turgan']) && $filters['auksonda_turgan'] === 'true') {
            $query->where(function ($q) {
                $q->where('tolov_turi', '!=', 'муддатли')
                    ->where('tolov_turi', '!=', 'муддатли эмас')
                    ->orWhereNull('tolov_turi');
            });
        } elseif (!empty($filters['toliq_tolangan']) && $filters['toliq_tolangan'] === 'true') {
            $query->where('tolov_turi', 'муддатли');
            $query->whereRaw('(
                (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0))
                - (
                    COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0)
                    + COALESCE(auksion_harajati, 0)
                )
            ) <= 0
            AND (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0)) > 0');
        } elseif (!empty($filters['nazoratda']) && $filters['nazoratda'] === 'true') {
            $query->where('tolov_turi', 'муддатли');
            $query->whereRaw('(
                (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0))
                - (
                    COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0)
                    + COALESCE(auksion_harajati, 0)
                )
            ) > 0');
        } elseif (!empty($filters['grafik_ortda']) && $filters['grafik_ortda'] === 'true') {
            $bugun = $this->yerSotuvService->getGrafikCutoffDate();

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
        } elseif (!empty($filters['tolov_turi'])) {
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

   $lotRaqamlari = (clone $query)->pluck('lot_raqami');


        $faktTolangan = DB::table('fakt_tolovlar')
    ->whereIn('lot_raqami', $lotRaqamlari)
    ->sum('tolov_summa');

$statistics = [
    'total_lots' => $query->count(),
    'total_area' => $query->sum('maydoni'),
    'total_price' => $query->sum('sotilgan_narx'),
    'boshlangich_narx' => $query->sum('boshlangich_narx'),
    'chegirma' => $query->sum('chegirma'),
    'golib_tolagan' => $query->sum('golib_tolagan'),
    'shartnoma_summasi' => $query->sum('shartnoma_summasi'),
    'auksion_harajati' => $query->sum('auksion_harajati'),
    'fakt_tolangan' => $faktTolangan,
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

        // Paginate results
        $yerlar = $query->paginate(50)->withQueryString();

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
}
