<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return view('pages.student.home');
        $students = Student::all();
        dd($students);
    }
}
