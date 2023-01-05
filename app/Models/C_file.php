<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class C_file extends Model
{

    use HasFactory;

    private $doc_types = [
        '1' => 'Some Files for Car, Housing, WorkLogs, Oswiadczenie, User',
        '2' => 'Cars, Vacancies Documents',
        '3' => 'Candidate Passport/ID',
        '4' => 'Candidate Tickets',
        '5' => 'Finance',
        '6' => 'Arrival Tickets',
        '7' => 'Candidate Legalisation Documents',
        '103' => 'Candidate Karta',
        '104' => 'Candidate Driver License',
        '105' => 'Candidate Diplom',
        '106' => 'Candidate University',
        '107' => 'Candidate Other Document',
    ];

    //  UUID
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($course) {
            $course->{$course->getKeyName()} = (string) Str::uuid();
        });
    }
    public function getIncrementing()
    {
        return false;
    }
    public function getKeyType()
    {
        return 'string';
    }
    //  UUID
}
