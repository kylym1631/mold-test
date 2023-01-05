<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Housing_client extends Model
{
    use HasFactory;

    public function Housing () {
        return $this->belongsTo(Housing::class);
    }

    public function Client () {
        return $this->belongsTo(Client::class);
    }
}
