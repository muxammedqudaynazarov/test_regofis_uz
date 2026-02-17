<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use App\Models\Workplace;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::where('structure', '12')->get()->pluck('id')->toArray();
        foreach ($departments as $department) {
            $page = 1;
            do {
                $response = Http::withToken(env('API_HEMIS'))->get('https://student.karsu.uz/rest/v1/data/employee-list', [
                    'type' => 'all', '_department' => $department, 'limit' => 200, 'page' => $page
                ]);
                if ($response->failed()) break;
                $resData = $response->json();
                $items = $resData['data']['items'] ?? [];
                foreach ($items as $item) {
                    User::firstOrCreate([
                        'id' => $item['id'],
                        'name' => json_encode([
                            'full_name' => $item['full_name'],
                            'second_name' => $item['second_name'],
                            'third_name' => $item['third_name'],
                            'short_name' => $item['short_name'],
                        ]),
                        'hemis_id' => $item['employee_id_number'],
                        'current_role' => 'teacher',
                        'hemis_roles' => json_encode(['teacher']),
                        'picture' => $item['image_full'],
                    ]);
                    Workplace::firstOrCreate([
                        'user_id' => $item['id'],
                        'department_id' => $item['department']['id'],
                        'head_type' => $item['staffPosition']['code'] == '16' ? 'department' : 'user',
                        'is_main' => $item['employmentForm']['code'] == '11' ? '1' : '0',
                    ]);
                }
                $pageCount = $resData['data']['pagination']['pageCount'] ?? 1;
                $page++;
            } while ($page <= $pageCount);
        }
    }
}
