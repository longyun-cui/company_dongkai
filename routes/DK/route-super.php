<?php


Route::get('/', function () {
    dd('route-super');
});


$controller = "DKSuperController";

Route::match(['get','post'], 'login', $controller.'@login');
Route::match(['get','post'], 'logout', $controller.'@logout');


/*
 * 超级管理员系统（后台）
 * 需要登录
 */
Route::group(['middleware' => ['yh.super.login']], function () {

    $controller = 'DKSuperController';


    Route::get('/', $controller.'@view_super_index');
    Route::get('/404', $controller.'@view_super_404');


    /*
     * 个人信息管理
     */
    Route::get('/my-account/my-profile-info-index/', $controller.'@view_my_profile_info_index');
    Route::match(['get','post'], '/my-account/my-profile-info-edit', $controller.'@operate_my_profile_info_edit');
    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');




    Route::match(['get','post'], '/staff/staff--list', $controller.'@view__staff__staff_list');
    Route::match(['get','post'], '/staff/staff--item-login', $controller.'@operate__staff__item_login');



    /*
     * 用户管理
     */
    Route::match(['get','post'], '/user/user-create', $controller.'@operate_user_user_create');
    Route::match(['get','post'], '/user/user-edit', $controller.'@operate_user_user_edit');
    Route::post('/user/user-delete', $controller.'@operate_user_user_delete');
    Route::post('/user/user-restore', $controller.'@operate_user_user_restore');
    Route::post('/user/user-delete-permanently', $controller.'@operate_user_user_delete_permanently');
    Route::post('/user/user-enable', $controller.'@operate_user_user_enable');
    Route::post('/user/user-disable', $controller.'@operate_user_user_disable');


    Route::match(['get','post'], '/user/user-login', $controller.'@operate_user_user_login');
    Route::match(['get','post'], '/user/user-admin-login', $controller.'@operate_user_admin_login');
    Route::match(['get','post'], '/user/user-staff-login', $controller.'@operate_user_staff_login');
    Route::match(['get','post'], '/user/user-client-login', $controller.'@operate_user_client_login');
    Route::match(['get','post'], '/user/user-client-staff-login', $controller.'@operate_user_client_staff_login');
    Route::match(['get','post'], '/user/user-finance-login', $controller.'@operate_user_finance_login');
    Route::match(['get','post'], '/user/user-customer-staff-login', $controller.'@operate_user_customer_staff_login');


    Route::match(['get','post'], '/user/user-list-for-all', $controller.'@view_user_list_for_all');
    Route::match(['get','post'], '/user/staff-list-for-all', $controller.'@view_user_staff_list_for_all');
    Route::match(['get','post'], '/user/client-list-for-all', $controller.'@view_user_client_list_for_all');
    Route::match(['get','post'], '/user/client-staff-list', $controller.'@view_user_client_staff_list');
    Route::match(['get','post'], '/user/finance-user-list-for-all', $controller.'@view_user_finance_user_list_for_all');
    Route::match(['get','post'], '/user/customer-list-for-all', $controller.'@view_user_customer_list_for_all');
    Route::match(['get','post'], '/user/customer-staff-list', $controller.'@view_user_customer_staff_list');

    Route::match(['get','post'], '/user/staff-create', $controller.'@operate_user_staff_create');
    Route::match(['get','post'], '/user/staff-edit', $controller.'@operate_user_staff_edit');
    // 【用户-员工管理】修改密码
    Route::match(['get','post'], '/user/staff-password-super-change', $controller.'@operate_user_staff_password_super_change');
    Route::match(['get','post'], '/user/staff-password-super-reset', $controller.'@operate_user_staff_password_super_reset');
    Route::match(['get','post'], '/user/staff-login', $controller.'@operate_user_super_login');
    // 【用户-员工管理】删除 & 恢复 & 永久删除
    Route::post('/user/staff-delete', $controller.'@operate_user_staff_delete');
    Route::post('/user/staff-restore', $controller.'@operate_user_staff_restore');
    Route::post('/user/staff-delete-permanently', $controller.'@operate_user_staff_delete_permanently');
    // 【用户-员工管理】启用 & 禁用
    Route::post('/user/staff-enable', $controller.'@operate_user_staff_enable');
    Route::post('/user/staff-disable', $controller.'@operate_user_staff_disable');





    /*
     * 任务管理
     */
    Route::match(['get','post'], '/item/task-create', $controller.'@operate_item_task_create');
    Route::match(['get','post'], '/item/task-edit', $controller.'@operate_item_task_edit');
    Route::post('/item/task-delete', $controller.'@operate_item_task_delete');
    Route::post('/item/task-restore', $controller.'@operate_item_task_restore');
    Route::post('/item/task-delete-permanently', $controller.'@operate_item_task_delete_permanently');
    Route::post('/item/task-enable', $controller.'@operate_item_task_enable');
    Route::post('/item/task-disable', $controller.'@operate_item_task_disable');
    Route::post('/item/task-publish', $controller.'@operate_item_task_publish');
    Route::post('/item/task-complete', $controller.'@operate_item_task_complete');
    Route::post('/item/task-remark-edit', $controller.'@operate_item_task_remark_edit');




    Route::match(['get','post'], '/item/item-list', $controller.'@view_item_list');
    Route::match(['get','post'], '/item/item-list-for-all', $controller.'@view_item_list_for_all');
    Route::match(['get','post'], '/item/record-list-for-all', $controller.'@view_record_list_for_all');

    Route::match(['get','post'], '/statistic/statistic-index', $controller.'@view_statistic_index');

    Route::post('/statistic/statistic-get-data-for-comprehensive', $controller.'@get_statistic_data_for_comprehensive');
    Route::post('/statistic/statistic-get-data-for-order', $controller.'@get_statistic_data_for_order');
    Route::post('/statistic/statistic-get-data-for-finance', $controller.'@get_statistic_data_for_finance');



});

