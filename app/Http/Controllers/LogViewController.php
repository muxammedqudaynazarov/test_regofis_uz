<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class LogViewController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer')->latest();

        // Qidiruv mantiqi
        if ($request->filled('search')) {
            $search = $request->search;
            // Polymorphic munosabat ichidan qidirish
            $query->whereHasMorph('causer', '*', function ($q) use ($search) {
                // Sizning bazangizda name JSON bo'lgani uchun LIKE bilan qidiramiz
                $q->where('name', 'LIKE', '%' . $search . '%');
            });
        }

        $logs = $query->paginate(50)->appends($request->query());

        return view('pages.web.logs.index', compact('logs'));
    }
}
