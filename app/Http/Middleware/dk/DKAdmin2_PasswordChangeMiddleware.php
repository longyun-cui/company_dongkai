<?php

namespace App\Http\Middleware\dk;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Auth, Response;

class DKAdmin2_PasswordChangeMiddleware
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
        $me_admin = Auth::guard('dk_admin_2')->user();
        // 判断用户是否初始密码
        if(password_check(env('Initial_Password_Admin_2',"12345678"),$me_admin->password))
        {
            return redirect('/my-account/my-password-change');
        }

        return $next($request);
    }

}
