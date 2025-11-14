<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TestExcelStructureSeeder extends Seeder
{
    public function run(): void
    {
        $file = storage_path('app/excel/Sotilgan_yerlar_13_11_2025_Bazaga+++.xlsx');

        if (!file_exists($file)) {
            $this->command->error("Fayl topilmadi: $file");
            return;
        }

        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Get header row
        $headers = $rows[0];

        $this->command->info("=== EXCEL TUZILISHI TEKSHIRUVI ===\n");

        // Show critical columns
        $criticalColumns = [
            49 => 'фарқи',
            50 => 'Шартнома бўйича тушадиган',
            51 => '2023 ноя',
            52 => '2023 дек',
            53 => '2024 янв',
            54 => '2024 фев',
        ];

        $this->command->info("MUHIM USTUNLAR (Index => Kutilgan nom):");
        foreach ($criticalColumns as $index => $expectedName) {
            $actualName = $headers[$index] ?? 'MAVJUD EMAS';
            $match = (trim($actualName) === $expectedName) ? '✅' : '❌';
            $this->command->line(sprintf(
                "  [%d] Kutilgan: %-35s | Haqiqiy: %-35s %s",
                $index,
                $expectedName,
                $actualName,
                $match
            ));
        }

        // Show first data row values for these columns
        if (count($rows) > 1) {
            $firstDataRow = $rows[1];

            $this->command->info("\nBIRINCHI QATOR MA'LUMOTLARI:");
            $this->command->line("  LOT raqami: " . ($firstDataRow[1] ?? 'N/A'));
            $this->command->line("  Tuman: " . ($firstDataRow[2] ?? 'N/A'));
            $this->command->line("  Tolov turi: " . ($firstDataRow[21] ?? 'N/A'));
            $this->command->line("  Golib to'lagan [28]: " . ($firstDataRow[28] ?? 'N/A'));
            $this->command->line("  Auksion harajati [31]: " . ($firstDataRow[31] ?? 'N/A'));
            $this->command->line("  Farqi [49]: " . ($firstDataRow[49] ?? 'N/A'));
            $this->command->line("  Shartnoma summasi [50]: " . ($firstDataRow[50] ?? 'N/A'));
            $this->command->line("  2023 noyabr [51]: " . ($firstDataRow[51] ?? 'N/A'));
            $this->command->line("  2023 dekabr [52]: " . ($firstDataRow[52] ?? 'N/A'));
            $this->command->line("  2024 yanvar [53]: " . ($firstDataRow[53] ?? 'N/A'));
        }

        // Count columns
        $this->command->info("\nUSTUNLAR SONI: " . count($headers));

        // Find where month columns start
        $this->command->info("\nOY USTUNLARINI QIDIRISH:");
        for ($i = 45; $i < min(60, count($headers)); $i++) {
            $headerName = trim($headers[$i] ?? '');
            if (preg_match('/(ноя|дек|янв|фев|март|апр|май|июнь|июль|авг|сент|окт)/i', $headerName)) {
                $this->command->line("  [$i] => $headerName");
            }
        }

        // Sample data analysis
        $this->command->info("\n=== SHARTNOMA SUMMASI TAHLILI ===");

        $totalRows = count($rows) - 1; // Exclude header
        $shartnomaSummaTotal = 0;
        $shartnomaSummaCount = 0;

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty(array_filter($row))) continue;

            $shartnomaSumma = $this->parseNumber($row[50] ?? null);
            if ($shartnomaSumma && $shartnomaSumma > 0) {
                $shartnomaSummaTotal += $shartnomaSumma;
                $shartnomaSummaCount++;
            }
        }

        $this->command->info("Jami qatorlar: $totalRows");
        $this->command->info("Shartnoma summasi > 0: $shartnomaSummaCount");
        $this->command->info("Jami shartnoma summasi: " . number_format($shartnomaSummaTotal, 2));
        $this->command->info("Millionlarda: " . number_format($shartnomaSummaTotal / 1000000, 2) . " million");
        $this->command->info("Milliardlarda: " . number_format($shartnomaSummaTotal / 1000000000, 2) . " milliard");
    }

    private function parseNumber($value): ?float
    {
        if ($value === null || $value === '') return null;

        if (is_string($value)) {
            $value = str_replace([',', ' ', "'"], '', trim($value));
        }

        return is_numeric($value) ? (float)$value : null;
    }
}
