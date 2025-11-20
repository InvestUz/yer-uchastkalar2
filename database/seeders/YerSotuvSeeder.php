<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class YerSotuvSeeder extends Seeder
{
    private const BATCH_SIZE = 50;
    private const MEMORY_LIMIT = '512M';

    private $oyNomlari = [
        1 => 'yanvar', 2 => 'fevral', 3 => 'mart', 4 => 'aprel',
        5 => 'may', 6 => 'iyun', 7 => 'iyul', 8 => 'avgust',
        9 => 'sentabr', 10 => 'oktabr', 11 => 'noyabr', 12 => 'dekabr'
    ];

    /**
     * CORRECTED PAYMENT SCHEDULE MAPPING
     * Column 51 = Contract total reference (NOT stored in grafik)
     * Columns 52-147 = Actual monthly payments
     */
    private $grafikColumnMap = [
        2022 => [1 => 52, 2 => 53, 3 => 54, 4 => 55, 5 => 56, 6 => 57, 7 => 58, 8 => 59, 9 => 60, 10 => 61, 11 => 62, 12 => 63],
        2023 => [1 => 64, 2 => 65, 3 => 66, 4 => 67, 5 => 68, 6 => 69, 7 => 70, 8 => 71, 9 => 72, 10 => 73, 11 => 74, 12 => 75],
        2024 => [1 => 76, 2 => 77, 3 => 78, 4 => 79, 5 => 80, 6 => 81, 7 => 82, 8 => 83, 9 => 84, 10 => 85, 11 => 86, 12 => 87],
        2025 => [1 => 88, 2 => 89, 3 => 90, 4 => 91, 5 => 92, 6 => 93, 7 => 94, 8 => 95, 9 => 96, 10 => 97, 11 => 98, 12 => 99],
        2026 => [1 => 100, 2 => 101, 3 => 102, 4 => 103, 5 => 104, 6 => 105, 7 => 106, 8 => 107, 9 => 108, 10 => 109, 11 => 110, 12 => 111],
        2027 => [1 => 112, 2 => 113, 3 => 114, 4 => 115, 5 => 116, 6 => 117, 7 => 118, 8 => 119, 9 => 120, 10 => 121, 11 => 122, 12 => 123],
        2028 => [1 => 124, 2 => 125, 3 => 126, 4 => 127, 5 => 128, 6 => 129, 7 => 130, 8 => 131, 9 => 132, 10 => 133, 11 => 134, 12 => 135],
        2029 => [1 => 136, 2 => 137, 3 => 138, 4 => 139, 5 => 140, 6 => 141, 7 => 142, 8 => 143, 9 => 144, 10 => 145, 11 => 146, 12 => 147]
    ];

    private $notFoundLots = [];
    private $skippedRecords = [];
    private $logFileName;
    private $grafikBatch = [];
    private $faktBatch = [];
    private $importErrors = [];

    // Table names (detect singular/plural)
    private $yerSotuvTable = 'yer_sotuvlar';
    private $grafikTable = 'grafik_tolov';
    private $faktTable = 'fakt_tolov';

    public function run(): void
    {
        ini_set('memory_limit', self::MEMORY_LIMIT);

        $this->logFileName = 'seeder_logs/import_' . now()->format('Y-m-d_H-i-s') . '.log';
        $this->writeLog("=== YER SOTUV IMPORT - PRODUCTION MODE ===");
        $this->writeLog("Boshlandi: " . now()->format('Y-m-d H:i:s'));
        $this->writeLog(str_repeat("=", 80));

        // Detect table names
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
        // Try to detect correct table names
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
        $file = storage_path('app/excel/new_data.xlsx');

        if (!file_exists($file)) {
            throw new \RuntimeException("Fayl topilmadi: $file");
        }

        $this->command->info("Fayl topildi: new_data.xlsx");
        $this->command->info("Excel fayl yuklanmoqda...");

        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, true);

        unset($rows[1]); // Remove header

        $totalRows = count($rows);
        $this->command->info("Jami {$totalRows} ta qator topildi.");
        $this->writeLog("Jami qatorlar: {$totalRows}");

        $bar = $this->command->getOutput()->createProgressBar($totalRows);
        $bar->start();

        $count = 0;
        $rowNumber = 1;

        foreach ($rows as $row) {
            $rowNumber++;
            $rowData = array_values($row);

            if (empty(array_filter($rowData))) {
                $bar->advance();
                continue;
            }

            $lotRaqami = $this->parseLotNumber($rowData[1] ?? null);

            if (!$lotRaqami) {
                $this->skippedRecords[] = "Qator {$rowNumber}: LOT topilmadi";
                $bar->advance();
                continue;
            }

            try {
                DB::beginTransaction();

                $yerSotuvId = $this->createYerSotuv($rowData, $lotRaqami, $rowNumber);

                if ($yerSotuvId) {
                    $this->createGrafikTolovlar($rowData, $yerSotuvId, $lotRaqami);
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

        $bar->finish();
        $this->command->newLine(2);
        $this->command->info("✓ Jami {$count} ta lot yuklandi!");
        $this->writeLog("Yuklangan: {$count} ta lot");
    }

    /**
     * Store ALL columns from Excel - flexible approach
     */
    private function createYerSotuv($row, $lotRaqami, $rowNumber): ?int
    {
        $auksionSana = $this->parseDate($row[15] ?? null);
        $shartnomaSana = $this->parseDate($row[26] ?? null);

        // Build data array with ALL Excel columns
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

        // Calculate shartnoma_summasi from grafik (columns 52-147)
        $shartnomaSummasi = $this->calculateShartnomaSummasiFromGrafik($row);
        $data['shartnoma_summasi'] = $shartnomaSummasi;

        // Filter out only columns that exist in the table
        $tableColumns = Schema::getColumnListing($this->yerSotuvTable);
        $filteredData = array_filter($data, function($key) use ($tableColumns) {
            return in_array($key, $tableColumns);
        }, ARRAY_FILTER_USE_KEY);

        // Add timestamps
        $filteredData['created_at'] = now();
        $filteredData['updated_at'] = now();

        $id = DB::table($this->yerSotuvTable)->insertGetId($filteredData);

        return $id;
    }

    /**
     * Calculate shartnoma_summasi using the correct formula:
     * MAX(Column 51, SUM(Columns 52-147))
     *
     * This handles 3 cases:
     * 1. LOTs with payment schedule → use schedule sum
     * 2. LOTs without schedule but Column 51 has value → use Column 51
     * 3. муддатли эмас LOTs with schedules → use schedule sum
     */
    private function calculateShartnomaSummasiFromGrafik($row): float
    {
        // Get Column 51 value (Contract Total Reference)
        $column51Value = $this->parseNumber($row[50] ?? null) ?? 0; // Column 51 = index 50

        // Calculate sum from payment schedule (columns 52-147)
        $paymentScheduleSum = 0;

        foreach ($this->grafikColumnMap as $yil => $oylar) {
            foreach ($oylar as $oy => $ustunIndex) {
                $arrayIndex = $ustunIndex - 1;

                if (isset($row[$arrayIndex])) {
                    $summa = $this->parseNumber($row[$arrayIndex]);
                    if ($summa !== null && $summa > 0) {
                        $paymentScheduleSum += $summa;
                    }
                }
            }
        }

        // Use the MAXIMUM of the two values
        // This ensures we don't lose data from either source
        return max($column51Value, $paymentScheduleSum);
    }

    private function createGrafikTolovlar($row, $yerSotuvId, $lotRaqami): void
    {
        $monthsWithData = [];
        $firstPaymentMonth = null;
        $lastPaymentMonth = null;

        foreach ($this->grafikColumnMap as $yil => $oylar) {
            foreach ($oylar as $oy => $ustunIndex) {
                $arrayIndex = $ustunIndex - 1;
                $summa = $this->parseNumber($row[$arrayIndex] ?? null);

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
        $file = storage_path('app/excel/Тушум 2024-2025-13.11.2025.xlsx');

        if (!file_exists($file)) {
            $this->command->warn("Fakt to'lovlar fayli topilmadi");
            return;
        }

        $this->command->info("\nFakt to'lovlar yuklanmoqda...");
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, true);

        unset($rows[1]);

        $totalRows = count($rows);
        $bar = $this->command->getOutput()->createProgressBar($totalRows);
        $bar->start();

        $count = 0;
        $skipped = 0;

        $existingLots = DB::table($this->yerSotuvTable)->pluck('lot_raqami')->flip();

        foreach ($rows as $row) {
            $rowData = array_values($row);

            if (empty(array_filter($rowData))) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $lotRaqami = $this->extractLotRaqami($rowData[7] ?? '');

            if (!$lotRaqami || !isset($existingLots[$lotRaqami])) {
                if ($lotRaqami && !in_array($lotRaqami, $this->notFoundLots)) {
                    $this->notFoundLots[] = $lotRaqami;
                }
                $skipped++;
                $bar->advance();
                continue;
            }

            $tolovSana = $this->parseDate($rowData[0] ?? null);
            if (!$tolovSana) {
                $tolovSana = Carbon::now()->format('Y-m-d');
            }

            $this->faktBatch[] = [
                'lot_raqami' => $lotRaqami,
                'tolov_sana' => $tolovSana,
                'hujjat_raqam' => $this->cleanValue($rowData[1] ?? null),
                'tolash_nom' => $this->cleanValue($rowData[2] ?? null),
                'tolash_hisob' => $this->cleanValue($rowData[3] ?? null),
                'tolash_inn' => $this->cleanValue($rowData[4] ?? null),
                'tolov_summa' => $this->parseNumber($rowData[5] ?? null) ?? 0,
                'detali' => $this->cleanValue($rowData[6] ?? null),
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (count($this->faktBatch) >= self::BATCH_SIZE) {
                $this->flushFaktBatch();
            }

            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine(2);
        $this->command->info("✓ Jami {$count} ta fakt to'lov yuklandi!");
        if ($skipped > 0) {
            $this->command->warn("⚠ {$skipped} ta o'tkazib yuborildi");
        }
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
        $cleanedText = preg_replace('/(\d+)[,\s]+(\d+)/', '$1$2', $text);

        $patterns = [
            '/L(\d+)L/i',
            '/L(\d+)/i',
            '/(\d+)L/i',
            '/LOT\s*(\d+)/i',
            '/\b(\d{6,})\b/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $cleanedText, $matches)) {
                return $matches[1];
            }
        }

        $cleanedText = str_replace([',', ' '], '', $cleanedText);
        if (is_numeric($cleanedText) && strlen($cleanedText) >= 6) {
            return (string)round($cleanedText);
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
            }
            elseif ($commaCount > 0 && $dotCount == 0) {
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
            }
            elseif ($dotCount > 0 && $commaCount == 0) {
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
            if (is_numeric($value) && $value > 0) {
                $timestamp = Date::excelToTimestamp($value);
                return Carbon::createFromTimestamp($timestamp)->format('Y-m-d');
            }

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

        // Safe validation check
        $lotsWithoutGrafik = DB::table($this->yerSotuvTable)
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                      ->from($this->grafikTable)
                      ->whereColumn("{$this->grafikTable}.yer_sotuv_id", "{$this->yerSotuvTable}.id");
            })
            ->where('tolov_turi', 'муддатли')
            ->count();

        $this->command->info(sprintf("\n[%s] Muddatli LOTlar grafiksiz:  %s ta",
            $lotsWithoutGrafik === 0 ? '✓' : '✗',
            $lotsWithoutGrafik
        ));

        $this->command->info("\n" . str_repeat("=", 80));
    }
}
