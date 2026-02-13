<?php

namespace Database\Seeders;

use App\Models\EduYear;
use App\Models\Semester;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class DataSeeder extends Seeder
{
    public function run(): void
    {
        $response = Http::withToken(env('API_HEMIS'))
            ->get('https://student.karsu.uz/rest/v1/data/classifier-list', [
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

        $response = Http::withToken(env('API_HEMIS'))
            ->get('https://student.karsu.uz/rest/v1/data/classifier-list', [
                'classifier' => 'h_semester'
            ]);
        $years = $response->json();
        foreach ($years['data']['items'][0]['options'] as $year) {
            Semester::create([
                'id' => $year['code'],
                'name' => $year['name'],
                'status' => '1',
            ]);
        }
    }
}
