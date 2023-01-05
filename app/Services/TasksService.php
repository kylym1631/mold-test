<?php

namespace App\Services;

use App\Models\Candidate;
use Illuminate\Support\Facades\Auth;
use App\Models\Lead;
use App\Models\Role;
use App\Models\User;
use App\Models\Task;
use App\Models\TaskTemplate;
use Carbon\Carbon;

class TasksService
{
    public function prepareGetJsonRequest($filter__status, $period, $search, $group_ids = [], $user_ids = [], $type_ids = [], $all = false)
    {
        if ($all) {
            $items = Task::where(function ($query) use ($filter__status) {
                if ($filter__status) {
                    return $query->whereIn('status', $filter__status);
                }
            });
        } else {
            $items = Task::where('to_user_id', Auth::user()->id)
                ->where(function ($query) use ($filter__status) {
                    if ($filter__status) {
                        return $query->whereIn('status', $filter__status);
                    } else {
                        return $query->whereIn('status', [1, 3]);
                    }
                });
        }

        if (!$all) {
            if ($period) {
                $items = $items->whereDate('start', '>=', Carbon::createFromFormat('Y-m-d', $period['from']))
                    ->whereDate('start', '<=', Carbon::createFromFormat('Y-m-d', $period['to']));
            } else {
                $items = $items->where('start', '<=', Carbon::now());
            }
        }

        if ($group_ids) {
            $items = $items->whereHas('User', function ($q) use ($group_ids) {
                $q->whereIn('group_id', $group_ids);
            });
        }

        if ($user_ids) {
            $items = $items->whereIn('to_user_id', $user_ids);
        }

        if ($type_ids) {
            $items = $items->whereIn('type', $type_ids);
        }

        if ($search != '') {
            $items = $items->where(function ($query) use ($search) {
                $cand_ids = Candidate::where('removed', false)
                    ->where(function ($query) use ($search) {
                        return $query->where('firstName', 'LIKE', '%' . $search . '%')
                            ->orWhere('lastName', 'LIKE', '%' . $search . '%');
                    })->limit(10)->pluck('id');

                $lead_ids = Lead::where(function ($query) use ($search) {
                    return $query->where('name', 'LIKE', '%' . $search . '%');
                })->limit(10)->pluck('id');

                $user_ids = User::where(function ($query) use ($search) {
                    return $query->where('firstName', 'LIKE', '%' . $search . '%')
                        ->orWhere('lastName', 'LIKE', '%' . $search . '%');
                })->limit(10)->pluck('id');

                return $query->where('title', 'LIKE', '%' . $search . '%')
                    ->orWhereIn('candidate_id', $cand_ids)
                    ->orWhereIn('lead_id', $lead_ids)
                    ->orWhereIn('autor_id', $user_ids);
            });
        }

        return $items;
    }

    public function prepareTemplatesJsonRequest($search)
    {
        $items = TaskTemplate::query();

        if ($search != '') {
            $items = $items->where('title', 'LIKE', '%' . $search . '%');
        }

        return $items;
    }

