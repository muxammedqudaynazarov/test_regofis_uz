<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class LogViewController extends Controller
{
    public function index()
    {
        $logs = Activity::with('causer')->latest()->paginate(50);
        return view('pages.web.logs.index', compact('logs'));
    }
}
