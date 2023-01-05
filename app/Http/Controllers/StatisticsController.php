<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Lead;
use Carbon\Carbon;
use App\Exports\EmploymentStatisticsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\StatisticsService;

class StatisticsController extends Controller
{
    public function getIndex()
    {

        // $filters = array(
        //     'recruiters' => array(),
        // );

        // $candidates = Candidate::whereNotNull('recruiter_id')->with('Recruiter')->get();

        // foreach ($candidates as $c) {
        //     if ($c->Recruiter != null) {
        //         $filters['recruiters'][$c->Recruiter->id] = $c->Recruiter->firstName .' '. $c->Recruiter->lastName;
        //     }
        // }

        return view('statistics.index')
            ->with('empl_table', $this->employment()['table']);
    }

    public function getTasksJson(Request $req)
    {
        $draw = $req->draw;
        $start = $req->start;
        $rowperpage = $req->length;
        $period = $req->period;

        $users = User::select('id', 'firstName', 'lastName', 'group_id');

        if (Auth::user()->isRecruitmentDirector()) {
            $users = $users->where('user_id', Auth::user()->id);
        }

        if ($req->group_id) {
            $users = $users->whereIn('group_id', $req->group_id);
        } else {
            $users = $users->whereIn('group_id', [2, 4, 8]);
        }

        $users = $users->with([
            'Tasks' => function ($query) use ($period) {
                $query = $query->select('to_user_id', 'status', 'start');

                if ($period) {
                    return $query
                        ->whereDate('start', '>=', Carbon::createFromFormat('Y-m-d', $period['from']))
                        ->whereDate('start', '<=', Carbon::createFromFormat('Y-m-d', $period['to']));
                } else {
                    return $query->whereDate('start', '<=', Carbon::now());
                }
            }
        ])->get();

        $result = array();

        foreach ($users as $user) {
            $total = 0;
            $todoCount = 0;
            $overdueCount = 0;
            $performedCount = 0;

            if ($user->Tasks) {
                $total = count($user->Tasks);

                foreach ($user->Tasks as $task) {
                    if ($task->status == 1) {
                        $todoCount++;
                    } elseif ($task->status == 2) {
                        $performedCount++;
                    } elseif ($task->status == 3) {
                        $overdueCount++;
                    }
                }
            }

            $result[] = array(
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'todoCount' => $todoCount,
                'performedCount' => $performedCount,
                'overdueCount' => $overdueCount,
                'groupName' => $user->getGroup(),
                'barWidth' => $total ? round(($performedCount / $total) * 100) : 0,
            );
        }

        $order = request('order');
        $columns = request('columns');
        $order_columns = [];

        if ($order) {
            foreach ($order as $o) {
                $name = $columns[$o['column']]['name'];
                if ($name) {
                    $order_columns[] = [
                        'name' => $name,
                        'dir' => $o['dir'],
                    ];
                }
            }
        }

        if ($order_columns) {
            foreach ($order_columns as $col) {
                usort($result, function ($a, $b) use ($col) {
                    if ($a[$col['name']] == $b[$col['name']]) {
                        return 0;
                    }

                    if ($col['dir'] == 'asc') {
                        return ($a[$col['name']] < $b[$col['name']]) ? -1 : 1;
                    } else if ($col['dir'] == 'desc') {
                        return ($a[$col['name']] > $b[$col['name']]) ? -1 : 1;
                    }
                });
            }
        }

        return response(array(
            'data' => $result,
            'draw' => $draw,
            'recordsTotal' => count($result),
            'recordsFiltered' => count($result),
        ), 200);
    }

