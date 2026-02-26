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
    public function edit($id)
    {
        $studentId = auth('student')->id();
        $exam = Exam::findOrFail($id);
        if ($exam->student_id == $studentId) {
            $lessons = Attempt::where('exam_id', $exam->id)->where('student_id', $studentId)
                ->with(['question', 'positions' => function ($query) {
                    $query->orderBy('pos', 'asc')->with('answer');
                }])
                ->orderBy('pos', 'asc')->get();

            return view('pages.student.test.review', compact('lessons'));
        }
        abort(403, 'Sizda bu natijani ko\'rish huquqi yo\'q.');
    }

    /*public function show($id, Request $request)
    {
        $qCount = (int)(Option::where('key', 'questions')->value('value') ?? 50);
        $duration = (int)(Option::where('key', 'duration')->value('value') ?? 50);
        if ($qCount <= 0) $qCount = 50;
        if ($duration <= 0) $duration = 50;
        $exam = Exam::findOrFail($id);
        $studentId = Auth::guard('student')->id();

        if ($exam->finished == '1') {
            $oExam = Exam::where('application_id', $exam->application_id)
                ->where('student_id', $studentId)
                ->where('subject_id', $exam->subject_id)
                ->where('failed_subject_id', $exam->failed_subject_id)
                ->where('group_id', $exam->group_id)
                ->where('semester_id', $exam->semester_id)
                ->where('attempt', 1)
                ->first();

            // Agar 1-urinish topilsa va u arxivlangan bo'lsa
            if ($oExam && $oExam->finished == '1' && $oExam->archived == '1') {
                $latestExam = Exam::where('application_id', $exam->application_id)
                    ->where('student_id', $studentId)
                    ->where('subject_id', $exam->subject_id)
                    ->where('semester_id', $exam->semester_id)
                    ->latest('id')
                    ->first();

                // Yangi nusxa (2-urinish) yaratish
                if ($latestExam->id === $oExam->id) {
                    $newExam = $oExam->replicate();
                    $newExam->attempt = $oExam->attempt + 1;
                    $newExam->status = (string)((int)$oExam->status + 1);
                    $newExam->finished = '0';
                    $newExam->finished_at = null;
                    $newExam->archived = '0';
                    $newExam->save();

                    return redirect()->route('tests.show', $newExam->id)
                        ->with('success', 'Ikkinchi urinish uchun imkoniyat berildi.');
                }
                elseif ($latestExam->id !== $exam->id) {
                    return redirect()->route('tests.show', $latestExam->id);
                }
            } else {
                return redirect()->route('student.home')->with('error', 'Siz ushbu imtihonni yakunlagansiz. Qayta topshirish uchun ruxsat yo‘q.');
            }
        }

        // 3. MUHIM: is_null o'rniga empty() ishlatamiz.
        // Bo'sh string kelib qolsa ham vaqtni aniq qo'shadi.
        if (empty($exam->finished_at)) {
            $exam->finished_at = now()->addMinutes($duration);
            $nextStatus = (int)$exam->status + 1;
            $exam->status = (string)$nextStatus;
            $exam->save();
        }

        // 4. Savollarni bazadan olish va biriktirish
        $exists = Attempt::where('exam_id', $exam->id)->where('student_id', $studentId)->exists();
        if (!$exists) {
            $questions = Question::where('subject_id', $exam->subject_id)
                ->where('language_id', Auth::guard('student')->user()->language_id)
                ->with('answers')
                ->get();

            if ($questions->isEmpty()) {
                return redirect()->route('student.home')->with('error', 'Bazada ushbu fan uchun savollar mavjud emas.');
            }

            // MUHIM: Savol yetarli bo'lmasa, testni orqaga qaytarib yubormaydi, borini oladi
            $actualQCount = min($qCount, $questions->count());

            DB::transaction(function () use ($questions, $actualQCount, $exam, $studentId) {
                $selectedQuestions = $questions->shuffle()->take($actualQCount);
                $qPos = 1;

                foreach ($selectedQuestions as $question) {
                    $attempt = Attempt::create([
                        'exam_id' => $exam->id,
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

        // 5. Test sahifasini ko'rsatish
        $attempts = Attempt::where('exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->with(['question', 'positions' => function ($query) {
                $query->orderBy('pos', 'asc')->with('answer');
            }])
            ->orderBy('pos', 'asc')
            ->get();

        return view('pages.student.test.show', compact('attempts', 'exam'), ['lesson' => $exam]);
    }*/
    public function show($id, Request $request)
    {
        $qCount = (int)(Option::where('key', 'questions')->value('value') ?? 50);
        $duration = (int)(Option::where('key', 'duration')->value('value') ?? 50);
        if ($qCount <= 0) $qCount = 50;
        if ($duration <= 0) $duration = 50;

        $exam = Exam::findOrFail($id);
        $studentId = Auth::guard('student')->id();

        // 1. Agar imtihon yakunlangan bo'lsa (2-urinish mantiqi)
        if ($exam->finished == '1') {
            $oExam = Exam::where([
                'application_id' => $exam->application_id, 'student_id' => $studentId,
                'subject_id' => $exam->subject_id, 'failed_subject_id' => $exam->failed_subject_id,
                'group_id' => $exam->group_id, 'semester_id' => $exam->semester_id, 'attempt' => 1
            ])->first();

            if ($oExam && $oExam->finished == '1' && $oExam->archived == '1') {
                $latestExam = Exam::where([
                    'application_id' => $exam->application_id, 'student_id' => $studentId,
                    'subject_id' => $exam->subject_id, 'semester_id' => $exam->semester_id
                ])->latest('id')->first();

                if ($latestExam->id === $oExam->id) {
                    $newExam = $oExam->replicate();
                    $newExam->attempt = $oExam->attempt + 1;
                    $newExam->status = (string)((int)$oExam->status + 1);
                    $newExam->finished = '0';
                    $newExam->finished_at = null;
                    $newExam->archived = '0';
                    $newExam->save();

                    return redirect()->route('tests.show', $newExam->id)->with('success', 'Ikkinchi urinish uchun imkoniyat berildi.');
                } elseif ($latestExam->id !== $exam->id) {
                    return redirect()->route('tests.show', $latestExam->id);
                }
            } else {
                return redirect()->route('student.home')->with('error', 'Siz ushbu imtihonni yakunlagansiz. Qayta topshirish uchun ruxsat yo‘q.');
            }
        }

        // 2. Taymerni ishga tushirish
        if (empty($exam->finished_at)) {
            $exam->finished_at = now()->addMinutes($duration);
            $exam->status = (string)((int)$exam->status + 1);
            $exam->save();
        }

        // 3. Savollarni generatsiya qilish (agar hali qilinmagan bo'lsa)
        $exists = Attempt::where('exam_id', $exam->id)->where('student_id', $studentId)->exists();
        if (!$exists) {
            $questions = Question::where('subject_id', $exam->subject_id)
                ->where('language_id', Auth::guard('student')->user()->language_id)
                ->with('answers')->get();

            if ($questions->isEmpty()) {
                return redirect()->route('student.home')->with('error', 'Bazada ushbu fan uchun savollar mavjud emas.');
            }

            $actualQCount = min($qCount, $questions->count());

            DB::transaction(function () use ($questions, $actualQCount, $exam, $studentId) {
                $selectedQuestions = $questions->shuffle()->take($actualQCount);
                $qPos = 1;
                foreach ($selectedQuestions as $question) {
                    $attempt = Attempt::create(['exam_id' => $exam->id, 'student_id' => $studentId, 'question_id' => $question->id, 'pos' => $qPos++]);
                    $aPos = 1;
                    foreach ($question->answers->shuffle() as $answer) {
                        Position::create(['attempt_id' => $attempt->id, 'answer_id' => $answer->id, 'pos' => $aPos++]);
                    }
                }
            });
        }

        // 4. Savollarni view'ga yuborish
        $attempts = Attempt::where('exam_id', $exam->id)->where('student_id', $studentId)
            ->with(['question', 'positions' => function ($query) {
                $query->orderBy('pos', 'asc')->with('answer');
            }])
            ->orderBy('pos', 'asc')->get();

        return view('pages.student.test.show', compact('attempts', 'exam'), ['lesson' => $exam]);
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

        // MUHIM: Carbon orqali tekshirish va barcha 'finished' statuslarni qamrab olish (2=1-urinish tugadi, 5=2-urinish tugadi, 8=3-urinish)
        if (Carbon::now()->isAfter(Carbon::parse($exam->finished_at)) || in_array($exam->status, ['2', '5', '8'])) {
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
