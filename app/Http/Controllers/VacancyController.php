<?php

namespace App\Http\Controllers;

use App\Models\C_file;
use App\Models\Vacancy_client;
use App\Models\Handbook;
use App\Models\Handbook_vacancy;
use App\Models\User;
use App\Models\Vacancy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Database\Eloquent\Builder;
use App\Services\VacanciesService;

class VacancyController extends Controller
{
    public function getIndex()
    {
        $industries = Handbook::where('handbook_category_id', 1)->where('active', 1)->get();
        $cities = Handbook::where('handbook_category_id', 3)->where('active', 1)->get();
        return view('vacancies.index')
            ->with('cities', $cities)
            ->with('industries', $industries);
    }

    public function getAdd(Request $r, VacanciesService $vs)
    {
        if ($r->has('id')) {
            $h_v_industry = [];
            $h_v_nacionality = [];
            $h_v_city = [];
            $Clients = [];
            $Doc = [];

            $vacancy = Vacancy::where('id', $r->id)
                ->with('h_v_industry')
                ->with('h_v_industry.Handbooks')
                ->with('h_v_nacionality')
                ->with('h_v_nacionality.Handbooks')
                ->with('h_v_city')
                ->with('h_v_city.Handbooks')
                ->with('Vacancy_client')
                ->with('Vacancy_client.Client')
                ->with('Doc')
                ->with('Candidates')
                ->first();

            foreach ($vacancy->h_v_industry as $industry) {
                if ($industry->Handbooks != null) {
                    $h_v_industry[] = [$industry->Handbooks->id, $industry->Handbooks->name];
                }
            }
            foreach ($vacancy->h_v_nacionality as $industry) {
                if ($industry->Handbooks != null) {
                    $h_v_nacionality[] = [$industry->Handbooks->id, $industry->Handbooks->name];
                }
            }
            foreach ($vacancy->h_v_city as $industry) {
                if ($industry->Handbooks != null) {
                    $h_v_city[] = [$industry->Handbooks->id, $industry->Handbooks->name];
                }
            }
            foreach ($vacancy->Vacancy_client as $industry) {
                if ($industry->Client != null) {
                    $Clients[] = [$industry->Client->id, $industry->Client->name];
                }
            }


            if ($vacancy->Doc != null) {
                $Doc = [$vacancy->Doc->id, $vacancy->Doc->name];
            }

            $filling = $vs->filling($vacancy->id);
        } else {
            $vacancy = null;
            $h_v_industry = null;
            $h_v_nacionality = null;
            $h_v_city = null;
            $Clients = null;
            $Doc = null;
            $filling = null;
        }



        return view('vacancies.add')
            ->with('h_v_city', $h_v_city)
            ->with('h_v_nacionality', $h_v_nacionality)
            ->with('h_v_industry', $h_v_industry)
            ->with('Clients', $Clients)
            ->with('Doc', $Doc)
            ->with('vacancy', $vacancy)
            ->with('filling', $filling);
    }

    public function filesAdd()
    {

        $vacancy_id = request()->get('vacancy_id');
        if ($vacancy_id == '') {
            $vacancy = new Vacancy();
            $vacancy->title = '';
            $vacancy->user_id = Auth::user()->id;
            $vacancy->save();
            $vacancy_id = $vacancy->id;
        }

        $file = request()->file('file');
        if ($file->isValid()) {

            $path = '/uploads/vacancies/' . Carbon::now()->format('m.Y') . '/' . $vacancy_id . '/files/';
            $name = Str::random(12) . '.' . $file->getClientOriginalExtension();

            $file->move(public_path($path), $name);
            $file_link = $path . $name;


            $file = new C_file();
            $file->autor_id = Auth::user()->id;
            $file->vacancy_id = $vacancy_id;
            $file->user_id = Auth::user()->id;
            $file->type = 2;
            $file->original_name = request()->file('file')->getClientOriginalName();
            $file->ext = request()->file('file')->getClientOriginalExtension();
            $file->path = $file_link;
            $file->save();

            return Response::json(array(
                'success' => "true",
                'vacancy_id' => $vacancy_id,
                'path' => url('/') . '' . $file_link
            ), 200);
        } else {
            return Response::json(array(
                'success' => "false",
                'error' => 'file not valid!'
            ), 200);
        }
    }

