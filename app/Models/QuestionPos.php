<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QuestionPos extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'student_id',
        'question_id',
        'pos',
    ];

    public function question(): hasOne
    {
        return $this->hasOne(Question::class, 'id', 'question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AnswerPos::class, 'question_id', 'question_id');
    }
}
