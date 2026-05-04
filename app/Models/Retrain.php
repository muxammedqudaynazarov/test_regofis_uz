<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Retrain extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'name',
    ];

    public function applications(): hasMany
    {
        return $this->hasMany(Application::class);
    }

    public function exams(): hasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function results(): hasMany
    {
        return $this->hasMany(Result::class);
    }
}
