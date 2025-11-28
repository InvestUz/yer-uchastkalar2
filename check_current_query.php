<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking current qoldiq_qarz query result ===\n\n";

$lots = DB::table('yer_sotuvlar')
    ->where('tolov_turi', 'муддатли эмас')
    ->whereNotNull('holat')
    ->where(function ($q) {
        $q->where(function ($sq) {
            $sq->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida%')
                ->orWhere('holat', 'like', '%G`olib shartnoma imzolashga rozilik bildirdi%')
                ->orWhere('holat', 'like', '%Ишл. кечикт. туф. мулкни қабул қил. тасдиқланмаған%')
                ->orWhere('holat', 'like', '%Бекор қилинган%')
                ->orWhere('holat', 'like', '%Иштирокчи ва Буюртмачи келишуви%');
        })
        ->orWhereIn('lot_raqami', ['19092338', '19227515']);
    })
    ->whereRaw('(
        (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0) - COALESCE(auksion_harajati, 0))
        >= COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0) - 0.01
    )')
    ->select('lot_raqami', 'holat', 'golib_tolagan', 'shartnoma_summasi', 'auksion_harajati')
    ->get();

echo "Total lots: " . $lots->count() . "\n\n";

$totalQoldiq = 0;
foreach ($lots as $lot) {
    $expected = ($lot->golib_tolagan ?? 0) + ($lot->shartnoma_summasi ?? 0) - ($lot->auksion_harajati ?? 0);
    $received = DB::table('fakt_tolovlar')
        ->where('lot_raqami', $lot->lot_raqami)
        ->sum('tolov_summa');
    $qoldiq = $expected - $received;
    $totalQoldiq += $qoldiq;

    echo sprintf(
        "%-10s | %-50s | %12s\n",
        $lot->lot_raqami,
        substr($lot->holat, 0, 50),
        number_format($qoldiq / 1000000000, 2) . ' млрд'
    );
}

echo "\nTotal qoldiq: " . number_format($totalQoldiq / 1000000000, 2) . " млрд\n";
