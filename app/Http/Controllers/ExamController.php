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
use App\Models\Result;
use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class ExamController extends Controller
{
    public function index()
    {
        if (auth()->user()->can('exam.view')) {
            $status = 'all';
            $exams = Exam::orderBy('created_at', 'desc')->where('archived', '0')->paginate(20);
            return view('pages.web.results.index', compact(['exams', 'status']));
        }
        abort(404);
    }

    public function status($status)
    {
        if (auth()->user()->can('exam.view')) {
            if (in_array($status, ['archived', 'uploaded'])) {
                if ($status == 'archived') {
                    $exams = Exam::with('result')->where('finished', '1')->where('archived', '1')
                        ->whereHas('result', function ($query) {
                            $query->where('status', '0')->where('uploaded', '0');
                        })->paginate(20);
                }
                if ($status == 'uploaded') {
                    $exams = Exam::with('result')->where('finished', '1')->where('archived', '1')
                        ->whereHas('result', function ($query) {
                            $query->where('status', '1')->where('uploaded', '1');
                        })->paginate(20);
                }
                return view('pages.web.results.index', compact(['exams', 'status']));
            }
        }
        abort(404);
    }

    public function store(Request $request)
    {
        $exams = Exam::with('result')->where('finished', '1')->where('archived', '0')
            ->whereHas('result', function ($query) {
                $query->where('status', '1')->where('uploaded', '0');
            })->get();

        //https://edu.regofis.uz/api/grade-sheets/create/
        foreach ($exams as $exam) {
            $res = Result::where('exam_id', $exam->id)->first();
            if ($res) {
                $response = Http::withToken(env('REGOFIS_TOKEN'))->post('https://edu.regofis.uz/api/grade-sheets/create/', [
                    'student_group' => $exam->group_id,
                    'failed_subject' => $exam->failed_subject_id,
                    'jn' => 0,
                    'on' => 0,
                    'yn' => $exam->result->point,
                ]);
                $exam->archived = '1';
                $exam->user_id = Auth::id();
                $exam->save();
                $res->uploaded = $response->successful() ? '1' : '0';
                $res->user_id = Auth::id();
                $res->save();
            } else continue;
        }
        return redirect()->route('final-results.index')->with('success', 'Natijalar serverga yuklandi!');
    }

    public function show($id)
    {
        dd($id);
        $exam = Exam::findOrFail($id);
        if ($exam->finished == '1' && $exam->archived == '0') {
            $res = Result::where('exam_id', $exam->id)->firstOrFail();
            if ($res->status == '1' && $res->uploaded == '0') {
                $response = Http::withToken(env('REGOFIS_TOKEN'))->post('https://edu.regofis.uz/api/grade-sheets/create/', [
                    'student_group' => $exam->group_id,
                    'failed_subject' => $exam->failed_subject_id,
                    'jn' => 0,
                    'on' => 0,
                    'yn' => $res->point,
                ]);
                $exam->archived = '1';
                $exam->user_id = Auth::id();
                $exam->save();
                $res->uploaded = $response->successful() ? '1' : '0';
                $res->user_id = Auth::id();
                $res->save();
                return redirect()->route('final-results.index')->with('success', 'Natijalar serverga yuklandi!');
            }
        }
        return redirect()->route('final-results.index')->with('error', 'Server xatoligi. Natijani ko‘chirib bo‘lmadi!');
    }

    public function update(Request $request, $id)
    {
        dd('OK');
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
                $exam->user_id = Auth::id();
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
