<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Lead;
use App\Models\User;
use App\Models\Task;
use App\Models\LeadSetting;
use App\Http\Controllers\CronController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\FieldsMutationController;
use App\Imports\LeadsImport;
use App\Models\Handbook;
use App\Models\LeadContact;
use App\Services\FilesService;
use App\Services\LeadsService;
use App\Services\TasksService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Maatwebsite\Excel\Facades\Excel;

class LeadsController extends Controller
{
    public function getIndex()
    {
        $statuses = [];

        $total = Lead::where('active', true)->count();
        $archive = Lead::where('active', false)->count();
        $archive_candidate = Lead::where('active', false)->whereNotNull('candidate_id')->count();
        $new = Lead::where('active', true)->whereNull('status')->whereNull('user_id')->count();

        $in_work = Lead::where('active', true)
            ->whereNotNull('user_id')
            ->where(function ($q) {
                $q->whereNot('status', 4)
                    ->orWhereNull('status');
            })
            ->count();

        $s_1 = Lead::where('active', true)->where('status', 1)->count();
        $s_2 = Lead::where('active', true)->where('status', 2)->count();

        $s_3_1 = Lead::where('active', true)->where('status', 3)
            ->where('count_failed_call', 1)
            ->count();

        $s_3_2 = Lead::where('active', true)->where('status', 3)
            ->where('count_failed_call', 2)
            ->count();

        $s_3_3 = Lead::where('active', true)->where('status', 3)
            ->where('count_failed_call', 3)
            ->count();

        $s_4 = Lead::where('active', true)->where('status', 4)->count();
        $s_7 = Lead::where('active', true)->where('status', 7)->count();

        $statuses[] = [
            'name' => 'Всего',
            'count' => $total,
        ];

        $statuses[] = [
            'name' => 'Не обработано',
            'count' => $new,
        ];

        $statuses[] = [
            'name' => 'В работе',
            'count' => $in_work,
        ];

        $statuses[] = [
            'name' => Lead::getStatusTitle(3) . ' 1',
            'count' => $s_3_1,
        ];
        $statuses[] = [
            'name' => Lead::getStatusTitle(3) . ' 2',
            'count' => $s_3_2,
        ];
        $statuses[] = [
            'name' => Lead::getStatusTitle(3) . ' 3',
            'count' => $s_3_3,
        ];

        $statuses[] = [
            'name' => Lead::getStatusTitle(2),
            'count' => $s_2,
        ];

        $statuses[] = [
            'name' => Lead::getStatusTitle(7),
            'count' => $s_7,
        ];

        $statuses[] = [
            'name' => Lead::getStatusTitle(4),
            'count' => $s_4,
        ];

        $statuses[] = [
            'name' => Lead::getStatusTitle(1),
            'count' => $s_1,
        ];

        $statuses[] = [
            'name' => 'Архив',
            'count' => $archive,
        ];

        $statuses[] = [
            'name' => 'Кандидат',
            'count' => $archive_candidate,
        ];

        return view('leads.index')->with('statuses', $statuses);
    }

    public function getHistoryIndex()
    {
        return view('leads.history');
    }

