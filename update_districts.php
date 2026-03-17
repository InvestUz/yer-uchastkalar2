<?php
/**
 * update_districts.php
 *
 * 1. Reads storage/app/district_overrides.csv for manual district assignments
 * 2. Updates all davaktiv_rasxods rows with detected district (text or override)
 * 3. Writes storage/app/district_overrides.csv pre-filled with unmatched records
 *    so the user can fill in the district column and re-run this script
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DavaktivRasxod;
use Illuminate\Support\Facades\DB;

// ── District patterns ────────────────────────────────────────────────────────
$districtPatterns = [
    'бектемир'     => 'Бектемир',
    'миробод'      => 'Миробод',
    'олмазор'      => 'Олмазор',
    'сергели'      => 'Сергели',
    'сирғали'      => 'Сергели',
    'сиргали'      => 'Сергели',
    'учтепа'       => 'Учтепа',
    'шайҳонтохур'  => 'Шайҳонтохур',
    'шайхонтохур'  => 'Шайҳонтохур',
    'шайхонтахур'  => 'Шайҳонтохур',
    'шайхонтаур'   => 'Шайҳонтохур',
    'юнусобод'     => 'Юнусобод',
    'яккасарой'    => 'Яккасарой',
    'чилонзор'     => 'Чилонзор',
    'мирзоулугбек' => 'Мирзо Улугбек',
    'мирзоулубек'  => 'Мирзо Улугбек',
    'мирзоулуғбек' => 'Мирзо Улугбек',
    'яшнобод'      => 'Яшнобод',
    'янгихаёт'     => 'Янги Хаёт',
    'янгиҳаёт'     => 'Янги Хаёт',
    'янгихает'     => 'Янги Хаёт',
];

$categoryDistrictFallbacks = [
    'ЯнгиХаёт индустриал технопарки дирекциясига' => 'Янги Хаёт',
    'Шайҳонтохур туманига'                         => 'Шайҳонтохур',
];

$normalizeText = function (?string $text): string {
    if ($text === null || $text === '') return '';
    $lower = mb_strtolower($text, 'UTF-8');
    return preg_replace('/[^\p{L}\p{N}]+/u', '', $lower) ?? $lower;
};

function detectDistrict(?string $details, ?string $article, array $patterns, array $fallbacks, callable $normalize): ?string
{
    if ($details) {
        $n = $normalize($details);
        foreach ($patterns as $pattern => $name) {
            if ($n !== '' && strpos($n, $pattern) !== false) return $name;
        }
    }
    if ($article && isset($fallbacks[$article])) return $fallbacks[$article];
    return null;
}

// ── Load manual overrides ────────────────────────────────────────────────────
$overridePath = storage_path('app/district_overrides.csv');
$overrides    = [];

if (file_exists($overridePath)) {
    $fh    = fopen($overridePath, 'r');
    $first = true;
    while (($row = fgetcsv($fh, 0, ';')) !== false) {
        if ($first) { $first = false; continue; }
        $docNum   = trim($row[0] ?? '');
        $district = trim($row[2] ?? '');
        if ($docNum !== '' && $district !== '') {
            $overrides[$docNum] = $district;
        }
    }
    fclose($fh);
    echo "✅ Loaded " . count($overrides) . " manual overrides from district_overrides.csv\n";
} else {
    echo "ℹ️  No district_overrides.csv found — will auto-detect only.\n";
}

// ── Process all records ──────────────────────────────────────────────────────
$records     = DavaktivRasxod::all();
$updated     = 0;
$alreadySet  = 0;
$unmatched   = [];

foreach ($records as $record) {
    $detected = detectDistrict(
        $record->details,
        $record->article,
        $districtPatterns,
        $categoryDistrictFallbacks,
        $normalizeText
    );

    // Apply manual override if no auto-detection
    if ($detected === null && $record->doc_number && isset($overrides[$record->doc_number])) {
        $detected = $overrides[$record->doc_number];
    }

    $newDistrict = $detected; // null means unmatched — leave NULL in DB (Бошқа in view)

    if ($record->district !== $newDistrict) {
        $record->district = $newDistrict;
        $record->save();
        $updated++;
    } else {
        $alreadySet++;
    }

    if ($newDistrict === null) {
        $unmatched[] = [
            'doc_number' => $record->doc_number,
            'category'   => $record->article,
            'district'   => '',
            'amount'     => $record->amount,
            'recipient'  => mb_substr($record->recipient_name ?? '', 0, 60),
        ];
    }
}

echo "\n=== DISTRICT UPDATE COMPLETE ===\n";
echo "Records updated : {$updated}\n";
echo "Already correct : {$alreadySet}\n";
echo "Still unmatched : " . count($unmatched) . " (district=NULL → shown as Бошқа)\n";

// ── Write / refresh override CSV ─────────────────────────────────────────────
// Sort unmatched by amount desc so most important are at top
usort($unmatched, fn($a, $b) => $b['amount'] <=> $a['amount']);

$csvLines = ["doc_number;category;district;amount;recipient_name"];
foreach ($unmatched as $r) {
    $csvLines[] = implode(';', [
        $r['doc_number'],
        $r['category'],
        $r['district'],  // BLANK — user fills this in
        number_format($r['amount'], 2, '.', ''),
        $r['recipient'],
    ]);
}

file_put_contents($overridePath, implode("\n", $csvLines));

echo "\n✅ Override CSV written to: {$overridePath}\n";
echo "   → Open this file, fill in the 'district' column (col 3) for each row,\n";
echo "     save it, then re-run:  php update_districts.php\n\n";

// ── Summary by category ──────────────────────────────────────────────────────
$byCategory = [];
foreach ($unmatched as $r) {
    $cat = $r['category'];
    if (!isset($byCategory[$cat])) $byCategory[$cat] = ['count' => 0, 'total' => 0.0];
    $byCategory[$cat]['count']++;
    $byCategory[$cat]['total'] += $r['amount'];
}
uasort($byCategory, fn($a, $b) => $b['total'] <=> $a['total']);
echo "--- Unmatched records by category ---\n";
foreach ($byCategory as $cat => $info) {
    printf("  %-55s  count: %3d  total: %s\n", $cat, $info['count'], number_format($info['total'], 2));
}
