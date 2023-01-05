<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidatePosition extends Model
{
    use HasFactory;

    public function Position()
    {
        return $this->belongsTo(Client_position::class, 'client_position_id');
    }

    public function Rates()
    {
        return $this->hasMany(ClientPositionRate::class, 'client_position_id', 'client_position_id');
    }
}
