<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class ManifestExport implements WithEvents, WithTitle
{
    protected array $manifestRows;
    protected string $departureDate;
    protected string $packageName;

    public function __construct(array $manifestRows, string $departureDate, string $packageName = '')
    {
        $this->manifestRows = $manifestRows;
        $this->departureDate = $departureDate;
        $this->packageName = $packageName;
    }

    public function title(): string
    {
        return 'Manifest';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Row 1: Title
                $sheet->setCellValue('A1', 'MANIFEST ' . $this->departureDate);
                $sheet->mergeCells('A1:N1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Row 2: Package name
                $sheet->setCellValue('A2', $this->packageName);
                $sheet->mergeCells('A2:N2');
                $sheet->getStyle('A2')->getFont()->setSize(9)->setItalic(true);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Row 3: Empty spacer
                // Row 4: Header
                $headers = ['NO', 'TITLE', 'FULL NAME', 'GENDER', 'NO PASSPORT', 'ISSUED DATE', 'EXPIRE DATE', 'NAT', 'DATE OF BIRTH', 'OFFICE ISSUED', 'BIRTH CITY', 'RELATION', 'GROUP', 'AGE'];
                $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
                
                foreach ($headers as $i => $h) {
                    $sheet->setCellValue($cols[$i] . '4', $h);
                }

                // Header style
                $sheet->getStyle('A4:N4')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B5E20']],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                ]);

                // Data rows start at row 5
                $rowNum = 5;
                foreach ($this->manifestRows as $idx => $row) {
                    $gender = strtolower($row['gender'] ?? '');
                    $genderDisplay = in_array($gender, ['male', 'l', 'laki-laki']) ? 'L/M' : 'P/F';
                    $relation = ($row['type'] ?? '') === 'main' ? '—' : ($row['relation'] ?? '');

                    $sheet->setCellValue("A{$rowNum}", $idx + 1);
                    $sheet->setCellValue("B{$rowNum}", $row['title'] ?? '');
                    $sheet->setCellValue("C{$rowNum}", strtoupper($row['full_name'] ?? ''));
                    $sheet->setCellValue("D{$rowNum}", $genderDisplay);
                    $sheet->setCellValueExplicit("E{$rowNum}", $row['passport_no'] ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValue("F{$rowNum}", $this->formatDate($row['issued_date'] ?? ''));
                    $sheet->setCellValue("G{$rowNum}", $this->formatDate($row['expire_date'] ?? ''));
                    $sheet->setCellValue("H{$rowNum}", $row['nationality'] ?? 'IDN');
                    $sheet->setCellValue("I{$rowNum}", $this->formatDate($row['date_of_birth'] ?? ''));
                    $sheet->setCellValue("J{$rowNum}", $row['office_issued'] ?? '');
                    $sheet->setCellValue("K{$rowNum}", $row['birth_city'] ?? '');
                    $sheet->setCellValue("L{$rowNum}", $relation);
                    $sheet->setCellValue("M{$rowNum}", $row['group_label'] ?? '');
                    $sheet->setCellValue("N{$rowNum}", $row['age'] ?? '');

                    // Alternating row color
                    if ($idx % 2 === 1) {
                        $sheet->getStyle("A{$rowNum}:N{$rowNum}")->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('E8F5E9');
                    }

                    // BERDEKATAN highlight
                    if (($row['group_label'] ?? '') === 'BERDEKATAN') {
                        $sheet->getStyle("A{$rowNum}:N{$rowNum}")->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('C8E6C9');
                    }

                    $rowNum++;
                }

                $lastRow = $rowNum - 1;

                // Borders on all data (header + rows)
                if ($lastRow >= 4) {
                    $sheet->getStyle("A4:N{$lastRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '333333']],
                        ],
                    ]);
                }

                // Center columns
                if ($lastRow >= 5) {
                    $sheet->getStyle("A5:A{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B5:B{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("D5:D{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E5:E{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("H5:H{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }

                // Column widths
                $widths = ['A' => 5, 'B' => 7, 'C' => 32, 'D' => 9, 'E' => 14, 'F' => 12, 'G' => 12, 'H' => 6, 'I' => 12, 'J' => 18, 'K' => 14, 'L' => 24, 'M' => 14, 'N' => 5];
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }
            },
        ];
    }

    private function formatDate(?string $date): string
    {
        if (empty($date)) return '-';
        try {
            return \Carbon\Carbon::parse($date)->format('d-M-y');
        } catch (\Exception $e) {
            return $date;
        }
    }
}
