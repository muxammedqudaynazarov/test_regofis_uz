<?php

namespace App\Exports;

use App\Models\SubjectList;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmptyLessonsExport implements FromQuery, WithMapping, WithHeadings, WithStyles, ShouldAutoSize
{
    public function query()
    {
        return SubjectList::doesntHave('teachers')->with(['subject', 'department.faculty']);
    }

    public function map($lesson): array
    {
        return [
            $lesson->id,
            $lesson->subject->name ?? '-',
            $lesson->semester->name ?? '-',
            $lesson->department->faculty->name ?? '-',
            $lesson->department->name ?? '-',
        ];
    }

    public function headings(): array
    {
        return ['#', 'Fan nomi', 'Semestr', 'Fakultet', 'Kafedra'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
