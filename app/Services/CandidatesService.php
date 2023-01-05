<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\CandidateDocument;
use App\Models\CandidateHousing;
use App\Models\CandidatePosition;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\WorkLogsService;
use App\Services\OptionsService;
use Illuminate\Http\Request;

class CandidatesService
{
    public function getList(Request $req, $model)
    {
        $start = $req->start;
        $rowperpage = $req->length; // Rows display per page

        $status = $req->status;
        $search = $req->search;
        $vacancies = $req->vacancies;
        $clients = $req->clients;
        $transportations = $req->transportations;
        $koordinators = $req->koordinators;
        $housings = $req->housings;
        $housing_room = $req->housing_room;
        $period = $req->period;
        $recruiter = $req->recruiter;
        $citizenship = $req->citizenship;
        $country = $req->country;
        $work_log_period = $req->work_log_period;

        $cand_model = match ($model) {
            'candidate' => Candidate::allowedWithStatus(),
            'employee' => Candidate::allowedWithEmployeeStatus(),
            'merged' => Candidate::allowedWithAllStatuses(),
            'accounting' => Candidate::allowedWithAccountantStatuses(),
        };

        $filtered_count = $this->prepareGetJsonRequest($cand_model, $status, $vacancies, $search, $clients, $period, $recruiter, $citizenship, $country, $housings, $housing_room, $koordinators, $work_log_period, $transportations, $model);
        $filtered_count = $filtered_count->count();

        $users = $this->prepareGetJsonRequest($cand_model, $status, $vacancies, $search, $clients, $period, $recruiter, $citizenship, $country, $housings, $housing_room, $koordinators, $work_log_period, $transportations, $model);

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
                $users = $users->orderBy($col['name'], $col['dir']);
            }
        } else {
            $users = $users->orderBy('id', 'DESC');
        }

        if ($model == 'accounting') {
            $users = $users->with([
                'Client',
                'Client_position',
                'Client.Coordinator',
            ]);
        } else {
            $users = $users
                ->with('Client')
                ->with('Recruiter')
                ->with('Vacancy')
                ->with('Housing')
                ->with('Housing_room')
                ->with('D_file')
                ->with([
                    'Candidate_arrival' => function ($q) {
                        return $q->orderBy('id', 'desc');
                    }
                ]);
        }

        $users = $users
            ->skip($start)
            ->take($rowperpage)
            ->get();

        return [
            'data' => $users,
            'filtered_count' => $filtered_count,
        ];
    }

    public function prepareGetJsonRequest($candidates_model, $status = '', $vacancies = '', $search = '', $clients = '', $period = '', $recruiter = '', $citizenship = '', $country = '', $housings = '', $housing_room = '', $koordinators = '', $work_log_period = '', $transportations = '', $model = '')
    {
        $users = $candidates_model;

        if ($status) {
            $users = $users->whereIn('active', $status);
        }

        if (Auth::user()->isFreelancer()) {
            $users = $users->where('user_id', Auth::user()->id);
        }

        if (Auth::user()->isRecruiter()) {
            $users = $users->where('recruiter_id', Auth::user()->id);
        }

        if (Auth::user()->isKoordinator()) {
            $users = $users->whereHas('Client', function ($q) {
                return $q->where('coordinator_id', Auth::user()->id);
            });
        }

        if ($model == 'accounting') {
            $users = $users->whereHas('WorkLog', function ($q) use ($work_log_period) {
                $q = $q->where('completed', true);

                if ($work_log_period) {
                    $q->whereMonth('period', Carbon::createFromFormat('Y-m', $work_log_period['from'])->month);
                } else {
                    $q->whereMonth('period', Carbon::now()->month);
                }
            });
        }

        if ($clients) {
            $users = $users->whereIn('client_id', $clients);
        }

        if ($transportations) {
            $users = $users->whereHas('Candidate_arrival', function ($q) use ($transportations) {
                $q->whereIn('transportation_id', $transportations);
            });
        }

        if ($koordinators) {
            $users = $users->whereHas('Client', function ($q) use ($koordinators) {
                return $q->whereIn('coordinator_id', $koordinators);
            });
        }

        if (Auth::user()->isAdmin() || Auth::user()->isRecruitmentDirector() || Auth::user()->isRecruiter() || Auth::user()->isLegalizationManager()) {
            if ($citizenship) {
                $users = $users->whereIn('citizenship_id', $citizenship);
            }

            if ($country) {
                $users = $users->whereIn('country_id', $country);
            }
        }

        if ((Auth::user()->isAdmin() || Auth::user()->isLegalizationManager()) && $recruiter) {
            $users = $users->whereIn('recruiter_id', $recruiter);
        }

        if (Auth::user()->isRecruitmentDirector()) {
            if ($recruiter) {
                $users = $users->whereIn('recruiter_id', $recruiter);
            } else {
                $us_ids = User::where('group_id', 2)->where('user_id', Auth::user()->id)->pluck('id');
                $users = $users->whereIn('recruiter_id', $us_ids);
            }
        }

        if ($period) {
            $users = $users
                ->whereDate('active_update', '>=', Carbon::createFromFormat('Y-m-d', $period['from']))
                ->whereDate('active_update', '<=', Carbon::createFromFormat('Y-m-d', $period['to']));
        }

        if ($vacancies) {
            $users = $users->whereIn('real_vacancy_id', $vacancies);
        }

        if ($housings) {
            $users = $users->whereIn('housing_id', $housings);
        }

        if ($housing_room) {
            $users = $users->whereIn('housing_room_id', $housing_room);
        }


        if ($search != '') {
            $users = $users->where(function ($query) use ($search) {
                $query->where('firstName', 'LIKE', '%' . $search . '%')
                    ->orWhere('lastName', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search . '%')
                    ->orWhere('viber', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone_parent', 'LIKE', '%' . $search . '%');
            });
        }

        return $users;
    }

    public function getResultData($users, $permission, $view)
    {
        $data = [];

        foreach ($users as $u) {
            if ($u->D_file != null) {
                if (config('app.env') === 'local') {
                    $path_url = url('/');
                } else {
                    $path_url = url('/') . '/public';
                }

                $file = '<a href="' . $path_url . $u->D_file->path . '" style="cursor: pointer;" class="js-view-doc svg-icon svg-icon-2x svg-icon-primary me-4" data-ext="' . $u->D_file->ext . '">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="currentColor"></path>
                            <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor"></path>
                        </svg>
                    </a>';
            } else {
                $file = '';
            }

            $count_failed_call_btn = '';

            if ($u->active == 15) {
                $count_failed_call_btn = '<button class="js-call-fail-btn" data-action="setCandidateStatus" data-candidate-id="' . $u->id . '" data-status="' . $u->active . '">Недозвон ' . ($u->count_failed_call + 1) . '</button>';
            }

            $info_btn = '';

            if ($u->active == 3 || $u->active == 5 || $u->active == 22) {
                $info_btn = '<button type="button" class="js-show-comment btn btn-sm btn-icon show-info-btn" data-tooltip="' . $u->reason_reject . '">
                <span class="svg-icon svg-icon-primary">
                <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 512 512" width="20px" height="20px"><path class="circ" d="M504.1,256C504.1,119,393,7.9,256,7.9C119,7.9,7.9,119,7.9,256C7.9,393,119,504.1,256,504.1C393,504.1,504.1,393,504.1,256z"/><path class="let" d="M323.2 367.5c-1.4-2-4-2.8-6.3-1.7-24.6 11.6-52.5 23.9-58 25-.1-.1-.4-.3-.6-.7-.7-1-1.1-2.3-1.1-4 0-13.9 10.5-56.2 31.2-125.7 17.5-58.4 19.5-70.5 19.5-74.5 0-6.2-2.4-11.4-6.9-15.1-4.3-3.5-10.2-5.3-17.7-5.3-12.5 0-26.9 4.7-44.1 14.5-16.7 9.4-35.4 25.4-55.4 47.5-1.6 1.7-1.7 4.3-.4 6.2 1.3 1.9 3.8 2.6 6 1.8 7-2.9 42.4-17.4 47.6-20.6 4.2-2.6 7.9-4 10.9-4 .1 0 .2 0 .3 0 0 .2.1.5.1.9 0 3-.6 6.7-1.9 10.7-30.1 97.6-44.8 157.5-44.8 183 0 9 2.5 16.2 7.4 21.5 5 5.4 11.8 8.1 20.1 8.1 8.9 0 19.7-3.7 33.1-11.4 12.9-7.4 32.7-23.7 60.4-49.7C324.3 372.2 324.6 369.5 323.2 367.5zM322.2 84.6c-4.9-5-11.2-7.6-18.7-7.6-9.3 0-17.5 3.7-24.2 11-6.6 7.2-9.9 15.9-9.9 26.1 0 8 2.5 14.7 7.3 19.8 4.9 5.2 11.1 7.8 18.5 7.8 9 0 17-3.9 24-11.6 6.9-7.6 10.4-16.4 10.4-26.4C329.6 96 327.1 89.6 322.2 84.6z"/></svg>
                </span>
                </button>';
            }

            if ($u->active == 14 || $u->active == 21) {
                $info_btn = '<button type="button" class="js-show-comment btn btn-sm btn-icon show-info-btn" data-tooltip="' . $u->reason_reject . '">
                <span class="svg-icon svg-icon-primary svg-icon-2x">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"></rect>
                        <polygon opacity="0.3" points="5 15 3 21.5 9.5 19.5"></polygon>
                        <path d="M13.5,21 C8.25329488,21 4,16.7467051 4,11.5 C4,6.25329488 8.25329488,2 13.5,2 C18.7467051,2 23,6.25329488 23,11.5 C23,16.7467051 18.7467051,21 13.5,21 Z M9,8 C8.44771525,8 8,8.44771525 8,9 C8,9.55228475 8.44771525,10 9,10 L18,10 C18.5522847,10 19,9.55228475 19,9 C19,8.44771525 18.5522847,8 18,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 C8,13.5522847 8.44771525,14 9,14 L14,14 C14.5522847,14 15,13.5522847 15,13 C15,12.4477153 14.5522847,12 14,12 L9,12 Z"></path>
                    </g>
                </svg>
                </span>
                </button>';
            }

            if (
                Auth::user()->isAdmin()
                || (Auth::user()->hasPermission($permission . '.edit')
                    && Auth::user()->hasPermission($permission . '.edit.status.' . $u->active))
            ) {
                $select_active = '<div class="row flex-nowrap status-actions"><select class="js-select-status form-select form-select-sm form-select-solid" data-action="setCandidateStatus" data-candidate-id="' . $u->id . '"> ' . $u->getStatusOptions() . ' </select>' . $info_btn . $count_failed_call_btn . '</div>';
            } else {
                $select_active = '<div class="row flex-nowrap status-actions">' . Candidate::getStatusTitle($u->active) . $info_btn . $count_failed_call_btn . '</div>';
            }

            $Recruiter = '';
            if ($u->Recruiter != null) {
                $Recruiter = $u->Recruiter->firstName . ' ' . $u->Recruiter->lastName;
            }

            $Vacancy = '';
            if ($u->Vacancy != null) {
                $Vacancy = '<span style="font-size: 11px;line-height: 1;">' . $u->Vacancy->title . '</span>';
            }

            $Housing = '';
            if ($u->Housing != null) {
                $Housing = $u->Housing->title;
            }

            $Housing_room = '';
            if ($u->Housing_room != null) {
                $Housing_room = $u->Housing_room->number;
            }

            $date_arrive = '';
            if ($arriv = $u->Candidate_arrival->values()->first()) {
                $date_arrive = Carbon::parse($arriv->date_arrive)->format('d.m.Y H:i');
            } elseif (
                Auth::user()->group_id == 4
                || Auth::user()->group_id == 1
                || Auth::user()->group_id == 2
            ) {
                $date_arrive = '<button type="button" class="js-add-arrival btn btn-sm btn-icon create-arrival-btn m-auto" data-candidate-id="' . $u->id . '" title="Добавить приезд">
                <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Scale.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24"/>
                    <path d="M10,14 L5,14 C4.33333333,13.8856181 4,13.5522847 4,13 C4,12.4477153 4.33333333,12.1143819 5,12 L12,12 L12,19 C12,19.6666667 11.6666667,20 11,20 C10.3333333,20 10,19.6666667 10,19 L10,14 Z M15,9 L20,9 C20.6666667,9.11438192 21,9.44771525 21,10 C21,10.5522847 20.6666667,10.8856181 20,11 L13,11 L13,4 C13,3.33333333 13.3333333,3 14,3 C14.6666667,3 15,3.33333333 15,4 L15,9 Z" fill="#000000" fill-rule="nonzero"/>
                    <path d="M3.87867966,18.7071068 L6.70710678,15.8786797 C7.09763107,15.4881554 7.73079605,15.4881554 8.12132034,15.8786797 C8.51184464,16.2692039 8.51184464,16.9023689 8.12132034,17.2928932 L5.29289322,20.1213203 C4.90236893,20.5118446 4.26920395,20.5118446 3.87867966,20.1213203 C3.48815536,19.7307961 3.48815536,19.0976311 3.87867966,18.7071068 Z M16.8786797,5.70710678 L19.7071068,2.87867966 C20.0976311,2.48815536 20.7307961,2.48815536 21.1213203,2.87867966 C21.5118446,3.26920395 21.5118446,3.90236893 21.1213203,4.29289322 L18.2928932,7.12132034 C17.9023689,7.51184464 17.2692039,7.51184464 16.8786797,7.12132034 C16.4881554,6.73079605 16.4881554,6.09763107 16.8786797,5.70710678 Z" fill="#000000" opacity="0.3"/>
                </g>
                </svg><!--end::Svg Icon--></span>
                </button>';
            }

            if (Auth::user()->isAdmin() || Auth::user()->isRecruitmentDirector() || Auth::user()->isLegalizationManager()) {
                $temp_arr = [
                    '<a href="/candidate/view?id=' . $u->id . '">' . $u->id . '</a>',
                    mb_strtoupper($u->firstName),
                    mb_strtoupper($u->lastName),
                    $Recruiter,
                    $u->phone,
                    $Vacancy,
                    $u->viber,
                    $u->phone_parent,
                    Carbon::parse($u->created_at)->format('d.m.Y H:i'),
                    $date_arrive,
                    $file,
                    $select_active
                ];
            } else {
                $temp_arr = [
                    '<a href="/candidate/view?id=' . $u->id . '">' . $u->id . '</a>',
                    mb_strtoupper($u->firstName),
                    mb_strtoupper($u->lastName),
                    $u->phone,
                    $Vacancy,
                    $u->viber,
                    $u->phone_parent,
                    Carbon::parse($u->created_at)->format('d.m.Y H:i'),
                    $date_arrive,
                    $file,
                    $select_active
                ];
            }

            if (
                Auth::user()->isAdmin() || Auth::user()->hasPermission($permission . '.delete')
            ) {
                if (
                    Auth::user()->isAdmin()
                    || Auth::user()->hasPermission($permission . '.delete.status.' . $u->active)
                ) {
                    $temp_arr[] = '<button type="button" class="btn btn-sm btn-icon delete-candidate-btn" href="#" data-id="' . $u->id . '"><span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Code/Error-circle.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <rect x="0" y="0" width="24" height="24"/> <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10"/> <path d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z" fill="#000000"/> </g> </svg><!--end::Svg Icon--></span></button>';
                } else {
                    $temp_arr[] = '';
                }
            }

            if ($view && $view == 'clients.view') {
                $temp_arr = [
                    '<a href="/candidate/view?id=' . $u->id . '">' . $u->id . '</a>',
                    mb_strtoupper($u->firstName),
                    mb_strtoupper($u->lastName),
                    $Vacancy,
                    $Housing,
                    $u->phone,
                    $u->getCurrentStatus(),
                ];
            }

            if ($view && $view == 'housing.view') {
                $temp_arr = [
                    '<a href="/candidate/view?id=' . $u->id . '">' . $u->id . '</a>',
                    mb_strtoupper($u->firstName),
                    mb_strtoupper($u->lastName),
                    $Vacancy,
                    $Housing_room,
                    $u->phone,
                    $u->getCurrentStatus(),
                ];
            }

            if ($view && $view == 'transportations.view') {
                $temp_arr = [
                    '<a href="/candidate/view?id=' . $u->id . '">' . $u->id . '</a>',
                    mb_strtoupper($u->firstName),
                    mb_strtoupper($u->lastName),
                    $Vacancy,
                    $u->phone,
                    $u->getCurrentStatus(),
                ];
            }

            $data[] = $temp_arr;
        }

        return $data;
    }

    public function getResultDataToAccountant($users, $period)
    {
        $data = [];
        $wls = new WorkLogsService;
        $os = new OptionsService;
        $options = $os->getByKeys(['min_rate_netto', 'min_rate_brutto']);

        $candidate_ids = array_map(function ($c_item) {
            return $c_item['id'];
        }, $users->toArray());

        $work_log_result = $wls->getResultDataByPositions($candidate_ids, $period);

        $table = [];

        foreach ($work_log_result as $res) {
            if (count($res['positions']) > 1) {
                $res['row'] = 'origin';
                $table[] = $res;

                foreach ($res['positions'] as $pos) {
                    $pos['row'] = 'position';
                    $table[] = $pos;
                }
            } else {
                $res['row'] = '';
                $table[] = $res;
            }
        }

        $num = 0;

        foreach ($table as $u) {
            $num_res = '';
            $work_time_sum = 0;
            $oswiadczenie_min_hours = 0;
            $hours = 0;
            $days_res = $u['days'];
            $salary = 0;
            $sum_1 = 0;
            $nominal = 0;
            $brutto = 0;
            $factur = 0;
            $vat = 0;
            $salary_brutto = 0;
            $salary_netto = 0;
            $delta_1 = 0;
            $delta_2 = 0;
            $sum_2 = 0;

            if ($u['row'] == 'origin') {
                foreach ($u['positions'] as $pos) {
                    $p_sum_1 = $pos['rate'] * $pos['work_time_sum'];
                    $p_nominal = $pos['oswiadczenie_min_hours'] / $pos['work_days_count'];
                    $p_hours = $pos['days'] * $p_nominal;
                    $p_salary = $p_hours * $pos['rate'];
                    $p_brutto = $p_hours * $options['min_rate_brutto'];
                    $p_delta_1 = $p_sum_1 - $p_salary;
                    $p_factur = $pos['personal_rate'] * $pos['work_time_sum'];
                    $p_vat = $p_factur * 0.23 + $p_factur;
                    $p_salary_brutto = $options['min_rate_brutto'] * $p_hours;
                    $p_salary_netto = $p_salary - $pos['prepayment'];
                    $p_delta_2 = $p_delta_1 - $pos['bhp_form'] - $pos['fine'] - $pos['housing_sum'] + $pos['premium'];
                    $p_sum_2 = $p_sum_1 - $pos['housing_sum'] - $pos['prepayment'] - $pos['fine'] - $pos['bhp_form'] + $pos['premium'];

                    $oswiadczenie_min_hours += $pos['oswiadczenie_min_hours'];
                    $work_time_sum += $pos['work_time_sum'];
                    $sum_1 += $p_sum_1;
                    $nominal += $p_nominal;
                    $hours += $p_hours;
                    $salary += $p_salary;
                    $brutto += $p_brutto;
                    $delta_1 += $p_delta_1;
                    $factur += $p_factur;
                    $vat += $p_vat;
                    $salary_brutto += $p_salary_brutto;
                    $salary_netto += $p_salary_netto;
                    $delta_2 += $p_delta_2;
                    $sum_2 += $p_sum_2;
                }

                $u['position_name'] = '<a href="#" class="js-show-positions-details" data-candidate-id="' . $u['candidate_id'] . '" style="white-space: nowrap"><span class="svg-icon svg-icon-primary svg-icon-2x"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <polygon points="0 0 24 0 24 24 0 24"/> <path d="M8.2928955,3.20710089 C7.90237121,2.8165766 7.90237121,2.18341162 8.2928955,1.79288733 C8.6834198,1.40236304 9.31658478,1.40236304 9.70710907,1.79288733 L15.7071091,7.79288733 C16.085688,8.17146626 16.0989336,8.7810527 15.7371564,9.17571874 L10.2371564,15.1757187 C9.86396402,15.5828377 9.23139665,15.6103407 8.82427766,15.2371482 C8.41715867,14.8639558 8.38965574,14.2313885 8.76284815,13.8242695 L13.6158645,8.53006986 L8.2928955,3.20710089 Z" fill="currentColor" fill-rule="nonzero" transform="translate(12.000003, 8.499997) scale(-1, -1) rotate(-90.000000) translate(-12.000003, -8.499997) "/> <path d="M6.70710678,19.2071045 C6.31658249,19.5976288 5.68341751,19.5976288 5.29289322,19.2071045 C4.90236893,18.8165802 4.90236893,18.1834152 5.29289322,17.7928909 L11.2928932,11.7928909 C11.6714722,11.414312 12.2810586,11.4010664 12.6757246,11.7628436 L18.6757246,17.2628436 C19.0828436,17.636036 19.1103465,18.2686034 18.7371541,18.6757223 C18.3639617,19.0828413 17.7313944,19.1103443 17.3242754,18.7371519 L12.0300757,13.8841355 L6.70710678,19.2071045 Z" fill="currentColor" fill-rule="nonzero" opacity="0.3" transform="translate(12.000003, 15.499997) scale(-1, -1) rotate(-360.000000) translate(-12.000003, -15.499997) "/> </g> </svg></span>должности</a>';
            } else {
                $oswiadczenie_min_hours = $u['oswiadczenie_min_hours'];
                $work_time_sum = $u['work_time_sum'];
                $sum_1 = $u['rate'] * $u['work_time_sum'];
                $nominal = $u['oswiadczenie_min_hours'] / $u['work_days_count'];
                $hours = $u['days'] * $nominal;
                $salary = $hours * $u['rate'];
                $brutto = $hours * $options['min_rate_brutto'];
                $delta_1 = $sum_1 - $salary;
                $factur = $u['personal_rate'] * $u['work_time_sum'];
                $vat = $factur * 0.23 + $factur;
                $salary_brutto = $options['min_rate_brutto'] * $hours;
                $salary_netto = $salary - $u['prepayment'];
                $delta_2 = $delta_1 - $u['bhp_form'] - $u['fine'] - $u['housing_sum'] + $u['premium'];
                $sum_2 = $sum_1 - $u['housing_sum'] - $u['prepayment'] - $u['fine'] - $u['bhp_form'] + $u['premium'];
            }

            if ($u['row'] == 'position') {
                $u['position_name'] = '<span data-candidate-position="' . $u['candidate_id'] . '">' . $u['position_name'] . '</span>';
            }

            if ($u['row'] != 'position') {
                $days_res = '<input type="text" value="' . $u['days'] . '" data-date="' . $u['period'] . '" data-candidate-id="' . $u['candidate_id'] . '" data-log-id="' . $u['log_id'] . '" class="js-client-hours-input" name="days">';

                $num++;
                $num_res = $num;
            }

            $temp_arr = [
                '<span>' . $num_res . '</span>',
                '<span>' . $u['candidate_id'] . '</span>',
                $u['pesel'],
                mb_strtoupper($u['firstName']),
                mb_strtoupper($u['lastName']),
                $u['account_number'],
                $u['position_name'],
                '--',
                $u['client_name'],
                $u['started_work'] ? Carbon::parse($u['started_work'])->format('d.m.Y') : '',
                $u['ended_work'] ? Carbon::parse($u['ended_work'])->format('d.m.Y') : '',
                $u['personal_rate'],
                $u['rate'],
                round($work_time_sum, 2),
                round($oswiadczenie_min_hours, 2),
                round($sum_1, 2),
                round($nominal, 2),
                $options['min_rate_netto'],
                $options['min_rate_brutto'],
                $days_res,
                round($hours, 2),
                round($salary, 2),
                round($brutto, 2),
                round($delta_1, 2),
                round($factur, 2),
                round($vat, 2),
                $u['premium'],
                $u['housing_sum'],
                $u['prepayment'],
                $u['fine'],
                $u['bhp_form'],
                round($salary_brutto, 2),
                round($salary_netto, 2),
                round($delta_2, 2),
                round($sum_2, 2),
            ];

            $data[] = $temp_arr;
        }

        return $data;
    }

    public function updatePositions($candidate, $client_position_id, $date_start_work = '')
    {
        if ($client_position_id == $candidate->client_position_id) {
            return false;
        }

        $prev_pos = CandidatePosition::where('candidate_id', $candidate->id)
            ->whereNull('end_at')
            ->orderBy('id', 'DESC')
            ->first();

        $new_start = null;

        if ($date_start_work) {
            $new_start = Carbon::parse($date_start_work)->startOfDay();
        } else {
            $new_start = Carbon::now()->startOfDay();
        }

        if ($prev_pos) {
            $prev_end = $new_start->subDay();

            if ($prev_end <= $prev_pos->start_at) {
                $prev_end = Carbon::parse($prev_pos->start_at)->addDay();
                $new_start = Carbon::parse($prev_end)->addDay();
            }

            $prev_pos->end_at = $prev_end;
            $prev_pos->save();
        }

        $new_pos = new CandidatePosition;

        $new_pos->candidate_id = $candidate->id;
        $new_pos->client_position_id = $client_position_id;
        $new_pos->start_at = $new_start;

        $new_pos->save();

        return true;
    }

    public function updateHousing($candidate, $housing_id, $housing_room_id, $start_at = '', $end_at = '')
    {
        if ($end_at) {
            $last_h = CandidateHousing::where('candidate_id', $candidate->id)
                ->where('housing_id', $candidate->housing_id)
                ->whereNull('end_at')
                ->orderBy('id', 'DESC')
                ->first();

            if ($last_h) {
                $last_h->end_at = $end_at;
                $last_h->save();
            }
        } else {
            if ($housing_id == $candidate->housing_id) {
                return false;
            }

            $start_at = $start_at ?: Carbon::now()->startOfDay();

            $prev = CandidateHousing::where('candidate_id', $candidate->id)
                ->whereNull('end_at')
                ->orderBy('id', 'DESC')
                ->first();

            if ($prev) {
                $prev->end_at = Carbon::parse($start_at)->subDay();
                $prev->save();
            }

            $new_h = new CandidateHousing;

            $new_h->candidate_id = $candidate->id;
            $new_h->housing_id = $housing_id;
            $new_h->housing_room_id = $housing_room_id;
            $new_h->start_at = $start_at;

            $new_h->save();
        }

        return true;
    }

    public function storeDocuments($documents, $candidate_id)
    {
        foreach ($documents as $doc) {
            $new_item = new CandidateDocument;

            $new_item->user_id = Auth::user()->id;
            $new_item->candidate_id = $candidate_id;
            $new_item->document = json_encode($doc['html']);
            $new_item->title = $doc['title'];

            $new_item->save();
        }
    }

    public function setLegalise($candidate_id, $status)
    {
        $item = Candidate::find($candidate_id);
        $item->is_legal = $status;
        $item->save();

        Task::where('candidate_id', $candidate_id)
            ->whereIn('status', [1, 3])
            ->update(['status' => 2]);

        $trudos = User::where('group_id', 5)->where('activation', 1)->get();

        foreach ($trudos as $trudo) {
            $task = new Task();
            $task->start = Carbon::now();
            $task->end = Carbon::now()->addHours(24);
            $task->autor_id = Auth::user()->id;
            $task->to_user_id = $trudo->id;
            $task->status = 1;
            $task->type = 1;
            $task->title = Task::getTypeTitle($task->type);
            $task->candidate_id = $candidate_id;
            $task->save();
        }
    }
}
