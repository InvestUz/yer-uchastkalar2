<?php

namespace App\Http\Controllers;

use App\Models\YerSotuv;
use App\Models\GrafikTolov;
use App\Models\FaktTolov;
use App\Services\YerSotuvFilterService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExportController extends Controller
{
    protected $filterService;

    public function __construct(YerSotuvFilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    /**
     * Export filtered data based on current query parameters
     * ✅ Exports only the filtered data (same as list page shows)
     */
    public function exportFiltered(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        // Get all filter parameters from request
        $filters = [
            'tuman' => $request->tuman,
            'yil' => $request->yil,
            'tolov_turi' => $request->tolov_turi,
            'holat' => $request->holat,
            'asos' => $request->asos,
            'auksion_sana_from' => $request->auksion_sana_from,
            'auksion_sana_to' => $request->auksion_sana_to,
            'narx_from' => $request->narx_from,
            'narx_to' => $request->narx_to,
            'maydoni_from' => $request->maydoni_from,
            'maydoni_to' => $request->maydoni_to,
            'search' => $request->search,
            'include_all' => $request->include_all,
            'include_bekor' => $request->include_bekor,
            'include_auksonda' => $request->include_auksonda,
            'grafik_ortda' => $request->grafik_ortda,
            'toliq_tolangan' => $request->toliq_tolangan,
            'nazoratda' => $request->nazoratda,
            'qoldiq_qarz' => $request->qoldiq_qarz,
            'auksonda_turgan' => $request->auksonda_turgan,
        ];

        // Build query with filters (same as list page)
        $query = YerSotuv::query()->with(['faktTolovlar', 'grafikTolovlar']);
        $this->filterService->applyFilters($query, $filters);

        // Get sorting
        $sortField = $request->get('sort', 'auksion_sana');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Get all filtered data (no pagination)
        $yerlar = $query->get();

        // Create Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Филтрланган маълумотлар');

        // Headers matching the list page columns
        $headers = [
            '№',
            'Лот рақами',
            'Туман',
            'Манзил',
            'Майдон (га)',
            'Бошланғич нарх',
            'Аукцион санаси',
            'Сотилган нарх',
            'Чегирма',
            'Ғолиб аукционга тўлаган сумма',
            'Ғолиб номи',
            'Тушадиган маблағ',
            'Тушган маблағ',
            'Қолдиқ маблағ',
            'Муддати ўтган қарздорлик',
            'Тўлов тури',
            'Ҳолат',
        ];

        $sheet->fromArray([$headers], null, 'A1');

        // Style header row
        $lastColumn = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '374151']
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Write data rows
        $currentRow = 2;
        $index = 0;

        foreach ($yerlar as $yer) {
            $index++;

            // Calculate values (same as blade template)
            $expected = ($yer->golib_tolagan ?? 0) + ($yer->shartnoma_summasi ?? 0) - ($yer->auksion_harajati ?? 0);
            $received = $yer->faktTolovlar->sum('tolov_summa');
            $qoldiq = $expected - $received;

            // Calculate muddati utgan qarzdorlik
            $muddatiUtganQarz = 0;
            if ($yer->tolov_turi === 'муддатли') {
                $cutoffDate = now()->subMonth()->endOfMonth()->format('Y-m-01');
                $grafikTushadigan = $yer->grafikTolovlar
                    ->filter(function($grafik) use ($cutoffDate) {
                        $grafikDate = $grafik->yil . '-' . str_pad($grafik->oy, 2, '0', STR_PAD_LEFT) . '-01';
                        return $grafikDate <= $cutoffDate;
                    })
                    ->sum('grafik_summa');
                $grafikTushgan = $yer->faktTolovlar
                    ->filter(function($fakt) {
                        $tolashNom = $fakt->tolash_nom ?? '';
                        return !str_contains($tolashNom, 'ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH');
                    })
                    ->sum('tolov_summa');
                $muddatiUtganQarz = max(0, $grafikTushadigan - $grafikTushgan);
            } elseif ($yer->tolov_turi === 'муддатли эмас') {
                $muddatiUtganQarz = max(0, $qoldiq);
            }

            $total_tolov = $yer->faktTolovlar->sum('tolov_summa');
            $golib_total = $yer->golib_tolagan + $total_tolov;

            $rowData = [
                $index,
                $yer->lot_raqami,
                $yer->tuman,
                $yer->manzil,
                $yer->maydoni,
                $yer->boshlangich_narx,
                $yer->auksion_sana ? $yer->auksion_sana->format('d.m.Y') : '',
                $yer->sotilgan_narx,
                $yer->chegirma,
                $golib_total,
                $yer->golib_nomi,
                $expected,
                $received,
                $qoldiq,
                $muddatiUtganQarz,
                $yer->tolov_turi,
                $yer->holat,
            ];

            $sheet->fromArray([$rowData], null, 'A' . $currentRow);
            $currentRow++;
        }

        // Add totals row
        $totalsRow = $currentRow;
        $lastDataRow = $currentRow - 1;

        $sheet->setCellValue('A' . $totalsRow, 'ЖАМИ:');
        $sheet->setCellValue('B' . $totalsRow, $yerlar->count() . ' та');

        // Add SUM formulas for numeric columns
        $sheet->setCellValue('E' . $totalsRow, '=SUM(E2:E' . $lastDataRow . ')'); // Maydon
        $sheet->setCellValue('F' . $totalsRow, '=SUM(F2:F' . $lastDataRow . ')'); // Boshlangich narx
        $sheet->setCellValue('H' . $totalsRow, '=SUM(H2:H' . $lastDataRow . ')'); // Sotilgan narx
        $sheet->setCellValue('I' . $totalsRow, '=SUM(I2:I' . $lastDataRow . ')'); // Chegirma
        $sheet->setCellValue('J' . $totalsRow, '=SUM(J2:J' . $lastDataRow . ')'); // Golib tolagan
        $sheet->setCellValue('L' . $totalsRow, '=SUM(L2:L' . $lastDataRow . ')'); // Tushadigan
        $sheet->setCellValue('M' . $totalsRow, '=SUM(M2:M' . $lastDataRow . ')'); // Tushgan
        $sheet->setCellValue('N' . $totalsRow, '=SUM(N2:N' . $lastDataRow . ')'); // Qoldiq
        $sheet->setCellValue('O' . $totalsRow, '=SUM(O2:O' . $lastDataRow . ')'); // Muddati utgan

        // Style totals row
        $sheet->getStyle('A' . $totalsRow . ':' . $lastColumn . $totalsRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FEF3C7']
            ]
        ]);

        // Auto-size columns
        foreach (range('A', $lastColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format number columns
        $sheet->getStyle('E2:E' . $totalsRow)->getNumberFormat()->setFormatCode('#,##0.0000');
        $sheet->getStyle('F2:F' . $totalsRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('H2:O' . $totalsRow)->getNumberFormat()->setFormatCode('#,##0');

        // Generate filename with filter info
        $filterInfo = [];
        if ($request->tolov_turi) $filterInfo[] = $request->tolov_turi;
        if ($request->grafik_ortda === 'true') $filterInfo[] = 'grafik_ortda';
        if ($request->qoldiq_qarz === 'true') $filterInfo[] = 'qoldiq_qarz';
        if ($request->tuman) $filterInfo[] = $request->tuman;

        $filterSuffix = !empty($filterInfo) ? '_' . implode('_', array_slice($filterInfo, 0, 2)) : '';
        $filename = 'Yer_Sotuvlar_Filtered' . $filterSuffix . '_' . date('Y-m-d_H-i') . '.xlsx';
        $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename); // Sanitize filename

        // Save and download
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Export full data with all columns and monthly grafik payments
     * Optimized with chunking to prevent memory exhaustion
     */
    public function exportToExcel(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers (matching your import structure exactly)
        $headers = [
            '№', 'Лотрақами', 'Туман', 'Ер манзили', 'Уникал рақами', 'Зона',
            'Бош режа бўйича жойлашув зонаси', 'Янги Ўзбекистон', 'Ер майдони', 'Локация',
            'Қурилишга рухсат берилган объект тури', 'Қурилишга рухсат берилган объект тури',
            'Қурилиш умумий майдони (кв,м)', 'Киритиладиган инвестиция (АҚШ долл)',
            'Бошланғич нархи', 'Аукцион санаси', 'Сотилган нархи', 'Аукцион ғолиби',
            'subyekt turi', 'Ғолиб номи', 'Телефон рақами', 'Тўлов тури', 'Асос',
            'Аукцион ўтказиш тури', 'Лот ҳолати', 'шартнома тузганлиги', 'сана', 'рақам',
            'Ғолиб аукционга тўлаган сумма', 'Буюртмачига ўтказилган сумма', 'Чегирма',
            'Аукцион ҳаражати 1 фоиз', 'Тушадиган маблағ', 'Давактив жамғармасига тушган маблағ',
            'шартнома бўйича тушган маблағ', 'Давактивда турган маблағ',
            'Ерни аукционга чиқариш ва аукцион харажатлари',
            'Махаллий бюджетга тушадиган', 'жамғармага тушадиган',
            'Янги Ўзбекистон дирекциясига тушадиган', 'Шайхонтаҳур ҳокимиятига тушадиган',
            'Махаллий бюджет тақсимланган', 'жамғармага тақсимланган',
            'Янги Ўзбекистон дирекцияси тақсимланган', 'Шайхонтаҳур ҳокимияти тақсимланган',
            'қолдиқ Маҳаллий бюджет', 'қолдиқ жамғарма',
            'қолдиқ Янги Ўзбекистон дирекцияси', 'қолдиқ Шайхонтаҳур ҳокимияти',
            'фарқи', 'Шартнома бўйича тушадиган'
        ];

        // Add monthly payment headers (2022-2029)
        $years = [2022, 2023, 2024, 2025, 2026, 2027, 2028, 2029];
        $months = ['янв', 'фев', 'март', 'апр', 'май', 'июнь', 'июль', 'авг', 'сент', 'окт', 'ноя', 'дек'];

        foreach ($years as $year) {
            foreach ($months as $month) {
                $headers[] = "$year $month";
            }
        }

        // Write headers
        $sheet->fromArray([$headers], null, 'A1');

        // Style header row
        $lastColumn = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(40);

        // Process data in chunks to save memory
        $currentRow = 2;
        $index = 0;

        YerSotuv::with(['grafikTolovlar', 'faktTolovlar'])
            ->orderBy('id')
            ->chunk(50, function ($lots) use ($sheet, &$currentRow, &$index) {
                foreach ($lots as $lot) {
                    $index++;

                    // Build row data
                    $rowData = $this->buildRowData($lot, $index);

                    // Write row
                    $sheet->fromArray([$rowData], null, 'A' . $currentRow);

                    $currentRow++;

                    // Clear memory
                    unset($rowData);
                }

                // Force garbage collection after each chunk
                gc_collect_cycles();
            });

        // Set column widths (only for visible columns)
        for ($i = 1; $i <= 50; $i++) {
            $columnLetter = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($columnLetter)->setWidth(15);
        }

        // Generate filename
        $filename = 'Yer_Sotuvlar_Export_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Save to temporary file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        // Clear memory
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        // Return download response
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Build row data for a single lot
     */
    private function buildRowData($lot, $index)
    {
        // Basic data columns (1-50)
        $rowData = [
            $index, // №
            $lot->lot_raqami,
            $lot->tuman,
            $lot->mfy,
            $lot->unikal_raqam,
            $lot->zona,
            $lot->bosh_reja_zona,
            $lot->yangi_ozbekiston,
            $lot->maydoni,
            $lot->lokatsiya,
            $lot->qurilish_turi_1,
            $lot->qurilish_turi_2,
            $lot->qurilish_maydoni,
            $lot->investitsiya,
            $lot->boshlangich_narx,
            $lot->auksion_sana ? $lot->auksion_sana->format('m/d/Y') : null,
            $lot->sotilgan_narx,
            $lot->auksion_golibi,
            $lot->golib_turi,
            $lot->golib_nomi,
            $lot->telefon,
            $lot->tolov_turi,
            $lot->asos,
            $lot->auksion_turi,
            $lot->holat,
            $lot->shartnoma_holati,
            $lot->shartnoma_sana ? $lot->shartnoma_sana->format('m/d/Y') : null,
            $lot->shartnoma_raqam,
            $lot->golib_tolagan,
            $lot->buyurtmachiga_otkazilgan,
            $lot->chegirma,
            $lot->auksion_harajati,
            $lot->tushadigan_mablagh,
            $lot->davaktiv_jamgarmasi,
            $lot->shartnoma_tushgan,
            $lot->davaktivda_turgan,
            $lot->yer_auksion_harajat,
            $lot->mahalliy_byudjet_tushadigan,
            $lot->jamgarma_tushadigan,
            $lot->yangi_oz_direksiya_tushadigan,
            $lot->shayxontohur_tushadigan,
            $lot->mahalliy_byudjet_taqsimlangan,
            $lot->jamgarma_taqsimlangan,
            $lot->yangi_oz_direksiya_taqsimlangan,
            $lot->shayxontohur_taqsimlangan,
            $lot->qoldiq_mahalliy_byudjet,
            $lot->qoldiq_jamgarma,
            $lot->qoldiq_yangi_oz_direksiya,
            $lot->qoldiq_shayxontohur,
            $lot->farqi,
            $lot->shartnoma_summasi,
        ];

        // Add monthly grafik data efficiently
        $monthlyData = $this->getMonthlyGrafikDataOptimized($lot);
        $rowData = array_merge($rowData, $monthlyData);

        return $rowData;
    }

    /**
     * Get monthly grafik data optimized for memory
     */
    private function getMonthlyGrafikDataOptimized($lot)
    {
        // Pre-allocate array with nulls
        $monthlyData = array_fill(0, 96, null);

        // Map year-month to array index
        $years = [2022, 2023, 2024, 2025, 2026, 2027, 2028, 2029];

        // Fill in actual values
        foreach ($lot->grafikTolovlar as $grafik) {
            $yearIndex = array_search($grafik->yil, $years);
            if ($yearIndex !== false) {
                $arrayIndex = ($yearIndex * 12) + ($grafik->oy - 1);
                if ($arrayIndex >= 0 && $arrayIndex < 96) {
                    $monthlyData[$arrayIndex] = $grafik->grafik_summa;
                }
            }
        }

        return $monthlyData;
    }

    /**
     * Export summary - optimized version
     */
    public function exportWithFaktSummary(Request $request)
    {
        ini_set('memory_limit', '256M');
        set_time_limit(180);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            '№',
            'Лотрақами',
            'Туман',
            'Ер манзили',
            'Ғолиб номи',
            'Телефон',
            'Тўлов тури',
            'Лот ҳолати',
            'Ер майдони',
            'Сотилган нархи',
            'Ғолиб тўлаган',
            'Аукцион ҳаражати',
            'Шартнома суммаси',
            'Жами график',
            'Жами факт',
            'Қарздорлик',
            'Тўлов фоизи',
            'Аукцион санаси',
            'Шартнома санаси'
        ];

        // Write headers
        $sheet->fromArray([$headers], null, 'A1');

        // Style headers
        $sheet->getStyle('A1:S1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ]
        ]);

        // Process in chunks
        $currentRow = 2;
        $index = 0;

        YerSotuv::chunk(100, function ($lots) use ($sheet, &$currentRow, &$index) {
            foreach ($lots as $lot) {
                $index++;

                // Calculate sums efficiently
                $grafikSum = GrafikTolov::where('lot_raqami', $lot->lot_raqami)->sum('grafik_summa');
                $faktSum = FaktTolov::where('lot_raqami', $lot->lot_raqami)->sum('tolov_summa');
                $qarzlik = $grafikSum - $faktSum;
                $foiz = $grafikSum > 0 ? round(($faktSum / $grafikSum) * 100, 1) : 0;

                $rowData = [
                    $index,
                    $lot->lot_raqami,
                    $lot->tuman,
                    $lot->mfy,
                    $lot->golib_nomi,
                    $lot->telefon,
                    $lot->tolov_turi,
                    $lot->holat,
                    $lot->maydoni,
                    $lot->sotilgan_narx,
                    $lot->golib_tolagan,
                    $lot->auksion_harajati,
                    $lot->shartnoma_summasi,
                    $grafikSum,
                    $faktSum,
                    $qarzlik,
                    $foiz,
                    $lot->auksion_sana ? $lot->auksion_sana->format('d.m.Y') : null,
                    $lot->shartnoma_sana ? $lot->shartnoma_sana->format('d.m.Y') : null,
                ];

                $sheet->fromArray([$rowData], null, 'A' . $currentRow);
                $currentRow++;

                unset($rowData);
            }

            gc_collect_cycles();
        });

        // Auto-size columns
        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add totals row
        $totalsRow = $currentRow;
        $lastDataRow = $currentRow - 1;

        $sheet->setCellValue('A' . $totalsRow, 'ЖАМИ:');
        $sheet->getStyle('A' . $totalsRow . ':S' . $totalsRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF2CC']
            ]
        ]);

        // Add SUM formulas
        $sheet->setCellValue('I' . $totalsRow, '=SUM(I2:I' . $lastDataRow . ')');
        $sheet->setCellValue('J' . $totalsRow, '=SUM(J2:J' . $lastDataRow . ')');
        $sheet->setCellValue('K' . $totalsRow, '=SUM(K2:K' . $lastDataRow . ')');
        $sheet->setCellValue('L' . $totalsRow, '=SUM(L2:L' . $lastDataRow . ')');
        $sheet->setCellValue('M' . $totalsRow, '=SUM(M2:M' . $lastDataRow . ')');
        $sheet->setCellValue('N' . $totalsRow, '=SUM(N2:N' . $lastDataRow . ')');
        $sheet->setCellValue('O' . $totalsRow, '=SUM(O2:O' . $lastDataRow . ')');
        $sheet->setCellValue('P' . $totalsRow, '=SUM(P2:P' . $lastDataRow . ')');

        // Generate filename
        $filename = 'Yer_Sotuvlar_Summary_' . date('Y-m-d_H-i-s') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        // Clear memory
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
