<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            $logs = $query->paginate(50)->appends($request->query());
            return view('pages.web.logs.index', compact('logs'));
        }
        abort(404);
    }

    public function destroy(Request $request)
    {
        if (auth()->user()->can('log.clean')) {
            DB::table('activity_log')->truncate();
            return redirect()->route('logs.index')->with('success', 'Tizim jurnali to‘liq tozalandi.');
        }
        abort(404);
    }
}
