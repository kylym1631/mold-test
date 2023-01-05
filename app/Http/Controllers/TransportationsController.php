<?php

namespace App\Http\Controllers;

use App\Models\Transportation;
use App\Services\ArrivalsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransportationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('transportations.index');
    }

    public function indexJson()
    {
        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length");
        $active = request()->get("active");
        $search = request()->get("search");

        $filtered_count = $this->prepareGetJsonRequest($active, $search);
        $filtered_count = $filtered_count->count();

        $items = $this->prepareGetJsonRequest($active, $search);
        $items = $items->orderBy('id', 'desc');

        $items = $items
            ->with([
                'Driver',
                'ArrivalPlace',
                'CandidatesArrivals',
                'CandidatesArrivals.Candidate',
            ])
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data = [];

        if ($items) {
            foreach ($items as $key => $item) {
                $drivers_phone = '';
                $arrival_place_name = '';
                $departure_date_format = '';
                $arrival_date_format = '';
                $places = '';
                $status_name = 'Не активный';

                if ($item->Driver) {
                    $drivers_phone = $item->Driver->phone;
                }

                if ($item->ArrivalPlace) {
                    $arrival_place_name = $item->ArrivalPlace->name;
                }

                if ($items[$key]->departure_date) {
                    $departure_date_format = Carbon::parse($items[$key]->departure_date)->format('d.m.Y H:i');
                }

                if ($items[$key]->arrival_date) {
                    $arrival_date_format = Carbon::parse($items[$key]->arrival_date)->format('d.m.Y H:i');
                }

                if ($item->active == 1) {
                    $status_name = 'Активный';
                }

                $places = count($item->CandidatesArrivals) .'/'. $item->places_count;

                $data[] = [
                    'id' => $item->id,
                    'title' => $item->title,
                    'departure_date' => $departure_date_format,
                    'drivers_phone' => $drivers_phone,
                    'departure_place' => $item->departure_place,
                    'arrival_place' => $arrival_place_name,
                    'arrival_date' => $arrival_date_format,
                    'places' => $places,
                    'status_name' => $status_name,
                    'number' => $item->number,
                    'comment' => $item->comment,
                ];
            }
        }

        return response()->json([
            'data' => $data,
            'draw' => $draw,
            'recordsTotal' => $filtered_count,
            'recordsFiltered' => $filtered_count,
        ], 200);
    }

    private function prepareGetJsonRequest($active, $search)
    {
        $items = Transportation::query();

        if ($active != '') {
            $items = $items->where('active', $active);
        }

        $items = $items->when($search, function ($query, $search) {
            return $query->where('title', 'LIKE', '%' . $search . '%');
        });

        return $items;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('transportations.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        $niceNames = [
            'title' => '«Название автобуса»',
            'departure_date' => '«Дата выезда»',
            'arrival_date' => '«Дата прибытия»',
            'departure_place' => '«Откуда»',
            'arrival_place_id' => '«Куда»',
            'driver_id' => '«Водитель»',
            'number' => '«Номер машины»',
            'places_count' => '«Количество мест»',
        ];

        $validate_arr = [
            'title' => 'required',
            'departure_date' => 'required|date',
            'arrival_date' => 'required|date',
            'departure_place' => 'required',
            'arrival_place_id' => 'required',
            'driver_id' => 'required',
            'number' => 'required',
            'places_count' => 'required|numeric',
        ];

        $validator = Validator::make($req->all(), $validate_arr, [], $niceNames);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json(['success' => 'false', 'error' => $error], 200);
        }

        $item = new Transportation;

        $item->title = $req->title;
        $item->departure_date = $req->departure_date
            ? Carbon::createFromFormat('d.m.Y H:i', $req->departure_date)
            : null;
        $item->arrival_date = $req->arrival_date
            ? Carbon::createFromFormat('d.m.Y H:i', $req->arrival_date)
            : null;
        $item->departure_place = $req->departure_place;
        $item->arrival_place_id = $req->arrival_place_id;
        $item->driver_id = $req->driver_id;
        $item->number = $req->number;
        $item->comment = $req->comment;
        $item->active = $req->active;
        $item->places_count = $req->places_count;

        $item->save();

        return response()->json(['success' => 'true'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Transportation::where('id', $id)
            ->with([
                'Driver',
                'ArrivalPlace',
            ])->first();

        $item->departure_date_format = Carbon::parse($item->departure_date)->format('d.m.Y H:i');
        $item->arrival_date_format = Carbon::parse($item->arrival_date)->format('d.m.Y H:i');

        return view('transportations.view')->with('item', $item);
    }

    public function itemJson($id)
    {
        $item = Transportation::where('id', $id)
            ->with([
                'Driver',
                'ArrivalPlace',
            ])->first();

        $item->departure_date_format = Carbon::parse($item->departure_date)->format('d.m.Y H:i');
        $item->arrival_date_format = Carbon::parse($item->arrival_date)->format('d.m.Y H:i');

        return response()->json($item, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Transportation::where('id', $id)
            ->with([
                'Driver',
                'ArrivalPlace',
            ])->first();

        $item->departure_date_format = Carbon::parse($item->departure_date)->format('d.m.Y H:i');
        $item->arrival_date_format = Carbon::parse($item->arrival_date)->format('d.m.Y H:i');

        return view('transportations.form')->with('item', $item);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, ArrivalsService $ASrv)
    {
        $niceNames = [
            'title' => '«Название автобуса»',
            'departure_date' => '«Дата выезда»',
            'arrival_date' => '«Дата прибытия»',
            'departure_place' => '«Откуда»',
            'arrival_place_id' => '«Куда»',
            'driver_id' => '«Водитель»',
            'number' => '«Номер машины»',
            'places_count' => '«Количество мест»',
        ];

        $validate_arr = [
            'title' => 'required',
            'departure_date' => 'required|date',
            'arrival_date' => 'required|date',
            'departure_place' => 'required',
            'arrival_place_id' => 'required',
            'driver_id' => 'required',
            'number' => 'required',
            'places_count' => 'required|numeric',
        ];

        $validator = Validator::make($req->all(), $validate_arr, [], $niceNames);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json(['success' => 'false', 'error' => $error], 200);
        }

        $item = Transportation::find($req->id);

        if (!$item) {
            return response()->json(['success' => 'false', 'error' => 'Объект не найден'], 200);
        }

        $item->title = $req->title;
        $item->departure_date = $req->departure_date
            ? Carbon::createFromFormat('d.m.Y H:i', $req->departure_date)
            : null;
        $item->arrival_date = $req->arrival_date
            ? Carbon::createFromFormat('d.m.Y H:i', $req->arrival_date)
            : null;
        $item->departure_place = $req->departure_place;
        $item->arrival_place_id = $req->arrival_place_id;
        $item->driver_id = $req->driver_id;
        $item->number = $req->number;
        $item->comment = $req->comment;
        $item->active = $req->active;
        $item->places_count = $req->places_count;

        $item->save();

        $ASrv->updateTransportation($item->id, $item->arrival_place_id, $item->arrival_date);

        return response()->json(['success' => 'true'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
