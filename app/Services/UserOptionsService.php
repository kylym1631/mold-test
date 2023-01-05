<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserOption;

class UserOptionsService
{
    public function setLanguage($user_id, $lang)
    {
        if ($lang) {
            $user = User::find($user_id);
            $user->lang = $lang;
            $user->save();
        }
    }
}