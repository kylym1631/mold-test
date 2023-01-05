<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Roles
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $params)
    {
        $success = false;
        $roles_or_permissions = explode('|', $params);

        foreach ($roles_or_permissions as $val) {
            if ($val == Auth::user()->group_id || Auth::user()->hasPermission($val)) {
                $success = true;
            }
        }

        if ($success) {
            return $next($request);
        } else {
            if ($request->ajax()) {
                return response(array('success' => "false", 'error' => 'У вас недостаточно прав'), 200);
            }

            return redirect(url('/'));
        }
    }
}
