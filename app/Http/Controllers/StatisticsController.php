<?php

namespace App\Http\Controllers;

use App\Models\Retrain;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {
        if (auth()->user()->current_role == 'department') {
            return redirect(route('statistics.department.resources'));
        }

        if (auth()->user()->current_role == 'teacher') {
            return redirect(route('home'));
        }

        if (!auth()->user()->can('statistics.view.sv')) {
            return redirect(route('home'));
        }

        $stats = [
            [
                'type'     => 'department_resources',
                'name'     => 'Kafedralar resurslari hisoboti',
                'disabled' => false,
                'optional' => ['status' => false, 'values' => []],
            ],
            [
                'type'     => 'empty_lessons',
                'name'     => 'Bo\'sh (o\'qituvchi biriktirilmagan) fanlar hisoboti',
                'disabled' => false,
                'optional' => ['status' => false, 'values' => []],
            ],
            [
                'type'     => 'teacher_activity',
                'name'     => 'O\'qituvchi faolligi (tizimga kirmagan o\'qituvchilar)',
                'disabled' => true,
                'optional' => ['status' => false, 'values' => []],
            ],
            [
                'type'     => 'finished_exams',
                'name'     => 'Yakuniy qaydnomalar',
                'disabled' => false,
                'optional' => [
                    'status' => true,
                    'values' => Retrain::select('id', 'name', 'status')->get()->toArray(),
                ],
            ],
            [
                'type'     => 'empty_exams',
                'name'     => 'Talaba arizasi mavjud, lekin bo\'sh holatidagi fanlar',
                'disabled' => false,
                'optional' => ['status' => false, 'values' => []],
            ],
            [
                'type'     => 'untaken_exams',
                'name'     => 'Imtihon boshlamagan talabalar',
                'disabled' => false,
                'optional' => ['status' => false, 'values' => []],
            ],
        ];

        return view('pages.web.statistics.index', compact('stats'));
    }
}
