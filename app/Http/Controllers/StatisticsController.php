<?php

namespace App\Http\Controllers;

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
                    ],
                    [
                        'name' => 'Bo‘sh (o‘qituvchi biriktirilmagan) fanlar hisoboti (.XLSX)',
                        'route' => 'lessons.empty.download',
                        'disabled' => false,
                    ],
                    [
                        'name' => 'O‘qituvchi faolligi (tizimga kirmagan o‘qituvchilar) (.XLSX)',
                        'route' => 'home',
                        'disabled' => true,
                    ],
                    [
                        'name' => 'Yakuniy qaydnomalar (.XLSX)',
                        'route' => 'final-results.download',
                        'disabled' => false,
                    ],
                    [
                        'name' => 'Talaba arizasi mavjud, lekin bo‘sh holatidagi fanlar (.XLSX)',
                        'route' => 'applications.lessons.empty.download',
                        'disabled' => false,
                    ],
                ];
                return view('pages.web.statistics.index', compact(['stats']));
            }
        }
    }

}
