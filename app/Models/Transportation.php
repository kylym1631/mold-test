<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transportation extends Model
{
    use HasFactory;

    public function Driver()
    {
        return $this->belongsTo(User::class, 'driver_id')->where('group_id', 102);
    }

    public function ArrivalPlace()
    {
        return $this->belongsTo(Handbook::class, 'arrival_place_id')->where('handbook_category_id', 8);
    }

    public function CandidatesArrivals()
    {
        return $this->hasMany(Candidate_arrival::class)->where('active', 1);
    }
}