    public function result($tasks, $filter__status = [])
    {
        $data = [];

        if ($tasks) {
            $i = 0;

            foreach ($tasks as $u) {
                if ($u->candidate_id && !$u->Candidate) {
                    continue;
                }

                $Autor = '';
                if ($u->Autor != null) {
                    $Autor = $u->Autor->firstName . ' ' . $u->Autor->lastName;
                }

                $title = $u->title;
                $Person = '';
                $Person_phone = '';
                $Person_viber = '';
                $lead_company = '';
                $model_obj_id = '';
                $model_obj_status = '';
                $model_obj_status_title = '';
                $model_obj_gender = '';
                $count_failed_call = null;
                $count_not_liquidity = null;
                $Vacancy = '';
                $Vacancy_id = '';
                $Client = '';
                $Client_id = '';
                $Recruiter = null;
                $file = null;
                $info_btn = '';
                $comment = '';


                if ($u->Freelancer != null) {
                    $Person = $u->Freelancer->firstName . ' ' . $u->Freelancer->lastName;
                    $model_obj_id = $u->Freelancer->id;
                }

                if ($u->Candidate != null) {
                    $Person = '<a href="' . url('/') . '/candidate/view?id=' . $u->Candidate->id . '">' . mb_strtoupper($u->Candidate->firstName . ' ' . $u->Candidate->lastName) . '</a>';

                    $Person_phone = $u->Candidate->phone;
                    $Person_viber = $u->Candidate->viber;

                    $model_obj_id = $u->Candidate->id;
                    $model_obj_status = $u->Candidate->active;
                    $count_failed_call = $u->Candidate->count_failed_call;
                    $model_obj_status_title = Candidate::getStatusTitle($u->Candidate->active);
                    $model_obj_gender = $u->Candidate->gender;

                    if ($u->Candidate->Recruiter != null) {
                        $Recruiter = mb_strtoupper($u->Candidate->Recruiter->firstName . ' ' . $u->Candidate->Recruiter->lastName);
                    }

                    if ($u->Candidate->D_file != null) {
                        if (config('app.env') === 'local') {
                            $path_url = url('/');
                        } else {
                            $path_url = url('/') . '/public';
                        }

                        $file = '<a href="' . $path_url . $u->Candidate->D_file->path . '" style="cursor: pointer;" class="js-view-doc svg-icon svg-icon-2x svg-icon-primary me-4" data-ext="' . $u->Candidate->D_file->ext . '">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="currentColor"></path>
                                    <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor"></path>
                                </svg>
                            </a>';
                    }

                    if ($u->Candidate->active == 3 || $u->Candidate->active == 5 || $u->Candidate->active == 22) {
                        $info_btn = '<button type="button" class="js-show-comment btn btn-sm btn-icon show-info-btn" data-tooltip="' . $u->Candidate->reason_reject . '">
                            <span class="svg-icon svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 512 512" width="20px" height="20px"><path class="circ" d="M504.1,256C504.1,119,393,7.9,256,7.9C119,7.9,7.9,119,7.9,256C7.9,393,119,504.1,256,504.1C393,504.1,504.1,393,504.1,256z"/><path class="let" d="M323.2 367.5c-1.4-2-4-2.8-6.3-1.7-24.6 11.6-52.5 23.9-58 25-.1-.1-.4-.3-.6-.7-.7-1-1.1-2.3-1.1-4 0-13.9 10.5-56.2 31.2-125.7 17.5-58.4 19.5-70.5 19.5-74.5 0-6.2-2.4-11.4-6.9-15.1-4.3-3.5-10.2-5.3-17.7-5.3-12.5 0-26.9 4.7-44.1 14.5-16.7 9.4-35.4 25.4-55.4 47.5-1.6 1.7-1.7 4.3-.4 6.2 1.3 1.9 3.8 2.6 6 1.8 7-2.9 42.4-17.4 47.6-20.6 4.2-2.6 7.9-4 10.9-4 .1 0 .2 0 .3 0 0 .2.1.5.1.9 0 3-.6 6.7-1.9 10.7-30.1 97.6-44.8 157.5-44.8 183 0 9 2.5 16.2 7.4 21.5 5 5.4 11.8 8.1 20.1 8.1 8.9 0 19.7-3.7 33.1-11.4 12.9-7.4 32.7-23.7 60.4-49.7C324.3 372.2 324.6 369.5 323.2 367.5zM322.2 84.6c-4.9-5-11.2-7.6-18.7-7.6-9.3 0-17.5 3.7-24.2 11-6.6 7.2-9.9 15.9-9.9 26.1 0 8 2.5 14.7 7.3 19.8 4.9 5.2 11.1 7.8 18.5 7.8 9 0 17-3.9 24-11.6 6.9-7.6 10.4-16.4 10.4-26.4C329.6 96 327.1 89.6 322.2 84.6z"/></svg>
                            </span>
                            </button>';
                    }
                }

                if ($u->Lead != null) {
                    if ($u->Lead->name) {
                        $Person = mb_strtoupper($u->Lead->name);
                    } else {
                        $Person = 'Имя лида не указано';
                    }

                    // if ($u->Lead->date) {
                    //     $Person .= '<br>' . Carbon::parse($u->Lead->date)->format('d.m.Y H:i');
                    // }

                    $Person_phone = $u->Lead->phone;
                    $Person_viber = $u->Lead->viber;
                    $lead_company = $u->Lead->company;

                    $model_obj_id = $u->Lead->id;
                    $model_obj_status = $u->Lead->status;
                    $count_failed_call = $u->Lead->count_failed_call;
                    $count_not_liquidity = $u->Lead->count_not_liquidity;
                    $model_obj_status_title = !$u->Lead->active ? 'Архив' : Lead::getStatusTitle($u->Lead->status);

                    if ($u->type == 23 && $u->Lead->status_comment) {
                        $title .= $u->Lead->status_comment;
                    }

                    // $title = '<a href="#" class="js-show-lead-details" data-lead-id="'. $u->Lead->id .'"><span class="svg-icon svg-icon-primary svg-icon-2x"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <polygon points="0 0 24 0 24 24 0 24"/> <path d="M8.2928955,3.20710089 C7.90237121,2.8165766 7.90237121,2.18341162 8.2928955,1.79288733 C8.6834198,1.40236304 9.31658478,1.40236304 9.70710907,1.79288733 L15.7071091,7.79288733 C16.085688,8.17146626 16.0989336,8.7810527 15.7371564,9.17571874 L10.2371564,15.1757187 C9.86396402,15.5828377 9.23139665,15.6103407 8.82427766,15.2371482 C8.41715867,14.8639558 8.38965574,14.2313885 8.76284815,13.8242695 L13.6158645,8.53006986 L8.2928955,3.20710089 Z" fill="currentColor" fill-rule="nonzero" transform="translate(12.000003, 8.499997) scale(-1, -1) rotate(-90.000000) translate(-12.000003, -8.499997) "/> <path d="M6.70710678,19.2071045 C6.31658249,19.5976288 5.68341751,19.5976288 5.29289322,19.2071045 C4.90236893,18.8165802 4.90236893,18.1834152 5.29289322,17.7928909 L11.2928932,11.7928909 C11.6714722,11.414312 12.2810586,11.4010664 12.6757246,11.7628436 L18.6757246,17.2628436 C19.0828436,17.636036 19.1103465,18.2686034 18.7371541,18.6757223 C18.3639617,19.0828413 17.7313944,19.1103443 17.3242754,18.7371519 L12.0300757,13.8841355 L6.70710678,19.2071045 Z" fill="currentColor" fill-rule="nonzero" opacity="0.3" transform="translate(12.000003, 15.499997) scale(-1, -1) rotate(-360.000000) translate(-12.000003, -15.499997) "/> </g> </svg></span>'. $title .'</a>';
                    $title = '<span>' . $title . '<a href="#" class="js-show-lead-details" data-lead-id="' . $u->Lead->id . '" style="opacity: 0"></a></span>';
                }

                if ($u->model_name == 'client' && $u->Client != null) {
                    $Person = '<a href="/client/view?id=' . $u->Client->id . '">' . mb_strtoupper($u->Client->name) . '</a>';
                }

                if ($u->model_name == 'housing' && $u->Housing != null) {
                    $Person = '<a href="/housing/view?id=' . $u->Housing->id . '">' . mb_strtoupper($u->Housing->title . ' ' . $u->Housing->address) . '</a>';
                }

                if ($u->model_name == 'vacancy' && $u->Vacancy != null) {
                    $Person = '<a href="/vacancy/add?id=' . $u->Vacancy->id . '">' . mb_strtoupper($u->Vacancy->title) . '</a>';
                }

                if ($u->model_name == 'car' && $u->Car != null) {
                    $Person = '<a href="/cars/view?id=' . $u->Car->id . '">' . mb_strtoupper($u->Car->brand) . '</a>';
                }

                if (Auth::user()->isTrud() || Auth::user()->isKoordinator()) {
                    if ($u->Candidate && $u->Candidate->Vacancy != null) {
                        $Vacancy = $u->Candidate->Vacancy->title;
                        $Vacancy_id = $u->Candidate->Vacancy->id;
                    }

                    if ($u->Candidate && $u->Candidate->Client != null) {
                        $Client = $u->Candidate->Client->name;
                        $Client_id = $u->Candidate->Client->id;
                    }
                }

                if ($u->type == 10 || $u->type == 12 || $u->type == 23) {
                    $output_start = Carbon::parse($u->start)->format('d.m.Y H:i');
                } else {
                    $output_start = Carbon::parse($u->start)->format('d.m.Y') . ' 08:00';
                }

                $output_end = Carbon::parse($u->end)->format('d.m.Y H:i');

                if ($u->type > 99) {
                    $temp_arr = array(
                        'taskId' => $u->id,
                        'taskType' => $u->type,
                        'taskStatus' => $u->status,
                        'start' => $output_start,
                        'end' => $output_end,
                        'title' => $title,
                        'author' => $Autor,
                        'person' => $Person,
                        'person_phone' => $Person_phone,
                        'person_viber' => $Person_viber,
                        'lead_company' => $lead_company,
                        'model_obj_id' => $model_obj_id,
                        'model_obj_status' => $model_obj_status,
                        'model_obj_status_title' => $model_obj_status_title,
                        'model_obj_gender' => $model_obj_gender,
                        'action' => $u->getCustomAction(),
                        'count_failed_call' => $count_failed_call,
                        'count_not_liquidity' => $count_not_liquidity,
                        'vacancy' => $Vacancy,
                        'vacancy_id' => $Vacancy_id,
                        'client' => $Client,
                        'client_id' => $Client_id,
                        'recruiter' => $Recruiter,
                        'file' => $file,
                        'info_btn' => $info_btn,
                        'comment' => $comment,
                    );
                } else {
                    $temp_arr = array(
                        'taskId' => $u->id,
                        'taskType' => $u->type,
                        'taskStatus' => $u->status,
                        'start' => $output_start,
                        'title' => $title,
                        'author' => $Autor,
                        'person' => $Person,
                        'person_phone' => $Person_phone,
                        'person_viber' => $Person_viber,
                        'lead_company' => $lead_company,
                        'model_obj_id' => $model_obj_id,
                        'model_obj_status' => $model_obj_status,
                        'model_obj_status_title' => $model_obj_status_title,
                        'model_obj_gender' => $model_obj_gender,
                        'action' => $u->getAction($model_obj_status),
                        'count_failed_call' => $count_failed_call,
                        'count_not_liquidity' => $count_not_liquidity,
                        'vacancy' => $Vacancy,
                        'vacancy_id' => $Vacancy_id,
                        'client' => $Client,
                        'client_id' => $Client_id,
                        'recruiter' => $Recruiter,
                        'file' => $file,
                        'info_btn' => $info_btn,
                        'comment' => $comment,
                    );
                }

                $data[] = $temp_arr;

                $i++;

                if (Auth::user()->isRecruiter() && $i >= 1) {
                    if (
                        !$filter__status
                        || ($filter__status && (in_array('1', $filter__status) || in_array('3', $filter__status)))
                    ) {
                        break;
                    }
                }
            }
        }

        return $data;
    }

