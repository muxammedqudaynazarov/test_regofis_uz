<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Question;
use App\Models\Subject;
use App\Models\SubjectList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function index()
    {
        if (\auth()->user()->can('subjects.view')) {
            $user = auth('web')->user();

            $subjects = SubjectList::whereHas('teachers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with('teachers')->paginate(20);
            return view('pages.web.lessons.index', compact('subjects'));
        }
        abort(404);
    }

    public function show($id)
    {
        if (\auth()->user()->can('subjects.resource.view')) {
            $subject = SubjectList::with(['teachers'])->findOrFail($id);
            if (!$subject->teachers->contains(Auth::id())) {
                abort(403, 'Bu fanga kirish huquqingiz yo‘q.');
            }
            $languages = Language::where('status', '1')->get();
            $questions = Question::where('subject_id', $id)->paginate(20);
            return view('pages.web.lessons.show', compact(['subject', 'questions', 'languages']));
        }
        abort(404);
    }
}
