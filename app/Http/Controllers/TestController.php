<?php

namespace App\Http\Controllers;

use App\Models\GroupSubject;
use App\Models\Test;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index()
    {
        $user_guard = Auth::guard('student')->check() ? 'student' : 'web';
        $user = auth($user_guard)->user();

        $subjects = GroupSubject::where('student_id', auth('student')->id())
            ->whereHas('subject.test', function ($query) {
                $query->where('status', '1');
            })
            ->with(['subject.test'])
            ->get();

        return view('pages.student.test.index', compact(['subjects']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'started_at' => 'required|date',
            'finished_at' => 'required|date|after:started_at',
            'durations' => 'required',
            'questions' => 'required',
            'attempts' => 'required',
            'points' => 'required',
        ]);

        try {
            DB::beginTransaction();
            $exists = Test::where('subject_id', $validated['subject_id'])->exists();
            if ($exists) {
                return redirect()->back()->with('error', 'Ushbu fanda test allaqachon mavjud.');
            }

            $subject = Subject::findOrFail($validated['subject_id']);

            Test::create([
                'name' => $subject->name . ' / YN', // Test nomi fan nomi bilan bir xil
                'subject_id' => $validated['subject_id'],
                'started_at' => $validated['started_at'],
                'finished_at' => $validated['finished_at'],
                'durations' => $validated['durations'],
                'questions' => $validated['questions'],
                'attempts' => $validated['attempts'],
                'retest' => 'y',    // Standart qiymat
                'points' => $validated['points'],
                'type' => 'yn',   // Standart qiymat
                'status' => '1',    // Aktiv holat
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Test muvaffaqiyatli yaratildi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }
}
