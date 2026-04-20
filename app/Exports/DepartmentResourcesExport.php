<?php

namespace App\Exports;

use App\Models\SubjectList;
use App\Models\Language;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepartmentResourcesExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $departmentId;

    public function __construct($departmentId)
    {
        $this->departmentId = $departmentId;
    }

    public function view(): View
    {
        $languages = Language::where('status', '1')->get();
        $subjects = SubjectList::where('department_id', $this->departmentId)
            ->with(['teachers' => function ($query) {
                $query->whereHas('workplaces', function ($subQuery) {
                    $subQuery->where('department_id', $this->departmentId);
                });
            }])->get();

        return view('pages.exports.department_role_stat', compact(['subjects', 'languages']));
    }

    // Exceldagi stillar (Header qalin bo'lishi uchun)
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            2 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
