<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\YerSotuvController;
use Illuminate\Http\Request;

$controller = app(YerSotuvController::class);

$summaryView = $controller->finXisobot(Request::create('/fin-xisobot', 'GET'));
$summaryData = $summaryView->getData();

echo "TOTAL_SUMMARY=" . number_format((float)($summaryData['totalAmount'] ?? 0), 2, '.', ',') . PHP_EOL;

echo "DISTRICTS=" . count($summaryData['districtData'] ?? []) . PHP_EOL;

$detailView = $controller->finXisobotDetails(Request::create('/fin-xisobot/details', 'GET', [
    'district' => 'Бошқа',
]));
$detailData = $detailView->getData();

echo "DETAIL_DISTRICT=" . ($detailData['selectedDistrict'] ?? '') . PHP_EOL;
echo "DETAIL_COUNT=" . ($detailData['recordCount'] ?? 0) . PHP_EOL;
echo "DETAIL_TOTAL=" . number_format((float)($detailData['totalAmount'] ?? 0), 2, '.', ',') . PHP_EOL;
