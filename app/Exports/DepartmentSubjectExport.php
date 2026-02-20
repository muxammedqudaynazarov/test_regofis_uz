<?php

namespace App\Exports;

use App\Models\Department;
use App\Models\Language;
use App\Models\Question;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DepartmentSubjectExport implements FromGenerator, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function title(): string
    {
        return 'Resurslar hisoboti';
    }

    public function generator(): \Generator
    {
        set_time_limit(0);
        $activeLanguages = Language::where('status', '1')->get();
        $departments = Department::where('structure', '12')->with(['faculty', 'subjects.teachers', 'subjects.subject'])->cursor();
        foreach ($departments as $dept) {
            foreach ($dept->subjects as $subject) {
                $teachersNames = [];
                if (!empty($subject->teachers) && is_iterable($subject->teachers)) {
                    foreach ($subject->teachers as $teacher) {
                        $nameData = json_decode($teacher->name);
                        if (isset($nameData->short_name)) {
                            $teachersNames[] = $nameData->short_name;
                        }
                    }
                }
                $teachersString = !empty($teachersNames) ? implode(",\n", $teachersNames) : '-';
                $row = [
                    'id' => $subject->id,
                    'subject' => $subject->subject->name ?? '-',
                    'teachers' => $teachersString,
                    'faculty' => $dept->faculty->name ?? '-',
                    'department' => $dept->name,
                ];

                foreach ($activeLanguages as $lang) {
                    $countValue = Question::where('subject_id', $subject->id)->where('language_id', $lang->id)->count();
                    $row[$lang->name] = ($countValue > 0) ? $countValue : '-';
                }
                yield $row;
            }
            gc_collect_cycles();
        }
    }

    public function headings(): array
    {
        $activeLanguages = Language::where('status', '1')->pluck('name')->toArray();
        return array_merge(['#', 'Fan nomi', 'O‘qituvchilar', 'Fakultet', 'Kafedra'], $activeLanguages);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setWrapText(true);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EFEFEF'],
                ],
            ],
        ];
    }
}
