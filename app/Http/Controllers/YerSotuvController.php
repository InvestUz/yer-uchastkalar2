<?php

namespace App\Http\Controllers;

use App\Models\GrafikTolov;
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

        // Calculate summary for both payment types
        $summaryMuddatli = $this->calculateMonitoringSummary($dateFilters, 'муддатли');
        $summaryMuddatliEmas = $this->calculateMonitoringSummary($dateFilters, 'муддатли эмас');

        // Get tuman statistics for муддатли
        $tumanStatsMuddatli = [];
        foreach ($tumanlar as $tuman) {
            $tumanPatterns = $this->yerSotuvService->getTumanPatterns($tuman);
            $stats = $this->calculateTumanMonitoring($tumanPatterns, $dateFilters, 'муддатли');

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

        // Get tuman statistics for муддатли эмас
        $tumanStatsMuddatliEmas = [];
        foreach ($tumanlar as $tuman) {
            $tumanPatterns = $this->yerSotuvService->getTumanPatterns($tuman);
            $stats = $this->calculateTumanMonitoring($tumanPatterns, $dateFilters, 'муддатли эмас');

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

        // Prepare chart data
        $chartData = $this->prepareChartData($tumanStatsMuddatli, $tumanStatsMuddatliEmas, $dateFilters);

        return view('yer-sotuvlar.monitoring', compact(
            'summaryMuddatli',
            'summaryMuddatliEmas',
            'tumanStatsMuddatli',
            'tumanStatsMuddatliEmas',
            'chartData',
            'dateFilters'
        ));
    }


    /**
     * Calculate monitoring summary
     */
    private function calculateMonitoringSummary(array $dateFilters, string $tolovTuri): array
    {
        $query = YerSotuv::query();

        $query->where('tolov_turi', $tolovTuri);
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

        // Calculate statistics using service
        $statistics = $this->yerSotuvService->getListStatistics(clone $query);

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
        1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
        5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
        9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
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
    // Get last month's last day
    $lastMonth = now()->subMonth()->endOfMonth();

    $filters = [
        'year' => $lastMonth->year,
        'month' => $lastMonth->month,
    ];

    $comparativeData = $this->yerSotuvService->getMonthlyComparativeData($filters);

    // Get available years
    $availableYears = DB::table('grafik_tolovlar')
        ->select('yil')
        ->distinct()
        ->orderBy('yil', 'desc')
        ->pluck('yil');

    // Only show the last completed month (single option)
    $monthNames = [
        1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
        5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
        9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
    ];

    $months = [
        $lastMonth->month => $monthNames[$lastMonth->month]
    ];

    return view('yer-sotuvlar.monitoring_mirzayev', compact(
        'comparativeData',
        'availableYears',
        'months',
        'filters'
    ));
}

}
