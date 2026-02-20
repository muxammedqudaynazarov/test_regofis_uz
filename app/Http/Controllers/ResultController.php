<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Attempt;
use App\Models\Exam;
use App\Models\Option;
use App\Models\Result;
use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    public function index()
    {
        $user = auth('student')->user();
        if ($user) {
            $subjects = Exam::where('student_id', auth('student')->id())->where('finished', '1')->paginate(20);
            return view('pages.student.subjects.index', compact(['subjects', 'user']));
        }
        abort(404);
    }

    public function update($id, Request $request)
    {
        $qCount = Option::where('key', 'questions')->value('value');
        $max_points = Option::where('key', 'max_points')->value('value');
        $min_points = Option::where('key', 'min_points')->value('value');
        $per_point = $max_points / $qCount;
        $examId = $request->exam_id;
        $attemptsData = $request->input('attempt', []); // attempt massivini olamiz
        $correctCount = 0;
        DB::transaction(function () use ($min_points, $per_point, $attemptsData, $examId, &$correctCount) {
            $exam = Exam::findOrFail($examId);
            if ($exam->status == '1' || $exam->status == '4' || $exam->status == '7') {
                foreach ($attemptsData as $attemptId => $answerId) {
                    $attempt = Attempt::where('question_id', $attemptId)->where('exam_id', $exam->id)->where('student_id', auth('student')->id())->first();
                    if ($attempt) {
                        $isCorrect = Answer::where('id', $attempt->answer_id)->where('correct', '1')->exists();
                        if ($isCorrect) $correctCount++;
                    }
                }
                $point = $correctCount * $per_point;
                $examStatus = '2';
                if ($exam->status == '4') $examStatus = '5';
                if ($exam->status == '7') $examStatus = '8';
                $exam->status = $examStatus;
                $exam->finished = '1';
                Result::firstOrCreate([
                    'student_id' => auth('student')->id(),
                    'exam_id' => $exam->id,
                    'point' => $point,
                    'status' => ($point < $min_points) ? '0' : '1',
                ]);
                $exam->save();
            } else return redirect()->back()->with('error', 'Imtihon yakunlangan, uni yana yuborib bo‘lmaydi');
        });
        return redirect(route('results.index'))->with('success', 'Imtihon yakunlandi. Natijalar serverga qayta ishlash uchun yuborildi.');
    }
}
