<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index()
    {
        $curr = Curriculum::paginate(20);
        return view('pages.web.curriculum.index', compact(['curr']));
    }
}
