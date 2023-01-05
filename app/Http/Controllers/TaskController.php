<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Candidate_arrival;
use App\Models\Car;
use App\Models\Client;
use App\Models\Housing;
use App\Models\Task;
use App\Models\Lead;
use App\Models\Role;
use App\Models\TaskTemplate;
use App\Models\User;
use App\Models\Vacancy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Services\TasksService;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function getIndex()
    {
        return view('tasks.index');
    }

    public function getJson(TasksService $t_srv)
    {
        Task::where('to_user_id', Auth::user()->id)
            ->where('status', 1)
            ->whereDate('start', '<', Carbon::now())
            ->update(['status' => 3]);

        if (Auth::user()->isLogist()) {
            Task::where('to_user_id', Auth::user()->id)
                ->whereIn('status', [1, 3])
                ->where(function ($query) {
                    $cand_ids = Candidate::allowedWithStatus()->pluck('id');
                    $query->whereNotIn('candidate_id', $cand_ids);
                })
                ->update(['status' => 2]);
        }

        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length"); // Rows display per page

        //ordering
        $order_col = 'start';
        $order_direction = 'desc';
        $cols = request('columns');
        $order = request('order');

        if (isset($order[0]['dir'])) {
            $order_direction = $order[0]['dir'];
        }

        // search
        $filter__status = request('status');
        $search = request('search');
        $filter__period = request('period');

        $filtered_count = $t_srv->prepareGetJsonRequest($filter__status, $filter__period, $search);
        $filtered_count = $filtered_count->count();

        $tasks = $t_srv->prepareGetJsonRequest($filter__status, $filter__period, $search);

        if (Auth::user()->isRecruiter()) {
            if (
                !$filter__status
                || ($filter__status && (in_array('1', $filter__status) || in_array('3', $filter__status)))
            ) {
                $tasks = $tasks->whereIn('status', [1, 3])->orderBy('start', 'ASC');
                $filtered_count = 1;
            } else {
                $tasks = $tasks->orderBy($order_col, $order_direction);
            }
        } else {
            $tasks = $tasks->orderBy($order_col, $order_direction);
        }

        $data = [];

        if (Auth::user()->isLogist()) {
            $tasks = $tasks
                ->with('Candidate')
                ->with('Candidate.Vacancy')
                ->with('Candidate.Citizenship')
                ->with('Candidate.Recruiter')
                ->with('Candidate_arrival')
                ->skip($start)
                ->take($rowperpage)
                ->get();

            $data = $t_srv->resultForLogist($tasks);
        } else {
            $tasks = $tasks
                ->with('Autor')
                ->with('Candidate')
                ->with('Candidate.Client')
                ->with('Candidate.Vacancy')
                ->with('Candidate.Recruiter')
                ->with('Candidate.D_file')
                ->with('Freelancer')
                ->with('Lead')
                ->with('Client')
                ->with('Housing')
                ->with('Car')
                ->with('Vacancy')
                ->skip($start)
                ->take($rowperpage)
                ->get();

            $data = $t_srv->result($tasks, $filter__status);
        }

        return Response::json(array(
            'data' => $data,
            "draw" => $draw,
            'recordsTotal' => $filtered_count,
            'recordsFiltered' => $filtered_count,
        ), 200);
    }

    public function allIndex()
    {
        $roles = Role::all();
        $users = User::where('activation', 1)->get();
        $types = Task::$types;

        return view('tasks.all')
            ->with('roles', $roles)
            ->with('users', $users)
            ->with('types', $types);
    }

    public function allIndexJson(TasksService $t_srv)
    {
        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length");

        $filter__status = request('status');
        $search = request('search');
        $filter__period = request('period');
        $group_ids = request('group');
        $user_ids = request('users');
        $type_ids = request('types');

        $filtered_count = $t_srv->prepareGetJsonRequest($filter__status, $filter__period, $search, $group_ids, $user_ids, $type_ids, true);
        $filtered_count = $filtered_count->count();

        $tasks = $t_srv->prepareGetJsonRequest($filter__status, $filter__period, $search, $group_ids, $user_ids, $type_ids, true);

        $tasks = $tasks
            ->with('User')
            ->with('Candidate')
            ->with('Lead')
            ->orderBy('start', 'DESC')
            ->skip($start)
            ->take($rowperpage)
            ->get();


        $data = [];

        if ($tasks) {
            foreach ($tasks as $task) {
                $User = '';
                $info_btn = '';
                $Person = '';
                $cur_status = '';

                if ($task->User) {
                    $User = mb_strtoupper($task->User->firstName . ' ' . $task->User->lastName);
                }

                if ($task->status == 4 && $task->comment) {
                    $info_btn = '<button type="button" class="js-show-comment btn btn-sm btn-icon show-info-btn" data-tooltip="' . $task->comment . '">
                    <span class="svg-icon svg-icon-primary svg-icon-2x"> <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <rect x="0" y="0" width="24" height="24"></rect> <polygon opacity="0.3" points="5 15 3 21.5 9.5 19.5"></polygon> <path d="M13.5,21 C8.25329488,21 4,16.7467051 4,11.5 C4,6.25329488 8.25329488,2 13.5,2 C18.7467051,2 23,6.25329488 23,11.5 C23,16.7467051 18.7467051,21 13.5,21 Z M9,8 C8.44771525,8 8,8.44771525 8,9 C8,9.55228475 8.44771525,10 9,10 L18,10 C18.5522847,10 19,9.55228475 19,9 C19,8.44771525 18.5522847,8 18,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 C8,13.5522847 8.44771525,14 9,14 L14,14 C14.5522847,14 15,13.5522847 15,13 C15,12.4477153 14.5522847,12 14,12 L9,12 Z"></path> </g> </svg> </span>
                    </button>';
                }

                if ($task->Candidate != null) {
                    $Person = '<a href="' . url('/') . '/candidate/view?id=' . $task->Candidate->id . '">' . mb_strtoupper($task->Candidate->firstName . ' ' . $task->Candidate->lastName) . '</a>';
                }

                if ($task->Lead != null) {
                    if ($task->Lead->name) {
                        $Person = mb_strtoupper($task->Lead->name);
                    } else {
                        $Person = 'Имя лида не указано';
                    }

                    $cur_status = 'NOW status num: '. $task->Lead->status . ' ; NOW failed calls: ' . $task->Lead->count_failed_call;
                }

                $data[] = [
                    'id' => $task->id,
                    'created_updated' => $task->created_at .'---'. $task->updated_at,
                    'date' => $task->type > 99 ? Carbon::parse($task->end)->format('d.m.Y H:i') : Carbon::parse($task->start)->format('d.m.Y H:i'),
                    'title' => $task->title,
                    'user_full_name' => $User,
                    'status' => '<div class="row flex-nowrap status-actions">' . $task->getStatus() . $info_btn . '</div>',
                    'person' => $Person,
                    'cur_status' => $cur_status,
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

    public function getTaskAjax($id)
    {
        $task = Task::where('id', $id)
            ->with('Autor')
            ->with('Candidate')
            ->with('Lead')
            ->first();

        if ($task == null) {
            return response(array('success' => "false", 'error' => 'Задача не найдена!'), 200);
        }

        if ($task->type == 10 || $task->type == 12 || $task->type == 23 || $task->type == 100) {
            $output_start = Carbon::parse($task->start)->format('d.m.Y H:i');
        } else {
            $output_start = Carbon::parse($task->start)->format('d.m.Y') . ' 08:00';
        }

        if ($output_start != null) {
            $task->output_start = $output_start;
        }

        if ($task->end != null) {
            $task->output_end = Carbon::parse($task->end)->format('d.m.Y H:i');
        }

        $Autor = 'Без автора';
        if ($task->Autor != null) {
            $Autor = $task->Autor->firstName . ' ' . $task->Autor->lastName;
        }

        $Candidate = '';
        if ($task->Candidate != null) {
            $Candidate = '<a href="' . url('/') . '/candidate/view?id=' . $task->Candidate->id . '">' . mb_strtoupper($task->Candidate->firstName . ' ' . $task->Candidate->lastName) . '</a>';
        }

        $Lead = '';
        $LeadCompany = '';
        if ($task->Lead != null) {
            $Lead = '<a href="#" class="js-show-lead" data-id="' . $task->Lead->id . '">' . mb_strtoupper($task->Lead->name) . '</a>';
            $LeadCompany = $task->Lead->company;

            if ($task->type == 23 && $task->Lead->status_comment) {
                $task->title .= $task->Lead->status_comment;
            }
        }

        $task->Candidate = $Candidate;
        $task->Lead = $Lead;
        $task->LeadCompany = $LeadCompany;
        $task->Autor = $Autor;
        $task->status = $task->getStatus();
        $task->createdAt = Carbon::parse($task->created_at)->format('d.m.Y H:i');

        return response(array('success' => "true", 'task' => $task), 200);
    }

    public static function getUnfinished()
    {
        $tasks = Task::whereIn('status', [1, 3])
            ->where('to_user_id', Auth::user()->id)
            ->select('candidate_id', 'start')
            ->with([
                'Candidate' => function ($q) {
                    return $q->select('id', 'removed');
                }
            ])
            ->whereDate('start', '<', Carbon::now()->addDays(1))
            ->get();

        $count = 0;

        foreach ($tasks as $task) {
            if ($task->candidate_id && !$task->Candidate) {
                continue;
            }

            $count++;
        }

        return $count;
    }

    public function create()
    {
        $roles = Role::with('Users')->get();
        $templates = TaskTemplate::select('id', 'title')->get();

        return view('tasks.create')
            ->with('roles', $roles)
            ->with('templates', $templates);
    }

    public function store(Request $req, TasksService $t_srv)
    {
        $validator = Validator::make($req->all(), [
            'title' => 'required',
            'end' => 'required|date',
        ], [], [
            'title' => '«Описание задачи»',
            'end' => '«Дедлайн»',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json(['success' => 'false', 'error' => $error], 200);
        }

        if ($req->all_users == '1') {
            if (!$req->to_user_roles) {
                return response()->json(['success' => 'false', 'error' => 'Выберите роли сотрудников'], 200);
            }
        } else
        if ($req->all_users == '0') {
            if (!$req->to_user_ids) {
                return response()->json(['success' => 'false', 'error' => 'Выберите сотрудников'], 200);
            }
        }

        if ($req->model_name && !$req->model_obj_id) {
            return response()->json(['success' => 'false', 'error' => 'Выберите кого касается задача'], 200);
        }

        $req = $req->all();

        $req['end'] = Carbon::createFromFormat('Y-m-d H:i', $req['end']);

        $t_srv->createTasks($req);

        return response()->json(['success' => 'true'], 200);
    }

    public function templatesIndex()
    {
        return view('tasks.templates');
    }

    public function templatesJson(TasksService $t_srv)
    {
        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length");

        $filter__status = request('status');
        $search = request('search');
        $filter__period = request('period');

        $filtered_count = $t_srv->prepareTemplatesJsonRequest($search);
        $filtered_count = $filtered_count->count();

        $items = $t_srv->prepareTemplatesJsonRequest($search);

        $items = $items
            ->orderBy('id', 'DESC')
            ->skip($start)
            ->take($rowperpage)
            ->get();


        $data = [];

        if ($items) {
            foreach ($items as $tpl) {
                $data[] = [
                    'id' => $tpl->id,
                    'title' => $tpl->title,
                    'description' => $tpl->description,
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

    public function createTemplate()
    {
        $roles = Role::with('Users')->get();

        return view('tasks.create-template')->with('roles', $roles);
    }

    public function storeTemplate(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'title' => 'required',
            'items.*.title' => 'required',
            'items.*.start_delay' => 'required',
            'items.*.end_delay' => 'required',
        ], [], [
            'title' => '«Название шаблона»',
            'items.*.title' => '«Описание задачи»',
            'items.*.start_delay' => '«Начало выполнения»',
            'items.*.end_delay' => '«Дедлайн»',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json(['success' => 'false', 'error' => $error], 200);
        }

        foreach ($req->items as $r_item) {
            if ($r_item['all_users'] == '1') {
                if (!$r_item['to_user_roles']) {
                    return response()->json(['success' => 'false', 'error' => 'Выберите роли сотрудников'], 200);
                }
            } else
            if ($r_item['all_users'] == '0') {
                if (!$r_item['to_user_ids']) {
                    return response()->json(['success' => 'false', 'error' => 'Выберите сотрудников'], 200);
                }
            }

            if ($r_item['model_name'] && !$r_item['model_obj_id']) {
                return response()->json(['success' => 'false', 'error' => 'Выберите кого касается задача'], 200);
            }
        }

        $item = new TaskTemplate;

        $item->user_id = Auth::user()->id;
        $item->title = $req->title;
        $item->description = $req->description ?: null;
        $item->scheme = json_encode($req->items);

        $item->save();

        return response()->json(['success' => 'true'], 200);
    }

    public function showTemplate($id)
    {
        $roles = Role::with('Users')->get();

        return view('tasks.show-template')
            ->with('id', $id)
            ->with('roles', $roles);
    }

    public function showTemplateJson($id)
    {
        $item = TaskTemplate::find($id);

        $scheme = [];

        foreach (json_decode($item->scheme) as $sch) {
            if ($sch->model_name) {
                if ($sch->model_name == 'car') {
                    if ($mo = Car::find($sch->model_obj_id)) {
                        $sch->model_obj_title = $mo->brand;
                    }
                } else 
                if ($sch->model_name == 'lead') {
                    if ($mo = Lead::find($sch->model_obj_id)) {
                        $sch->model_obj_title = $mo->name;
                    }
                } else 
                if ($sch->model_name == 'candidate') {
                    if ($mo = Candidate::find($sch->model_obj_id)) {
                        $sch->model_obj_title = $mo->firstName . ' ' . $mo->lastName;
                    }
                } else 
                if ($sch->model_name == 'vacancy') {
                    if ($mo = Vacancy::find($sch->model_obj_id)) {
                        $sch->model_obj_title = $mo->title;
                    }
                } else 
                if ($sch->model_name == 'housing') {
                    if ($mo = Housing::find($sch->model_obj_id)) {
                        $sch->model_obj_title = $mo->id . '' . $mo->address;
                    }
                } else 
                if ($sch->model_name == 'client') {
                    if ($mo = Client::find($sch->model_obj_id)) {
                        $sch->model_obj_title = $mo->name;
                    }
                }
            }

            $scheme[] = $sch;
        }

        $item->items = $scheme;

        return response()->json($item, 200);
    }

    public function editTemplate($id)
    {
        $roles = Role::with('Users')->get();

        return view('tasks.edit-template')
            ->with('id', $id)
            ->with('roles', $roles);
    }

    public function updateTemplate(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'title' => 'required',
            'items.*.title' => 'required',
            'items.*.start_delay' => 'required',
            'items.*.end_delay' => 'required',
        ], [], [
            'title' => '«Название шаблона»',
            'items.*.title' => '«Описание задачи»',
            'items.*.start_delay' => '«Начало выполнения»',
            'items.*.end_delay' => '«Дедлайн»',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json(['success' => 'false', 'error' => $error], 200);
        }

        foreach ($req->items as $r_item) {
            if ($r_item['all_users'] == '1') {
                if (!$r_item['to_user_roles']) {
                    return response()->json(['success' => 'false', 'error' => 'Выберите роли сотрудников'], 200);
                }
            } else
            if ($r_item['all_users'] == '0') {
                if (!$r_item['to_user_ids']) {
                    return response()->json(['success' => 'false', 'error' => 'Выберите сотрудников'], 200);
                }
            }

            if ($r_item['model_name'] && !$r_item['model_obj_id']) {
                return response()->json(['success' => 'false', 'error' => 'Выберите кого касается задача'], 200);
            }
        }

        $item = TaskTemplate::find($req->id);

        if (!$item) {
            return response()->json(['success' => 'false', 'error' => 'Шаблон не найден'], 200);
        }

        $item->title = $req->title;
        $item->description = $req->description ?: null;
        $item->scheme = json_encode($req->items);

        $item->save();

        return response()->json(['success' => 'true'], 200);
    }
}
