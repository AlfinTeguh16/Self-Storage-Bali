<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportsExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $data;
    protected $month;
    protected $year;

    public function __construct($data, $month, $year)
    {
        $this->data = $data;
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'Deskripsi',
            'Nilai',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 55,
            'B' => 35,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header row styling
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F2937'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Get total rows
        $totalRows = count($this->data) + 1; // +1 for header

        // Style all data cells
        $sheet->getStyle('A2:B' . $totalRows)->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Right align column B (values)
        $sheet->getStyle('B2:B' . $totalRows)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $totalRows = count($this->data) + 1;

                // Style section headers (rows containing ═══)
                for ($row = 2; $row <= $totalRows; $row++) {
                    $cellValue = $sheet->getCell('A' . $row)->getValue();
                    
                    if (str_contains($cellValue ?? '', '═══')) {
                        // Section header styling
                        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'size' => 11,
                                'color' => ['rgb' => 'FFFFFF'],
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $this->getSectionColor($cellValue)],
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_LEFT,
                            ],
                        ]);
                    }
                    
                    // Style total rows
                    if (str_contains($cellValue ?? '', 'TOTAL')) {
                        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                            'font' => [
                                'bold' => true,
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'E5E7EB'],
                            ],
                            'borders' => [
                                'top' => [
                                    'borderStyle' => Border::BORDER_MEDIUM,
                                    'color' => ['rgb' => '9CA3AF'],
                                ],
                            ],
                        ]);
                    }
                    
                    // Style sub-header rows (Rincian, Detail, Booking Terbaru)
                    if (str_contains($cellValue ?? '', 'Rincian') || 
                        str_contains($cellValue ?? '', 'Detail') || 
                        str_contains($cellValue ?? '', 'Booking Terbaru')) {
                        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'italic' => true,
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F3F4F6'],
                            ],
                        ]);
                    }
                }

                // Add borders to all data
                $sheet->getStyle('A1:B' . $totalRows)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E5E7EB'],
                        ],
                    ],
                ]);

                // Set row height for header
                $sheet->getRowDimension(1)->setRowHeight(25);
            },
        ];
    }

    private function getSectionColor($cellValue): string
    {
        if (str_contains($cellValue, 'PENDAPATAN')) {
            return '059669'; // Green
        }
        if (str_contains($cellValue, 'STORAGE')) {
            return '4F46E5'; // Indigo
        }
        if (str_contains($cellValue, 'PEMESANAN')) {
            return '2563EB'; // Blue
        }
        return '1F2937'; // Default dark
    }

    public function title(): string
    {
        return 'Laporan ' . date('F Y', mktime(0, 0, 0, $this->month, 1, $this->year));
    }
}

