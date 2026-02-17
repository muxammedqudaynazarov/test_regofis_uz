<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use App\Models\Department;
use App\Models\EduYear;
use App\Models\Specialty;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->can('curriculum.view')) {
            $query = Curriculum::with(['department', 'specialty', 'edu_year']);
            if ($request->filled('department_id')) {
                $query->where('department_id', $request->department_id);
            }
            if ($request->filled('specialty_id')) {
                $query->where('specialty_id', $request->specialty_id);
            }
            if ($request->filled('edu_year_id')) {
                $query->where('edu_year_id', $request->edu_year_id);
            }
            $curr = $query->orderBy('id', 'desc')->paginate(20);
            $departments = Department::where('structure', '11')->orderBy('name')->get();
            if ($request->filled('department_id')) {
                $specialties = Specialty::where('department_id', $request->department_id)->get();
            } else {
                $specialties = Specialty::all();
            }
            $eduYears = EduYear::all();
            return view('pages.web.curriculum.index', compact('curr', 'departments', 'specialties', 'eduYears'));
        }
        abort(404);
    }

    public function destroy($id)
    {
        if (auth()->user()->can('curriculum.delete')) {
            $curriculum = Curriculum::findOrFail($id);
            if ($curriculum->subjects()->exists()) {
                return redirect()->back()->with('error', 'Bu o‘quv rejada fanlar mavjud! Oldin fanlarni o‘chiring yoki boshqa rejaga o‘tkazing.');
            }
            $curriculum->delete();
            return redirect()->back()->with('success', 'O‘quv reja muvaffaqiyatli o‘chirildi!');
        }
        abort(404);
    }

    public function getSpecialties(Request $request)
    {
        if (auth()->user()->can('curriculum.view')) {
            if ($request->has('department_id') && $request->department_id != null) {
                $specialties = Specialty::where('department_id', $request->department_id)->get();
            } else {
                $specialties = Specialty::all();
            }
            return response()->json($specialties);
        }
        abort(404);
    }
}
