<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workplace extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'department_id', 'head_type', 'is_main',
    ];
}