    private function employment()
    {
        $table = [
            'total' => 'Всего',
            //in work
            // '2' => 'Лид',            
            // '1' => 'Новый кандидат',
            // '14' => 'Перезвонить',
            'in_work' => 'Новый кандидат',
            //oforml
            '4' => 'Оформлен',
            '3' => 'Отказ',
            'c_3' => 'Конверсия отказы',
            //logist
            '6' => 'Подтвердил Выезд',
            // '21' => 'Перезвонить',
            'logist_3' => 'Отказ',
            '19' => 'В пути',
            //trud           
            '12' => 'Приехал',
            'c_12' => 'Конверсия приехавших',
            '20' => 'Не доехал',
            'trud_3' => 'Отказ',
            '22' => 'Не рекрутируем',
            '8' => 'Трудоустроен',
            //Координатор                             
            '7' => 'Заселен',
            '9' => 'Приступил к Работе',
            'worked' => 'Отработал 7 дней',
            'c_worked' => 'Конверсия отработал<br> 7 дней',
            '11' => 'Уволен',
            //Общие
            '5' => 'Архив',
        ];

        return [
            'table' => $table,
        ];
    }

    public function getEmploymentJson(Request $req)
    {
        $draw = $req->draw;
        $start = $req->start;
        $rowperpage = $req->length;
        $period = $req->period;
        $period_oform = $req->period_oform;
        $period_created = $req->period_created;
        $is_filter = $req->is_filter;

        $users = User::select('id', 'firstName', 'lastName', 'group_id');

        if (Auth::user()->isRecruitmentDirector()) {
            $users = $users->where('user_id', Auth::user()->id);
        }

        if (Auth::user()->isRecruiter()) {
            $users = $users->where('id', Auth::user()->id);
        }

        $users = $users->where('group_id', 2);

        if ($req->filter__activation) {
            $users = $users->where('activation', $req->filter__activation);
        }

        $users = $users->with([
            'RecruiterCandidates' => function ($query) use ($period, $is_filter, $period_created) {
                $query = $query->select('id', 'active_update', 'active', 'worked', 'recruiter_id', 'client_id', 'removed');

                if (Auth::user()->isKoordinator()) {
                    $query = $query->whereHas('Client', function ($q) {
                        $q->where('coordinator_id', Auth::user()->id);
                    });
                }

                if ($period_created) {
                    $query = $query
                        ->whereDate('created_at', '>=', Carbon::createFromFormat('Y-m-d', $period_created['from']))
                        ->whereDate('created_at', '<=', Carbon::createFromFormat('Y-m-d', $period_created['to']));
                }

                if ($is_filter && $period) {
                    $query = $query
                        ->whereDate('active_update', '>=', Carbon::createFromFormat('Y-m-d', $period['from']))
                        ->whereDate('active_update', '<=', Carbon::createFromFormat('Y-m-d', $period['to']));
                }

                $query->orderBy('id', 'desc');
            },
            'RecruiterCandidates.ActiveHistory' => function ($query) use ($period, $period_oform) {
                $query = $query->select('id', 'model_name', 'model_obj_id', 'current_value', 'created_at', 'user_role');

                if ($period_oform) {
                    $query = $query
                        ->where('current_value', '4')
                        ->whereDate('created_at', '>=', Carbon::createFromFormat('Y-m-d', $period_oform['from']))
                        ->whereDate('created_at', '<=', Carbon::createFromFormat('Y-m-d', $period_oform['to']));
                }

                if ($period) {
                    $query = $query
                        ->whereDate('created_at', '>=', Carbon::createFromFormat('Y-m-d', $period['from']))
                        ->whereDate('created_at', '<=', Carbon::createFromFormat('Y-m-d', $period['to']));
                }

                $query->orderBy('id', 'desc');
            }
        ])->get();

        $result = [];
        $result_sum = [
            'firstName' => 'Всего',
            'lastName' => '',
            'groupName' => '',
            'table' => [],
        ];

        foreach ($this->employment()['table'] as $key => $value) {
            $result_sum['table'][$key] = 0;
        }

        foreach ($users as $user) {
            $table = [];

            foreach ($this->employment()['table'] as $key => $value) {
                $table[$key] = 0;
            }

            if ($user->RecruiterCandidates) {
                foreach ($user->RecruiterCandidates as $Candidate) {

                    $is_worked = false;

                    $is_11 = false;
                    $is_9 = false;
                    $is_7 = false;
                    $is_8 = false;
                    $is_22 = false;
                    $is_12 = false;
                    $is_20 = false;
                    $is_19 = false;
                    $is_6 = false;
                    $is_21 = false;
                    $is_4 = false;
                    $is_3 = false;
                    $is_trud_3 = false;
                    $is_logist_3 = false;
                    $is_5 = false;
                    $is_in_work = false;

                    $is_also_11 = false;
                    $is_also_9 = false;
                    $is_also_7 = false;
                    $is_also_8 = false;
                    $is_also_22 = false;
                    $is_also_12 = false;
                    $is_also_20 = false;
                    $is_also_19 = false;
                    $is_also_6 = false;
                    $is_also_21 = false;
                    $is_also_4 = false;
                    $is_also_trud_3 = false;
                    $is_also_logist_3 = false;
                    $is_also_3 = false;
                    $is_also_5 = false;

                    if ($Candidate->ActiveHistory && count($Candidate->ActiveHistory) > 0) {
                        $table['total']++;

                        if ($Candidate->worked) {
                            $is_worked = true;
                        }

                        $i = 0;
                        foreach ($Candidate->ActiveHistory as $item) {
                            if ($i == 0) {
                                if ($item->current_value == '11') {
                                    $is_11 = true;
                                }

                                if ($item->current_value == '9') {
                                    $is_9 = true;
                                }

                                if ($item->current_value == '7') {
                                    $is_7 = true;
                                }

                                if ($item->current_value == '8') {
                                    $is_8 = true;
                                }

                                if ($item->current_value == '22') {
                                    $is_22 = true;
                                }

                                if ($item->current_value == '12') {
                                    $is_12 = true;
                                }

                                if ($item->current_value == '20') {
                                    $is_20 = true;
                                }

                                if ($item->current_value == '19') {
                                    $is_19 = true;
                                }

                                if ($item->current_value == '6') {
                                    $is_6 = true;
                                }

                                if ($item->current_value == '21') {
                                    $is_21 = true;
                                }

                                if ($item->current_value == '4') {
                                    $is_4 = true;
                                }

                                if (
                                    $item->current_value == '1'
                                    || $item->current_value == '2'
                                    || $item->current_value == '14'
                                ) {
                                    $is_in_work = true;
                                }

                                if ($item->current_value == '5') {
                                    $is_5 = true;
                                }

                                if ($item->current_value == '3') {
                                    if ($item->user_role == 5) {
                                        $is_trud_3 = true;
                                    } elseif ($item->user_role == 4) {
                                        $is_logist_3 = true;
                                    } else {
                                        $is_3 = true;
                                    }
                                }
                            } else {
                                if ($item->current_value == '11') {
                                    $is_also_11 = true;
                                }

                                if ($item->current_value == '9') {
                                    $is_also_9 = true;
                                }

                                if ($item->current_value == '7') {
                                    $is_also_7 = true;
                                }

                                if ($item->current_value == '8') {
                                    $is_also_8 = true;
                                }

                                if ($item->current_value == '22') {
                                    $is_also_22 = true;
                                }

                                if ($item->current_value == '12') {
                                    $is_also_12 = true;
                                }

                                if ($item->current_value == '20') {
                                    $is_also_20 = true;
                                }

                                if ($item->current_value == '19') {
                                    $is_also_19 = true;
                                }

                                if ($item->current_value == '6') {
                                    $is_also_6 = true;
                                }

                                if ($item->current_value == '21') {
                                    $is_also_21 = true;
                                }

                                if ($item->current_value == '4') {
                                    $is_also_4 = true;
                                }

                                if ($item->current_value == '3') {
                                    if ($item->user_role == 5) {
                                        $is_also_trud_3 = true;
                                    } elseif ($item->user_role == 4) {
                                        $is_also_logist_3 = true;
                                    } else {
                                        $is_also_3 = true;
                                    }
                                }

                                if ($item->current_value == '5') {
                                    $is_also_5 = false;
                                }
                            }

                            $i++;
                        }
                    }

                    if ($is_5) {
                        $table['5']++;

                        if ($is_also_trud_3) {
                            $table['trud_3']++;
                        } elseif ($is_also_logist_3) {
                            $table['logist_3']++;
                        } elseif ($is_also_3) {
                            $table['3']++;
                        }

                        if ($is_also_11) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            $table['9']++;
                            $table['11']++;

                            if ($is_worked) {
                                $table['worked']++;
                            }

                            continue;
                        }

                        if ($is_worked) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            $table['9']++;
                            $table['worked']++;
                            continue;
                        }

                        if ($is_also_9) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            $table['9']++;
                            continue;
                        }

                        if ($is_also_7) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            continue;
                        }

                        if ($is_also_8) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            continue;
                        }

