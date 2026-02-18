<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SubjectList extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'department_id',
        'curriculum_id',
        'semester_id'
    ];

    public function curriculum(): hasOne
    {
        return $this->hasOne(Curriculum::class, 'id', 'curriculum_id');
    }

    public function department(): hasOne
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }

    public function semester(): hasOne
    {
        return $this->hasOne(Semester::class, 'id', 'semester_id');
    }

    public function subject(): hasOne
    {
        return $this->hasOne(Subject::class, 'id', 'subject_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_teachers', 'subject_id', 'user_id')->withTimestamps();;
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'subject_id', 'id');
    }

    public function getQuestionCountByTeacherAndLang($teacherId, $languageId)
    {
        return $this->questions()->where('user_id', $teacherId)->where('language_id', $languageId)->count();
    }

    public function langauges(): HasMany
    {
        return $this->hasMany(Language::class, 'subject_id', 'id');
    }
}