    public function getJson(LeadsService $ls)
    {
        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length");
        $status = request('status');
        $search = request('search');
        $period = request('period');
        $last_action_at = request('last_action_at');
        $recruiter = request('recruiter');
        $company = request('company');
        $status = request('status');
        $speciality = request('speciality');

        $filtered_count = $ls->prepareGetJsonRequest($search, $period, $recruiter, $company, $status, $speciality, $last_action_at);
        $filtered_count = $filtered_count->count();

        $users = $ls->prepareGetJsonRequest($search, $period, $recruiter, $company, $status, $speciality, $last_action_at);

        $order = request('order');
        $columns = request('columns');
        $order_columns = [];

        if ($order) {
            foreach ($order as $o) {
                $name = $columns[$o['column']]['name'];
                if ($name) {
                    $order_columns[] = [
                        'name' => $name,
                        'dir' => $o['dir'] ?: 'desc',
                    ];

                    if ($name == 'status') {
                        $order_columns[] = [
                            'name' => 'status_comment',
                            'dir' => $o['dir'] ?: 'desc',
                        ];
                    }
                }
            }
        }

        if ($order_columns) {
            foreach ($order_columns as $col) {
                $users = $users->orderBy($col['name'], $col['dir']);
            }
        } else {
            $users = $users->orderBy('date', 'DESC');
        }

        $users = $users
            ->with('Recruiter')
            ->with('Speciality')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $lead_settings = LeadSetting::all();
        $without_spec = Handbook::where('handbook_category_id', 13)->first();

        $data = [];

        foreach ($users as $u) {
            $status_id = $u->status;

            if ($u->last_action_at) {
                $u->last_action_at = Carbon::parse($u->last_action_at)->format('d.m.Y H:i');
            }

            if ($u->active == 0) {
                $u->status = 'Архив' . ($u->status_comment ? ': ' . $u->status_comment : '');
            } else if ($u->status == 1) {
                $l_label = '';

                if (Auth::user()->isAdmin()) {
                    if (!$u->count_liquidity || $u->count_liquidity == 1) {
                        $l_label = 'через 1 день';
                    } elseif ($u->count_liquidity == 2) {
                        $l_label = 'через 1 день';
                    }
                }

                $u->status = $u->getStatus() . ' ' . $l_label;
            } elseif ($u->status == 3) {
                $u->status = $u->getStatus() . ': ' . $u->count_failed_call;
            } elseif ($u->status == 7) {
                $u->status = $u->getStatus() . ': ' . $u->count_not_interested;
            } else {
                $u->status = $u->getStatus();
            }

            $u->requiter_name = '';

            if ($u->Recruiter) {
                $u->requiter_name = mb_strtoupper($u->Recruiter->firstName . ' ' . $u->Recruiter->lastName);
            }

            $u->speciality_name = '';
            $speciality_id = '';

            if ($u->Speciality) {
                $u->speciality_name = $u->Speciality->name;
                $speciality_id = $u->Speciality->id;
            } else {
                $u->speciality_name = $without_spec->name;
            }


            $settings = [];

            if ($u->active) {
                foreach ($lead_settings as $st_item) {
                    $src = json_decode($st_item->sources);
                    $stt = json_decode($st_item->statuses);
                    $spc = json_decode($st_item->speciality);

                    if (empty($status_id)) {
                        $status_id = '0';
                    }

                    if (empty($speciality_id)) {
                        $speciality_id = $without_spec->id;
                    }

                    $date = Carbon::createFromDate(1991, 1, 1)->startOfDay();

                    if ($st_item->lifetime_days != null) {
                        $date = Carbon::now()->subDays($st_item->lifetime_days)->startOfDay();
                    }

                    if (
                        $src && in_array($u->company == '' ? 'Без компании' : trim($u->company), $src)
                        && $stt && in_array($status_id, $stt)
                        && $spc && in_array($speciality_id, $spc)
                        && Carbon::parse($u->date)->startOfDay() >= $date
                    ) {
                        $settings[] = $st_item->name;
                    }
                }
            }

            $u->settings = $settings;

            $u->date = Carbon::parse($u->date)->format('d.m.Y H:i');

            $data[] = $u;
        }

        return Response::json(array(
            'data' => $data,
            'draw' => $draw,
            'recordsTotal' => $filtered_count,
            'recordsFiltered' => $filtered_count,
        ), 200);
    }

    public function forceImport()
    {
        $cron = new CronController;
        $cron->importLeads();

        return redirect()->route('leads');
    }

    public function reset(LeadsService $ls, TasksService $ts)
    {
        $ls->resetTasks();
        $ls->distributeToUsers();

        $ts->closeIfLeadsNotExists();

        return redirect()->route('leads');
    }

    public function getLeadAjax(Request $req)
    {
        $lead = Lead::where('id', $req->id)
            ->with('Contacts')
            ->first();

        if ($lead == null) {
            return response(array('success' => "false", 'error' => 'Лид не найден!'), 200);
        }

        return response(array('success' => "true", 'lead' => $lead), 200);
    }