    public function postAdd(Request $r)
    {

        $niceNames = [
            'title' => '«название»',
            'description' => '«описание»',
            'client_id' => '«клиент»',
            'deadline_from' => '«дедлайн с»',
            'deadline_to' => '«дедлайн по»',
            'salary' => '«ставка»',
            'salary_description' => '«ставка описание»',
            'count_hours' => '«часов»',
            'doc_id' => '«документ»',
            'housing_cost' => '«стоимость жилья»',
            'housing_description' => '«описание жилья»',
            'housing_people' => '«кол-во людей в комнате»',
            'industry_id' => '«отрасль»',
            'nationality_id' => '«национальность»',
            'work_place_id' => '«место работы»',
        ];
        $validator = Validator::make($r->all(), [
            'title' => 'required',
            'description' => 'required',
            'client_id' => 'required',
            'deadline_from' => 'required',
            'deadline_to' => 'required',
            'salary' => 'required|numeric',
            'salary_description' => 'required',
            'count_hours' => 'required|numeric',
            'doc_id' => 'required',
            'housing_cost' => 'required|numeric',
            'housing_people' => 'required',
            'housing_description' => 'required',
            'industry_id' => 'required',
            'nationality_id' => 'required',
            'work_place_id' => 'required',
        ], [], $niceNames);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        $vacancy = Vacancy::find($r->id);
        if ($vacancy == null) {
            $vacancy = new Vacancy();
            $vacancy->activation = 1;
        }


        $vacancy->title = $r->title;
        $vacancy->description = $r->description;
        $vacancy->count_men = $r->count_men ?: 0;
        $vacancy->count_women = $r->count_women ?: 0;
        $vacancy->count_people = $r->count_people ?: 0;
        $vacancy->salary = $r->salary;
        $vacancy->salary_description = $r->salary_description;
        $vacancy->count_hours = $r->count_hours;
        $vacancy->doc_id = $r->doc_id;
        $vacancy->housing_cost = $r->housing_cost;
        $vacancy->housing_people = $r->housing_people;
        $vacancy->housing_description = $r->housing_description;
        $vacancy->user_id = Auth::user()->id;
        $vacancy->deadline_from = Carbon::createFromFormat('d.m.Y', $r->deadline_from);
        $vacancy->deadline_to = Carbon::createFromFormat('d.m.Y', $r->deadline_to);

        if (!Auth::user()->isRecruiter()) {
            $vacancy->recruting_cost = $r->recruting_cost;
            $vacancy->cost_pay_lead = $r->cost_pay_lead;
        } else {
            if ($vacancy->recruting_cost == '' || $vacancy->cost_pay_lead == '') {
                return response(array('success' => "false", 'error' => 'Заполните стоимость'), 200);
            }
        }

        $vacancy->save();


        Handbook_vacancy::where('vacancy_id', $vacancy->id)->delete();
        $arrs = explode(',', $r->industry_id);
        foreach ($arrs as $arr) {
            $Hand = new Handbook_vacancy();
            $Hand->vacancy_id = $vacancy->id;
            $Hand->handbook_id = $arr;
            $Hand->handbook_category_id = 1;
            $Hand->save();
        }
        $arrs = explode(',', $r->nationality_id);
        foreach ($arrs as $arr) {
            $Hand = new Handbook_vacancy();
            $Hand->vacancy_id = $vacancy->id;
            $Hand->handbook_id = $arr;
            $Hand->handbook_category_id = 2;
            $Hand->save();
        }
        $arrs = explode(',', $r->work_place_id);
        foreach ($arrs as $arr) {
            $Hand = new Handbook_vacancy();
            $Hand->vacancy_id = $vacancy->id;
            $Hand->handbook_id = $arr;
            $Hand->handbook_category_id = 3;
            $Hand->save();
        }

        Vacancy_client::where('vacancy_id', $vacancy->id)->delete();
        $arrs = explode(',', $r->client_id);
        foreach ($arrs as $arr) {
            $Hand = new Vacancy_client();
            $Hand->vacancy_id = $vacancy->id;
            $Hand->client_id = $arr;
            $Hand->save();
        }

        self::pauseIfFilled();

        return Response::json(array('success' => "true", 200));
    }

