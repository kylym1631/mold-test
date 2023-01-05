<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oswiadczenie extends Model
{
    use HasFactory;

    public function Files()
    {
        return $this->hasMany(C_file::class);
    }
}
