<?php

namespace App\Http\Middleware\dk;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Auth, Response;

class DKCustomerStaffLoginMiddleware
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
        if(!Auth::guard('dk_customer_staff')->check()) // 未登录
        {
            return redirect('/login');

//            $return["status"] = false;
//            $return["log"] = "admin-no-login";
//            $return["msg"] = "请先登录";
//            return Response::json($return);
        }
        else
        {
            $me_staff = Auth::guard('dk_customer_staff')->user();
            // 判断用户是否重新登录
            if($me_staff->admin_token == 'logout')
            {
                Auth::guard('dk_customer_staff')->logout();
                return redirect('/login');
            }
            $me_staff->load('client_er');
            // 判断所属客户是否被封禁
            if($me_staff->client_er->user_status != 1)
            {
                Auth::guard('dk_customer_staff')->logout();
                return redirect('/login');
            }
            // 判断用户是否被封禁
            if($me_staff->user_status != 1)
            {
                Auth::guard('dk_customer_staff')->logout();
                return redirect('/login');
            }
            view()->share('me_client', $me_staff);
        }
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $me_staff = Auth::guard('dk_customer_staff')->user();
        view()->share('me', $me_staff);
    }
}
