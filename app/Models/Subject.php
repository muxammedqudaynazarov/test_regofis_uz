<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'failed_subject_id', 'subject_id'];

    public function tests(): HasMany
    {
        return $this->hasMany(Test::class, 'subject_id', 'id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(GroupSubject::class, 'subject_id', 'id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subject_teachers', 'subject_id', 'user_id');
    }
}
