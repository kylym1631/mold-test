<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UsersService
{
    public function prepareGetJsonRequest($filter__status, $search, $group = [])
    {
        if ($filter__status != '') {
            $users = User::where('activation', $filter__status);
        } else {
            $users = User::where('activation', 1);
        }

        $users = $users->whereNot('id', Auth::user()->id);

        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('user.viewAnothers')) {
            $users = $users->where('user_id', Auth::user()->id);
        }

        if (!Auth::user()->isAdmin()) {
            $role_ids = Auth::user()->getChildPermissions('user.view.role.');
            $users = $users->whereIn('group_id', $role_ids);
        }

        if ($search != '') {
            $users = $users->where(function ($query) use ($search) {
                $query->where('firstName', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                    ->orWhere('lastName', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search . '%');
            });
        }

        if ($group) {
            $users = $users->whereIn('group_id', $group);
        }

        return $users;
    }
}