<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Handbook;
use App\Models\Candidate;
use App\Models\Client;
use App\Models\Work_log;
use App\Models\Work_log_addition;
use App\Models\Work_log_day;
use App\Services\WorkLogsService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class WorkLogsController extends Controller
{
    public function getView()
    {
        return view('work-logs.index');
    }

    public function getJson(Request $req, WorkLogsService $wls)
    {
        $offset = $req->start;
        $limit = $req->length;
        $period = json_decode($req->period, true);

        if (!$period) {
            $period = ['from' => Carbon::now()->format('Y-m')];
        }

        $filter = [
            'clients' => $req->clients,
            'status' => $req->status,
            'period' => $period,
            'candidate_id' => $req->candidate_id,
        ];

        if (!$filter['clients'] && Auth::user()->isAdmin()) {
            $filter['clients'] = Client::pluck('id')->toArray();
        }

        $filtered_count = $this->prepareGetJsonRequest($filter);
        $filtered_count = $filtered_count->count();

        $items = $this->prepareGetJsonRequest($filter);

        $item_ids = $items
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->pluck('id');

        $clients = [];

        if ($filter['clients']) {
            $clients = array_merge($clients, $filter['clients']);
        }

        if (Auth::user()->isCoordinator()) {
            $more_clients = Client::where('coordinator_id', Auth::user()->id)->pluck('id');
            $clients = array_merge($clients, $more_clients->toArray());
        }

        // $data = $wls->getResultData($item_ids, $period);
        $data = $wls->getLogDaysByPositions($item_ids, $period, $clients);

        return Response::json([
            'data' => $data,
            'draw' => $req->draw,
            'recordsTotal' => $filtered_count,
            'recordsFiltered' => $filtered_count,
            'updateCandidate' => (int) $req->candidate_id,
        ], 200);
    }

    public function prepareGetJsonRequest($filter)
    {
        $period_instance = Carbon::now();

        if ($filter['period']) {
            if (isset($filter['period']['from'])) {
                $period_instance = Carbon::createFromFormat('Y-m', $filter['period']['from']);
            } else {
                $period_instance = Carbon::parse($filter['period']);
            }
        }

        $items = Candidate::allowedWithStatus()
            ->whereHas('Client_position');

        if (Auth::user()->isCoordinator()) {
            $items = $items->whereHas('Client', function ($q) {
                $q->where('coordinator_id', Auth::user()->id);
            })
                ->orWhereHas('Positions', function ($q) use ($period_instance) {
                    $q->where(function ($qry) use ($period_instance) {
                        $qry->whereMonth('start_at', $period_instance->month)
                            ->orWhereMonth('end_at', $period_instance->month);
                    })->whereHas('Position.Client', function ($q) {
                        $q->where('coordinator_id', Auth::user()->id);
                    });
                });
        }

        if ($filter['clients']) {
            $items = $items->whereIn('client_id', $filter['clients'])
                ->orWhereHas('Positions', function ($q) use ($period_instance, $filter) {
                    $q->where(function ($qry) use ($period_instance) {
                        $qry->whereMonth('start_at', $period_instance->month)
                            ->orWhereMonth('end_at', $period_instance->month);
                    })->whereHas('Position.Client', function ($q) use ($filter) {
                        $q->whereIn('id', $filter['clients']);
                    });
                });
        }

        if ($filter['candidate_id']) {
            $items = $items->where('id', $filter['candidate_id']);
        }

        if ($filter['status']) {
            $items = $items->whereIn('active', $filter['status']);
        }

        return $items;
    }

    public function additionsJson(Request $req, $type)
    {
        $data = [];
        $period = $req->period;

        if (!$period) {
            $period = ['from' => Carbon::now()->format('Y-m')];
        }

        $items = Work_log_addition::where('type', $type)
            ->where('candidate_id', $req->candidate_id)
            ->whereMonth('date', Carbon::createFromFormat('Y-m', $period['from'])->month)
            ->whereYear('date', Carbon::createFromFormat('Y-m', $period['from'])->year)
            ->with('Files')
            ->get();

        if ($items) {
            foreach ($items as $item) {
                $item->date = Carbon::parse($item->date)->format('d.m.Y');
                $data[] = $item;
            }
        }

        return response()->json([
            'data' => $data,
            'draw' => $req->draw,
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
        ], 200);
    }

    public function additionItemJson($id)
    {
        $item = Work_log_addition::where('id', $id)
            ->with('Files')
            ->first();

        if ($item) {
            $item->date = Carbon::parse($item->date)->format('Y-m-d');
        }

        return response()->json($item, 200);
    }

    public function create(Request $req)
    {
        $log = Work_log::find($req->log_id);

        if (!Auth::user()->isAccountant() && $log && $log->completed) {
            return response(array('success' => "false", 'error' => 'Вы не можете внести изменения. Месяц уже рассчитан.'), 200);
        }

        $validator = null;

        if ($req->work_time_format == 'decimal') {
            $validator = Validator::make($req->all(), [
                'log_day_hours' => 'numeric',
                'fine' => 'numeric',
            ], [], [
                'log_day_hours' => '«Часы»',
                'fine' => '«Штрафы»',
            ]);
        } elseif (Auth::user()->isAccountant()) {
            $validator = Validator::make($req->all(), [
                'client_work_time' => 'numeric',
                'rate' => 'numeric',
                'days' => 'numeric',
                'witness' => 'numeric',
            ], [], [
                'client_work_time' => '«Часы (Клиент)»',
                'rate' => '«Ставка»',
                'days' => '«Дни»',
                'witness' => '«OSWIADKA»',
            ]);
        } else {
            $validator = Validator::make($req->all(), [
                'log_day_hours' => 'regex:/^\d{1,2}:\d{1,2}$/',
                'fine' => 'numeric',
            ], [], [
                'log_day_hours' => '«Часы»',
                'fine' => '«Штрафы»',
            ]);
        }

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        if (!$log) {
            $log = new Work_log;
            $log->candidate_id = $req->candidate_id;
        }

        if (!$log) {
            return response(array('success' => "false", 'error' => 'Log error'), 200);
        }

        $log->period = $req->period;

        if ($req->fine) {
            $log->fine = $req->fine;
        }

        if ($req->premium) {
            $log->premium = $req->premium;
        }

        if ($req->bhp_form) {
            $log->bhp_form = $req->bhp_form;
        }

        // from accontant
        if ($req->client_work_time) {
            $log->client_work_time = $req->client_work_time * 60;
        }

        if ($req->rate) {
            $log->rate = $req->rate;
        }

        if ($req->days) {
            $log->days = $req->days;
        }

        if ($req->witness) {
            $log->witness = $req->witness;
        }

        $log->save();

        if ($req->has('log_day_id')) {
            if (!$req->log_day_id || $req->log_day_id == 'null') {
                $day = new Work_log_day;
                $day->work_log_id = $log->id;
                $day->date = $req->log_day_date;
            } else {
                $day = Work_log_day::find($req->log_day_id);
            }

            if ($req->work_time_format == 'decimal') {
                $day->work_time = (float) $req->log_day_hours * 60;
            } else {
                $hours = explode(':', $req->log_day_hours);

                $day->work_time = $hours[0] * 60 + $hours[1];
            }

            if ($day->work_time > 60 * 20) {
                return response(array('success' => "false", 'error' => 'Максимум 20 часов в смену'), 200);
            }

            $day->save();
        }

        return Response::json(['success' => 'true', 'req' => $req->all()], 200);
    }

    public function createAdditions(Request $req, WorkLogsService $wls)
    {
        $validator = Validator::make($req->all(), [
            'date' => 'required',
            'amount' => 'required|numeric',
        ], [], [
            'date' => '«Дата»',
            'amount' => '«Сумма»',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        $log = Work_log::where('candidate_id', $req->candidate_id)
            ->whereMonth('period', Carbon::createFromFormat('Y-m-d', $req->date)->month)
            ->whereYear('period', Carbon::createFromFormat('Y-m-d', $req->date)->year)
            ->first();

        if ($log && $log->completed) {
            return response(array('success' => "false", 'error' => 'Вы не можете внести изменения. Месяц уже рассчитан.'), 200);
        }

        $item = null;

        if ($req->has('id')) {
            $item = Work_log_addition::find($req->id);
        } else {
            $item = new Work_log_addition;
        }

        if (!$item) {
            return response()->json(['success' => 'false', 'error' => 'work log error'], 200);
        }

        $item->type = $req->type;
        $item->candidate_id = $req->candidate_id;
        $item->amount = (float) $req->amount;
        $item->comment = $req->comment;
        $item->date = Carbon::createFromFormat('Y-m-d', $req->date);

        $item->save();

        $f_check = $wls->checkFiles($req);

        if ($f_check && $f_check['success'] == 'false') {
            return response()->json($f_check, 200);
        }

        $wls->addFiles($req, $item->id);

        return response()->json(['success' => 'true'], 200);
    }

    public function completeLog(Request $req)
    {
        $item = Work_log::find($req->id);

        // if (!$item) {
        //     return response()->json(['success' => 'false', 'error' => 'Данный месяц не найден в базе. Добавьте хотя бы один день'], 200);
        // }

        if (!$item) {
            $item = new Work_log;
            $item->candidate_id = $req->candidate_id;
            $item->period = $req->period ?: Carbon::now();
        }

        if (!$item) {
            return response(array('success' => "false", 'error' => 'Log error'), 200);
        }

        if (Auth::user()->isAdmin()) {
            if ($req->uncomplete == '1') {
                $item->completed = 0;
            } else {
                $item->completed = 1;
            }
        } else {
            if ($item->completed) {
                return response(array('success' => "false", 'error' => 'Месяц уже рассчитан'), 200);
            }

            $item->completed = 1;
        }

        $item->save();

        return response()->json(['success' => 'true'], 200);
    }
}
