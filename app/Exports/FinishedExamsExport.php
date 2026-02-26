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

class FinishedExamsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            '#',
            'Talaba F.I.Sh.',
            'Ariza raqami',
            'Fakultet',
            'Mutxassislik',
            'Fan nomi',
            'To‘plagan bali',
            'Sinxronizatsiya',
            'Holati',
            'Yakunlangan vaqti'
        ];
    }

    public function map($exam): array
    {
        return [
            '#' . $exam->id,
            json_decode($exam->application->student->name)->full_name ?? 'Noma’lum talaba',
            $exam->application->application_number,
            $exam->student->specialty->department->name,
            $exam->student->specialty->name,
            $exam->failed_subject->subject_name ?? 'Noma’lum fan',
            $exam->results->first()->point ?? '-',
            ($exam->result->uploaded == '1') ? 'Ha' : 'Yo‘q',
            'Yakunlangan',
            $exam->updated_at ? $exam->updated_at->format('d.m.Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Sarlavhalar dizayni
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF28A745', // Yashil fon (Yakunlanganligini bildirish uchun)
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Kataklar chegarasi (Borders)
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

        // Matnlarni o'rtaga joylashtirish
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
