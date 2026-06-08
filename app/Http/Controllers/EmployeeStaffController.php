<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use App\Models\Result;
use App\Models\Subject;
use App\Models\SubjectList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

    public function subjects()
    {
        $curriculums = Curriculum::pluck('id')->toArray();
        foreach ($curriculums as $curriculumId) {
            $page = 1;
            do {
                $response = Http::withToken(env('API_HEMIS'))->get('https://student.karsu.uz/rest/v1/data/curriculum-subject-list', [
                    '_curriculum' => $curriculumId, 'limit' => 200, 'page' => $page
                ]);
                if ($response->failed()) break;
                $resData = $response->json();
                $items = $resData['data']['items'] ?? [];
                foreach ($items as $curr) {
                    Subject::updateOrCreate(
                        ['id' => $curr['subject']['id']],
                        [
                            'name' => $curr['subject']['name'],
                            'code' => $curr['subject']['code'] ?? null,
                        ]
                    );

                    SubjectList::updateOrCreate(
                        ['id' => $curr['id']],
                        [
                            'subject_id' => $curr['subject']['id'],
                            'department_id' => $curr['department']['id'] ?? null,
                            'curriculum_id' => $curr['_curriculum'] ?? $curriculumId,
                            'semester_id' => $curr['semester']['code'] ?? null,
                        ]
                    );
                }
                $pageCount = $resData['data']['pagination']['pageCount'] ?? 1;
                $page++;
            } while ($page <= $pageCount);
        }
    }
}
