<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    public function Gallery()
    {
        return $this->hasMany(C_file::class);
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
