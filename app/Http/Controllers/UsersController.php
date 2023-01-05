<?php

namespace App\Http\Controllers;

use App\Models\C_file;
use App\Models\Task;
use App\Models\User;
use App\Models\UserOption;
use App\Models\UserPermission;
use App\Models\LeadSetting;
use App\Models\Role;
use App\Services\StatisticsService;
use App\Services\UsersService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UsersController extends Controller
{
    public function getIndex()
    {
        $roles = Role::all();

        return view('users.index', compact('roles'));
    }

    public function getJson(UsersService $us)
    {

        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length"); // Rows display per page

        $filter__status = request('status');
        $search = request('search');
        $group = request('group');

        $filtered_count = $us->prepareGetJsonRequest($filter__status, $search, $group);
        $filtered_count = $filtered_count->count();

        $users = $us->prepareGetJsonRequest($filter__status, $search, $group);

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
            ->with('D_file')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data = [];

        $checkbox = '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input" type="checkbox" value="1">
                                                </div>';


        foreach ($users as $u) {
            $select_active = $u->activation == 1 ? 'Активный' : 'Не активный';

            if ($u->D_file != null) {

                if (config('app.env') === 'local') {
                    $path_url = url('/');
                } else {
                    $path_url = url('/') . '/public';
                }

                $file = '<a target="_blank" href="' . $path_url . $u->D_file->path . '" style="cursor: pointer;" class="svg-icon svg-icon-2x svg-icon-primary me-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="currentColor"></path>
                        <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor"></path>
                    </svg>
                </a>';
            } else {
                $file = '';
            }

            if (
                Auth::user()->isAdmin()
                || (Auth::user()->hasPermission('user.edit')
                    && Auth::user()->hasPermission('user.edit.role.' . $u->group_id))
            ) {
                if ($u->activation == 1) {
                    $select_active = '<select onchange="changeActivation(' . $u->id . ')"
                                    class="form-select form-select-sm form-select-solid changeActivation' . $u->id . '">
                                <option selected value="1">Активный</option>
                                <option value="2">Не активный</option>
                            </select>';
                } else {
                    $select_active = '<select onchange="changeActivation(' . $u->id . ')"
                                    class="form-select form-select-sm form-select-solid changeActivation' . $u->id . '">
                                <option value="1">Активный</option>
                                <option selected value="2">Не активный</option>
                            </select>';
                }
            }

            if (Auth::user()->isAdmin() || Auth::user()->isRecruitmentDirector()) {
                $login = ' <a href="' . url('/') . '/a/id?id=' . $u->id . '">войти</a>';
            } elseif (Auth::user()->isSupportManager()) {
                if ($u->group_id == 3) {
                    $login = ' <a href="' . url('/') . '/a/id?id=' . $u->id . '">войти</a>';
                } else {
                    $login = '';
                }
            } else {
                $login = '';
            }

            $temp_arr = [
                (Auth::user()->isAdmin() || (Auth::user()->hasPermission('user.edit') && Auth::user()->hasPermission('user.edit.role.' . $u->group_id)))
                    ? '<a href="javascript:;" onclick="editUser(' . $u->id . ')">' . $u->id . '</a>'
                    : '<span>' . $u->id . '<span>',
                mb_strtoupper($u->firstName) . $login,
                mb_strtoupper($u->lastName),
                $u->getGroup(),
                $u->phone,
                $u->email,
                $file,
                $select_active,
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

    public function withLeadSettingsJson(UsersService $us)
    {
        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length");

        $filter__status = request('status');
        $search = request('search');
        $group = [2];

        $filtered_count = $us->prepareGetJsonRequest($filter__status, $search, $group);
        $filtered_count = $filtered_count->count();

        $users = $us->prepareGetJsonRequest($filter__status, $search, $group);

        $users = $users
            ->with('LeadsSettings')
            ->orderBy('id', 'DESC')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data = [];

        foreach ($users as $u) {
            $leads_settings = [];

            if ($u->LeadsSettings) {
                foreach ($u->LeadsSettings as $LeadSetting) {
                    $leads_settings[] = (int) $LeadSetting->value;
                }
            }

            $data[] = [
                'id' => $u->id,
                'firstName' => mb_strtoupper($u->firstName),
                'lastName' => mb_strtoupper($u->lastName),
                'leads_settings' => $leads_settings,
            ];
        }


        return Response::json(array(
            'data' => $data,
            "draw" => $draw,
            'recordsTotal' => $filtered_count,
            'recordsFiltered' => $filtered_count,
        ), 200);
    }

    public function addUser(Request $r)
    {
        if ($r->group_id == 1 && Auth::user()->group_id != 1) {
            return response(array('success' => "false", 'error' => 'У вас нет разрешения создавать администратора'), 200);
        }

        $user = User::find($r->id);
        if ($user == null) {
            if (
                !Auth::user()->isAdmin()
                && !Auth::user()->hasPermission('user.create.role.' . $r->group_id)
            ) {
                return response(array('success' => "false", 'error' => 'У вас нет разрешения создавать эту роль'), 200);
            }

            $user = new User();

            $validator = Validator::make($r->all(), [
                'password' => ['required', Password::min(10)],
                'firstName' => 'required',
                'lastName' => 'required',
                'phone' => 'required|regex:/\+[0-9]{9,12}/',
                'email' => 'required|email:rfc,dns|unique:users,email',
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response(array('success' => "false", 'error' => $error), 200);
            }
        } else {
            if (
                !Auth::user()->isAdmin()
                && !Auth::user()->hasPermission('user.edit.role.' . $user->group_id)
            ) {
                return response(array('success' => "false", 'error' => 'У вас нет разрешения редактировать эту роль'), 200);
            }

            $validator = Validator::make($r->all(), [
                'email' => 'required|email:rfc,dns|unique:users,email,' . $user->id,
                'firstName' => 'required',
                'lastName' => 'required',
                'phone' => 'required|regex:/\+[0-9]{9,12}/',
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response(array('success' => "false", 'error' => $error), 200);
            }
        }

        $user->email = $r->email;
        $user->group_id = $r->group_id;
        $user->activation = $r->activation;
        $user->lang = $r->lang;
        if ($r->has('user_id')) {
            $user->user_id = $r->user_id;
        }
        if ($r->has('firstName')) {
            $user->firstName = $r->firstName;
        }
        if ($r->has('lastName')) {
            $user->lastName = $r->lastName;
        }
        if ($r->has('phone')) {
            $user->phone = $r->phone;
        }
        if ($r->has('account')) {
            $user->account = $r->account;
        }

        if ($r->has('password') && $r->password != '') {
            $user->password = Hash::make($r->password);
            $user->remember_token = Hash::make($user->password);
        }

        $user->save();

        if (Auth::user()->isAdmin() || Auth::user()->isRecruitmentDirector()) {
            UserOption::where('user_id', $user->id)->where('key', 'leads_settings')->delete();

            if ($r->has('leads_settings')) {
                if ($r->leads_settings) {
                    foreach ($r->leads_settings as $val) {
                        $uo = new UserOption;
                        $uo->user_id = $user->id;
                        $uo->key = 'leads_settings';
                        $uo->value = $val;
                        $uo->save();
                    }
                }
            }
        }

        if (Auth::user()->isAdmin()) {
            UserPermission::where('user_id', $user->id)->delete();

            if ($r->permissions) {
                foreach ($r->permissions as $val) {
                    $p = new UserPermission;
                    $p->user_id = $user->id;
                    $p->alias = $val;
                    $p->save();
                }
            }
        }

        return response(array('success' => "true"), 200);
    }

    public function activateLeadSettings(Request $r)
    {
        $user = User::find($r->id);

        if (
            !Auth::user()->isAdmin()
            && !Auth::user()->hasPermission('user.edit.role.' . $user->group_id)
        ) {
            return response(array('success' => "false", 'error' => 'У вас нет разрешения редактировать эту роль'), 200);
        }

        UserOption::where('user_id', $user->id)->where('key', 'leads_settings')->delete();

        if ($r->has('leads_settings')) {
            if ($r->leads_settings) {
                foreach ($r->leads_settings as $val) {
                    $uo = new UserOption;
                    $uo->user_id = $user->id;
                    $uo->key = 'leads_settings';
                    $uo->value = $val;
                    $uo->save();
                }
            }
        }

        return response(array('success' => "true"), 200);
    }

    public function addFlUser(Request $r)
    {
        $user = User::find($r->id);

        if ($user == null) {
            $user = new User();
            $user->group_id = 3;
            $user->activation = 1;
            $user->fl_status = 1;
            $user->user_id = Auth::user()->id;

            if (Auth::user()->isRecruiter()) {
                $user->recruter_id = Auth::user()->id;
            }

            if (Auth::user()->isAdmin()) {
                $user->recruter_id = $r->recruter_id;
                $user->manager_id = $r->manager_id;
            }

            $niceNames = [
                'lastName' => '«Фамилия»',
                'firstName' => '«Имя»',
                'phone' => '«Телефон»',
                'viber' => '«Viber»',
                'password' => '«Пароль»',
            ];

            $validator = Validator::make($r->all(), [
                'firstName' => 'required',
                'lastName' => 'required',
                'phone' => 'required|regex:/\+[0-9]{9,12}/|unique:users,phone',
                'viber' => 'required|regex:/\+[0-9]{9,12}/|unique:users,viber',
                'email' => 'required|email:rfc,dns|unique:users,email',
                'password' => ['required', Password::min(10)],
            ], [], $niceNames);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response(array('success' => "false", 'error' => $error), 200);
            }
        } else {
            $niceNames = [
                'lastName' => '«Фамилия»',
                'firstName' => '«Имя»',
                'phone' => '«Телефон»',
                'viber' => '«Viber»',
                'password' => '«Пароль»',
            ];

            $validator = Validator::make($r->all(), [
                'email' => 'required|email:rfc,dns|unique:users,email,' . $user->id,
                'phone' => 'required|regex:/\+[0-9]{9,12}/|unique:users,phone,' . $user->id,
                'viber' => 'required|regex:/\+[0-9]{9,12}/|unique:users,viber,' . $user->id,
                'firstName' => 'required',
                'lastName' => 'required',
            ], [], $niceNames);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response(array('success' => "false", 'error' => $error), 200);
            }

            if (Auth::user()->isAdmin()) {
                $user->recruter_id = $r->recruter_id;
                $user->manager_id = $r->manager_id;
            }
        }

        $user->email = $r->email;
        $user->firstName = $r->firstName;
        $user->lastName = $r->lastName;
        $user->phone = $r->phone;
        $user->viber = $r->viber;
        $user->facebook = $r->facebook;
        $user->account_type = $r->account_type;
        $user->account_poland = $r->account_poland;
        $user->account_paypal = $r->account_paypal;
        $user->account_bank_name = $r->account_bank_name;
        $user->account_iban = $r->account_iban;
        $user->account_card = $r->account_card;
        $user->account_swift = $r->account_swift;

        if ($r->has('password') && $r->password != '') {
            $user->password = Hash::make($r->password);
            $user->remember_token = Hash::make($user->password);
        }

        $exists = User::where('group_id', 3)->get();
        $managers = User::where('group_id', 8)->where('activation', 1)->get();
        $managers_count = array();

        foreach ($managers->toArray() as $manItem) {
            $managers_count[$manItem['id']] = 0;
        }

        foreach ($exists->toArray() as $item) {
            if ($item['manager_id']) {
                $managers_count[$item['manager_id']]++;
            }
        }

        $target_manager_id = null;
        $target_manager_count = 999999;

        foreach ($managers_count as $id => $count) {
            if ($count < $target_manager_count) {
                $target_manager_id = $id;
                $target_manager_count = $count;
            }
        }

        $user->manager_id = $target_manager_id;

        $user->save();

        $task = new Task();
        $task->start = Carbon::now();
        $task->autor_id = Auth::user()->id;
        $task->to_user_id = $target_manager_id;
        $task->status = 1;
        $task->type = 7;
        $task->title = Task::getTypeTitle($task->type);
        $task->freelancer_id = $user->id;
        $task->save();

        return response(array('success' => "true"), 200);
    }

    public function postProfile(Request $r)
    {
        $user = User::where('id', Auth::user()->id)->first();

        $validator = Validator::make($r->all(), [
            'email' => 'required|email:rfc,dns|unique:users,email,' . $user->id,
            'firstName' => 'required',
            'lastName' => 'required',
            'phone' => 'required|regex:/\+[0-9]{9,12}/',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        $user->email = $r->email;
        $user->firstName = $r->firstName;
        $user->lastName = $r->lastName;
        $user->phone = $r->phone;
        $user->lang = $r->lang;

        if ($r->has('password') && $r->password != '') {
            $user->password = Hash::make($r->password);
            $user->remember_token = Hash::make($user->password);
        }

        $user->save();
        return response(array('success' => "true"), 200);
    }

    public function filesUserAdd()
    {

        $user_id = request()->get('user_id');
        if ($user_id == '') {
            $user = new User();
            $user->email = Str::random(12) . '@test.com';
            $user->group_id = 1;
            $user->activation = 2;
            $user->save();
            $user_id = $user->id;
        } else {
            $C_files = C_file::where('user_id', $user_id)->where('type', 1)->get();
            foreach ($C_files as $C_file) {
                unlink(public_path() . $C_file->path);
                C_file::where('id', $C_file->id)->delete();
            }
        }

        $file = request()->file('file');
        if ($file->isValid()) {

            $path = '/uploads/users/' . Carbon::now()->format('m.Y') . '/' . $user_id . '/files/';
            $name = Str::random(12) . '.' . $file->getClientOriginalExtension();

            $file->move(public_path($path), $name);
            $file_link = $path . $name;


            $file = new C_file();
            $file->autor_id = Auth::user()->id;
            $file->user_id = $user_id;
            $file->type = 1;
            $file->original_name = request()->file('file')->getClientOriginalName();
            $file->ext = request()->file('file')->getClientOriginalExtension();
            $file->path = $file_link;
            $file->save();

            return Response::json(array(
                'success' => "true",
                'user_id' => $user_id,
                'path' => $file_link
            ), 200);
        }
    }

    public function getUserAjax($id)
    {
        $user = User::find($id);

        if ($user == null) {
            return response(array('success' => "false", 'error' => 'Пользователь не найден!'), 200);
        }

        $supervisor = '';

        if ($user->user_id) {
            $supv = User::find($user->user_id);
            if ($supv) {
                $supervisor = mb_strtoupper($supv->firstName . ' ' . $supv->lastName);
            }
        }

        $user->supervisor = $supervisor;

        if ($user->group_id == 2) {
            $leads_settings = UserOption::where('user_id', $id)
                ->where('key', 'leads_settings')
                ->pluck('value');

            $leads_settings = LeadSetting::whereIn('id', $leads_settings)->get();

            $user->leads_settings = $leads_settings->toArray();
        }

        if (Auth::user()->isAdmin()) {
            $user->permissions = UserPermission::where('user_id', $id)->pluck('alias');
        }

        return response(array('success' => "true", 'user' => $user), 200);
    }

    public function usersActivation(Request $r)
    {
        $user = User::where('id', $r->id)->first();

        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('user.edit.role.' . $user->group_id)) {
            return response(array('success' => "false", 'error' => 'У вас нет разрешения редактировать эту роль'), 200);
        }

        $user->activation = $r->s;
        $user->save();

        return response(array('success' => "true"), 200);
    }

    function authBy($id)
    {
        Auth::loginUsingId($id);
        return Redirect::to(url('/') . '/dashboard');
    }

    public function getProfile()
    {
        return view('users.profile.index');
    }

    public function getRecruitersRatingJson(StatisticsService $stat)
    {
        $users = User::select('id', 'firstName', 'lastName', 'group_id');
        $users = $users->where('group_id', 2);

        $users = $users->with([
            'RecruiterCandidates' => function ($query) {
                $query = $query->select('id', 'active_update', 'active', 'worked', 'recruiter_id', 'client_id', 'removed');
                $query->orderBy('id', 'desc');
            },
            'RecruiterCandidates.ActiveHistory' => function ($query) {
                $query = $query->select('id', 'model_name', 'model_obj_id', 'current_value', 'created_at', 'user_role');

                $query = $query->where('current_value', '4')
                    ->whereMonth('created_at', Carbon::now());

                $query->orderBy('id', 'desc');
            }
        ])->get();

        $result = [];

        foreach ($users as $k => $u) {
            $result[$k] = $u;
            $result[$k]['oform'] = 0;

            if ($u->id == Auth::user()->id) {
                $result[$k]['is_me'] = true;
            }

            if ($u->RecruiterCandidates) {
                $candidates_stat = $stat->candidates($u->RecruiterCandidates);
                $result[$k]['oform'] = $candidates_stat['4'];
            }
        }

        return response()->json($result, 200);
    }
}
