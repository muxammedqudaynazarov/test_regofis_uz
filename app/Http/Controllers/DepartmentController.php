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
                return view('pages.web.departments.faculties', compact(['faculties']));
            }
            if ($type === 'show') {
                return view('pages.web.departments.show', compact(['faculties']));
            } else abort(404);
        }
    }
}
