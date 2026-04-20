<?php

namespace App\Models;

use App\Traits\LogsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory, LogsTrait;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = ['id', 'name'];
}
