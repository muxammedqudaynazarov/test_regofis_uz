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
        } elseif (auth()->user()->current_role != 'teacher') {
            if (auth()->user()->can('statistics.view.sv')) {
                $stats = [
                    [
                        'name' => 'Kafedralar resurslari hisoboti (.XLSX)',
                        'route' => 'departments.download',
                        'disabled' => false,
                        'optional' => [
                            'status' => false,
                            'values' => [],
                        ],
                    ],
                    [
                        'name' => 'Bo‘sh (o‘qituvchi biriktirilmagan) fanlar hisoboti (.XLSX)',
                        'route' => 'lessons.empty.download',
                        'disabled' => false,
                        'optional' => [
                            'status' => false,
                            'values' => [],
                        ],
                    ],
                    [
                        'name' => 'O‘qituvchi faolligi (tizimga kirmagan o‘qituvchilar) (.XLSX)',
                        'route' => 'home',
                        'disabled' => true,
                        'optional' => [
                            'status' => false,
                            'values' => [],
                        ],
                    ],
                    [
                        'name' => 'Yakuniy qaydnomalar (.XLSX)',
                        'route' => 'final-results.download',
                        'disabled' => false,
                        'optional' => [
                            'status' => true,
                            'values' => Retrain::select('id', 'name', 'status')->get()->toArray(),
                        ],
                    ],
                    [
                        'name' => 'Talaba arizasi mavjud, lekin bo‘sh holatidagi fanlar (.XLSX)',
                        'route' => 'applications.lessons.empty.download',
                        'disabled' => false,
                        'optional' => [
                            'status' => false,
                            'values' => [],
                        ],
                    ],
                    [
                        'name' => 'Imtihon boshlamagan talabalar (.XLSX)',
                        'route' => 'exam.status.new.download',
                        'disabled' => false,
                        'optional' => [
                            'status' => false,
                            'values' => [],
                        ],
                    ],
                ];
                return view('pages.web.statistics.index', compact(['stats']));
            }
        }
    }

}
