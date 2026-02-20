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
            $stats = [
                [
                    'name' => 'Kafedra resurslari hisoboti (.XLSX)',
                    'route' => 'departments.download',
                    'disabled' => false,
                ],
                [
                    'name' => 'Bo‘sh (o‘qituvchi biriktirilmagan) fanlar hisoboti (.XLSX)',
                    'route' => 'lessons.empty.download',
                    'disabled' => false,
                ],
                [
                    'name' => 'O‘qituvchi faolligi (.XLSX)',
                    'route' => 'home',
                    'disabled' => true,
                ],
                [
                    'name' => 'Qaydnomalar (.XLSX)',
                    'route' => 'home',
                    'disabled' => true,
                ],
            ];
            return view('pages.web.statistics.index', compact(['stats']));
        }
    }
}
