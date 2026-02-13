<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function index()
    {
        $user_guard = Auth::guard('student')->check() ? 'student' : 'web';
        $user = auth($user_guard)->user();
        if ($user_guard == 'student') return view('pages.student.home', compact(['user', 'user_guard']));
        if ($user_guard == 'web') return view('pages.web.home', compact(['user', 'user_guard']));
    }
}
