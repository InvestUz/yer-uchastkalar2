<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;

class DiagnosticSeeder extends Seeder
{
    public function run(): void
    {
        $file = storage_path('app/excel/Sotilgan_yerlar_17_11_2025_Bazaga(Abdulazizga).xlsx');

        if (!file_exists($file)) {
            $this->command->error("Fayl topilmadi: $file");
            return;
        }

        $logFile = 'diagnostic_' . now()->format('Y-m-d_H-i-s') . '.txt';

        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Get headers
        $headers = $rows[0];

        $output = "=== EXCEL COLUMN DIAGNOSTIC ===\n\n";
        $output .= "Total columns: " . count($headers) . "\n\n";

        // Show column mapping for indices 45-150
        $output .= "COLUMN MAPPING (Index => Header):\n";
        $output .= str_repeat("-", 100) . "\n";

        for ($i = 45; $i <= min(150, count($headers) - 1); $i++) {
            $header = $headers[$i] ?? 'N/A';
            $output .= sprintf("Index %3d => %s\n", $i, $header);
        }

        $output .= "\n" . str_repeat("=", 100) . "\n";
        $output .= "FIRST 3 DATA ROWS (showing columns 45-70):\n";
        $output .= str_repeat("=", 100) . "\n\n";

        // Show first 3 data rows
        for ($rowIdx = 1; $rowIdx <= min(3, count($rows) - 1); $rowIdx++) {
            $row = $rows[$rowIdx];
            $lotNumber = $row[1] ?? 'N/A';

            $output .= "ROW {$rowIdx} - LOT: {$lotNumber}\n";
            $output .= str_repeat("-", 100) . "\n";

            for ($colIdx = 45; $colIdx <= min(70, count($row) - 1); $colIdx++) {
                $value = $row[$colIdx] ?? 'NULL';
                $header = $headers[$colIdx] ?? 'N/A';

                if ($value === null || $value === '') {
                    $displayValue = 'EMPTY';
                } elseif (is_numeric($value)) {
                    $displayValue = number_format($value, 2);
                } else {
                    $displayValue = substr($value, 0, 30);
                }

                $output .= sprintf("  [%3d] %-30s = %s\n", $colIdx, substr($header, 0, 30), $displayValue);
            }

            $output .= "\n";
        }

        // Check specifically for monthly payment columns
        $output .= "\n" . str_repeat("=", 100) . "\n";
        $output .= "MONTHLY PAYMENT COLUMNS CHECK (Row 1 - LOT " . ($rows[1][1] ?? 'N/A') . "):\n";
        $output .= str_repeat("=", 100) . "\n\n";

        $monthColumns = [
            '2022 янв' => 51,
            '2022 фев' => 52,
            '2022 март' => 53,
            '2023 янв' => 63,
            '2023 дек' => 74,
            '2024 янв' => 75,
            '2024 дек' => 86,
            '2025 янв' => 87,
            '2025 дек' => 98,
        ];

        foreach ($monthColumns as $expectedMonth => $colIdx) {
            $headerValue = $headers[$colIdx] ?? 'HEADER_NOT_FOUND';
            $dataValue = $rows[1][$colIdx] ?? 'DATA_NOT_FOUND';

            if (is_numeric($dataValue)) {
                $displayValue = number_format($dataValue, 2);
            } else {
                $displayValue = $dataValue;
            }

            $output .= sprintf("Expected: %-15s | Index: %3d | Header: %-20s | Value: %s\n",
                $expectedMonth,
                $colIdx,
                substr($headerValue, 0, 20),
                $displayValue
            );
        }

        // Find where actual payment data starts
        $output .= "\n" . str_repeat("=", 100) . "\n";
        $output .= "SEARCHING FOR PAYMENT DATA (Row 1):\n";
        $output .= str_repeat("=", 100) . "\n\n";

        $foundPayments = [];
        for ($i = 0; $i < count($rows[1]); $i++) {
            $value = $rows[1][$i];

            // Look for numeric values > 1000 after column 45
            if ($i >= 45 && is_numeric($value) && $value > 1000) {
                $foundPayments[] = [
                    'index' => $i,
                    'header' => $headers[$i] ?? 'N/A',
                    'value' => $value
                ];
            }
        }

        $output .= "Found " . count($foundPayments) . " potential payment columns:\n\n";
        foreach ($foundPayments as $payment) {
            $output .= sprintf("Index %3d | Header: %-30s | Value: %s\n",
                $payment['index'],
                substr($payment['header'], 0, 30),
                number_format($payment['value'], 2)
            );
        }

        // Save to storage
        Storage::put($logFile, $output);

        $this->command->info("\n" . str_repeat("=", 80));
        $this->command->info($output);
        $this->command->info(str_repeat("=", 80));
        $this->command->info("\nDiagnostika yakunlandi!");
        $this->command->info("To'liq log fayl: storage/app/{$logFile}");
    }
}
