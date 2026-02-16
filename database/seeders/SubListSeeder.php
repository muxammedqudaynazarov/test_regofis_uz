<?php

namespace Database\Seeders;

use App\Models\Curriculum;
use App\Models\Department;
use App\Models\Subject;
use App\Models\SubjectList;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class SubListSeeder extends Seeder
{
    public function run(): void
    {
        /*$departments = Department::where('structure', '12')->get()->pluck('id')->toArray();
        foreach ($departments as $faculty) {
            $page = 1;
            do {
                $response = Http::withToken(env('API_HEMIS'))->get('https://student.karsu.uz/rest/v1/data/curriculum-subject-list', [
                    '_department' => $faculty, 'limit' => 200, 'page' => $page
                ]);
                $resData = $response->json();
                if (isset($resData['data']['items'])) {
                    $i = 1;
                    foreach ($resData['data']['items'] as $curr) {
                        Subject::firstOrCreate([
                            'id' => $curr['subject']['id'],
                        ], [
                            'name' => $curr['subject']['name'],
                            'code' => $curr['subject']['code'],
                        ]);
                        SubjectList::firstOrCreate([
                            'id' => $curr['id'],
                        ], [
                            'subject_id' => $curr['subject']['id'],
                            'department_id' => $curr['department']['id'],
                            'curriculum_id' => $curr['_curriculum'],
                            'semester_id' => $curr['semester']['code'],
                        ]);
                    }
                }
                $pageCount = $resData['data']['pagination']['pageCount'] ?? 1;
                $page++;
            } while ($page <= $pageCount);
        }*/

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

                    /*$deptId = $curr['department']['id'] ?? null;
                    $subjectId = $curr['subject']['id'] ?? null;
                    if (is_null($deptId)) {
                        $this->command->error("Xatolik: Curriculum ID {$curriculumId} dagi {$subjectId} fanda department_id NULL keldi. O'tkazib yuborildi.");
                        continue;
                    }*/

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
