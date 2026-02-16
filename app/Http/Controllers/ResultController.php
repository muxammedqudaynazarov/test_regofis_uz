<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function store(Request $request)
    {
        $student = Auth::guard('student')->user();
        $exam = Exam::findOrFail($request->exam_id);

        $now = Carbon::now();
        if ($exam->test->status != '1') return redirect()->back()->with('error', 'Test vaqtincha nofaol.');
        if ($now < $exam->test->started_at) return redirect()->back()->with('error', 'Test hali boshlanmagan.');
        if ($now > $exam->test->finished_at) return redirect()->back()->with('error', 'Test vaqti tugagan.');
        $per_q_point = $exam->test->points / $exam->test->questions;
        if ($exam->student_id == $student->id) {
            if ($exam->status == '2') {
                $point = 0;
                foreach ($request->answers as $answer) {
                    $find = Answer::find($answer);
                    if ($find->correct == '1') $point++;
                }
                if (($point * $per_q_point) < $exam->test->prod_point && $exam->test->retest == 'y')
                    $exam->status = '4';
                else $exam->status = '3';
                $exam->save();

                Result::firstOrCreate([
                    'student_id' => $student->id,
                    'exam_id' => $exam->id,
                ], [
                    'point' => ($point * $per_q_point),
                    'status' => '1',
                ]);
            }
        }
        return redirect()->to(route('home'));
    }
}
