<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        'login',
        'email/*',
        'testing/*',
        'api/receive/*',
        'api/by/*',
        'api_cc/receive/*',
        'cc_api/receive/*',
        'cc_api/okcc/*',
        'choice_api/okcc/*'
    ];


    public function handle($request, Closure $next)
    {
        // api.xx.com 完全不走 CSRF（无状态 API）
//        if ($request->getHost() === env('DOMAIN_DK_API'))
        if ($request->getSchemeAndHttpHost() === env('DOMAIN_DK_API'))
        {
            return $next($request);
        }

        return parent::handle($request, $next);
    }


}
