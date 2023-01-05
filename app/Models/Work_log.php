<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work_log extends Model
{
    use HasFactory;

    public function WorkLogDays()
    {
        return $this->hasMany(Work_log_day::class);
    }
}
