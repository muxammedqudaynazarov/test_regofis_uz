<?php

namespace App\Http\Controllers;

use App\Exports\EmptyExamsExport;
use App\Models\Application;
use App\Models\EduYear;
use App\Models\Exam;
use App\Models\Group;
use App\Models\GroupSubject;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ApplicationController extends Controller
{
    public function index()
    {
        if (auth()->user()->can('applications.view')) {
            $applications = Application::paginate(20);
            return view('pages.web.applications.index', compact(['applications']));
        }
        abort(404);
    }

    public function show($app_num)
    {
        if (auth()->user()->can('applications.show')) {
            $app = Application::where('application_number', $app_num)->firstOrFail();
            return view('pages.web.applications.show', compact(['app']));
        }
        abort(404);
    }

    public function empty_lessons_download()
    {
        if (!auth()->user()->can('statistics.view.sv')) abort(404);
        $emptyExams = Exam::select('exams.*')
            ->join('applications', 'exams.application_id', '=', 'applications.id')
            ->join('students', 'applications.student_id', '=', 'students.id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('questions')->whereColumn('questions.subject_id', 'exams.subject_id')
                    ->whereColumn('questions.language_id', 'students.language_id');
            })->with(['application.student'])->get();
        if ($emptyExams->isEmpty()) return back()->with('info', 'Savolsiz imtihonlar topilmadi.');
        return Excel::download(new EmptyExamsExport($emptyExams), 'resurslar_yoq_fanlar' . date('dmy-Hi') . '.xlsx');
    }

    public function store(Request $request)
    {
        try {
            // 1. REGOFIS API dan arizalarni olish (Timeout belgilaymiz)
            $response = Http::withToken(env('REGOFIS_TOKEN'))
                ->timeout(30)
                ->get('https://edu.regofis.uz/api/applications/', [
                    'student_id' => auth('student')->id(),
                    'pageSize' => 100,
                ]);

            // Agar server 200 OK qaytarmasa
            if (!$response->successful()) {
                return redirect()->route('subjects.index')->with('error', 'RegOFIS tizimi bilan aloqa o‘rnatilmadi. Qayta urinib ko‘ring.');
            }

            $data = $response->json();
            $apps = $data['data'][0]['items'] ?? []; // Xavfsiz olish

            if (empty($apps)) {
                return redirect()->route('subjects.index')->with('info', 'Sizda tasdiqlangan arizalar mavjud emas.');
            }

            // OPTIMIZATSIYA: HEMIS API ni sikl ichidan tashqariga chiqardik (tezlikni 10 barobar oshiradi)
            $hemisResponse = Http::withToken(env('API_HEMIS'))
                ->timeout(30)
                ->get('https://student.karsu.uz/rest/v1/data/curriculum-subject-list', [
                    '_curriculum' => auth('student')->user()->curriculum_id,
                    'limit' => 200,
                ]);

            if (!$hemisResponse->successful()) {
                return redirect()->route('subjects.index')->with('error', 'HEMIS tizimi bilan aloqa o‘rnatilmadi. Qayta urinib ko‘ring.');
            }

            $subject_hemis = $hemisResponse->json();
            $subject_lists = $subject_hemis['data']['items'] ?? [];

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

                    $details = $app['details'] ?? [];

                    foreach ($details as $detail) {
                        if (!empty($detail['student_group'])) {
                            $group = Group::firstOrCreate([
                                'id' => $detail['student_group']['id'],
                            ], [
                                'name' => $detail['student_group']['name'],
                            ]);

                            GroupSubject::firstOrCreate([
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
                            foreach ($subject_lists as $s_list) {
                                if (isset($s_list['subject']['name']) && trim($s_list['subject']['name']) == trim($detail['subject_name'])) {
                                    $subject_id = $s_list['subject']['id'];
                                    break;
                                }
                            }

                            $db_subject_list = SubjectList::where('subject_id', $subject_id)
                                ->where('curriculum_id', auth('student')->user()->curriculum_id)
                                ->where('semester_id', $detail['semester_code'])
                                ->first();

                            // 500 xatolikning oldini olish: Agar subject_list topilsagina Exam yaratamiz
                            if ($db_subject_list) {
                                Exam::firstOrCreate([
                                    'application_id' => $application->id,
                                    'student_id' => $app['student_id'],
                                    'subject_id' => $db_subject_list->id,
                                    'failed_subject_id' => $detail['failed_subject_id'],
                                    'group_id' => $group->id,
                                    'semester_id' => $detail['semester_code'],
                                ], [
                                    'status' => '0',
                                ]);
                            }
                        }
                    }
                }
            }

            return redirect()->route('subjects.index')->with('success', 'Fan ma’lumotlari muvaffaqiyatli yangilandi.');

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // API serverlar javob bermasa yoki ulanish uzilib qolsa ushlaymiz
            return redirect()->route('subjects.index')->with('error', 'Tashqi tizimlar (RegOFIS/HEMIS) bilan aloqa vaqti tugadi. Iltimos, birozdan so‘ng qayta urinib ko‘ring.');

        } catch (\Exception $e) {
            // Boshqa kutilmagan xatolar (masalan, bazadagi xatolar) ni ushlaymiz
            // Log::error orqali xatoni xotiraga yozamiz, shunda Laravel log faylida aniq ko'rasiz
            Log::error('RegOFIS Store Xatoligi: ' . $e->getMessage(), ['line' => $e->getLine(), 'file' => $e->getFile()]);
            return redirect()->route('subjects.index')->with('error', 'Kutilmagan xatolik yuz berdi. Iltimos, administratorga murojaat qiling.');
        }
    }
}
