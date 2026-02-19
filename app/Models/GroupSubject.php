<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GroupSubject extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'failed_subject_id', 'subject_id', 'group_id', 'application_id', 'subject_name', 'semester_code', 'credit'];
}
