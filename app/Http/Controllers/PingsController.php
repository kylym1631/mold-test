<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Auth;

class PingsController extends Controller
{
    public function index(Request $req)
    {
        $logout = false;
        $user = Auth::user();

        if (isset($req->activity) && $req->activity > 0) {
            $user->was_online_at = Carbon::now();
            $user->save();
        }

        if (
            Auth::user()->isRecruiter()
            && Auth::user()->was_online_at !== null
            && Auth::user()->was_online_at < Carbon::now()->subMinutes(10)
        ) {
            $logout = true;
        }

        $tasks_count = TaskController::getUnfinished();

        return response(array(
            'success' => "true", 
            'tasksCount' => $tasks_count,
            'logout' => $logout,
        ), 200);
    }
}
