<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Curriculum extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'name', 'department_id', 'specialty_id', 'edu_year_id'
    ];

    public function department(): HasOne
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }

    public function specialty(): HasOne
    {
        return $this->hasOne(Specialty::class, 'id', 'specialty_id');
    }

    public function edu_year(): HasOne
    {
        return $this->hasOne(EduYear::class, 'id', 'edu_year_id');
    }
}
