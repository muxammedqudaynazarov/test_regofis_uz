<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use HasFactory;

    protected $table = 'students';
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'id', 'name', 'student_id', 'uuid', 'picture'
    ];

    public function course(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(Course::class, StudentCourse::class, 'student_id', 'id', 'id', 'course_id');
    }

    public function level(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(Level::class, StudentCourse::class, 'student_id', 'id', 'id', 'level_id');
    }
}
