<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SubjectList;
use App\Models\SubjectTeacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectTeacherController extends Controller
{
    public function index()
    {
        $user_guard = Auth::guard('student')->check() ? 'student' : 'web';
        $user = auth($user_guard)->user();
        $subjects = SubjectList::paginate(20);
        $users = User::all();
        return view('pages.web.subject_register.index', compact(['user', 'subjects', 'users']));
    }

    public function store(Request $request)
    {
        dd($request);
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'user_ids' => 'nullable|array',
        ]);
        $subject = SubjectList::findOrFail($request->subject_id);
        $subject->teachers()->sync($request->input('user_ids', []));

        return redirect()->back()->with('success', 'Ma’lumotlar yangilandi!');
    }
}
