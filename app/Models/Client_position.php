<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client_position extends Model
{
    use HasFactory;

    public function Client()
    {
        return $this->belongsTo(Client::class);
    }

    public function Rate()
    {
        return $this->hasOne(ClientPositionRate::class)->where('type', 'rate')->orderBy('start_at', 'DESC');
    }

    public function Rates()
    {
        return $this->hasMany(ClientPositionRate::class);
    }
}
