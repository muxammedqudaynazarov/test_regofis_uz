<?php

namespace App\Models;

use App\Traits\LogsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory, LogsTrait;

    public $incrementing = false;
    protected $fillable = ['id', 'name', 'status'];
}
