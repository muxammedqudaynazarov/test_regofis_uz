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
    public function index()
    {
        $exams = Exam::paginate(20);
        return view('pages.web.results.index', compact(['exams']));
    }
}
