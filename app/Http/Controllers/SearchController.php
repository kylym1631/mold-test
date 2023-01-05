<?php

namespace App\Http\Controllers;


use App\Models\Client;
use App\Models\Client_position;
use App\Models\User;
use App\Models\Candidate;
use App\Models\Candidate_client;
use App\Models\Car;
use App\Models\Handbook_category;
use App\Models\Handbook_client;
use App\Models\Handbook;
use App\Models\Vacancy;
use App\Models\Vacancy_client;
use App\Models\Lead;
use App\Models\LeadSetting;
use App\Models\Housing;
use App\Models\Housing_room;
use App\Models\Transportation;
use App\Services\CandidatesService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function getAjaxVacancyClients()
    {
        $search = request('f_search');

        $Course = Client::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxVacancyNationality()
    {
        $search = request('f_search');

        $ids = Handbook_client::whereIn('client_id', explode(',', request('client_id')))->pluck('handbook_id');
        $Course = Handbook::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->where('handbook_category_id', 2)
            ->whereIn('id', $ids)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxVacancyWorkplace()
    {
        $search = request('f_search');
        $ids = Handbook_client::whereIn('client_id', explode(',', request('client_id')))->pluck('handbook_id');

        $Course = Handbook::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->where('handbook_category_id', 3)
            ->whereIn('id', $ids)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxVacancyIndustry()
    {
        $search = request('f_search');
        $ids = Handbook_client::whereIn('client_id', explode(',', request('client_id')))->pluck('handbook_id');

        $Course = Handbook::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->where('handbook_category_id', 1)
            ->whereIn('id', $ids)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxClientIndustry()
    {
        $search = request('f_search');

        $Course = Handbook::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->where('handbook_category_id', 1)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxVacancyDocs()
    {
        $search = request('f_search');

        $Course = Handbook::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->where('handbook_category_id', 4)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxClientWorkplace()
    {
        $search = request('f_search');

        $Course = Handbook::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->where('handbook_category_id', 3)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxClientCoordinator()
    {
        $search = request('f_search');

        $Course = User::where(function ($query) use ($search) {
            $query->where('firstName', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('lastName', 'LIKE', '%' . $search . '%')
                ->orWhere('phone', 'LIKE', '%' . $search . '%');
        })->where('group_id', 6)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->firstName . ' ' . $c->lastName;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxCandidateVacancy()
    {
        $search = request('f_search');
        $view = request('view');
        $gender = request('gender');
        // $nacionality_id = request('nacionality_id');
        $client_id = request('client_id');

        if ((!$gender || $gender == 'null') && ($view == 'candidate.add' || $view == 'tasks')) {
            return response(array('success' => "false", 'error' => 'Укажите пол кандидата', 'code' => 'GENDER_NONE'), 200);
        }

        // if (Auth::user()->isAdmin() || Auth::user()->isTrud()) {
        //     if ((!$nacionality_id || $nacionality_id == 'null') && $view == 'candidate.add') {
        //         return response(array('success' => "false", 'error' => 'Укажите национальность кандидата'), 200);
        //     }
        // }

        $vac_ids = [];
        if ($client_id) {
            $vac_ids = Vacancy_client::where('client_id', $client_id)->pluck('vacancy_id');
        }

        if ($view == 'filter' && (Auth::user()->isAdmin() || Auth::user()->isRecruitmentDirector())) {
            $items = Vacancy::whereIn('activation', [1, 2, 3, 4]);
        } else {
            $items = Vacancy::where('activation', 1);
        }

        if ($vac_ids) {
            $items = $items->whereIn('id', $vac_ids);
        }

        if ($search != '') {
            $items = $items->where('title', 'LIKE', '%' . $search . '%');
        }

        $items = $items
            ->with('Candidates')
            // ->with('h_v_nacionality')
            ->with('Vacancy_client')
            ->with('Vacancy_client.Client.h_v_nationality')
            ->get();

        $filled_ids = [];
        $disabled_ids = [];

        if ($items) {
            foreach ($items as $vacancy) {
                if ($vacancy->Candidates && ($view == 'candidate.add' || $view == 'tasks')) {
                    $vacancy->filled_men = 0;
                    $vacancy->filled_women = 0;
                    $vacancy->filled_people = 0;

                    foreach ($vacancy->Candidates as $Candidate) {
                        if ($Candidate->gender == 'm') {
                            $vacancy->filled_men++;
                        } elseif ($Candidate->gender == 'f') {
                            $vacancy->filled_women++;
                        } else {
                            $vacancy->filled_it++;
                        }
                    }

                    if ($gender == 'm') {
                        $count_people = $vacancy->count_people - $vacancy->filled_it;

                        if ($vacancy->filled_women > $vacancy->count_women) {
                            $count_people = $count_people - ($vacancy->filled_women - $vacancy->count_women);
                        }

                        if ($count_people < 0) {
                            $count_people = 0;
                        }

                        if ($vacancy->filled_men >= $vacancy->count_men + $count_people) {
                            $filled_ids[] = $vacancy->id;
                        }
                    }

                    if ($gender == 'f') {
                        $count_people = $vacancy->count_people - $vacancy->filled_it;

                        if ($vacancy->filled_men > $vacancy->count_men) {
                            $count_people = $count_people - ($vacancy->filled_men - $vacancy->count_men);
                        }

                        if ($count_people < 0) {
                            $count_people = 0;
                        }

                        if ($vacancy->filled_women >= $vacancy->count_women + $count_people) {
                            $filled_ids[] = $vacancy->id;
                        }
                    }
                }

                // if (Auth::user()->isAdmin() || Auth::user()->isTrud()) {
                //     $available_nanacionality_ids = [];

                //     if ($vacancy->h_v_nacionality) {
                //         foreach ($vacancy->h_v_nacionality as $h_v_nacionality) {
                //             $available_nanacionality_ids[] = $h_v_nacionality->handbook_id;
                //         }
                //     }

                //     if ($vacancy->Vacancy_client) {
                //         foreach ($vacancy->Vacancy_client as $Vacancy_client) {
                //             if ($Vacancy_client->Client->h_v_nationality) {
                //                 foreach ($Vacancy_client->Client->h_v_nationality as $h_v_nationality) {
                //                     $available_nanacionality_ids[] = $h_v_nacionality->handbook_id;
                //                 }
                //             }
                //         }
                //     }

                //     if (!in_array($nacionality_id, $available_nanacionality_ids)) {
                //         $disabled_ids[] = $vacancy->id;
                //     }
                // }
            }
        }

        $p_temp = [];

        if (count($items)) {
            foreach ($items as $m) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $m->title;
                $p_temp_arr['id'] = $m->id;
                $p_temp_arr['filled'] = in_array($m->id, $filled_ids);
                $p_temp_arr['disabled'] = in_array($m->id, $disabled_ids);
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($items)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxClientNationality()
    {
        $search = request('f_search');


        $Course = Handbook::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->where('handbook_category_id', 2)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxClientCitizenship()
    {
        $search = request('f_search');


        $Course = Handbook::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->where('handbook_category_id', 10)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxClientNacionality()
    {
        $search = request('f_search');


        $Course = Handbook::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->where('handbook_category_id', 2)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxClientCountry()
    {
        $search = request('f_search');


        $Course = Handbook::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->where('handbook_category_id', 5)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxClientTypedocs()
    {
        $search = request('f_search');


        $Course = Handbook::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->where('handbook_category_id', 6)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxClientPlacearrive()
    {
        $search = request('f_search');


        $Course = Handbook::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->where('handbook_category_id', 8)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxClientRealstatuswork()
    {
        $search = request('f_search');


        $Course = Handbook::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->where('handbook_category_id', 9)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxClientTransport()
    {
        $search = request('f_search');


        $Course = Handbook::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->where('handbook_category_id', 7)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }

            $p_temp_arr = [];
            $p_temp_arr['value'] = 'Регулярные перевозки';
            $p_temp_arr['id'] = 999999;
            $p_temp[] = $p_temp_arr;
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxCandidateRecruter()
    {
        $search = request('f_search');

        $Course = User::where(function ($query) use ($search) {
            $query->where('firstName', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('lastName', 'LIKE', '%' . $search . '%')
                ->orWhere('phone', 'LIKE', '%' . $search . '%');
        })->where('group_id', 2)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->firstName . ' ' . $c->lastName;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxLeadsRecruiters()
    {
        $user_ids = Lead::whereNotNull('user_id')->pluck('user_id');
        $users = User::whereIn('id', $user_ids)->get();

        $data = array();

        foreach ($users as $u) {
            $data[] = array(
                'value' => $u->firstName . ' ' . $u->lastName,
                'id' => $u->id,
            );
        }

        if ($data) {
            return response($data, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getLeadsCompanyJson()
    {
        $items = Lead::pluck('company');

        $data = [];
        $is_set = [];

        foreach ($items as $m) {
            if ($m == '' || $m == null) {
                $m = 'Без компании';
            }

            $m = trim($m);

            if (!in_array($m, $is_set)) {
                $is_set[] = $m;

                $data[] = [
                    'value' => $m,
                    'id' => $m,
                ];
            }
        }

        if ($data) {
            return response($data, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getLeadsSettingsJson()
    {
        $items = LeadSetting::all();

        $data = [];

        foreach ($items as $m) {
            $data[] = [
                'value' => $m->name,
                'id' => $m->id,
            ];
        }

        if ($data) {
            return response($data, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxCandidateClient()
    {
        $search = request('f_search');

        $ids = Vacancy_client::where('vacancy_id', request('vacancy_id'))->pluck('client_id');

        $Course = Client::where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        })->whereIn('id', $ids)
            ->where('active', 1)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxCandidatesClients(CandidatesService $cs)
    {
        $search = request('f_search');

        $candidates_query = $cs->prepareGetJsonRequest(Candidate::allowedWithStatus());
        $ids = $candidates_query->pluck('client_id');

        $Course = Client::where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        })->whereIn('id', $ids)
            ->where('active', 1)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxCandidatesKoordinators(CandidatesService $cs)
    {
        $search = request('f_search');

        $candidates_query = $cs->prepareGetJsonRequest(Candidate::allowedWithStatus());
        $ids = $candidates_query->pluck('client_id');

        $koordinators_ids = Client::whereIn('id', $ids)->where('active', 1)->pluck('coordinator_id');

        $Course = User::where('group_id', 6)->whereIn('id', $koordinators_ids)->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = mb_strtoupper($c->firstName . ' ' . $c->lastName);
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxCandidateClientPosition(Request $req)
    {
        $Course = [];

        if ($req->client_id) {
            $Course = Client_position::where('client_id', $req->client_id)->get();
        } elseif ($req->candidate_id) {
            $cand = Candidate::find($req->candidate_id);

            if ($cand) {
                $Course = Client_position::where('client_id', $cand->client_id)->get();
            }
        }

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->title;
                $p_temp_arr['id'] = $c->id;
                $p_temp_arr['data'] = $c->toArray();
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }


    public function getAjaxCandidateCoordinatorsClient()
    {
        $search = request('f_search');

        $Course = Client::where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        })->where('coordinator_id', Auth::user()->id)
            ->where('active', 1)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxCandidateFreelacnsers()
    {
        $search = request('f_search');

        $Course = User::where(function ($query) use ($search) {
            $query->where('firstName', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('lastName', 'LIKE', '%' . $search . '%')
                ->orWhere('phone', 'LIKE', '%' . $search . '%');
        })->where('group_id', 3)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->firstName . ' ' . $c->lastName;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxCity()
    {
        $search = request('f_search');

        $Course = Handbook::where('active', 1)
            ->where('handbook_category_id', 3);

        if ($search) {
            $Course = $Course->where('name', 'LIKE', '%' . $search . '%');
        }

        $Course = $Course->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getAjaxCandidates()
    {
        $search = request('f_search');

        $items = Candidate::where('removed', false);

        if ($search) {
            $items = $items->where(function ($q) use ($search) {
                $q->where('firstName', 'LIKE', '%' . $search . '%')
                    ->orWhere('firstName', 'LIKE', '%' . $search . '%');
            });
        }

        $items = $items->get();

        $p_temp = [];

        if (count($items)) {
            foreach ($items as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->firstName . ' ' . $c->lastName;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($items)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getHousingJson()
    {
        $search = request('f_search');

        $housing = Housing::where('active', 1);

        if (Auth::user()->isCoordinator()) {
            $housing = $housing->whereHas('Housing_client.Client', function ($q) {
                $q->where('coordinator_id', Auth::user()->id);
            });
        }

        if ($search) {
            $housing = $housing->where('title', 'LIKE', '%' . $search . '%');
        }

        $housing = $housing->get();

        $p_temp = [];

        if (count($housing)) {
            foreach ($housing as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->title . ' ' . $c->address;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($housing)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getHousingRoomJson()
    {
        $search = request('f_search');
        $h_id = request('s');

        $items = Housing_room::where('housing_id', $h_id);

        if ($search) {
            $items = $items->where('number', 'LIKE', '%' . $search . '%');
        }

        $items = $items
            ->with('Candidates')
            ->get();

        $p_temp = [];

        if (count($items)) {
            foreach ($items as $c) {
                if (!$c->Candidates || count($c->Candidates) < $c->places_count) {
                    $p_temp_arr = [];
                    $p_temp_arr['value'] = $c->number;
                    $p_temp_arr['id'] = $c->id;
                    $p_temp[] = $p_temp_arr;
                }
            }
        }

        if (count($items)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getClientJson()
    {
        $search = request('f_search');
        $h_id = request('s');

        $items = Client::where('active', 1);

        if ($search) {
            $items = $items->where('number', 'LIKE', '%' . $search . '%');
        }

        if (Auth::user()->isKoordinator()) {
            $items = $items->where('coordinator_id', Auth::user()->id);
        }

        $items = $items->get();

        $p_temp = [];

        if (count($items)) {
            foreach ($items as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($items)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getLeadsJson()
    {
        $search = request('f_search');

        $items = Lead::where('active', 1);

        if ($search) {
            $items = $items->where('name', 'LIKE', '%' . $search . '%');
        }

        $items = $items->get();

        $p_temp = [];

        if (count($items)) {
            foreach ($items as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($items)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getVacancyJson()
    {
        $search = request('f_search');

        $items = Vacancy::where('activation', 1);

        if ($search) {
            $items = $items->where('title', 'LIKE', '%' . $search . '%');
        }

        $items = $items->get();

        $p_temp = [];

        if (count($items)) {
            foreach ($items as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->title;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($items)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getCarJson()
    {
        $search = request('f_search');

        $items = Car::where('active', 1);

        if ($search) {
            $items = $items->where('brand', 'LIKE', '%' . $search . '%');
        }

        $items = $items->get();

        $p_temp = [];

        if (count($items)) {
            foreach ($items as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->brand;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($items)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getTransportationsJson()
    {
        $search = request('f_search');
        $items = Transportation::where('active', 1)
            ->where('departure_date', '>=', Carbon::now());

        if ($search) {
            $items = $items->where('title', 'LIKE', '%' . $search . '%');
        }

        $items = $items->get();

        $p_temp = [];

        if (count($items)) {
            foreach ($items as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->title;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($items)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getUserJson(Request $req, $role)
    {
        $search = request('f_search');

        $items = User::where('activation', 1);

        if ($role != 'all') {
            $items = $items->where('group_id', $role);
        }

        if ($search != '') {
            $items = $items->where(function ($query) use ($search) {
                $query->where('firstName', 'LIKE', '%' . $search . '%')
                    ->orWhere('lastName', 'LIKE', '%' . $search . '%');
            });
        }

        $items = $items->get();

        $p_temp = [];

        if (count($items)) {
            foreach ($items as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = mb_strtoupper($c->firstName . ' ' . $c->lastName);
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($items)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getFieldsMutationUsersJson(Request $req, $candidate_id)
    {
        $items = User::whereHas('FieldsMutations', function ($q) use ($candidate_id) {
            $q->where('model_obj_id', $candidate_id);
        })->get();

        $p_temp = [];

        if (count($items)) {
            foreach ($items as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = mb_strtoupper($c->firstName . ' ' . $c->lastName);
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($items)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getCarsUsersJson(Request $req)
    {
        $items = User::whereHas('Cars')->get();

        $p_temp = [];

        if (count($items)) {
            foreach ($items as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = mb_strtoupper($c->firstName . ' ' . $c->lastName);
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($items)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }

    public function getSpecialityJson()
    {
        $search = request('f_search');

        $Course = Handbook::where('name', 'LIKE', '%' . $search . '%')
            ->where('active', 1)
            ->where('handbook_category_id', 13)
            ->get();

        $p_temp = [];

        if (count($Course)) {
            foreach ($Course as $c) {
                $p_temp_arr = [];
                $p_temp_arr['value'] = $c->name;
                $p_temp_arr['id'] = $c->id;
                $p_temp[] = $p_temp_arr;
            }
        }

        if (count($Course)) {
            return response($p_temp, 200);
        } else {
            return response(array('success' => "false"), 200);
        }
    }
}
