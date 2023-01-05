<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\C_file;
use App\Models\Candidate_arrival;
use App\Models\Task;
use App\Models\User;
use App\Models\Candidate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Controllers\FieldsMutationController;

class CandidateArrivalsController extends Controller
{
    public function getArrivalsIndex()
    {
        $filter_vars = array(
            'countries' => array(),
            'vacancies' => array(),
            'places' => array(),
            'recruiters' => array(),
        );

        $arrivals = Candidate_arrival::with('Place_arrive')
            ->with('Candidate')
            ->with('Candidate.Citizenship')
            ->with('Candidate.Vacancy')
            ->with('Candidate.Recruiter')
            ->get();

        foreach ($arrivals as $ar) {
            if ($ar->Candidate != null && $ar->Candidate->Citizenship != null) {
                $filter_vars['countries'][$ar->Candidate->Citizenship->id] = $ar->Candidate->Citizenship->name;
            }

            if ($ar->Candidate != null && $ar->Candidate->Vacancy != null) {
                $filter_vars['vacancies'][$ar->Candidate->Vacancy->id] = $ar->Candidate->Vacancy->title;
            }

            if ($ar->Place_arrive != null) {
                $filter_vars['places'][$ar->Place_arrive->id] = $ar->Place_arrive->name;
            }

            if ($ar->Candidate != null && $ar->Candidate->Recruiter != null) {
                $filter_vars['recruiters'][$ar->Candidate->Recruiter->id] = $ar->Candidate->Recruiter->firstName . ' ' . $ar->Candidate->Recruiter->lastName;
            }
        }

        return view('arrivals.index', compact('filter_vars'));
    }

