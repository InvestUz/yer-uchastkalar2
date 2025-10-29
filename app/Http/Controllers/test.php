<?php

namespace App\Http\Controllers;

use App\Models\YerSotuv;
use App\Models\GrafikTolov;
use App\Models\FaktTolov;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class YerSotuvController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['tuman', 'yil', 'tolov_turi', 'holat', 'asos', 'auksonda_turgan']);

        // Debug mode
        if ($request->has('debug')) {
            return $this->debugMulkQabul();
        }

        // Agar filter bo'lsa, filtered data ko'rsatish
        if (!empty(array_filter($filters))) {
            return $this->showFilteredData($request, $filters);
        }

        // Aks holda statistics table ko'rsatish
        $statistics = $this->getDetailedStatistics();

        return view('yer-sotuvlar.statistics', compact('statistics'));
    }




    // SVOD 3 - Bo'lib to'lash jadvali
    public function svod3(Request $request)
    {
        $statistics = $this->getSvod3Statistics();

        return view('yer-sotuvlar.svod3', compact('statistics'));
    }

   private function getSvod3Statistics()
    {
        $tumanlar = [
            'Бектемир т.',
            'Мирзо Улуғбек т.',
            'Миробод т.',
            'Олмазор т.',
            'Сирғали т.',
            'Учтепа т.',
            'Чилонзор т.',
            'Шайхонтоҳур т.',
            'Юнусобод т.',
            'Яккасарой т.',
            'Янги ҳаёт т.',
            'Яшнобод т.'
        ];

        $result = [
            'jami' => $this->initializeSvod3Total(),
            'tumanlar' => []
        ];

        foreach ($tumanlar as $tuman) {
            $tumanPatterns = $this->getTumanPatterns($tuman);

            $stat = [
                'tuman' => $tuman,
                'narhini_bolib' => $this->getNarhiniBolib($tumanPatterns),
                'toliq_tolanganlar' => $this->getToliqTolanganlar($tumanPatterns),
                'nazoratdagilar' => $this->getNazoratdagilar($tumanPatterns),
                'grafik_ortda' => $this->getGrafikOrtda($tumanPatterns)
            ];

            $result['tumanlar'][] = $stat;
            $this->addToSvod3Total($result['jami'], $stat);
        }

        return $result;
    }

/**
 * NARHINI BO'LIB - All lots sold with installment payment
 * Formula: T = golib_tolagan + shartnoma_summasi
 */
private function getNarhiniBolib($tumanPatterns = null)
{
    $query = YerSotuv::query();

    if ($tumanPatterns !== null && !empty($tumanPatterns)) {
        $query->where(function ($q) use ($tumanPatterns) {
            foreach ($tumanPatterns as $pattern) {
                $q->orWhere('tuman', 'like', '%' . $pattern . '%');
            }
        });
    }

    // Bo'lib to'lash (muddatli)
    $query->where('tolov_turi', 'муддатли');

    $data = $query->selectRaw('
        COUNT(*) as soni,
        SUM(maydoni) as maydoni,
        SUM(boshlangich_narx) as boshlangich_narx,
        SUM(sotilgan_narx) as sotilgan_narx,
        SUM(COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0)) as tushadigan_mablagh
    ')->first();

    return [
        'soni' => $data->soni ?? 0,
        'maydoni' => $data->maydoni ?? 0,
        'boshlangich_narx' => $data->boshlangich_narx ?? 0,
        'sotilgan_narx' => $data->sotilgan_narx ?? 0,
        'tushadigan_mablagh' => $data->tushadigan_mablagh ?? 0
    ];
}


/**
 * TO'LIQ TO'LANGANLAR - Fully paid lots
 * Formula:
 * T = golib_tolagan + shartnoma_summasi
 * B = T - (fakt_tolovlar + auksion_harajati)
 * Fully paid when: B ≤ 0
 */








    // Qolgan metodlar (showFilteredData, getDetailedStatistics, va h.k.)

    public function list(Request $request)
    {
        $filters = [
            'tuman' => $request->tuman,
            'yil' => $request->yil,
            'tolov_turi' => $request->tolov_turi,
            'holat' => $request->holat,
            'asos' => $request->asos,
        ];

        return $this->showFilteredData($request, $filters);
    }




















}