    public static function setStatus(Request $req)
    {
        $lead = Lead::find($req->id);

        if ($lead == null) {
            return response(array('success' => "false", 'error' => 'Лид не найден!'), 200);
        }

        $comment = null;

        if (!empty($req->comment)) {
            $comment = $req->comment;
        }

        if ($req->status == 7) {
            $lead->count_not_interested = $lead->count_not_interested + 1;
            $lead->user_id = null;

            if ($lead->count_not_interested >= 2) {
                $lead->active = 0;
                $comment = 'Не заинтересован';
            }
        } else if ($req->status == 6 || $req->status == 5) {
            $lead->user_id = null;
            $lead->active = 0;

            if ($req->status == 6) {
                $comment = 'Не рекрутируем';
            } elseif ($req->status == 5) {
                $comment = 'Брак номера';
            }
        } else if ($req->status == 4) {
            $lead->user_id = Auth::user()->id;

            $task = new Task;
            $task->start = Carbon::createFromFormat('d.m.Y H:i', $req->date);
            $task->autor_id = Auth::user()->id;
            $task->to_user_id = Auth::user()->id;
            $task->status = 1;
            $task->type = 23;
            $task->title = Task::getTypeTitle($task->type);
            $task->lead_id = $lead->id;
            $task->save();
        } else if ($req->status == 3) {
            $lead->count_failed_call = $lead->count_failed_call + 1;
            $lead->user_id = null;

            if ($lead->count_failed_call >= 4) {
                $lead->active = 0;
                $comment = 'Недозвон';
            }
        } else if ($req->status == 2) {
            $lead->count_not_liquidity = $lead->count_not_liquidity + 1;
            $lead->user_id = null;

            if ($lead->count_not_liquidity >= 2) {
                $lead->active = 0;
                $comment = 'Не оставлял заявку';
            }
        } elseif ($req->status == 1) {
            if ($lead->status == 1 && $lead->count_liquidity == 0) {
                $lead->count_liquidity = $lead->count_liquidity + 2;
            } else {
                $lead->count_liquidity = $lead->count_liquidity + 1;
            }

            $lead->user_id = null;

            if ($lead->count_liquidity >= 3) {
                $lead->active = 0;
                $comment = 'Холодный';
            }
        }

        $req->status_comment = $comment;

        FieldsMutationController::addLeadLog($req, $lead, 'Lead.setStatus');

        $lead->last_action_at = Carbon::now();
        $lead->status_comment = $comment;
        $lead->status = $req->status;
        $lead->save();

        return response(array(
            'success' => "true",
            'candidate_id' => $lead->candidate_id,
        ), 200);
    }

    public function storeDetails(Request $req)
    {
        if ($req->contacts) {
            foreach ($req->contacts as $cItem) {
                foreach ($cItem['numbers'] as $key => $num) {
                    if (!preg_match('/\+[0-9]{9,12}/', $num)) {
                        return response(['success' => 'false', 'error' => 'Поле «' . $key . '» имеет ошибочный формат'], 200);
                    }
                }
            }

            foreach ($req->contacts as $cItem) {
                $contact = null;

                if (isset($cItem['id'])) {
                    $contact = LeadContact::find($cItem['id']);
                }

                if (!$contact) {
                    $contact = new LeadContact;
                    $contact->lead_id = $req->lead_id;
                }

                $contact->user_id = Auth::user()->id;
                $contact->type = isset($cItem['type']) ? $cItem['type'] : null;
                $contact->email = isset($cItem['email']) ? $cItem['email'] : null;
                $contact->first_name = isset($cItem['first_name']) ? $cItem['first_name'] : null;
                $contact->last_name = isset($cItem['last_name']) ? $cItem['last_name'] : null;
                $contact->numbers = isset($cItem['numbers']) ? json_encode($cItem['numbers']) : null;

                $contact->save();
            }
        }

        if ($req->speciality_id) {
            $l = Lead::find($req->lead_id);
            $l->speciality_id = $req->speciality_id;
            $l->save();
        }

        $contacts = LeadContact::where('lead_id', $req->lead_id)->get();

        return response()->json([
            'success' => 'true',
            'data' => [
                'contacts' => $contacts,
            ],
        ], 200);
    }

    public function importIndex()
    {
        return view('leads.import');
    }

    public function importUpload(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'file' => [
                'required',
                File::types(['xlsx', 'xls'])->max(8 * 1024),
            ],
        ], [], [
            'file' => '«Файл»',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json(['success' => 'false', 'error' => $error], 200);
        }

        $path = $req->file('file')->store('imports');

        $array = Excel::toArray(new LeadsImport, storage_path('app/' . $path));

        $n_arr = array_slice($array[0], 0, 7);

        return response()->json(['data' => $n_arr, 'path' => $path], 200);
    }

    public function importProcess(Request $req)
    {
        $columns = [];

        if ($req->column_name) {
            foreach ($req->column_name as $ind => $name) {
                $columns[$name] = $req->column_ind[$ind];
            }
        }

        $imp = new LeadsImport;
        $imp->columns = $columns;
        $imp->exclude_first_row = $req->exclude_first_row ? true : false;

        Excel::import($imp, storage_path('app/' . $req->file_path));

        return response()->json(['success' => 'true'], 200);
    }
}
