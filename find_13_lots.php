<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Finding the 13 lots that total 21.57 млрд ===\n\n";

// Get ALL муддатли эмас lots with qoldiq (excluding "Лот якунланди")
$allLots = DB::table('yer_sotuvlar')
    ->where('tolov_turi', 'муддатли эмас')
    ->whereNotNull('holat')
    ->where('holat', 'not like', '%Лот якунланди%') // Exclude completed
    ->whereRaw('(
        (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0) - COALESCE(auksion_harajati, 0))
        >= COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0) - 0.01
    )')
    ->select('lot_raqami', 'holat', 'golib_tolagan', 'shartnoma_summasi', 'auksion_harajati')
    ->get();

echo "Total муддатли эмас lots with qoldiq (excluding Лот якунланди): " . $allLots->count() . "\n\n";

$totalQoldiq = 0;
$lots = [];

foreach ($allLots as $lot) {
    $expected = ($lot->golib_tolagan ?? 0) + ($lot->shartnoma_summasi ?? 0) - ($lot->auksion_harajati ?? 0);
    $received = DB::table('fakt_tolovlar')
        ->where('lot_raqami', $lot->lot_raqami)
        ->sum('tolov_summa');
    $qoldiq = $expected - $received;

    $lots[] = [
        'lot_raqami' => $lot->lot_raqami,
        'holat' => $lot->holat,
        'qoldiq' => $qoldiq
    ];

    $totalQoldiq += $qoldiq;
}

// Sort by qoldiq descending
usort($lots, function($a, $b) {
    return $b['qoldiq'] <=> $a['qoldiq'];
});

echo "All lots sorted by qoldiq:\n";
echo str_repeat("-", 100) . "\n";
foreach ($lots as $lot) {
    echo sprintf(
        "%-10s | %-70s | %15s\n",
        $lot['lot_raqami'],
        substr($lot['holat'], 0, 70),
        number_format($lot['qoldiq'] / 1000000000, 2) . ' млрд'
    );
}

echo str_repeat("-", 100) . "\n";
echo "Total qoldiq: " . number_format($totalQoldiq / 1000000000, 2) . " млрд\n";
echo "Total lots: " . count($lots) . "\n";
