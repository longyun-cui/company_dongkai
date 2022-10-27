<?php

namespace App\Http\Middleware;

use Closure;
use Auth, Response;
use Lib\Wechat\TokenManager;

class WXShareMiddleware
{

    public function handle($request, Closure $next)
    {
        if(env('APP_ENV') != 'local')
        {
            $wx_config = json_encode(TokenManager::getConfig());
            view()->share('wx_config', $wx_config);
        }

        return $next($request);
    }
}
