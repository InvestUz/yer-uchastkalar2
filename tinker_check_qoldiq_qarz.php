<?php

/**
 * Tinker Script to Check "Аукционда турган маблағ" Calculation
 *
 * Run in Laravel Tinker:
 * php artisan tinker
 * include 'tinker_check_qoldiq_qarz.php';
 */

echo "========================================\n";
echo "Checking МУДДАТЛИ ЭМАС with QOLDIQ QARZ\n";
echo "========================================\n\n";

// Date filters (default: from 2024-01-01 to today)
$dateFrom = '2024-01-01';
$dateTo = now()->toDateString();

echo "Date Range: {$dateFrom} to {$dateTo}\n\n";

// Step 1: Get all муддатли эмас lots (WITHOUT bekor exclusion - NEW LOGIC)
echo "--- STEP 1: Get ALL муддатли эмас lots (INCLUDING Бекор қилинган) ---\n";

$allMuddatliEmas = DB::table('yer_sotuvlar')
    ->where('tolov_turi', 'муддатли эмас')
    ->whereNotNull('holat')
    ->whereBetween('auksion_sana', [$dateFrom, $dateTo])
    ->get();

echo "Total муддатли эмас lots (all): " . $allMuddatliEmas->count() . "\n\n";

// Step 2: Filter by qoldiq_qarz statuses (INCLUDING Бекор қилинган)
echo "--- STEP 2: Filter by qoldiq_qarz statuses ---\n";
echo "Allowed statuses:\n";
echo "  1. Ishtirokchi roziligini kutish jarayonida\n";
echo "  2. G`olib shartnoma imzolashga rozilik bildirdi\n";
echo "  3. Ишл. кечикт. туф. мулкни қабул қил. тасдиқланмаган\n";
echo "  4. Бекор қилинган (NEW - ADDED)\n\n";

$qoldiqQarzLots = DB::table('yer_sotuvlar')
    ->where('tolov_turi', 'муддатли эмас')
    ->whereNotNull('holat')
    ->whereBetween('auksion_sana', [$dateFrom, $dateTo])
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
    ->get();

echo "Lots with qoldiq_qarz: " . $qoldiqQarzLots->count() . "\n\n";

// Step 3: Show detailed lot information
echo "--- STEP 3: Detailed lot information ---\n\n";

$totalExpected = 0;
$totalReceived = 0;
$totalQoldiq = 0;

foreach ($qoldiqQarzLots as $lot) {
    // Calculate expected
    $expected = ($lot->golib_tolagan ?? 0) + ($lot->shartnoma_summasi ?? 0) - ($lot->auksion_harajati ?? 0);

    // Get received amount
    $received = DB::table('fakt_tolovlar')
        ->where('lot_raqami', $lot->lot_raqami)
        ->sum('tolov_summa');

    $qoldiq = $expected - $received;

    $totalExpected += $expected;
    $totalReceived += $received;
    $totalQoldiq += $qoldiq;

    echo "Lot: {$lot->lot_raqami}\n";
    echo "  Holat: {$lot->holat}\n";
    echo "  Expected: " . number_format($expected / 1000000000, 2) . " млрд\n";
    echo "  Received: " . number_format($received / 1000000000, 2) . " млрд\n";
    echo "  Qoldiq: " . number_format($qoldiq / 1000000000, 2) . " млрд\n";
    echo "  ---\n";
}

echo "\n========================================\n";
echo "SUMMARY (АУКЦИОНДА ТУРГАН МАБЛАҒ)\n";
echo "========================================\n";
echo "Total lots: " . $qoldiqQarzLots->count() . "\n";
echo "Total Expected: " . number_format($totalExpected / 1000000000, 2) . " млрд сўм\n";
echo "Total Received: " . number_format($totalReceived / 1000000000, 2) . " млрд сўм\n";
echo "Total Qoldiq: " . number_format($totalQoldiq / 1000000000, 2) . " млрд сўм\n";
echo "========================================\n\n";

// Step 4: Check monitoring calculation
echo "--- STEP 4: Compare with MonitoringService calculation ---\n";

$monitoringService = app(\App\Services\YerSotuvMonitoringService::class);
$yerSotuvService = app(\App\Services\YerSotuvService::class);

$dateFilters = [
    'auksion_sana_from' => $dateFrom,
    'auksion_sana_to' => $dateTo,
];

$tumanlar = $monitoringService->getTumanlar();
$monitoringStatistics = [];

foreach ($tumanlar as $tuman) {
    $tumanPatterns = $yerSotuvService->getTumanPatterns($tuman);
    $stat = $monitoringService->calculateTumanStatistics($tumanPatterns, $dateFilters, false);
    $stat['tuman'] = $tuman;
    $monitoringStatistics[] = $stat;
}

$jami = $monitoringService->calculateJamiTotals($monitoringStatistics);

echo "\nMonitoring calculation:\n";
echo "  biryola_tushadigan: " . number_format($jami['biryola_tushadigan'] / 1000000000, 2) . " млрд\n";
echo "  biryola_tushgan: " . number_format($jami['biryola_tushgan'] / 1000000000, 2) . " млрд\n";
echo "  Difference (Аукционда турган): " . number_format(($jami['biryola_tushadigan'] - $jami['biryola_tushgan']) / 1000000000, 2) . " млрд\n";
echo "\n========================================\n";
echo "✅ Script completed!\n";
echo "========================================\n";
