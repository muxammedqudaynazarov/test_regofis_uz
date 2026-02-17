<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = ['id', 'name', 'structure', 'access', 'status', 'parent_id'];

    public function children(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id', 'id');
    }

    public function curricula(): HasMany
    {
        return $this->hasMany(Curriculum::class, 'department_id', 'id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(SubjectList::class, 'department_id', 'id');
    }

    public function workplaces()
    {
        return $this->hasMany(Workplace::class, 'department_id', 'id');
    }
}
