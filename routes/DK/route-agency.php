<?php


Route::get('/me', function () {
    dd('route-agency');
});
Route::get('{any}', function () {
    dd('route-agency-any');
});


$controller = "DKAgencyController";

Route::match(['get','post'], 'login', $controller.'@login');
Route::match(['get','post'], 'logout', $controller.'@logout');
Route::match(['get','post'], 'logout_without_token', $controller.'@logout_without_token');

Route::match(['get','post'], '/cc_api/okcc/receiving-result', $controller.'@operate_api_OKCC_receiving_result');

/*
 * 超级管理员系统（后台）
 * 需要登录
 */
Route::group(['middleware' => []], function () {

    $controller = 'DKAgencyController';

    Route::post('/is_only_me', $controller.'@check_is_only_me');

    Route::get('/404', $controller.'@view_admin_404');

    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');
});

Route::group(['middleware' => []], function () {

    $controller = 'DKAgencyController';


//    Route::post('/is_only_me', $controller.'@check_is_only_me');
    Route::get('/', $controller.'@view_admin_index');
//    Route::get('/404', $controller.'@view_admin_404');


    /*
     * 个人信息管理
     */
    Route::get('/my-account/my-profile-info-index/', $controller.'@view_my_profile_info_index');
    Route::match(['get','post'], '/my-account/my-profile-info-edit', $controller.'@operate_my_profile_info_edit');
//    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');


    // select2
    Route::match(['get','post'], '/company/team_select2_leader', $controller.'@operate_department_select2_leader');
    Route::match(['get','post'], '/company/team_select2_superior_department', $controller.'@operate_department_select2_superior_department');







    Route::post('/delivery/delivery-list', $controller.'@get_datatable_delivery_list');
    Route::post('/delivery/delivery-daily', $controller.'@get_datatable_delivery_daily');







});

