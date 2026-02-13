<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HemisController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubjectTeacherController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/*Route::middleware(['exam.client'])->get('/', function () {
    return view('welcome');
});*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Route removed as part of code optimization

Route::prefix('login')->group(function () {
    Route::get('/user', [HemisController::class, 'user'])->name('login.user');
    Route::get('/student', [HemisController::class, 'student'])->name('login.student');
});

Route::get('/logout', function () {
    Auth::guard('student')->logout();
    Auth::guard('web')->logout();
    return redirect('/');
});

Route::post('/test/ping', [ExamController::class, 'ping']);

Route::prefix('home')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::resource('subjects', SubjectController::class);
    Route::resource('lessons', SubjectTeacherController::class);
    Route::resource('subjects-register', SubjectTeacherController::class);
    Route::resource('applications', ApplicationController::class)->only(['store']);
});
