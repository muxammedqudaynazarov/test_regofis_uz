<?php

namespace App\Jobs;

use App\Models\Application;
use App\Models\Exam;
use App\Models\Group;
use App\Models\GroupSubject;
use App\Models\Retrain;
use App\Models\Student;
use App\Models\SubjectList;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncStudentApplicationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;  // 2 daqiqa
    public int $tries = 2;    // Xato bo'lsa 2 marta urinish

    public function __construct(
        private readonly int $studentId
    )
    {
    }

    public function handle(): void
    {
        $student = Student::findOrFail($this->studentId);

        // RegOFIS dan arizalarni olish
        $response = Http::withToken(config('services.regofis.token'))
            ->timeout(30)
            ->get(config('services.regofis.api_url') . '/applications/', [
                'student_id' => $this->studentId,
                'pageSize' => 100,
            ]);

        if (!$response->successful()) {
            Log::warning("RegOFIS API javob bermadi, student: {$this->studentId}");
            return;
        }

        $apps = $response->json()['data'][0]['items'] ?? [];
        if (empty($apps)) return;

        // HEMIS dan performance ma'lumotlari
        $hemisResponse = Http::withToken(config('services.hemis.token'))
            ->timeout(30)
            ->get(config('services.hemis.student_url') . '/rest/v1/data/student-performance-list', [
                '_student' => $student->hemis_id,
                'limit' => 200,
                'page' => 1,
            ]);

        if (!$hemisResponse->successful()) {
            Log::warning("HEMIS API javob bermadi, student: {$this->studentId}");
            return;
        }

        $hemisData = $hemisResponse->json();
        $subjects = collect($hemisData['data']['items'] ?? [])
            ->where('examType.code', '13')
            ->values();

        $retrain = Retrain::where('status', '1')->first();
        if (!$retrain) return;

        DB::transaction(function () use ($apps, $subjects, $student, $retrain) {
            foreach ($apps as $app) {
                if ($app['status'] !== 'approved') continue;

                $application = Application::updateOrCreate(
                    ['id' => $app['id'], 'application_number' => $app['application_number']],
                    [
                        'student_id' => $app['student_id'],
                        'retrain_id' => $retrain->id,
                        'education_year' => $app['education_year'],
                        'status' => $app['status'],
                        'created_at' => $app['created_at'],
                    ]
                );

                foreach ($app['details'] ?? [] as $detail) {
                    if (empty($detail['student_group'])) continue;

                    $group = Group::updateOrCreate(
                        ['id' => $detail['student_group']['id']],
                        ['name' => $detail['student_group']['name']]
                    );

                    GroupSubject::updateOrCreate(
                        ['id' => $detail['id']],
                        [
                            'failed_subject_id' => $detail['failed_subject_id'],
                            'subject_id' => $detail['subject_id'],
                            'application_id' => $application->id,
                            'group_id' => $group->id,
                            'subject_name' => $detail['subject_name'],
                            'semester_code' => $detail['semester_code'],
                            'credit' => $detail['credit'],
                        ]
                    );

                    $match = $subjects->first(fn($s) => isset($s['subject']['name']) &&
                        trim($s['subject']['name']) === trim($detail['subject_name']) &&
                        ($s['semester']['code'] ?? null) == $detail['semester_code']
                    );

                    if (!$match) continue;

                    $subjectList = SubjectList::where('subject_id', $match['subject']['id'])
                        ->where('curriculum_id', $student->curriculum_id)
                        ->where('semester_id', $match['semester']['code'])
                        ->first();

                    if ($subjectList) {
                        Exam::firstOrCreate(
                            [
                                'application_id' => $application->id,
                                'student_id' => $app['student_id'],
                                'subject_id' => $subjectList->id,
                                'failed_subject_id' => $detail['failed_subject_id'],
                                'group_id' => $group->id,
                            ],
                            [
                                'semester_id' => $match['semester']['code'],
                                'status' => '0',
                                'retrain_id' => $retrain->id,
                            ]
                        );
                    }
                }
            }
        });
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SyncStudentApplicationsJob xatolik (student: {$this->studentId}): " . $exception->getMessage());
    }
}
