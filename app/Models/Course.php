<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'specialty_id', 'language_id'];

    public function specialty(): hasOne
    {
        return $this->hasOne(Specialty::class, 'id', 'specialty_id');
    }
    public function level(): hasOne
    {
        return $this->hasOne(Level::class, 'id', 'level_id');
    }
}
