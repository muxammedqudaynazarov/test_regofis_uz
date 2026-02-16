<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'test_id',
        'status',
        'finished_at',
        'last_activity_at',
    ];

    public function test(): hasOne
    {
        return $this->hasOne(Test::class, 'id', 'test_id');
    }

    public function result(): hasOne
    {
        return $this->hasOne(Result::class, 'exam_id', 'id');
    }
}
