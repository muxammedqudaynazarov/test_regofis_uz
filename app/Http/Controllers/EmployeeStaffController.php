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
                $response = Http::withToken(config('services.hemis.token'))
                    ->get(config('services.hemis.student_url') . '/rest/v1/data/curriculum-subject-list', [
                    '_curriculum' => $curriculumId, 'limit' => 200, 'page' => $page, '_semester' => '18'
                ]);

                if ($response->failed()) break;

                $resData = $response->json();
                $items = $resData['data']['items'] ?? [];

                foreach ($items as $curr) {
                    // Subject uchun Upsert
                    Subject::upsert(
                        [
                            [
                                'id' => $curr['subject']['id'],
                                'name' => $curr['subject']['name'],
                                'code' => $curr['subject']['code'] ?? null,
                            ]
                        ],
                        ['id'], // Qaysi ustun bo'yicha izlash kerak (Unique key)
                        ['name', 'code'] // Agar topilsa, qaysi ustunlarni yangilash kerak
                    );

                    // SubjectList uchun Upsert
                    SubjectList::upsert(
                        [
                            [
                                'id' => $curr['id'],
                                'subject_id' => $curr['subject']['id'],
                                'department_id' => $curr['department']['id'] ?? null,
                                'curriculum_id' => $curr['_curriculum'] ?? $curriculumId,
                                'semester_id' => $curr['semester']['code'] ?? null,
                            ]
                        ],
                        ['id'], // Izlanadigan ustun
                        ['subject_id', 'department_id', 'curriculum_id', 'semester_id'] // Yangilanadigan ustunlar
                    );
                }

                $pageCount = $resData['data']['pagination']['pageCount'] ?? 1;
                $page++;
            } while ($page <= $pageCount);
        }
    }
}
