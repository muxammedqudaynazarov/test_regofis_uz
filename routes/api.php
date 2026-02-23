<?php

use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\LevelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/get-specialties', [CurriculumController::class, 'getSpecialties'])->name('api.get-specialties');
Route::post('/getsms/send', [LevelController::class, 'sms_sender']);
