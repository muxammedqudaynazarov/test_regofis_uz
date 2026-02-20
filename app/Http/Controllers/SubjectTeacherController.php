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
        if ($user->can('lessons.view')) {
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
            $subjects = $query->paginate(20);
            if ($departmentId) {
                $curriculumIds = SubjectList::where('department_id', $departmentId)->distinct()->pluck('curriculum_id');
                $curriculums = Curriculum::whereIn('id', $curriculumIds)->get();
                $users = User::whereHas('workplaces', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })->get();
            } else {
                $curriculums = Curriculum::all();
                $users = User::all();
            }
            return view('pages.web.subject_register.index', compact(['user', 'subjects', 'users', 'curriculums', 'filterCurriculum', 'filterStatus']));
        }
        abort(404);
    }

    public function edit($id, Request $request)
    {
        if (\auth()->user()->current_role == 'department') {
            $lesson = SubjectList::findOrFail($id);
            if ($lesson) {
                $lesson->request_delete = '1';
                $lesson->save();
                return redirect()->back()->with('success', 'O‘chirish so‘rovi kiritildi.');
            }
        }
        abort(404);
    }

    public function create()
    {
        if (auth()->user()->can('lessons.request.show')) {
            $subjects = SubjectList::where('request_delete', '1')->paginate(20);
            return view('pages.web.subject_register.request', compact(['subjects']));
        }
        abort(404);
    }

    public function store(Request $request)
    {
        if (\auth()->user()->can('lessons.teachers')) {
            $request->validate([
                'subject_id' => 'required|exists:subject_lists,id',
                'user_ids' => 'nullable|array',
            ]);
            $subject = SubjectList::findOrFail($request->subject_id);
            $subject->teachers()->sync($request->input('user_ids', []));
            return redirect()->back()->with('success', 'Ma’lumotlar yangilandi!');
        }
        abort(404);
    }

    public function destroy($id, Request $request)
    {
        if (\auth()->user()->can('lessons.delete')) {
            $request->validate([
                'type' => 'required|in:0,5',
            ]);
            $lesson = SubjectList::findOrFail($id);
            if ($lesson) {
                $lesson->request_delete = $request->type;
                $lesson->save();
                return redirect()->back()->with('success', 'Talabnoma muvaffaqiyatli qanoatlantirildi. Fan o‘chirildi.');
            }
        }
        abort(404);
    }
}
