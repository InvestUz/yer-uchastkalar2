<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Finding ALL муддатли эмас lots with qoldiq ===\n\n";

// Get ALL муддатли эмас lots with positive qoldiq
$allLots = DB::table('yer_sotuvlar')
    ->where('tolov_turi', 'муддатли эмас')
    ->whereNotNull('holat')
    ->whereRaw('(
        (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0) - COALESCE(auksion_harajati, 0))
        >= COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0) - 0.01
    )')
    ->select('lot_raqami', 'holat', 'golib_tolagan', 'shartnoma_summasi', 'auksion_harajati')
    ->get();

echo "Total муддатли эмас lots with qoldiq: " . $allLots->count() . "\n\n";

$matchedCount = 0;
$unmatchedCount = 0;

echo "MATCHED (in filter):\n";
echo str_repeat("-", 80) . "\n";
foreach ($allLots as $lot) {
    $holat = $lot->holat;
    $matches =
        stripos($holat, 'Ishtirokchi roziligini kutish jarayonida') !== false ||
        stripos($holat, 'G`olib shartnoma imzolashga rozilik bildirdi') !== false ||
        stripos($holat, 'Ишл. кечикт. туф. мулкни қабул қил. тасдиқланмаған') !== false ||
        stripos($holat, 'Бекор қилинған') !== false;

    if ($matches) {
        $matchedCount++;
        echo "{$lot->lot_raqami} => {$holat}\n";
    }
}

echo "\nNOT MATCHED (should be added to filter?):\n";
echo str_repeat("-", 80) . "\n";
foreach ($allLots as $lot) {
    $holat = $lot->holat;
    $matches =
        stripos($holat, 'Ishtirokchi roziligini kutish jarayonida') !== false ||
        stripos($holat, 'G`olib shartnoma imzolashga rozilik bildirdi') !== false ||
        stripos($holat, 'Ишл. кечикт. туф. мулкни қабул қил. тасдиқланмаған') !== false ||
        stripos($holat, 'Бекор қилинған') !== false;

    if (!$matches) {
        $unmatchedCount++;
        $expected = ($lot->golib_tolagan ?? 0) + ($lot->shartnoma_summasi ?? 0) - ($lot->auksion_harajati ?? 0);
        $received = DB::table('fakt_tolovlar')
            ->where('lot_raqami', $lot->lot_raqami)
            ->sum('tolov_summa');
        $qoldiq = $expected - $received;

        echo "{$lot->lot_raqami} => {$holat}\n";
        echo "  Qoldiq: " . number_format($qoldiq, 2) . "\n";
    }
}

echo "\n=== Summary ===\n";
echo "Matched: {$matchedCount}\n";
echo "Unmatched: {$unmatchedCount}\n";
echo "Total: " . $allLots->count() . "\n";
