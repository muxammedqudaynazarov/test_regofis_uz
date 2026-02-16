<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AnswerPos extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'question_id',
        'answer_id',
        'pos',
    ];

    public function answer(): HasOne
    {
        return $this->hasOne(Answer::class, 'id', 'answer_id');
    }
}
