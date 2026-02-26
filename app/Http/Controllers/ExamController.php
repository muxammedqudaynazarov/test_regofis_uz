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
                        })->orderBy('updated_at')->paginate(20);
                }
                if ($status == 'uploaded') {
                    $exams = Exam::with('result')->where('finished', '1')->where('archived', '1')
                        ->whereHas('result', function ($query) {
                            $query->where('status', '1')->where('uploaded', '1');
                        })->orderBy('updated_at')->paginate(20);
                }
                return view('pages.web.results.index', compact(['exams', 'status']));
            }
        }
        abort(404);
    }

    public function store(Request $request)
    {
        // Eager loading orqali so'rovlar sonini kamaytiramiz
        $exams = Exam::with('result')
            ->where('finished', '1')
            ->where('archived', '0')
            ->whereHas('result', function ($query) {
                $query->where('status', '1')->where('uploaded', '0');
            })->get();

        if ($exams->isEmpty()) {
            return redirect()->route('final-results.index')->with('info', 'Yuklash uchun yangi natijalar topilmadi.');
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($exams as $exam) {
            $res = $exam->result; // with('result') ishlatganimiz uchun bazaga qayta so'rov bermaydi

            try {
                $response = Http::withToken(env('REGOFIS_TOKEN'))
                [cite_start]->timeout(30) // Har bir so'rov uchun 30 soniya limit [cite: 1]
                    ->post('https://edu.regofis.uz/api/grade-sheets/create/', [
                        'student_group' => $exam->group_id,
                        'failed_subject' => $exam->failed_subject_id,
                        'jn' => 0,
                        'on' => 0,
                        'yn' => $res->point,
                    ]);

                if ($response->successful()) {
                    // API muvaffaqiyatli javob bersagina bazani yangilaymiz
                    $exam->update([
                        'archived' => '1',
                        'user_id' => Auth::id()
                    ]);

                    $res->update([
                        'uploaded' => '1',
                        'user_id' => Auth::id()
                    ]);

                    $successCount++;
                } else {
                    $errorCount++;
                }
            } catch (\Exception $e) {
                // Timeout yoki tarmoq xatosi bo'lsa, siklni to'xtatmasdan keyingisiga o'tadi
                $errorCount++;
                continue;
            }
        }

        $msg = "$successCount ta natija yuklandi.";
        if ($errorCount > 0) $msg .= " $errorCount tasida xatolik yuz berdi.";

        return redirect()->route('final-results.index')->with($errorCount > 0 ? 'warning' : 'success', $msg);
    }

    public function show(Request $request, $id)
    {
        $exam = Exam::with('results')->findOrFail($id);
        $res = $exam->results->first();

        // Shartlarni tekshirish
        if ($exam->finished == '1' && $exam->archived == '0' && $res && $res->status == '1' && $res->uploaded == '0') {
            try {
                // API so'rovini yuborish (Timeoutni oshirgan holda)
                $response = Http::withToken(env('REGOFIS_TOKEN'))
                    ->timeout(60) // 60 soniyagacha kutishga ruxsat beramiz
                    ->connectTimeout(15)
                    ->post('https://edu.regofis.uz/api/grade-sheets/create/', [
                        'student_group' => $exam->group_id,
                        'failed_subject' => $exam->failed_subject_id,
                        'jn' => 0,
                        'on' => 0,
                        'yn' => $res->point,
                    ]);

                if ($response->successful()) {
                    // Faqat javob muvaffaqiyatli bo'lsagina arxivlaymiz va belgilaymiz
                    $exam->update([
                        'archived' => '1',
                        'user_id' => Auth::id()
                    ]);

                    $res->update([
                        'uploaded' => '1',
                        'user_id' => Auth::id()
                    ]);

                    return redirect()->route('final-results.index')->with('success', 'Natijalar serverga yuklandi!');
                }

                // API xato qaytarsa
                return redirect()->route('final-results.index')->with('error', 'API xatoligi: ' . $response->status());

            } catch (\Exception $e) {
                // Timeout yoki ulanish xatosi (cURL error 28) yuzaga kelsa
                return redirect()->route('final-results.index')->with('error', 'Tashqi server bilan bog‘lanish vaqti tugadi (Timeout).');
            }
        }

        return redirect()->route('final-results.index')->with('error', 'Ushbu amalni bajarib bo‘lmaydi yoki natija allaqachon yuklangan.');
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
