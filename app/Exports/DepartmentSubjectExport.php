<?php

namespace App\Exports;

use App\Models\Department;
use App\Models\Language;
use App\Models\Question;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepartmentSubjectExport implements FromGenerator, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * Xotirani tejash uchun Generator'dan foydalanamiz.
     * Bu usul minglab qatorlarni xotira limiti (128MB) ichida qayta ishlay oladi.
     */
    public function generator(): \Generator
    {
        // Xotira limitini vaqtinchalik oshirish (ehtiyot shart)
        ini_set('memory_limit', '512M');

        $activeLanguages = Language::where('status', '1')->get();

        // cursor() metodi ma'lumotlarni bazadan bittalab o'qiydi (xotira tejaydi)
        foreach (Department::where('structure', '12')->cursor() as $dept) {

            // Har bir kafedraga tegishli fanlarni yuklaymiz
            foreach ($dept->subjects as $subject) {

                $row = [
                    'department' => $dept->name,
                    'subject'    => $subject->subject->name ?? '-',
                ];

                foreach ($activeLanguages as $lang) {
                    // count() so'rovi SQL darajasida bajariladi, obyekt yaratilmaydi
                    $countValue = Question::where('subject_id', $subject->id)
                        ->where('language_id', $lang->id)
                        ->count();

                    $row[$lang->name] = ($countValue > 0) ? $countValue : '-';
                }

                // yield ma'lumotni darhol Excel'ga uzatadi va xotirani bo'shatadi
                yield $row;
            }

            // Har bir kafedradan keyin PHP xotira yig'uvchisini tozalash
            gc_collect_cycles();
        }
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
