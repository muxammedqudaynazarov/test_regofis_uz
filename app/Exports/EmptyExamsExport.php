<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class EmptyExamsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $exams;

    public function __construct($exams)
    {
        $this->exams = $exams;
    }

    public function collection()
    {
        return $this->exams;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fan nomi',
            'Kafedra',
            'Mutaxassislik / Yo‘nalish',
            'O‘quv reja',
            'Semestr',
            'Fan tili',
            'Ariza raqami',
            'Ariza sanasi',
        ];
    }

    public function map($exam): array
    {
        return [
            '#' . $exam->subject_id,
            $exam->failed_subject->subject_name ?? '-',
            $exam->subject->department->name ?? '-',
            ($exam->subject->curriculum->specialty->code ?? '') . ' – ' . ($exam->subject->curriculum->specialty->name ?? ''),
            ($exam->subject->curriculum->name ?? ''),
            $exam->subject->semester->name ?? '-',
            $exam->application->student->language->name ?? '-',
            $exam->application->application_number ?? '-',
            $exam->created_at ? $exam->created_at->format('Y-m-d H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF4F81BD',
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        return [];
    }
}
