<?php

namespace App\Http\Controllers;

use App\Exports\DepartmentSubjectExport;
use App\Models\Department;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DepartmentController extends Controller
{
    public function show($type)
    {
        if ($type === 'faculties' || $type === 'show') {
            if ($type === 'faculties') {
                $faculties = Department::where('structure', '11')->with('children')->withCount('students')->paginate(20);
                if (auth()->user()->can('department.faculties.view')) {
                    return view('pages.web.departments.faculties', compact(['faculties']));
                }
            }
            if ($type === 'show') {
                if (auth()->user()->can('department.view')) {
                    $faculties = Department::where('structure', '12')
                        ->withCount(['workplaces as teachers_count' => function ($q) {
                            $q->select(\DB::raw('count(distinct user_id)'));
                        }])->paginate(20);
                    return view('pages.web.departments.show', compact(['faculties']));
                }
            }
        }
        abort(404);
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->can('department.faculties.access')) {
            if ($request->ajax() && $request->has('access')) {
                $department = Department::findOrFail($id);
                $department->access = $request->access;
                $department->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Ruxsat holati o‘zgartirildi!'
                ]);
            }
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $department = Department::findOrFail($id);
            $department->update($request->all());
            return redirect()->back()->with('success', 'Ma’lumotlar yangilandi');
        }
        abort(404);
    }

    public function download()
    {
        if (!auth()->user()->can('statistics.view.sv')) abort(404);
        $fileName = 'Kafedralar_bo_yicha_savollar_hisoboti_' . now()->format('Y-m-d_H-i') . '.xlsx';
        return Excel::download(new DepartmentSubjectExport, $fileName);
    }
}
