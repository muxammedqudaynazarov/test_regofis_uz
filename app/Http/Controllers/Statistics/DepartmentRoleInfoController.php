<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Subject;
use App\Models\SubjectList;
use App\Models\Workplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentRoleInfoController extends Controller
{
    public function role_department()
    {
        $user = Auth::user();
        if ($user->current_role !== 'department') {
            abort(403, 'Sizda ushbu sahifani ko‘rish huquqi yo‘q.');
        }
        $workplace = Workplace::where('user_id', $user->id)->where('head_type', 'department')->first();
        if (!$workplace) {
            return redirect()->back()->with('error', 'Sizga kafedra biriktirilmagan!');
        }
        $departmentId = $workplace->department_id;
        $languages = Language::where('status', '1')->get();

        $subjects = SubjectList::where('department_id', $departmentId)
            ->with(['teachers' => function ($query) use ($departmentId) {
                $query->whereHas('workplaces', function ($subQuery) use ($departmentId) {
                    $subQuery->where('department_id', $departmentId);
                });
            }])->paginate(20);

        return view('pages.web.statistics.department_head.role', compact('subjects', 'languages'));
    }
}
