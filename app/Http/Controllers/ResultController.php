<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Attempt;
use App\Models\Exam;
use App\Models\Option;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResultController extends Controller
{
    public function index()
    {
        $user = auth('student')->user();
        if ($user) {
            $subjects = Exam::where('student_id', $user->id)
                ->where('finished', '1')
                ->paginate(20);
            return view('pages.student.subjects.result', compact('subjects', 'user'));
        }
        abort(404);
    }
    public function update($id, Request $request)
    {
        $qCount = (int)(Option::where('key', 'questions')->value('value') ?: 50);
        $max_points = (float)(Option::where('key', 'max_points')->value('value') ?: 100);
        $min_points = (float)(Option::where('key', 'min_points')->value('value') ?: 60);
        $per_point = $qCount > 0 ? $max_points / $qCount : 0;

        $examId = $request->exam_id;
        $studentId = auth('student')->id();

        // Forma yuborgan javoblar: [question_id => answer_id]
        $formAnswers = $request->input('attempt', []);

        try {
            DB::transaction(function () use (
                $examId, $studentId, $formAnswers,
                $min_points, $per_point
            ) {
                $exam = Exam::findOrFail($examId);

                // 1. Egalik tekshiruvi
                if ($exam->student_id !== $studentId) {
                    throw new \Exception('Bu imtihon sizga tegishli emas.', 403);
                }

                // 2. Status tekshiruvi
                if (!in_array($exam->status, ['1', '4', '7'])) {
                    throw new \Exception('Imtihon allaqachon yakunlangan.', 422);
                }

                // 3. Forma javoblarini DB ga saqlash
                // (AJAX race condition ni hal qiladi — forma eng so'nggi holat)
                if (!empty($formAnswers)) {
                    foreach ($formAnswers as $questionId => $answerId) {
                        Attempt::where('exam_id', $examId)
                            ->where('student_id', $studentId)
                            ->where('question_id', $questionId)
                            ->update(['answer_id' => $answerId]);
                    }
                }

                // 4. DB dan barcha javoblarni o'qib ball hisoblash
                $correctCount = Attempt::where('exam_id', $examId)
                    ->where('student_id', $studentId)
                    ->whereNotNull('answer_id')
                    ->whereHas('answer', fn($q) => $q->where('correct', '1'))
                    ->count();

                $point = $correctCount * $per_point;

                // 5. Exam statusini yangilash
                $newStatus = match ($exam->status) {
                    '4' => '5',
                    '7' => '8',
                    default => '2',
                };
                $exam->status = $newStatus;
                $exam->finished = '1';
                $exam->save();

                // 6. Natijani saqlash (to'g'ri firstOrCreate)
                Result::firstOrCreate(
                // Faqat unique identifierlar — izlash uchun
                    [
                        'student_id' => $studentId,
                        'exam_id' => $exam->id,
                    ],
                    // Topilmasa yaratish uchun qiymatlar
                    [
                        'retrain_id' => $exam->retrain_id,
                        'point' => $point,
                        'status' => ($point < $min_points) ? '0' : '1',
                    ]
                );
            });

            return redirect(route('results.index'))
                ->with('success', 'Imtihon yakunlandi.');

        } catch (\Exception $e) {
            $code = $e->getCode();
            if (in_array($code, [403, 422])) {
                return redirect()->back()->with('error', $e->getMessage());
            }
            Log::error('ResultController::update xatolik: ' . $e->getMessage(), [
                'exam_id' => $examId,
                'student_id' => $studentId,
            ]);
            return redirect()->back()->with('error', 'Tizim xatoligi yuz berdi.');
        }
    }

    public function autoFinishExams()
    {
        $qCount = (int)(Option::where('key', 'questions')->value('value') ?: 1);
        $maxPoints = (float)(Option::where('key', 'max_points')->value('value') ?: 100);
        $minPoints = (float)(Option::where('key', 'min_points')->value('value') ?: 60);
        $perPoint = $qCount > 0 ? $maxPoints / $qCount : 0;

        $exams = Exam::whereIn('status', ['1', '4', '7'])
            ->where('finished_at', '<=', now())
            ->get();

        foreach ($exams as $exam) {
            DB::transaction(function () use ($exam, $perPoint, $minPoints) {

                // DB dan barcha to'g'ri javoblarni sanash (N+1 yo'q)
                $correctCount = Attempt::where('exam_id', $exam->id)
                    ->whereNotNull('answer_id')
                    ->whereHas('answer', fn($q) => $q->where('correct', '1'))
                    ->count();

                $point = $correctCount * $perPoint;
                $newStatus = match ($exam->status) {
                    '4' => '5',
                    '7' => '8',
                    default => '2',
                };

                $exam->update([
                    'status' => $newStatus,
                    'finished' => '1',
                ]);

                Result::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'student_id' => $exam->student_id,
                    ],
                    [
                        'retrain_id' => $exam->retrain_id, // ✅ qo'shildi
                        'point' => $point,
                        'status' => ($point < $minPoints) ? '0' : '1',
                    ]
                );
            });
        }

        return count($exams) . " ta imtihon avtomatik yakunlandi.";
    }
}
