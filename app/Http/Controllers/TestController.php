<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Exam;
use App\Models\Option;
use App\Models\Position;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    // -------------------------------------------------------
    // 1. Savollarni tekshirish (AJAX) — testdan oldin
    // -------------------------------------------------------
    public function check($id)
    {
        $exam    = Exam::findOrFail($id);
        $student = auth('student')->user();

        if ($exam->student_id !== $student->id) {
            return response()->json(['ok' => false, 'reason' => 'Bu imtihon sizga tegishli emas.']);
        }

        if ($exam->finished == '1') {
            return response()->json(['ok' => false, 'reason' => 'Imtihon allaqachon yakunlangan.']);
        }

        if (($student->specialty->department->access ?? '0') != '1') {
            return response()->json(['ok' => false, 'reason' => 'Administrator tomonidan ruxsat berilmagan.']);
        }

        $qCount        = (int)(Option::where('key', 'questions')->value('value') ?? 50);
        $questionCount = Question::where('subject_id', $exam->subject_id)
            ->where('language_id', $student->language_id)
            ->where('status', '1')
            ->count();

        if ($questionCount === 0) {
            $langName = $student->language->name ?? 'tanlangan til';
            return response()->json([
                'ok'     => false,
                'reason' => "Bu fandan «{$langName}» tilida savollar mavjud emas.",
            ]);
        }

        if ($questionCount < $qCount) {
            return response()->json([
                'ok'     => false,
                'reason' => "Savollar yetarli emas: {$questionCount} ta mavjud, {$qCount} ta kerak. O'qituvchi savollarni to'ldirishini kuting.",
            ]);
        }

        return response()->json([
            'ok'     => true,
            'reason' => "Savollar tayyor: {$questionCount} ta mavjud, {$qCount} ta tanlanadi.",
        ]);
    }

    // -------------------------------------------------------
    // 2. Qoidabuzarlikni sessionga yozish (AJAX)
    // -------------------------------------------------------
    public function recordViolation(Request $request)
    {
        $examId  = $request->exam_id;
        $student = auth('student')->user();

        $exam = Exam::where('id', $examId)->where('student_id', $student->id)->first();
        if (!$exam) {
            return response()->json(['error' => 'Exam not found'], 404);
        }

        $maxViolations = 3;
        $key           = "exam_violations_{$examId}_{$student->id}";
        $count         = session()->get($key, 0) + 1;
        session()->put($key, $count);

        return response()->json([
            'count'            => $count,
            'max'              => $maxViolations,
            'should_terminate' => $count >= $maxViolations,
            'remaining'        => max(0, $maxViolations - $count),
        ]);
    }

    // -------------------------------------------------------
    // 3. Natijani ko'rish (tugallangan imtihon)
    // -------------------------------------------------------
    public function edit($id)
    {
        $studentId = auth('student')->id();
        $exam      = Exam::findOrFail($id);

        if ($exam->student_id == $studentId) {
            $lessons = Attempt::where('exam_id', $exam->id)
                ->where('student_id', $studentId)
                ->with(['question', 'positions' => function ($query) {
                    $query->orderBy('pos', 'asc')->with('answer');
                }])
                ->orderBy('pos', 'asc')
                ->get();

            return view('pages.student.test.review', compact('lessons'));
        }

        abort(403, 'Sizda bu natijani ko\'rish huquqi yo\'q.');
    }

    // -------------------------------------------------------
    // 4. Testni boshlash / davom ettirish
    // -------------------------------------------------------
    public function show($id, Request $request)
    {
        $qCount   = (int)(Option::where('key', 'questions')->value('value') ?? 50);
        $duration = (int)(Option::where('key', 'duration')->value('value') ?? 50);
        if ($qCount <= 0) $qCount = 50;
        if ($duration <= 0) $duration = 50;

        $exam      = Exam::findOrFail($id);
        $student   = Auth::guard('student')->user();
        $studentId = $student->id;
        $langId    = $student->language_id;

        // Egalik tekshiruvi
        if ($exam->student_id !== $studentId) {
            abort(403, 'Bu imtihon sizga tegishli emas.');
        }

        // Savollar mavjudligi
        $hasQuestions = Question::where('subject_id', $exam->subject_id)
            ->where('language_id', $langId)
            ->where('status', '1')
            ->exists();

        if (!$hasQuestions) {
            return redirect()->back()->with('error', 'Imtihonni boshlash uchun savollar mavjud emas.');
        }

        // Imtihon tugallangan bo'lsa — 2-urinish logikasi
        if ($exam->finished == '1') {
            $oExam = Exam::where([
                'application_id'   => $exam->application_id,
                'student_id'       => $studentId,
                'subject_id'       => $exam->subject_id,
                'failed_subject_id'=> $exam->failed_subject_id,
                'group_id'         => $exam->group_id,
                'semester_id'      => $exam->semester_id,
                'attempt'          => 1,
            ])->first();

            if ($oExam && $oExam->finished == '1' && $oExam->archived == '1') {
                $latestExam = Exam::where([
                    'application_id' => $exam->application_id,
                    'student_id'     => $studentId,
                    'subject_id'     => $exam->subject_id,
                    'semester_id'    => $exam->semester_id,
                ])->latest('id')->first();

                if ($latestExam->id === $oExam->id) {
                    $newExam             = $oExam->replicate();
                    $newExam->attempt    = $oExam->attempt + 1;
                    $newExam->status     = (string)((int)$oExam->status + 1);
                    $newExam->finished   = '0';
                    $newExam->finished_at= null;
                    $newExam->archived   = '0';
                    $newExam->save();

                    return redirect()->route('tests.show', $newExam->id)
                        ->with('success', 'Ikkinchi urinish uchun imkoniyat berildi.');
                }

                if ($latestExam->id !== $exam->id) {
                    return redirect()->route('tests.show', $latestExam->id);
                }
            } else {
                return redirect()->route('student.home')
                    ->with('error', 'Siz ushbu imtihonni yakunlagansiz.');
            }
        }

        // Taymerni ishga tushirish
        if (empty($exam->finished_at)) {
            $exam->finished_at = now()->addMinutes($duration);
            $exam->status      = (string)((int)$exam->status + 1);
            $exam->save();
        }

        // Savollarni generatsiya qilish (bir marta)
        $exists = Attempt::where('exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->exists();

        if (!$exists) {
            $questions  = Question::where('subject_id', $exam->subject_id)
                ->where('language_id', $langId)
                ->where('status', '1')
                ->with('answers')
                ->get();

            $actualQCount = min($qCount, $questions->count());

            DB::transaction(function () use ($questions, $actualQCount, $exam, $studentId) {
                $selected = $questions->shuffle()->take($actualQCount);
                $qPos     = 1;

                foreach ($selected as $question) {
                    $attempt = Attempt::create([
                        'exam_id'    => $exam->id,
                        'student_id' => $studentId,
                        'question_id'=> $question->id,
                        'pos'        => $qPos++,
                    ]);

                    $aPos = 1;
                    foreach ($question->answers->shuffle() as $answer) {
                        Position::create([
                            'attempt_id' => $attempt->id,
                            'answer_id'  => $answer->id,
                            'pos'        => $aPos++,
                        ]);
                    }
                }
            });
        }

        // Savollarni paginatsiya uchun yig'ish
        $attempts = Attempt::where('exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->with(['question', 'positions' => function ($q) {
                $q->orderBy('pos', 'asc')->with('answer');
            }])
            ->orderBy('pos', 'asc')
            ->get();

        // Sessiondan qoidabuzarliklar sonini olish
        $violationKey   = "exam_violations_{$exam->id}_{$studentId}";
        $violationCount = session()->get($violationKey, 0);

        return view('pages.student.test.show',
            compact('attempts', 'exam', 'violationCount'),
            ['lesson' => $exam]
        );
    }

    // -------------------------------------------------------
    // 5. Javobni AJAX orqali saqlash
    // -------------------------------------------------------
    public function upload_answer(Request $request)
    {
        $student = Auth::guard('student')->user();
        $exam    = Exam::find($request->exam_id);

        $request->validate([
            'attempt_id'  => 'required',
            'question_id' => 'required',
            'answer_id'   => 'required',
        ]);

        if (!$exam || $exam->student_id != $student->id) {
            return response()->json(['status' => 'error', 'message' => 'Test topilmadi'], 404);
        }

        if (Carbon::now()->isAfter(Carbon::parse($exam->finished_at))
            || in_array($exam->status, ['2', '5', '8'])) {
            return response()->json(['status' => 'error', 'message' => 'Test vaqti tugagan'], 403);
        }

        try {
            Attempt::updateOrCreate(
                [
                    'student_id'  => $student->id,
                    'exam_id'     => $request->exam_id,
                    'question_id' => $request->question_id,
                ],
                ['answer_id' => $request->answer_id]
            );

            return response()->json(['status' => 'success', 'message' => 'Javob saqlandi']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Xatolik yuz berdi'], 500);
        }
    }
}
