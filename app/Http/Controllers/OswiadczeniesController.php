<?php

namespace App\Http\Controllers;

use App\Models\Oswiadczenie;
use App\Services\FilesService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OswiadczeniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $items = Oswiadczenie::where('candidate_id', $req->candidate_id)
            ->with('Files')
            ->orderBy('date', 'DESC')
            ->get();

        $data = array_map(function ($item) {
            $item['date'] = Carbon::parse($item['date'])->format('d.m.Y');
            return $item;
        }, $items->toArray());

        return response()->json([
            'data' => $data,
            'draw' => $req->draw,
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $req)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req, FilesService $FS)
    {
        $validator = Validator::make($req->all(), [
            'date' => 'required',
            'cost' => 'required|numeric|min:0|max:1000000',
            'min_hours' => 'required|numeric|min:0|max:1000000',
        ], [], [
            'date' => '«Дата»',
            'cost' => '«Стоимость»',
            'min_hours' => '«Минимальное количество часов»',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        $item = new Oswiadczenie;

        $item->candidate_id = $req->candidate_id;
        $item->date = $req->date;
        $item->cost = $req->cost;
        $item->min_hours = $req->min_hours;

        $item->save();

        $f_check = $FS->checkFiles($req);

        if ($f_check && $f_check['success'] == 'false') {
            return response()->json($f_check, 200);
        }

        $FS->addFiles($req, 'oswiadczenie_id', $item->id);

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
        $item = Oswiadczenie::where('id', $id)
            ->with('Files')
            ->first();

        if ($item) {
            $item->date = Carbon::parse($item->date)->format('Y-m-d');
        }

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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, FilesService $FS, $id)
    {
        $validator = Validator::make($req->all(), [
            'date' => 'required',
            'cost' => 'required|numeric|min:0|max:1000000',
            'min_hours' => 'required|numeric|min:0|max:1000000',
        ], [], [
            'date' => '«Дата»',
            'cost' => '«Стоимость»',
            'min_hours' => '«Минимальное количество часов»',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        $item = Oswiadczenie::find($id);

        $item->candidate_id = $req->candidate_id;
        $item->date = $req->date;
        $item->cost = $req->cost;
        $item->min_hours = $req->min_hours;

        $item->save();

        $f_check = $FS->checkFiles($req);

        if ($f_check && $f_check['success'] == 'false') {
            return response()->json($f_check, 200);
        }

        $FS->addFiles($req, 'oswiadczenie_id', $item->id);

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
