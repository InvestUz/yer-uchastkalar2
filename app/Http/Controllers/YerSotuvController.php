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
        $filters = $request->only(['tuman', 'yil', 'tolov_turi', 'holat', 'asos']);
        
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
        foreach($holatlar as $h) {
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
        foreach($asoslar as $a) {
            $highlight = (strpos($a->asos, '135') !== false || strpos($a->asos, 'ПФ') !== false) ? 'background: yellow;' : '';
            echo "<tr style='{$highlight}'><td>{$a->asos}</td><td><strong>{$a->count}</strong></td></tr>";
        }
        echo "</table><br>";
        
        echo "<h2>3. TEST QUERIES</h2>";
        
        // Test 1: Aniq
        $test1 = YerSotuv::where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%')
            ->where('asos', 'like', '%ПФ-135%')
            ->count();
        echo "<p><strong>Test 1 (ANIQ):</strong> holat LIKE '%Ishtirokchi roziligini kutish jarayonida (34)%' AND asos LIKE '%ПФ-135%'</p>";
        echo "<p style='color: red; font-size: 20px;'>COUNT: <strong>{$test1}</strong></p>";
        
        // Test 2: Faqat (34)
        $test2 = YerSotuv::where('holat', 'like', '%34%')->count();
        echo "<p><strong>Test 2:</strong> holat LIKE '%34%'</p>";
        echo "<p style='color: blue; font-size: 20px;'>COUNT: <strong>{$test2}</strong></p>";
        
        // Test 3: Faqat 135
        $test3 = YerSotuv::where('asos', 'like', '%135%')->count();
        echo "<p><strong>Test 3:</strong> asos LIKE '%135%'</p>";
        echo "<p style='color: green; font-size: 20px;'>COUNT: <strong>{$test3}</strong></p>";
        
        // Test 4: Kombinatsiya (keng)
        $test4 = YerSotuv::where('holat', 'like', '%34%')
            ->where('asos', 'like', '%135%')
            ->count();
        echo "<p><strong>Test 4 (KENG):</strong> holat LIKE '%34%' AND asos LIKE '%135%'</p>";
        echo "<p style='color: purple; font-size: 20px;'>COUNT: <strong>{$test4}</strong></p>";
        
        echo "<h2>4. BIRINCHI 10 TA (asos LIKE '%135%')</h2>";
        $samples = YerSotuv::select('lot_raqami', 'tuman', 'holat', 'asos')
            ->where('asos', 'like', '%135%')
            ->limit(10)
            ->get();
        
        echo "<table border='1' style='width: 100%; font-size: 11px;'>";
        echo "<tr><th>Lot</th><th>Tuman</th><th>Holat</th><th>Asos</th></tr>";
        foreach($samples as $s) {
            echo "<tr>";
            echo "<td>{$s->lot_raqami}</td>";
            echo "<td>{$s->tuman}</td>";
            echo "<td>" . substr($s->holat ?? '', 0, 60) . "...</td>";
            echo "<td>{$s->asos}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        die();
    }
    
    private function showFilteredData(Request $request, array $filters)
    {
        $query = YerSotuv::with(['grafikTolovlar', 'faktTolovlar']);
        
        // Tuman filter
        if (!empty($filters['tuman'])) {
            $tumanPatterns = $this->getTumanPatterns($filters['tuman']);
            $query->where(function($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }
        
        // Other filters
        if (!empty($filters['yil'])) {
            $query->where('yil', $filters['yil']);
        }
        
        if (!empty($filters['tolov_turi'])) {
            $query->where('tolov_turi', $filters['tolov_turi']);
        }
        
        if (!empty($filters['holat'])) {
            $query->where('holat', 'like', '%' . $filters['holat'] . '%');
        }
        
        if (!empty($filters['asos'])) {
            $query->where('asos', 'like', '%' . $filters['asos'] . '%');
        }
        
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
        
        return view('yer-sotuvlar.list', compact('yerlar', 'tumanlar', 'yillar', 'filters'));
    }
    
    private function getDetailedStatistics()
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
        
        $statistics = [];
        
        // Har bir tuman uchun statistika
        foreach ($tumanlar as $tuman) {
            $stat = $this->calculateTumanStatistics($tuman);
            $statistics[] = $stat;
        }
        
        // JAMI ni to'g'ridan-to'g'ri hisoblash (tuman filtersiz)
        $jami = [
            'jami' => $this->getTumanData(null),
            'bir_yola' => $this->getTumanData(null, 'муддатли эмас'),
            'bolib' => $this->getTumanData(null, 'муддатли'),
            'auksonda' => $this->getAuksondaTurgan(null),
            'mulk_qabul' => $this->getMulkQabulQilmagan(null)
        ];
        
        return [
            'tumanlar' => $statistics,
            'jami' => $jami
        ];
    }
        
    private function calculateTumanStatistics($tumanName)
    {
        // Tuman nomini olish (masalan: "Бектемир т." -> pattern matching uchun)
        $tumanPatterns = $this->getTumanPatterns($tumanName);
        
        // Jami sotilgan yerlar
        $jami = $this->getTumanData($tumanPatterns);
        
        // Bir yo'la to'lash
        $birYola = $this->getTumanData($tumanPatterns, 'муддатли эмас');
        
        // Bo'lib to'lash
        $bolib = $this->getTumanData($tumanPatterns, 'муддатли');
        
        // Auksonda turgan
        $auksonda = $this->getAuksondaTurgan($tumanPatterns);
        
        // Mulk qabul qilish tugmasi bosilmagan
        $mulkQabul = $this->getMulkQabulQilmagan($tumanPatterns);
        
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
        // Turli variantlarni yaratish
        $base = str_replace([' т.', ' тумани'], '', $tumanName);
        
        // Maxsus variantlar (masalan: Шайхонтоҳур va Шайхонтоҳур)
        $patterns = [
            $base,                          // Шайхонтоҳур
            $base . ' т.',                  // Шайхонтоҳур т.
            $base . ' тумани',              // Шайхонтоҳур тумани
        ];
        
        // О/о variant (masalan: Шайхонтоҳур <-> Шайхонтоҳур)
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
    
    private function getTumanData($tumanPatterns = null, $tolovTuri = null)
    {
        $query = YerSotuv::query();
        
        // Tuman filter (agar mavjud bo'lsa)
        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }
        
        // Tolov turi filter
        if ($tolovTuri) {
            $query->where('tolov_turi', $tolovTuri);
        }
        
        $data = $query->selectRaw('
            COUNT(*) as soni,
            SUM(maydoni) as maydoni,
            SUM(boshlangich_narx) as boshlangich_narx,
            SUM(sotilgan_narx) as sotilgan_narx,
            SUM(chegirma) as chegirma,
            SUM(tushadigan_mablagh) as tushadigan_mablagh
        ')->first();
        
        return [
            'soni' => $data->soni ?? 0,
            'maydoni' => $data->maydoni ?? 0,
            'boshlangich_narx' => $data->boshlangich_narx ?? 0,
            'sotilgan_narx' => $data->sotilgan_narx ?? 0,
            'chegirma' => $data->chegirma ?? 0,
            'tushadigan_mablagh' => $data->tushadigan_mablagh ?? 0
        ];
    }
    
    private function getAuksondaTurgan($tumanPatterns = null)
    {
        $query = YerSotuv::query();
        
        // Tuman filter (agar mavjud bo'lsa)
        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }
        
        // Holat filter
        $query->where(function($q) {
            $q->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida%')
              ->orWhere('holat', 'like', '%расмийлаштиришда%');
        });
        
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
            'sotilgan_narx' => $data->sotilgan_narx ?? 0
        ];
    }
    
    private function getMulkQabulQilmagan($tumanPatterns = null)
    {
        // FAQAT HOLAT (34) BO'YICHA - 17 ta
        // Agar tuman pattern bo'lsa, faqat o'sha tuman uchun
        // Aks holda, barcha tumanlar uchun
        
        $query = YerSotuv::query();
        
        // Tuman filter (agar mavjud bo'lsa)
        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }
        
        // Holat filter
        $query->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%');
        
        // Agar davaktivda_turgan mavjud bo'lsa uni ishlatish, aks holda sotilgan_narx
        $data = $query->selectRaw('
            COUNT(*) as soni,
            SUM(CASE 
                WHEN davaktivda_turgan IS NOT NULL AND davaktivda_turgan > 0 
                THEN davaktivda_turgan 
                ELSE sotilgan_narx 
            END) as auksion_mablagh
        ')->first();
        
        return [
            'soni' => $data->soni ?? 0,
            'auksion_mablagh' => $data->auksion_mablagh ?? 0
        ];
    }
    
    private function initializeTotal()
    {
        return [
            'jami' => ['soni' => 0, 'maydoni' => 0, 'boshlangich_narx' => 0, 'sotilgan_narx' => 0, 'chegirma' => 0, 'tushadigan_mablagh' => 0],
            'bir_yola' => ['soni' => 0, 'maydoni' => 0, 'boshlangich_narx' => 0, 'sotilgan_narx' => 0, 'chegirma' => 0, 'tushadigan_mablagh' => 0],
            'bolib' => ['soni' => 0, 'maydoni' => 0, 'boshlangich_narx' => 0, 'sotilgan_narx' => 0, 'chegirma' => 0, 'tushadigan_mablagh' => 0],
            'auksonda' => ['soni' => 0, 'maydoni' => 0, 'boshlangich_narx' => 0, 'sotilgan_narx' => 0],
            'mulk_qabul' => ['soni' => 0, 'auksion_mablagh' => 0]
        ];
    }
    
    private function addToTotal(&$jami, $stat)
    {
        foreach (['jami', 'bir_yola', 'bolib'] as $key) {
            foreach ($stat[$key] as $field => $value) {
                $jami[$key][$field] += $value;
            }
        }
        
        foreach ($stat['auksonda'] as $field => $value) {
            $jami['auksonda'][$field] += $value;
        }
        
        foreach ($stat['mulk_qabul'] as $field => $value) {
            $jami['mulk_qabul'][$field] += $value;
        }
    }
    
   public function show($lot_raqami)
    {
        // Eager loading bilan barcha ma'lumotlarni olish
        $yer = YerSotuv::where('lot_raqami', $lot_raqami)
            ->with([
                'grafikTolovlar' => function($query) {
                    $query->orderBy('yil')->orderBy('oy');
                },
                'faktTolovlar' => function($query) {
                    $query->orderByDesc('tolov_sana');
                }
            ])
            ->firstOrFail();

        // To'lovlarni taqqoslash
        $tolovTaqqoslash = $this->taqqoslashHisoblash($yer);

        return view('yer-sotuvlar.show', compact('yer', 'tolovTaqqoslash'));
    }
      private function taqqoslashHisoblash($yer)
    {
        // Grafik to'lovlarni guruhlash
        $grafikByMonth = $yer->grafikTolovlar->groupBy(function($item) {
            return $item->yil . '-' . str_pad($item->oy, 2, '0', STR_PAD_LEFT);
        });

        // Fakt to'lovlarni oy bo'yicha guruhlash
        $faktByMonth = $yer->faktTolovlar->groupBy(function($item) {
            return $item->tolov_sana->format('Y-m');
        });

        $taqqoslash = [];

        foreach ($grafikByMonth as $key => $grafikItems) {
            $grafikSumma = $grafikItems->sum('grafik_summa');
            $faktSumma = $faktByMonth->get($key)?->sum('tolov_summa') ?? 0;
            $farq = $grafikSumma - $faktSumma;
            $foiz = $grafikSumma > 0 ? round(($faktSumma / $grafikSumma) * 100, 1) : 0;

            $taqqoslash[] = [
                'yil' => $grafikItems->first()->yil,
                'oy' => $grafikItems->first()->oy,
                'oy_nomi' => $grafikItems->first()->oy_nomi,
                'grafik' => $grafikSumma,
                'fakt' => $faktSumma,
                'farq' => $farq,
                'foiz' => $foiz
            ];
        }

        return collect($taqqoslash)->sortBy('yil')->values()->all();
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
}