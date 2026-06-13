<?php

namespace App\Exports;

use App\Models\Vacation;
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

class VacationsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithDrawings, WithCustomStartCell, WithEvents
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Vacation::with(['employee.department']);

        if (!empty($this->filters['year'])) {
            $query->whereYear('start_date', $this->filters['year']);
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (!empty($this->filters['employee_id'])) {
            $query->where('employee_id', $this->filters['employee_id']);
        }

        return $query->orderBy('start_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Cédula',
            'Empleado',
            'Departamento',
            'Fecha Inicio',
            'Fecha Fin',
            'Días Regulares',
            'Días Vencidos',
            'Total Días',
            'Estado',
        ];
    }

    public function map($v): array
    {
        return [
            $v->employee->id_number ?? 'N/A',
            $v->employee->full_name ?? 'N/A',
            $v->employee->department->name ?? 'N/A',
            $v->start_date ? Carbon::parse($v->start_date)->format('d/m/Y') : 'N/A',
            $v->end_date   ? Carbon::parse($v->end_date)->format('d/m/Y')   : 'N/A',
            $v->regular_days_taken   ?? 0,
            $v->accumulated_days_used ?? 0,
            $v->days_taken ?? 0,
            ucfirst($v->status ?? ''),
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
            $drawing2->setCoordinates('I1'); // 9 columns
            $drawings[] = $drawing2;
        }

        return $drawings;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $sheet->mergeCells('B2:H2');
                $sheet->mergeCells('B3:H3');
                $sheet->mergeCells('B4:H4');

                $sheet->setCellValue('B2', 'REPÚBLICA BOLIVARIANA DE VENEZUELA');
                $sheet->setCellValue('B3', 'GOBERNACIÓN DEL ESTADO BOLÍVAR');
                $sheet->setCellValue('B4', 'PROTECCIÓN CIVIL Y ADMINISTRACIÓN DE DESASTRES');
                
                $styleArray = [
                    'font' => [
                        'bold' => true,
                        'name' => 'Arial',
                        'color' => ['rgb' => 'F97316'] // Vacation Orange Theme for Header Text
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ]
                ];

                $sheet->getStyle('B2:H4')->applyFromArray($styleArray);
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
                $sheet->getColumnDimension('I')->setAutoSize(false)->setWidth(18);
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
                    'startColor' => ['rgb' => 'F97316'],
                ],
            ],
        ];
    }
}
