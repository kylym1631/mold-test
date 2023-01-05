<?php

namespace App\Services;

use App\Models\C_file;
use App\Models\Candidate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WorkLogsService
{
    public function getResultData($candidate_ids, $period = '', $clients = [])
    {
        $period_instance = Carbon::now();

        if ($period) {
            if (isset($period['from'])) {
                $period_instance = Carbon::createFromFormat('Y-m', $period['from']);
            } else {
                $period_instance = Carbon::parse($period);
            }
        }

        $candidates = Candidate::whereIn('id', $candidate_ids)
            ->with([
                'Client',
                'Client_position',
                'Client.Coordinator',
                'WorkLog' => function ($query) use ($period_instance) {
                    $query->whereMonth('period', $period_instance->month);
                },
                'WorkLog.WorkLogDays' => function ($query) use ($period_instance) {
                    $query->whereMonth('date', $period_instance->month);
                },
                'WorkLogAdditions' => function ($query) use ($period_instance) {
                    $query->whereMonth('date', $period_instance->month);
                },
                'PositionsAll' => function ($query) {
                    $query = $query->orderBy('start_at', 'ASC');
                },
                'Positions' => function ($query) use ($period_instance) {
                    $query = $query->orderBy('start_at', 'ASC');

                    $query
                        ->whereMonth('start_at', '<=', $period_instance->month)
                        ->whereMonth('end_at', '>=', $period_instance->month)
                        ->orWhere(function ($q) use ($period_instance) {
                            $q->whereMonth('start_at', '<=', $period_instance->month)
                                ->whereNull('end_at');
                        });
                },
                'Positions.Position',
                'Positions.Position.Client',
                'Positions.Rates' => function ($query) use ($period_instance) {
                    $query = $query->orderBy('start_at', 'ASC');

                    $query->whereMonth('start_at', '<=', $period_instance->month);
                },
                'HousingPeriods' => function ($query) use ($period_instance) {
                    $query
                        ->whereMonth('start_at', '<=', $period_instance->month)
                        ->whereMonth('end_at', '>=', $period_instance->month)
                        ->orWhereNull('end_at');
                },
                'HousingPeriods.Housing',
                'Oswiadczenies' => function ($query) use ($period_instance) {
                    $query->orderBy('date', 'ASC');
                },
            ])
            ->get();

        $data = [];
        $days_count = $period_instance->daysInMonth;

        foreach ($candidates as $m) {
            $work_days_count = 0;
            $month_days = [];

            for ($i = 1; $i <= $days_count; $i++) {
                $date = $period_instance->setDay($i);
                $dayOfWeek = $date->dayOfWeek;
                $work_day = false;

                if ($dayOfWeek !== 0 && $dayOfWeek !== 6) {
                    $work_days_count++;
                    $work_day = true;
                }

                $month_days[] = [
                    'id' => '',
                    'date' => $date->format('Y-m-d'),
                    'dateFormated' => $date->format('d.m.Y'),
                    'log_id' => '',
                    'work_time' => '',
                    'work_time_raw' => 0,
                    'rate' => 0,
                    'personal_rate' => 0,
                    'housing' => 0,
                    'position_id' => '',
                    'is_work_day' => $work_day,
                ];
            }

            $res = [
                'firstName' => $m->firstName,
                'lastName' => $m->lastName,
                'candidate_id' => $m->id,
                'period' => $period_instance->format('Y-m-d'),
                'pesel' => $m->pesel,
                'account_number' => $m->account_number,
                'client_id' => '',
                'client_name' => '',
                'current_position' => '',
                'work_time_format' => '',
                'coordinator_id' => '',
                'coordinator_name' => '',
                'log_id' => '',
                'client_work_time' => 0,
                'personal_rate' => 0,
                'rate' => 0,
                'position_name' => 0,
                'days' => 0,
                'witness' => 0,
                'bhp_form' => 0,
                'fine' => 0,
                'premium' => 0,
                'stay_cards_cost' => 0,
                'recommendation' => 0,
                'transport' => 0,
                'work_permits' => 0,
                'prepayment' => 0,
                'salary' => 0,
                'payoff' => 0,
                'completed' => false,
                'work_log_days' => [],
                'work_time_sum' => 0,
                'housing_sum' => 0,
                'positions' => [],
                'started_work' => '',
                'ended_work' => '',
                'work_days_count' => $work_days_count,
                'oswiadczenie_min_hours' => 0,
            ];

            if ($m->Client) {
                $res['client_name'] = $m->Client->name;
                $res['client_id'] = $m->Client->id;

                if ($m->Client->Coordinator) {
                    $res['coordinator_id'] = $m->Client->Coordinator->id;
                    $res['coordinator_name'] = mb_strtoupper($m->Client->Coordinator->firstName . ' ' . $m->Client->Coordinator->lastName);
                }
            }

            if ($m->Client_position) {
                $res['current_position'] = $m->Client_position->title;
            }

            if ($m->PositionsAll && !$m->PositionsAll->isEmpty()) {
                $res['started_work'] = $m->PositionsAll[0]->start_at;
                $res['ended_work'] = $m->PositionsAll[count($m->PositionsAll) - 1]->end_at;
            }

            if ($m->Positions && !$m->Positions->isEmpty()) {
                $res['positions'] = $m->Positions->toArray();

                foreach ($m->Positions as $p_key => $position) {

                    $rate_type = 'rate';

                    if ($position->Position) {
                        $res['position_name'] = $position->Position->title;
                        $res['positions'][$p_key]['position_name'] = $position->Position->title;
                    }

                    $pos_start = Carbon::parse($position->start_at)->format('Y-m-d');
                    $pos_end = $position->end_at ? Carbon::parse($position->end_at)->format('Y-m-d') : null;

                    foreach ($month_days as $day_key => $day_item) {

                        if (
                            ($pos_end && $pos_start <= $day_item['date'] && $day_item['date'] <= $pos_end)
                            || (!$pos_end && $pos_start <= $day_item['date'])
                        ) {
                            $rate_type = 'rate';

                            if (Carbon::parse($day_item['date']) >= Carbon::parse($position->start_at)->addMonths(3)) {
                                $rate_type = 'rate_after';
                            }

                            if ($position->Position) {
                                $month_days[$day_key]['position_name'] = $position->Position->title;
                                $month_days[$day_key]['position_client_id'] = $position->Position->client_id;
                                $month_days[$day_key]['work_time_format'] = $position->Position->Client->work_time_format ?: 'decimal';
                                $month_days[$day_key]['work_time'] = $month_days[$day_key]['work_time_format'] == 'decimal' ? '0' : '00:00';
                                $month_days[$day_key]['position_id'] = $position->client_position_id;
                                $month_days[$day_key]['is_client'] = in_array($month_days[$day_key]['position_client_id'], $clients);
                            }

                            if ($position->Rates && !$position->Rates->isEmpty()) {
                                foreach ($position->Rates as $rate) {

                                    $rate_start = Carbon::parse($rate->start_at)->format('Y-m-d');

                                    if ($day_item['date'] >= $rate_start && $rate->type == $rate_type) {
                                        $month_days[$day_key]['rate'] = $rate->amount;
                                    }
                                    if ($day_item['date'] >= $rate_start && $rate->type == 'personal_rate') {
                                        $month_days[$day_key]['personal_rate'] = $rate->amount;
                                    }
                                }
                            }
                        }
                    }

                    if ($position->Rates && !$position->Rates->isEmpty()) {
                        foreach ($position->Rates as $rate) {
                            if ($rate->type == 'personal_rate') {
                                $res['personal_rate'] = $rate->amount;
                                $res['positions'][$p_key]['personal_rate'] = $rate->amount;
                            }
                            if ($rate->type == $rate_type) {
                                $res['rate'] = $rate->amount;
                                $res['positions'][$p_key]['rate'] = $rate->amount;
                            }
                        }
                    }
                }
            }

            if ($m->HousingPeriods && !$m->HousingPeriods->isEmpty()) {
                foreach ($m->HousingPeriods as $housing) {

                    $h_start = Carbon::parse($housing->start_at)->format('Y-m-d');
                    $h_end = $housing->end_at ? Carbon::parse($housing->end_at)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

                    foreach ($month_days as $day_key => $day_item) {
                        if ($h_start <= $day_item['date'] && $day_item['date'] <= $h_end) {

                            $month_days[$day_key]['housing_id'] = $housing->housing_id;
                            $month_days[$day_key]['housing'] = $housing->Housing->cost_per_day;

                            $res['housing_sum'] += $housing->Housing->cost_per_day;
                        }
                    }
                }
            }

            if ($m->Oswiadczenies && !$m->Oswiadczenies->isEmpty()) {
                foreach ($m->Oswiadczenies as $oswiadczenie) {
                    $res['oswiadczenie_min_hours'] = $oswiadczenie->min_hours;
                }
            }

            if ($m->WorkLogAdditions && !$m->WorkLogAdditions->isEmpty()) {
                foreach ($m->WorkLogAdditions as $addItem) {
                    $res[$addItem->type] = $res[$addItem->type] + $addItem->amount;
                }
            }

            if ($m->WorkLog) {
                $res['log_id'] = $m->WorkLog->id;
                $res['client_work_time'] = $m->WorkLog->client_work_time ?: 0;
                $res['days'] = $m->WorkLog->days ?: 0;
                $res['witness'] = $m->WorkLog->witness ?: 0;
                $res['period'] = $m->WorkLog->period;
                $res['completed'] = $m->WorkLog->completed;

                if ($m->WorkLog->WorkLogDays && !$m->WorkLog->WorkLogDays->isEmpty()) {
                    foreach ($month_days as $day_key => $day_item) {
                        foreach ($m->WorkLog->WorkLogDays as $log_day) {
                            if ($day_item['date'] === $log_day->date) {

                                $month_days[$day_key]['id'] = $log_day->id;
                                $month_days[$day_key]['log_id'] = $log_day->work_log_id;

                                if ($log_day->work_time) {
                                    $month_days[$day_key]['work_time_raw'] = $log_day->work_time;

                                    if ($month_days[$day_key]['work_time_format'] == 'decimal') {
                                        $month_days[$day_key]['work_time'] = round($log_day->work_time / 60, 2);
                                    } else {
                                        $hours = intdiv($log_day->work_time, 60);
                                        $min = $log_day->work_time % 60;

                                        $month_days[$day_key]['work_time'] = ($hours < 10 ? '0' . $hours : $hours) . ':' . ($min < 10 ? '0' . $min : $min);
                                    }
                                }

                                $res['work_time_sum'] += $log_day->work_time ?: 0;
                                $res['salary'] += ($log_day->work_time ?: 0) * ($day_item['rate'] / 60);
                            }
                        }
                    }
                }
            }


            $res['work_log_days'] = $month_days;

            if ($res['work_time_sum'] > 0) {
                $res['work_time_sum'] = round($res['work_time_sum'] / 60, 2);
            }

            if ($res['client_work_time'] > 0) {
                $res['client_work_time'] = round($res['client_work_time'] / 60, 2);
            }

            $res['salary'] = round($res['salary'], 2);

            $res['payoff'] = round($res['salary'] - $res['housing_sum'] - $res['fine'] - $res['stay_cards_cost'] + $res['premium'] + $res['recommendation'] - $res['transport'] - $res['work_permits'] - $res['prepayment'], 2);

            $data[] = $res;
        }

        return $data;
    }

    public function getResultDataByPositions($candidate_ids, $period)
    {
        $result = $this->getResultData($candidate_ids, $period);

        foreach ($result as $res_key => $res_item) {
            foreach ($res_item['positions'] as $key => $pos) {
                $res_item['positions'][$key]['work_log_days'] = [];
                $work_time_sum = 0;
                $work_days_count = 0;
                $housing_sum = 0;

                foreach ($res_item['work_log_days'] as $day) {

                    if ($day['position_id'] == $pos['client_position_id']) {
                        $work_time_sum += $day['work_time_raw'];
                        $housing_sum += $day['housing'];

                        if ($day['is_work_day']) {
                            $work_days_count++;
                        }
                    } else {
                        $day['work_time'] = '';
                    }

                    $res_item['positions'][$key]['work_log_days'][] = $day;
                }

                $res_item['positions'][$key]['work_time_sum'] = round($work_time_sum / 60, 2);
                $res_item['positions'][$key]['housing_sum'] = $housing_sum;
                $res_item['positions'][$key]['work_days_count'] = $work_days_count;
                $res_item['positions'][$key]['witness'] = $res_item['witness'] / count($res_item['positions']);
                $res_item['positions'][$key]['oswiadczenie_min_hours'] = $res_item['oswiadczenie_min_hours'] / count($res_item['positions']);
                $res_item['positions'][$key]['days'] = $res_item['days'] / count($res_item['positions']);
                $res_item['positions'][$key]['fine'] = $res_item['fine'] / count($res_item['positions']);
                $res_item['positions'][$key]['premium'] = $res_item['premium'] / count($res_item['positions']);
                $res_item['positions'][$key]['prepayment'] = $res_item['prepayment'] / count($res_item['positions']);
                $res_item['positions'][$key]['bhp_form'] = $res_item['bhp_form'] / count($res_item['positions']);
                $res_item['positions'][$key]['period'] = '';
                $res_item['positions'][$key]['log_id'] = '';
                $res_item['positions'][$key]['pesel'] = '';
                $res_item['positions'][$key]['firstName'] = '';
                $res_item['positions'][$key]['lastName'] = '';
                $res_item['positions'][$key]['account_number'] = '';
                $res_item['positions'][$key]['candidate_id'] = $res_item['candidate_id'];
                $res_item['positions'][$key]['client_name'] = $res_item['client_name'];
                $res_item['positions'][$key]['started_work'] = $pos['start_at'];
                $res_item['positions'][$key]['ended_work'] = $pos['end_at'];
            }

            $result[$res_key]['positions'] = $res_item['positions'];
        }

        return $result;
    }

    public function getLogDaysByPositions($candidate_ids, $period, $clients)
    {
        $candidates = $this->getResultData($candidate_ids, $period, $clients);

        $result = [];

        foreach ($candidates as $cnd) {
            $cnd['row_key'] = $cnd['candidate_id'];
            $result[] = $cnd;

            if (count($cnd['positions']) < 2) {
                continue;
            }

            $positions = [];

            foreach ($cnd['positions'] as $pos) {
                if (!isset($positions[$pos['client_position_id']])) {
                    $positions[$pos['client_position_id']] = $pos;
                }
            }

            foreach ($positions as $pos_id => $pos) {
                $work_log_days = [];
                $work_time_sum = 0;
                $housing_sum = 0;
                $work_days_count = 0;
                $salary = 0;

                foreach ($cnd['work_log_days'] as $day) {
                    if ($day['position_id'] == $pos_id) {
                        $work_time_sum += $day['work_time_raw'];
                        $housing_sum += $day['housing'];
                        $salary += (($day['work_time_raw'] / 60) * $day['rate']);

                        if ($day['is_work_day']) {
                            $work_days_count++;
                        }
                    } else {
                        $day['work_time'] = '';
                        $day['position_id'] = '';
                    }

                    $work_log_days[] = $day;
                }

                $pos['work_log_days'] = $work_log_days;
                $pos['work_time_sum'] = round($work_time_sum / 60, 2);
                $pos['housing_sum'] = round($housing_sum, 2);
                $pos['salary'] = round($salary, 2);
                $pos['work_days_count'] = $work_days_count;
                $pos['is_position'] = true;
                $pos['row_key'] = $cnd['candidate_id'] . '-' . $pos['client_position_id'];
                $pos['log_id'] = $cnd['log_id'];
                $pos['current_position'] = $pos['position_name'];

                $result[] = $pos;
            }
        }

        return $result;
    }

    public static function checkFiles($r)
    {
        if (empty($r->file)) {
            return null;
        }

        foreach ($r->file as $fileItem) {
            if ($fileItem->isValid()) {
                $ext = $fileItem->getClientOriginalExtension();

                if ($ext != 'jpeg' && $ext != 'jpg' && $ext != 'png' && $ext != 'gif' && $ext != 'pdf') {
                    return array(
                        'success' => "false",
                        'error' => 'Недопустимый тип файла'
                    );
                }

                if ($fileItem->getSize() > 5000000) {
                    return array(
                        'success' => "false",
                        'error' => 'Недопустимый вес файла'
                    );
                }
            } else {
                return array(
                    'success' => "false",
                    'error' => 'Файл повреждён и не может быть загружен!'
                );
            }
        }
    }

    public function addFiles($req, $h_id)
    {
        if ($req->to_delete_files) {
            foreach ($req->to_delete_files as $fName) {
                C_file::where('work_log_addition_id', $h_id)
                    ->where('original_name', $fName)
                    ->delete();
            }
        }

        if ($req->file) {
            foreach ($req->file as $fileItem) {
                $path = '/uploads/work_log_addition/' . Carbon::now()->format('m.Y') . '/' . $h_id . '/files/';
                $name = Str::random(12) . '.' . $fileItem->getClientOriginalExtension();

                $fileItem->move(public_path($path), $name);
                $file_link = $path . $name;

                $file = new C_file();
                $file->autor_id = Auth::user()->id;
                $file->work_log_addition_id = $h_id;
                $file->user_id = Auth::user()->id;
                $file->type = 1;
                $file->original_name = $fileItem->getClientOriginalName();
                $file->ext = $fileItem->getClientOriginalExtension();
                $file->path = $file_link;
                $file->save();
            }
        }
    }
}
