<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UsersService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AccountantsDepartmentController extends Controller
{
    public function listView()
    {
        $controller_name = 'AccountantsDepartmentController';
        $roles = Role::all();

        return view('users.index', compact(['controller_name', 'roles']));
    }

    public function listJson()
    {
        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length"); // Rows display per page

        //ordering
        $order_col = 'id';
        $order_direction = 'desc';
        $cols = request('columns');
        $order = request('order');

        // search
        $filter__status = request('status');
        $search = request('search');

        $filtered_count = $this->prepareGetJsonRequest($filter__status, $search);
        $filtered_count = $filtered_count->count();

        $users = $this->prepareGetJsonRequest($filter__status, $search);
        $users = $users->orderBy($order_col, $order_direction);

        $users = $users
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data = [];

        foreach ($users as $u) {
            $temp_arr = [
                '<span class="main-color">' . $u->id . '</span>',
                mb_strtoupper($u->firstName),
                mb_strtoupper($u->lastName),
                $u->phone,
                $u->email,
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

    public function prepareGetJsonRequest($filter__status, $search)
    {
        if ($filter__status != '') {
            $users = User::where('activation', $filter__status);
        } else {
            $users = User::where('activation', 1);
        }

        if ($search != '') {
            $users = $users->where(function ($query) use ($search) {
                $query->where('firstName', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                    ->orWhere('lastName', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search . '%');
            });
        }

        $users = $users->whereIn('group_id', [7])
            ->whereNot('id', Auth::user()->id);

        return $users;
    }
}
