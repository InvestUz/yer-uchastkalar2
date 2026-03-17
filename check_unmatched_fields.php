<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DavaktivRasxod;

$districtPatterns = [
    'бектемир'=>'Бектемир','миробод'=>'Миробод','олмазор'=>'Олмазор',
    'сергели'=>'Сергели','сирғали'=>'Сергели','сиргали'=>'Сергели','учтепа'=>'Учтепа',
    'шайҳонтохур'=>'Шайҳонтохур','шайхонтохур'=>'Шайҳонтохур','шайхонтахур'=>'Шайҳонтохур','шайхонтаур'=>'Шайҳонтохур',
    'юнусобод'=>'Юнусобод','яккасарой'=>'Яккасарой','чилонзор'=>'Чилонзор',
    'мирзоулугбек'=>'Мирзо Улугбек','мирзоулубек'=>'Мирзо Улугбек','мирзоулуғбек'=>'Мирзо Улугбек',
    'яшнобод'=>'Яшнобод','янгихаёт'=>'Янги Хаёт','янгиҳаёт'=>'Янги Хаёт','янгихает'=>'Янги Хаёт',
];
$paymentCategories = [
    'Чегирма'=>0,'Харидорларга қайтарилган маблағлар'=>0,'Тошкент ш. қурилиш бошкармасига (1%)'=>0,
    'Давлат кадастрлар палатасига'=>0,'Геоахборот шахарсозлик кадастрига'=>0,
    'Солиқ қўмитаси хузуридаги Кадастр агентлигига'=>0,'Тошкент шахар махаллий бюджетига'=>0,
    'Жамғармага'=>0,'Туманга'=>0,'ЯнгиХаёт индустриал технопарки дирекциясига'=>0,
    'Шайҳонтохур туманига'=>0,'Тошкент сити дирекциясига'=>0,
];
$categoryDistrictFallbacks = [
    'ЯнгиХаёт индустриал технопарки дирекциясига' => 'Янги Хаёт',
    'Шайҳонтохур туманига' => 'Шайҳонтохур',
];
$normalizeText = function(?string $text): string {
    if ($text === null || $text === '') return '';
    $lower = mb_strtolower($text, 'UTF-8');
    return preg_replace('/[^\p{L}\p{N}]+/u', '', $lower) ?? $lower;
};

$records = DavaktivRasxod::all();
$rows = [];

foreach ($records as $r) {
    $amount = (float)($r->amount ?? 0);
    if ($amount <= 0) continue;
    $articleName = $r->article ?? 'Тошкент шахар махаллий бюджетига';
    $category = isset($paymentCategories[$articleName]) ? $articleName : 'Тошкент шахар махаллий бюджетига';
    foreach (array_keys($paymentCategories) as $ck) {
        if ($category === 'Тошкент шахар махаллий бюджетига' && $ck !== $articleName) {
            if (stripos($articleName, $ck) !== false || stripos($ck, $articleName) !== false) {
                $category = $ck; break;
            }
        }
    }
    $normalizedDets = $normalizeText($r->details);
    $district = null;
    foreach ($districtPatterns as $p => $dn) {
        if ($normalizedDets !== '' && strpos($normalizedDets, $p) !== false) { $district = $dn; break; }
    }
    if ($district === null && isset($categoryDistrictFallbacks[$category])) $district = $categoryDistrictFallbacks[$category];
    if ($district !== null) continue;

    $rows[] = [
        'id'             => $r->id,
        'doc_number'     => $r->doc_number,
        'doc_date'       => $r->doc_date,
        'category'       => $category,
        'amount'         => $amount,
        'recipient_name' => $r->recipient_name,
        'by_articles'    => $r->by_articles,
        'details'        => $r->details,
    ];
}

// Also check recipient_name and by_articles for district patterns
echo "=== ALL UNMATCHED RECORDS (" . count($rows) . ") ===\n\n";
$uniqueRecipients = [];
$uniqueByArticles = [];
foreach ($rows as $row) {
    $uniqueRecipients[$row['recipient_name']] = ($uniqueRecipients[$row['recipient_name']] ?? 0) + $row['amount'];
    $uniqueByArticles[$row['by_articles']]    = ($uniqueByArticles[$row['by_articles']] ?? 0) + $row['amount'];
}

echo "--- Unique recipient_name values ---\n";
arsort($uniqueRecipients);
foreach ($uniqueRecipients as $name => $amt) {
    echo "  " . number_format($amt, 0) . "  =>  " . $name . "\n";
}

echo "\n--- Unique by_articles values ---\n";
arsort($uniqueByArticles);
foreach ($uniqueByArticles as $ba => $amt) {
    echo "  " . number_format($amt, 0) . "  =>  " . $ba . "\n";
}

echo "\n--- Unique details snippets (first 120 chars) ---\n";
$uniqueDetails = [];
foreach ($rows as $row) {
    $key = mb_substr($row['details'] ?? '', 0, 120);
    if (!isset($uniqueDetails[$key])) $uniqueDetails[$key] = ['count'=>0,'amount'=>0,'category'=>$row['category']];
    $uniqueDetails[$key]['count']++;
    $uniqueDetails[$key]['amount'] += $row['amount'];
}
uasort($uniqueDetails, fn($a,$b)=>$b['amount']<=>$a['amount']);
foreach ($uniqueDetails as $det => $info) {
    echo "\n  [{$info['category']}] count={$info['count']} total=" . number_format($info['amount'], 0) . "\n";
    echo "  details: " . $det . "\n";
}
