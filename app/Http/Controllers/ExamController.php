<?php

namespace App\Http\Controllers;

use App\Exports\FinishedExamsExport;
use App\Models\Answer;
use App\Models\AnswerPos;
use App\Models\Attempt;
use App\Models\Exam;
use App\Models\GroupSubject;
use App\Models\Question;
use App\Models\QuestionPos;
use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExamController extends Controller
{
    public function index()
    {
        if (auth()->user()->can('exam.view')) {
            $exams = Exam::orderBy('created_at', 'desc')->where('archived', '0')->paginate(20);
            return view('pages.web.results.index', compact(['exams']));
        }
        abort(404);
    }

    public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        if ($exam->finished == '1' && $exam->attempt == 1 && $exam->archived == '0') {
            $result = $exam->results->first();

            if ($result && $result->status == '0') {
                $newExam = $exam->replicate();
                $newExam->finished = '0';
                $newExam->archived = '0';
                $newExam->attempt = 2;
                $newExam->status = '0';
                $newExam->finished_at = null;
                $newExam->save();
                $exam->archived = '1';
                $exam->save();
                return redirect(route('final-results.index'))->with('success', 'Talabaning imtihon natijalari arxivga olindi.');
            }
        }
        return redirect()->back()->with('error', 'Talabaning natijalarini arxivlab bo‘lmaydi yoki oldindan arxiv ma’lumotlar mavjud!');
    }

    public function download()
    {
        if (!auth()->user()->can('statistics.view.sv')) abort(404);
        $exams = Exam::with(['application.student', 'subject'])->where('status', '2')->get();
        if ($exams->isEmpty()) return back()->with('error', 'Yuklab olish uchun yakunlangan imtihonlar topilmadi.');
        return Excel::download(new FinishedExamsExport($exams), 'Yakuniy_natijalar_' . date('dmY-His') . '.xlsx');
    }
}
