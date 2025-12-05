<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Yer2SotuvSeeder extends Seeder
{
    private const BATCH_SIZE = 50;
    private const MEMORY_LIMIT = '512M';

    private $oyNomlari = [
        1 => 'yanvar',
        2 => 'fevral',
        3 => 'mart',
        4 => 'aprel',
        5 => 'may',
        6 => 'iyun',
        7 => 'iyul',
        8 => 'avgust',
        9 => 'sentabr',
        10 => 'oktabr',
        11 => 'noyabr',
        12 => 'dekabr'
    ];

    private $grafikColumnMap = [
        2022 => [1 => 51, 2 => 52, 3 => 53, 4 => 54, 5 => 55, 6 => 56, 7 => 57, 8 => 58, 9 => 59, 10 => 60, 11 => 61, 12 => 62],
        2023 => [1 => 63, 2 => 64, 3 => 65, 4 => 66, 5 => 67, 6 => 68, 7 => 69, 8 => 70, 9 => 71, 10 => 72, 11 => 73, 12 => 74],
        2024 => [1 => 75, 2 => 76, 3 => 77, 4 => 78, 5 => 79, 6 => 80, 7 => 81, 8 => 82, 9 => 83, 10 => 84, 11 => 85, 12 => 86],
        2025 => [1 => 87, 2 => 88, 3 => 89, 4 => 90, 5 => 91, 6 => 92, 7 => 93, 8 => 94, 9 => 95, 10 => 96, 11 => 97, 12 => 98],
        2026 => [1 => 99, 2 => 100, 3 => 101, 4 => 102, 5 => 103, 6 => 104, 7 => 105, 8 => 106, 9 => 107, 10 => 108, 11 => 109, 12 => 110],
        2027 => [1 => 111, 2 => 112, 3 => 113, 4 => 114, 5 => 115, 6 => 116, 7 => 117, 8 => 118, 9 => 119, 10 => 120, 11 => 121, 12 => 122],
        2028 => [1 => 123, 2 => 124, 3 => 125, 4 => 126, 5 => 127, 6 => 128, 7 => 129, 8 => 130, 9 => 131, 10 => 132, 11 => 133, 12 => 134],
        2029 => [1 => 135, 2 => 136, 3 => 137, 4 => 138, 5 => 139, 6 => 140, 7 => 141, 8 => 142, 9 => 143, 10 => 144, 11 => 145, 12 => 146]
    ];

    private $notFoundLots = [];
    private $skippedRecords = [];
    private $logFileName;
    private $grafikBatch = [];
    private $faktBatch = [];
    private $importErrors = [];

    private $yerSotuvTable = 'yer_sotuvlar';
    private $grafikTable = 'grafik_tolov';
    private $faktTable = 'fakt_tolov';

    public function run(): void
    {
        ini_set('memory_limit', self::MEMORY_LIMIT);

        $this->logFileName = 'seeder_logs/import_' . now()->format('Y-m-d_H-i-s') . '.log';
        $this->writeLog("=== YER SOTUV IMPORT - CSV FORMAT ===");
        $this->writeLog("Boshlandi: " . now()->format('Y-m-d H:i:s'));
        $this->writeLog(str_repeat("=", 80));

        $this->detectTableNames();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->command->info("Ma'lumotlar tozalanmoqda...");
        DB::table($this->faktTable)->truncate();
        DB::table($this->grafikTable)->truncate();
        DB::table($this->yerSotuvTable)->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info("Import boshlanmoqda...");
        $startTime = microtime(true);

        try {
            $this->importAsosiyMalumot();
            $this->flushGrafikBatch();

            $this->importFaktTolovlar();
            $this->flushFaktBatch();

            $duration = round(microtime(true) - $startTime, 2);

            $this->writeFinalSummary($duration);
            $this->showVerificationStatistics();

            if (!empty($this->notFoundLots)) {
                $this->command->warn("\n=== Topilmagan LOT raqamlar ===");
                $count = min(10, count($this->notFoundLots));
                for ($i = 0; $i < $count; $i++) {
                    $this->command->error("LOT {$this->notFoundLots[$i]}");
                }
                if (count($this->notFoundLots) > 10) {
                    $this->command->warn("... va yana " . (count($this->notFoundLots) - 10) . " ta");
                }
            }

            $this->command->info("\n✓ Import muvaffaqiyatli yakunlandi! ({$duration}s)");
            $this->command->info("✓ Log: storage/app/{$this->logFileName}");
        } catch (\Exception $e) {
            $this->command->error("\n✗ XATOLIK: " . $e->getMessage());
            $this->writeLog("\nXATOLIK: " . $e->getMessage());
            throw $e;
        }
    }

    private function detectTableNames(): void
    {
        if (Schema::hasTable('yer_sotuv')) {
            $this->yerSotuvTable = 'yer_sotuv';
        }

        if (Schema::hasTable('grafik_tolovs')) {
            $this->grafikTable = 'grafik_tolovs';
        } elseif (Schema::hasTable('grafik_tolovlar')) {
            $this->grafikTable = 'grafik_tolovlar';
        }

        if (Schema::hasTable('fakt_tolovs')) {
            $this->faktTable = 'fakt_tolovs';
        } elseif (Schema::hasTable('fakt_tolovlar')) {
            $this->faktTable = 'fakt_tolovlar';
        }

        $this->writeLog("Table names: {$this->yerSotuvTable}, {$this->grafikTable}, {$this->faktTable}");
    }
    private function importAsosiyMalumot(): void
    {
        $file = storage_path('app/excel/new_data.csv');

        if (!file_exists($file)) {
            throw new \RuntimeException("Fayl topilmadi: $file");
        }

        $this->command->info("Fayl topildi: new_data.csv");
        $this->command->info("CSV fayl yuklanmoqda...");

        $handle = fopen($file, 'r');
        if ($handle === false) {
            throw new \RuntimeException("CSV faylni ochib bo'lmadi");
        }

        $headers = fgetcsv($handle, 0, ';');
        $this->command->info("CSV ustunlar: " . count($headers));
        $this->writeLog("CSV columns: " . count($headers));

        $totalRows = 0;
        while (fgets($handle) !== false) {
            $totalRows++;
        }
        rewind($handle);
        fgetcsv($handle, 0, ';');

        $this->command->info("Jami {$totalRows} ta qator topildi.");
        $this->writeLog("Jami qatorlar: {$totalRows}");

        $bar = $this->command->getOutput()->createProgressBar($totalRows);
        $bar->start();

        $count = 0;
        $rowNumber = 1;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $rowNumber++;

            if (empty(array_filter($row))) {
                $bar->advance();
                continue;
            }

            // FIX: Use column 2 (index 1) "Лотрақами" as the actual LOT number
            $lotRaqami = $this->parseLotNumber($row[1] ?? null);

            if (!$lotRaqami) {
                $this->skippedRecords[] = "Qator {$rowNumber}: LOT topilmadi";
                $bar->advance();
                continue;
            }

            try {
                DB::beginTransaction();

                $yerSotuvId = $this->createYerSotuv($row, $lotRaqami, $rowNumber);

                if ($yerSotuvId) {
                    $this->createGrafikTolovlar($row, $yerSotuvId, $lotRaqami);
                    $count++;
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->importErrors[] = "Qator {$rowNumber}, LOT {$lotRaqami}: " . $e->getMessage();
                $this->writeLog("XATOLIK qator {$rowNumber}: " . $e->getMessage());
            }

            $bar->advance();

            if ($count % 100 === 0) {
                gc_collect_cycles();
            }
        }

        fclose($handle);

        $bar->finish();
        $this->command->newLine(2);
        $this->command->info("✓ Jami {$count} ta lot yuklandi!");
        $this->writeLog("Yuklangan: {$count} ta lot");
    }

  private function createYerSotuv($row, $lotRaqami, $rowNumber): ?int
{
    $auksionSana = $this->parseDate($row[15] ?? null);
    $shartnomaSana = $this->parseDate($row[26] ?? null);

    $data = [
        'lot_raqami' => $lotRaqami,
        'tuman' => $this->cleanValue($row[2] ?? null),
        'mfy' => $this->cleanValue($row[3] ?? null),
        'manzil' => $this->cleanValue($row[3] ?? null),
        'unikal_raqam' => $this->cleanValue($row[4] ?? null),
        'zona' => $this->cleanValue($row[5] ?? null),
        'bosh_reja_zona' => $this->cleanValue($row[6] ?? null),
        'yangi_ozbekiston' => $this->cleanValue($row[7] ?? null),
        'maydoni' => $this->parseNumber($row[8] ?? null),
        'lokatsiya' => $this->cleanValue($row[9] ?? null),
        'qurilish_turi_1' => $this->cleanValue($row[10] ?? null),
        'qurilish_turi_2' => $this->cleanValue($row[11] ?? null),
        'qurilish_maydoni' => $this->parseNumber($row[12] ?? null),
        'investitsiya' => $this->parseNumber($row[13] ?? null),
        'boshlangich_narx' => $this->parseNumber($row[14] ?? null),
        'auksion_sana' => $auksionSana ? Carbon::parse($auksionSana) : null,
        'sotilgan_narx' => $this->parseNumber($row[16] ?? null),
        'auksion_golibi' => $this->cleanValue($row[17] ?? null),
        'golib_turi' => $this->cleanValue($row[18] ?? null),
        'golib_nomi' => $this->cleanValue($row[19] ?? null),
        'telefon' => $this->cleanValue($row[20] ?? null),
        'tolov_turi' => $this->cleanValue($row[21] ?? null),
        'asos' => $this->cleanValue($row[22] ?? null),
        'auksion_turi' => $this->cleanValue($row[23] ?? null),
        'holat' => $this->cleanValue($row[24] ?? null),
        'shartnoma_holati' => $this->cleanValue($row[25] ?? null),
        'shartnoma_sana' => $shartnomaSana,
        'shartnoma_raqam' => $this->cleanValue($row[27] ?? null),
        'golib_tolagan' => $this->parseNumber($row[28] ?? null),
        'buyurtmachiga_otkazilgan' => $this->parseNumber($row[29] ?? null),
        'chegirma' => $this->parseNumber($row[30] ?? null),
        'auksion_harajati' => $this->parseNumber($row[31] ?? null),
        'tushadigan_mablagh' => $this->parseNumber($row[32] ?? null),
        'davaktiv_jamgarmasi' => $this->parseNumber($row[33] ?? null),
        'shartnoma_tushgan' => $this->parseNumber($row[34] ?? null),
        'davaktivda_turgan' => $this->parseNumber($row[35] ?? null),
        'yer_auksion_harajat' => $this->parseNumber($row[36] ?? null),
        'mahalliy_byudjet_tushadigan' => $this->parseNumber($row[37] ?? null),
        'jamgarma_tushadigan' => $this->parseNumber($row[38] ?? null),
        'yangi_oz_direksiya_tushadigan' => $this->parseNumber($row[39] ?? null),
        'shayxontohur_tushadigan' => $this->parseNumber($row[40] ?? null),
        'mahalliy_byudjet_taqsimlangan' => $this->parseNumber($row[41] ?? null),
        'jamgarma_taqsimlangan' => $this->parseNumber($row[42] ?? null),
        'yangi_oz_direksiya_taqsimlangan' => $this->parseNumber($row[43] ?? null),
        'shayxontohur_taqsimlangan' => $this->parseNumber($row[44] ?? null),
        'qoldiq_mahalliy_byudjet' => $this->parseNumber($row[45] ?? null),
        'qoldiq_jamgarma' => $this->parseNumber($row[46] ?? null),
        'qoldiq_yangi_oz_direksiya' => $this->parseNumber($row[47] ?? null),
        'qoldiq_shayxontohur' => $this->parseNumber($row[48] ?? null),
        'farqi' => $this->parseNumber($row[49] ?? null),
        'yil' => $auksionSana ? Carbon::parse($auksionSana)->year : date('Y')
    ];

    // CRITICAL FIX: Calculate shartnoma_summasi correctly
    $shartnomaSummasi = $this->calculateShartnomaSummasiFromGrafik($row);
    $data['shartnoma_summasi'] = $shartnomaSummasi;

    // Add logging for verification in production
    if ($shartnomaSummasi > 0) {
        Log::info("LOT {$lotRaqami} shartnoma_summasi calculated", [
            'lot' => $lotRaqami,
            'shartnoma_summasi' => $shartnomaSummasi,
            'tolov_turi' => $data['tolov_turi']
        ]);
    }

    $tableColumns = Schema::getColumnListing($this->yerSotuvTable);
    $filteredData = array_filter($data, function ($key) use ($tableColumns) {
        return in_array($key, $tableColumns);
    }, ARRAY_FILTER_USE_KEY);

    $filteredData['created_at'] = now();
    $filteredData['updated_at'] = now();

    $id = DB::table($this->yerSotuvTable)->insertGetId($filteredData);

    return $id;
}

private function calculateShartnomaSummasiFromGrafik($row): float
{
    // Column 51 (index 50): "Шартнома бўйича тушадиган"
    $explicitContractAmount = $this->parseNumber($row[50] ?? null) ?? 0;

    // Sum all scheduled payments from columns 51-146 (months 2022-2029)
    $paymentScheduleSum = 0;

    foreach ($this->grafikColumnMap as $yil => $oylar) {
        foreach ($oylar as $oy => $csvIndex) {
            if (isset($row[$csvIndex])) {
                $summa = $this->parseNumber($row[$csvIndex]);
                if ($summa !== null && $summa > 0) {
                    $paymentScheduleSum += $summa;
                }
            }
        }
    }

    // Use maximum of explicit amount or calculated schedule sum
    // This handles cases where schedule might have extra payments
    $finalAmount = max($explicitContractAmount, $paymentScheduleSum);

    return $finalAmount;
}

    private function createGrafikTolovlar($row, $yerSotuvId, $lotRaqami): void
    {
        $monthsWithData = [];
        $firstPaymentMonth = null;
        $lastPaymentMonth = null;

        foreach ($this->grafikColumnMap as $yil => $oylar) {
            foreach ($oylar as $oy => $csvIndex) {
                $summa = $this->parseNumber($row[$csvIndex] ?? null);

                if ($summa !== null && $summa > 0) {
                    $currentMonth = Carbon::create($yil, $oy, 1);
                    $monthsWithData[] = [
                        'yil' => $yil,
                        'oy' => $oy,
                        'summa' => $summa
                    ];

                    if ($firstPaymentMonth === null || $currentMonth->lt($firstPaymentMonth)) {
                        $firstPaymentMonth = $currentMonth;
                    }
                    if ($lastPaymentMonth === null || $currentMonth->gt($lastPaymentMonth)) {
                        $lastPaymentMonth = $currentMonth;
                    }
                }
            }
        }

        if (empty($monthsWithData)) {
            return;
        }

        $currentDate = $firstPaymentMonth->copy();

        while ($currentDate->lte($lastPaymentMonth)) {
            $yil = $currentDate->year;
            $oy = $currentDate->month;

            $summa = 0;
            foreach ($monthsWithData as $monthData) {
                if ($monthData['yil'] == $yil && $monthData['oy'] == $oy) {
                    $summa = $monthData['summa'];
                    break;
                }
            }

            $this->grafikBatch[] = [
                'yer_sotuv_id' => $yerSotuvId,
                'lot_raqami' => $lotRaqami,
                'yil' => $yil,
                'oy' => $oy,
                'oy_nomi' => $this->oyNomlari[$oy],
                'grafik_summa' => $summa,
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (count($this->grafikBatch) >= self::BATCH_SIZE) {
                $this->flushGrafikBatch();
            }

            $currentDate->addMonth();
        }
    }

    private function flushGrafikBatch(): void
    {
        if (!empty($this->grafikBatch)) {
            DB::table($this->grafikTable)->insert($this->grafikBatch);
            $this->grafikBatch = [];
        }
    }

    private function importFaktTolovlar(): void
    {
        $file = storage_path('app/excel/fakt_tolovlar.csv');

        if (!file_exists($file)) {
            $this->command->warn("Fakt to'lovlar fayli topilmadi: " . $file);
            $this->writeLog("Fakt fayl topilmadi");
            return;
        }

        $this->command->info("\nFakt to'lovlar yuklanmoqda (CSV)...");

        // DEBUG: Show all LOT numbers in database
        $existingLots = DB::table($this->yerSotuvTable)->pluck('lot_raqami')->toArray();
        sort($existingLots);

        $this->command->info("\n=== DATABASE LOT NUMBERS ===");
        $this->command->info("Total LOTs in DB: " . count($existingLots));
        $this->command->info("First 20: " . implode(', ', array_slice($existingLots, 0, 20)));
        $this->command->info("Last 20: " . implode(', ', array_slice($existingLots, -20)));

        // Check if any LOT matches the pattern from fakt file
        $longLots = array_filter($existingLots, function ($lot) {
            return strlen($lot) >= 7;
        });
        $this->command->info("LOTs with 7+ digits: " . count($longLots));
        if (!empty($longLots)) {
            $this->command->info("Sample long LOTs: " . implode(', ', array_slice($longLots, 0, 10)));
        }

        $this->command->newLine();

        $handle = fopen($file, 'r');
        if ($handle === false) {
            throw new \RuntimeException("CSV faylni ochib bo'lmadi");
        }

        fgetcsv($handle, 0, ';'); // Skip header

        $count = 0;
        $skipped = 0;

        $totalRows = 0;
        while (fgets($handle) !== false) {
            $totalRows++;
        }
        rewind($handle);
        fgetcsv($handle, 0, ';');

        $bar = $this->command->getOutput()->createProgressBar($totalRows);
        $bar->start();

        $existingLotsFlipped = array_flip($existingLots);

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (empty(array_filter($row))) {
                $skipped++;
                $bar->advance();
                continue;
            }

            // LOT is directly in column 7 (index 7) - no regex needed
            $lotRaqami = $this->cleanValue($row[7] ?? '');

            // Clean and validate LOT number
            if ($lotRaqami) {
                $lotRaqami = trim($lotRaqami);
                // Remove any non-numeric characters
                $lotRaqami = preg_replace('/[^0-9]/', '', $lotRaqami);
            }

            if (!$lotRaqami || !isset($existingLotsFlipped[$lotRaqami])) {
                if ($lotRaqami && !in_array($lotRaqami, $this->notFoundLots)) {
                    $this->notFoundLots[] = $lotRaqami;
                }
                $skipped++;
                $bar->advance();
                continue;
            }

            $tolovSana = $this->parseDate($row[0] ?? null);
            if (!$tolovSana) {
                $tolovSana = Carbon::now()->format('Y-m-d');
            }

            $this->faktBatch[] = [
                'lot_raqami' => $lotRaqami,
                'tolov_sana' => $tolovSana,
                'hujjat_raqam' => $this->cleanValue($row[1] ?? null),
                'tolash_nom' => $this->cleanValue($row[2] ?? null),
                'tolash_hisob' => $this->cleanValue($row[3] ?? null),
                'tolash_inn' => $this->cleanValue($row[4] ?? null),
                'tolov_summa' => $this->parseNumber($row[5] ?? null) ?? 0,
                'detali' => $this->cleanValue($row[6] ?? null),
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (count($this->faktBatch) >= self::BATCH_SIZE) {
                $this->flushFaktBatch();
            }

            $count++;
            $bar->advance();
        }

        fclose($handle);

        $bar->finish();
        $this->command->newLine(2);
        $this->command->info("✓ Jami {$count} ta fakt to'lov yuklandi!");
        if ($skipped > 0) {
            $this->command->warn("⚠ {$skipped} ta o'tkazib yuborildi");
        }

        if (!empty($this->notFoundLots)) {
            $this->command->warn("\nTopilmagan LOT namunalari (birinchi 10 ta):");
            $samples = array_slice(array_unique($this->notFoundLots), 0, 10);
            foreach ($samples as $lot) {
                $this->command->warn("  - " . $lot);
            }
        }

        $this->writeLog("Fakt to'lovlar: {$count} ta yuklandi, {$skipped} ta o'tkazildi");
    }
    private function flushFaktBatch(): void
    {
        if (!empty($this->faktBatch)) {
            DB::table($this->faktTable)->insert($this->faktBatch);
            $this->faktBatch = [];
        }
    }

    private function parseLotNumber($value): ?string
    {
        if ($value === null || $value === '') return null;

        if (is_string($value)) {
            $cleaned = str_replace([',', ' ', '.'], '', trim($value));
            if (is_numeric($cleaned)) {
                return $cleaned;
            }
        }

        if (is_numeric($value)) {
            return (string)round($value);
        }

        return null;
    }

    private function extractLotRaqami($text): ?string
    {
        if (empty($text)) return null;

        $text = trim($text);

        // Pattern 1: Number at the END of string (most common in your CSV)
        // Matches 6-9 digit numbers at the end, possibly after tab or special chars
        if (preg_match('/(\d{6,9})\s*$/', $text, $matches)) {
            return $matches[1];
        }

        // Pattern 2: L{number}L
        if (preg_match('/L(\d+)L/i', $text, $matches)) {
            return $matches[1];
        }

        // Pattern 3: L{number}
        if (preg_match('/L(\d+)/i', $text, $matches)) {
            return $matches[1];
        }

        // Pattern 4: {number}L
        if (preg_match('/(\d+)L/i', $text, $matches)) {
            return $matches[1];
        }

        // Pattern 5: LOT {number}
        if (preg_match('/LOT\s*[:\-]?\s*(\d+)/i', $text, $matches)) {
            return $matches[1];
        }

        // Pattern 6: Any 6+ digit number (fallback)
        if (preg_match('/\b(\d{6,})\b/', $text, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function cleanValue($value): ?string
    {
        if ($value === null || $value === '') return null;
        return trim($value);
    }

    private function parseNumber($value): ?float
    {
        if ($value === null || $value === '') return null;

        if (is_numeric($value)) {
            return (float)$value;
        }

        if (is_string($value)) {
            $value = trim($value);
            $value = str_replace([' ', "'", "\xC2\xA0"], '', $value);

            $commaCount = substr_count($value, ',');
            $dotCount = substr_count($value, '.');

            if ($commaCount > 0 && $dotCount > 0) {
                $commaPos = strpos($value, ',');
                $dotPos = strpos($value, '.');

                if ($dotPos > $commaPos) {
                    $value = str_replace(',', '', $value);
                } else {
                    $value = str_replace('.', '', $value);
                    $value = str_replace(',', '.', $value);
                }
            } elseif ($commaCount > 0 && $dotCount == 0) {
                if ($commaCount > 1) {
                    $value = str_replace(',', '', $value);
                } else {
                    $commaPos = strpos($value, ',');
                    $afterComma = substr($value, $commaPos + 1);

                    if (strlen($afterComma) <= 4 && is_numeric($afterComma)) {
                        $value = str_replace(',', '.', $value);
                    } else {
                        $value = str_replace(',', '', $value);
                    }
                }
            } elseif ($dotCount > 0 && $commaCount == 0) {
                if ($dotCount > 1) {
                    $value = str_replace('.', '', $value);
                }
            }

            return is_numeric($value) ? (float)$value : null;
        }

        return null;
    }

    private function parseDate($value): ?string
    {
        if (!$value) return null;

        try {
            if (is_string($value)) {
                $value = trim($value);

                if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value)) {
                    return Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
                }

                if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $value)) {
                    return Carbon::createFromFormat('d.m.Y', $value)->format('Y-m-d');
                }

                return Carbon::parse($value)->format('Y-m-d');
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function writeLog($message): void
    {
        Storage::append($this->logFileName, $message);
    }

    private function writeFinalSummary($duration): void
    {
        $this->writeLog("\n" . str_repeat("=", 80));
        $this->writeLog("=== YAKUNIY HISOBOT ===");

        $totalYerSotuv = DB::table($this->yerSotuvTable)->count();
        $totalGrafik = DB::table($this->grafikTable)->count();
        $totalFakt = DB::table($this->faktTable)->count();

        $this->writeLog(sprintf("%-40s: %s", "Vaqt", $duration . "s"));
        $this->writeLog(sprintf("%-40s: %s ta", "LOTlar", number_format($totalYerSotuv, 0)));
        $this->writeLog(sprintf("%-40s: %s ta", "Grafik", number_format($totalGrafik, 0)));
        $this->writeLog(sprintf("%-40s: %s ta", "Fakt", number_format($totalFakt, 0)));
        $this->writeLog(str_repeat("=", 80));
    }

    private function showVerificationStatistics(): void
    {
        $this->command->info("\n" . str_repeat("=", 80));
        $this->command->info("TEKSHIRISH VA STATISTIKA");
        $this->command->info(str_repeat("=", 80) . "\n");

        $totalLots = DB::table($this->yerSotuvTable)->count();
        $totalGrafik = DB::table($this->grafikTable)->count();
        $totalFakt = DB::table($this->faktTable)->count();

        $this->command->info(sprintf("Jami LOTlar:           %s ta", number_format($totalLots, 0)));
        $this->command->info(sprintf("Jami grafik to'lovlar: %s ta", number_format($totalGrafik, 0)));
        $this->command->info(sprintf("Jami fakt to'lovlar:   %s ta", number_format($totalFakt, 0)));

        $financials = DB::table($this->yerSotuvTable)->selectRaw('
            SUM(maydoni) as jami_maydon,
            SUM(sotilgan_narx) as jami_sotilgan,
            SUM(golib_tolagan) as jami_golib,
            SUM(shartnoma_summasi) as jami_shartnoma
        ')->first();

        if ($financials) {
            $this->command->info(sprintf("\nJami maydon:           %s ga", number_format($financials->jami_maydon ?? 0, 2)));
            $this->command->info(sprintf("Jami sotilgan narx:    %s so'm", number_format($financials->jami_sotilgan ?? 0, 0)));
            $this->command->info(sprintf("Jami g'olib to'lagan:  %s so'm", number_format($financials->jami_golib ?? 0, 0)));
            $this->command->info(sprintf("Jami shartnoma:        %s so'm", number_format($financials->jami_shartnoma ?? 0, 0)));
        }

        $grafikSum = DB::table($this->grafikTable)->sum('grafik_summa');
        $this->command->info(sprintf("Jami grafik summa:     %s so'm", number_format($grafikSum, 0)));

        $faktSum = DB::table($this->faktTable)->sum('tolov_summa');
        $this->command->info(sprintf("Jami fakt summa:       %s so'm", number_format($faktSum, 0)));

        $lotsWithoutGrafik = DB::table($this->yerSotuvTable)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from($this->grafikTable)
                    ->whereColumn("{$this->grafikTable}.yer_sotuv_id", "{$this->yerSotuvTable}.id");
            })
            ->where('tolov_turi', 'муддатли')
            ->count();

        $this->command->info(sprintf(
            "\n[%s] Muddatli LOTlar grafiksiz:  %s ta",
            $lotsWithoutGrafik === 0 ? '✓' : '✗',
            $lotsWithoutGrafik
        ));

        $this->command->info("\n" . str_repeat("=", 80));
    }
}
