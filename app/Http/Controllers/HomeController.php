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

    public function switch_role($role)
    {
        $user = Auth::user();
        $rols = $user->hemis_roles;
        if (is_string($rols)) {
            $rols = json_decode($rols, true);
        }
        if (!is_array($rols)) {
            $rols = [];
        }
        if (in_array($role, $rols)) {
            $user->current_role = $role;
            $user->save();
            return redirect()->back()->with('success', 'Rol o‘zgartirildi');
        }

        return redirect()->back()->with('error', 'Sizda bu rol mavjud emas!');
    }
}
