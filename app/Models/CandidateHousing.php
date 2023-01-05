<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateHousing extends Model
{
    use HasFactory;

    public function Housing()
    {
        return $this->belongsTo(Housing::class);
    }

    public function Housing_room()
    {
        return $this->belongsTo(Housing_room::class);
    }

    public function Candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
