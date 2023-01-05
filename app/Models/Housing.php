<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Housing extends Model
{
    use HasFactory;

    public function City()
    {
        return $this->belongsTo(Handbook::class, 'city')->where('handbook_category_id', 3);
    }

    public function Gallery()
    {
        return $this->hasMany(C_file::class);
    }

    public function Housing_contact()
    {
        return $this->hasMany(Housing_contact::class);
    }

    public function Housing_client()
    {
        return $this->hasMany(Housing_client::class);
    }
}
