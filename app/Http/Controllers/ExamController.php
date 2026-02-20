<?php

namespace App\Http\Controllers;

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

class ExamController extends Controller
{
    public function show($test_id)
    {
        $student = Auth::guard('student')->user();
        $test = Test::with('subject')->findOrFail($test_id);

        // 1. RUXSATNI TEKSHIRISH
        $hasAccess = GroupSubject::where('student_id', $student->id)
            ->where('subject_id', $test->subject_id)
            ->exists();

        if (!$hasAccess) {
            return redirect()->route('student.home')->with('error', 'Sizga ushbu testga kirishga ruxsat yo‘q.');
        }

        // 2. VAQT VA STATUS
        $now = Carbon::now();
        if ($test->status != '1') return redirect()->back()->with('error', 'Test vaqtincha nofaol.');
        if ($now < $test->started_at) return redirect()->back()->with('error', 'Test hali boshlanmagan.');
        if ($now > $test->finished_at) return redirect()->back()->with('error', 'Test vaqti tugagan.');

        // 3. EXAM SESSIYASINI BOSHQARISH
        $activeExam = Exam::where('test_id', $test->id)
            ->where('student_id', $student->id)
            ->where('status', '2')
            ->first();

        if ($activeExam) {
            if ($now > $activeExam->finished_at) {
                $activeExam->update(['status' => '3']); // Vaqt tugadi
                $exam = null;
            } else {
                $exam = $activeExam;
            }
        }

        if (!isset($exam)) {
            $attemptsUsed = Exam::where('test_id', $test->id)->where('student_id', $student->id)->count();
            if ($attemptsUsed >= $test->attempts) {
                return redirect()->back()->with('error', 'Sizda urinishlar qolmadi.');
            }

            try {
                $exam = Exam::create([
                    'student_id' => $student->id,
                    'test_id' => $test->id,
                    'status' => '2',
                    'finished_at' => $now->copy()->addMinutes($test->durations),
                    'last_activity_at' => $now,
                ]);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Tizim xatoligi: ' . $e->getMessage());
            }
        }

        // 4. SAVOLLARNI GENERATSIYA QILISH VA ARALASHTIRISH (SHUFFLE)
        $exists = QuestionPos::where('student_id', $student->id)->where('test_id', $test_id)->exists();

        if (!$exists) {
            $questions = Question::where('test_id', $test->id)
                ->where('status', '1')
                ->inRandomOrder() // Savollarni aralashtiramiz
                ->limit($test->questions)
                ->get();

            if ($questions->isEmpty()) {
                return redirect()->back()->with('error', 'Test savollari topilmadi.');
            }

            DB::beginTransaction(); // Tranzaksiya boshlash (muhim!)
            try {
                $q_pos = 1;
                foreach ($questions as $question) {
                    // A) Savol pozitsiyasini saqlash
                    QuestionPos::create([
                        'test_id' => $test_id,
                        'student_id' => $student->id,
                        'question_id' => $question->id,
                        'pos' => $q_pos++,
                    ]);

                    // B) Javoblarni aralashtirish (Collection shuffle)
                    $shuffledAnswers = $question->answers->shuffle();

                    $a_pos = 1;
                    foreach ($shuffledAnswers as $answer) {
                        // C) Javob pozitsiyasini saqlash
                        // MUHIM: student_id qo'shilishi shart!
                        AnswerPos::create([
                            'student_id' => $student->id, // <--- SHU YERDA XATO BO'LGAN
                            'question_id' => $question->id,
                            'answer_id' => $answer->id,
                            'pos' => $a_pos++,
                        ]);
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Generatsiya xatoligi: ' . $e->getMessage());
            }
        }

        // 5. VIEW UCHUN MA'LUMOTLARNI TAYYORLASH
        // Savollarni POS bo'yicha olamiz
        $questions_pos = QuestionPos::where('student_id', $student->id)
            ->where('test_id', $test_id)
            ->orderBy('pos', 'asc')
            ->with('question')
            ->get();

        // Har bir savol ichiga uning javoblarini POS bo'yicha joylaymiz
        foreach ($questions_pos as $q_pos) {
            $sorted_answers = AnswerPos::where('student_id', $student->id) // <--- SHU YERDA HAM student_id KERAK
            ->where('question_id', $q_pos->question_id)
                ->orderBy('pos', 'asc') // Aralashtirilgan tartibda o'qish
                ->with('answer') // Asl javob matnini yuklash
                ->get();

            // Blade uchun yangi o'zgaruvchiga biriktiramiz
            $q_pos->sorted_answers_list = $sorted_answers;
        }
        $attempts = Attempt::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->pluck('answer_id', 'question_id')
            ->toArray();
        return view('pages.student.test.show', compact(['exam', 'test', 'questions_pos', 'attempts']));
    }
}
