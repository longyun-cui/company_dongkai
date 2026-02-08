<?php

namespace App\Http\Middleware\dk\staff;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Auth, Response;

class DKStaffUserLoginMiddleware
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
        if(!Auth::guard('dk_staff_user')->check()) // 未登录
        {
//            dd(6);
            return redirect('/login');

//            $return["status"] = false;
//            $return["log"] = "admin-no-login";
//            $return["msg"] = "请先登录";
//            return Response::json($return);
        }
        else
        {
            $me_user = Auth::guard('dk_staff_user')->user();
            // 判断用户是否被封禁
            if($me_user->item_status != 1)
            {
                Auth::guard('dk_staff_user')->logout();
                return redirect('/login');
            }
            view()->share('me_user', $me_user);
        }
        return $next($request);
    }
}
