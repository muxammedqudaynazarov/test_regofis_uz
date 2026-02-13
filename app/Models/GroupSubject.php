<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GroupSubject extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'group_id', 'subject_id', 'semester_id', 'credit'];

    public function group(): hasOne
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }

    public function subject(): hasOne
    {
        return $this->hasOne(Subject::class, 'id', 'subject_id');
    }

    public function semester(): hasOne
    {
        return $this->hasOne(Semester::class, 'id', 'semester_id');
    }
}
