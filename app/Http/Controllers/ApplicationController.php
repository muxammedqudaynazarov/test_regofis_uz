<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\EduYear;
use App\Models\Group;
use App\Models\GroupSubject;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        $response = Http::withToken(env('REGOFIS_TOKEN'))
            ->get('https://edu.regofis.uz/api/applications/', [
                'student_id' => auth('student')->id(),
                'pageSize' => 100,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $apps = $data['data'][0]['items'];
            foreach ($apps as $app) {
                if ($app['status'] == 'approved') {
                    $application = Application::firstOrCreate([
                        'uuid' => $app['application_number'],
                        'o_app_id' => $app['id'],
                    ], [
                        'student_id' => $app['student_id'],
                        'year_id' => $app['education_year'],
                        'status' => $app['status'],
                        'created_at' => $app['created_at'],
                    ]);
                    foreach ($app['details'] as $detail) {
                        if ($detail['student_group']) {
                            $group = Group::firstOrCreate([
                                'id' => $detail['student_group']['id'],
                            ], [
                                'name' => $detail['student_group']['name'],
                            ]);

                            $subject = Subject::firstOrCreate([
                                'failed_subject_id' => $detail['failed_subject_id'],
                                'subject_id' => $detail['subject_id'],
                            ], [
                                'name' => $detail['subject_name'],
                            ]);

                            GroupSubject::firstOrCreate([
                                'student_id' => $app['student_id'],
                                'group_id' => $group->id,
                                'subject_id' => $subject->id,
                                'semester_id' => $detail['semester_code'],
                                'credit' => $detail['credit'],
                            ]);
                        }
                    }
                }
            }
        }
        return redirect(route('subjects.index'));
    }
}
