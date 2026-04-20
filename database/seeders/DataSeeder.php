<?php

namespace Database\Seeders;

use App\Models\Curriculum;
use App\Models\Department;
use App\Models\EduYear;
use App\Models\Language;
use App\Models\Option;
use App\Models\Semester;
use App\Models\Specialty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class DataSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 10; $i <= 18; $i++) {
            $page = 1;
            do {
                $response = Http::withToken(env('API_HEMIS'))->get('https://student.karsu.uz/rest/v1/data/department-list', [
                    '_structure_type' => $i, 'limit' => 200, 'page' => $page
                ]);
                if ($response->failed()) break;
                $resData = $response->json();
                $items = $resData['data']['items'] ?? [];

                foreach ($items as $department) {
                    $parentId = $department['parent'] ?? null;
                    Department::updateOrCreate(
                        ['id' => $department['id']],
                        [
                            'name' => $department['name'],
                            'parent_id' => $parentId,
                            'structure' => $i,
                            'status' => '1',
                        ]
                    );
                }

                $pageCount = $resData['data']['pagination']['pageCount'] ?? 1;
                $page++;
            } while ($page <= $pageCount);
        }

        // Oqiw jillari dizimin kiritiw
        $response = Http::withToken(env('API_HEMIS'))->get('https://student.karsu.uz/rest/v1/data/classifier-list', [
            'classifier' => 'h_education_year'
        ]);
        $years = $response->json();
        foreach ($years['data']['items'][0]['options'] as $year) {
            EduYear::create([
                'id' => $year['code'],
                'name' => $year['name'],
                'status' => '1',
            ]);
        }

        // Tiller dizimin kiritiw
        $response = Http::withToken(env('API_HEMIS'))->get('https://student.karsu.uz/rest/v1/data/classifier-list', [
            'classifier' => 'h_language'
        ]);
        $years = $response->json();
        foreach ($years['data']['items'][0]['options'] as $year) {
            Language::create([
                'id' => $year['code'],
                'name' => $year['name'],
                'status' => '1',
            ]);
        }

        // Semestrler dizimin kiritiw
        $response = Http::withToken(env('API_HEMIS'))->get('https://student.karsu.uz/rest/v1/data/classifier-list', [
            'classifier' => 'h_semester'
        ]);
        $semesters = $response->json();
        foreach ($semesters['data']['items'][0]['options'] as $semester) {
            Semester::create([
                'id' => $semester['code'],
                'name' => $semester['name'],
                'status' => '1',
            ]);
        }

        // Fakultetler dizimin aliw
        $faculties = Department::where('structure', '11')->get()->pluck('id')->toArray();

        // Qanigelikler dizimin kiritiw
        foreach ($faculties as $faculty) {
            $response = Http::withToken(env('API_HEMIS'))->get('https://student.karsu.uz/rest/v1/data/specialty-list', [
                '_department' => $faculty, 'limit' => 200
            ]);
            $specs = $response->json();
            foreach ($specs['data']['items'] as $spec) {
                Specialty::updateOrCreate([
                    'id' => $spec['id'],
                    'uuid' => $spec['bachelorSpecialty']['id'] ?? null,
                    'department_id' => $spec['department']['id'],
                ], [
                    'name' => $spec['name'],
                    'code' => $spec['code'],
                ]);
            }
        }

        // Oqiw rejelerdi kiritiw
        foreach ($faculties as $faculty) {
            $page = 1;
            do {
                $response = Http::withToken(env('API_HEMIS'))->get('https://student.karsu.uz/rest/v1/data/curriculum-list', [
                    '_department' => $faculty, 'limit' => 200, 'page' => $page
                ]);
                if ($response->failed()) break;
                $currs = $response->json();
                if (isset($currs['data']['items'])) {
                    foreach ($currs['data']['items'] as $curr) {
                        $curriculumName = mb_strtolower($curr['name']);
                        if (str_contains($curriculumName, 'bitirgan') ||
                            str_contains($curriculumName, 'biritgan') ||
                            str_contains($curriculumName, 'bititrgan') ||
                            str_contains($curriculumName, 'bitrgan')) continue;

                        Curriculum::updateOrCreate(
                            ['id' => $curr['id']],
                            [
                                'department_id' => $curr['department']['id'] ?? null,
                                'specialty_id' => $curr['specialty']['id'] ?? null,
                                'edu_year_id' => $curr['educationYear']['code'] ?? null,
                                'name' => $curr['name'],
                            ]
                        );
                    }
                }

                $pageCount = $currs['data']['pagination']['pageCount'] ?? 1;
                $page++;
            } while ($page <= $pageCount);
        }
        Option::create([
            'name' => 'Test savollari soni',
            'key' => 'questions',
            'value' => '50'
        ]);
        Option::create([
            'name' => 'Test topshirish davomiyligi (daqiqa)',
            'key' => 'durations',
            'value' => '50'
        ]);
        Option::create([
            'name' => 'Minimal o‘tish bali chegarasi',
            'key' => 'min_points',
            'value' => '60'
        ]);
        Option::create([
            'name' => 'Maksimal to‘planishi mumkin bo‘lgan ball',
            'key' => 'max_points',
            'value' => '100'
        ]);
        Option::create([
            'name' => 'Urinishlar soni',
            'key' => 'attempts',
            'value' => '1'
        ]);
        Option::create([
            'name' => 'Agar Testdan o‘ta olmasa qo‘shimcha imkon berish',
            'key' => 'retest',
            'value' => '1'
        ]);
    }
}
