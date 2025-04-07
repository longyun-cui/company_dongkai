<?php

namespace App\Http\Middleware;

use App\Models\DK\DK_Department;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Auth, Response;

class YHAdminLoginMiddleware
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
        if(!Auth::guard('yh_admin')->check()) // 未登录
        {
            return redirect('/login');

//            $return["status"] = false;
//            $return["log"] = "admin-no-login";
//            $return["msg"] = "请先登录";
//            return Response::json($return);
        }
        else
        {
            $me_admin = Auth::guard('yh_admin')->user();
            // 判断用户是否重新登录
            if($me_admin->admin_token == 'logout')
            {
                Auth::guard('yh_admin')->logout();
                return redirect('/login');
            }
            // 判断用户是否被封禁
            if($me_admin->user_status != 1)
            {
                Auth::guard('yh_admin')->logout();
                return redirect('/login');
            }

            $department_district_id = $me_admin->department_district_id;
            $department_group_id = $me_admin->department_group_id;

            if($department_district_id > 0)
            {
                $department_district = DK_Department::find($department_district_id);
                if($department_district)
                {
                    if($department_district->item_status != 1)
                    {
                        return redirect('/logout');
                    }
                }
                else return redirect('/logout');
            }


            if($department_group_id > 0)
            {
                $department_group = DK_Department::find($department_group_id);
                if($department_group)
                {
                    if($department_group->item_status != 1)
                    {
                        return redirect('/logout');
                    }
                }
                else return redirect('/logout');
            }

            view()->share('me_admin', $me_admin);
        }
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $me_admin = Auth::guard('yh_admin')->user();
        view()->share('me', $me_admin);
    }
}
