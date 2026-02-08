<?php


Route::get('/', function () {
    dd('dk.staff');
});


$controller = "DKStaffController";

Route::match(['get','post'], 'login', $controller.'@login');
Route::match(['get','post'], 'logout', $controller.'@logout');


/*
 * 电销员工系统（前台）
 * 需要登录
 */
Route::group(['middleware' => ['dk.staff.user.login']], function () {

    $controller = 'DKStaffController';


    Route::get('/', $controller.'@view_staff_index');
    Route::get('/404', $controller.'@view_staff_404');



    // 【公司】
    Route::post('/o1/company/company-list/datatable-query', $controller.'@o1__company__list__datatable_query');
    Route::post('/o1/company/item-get', $controller.'@o1__company__item_get');
    Route::post('/o1/company/item-save', $controller.'@o1__company__item_save');
    // 【公司】删除 & 恢复 & 永久删除
    Route::post('/o1/company/item-delete', $controller.'@o1__company__item_delete');
    Route::post('/o1/company/item-restore', $controller.'@o1__company__item_restore');
    Route::post('/o1/company/item-delete-permanently', $controller.'@o1__company__item_delete_permanently');
    // 【公司】启用 & 禁用
    Route::post('/o1/company/item-enable', $controller.'@o1__company__item_enable');
    Route::post('/o1/company/item-disable', $controller.'@o1__company__item_disable');
    // 【公司】操作记录
    Route::post('/o1/company/item-operation-record-list/datatable-query', $controller.'@o1__company__item_operation_record_list__datatable_query');


    // 【部门】
    Route::post('/o1/department/department-list/datatable-query', $controller.'@o1__department__list__datatable_query');
    Route::post('/o1/department/item-get', $controller.'@o1__department__item_get');
    Route::post('/o1/department/item-save', $controller.'@o1__department__item_save');
    // 【部门】删除 & 恢复 & 永久删除
    Route::post('/o1/department/item-delete', $controller.'@o1__department__item_delete');
    Route::post('/o1/department/item-restore', $controller.'@o1__department__item_restore');
    Route::post('/o1/department/item-delete-permanently', $controller.'@o1__department__item_delete_permanently');
    // 【部门】启用 & 禁用
    Route::post('/o1/department/item-enable', $controller.'@o1__department__item_enable');
    Route::post('/o1/department/item-disable', $controller.'@o1__department__item_disable');
    // 【部门】操作记录
    Route::post('/o1/department/item-operation-record-list/datatable-query', $controller.'@o1__department__item_operation_record_list__datatable_query');


    // 【团队】
    Route::post('/o1/team/team-list/datatable-query', $controller.'@o1__team__list__datatable_query');
    Route::post('/o1/team/item-get', $controller.'@o1__team__item_get');
    Route::post('/o1/team/item-save', $controller.'@o1__team__item_save');
    // 【团队】删除 & 恢复 & 永久删除
    Route::post('/o1/team/item-delete', $controller.'@o1__team__item_delete');
    Route::post('/o1/team/item-restore', $controller.'@o1__team__item_restore');
    Route::post('/o1/team/item-delete-permanently', $controller.'@o1__team__item_delete_permanently');
    // 【团队】启用 & 禁用
    Route::post('/o1/team/item-enable', $controller.'@o1__team__item_enable');
    Route::post('/o1/team/item-disable', $controller.'@o1__team__item_disable');
    // 【团队】操作记录
    Route::post('/o1/team/item-operation-record-list/datatable-query', $controller.'@o1__team__item_operation_record_list__datatable_query');


    // 【员工】
    Route::post('/o1/staff/staff-list/datatable-query', $controller.'@o1__staff__list__datatable_query');
    Route::post('/o1/staff/item-get', $controller.'@o1__staff__item_get');
    Route::post('/o1/staff/item-save', $controller.'@o1__staff__item_save');
    // 【员工】删除 & 恢复 & 永久删除
    Route::post('/o1/staff/item-delete', $controller.'@o1__staff__item_delete');
    Route::post('/o1/staff/item-restore', $controller.'@o1__staff__item_restore');
    Route::post('/o1/staff/item-delete-permanently', $controller.'@o1__staff__item_delete_permanently');
    // 【员工】启用 & 禁用
    Route::post('/o1/staff/item-enable', $controller.'@o1__staff__item_enable');
    Route::post('/o1/staff/item-disable', $controller.'@o1__staff__item_disable');
    // 【员工】操作记录
    Route::post('/o1/staff/item-operation-record-list/datatable-query', $controller.'@o1__staff__item_operation_record_list__datatable_query');




    // 【客户】
    Route::post('/o1/client/client-list/datatable-query', $controller.'@o1__client__list__datatable_query');
    Route::post('/o1/client/item-get', $controller.'@o1__client__item_get');
    Route::post('/o1/client/item-save', $controller.'@o1__client__item_save');
    // 【客户】删除 & 恢复 & 永久删除
    Route::post('/o1/client/item-delete', $controller.'@o1__client__item_delete');
    Route::post('/o1/client/item-restore', $controller.'@o1__client__item_restore');
    Route::post('/o1/client/item-delete-permanently', $controller.'@o1__client__item_delete_permanently');
    // 【客户】启用 & 禁用
    Route::post('/o1/client/item-enable', $controller.'@o1__client__item_enable');
    Route::post('/o1/client/item-disable', $controller.'@o1__client__item_disable');
    // 【客户】操作记录
    Route::post('/o1/client/item-operation-record-list/datatable-query', $controller.'@o1__client__item_operation_record_list__datatable_query');


    // 【项目】
    Route::post('/o1/project/project-list/datatable-query', $controller.'@o1__project__list__datatable_query');
    Route::post('/o1/project/item-get', $controller.'@o1__project__item_get');
    Route::post('/o1/project/item-save', $controller.'@o1__project__item_save');
    // 【项目】删除 & 恢复 & 永久删除
    Route::post('/o1/project/item-delete', $controller.'@o1__project__item_delete');
    Route::post('/o1/project/item-restore', $controller.'@o1__project__item_restore');
    Route::post('/o1/project/item-delete-permanently', $controller.'@o1__project__item_delete_permanently');
    // 【项目】启用 & 禁用
    Route::post('/o1/project/item-enable', $controller.'@o1__project__item_enable');
    Route::post('/o1/project/item-disable', $controller.'@o1__project__item_disable');
    // 【项目】操作记录
    Route::post('/o1/project/item-operation-record-list/datatable-query', $controller.'@o1__project__item_operation_record_list__datatable_query');




});

