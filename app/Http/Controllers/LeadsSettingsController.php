<?php

namespace App\Http\Controllers;

use App\Models\Handbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use App\Models\Lead;
use App\Models\LeadSetting;

class LeadsSettingsController extends Controller
{
    public function listView()
    {
        $all_packages = LeadSetting::select('id', 'name')->orderBy('id', 'ASC')->get();
        return view('leads.settings')->with('all_packages', $all_packages);
    }

    public function listJson()
    {
        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length");

        $filtered_count = $this->prepareGetJsonRequest();
        $filtered_count = $filtered_count->count();

        $items = $this->prepareGetJsonRequest();
        $items = $items->orderBy('id', 'DESC');

        $items = $items
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data = [];

        foreach ($items as $m) {
            $m->lifetime = 'Все';

            if ($m->statuses) {
                $m->statuses = array_map(function ($key) {
                    return Lead::getStatusTitle($key);
                }, json_decode($m->statuses));
            }

            if ($m->sources) {
                $m->sources = json_decode($m->sources);
            }

            if ($m->speciality) {
                $spec = Handbook::whereIn('id', json_decode($m->speciality))->get();

                $m->speciality = array_map(function ($item) {
                    return $item['name'];
                }, $spec->toArray());
            }

            if ($m->lifetime_days != null) {
                $m->lifetime = $m->lifetime_days;
            }

            $data[] = $m;
        }

        return Response::json(array(
            'data' => $data,
            'draw' => $draw,
            'recordsTotal' => $filtered_count,
            'recordsFiltered' => $filtered_count,
        ), 200);
    }

    private function prepareGetJsonRequest()
    {
        $items = LeadSetting::query();

        return $items;
    }

    public function itemJson(Request $req)
    {
        $item = LeadSetting::find($req->id);

        if (!$item) {
            return response()->json(['error' => 'Пакет не найден'], 200);
        }

        if ($item->statuses) {
            $item->statuses = json_decode($item->statuses);
        }

        if ($item->sources) {
            $item->sources = json_decode($item->sources);
        }

        if ($item->delays) {
            $delays = json_decode($item->delays);

            $item->failed_call_1_delay = $delays->failed_call_1_delay;
            $item->failed_call_2_delay = $delays->failed_call_2_delay;
            $item->failed_call_3_delay = $delays->failed_call_3_delay;
            $item->not_interested_delay = $delays->not_interested_delay;
            $item->not_liquidity_delay = $delays->not_liquidity_delay;
            $item->liquidity_delay = $delays->liquidity_delay;
        }

        if ($item->speciality) {
            $spec = Handbook::whereIn('id', json_decode($item->speciality))->get();

            $item->speciality = array_map(function ($item) {
                return [$item['id'], $item['name']];
            }, $spec->toArray());
        }

        $item->lifetime = '';

        if ($item->lifetime_days != null) {
            $item->lifetime = $item->lifetime_days;
        }

        return response()->json(['data' => $item], 200);
    }

    public function update(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'lifetime' => 'nullable|numeric|max:9999',
        ], [], [
            'name' => '«Название»',
            'lifetime' => '«Время жизни лида»',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        $item = null;

        if ($req->id) {
            $item = LeadSetting::find($req->id);
        } else {
            $item = new LeadSetting;
        }

        $item->name = $req->name;
        $item->lifetime_days = isset($req->lifetime) ? $req->lifetime : null;
        $item->sources = $req->sources ? json_encode($req->sources) : null;
        $item->statuses = $req->statuses ? json_encode($req->statuses) : null;
        $item->speciality = $req->speciality ? json_encode($req->speciality) : null;

        $delays = [
            'failed_call_1_delay' => $req->failed_call_1_delay ?: '',
            'failed_call_2_delay' => $req->failed_call_2_delay ?: '',
            'failed_call_3_delay' => $req->failed_call_3_delay ?: '',
            'not_interested_delay' => $req->not_interested_delay ?: '',
            'not_liquidity_delay' => $req->not_liquidity_delay ?: '',
            'liquidity_delay' => $req->liquidity_delay ?: '',
        ];

        $item->delays = json_encode($delays);

        $item->save();

        return response()->json(['success' => 'true', 'id' => $item->id], 200);
    }
}
