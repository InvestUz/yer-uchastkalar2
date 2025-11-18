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

        // Prepare summary statistics
        $totalNotFound = count($this->notFoundLots);
        $totalSkipped = count($this->skippedRecords);

        // Count successful imports
        $totalYerSotuv = YerSotuv::count();
        $totalGrafik = GrafikTolov::count();
        $totalFakt = FaktTolov::count();

        // Group skipped records by reason
        $groupedByReason = [];
        foreach ($this->skippedRecords as $record) {
            $reason = $record['sabab'];
            if (!isset($groupedByReason[$reason])) {
                $groupedByReason[$reason] = 0;
            }
            $groupedByReason[$reason]++;
        }

        // Create summary table
        $this->writeLog("\n╔═══════════════════════════════════════════════════════════════════════════╗");
        $this->writeLog("║                         IMPORT STATISTIKASI                               ║");
        $this->writeLog("╠═══════════════════════════════════════════════════════════════════════════╣");
        $this->writeLog(sprintf("║ %-50s │ %23s ║", "JAMI YUKLANGAN MA'LUMOTLAR", ""));
        $this->writeLog("╟───────────────────────────────────────────────────────────────────────────╢");
        $this->writeLog(sprintf("║ %-50s │ %23s ║", "  • Yer sotuv (LOT) yozuvlari", str_pad($totalYerSotuv . " ta", 23)));
        $this->writeLog(sprintf("║ %-50s │ %23s ║", "  • Grafik to'lovlar", str_pad($totalGrafik . " ta", 23)));
        $this->writeLog(sprintf("║ %-50s │ %23s ║", "  • Fakt to'lovlar", str_pad($totalFakt . " ta", 23)));
        $this->writeLog("╠═══════════════════════════════════════════════════════════════════════════╣");
        $this->writeLog(sprintf("║ %-50s │ %23s ║", "TOPILMAGAN LOT RAQAMLAR", str_pad($totalNotFound . " ta", 23)));
        $this->writeLog("╠═══════════════════════════════════════════════════════════════════════════╣");
        $this->writeLog(sprintf("║ %-50s │ %23s ║", "O'TKAZIB YUBORILGAN YOZUVLAR", str_pad($totalSkipped . " ta", 23)));
        $this->writeLog("╟───────────────────────────────────────────────────────────────────────────╢");

        foreach ($groupedByReason as $reason => $count) {
            $reasonShort = mb_substr($reason, 0, 47);
            if (mb_strlen($reason) > 47) $reasonShort .= "...";
            $this->writeLog(sprintf("║   %-48s │ %23s ║", $reasonShort, str_pad($count . " ta", 23)));
        }

        $this->writeLog("╚═══════════════════════════════════════════════════════════════════════════╝");

        // Detailed lists if needed
        if (!empty($this->notFoundLots) && $totalNotFound <= 20) {
            $this->writeLog("\n### TOPILMAGAN LOT RAQAMLAR RO'YXATI ###");
            foreach ($this->notFoundLots as $lot) {
                $this->writeLog("  - LOT {$lot}");
            }
        } elseif ($totalNotFound > 20) {
            $this->writeLog("\n### TOPILMAGAN LOT RAQAMLAR (Jami {$totalNotFound} ta - faqat birinchi 20 ta) ###");
            foreach (array_slice($this->notFoundLots, 0, 20) as $lot) {
                $this->writeLog("  - LOT {$lot}");
            }
            $this->writeLog("  ... va yana " . ($totalNotFound - 20) . " ta");
        }

        $this->writeLog("\n" . str_repeat("=", 80));
        $this->writeLog("Yakunlandi: " . now()->format('Y-m-d H:i:s'));
        $this->writeLog(str_repeat("=", 80));
    }

    private function importAsosiyMalumot(): void
    {
        $file = storage_path('app/excel/Sotilgan_yerlar_18_11_2025_Bazaga(Abdulazizga).xlsx');

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
            $this->writeLog("Fayl: Sotilgan_yerlar_18_11_2025_Bazaga(Abdulazizga).xlsx");

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
            'shartnoma_summasi' => 0, // Will be calculated from grafik_tolovlar

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
        // CORRECT column mapping based on actual Excel structure
        // Column index 50 = "Шартнома бўйича тушадиган" (NOT a monthly payment)
        // Monthly payments start from column index 51 (2022 January)

        $grafikData = [
            2022 => [
                1 => 51,   // 2022 yanvar
                2 => 52,   // 2022 fevral
                3 => 53,   // 2022 mart
                4 => 54,   // 2022 aprel
                5 => 55,   // 2022 may
                6 => 56,   // 2022 iyun
                7 => 57,   // 2022 iyul
                8 => 58,   // 2022 avgust
                9 => 59,   // 2022 sentabr
                10 => 60,  // 2022 oktabr
                11 => 61,  // 2022 noyabr
                12 => 62   // 2022 dekabr
            ],
            2023 => [
                1 => 63,   // 2023 yanvar
                2 => 64,   // 2023 fevral
                3 => 65,   // 2023 mart
                4 => 66,   // 2023 aprel
                5 => 67,   // 2023 may
                6 => 68,   // 2023 iyun
                7 => 69,   // 2023 iyul
                8 => 70,   // 2023 avgust
                9 => 71,   // 2023 sentabr
                10 => 72,  // 2023 oktabr
                11 => 73,  // 2023 noyabr
                12 => 74   // 2023 dekabr
            ],
            2024 => [
                1 => 75,   // 2024 yanvar
                2 => 76,   // 2024 fevral
                3 => 77,   // 2024 mart
                4 => 78,   // 2024 aprel
                5 => 79,   // 2024 may
                6 => 80,   // 2024 iyun
                7 => 81,   // 2024 iyul
                8 => 82,   // 2024 avgust
                9 => 83,   // 2024 sentabr
                10 => 84,  // 2024 oktabr
                11 => 85,  // 2024 noyabr
                12 => 86   // 2024 dekabr
            ],
            2025 => [
                1 => 87,   // 2025 yanvar
                2 => 88,   // 2025 fevral
                3 => 89,   // 2025 mart
                4 => 90,   // 2025 aprel
                5 => 91,   // 2025 may
                6 => 92,   // 2025 iyun
                7 => 93,   // 2025 iyul
                8 => 94,   // 2025 avgust
                9 => 95,   // 2025 sentabr
                10 => 96,  // 2025 oktabr
                11 => 97,  // 2025 noyabr
                12 => 98   // 2025 dekabr
            ],
            2026 => [
                1 => 99,   // 2026 yanvar
                2 => 100,  // 2026 fevral
                3 => 101,  // 2026 mart
                4 => 102,  // 2026 aprel
                5 => 103,  // 2026 may
                6 => 104,  // 2026 iyun
                7 => 105,  // 2026 iyul
                8 => 106,  // 2026 avgust
                9 => 107,  // 2026 sentabr
                10 => 108, // 2026 oktabr
                11 => 109, // 2026 noyabr
                12 => 110  // 2026 dekabr
            ],
            2027 => [
                1 => 111,  // 2027 yanvar
                2 => 112,  // 2027 fevral
                3 => 113,  // 2027 mart
                4 => 114,  // 2027 aprel
                5 => 115,  // 2027 may
                6 => 116,  // 2027 iyun
                7 => 117,  // 2027 iyul
                8 => 118,  // 2027 avgust
                9 => 119,  // 2027 sentabr
                10 => 120, // 2027 oktabr
                11 => 121, // 2027 noyabr
                12 => 122  // 2027 dekabr
            ],
            2028 => [
                1 => 123,  // 2028 yanvar
                2 => 124,  // 2028 fevral
                3 => 125,  // 2028 mart
                4 => 126,  // 2028 aprel
                5 => 127,  // 2028 may
                6 => 128,  // 2028 iyun
                7 => 129,  // 2028 iyul
                8 => 130,  // 2028 avgust
                9 => 131,  // 2028 sentabr
                10 => 132, // 2028 oktabr
                11 => 133, // 2028 noyabr
                12 => 134  // 2028 dekabr
            ],
            2029 => [
                1 => 135,  // 2029 yanvar
                2 => 136,  // 2029 fevral
                3 => 137,  // 2029 mart
                4 => 138,  // 2029 aprel
                5 => 139,  // 2029 may
                6 => 140,  // 2029 iyun
                7 => 141,  // 2029 iyul
                8 => 142,  // 2029 avgust
                9 => 143,  // 2029 sentabr
                10 => 144, // 2029 oktabr
                11 => 145, // 2029 noyabr
                12 => 146  // 2029 dekabr
            ]
        ];

        $totalSumma = 0;

        foreach ($grafikData as $yil => $oylar) {
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
        // CORRECT column mapping - monthly payments start from index 51
        $grafikData = [
            2022 => [
                1 => 51,   // 2022 yanvar - Column AZ (index 51)
                2 => 52,   // 2022 fevral
                3 => 53,   // 2022 mart
                4 => 54,   // 2022 aprel
                5 => 55,   // 2022 may
                6 => 56,   // 2022 iyun
                7 => 57,   // 2022 iyul
                8 => 58,   // 2022 avgust
                9 => 59,   // 2022 sentabr
                10 => 60,  // 2022 oktabr
                11 => 61,  // 2022 noyabr
                12 => 62   // 2022 dekabr
            ],
            2023 => [
                1 => 63,   // 2023 yanvar
                2 => 64,   // 2023 fevral
                3 => 65,   // 2023 mart
                4 => 66,   // 2023 aprel
                5 => 67,   // 2023 may
                6 => 68,   // 2023 iyun
                7 => 69,   // 2023 iyul
                8 => 70,   // 2023 avgust
                9 => 71,   // 2023 sentabr
                10 => 72,  // 2023 oktabr
                11 => 73,  // 2023 noyabr
                12 => 74   // 2023 dekabr
            ],
            2024 => [
                1 => 75,   // 2024 yanvar
                2 => 76,   // 2024 fevral
                3 => 77,   // 2024 mart
                4 => 78,   // 2024 aprel
                5 => 79,   // 2024 may
                6 => 80,   // 2024 iyun
                7 => 81,   // 2024 iyul
                8 => 82,   // 2024 avgust
                9 => 83,   // 2024 sentabr
                10 => 84,  // 2024 oktabr
                11 => 85,  // 2024 noyabr
                12 => 86   // 2024 dekabr
            ],
            2025 => [
                1 => 87,   // 2025 yanvar
                2 => 88,   // 2025 fevral
                3 => 89,   // 2025 mart
                4 => 90,   // 2025 aprel
                5 => 91,   // 2025 may
                6 => 92,   // 2025 iyun
                7 => 93,   // 2025 iyul
                8 => 94,   // 2025 avgust
                9 => 95,   // 2025 sentabr
                10 => 96,  // 2025 oktabr
                11 => 97,  // 2025 noyabr
                12 => 98   // 2025 dekabr
            ],
            2026 => [
                1 => 99,   // 2026 yanvar
                2 => 100,  // 2026 fevral
                3 => 101,  // 2026 mart
                4 => 102,  // 2026 aprel
                5 => 103,  // 2026 may
                6 => 104,  // 2026 iyun
                7 => 105,  // 2026 iyul
                8 => 106,  // 2026 avgust
                9 => 107,  // 2026 sentabr
                10 => 108, // 2026 oktabr
                11 => 109, // 2026 noyabr
                12 => 110  // 2026 dekabr
            ],
            2027 => [
                1 => 111,  // 2027 yanvar
                2 => 112,  // 2027 fevral
                3 => 113,  // 2027 mart
                4 => 114,  // 2027 aprel
                5 => 115,  // 2027 may
                6 => 116,  // 2027 iyun
                7 => 117,  // 2027 iyul
                8 => 118,  // 2027 avgust
                9 => 119,  // 2027 sentabr
                10 => 120, // 2027 oktabr
                11 => 121, // 2027 noyabr
                12 => 122  // 2027 dekabr
            ],
            2028 => [
                1 => 123,  // 2028 yanvar
                2 => 124,  // 2028 fevral
                3 => 125,  // 2028 mart
                4 => 126,  // 2028 aprel
                5 => 127,  // 2028 may
                6 => 128,  // 2028 iyun
                7 => 129,  // 2028 iyul
                8 => 130,  // 2028 avgust
                9 => 131,  // 2028 sentabr
                10 => 132, // 2028 oktabr
                11 => 133, // 2028 noyabr
                12 => 134  // 2028 dekabr
            ],
            2029 => [
                1 => 135,  // 2029 yanvar
                2 => 136,  // 2029 fevral
                3 => 137,  // 2029 mart
                4 => 138,  // 2029 aprel
                5 => 139,  // 2029 may
                6 => 140,  // 2029 iyun
                7 => 141,  // 2029 iyul
                8 => 142,  // 2029 avgust
                9 => 143,  // 2029 sentabr
                10 => 144, // 2029 oktabr
                11 => 145, // 2029 noyabr
                12 => 146  // 2029 dekabr
            ]
        ];

        // Step 1: Collect all months with data and find first/last payment month
        $monthsWithData = [];
        $firstPaymentMonth = null;
        $lastPaymentMonth = null;

        foreach ($grafikData as $yil => $oylar) {
            foreach ($oylar as $oy => $ustunIndex) {
                // Get the value, treating NULL as 0
                $cellValue = $row[$ustunIndex] ?? null;
                $summa = $this->parseNumber($cellValue);

                // Convert NULL to 0 for consistency
                if ($summa === null) {
                    $summa = 0;
                }

                // Only track months with actual payment data (> 0) for range detection
                if ($summa > 0) {
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

        // If no payment data found, skip this LOT
        if (empty($monthsWithData)) {
            $this->writeLog("  LOT {$yerSotuv->lot_raqami}: Grafik to'lovlar topilmadi (barcha oylar bo'sh)");
            return;
        }

        // Log the payment schedule for verification
        $this->writeLog("\n  LOT {$yerSotuv->lot_raqami} - To'lov grafigi:");
        $this->writeLog("  Birinchi to'lov: {$firstPaymentMonth->format('Y-m')}");
        $this->writeLog("  Oxirgi to'lov: {$lastPaymentMonth->format('Y-m')}");
        $this->writeLog("  To'lovli oylar soni: " . count($monthsWithData));

        // Show sample payments
        $sampleCount = min(5, count($monthsWithData));
        for ($i = 0; $i < $sampleCount; $i++) {
            $month = $monthsWithData[$i];
            $this->writeLog(sprintf("    %s: %s",
                $month['date']->format('Y-m'),
                number_format($month['summa'], 0, '.', ',')
            ));
        }
        if (count($monthsWithData) > 5) {
            $this->writeLog("    ... va yana " . (count($monthsWithData) - 5) . " ta oy");
        }

        // Step 2: Create records for ALL months between first and last payment
        $grafikCount = 0;
        $monthsWithPayment = 0;
        $totalScheduled = 0;
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

            // Create record for this month (with summa or 0)
            GrafikTolov::create([
                'yer_sotuv_id' => $yerSotuv->id,
                'lot_raqami' => $yerSotuv->lot_raqami,
                'yil' => $yil,
                'oy' => $oy,
                'oy_nomi' => $this->oyNomlari[$oy],
                'grafik_summa' => $summa
            ]);

            $grafikCount++;
            if ($summa > 0) {
                $monthsWithPayment++;
                $totalScheduled += $summa;
            }

            $currentDate->addMonth();
        }

        if ($grafikCount > 0) {
            $this->command->info(sprintf(
                "  LOT %s: %d ta grafik (%s dan %s gacha, %d oyda to'lov, jami: %s)",
                $yerSotuv->lot_raqami,
                $grafikCount,
                $firstPaymentMonth->format('Y-m'),
                $lastPaymentMonth->format('Y-m'),
                $monthsWithPayment,
                number_format($totalScheduled, 0, '.', ',')
            ));
        }
    }

    private function importFaktTolovlar(): void
    {
        $file = storage_path('app/excel/Тушум 2024-2025-13.11.2025.xlsx');

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
            $this->writeLog("Fayl: Тушум 2024-2025-13.11.2025.xlsx");

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

        // Pattern 5: Find any 6+ digit number in text
        if (preg_match('/\b(\d{6,})\b/', $cleanedText, $matches)) {
            return $matches[1];
        }

        // Pattern 6: If it's just a number after cleaning
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