    public function getJson(VacanciesService $vs)
    {

        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length"); // Rows display per page

        $filter__status = request('status');
        $filter__industry = request('industry');
        $filter__city = request('city');
        $filter__genre = request('genre');
        $search = request('search');

        $filtered_count = $this->prepareGetJsonRequest($filter__status, $filter__industry, $filter__city, $filter__genre, $search);
        $filtered_count = $filtered_count->count();

        $users = $this->prepareGetJsonRequest($filter__status, $filter__industry, $filter__city, $filter__genre, $search);

        $users = $users
            ->with('h_v_industry')
            ->with('h_v_city')
            ->with('h_v_industry.Handbooks')
            ->with('Candidates');

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

        $users = $users
            ->skip($start)
            ->take($rowperpage)
            ->get();


        $data = [];

        foreach ($users as $u) {

            $h_v_industry = '';
            foreach ($u->h_v_industry as $one) {
                if ($one->Handbooks != null) {
                    $h_v_industry .= $one->Handbooks->name . ' ';
                }
            }

            if ($u->activation == 1) {
                $select_active = '<select onchange="changeActivation(' . $u->id . ')"
                                    class="form-select form-select-sm form-select-solid changeActivation' . $u->id . '">
                                <option selected value="1">Активный</option>
                                <option value="2">Пауза</option>
                                <option value="3">Завершена</option>
                                <option value="4">Удалена</option>
                            </select>';
            } else if ($u->activation == 2) {
                $select_active = '<select onchange="changeActivation(' . $u->id . ')"
                                    class="form-select form-select-sm form-select-solid changeActivation' . $u->id . '">
                                 <option value="1">Активный</option>
                                <option selected value="2">Пауза</option>
                                <option value="3">Завершена</option>
                                <option value="4">Удалена</option>
                            </select>';
            } else if ($u->activation == 3) {
                $select_active = '<select onchange="changeActivation(' . $u->id . ')"
                                    class="form-select form-select-sm form-select-solid changeActivation' . $u->id . '">
                                 <option value="1">Активный</option>
                                <option   value="2">Пауза</option>
                                <option selected value="3">Завершена</option>
                                <option value="4">Удалена</option>
                            </select>';
            } else if ($u->activation == 4) {
                $select_active = '<select onchange="changeActivation(' . $u->id . ')"
                                    class="form-select form-select-sm form-select-solid changeActivation' . $u->id . '">
                                 <option value="1">Активный</option>
                                <option   value="2">Пауза</option>
                                <option   value="3">Завершена</option>
                                <option selected value="4">Удалена</option>
                            </select>';
            }

            // $recruting_cost = '<input  onchange="changeCost(' . $u->id . ')" class="w-55px changeCost' . $u->id . '" value="' . $u->recruting_cost . '" style="border: none;" type="text">';
            // $recruting_cost_pay_lead = '<input  onchange="changeCost_pay_lead(' . $u->id . ')" class="w-55px changeCost_pay_lead' . $u->id . '" value="' . $u->cost_pay_lead . '" style="border: none;" type="text">';
            // $recruting_housing_cost = '<input  onchange="change_housing_cost(' . $u->id . ')" class="w-45px change_housing_cost' . $u->id . '" value="' . $u->housing_cost . '" style="border: none;" type="text">';
            // $recruting_count_men = '<input  onchange="change_count_men(' . $u->id . ')" class="w-30px change_count_men' . $u->id . '" value="' . $u->count_men . '" style="border: none;" type="text">';
            // $recruting_count_women = '<input  onchange="change_count_women(' . $u->id . ')" class="w-30px change_count_women' . $u->id . '" value="' . $u->count_women . '" style="border: none;" type="text">';
            // $recruting_count_people = '<input  onchange="change_count_people(' . $u->id . ')" class="w-30px change_count_people' . $u->id . '" value="' . $u->count_people . '" style="border: none;" type="text">';
            // $recruting_salary = '<input  onchange="change_salary(' . $u->id . ')" class="w-45px change_salary' . $u->id . '" value="' . $u->salary . '" style="border: none;" type="text">';

            $cur_count_men = 0;
            $cur_count_women = 0;
            $cur_count_it = 0;

            $filling = $vs->filling($u->id);

            if ($filling) {
                $cur_count_men = $filling['men'];
                $cur_count_women = $filling['women'];
                $cur_count_it = $filling['it'];
            }

            $recruting_cost = $u->recruting_cost;
            $recruting_cost_pay_lead = $u->cost_pay_lead;
            $recruting_housing_cost = $u->housing_cost;
            $recruting_count_men = $cur_count_men . ' / ' . $u->count_men;
            $recruting_count_women = $cur_count_women . ' / ' . $u->count_women;
            $recruting_count_people = $cur_count_it . ' / ' . $u->count_people;
            $recruting_salary = $u->salary;

            if (
                Auth::user()->isRecruiter()
                || Auth::user()->isFreelancer()
                || Auth::user()->isHeadOfEmploymentDepartment()
            ) {
                if (Auth::user()->isRecruiter()) {
                    $add_link = '<a href="' . url('/') . '/candidate/add?r_id=' . Auth::user()->id . '&vid=' . $u->id . '"><i class="fas fa-user-plus"></i></a>';
                }

                if (Auth::user()->isFreelancer()) {
                    if (Auth::user()->fl_status == 2) {
                        $add_link = '<a href="' . url('/') . '/candidate/add?r_id=' . Auth::user()->recruter_id . '&vid=' . $u->id . '"><i class="fas fa-user-plus"></i></a>';
                    } else {
                        $add_link = '';
                    }
                }


                $count_men = '';
                $count_women = '';
                $count_people = '';

                if (
                    Auth::user()->isFreelancer()
                    || Auth::user()->isRecruiter()
                    || Auth::user()->isHeadOfEmploymentDepartment()
                ) {
                    if ($u->count_men > 0) $count_men = 'М';
                    if ($u->count_women > 0) $count_women = 'Ж';
                    if ($u->count_people > 0) $count_people = 'Н';
                } else {
                    $count_men = $u->count_men;
                    $count_women = $u->count_women;
                    $count_people = $u->count_people;
                }


                // Только фрилансер
                $temp_arr = [
                    '<a href="vacancy/add?id=' . $u->id . '">' . $u->id . '</a>',
                    $u->title,
                    $h_v_industry,
                    Carbon::parse($u->deadline_to)->format('d.m.Y'),
                    $count_men,
                    $count_women,
                    $count_people,
                    $u->salary,
                    $u->salary_description,
                    $u->housing_cost,
                ];

                if (!Auth::user()->isHeadOfEmploymentDepartment()) {
                    $temp_arr[] = $add_link;
                }
            } else {
                $temp_arr = [
                    '<a href="vacancy/add?id=' . $u->id . '">' . $u->id . '</a>',
                    $u->title,
                    $h_v_industry,
                    Carbon::parse($u->deadline_to)->format('d.m.Y'),
                    $recruting_count_men,
                    $recruting_count_women,
                    $recruting_count_people,
                    $recruting_salary,
                    $u->salary_description,
                    $recruting_housing_cost,
                    $recruting_cost_pay_lead,
                    $recruting_cost,
                    $select_active
                ];
            }

            $data[] = $temp_arr;
        }


        return Response::json(array(
            'data' => $data,
            "draw" => $draw,
            'recordsTotal' => $filtered_count,
            'recordsFiltered' => $filtered_count,
        ), 200);
    }

