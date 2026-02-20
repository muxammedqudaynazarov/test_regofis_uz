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
use function PHPUnit\Framework\isNull;

class TestController extends Controller
{
        public function show($id, Request $request)
        {
            // 1. Parametrlarni olish
            $qCount = Option::where('key', 'questions')->value('value') ?? 50;
            $aCount = Option::where('key', 'attempts')->value('value') ?? 1; // Maksimal urinishlar (masalan: 3)
            $duration = Option::where('key', 'duration')->value('value') ?? 50;
            $min_points = Option::where('key', 'min_points')->value('value') ?? 60;

            $lesson = Exam::findOrFail($id);
            $studentId = auth('student')->id();

            // 2. Statusga qarab qayta topshirish mantiqi (Juft statuslar: 2, 5)
            // 8-statusda to'xtaydi, chunki u tizimning yakuniy nuqtasi
            if (in_array($lesson->status, ['2', '5', '8'])) {
                $lastResult = $lesson->results->first();
                $lastPoint = $lastResult ? $lastResult->point : 0;

                // Jami urinishlar sonini hisoblash (application_id bo'yicha)
                $currentAttempts = Exam::where('application_id', $lesson->application_id)
                    ->where('student_id', $studentId)
                    ->count();

                // Agar ball past bo'lsa VA urinishlar soni limitdan oshmagan bo'lsa
                if ($lastPoint < $min_points && $currentAttempts < $aCount) {
                    $newLesson = $lesson->replicate();

                    // Statusni ko'tarish: 2 -> 3 (Ikkinchi imkoniyat) yoki 5 -> 6 (Oxirgi imkoniyat)
                    $newLesson->status = ($lesson->status == '2') ? '3' : '6';

                    $newLesson->finished_at = null;
                    $newLesson->save();

                    return redirect()->route('tests.show', $newLesson->id);
                } else {
                    // Agar limit tugagan bo'lsa, statusni 8 ga o'tkazish tavsiya etiladi
                    return redirect()->back()->with('error', 'Sizda urinishlar qolmagan yoki minimal ball to‘plangan.');
                }
            }

            // 3. Testni boshlash (Vaqtni belgilash va statusni 'yuklangan' holatiga o'tkazish)
            // 0 -> 1 (Dastlabki), 3 -> 4 (Ikkinchi), 6 -> 7 (Oxirgi)
            if (is_null($lesson->finished_at)) {
                $lesson->finished_at = now()->addMinutes($duration);

                // Statusni toq songa (savollar yuklangan holatga) o'tkazamiz
                $nextStatus = (int)$lesson->status + 1;
                $lesson->status = (string)$nextStatus;

                $lesson->save();
            }
            $exists = Attempt::where('exam_id', $lesson->id)->where('student_id', $studentId)->exists();
            if (!$exists) {
                $questions = Question::where('subject_id', $lesson->subject_id)
                    ->where('language_id', auth('student')->user()->language_id)->with('answers')->get();

                if ($questions->count() < $qCount) {
                    return redirect()->back()->with('error', 'Resurslar yetarli emas.');
                }

                DB::transaction(function () use ($questions, $qCount, $lesson, $studentId) {
                    $selectedQuestions = $questions->shuffle()->take($qCount);
                    $qPos = 1;
                    foreach ($selectedQuestions as $question) {
                        $attempt = Attempt::create([
                            'exam_id' => $lesson->id,
                            'student_id' => $studentId,
                            'question_id' => $question->id,
                            'pos' => $qPos++,
                        ]);

                        $shuffledAnswers = $question->answers->shuffle();
                        $aPos = 1;
                        foreach ($shuffledAnswers as $answer) {
                            Position::create([
                                'attempt_id' => $attempt->id,
                                'answer_id' => $answer->id,
                                'pos' => $aPos++,
                            ]);
                        }
                    }
                });
            }

            // 5. Savollarni va tanlangan javoblarni yuklash
            $attempts = Attempt::where('exam_id', $lesson->id)
                ->where('student_id', $studentId)
                ->with(['question', 'positions' => function ($query) {
                    $query->orderBy('pos', 'asc')->with('answer');
                }])
                ->orderBy('pos', 'asc')
                ->get();

            return view('pages.student.test.show', compact(['attempts', 'lesson']));
        }


    public function upload_answer(Request $request)
    {
        $request->validate([
            'attempt_id' => 'required',
            'question_id' => 'required',
            'answer_id' => 'required',
        ]);
        $student = Auth::guard('student')->user();
        $exam = Exam::find($request->exam_id);
        if (!$exam || $exam->student_id != $student->id) {
            return response()->json(['status' => 'error', 'message' => 'Test topilmadi'], 404);
        }
        if (Carbon::now() > $exam->finished_at || $exam->status == '2' || $exam->status == '5' || $exam->status == '8') {
            return response()->json(['status' => 'error', 'message' => 'Test vaqti tugagan'], 403);
        }
        try {
            Attempt::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'exam_id' => $request->exam_id,
                    'question_id' => $request->question_id,
                ],
                [
                    'answer_id' => $request->answer_id,
                ]
            );
            return response()->json(['status' => 'success', 'message' => 'Javob saqlandi']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Xatolik yuz berdi'], 500);
        }
    }
}
