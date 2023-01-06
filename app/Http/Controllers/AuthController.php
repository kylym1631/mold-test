<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\TaskController;
use App\Services\LeadsService;

class AuthController extends Controller
{

    public function getMain()
    {
        if (Auth::check()) {
            return Redirect::to('dashboard');
        }
        return Redirect::to('login');
    }

    public function getLogin()
    {
        return view('auth.login', array('title' => 'Login'));
    }

    public function postLogin()
    {
        $login = request()->get('login');
        $password = request()->get('password');

        $user = User::where('email', $login)->first();

        if ($user && $user->password_fails >= 3) {
            if ($user->updated_at > Carbon::now()->subMinutes(3)) {
                $remain = $user->updated_at->diffInMinutes(Carbon::now()->subMinutes(3)) + 1;

                return Response::json(array('success' => "false", 'error' => 'Ваш аккаунт заблокирован. Вход будет доступен через '. $remain .' мин.'), 200);
            } else {
                $user->password_fails = 0;
                $user->save();
            }
        }

        if (Auth::attempt(array('email' => $login, 'password' => $password))) {
            // Проверить активирован или нет
            if (Auth::user()->activation == 1) {
                $user = Auth::user();
                $user->was_online_at = Carbon::now();
                $user->password_fails = 0;
                $user->save();

                return Response::json(array('success' => "true", 'group' => Auth::user()->group_id), 200);
            } else {
                Auth::logout();
                return Response::json(array('success' => "false", 'error' => 'Ваш доступ больше не активен'), 200);
            }
        } else {
            $user = User::where('email', $login)->first();

            if ($user) {
                $user->password_fails = $user->password_fails + 1;
                $user->save();

                if ($user->password_fails >= 3) {
                    return Response::json(array('success' => "false", 'error' => 'Ваш аккаунт заблокирован. Вход будет доступен через 3 мин.'), 200);
                }

                $fc = 3 - $user->password_fails;

                return Response::json(array(
                    'success' => "false",
                    'error' => 'Не верный пароль. Осталось '. $fc . ($fc == 2 ? ' попытки.' : ' попытка.'),
                ), 200);
            }

            return Response::json(array('success' => "false", 'error' => 'Не верный логин или пароль'), 200);
        }
    }

    public function getLogout()
    {
        // if (Auth::user() && Auth::user()->isRecruiter()) {
        //     $user_id = Auth::user()->id;
        //     $ls = new LeadsService;
        //     $ls->resetLeadTasksForUser($user_id);
        // }

        Auth::logout();
        return Redirect::to(URL::to('/') . '/');
    }

    public function postAuthById(Request $r)
    {
        $u = User::find($r->id);
        $u->was_online_at = Carbon::now();
        $u->save();

        if(Auth::check()){
            if(Auth::user()->isAdmin()){
                Auth::loginUsingId($r->id);
            }

            if(Auth::user()->isRecruitmentDirector()){
                $user = User::find($r->id);

                if($user->user_id == Auth::user()->id){
                    Auth::loginUsingId($r->id);
                }
            }

            if(Auth::user()->isSupportManager()){
                $user = User::find($r->id);
                if($user->group_id == 3){
                    Auth::loginUsingId($r->id);
                }
            }
        }
        return Redirect::to(URL::to('/') . '/');
    }


    public function getSignup()
    {
        return view('auth.signup', array('title' => 'Signup'));
    }

    public function postSignup()
    {

        if (!request()->has('password') || request('password') == '') {
            return response(array('success' => "false", 'error' => "Error! Fill in the required fields!"), 200);
        }
        if (!request()->has('confirm_password') || request('confirm_password') == '') {
            return response(array('success' => "false", 'error' => "Error! Fill in the required fields!"), 200);
        }
        if (!request()->has('name') || request('name') == '') {
            return response(array('success' => "false", 'error' => "Error! Fill in the required fields!"), 200);
        }
        if (request()->has('password') && request()->has('confirm_password')
            && request('password') != request('confirm_password')) {
            return Response::json(array('success' => "false", 'error' => 'Passwords do not match'), 200);
        }

        if (\request()->has('login') && \request('login') != '') {
            $email = User::where('email', request('login'))->first();
            if ($email != null) {
                return response(array('success' => "false", 'error' => "Error! The user with such email is registered!"), 200);
            }
            $email_req = \request('login');
        } else {
            return response(array('success' => "false", 'error' => "Error! Fill in the required fields!"), 200);
        }

        $user = new User();
        $user->name = request('name');
        $user->group_id = request('role');
        $user->email = request('login');
        $user->password = Hash::make(request('password'));
        $user->remember_token = Hash::make($user->password);
        $user->activation = 1;

        if ($user->save()) {
            Auth::loginUsingId($user->id);
            return response(array('success' => "true"), 200);
        } else {
            return response(array('success' => "false", 'error' => "Error!"), 200);
        }
    }


}

