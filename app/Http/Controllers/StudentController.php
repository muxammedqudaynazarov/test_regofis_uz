<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $lessons = Exam::where('student_id', auth()->id())->take(10)->get();
        return view('pages.student.home', compact(['lessons']));
    }
}
