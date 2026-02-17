<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function index()
    {
        if (auth()->user()->can('system.view')) {
            $options = Option::all();
            return view('pages.web.options.index', compact('options'));
        }
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->can('system.update')) {
            $request->validate([
                'value' => 'required',
            ]);
            $option = Option::findOrFail($id);
            $option->value = $request->value;
            $option->save();
            return redirect()->back()->with('success', 'Sozlama muvaffaqiyatli yangilandi!');
        }
    }
}
