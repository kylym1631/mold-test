<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Lead extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'company',
        'name',
        'viber',
        'active',
        'status_comment',
        'status',
    ];

    public function Candidate()
    {
        return $this->belongsTo(Candidate::class)->where('removed', false);
    }

    public function Recruiter()
    {
        return $this->belongsTo(User::class, 'user_id')->where('group_id', 2);
    }

    public function Contacts()
    {
        return $this->hasMany(LeadContact::class);
    }

    public function Speciality()
    {
        return $this->belongsTo(Handbook::class, 'speciality_id')->where('handbook_category_id', 13);
    }

    public function FieldsMutations()
    {
        return $this->hasMany(FieldsMutation::class, 'model_obj_id')->where('model_name', 'Lead');
    }

    public static function getStatusesArr()
    {
        return array(
            '1' => 'Горячий',
            '2' => 'Не оставлял заявку',
            '3' => 'Не дозвон',
            '4' => 'Перезвонить',
            '5' => 'Брак номера',
            '6' => 'Не рекрутируем',
            '7' => 'Не заинтересован',
        );
    }

    public static function getStatusTitle($key)
    {
        $arr = array(
            '0' => 'Новый лид',
            '1' => 'Горячий', // ликвид
            '2' => 'Не оставлял заявку', // неликвид
            '3' => 'Не дозвон',
            '4' => 'Перезвонить',
            '5' => 'Брак номера',
            '6' => 'Не рекрутируем',
            '7' => 'Не заинтересован',
        );

        if ($key === null) {
            return $arr[0];
        }

        return isset($arr[$key]) ? $arr[$key] : $key;
    }

    public function getStatus()
    {
        if ($this->status == 1) {
            return 'Горячий';
        } else if ($this->status == 2) {
            return 'Не оставлял заявку';
        } else if ($this->status == 3) {
            return 'Не дозвон';
        } else if ($this->status == 4) {
            return 'Перезвонить';
        } else if ($this->status == 5) {
            return 'Брак номера';
        } else if ($this->status == 6) {
            return 'Не рекрутируем';
        } else if ($this->status == 7) {
            return 'Не заинтересован';
        } else {
            return 'Новый лид';
        }
    }
}