    public function resultForLogist($tasks)
    {
        $data = [];

        foreach ($tasks as $u) {
            if ($u->candidate_id && !$u->Candidate) {
                continue;
            }

            $file = '';
            $firstName = '';
            $lastName = '';
            $phone = '';
            $viber = '';
            $model_obj_id = '';
            $model_obj_status = '';
            $model_obj_status_title = '';
            $date_arrive = '';
            $date_arrive_time = '';
            $Place_arrive = '';
            $Transport = '';
            $date_link = '';
            $comment = '';
            $info_btn = '';

            if ($u->Candidate_arrival) {
                if ($u->Candidate_arrival->D_file) {
                    if (config('app.env') === 'local') {
                        $path_url = url('/');
                    } else {
                        $path_url = url('/') . '/public';
                    }

                    $file = '<a href="' . $path_url . $u->Candidate_arrival->D_file->path . '" style="cursor: pointer;" class="js-view-doc svg-icon svg-icon-2x svg-icon-primary me-4" data-ext="' . $u->Candidate_arrival->D_file->ext . '">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="currentColor"></path>
                            <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor"></path>
                        </svg>
                    </a>';
                }

                if ($u->Candidate_arrival->date_arrive) {
                    $date_arrive = Carbon::parse($u->Candidate_arrival->date_arrive)->format('d.m.Y');
                    $date_arrive_time = Carbon::parse($u->Candidate_arrival->date_arrive)->format('H:i');
                }

                if ($u->Candidate_arrival->Place_arrive) {
                    $Place_arrive = $u->Candidate_arrival->Place_arrive->name;
                }

                if ($u->Candidate_arrival->Transport) {
                    $Transport = $u->Candidate_arrival->Transport->name;
                }

                $date_link = '<a href="#" data-comment="' . $u->Candidate_arrival->comment . '" data-place_arrive_name="' . $Place_arrive . '" data-transport_name="' . $Transport . '" data-id="' . $u->Candidate_arrival->id . '" data-date_arrive="' . Carbon::parse($u->Candidate_arrival->date_arrive)->format('d.m.Y H:i') . '" data-transport_id="' . $u->Candidate_arrival->transport_id . '" data-place_arrive_id="' . $u->Candidate_arrival->place_arrive_id . '" class="js-edit-arrival">' . $date_arrive . '</a>';

                if ($u->Candidate_arrival->comment) {
                    $comment = '<button type="button" class="js-show-comment btn btn-sm btn-icon create-arrival-btn m-auto" data-tooltip="' . $u->Candidate_arrival->comment . '">
                    <span class="svg-icon svg-icon-primary svg-icon-2x">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"/>
                            <polygon opacity="0.3" points="5 15 3 21.5 9.5 19.5"/>
                            <path d="M13.5,21 C8.25329488,21 4,16.7467051 4,11.5 C4,6.25329488 8.25329488,2 13.5,2 C18.7467051,2 23,6.25329488 23,11.5 C23,16.7467051 18.7467051,21 13.5,21 Z M9,8 C8.44771525,8 8,8.44771525 8,9 C8,9.55228475 8.44771525,10 9,10 L18,10 C18.5522847,10 19,9.55228475 19,9 C19,8.44771525 18.5522847,8 18,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 C8,13.5522847 8.44771525,14 9,14 L14,14 C14.5522847,14 15,13.5522847 15,13 C15,12.4477153 14.5522847,12 14,12 L9,12 Z"/>
                        </g>
                    </svg><!--end::Svg Icon--></span>
                    </button>';
                }
            }

            if ($u->Candidate) {
                $firstName = mb_strtoupper($u->Candidate->firstName);
                $lastName = mb_strtoupper($u->Candidate->lastName);
                $phone = $u->Candidate->phone;
                $viber = $u->Candidate->viber;
                $model_obj_id = $u->Candidate->id;
                $model_obj_status = $u->Candidate->active;
                $model_obj_status_title = Candidate::getStatusTitle($u->Candidate->active);
            }

            $Person = '';

            if ($u->model_name == 'client' && $u->Client != null) {
                $Person = '<a href="/client/view?id=' . $u->Client->id . '">' . mb_strtoupper($u->Client->name) . '</a>';
            }

            if ($u->model_name == 'housing' && $u->Housing != null) {
                $Person = '<a href="/housing/view?id=' . $u->Housing->id . '">' . mb_strtoupper($u->Housing->title . ' ' . $u->Housing->address) . '</a>';
            }

            if ($u->model_name == 'vacancy' && $u->Vacancy != null) {
                $Person = '<a href="/vacancy/add?id=' . $u->Vacancy->id . '">' . mb_strtoupper($u->Vacancy->title) . '</a>';
            }

            if ($u->model_name == 'car' && $u->Car != null) {
                $Person = '<a href="/cars/view?id=' . $u->Car->id . '">' . mb_strtoupper($u->Car->brand) . '</a>';
            }

            if ($u->Candidate && $u->Candidate->Citizenship) {
                $Citizenship = $u->Candidate->Citizenship->name;
            } else {
                $Citizenship = '';
            }

            if ($u->Candidate && $u->Candidate->Vacancy) {
                $Vacancy = $u->Candidate->Vacancy->title;
            } else {
                $Vacancy = '';
            }

            if ($u->Candidate && $u->Candidate->Recruiter) {
                $Recruiter = mb_strtoupper($u->Candidate->Recruiter->firstName . ' ' . $u->Candidate->Recruiter->lastName);
            } else {
                $Recruiter = '';
            }

            if ($u->type == 10 || $u->type == 12 || $u->type == 23) {
                $start = Carbon::parse($u->start)->format('d.m.Y H:i');
            } else {
                $start = Carbon::parse($u->start)->format('d.m.Y') . ' 08:00';
            }

            $output_end = Carbon::parse($u->end)->format('d.m.Y H:i');

            if ($u->type > 99) {
                $temp_arr = array(
                    'taskId' => $u->id,
                    'taskType' => $u->type,
                    'taskStatus' => $u->status,
                    'start' => $start,
                    'end' => $output_end,
                    'title' => $u->title,
                    'person' => $Person,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'recruiter' => $Recruiter,
                    'phone' => $phone,
                    'viber' => $viber,
                    'citizenship' => $Citizenship,
                    'vacancy' => $Vacancy,
                    'place_arrive' => $Place_arrive,
                    'transport' => $Transport,
                    'date_link' => $date_link,
                    'date_arrive_time' => $date_arrive_time,
                    'file' => $file,
                    'comment' => $comment,
                    'model_obj_id' => $model_obj_id,
                    'model_obj_status' => $model_obj_status,
                    'model_obj_status_title' => $model_obj_status_title,
                    'action' => $u->getCustomAction(),
                    'info_btn' => $info_btn,
                );
            } else {
                $temp_arr = array(
                    'taskId' => $u->id,
                    'taskType' => $u->type,
                    'taskStatus' => $u->status,
                    'start' => $start,
                    'title' => $u->title,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'recruiter' => $Recruiter,
                    'phone' => $phone,
                    'viber' => $viber,
                    'citizenship' => $Citizenship,
                    'vacancy' => $Vacancy,
                    'place_arrive' => $Place_arrive,
                    'transport' => $Transport,
                    'date_link' => $date_link,
                    'date_arrive_time' => $date_arrive_time,
                    'file' => $file,
                    'comment' => $comment,
                    'model_obj_id' => $model_obj_id,
                    'model_obj_status' => $model_obj_status,
                    'model_obj_status_title' => $model_obj_status_title,
                    'action' => $u->getAction($model_obj_status),
                    'info_btn' => $info_btn,
                );
            }

            $data[] = $temp_arr;
        }

        return $data;
    }

