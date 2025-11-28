<?php

// Run this in Tinker: include 'debug_qoldiq_qarz_count.php';

use Illuminate\Support\Facades\DB;

echo "=== Debugging Qoldiq Qarz Filter Count ===\n\n";

// Get the exact count using the same logic as the filter
$count = DB::table('yer_sotuvlar')
    ->where('tolov_turi', 'муддатли эмас')
    ->whereNotNull('holat')
    ->where(function ($q) {
        $q->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida%')
            ->orWhere('holat', 'like', '%G`olib shartnoma imzolashga rozilik bildirdi%')
            ->orWhere('holat', 'like', '%Ишл. кечикт. туф. мулкни қабул қил. тасдиқланмаган%')
            ->orWhere('holat', 'like', '%Бекор қилинган%');
    })
    ->whereRaw('(
        (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0) - COALESCE(auksion_harajati, 0))
        >= COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0) - 0.01
    )')
    ->count();

echo "Total lots matching qoldiq_qarz filter: {$count}\n\n";

// Get the lots with details
$lots = DB::table('yer_sotuvlar')
    ->where('tolov_turi', 'муддатли эмас')
    ->whereNotNull('holat')
    ->where(function ($q) {
        $q->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida%')
            ->orWhere('holat', 'like', '%G`olib shartnoma imzolashga rozilik bildirdi%')
            ->orWhere('holat', 'like', '%Ишл. кечикт. туф. мулкни қабул қил. тасдиқланмаган%')
            ->orWhere('holat', 'like', '%Бекор қилинган%');
    })
    ->whereRaw('(
        (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0) - COALESCE(auksion_harajati, 0))
        >= COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0) - 0.01
    )')
    ->get(['lot_raqami', 'holat', 'golib_tolagan', 'shartnoma_summasi', 'auksion_harajati']);

echo "Lot details:\n";
echo str_repeat("-", 80) . "\n";
foreach ($lots as $lot) {
    $expected = ($lot->golib_tolagan ?? 0) + ($lot->shartnoma_summasi ?? 0) - ($lot->auksion_harajati ?? 0);

    $received = DB::table('fakt_tolovlar')
        ->where('lot_raqami', $lot->lot_raqami)
        ->sum('tolov_summa');

    $qoldiq = $expected - $received;

    echo "Lot: {$lot->lot_raqami}\n";
    echo "  Holat: " . substr($lot->holat, 0, 50) . (strlen($lot->holat) > 50 ? '...' : '') . "\n";
    echo "  Expected: " . number_format($expected, 2) . "\n";
    echo "  Received: " . number_format($received, 2) . "\n";
    echo "  Qoldiq: " . number_format($qoldiq, 2) . "\n";
    echo str_repeat("-", 80) . "\n";
}

echo "\n=== Summary by Status ===\n";
$statusCounts = DB::table('yer_sotuvlar')
    ->where('tolov_turi', 'муддатли эмас')
    ->whereNotNull('holat')
    ->where(function ($q) {
        $q->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida%')
            ->orWhere('holat', 'like', '%G`olib shartnoma imzolashga rozilik bildirdi%')
            ->orWhere('holat', 'like', '%Ишл. кечикт. туф. мулкни қабул қил. тасдиқланмаган%')
            ->orWhere('holat', 'like', '%Бекор қилинган%');
    })
    ->whereRaw('(
        (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0) - COALESCE(auksion_harajati, 0))
        >= COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0) - 0.01
    )')
    ->selectRaw('
        CASE
            WHEN holat LIKE "%Ishtirokchi roziligini kutish jarayonida%" THEN "Ishtirokchi roziligini kutish"
            WHEN holat LIKE "%G`olib shartnoma imzolashga rozilik bildirdi%" THEN "G`olib rozilik bildirdi"
            WHEN holat LIKE "%Ишл. кечикт. туф. мулкни қабул қил. тасдиқланмаган%" THEN "Mulkni qabul qilish"
            WHEN holat LIKE "%Бекор қилинган%" THEN "Bekor qilingan"
            ELSE "Other"
        END as status_group,
        COUNT(*) as count
    ')
    ->groupBy('status_group')
    ->get();

foreach ($statusCounts as $statusCount) {
    echo "{$statusCount->status_group}: {$statusCount->count}\n";
}

echo "\n=== Done ===\n";
