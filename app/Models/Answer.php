<?php

namespace App\Models;

use App\Traits\LogsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory, LogsTrait;

    protected $fillable = [
        'question_id',
        'answer',
        'correct',
        'type',
        'status',
    ];
}
