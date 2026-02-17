<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index()
    {
        if (\auth()->user()->can('languages.view')) {
            $languages = Language::all();
            return view('pages.web.languages.index', compact(['languages']));
        }
    }

    public function update(Request $request, $id)
    {
        if (\auth()->user()->can('languages.status')) {
            $language = Language::findOrFail($id);
            $language->status = ($language->status == '1') ? '0' : '1';
            $language->save();
            $text = "&laquo;" . $language->name . "&raquo;";
            if ($language->status == '1') $text .= ' tili cheklovi olindi.';
            else if ($language->status == '0') $text .= ' tili cheklab qo‘yildi.';

            return response()->json(['message' => $text, 'status' => 'success']);
        }
    }
}
