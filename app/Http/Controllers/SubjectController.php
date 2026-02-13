<?php

namespace App\Http\Controllers;

use App\Models\GroupSubject;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index()
    {
        $user_guard = Auth::guard('student')->check() ? 'student' : 'web';
        $user = auth($user_guard)->user();

        $subjects = GroupSubject::where('student_id', auth('student')->id())->get();
        return view('pages.student.subjects.index', compact(['subjects', 'user', 'user_guard']));
    }
}
