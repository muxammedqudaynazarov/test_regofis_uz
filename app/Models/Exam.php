<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'student_id',
        'subject_id',
        'failed_subject_id',
        'group_id',
        'semester_id',
        'finished_at',
        'status',
    ];

    public function application(): HasOne
    {
        return $this->hasOne(Application::class, 'id', 'application_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class, 'exam_id', 'id');
    }

    public function failed_subject(): HasOne
    {
        return $this->hasOne(GroupSubject::class, 'failed_subject_id', 'failed_subject_id');
    }

    public function group(): HasOne
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }

    public function semester(): HasOne
    {
        return $this->hasOne(Semester::class, 'id', 'semester_id');
    }

    public function resource(): HasOne
    {
        return $this->hasOne(SubjectList::class, 'id', 'subject_id');
    }
}
