<?php

use App\Http\Controllers\YerSotuvController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [YerSotuvController::class, 'index'])->name('yer-sotuvlar.index');
Route::get('/svod3', [YerSotuvController::class, 'svod3'])->name('yer-sotuvlar.svod3');
Route::get('/ruyxat', [YerSotuvController::class, 'list'])->name('yer-sotuvlar.list');

Route::get('/yer/{lot_raqami}', [YerSotuvController::class, 'show'])->name('yer-sotuvlar.show');
// Add these routes to web.php
Route::put('/yer/{lot_raqami}', [YerSotuvController::class, 'update'])->name('yer-sotuvlar.update');
Route::get('/yer/{lot_raqami}/edit', [YerSotuvController::class, 'edit'])->name('yer-sotuvlar.edit');

Route::get('/debug-davaktiv', function() {

    echo "<h1>DEBUG: Davaktivda Turgan Ma'lumotlar</h1>";

    echo "<h2>1. HOLAT (34) bo'yicha ma'lumotlar</h2>";

    $data = DB::table('yer_sotuvlar')
        ->select('lot_raqami', 'tuman', 'holat', 'asos', 'davaktivda_turgan', 'sotilgan_narx')
        ->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%')
        ->get();

    echo "<p><strong>Jami:</strong> {$data->count()} ta</p>";

    $jamiDavaktiv = $data->sum('davaktivda_turgan');
    $jamiSotilgan = $data->sum('sotilgan_narx');

    echo "<p><strong>Jami davaktivda_turgan:</strong> " . number_format($jamiDavaktiv, 2) . " so'm</p>";
    echo "<p><strong>Jami davaktivda_turgan (mlrd):</strong> " . number_format($jamiDavaktiv / 1000000000, 3) . " млрд</p>";
    echo "<p><strong>Jami sotilgan_narx:</strong> " . number_format($jamiSotilgan, 2) . " so'm</p>";

    echo "<h2>2. Tuman bo'yicha</h2>";

    $byTuman = DB::table('yer_sotuvlar')
        ->select('tuman',
                 DB::raw('COUNT(*) as count'),
                 DB::raw('SUM(davaktivda_turgan) as jami_davaktiv'),
                 DB::raw('SUM(sotilgan_narx) as jami_sotilgan'))
        ->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%')
        ->groupBy('tuman')
        ->orderBy('count', 'desc')
        ->get();

    echo "<table border='1' style='width: 100%; font-size: 12px;'>";
    echo "<tr>";
    echo "<th>Tuman</th>";
    echo "<th>Count</th>";
    echo "<th>Jami Davaktiv (so'm)</th>";
    echo "<th>Jami Davaktiv (млрд)</th>";
    echo "<th>Jami Sotilgan (млрд)</th>";
    echo "</tr>";

    foreach($byTuman as $t) {
        echo "<tr>";
        echo "<td>{$t->tuman}</td>";
        echo "<td><strong>{$t->count}</strong></td>";
        echo "<td>" . number_format($t->jami_davaktiv, 0) . "</td>";
        echo "<td><strong>" . number_format($t->jami_davaktiv / 1000000000, 3) . "</strong></td>";
        echo "<td>" . number_format($t->jami_sotilgan / 1000000000, 3) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<h2>3. Batafsil ma'lumot (barcha 17 ta)</h2>";

    echo "<table border='1' style='width: 100%; font-size: 11px;'>";
    echo "<tr>";
    echo "<th>№</th>";
    echo "<th>Lot</th>";
    echo "<th>Tuman</th>";
    echo "<th>Holat</th>";
    echo "<th>Davaktivda (so'm)</th>";
    echo "<th>Davaktivda (млрд)</th>";
    echo "<th>Sotilgan (млрд)</th>";
    echo "</tr>";

    $i = 1;
    foreach($data as $d) {
        $highlight = ($d->davaktivda_turgan > 0) ? 'background: yellow;' : '';
        echo "<tr style='{$highlight}'>";
        echo "<td>{$i}</td>";
        echo "<td>{$d->lot_raqami}</td>";
        echo "<td>{$d->tuman}</td>";
        echo "<td>" . substr($d->holat, 0, 40) . "...</td>";
        echo "<td>" . number_format($d->davaktivda_turgan, 0) . "</td>";
        echo "<td><strong>" . number_format($d->davaktivda_turgan / 1000000000, 3) . "</strong></td>";
        echo "<td>" . number_format($d->sotilgan_narx / 1000000000, 3) . "</td>";
        echo "</tr>";
        $i++;
    }
    echo "</table>";

    echo "<h2>4. NULL yoki 0 bo'lganlar</h2>";

    $nullCount = DB::table('yer_sotuvlar')
        ->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%')
        ->where(function($q) {
            $q->whereNull('davaktivda_turgan')
              ->orWhere('davaktivda_turgan', 0);
        })
        ->count();

    echo "<p><strong>NULL yoki 0:</strong> {$nullCount} ta</p>";

    $nonZeroCount = DB::table('yer_sotuvlar')
        ->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%')
        ->where('davaktivda_turgan', '>', 0)
        ->count();

    echo "<p><strong>0 dan katta:</strong> {$nonZeroCount} ta</p>";

    echo "<h2>5. Controller metodidan olingan ma'lumot</h2>";

    $controllerData = App\Models\YerSotuv::where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%')
        ->selectRaw('
            COUNT(*) as soni,
            SUM(davaktivda_turgan) as auksion_mablagh
        ')->first();

    echo "<p><strong>Soni:</strong> {$controllerData->soni}</p>";
    echo "<p><strong>Auksion mablagh (so'm):</strong> " . number_format($controllerData->auksion_mablagh, 0) . "</p>";
    echo "<p><strong>Auksion mablagh (млрд):</strong> " . number_format($controllerData->auksion_mablagh / 1000000000, 3) . "</p>";

    die();
});



Route::get('/debug-mulk-qabul', function() {

    echo "<h2>1. UMUMIY MA'LUMOT (holat va asos bo'yicha)</h2>";

    // Barcha holat variantlari
    $holatlar = DB::table('yer_sotuvlar')
        ->select('holat', DB::raw('COUNT(*) as count'))
        ->whereNotNull('holat')
        ->groupBy('holat')
        ->orderBy('count', 'desc')
        ->get();

    echo "<table border='1'>";
    echo "<tr><th>Holat</th><th>Count</th></tr>";
    foreach($holatlar as $h) {
        echo "<tr><td>{$h->holat}</td><td>{$h->count}</td></tr>";
    }
    echo "</table><br>";

    echo "<h2>2. ASOS VARIANTLARI</h2>";

    $asoslar = DB::table('yer_sotuvlar')
        ->select('asos', DB::raw('COUNT(*) as count'))
        ->whereNotNull('asos')
        ->groupBy('asos')
        ->orderBy('count', 'desc')
        ->get();

    echo "<table border='1'>";
    echo "<tr><th>Asos</th><th>Count</th></tr>";
    foreach($asoslar as $a) {
        echo "<tr><td>{$a->asos}</td><td>{$a->count}</td></tr>";
    }
    echo "</table><br>";

    echo "<h2>3. HOLAT + ASOS KOMBINATSIYASI</h2>";

    // Holat (34) va ПФ-135 bilan
    $query1 = DB::table('yer_sotuvlar')
        ->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%')
        ->where('asos', 'like', '%ПФ-135%')
        ->count();

    echo "<p><strong>ANIQ:</strong> holat LIKE '%Ishtirokchi roziligini kutish jarayonida (34)%' AND asos LIKE '%ПФ-135%'</p>";
    echo "<p>COUNT: <strong style='color: red; font-size: 20px;'>{$query1}</strong></p>";

    // Faqat (34)
    $query2 = DB::table('yer_sotuvlar')
        ->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida (34)%')
        ->count();

    echo "<p><strong>FAQAT (34):</strong> holat LIKE '%Ishtirokchi roziligini kutish jarayonida (34)%'</p>";
    echo "<p>COUNT: <strong style='color: blue; font-size: 20px;'>{$query2}</strong></p>";

    // Faqat ПФ-135
    $query3 = DB::table('yer_sotuvlar')
        ->where('asos', 'like', '%ПФ-135%')
        ->count();

    echo "<p><strong>FAQAT ПФ-135:</strong> asos LIKE '%ПФ-135%'</p>";
    echo "<p>COUNT: <strong style='color: green; font-size: 20px;'>{$query3}</strong></p>";

    // Ishtirokchi (har qanday variant)
    $query4 = DB::table('yer_sotuvlar')
        ->where('holat', 'like', '%Ishtirokchi%')
        ->count();

    echo "<p><strong>HAR QANDAY Ishtirokchi:</strong> holat LIKE '%Ishtirokchi%'</p>";
    echo "<p>COUNT: <strong style='color: orange; font-size: 20px;'>{$query4}</strong></p>";

    echo "<h2>4. TUMAN BO'YICHA (ПФ-135)</h2>";

    $byTuman = DB::table('yer_sotuvlar')
        ->select('tuman', DB::raw('COUNT(*) as count'))
        ->where('asos', 'like', '%ПФ-135%')
        ->groupBy('tuman')
        ->orderBy('count', 'desc')
        ->get();

    echo "<table border='1'>";
    echo "<tr><th>Tuman</th><th>Count</th></tr>";
    foreach($byTuman as $t) {
        echo "<tr><td>{$t->tuman}</td><td>{$t->count}</td></tr>";
    }
    echo "</table><br>";

    echo "<h2>5. TO'LIQ MA'LUMOT (birinchi 20 ta)</h2>";

    $full = DB::table('yer_sotuvlar')
        ->select('lot_raqami', 'tuman', 'holat', 'asos', 'davaktivda_turgan')
        ->where('asos', 'like', '%ПФ-135%')
        ->limit(20)
        ->get();

    echo "<table border='1' style='font-size: 11px;'>";
    echo "<tr><th>Lot</th><th>Tuman</th><th>Holat</th><th>Asos</th><th>Davaktivda</th></tr>";
    foreach($full as $f) {
        echo "<tr>";
        echo "<td>{$f->lot_raqami}</td>";
        echo "<td>{$f->tuman}</td>";
        echo "<td>" . substr($f->holat, 0, 50) . "...</td>";
        echo "<td>{$f->asos}</td>";
        echo "<td>" . number_format($f->davaktivda_turgan, 0) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
});
