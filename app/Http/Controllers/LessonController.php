<?php

namespace App\Http\Controllers;

use App\Exports\EmptyLessonsExport;
use App\Models\Language;
use App\Models\Question;
use App\Models\Subject;
use App\Models\SubjectList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class LessonController extends Controller
{
    public function index()
    {
        if (\auth()->user()->can('subjects.view')) {
            $user = auth('web')->user();

            $subjects = SubjectList::whereHas('teachers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with('teachers')->paginate(20);
            return view('pages.web.lessons.index', compact(['subjects']));
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
            $questions = Question::where('subject_id', $id)->where('user_id', \auth()->id())->paginate(20);
            return view('pages.web.lessons.show', compact(['subject', 'questions', 'languages']));
        }
        abort(404);
    }

    public function update(Request $request, $id)
    {
        if (!\auth()->user()->can('subjects.resource.view')) {
            return redirect()->back()->with('error', 'Ruxsat yo‘q!');
        }
        $subjectId = $id;
        $languageId = $request->input('language_load');
        $questions = Question::where('subject_id', $subjectId)->where('language_id', $languageId)->with('answers')->get();

        if ($questions->isEmpty()) return redirect()->back()->with('error', 'Tanlangan tilda savollar topilmadi.');
        $fileName = "questions_subject_" . $subjectId . "_lang_" . $languageId . ".txt";

        return response()->streamDownload(function () use ($questions) {
            $output = "";
            foreach ($questions as $question) {
                $output .= $question->question_text . "\n";
                $correctAnswer = "";
                $i = 0;
                foreach ($question->answers as $index => $answer) {
                    $output .= chr(65 + $i) . '. ' . $answer->answer . "\n";
                    if ($answer->correct == '1') $correctAnswer = chr(65 + $i);
                    $i++;
                }
                $output .= "ANSWER: " . $correctAnswer . "\n\n";
            }
            echo $output;
        }, $fileName, [
            'Content-Type' => 'text/plain',
        ]);
    }

    public function empty_lessons_download()
    {
        return Excel::download(new EmptyLessonsExport, 'Bosh_fanlar_' . date('dmY_Hi') . '.xlsx');
    }
}
