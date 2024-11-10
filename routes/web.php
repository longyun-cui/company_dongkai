<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
////    dd(0);
////    return view('welcome');
//});




/*
 * Common 通用功能
 */
Route::group(['prefix'=>'common'], function () {

    $controller = "CommonController";

    // 验证码
    Route::match(['get','post'], 'change_captcha', $controller.'@change_captcha');

    //
    Route::get('dataTableI18n', function () {
        return trans('pagination.i18n');
    });
});






/*
 * DK 董凯
 */
Route::group(['domain'=>env('DOMAIN_ROOT'), 'namespace'=>'DK'], function () {
    require(__DIR__ . '/DK/route.php');
});
Route::group(['domain'=>env('DOMAIN_DK_SUPER'), 'namespace'=>'DK'], function () {
    require(__DIR__ . '/DK/route-super.php');
});
Route::group(['domain'=>env('DOMAIN_DK_ADMIN'), 'namespace'=>'DK'], function () {
    require(__DIR__ . '/DK/route-admin.php');
});
Route::group(['domain'=>env('DOMAIN_DK_STAFF'), 'namespace'=>'DK'], function () {
    require(__DIR__ . '/DK/route-staff.php');
});
Route::group(['domain'=>env('DOMAIN_DK_CLIENT'), 'namespace'=>'DK'], function () {
    require(__DIR__ . '/DK/route-client.php');
});
Route::group(['domain'=>env('DOMAIN_DK_FINANCE'), 'namespace'=>'DK'], function () {
    require(__DIR__ . '/DK/route-finance.php');
});
Route::group(['domain'=>env('DOMAIN_DK_ADMIN_2'), 'namespace'=>'DK'], function () {
    require(__DIR__ . '/DK/route-choice.php');
});
Route::group(['domain'=>env('DOMAIN_DK_CHOICE'), 'namespace'=>'DK'], function () {
    require(__DIR__ . '/DK/route-choice.php');
});
Route::group(['domain'=>env('DOMAIN_DK_CUSTOMER'), 'namespace'=>'DK'], function () {
    require(__DIR__ . '/DK/route-customer.php');
});


