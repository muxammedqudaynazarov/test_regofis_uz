<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\EduYear;
use App\Models\Exam;
use App\Models\Group;
use App\Models\GroupSubject;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApplicationController extends Controller
{
    public function index()
    {

    }

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
                        'id' => $app['id'],
                        'application_number' => $app['application_number'],
                    ], [
                        'student_id' => $app['student_id'],
                        'education_year' => $app['education_year'],
                        'status' => $app['status'],
                        'created_at' => $app['created_at'],
                    ]);
                    $response = Http::withToken(env('API_HEMIS'))->get('https://student.karsu.uz/rest/v1/data/curriculum-subject-list', [
                        '_curriculum' => auth('student')->user()->curriculum_id,
                        'limit' => 200,
                    ]);
                    $subject_hemis = $response->json();
                    $subject_lists = $subject_hemis['data']['items'];
                    foreach ($app['details'] as $detail) {
                        if ($detail['student_group']) {
                            $group = Group::firstOrCreate([
                                'id' => $detail['student_group']['id'],
                            ], [
                                'name' => $detail['student_group']['name'],
                            ]);

                            $group_subject = GroupSubject::firstOrCreate([
                                'id' => $detail['id'],
                                'failed_subject_id' => $detail['failed_subject_id'],
                                'subject_id' => $detail['subject_id'],
                            ], [
                                'application_id' => $application->id,
                                'group_id' => $group->id,
                                'subject_name' => $detail['subject_name'],
                                'semester_code' => $detail['semester_code'],
                                'credit' => $detail['credit'],
                            ]);
                            $subject_id = null;
                            foreach ($subject_lists as $subject_list) {
                                if (trim($subject_list['subject']['name']) == trim($detail['subject_name'])) {
                                    $subject_id = $subject_list['subject']['id'];
                                    break;
                                }
                            }
                            $subject_list = SubjectList::where('subject_id', $subject_id)
                                ->where('curriculum_id', auth('student')->user()->curriculum_id)
                                ->where('semester_id', $detail['semester_code'])->first();
                            Exam::firstOrCreate([
                                'application_id' => $application->id,
                                'student_id' => $app['student_id'],
                                'subject_id' => $subject_list->id,
                                'failed_subject_id' => $detail['failed_subject_id'],
                                'group_id' => $group->id,
                                'semester_id' => $detail['semester_code'],],
                                ['status' => '0',]);
                        }
                    }
                }
            }
        }
        return redirect(route('subjects.index'))->with('success', 'Fan ma’lumotlari yangilandi.');
    }
}
