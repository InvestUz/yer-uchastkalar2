<?php

namespace App\Http\Controllers;

use App\Models\YerSotuv;
use App\Models\GrafikTolov;
use App\Models\FaktTolov;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExportController extends Controller
{
    /**
     * Export full data with all columns and monthly grafik payments
     */
    public function exportToExcel(Request $request)
    {
        // Get all yer_sotuv records with their relations
        $yerSotuvlar = YerSotuv::with(['grafikTolovlar', 'faktTolovlar'])
            ->orderBy('id')
            ->get();

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

        // Write headers using fromArray
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
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(40);

        // Prepare data rows
        $dataRows = [];
        foreach ($yerSotuvlar as $index => $lot) {
            // Basic data columns (1-50)
            $rowData = [
                $index + 1, // №
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

            // Add monthly grafik data (96 columns for 2022-2029)
            $monthlyData = $this->getMonthlyGrafikData($lot);
            $rowData = array_merge($rowData, $monthlyData);

            $dataRows[] = $rowData;
        }

        // Write all data at once
        if (!empty($dataRows)) {
            $sheet->fromArray($dataRows, null, 'A2');
        }

        // Set column widths
        for ($i = 1; $i <= count($headers); $i++) {
            $columnLetter = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($columnLetter)->setWidth(15);
        }

        // Set number format for numeric columns
        $lastRow = count($dataRows) + 1;
        $sheet->getStyle('A2:' . $lastColumn . $lastRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // Generate filename
        $filename = 'Yer_Sotuvlar_Export_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Save to temporary file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        // Return download response
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Get monthly grafik data for a lot organized by year and month
     * Returns array with 96 elements (8 years × 12 months)
     */
    private function getMonthlyGrafikData($lot)
    {
        $monthlyData = [];

        // Create a lookup array from grafik_tolovlar
        $grafikLookup = [];
        foreach ($lot->grafikTolovlar as $grafik) {
            $key = $grafik->yil . '-' . $grafik->oy;
            $grafikLookup[$key] = $grafik->grafik_summa;
        }

        // Generate data for 2022-2029
        $years = [2022, 2023, 2024, 2025, 2026, 2027, 2028, 2029];
        foreach ($years as $year) {
            for ($month = 1; $month <= 12; $month++) {
                $key = $year . '-' . $month;
                $monthlyData[] = $grafikLookup[$key] ?? null;
            }
        }

        return $monthlyData;
    }

    /**
     * Export summary with calculated grafik and fakt totals
     * Includes ALL important columns for analysis
     */
    public function exportWithFaktSummary(Request $request)
    {
        $yerSotuvlar = YerSotuv::with(['grafikTolovlar', 'faktTolovlar'])
            ->orderBy('id')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers - ALL important columns including calculated fields
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
            'Жами график (ҳисобланган)',
            'Жами факт (ҳисобланган)',
            'Қарздорлик',
            'Тўлов фоизи',
            'Аукцион санаси',
            'Шартнома санаси'
        ];

        // Write headers using fromArray
        $sheet->fromArray([$headers], null, 'A1');

        // Style headers
        $sheet->getStyle('A1:S1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        // Prepare data
        $dataRows = [];
        foreach ($yerSotuvlar as $index => $lot) {
            // Calculate grafik and fakt sums
            $grafikSum = $lot->grafikTolovlar->sum('grafik_summa');
            $faktSum = $lot->faktTolovlar->sum('tolov_summa');
            $qarzlik = $grafikSum - $faktSum;
            $foiz = $grafikSum > 0 ? round(($faktSum / $grafikSum) * 100, 1) : 0;

            $dataRows[] = [
                $index + 1,
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
                $grafikSum,  // Calculated from grafik_tolovlar
                $faktSum,    // Calculated from fakt_tolovlar
                $qarzlik,
                $foiz,
                $lot->auksion_sana ? $lot->auksion_sana->format('d.m.Y') : null,
                $lot->shartnoma_sana ? $lot->shartnoma_sana->format('d.m.Y') : null,
            ];
        }

        // Write data using fromArray
        if (!empty($dataRows)) {
            $sheet->fromArray($dataRows, null, 'A2');
        }

        // Auto-size columns
        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Number format for numeric columns
        $lastRow = count($dataRows) + 1;
        $sheet->getStyle('I2:Q' . $lastRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // Percentage format for payment percentage column
        $sheet->getStyle('Q2:Q' . $lastRow)
            ->getNumberFormat()
            ->setFormatCode('0.0"%"');

        // Add totals row
        $totalsRow = $lastRow + 1;
        $sheet->setCellValue('A' . $totalsRow, 'ЖАМИ:');
        $sheet->getStyle('A' . $totalsRow . ':S' . $totalsRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF2CC']
            ]
        ]);

        // Add SUM formulas for numeric columns
        $sheet->setCellValue('I' . $totalsRow, '=SUM(I2:I' . $lastRow . ')'); // Maydoni
        $sheet->setCellValue('J' . $totalsRow, '=SUM(J2:J' . $lastRow . ')'); // Sotilgan narx
        $sheet->setCellValue('K' . $totalsRow, '=SUM(K2:K' . $lastRow . ')'); // Golib tolagan
        $sheet->setCellValue('L' . $totalsRow, '=SUM(L2:L' . $lastRow . ')'); // Auksion harajati
        $sheet->setCellValue('M' . $totalsRow, '=SUM(M2:M' . $lastRow . ')'); // Shartnoma summasi
        $sheet->setCellValue('N' . $totalsRow, '=SUM(N2:N' . $lastRow . ')'); // Jami grafik
        $sheet->setCellValue('O' . $totalsRow, '=SUM(O2:O' . $lastRow . ')'); // Jami fakt
        $sheet->setCellValue('P' . $totalsRow, '=SUM(P2:P' . $lastRow . ')'); // Qarzdorlik

        // Generate filename
        $filename = 'Yer_Sotuvlar_Summary_' . date('Y-m-d_H-i-s') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
