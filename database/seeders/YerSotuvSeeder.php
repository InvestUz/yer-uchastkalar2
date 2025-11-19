<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\YerSotuv;
use App\Models\GrafikTolov;
use App\Models\FaktTolov;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;
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

    // VERIFIED CORRECT column mapping based on actual Excel structure
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

    public function run(): void
    {
        ini_set('memory_limit', self::MEMORY_LIMIT);

        $this->logFileName = 'seeder_logs/import_' . now()->format('Y-m-d_H-i-s') . '.log';
        $this->writeLog("=== YER SOTUV IMPORT LOG (PRODUCTION MODE) ===");
        $this->writeLog("Boshlandi: " . now()->format('Y-m-d H:i:s'));
        $this->writeLog("Memory Limit: " . self::MEMORY_LIMIT);
        $this->writeLog(str_repeat("=", 80));

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->command->info("Ma'lumotlar tozalanmoqda...");
        FaktTolov::query()->delete();
        GrafikTolov::query()->delete();
        YerSotuv::query()->delete();

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
                $this->command->warn("\n=== OGOHLANTIRISH: Topilmagan LOT raqamlar ===");
                $count = min(10, count($this->notFoundLots));
                for ($i = 0; $i < $count; $i++) {
                    $this->command->error("LOT {$this->notFoundLots[$i]} ma'lumotlar bazasida topilmadi!");
                }
                if (count($this->notFoundLots) > 10) {
                    $this->command->warn("... va yana " . (count($this->notFoundLots) - 10) . " ta");
                }
                $this->command->warn("Jami topilmagan: " . count($this->notFoundLots) . " ta\n");
            }

            $this->command->info("\n✓ Import muvaffaqiyatli yakunlandi! ({$duration}s)");
            $this->command->info("✓ Log fayl: storage/app/{$this->logFileName}");

        } catch (\Exception $e) {
            $this->command->error("\n✗ KRITIK XATOLIK: " . $e->getMessage());
            $this->command->error("Stack: " . $e->getTraceAsString());
            $this->writeLog("\nKRITIK XATOLIK: " . $e->getMessage());
            $this->writeLog($e->getTraceAsString());
            throw $e;
        }
    }

    private function importAsosiyMalumot(): void
    {
        // Try multiple possible file names
        $possibleFiles = [
            'Sotilgan_yerlar_18_11_2025_Bazaga(Abdulazizga).xlsx',
        ];

        $file = null;
        foreach ($possibleFiles as $filename) {
            $path = storage_path('app/excel/' . $filename);
            if (file_exists($path)) {
                $file = $path;
                $this->command->info("Fayl topildi: {$filename}");
                break;
            }
        }

        if (!$file) {
            $file = storage_path('app/excel/Sotilgan_yerlar_18_11_2025_Bazaga(Abdulazizga).xlsx');
        }

        if (!file_exists($file)) {
            throw new \RuntimeException("Fayl topilmadi: $file");
        }

        $this->command->info("Excel fayl yuklanmoqda...");
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, true);

        // Remove header row
        unset($rows[1]);

        $totalRows = count($rows);
        $this->command->info("Jami {$totalRows} ta qator topildi.");
        $this->writeLog("\n=== ASOSIY MA'LUMOTLAR IMPORT ===");
        $this->writeLog("Fayl: Sotilgan_yerlar_18_11_2025_Bazaga(Abdulazizga).xlsx");
        $this->writeLog("Jami qatorlar: {$totalRows}");

        $bar = $this->command->getOutput()->createProgressBar($totalRows);
        $bar->start();

        $count = 0;
        $rowNumber = 1;

        foreach ($rows as $rowIndex => $row) {
            $rowNumber++;

            // Convert associative array to indexed array
            $rowData = array_values($row);

            if (empty(array_filter($rowData))) {
                $this->skippedRecords[] = [
                    'sabab' => 'Bo\'sh qator',
                    'qator' => $rowNumber
                ];
                $bar->advance();
                continue;
            }

            // Column B (index 1) contains LOT number
            $lotRaqami = $this->parseLotNumber($rowData[1] ?? null);

            if (!$lotRaqami) {
                $this->skippedRecords[] = [
                    'sabab' => 'LOT raqami topilmadi',
                    'qator' => $rowNumber,
                    'qoshimcha' => 'Ustun B: ' . ($rowData[1] ?? 'bo\'sh')
                ];
                $bar->advance();
                continue;
            }

            try {
                DB::beginTransaction();

                $yerSotuv = $this->createYerSotuv($rowData, $lotRaqami, $rowNumber);

                if ($yerSotuv) {
                    $this->createGrafikTolovlar($rowData, $yerSotuv);
                    $count++;
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $this->skippedRecords[] = [
                    'sabab' => 'Exception xatolik',
                    'lot_raqami' => $lotRaqami,
                    'qator' => $rowNumber,
                    'qoshimcha' => $e->getMessage()
                ];
                $this->writeLog("XATOLIK qator {$rowNumber}: " . $e->getMessage());
            }

            $bar->advance();

            // Memory cleanup every 100 records
            if ($count % 100 === 0) {
                gc_collect_cycles();
            }
        }

        $bar->finish();
        $this->command->newLine(2);
        $this->command->info("✓ Jami {$count} ta lot yuklandi!");
        $this->writeLog("Muvaffaqiyatli yuklandi: {$count} ta lot");
    }

    private function createYerSotuv($row, $lotRaqami, $rowNumber): ?YerSotuv
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
            'shartnoma_summasi' => 0,
            'yil' => $auksionSana ? Carbon::parse($auksionSana)->year : date('Y')
        ];

        $yerSotuv = YerSotuv::create($data);

        // Calculate shartnoma_summasi from grafik schedule
        $shartnomaSummasi = $this->calculateShartnomaSummasiFromGrafik($row);
        $yerSotuv->update(['shartnoma_summasi' => $shartnomaSummasi]);

        return $yerSotuv;
    }

    private function calculateShartnomaSummasiFromGrafik($row): float
    {
        $totalSumma = 0;

        foreach ($this->grafikColumnMap as $yil => $oylar) {
            foreach ($oylar as $oy => $ustunIndex) {
                if (isset($row[$ustunIndex])) {
                    $summa = $this->parseNumber($row[$ustunIndex]);
                    if ($summa !== null && $summa > 0) {
                        $totalSumma += $summa;
                    }
                }
            }
        }

        return $totalSumma;
    }

    private function createGrafikTolovlar($row, $yerSotuv): void
    {
        // Collect all months with payment data
        $monthsWithData = [];
        $firstPaymentMonth = null;
        $lastPaymentMonth = null;

        foreach ($this->grafikColumnMap as $yil => $oylar) {
            foreach ($oylar as $oy => $ustunIndex) {
                $summa = $this->parseNumber($row[$ustunIndex] ?? null);

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
            return; // No payment schedule
        }

        // Create records for all months between first and last payment
        $currentDate = $firstPaymentMonth->copy();

        while ($currentDate->lte($lastPaymentMonth)) {
            $yil = $currentDate->year;
            $oy = $currentDate->month;

            // Find payment for this month
            $summa = 0;
            foreach ($monthsWithData as $monthData) {
                if ($monthData['yil'] == $yil && $monthData['oy'] == $oy) {
                    $summa = $monthData['summa'];
                    break;
                }
            }

            $this->grafikBatch[] = [
                'yer_sotuv_id' => $yerSotuv->id,
                'lot_raqami' => $yerSotuv->lot_raqami,
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
            GrafikTolov::insert($this->grafikBatch);
            $this->grafikBatch = [];
        }
    }

    private function importFaktTolovlar(): void
    {
        $file = storage_path('app/excel/Тушум 2024-2025-13.11.2025.xlsx');

        if (!file_exists($file)) {
            $this->command->warn("Fakt to'lovlar fayli topilmadi, o'tkazib yuborilmoqda...");
            $this->writeLog("OGOHLANTIRISH: Fakt to'lovlar fayli topilmadi");
            return;
        }

        $this->command->info("\nFakt to'lovlar yuklanmoqda...");
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, true);

        unset($rows[1]); // Remove header

        $totalRows = count($rows);
        $this->writeLog("\n=== FAKT TO'LOVLAR IMPORT ===");
        $this->writeLog("Fayl: Тушум 2024-2025-13.11.2025.xlsx");
        $this->writeLog("Jami qatorlar: {$totalRows}");

        $bar = $this->command->getOutput()->createProgressBar($totalRows);
        $bar->start();

        $count = 0;
        $skipped = 0;
        $rowNumber = 1;

        // Pre-load all LOT numbers for faster lookup
        $existingLots = YerSotuv::pluck('lot_raqami')->flip();

        foreach ($rows as $rowIndex => $row) {
            $rowNumber++;
            $rowData = array_values($row);

            if (empty(array_filter($rowData))) {
                $skipped++;
                $bar->advance();
                continue;
            }

            // Column H (index 7) contains LOT reference
            $lotRaqami = $this->extractLotRaqami($rowData[7] ?? '');

            if (!$lotRaqami) {
                $this->skippedRecords[] = [
                    'sabab' => 'LOT raqami topilmadi (Fakt)',
                    'qator' => $rowNumber
                ];
                $skipped++;
                $bar->advance();
                continue;
            }

            if (!isset($existingLots[$lotRaqami])) {
                if (!in_array($lotRaqami, $this->notFoundLots)) {
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

        $this->writeLog("Muvaffaqiyatli yuklandi: {$count} ta fakt to'lov");
        $this->writeLog("O'tkazib yuborildi: {$skipped} ta");
    }

    private function flushFaktBatch(): void
    {
        if (!empty($this->faktBatch)) {
            FaktTolov::insert($this->faktBatch);
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

        // Patterns in order of priority
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

    /**
     * Parse number with support for BOTH comma and dot formats
     * Examples:
     *   1,658   -> 1.658 (comma as decimal separator)
     *   1658    -> 1658
     *   4.1865  -> 4.1865 (dot as decimal separator)
     *   1,234,567.89 -> 1234567.89 (comma as thousands, dot as decimal)
     */
    private function parseNumber($value): ?float
    {
        if ($value === null || $value === '') return null;

        // Already a number
        if (is_numeric($value)) {
            return (float)$value;
        }

        if (is_string($value)) {
            $value = trim($value);

            // Remove spaces and apostrophes (thousands separators)
            $value = str_replace([' ', "'"], '', $value);

            // Count commas and dots to determine format
            $commaCount = substr_count($value, ',');
            $dotCount = substr_count($value, '.');

            // Case 1: "1,234,567.89" - comma as thousands, dot as decimal
            if ($commaCount > 0 && $dotCount > 0) {
                // Remove commas (thousands separator), keep dot (decimal)
                $value = str_replace(',', '', $value);
            }
            // Case 2: "1,658" - single comma could be decimal OR thousands
            // We need to check the position and context
            elseif ($commaCount == 1 && $dotCount == 0) {
                // If comma is followed by 3 digits at the end, it's likely a decimal
                // e.g., "1,658" -> 1.658
                // But if it's in the middle like "1,234" it could be thousands

                // Get position of comma
                $commaPos = strpos($value, ',');
                $afterComma = substr($value, $commaPos + 1);

                // If there are 1-4 digits after comma and no other commas, treat as decimal
                if (strlen($afterComma) <= 4 && is_numeric($afterComma)) {
                    $value = str_replace(',', '.', $value);
                } else {
                    // Treat as thousands separator
                    $value = str_replace(',', '', $value);
                }
            }
            // Case 3: Multiple commas, no dots - e.g., "1,234,567"
            elseif ($commaCount > 1 && $dotCount == 0) {
                // Commas are thousands separators
                $value = str_replace(',', '', $value);
            }
            // Case 4: "4.1865" - dot as decimal separator
            // No changes needed, PHP handles this natively

            return is_numeric($value) ? (float)$value : null;
        }

        return null;
    }

    private function parseDate($value): ?string
    {
        if (!$value) return null;

        try {
            // Excel numeric date
            if (is_numeric($value) && $value > 0) {
                $timestamp = Date::excelToTimestamp($value);
                return Carbon::createFromTimestamp($timestamp)->format('Y-m-d');
            }

            if (is_string($value)) {
                $value = trim($value);

                // m/d/Y format
                if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value)) {
                    return Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
                }

                // d.m.Y format
                if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $value)) {
                    return Carbon::createFromFormat('d.m.Y', $value)->format('Y-m-d');
                }

                // Try general parsing
                return Carbon::parse($value)->format('Y-m-d');
            }

            return null;
        } catch (\Exception $e) {
            $this->writeLog("OGOHLANTIRISH: Sana parse xatolik - {$value}");
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
        $this->writeLog(str_repeat("=", 80));

        $totalYerSotuv = YerSotuv::count();
        $totalGrafik = GrafikTolov::count();
        $totalFakt = FaktTolov::count();
        $totalNotFound = count($this->notFoundLots);
        $totalSkipped = count($this->skippedRecords);

        $this->writeLog(sprintf("\n%-50s: %s", "Bajarilish vaqti", $duration . "s"));
        $this->writeLog(sprintf("%-50s: %s ta", "Yuklangan LOTlar", number_format($totalYerSotuv, 0)));
        $this->writeLog(sprintf("%-50s: %s ta", "Yuklangan grafik to'lovlar", number_format($totalGrafik, 0)));
        $this->writeLog(sprintf("%-50s: %s ta", "Yuklangan fakt to'lovlar", number_format($totalFakt, 0)));
        $this->writeLog(sprintf("%-50s: %s ta", "Topilmagan LOTlar", $totalNotFound));
        $this->writeLog(sprintf("%-50s: %s ta", "O'tkazib yuborilgan qatorlar", $totalSkipped));

        if ($totalNotFound > 0 && $totalNotFound <= 20) {
            $this->writeLog("\n### TOPILMAGAN LOT RAQAMLAR ###");
            foreach ($this->notFoundLots as $lot) {
                $this->writeLog("  - LOT {$lot}");
            }
        }

        $this->writeLog("\n" . str_repeat("=", 80));
        $this->writeLog("Yakunlandi: " . now()->format('Y-m-d H:i:s'));
        $this->writeLog(str_repeat("=", 80));
    }

    private function showVerificationStatistics(): void
    {
        $this->command->info("\n" . str_repeat("=", 80));
        $this->command->info("TEKSHIRISH VA STATISTIKA");
        $this->command->info(str_repeat("=", 80) . "\n");

        $totalLots = YerSotuv::count();
        $totalGrafik = GrafikTolov::count();
        $totalFakt = FaktTolov::count();

        $this->command->info(sprintf("Jami LOTlar:           %s ta", number_format($totalLots, 0)));
        $this->command->info(sprintf("Jami grafik to'lovlar: %s ta", number_format($totalGrafik, 0)));
        $this->command->info(sprintf("Jami fakt to'lovlar:   %s ta", number_format($totalFakt, 0)));

        $financials = YerSotuv::selectRaw('
            SUM(maydoni) as jami_maydon,
            SUM(sotilgan_narx) as jami_sotilgan,
            SUM(golib_tolagan) as jami_golib,
            SUM(shartnoma_summasi) as jami_shartnoma
        ')->first();

        $this->command->info(sprintf("\nJami maydon:           %s ga", number_format($financials->jami_maydon ?? 0, 2)));
        $this->command->info(sprintf("Jami sotilgan narx:    %s", number_format($financials->jami_sotilgan ?? 0, 0)));
        $this->command->info(sprintf("Jami g'olib to'lagan:  %s", number_format($financials->jami_golib ?? 0, 0)));
        $this->command->info(sprintf("Jami shartnoma:        %s", number_format($financials->jami_shartnoma ?? 0, 0)));

        $grafikSum = GrafikTolov::sum('grafik_summa');
        $this->command->info(sprintf("Jami grafik summa:     %s", number_format($grafikSum, 0)));

        $faktSum = FaktTolov::sum('tolov_summa');
        $this->command->info(sprintf("Jami fakt summa:       %s", number_format($faktSum, 0)));

        $lotsWithoutGrafik = YerSotuv::whereDoesntHave('grafikTolovlar')
            ->where('tolov_turi', 'муддатли')
            ->count();

        $this->command->info(sprintf("\n[%s] Muddatli LOTlar grafiksiz:  %s ta",
            $lotsWithoutGrafik === 0 ? '✓' : '✗',
            $lotsWithoutGrafik
        ));

        $this->command->info("\n" . str_repeat("=", 80));
    }
}
