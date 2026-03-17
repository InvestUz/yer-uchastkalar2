<?php

namespace Database\Seeders;

use App\Models\DavaktivRasxod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DavaktivRasxodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // District patterns (same as controller)
    private array $districtPatterns = [
        'бектемир'     => 'Бектемир',
        'миробод'      => 'Миробод',
        'олмазор'      => 'Олмазор',
        'сергели'      => 'Сергели',
        'сирғали'      => 'Сергели',
        'сиргали'      => 'Сергели',
        'учтепа'       => 'Учтепа',
        'шайҳонтохур'  => 'Шайҳонтохур',
        'шайхонтохур'  => 'Шайҳонтохур',
        'шайхонтахур'  => 'Шайҳонтохур',
        'шайхонтаур'   => 'Шайҳонтохур',
        'юнусобод'     => 'Юнусобод',
        'яккасарой'    => 'Яккасарой',
        'чилонзор'     => 'Чилонзор',
        'мирзоулугбек' => 'Мирзо Улугбек',
        'мирзоулубек'  => 'Мирзо Улугбек',
        'мирзоулуғбек' => 'Мирзо Улугбек',
        'яшнобод'      => 'Яшнобод',
        'янгихаёт'     => 'Янги Хаёт',
        'янгиҳаёт'     => 'Янги Хаёт',
        'янгихает'     => 'Янги Хаёт',
    ];

    private array $categoryDistrictFallbacks = [
        'ЯнгиХаёт индустриал технопарки дирекциясига' => 'Янги Хаёт',
        'Шайҳонтохур туманига'                         => 'Шайҳонтохур',
    ];

    public function run(): void
    {
        $csvPath = storage_path('app/excel/davaktiv_rasxod.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("CSV файл топилмади: {$csvPath}");
            return;
        }

        try {
            // Clear existing data
            DavaktivRasxod::truncate();

                // Load manual district overrides (doc_number => district)
                $overrides = $this->loadDistrictOverrides();

            $handle = fopen($csvPath, 'r');
            $rowNum = 0;
            $headers = [];
            $insertCount = 0;
            $batchData = [];
            $batchSize = 100; // Insert in chunks of 100

            while (($row = fgetcsv($handle, 0, ';')) !== FALSE) {
                $rowNum++;

                // Set headers from first row
                if ($rowNum === 1) {
                    $headers = array_map('trim', $row);
                    continue;
                }

                // Skip empty rows
                if (empty($row[0])) {
                    continue;
                }

                // Build record from CSV row
                $record = [];
                foreach ($headers as $idx => $header) {
                    $record[$header] = isset($row[$idx]) ? trim($row[$idx]) : null;
                }

                // Parse data
                $amount = $this->parseAmount($record['Сумма'] ?? 0);

                if ($amount > 0) {
                    // Parse date
                    $docDate = null;
                    if (!empty($record['Дата документа'])) {
                        try {
                            $docDate = Carbon::createFromFormat('d.m.Y', $record['Дата документа'])->format('Y-m-d');
                        } catch (\Exception $e) {
                            $docDate = null;
                        }
                    }

                        $docNumber = $record['№ документа'] ?? null;
                        $article   = $record['Статья'] ?? null;
                        $details   = $record['Детали документа'] ?? null;

                        // Auto-detect district from details text, then check manual overrides
                        $district = $this->detectDistrict($details, $article);
                        if ($district === null && $docNumber && isset($overrides[$docNumber])) {
                            $district = $overrides[$docNumber];
                        }

                    $batchData[] = [
                        'doc_date' => $docDate,
                        'month' => $record['месяц'] ?? null,
                            'doc_number' => $docNumber,
                        'recipient_name' => $record['Наименование получателя'] ?? null,
                            'article' => $article,
                        'account_number' => $record['Счет получателя'] ?? null,
                        'bank_code' => $record['Код банка получателя'] ?? null,
                        'amount' => $amount,
                            'details' => $details,
                        'by_articles' => $record['По статьям'] ?? null,
                            'district' => $district,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Insert in batches
                    if (count($batchData) >= $batchSize) {
                        DavaktivRasxod::insert($batchData);
                        $insertCount += count($batchData);
                        $batchData = [];
                    }
                }
            }

            // Insert remaining data
            if (count($batchData) > 0) {
                DavaktivRasxod::insert($batchData);
                $insertCount += count($batchData);
            }

            fclose($handle);

            $this->command->info("✅ Davaktiv rasxod seed мувоффақияти ўтказилди! {$insertCount} та ёзувлар қўшилди.");

        } catch (\Exception $e) {
            $this->command->error("❌ Seed алоқасида хато: " . $e->getMessage());
            \Log::error('DavaktivRasxodSeeder error: ' . $e->getMessage());
        }
    }

    /**
     * Parse amount from string (handles various formats)
     */
    private function parseAmount($amountStr)
    {
        if (empty($amountStr)) {
            return 0;
        }

        // Remove spaces, commas, and convert to float
        $amount = (string)$amountStr;
        $amount = str_replace([' ', ','], '', $amount);

        return (float)$amount;
    }

        private function detectDistrict(?string $details, ?string $article): ?string
        {
            if ($details) {
                $lower      = mb_strtolower($details, 'UTF-8');
                $normalized = preg_replace('/[^\p{L}\p{N}]+/u', '', $lower) ?? $lower;
                foreach ($this->districtPatterns as $pattern => $districtName) {
                    if ($normalized !== '' && strpos($normalized, $pattern) !== false) {
                        return $districtName;
                    }
                }
            }
            if ($article && isset($this->categoryDistrictFallbacks[$article])) {
                return $this->categoryDistrictFallbacks[$article];
            }
            return null;
        }

        private function loadDistrictOverrides(): array
        {
            $path = storage_path('app/district_overrides.csv');
            if (!file_exists($path)) {
                return [];
            }
            $overrides = [];
            $handle    = fopen($path, 'r');
            $first     = true;
            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                if ($first) { $first = false; continue; } // skip header
                $docNum   = trim($row[0] ?? '');
                $district = trim($row[2] ?? ''); // col 0=doc_number, 1=category, 2=district
                if ($docNum !== '' && $district !== '') {
                    $overrides[$docNum] = $district;
                }
            }
            fclose($handle);
            return $overrides;
        }
}
