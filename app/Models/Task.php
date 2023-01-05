<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'comment',
    ];

    public static $types = [
        '1' => 'Оформить кандидата',
        '2' => 'Связаться с кандидатом',
        '3' => 'Обработать лид (Фрилансер)', // from Freelancer
        '4' => 'Подтвердить вакансию и дату приезда',
        '6' => 'Указать первый рабочий день',
        '7' => 'Верифицировать фрилансера',
        '8' => 'Оплатить счет',
        '9' => 'Встретить кандидата и подписать договор',
        '10' => 'Перезвонить: ',
        '11' => 'Не дозвон',
        '12' => 'Произвольный Комментарий',
        '13' => 'Перезвонить кандидату',
        '14' => 'Подтвердить приезд за 9-7 дней',
        '15' => 'Подтвердить приезд за 5-3 дней',
        '16' => 'Подтвердить приезд за 1 дней',
        '17' => 'Прозвонить в день приезда',
        '18' => 'Обработать отказ',
        '19' => 'Перепланировать приезд',
        '21' => 'Обработать лид из таблицы лидов', // from Leads table
        '22' => 'Встретить и заселить',
        '23' => 'Перезвонить лиду с комментарием',
        '24' => 'Выслать документы в PUP',
        '25' => 'Выслать ZUS',
        '26' => 'Подтвердить легальный срок пребывания',
        '100' => 'Кастомная задача (Шаблон задач)', // custom task
    ];

    public function Autor()
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function Freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function Candidate()
    {
        return $this->belongsTo(Candidate::class)->where('removed', false);
    }

    public function Candidate_arrival()
    {
        return $this->hasOne(Candidate_arrival::class, 'candidate_id', 'candidate_id')
            ->where('active', true);
    }

    public function Lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function Client()
    {
        return $this->belongsTo(Client::class, 'model_obj_id');
    }

    public function Housing()
    {
        return $this->belongsTo(Housing::class, 'model_obj_id');
    }

    public function Car()
    {
        return $this->belongsTo(Car::class, 'model_obj_id');
    }

    public function Vacancy()
    {
        return $this->belongsTo(Vacancy::class, 'model_obj_id');
    }

    public function getStatus()
    {
        if ($this->status == 1) {
            return 'К выполнению';
        } else if ($this->status == 2) {
            return 'Выполнено';
        } else if ($this->status == 3) {
            return 'Просрочено';
        } else if ($this->status == 4) {
            return 'Отклонена';
        }
    }

    public static function getTypeTitle($key)
    {
        $arr = array(
            '1' => 'Оформить кандидата',
            '2' => 'Связаться с кандидатом',
            '3' => 'Обработать лид', // from Freelancer
            '4' => 'Подтвердить вакансию и дату приезда',
            '6' => 'Указать первый рабочий день',
            '7' => 'Верифицировать фрилансера',
            '8' => 'Оплатить счет',
            '9' => 'Встретить кандидата и подписать договор',
            '10' => 'Перезвонить: ',
            '11' => 'Не дозвон',
            '12' => '#comment#',
            '13' => 'Перезвонить кандидату',
            '14' => 'Подтвердить приезд за 9-7 дней',
            '15' => 'Подтвердить приезд за 5-3 дней',
            '16' => 'Подтвердить приезд за 1 дней',
            '17' => 'Прозвонить в день приезда',
            '18' => 'Обработать отказ',
            '19' => 'Перепланировать приезд',
            '21' => 'Обработать лид', // from Leads table
            '22' => 'Встретить и заселить',
            '23' => 'Перезвонить лиду: ',
            '24' => 'Выслать документы в PUP',
            '25' => 'Выслать ZUS',
            '26' => 'Подтвердить легальный срок пребывания',
            '100' => '#title#', // custom task
        );

        return isset($arr[$key]) ? $arr[$key] : $key;
    }

    public function getAction($model_obj_status)
    {
        if ($this->type == 26) {
            return [
                'action' => 'setLegalise',
                'options' => [
                    '1' => 'Легален',
                    '0' => 'Нелегален',
                ],
            ];

        } else if ($this->type == 24 || $this->type == 25) {
            return [
                'action' => 'setTaskStatus',
                'options' => [
                    '2' => 'Выполнено',
                ],
            ];

        } else if ($this->type == 21 || $this->type == 23) {
            $add_actions = [];

            if (Auth::user()->isRecruiter()) {
                $add_actions = ['createCandidate'];
            }

            return array(
                'action' => 'setLeadStatus',
                'options' => Lead::getStatusesArr(),
                'add_actions' => $add_actions,
            );
        } else {
            $add_actions = array();

            if (
                Auth::user()->isAdmin()
                || Auth::user()->isLogist()
                || Auth::user()->isRecruitmentDirector()
            ) {
                if ($model_obj_status == 4 || $model_obj_status == 6 || $this->type == 19) {
                    $add_actions = array('createArrival');
                }
            }

            $statuses_arr = Candidate::getStatusesArr($model_obj_status);

            // if ($this->type == 17) {
            //     $statuses_arr = ['19' => 'В пути'];
            // }

            return array(
                'action' => 'setCandidateStatus',
                'options' => $statuses_arr,
                'add_actions' => $add_actions,
            );
        }

        return array();
    }

    public function getCustomAction()
    {
        return [
            'action' => 'setTaskStatus',
            'options' => [
                '2' => 'Выполнено',
                '4' => 'Отказаться',
            ],
        ];
    }
}
