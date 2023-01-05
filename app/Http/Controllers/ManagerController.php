<?php

namespace App\Http\Controllers;

use App\Models\C_file;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\FreelancersController;

class ManagerController extends Controller
{
    public function getIndex()
    {

        $invite_link = url('/') . '/manager/invite/' . $this->encodeLink();
        return view('manager.dashboard')->with('invite_link', $invite_link);
    }

    public function getInvite($id)
    {
        $res = $this->decodeLink($id);
        if ($res == '') {
            echo 'Ссылка больше не существует!';
            return '';
        }
        return view('manager.invite_freelancer')->with('rec_id', $res->id);
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

    private function encodeLink()
    {
        $hash = Auth::user()->id . '&' . md5(Carbon::now()->format('d.m.Y') . Auth::user()->id . 'pasSwrd');
        $hash = base64_encode($hash);
        return $hash;
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

        $user = FreelancersController::addUser($r, $r->manager_id);

        Auth::loginUsingId($user->id);

        return response(array('success' => "true"), 200);

    }

}
