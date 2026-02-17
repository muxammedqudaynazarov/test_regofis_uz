<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, HasPermissions;

    protected $fillable = ['id', 'name', 'current_role', 'hemis_roles', 'hemis_id', 'uuid', 'picture'];

    public function workplaces(): HasMany
    {
        return $this->hasMany(Workplace::class, 'user_id', 'id');
    }
}
