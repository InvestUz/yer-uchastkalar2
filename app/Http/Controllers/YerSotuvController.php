<?php

namespace App\Http\Controllers;

use App\Models\YerSotuv;
use App\Models\GrafikTolov;
use App\Models\FaktTolov;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YerSotuvController extends Controller
{
    public function index(Request $request)
    {
        $query = YerSotuv::with(['grafikTolovlar', 'faktTolovlar']);

        $filters = $request->only(['search', 'tuman', 'yil', 'tolov_turi']);
        $query->filter($filters);

        $sortField = $request->get('sort', 'auksion_sana');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['auksion_sana', 'sotilgan_narx', 'tuman'])) {
            $query->orderBy($sortField, $sortDirection);
        }

        $yerlar = $query->paginate(30)->withQueryString();

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

        $statistics = $this->getStatistics($filters);

        return view('yer-sotuvlar.index', compact(
            'yerlar', 'tumanlar', 'yillar', 'statistics'
        ));
    }

    public function show($lotRaqami)
    {
        $yer = YerSotuv::where('lot_raqami', $lotRaqami)
            ->with(['grafikTolovlar', 'faktTolovlar'])
            ->firstOrFail();

        $grafikByMonth = $yer->grafikTolovlar()
            ->selectRaw('yil, oy, oy_nomi, SUM(grafik_summa) as summa')
            ->groupBy('yil', 'oy', 'oy_nomi')
            ->orderBy('yil')
            ->orderBy('oy')
            ->get();

        $faktByMonth = $yer->faktTolovlar()
            ->selectRaw('YEAR(tolov_sana) as yil, MONTH(tolov_sana) as oy, SUM(tolov_summa) as summa')
            ->groupBy(DB::raw('YEAR(tolov_sana)'), DB::raw('MONTH(tolov_sana)'))
            ->orderBy(DB::raw('YEAR(tolov_sana)'))
            ->orderBy(DB::raw('MONTH(tolov_sana)'))
            ->get();

        $tolovTaqqoslash = $this->comparePayments($grafikByMonth, $faktByMonth);

        return view('yer-sotuvlar.show', compact('yer', 'tolovTaqqoslash'));
    }

    private function comparePayments($grafik, $fakt)
    {
        $result = [];

        foreach ($grafik as $g) {
            $faktSumma = $fakt->where('yil', $g->yil)
                              ->where('oy', $g->oy)
                              ->sum('summa');

            $result[] = [
                'yil' => $g->yil,
                'oy' => $g->oy,
                'oy_nomi' => $g->oy_nomi,
                'grafik' => $g->summa,
                'fakt' => $faktSumma,
                'farq' => $g->summa - $faktSumma,
                'foiz' => $g->summa > 0 ? round(($faktSumma / $g->summa) * 100, 1) : 0
            ];
        }

        return $result;
    }

    private function getStatistics($filters = [])
    {
        // Birinchi query - statistika uchun
        $statsQuery = YerSotuv::query();

        if (!empty($filters['tuman'])) {
            $statsQuery->where('tuman', $filters['tuman']);
        }
        if (!empty($filters['yil'])) {
            $statsQuery->where('yil', $filters['yil']);
        }

        $stats = $statsQuery->selectRaw('
            COUNT(*) as jami_soni,
            SUM(maydoni) as jami_maydoni,
            SUM(sotilgan_narx) as jami_sotilgan,
            SUM(shartnoma_summasi) as jami_shartnoma,
            AVG(sotilgan_narx) as ortacha_narx
        ')->first();

        // Ikkinchi query - lot raqamlar uchun (YANGI QUERY!)
        $lotQuery = YerSotuv::query();

        if (!empty($filters['tuman'])) {
            $lotQuery->where('tuman', $filters['tuman']);
        }
        if (!empty($filters['yil'])) {
            $lotQuery->where('yil', $filters['yil']);
        }

        $lotRaqamlar = $lotQuery->pluck('lot_raqami')->toArray();

        $grafikJami = 0;
        $faktJami = 0;

        if (!empty($lotRaqamlar)) {
            $grafikJami = GrafikTolov::whereIn('lot_raqami', $lotRaqamlar)
                ->sum('grafik_summa');

            $faktJami = FaktTolov::whereIn('lot_raqami', $lotRaqamlar)
                ->sum('tolov_summa');
        }

        $tumanStatistics = [];
        if (empty($filters['tuman'])) {
            $tumanQuery = YerSotuv::query();
            if (!empty($filters['yil'])) {
                $tumanQuery->where('yil', $filters['yil']);
            }

            $tumanStats = $tumanQuery
                ->select('tuman')
                ->selectRaw('COUNT(*) as soni')
                ->selectRaw('SUM(maydoni) as maydoni')
                ->selectRaw('SUM(sotilgan_narx) as summa')
                ->groupBy('tuman')
                ->orderBy('summa', 'desc')
                ->get();

            foreach ($tumanStats as $tuman) {
                // Har bir tuman uchun lot raqamlarini olish
                $tumanLotQuery = YerSotuv::where('tuman', $tuman->tuman);

                if (!empty($filters['yil'])) {
                    $tumanLotQuery->where('yil', $filters['yil']);
                }

                $tumanLotlar = $tumanLotQuery->pluck('lot_raqami')->toArray();

                $grafikSum = 0;
                $faktSum = 0;

                if (!empty($tumanLotlar)) {
                    $grafikSum = GrafikTolov::whereIn('lot_raqami', $tumanLotlar)
                        ->sum('grafik_summa');

                    $faktSum = FaktTolov::whereIn('lot_raqami', $tumanLotlar)
                        ->sum('tolov_summa');
                }

                $tuman->grafik = $grafikSum;
                $tuman->fakt = $faktSum;
                $tuman->qarzdorlik = $grafikSum - $faktSum;
                $tuman->foiz = $grafikSum > 0 ? round(($faktSum / $grafikSum) * 100, 1) : 0;
            }

            $tumanStatistics = $tumanStats;
        }

        return [
            'umumiy' => (object)[
                'jami_soni' => $stats->jami_soni ?? 0,
                'jami_maydoni' => $stats->jami_maydoni ?? 0,
                'jami_sotilgan' => $stats->jami_sotilgan ?? 0,
                'jami_shartnoma' => $stats->jami_shartnoma ?? 0,
                'ortacha_narx' => $stats->ortacha_narx ?? 0,
                'grafik_jami' => $grafikJami,
                'fakt_jami' => $faktJami,
                'qarzdorlik' => $grafikJami - $faktJami,
                'tolov_foizi' => $grafikJami > 0 ? round(($faktJami / $grafikJami) * 100, 1) : 0
            ],
            'tumanlar' => $tumanStatistics
        ];
    }
}
