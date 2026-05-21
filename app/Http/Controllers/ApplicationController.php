<?php

namespace App\Http\Controllers;

use App\Exports\EmptyExamsExport;
use App\Exports\UntakenExamsExport;
use App\Models\Application;
use App\Models\Attempt;
use App\Models\EduYear;
use App\Models\Exam;
use App\Models\Group;
use App\Models\GroupSubject;
use App\Models\Result;
use App\Models\Retrain;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\NoReturn;
use Maatwebsite\Excel\Facades\Excel;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->can('applications.view')) {

            $query = Application::query();

            // Agar qidiruv so'rovi kelsa (AJAX yoki oddiy GET)
            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    // 1. Ariza raqami (application_number) yoki jadval id'si bo'yicha
                    $q->where('application_number', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%")
                        // 2. Talaba ID raqami bo'yicha
                        ->orWhere('student_id', 'like', "%{$search}%")
                        // 3. Talaba F.I.Sh bo'yicha (JSON ustun ichidan qidirish)
                        ->orWhereHas('student', function ($studentQuery) use ($search) {
                            $studentQuery->where('name->first_name', 'like', "%{$search}%")
                                ->orWhere('name->second_name', 'like', "%{$search}%")
                                ->orWhere('name->third_name', 'like', "%{$search}%")
                                ->orWhere('name->full_name', 'like', "%{$search}%")
                                ->orWhere('name->short_name', 'like', "%{$search}%");
                        });
                });
            }

            $applications = $query->latest()->paginate(20)->appends($request->all());

            if ($request->ajax()) {
                try {
                    return response()->json([
                        'table_html' => view('pages.web.applications.table_rows', compact('applications'))->render(),
                        'pagination_html' => (string)$applications->appends($request->all())->links()
                    ]);
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
                }
            }
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

    public function update(Request $request, $exam)
    {
        $exam = Exam::findOrFail($exam);
        if ($exam->status == '2') {
            if ($exam->results->first()->point < 60) {
                $exam->finished = '0';
                $exam->archived = '0';
                $exam->attempt = 1;
                $exam->status = '0';
                $exam->user_id = auth()->id();
                $exam->finished_at = null;
                $exam->save();
                Attempt::where('exam_id', $exam->id)->delete();
                Result::where('exam_id', $exam->id)->delete();
                return redirect()->back()->with('success', 'Hamma natijalar bekor qilindi va talaba uchun test imkoniyati yaratildi!');
            }
            return redirect()->back()->with('success', 'O‘tish balini to‘plagan talabaning natijalarini bekor qilib bo‘lmaydi!');
        }
        return redirect()->back()->with('success', 'Talaba imtihonni boshlamagan yoki yakunlamagan!');
    }

    public function untaken_exams_download()
    {
        if (!auth()->user()->can('statistics.view.sv')) abort(404);
        $untakenExams = Exam::select('exams.*')
            ->join('applications', 'exams.application_id', '=', 'applications.id')
            ->join('students', 'applications.student_id', '=', 'students.id')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('questions')
                    ->whereColumn('questions.subject_id', 'exams.subject_id')
                    ->whereColumn('questions.language_id', 'students.language_id');
            })
            ->whereNull('exams.finished_at')
            ->with(['application.student'])
            ->get();
        if ($untakenExams->isEmpty()) {
            return back()->with('success', 'Tayyor resursli, lekin topshirilmagan imtihonlar topilmadi.');
        }
        return Excel::download(new UntakenExamsExport($untakenExams), 'topshirilmagan_tayyor_fanlar_' . date('dmy-Hi') . '.xlsx');
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
            $response = Http::withToken(env('REGOFIS_TOKEN'))
                ->timeout(15)
                ->get('https://edu.regofis.uz/api/applications/', [
                    'student_id' => auth('student')->id(),
                    'pageSize' => 100,
                ]);
            if (!$response->successful()) {
                return redirect()->route('subjects.index')
                    ->with('error', 'RegOFIS tizimi bilan aloqa o‘rnatilmadi. Qayta urinib ko‘ring.');
            }
            $data = $response->json();
            $apps = $data['data'][0]['items'] ?? [];
            if (empty($apps)) {
                return redirect()->route('subjects.index')
                    ->with('info', 'Sizda tasdiqlangan arizalar mavjud emas.');
            }
            $hemisPerformances = Http::withToken(env('API_HEMIS'))->timeout(15)
                ->get('https://student.karsu.uz/rest/v1/data/student-performance-list', [
                    '_student' => auth('student')->user()->hemis_id,
                    'limit' => 200,
                    'page' => 1
                ]);
            if (!$hemisPerformances->successful()) {
                return redirect()->route('subjects.index')
                    ->with('error', 'HEMIS tizimi javob bermayapti. Iltimos qayta urinib ko‘ring.');
            }
            $hemisResponses = $hemisPerformances->json();
            $pageCount = $hemisResponses['data']['pagination']['pageCount'] ?? 1;
            $subjects = [];
            foreach ($hemisResponses['data']['items'] as $item) {
                if (($item['examType']['code'] ?? '') == '13') {
                    $subjects[] = $item;
                }
            }
            if ($pageCount > 1) {
                $poolResponses = Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($pageCount) {
                    $requests = [];
                    for ($i = 2; $i <= $pageCount; $i++) {
                        $requests[] = $pool->withToken(env('API_HEMIS'))
                            ->timeout(15)
                            ->get('https://student.karsu.uz/rest/v1/data/student-performance-list', [
                                '_student' => auth('student')->user()->hemis_id,
                                'limit' => 200,
                                'page' => $i
                            ]);
                    }
                    return $requests;
                });
                foreach ($poolResponses as $poolResponse) {
                    if ($poolResponse->ok()) {
                        $resData = $poolResponse->json();
                        foreach ($resData['data']['items'] as $item) {
                            if (($item['examType']['code'] ?? '') == '13') {
                                $subjects[] = $item;
                            }
                        }
                    }
                }
            }
            $retrain = Retrain::where('status', '1')->first();
            DB::transaction(function () use ($retrain, $apps, $subjects) {
                foreach ($apps as $app) {
                    $application = Application::updateOrCreate([
                        'id' => $app['id'],
                        'application_number' => $app['application_number'],
                    ], [
                        'student_id' => $app['student_id'],
                        'retrain_id' => $retrain->id,
                        'education_year' => $app['education_year'],
                        'status' => $app['status'],
                        'created_at' => $app['created_at'],
                    ]);
                    if ($app['status'] == 'approved') {
                        $details = $app['details'] ?? [];
                        //dd($details, auth('student')->user());
                        foreach ($details as $detail) {
                            if (!empty($detail['student_group'])) {
                                $group = Group::updateOrCreate(
                                    ['id' => $detail['student_group']['id']],
                                    ['name' => $detail['student_group']['name']]
                                );
                                GroupSubject::updateOrCreate([
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
                                $semester_id = null;
                                //dd($subjects);
                                foreach ($subjects as $list) {
                                    if (isset($list['subject']['name']) && trim($list['subject']['name']) == trim($detail['subject_name'])) {
                                        $subject_id = $list['subject']['id'];
                                        $semester_id = $list['semester']['code'] ?? null;
                                        if ($semester_id == $detail['semester_code']) break;
                                    }
                                }
                                /*                                if (auth('student')->id() == 346231101232)
                                                                    dd($subjects, $subject_id, $semester_id, auth('student')->user()->curriculum_id);*/
                                $db_subject_list = SubjectList::where('subject_id', $subject_id)
                                    ->where('curriculum_id', auth('student')->user()->curriculum_id)
                                    ->where('semester_id', $semester_id)
                                    ->first();
                                if ($db_subject_list) {
                                    Exam::firstOrCreate([
                                        'application_id' => $application->id,
                                        'student_id' => $app['student_id'],
                                        'subject_id' => $db_subject_list->id,
                                        'failed_subject_id' => $detail['failed_subject_id'],
                                        'group_id' => $group->id,
                                    ], [
                                        'semester_id' => $semester_id,
                                        'status' => '0',
                                        'retrain_id' => $retrain->id,
                                    ]);
                                }
                            }
                        }
                    }
                }
            });
            return redirect()->route('subjects.index')
                ->with('success', 'Fan ma’lumotlari muvaffaqiyatli yangilandi.');

        } catch (\Illuminate\Http\Client\ConnectionException|\Illuminate\Http\Client\RequestException $e) {
            // 5. Tashqi API qulaganini ushlab qolish
            return redirect()->route('subjects.index')
                ->with('error', 'Tashqi tizimlar (RegOFIS/HEMIS) javob bermayapti. Iltimos, serverlar ishlayotganini tekshiring.');
        } catch (\Exception $e) {
            Log::error('RegOFIS Store Xatoligi: ' . $e->getMessage(), ['line' => $e->getLine(), 'file' => $e->getFile()]);
            return redirect()->route('subjects.index')
                ->with('error', 'Kutilmagan xatolik yuz berdi. Iltimos, administratorga murojaat qiling.');
        }
    }
    /*public function store(Request $request)
    {
        try {
            $response = Http::withToken(env('REGOFIS_TOKEN'))
                ->timeout(60)->get('https://edu.regofis.uz/api/applications/', [
                    'student_id' => auth('student')->id(),
                    'pageSize' => 100,
                ]);

            if (!$response->successful()) {
                return redirect()->route('subjects.index')
                    ->with('error', 'RegOFIS tizimi bilan aloqa o‘rnatilmadi. Qayta urinib ko‘ring.');
            }

            $data = $response->json();
            $apps = $data['data'][0]['items'] ?? []; // Xavfsiz olish

            if (empty($apps)) {
                return redirect()->route('subjects.index')
                    ->with('info', 'Sizda tasdiqlangan arizalar mavjud emas.');
            }

            //$hemisResponse = Http::withToken(env('API_HEMIS'))->timeout(60)
            //    ->get('https://student.karsu.uz/rest/v1/data/curriculum-subject-list', [
            //        '_curriculum' => auth('student')->user()->curriculum_id,
            //        'limit' => 200,
            //    ]);


            $page = 1;
            $subjects = [];
            do {
                $hemisPerformances = Http::withToken(env('API_HEMIS'))->timeout(60)
                    ->get('https://student.karsu.uz/rest/v1/data/student-performance-list', [
                        '_student' => auth('student')->user()->hemis_id,
                        'limit' => 200,
                        'page' => $page
                    ]);
                $hemisResponses = $hemisPerformances->json();
                foreach ($hemisResponses['data']['items'] as $item) {
                    if ($item['examType']['code'] == '13' && $item['grade'] < 30) $subjects[] = $item;
                }
                $pageCount = $hemisResponses['data']['pagination']['pageCount'] ?? 1;
                $page++;
            } while ($page <= $pageCount);


            foreach ($apps as $app) {
                if ($app['status'] == 'approved') {
                    $application = Application::updateOrCreate([
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
                            $group = Group::updateOrCreate([
                                'id' => $detail['student_group']['id'],
                            ], [
                                'name' => $detail['student_group']['name'],
                            ]);
                            GroupSubject::updateOrCreate([
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
                            $semester_id = null;
                            foreach ($subjects as $list) {
                                if (isset($list['subject']['name']) && trim($list['subject']['name']) == trim($detail['subject_name'])) {
                                    $subject_id = $list['subject']['id'];
                                    $semester_id = $list['semester']['code'];
                                    if ($semester_id == $detail['semester_code']) break;
                                }
                            }

                            $db_subject_list = SubjectList::where('subject_id', $subject_id)
                                ->where('curriculum_id', auth('student')->user()->curriculum_id)
                                ->where('semester_id', $semester_id)
                                ->first();
                            if ($db_subject_list) {
                                Exam::updateOrCreate([
                                    'application_id' => $application->id,
                                    'student_id' => $app['student_id'],
                                    'subject_id' => $db_subject_list->id,
                                    'failed_subject_id' => $detail['failed_subject_id'],
                                    'group_id' => $group->id,
                                ], [
                                    'semester_id' => $semester_id,
                                    'status' => '0',
                                ]);
                            }
                        }
                    }
                }
            }
            return redirect()->route('subjects.index')
                ->with('success', 'Fan ma’lumotlari muvaffaqiyatli yangilandi.');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return redirect()->route('subjects.index')
                ->with('error', 'Tashqi tizimlar (RegOFIS/HEMIS) bilan aloqa yo‘q. Iltimos, birozdan so‘ng qayta urinib ko‘ring.');

        } catch (\Exception $e) {
            Log::error('RegOFIS Store Xatoligi: ' . $e->getMessage(), ['line' => $e->getLine(), 'file' => $e->getFile()]);
            return redirect()->route('subjects.index')
                ->with('error', 'Kutilmagan xatolik yuz berdi. Iltimos, administratorga murojaat qiling.');
        }
    }*/
}
