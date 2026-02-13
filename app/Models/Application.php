<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'uuid', 'student_id', 'o_app_id', 'year_id', 'status', 'created_at'
    ];
}
