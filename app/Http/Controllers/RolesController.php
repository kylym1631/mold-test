<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;
use App\Models\RolePermission;

class RolesController extends Controller
{
    public function listView()
    {
        return view('roles.index');
    }

    public function listJson()
    {
        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length");
        $search = request()->get("search");

        $filtered_count = $this->prepareGetJsonRequest($search);
        $filtered_count = $filtered_count->count();

        $items = $this->prepareGetJsonRequest($search);
        $items = $items->orderBy('id', 'desc');

        $items = $items
            ->with('Permissions')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data = array_map(function ($item) {
            $permissions = [];

            if (isset($item['permissions']) && count($item['permissions'])) {
                foreach ($item['permissions'] as $Permission) {
                    $permissions[] = RolePermission::getName($Permission['alias']);
                }
            }

            $item['cur_permissions'] = $permissions;

            if ($item['id'] < 100) {
                $stat_perm = RolePermission::getStaticPermissions($item['id']);

                foreach ($stat_perm as $alias) {
                    array_push($item['cur_permissions'], RolePermission::getName($alias));
                }
            }

            return $item;
        }, $items->toArray());

        return response()->json([
            'data' => $data,
            'draw' => $draw,
            'recordsTotal' => $filtered_count,
            'recordsFiltered' => $filtered_count,
        ], 200);
    }

    private function prepareGetJsonRequest($search)
    {
        $items = Role::query();

        $items = $items->when($search, function ($query, $search) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        });

        return $items;
    }

    public function itemView(Request $req)
    {
        $item = null;

        if ($req->has('id')) {
            $item = Role::where('id', $req->id)
                ->with('Permissions')
                ->first();

            if (!$item) {
                return abort(404);
            }

            $permissions = [];

            if ($item->Permissions) {
                foreach ($item->Permissions as $Permission) {
                    $permissions[] = $Permission->alias;
                }
            }


            $item->cur_permissions = $permissions;

            if ($req->id < 100) {
                $stat_perm = RolePermission::getStaticPermissions($req->id);
                
                $item->static_permissions = $stat_perm;
                $item->cur_permissions = array_merge($item->cur_permissions, $stat_perm);
            }
        }

        if ($req->is('*roles/add*')) {
            return view('roles.add')->with('item', $item);
        } else {
            return view('roles.view')->with('item', $item);
        }
    }

    public function createOrUpdate(Request $req)
    {
        $is_new = true;
        $item = null;

        $validator = Validator::make($req->all(), [
            'name' => 'required',
        ], [], [
            'name' => '«Имя роли»',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json(['success' => 'false', 'error' => $error], 200);
        }

        if ($req->has('id')) {
            $is_new = false;
            $item = Role::find($req->id);
        } else {
            $is_new = true;
            $item = new Role;
        }

        if ($item) {
            if ($is_new || $item->id > 99) {
                $item->name = $req->name;
            }

            $item->save();

            if (!$is_new) {
                RolePermission::where('role_id', $item->id)->delete();
            }

            if ($req->permission) {
                foreach ($req->permission as $val) {
                    $p = new RolePermission;
                    $p->role_id = $item->id;
                    $p->alias = $val;
                    $p->save();
                }
            }

            return response()->json(['success' => 'true'], 200);
        } else {
            return response()->json(['success' => 'false', 'error' => 'Роль не найдена'], 200);
        }
    }
}
