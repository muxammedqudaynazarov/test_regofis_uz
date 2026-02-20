<?php

namespace App\Exports;

use App\Models\Department;
use App\Models\Language;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepartmentSubjectExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        $activeLanguages = Language::where('status', '1')->get();
        $departments = Department::where('structure', '12')->with(['subjects' => function ($query) use ($activeLanguages) {
            foreach ($activeLanguages as $language) {
                $query->withCount(['questions as lang_' . $language->id . '_count' => function ($q) use ($language) {
                    $q->where('language_id', $language->id);
                }]);
            }
        }])->get();
        $data = collect();
        foreach ($departments as $dept) {
            foreach ($dept->subjects as $subject) {
                $row = [
                    'department' => $dept->name,
                    'subject' => $subject->subject->name,
                ];

                foreach ($activeLanguages as $lang) {
                    $countKey = 'lang_' . $lang->id . '_count';
                    $row[$lang->name] = $subject->$countKey ?? '-';
                }
                $data->push($row);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        $activeLanguages = Language::where('status', '1')->pluck('name')->toArray();
        return array_merge(['Kafedra', 'Fan nomi'], $activeLanguages);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }
}