    public function getArrivalsAllJson()
    {
        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length");

        //ordering
        $order_col = 'date_arrive';
        $order_direction = 'desc';
        $cols = request('columns');
        $order = request('order');

        if (isset($order[0]['dir'])) {
            $order_direction = $order[0]['dir'];
        }
        if (isset($order[0]['column']) && isset($cols)) {
            $col_number = $order[0]['column'];
            if (isset($cols[$col_number]) && isset($cols[$col_number]['data'])) {
                $data = $cols[$col_number]['data'];
                if ($data == 0) {
                    $order_col = 'date_arrive';
                    $order_direction = 'asc';
                }
            }
        }
        // search
        $active = request('active');
        $status = request('status');
        $country = request('country');
        $recruiter = request('recruiter');
        $vacancy = request('vacancy');
        $place = request('place');
        $search = request('search');
        $period = request('period');

        $filtered_count = $this->prepareGetArrivalsAllJsonRequest($status, $search, $period, $country, $recruiter, $vacancy, $place, $active);

        $filtered_count = $filtered_count->count();

        $users = $this->prepareGetArrivalsAllJsonRequest($status, $search, $period, $country, $recruiter, $vacancy, $place, $active);

        $users = $users->orderBy($order_col, $order_direction);

        $users = $users
            ->with('Place_arrive')
            ->with('Transport')
            ->with('Candidate')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data = [];

        foreach ($users as $u) {
            if (!$u->Candidate) {
                continue;
            }

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

            $info_btn = '';

            if ($u->Candidate->active == 3 || $u->Candidate->active == 5 || $u->Candidate->active == 22) {
                $info_btn = '<button type="button" class="js-show-comment btn btn-sm btn-icon show-info-btn" data-tooltip="' . $u->Candidate->reason_reject . '">
                <span class="svg-icon svg-icon-primary">
                <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 512 512" width="20px" height="20px"><path class="circ" d="M504.1,256C504.1,119,393,7.9,256,7.9C119,7.9,7.9,119,7.9,256C7.9,393,119,504.1,256,504.1C393,504.1,504.1,393,504.1,256z"/><path class="let" d="M323.2 367.5c-1.4-2-4-2.8-6.3-1.7-24.6 11.6-52.5 23.9-58 25-.1-.1-.4-.3-.6-.7-.7-1-1.1-2.3-1.1-4 0-13.9 10.5-56.2 31.2-125.7 17.5-58.4 19.5-70.5 19.5-74.5 0-6.2-2.4-11.4-6.9-15.1-4.3-3.5-10.2-5.3-17.7-5.3-12.5 0-26.9 4.7-44.1 14.5-16.7 9.4-35.4 25.4-55.4 47.5-1.6 1.7-1.7 4.3-.4 6.2 1.3 1.9 3.8 2.6 6 1.8 7-2.9 42.4-17.4 47.6-20.6 4.2-2.6 7.9-4 10.9-4 .1 0 .2 0 .3 0 0 .2.1.5.1.9 0 3-.6 6.7-1.9 10.7-30.1 97.6-44.8 157.5-44.8 183 0 9 2.5 16.2 7.4 21.5 5 5.4 11.8 8.1 20.1 8.1 8.9 0 19.7-3.7 33.1-11.4 12.9-7.4 32.7-23.7 60.4-49.7C324.3 372.2 324.6 369.5 323.2 367.5zM322.2 84.6c-4.9-5-11.2-7.6-18.7-7.6-9.3 0-17.5 3.7-24.2 11-6.6 7.2-9.9 15.9-9.9 26.1 0 8 2.5 14.7 7.3 19.8 4.9 5.2 11.1 7.8 18.5 7.8 9 0 17-3.9 24-11.6 6.9-7.6 10.4-16.4 10.4-26.4C329.6 96 327.1 89.6 322.2 84.6z"/></svg>
                </span>
                </button>';
            }

            if ($u->active == 0) {
                $select_active = $u->Candidate->getCurrentStatus();
            } elseif (
                Auth::user()->isAdmin()
                || Auth::user()->isLogist()
                || Auth::user()->isRecruitmentDirector()
            ) {
                $select_active = '<div class="row flex-nowrap status-actions"><select class="js-select-status form-select form-select-sm form-select-solid" data-action="setCandidateStatus" data-candidate-id="' . $u->Candidate->id . '"> ' . $u->Candidate->getStatusOptions() . ' </select>' . $info_btn . '<button type="button" class="js-add-arrival btn btn-sm btn-icon create-arrival-btn" data-candidate-id="' . $u->Candidate->id . '" title="Добавить приезд">
                <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Scale.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24"/>
                    <path d="M10,14 L5,14 C4.33333333,13.8856181 4,13.5522847 4,13 C4,12.4477153 4.33333333,12.1143819 5,12 L12,12 L12,19 C12,19.6666667 11.6666667,20 11,20 C10.3333333,20 10,19.6666667 10,19 L10,14 Z M15,9 L20,9 C20.6666667,9.11438192 21,9.44771525 21,10 C21,10.5522847 20.6666667,10.8856181 20,11 L13,11 L13,4 C13,3.33333333 13.3333333,3 14,3 C14.6666667,3 15,3.33333333 15,4 L15,9 Z" fill="#000000" fill-rule="nonzero"/>
                    <path d="M3.87867966,18.7071068 L6.70710678,15.8786797 C7.09763107,15.4881554 7.73079605,15.4881554 8.12132034,15.8786797 C8.51184464,16.2692039 8.51184464,16.9023689 8.12132034,17.2928932 L5.29289322,20.1213203 C4.90236893,20.5118446 4.26920395,20.5118446 3.87867966,20.1213203 C3.48815536,19.7307961 3.48815536,19.0976311 3.87867966,18.7071068 Z M16.8786797,5.70710678 L19.7071068,2.87867966 C20.0976311,2.48815536 20.7307961,2.48815536 21.1213203,2.87867966 C21.5118446,3.26920395 21.5118446,3.90236893 21.1213203,4.29289322 L18.2928932,7.12132034 C17.9023689,7.51184464 17.2692039,7.51184464 16.8786797,7.12132034 C16.4881554,6.73079605 16.4881554,6.09763107 16.8786797,5.70710678 Z" fill="#000000" opacity="0.3"/>
                </g>
                </svg><!--end::Svg Icon--></span>
                </button></div>';
            } else {
                $select_active = '<div class="row flex-nowrap status-actions"><select class="js-select-status form-select form-select-sm form-select-solid" data-action="setCandidateStatus" data-candidate-id="' . $u->Candidate->id . '"> ' . $u->Candidate->getStatusOptions() . ' </select>' . $info_btn . '</div>';
            }

            if ($u->date_arrive != null) {
                $date_arrive = Carbon::parse($u->date_arrive)->format('d.m.Y');
                $date_arrive_time = Carbon::parse($u->date_arrive)->format('H:i');
            } else {
                $date_arrive = '';
                $date_arrive_time = '';
            }
            if ($u->Place_arrive != null) {
                $Place_arrive = $u->Place_arrive->name;
            } else {
                $Place_arrive = '';
            }

            if ($u->Candidate != null && $u->Candidate->Citizenship != null) {
                $Citizenship = $u->Candidate->Citizenship->name;
            } else {
                $Citizenship = '';
            }

            if ($u->Candidate != null && $u->Candidate->Vacancy != null) {
                $Vacancy = $u->Candidate->Vacancy->title;
            } else {
                $Vacancy = '';
            }

            if ($u->Candidate != null && $u->Candidate->Recruiter != null) {
                $Recruiter = $u->Candidate->Recruiter->firstName . ' ' . $u->Candidate->Recruiter->lastName;
            } else {
                $Recruiter = '';
            }

            $Transport = '';
            $transportation = '';
            $transportation_id = '';
            if ($u->Transport != null) {
                $Transport = $u->Transport->name;
            } else if ($u->transport_id == 999999 && $u->Transportation) {
                $Transport = 'Регулярные перевозки';
                $transportation = $u->Transportation->title;
                $transportation_id = $u->Transportation->id;

                if ($u->Transportation->ArrivalPlace) {
                    $Place_arrive = $u->Transportation->ArrivalPlace->name;
                    $date_arrive = Carbon::parse($u->Transportation->arrival_date)->format('d.m.Y');
                    $date_arrive_time = '00:00';
                }
            }

            if (Auth::user()->isRecruitmentDirector()) {
                $date_link = '<span style="color: #FF5612">' . $date_arrive . '</span>';
            } else {
                $date_link = '<a href="#" data-comment="' . $u->comment . '" data-place_arrive_name="' . $Place_arrive . '" data-transport_name="' . $Transport . '" data-id="' . $u->id . '" data-date_arrive="' . Carbon::parse($u->date_arrive)->format('d.m.Y H:i') . '" data-transport_id="' . $u->transport_id . '" data-place_arrive_id="' . $u->place_arrive_id . '" data-transportation="' . $transportation . '" data-transportation-id="' . $transportation_id . '" class="js-edit-arrival">' . $date_arrive . '</a>';
            }

            $comment = '';

            if ($u->comment) {
                $comment = '<button type="button" class="js-show-comment btn btn-sm btn-icon comment-btn m-auto" data-tooltip="' . $u->comment . '">
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

            $temp_arr = [
                '<a href="/candidate/add?id=' . $u->Candidate->id . '" style="color: #FF5612">' . $u->id . '</a>',
                mb_strtoupper($u->Candidate->firstName),
                mb_strtoupper($u->Candidate->lastName),
                $Recruiter,
                $u->Candidate->phone,
                $u->Candidate->viber,
                $Citizenship,
                $Vacancy,
                $Place_arrive,
                $Transport . ' ' . $transportation,
                $date_link,
                $date_arrive_time,
                $file,
                $comment,
                $select_active
            ];

            $data[] = $temp_arr;
        }


        return Response::json(array(
            'data' => $data,
            "draw" => $draw,
            'recordsTotal' => $filtered_count,
            'recordsFiltered' => $filtered_count,
        ), 200);
    }

    private function prepareGetArrivalsAllJsonRequest($status, $search, $period, $country, $recruiter, $vacancy, $place, $active)
    {

        $users = Candidate_arrival::where('active', 1);

        if ($status) {
            $cand_ids = Candidate::allowedWithStatus()->whereIn('active', $status)->pluck('id');
            $users = $users->whereIn('candidate_id', $cand_ids);
        }

        if ($search != '') {
            $cand_ids = Candidate::where('removed', false)
                ->where(function ($query) use ($search) {
                    $query->where('firstName', 'LIKE', '%' . $search . '%')
                        ->orWhere('lastName', 'LIKE', '%' . $search . '%')
                        ->orWhere('phone', 'LIKE', '%' . $search . '%')
                        ->orWhere('viber', 'LIKE', '%' . $search . '%')
                        ->orWhere('phone_parent', 'LIKE', '%' . $search . '%');
                })->limit(10)->pluck('id');

            $users = $users->whereIn('candidate_id', $cand_ids);
        }

        if (Auth::user()->isLogist()) {
            $cand_ids = Candidate::allowedWithStatus()->pluck('id');
            $users = $users->whereIn('candidate_id', $cand_ids);
        }

        if (Auth::user()->isTrud()) {
            $cand_ids = Candidate::allowedWithStatus()->pluck('id');
            $users = $users->whereIn('candidate_id', $cand_ids);
        }

        if ($country) {
            $cand_ids = Candidate::allowedWithStatus()->whereIn('citizenship_id', $country)->pluck('id');
            $users = $users->whereIn('candidate_id', $cand_ids);
        }

        if ($vacancy) {
            $cand_ids = Candidate::allowedWithStatus()->whereIn('real_vacancy_id', $vacancy)->pluck('id');
            $users = $users->whereIn('candidate_id', $cand_ids);
        }

        if ($recruiter) {
            $cand_ids = Candidate::allowedWithStatus()->whereIn('recruiter_id', $recruiter)->pluck('id');
            $users = $users->whereIn('candidate_id', $cand_ids);
        }

        if ($place) {
            $users = $users->whereIn('place_arrive_id', $place);
        }

        if ($period) {
            $users = $users
                ->whereDate('date_arrive', '>=', Carbon::createFromFormat('Y-m-d', $period['from']))
                ->whereDate('date_arrive', '<=', Carbon::createFromFormat('Y-m-d', $period['to']));
        }

        return $users;
    }

    public function getArrivalsJson()
    {
        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length"); // Rows display per page

        //ordering
        $order_col = 'id';
        $order_direction = 'desc';
        $cols = request('columns');
        $order = request('order');

        if (isset($order[0]['dir'])) {
            $order_direction = $order[0]['dir'];
        }
        if (isset($order[0]['column']) && isset($cols)) {
            $col_number = $order[0]['column'];
            if (isset($cols[$col_number]) && isset($cols[$col_number]['data'])) {
                $data = $cols[$col_number]['data'];
                if ($data == 0) {
                    $order_col = 'id';
                    $order_direction = 'desc';
                }
            }
        }
        // search
        $status = request('status');
        $search = request('search');
        $candidate_id = request('canddaite_id');

        $filtered_count = $this->prepareGetArrivalsJsonRequest($status, $candidate_id);
        $filtered_count = $filtered_count->count();

        $users = $this->prepareGetArrivalsJsonRequest($status, $candidate_id);
        $users = $users->orderBy($order_col, $order_direction);

        $users = $users
            ->with('Place_arrive')
            ->with('Transport')
            ->with('Transportation')
            ->with('Transportation.ArrivalPlace')
            ->with('Candidate')
            ->with('D_file')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data = [];


        foreach ($users as $u) {

            if ($u->D_file != null) {

                if (config('app.env') === 'local') {
                    $path_url = url('/');
                } else {
                    $path_url = url('/') . '/public';
                }

                $file = '<a   href="javascript:;"><i data-id="' . $u->id . '" id="file_' . $u->id . '"  class="fa fa-pen add_file"></i></a>';
                $file .= '<a target="_blank" href="' . $path_url . $u->D_file->path . '" style="cursor: pointer;" class="svg-icon svg-icon-2x svg-icon-primary me-4">
																<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																	<path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="currentColor"></path>
																	<path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor"></path>
																</svg>
															</a>';
            } else if ($u->active == 1) {
                $file = '<a data-id="' . $u->id . '" id="file_' . $u->id . '" class="add_file" href="javascript:;">загрузить</a>';
            } else {
                $file = '';
            }

            if ($u->active == 0) {
                $select_active = $u->Candidate->getCurrentStatus();
            } elseif (
                Auth::user()->isAdmin()
                || Auth::user()->isLogist()
                || Auth::user()->isRecruitmentDirector()
            ) {
                $select_active = '<div class="row flex-nowrap status-actions"><select class="js-select-status form-select form-select-sm form-select-solid" data-action="setCandidateStatus" data-candidate-id="' . $u->Candidate->id . '"> ' . $u->Candidate->getStatusOptions() . ' </select>
                <button type="button" class="js-add-arrival btn btn-sm btn-icon create-arrival-btn" data-candidate-id="' . $u->Candidate->id . '" title="Добавить приезд">
                <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Scale.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24"/>
                    <path d="M10,14 L5,14 C4.33333333,13.8856181 4,13.5522847 4,13 C4,12.4477153 4.33333333,12.1143819 5,12 L12,12 L12,19 C12,19.6666667 11.6666667,20 11,20 C10.3333333,20 10,19.6666667 10,19 L10,14 Z M15,9 L20,9 C20.6666667,9.11438192 21,9.44771525 21,10 C21,10.5522847 20.6666667,10.8856181 20,11 L13,11 L13,4 C13,3.33333333 13.3333333,3 14,3 C14.6666667,3 15,3.33333333 15,4 L15,9 Z" fill="#000000" fill-rule="nonzero"/>
                    <path d="M3.87867966,18.7071068 L6.70710678,15.8786797 C7.09763107,15.4881554 7.73079605,15.4881554 8.12132034,15.8786797 C8.51184464,16.2692039 8.51184464,16.9023689 8.12132034,17.2928932 L5.29289322,20.1213203 C4.90236893,20.5118446 4.26920395,20.5118446 3.87867966,20.1213203 C3.48815536,19.7307961 3.48815536,19.0976311 3.87867966,18.7071068 Z M16.8786797,5.70710678 L19.7071068,2.87867966 C20.0976311,2.48815536 20.7307961,2.48815536 21.1213203,2.87867966 C21.5118446,3.26920395 21.5118446,3.90236893 21.1213203,4.29289322 L18.2928932,7.12132034 C17.9023689,7.51184464 17.2692039,7.51184464 16.8786797,7.12132034 C16.4881554,6.73079605 16.4881554,6.09763107 16.8786797,5.70710678 Z" fill="#000000" opacity="0.3"/>
                </g>
                </svg><!--end::Svg Icon--></span>
                </button></div>';
            } else {
                $select_active = '<select class="js-select-status form-select form-select-sm form-select-solid" data-action="setCandidateStatus" data-candidate-id="' . $u->Candidate->id . '"> ' . $u->Candidate->getStatusOptions() . ' </select>';
            }

            if ($u->date_arrive != null) {
                $date_arrive = Carbon::parse($u->date_arrive)->format('d.m.Y');
                $date_arrive_time = Carbon::parse($u->date_arrive)->format('H:i');
            } else {
                $date_arrive = '';
                $date_arrive_time = '';
            }
            if ($u->Place_arrive != null) {
                $Place_arrive = $u->Place_arrive->name;
            } else {
                $Place_arrive = '';
            }

            $Transport = '';
            $transportation = '';
            $transportation_id = '';
            if ($u->Transport != null) {
                $Transport = $u->Transport->name;
            } else if ($u->transport_id == 999999 && $u->Transportation) {
                $Transport = 'Регулярные перевозки';
                $transportation = $u->Transportation->title;
                $transportation_id = $u->Transportation->id;

                if ($u->Transportation->ArrivalPlace) {
                    $Place_arrive = $u->Transportation->ArrivalPlace->name;
                    $date_arrive = Carbon::parse($u->Transportation->arrival_date)->format('d.m.Y');
                    $date_arrive_time = '00:00';
                }
            }

            $temp_arr = [
                $u->active == 0 ? '' : '<a href="#" data-comment="' . $u->comment . '" data-place_arrive_name="' . $Place_arrive . '" data-transport_name="' . $Transport . '" data-id="' . $u->id . '" data-date_arrive="' . Carbon::parse($u->date_arrive)->format('d.m.Y H:i') . '" data-transport_id="' . $u->transport_id . '" data-place_arrive_id="' . $u->place_arrive_id . '" data-transportation="' . $transportation . '" data-transportation-id="' . $transportation_id . '" class="js-edit-arrival"><i class="fa fa-pen"></i></a>',
                $u->comment,
                $Place_arrive,
                $date_arrive,
                $date_arrive_time,
                $Transport . ' ' . $transportation,
                $file,
                $select_active

            ];
            $data[] = $temp_arr;
        }


        return Response::json(array(
            'data' => $data,
            "draw" => $draw,
            'recordsTotal' => $filtered_count,
            'recordsFiltered' => $filtered_count,
        ), 200);
    }

    private function prepareGetArrivalsJsonRequest($status, $candidate_id)
    {
        $users = Candidate_arrival::where('candidate_id', $candidate_id);
        return $users;
    }

    public static function postArrivalAdd(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'place_arrive_id' => 'required',
            'transport_id' => 'required',
            'date_arrive' => 'required',
        ], [], [
            'place_arrive_id' => '«Место»',
            'transport_id' => '«Транспорт»',
            'date_arrive' => '«Дата»',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        // if (Carbon::createFromFormat('d.m.Y H:i', $r->date_arrive) <= Carbon::now()->addHours(5)) {
        //     return response(array('success' => "false", 'error' => 'Укажите актуальную дату приезда'), 200);
        // }

        $arrival = null;
        $candidate = null;
        $addNew = true;

        if ($r->id) {
            $arrival = Candidate_arrival::find($r->id);

            if ($arrival) {
                $addNew = false;
            }
        }

        if ($addNew) {
            $candidate = Candidate::find($r->candidate_id);
        } else {
            $candidate = Candidate::find($arrival->candidate_id);
        }

        if (!$candidate) {
            return response(array('success' => "false", 'error' => 'Кандидат не найден'), 200);
        }

        if ($addNew) {
            $arrival = new Candidate_arrival();

            $arrival->active = 1;

            $prev_arrival = Candidate_arrival::where('candidate_id', $candidate->id)->latest()->first();

            if ($prev_arrival) {
                $prev_arrival->active = 0;
                $prev_arrival->save();
            }
        } else {
            FieldsMutationController::addLog($r, $arrival, 'CandidateArrival');
        }

        $arrival->place_arrive_id = $r->place_arrive_id;
        $arrival->transport_id = $r->transport_id;
        $arrival->comment = $r->comment;
        $arrival->date_arrive = Carbon::parse($r->date_arrive);
        $arrival->transportation_id = $r->transportation_id ?: null;
        $arrival->candidate_id = $candidate->id;
        $arrival->save();

        if ($candidate->active == 6 || $candidate->active == 19) {
            $start = Carbon::now();

            if ($arrival->date_arrive >= Carbon::now()->addDays(9)) {
                $type = 14;
                $start = Carbon::parse($arrival->date_arrive)->subDays(9);
                $end = Carbon::parse($arrival->date_arrive)->subDays(9 - 3);
            } elseif ($arrival->date_arrive >= Carbon::now()->addDays(5)) {
                $type = 15;
                $start = Carbon::parse($arrival->date_arrive)->subDays(5);
                $end = Carbon::parse($arrival->date_arrive)->subDays(5 - 3);
            } elseif ($arrival->date_arrive >= Carbon::now()->addDays(1)) {
                $type = 16;
                $start = Carbon::parse($arrival->date_arrive)->subDays(1);
                $end = Carbon::parse($arrival->date_arrive);
            } elseif ($arrival->date_arrive >= Carbon::now()->format('Y-m-d')) {
                $type = 17;
                $start = Carbon::parse($arrival->date_arrive);
                $end = Carbon::parse($arrival->date_arrive)->addDays(1);
            }

            if ($start) {
                Task::where('candidate_id', $candidate->id)
                    ->whereIn('status', [1, 3])
                    ->update(['status' => 2]);

                $logists = User::where('group_id', 4)->where('activation', 1)->get();
                foreach ($logists as $logist) {
                    $task = new Task();
                    $task->start = $start;
                    $task->end = $end;
                    $task->autor_id = Auth::user()->id;
                    $task->to_user_id = $logist->id;
                    $task->status = 1;
                    $task->type = $type;
                    $task->title = Task::getTypeTitle($task->type);
                    $task->candidate_id = $candidate->id;
                    $task->save();
                }
            }
        }

        if ($r->isAddNewCandidate && request()->file('ticket_file')) {
            $r->arrival_id = $arrival->id;
            return self::addTicketDoc($r);
        }

        return response(array('success' => "true"), 200);
    }

    public function postArrivalsActivation(Request $r)
    {

        if (Auth::user()->isTrud()) {
            if ($r->s == 1) {
                return response(array('success' => "false", 'error' => 'У вас нет прав ставить статус в пути'), 200);
            }
        }

        $arrivals = Candidate_arrival::find($r->id);
        if ($arrivals != null) {
            FieldsMutationController::addLog($r, $arrivals, 'CandidateArrival.setStatus');

            Candidate_arrival::where('id', $r->id)->update(['status' => $r->s]);
        }

        return response(array('success' => "true"), 200);
    }

    public static function addTicketDoc(Request $req)
    {
        if ($req->isAddNewCandidate) {
            $ticket_file = request()->file('ticket_file');
            $arrival_id = $req->arrival_id;
        } else {
            $ticket_file = request()->file('file');
            $arrival_id = request()->get('id');
        }

        if ($ticket_file->isValid()) {
            $path = '/uploads/tickets/' . Carbon::now()->format('m.Y') . '/' . $arrival_id . '/files/';
            $name = Str::random(12) . '.' . $ticket_file->getClientOriginalExtension();

            $ticket_file->move(public_path($path), $name);
            $file_link = $path . $name;

            $file = new C_file();
            $file->autor_id = Auth::user()->id;
            $file->user_id = Auth::user()->id;
            $file->type = 6;
            $file->original_name = $ticket_file->getClientOriginalName();
            $file->ext = $ticket_file->getClientOriginalExtension();
            $file->path = $file_link;
            $file->save();

            $arrival = Candidate_arrival::find($arrival_id);
            $arrival->file_id = $file->id;
            $arrival->save();

            return Response::json(array(
                'success' => "true",
                'path' => url('/') . '' . $file_link
            ), 200);
        } else {
            return Response::json(array(
                'success' => "false",
                'error' => 'file not valid!'
            ), 200);
        }
    }

    public function getArrivalsCount(Request $req)
    {
        $arrivals_count = Candidate_arrival::where('candidate_id', $req->candidate_id)->count();

        return response(array('success' => "true", 'count' => $arrivals_count), 200);
    }
}
