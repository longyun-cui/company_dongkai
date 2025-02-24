<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

        'wx.share' => \App\Http\Middleware\WXShareMiddleware::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'home' => \App\Http\Middleware\HomeMiddleware::class,
        'notification' => \App\Http\Middleware\NotificationMiddleware::class,
        'login' => \App\Http\Middleware\LoginMiddleware::class,
        'login.turn' => \App\Http\Middleware\TurnToLoginMiddleware::class,

        'yh.user.login' => \App\Http\Middleware\YHUserLoginMiddleware::class,
        'yh.super.login' => \App\Http\Middleware\YHSuperLoginMiddleware::class,
        'yh.admin.login' => \App\Http\Middleware\YHAdminLoginMiddleware::class,
        'yh.staff.login' => \App\Http\Middleware\YHStaffLoginMiddleware::class,



        'dk.cc.login' => \App\Http\Middleware\dk\DKCCLoginMiddleware::class,
        'dk.cc.password_change' => \App\Http\Middleware\dk\DKCCPasswordChangeMiddleware::class,


        'dk.admin.login' => \App\Http\Middleware\dk\DKAdminLoginMiddleware::class,
        'dk.admin.password_change' => \App\Http\Middleware\dk\DKAdminPasswordChangeMiddleware::class,

        'dk.agency.login' => \App\Http\Middleware\dk\DKAgencyLoginMiddleware::class,
        'dk.agency.password_change' => \App\Http\Middleware\dk\DKAgencyPasswordChangeMiddleware::class,

        'dk.client.login' => \App\Http\Middleware\dk\DKClientLoginMiddleware::class,
        'dk.client.staff.login' => \App\Http\Middleware\dk\DKClientStaffLoginMiddleware::class,

        'dk.finance.user.login' => \App\Http\Middleware\dk\DKFinanceUserLoginMiddleware::class,
        'dk.finance.password_change' => \App\Http\Middleware\dk\DKFinancePasswordChangeMiddleware::class,


        'dk.admin_2.login' => \App\Http\Middleware\dk\DKAdmin2_LoginMiddleware::class,
        'dk.admin_2.password_change' => \App\Http\Middleware\dk\DKAdmin2_PasswordChangeMiddleware::class,

        'dk.customer.login' => \App\Http\Middleware\dk\DKCustomerLoginMiddleware::class,
        'dk.customer.staff.login' => \App\Http\Middleware\dk\DKCustomerStaffLoginMiddleware::class,
        'dk.customer.password_change' => \App\Http\Middleware\dk\DKCustomerPasswordChangeMiddleware::class,
    ];
}
