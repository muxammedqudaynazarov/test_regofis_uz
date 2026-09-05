<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\EmployeeStaffController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HemisController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LogViewController;
use App\Http\Controllers\OfficeApplicationController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\RetrainController;
use App\Http\Controllers\Statistics\DepartmentRoleInfoController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubjectTeacherController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) return redirect()->route('home');
    if (Auth::guard('student')->check()) return redirect()->route('student.home');
    return view('welcome');
});

Auth::routes();

Route::prefix('login')->group(function () {
    Route::get('/user',    [HemisController::class, 'user'])->name('login.user');
    Route::get('/student', [HemisController::class, 'student'])->name('login.student');
});

Route::get('/logout', function () {
    Auth::guard('student')->logout();
    Auth::guard('web')->logout();
    return redirect('/');
});
Route::get('/login', fn() => redirect('/'));

// ─── TALABA ROUTELARI ──────────────────────────────────────────────────────
Route::prefix('student')->middleware('auth:student')->group(function () {
    Route::get('/', [StudentController::class, 'index'])->name('student.home');

    Route::resource('subjects', SubjectController::class)->only(['index']);

    Route::resource('applications', ApplicationController::class)
        ->only(['store'])
        ->middleware('throttle:5,1');

    // Test tekshiruvi (AJAX, throttle:5,1 — ortiqcha so'rovni cheklash)
    Route::get('/tests/{id}/check', [TestController::class, 'check'])
        ->name('tests.check')
        ->middleware('throttle:10,1');

    // Qoidabuzarlikni sessionga yozish (AJAX)
    Route::post('/exams/violation', [TestController::class, 'recordViolation'])
        ->name('exams.violation')
        ->middleware('throttle:30,1');

    Route::resource('tests', TestController::class)
        ->only(['show', 'edit'])
        ->middleware('throttle:50,1');

    Route::post('/exams/answer/upload', [TestController::class, 'upload_answer'])
        ->middleware(['throttle:420,1', 'exam.client']);

    Route::resource('results', ResultController::class)->only(['index']);
    Route::resource('results', ResultController::class)
        ->only(['update'])
        ->middleware('throttle:10,1');
});

// ─── ADMIN ROUTELARI ───────────────────────────────────────────────────────
Route::prefix('home')->middleware('auth:web')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/user/{role}', [HomeController::class, 'switch_role'])->name('switch.role');

    Route::resource('departments', DepartmentController::class)->only(['show', 'update']);
    Route::resource('logs', LogViewController::class)->only(['index', 'destroy']);
    Route::resource('options', OptionController::class)->only(['index', 'update']);
    Route::resource('applications', ApplicationController::class)->only(['index', 'show', 'update']);
    Route::resource('office_applications', OfficeApplicationController::class)->only(['index', 'store']);
    Route::resource('curriculum', CurriculumController::class)->only(['index', 'destroy']);
    Route::resource('subjects-register', SubjectTeacherController::class)->only(['index', 'create', 'store', 'edit', 'destroy']);
    Route::resource('lessons', LessonController::class)->only(['index', 'show', 'update']);
    Route::resource('retrains', RetrainController::class);
    Route::resource('languages', LanguageController::class)->only(['index', 'update']);

    Route::delete('/questions/destroy-many', [QuestionController::class, 'destroyMany'])->name('questions.destroyMany');
    Route::resource('questions', QuestionController::class)->only(['update', 'destroy']);

    Route::resource('final-results', ExamController::class)->only(['index', 'store', 'show', 'update']);
    Route::get('/final-results/{to}/list', [ExamController::class, 'status'])->name('final-results.status');

    Route::get('/department/resources', [DepartmentRoleInfoController::class, 'role_department'])
        ->name('statistics.department.resources');

    Route::resource('statistics', StatisticsController::class)->only(['index']);

    // ─── Async Exportlar ───────────────────────────────────────────────
    Route::post('/downloads',            [DownloadController::class, 'store'])->name('downloads.store');
    Route::get('/downloads/{id}/status', [DownloadController::class, 'status'])->name('downloads.status');
    Route::get('/downloads/{id}/file',   [DownloadController::class, 'download'])->name('downloads.file');
    Route::delete('/downloads/{id}',     [DownloadController::class, 'destroy'])->name('downloads.destroy');

    // ─── Foydalanuvchilarni boshqarish ────────────────────────────────
    Route::resource('users', UserController::class)->only(['index', 'show', 'update']);
});
