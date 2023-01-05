<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Candidate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lastName',
        'firstName',
        'phone',
        'viber',
        'dateOfBirth',
        'recruiter_id',
        'client_id',
        'active',
    ];

    public function Vacancy()
    {
        return $this->belongsTo(Vacancy::class, 'real_vacancy_id');
    }

    public function D_file()
    {
        return $this->hasOne(C_file::class)->where('type', 3);
    }

    public function D_file_karta()
    {
        return $this->hasOne(C_file::class)->where('type', 103);
    }
    public function D_file_driver()
    {
        return $this->hasOne(C_file::class)->where('type', 104);
    }
    public function D_file_diplom()
    {
        return $this->hasOne(C_file::class)->where('type', 105);
    }
    public function D_file_legitim()
    {
        return $this->hasOne(C_file::class)->where('type', 106);
    }
    public function D_file_else()
    {
        return $this->hasOne(C_file::class)->where('type', 107);
    }

    public function getPasportLink()
    {
        if ($this->D_file != null) {
            if (config('app.env') === 'local') {
                $path_url = url('/');
            } else {
                $path_url = url('/') . '/public';
            }
            return $path_url . $this->D_file->path;
        } else {
            return '';
        }
    }

    public function getKartapobytu()
    {
        if ($this->D_file_karta != null) {
            if (config('app.env') === 'local') {
                $path_url = url('/');
            } else {
                $path_url = url('/') . '/public';
            }
            return $path_url . $this->D_file_karta->path;
        } else {
            return '';
        }
    }

    public function getDriverLicense()
    {
        if ($this->D_file_driver != null) {
            if (config('app.env') === 'local') {
                $path_url = url('/');
            } else {
                $path_url = url('/') . '/public';
            }
            return $path_url . $this->D_file_driver->path;
        } else {
            return '';
        }
    }

    public function getDiplom()
    {
        if ($this->D_file_diplom != null) {
            if (config('app.env') === 'local') {
                $path_url = url('/');
            } else {
                $path_url = url('/') . '/public';
            }
            return $path_url . $this->D_file_diplom->path;
        } else {
            return '';
        }
    }

    public function getLegitim()
    {
        if ($this->D_file_legitim != null) {
            if (config('app.env') === 'local') {
                $path_url = url('/');
            } else {
                $path_url = url('/') . '/public';
            }
            return $path_url . $this->D_file_legitim->path;
        } else {
            return '';
        }
    }

    public function getElsefile()
    {
        if ($this->D_file_else != null) {
            if (config('app.env') === 'local') {
                $path_url = url('/');
            } else {
                $path_url = url('/') . '/public';
            }
            return $path_url . $this->D_file_else->path;
        } else {
            return '';
        }
    }

    public function Citizenship()
    {
        return $this->belongsTo(Handbook::class)->where('handbook_category_id', 10);
    }

    // public function Nacionality()
    // {
    //     return $this->belongsTo(Handbook::class)->where('handbook_category_id', 2);
    // }

    public function Speciality()
    {
        return $this->belongsTo(Handbook::class)->where('handbook_category_id', 13);
    }

    public function Country()
    {
        return $this->belongsTo(Handbook::class)->where('handbook_category_id', 5);
    }

    public function Type_doc()
    {
        return $this->belongsTo(Handbook::class)->where('handbook_category_id', 6);
    }

    public function Logist_place_arrive()
    {
        return $this->belongsTo(Handbook::class)->where('handbook_category_id', 8);
    }

    public function Real_status_work()
    {
        return $this->belongsTo(Handbook::class)->where('handbook_category_id', 9);
    }

    public function Candidate_arrival()
    {
        return $this->hasMany(Candidate_arrival::class);
    }

    public function Client()
    {
        return $this->belongsTo(Client::class);
    }

    public function Client_position()
    {
        return $this->belongsTo(Client_position::class);
    }

    public function Housing()
    {
        return $this->belongsTo(Housing::class);
    }

    public function Housing_room()
    {
        return $this->belongsTo(Housing_room::class);
    }

    public function Transport()
    {
        return $this->belongsTo(Handbook::class)->where('handbook_category_id', 7);
    }

    public function Recruiter()
    {
        if (Auth::user()->isRecruitmentDirector()) {
            return $this->belongsTo(User::class)->where('group_id', 2)->where('user_id', Auth::user()->id);
        } else {
            return $this->belongsTo(User::class)->where('group_id', 2);
        }
    }

    public function ActiveHistory()
    {
        return $this->hasMany(FieldsMutation::class, 'model_obj_id')->where('model_name', 'Candidate')->where('field_name', 'active');
    }

    public function WorkLog()
    {
        return $this->hasOne(Work_log::class);
    }

    public function WorkLogAdditions()
    {
        return $this->hasMany(Work_log_addition::class);
    }

    public function ClientPosition()
    {
        return $this->belongsTo(Client_position::class, 'client_position_id');
    }

    public function Positions()
    {
        return $this->hasMany(CandidatePosition::class);
    }

    public function PositionsAll()
    {
        return $this->hasMany(CandidatePosition::class);
    }

    public function HousingPeriods()
    {
        return $this->hasMany(CandidateHousing::class);
    }

    public function Oswiadczenies()
    {
        return $this->hasMany(Oswiadczenie::class);
    }

    public function Legalisation()
    {
        return $this->hasOne(CandidateLegalisation::class)->orderBy('date_to', 'DESC');
    }

    public static function getStatuses()
    {
        return array(
            //Рекрутер
            '2' => 'Лид',
            '1' => 'Новый кандидат',
            '14' => 'Перезвонить',
            '4' => 'Оформлен',
            //Логист
            '6' => 'Подтвердил Выезд',
            '19' => 'В пути',
            '21' => 'Перезвонить',
            //Трудоустройство           
            '12' => 'Приехал',
            '10' => 'Проверка легализации',
            '8' => 'Трудоустроен',
            '20' => 'Не доехал',
            '22' => 'Не рекрутируем',
            //Координатор                             
            '7' => 'Заселен',
            '9' => 'Приступил к Работе',
            '11' => 'Уволен',
            //Общие
            '5' => 'Архив',
            '3' => 'Отказ',
            // Не актуалные
            // '13' => 'Архив (отказ)',
            // '17' => 'Жду документа',   
            // '16' => 'Оформление',
            // '18' => 'Не говорит по русски', 
            // '15' => 'Недозвон',
        );
    }

    public static function getStatusesArr($active)
    {
        $arr = array();

        if (Auth::user()->isRecruiter()) {
            $arr = array(14, 4, 5);
        }

        if (Auth::user()->isFreelancer()) {
            $arr = array(1, 2, 3, 4, 5);
        }

        if (Auth::user()->isLogist()) {
            $arr = array(6, 3, 21);

            if (in_array($active, [6])) {
                $arr = array(19, 3, 21);
            } elseif (in_array($active, [19])) {
                $arr = null;
            }
        }

        if (Auth::user()->isCoordinator() || Auth::user()->group_id == 104) {
            $arr = array(7, 9, 11, 3);

            if (in_array($active, [8])) {
                $arr = array(7, 11, 3);
            } elseif (in_array($active, [7])) {
                $arr = array(9, 11, 3);
            }
        }

        if (Auth::user()->isTrud()) {
            $arr = array(12, 20);

            if (in_array($active, [19])) {
                $arr = array(12, 20);
            } elseif (in_array($active, [12])) {
                $arr = array(3, 10, 22);
            } elseif (in_array($active, [10, 20, 3, 8, 22])) {
                $arr = array(3, 8, 22);
            }
        }

        if ($arr) {
            $n_arr = array();

            foreach ($arr as $k) {
                $n_arr[$k] = self::getStatusTitle($k);
            }

            return $n_arr;
        } elseif ($arr === null) {
            return [];
        }

        return self::getStatuses();
    }

    public static function getStatusTitle($key)
    {
        $arr = self::getStatuses();
        return isset($arr[$key]) ? $arr[$key] : $key;
    }

    public function getCurrentStatus()
    {
        return self::getStatusTitle($this->active);
    }

    public function getStatusOptions()
    {
        $html = '';
        $isset = false;
        foreach (self::getStatusesArr($this->active) as $k => $a) {
            if ($k == $this->active) {
                $html .= '<option selected value="' . $k . '">' . $a . '</option>';
                $isset = true;
            } else {
                $html .= '<option value="' . $k . '">' . $a . '</option>';
            }
        }

        if (!$isset) {
            $html = '<option disabled selected>' . $this->getCurrentStatus() . '</option>' . $html;
        }

        return $html;
    }

    public static function allowedStatusesToView()
    {
        if (Auth::user()->isAdmin()) {
            $statuses = [];

            foreach (self::getStatuses() as $st => $name) {
                if (!in_array($st, [7, 8, 9])) {
                    $statuses[] = $st;
                }
            }

            return $statuses;
        } else 
        if (Auth::user()->hasPermission('candidate.view')) {
            return Auth::user()->getChildPermissions('candidate.view.status.');
        }

        return [];
    }

    public static function allowedStatusesToEmployeeView()
    {
        if (Auth::user()->isAdmin()) {
            return [7, 8, 9];
        } else 
        if (Auth::user()->hasPermission('employee.view')) {
            return Auth::user()->getChildPermissions('employee.view.status.');
        }

        return [];
    }

    public static function allowedStatusesToViewAll()
    {
        if (Auth::user()->isAdmin()) {
            return array_keys(self::getStatuses());
        }

        $statuses = [];

        if (Auth::user()->hasPermission('candidate.view')) {
            $statuses = array_merge($statuses, Auth::user()->getChildPermissions('candidate.view.status.'));
        }

        if (Auth::user()->hasPermission('employee.view')) {
            $statuses = array_merge($statuses, Auth::user()->getChildPermissions('employee.view.status.'));
        }

        return $statuses;
    }

    public function scopeAllowedWithStatus($query)
    {
        $query->whereIn('active', self::allowedStatusesToView())->where('removed', false);
    }

    public function scopeAllowedWithEmployeeStatus($query)
    {
        $query->whereIn('active', self::allowedStatusesToEmployeeView())->where('removed', false);
    }

    public function scopeAllowedWithAllStatuses($query)
    {
        $query->whereIn('active', self::allowedStatusesToViewAll())->where('removed', false);
    }

    public function scopeAllowedWithAccountantStatuses($query)
    {
        $query->whereIn('active', [9, 11])->where('removed', false);
    }
}
