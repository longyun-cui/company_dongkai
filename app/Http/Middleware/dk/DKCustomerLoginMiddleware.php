<?php

namespace App\Http\Middleware\dk;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Auth, Response;

class DKCustomerLoginMiddleware
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
        if(!Auth::guard('dk_customer')->check()) // 未登录
        {
            return redirect('/login');

//            $return["status"] = false;
//            $return["log"] = "admin-no-login";
//            $return["msg"] = "请先登录";
//            return Response::json($return);
        }
        else
        {
            $me_admin = Auth::guard('dk_customer')->user();
            // 判断用户是否重新登录
            if($me_admin->admin_token == 'logout')
            {
                Auth::guard('dk_customer')->logout();
                return redirect('/login');
            }
            // 判断用户是否被封禁
            if($me_admin->user_status != 1)
            {
                Auth::guard('dk_customer')->logout();
                return redirect('/login');
            }
            view()->share('me_admin', $me_admin);
        }
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $me_admin = Auth::guard('dk_customer')->user();
        view()->share('me', $me_admin);
    }
}
