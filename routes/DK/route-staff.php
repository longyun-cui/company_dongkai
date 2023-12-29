<?php


Route::get('/', function () {
    dd('yh.staff');
});


$controller = "DKStaffController";

Route::match(['get','post'], 'login', $controller.'@login');
Route::match(['get','post'], 'logout', $controller.'@logout');


/*
 * 电销员工系统（前台）
 * 需要登录
 */
Route::group(['middleware' => ['yh.staff.login']], function () {

    $controller = 'DKStaffController';


    Route::get('/', $controller.'@view_staff_index');
    Route::get('/404', $controller.'@view_staff_404');


    /*
     * 个人信息管理
     */
    Route::get('/my-account/my-profile-info-index', $controller.'@view_my_profile_info_index');
    Route::match(['get','post'], '/my-account/my-profile-info-edit', $controller.'@operate_my_profile_info_edit');
    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');




    /*
     * 用户管理
     */
    Route::match(['get','post'], '/user/staff-create', $controller.'@operate_user_staff_create');
    Route::match(['get','post'], '/user/staff-edit', $controller.'@operate_user_staff_edit');
    Route::match(['get','post'], '/user/staff-list', $controller.'@view_user_staff_list');
    Route::post('/user/staff-delete', $controller.'@operate_user_staff_delete');
    Route::post('/user/staff-restore', $controller.'@operate_user_staff_restore');
    Route::post('/user/staff-delete-permanently', $controller.'@operate_user_staff_delete_permanently');





    /*
     * 内容管理-模块
     */


    Route::match(['get','post'], '/item/item-list', $controller.'@view_item_list');
    Route::match(['get','post'], '/item/item-list-for-all', $controller.'@view_item_list_for_all');
    Route::match(['get','post'], '/item/item-list-for-menu_type', $controller.'@view_item_list_for_menu_type');
    Route::match(['get','post'], '/item/item-list-for-time_line', $controller.'@view_item_list_for_time_line');
    Route::match(['get','post'], '/item/item-list-for-debase', $controller.'@view_item_list_for_debase');


    Route::match(['get','post'], '/item/content-management', $controller.'@view_item_content_management');
    Route::post('/item/content-edit', $controller.'@operate_item_content_edit');
    Route::post('/item/content-get', $controller.'@operate_item_content_get');
    Route::post('/item/content-delete', $controller.'@operate_item_content_delete');
    Route::post('/item/content-enable', $controller.'@operate_item_content_enable');
    Route::post('/item/content-disable', $controller.'@operate_item_content_disable');




    /*
     * 任务管理-模块
     */
    Route::match(['get','post'], '/item/task-create', $controller.'@operate_item_task_create');
    Route::match(['get','post'], '/item/task-edit', $controller.'@operate_item_task_edit');
    Route::post('/item/task-delete', $controller.'@operate_item_task_delete');
    Route::post('/item/task-restore', $controller.'@operate_item_task_restore');
    Route::post('/item/task-delete-permanently', $controller.'@operate_item_task_delete_permanently');
    Route::post('/item/task-publish', $controller.'@operate_item_task_publish');
    Route::post('/item/task-complete', $controller.'@operate_item_task_complete');
    Route::post('/item/task-remark-edit', $controller.'@operate_item_task_remark_edit');


    Route::get('item/{id?}', $controller.'@view_item');








    Route::match(['get','post'], '/user/my-administrator-list', $controller.'@view_user_my_administrator_list');
    Route::match(['get','post'], '/user/relation-administrator', $controller.'@operate_user_relation_administrator');
    Route::match(['get','post'], '/user/administrator-relation-add', $controller.'@operate_user_administrator_relation_add');
    Route::match(['get','post'], '/user/administrator-relation-add-bulk', $controller.'@operate_user_administrator_relation_add_bulk');

    Route::match(['get','post'], '/user/administrator-relation-remove', $controller.'@operate_user_administrator_relation_remove');




    /*
     * statistic 统计管理-模块
     */
    Route::match(['get','post'], '/statistic/statistic-index', $controller.'@view_statistic_index');


});

