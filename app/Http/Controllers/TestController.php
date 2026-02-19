<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Exam;
use App\Models\Option;
use App\Models\Position;
use App\Models\Question;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function update($id, Request $request)
    {
        $qCount = Option::where('key', 'questions')->value('value') ?? 50;
        $lesson = Exam::findOrFail($id);
        $studentId = auth('student')->id();
        $exists = Attempt::where('exam_id', $id)->where('student_id', $studentId)->exists();
        if (!$exists) {
            $questions = Question::where('subject_id', $lesson->subject_id)
                ->where('language_id', auth()->user()->language_id)->with('answers')->get();

            if ($questions->count() < $qCount) {
                return redirect()->back()->with('error', 'Fan resurslar juda kam. Universitet ma’muriyatiga bu haqida ma’lumot bering!');
            }

            $selectedQuestions = $questions->shuffle()->take($qCount);
            $qPos = 1;
            foreach ($selectedQuestions as $question) {
                $attempt = Attempt::create([
                    'exam_id' => $id,
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
        }
        $finalQuestions = Attempt::where('exam_id', $id)->where('student_id', $studentId)
            ->with(['questions'])->orderBy('pos')->get();

        dd($finalQuestions);
    }
}
