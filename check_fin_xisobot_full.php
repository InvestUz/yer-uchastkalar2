<?php
// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DavaktivRasxod;

// ─── Categories ────────────────────────────────────────────────────────────────
$paymentCategories = [
    'Чегирма'                                             => 0,
    'Харидорларга қайтарилган маблағлар'                  => 0,
    'Тошкент ш. қурилиш бошкармасига (1%)'               => 0,
    'Давлат кадастрлар палатасига'                        => 0,
    'Геоахборот шахарсозлик кадастрига'                   => 0,
    'Солиқ қўмитаси хузуридаги Кадастр агентлигига'      => 0,
    'Тошкент шахар махаллий бюджетига'                    => 0,
    'Жамғармага'                                          => 0,
    'Туманга'                                             => 0,
    'ЯнгиХаёт индустриал технопарки дирекциясига'        => 0,
    'Шайҳонтохур туманига'                                => 0,
    'Тошкент сити дирекциясига'                           => 0,
];

// ─── Normalizer ────────────────────────────────────────────────────────────────
$normalizeText = static function (?string $text): string {
    if ($text === null || $text === '') return '';
    $lower = mb_strtolower($text, 'UTF-8');
    $clean = preg_replace('/[^\p{L}\p{N}]+/u', '', $lower);
    return $clean ?? $lower;
};

// ─── District patterns ─────────────────────────────────────────────────────────
$districtPatterns = [
    'бектемир'      => 'Бектемир',
    'миробод'       => 'Миробод',
    'олмазор'       => 'Олмазор',
    'сергели'       => 'Сергели',
    'сирғали'       => 'Сергели',
    'сиргали'       => 'Сергели',
    'учтепа'        => 'Учтепа',
    'шайҳонтохур'   => 'Шайҳонтохур',
    'шайхонтохур'   => 'Шайҳонтохур',
    'шайхонтахур'   => 'Шайҳонтохур',
    'шайхонтаур'    => 'Шайҳонтохур',
    'юнусобод'      => 'Юнусобод',
    'яккасарой'     => 'Яккасарой',
    'чилонзор'      => 'Чилонзор',
    'мирзоулугбек'  => 'Мирзо Улугбек',
    'мирзоулубек'   => 'Мирзо Улугбек',
    'мирзоулуғбек'  => 'Мирзо Улугбек',
    'яшнобод'       => 'Яшнобод',
    'янгихаёт'      => 'Янги Хаёт',
    'янгиҳаёт'      => 'Янги Хаёт',
    'янгихает'      => 'Янги Хаёт',
];

$categoryDistrictFallbacks = [
    'ЯнгиХаёт индустриал технопарки дирекциясига' => 'Янги Хаёт',
    'Шайҳонтохур туманига'                         => 'Шайҳонтохур',
];

// ─── Process records ───────────────────────────────────────────────────────────
$records = DavaktivRasxod::all();

$districtData   = [];
$categoryTotals = $paymentCategories;
$totalAmount    = 0;
$totalCount     = 0;

// Track unmatched records
$unmatchedRecords = [];

