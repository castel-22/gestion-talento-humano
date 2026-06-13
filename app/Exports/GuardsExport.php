<?php

namespace App\Exports;

use App\Models\GuardDuty;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;

class GuardsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithDrawings, WithCustomStartCell, WithEvents
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = GuardDuty::with(['employee', 'rotation']);

        if (!empty($this->filters['rotation_id'])) {
            $query->where('guard_rotation_id', $this->filters['rotation_id']);
        }
        if (!empty($this->filters['month']) && !empty($this->filters['year'])) {
            $query->whereYear('date', $this->filters['year'])
                  ->whereMonth('date', $this->filters['month']);
        } elseif (!empty($this->filters['year'])) {
            $query->whereYear('date', $this->filters['year']);
        }

        return $query->orderBy('date')->get();
    }

    public function headings(): array
    {
        return [
            'Rotación',
            'Fecha',
            'Día Semana',
            'Letra Guardia',
            'Cédula',
            'Técnico Asignado',
            'Observaciones',
        ];
    }

    public function map($duty): array
    {
        $date = Carbon::parse($duty->date);
        return [
            $duty->rotation->name ?? 'N/A',
            $date->format('d/m/Y'),
            ucfirst($date->locale('es')->dayName),
            $duty->letter,
            $duty->employee->id_number ?? '—',
            $duty->employee->full_name ?? '— Sin asignar —',
            $duty->notes ?? '',
        ];
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function drawings()
    {
        $drawings = [];

        if (file_exists(public_path('images/logo_pc.png'))) {
            $drawing1 = new Drawing();
            $drawing1->setName('Logo PC');
            $drawing1->setDescription('Logo Protección Civil');
            $drawing1->setPath(public_path('images/logo_pc.png'));
            $drawing1->setHeight(65);
            $drawing1->setOffsetX(10);
            $drawing1->setOffsetY(8);
            $drawing1->setCoordinates('A1');
            $drawings[] = $drawing1;
        }

        if (file_exists(public_path('images/logo_ciudad_bolivar.png'))) {
            $drawing2 = new Drawing();
            $drawing2->setName('Logo Gobernación');
            $drawing2->setDescription('Logo Gobernación');
            $drawing2->setPath(public_path('images/logo_ciudad_bolivar.png'));
            $drawing2->setHeight(65);
            $drawing2->setOffsetX(10);
            $drawing2->setOffsetY(8);
            $drawing2->setCoordinates('G1'); // 7 columns
            $drawings[] = $drawing2;
        }

        return $drawings;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $sheet->mergeCells('B2:F2');
                $sheet->mergeCells('B3:F3');
                $sheet->mergeCells('B4:F4');

                $sheet->setCellValue('B2', 'REPÚBLICA BOLIVARIANA DE VENEZUELA');
                $sheet->setCellValue('B3', 'GOBERNACIÓN DEL ESTADO BOLÍVAR');
                $sheet->setCellValue('B4', 'PROTECCIÓN CIVIL Y ADMINISTRACIÓN DE DESASTRES');
                
                $styleArray = [
                    'font' => [
                        'bold' => true,
                        'name' => 'Arial',
                        'color' => ['rgb' => '0B3B5E']
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ]
                ];

                $sheet->getStyle('B2:F4')->applyFromArray($styleArray);
                $sheet->getStyle('B2')->getFont()->setSize(12);
                $sheet->getStyle('B3')->getFont()->setSize(11);
                $sheet->getStyle('B4')->getFont()->setSize(10);
                
                $sheet->getRowDimension(1)->setRowHeight(10);
                $sheet->getRowDimension(2)->setRowHeight(24);
                $sheet->getRowDimension(3)->setRowHeight(24);
                $sheet->getRowDimension(4)->setRowHeight(24);
                $sheet->getRowDimension(5)->setRowHeight(10);
            },
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(18);
                $sheet->getColumnDimension('G')->setAutoSize(false)->setWidth(18);
            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            6 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0B3B5E'],
                ],
            ],
        ];
    }
}
