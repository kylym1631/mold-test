<?php

namespace App\Http\Controllers;

use App\Models\Client_contact;
use App\Models\Client_position;
use App\Models\User;
use App\Models\Client;
use App\Models\ClientPositionRate;
use App\Models\Handbook;
use App\Models\Handbook_client;
use App\Models\Housing_client;
use App\Services\OptionsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function getIndex()
    {
        $industries = Handbook::where('handbook_category_id', 1)->where('active', 1)->get();
        return view('clients.index')->with('industries', $industries);
    }

    public function getJson()
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
        $coordinator_id = request('coordinator_id');
        $industry_id = request('industry_id');

        $filtered_count = $this->prepareGetJsonRequest($status, $coordinator_id, $industry_id, $search);
        $filtered_count = $filtered_count->count();

        $users = $this->prepareGetJsonRequest($status, $coordinator_id, $industry_id, $search);
        $users = $users->orderBy($order_col, $order_direction);

        $users = $users
            ->with('h_v_industry')
            ->with([
                'Candidates' => function ($query) {
                    $query->select('id', 'active', 'client_id', 'removed')
                        ->where('active', 9);
                },
            ])
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


            if ($u->active == 1 || $u->active == '') {
                $select_active = '<select onchange="changeActivation(' . $u->id . ')"
                                    class="form-select form-select-sm form-select-solid changeActivation' . $u->id . '">
                                         <option  selected value="1">Активный</option>
                                            <option value="2">Деактивированный</option>
                            </select>';
            } else if ($u->active == 2) {
                $select_active = '<select onchange="changeActivation(' . $u->id . ')"
                                    class="form-select form-select-sm form-select-solid changeActivation' . $u->id . '">
                                         <option value="1">Активный</option>
                                            <option selected value="2">Деактивированный</option>
                            </select>';
            }

            $Coordinator = '';
            if ($u->Coordinator != null) {
                $Coordinator = $u->Coordinator->firstName . ' ' . $u->Coordinator->lastName;
            }

            $Candidates_count = 0;
            if ($u->Candidates != null) {
                $Candidates_count = count($u->Candidates);
            }

            $id_link = '<a href="client/add?id=' . $u->id . '">' . $u->id . '</a>';

            if (
                Auth::user()->isKoordinator()
                || Auth::user()->isRealEstateManager()
                || Auth::user()->isAccountant()
                || Auth::user()->group_id > 99
            ) {
                $id_link = '<a href="client/view?id=' . $u->id . '">' . $u->id . '</a>';
            }

            if (Auth::user()->hasPermission('client.edit')) {
                $id_link = '<a href="client/add?id=' . $u->id . '">' . $u->id . '</a>';
            }

            $temp_arr = [
                $id_link,
                $u->name,
                $Candidates_count,
                $h_v_industry,
                $u->address,
                $Coordinator,
                // $select_active

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

    private function prepareGetJsonRequest($status, $coordinator_id, $industry_id, $search)
    {
        $users = Client::query();

        if ($status != '') {
            $users = $users->where('active', $status);
        }

        if (Auth::user()->isKoordinator()) {
            $users = $users->where('coordinator_id', Auth::user()->id);
        } elseif ($coordinator_id) {
            $users = $users->whereIn('coordinator_id', $coordinator_id);
        }

        if ($industry_id) {
            $ids_h = Handbook_client::whereIn('handbook_id', $industry_id)->pluck('client_id');
            $users = $users->whereIn('id', $ids_h);
        }

        if ($search != '') {
            $users = $users->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            });
        }

        return $users;
    }

    public function viewIndex(Request $r, OptionsService $os)
    {
        if ($r->has('id')) {
            $client = Client::where('id', $r->id)
                ->with('h_v_industry')
                ->with('h_v_industry.Handbooks')
                ->with('h_v_city')
                ->with('h_v_city.Handbooks')
                ->with('h_v_nationality')
                ->with('h_v_nationality.Handbooks')
                ->with('h_v_housing')
                ->with('h_v_housing.Housing')
                ->with('Contacts')
                ->with('Positions')
                ->with(['Positions.Rates' => function ($q) {
                    $q->orderBy('start_at', 'ASC');
                }])
                ->first();
            if ($client->Coordinator != null) {
                $Coordinator = [$client->Coordinator->id, $client->Coordinator->firstName . ' ' . $client->Coordinator->lastName];
            }
            $h_v_industry = [];
            $h_v_city = [];
            $h_v_nationality = [];
            $h_v_housing = [];

            foreach ($client->h_v_industry as $industry) {
                if ($industry->Handbooks != null) {
                    $h_v_industry[] = [$industry->Handbooks->id, $industry->Handbooks->name];
                }
            }
            foreach ($client->h_v_city as $city) {
                if ($city->Handbooks != null) {
                    $h_v_city[] = [$city->Handbooks->id, $city->Handbooks->name];
                }
            }
            foreach ($client->h_v_nationality as $nationality) {
                if ($nationality->Handbooks != null) {
                    $h_v_nationality[] = [$nationality->Handbooks->id, $nationality->Handbooks->name];
                }
            }
            foreach ($client->h_v_housing as $housing) {
                if ($housing->Housing != null) {
                    $h_v_housing[] = [$housing->Housing->id, $housing->Housing->title . ' ' . $housing->Housing->address];
                }
            }

            if ($client->min_work_time) {
                $client->min_work_time_dec = round($client->min_work_time / 60, 2);
            }
        } else {
            $client = null;
            $Coordinator = null;
            $h_v_industry = null;
            $h_v_city = null;
            $h_v_nationality = null;
            $h_v_housing = null;
        }

        $options = $os->getByKeys(['min_rate_netto', 'min_rate_brutto']);

        return view('clients.view')
            ->with('h_v_nationality', $h_v_nationality)
            ->with('h_v_city', $h_v_city)
            ->with('h_v_industry', $h_v_industry)
            ->with('h_v_housing', $h_v_housing)
            ->with('Coordinator', $Coordinator)
            ->with('client', $client)
            ->with('options', $options);
    }

    public function clientsActivation(Request $r)
    {
        Client::where('id', $r->id)->update(['active' => $r->s]);
        return response(array('success' => "true"), 200);
    }

    public function getAdd(Request $r, OptionsService $os)
    {
        if ($r->has('id')) {
            $client = Client::where('id', $r->id)
                ->with('h_v_industry')
                ->with('h_v_industry.Handbooks')
                ->with('h_v_city')
                ->with('h_v_city.Handbooks')
                ->with('h_v_nationality')
                ->with('h_v_nationality.Handbooks')
                ->with('h_v_housing')
                ->with('h_v_housing.Housing')
                ->with('Contacts')
                ->with('Positions')
                ->with(['Positions.Rates' => function ($q) {
                    $q->orderBy('start_at', 'ASC');
                }])
                ->first();
            if ($client->Coordinator != null) {
                $Coordinator = [$client->Coordinator->id, $client->Coordinator->firstName . ' ' . $client->Coordinator->lastName];
            }
            $h_v_industry = [];
            $h_v_city = [];
            $h_v_nationality = [];
            $h_v_housing = [];

            foreach ($client->h_v_industry as $industry) {
                if ($industry->Handbooks != null) {
                    $h_v_industry[] = [$industry->Handbooks->id, $industry->Handbooks->name];
                }
            }
            foreach ($client->h_v_city as $city) {
                if ($city->Handbooks != null) {
                    $h_v_city[] = [$city->Handbooks->id, $city->Handbooks->name];
                }
            }
            foreach ($client->h_v_nationality as $nationality) {
                if ($nationality->Handbooks != null) {
                    $h_v_nationality[] = [$nationality->Handbooks->id, $nationality->Handbooks->name];
                }
            }
            foreach ($client->h_v_housing as $housing) {
                if ($housing->Housing != null) {
                    $h_v_housing[] = [$housing->Housing->id, $housing->Housing->title . ' ' . $housing->Housing->address];
                }
            }

            if ($client->min_work_time) {
                $client->min_work_time_dec = round($client->min_work_time / 60, 2);
            }
        } else {
            $client = null;
            $Coordinator = null;
            $h_v_industry = null;
            $h_v_city = null;
            $h_v_nationality = null;
            $h_v_housing = null;
        }

        $options = $os->getByKeys(['min_rate_netto', 'min_rate_brutto']);

        return view('clients.add')
            ->with('h_v_nationality', $h_v_nationality)
            ->with('h_v_housing', $h_v_housing)
            ->with('h_v_city', $h_v_city)
            ->with('h_v_industry', $h_v_industry)
            ->with('Coordinator', $Coordinator)
            ->with('client', $client)
            ->with('options', $options);
    }

    public function postAdd(Request $r)
    {

        $niceNames = [
            'name' => '«имя»',
            'coordinator_id' => '«координатор»',
            'industry_id' => '«отрасль»',
            'work_place_id' => '«место работы»',
            'nationality_id' => '«Национальность»',
            'housing_id' => '«Жилье»',
            'address' => '«Адрес»',
            'min_work_time' => '«Мин. кол-во часов»',
            'positions' => '«Должности»',
            'positions.rates.*' => '«Ставки»',
        ];

        $validator = Validator::make($r->all(), [
            'name' => 'required',
            'coordinator_id' => 'required',
            'address' => 'required',
            'work_place_id' => 'required',
            'industry_id' => 'required',
            'nationality_id' => 'required',
            'housing_id' => '',
            'min_work_time' => 'required|numeric',
            'positions' => 'required',
            'positions.rates.*' => 'required',

        ], [], $niceNames);
        
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        $positions = json_decode($r->positions, true);

        foreach ($positions as $pos) {
            foreach ($pos['rates'] as $rate) {

                $validator = Validator::make($rate, [
                    'type' => 'required',
                    'start_at' => 'required|date',
                    'amount' => 'required|numeric|max:1000000',
                ], [], [
                    'housing_id' => '«Жилье»',
                    'type' => '«Тип ставки»',
                    'start_at' => '«Дата начала ставки»',
                    'amount' => '«Величина ставки»',
                ]);

                if ($validator->fails()) {
                    $error = $validator->errors()->first();
                    return response()->json(['success' => 'false', 'error' => $error], 200);
                }
            }
        }

        $client = Client::find($r->id);
        if ($client == null) {
            $client = new Client();
            $client->active = 1;
        } else {
            $client->active = (int) $r->active;
        }


        $client->name = $r->name;
        $client->coordinator_id = $r->coordinator_id;
        $client->address = $r->address;
        $client->work_time_format = $r->work_time_format;
        $client->min_work_time = $r->min_work_time * 60;

        $client->save();

        Handbook_client::where('client_id', $client->id)->delete();
        Housing_client::where('client_id', $client->id)->delete();

        $arrs = explode(',', $r->industry_id);
        foreach ($arrs as $arr) {
            $Hand = new Handbook_client();
            $Hand->client_id = $client->id;
            $Hand->handbook_id = $arr;
            $Hand->handbook_category_id = 1;
            $Hand->save();
        }
        $arrs = explode(',', $r->work_place_id);
        foreach ($arrs as $arr) {
            $Hand = new Handbook_client();
            $Hand->client_id = $client->id;
            $Hand->handbook_id = $arr;
            $Hand->handbook_category_id = 3;
            $Hand->save();
        }
        $arrs = explode(',', $r->nationality_id);
        foreach ($arrs as $arr) {
            $Hand = new Handbook_client();
            $Hand->client_id = $client->id;
            $Hand->handbook_id = $arr;
            $Hand->handbook_category_id = 2;
            $Hand->save();
        }

        if ($r->housing_id) {
            $arrs = explode(',', $r->housing_id);
            foreach ($arrs as $arr) {
                $new_item = new Housing_client();
                $new_item->client_id = $client->id;
                $new_item->housing_id = $arr;
                $new_item->save();
            }
        }

        $contacts = json_decode($r->contacts);
        Client_contact::where('client_id', $client->id)->delete();
        foreach ($contacts as $one) {
            $cl = new Client_contact();
            $cl->user_id = Auth::user()->id;
            $cl->client_id = $client->id;
            $cl->firstName = $one[0];
            $cl->lastName = $one[1];
            $cl->position = $one[2];
            $cl->phone = $one[3];
            $cl->email = $one[4];
            $cl->save();
        }

        $positions = json_decode($r->positions, true);

        foreach ($positions as $pos) {
            $ps = null;

            if (isset($pos['id'])) {
                $ps = Client_position::find($pos['id']);
            } else {
                $ps = new Client_position;
            }

            if ($ps) {
                $cur_rates = ClientPositionRate::where('client_position_id', $ps->id)
                    ->orderBy('start_at', 'DESC')
                    ->get();

                foreach ($pos['rates'] as $rate) {

                    if (!isset($rate['id'])) {
                        foreach ($cur_rates as $cur_rate) {
                            if (
                                $rate['type'] == $cur_rate['type']
                                && Carbon::createFromFormat('d.m.Y', $rate['start_at'])->startOfDay() <= $cur_rate['start_at']
                            ) {
                                return response()->json(['error' => 'Дата начала следующей ставки должна быть больше чем предыдущая'], 200);
                            }
                        }
                    }
                }

                $ps->client_id = $client->id;
                $ps->description = $pos['description'];
                $ps->title = $pos['title'];

                $ps->save();

                foreach ($pos['rates'] as $rate) {
                    $rt = null;

                    if (isset($rate['id'])) {
                        if (ClientPositionRate::find($rate['id'])) {
                            continue;
                        }
                    } else {
                        $rt = new ClientPositionRate;
                    }

                    if ($rt) {
                        $rt->client_position_id = $ps->id;
                        $rt->type = $rate['type'];
                        $rt->amount = $rate['amount'];
                        $rt->start_at = $rate['start_at'] ? Carbon::createFromFormat('d.m.Y', $rate['start_at'])->startOfDay() : Carbon::now()->startOfDay();

                        $rt->save();
                    }
                }
            }
        }

        return Response::json(array('success' => "true", 200));
    }
}
