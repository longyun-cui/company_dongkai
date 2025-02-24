<?php

use App\Models\DK\DK_Client;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'users',
        ],


        'yh_user' => [
            'driver' => 'session',
            'provider' => 'yh_users',
        ],
        'yh_super' => [
            'driver' => 'session',
            'provider' => 'yh_supers',
        ],
        'yh_admin' => [
            'driver' => 'session',
            'provider' => 'yh_admins',
        ],
        'yh_staff' => [
            'driver' => 'session',
            'provider' => 'yh_staffs',
        ],




        'dk_cc' => [
            'driver' => 'session',
            'provider' => 'dk_ccs',
        ],


        'dk_admin' => [
            'driver' => 'session',
            'provider' => 'dk_admins',
        ],


        'dk_agency' => [
            'driver' => 'session',
            'provider' => 'dk_agency',
        ],
        'dk_agency_staff' => [
            'driver' => 'session',
            'provider' => 'dk_agency_staffs',
        ],


        'dk_client' => [
            'driver' => 'session',
            'provider' => 'dk_clients',
        ],
        'dk_client_staff' => [
            'driver' => 'session',
            'provider' => 'dk_clients_staffs',
        ],




        'dk_finance_user' => [
            'driver' => 'session',
            'provider' => 'dk_finance_users',
        ],




        'dk_admin_2' => [
            'driver' => 'session',
            'provider' => 'dk_admins_2',
        ],


        'dk_customer' => [
            'driver' => 'session',
            'provider' => 'dk_customers',
        ],
        'dk_customer_staff' => [
            'driver' => 'session',
            'provider' => 'dk_customers_staffs',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\User::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Administrator::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],


        'yh_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\DK\DK_User::class,
        ],

        'yh_supers' => [
            'driver' => 'eloquent',
            'model' => App\Models\DK\DK_User::class,
        ],

        'yh_admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\DK\DK_User::class,
        ],

        'yh_staffs' => [
            'driver' => 'eloquent',
            'model' => App\Models\DK\DK_User::class,
        ],




        'dk_ccs' => [
            'driver' => 'eloquent',
            'model' => App\Models\DK\DK_User::class,
        ],




        'dk_admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\DK\DK_User::class,
        ],


        'dk_agency' => [
            'driver' => 'eloquent',
            'model' => App\Models\DK\DK_Company::class,
        ],
        'dk_agency_staffs' => [
            'driver' => 'eloquent',
            'model' => App\Models\DK_Company\DK_Company_User::class,
        ],


        'dk_clients' => [
            'driver' => 'eloquent',
            'model' => App\Models\DK\DK_Client::class,
        ],
        'dk_clients_staffs' => [
            'driver' => 'eloquent',
            'model' => App\Models\DK_Client\DK_Client_User::class,
        ],




        'dk_finance_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\DK_Finance\DK_Finance_User::class,
        ],




        'dk_admins_2' => [
            'driver' => 'eloquent',
            'model' => App\Models\DK_Choice\DK_Choice_User::class,
        ],


        'dk_customers' => [
            'driver' => 'eloquent',
            'model' => App\Models\DK_Choice\DK_Choice_Customer::class,
        ],
        'dk_customers_staffs' => [
            'driver' => 'eloquent',
            'model' => App\Models\DK_Customer\DK_Customer_User::class,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
        ],
    ],

];
