<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HemisController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResultController;
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


Route::prefix('home')->middleware('auth:web,student')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::resource('departments', DepartmentController::class)->only(['show']);
    Route::resource('curriculum', CurriculumController::class)->only(['index']);
    Route::resource('subjects', SubjectController::class);
    Route::resource('subjects-register', SubjectTeacherController::class);
    Route::resource('lessons', LessonController::class);
    Route::resource('questions', QuestionController::class);
    Route::resource('tests', TestController::class);
    Route::resource('exams', ExamController::class)->only(['show', 'update']);
    Route::post('exams/answer/upload', [ExamController::class, 'upload_answer']);
    Route::resource('results', ResultController::class)->only(['store']);
    Route::resource('applications', ApplicationController::class)->only(['store']);
});
