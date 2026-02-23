<?php

namespace App\Models;

use App\Traits\LogsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EduYear extends Model
{
    use HasFactory, LogsTrait;

    protected $fillable = ['id', 'name', 'year', 'status'];
}
