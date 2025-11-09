<?php

namespace App\Http\Middleware;

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
        'email/*',
        'testing/*',
        'api/receive/*',
        'api/by/*',
        'api_cc/receive/*',
        'cc_api/receive/*',
        'cc_api/okcc/*',
        'choice_api/okcc/*'
    ];
}
