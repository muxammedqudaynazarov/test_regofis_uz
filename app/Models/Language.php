<?php

namespace App\Models;

use App\Traits\LogsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory, LogsTrait;

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name', 'status'];
}
