<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use HasFactory;

    protected $table = 'students';
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'id', 'name', 'picture', 'curriculum_id', 'specialty_id', 'language_id'
    ];

    public function specialty(): HasOne
    {
        return $this->hasOne(Specialty::class, 'id', 'specialty_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'student_id', 'id');
    }
}
