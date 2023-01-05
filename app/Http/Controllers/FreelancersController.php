<?php

namespace App\Http\Controllers;

use App\Models\C_file;
use App\Models\User;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class FreelancersController extends Controller
{
    public function getIndex()
    {
        $invite_link = '';

        if (Auth::user()->isRecruiter()) {
            $invite_link = url('/') . '/recruiter/invite/' . $this->encodeLink();
        } elseif (Auth::user()->hasPermission('freelancer.create')) {
            $invite_link = url('/') . '/user/invite/' . $this->encodeLink();
        }

        $recruters = User::where('group_id', 2)->where('activation', 1)->get();
        $managers = User::where('group_id', 8)->where('activation', 1)->get();
        return view('freelansers.index')
            ->with('managers', $managers)
            ->with('invite_link', $invite_link)
            ->with('recruters', $recruters);
    }

    private function encodeLink()
    {
        $hash = Auth::user()->id . '&' . md5(Carbon::now()->format('d.m.Y') . Auth::user()->id . 'pasSwrd');
        $hash = base64_encode($hash);
        return $hash;
    }

    public function getInvite($id)
    {
        $res = $this->decodeLink($id);
        if ($res == '') {
            echo 'Ссылка больше не существует!';
            return '';
        }
        return view('freelansers.includes.inviteform')->with('u_id', $res->id);
    }

    private function decodeLink($hash)
    {
        $clean = strtr($hash, ' ', '+');
        $ascii = base64_decode($clean);
        $res = explode('&', $ascii);
        if (array_key_exists(0, $res) && array_key_exists(1, $res)) {

            $valid_hash = md5(Carbon::now()->format('d.m.Y') . $res[0] . 'pasSwrd');
            if ($valid_hash != $res[1]) {
                return '';
            }

            $recrutier = User::find($res[0]);
            return $recrutier;
        } else {
            return '';
        }
    }

    public function getInviteAdd(Request $r)
    {
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

        $user = self::addUser($r, $r->user_id, $r->user_id);

        Auth::loginUsingId($user->id);

        return response(array('success' => "true"), 200);
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
        $fl_status = request('fl_status');
        $search = request('search');

        $filtered_count = $this->prepareGetJsonRequest($fl_status, $search);
        $filtered_count = $filtered_count->count();

        $users = $this->prepareGetJsonRequest($fl_status, $search);
        $users = $users->orderBy($order_col, $order_direction);

        $users = $users
            ->with('D_file')
            ->with('Recruter')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data = [];

        $checkbox = '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input" type="checkbox" value="1">
                                                </div>';


        foreach ($users as $u) {
            if (config('app.env') === 'local') {
                $path_url = url('/');
            } else {
                $path_url = url('/') . '/public';
            }
            if ($u->D_file != null) {
                $file = '<a target="_blank" href="' . $path_url . $u->D_file->path . '" style="cursor: pointer;" class="svg-icon svg-icon-2x svg-icon-primary me-4">
																<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																	<path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="currentColor"></path>
																	<path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor"></path>
																</svg>
															</a>';
            } else {
                $file = '';
            }

            if (Auth::user()->group_id == 1 || Auth::user()->group_id == 8) {
                if ($u->fl_status == 1 || $u->fl_status == '') {
                    $select_active = '<select onchange="changeFl_status(' . $u->id . ')"
                                    class="form-select form-select-sm form-select-solid changeActivation' . $u->id . '">
                                    <option selected value="1">Новый</option>
                                            <option value="2">Верифицирован</option>
                                            <option value="3">Уволен</option>
                            </select>';
                } else if ($u->fl_status == 2) {
                    $select_active = '<select onchange="changeFl_status(' . $u->id . ')"
                                    class="form-select form-select-sm form-select-solid changeActivation' . $u->id . '">
                                    <option  value="1">Новый</option>
                                            <option selected value="2">Верифицирован</option>
                                            <option value="3">Уволен</option>
                            </select>';
                } else if ($u->fl_status == 3) {
                    $select_active = '<select onchange="changeFl_status(' . $u->id . ')"
                                    class="form-select form-select-sm form-select-solid changeActivation' . $u->id . '">
                                    <option   value="1">Новый</option>
                                            <option value="2">Верифицирован</option>
                                            <option selected value="3">Уволен</option>
                            </select>';
                }
            } else {
                $select_active = $u->getFl_status();
            }


            $Recruter = '';
            if ($u->Recruter != null) {
                $Recruter = $u->Recruter->firstName . ' ' . $u->Recruter->lastName;
            }
            $Manager = '';
            if ($u->Manager != null) {
                $Manager = $u->Manager->firstName . ' ' . $u->Manager->lastName;
            }

            $temp_arr = [

                Auth::user()->hasPermission('freelancer.edit')
                    ? '<a href="javascript:;" onclick="editUser(' . $u->id . ')">' . $u->id . '</a>'
                    : '<span>' . $u->id . '<span>',
                $u->firstName,
                $u->lastName,
                $Manager,
                $Recruter,
                $u->phone,
                $u->email,
                $file,
                $select_active

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

    private function prepareGetJsonRequest($fl_status, $search)
    {
        $users = User::where('activation', 1);
        $users = $users->where('group_id', 3);

        if ($fl_status) {
            $users = $users->whereIn('fl_status', $fl_status);
        }

        if (Auth::user()->isRecruiter()) {
            $users = $users->where('recruter_id', Auth::user()->id);
        }

        if (Auth::user()->isSupportManager()) {
            $users = $users->where('manager_id', Auth::user()->id);
        }

        if ($search != '') {
            $users = $users->where(function ($query) use ($search) {
                $query->where('firstName', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                    ->orWhere('lastName', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search . '%');
            });
        }

        return $users;
    }

    function setFlStatus(Request $r)
    {
        User::where('id', $r->id)->update(['fl_status' => $r->s]);
        return response(array('success' => "true"), 200);
    }

    public static function addUser($r, $autor_id, $user_id = null)
    {
        $user = new User;
        $user->group_id = 3;
        $user->activation = 1;
        $user->fl_status = 1;
        $user->recruter_id = $r->recruter_id ?: null;
        $user->user_id = $user_id ?: null;
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
        $user->password = Hash::make($r->password);
        $user->remember_token = Hash::make($user->password);

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
        $task->autor_id = $autor_id;
        $task->to_user_id = $target_manager_id;
        $task->status = 1;
        $task->type = 7;
        $task->title = Task::getTypeTitle($task->type);
        $task->freelancer_id = $user->id;
        $task->save();

        return $user;
    }
}
