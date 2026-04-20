<?php

namespace App\Models;

use App\Traits\LogsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCourse extends Model
{
    use HasFactory, LogsTrait;

    protected $fillable = ['student_id', 'course_id', 'level_id'];
}
