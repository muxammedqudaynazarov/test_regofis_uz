<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Specialty extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'code', 'uuid', 'department_id'];

    public function department(): HasOne
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }
}
