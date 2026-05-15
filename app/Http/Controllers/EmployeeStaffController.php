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
            foreach ($value->exam->attempts as $attempt) {
                dd($attempt);
            }
        }
    }
}
