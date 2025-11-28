<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking lots 19092338 and 19227515 ===\n\n";

$lots = DB::table('yer_sotuvlar')
    ->whereIn('lot_raqami', ['19092338', '19227515'])
    ->select('lot_raqami', 'tolov_turi', 'holat', 'golib_tolagan', 'shartnoma_summasi', 'auksion_harajati')
    ->get();

foreach ($lots as $lot) {
    echo "Lot: {$lot->lot_raqami}\n";
    echo "  Tolov turi: {$lot->tolov_turi}\n";
    echo "  Holat: {$lot->holat}\n";

    $expected = ($lot->golib_tolagan ?? 0) + ($lot->shartnoma_summasi ?? 0) - ($lot->auksion_harajati ?? 0);
    $received = DB::table('fakt_tolovlar')
        ->where('lot_raqami', $lot->lot_raqami)
        ->sum('tolov_summa');
    $qoldiq = $expected - $received;

    echo "  Expected: " . number_format($expected) . "\n";
    echo "  Received: " . number_format($received) . "\n";
    echo "  Qoldiq: " . number_format($qoldiq) . " (" . number_format($qoldiq / 1000000000, 2) . " млрд)\n";
    echo "\n";
}