    public function closeIfLeadsNotExists()
    {
        $items = Task::whereIn('type', [21, 23])
            ->whereIn('status', [1, 3])
            ->get();

        if ($items) {
            $lead_ids = array_map(function ($item) {
                return $item['lead_id'];
            }, $items->toArray());

            $leads_with_id = Lead::whereIn('id', $lead_ids)->pluck('id');
            $leads_with_id = $leads_with_id->toArray();

            $to_close = [];

            foreach ($items as $item) {
                if (!in_array($item->lead_id, $leads_with_id)) {
                    $to_close[] = $item->id;
                }
            }
            print_r($to_close);
            Task::whereIn('id', $to_close)
                ->update(['status' => 2]);
        }
    }

    public function createTasks($req = [])
    {
        $to_user_ids = [];

        if ($req['all_users'] == '1') {
            $roles = Role::whereIn('id', $req['to_user_roles'])
                ->with('Users')
                ->get();

            foreach ($roles as $role) {
                if ($role->Users) {
                    foreach ($role->Users as $user) {
                        $to_user_ids[] = $user->id;
                    }
                }
            }
        } else if ($req['to_user_ids']) {
            $to_user_ids = $req['to_user_ids'];
        }

        if ($to_user_ids) {
            $group = null;

            if ($req['model_obj_id']) {
                $group = Auth::user()->id . '.' . time() . '.' . $req['model_name'] . '.' . $req['model_obj_id'];
            }

            foreach ($to_user_ids as $u_id) {
                $task = new Task;

                $task->start = isset($req['start']) ? $req['start'] : Carbon::now();
                $task->end = $req['end'];
                $task->autor_id = Auth::user()->id;
                $task->to_user_id = $u_id;
                $task->status = 1;
                $task->type = 100;
                $task->title = $req['title'];
                $task->task_template_id = isset($req['task_template_id']) ? $req['task_template_id'] : null;
                $task->task_template_step_id = isset($req['step_id']) ? $req['step_id'] : null;
                $task->task_group = $group;

                if (isset($req['model_name']) && isset($req['model_obj_id'])) {
                    $task->model_name = $req['model_name'];

                    if ($req['model_name'] == 'candidate') {
                        $task->candidate_id = $req['model_obj_id'];
                    } else if ($req['model_name'] == 'lead') {
                        $task->lead_id = $req['model_obj_id'];
                    } else {
                        $task->model_obj_id = $req['model_obj_id'] ?: null;
                    }
                }

                $task->save();
            }
        }
    }

