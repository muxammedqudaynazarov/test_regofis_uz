<?php

namespace App\Exports;

use App\Models\Department;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
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

        // 1. ENG ASOSIY OPTIMIZATSIYA: Barcha savollar sonini BITTA so'rov bilan olamiz.
        // Bu 1500+ ta so'rovni 1 ta tezkor so'rovga qisqartiradi.
        $questionCounts = DB::table('questions')
            ->select('subject_id', 'language_id', DB::raw('count(*) as total'))
            ->groupBy('subject_id', 'language_id')
            ->get()
            ->groupBy('subject_id')
            ->map(function ($items) {
                return $items->keyBy('language_id');
            });

        // 2. Faqat kerakli ustunlarni yuklash xotirani sezilarli darajada tejaydi
        // "faculty:id,name" kabi faqat kerakli ustunlar olingan.
        $departments = Department::where('structure', '12')
            ->with(['faculty:id,name', 'subjects.teachers', 'subjects.subject:id,name'])
            ->cursor();

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

                // 3. Bazaga qayta-qayta murojaat qilmasdan, tayyor massivdan raqamlarni olamiz
                $subjectCounts = $questionCounts->get($subject->id);

                foreach ($activeLanguages as $lang) {
                    $countValue = $subjectCounts ? ($subjectCounts->get($lang->id)->total ?? 0) : 0;
                    $row[$lang->name] = ($countValue > 0) ? $countValue : '-';
                }

                yield $row;
            }
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
