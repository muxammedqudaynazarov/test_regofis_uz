<?php

namespace App\Http\Controllers;

use App\Exports\DepartmentResourcesExport;
use App\Exports\DepartmentSubjectExport;
use App\Exports\EmptyExamsExport;
use App\Exports\EmptyLessonsExport;
use App\Exports\FinishedExamsExport;
use App\Exports\UntakenExamsExport;
use App\Jobs\ExportJob;
use App\Models\Download;
use App\Models\Exam;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    // Export turlari va ularning export klasslari
    private array $exportTypes = [
        'department_resources'    => [
            'name'  => 'Kafedralar resurslari hisoboti',
            'class' => DepartmentResourcesExport::class,
            'perm'  => 'statistics.view.sv',
        ],
        'empty_lessons'           => [
            'name'  => 'Bo\'sh fanlar hisoboti',
            'class' => EmptyLessonsExport::class,
            'perm'  => 'statistics.view.sv',
        ],
        'finished_exams'          => [
            'name'  => 'Yakuniy qaydnomalar',
            'class' => FinishedExamsExport::class,
            'perm'  => 'statistics.view.sv',
        ],
        'empty_exams'             => [
            'name'  => 'Resurs yo\'q fanlar',
            'class' => EmptyExamsExport::class,
            'perm'  => 'statistics.view.sv',
        ],
        'untaken_exams'           => [
            'name'  => 'Imtihon boshlamagan talabalar',
            'class' => UntakenExamsExport::class,
            'perm'  => 'statistics.view.sv',
        ],
        'department_subject'      => [
            'name'  => 'Kafedra bo\'yicha resurslar',
            'class' => DepartmentSubjectExport::class,
            'perm'  => 'statistics.export',
        ],
    ];

    // Export navbatga qo'shish
    public function store(Request $request)
    {
        $type = $request->input('type');

        if (!isset($this->exportTypes[$type])) {
            return redirect()->back()->with('error', 'Noto\'g\'ri export turi.');
        }

        $meta = $this->exportTypes[$type];

        if (!auth()->user()->can($meta['perm'])) {
            abort(403);
        }

        // Parametrlarni yig'ish
        $params   = [];
        $metadata = [];

        if ($type === 'finished_exams') {
            $retrainId = $request->input('optional_id');
            $metadata  = ['retrain_id' => $retrainId];

            $query = Exam::where('status', '2');
            if ($retrainId) {
                $query->whereHas('application', fn($q) => $q->where('retrain_id', $retrainId));
            }
            $latestIds = $query->select(DB::raw('MAX(id) as id'))
                ->groupBy('student_id', 'subject_id')->pluck('id');
            $exams = Exam::with([
                'application.student.specialty.department',
                'failed_subject', 'semester', 'results', 'result'
            ])->whereIn('id', $latestIds)->orderBy('id', 'desc')->get();

            if ($exams->isEmpty()) {
                return redirect()->back()->with('error', 'Yuklab olish uchun ma\'lumot topilmadi.');
            }
            $params = [$exams];

        } elseif ($type === 'empty_exams') {
            $emptyExams = Exam::select('exams.*')
                ->join('applications', 'exams.application_id', '=', 'applications.id')
                ->join('students', 'applications.student_id', '=', 'students.id')
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))->from('questions')
                      ->whereColumn('questions.subject_id', 'exams.subject_id')
                      ->whereColumn('questions.language_id', 'students.language_id');
                })->with(['application.student'])->get();

            if ($emptyExams->isEmpty()) {
                return redirect()->back()->with('info', 'Savolsiz imtihonlar topilmadi.');
            }
            $params = [$emptyExams];

        } elseif ($type === 'untaken_exams') {
            $untakenExams = Exam::select('exams.*')
                ->join('applications', 'exams.application_id', '=', 'applications.id')
                ->join('students', 'applications.student_id', '=', 'students.id')
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))->from('questions')
                      ->whereColumn('questions.subject_id', 'exams.subject_id')
                      ->whereColumn('questions.language_id', 'students.language_id');
                })
                ->whereNull('exams.finished_at')
                ->with(['application.student'])->get();

            if ($untakenExams->isEmpty()) {
                return redirect()->back()->with('info', 'Topshirilmagan imtihonlar topilmadi.');
            }
            $params = [$untakenExams];
        }

        $filename = $type . '_' . date('dmY-His') . '.xlsx';

        $download = Download::create([
            'type'     => $type,
            'name'     => $meta['name'],
            'filename' => $filename,
            'status'   => 'pending',
            'metadata' => $metadata ?: null,
            'user_id'  => auth()->id(),
        ]);

        ExportJob::dispatch($download->id, $meta['class'], $params)
            ->onQueue('default');

        return redirect()->back()->with('info',
            "«{$meta['name']}» hisoboti tayyorlanmoqda. Tayyor bo'lgach \"Yuklab olish\" tugmasi faollashadi."
        );
    }

    // Fayl holati (AJAX)
    public function status($id)
    {
        $download = Download::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'status'  => $download->status,
            'label'   => $download->statusLabel(),
            'badge'   => $download->statusBadge(),
            'ready'   => $download->isReady(),
        ]);
    }

    // Faylni yuklab olish
    public function download($id)
    {
        $download = Download::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'ready')
            ->firstOrFail();

        if (!Storage::disk('local')->exists($download->path)) {
            return redirect()->back()->with('error', 'Fayl topilmadi. Qayta export qiling.');
        }

        return Storage::disk('local')->download($download->path, $download->filename);
    }

    // Eski yuklanmalarni o'chirish
    public function destroy($id)
    {
        $download = Download::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($download->path && Storage::disk('local')->exists($download->path)) {
            Storage::disk('local')->delete($download->path);
        }

        $download->delete();
        return redirect()->back()->with('success', 'O\'chirildi.');
    }
}
