<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

// Kenglikni avtomatik sozlash
use Maatwebsite\Excel\Concerns\WithStyles;

// Stil qo'shish interfeysi
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
            'Ariza sanasi'
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

    // Excel stullarini shu yerda sozlaymiz
    public function styles(Worksheet $sheet)
    {
        // 1. Sarlavha (1-qator) uchun maxsus uslub
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // Oq rangli shrift
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF4F81BD', // To'q ko'k rangli fon
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // 2. Barcha ma'lumotlar bor kataklarga chegara (border) chizish
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'], // Qora chegara
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // 3. ID va Sanani o'rtaga (center) to'g'rilash (A va G ustunlari)
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
