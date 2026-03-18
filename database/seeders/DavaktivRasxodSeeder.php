<?php

namespace Database\Seeders;

use App\Models\DavaktivRasxod;
use App\Models\FaktTolov;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

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

            // Load FaktTolov -> YerSotuv lookup for unmatched district inference
            $faktLookup = $this->loadFaktTolovDistrictLookup();

            $handle = fopen($csvPath, 'r');
            $rowNum = 0;
            $headers = [];
            $insertCount = 0;
            $batchData = [];
            $batchSize = 100; // Insert in chunks of 100
            $matchedByText = 0;
            $matchedByOverride = 0;
            $matchedByFakt = 0;
            $stillUnmatched = 0;

            while (($row = fgetcsv($handle, 0, ';')) !== FALSE) {
                $rowNum++;

                // Set headers from first row
                if ($rowNum === 1) {
                    $headers = array_map(function ($header) {
                        return $this->normalizeCsvHeader((string)$header);
                    }, $row);
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

                // Parse data (fallback to fixed column indexes if header keys are inconsistent)
                $docDateRaw = $record['Дата документа'] ?? ($row[0] ?? null);
                $monthRaw = $record['месяц'] ?? ($row[1] ?? null);
                $docNumberRaw = $record['№ документа'] ?? ($row[2] ?? null);
                $recipientRaw = $record['Наименование получателя'] ?? ($row[3] ?? null);
                $articleRaw = $record['Статья'] ?? ($row[4] ?? null);
                $accountRaw = $record['Счет получателя'] ?? ($row[5] ?? null);
                $bankCodeRaw = $record['Код банка получателя'] ?? ($row[6] ?? null);
                $amountRaw = $record['Сумма'] ?? ($row[7] ?? 0);
                $detailsRaw = $record['Детали документа'] ?? ($row[8] ?? null);
                $byArticlesRaw = $record['По статьям'] ?? ($row[9] ?? null);

                $amount = $this->parseAmount($amountRaw);

                if ($amount > 0) {
                    $docDate = $this->parseDocDate($docDateRaw);
                    $docNumber = $docNumberRaw !== null ? trim((string)$docNumberRaw) : null;
                    $article = $articleRaw !== null ? trim((string)$articleRaw) : null;
                    $details = $detailsRaw !== null ? trim((string)$detailsRaw) : null;
                    $month = $monthRaw !== null ? trim((string)$monthRaw) : null;
                    $recipient = $recipientRaw !== null ? trim((string)$recipientRaw) : null;
                    $accountNumber = $accountRaw !== null ? trim((string)$accountRaw) : null;
                    $bankCode = $bankCodeRaw !== null ? trim((string)$bankCodeRaw) : null;
                    $byArticles = $byArticlesRaw !== null ? trim((string)$byArticlesRaw) : null;

                    // 1) Text/category detection
                    $district = $this->detectDistrict($details, $article);
                    if ($district !== null) {
                        $matchedByText++;
                    }

                    // 2) Manual override for unresolved records
                    if ($district === null && $docNumber && isset($overrides[$docNumber])) {
                        $district = $overrides[$docNumber];
                        $matchedByOverride++;
                    }

                    // 3) FaktTolov/YerSotuv-based inference for still unresolved records
                    if ($district === null) {
                        $district = $this->detectDistrictFromFaktTolov(
                            $details,
                            $docNumber,
                            $docDate,
                            $amount,
                            $faktLookup
                        );
                        if ($district !== null) {
                            $matchedByFakt++;
                        } else {
                            $stillUnmatched++;
                        }
                    }

                    $batchData[] = [
                        'doc_date' => $docDate,
                        'month' => $month,
                        'doc_number' => $docNumber,
                        'recipient_name' => $recipient,
                        'article' => $article,
                        'account_number' => $accountNumber,
                        'bank_code' => $bankCode,
                        'amount' => $amount,
                        'details' => $details,
                        'by_articles' => $byArticles,
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
            $this->command->line("   - Text/category matches: {$matchedByText}");
            $this->command->line("   - Manual overrides     : {$matchedByOverride}");
            $this->command->line("   - FaktTolov matches    : {$matchedByFakt}");
            $this->command->line("   - Still unmatched      : {$stillUnmatched}");

        } catch (\Exception $e) {
            $this->command->error("❌ Seed алоқасида хато: " . $e->getMessage());
            Log::error('DavaktivRasxodSeeder error: ' . $e->getMessage());
        }
    }

    /**
     * Parse amount from string (handles various formats)
     */
    private function parseAmount($amountStr): float
    {
        if (empty($amountStr)) {
            return 0;
        }

        $amount = trim((string)$amountStr);
        if ($amount === '') {
            return 0;
        }

        // Normalize regular and non-breaking spaces used as thousand separators.
        $amount = str_replace(["\xC2\xA0", "\xE2\x80\xAF", ' '], '', $amount);
        $amount = preg_replace('/[^0-9,\.\-]/u', '', $amount) ?? '';

        if ($amount === '' || $amount === '-' || $amount === ',' || $amount === '.') {
            return 0;
        }

        $lastCommaPos = strrpos($amount, ',');
        $lastDotPos = strrpos($amount, '.');

        if ($lastCommaPos !== false && $lastDotPos !== false) {
            if ($lastCommaPos > $lastDotPos) {
                // Example: 1.234.567,89
                $amount = str_replace('.', '', $amount);
                $amount = str_replace(',', '.', $amount);
            } else {
                // Example: 1,234,567.89
                $amount = str_replace(',', '', $amount);
            }
        } elseif ($lastCommaPos !== false) {
            $fractionLength = strlen($amount) - $lastCommaPos - 1;
            if ($fractionLength <= 2) {
                // Example: 14549588074,80
                $amount = str_replace(',', '.', $amount);
            } else {
                // Example: 1,234,567
                $amount = str_replace(',', '', $amount);
            }
        } elseif ($lastDotPos !== false) {
            $fractionLength = strlen($amount) - $lastDotPos - 1;
            if ($fractionLength > 2) {
                // Example: 1.234.567
                $amount = str_replace('.', '', $amount);
            }
        }

        return (float)$amount;
    }

    private function parseDocDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        $rawDate = trim((string)$date);
        if ($rawDate === '') {
            return null;
        }

        $formats = ['d.m.Y', 'd,m,Y', 'Y-m-d'];
        try {
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $rawDate)->format('Y-m-d');
                } catch (\Exception $e) {
                    // Try next format.
                }
            }
        } catch (\Exception $e) {
            // Keep null when parsing fails.
        }

        return null;
    }

    private function normalizeCsvHeader(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/u', '', $header) ?? $header;
        $header = str_replace(["\xC2\xA0", "\xE2\x80\xAF"], ' ', $header);

        return trim($header);
    }

    private function detectDistrict(?string $details, ?string $article): ?string
    {
        if ($details) {
            $normalized = $this->normalizeText($details);
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
        $handle = fopen($path, 'r');
        $first = true;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if ($first) {
                $first = false;
                continue;
            }

            $docNum = trim($row[0] ?? '');
            $district = trim($row[2] ?? ''); // col 0=doc_number, 1=category, 2=district
            if ($docNum !== '' && $district !== '') {
                $overrides[$docNum] = $district;
            }
        }

        fclose($handle);
        return $overrides;
    }

    private function loadFaktTolovDistrictLookup(): array
    {
        $lookup = [
            'lot' => [],
            'doc' => [],
            'contract' => [],
            'amount' => [],
            'amount_date' => [],
        ];

        FaktTolov::query()
            ->with('yerSotuv:lot_raqami,tuman,shartnoma_raqam')
            ->select(['lot_raqami', 'tolov_sana', 'hujjat_raqam', 'tolov_summa'])
            ->chunk(1000, function ($payments) use (&$lookup) {
                foreach ($payments as $payment) {
                    $district = $this->normalizeDistrictFromTuman($payment->yerSotuv?->tuman);
                    if ($district === null) {
                        continue;
                    }

                    $lotToken = preg_replace('/\D+/u', '', (string)$payment->lot_raqami) ?? '';
                    if ($lotToken !== '' && strlen($lotToken) >= 6 && strlen($lotToken) <= 8) {
                        $lookup['lot'][$lotToken][$district] = true;
                    }

                    $docToken = preg_replace('/\D+/u', '', (string)$payment->hujjat_raqam) ?? '';
                    if ($docToken !== '' && strlen($docToken) >= 6) {
                        $lookup['doc'][$docToken][$district] = true;
                    }

                    $contractToken = $this->normalizeIdentifier($payment->yerSotuv?->shartnoma_raqam);
                    if ($contractToken !== '' && mb_strlen($contractToken, 'UTF-8') >= 3) {
                        $lookup['contract'][$contractToken][$district] = true;
                    }

                    $amountCents = (int)round(((float)$payment->tolov_summa) * 100);
                    if ($amountCents > 0) {
                        $lookup['amount'][$amountCents][$district] = true;

                        $paymentDate = $payment->tolov_sana
                            ? Carbon::parse($payment->tolov_sana)->format('Y-m-d')
                            : null;
                        if ($paymentDate !== null) {
                            $lookup['amount_date'][$paymentDate . '|' . $amountCents][$district] = true;
                        }
                    }
                }
            });

        return $lookup;
    }

    private function detectDistrictFromFaktTolov(
        ?string $details,
        ?string $docNumber,
        ?string $docDate,
        float $amount,
        array $lookup
    ): ?string {
        $detailsText = (string)($details ?? '');
        $detailsNormalized = $this->normalizeIdentifier($detailsText);

        // 1) Try lot number tokens (6-8 digits) found in details
        $lotCandidates = [];
        preg_match_all('/\d{6,8}/u', $detailsText, $lotMatches);
        foreach (array_unique($lotMatches[0] ?? []) as $token) {
            if (isset($lookup['lot'][$token])) {
                $this->addDistrictCandidates($lotCandidates, $lookup['lot'][$token]);
            }
        }
        $lotDistrict = $this->resolveUniqueDistrict($lotCandidates);
        if ($lotDistrict !== null) {
            return $lotDistrict;
        }

        // 2) Try direct doc number match against FaktTolov.hujjat_raqam
        $docNumberToken = preg_replace('/\D+/u', '', (string)($docNumber ?? '')) ?? '';
        if ($docNumberToken !== '' && isset($lookup['doc'][$docNumberToken])) {
            $docDistrict = $this->resolveUniqueDistrict($lookup['doc'][$docNumberToken]);
            if ($docDistrict !== null) {
                return $docDistrict;
            }
        }

        // 3) Try numeric tokens in details against FaktTolov.hujjat_raqam
        $docCandidates = [];
        preg_match_all('/\d{6,12}/u', $detailsText, $docMatches);
        foreach (array_unique($docMatches[0] ?? []) as $token) {
            if (isset($lookup['doc'][$token])) {
                $this->addDistrictCandidates($docCandidates, $lookup['doc'][$token]);
            }
        }
        $docDistrict = $this->resolveUniqueDistrict($docCandidates);
        if ($docDistrict !== null) {
            return $docDistrict;
        }

        // 4) Try contract number tokens from YerSotuv.shartnoma_raqam inside details text
        if ($detailsNormalized !== '') {
            $contractCandidates = [];
            foreach ($lookup['contract'] as $contractToken => $districtSet) {
                if (strpos($detailsNormalized, $contractToken) !== false) {
                    $this->addDistrictCandidates($contractCandidates, $districtSet);
                }
            }
            $contractDistrict = $this->resolveUniqueDistrict($contractCandidates);
            if ($contractDistrict !== null) {
                return $contractDistrict;
            }
        }

        // 5) Try exact amount match by date first, then amount only
        $amountCents = (int)round($amount * 100);
        if ($amountCents > 0) {
            if ($docDate !== null) {
                $amountDateKey = $docDate . '|' . $amountCents;
                if (isset($lookup['amount_date'][$amountDateKey])) {
                    $amountDateDistrict = $this->resolveUniqueDistrict($lookup['amount_date'][$amountDateKey]);
                    if ($amountDateDistrict !== null) {
                        return $amountDateDistrict;
                    }
                }
            }

            if (isset($lookup['amount'][$amountCents])) {
                return $this->resolveUniqueDistrict($lookup['amount'][$amountCents]);
            }
        }

        return null;
    }

    private function normalizeDistrictFromTuman(?string $tuman): ?string
    {
        if ($tuman === null || trim($tuman) === '') {
            return null;
        }

        $normalized = $this->normalizeText($tuman);
        foreach ($this->districtPatterns as $pattern => $districtName) {
            if ($normalized !== '' && strpos($normalized, $pattern) !== false) {
                return $districtName;
            }
        }

        return null;
    }

    private function normalizeText(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $lower = mb_strtolower($text, 'UTF-8');
        $clean = preg_replace('/[^\p{L}\p{N}]+/u', '', $lower);

        return $clean ?? $lower;
    }

    private function normalizeIdentifier(?string $value): string
    {
        return $this->normalizeText($value);
    }

    private function addDistrictCandidates(array &$target, array $districtSet): void
    {
        foreach (array_keys($districtSet) as $district) {
            $target[$district] = true;
        }
    }

    private function resolveUniqueDistrict(array $districtSet): ?string
    {
        if (count($districtSet) !== 1) {
            return null;
        }

        return array_key_first($districtSet);
    }
}
