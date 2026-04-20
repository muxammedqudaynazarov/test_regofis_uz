<?php

namespace App\Http\Controllers;

use App\Models\Exam;

class SubjectController extends Controller
{
    public function index()
    {
        $user = auth('student')->user();
        if ($user) {
            $subjects = Exam::where('student_id', auth('student')->id())->where('finished', '0')->paginate(20);
            return view('pages.student.subjects.index', compact(['subjects', 'user']));
        }
        abort(404);
    }
}
