<?php

namespace App\Models;

use App\Traits\LogsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Result extends Model
{
    use HasFactory, LogsTrait;

    protected $fillable = [
        'student_id',
        'exam_id',
        'retrain_id',
        'point',
        'uploaded',
        'user_id',
        'status',
    ];

    public function exam(): hasOne
    {
        return $this->hasOne(Exam::class, 'id', 'exam_id');
    }
}