                        if ($is_also_22) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['22']++;
                            continue;
                        }

                        if ($is_also_12) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            continue;
                        }

                        if ($is_also_20) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['20']++;
                            continue;
                        }

                        if ($is_also_19) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            continue;
                        }

                        if ($is_also_6) {
                            $table['4']++;
                            $table['6']++;
                            continue;
                        }

                        if ($is_also_21) {
                            // $table['21']++;
                            $table['4']++;
                            if ($is_also_6) {
                                $table['6']++;
                            }
                            continue;
                        }

                        if ($is_also_4) {
                            $table['4']++;
                            continue;
                        }

                        continue;
                    }

                    if ($is_trud_3 || $is_logist_3 || $is_3) {

                        if ($is_trud_3) {
                            $table['trud_3']++;
                        } elseif ($is_logist_3) {
                            $table['logist_3']++;
                        } elseif ($is_3) {
                            $table['3']++;
                        }

                        if ($is_also_11) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            $table['9']++;
                            $table['11']++;

                            if ($is_worked) {
                                $table['worked']++;
                            }

                            continue;
                        }

                        if ($is_worked) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            $table['9']++;
                            $table['worked']++;
                            continue;
                        }

                        if ($is_also_9) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            $table['9']++;
                            continue;
                        }

                        if ($is_also_7) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            continue;
                        }

                        if ($is_also_8) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            continue;
                        }

                        if ($is_also_22) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['22']++;
                            continue;
                        }

                        if ($is_also_12) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            continue;
                        }

                        if ($is_also_20) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['20']++;
                            continue;
                        }

                        if ($is_also_19) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            continue;
                        }

                        if ($is_also_6) {
                            $table['4']++;
                            $table['6']++;
                            continue;
                        }

                        if ($is_also_21) {
                            // $table['21']++;
                            $table['4']++;
                            if ($is_also_6) {
                                $table['6']++;
                            }
                            continue;
                        }

                        if ($is_also_4) {
                            $table['4']++;
                            continue;
                        }

                        continue;
                    }

                    if ($is_11) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        $table['11']++;

                        if ($is_worked) {
                            $table['worked']++;
                        }

                        continue;
                    }

                    if ($is_worked) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        $table['worked']++;
                        continue;
                    }

                    if ($is_9) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        $table['9']++;
                        continue;
                    }

                    if ($is_7) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        $table['7']++;
                        continue;
                    }

                    if ($is_8) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['8']++;
                        continue;
                    }

                    if ($is_22) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        $table['22']++;
                        continue;
                    }

                    if ($is_12) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['12']++;
                        continue;
                    }

                    if ($is_20) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;
                        $table['20']++;
                        continue;
                    }

                    if ($is_19) {
                        $table['4']++;
                        $table['6']++;
                        $table['19']++;

                        if ($is_also_12) {
                            $table['12']++;
                            continue;
                        }

                        if ($is_also_20) {
                            $table['20']++;
                            continue;
                        }

                        continue;
                    }

                    if ($is_6) {
                        $table['4']++;
                        $table['6']++;
                        continue;
                    }

                    if ($is_21) {
                        if ($is_also_12) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            continue;
                        }

                        if ($is_also_20) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['20']++;
                            continue;
                        }

                        if ($is_also_19) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            continue;
                        }

                        if ($is_also_6) {
                            $table['4']++;
                            $table['6']++;
                            continue;
                        }

                        continue;
                    }

                    if ($is_4) {
                        $table['4']++;

                        if ($is_also_5) {
                            $table['5']++;
                        }

                        if ($is_also_11) {
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            $table['9']++;
                            $table['11']++;

                            if ($is_worked) {
                                $table['worked']++;
                            }

                            continue;
                        }

                        if ($is_worked) {
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            $table['9']++;
                            $table['worked']++;
                            continue;
                        }

                        if ($is_also_9) {
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            $table['9']++;
                            continue;
                        }

                        if ($is_also_7) {
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            continue;
                        }

                        if ($is_also_8) {
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            continue;
                        }

                        if ($is_also_22) {
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['22']++;
                            continue;
                        }

                        if ($is_also_12) {
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            continue;
                        }

                        if ($is_also_20) {
                            $table['6']++;
                            $table['19']++;
                            $table['20']++;
                            continue;
                        }

                        if ($is_also_19) {
                            $table['6']++;
                            $table['19']++;
                            continue;
                        }

                        if ($is_also_6) {
                            $table['6']++;
                            continue;
                        }

                        if ($is_also_21) {
                            if ($is_also_6) {
                                $table['6']++;
                            }
                            continue;
                        }

                        continue;
                    }

                    if ($is_in_work) {
                        $table['in_work']++;

                        if ($is_also_5) {
                            $table['5']++;
                        }

                        if ($is_also_trud_3) {
                            $table['trud_3']++;
                        } elseif ($is_also_logist_3) {
                            $table['logist_3']++;
                        } elseif ($is_also_3) {
                            $table['3']++;
                        }

                        if ($is_also_11) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            $table['9']++;
                            $table['11']++;

                            if ($is_worked) {
                                $table['worked']++;
                            }

                            continue;
                        }

                        if ($is_worked) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            $table['9']++;
                            $table['worked']++;
                            continue;
                        }

                        if ($is_also_9) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            $table['9']++;
                            continue;
                        }

                        if ($is_also_7) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            $table['7']++;
                            continue;
                        }

                        if ($is_also_8) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['8']++;
                            continue;
                        }

                        if ($is_also_22) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            $table['22']++;
                            continue;
                        }

                        if ($is_also_12) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['12']++;
                            continue;
                        }

                        if ($is_also_20) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            $table['20']++;
                            continue;
                        }

                        if ($is_also_19) {
                            $table['4']++;
                            $table['6']++;
                            $table['19']++;
                            continue;
                        }

                        if ($is_also_6) {
                            $table['4']++;
                            $table['6']++;
                            continue;
                        }

                        if ($is_also_21) {
                            $table['4']++;
                            if ($is_also_6) {
                                $table['6']++;
                            }
                            continue;
                        }

                        if ($is_also_4) {
                            $table['4']++;
                            continue;
                        }

                        continue;
                    }
                }
            }

            $table['c_3'] = $table['total'] ? round(($table['3'] / $table['total']) * 100) : 0;
            $table['c_12'] = $table['6'] ? round(($table['12'] / $table['6']) * 100) : 0;
            $table['c_worked'] = $table['8'] ? round(($table['worked'] / $table['8']) * 100) : 0;

            $result[] = array(
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'groupName' => $user->getGroup(),
                'table' => $table,
            );

            foreach ($table as $key => $value) {
                $result_sum['table'][$key] += $value;
            }

            $result_sum['table']['c_3'] = $result_sum['table']['total'] ? round(($result_sum['table']['3'] / $result_sum['table']['total']) * 100) : 0;

            $result_sum['table']['c_12'] = $result_sum['table']['6'] ? round(($result_sum['table']['12'] / $result_sum['table']['6']) * 100) : 0;

            $result_sum['table']['c_worked'] = $result_sum['table']['8'] ? round(($result_sum['table']['worked'] / $result_sum['table']['8']) * 100) : 0;
        }

        $order = request('order');
        $columns = request('columns');
        $order_columns = [];

        if ($order) {
            foreach ($order as $o) {
                $name = $columns[$o['column']]['name'];
                if ($name) {
                    $order_columns[] = [
                        'name' => $name,
                        'dir' => $o['dir'],
                    ];
                }
            }
        }

        if ($order_columns) {
            foreach ($order_columns as $col) {
                usort($result, function ($a, $b) use ($col) {
                    if ($a['table'][$col['name']] == $b['table'][$col['name']]) {
                        return 0;
                    }

                    if ($col['dir'] == 'asc') {
                        return ($a['table'][$col['name']] < $b['table'][$col['name']]) ? -1 : 1;
                    } else if ($col['dir'] == 'desc') {
                        return ($a['table'][$col['name']] > $b['table'][$col['name']]) ? -1 : 1;
                    }
                });
            }
        }

        return response(array(
            'data' => $result,
            'sum_data' => $result_sum,
            'draw' => $draw,
            'recordsTotal' => count($result) + 1,
            'recordsFiltered' => count($result) + 1,
        ), 200);
    }

    public function exportEmploymentExcel(Request $req)
    {
        $stat = $this->getEmploymentJson($req);

        $export = new EmploymentStatisticsExport([
            'table' => $this->employment()['table'],
            'sum_data' => $stat->original['sum_data'],
            'data' => $stat->original['data'],
        ]);

        return Excel::download($export, 'Employment_Statistics.xlsx');
    }

    // public function getLeadsJson(Request $req)
    // {
    //     $draw = $req->draw;
    //     $start = $req->start;
    //     $rowperpage = $req->length;
    //     $period = $req->period;

    //     $leads = Lead::select('date', 'source', 'company', 'candidate_id');

    //     if ($period) {
    //         $leads = $leads
    //             ->whereDate('date', '>=', Carbon::createFromFormat('Y-m-d', $period['from']))
    //             ->whereDate('date', '<=', Carbon::createFromFormat('Y-m-d', $period['to']));
    //     }

    //     $leads = $leads->with([
    //         'Candidate.ActiveHistory' => function ($query) {
    //             return $query->select('model_name', 'model_obj_id', 'current_value', 'created_at')
    //                 ->orderBy('created_at', 'desc');
    //         }
    //     ])->orderBy('date', 'desc')->get();

    //     $sources = array();

    //     foreach ($leads as $lead) {
    //         if (!isset($sources[$lead->source])) {
    //             $sources[$lead->source] = array(
    //                 'name' => $lead->source,
    //                 'company' => $lead->company,
    //                 'leads' => array()
    //             );
    //         }

    //         $sources[$lead->source]['leads'][] = $lead;
    //     }

    //     $result = array();

    //     foreach ($sources as $source) {
    //         $total = count($source['leads']);
    //         $col_1 = 0;
    //         $col_2 = 0;

    //         foreach ($source['leads'] as $lead) {
    //             if ($lead->Candidate && $lead->Candidate->ActiveHistory) {
    //                 foreach ($lead->Candidate->ActiveHistory as $item) {
    //                     if ($item->current_value == '4') {
    //                         $col_1++;
    //                         break;
    //                     } elseif ($item->current_value == '9') {
    //                         $col_2++;
    //                         break;
    //                     }
    //                 }
    //             }
    //         }

    //         $result[] = array(
    //             'name' => $source['name'],
    //             'company' => $source['company'],
    //             'total' => $total,
    //             'col_1' => $col_1,
    //             'col_2' => $col_2,
    //             'barWidth' => $total ? round((($col_1 + $col_2) / $total) * 100) : 0,
    //         );
    //     }

    //     return response(array(
    //         'data' => $result,
    //         'draw' => $draw,
    //         'recordsTotal' => $filtered_count,
    //         'recordsFiltered' => count($result),
    //     ), 200);
    // }

    public function getLeadsJson(Request $req, StatisticsService $stat)
    {
        $draw = $req->draw;
        $start = $req->start;
        $rowperpage = $req->length;
        $period = $req->period;

        $leads = Lead::select('date', 'source', 'company', 'status', 'active', 'candidate_id')->where('active', true);

        if ($period) {
            $leads = $leads
                ->whereDate('date', '>=', Carbon::createFromFormat('Y-m-d', $period['from']))
                ->whereDate('date', '<=', Carbon::createFromFormat('Y-m-d', $period['to']));
        }

        $leads = $leads
            ->with([
                'Candidate',
                'Candidate.ActiveHistory' => function ($query) use ($period) {
                    $query = $query->select('id', 'model_name', 'model_obj_id', 'current_value', 'created_at', 'user_role');

                    if ($period) {
                        $query = $query
                            ->whereDate('created_at', '>=', Carbon::createFromFormat('Y-m-d', $period['from']))
                            ->whereDate('created_at', '<=', Carbon::createFromFormat('Y-m-d', $period['to']));
                    }

                    $query->orderBy('id', 'desc');
                }
            ])
            ->orderBy('date', 'desc')
            ->get();

        $sources = [];

        foreach ($leads as $lead) {
            if (!isset($sources[$lead->source . $lead->company])) {
                $sources[$lead->source . $lead->company] = array(
                    'name' => $lead->source,
                    'company' => $lead->company,
                    'leads' => [],
                );
            }

            $sources[$lead->source . $lead->company]['leads'][] = $lead;
        }

        $result = [];

        foreach ($sources as $source) {
            $total = count($source['leads']);
            $col_1 = 0;
            $col_2 = 0;
            $col_3 = 0;
            $col_4 = 0;
            $col_new = 0;
            $Candidates = [];

            foreach ($source['leads'] as $lead) {
                if ($lead->status == 1) {
                    $col_1++;
                } elseif ($lead->status == 2) {
                    $col_2++;
                } elseif ($lead->status == 3) {
                    $col_3++;
                } elseif ($lead->status == 4) {
                    $col_4++;
                } else {
                    $col_new++;
                }

                if ($lead->Candidate) {
                    $Candidates[] = $lead->Candidate;
                }
            }

            $candidates_stat = $stat->candidates($Candidates);

            $result[] = array(
                'name' => $source['name'],
                'company' => $source['company'],
                'total' => $total,
                'col_new' => $col_new,
                'col_1' => $col_1,
                'col_2' => $col_2,
                'col_3' => $col_3,
                'col_4' => $col_4,
                'candidates' => $candidates_stat,
            );
        }

        $order = request('order');
        $columns = request('columns');
        $order_columns = [];

        if ($order) {
            foreach ($order as $o) {
                $name = $columns[$o['column']]['name'];
                if ($name) {
                    $order_columns[] = [
                        'name' => $name,
                        'dir' => $o['dir'],
                    ];
                }
            }
        }

        if ($order_columns) {
            foreach ($order_columns as $col) {
                usort($result, function ($a, $b) use ($col) {

                    if (stripos($col['name'], 'candidates.') !== false) {
                        $name = explode('.', $col['name']);
                        $name = $name[1];

                        if ($a['candidates'][$name] == $b['candidates'][$name]) {
                            return 0;
                        }

                        if ($col['dir'] == 'asc') {
                            return ($a['candidates'][$name] < $b['candidates'][$name]) ? -1 : 1;
                        } else if ($col['dir'] == 'desc') {
                            return ($a['candidates'][$name] > $b['candidates'][$name]) ? -1 : 1;
                        }
                    }

                    if ($col['dir'] == 'asc') {
                        return ($a[$col['name']] < $b[$col['name']]) ? -1 : 1;
                    } else if ($col['dir'] == 'desc') {
                        return ($a[$col['name']] > $b[$col['name']]) ? -1 : 1;
                    }
                });
            }
        }

        return response(array(
            'data' => $result,
            'draw' => $draw,
            'recordsTotal' => count($result),
            'recordsFiltered' => count($result),
        ), 200);
    }
}
