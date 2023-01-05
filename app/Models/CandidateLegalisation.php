<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateLegalisation extends Model
{
    use HasFactory;

    public function Files()
    {
        return $this->hasMany(C_file::class);
    }

    public function Type()
    {
        return $this->belongsTo(Handbook::class, 'doc_type_id')->where('handbook_category_id', 6);
    }
}
