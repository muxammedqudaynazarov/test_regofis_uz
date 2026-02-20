<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HemisController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\Statistics\DepartmentRoleInfoController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubjectTeacherController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/hemis', [HemisController::class, 'data']);

Route::prefix('login')->group(function () {
    Route::get('/user', [HemisController::class, 'user'])->name('login.user');
    Route::get('/student', [HemisController::class, 'student'])->name('login.student');
});

Route::get('/logout', function () {
    Auth::guard('student')->logout();
    Auth::guard('web')->logout();
    return redirect('/');
});
Route::get('/login', function () {
    return redirect('/');
});
Route::prefix('student')->middleware('auth:student')->group(function () {
    Route::get('/', [StudentController::class, 'index'])->name('student.home');
    Route::resource('subjects', SubjectController::class)->only(['index']);
    Route::resource('applications', ApplicationController::class)->only(['index', 'store']);
    Route::resource('tests', TestController::class)->only(['index', 'show']);
    Route::post('/exams/answer/upload', [TestController::class, 'upload_answer']);
    Route::resource('results', ResultController::class)->only(['index', 'update']);
});


Route::prefix('home')->middleware('auth:web')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/user/{role}', [HomeController::class, 'switch_role'])->name('switch.role');
    Route::resource('departments', DepartmentController::class)->only(['show', 'update']);
    Route::resource('options', OptionController::class)->only(['index', 'update']);
    Route::resource('curriculum', CurriculumController::class)->only(['index', 'destroy']);
    Route::resource('subjects-register', SubjectTeacherController::class)->only(['index', 'store', 'edit', 'create', 'destroy']);
    Route::resource('lessons', LessonController::class)->only(['index', 'show', 'update']);
    Route::resource('languages', LanguageController::class)->only(['index', 'update']);
    Route::delete('/questions/destroy-many', [QuestionController::class, 'destroyMany'])->name('questions.destroyMany');
    Route::resource('questions', QuestionController::class)->only(['update', 'destroy']);
    Route::resource('final-results', ExamController::class)->only(['index']);
    Route::resource('statistics', StatisticsController::class)->only(['index']);
    Route::prefix('statistics')->group(function () {
        Route::get('/department/resources', [DepartmentRoleInfoController::class, 'role_department'])->name('statistics.department.resources');
        Route::get('/department/resources/download', [DepartmentRoleInfoController::class, 'export_role_department'])->name('statistics.department.resources.export');
        Route::get('/departments/show/download', [DepartmentController::class, 'download'])->name('departments.download');
        Route::get('/lessons/empty/download', [LessonController::class, 'empty_lessons_download'])->name('lessons.empty.download');
    });
});
