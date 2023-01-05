<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public function Permissions()
    {
        return $this->hasMany(RolePermission::class);
    }

    public function Users()
    {
        return $this->hasMany(User::class, 'group_id');
    }
}
