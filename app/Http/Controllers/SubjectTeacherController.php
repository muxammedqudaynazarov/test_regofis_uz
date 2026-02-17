<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use App\Models\Subject;
use App\Models\SubjectList;
use App\Models\SubjectTeacher;
use App\Models\User;
use App\Models\Workplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectTeacherController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($request->has('filter_submit')) {
            session(['sub_reg_curriculum_id' => $request->curriculum_id]);
            session(['sub_reg_status' => $request->status]);
        } elseif ($request->has('filter_clear')) {
            session()->forget(['sub_reg_curriculum_id', 'sub_reg_status']);
        }
        $filterCurriculum = session('sub_reg_curriculum_id');
        $filterStatus = session('sub_reg_status');
        $query = SubjectList::with(['subject', 'department', 'curriculum', 'semester', 'teachers']);
        $departmentId = null;
        if ($user->current_role == 'department') {
            $dep = Workplace::where('user_id', $user->id)->where('head_type', 'department')->first();
            if ($dep) {
                $query->where('department_id', $dep->department_id);
                $departmentId = $dep->department_id;
            }
        }
        if ($filterCurriculum) {
            $query->where('curriculum_id', $filterCurriculum);
        }

        if ($filterStatus) {
            if ($filterStatus == 'attached') {
                $query->whereHas('teachers');
            } elseif ($filterStatus == 'detached') {
                $query->whereDoesntHave('teachers');
            }
        }

        // Natijani olish
        $subjects = $query->paginate(20);

        // 4. QO'SHIMCHA MA'LUMOTLARNI FILTRLASH (Selectlar uchun)

        if ($departmentId) {
            // A) O'quv rejalar (Faqat shu kafedraga tegishli)
            $curriculumIds = SubjectList::where('department_id', $departmentId)
                ->distinct()
                ->pluck('curriculum_id');
            $curriculums = Curriculum::whereIn('id', $curriculumIds)->get();

            // B) O'QITUVCHILAR (User) - FAQAT SHU KAFEDRADAGILAR
            // User modelida 'workplaces' relatsiyasi bo'lishi shart!
            $users = User::whereHas('workplaces', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            })->get();

        } else {
            // Admin yoki boshqa rol bo'lsa, hammasi
            $curriculums = Curriculum::all();
            $users = User::all();
        }

        return view('pages.web.subject_register.index', compact(['user', 'subjects', 'users', 'curriculums', 'filterCurriculum', 'filterStatus']));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subject_lists,id',
            'user_ids' => 'nullable|array',
        ]);
        $subject = SubjectList::findOrFail($request->subject_id);
        $subject->teachers()->sync($request->input('user_ids', []));

        return redirect()->back()->with('success', 'Ma’lumotlar yangilandi!');
    }
}
