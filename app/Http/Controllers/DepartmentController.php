<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function show($type)
    {
        if ($type === 'faculties' || $type === 'show') {
            $faculties = Department::where('structure', '11')->with('children')->paginate(20);
            if ($type === 'faculties') {
                if (auth()->user()->can('department.faculties.view')) {
                    return view('pages.web.departments.faculties', compact(['faculties']));
                }
            }
            if ($type === 'show') {
                if (auth()->user()->can('department.faculties.view')) {
                    $faculties = Department::where('structure', '11')
                        ->with(['children' => function ($query) {
                            $query->withCount(['workplaces as teachers_count' => function ($q) {
                                $q->select(\DB::raw('count(distinct user_id)'));
                            }]);
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
}
