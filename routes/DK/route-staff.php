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




    // select2
    Route::post('/o1/select2/select2--company', $controller.'@o1__select2__company');
    Route::post('/o1/select2/select2--department', $controller.'@o1__select2__department');
    Route::post('/o1/select2/select2--team', $controller.'@o1__select2__team');
    Route::post('/o1/select2/select2--staff', $controller.'@o1__select2__staff');
    Route::post('/o1/select2/select2--client', $controller.'@o1__select2__client');
    Route::post('/o1/select2/select2--location', $controller.'@o1__select2__location');
    Route::post('/o1/select2/select2--project', $controller.'@o1__select2__project');
    Route::post('/o1/select2/select2--order', $controller.'@o1__select2__order');




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
    // 【员工】登录
    Route::post('/o1/staff/item-login', $controller.'@o1__staff__item_login');
    // 【员工】操作记录
    Route::post('/o1/staff/item-operation-record-list/datatable-query', $controller.'@o1__staff__item_operation_record_list__datatable_query');




    // 【地区】
    Route::post('/o1/location/location-list/datatable-query', $controller.'@o1__location__list__datatable_query');
    Route::post('/o1/location/item-get', $controller.'@o1__location__item_get');
    Route::post('/o1/location/item-save', $controller.'@o1__location__item_save');
    // 【地区】删除 & 恢复 & 永久删除
    Route::post('/o1/location/item-delete', $controller.'@o1__location__item_delete');
    Route::post('/o1/location/item-restore', $controller.'@o1__location__item_restore');
    Route::post('/o1/location/item-delete-permanently', $controller.'@o1__location__item_delete_permanently');
    // 【地区】启用 & 禁用
    Route::post('/o1/location/item-enable', $controller.'@o1__location__item_enable');
    Route::post('/o1/location/item-disable', $controller.'@o1__location__item_disable');
    // 【地区】操作记录
    Route::post('/o1/location/item-operation-record-list/datatable-query', $controller.'@o1__location__item_operation_record_list__datatable_query');




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








    // 【工单】列表
    Route::post('/o1/order/order-list/datatable-query', $controller.'@o1__order__list__datatable_query');
    // 【工单】操作
    Route::post('/o1/order/item-get', $controller.'@o1__order__item_get');
    Route::post('/o1/order/item-save', $controller.'@o1__order__item_save');
    Route::post('/o1/order/order-dental/item-save', $controller.'@o1__order_dental__item_save');
    Route::post('/o1/order/order-aesthetic/item-save', $controller.'@o1__order_aesthetic__item_save');
    Route::post('/o1/order/order-luxury/item-save', $controller.'@o1__order_dental__item_save');
    // 【工单】删除 & 恢复 & 永久删除
    Route::post('/o1/order/item-delete', $controller.'@o1__order__item_delete');
    Route::post('/o1/order/item-restore', $controller.'@o1__order__item_restore');
    Route::post('/o1/order/item-delete-permanently', $controller.'@o1__order__item_delete_permanently');
    // 【工单】启用 & 禁用
    Route::post('/o1/order/item-enable', $controller.'@o1__order__item_enable');
    Route::post('/o1/order/item-disable', $controller.'@o1__order__item_disable');
    // 【工单】发布
    Route::post('/o1/order/item-publish', $controller.'@o1__order__item_publish');
    // 【工单】完成
    Route::post('/o1/order/item-complete', $controller.'@o1__order__item_publish');
    // 【工单】跟进
    Route::post('/o1/order/item-follow-save', $controller.'@o1__order__item_follow_save');
    Route::post('/o1/order/item-inspecting-save', $controller.'@o1__order__item_inspecting_save');
    Route::post('/o1/order/item-delivering-save', $controller.'@o1__order__item_delivering_save');
    Route::post('/o1/order/bulk-delivering-save', $controller.'@o1__order__bulk_delivering_save');
    Route::post('/o1/order/item-delivering-save--by-fool', $controller.'@o1__order__item_delivering_save__by_fool');
    Route::post('/o1/order/bulk-delivering-save--by-fool', $controller.'@o1__order__bulk_delivering_save__by_fool');
    // 【工单】操作记录
    Route::post('/o1/order/item-operation-record-list/datatable-query', $controller.'@o1__order__item_operation_record_list__datatable_query');
    Route::post('/o1/order/item-delivery-record-list/datatable-query', $controller.'@o1__order__item_delivery_record_list__datatable_query');




    // 【交付】列表
    Route::post('/o1/delivery/delivery-list/datatable-query', $controller.'@o1__delivery__list__datatable_query');




});

