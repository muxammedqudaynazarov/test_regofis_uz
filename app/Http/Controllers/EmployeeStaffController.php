<?php

namespace App\Http\Controllers;

use App\Models\Result;
use Illuminate\Http\Request;

class EmployeeStaffController extends Controller
{
    public function index()
    {
        $res = Result::whereNull('retrain_id')->get();
        foreach ($res as $value) {
            $all = $value->exam->attempts->count();
            $cur = 0;
            foreach ($value->exam->attempts as $attempt) {
                if ($attempt->answer_id == null) $cur++;
            }
            if ($cur == $all) echo $value->student_id . '<br>';
        }
    }
}