foreach ($records as $record) {
    $articleName = $record->article ?? 'Тошкент шахар махаллий бюджетига';
    $amount      = (float)($record->amount ?? 0);
    if ($amount <= 0) continue;

    $totalAmount += $amount;
    $totalCount++;

    // ── Determine category ──────────────────────────────────────────────────
    $category = 'Тошкент шахар махаллий бюджетига';
    if (isset($paymentCategories[$articleName])) {
        $category = $articleName;
    } else {
        foreach (array_keys($paymentCategories) as $catKey) {
            if (stripos($articleName, $catKey) !== false || stripos($catKey, $articleName) !== false) {
                $category = $catKey;
                break;
            }
        }
    }

    if (!isset($categoryTotals[$category])) $categoryTotals[$category] = 0;
    $categoryTotals[$category] += $amount;

    // ── Determine district ──────────────────────────────────────────────────
    $district        = null;
    $details         = $record->details ?? '';
    $normalizedDets  = $normalizeText($details);

    foreach ($districtPatterns as $pattern => $districtName) {
        if ($normalizedDets !== '' && strpos($normalizedDets, $pattern) !== false) {
            $district = $districtName;
            break;
        }
    }

    if ($district === null && isset($categoryDistrictFallbacks[$category])) {
        $district = $categoryDistrictFallbacks[$category];
    }

    if ($district === null) {
        $district = 'Бошқа';
        $unmatchedRecords[] = [
            'id'         => $record->id,
            'doc_number' => $record->doc_number,
            'article'    => $articleName,
            'category'   => $category,
            'amount'     => $amount,
            'details'    => mb_substr($details, 0, 200),
        ];
    }

    // ── Accumulate into districtData ────────────────────────────────────────
    if (!isset($districtData[$district])) {
        $districtData[$district] = array_merge(['Жами' => 0, 'count' => 0], $paymentCategories);
    }
    $districtData[$district]['Жами']  += $amount;
    $districtData[$district]['count'] += 1;
    if (isset($districtData[$district][$category])) {
        $districtData[$district][$category] += $amount;
    }
}

// ─── Sort districts by total desc ─────────────────────────────────────────────
uasort($districtData, fn($a, $b) => $b['Жами'] <=> $a['Жами']);

// ─── Build output ──────────────────────────────────────────────────────────────
$output = [
    'generated_at'     => date('Y-m-d H:i:s'),
    'total_records'    => $totalCount,
    'total_amount'     => $totalAmount,
    'total_amount_fmt' => number_format($totalAmount, 2),

    // ── Category totals row (ЖАМИ) ─────────────────────────────────────────
    'category_totals'  => array_map(fn($v) => [
        'amount' => $v,
        'fmt'    => number_format($v, 2),
    ], $categoryTotals),

    // ── Per-district breakdown ─────────────────────────────────────────────
    'districts' => [],

    // ── Unmatched (Бошқа) records ─────────────────────────────────────────
    'unmatched_records_count' => count($unmatchedRecords),
    'unmatched_records'       => $unmatchedRecords,
];

$rowNum = 1;
foreach ($districtData as $distName => $data) {
    $row = [
        'row'        => $rowNum++,
        'district'   => $distName,
        'count'      => $data['count'],
        'total'      => $data['Жами'],
        'total_fmt'  => number_format($data['Жами'], 2),
        'categories' => [],
    ];

    foreach ($paymentCategories as $catName => $_) {
        $val = $data[$catName] ?? 0;
        $row['categories'][$catName] = [
            'amount' => $val,
            'fmt'    => number_format($val, 2),
        ];
    }

    $output['districts'][] = $row;
}

// ─── Save JSON ─────────────────────────────────────────────────────────────────
$outPath = storage_path('logs/fin_xisobot_full_check.json');
file_put_contents($outPath, json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\n=== FIN-XISOBOT FULL CHECK ===\n";
echo "Total records : {$totalCount}\n";
echo "Total amount  : " . number_format($totalAmount, 2) . "\n";
echo "Districts found: " . count($districtData) . "\n";
echo "\n--- District summary ---\n";
$i = 0;
foreach ($districtData as $d => $data) {
    $i++;
    echo sprintf("  %2d. %-30s  records: %3d  total: %s\n",
        $i, $d, $data['count'], number_format($data['Жами'], 2));
}
echo "\n--- Category totals (ЖАМИ row) ---\n";
foreach ($categoryTotals as $cat => $val) {
    echo sprintf("  %-55s  %s\n", $cat, number_format($val, 2));
}
echo "\n--- Unmatched (Бошқа) records : " . count($unmatchedRecords) . " ---\n";

// Group unmatched by category
$unmatchedByCat = [];
foreach ($unmatchedRecords as $r) {
    $unmatchedByCat[$r['category']][] = $r;
}
foreach ($unmatchedByCat as $cat => $rows) {
    $sum = array_sum(array_column($rows, 'amount'));
    echo sprintf("  %-55s  count: %3d  total: %s\n", $cat, count($rows), number_format($sum, 2));
}

echo "\n✅ Full result saved to: {$outPath}\n";
