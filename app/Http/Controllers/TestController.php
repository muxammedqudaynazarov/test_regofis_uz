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
        $qCount = Option::where('key', 'questions')->value('value') ?? 50;
        $aCount = Option::where('key', 'attempts')->value('value') ?? 1;
        $duration = Option::where('key', 'duration')->value('value') ?? 50;
        $min_points = Option::where('key', 'min_points')->value('value') ?? 60;

        $lesson = Exam::findOrFail($id);
        $studentId = auth('student')->id();
        if (in_array($lesson->status, ['1', '3'])) {
            $lastPoint = $lesson->results->first()->point ?? 0;
            $totalAttempts = Exam::where('application_id', $lesson->application_id)->where('student_id', $studentId)->count();
            if ($lastPoint < $min_points && $totalAttempts <= $aCount) {
                $newLesson = $lesson->replicate();
                $newLesson->status = '2';
                $newLesson->finished_at = null;
                $newLesson->save();
                return redirect()->route('tests.show', $newLesson->id);
            } else {
                return redirect()->back()->with('error', 'Sizda urinishlar qolmagan yoki minimal ball to‘plangan.');
            }
        }
        if (is_null($lesson->finished_at)) {
            $lesson->finished_at = now()->addMinutes($duration);
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

        $attempts = Attempt::where('exam_id', $lesson->id)->where('student_id', $studentId)->with(['questions', 'positions.answer'])->orderBy('pos', 'asc')->get();

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
        if (Carbon::now() > $exam->finished_at || $exam->status == '3' || $exam->status == '1') {
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
