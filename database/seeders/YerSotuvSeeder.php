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

    private $notFoundLots = [];
    private $skippedRecords = [];
    private $logFileName;

    public function run(): void
    {
        // Initialize log file
        $this->logFileName = 'seeder_logs/import_' . now()->format('Y-m-d_H-i-s') . '.log';
        $this->writeLog("=== YER SOTUV IMPORT LOG ===");
        $this->writeLog("Boshlandi: " . now()->format('Y-m-d H:i:s'));
        $this->writeLog(str_repeat("=", 80));

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        FaktTolov::truncate();
        GrafikTolov::truncate();
        YerSotuv::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info("Ma'lumotlar o'chirildi. Import boshlanmoqda...");
        $this->writeLog("\nMa'lumotlar o'chirildi. Import boshlanmoqda...\n");

        $this->importAsosiyMalumot();
        $this->importFaktTolovlar();

        // Write final summary to log
        $this->writeFinalSummary();

        if (!empty($this->notFoundLots)) {
            $this->command->warn("\n=== OGOHLANTIRISH: Topilmagan LOT raqamlar ===");
            foreach ($this->notFoundLots as $lot) {
                $this->command->error("LOT {$lot} ma'lumotlar bazasida topilmadi!");
            }
            $this->command->warn("Jami topilmagan: " . count($this->notFoundLots) . " ta\n");
        }

        $this->command->info("Import muvaffaqiyatli yakunlandi!");
        $this->command->info("Log fayl saqlandi: storage/app/{$this->logFileName}");
    }

    private function writeLog($message): void
    {
        Storage::append($this->logFileName, $message);
    }

    private function writeFinalSummary(): void
    {
        $this->writeLog("\n" . str_repeat("=", 80));
        $this->writeLog("=== YAKUNIY HISOBOT ===");
        $this->writeLog(str_repeat("=", 80));

        // Summary of not found LOTs
        if (!empty($this->notFoundLots)) {
            $this->writeLog("\n### TOPILMAGAN LOT RAQAMLAR ###");
            $this->writeLog("Jami: " . count($this->notFoundLots) . " ta\n");

            foreach ($this->notFoundLots as $lot) {
                $this->writeLog("  - LOT {$lot}: Ma'lumotlar bazasida topilmadi");
            }
        }

        // Summary of skipped records
        if (!empty($this->skippedRecords)) {
            $this->writeLog("\n### O'TKAZIB YUBORILGAN YOZUVLAR ###");
            $this->writeLog("Jami: " . count($this->skippedRecords) . " ta\n");

            // Group by reason
            $groupedByReason = [];
            foreach ($this->skippedRecords as $record) {
                $reason = $record['sabab'];
                if (!isset($groupedByReason[$reason])) {
                    $groupedByReason[$reason] = [];
                }
                $groupedByReason[$reason][] = $record;
            }

            foreach ($groupedByReason as $reason => $records) {
                $this->writeLog("\nSabab: {$reason}");
                $this->writeLog("Soni: " . count($records) . " ta");

                foreach ($records as $record) {
                    $details = [];
                    if (isset($record['lot_raqami'])) {
                        $details[] = "LOT: {$record['lot_raqami']}";
                    }
                    if (isset($record['qator'])) {
                        $details[] = "Qator: {$record['qator']}";
                    }
                    if (isset($record['qoshimcha'])) {
                        $details[] = $record['qoshimcha'];
                    }

                    $this->writeLog("  - " . implode(" | ", $details));
                }
            }
        }

        $this->writeLog("\n" . str_repeat("=", 80));
        $this->writeLog("Yakunlandi: " . now()->format('Y-m-d H:i:s'));
        $this->writeLog(str_repeat("=", 80));
    }

    private function importAsosiyMalumot(): void
    {
        $file = storage_path('app/excel/Sotilgan_yerlar_11_11_2025_Bazaga++.xlsx');

        if (!file_exists($file)) {
            $this->command->error("Fayl topilmadi: $file");
            $this->writeLog("XATOLIK: Fayl topilmadi - $file");
            return;
        }

        try {
            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            array_shift($rows);

            $this->command->info("Asosiy ma'lumotlar yuklanmoqda...");
            $this->writeLog("\n=== ASOSIY MA'LUMOTLAR IMPORT ===");
            $this->writeLog("Fayl: Sotilgan_yerlar_11_11_2025_Bazaga++.xlsx");

            $count = 0;
            foreach ($rows as $rowIndex => $row) {
                if (empty(array_filter($row))) {
                    $this->skippedRecords[] = [
                        'sabab' => 'Bo\'sh qator',
                        'qator' => $rowIndex + 2
                    ];
                    continue;
                }

                $lotRaqami = $this->parseLotNumber($row[1] ?? null);

                if (!$lotRaqami) {
                    $this->skippedRecords[] = [
                        'sabab' => 'LOT raqami topilmadi',
                        'qator' => $rowIndex + 2,
                        'qoshimcha' => 'Ustun B: ' . ($row[1] ?? 'bo\'sh')
                    ];
                    continue;
                }

                try {
                    $yerSotuv = $this->createYerSotuv($row, $lotRaqami);

                    if ($yerSotuv) {
                        $this->createGrafikTolovlar($row, $yerSotuv);
                        $count++;

                        if ($count % 10 == 0) {
                            $this->command->info("  Yuklandi: {$count} ta lot");
                        }
                    }
                } catch (\Exception $e) {
                    $this->command->error("Qator " . ($rowIndex + 2) . " xatolik: " . $e->getMessage());
                    $this->skippedRecords[] = [
                        'sabab' => 'Exception xatolik',
                        'lot_raqami' => $lotRaqami,
                        'qator' => $rowIndex + 2,
                        'qoshimcha' => $e->getMessage()
                    ];
                    continue;
                }
            }

            $this->command->info("Jami {$count} ta lot yuklandi!");
            $this->writeLog("Muvaffaqiyatli yuklandi: {$count} ta lot");
        } catch (\Exception $e) {
            $this->command->error("Xatolik: " . $e->getMessage());
            $this->writeLog("KRITIK XATOLIK: " . $e->getMessage());
        }
    }

    private function createYerSotuv($row, $lotRaqami): ?YerSotuv
    {
        $auksionSana = $this->parseDate($row[15] ?? null);
        $shartnomaSana = $this->parseDate($row[26] ?? null);

        $data = [
            // Asosiy ma'lumotlar
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

            // Qurilish ma'lumotlari
            'qurilish_turi_1' => $this->cleanValue($row[10] ?? null),
            'qurilish_turi_2' => $this->cleanValue($row[11] ?? null),
            'qurilish_maydoni' => $this->parseNumber($row[12] ?? null),
            'investitsiya' => $this->parseNumber($row[13] ?? null),

            // Auksion ma'lumotlari
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

            // Shartnoma ma'lumotlari
            'shartnoma_holati' => $this->cleanValue($row[25] ?? null),
            'shartnoma_sana' => $shartnomaSana,
            'shartnoma_raqam' => $this->cleanValue($row[27] ?? null),

            // Moliyaviy ma'lumotlar
            'golib_tolagan' => $this->parseNumber($row[28] ?? null),
            'buyurtmachiga_otkazilgan' => $this->parseNumber($row[29] ?? null),
            'chegirma' => $this->parseNumber($row[30] ?? null),
            'auksion_harajati' => $this->parseNumber($row[31] ?? null),
            'tushadigan_mablagh' => $this->parseNumber($row[32] ?? null),
            'davaktiv_jamgarmasi' => $this->parseNumber($row[33] ?? null),
            'shartnoma_tushgan' => $this->parseNumber($row[34] ?? null),
            'davaktivda_turgan' => $this->parseNumber($row[35] ?? null),
            'yer_auksion_harajat' => $this->parseNumber($row[36] ?? null),

            // Taqsimot - tushadigan
            'mahalliy_byudjet_tushadigan' => $this->parseNumber($row[37] ?? null),
            'jamgarma_tushadigan' => $this->parseNumber($row[38] ?? null),
            'yangi_oz_direksiya_tushadigan' => $this->parseNumber($row[39] ?? null),
            'shayxontohur_tushadigan' => $this->parseNumber($row[40] ?? null),

            // Taqsimot - taqsimlangan
            'mahalliy_byudjet_taqsimlangan' => $this->parseNumber($row[41] ?? null),
            'jamgarma_taqsimlangan' => $this->parseNumber($row[42] ?? null),
            'yangi_oz_direksiya_taqsimlangan' => $this->parseNumber($row[43] ?? null),
            'shayxontohur_taqsimlangan' => $this->parseNumber($row[44] ?? null),

            // Qoldiq
            'qoldiq_mahalliy_byudjet' => $this->parseNumber($row[45] ?? null),
            'qoldiq_jamgarma' => $this->parseNumber($row[46] ?? null),
            'qoldiq_yangi_oz_direksiya' => $this->parseNumber($row[47] ?? null),
            'qoldiq_shayxontohur' => $this->parseNumber($row[48] ?? null),

            'farqi' => $this->parseNumber($row[49] ?? null),
            'shartnoma_summasi' => $this->parseNumber($row[50] ?? null),

            'yil' => $auksionSana ? Carbon::parse($auksionSana)->year : date('Y')
        ];

        return YerSotuv::create($data);
    }

   private function createGrafikTolovlar($row, $yerSotuv): void
{
    $grafikData = [
        2024 => [
            2 => 51, 3 => 52, 4 => 53, 5 => 54, 6 => 55, 7 => 56,
            8 => 57, 9 => 58, 10 => 59, 11 => 60, 12 => 61
        ],
        2025 => [
            1 => 62, 2 => 63, 3 => 64, 4 => 65, 5 => 66, 6 => 67,
            7 => 68, 8 => 69, 9 => 70, 10 => 71, 11 => 72, 12 => 73
        ],
        2026 => [
            1 => 74, 2 => 75, 3 => 76, 4 => 77, 5 => 78, 6 => 79,
            7 => 80, 8 => 81, 9 => 82, 10 => 83, 11 => 84, 12 => 85
        ],
        2027 => [
            1 => 86, 2 => 87, 3 => 88, 4 => 89, 5 => 90, 6 => 91,
            7 => 92, 8 => 93, 9 => 94, 10 => 95, 11 => 96, 12 => 97
        ],
        2028 => [
            1 => 98, 2 => 99, 3 => 100, 4 => 101, 5 => 102, 6 => 103,
            7 => 104, 8 => 105, 9 => 106, 10 => 107, 11 => 108, 12 => 109
        ],
        2029 => [
            1 => 110, 2 => 111, 3 => 112, 4 => 113, 5 => 114, 6 => 115,
            7 => 116, 8 => 117, 9 => 118, 10 => 119, 11 => 120, 12 => 121
        ]
    ];

    // Step 1: Collect all months with data and find first/last payment month
    $monthsWithData = [];
    $firstPaymentMonth = null;
    $lastPaymentMonth = null;

    foreach ($grafikData as $yil => $oylar) {
        foreach ($oylar as $oy => $ustunIndex) {
            if (!isset($row[$ustunIndex])) {
                continue;
            }

            $summa = $this->parseNumber($row[$ustunIndex]);

            if ($summa !== null && $summa > 0) {
                $currentMonth = Carbon::create($yil, $oy, 1);
                $monthsWithData[] = [
                    'date' => $currentMonth,
                    'yil' => $yil,
                    'oy' => $oy,
                    'summa' => $summa
                ];

                // Track first and last payment months
                if ($firstPaymentMonth === null || $currentMonth->lt($firstPaymentMonth)) {
                    $firstPaymentMonth = $currentMonth;
                }
                if ($lastPaymentMonth === null || $currentMonth->gt($lastPaymentMonth)) {
                    $lastPaymentMonth = $currentMonth;
                }
            }
        }
    }

    // If no payment data found, skip
    if (empty($monthsWithData)) {
        return;
    }

    // Step 2: Create records for ALL months between first and last payment
    $grafikCount = 0;
    $currentDate = $firstPaymentMonth->copy();

    while ($currentDate->lte($lastPaymentMonth)) {
        $yil = $currentDate->year;
        $oy = $currentDate->month;

        // Find if this month has payment data
        $summa = 0;
        foreach ($monthsWithData as $monthData) {
            if ($monthData['yil'] == $yil && $monthData['oy'] == $oy) {
                $summa = $monthData['summa'];
                break;
            }
        }

        // Create record for this month (with data or 0)
        GrafikTolov::create([
            'yer_sotuv_id' => $yerSotuv->id,
            'lot_raqami' => $yerSotuv->lot_raqami,
            'yil' => $yil,
            'oy' => $oy,
            'oy_nomi' => $this->oyNomlari[$oy],
            'grafik_summa' => $summa
        ]);

        $grafikCount++;
        $currentDate->addMonth();
    }

    if ($grafikCount > 0) {
        $this->command->info("  LOT {$yerSotuv->lot_raqami}: {$grafikCount} ta grafik to'lov ({$firstPaymentMonth->format('Y-m')} dan {$lastPaymentMonth->format('Y-m')} gacha)");
    }
}

    private function importFaktTolovlar(): void
    {
        $file = storage_path('app/excel/Тушум 2024-2025.xlsx');

        if (!file_exists($file)) {
            $this->command->error("Fakt to'lovlar fayli topilmadi");
            $this->writeLog("XATOLIK: Fakt to'lovlar fayli topilmadi - $file");
            return;
        }

        try {
            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header
            array_shift($rows);

            $this->command->info("Fakt to'lovlar yuklanmoqda...");
            $this->writeLog("\n=== FAKT TO'LOVLAR IMPORT ===");
            $this->writeLog("Fayl: Тушум 2024-2025.xlsx");

            $count = 0;
            $skipped = 0;

            foreach ($rows as $rowIndex => $row) {
                if (empty(array_filter($row))) {
                    $this->skippedRecords[] = [
                        'sabab' => 'Bo\'sh qator (Fakt to\'lovlar)',
                        'qator' => $rowIndex + 2
                    ];
                    $skipped++;
                    continue;
                }

                // Extract LOT number from column 7 (index 7)
                $lotRaqami = $this->extractLotRaqami($row[7] ?? '');

                if (!$lotRaqami) {
                    $this->skippedRecords[] = [
                        'sabab' => 'LOT raqami topilmadi (Fakt to\'lovlar)',
                        'qator' => $rowIndex + 2,
                        'qoshimcha' => 'Ustun H: ' . ($row[7] ?? 'bo\'sh')
                    ];
                    $skipped++;
                    continue;
                }

                // Check if LOT exists in yer_sotuv
                if (!YerSotuv::where('lot_raqami', $lotRaqami)->exists()) {
                    if (!in_array($lotRaqami, $this->notFoundLots)) {
                        $this->notFoundLots[] = $lotRaqami;
                    }
                    $this->skippedRecords[] = [
                        'sabab' => 'LOT bazada topilmadi (Fakt to\'lovlar)',
                        'lot_raqami' => $lotRaqami,
                        'qator' => $rowIndex + 2
                    ];
                    $skipped++;
                    continue;
                }

                $tolovSana = $this->parseDate($row[0] ?? null);
                if (!$tolovSana) {
                    $tolovSana = Carbon::now()->format('Y-m-d');
                }

                FaktTolov::create([
                    'lot_raqami' => $lotRaqami,
                    'tolov_sana' => $tolovSana,
                    'hujjat_raqam' => $this->cleanValue($row[1] ?? null),
                    'tolash_nom' => $this->cleanValue($row[2] ?? null),
                    'tolash_hisob' => $this->cleanValue($row[3] ?? null),
                    'tolash_inn' => $this->cleanValue($row[4] ?? null),
                    'tolov_summa' => $this->parseNumber($row[5] ?? null),
                    'detali' => $this->cleanValue($row[6] ?? null)
                ]);

                $count++;

                if ($count % 100 == 0) {
                    $this->command->info("  Yuklandi: {$count} ta to'lov");
                }
            }

            $this->command->info("Jami {$count} ta to'lov yuklandi!");
            $this->writeLog("Muvaffaqiyatli yuklandi: {$count} ta to'lov");

            if ($skipped > 0) {
                $this->command->warn("{$skipped} ta o'tkazib yuborildi");
                $this->writeLog("O'tkazib yuborildi: {$skipped} ta");
            }
        } catch (\Exception $e) {
            $this->command->error("Xatolik: " . $e->getMessage());
            $this->writeLog("KRITIK XATOLIK: " . $e->getMessage());
            $this->command->error("Stack trace: " . $e->getTraceAsString());
        }
    }

    private function parseLotNumber($value): ?string
    {
        if ($value === null || $value === '') return null;

        // Remove commas, spaces, dots
        if (is_string($value)) {
            $cleaned = str_replace([',', ' ', '.'], '', trim($value));
            if (is_numeric($cleaned)) {
                return $cleaned;
            }
        }

        // If it's already numeric
        if (is_numeric($value)) {
            return (string)round($value);
        }

        return null;
    }

    private function extractLotRaqami($text): ?string
    {
        if (empty($text)) return null;

        $text = trim($text);

        // First, remove commas and spaces from numbers (e.g., "3,808,404" → "3808404")
        $cleanedText = preg_replace('/(\d+),(\d+)/', '$1$2', $text);
        $cleanedText = preg_replace('/(\d+)\s+(\d+)/', '$1$2', $cleanedText);

        // Pattern 1: L[number]L (e.g., L10889408L)
        if (preg_match('/L(\d+)L/i', $cleanedText, $matches)) {
            return $matches[1];
        }

        // Pattern 2: L[number] (e.g., L10889408)
        if (preg_match('/L(\d+)/i', $cleanedText, $matches)) {
            return $matches[1];
        }

        // Pattern 3: [number]L (e.g., 10889408L)
        if (preg_match('/(\d+)L/i', $cleanedText, $matches)) {
            return $matches[1];
        }

        // Pattern 4: LOT [number] (e.g., LOT 10889408)
        if (preg_match('/LOT\s*(\d+)/i', $cleanedText, $matches)) {
            return $matches[1];
        }

        // Pattern 5: Find any 6+ digit number in text (lowered from 7 to catch shorter LOTs)
        // This will catch: 3808404, 10889408, 13450799, etc.
        if (preg_match('/\b(\d{6,})\b/', $cleanedText, $matches)) {
            return $matches[1];
        }

        // Pattern 6: If it's just a number after cleaning (e.g., "10889408")
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

        if (is_string($value)) {
            // Remove formatting characters
            $value = str_replace([',', ' ', "'"], '', trim($value));
        }

        return is_numeric($value) ? (float)$value : null;
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

            // String date formats
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
            $this->command->warn("Sanani parse qilishda xatolik: {$value}");
            $this->writeLog("OGOHLANTIRISH: Sanani parse qilishda xatolik - {$value}");
            return null;
        }
    }
}
