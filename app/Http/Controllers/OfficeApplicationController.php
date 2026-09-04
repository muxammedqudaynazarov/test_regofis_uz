<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Exam;
use App\Models\Group;
use App\Models\GroupSubject;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OfficeApplicationController extends Controller
{
    public function index(Request $request)
    {
        $student = null;
        $applications = [];
        if ($request->filled('student_id')) {
            $studentId = $request->input('student_id');
            $student = Student::with('specialty')->find($studentId);
            if (!$student) {
                return redirect()->route('office_applications.index')
                    ->with('error', "Kiritilgan ID ({$studentId}) bo'yicha talaba topilmadi.");
            }
            $response = Http::withToken(config('services.regofis.token'))->timeout(15)
                ->get(config('services.regofis.api_url') . '/applications/', [
                    'student_id' => $studentId,
                    'pageSize' => 100,
                ]);
            if (!$response->successful()) {
                return back()->with('error', 'RegOFIS tizimi bilan aloqa o‘rnatilmadi. Qayta urinib ko‘ring.');
            }
            $data = $response->json();
            $applications = $data['data'][0]['items'] ?? [];
            //dd($applications);
        }
        return view('pages.web.office.index', compact(['applications', 'student']));
    }

    public function store(Request $request)
    {
        // JSON larni ochamiz
        $app = json_decode($request->app);
        $detail = json_decode($request->detail);

        // Faqat bizga kerakli ustunlarni olamiz (xotirani tejaydi)
        $student = Student::select('id', 'curriculum_id')->findOrFail($app->student_id);

        // 1. Dastlab ruxsat borligini tekshiramiz (erta qaytish - Early Return)
        $accessCreate = SubjectList::where('subject_id', $app->lesson_id)
            ->where('semester_id', $app->semester_id)->where('curriculum_id', $student->curriculum_id)->exists();

        if (!$accessCreate) {
            return redirect()->back()->with('error', 'Ushbu parametrlar bo‘yicha imtihon yaratishga ruxsat yo‘q.');
        }

        // 2. Exam uchun fanni topamiz
        $mySubject = SubjectList::where('subject_id', $app->lesson_id)
            ->where('semester_id', $detail->semester_code)->where('curriculum_id', $student->curriculum_id)->firstOrFail();

        // 3. Ma'lumotlarni bazaga yozish (Tranzaksiya ichida)
        // Agar bitta jadvalga yozishda xatolik chiqsa, barcha o'zgarishlar orqaga qaytariladi (rollback)
        DB::transaction(function () use ($app, $detail, $student, $mySubject) {
            $application = Application::firstOrCreate([
                'id' => $app->id,
                'application_number' => $app->number,
            ], [
                'education_year' => $app->education_year,
                'student_id' => $student->id,
                'status' => 'approved'
            ]);

            $st_group = Group::updateOrCreate([
                'id' => $detail->student_group->id,
            ], [
                'name' => $detail->student_group->name,
            ]);

            GroupSubject::updateOrCreate([
                'id' => $detail->id,
            ], [
                'failed_subject_id' => $detail->failed_subject_id,
                'subject_id' => $detail->subject_id,
                'application_id' => $application->id,
                'group_id' => $st_group->id,
                'subject_name' => $detail->subject_name,
                'semester_code' => $detail->semester_code,
                'credit' => $detail->credit,
            ]);

            Exam::updateOrCreate([
                'application_id' => $application->id,
                'student_id' => $application->student_id,
                'subject_id' => $mySubject->id,
                'failed_subject_id' => $detail->failed_subject_id,
                'group_id' => $st_group->id,
            ], [
                'semester_id' => $detail->semester_code,
                'status' => '0',
            ]);

        });

        return redirect()->back()->with('success', 'Talabaga fan bo‘yicha imtihon yaratildi!');
    }
}
