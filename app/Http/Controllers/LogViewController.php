<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class LogViewController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->can('log.view')) {
            $query = Activity::with('causer')->latest();
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHasMorph('causer', '*', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%');
                });
            }
            $logs = $query->paginate(50)->appends($request->query());
            return view('pages.web.logs.index', compact('logs'));
        }
        abort(404);
    }
}
