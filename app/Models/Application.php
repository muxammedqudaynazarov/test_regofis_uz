<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'application_number', 'student_id', 'education_year', 'status', 'created_at'
    ];

    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'id', 'student_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'application_id', 'id');
    }

    public function edu_year(): HasOne
    {
        return $this->hasOne(EduYear::class, 'id', 'education_year');
    }
}
