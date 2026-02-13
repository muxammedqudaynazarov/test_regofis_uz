<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\User;

class CourseController extends Controller
{
    public function index()
    {
        $lessons = Subject::paginate(20);
        $users = User::all();
        return view('pages.web.subject_register.index', compact(['lessons']));
    }
}