    private function prepareGetJsonRequest($filter__status, $filter__industry, $filter__city, $filter__genre, $search)
    {
        if ($filter__status) {
            $users = Vacancy::whereIn('activation', $filter__status);
        } else {
            $users = Vacancy::whereIn('activation', [1, 2, 3]);
        }

        if (Auth::user()->isFreelancer() || Auth::user()->isRecruiter() || Auth::user()->isRecruitmentDirector()) {
            $users = Vacancy::whereIn('activation', [1]);
        }

        if ($filter__industry) {
            $users = $users->whereHas('h_v_industry', function (Builder $query) use ($filter__industry) {
                $query->whereIn('handbook_id', $filter__industry);
            });
        }

        if ($filter__city) {
            $users = $users->whereHas('h_v_city', function (Builder $query) use ($filter__city) {
                $query->whereIn('handbook_id', $filter__city);
            });
        }
        if ($filter__genre && in_array('1', $filter__genre)) {
            $users = $users->where('count_men', '>', 0);
        }
        if ($filter__genre && in_array('2', $filter__genre)) {
            $users = $users->where('count_women', '>', 0);
        }
        if ($filter__genre && in_array('3', $filter__genre)) {
            $users = $users->where('count_people', '>', 0);
        }

        if ($search != '') {
            $users = $users->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%');
            });
        }

        return $users;
    }

    public function vacancyActivation(Request $r)
    {
        Vacancy::where('id', $r->id)->update(['activation' => $r->s]);
        return response(array('success' => "true"), 200);
    }

    public function vacancyChangecost(Request $r)
    {
        Vacancy::where('id', $r->id)->update(['recruting_cost' => $r->s]);
        return response(array('success' => "true"), 200);
    }

    public function vacancyChangecostpaylead(Request $r)
    {
        Vacancy::where('id', $r->id)->update(['cost_pay_lead' => $r->s]);
        return response(array('success' => "true"), 200);
    }

    public function vacancyChangehousingcost(Request $r)
    {
        Vacancy::where('id', $r->id)->update(['housing_cost' => $r->s]);
        return response(array('success' => "true"), 200);
    }

    public function vacancySalary(Request $r)
    {
        Vacancy::where('id', $r->id)->update(['salary' => $r->s]);
        return response(array('success' => "true"), 200);
    }

    public function vacancyCountpeople(Request $r)
    {
        Vacancy::where('id', $r->id)->update(['count_people' => $r->s ?: 0]);
        self::pauseIfFilled();
        return response(array('success' => "true"), 200);
    }

    public function vacancyCountwomen(Request $r)
    {
        Vacancy::where('id', $r->id)->update(['count_women' => $r->s ?: 0]);
        self::pauseIfFilled();
        return response(array('success' => "true"), 200);
    }

    public function vacancyCountmen(Request $r)
    {
        Vacancy::where('id', $r->id)->update(['count_men' => $r->s ?: 0]);
        self::pauseIfFilled();
        return response(array('success' => "true"), 200);
    }

    public function checkFilling(Request $req)
    {
        $vacancy = Vacancy::where('id', $req->vid)
            ->select('id', 'count_men', 'count_women', 'count_people')
            ->with('Candidates')
            ->first();

        if (!$vacancy) {
            return false;
        }

        $vacancy->filled_men = 0;
        $vacancy->filled_women = 0;
        $vacancy->filled_it = 0;

        $is_filled = false;

        if ($vacancy->Candidates) {
            foreach ($vacancy->Candidates as $Candidate) {
                if ($Candidate->gender == 'm') {
                    $vacancy->filled_men++;
                } elseif ($Candidate->gender == 'f') {
                    $vacancy->filled_women++;
                } else {
                    $vacancy->filled_it++;
                }
            }
        }

        if ($req->gender == 'm') { //например выбираем мужчину или вакансию для мужчины
            $count_people = $vacancy->count_people - $vacancy->filled_it; // вычетаем из количества НЕВАЖНО людей без пола и запоминаем

            if ($vacancy->filled_women > $vacancy->count_women) { // проверяем или женщин больше чем количество женских вакансий
                $count_people = $count_people - ($vacancy->filled_women - $vacancy->count_women); // если женщин больше то их остаток перетекает в НЕВАЖНО (отнимаем еще вакансии у НЕВАЖНО и запоминаем)
            }

            if ($count_people < 0) { // проверяем или вакансий НЕВАЖНО не стало меньше нуля, если меньше то не учитываем (ставим 0)
                $count_people = 0;
            }

            if ($vacancy->filled_men >= $vacancy->count_men + $count_people) { // проверяем или количество текущих мужчин меньше чем сумма мужских вакансий и ванансий НЕВАЖНО (за вычетом возможных женщин и бесполых)
                $is_filled = true; // если мужчин больше суммы вакансий М + НВ - мест больше нет ни в М ни в НЕВАЖНО
            }

            // В результате в НЕВАЖНО никогда не будет мест, так как бесполых кандидатов у нас много, к тому же вакансия будет ставиться на паузу не достигнув например мужчин или женщин. Так как пауза это сумма кандидатов больше чем сумма вакансий
        }

        if ($req->gender == 'f') {
            $count_people = $vacancy->count_people - $vacancy->filled_it;

            if ($vacancy->filled_men > $vacancy->count_men) {
                $count_people = $count_people - ($vacancy->filled_men - $vacancy->count_men);
            }

            if ($count_people < 0) {
                $count_people = 0;
            }

            if ($vacancy->filled_women >= $vacancy->count_women + $count_people) {
                $is_filled = true;
            }
        }

        return response()->json(['is_filled' => $is_filled, 'id' => $vacancy->id], 200);
    }

    // public function checkNacionality(Request $req)
    // {
    //     $vacancy = Vacancy::where('id', $req->vid)
    //         ->with('h_v_nacionality')
    //         ->with('Vacancy_client')
    //         ->with('Vacancy_client.Client.h_v_nationality')
    //         ->first();

    //     if (!$vacancy) {
    //         return false;
    //     }

    //     $is_disabled = false;

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

    //     if (!in_array($req->nacionality_id, $available_nanacionality_ids)) {
    //         $is_disabled = true;
    //     }

    //     return response()->json(['is_disabled' => $is_disabled, 'id' => $vacancy->id], 200);
    // }

    public static function pauseIfFilled()
    {
        $items = Vacancy::where('activation', 1)
            ->select('id', 'count_men', 'count_women', 'count_people')
            ->with('Candidates')
            ->get();

        if ($items) {
            $filled_ids = [];

            foreach ($items as $vacancy) {
                if ($vacancy->Candidates) {
                    if (count($vacancy->Candidates) >= $vacancy->count_men + $vacancy->count_women + $vacancy->count_people) {
                        $filled_ids[] = $vacancy->id;
                    }
                }
            }

            Vacancy::whereIn('id', $filled_ids)->update(['activation' => 2]);
        }
    }
}
