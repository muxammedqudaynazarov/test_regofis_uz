<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {
        if (auth()->user()->current_role == 'department') {
            return redirect(route('statistics.department.resources'));
        } elseif (auth()->user()->current_role != 'teacher') {
            return view('pages.web.statistics.index');
        }
    }
}
