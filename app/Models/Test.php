<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject_id',
        'started_at',
        'finished_at',
        'durations',
        'questions',
        'attempts',
        'points',
        'prod_point',
        'retest',
        'type',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function questions_list(): HasMany
    {
        return $this->hasMany(Question::class, 'test_id', 'id');
    }

    public function subject(): HasOne
    {
        return $this->hasOne(Subject::class, 'subject_id', 'id');
    }

    public function exams(): hasMany
    {
        return $this->hasMany(Exam::class, 'test_id', 'id');
    }
}
