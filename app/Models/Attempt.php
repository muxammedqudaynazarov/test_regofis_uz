<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Attempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'question_id',
        'pos',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'id', 'question_id');
    }

}