    public function createNextTaskFromTemplate($task_id)
    {
        $cur_task = Task::find($task_id);

        if (!$cur_task->task_template_id) {
            return null;
        }

        $tpl = TaskTemplate::find($cur_task->task_template_id);

        if (!$tpl) {
            return ['error' => 'Шаблон задач не найден'];
        }

        $scheme = json_decode($tpl->scheme);

        $next_task_data = null;

        if (!$cur_task->task_template_step_id) {
            $next_task_data = $scheme[0];
        } else {
            $cur_sch_index = null;

            foreach ($scheme as $ind => $sch) {
                if ($cur_task->task_template_step_id == $sch->step_id) {
                    $cur_sch_index = $ind;
                }
            }

            if ($cur_sch_index !== null && isset($scheme[$cur_sch_index + 1])) {
                $next_task_data = $scheme[$cur_sch_index + 1];
            }
        }

        if ($next_task_data) {
            $next_task_data = json_decode(json_encode($next_task_data), true);

            $next_task_data['task_template_id'] = $cur_task->task_template_id;

            $next_task_data['start'] = Carbon::now()->addMinutes($next_task_data['start_delay'])->format('Y-m-d H:i');

            $next_task_data['end'] = Carbon::parse($next_task_data['start'])->addMinutes($next_task_data['end_delay'])->format('Y-m-d H:i');

            $this->createTasks($next_task_data);
        }
    }
}
