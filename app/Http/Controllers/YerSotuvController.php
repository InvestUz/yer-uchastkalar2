<?php

namespace App\Http\Controllers;

use App\Models\YerSotuv;
use App\Services\YerSotuvService;
use Illuminate\Http\Request;

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
        }
        elseif (!empty($filters['toliq_tolangan']) && $filters['toliq_tolangan'] === 'true') {
            $query->where('tolov_turi', 'муддатли');
            $query->whereRaw('(
                (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0))
                - (
                    COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0)
                    + COALESCE(auksion_harajati, 0)
                )
            ) <= 0
            AND (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0)) > 0');
        }
        elseif (!empty($filters['nazoratda']) && $filters['nazoratda'] === 'true') {
            $query->where('tolov_turi', 'муддатли');
            $query->whereRaw('(
                (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0))
                - (
                    COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0)
                    + COALESCE(auksion_harajati, 0)
                )
            ) > 0');
        }
        elseif (!empty($filters['grafik_ortda']) && $filters['grafik_ortda'] === 'true') {
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
        }
        elseif (!empty($filters['tolov_turi'])) {
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

        // Calculate statistics for filtered data
        $statistics = [
            'total_lots' => $query->count(),
            'total_area' => $query->sum('maydoni'),
            'total_price' => $query->sum('sotilgan_narx'),
            'boshlangich_narx' => $query->sum('boshlangich_narx'),
            'chegirma' => $query->sum('chegirma'),
            'golib_tolagan' => $query->sum('golib_tolagan'),
        ];

        // Sorting
        $sortField = $request->get('sort', 'auksion_sana');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSortFields = [
            'auksion_sana', 'shartnoma_sana', 'sotilgan_narx', 'boshlangich_narx',
            'maydoni', 'tuman', 'lot_raqami', 'yil', 'manzil', 'golib_nomi',
            'telefon', 'tolov_turi', 'holat', 'asos'
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
