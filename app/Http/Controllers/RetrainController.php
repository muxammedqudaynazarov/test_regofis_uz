<?php

namespace App\Http\Controllers;

use App\Models\Retrain;
use Illuminate\Http\Request;

class RetrainController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('retrains.view')) return redirect()->route('home')->with('error', 'Ruxsat etilmagan sahifa!');
        $retrains = Retrain::paginate(20);
        return view('pages.web.retrains.index', compact(['retrains']));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('retrains.create')) return redirect()->route('home')->with('error', 'Ruxsat etilmagan sahifa!');
        $request->validate([
            'name' => 'required',
        ]);
        Retrain::create([
            'name' => $request->name,
            'status' => '0',
        ]);
        return redirect()->route('retrains.index')->with('success', 'Qayta o‘qish yaratildi.');
    }

    public function update(Retrain $retrain, Request $request)
    {
        if (!auth()->user()->can('retrains.update')) return redirect()->route('home')->with('error', 'Ruxsat etilmagan sahifa!');
        $retrains = Retrain::all();
        foreach ($retrains as $re) {
            $re->update(['status' => '0']);
        }
        $retrain->update(['status' => '1']);
        return redirect()->route('retrains.index')->with('success', 'Qayta o‘qish tanlandi.');
    }
}
