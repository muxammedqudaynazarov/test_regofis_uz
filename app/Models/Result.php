<?php

namespace App\Models;

use App\Traits\LogsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory, LogsTrait;

    protected $fillable = [
        'student_id',
        'exam_id',
        'point',
        'uploaded',
        'user_id',
        'status',
    ];
}
