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

class YerSotuvSeeder extends Seeder
{
    private $oyNomlari = [
        1 => 'yanvar', 2 => 'fevral', 3 => 'mart', 4 => 'aprel',
        5 => 'may', 6 => 'iyun', 7 => 'iyul', 8 => 'avgust',
        9 => 'sentabr', 10 => 'oktabr', 11 => 'noyabr', 12 => 'dekabr'
    ];

    private $notFoundLots = [];

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        FaktTolov::truncate();
        GrafikTolov::truncate();
        YerSotuv::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info("Ma'lumotlar o'chirildi. Import boshlanmoqda...");

        $this->importAsosiyMalumot();
        $this->importFaktTolovlar();

        if (!empty($this->notFoundLots)) {
            $this->command->warn("\n=== OGOHLANTIRISH: Topilmagan LOT raqamlar ===");
            foreach ($this->notFoundLots as $lot) {
                $this->command->error("LOT {$lot} ma'lumotlar bazasida topilmadi!");
            }
            $this->command->warn("Jami topilmagan: " . count($this->notFoundLots) . " ta\n");
        }

        $this->command->info("Import muvaffaqiyatli yakunlandi!");
    }

    private function importAsosiyMalumot(): void
    {
        $file = storage_path('app/excel/Sotilgan_yerlar_27_10_2025.xlsx');

        if (!file_exists($file)) {
            $this->command->error("Fayl topilmadi: $file");
            return;
        }

        try {
            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            array_shift($rows);

            $this->command->info("Asosiy ma'lumotlar yuklanmoqda...");

            $count = 0;
            foreach ($rows as $rowIndex => $row) {
                if (empty(array_filter($row))) continue;

                $lotRaqami = $this->parseLotNumber($row[1] ?? null);

                if (!$lotRaqami) {
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
                    continue;
                }
            }

            $this->command->info("Jami {$count} ta lot yuklandi!");

        } catch (\Exception $e) {
            $this->command->error("Xatolik: " . $e->getMessage());
        }
    }

    private function createYerSotuv($row, $lotRaqami): ?YerSotuv
    {
        $auksionSana = $this->parseDate($row[16] ?? null);
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
            'auksion_sana' => Carbon::parse($auksionSana) ?? $auksionSana,
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
            2024 => [8 => 52, 9 => 53, 10 => 54, 11 => 55, 12 => 56],
            2025 => [1 => 57, 2 => 58, 3 => 59, 4 => 60, 5 => 61, 6 => 62, 7 => 63, 8 => 64, 9 => 65, 10 => 66, 11 => 67, 12 => 68],
            2026 => [1 => 69, 2 => 70, 3 => 71, 4 => 72, 5 => 73, 6 => 74, 7 => 75, 8 => 76, 9 => 77, 10 => 78, 11 => 79, 12 => 80],
            2027 => [1 => 81, 2 => 82, 3 => 83, 4 => 84, 5 => 85, 6 => 86, 7 => 87, 8 => 88, 9 => 89, 10 => 90, 11 => 91, 12 => 92],
            2028 => [1 => 93, 2 => 94, 3 => 95, 4 => 96, 5 => 97, 6 => 98, 7 => 99, 8 => 100, 9 => 101, 10 => 102, 11 => 103, 12 => 104],
            2029 => [1 => 105, 2 => 106, 3 => 107, 4 => 108, 5 => 109, 6 => 110, 7 => 111, 8 => 112, 9 => 113, 10 => 114, 11 => 115, 12 => 116]
        ];

        foreach ($grafikData as $yil => $oylar) {
            foreach ($oylar as $oy => $ustunIndex) {
                $summa = $this->parseNumber($row[$ustunIndex] ?? null);

                if ($summa > 0) {
                    GrafikTolov::create([
                        'yer_sotuv_id' => $yerSotuv->id,
                        'lot_raqami' => $yerSotuv->lot_raqami,
                        'yil' => $yil,
                        'oy' => $oy,
                        'oy_nomi' => $this->oyNomlari[$oy],
                        'grafik_summa' => $summa
                    ]);
                }
            }
        }
    }

    private function importFaktTolovlar(): void
    {
        $file = storage_path('app/excel/Yer_2025-2024-fakt.xlsx');

        if (!file_exists($file)) {
            $this->command->error("Fakt to'lovlar fayli topilmadi");
            return;
        }

        try {
            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            array_shift($rows);

            $this->command->info("Fakt to'lovlar yuklanmoqda...");

            $count = 0;
            $skipped = 0;

            foreach ($rows as $row) {
                if (empty(array_filter($row))) continue;

                $lotRaqami = $this->extractLotRaqami($row[7] ?? '');

                if (!$lotRaqami) {
                    $skipped++;
                    continue;
                }

                if (!YerSotuv::where('lot_raqami', $lotRaqami)->exists()) {
                    if (!in_array($lotRaqami, $this->notFoundLots)) {
                        $this->notFoundLots[] = $lotRaqami;
                    }
                    $skipped++;
                    continue;
                }

                FaktTolov::create([
                    'lot_raqami' => $lotRaqami,
                    'tolov_sana' => $this->parseDate($row[0] ?? null) ?? Carbon::now()->format('Y-m-d'),
                    'hujjat_raqam' => $this->cleanValue($row[1] ?? null),
                    'tolash_nom' => $this->cleanValue($row[2] ?? null),
                    'tolash_hisob' => $this->cleanValue($row[3] ?? null),
                    'tolash_inn' => null,
                    'tolov_summa' => $this->parseNumber($row[5] ?? null),
                    'detali' => $this->cleanValue($row[6] ?? null)
                ]);

                $count++;

                if ($count % 100 == 0) {
                    $this->command->info("  Yuklandi: {$count} ta to'lov");
                }
            }

            $this->command->info("Jami {$count} ta to'lov yuklandi!");
            if ($skipped > 0) {
                $this->command->warn("{$skipped} ta o'tkazib yuborildi");
            }

        } catch (\Exception $e) {
            $this->command->error("Xatolik: " . $e->getMessage());
        }
    }

    private function parseLotNumber($value): ?string
    {
        if ($value === null || $value === '') return null;

        // String bo'lsa, vergul va bo'shliqlarni olib tashlash
        if (is_string($value)) {
            $cleaned = str_replace([',', ' ', '.'], '', trim($value));
            if (is_numeric($cleaned)) {
                return $cleaned;
            }
        }

        // Raqam bo'lsa
        if (is_numeric($value)) {
            return (string)$value;
        }

        return null;
    }

    private function extractLotRaqami($text): ?string
    {
        if (preg_match('/L(\d+)L/', $text, $matches)) {
            return $matches[1];
        }

        $text = trim($text);
        if (is_numeric($text)) {
            return $text;
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
            $value = str_replace([',', ' ', "'"], '', trim($value));
        }

        return is_numeric($value) ? (float)$value : null;
    }

    private function parseDate($value): ?string
    {
        if (!$value) return null;

        try {
            if (is_numeric($value) && $value > 0) {
                return Carbon::createFromTimestamp(Date::excelToTimestamp($value))->format('Y-m-d');
            }

            if (is_string($value)) {
                if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value)) {
                    return Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
                }
                return Carbon::parse($value)->format('Y-m-d');
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
