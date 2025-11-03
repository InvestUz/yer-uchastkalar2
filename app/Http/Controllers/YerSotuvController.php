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
    /**
     * Helper method to get consistent cutoff date for grafik calculations
     * Uses LAST day of PREVIOUS month to include only COMPLETED months
     */
    private function getGrafikCutoffDate()
    {
        return now()->subMonth()->endOfMonth()->format('Y-m-d');
    }

    public function index(Request $request)
    {
        if ($request->has('debug')) {
            return $this->debugMulkQabul();
        }

        $dateFilters = [
            'auksion_sana_from' => $request->auksion_sana_from,
            'auksion_sana_to' => $request->auksion_sana_to,
        ];

        $statistics = $this->getDetailedStatistics($dateFilters);

        return view('yer-sotuvlar.statistics', compact('statistics', 'dateFilters'));
    }

    public function svod3(Request $request)
    {
        $dateFilters = [
            'auksion_sana_from' => $request->auksion_sana_from,
            'auksion_sana_to' => $request->auksion_sana_to,
        ];

        $statistics = $this->getSvod3Statistics($dateFilters);

        return view('yer-sotuvlar.svod3', compact('statistics', 'dateFilters'));
    }

    private function getSvod3Statistics($dateFilters = [])
    {
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

        $result = [
            'jami' => $this->initializeSvod3Total(),
            'tumanlar' => []
        ];

        foreach ($tumanlar as $tuman) {
            $tumanPatterns = $this->getTumanPatterns($tuman);

            $stat = [
                'tuman' => $tuman,
                'narhini_bolib' => $this->getNarhiniBolib($tumanPatterns, $dateFilters),
                'toliq_tolanganlar' => $this->getToliqTolanganlar($tumanPatterns, $dateFilters),
                'nazoratdagilar' => $this->getNazoratdagilar($tumanPatterns, $dateFilters),
                'grafik_ortda' => $this->getGrafikOrtda($tumanPatterns, $dateFilters)
            ];

            $result['tumanlar'][] = $stat;
            $this->addToSvod3Total($result['jami'], $stat);
        }

        return $result;
    }

    private function getNarhiniBolib($tumanPatterns = null, $dateFilters = [])
    {
        $query = YerSotuv::query();

        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        $query->where('tolov_turi', 'муддатли');

        if (!empty($dateFilters['auksion_sana_from'])) {
            $query->whereDate('auksion_sana', '>=', $dateFilters['auksion_sana_from']);
        }
        if (!empty($dateFilters['auksion_sana_to'])) {
            $query->whereDate('auksion_sana', '<=', $dateFilters['auksion_sana_to']);
        }

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

    private function getToliqTolanganlar($tumanPatterns = null, $dateFilters = [])
    {
        $query = YerSotuv::query();

        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        $query->where('tolov_turi', 'муддатли');

        if (!empty($dateFilters['auksion_sana_from'])) {
            $query->whereDate('auksion_sana', '>=', $dateFilters['auksion_sana_from']);
        }
        if (!empty($dateFilters['auksion_sana_to'])) {
            $query->whereDate('auksion_sana', '<=', $dateFilters['auksion_sana_to']);
        }

        $query->whereRaw('lot_raqami IN (
            SELECT ys.lot_raqami
            FROM yer_sotuvlar ys
            LEFT JOIN (
                SELECT lot_raqami, SUM(tolov_summa) as jami_fakt
                FROM fakt_tolovlar
                GROUP BY lot_raqami
            ) f ON f.lot_raqami = ys.lot_raqami
            WHERE ys.tolov_turi = "муддатли"
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
            SUM(COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0)) as T_total
        ')->first();

        $tushganSumma = $data->T_total ?? 0;
        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'boshlangich_narx' => $data->boshlangich_narx ?? 0,
            'sotilgan_narx' => $data->sotilgan_narx ?? 0,
            'tushadigan_mablagh' => $data->T_total ?? 0,
            'tushgan_summa' => $tushganSumma
        ];
    }

    private function getNazoratdagilar($tumanPatterns = null, $dateFilters = [])
    {
        $query = YerSotuv::query();

        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        $query->where('tolov_turi', 'муддатли');

        if (!empty($dateFilters['auksion_sana_from'])) {
            $query->whereDate('auksion_sana', '>=', $dateFilters['auksion_sana_from']);
        }
        if (!empty($dateFilters['auksion_sana_to'])) {
            $query->whereDate('auksion_sana', '<=', $dateFilters['auksion_sana_to']);
        }

        $query->whereRaw('lot_raqami IN (
            SELECT ys.lot_raqami
            FROM yer_sotuvlar ys
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
        )');

        $data = $query->selectRaw('
            COUNT(*) as soni,
            SUM(maydoni) as maydoni,
            SUM(boshlangich_narx) as boshlangich_narx,
            SUM(sotilgan_narx) as sotilgan_narx,
            SUM(COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0)) as T_total
        ')->first();

        $lotlar = YerSotuv::query();
        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $lotlar->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }
        $lotlar->where('tolov_turi', 'муддатли');
        $lotlar->whereRaw('lot_raqami IN (
            SELECT ys.lot_raqami
            FROM yer_sotuvlar ys
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
        )');

        $lotRaqamlari = $lotlar->pluck('lot_raqami')->toArray();

        $tushganData = DB::table('yer_sotuvlar as ys')
            ->leftJoin('fakt_tolovlar as f', 'f.lot_raqami', '=', 'ys.lot_raqami')
            ->whereIn('ys.lot_raqami', $lotRaqamlari)
            ->selectRaw('
                SUM(COALESCE(f.tolov_summa, 0)) as jami_fakt,
                SUM(COALESCE(ys.auksion_harajati, 0)) as jami_auksion
            ')
            ->first();

        $tushganSumma = ($tushganData->jami_fakt ?? 0) + ($tushganData->jami_auksion ?? 0);

        $tolovData = DB::table('yer_sotuvlar as ys')
            ->leftJoin('grafik_tolovlar as g', 'g.lot_raqami', '=', 'ys.lot_raqami')
            ->leftJoin('fakt_tolovlar as f', 'f.lot_raqami', '=', 'ys.lot_raqami')
            ->whereIn('ys.lot_raqami', $lotRaqamlari)
            ->selectRaw('
                SUM(COALESCE(g.grafik_summa, 0)) as jami_grafik,
                SUM(COALESCE(f.tolov_summa, 0)) as jami_fakt
            ')
            ->first();

        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'boshlangich_narx' => $data->boshlangich_narx ?? 0,
            'sotilgan_narx' => $data->sotilgan_narx ?? 0,
            'tushadigan_mablagh' => $data->T_total ?? 0,
            'grafik_summa' => $tolovData->jami_grafik ?? 0,
            'fakt_summa' => $tolovData->jami_fakt ?? 0,
            'tushgan_summa' => $tushganSumma
        ];
    }

    private function getGrafikOrtda($tumanPatterns = null, $dateFilters = [])
    {
        $bugun = $this->getGrafikCutoffDate();

        $query = YerSotuv::query();

        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        $query->where('tolov_turi', 'муддатли');

        if (!empty($dateFilters['auksion_sana_from'])) {
            $query->whereDate('auksion_sana', '>=', $dateFilters['auksion_sana_from']);
        }
        if (!empty($dateFilters['auksion_sana_to'])) {
            $query->whereDate('auksion_sana', '<=', $dateFilters['auksion_sana_to']);
        }

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

        $data = $query->selectRaw('
            COUNT(*) as soni,
            SUM(maydoni) as maydoni
        ')->first();

        $lotlarQuery = YerSotuv::query();

        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $lotlarQuery->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        $lotlarQuery->where('tolov_turi', 'муддатли');
        $lotlarQuery->whereRaw('lot_raqami IN (
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

        $lotRaqamlari = $lotlarQuery->pluck('lot_raqami')->toArray();

        if (empty($lotRaqamlari)) {
            return [
                'soni' => 0,
                'maydoni' => 0,
                'grafik_summa' => 0,
                'fakt_summa' => 0,
                'farq_summa' => 0,
                'foiz' => 0
            ];
        }

        $grafikSumma = DB::table('grafik_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->whereRaw('CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") <= ?', [$bugun])
            ->sum('grafik_summa');

        $faktSumma = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $lotRaqamlari)
            ->sum('tolov_summa');

        $foiz = $grafikSumma > 0 ? round(($faktSumma / $grafikSumma) * 100, 1) : 0;

        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'grafik_summa' => $grafikSumma,
            'fakt_summa' => $faktSumma,
            'farq_summa' => $grafikSumma - $faktSumma,
            'foiz' => $foiz
        ];
    }

    private function initializeSvod3Total()
    {
        return [
            'narhini_bolib' => ['soni' => 0, 'maydoni' => 0, 'boshlangich_narx' => 0, 'sotilgan_narx' => 0, 'tushadigan_mablagh' => 0],
            'toliq_tolanganlar' => ['soni' => 0, 'maydoni' => 0, 'boshlangich_narx' => 0, 'sotilgan_narx' => 0, 'tushadigan_mablagh' => 0, 'tushgan_summa' => 0],
            'nazoratdagilar' => ['soni' => 0, 'maydoni' => 0, 'boshlangich_narx' => 0, 'sotilgan_narx' => 0, 'tushadigan_mablagh' => 0, 'grafik_summa' => 0, 'fakt_summa' => 0, 'tushgan_summa' => 0],
            'grafik_ortda' => ['soni' => 0, 'maydoni' => 0, 'grafik_summa' => 0, 'fakt_summa' => 0, 'foiz' => 0]
        ];
    }

    private function addToSvod3Total(&$jami, $stat)
    {
        foreach (['soni', 'maydoni', 'boshlangich_narx', 'sotilgan_narx', 'tushadigan_mablagh'] as $field) {
            $jami['narhini_bolib'][$field] += $stat['narhini_bolib'][$field];
        }

        foreach (['soni', 'maydoni', 'boshlangich_narx', 'sotilgan_narx', 'tushadigan_mablagh', 'tushgan_summa'] as $field) {
            $jami['toliq_tolanganlar'][$field] += $stat['toliq_tolanganlar'][$field];
        }

        foreach (['soni', 'maydoni', 'boshlangich_narx', 'sotilgan_narx', 'tushadigan_mablagh', 'grafik_summa', 'fakt_summa', 'tushgan_summa'] as $field) {
            $jami['nazoratdagilar'][$field] += $stat['nazoratdagilar'][$field];
        }

        foreach (['soni', 'maydoni', 'grafik_summa', 'fakt_summa'] as $field) {
            $jami['grafik_ortda'][$field] += $stat['grafik_ortda'][$field];
        }

        if ($jami['grafik_ortda']['grafik_summa'] > 0) {
            $jami['grafik_ortda']['foiz'] = round(($jami['grafik_ortda']['fakt_summa'] / $jami['grafik_ortda']['grafik_summa']) * 100, 1);
        }
    }

private function calculateAdditionalColumns($tumanPatterns = null, $dateFilters = [])
{
    // Get base data
    $jami = $this->getTumanData($tumanPatterns, null, $dateFilters);
    $birYola = $this->getTumanData($tumanPatterns, 'муддатли эмас', $dateFilters);
    $bolib = $this->getTumanData($tumanPatterns, 'муддатли', $dateFilters);

    // Get lot numbers for bo'lib to'lash
    $bolibLots = $this->getBolibLotlar($tumanPatterns, $dateFilters);

    // Calculate fakt tolovlar (actual payments received) for bo'lib
    $faktTolovlarBolib = 0;
    if (!empty($bolibLots)) {
        $faktTolovlarBolib = DB::table('fakt_tolovlar')
            ->whereIn('lot_raqami', $bolibLots)
            ->sum('tolov_summa');
    }

    // Get auksion harajati for bo'lib
    $auksionHarajatiBolib = 0;
    if (!empty($bolibLots)) {
        $auksionHarajatiBolib = YerSotuv::whereIn('lot_raqami', $bolibLots)->sum('auksion_harajati');
    }

    // Get golib_tolagan and auksion_harajati for bir yo'la
    $birYolaQuery = YerSotuv::query()
        ->when($tumanPatterns, function($q) use ($tumanPatterns) {
            $q->where(function ($query) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $query->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        })
        ->where('tolov_turi', 'муддатли эмас')
        ->when(!empty($dateFilters['auksion_sana_from']), function($q) use ($dateFilters) {
            $q->whereDate('auksion_sana', '>=', $dateFilters['auksion_sana_from']);
        })
        ->when(!empty($dateFilters['auksion_sana_to']), function($q) use ($dateFilters) {
            $q->whereDate('auksion_sana', '<=', $dateFilters['auksion_sana_to']);
        });

    $golibTolaganBirYola = $birYolaQuery->sum('golib_tolagan');
    $auksionHarajatiBirYola = $birYolaQuery->sum('auksion_harajati');

    // Calculate Mulk Qabul Qilmagan for муддатли эмас only
    $mulkQabulBirYolaQuery = YerSotuv::query()
        ->when($tumanPatterns, function($q) use ($tumanPatterns) {
            $q->where(function ($query) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $query->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        })
        ->where('tolov_turi', 'муддатли эмас')
        ->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%')
        ->where('asos', 'ПФ-135')
        ->when(!empty($dateFilters['auksion_sana_from']), function($q) use ($dateFilters) {
            $q->whereDate('auksion_sana', '>=', $dateFilters['auksion_sana_from']);
        })
        ->when(!empty($dateFilters['auksion_sana_to']), function($q) use ($dateFilters) {
            $q->whereDate('auksion_sana', '<=', $dateFilters['auksion_sana_to']);
        });

    $mulkQabulData = $mulkQabulBirYolaQuery->selectRaw('
        SUM(COALESCE(golib_tolagan, 0) - COALESCE(auksion_harajati, 0)) as mulk_qabul_mablagh
    ')->first();

    $mulkQabulMablaghBirYola = $mulkQabulData->mulk_qabul_mablagh ?? 0;

    // Get golib_tolagan for bo'lib
    $golibTolaganBolib = YerSotuv::query()
        ->when($tumanPatterns, function($q) use ($tumanPatterns) {
            $q->where(function ($query) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $query->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        })
        ->where('tolov_turi', 'муддатли')
        ->when(!empty($dateFilters['auksion_sana_from']), function($q) use ($dateFilters) {
            $q->whereDate('auksion_sana', '>=', $dateFilters['auksion_sana_from']);
        })
        ->when(!empty($dateFilters['auksion_sana_to']), function($q) use ($dateFilters) {
            $q->whereDate('auksion_sana', '<=', $dateFilters['auksion_sana_to']);
        })
        ->sum('golib_tolagan');

    // Get shartnoma summasi for bo'lib
    $shartnomaByYigindi = YerSotuv::query()
        ->when($tumanPatterns, function($q) use ($tumanPatterns) {
            $q->where(function ($query) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $query->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        })
        ->where('tolov_turi', 'муддатли')
        ->when(!empty($dateFilters['auksion_sana_from']), function($q) use ($dateFilters) {
            $q->whereDate('auksion_sana', '>=', $dateFilters['auksion_sana_from']);
        })
        ->when(!empty($dateFilters['auksion_sana_to']), function($q) use ($dateFilters) {
            $q->whereDate('auksion_sana', '<=', $dateFilters['auksion_sana_to']);
        })
        ->sum('shartnoma_summasi');

    // Column: Bir yo'la - golib tolagan minus auksion harajati
    $column_biryola_tushgan_minus_fee = $golibTolaganBirYola - $auksionHarajatiBirYola;

    // Calculate biryola_fakt: tushadigan_mablagh (муддатли эмас) - Mulk Qabul Qilmagan
    $biryola_fakt = $birYola['tushadigan_mablagh'] - $mulkQabulMablaghBirYola;

    // Column: Bo'lib - golib tolagan minus auksion harajati
    $column_bolib_golib_minus_fee = $golibTolaganBolib - $auksionHarajatiBolib;

    // Column: Bo'lib tushadigan (golib + shartnoma - auksion harajati)
    $column_bolib_tushadigan = $golibTolaganBolib + $shartnomaByYigindi - $auksionHarajatiBolib;

    // Column: Bo'lib tushgan (fakt tolovlar)
    $column_bolib_tushgan = $faktTolovlarBolib;

    // Column: Jami tushgan (bir yo'la + bo'lib)
    $column_jami_tushgan_yigindi = $biryola_fakt + $column_bolib_tushgan;

    // Get auksonda data
    $auksonda = $this->getAuksondaTurgan($tumanPatterns, $dateFilters);

    // Column: Jami tushadigan + auksion
    $column_jami_tushadigan_plus_auksion = $jami['tushadigan_mablagh'] + $auksonda['sotilgan_narx'];

    return [
        'jami_tushadigan_plus_auksion' => $column_jami_tushadigan_plus_auksion,
        'jami_tushgan_yigindi' => $column_jami_tushgan_yigindi,
        'biryola_tushgan_minus_fee' => $column_biryola_tushgan_minus_fee,
        'biryola_fakt' => $biryola_fakt,
        'bolib_golib_minus_fee' => $column_bolib_golib_minus_fee,
        'bolib_tushadigan' => $column_bolib_tushadigan,
        'bolib_tushgan' => $column_bolib_tushgan,
    ];
}

    private function getBolibLotlar($tumanPatterns = null, $dateFilters = [])
    {
        $query = YerSotuv::query();

        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        $query->where('tolov_turi', 'муддатли');

        if (!empty($dateFilters['auksion_sana_from'])) {
            $query->whereDate('auksion_sana', '>=', $dateFilters['auksion_sana_from']);
        }
        if (!empty($dateFilters['auksion_sana_to'])) {
            $query->whereDate('auksion_sana', '<=', $dateFilters['auksion_sana_to']);
        }

        return $query->pluck('lot_raqami')->toArray();
    }

    private function getDetailedStatistics($dateFilters = [])
    {
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

        $statistics = [];

        foreach ($tumanlar as $tuman) {
            $stat = $this->calculateTumanStatistics($tuman, $dateFilters);

            // ✅ ADD: Calculate additional columns
            $additionalCols = $this->calculateAdditionalColumns(
                $stat['tuman_patterns'],
                $dateFilters
            );

            // Merge additional columns into statistics
            $stat = array_merge($stat, $additionalCols);

            $statistics[] = $stat;
        }

        $jami = [
            'jami' => $this->getTumanData(null, null, $dateFilters),
            'bir_yola' => $this->getTumanData(null, 'муддатли эмас', $dateFilters),
            'bolib' => $this->getTumanData(null, 'муддатли', $dateFilters),
            'auksonda' => $this->getAuksondaTurgan(null, $dateFilters),
            'mulk_qabul' => $this->getMulkQabulQilmagan(null, $dateFilters)
        ];

        // ✅ ADD: Calculate additional columns for JAMI
        $additionalColsJami = $this->calculateAdditionalColumns(null, $dateFilters);
        $jami = array_merge($jami, $additionalColsJami);

        return [
            'tumanlar' => $statistics,
            'jami' => $jami
        ];
    }

    private function calculateTumanStatistics($tumanName, $dateFilters = [])
    {
        $tumanPatterns = $this->getTumanPatterns($tumanName);

        $jami = $this->getTumanData($tumanPatterns, null, $dateFilters);
        $birYola = $this->getTumanData($tumanPatterns, 'муддатли эмас', $dateFilters);
        $bolib = $this->getTumanData($tumanPatterns, 'муддатли', $dateFilters);
        $auksonda = $this->getAuksondaTurgan($tumanPatterns, $dateFilters);
        $mulkQabul = $this->getMulkQabulQilmagan($tumanPatterns, $dateFilters);

        return [
            'tuman' => $tumanName,
            'tuman_patterns' => $tumanPatterns,
            'jami' => $jami,
            'bir_yola' => $birYola,
            'bolib' => $bolib,
            'auksonda' => $auksonda,
            'mulk_qabul' => $mulkQabul
        ];
    }

    private function getTumanPatterns($tumanName)
    {
        $base = str_replace([' т.', ' тумани'], '', $tumanName);

        $patterns = [
            $base,
            $base . ' т.',
            $base . ' тумани',
        ];

        if (mb_strpos($base, 'о') !== false) {
            $altBase = str_replace('о', 'ҳ', $base);
            $patterns[] = $altBase;
            $patterns[] = $altBase . ' т.';
            $patterns[] = $altBase . ' тумани';
        }

        if (mb_strpos($base, 'ҳ') !== false) {
            $altBase = str_replace('ҳ', 'о', $base);
            $patterns[] = $altBase;
            $patterns[] = $altBase . ' т.';
            $patterns[] = $altBase . ' тумани';
        }

        return array_unique($patterns);
    }

    private function getTumanData($tumanPatterns = null, $tolovTuri = null, $dateFilters = [])
    {
        $query = YerSotuv::query();

        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        if ($tolovTuri) {
            $query->where('tolov_turi', $tolovTuri);
        }

        if (!empty($dateFilters['auksion_sana_from'])) {
            $query->whereDate('auksion_sana', '>=', $dateFilters['auksion_sana_from']);
        }
        if (!empty($dateFilters['auksion_sana_to'])) {
            $query->whereDate('auksion_sana', '<=', $dateFilters['auksion_sana_to']);
        }

        $data = $query->selectRaw('
            COUNT(*) as soni,
            SUM(maydoni) as maydoni,
            SUM(boshlangich_narx) as boshlangich_narx,
            SUM(sotilgan_narx) as sotilgan_narx,
            SUM(chegirma) as chegirma
        ')->first();

        $queryTushadigan = YerSotuv::query();

        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $queryTushadigan->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        if ($tolovTuri) {
            $queryTushadigan->where('tolov_turi', $tolovTuri);
        }

        $queryTushadigan->where('tolov_turi', '!=', 'низоли');

        if (!empty($dateFilters['auksion_sana_from'])) {
            $queryTushadigan->whereDate('auksion_sana', '>=', $dateFilters['auksion_sana_from']);
        }
        if (!empty($dateFilters['auksion_sana_to'])) {
            $queryTushadigan->whereDate('auksion_sana', '<=', $dateFilters['auksion_sana_to']);
        }

        $tushadiganData = $queryTushadigan->selectRaw('
            SUM(COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0)) as tushadigan_mablagh,
            SUM(COALESCE(auksion_harajati, 0)) as auksion_harajati
        ')->first();



        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'boshlangich_narx' => $data->boshlangich_narx ?? 0,
            'sotilgan_narx' => $data->sotilgan_narx ?? 0,
            'chegirma' => $data->chegirma ?? 0,
            'tushadigan_mablagh' => ($tushadiganData->tushadigan_mablagh - $tushadiganData->auksion_harajati ) ?? 0
        ];
    }

    private function getAuksondaTurgan($tumanPatterns = null, $dateFilters = [])
    {
        $query = YerSotuv::query();

        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        $query->where(function ($q) {
            $q->where('tolov_turi', '!=', 'муддатли')
                ->where('tolov_turi', '!=', 'муддатли эмас')
                ->orWhereNull('tolov_turi');
        });

        if (!empty($dateFilters['auksion_sana_from'])) {
            $query->whereDate('auksion_sana', '>=', $dateFilters['auksion_sana_from']);
        }
        if (!empty($dateFilters['auksion_sana_to'])) {
            $query->whereDate('auksion_sana', '<=', $dateFilters['auksion_sana_to']);
        }

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
            'chegirma' => $data->chegirma ?? 0,
            'golib_tolagan' => $data->golib_tolagan ?? 0,
        ];
    }
private function getMulkQabulQilmagan($tumanPatterns = null, $dateFilters = [])
{
    $query = YerSotuv::query();

    if ($tumanPatterns !== null && !empty($tumanPatterns)) {
        $query->where(function ($q) use ($tumanPatterns) {
            foreach ($tumanPatterns as $pattern) {
                $q->orWhere('tuman', 'like', '%' . $pattern . '%');
            }
        });
    }

    $query->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%');
    $query->where('asos', 'ПФ-135');
    // Removed the tolov_turi filter to get both types

    if (!empty($dateFilters['auksion_sana_from'])) {
        $query->whereDate('auksion_sana', '>=', $dateFilters['auksion_sana_from']);
    }
    if (!empty($dateFilters['auksion_sana_to'])) {
        $query->whereDate('auksion_sana', '<=', $dateFilters['auksion_sana_to']);
    }

    // Clone query for debugging
    $debugQuery = clone $query;
    $lots = $debugQuery->get(['id', 'lot_raqami', 'tuman', 'tolov_turi', 'golib_tolagan', 'auksion_harajati']);

    // Log detailed information
    \Log::info('SQL Query: ' . $debugQuery->toSql());
    \Log::info('Query Bindings:', $debugQuery->getBindings());
    \Log::info('Lots Found: ' . $lots->count());
    \Log::info('Lot IDs: ' . $lots->pluck('id')->implode(', '));
    \Log::info('Lot Numbers: ' . $lots->pluck('lot_raqami')->implode(', '));

    // Log golib_tolagan and auksion_harajati for each lot
    if ($lots->count() > 0) {
        \Log::info('=== Lot Financial Details ===');
        foreach ($lots as $lot) {
            \Log::info(sprintf(
                'Lot ID: %s, Lot #: %s, Tuman: %s, Tolov Turi: %s, Golib Tolagan: %s, Auksion Harajati: %s',
                $lot->id,
                $lot->lot_raqami,
                $lot->tuman,
                $lot->tolov_turi ?? 'NULL',
                $lot->golib_tolagan ?? 'NULL',
                $lot->auksion_harajati ?? 'NULL'
            ));
        }

        // Calculate totals with conditional logic
        $totalGolibTolagan = 0;
        $totalAuksionHarajatiSubtracted = 0;

        foreach ($lots as $lot) {
            $golibTolagan = floatval($lot->golib_tolagan ?? 0);
            $auksionHarajati = floatval($lot->auksion_harajati ?? 0);

            // Only subtract auksion_harajati if tolov_turi is 'муддатли эмас'
            if ($lot->tolov_turi === 'муддатли эмас') {
                $totalGolibTolagan += ($golibTolagan - $auksionHarajati);
                $totalAuksionHarajatiSubtracted += $auksionHarajati;
            } else {
                // For 'муддатли', just add golib_tolagan without subtracting
                $totalGolibTolagan += $golibTolagan;
            }
        }

        \Log::info('=== Totals ===');
        \Log::info('Total Auksion Harajati (subtracted only from муддатли эмас): ' . number_format($totalAuksionHarajatiSubtracted, 2, '.', ','));
        \Log::info('Total Auksion Mablagh (after conditional subtraction): ' . number_format($totalGolibTolagan, 2, '.', ','));
    }

    // Calculate using the same logic for return value
    $results = $query->get(['tolov_turi', 'golib_tolagan', 'auksion_harajati']);

    $auksionMablagh = 0;
    foreach ($results as $result) {
        $golibTolagan = floatval($result->golib_tolagan ?? 0);
        $auksionHarajati = floatval($result->auksion_harajati ?? 0);

        if ($result->tolov_turi === 'муддатли эмас') {
            $auksionMablagh += ($golibTolagan - $auksionHarajati);
        } else {
            $auksionMablagh += $golibTolagan;
        }
    }

    return [
        'soni' => $results->count(),
        'auksion_mablagh' => $auksionMablagh
    ];
}
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

    private function showFilteredData(Request $request, array $filters)
    {
        $query = YerSotuv::query();

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

        if (!empty($filters['tuman'])) {
            $tumanPatterns = $this->getTumanPatterns($filters['tuman']);
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }

        if (!empty($filters['yil'])) {
            $query->where('yil', $filters['yil']);
        }

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

        if (!empty($filters['narx_from'])) {
            $query->where('sotilgan_narx', '>=', $filters['narx_from']);
        }
        if (!empty($filters['narx_to'])) {
            $query->where('sotilgan_narx', '<=', $filters['narx_to']);
        }

        if (!empty($filters['maydoni_from'])) {
            $query->where('maydoni', '>=', $filters['maydoni_from']);
        }
        if (!empty($filters['maydoni_to'])) {
            $query->where('maydoni', '<=', $filters['maydoni_to']);
        }

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
            $bugun = $this->getGrafikCutoffDate();

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

        if (!empty($filters['holat'])) {
            $query->where('holat', 'like', '%' . $filters['holat'] . '%');
            if (strpos($filters['holat'], '(34)') !== false) {
                $query->where('asos', 'ПФ-135');
            }
        }

        if (!empty($filters['asos'])) {
            $query->where('asos', 'like', '%' . $filters['asos'] . '%');
        }

        $statistics = [
            'total_lots' => $query->count(),
            'total_area' => $query->sum('maydoni'),
            'total_price' => $query->sum('sotilgan_narx'),
            'boshlangich_narx' => $query->sum('boshlangich_narx'),
            'chegirma' => $query->sum('chegirma'),
            'golib_tolagan' => $query->sum('golib_tolagan'),
        ];

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

        $yerlar = $query->paginate(50)->withQueryString();

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

    private function debugMulkQabul()
    {
        echo "<h1>DEBUG: Mulk Qabul Qilmagan Yerlar</h1>";

        echo "<h2>1. BARCHA HOLATLAR</h2>";
        $holatlar = YerSotuv::select('holat', DB::raw('COUNT(*) as count'))
            ->whereNotNull('holat')
            ->groupBy('holat')
            ->orderBy('count', 'desc')
            ->limit(20)
            ->get();

        echo "<table border='1' style='width: 100%; font-size: 12px;'>";
        echo "<tr><th>Holat</th><th>Count</th></tr>";
        foreach ($holatlar as $h) {
            $highlight = (strpos($h->holat, '34') !== false || strpos($h->holat, 'Ishtirokchi') !== false) ? 'background: yellow;' : '';
            echo "<tr style='{$highlight}'><td>{$h->holat}</td><td><strong>{$h->count}</strong></td></tr>";
        }
        echo "</table><br>";

        echo "<h2>2. BARCHA ASOSLAR</h2>";
        $asoslar = YerSotuv::select('asos', DB::raw('COUNT(*) as count'))
            ->whereNotNull('asos')
            ->groupBy('asos')
            ->orderBy('count', 'desc')
            ->get();

        echo "<table border='1' style='width: 100%; font-size: 12px;'>";
        echo "<tr><th>Asos</th><th>Count</th></tr>";
        foreach ($asoslar as $a) {
            $highlight = (strpos($a->asos, '135') !== false || strpos($a->asos, 'ПФ') !== false) ? 'background: yellow;' : '';
            echo "<tr style='{$highlight}'><td>{$a->asos}</td><td><strong>{$a->count}</strong></td></tr>";
        }
        echo "</table><br>";

        die();
    }

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

        $tolovTaqqoslash = $this->taqqoslashHisoblash($yer);

        return view('yer-sotuvlar.show', compact('yer', 'tolovTaqqoslash'));
    }

    private function taqqoslashHisoblash($yer)
    {
        $grafikByMonth = $yer->grafikTolovlar->groupBy(function ($item) {
            return $item->yil . '-' . str_pad($item->oy, 2, '0', STR_PAD_LEFT);
        });

        $faktByMonth = $yer->faktTolovlar->groupBy(function ($item) {
            return $item->tolov_sana->format('Y-m');
        });

        $taqqoslash = [];
        $allMonths = [];

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
}
