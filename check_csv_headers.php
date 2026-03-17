<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$csvPath = storage_path('app/excel/davaktiv_rasxod.csv');
$handle = fopen($csvPath, 'r');
$headers = fgetcsv($handle, 0, ';');
echo "=== CSV HEADERS ===\n";
foreach ($headers as $i => $h) {
    echo "  [$i] " . trim($h) . "\n";
}
echo "\n=== FIRST 5 DATA ROWS ===\n";
for ($i = 0; $i < 5; $i++) {
    $row = fgetcsv($handle, 0, ';');
    if (!$row) break;
    echo "\nROW " . ($i + 1) . ":\n";
    foreach ($headers as $idx => $h) {
        $val = trim($row[$idx] ?? '');
        echo "  [" . trim($h) . "] => " . $val . "\n";
    }
}
fclose($handle);
