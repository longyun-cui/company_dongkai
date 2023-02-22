<?php


Route::get('/', function () {
    dd('route-admin');
});


$controller = "YHAdminController";

Route::match(['get','post'], 'login', $controller.'@login');
Route::match(['get','post'], 'logout', $controller.'@logout');


/*
 * 超级管理员系统（后台）
 * 需要登录
 */
Route::group(['middleware' => ['yh.admin.login']], function () {

    $controller = 'YHAdminController';


    Route::get('/', $controller.'@view_admin_index');
    Route::get('/404', $controller.'@view_admin_404');


    /*
     * 个人信息管理
     */
    Route::get('/my-account/my-profile-info-index/', $controller.'@view_my_profile_info_index');
    Route::match(['get','post'], '/my-account/my-profile-info-edit', $controller.'@operate_my_profile_info_edit');
    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');




    /*
     * 用户-员工管理
     */
    Route::match(['get','post'], '/user/user_select2_district', $controller.'@operate_user_select2_district');
    Route::match(['get','post'], '/user/user_select2_sales', $controller.'@operate_user_select2_sales');

    // 【用户-员工管理】创建 & 修改
    Route::match(['get','post'], '/user/staff-create', $controller.'@operate_user_staff_create');
    Route::match(['get','post'], '/user/staff-edit', $controller.'@operate_user_staff_edit');
    // 【用户-员工管理】修改密码
    Route::match(['get','post'], '/user/staff-password-admin-change', $controller.'@operate_user_staff_password_admin_change');
    Route::match(['get','post'], '/user/staff-password-admin-reset', $controller.'@operate_user_staff_password_admin_reset');
    Route::match(['get','post'], '/user/user-login', $controller.'@operate_user_user_login');
    // 【用户-员工管理】删除 & 恢复 & 永久删除
    Route::post('/user/staff-admin-delete', $controller.'@operate_user_staff_admin_delete');
    Route::post('/user/staff-admin-restore', $controller.'@operate_user_staff_admin_restore');
    Route::post('/user/staff-admin-delete-permanently', $controller.'@operate_user_staff_admin_delete_permanently');
    // 【用户-员工管理】启用 & 禁用
    Route::post('/user/staff-admin-enable', $controller.'@operate_user_staff_admin_enable');
    Route::post('/user/staff-admin-disable', $controller.'@operate_user_staff_admin_disable');

    // 列表
    Route::match(['get','post'], '/user/staff-list', $controller.'@view_user_staff_list');
    Route::match(['get','post'], '/user/staff-list-for-all', $controller.'@view_staff_list_for_all');








    /*
     * 驾驶员管理
     */
    Route::match(['get','post'], '/user/user_select2_district', $controller.'@operate_user_select2_district');
    Route::match(['get','post'], '/user/user_select2_sales', $controller.'@operate_user_select2_sales');

    // 创建 & 修改
    Route::match(['get','post'], '/user/driver-create', $controller.'@operate_user_driver_create');
    Route::match(['get','post'], '/user/driver-edit', $controller.'@operate_user_driver_edit');
    // 编辑-信息
    Route::post('/user/driver-info-text-set', $controller.'@operate_user_driver_info_text_set');
    Route::post('/user/driver-info-time-set', $controller.'@operate_user_driver_info_time_set');
    Route::post('/user/driver-info-option-set', $controller.'@operate_user_driver_info_option_set');
    Route::post('/user/driver-info-radio-set', $controller.'@operate_user_driver_info_option_set');
    Route::post('/user/driver-info-select-set', $controller.'@operate_user_driver_info_option_set');
    Route::post('/user/driver-info-select2-set', $controller.'@operate_user_driver_info_option_set');
    Route::post('/user/driver-info-image-set', $controller.'@operate_user_driver_info_image_set');
    // 编辑-附件
    Route::match(['get','post'], '/user/driver-get-attachment-html', $controller.'@operate_user_driver_get_attachment_html');
    Route::post('/user/driver-info-attachment-get-html', $controller.'@operate_user_driver_info_attachment_get_html');
    Route::post('/user/driver-info-attachment-set', $controller.'@operate_user_driver_info_attachment_set');
    Route::post('/user/driver-info-attachment-delete', $controller.'@operate_user_driver_info_attachment_delete');
    // 修改密码
    Route::match(['get','post'], '/user/driver-password-admin-change', $controller.'@operate_user_driver_password_admin_change');
    Route::match(['get','post'], '/user/driver-password-admin-reset', $controller.'@operate_user_driver_password_admin_reset');
    Route::match(['get','post'], '/user/driver-login', $controller.'@operate_user_user_login');
    // 删除 & 恢复 & 永久删除
    Route::post('/user/driver-admin-delete', $controller.'@operate_user_driver_admin_delete');
    Route::post('/user/driver-admin-restore', $controller.'@operate_user_driver_admin_restore');
    Route::post('/user/driver-admin-delete-permanently', $controller.'@operate_user_driver_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/user/driver-admin-enable', $controller.'@operate_user_driver_admin_enable');
    Route::post('/user/driver-admin-disable', $controller.'@operate_user_driver_admin_disable');

    // 列表
    Route::match(['get','post'], '/user/driver-list', $controller.'@view_user_driver_list');
    Route::match(['get','post'], '/user/driver-list-for-all', $controller.'@view_user_driver_list_for_all');

    // 修改-列表
    Route::match(['get','post'], '/user/driver-modify-record', $controller.'@view_user_driver_modify_record');









    /*
     * 客户管理
     */
    // 创建 & 修改
    Route::match(['get','post'], '/user/client-create', $controller.'@operate_user_client_create');
    Route::match(['get','post'], '/user/client-edit', $controller.'@operate_user_client_edit');
    // 删除 & 恢复
    Route::post('/user/client-admin-delete', $controller.'@operate_user_client_admin_delete');
    Route::post('/user/client-admin-restore', $controller.'@operate_user_client_admin_restore');
    Route::post('/user/client-admin-delete-permanently', $controller.'@operate_user_client_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/user/client-admin-enable', $controller.'@operate_user_client_admin_enable');
    Route::post('/user/client-admin-disable', $controller.'@operate_user_client_admin_disable');

    // 列表
    Route::match(['get','post'], '/user/client-list', $controller.'@view_user_client_list');
    Route::match(['get','post'], '/user/client-list-for-all', $controller.'@view_user_client_list_for_all');








    /*
     * 车辆管理
     */
    // 创建 & 修改
    Route::match(['get','post'], '/item/car-create', $controller.'@operate_item_car_create');
    Route::match(['get','post'], '/item/car-edit', $controller.'@operate_item_car_edit');

    // 编辑-信息
    Route::post('/item/car-info-text-set', $controller.'@operate_item_car_info_text_set');
    Route::post('/item/car-info-time-set', $controller.'@operate_item_car_info_time_set');
    Route::post('/item/car-info-radio-set', $controller.'@operate_item_car_info_option_set');
    Route::post('/item/car-info-select-set', $controller.'@operate_item_car_info_option_set');
    Route::post('/item/car-info-select2-set', $controller.'@operate_item_car_info_option_set');
    // 编辑-附件
    Route::match(['get','post'], '/item/car-get-attachment-html', $controller.'@operate_item_car_get_attachment_html');
    Route::post('/item/car-info-attachment-set', $controller.'@operate_item_car_info_attachment_set');
    Route::post('/item/car-info-attachment-delete', $controller.'@operate_item_car_info_attachment_delete');

    // 删除 & 恢复
    Route::post('/item/car-admin-delete', $controller.'@operate_item_car_admin_delete');
    Route::post('/item/car-admin-restore', $controller.'@operate_item_car_admin_restore');
    Route::post('/item/car-admin-delete-permanently', $controller.'@operate_item_car_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/item/car-admin-enable', $controller.'@operate_item_car_admin_enable');
    Route::post('/item/car-admin-disable', $controller.'@operate_item_car_admin_disable');

    // 列表
    Route::match(['get','post'], '/item/car-list', $controller.'@view_item_car_list');
    Route::match(['get','post'], '/item/car-list-for-all', $controller.'@view_item_car_list_for_all');

    // 车辆-修改信息
    Route::match(['get','post'], '/item/car-modify-record', $controller.'@view_item_car_modify_record');








    /*
     * 线路管理
     */
    // 创建 & 修改
    Route::match(['get','post'], '/item/route-create', $controller.'@operate_item_route_create');
    Route::match(['get','post'], '/item/route-edit', $controller.'@operate_item_route_edit');

    // 编辑-信息
    Route::post('/item/route-info-text-set', $controller.'@operate_item_route_info_text_set');
    Route::post('/item/route-info-time-set', $controller.'@operate_item_route_info_time_set');
    Route::post('/item/route-info-radio-set', $controller.'@operate_item_route_info_option_set');
    Route::post('/item/route-info-select-set', $controller.'@operate_item_route_info_option_set');
    Route::post('/item/route-info-select2-set', $controller.'@operate_item_route_info_option_set');
    // 编辑-附件
    Route::match(['get','post'], '/item/route-get-attachment-html', $controller.'@operate_item_route_get_attachment_html');
    Route::post('/item/route-info-attachment-set', $controller.'@operate_item_route_info_attachment_set');
    Route::post('/item/route-info-attachment-delete', $controller.'@operate_item_route_info_attachment_delete');

    // 删除 & 恢复
    Route::post('/item/route-admin-delete', $controller.'@operate_item_route_admin_delete');
    Route::post('/item/route-admin-restore', $controller.'@operate_item_route_admin_restore');
    Route::post('/item/route-admin-delete-permanently', $controller.'@operate_item_route_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/item/route-admin-enable', $controller.'@operate_item_route_admin_enable');
    Route::post('/item/route-admin-disable', $controller.'@operate_item_route_admin_disable');

    // 列表
    Route::match(['get','post'], '/item/route-list', $controller.'@view_item_route_list');
    Route::match(['get','post'], '/item/route-list-for-all', $controller.'@view_item_route_list_for_all');

    // 修改-列表
    Route::match(['get','post'], '/item/route-modify-record', $controller.'@view_item_route_modify_record');








    /*
     * 定价管理
     */
    // 创建 & 修改
    Route::match(['get','post'], '/item/pricing-create', $controller.'@operate_item_pricing_create');
    Route::match(['get','post'], '/item/pricing-edit', $controller.'@operate_item_pricing_edit');

    // 编辑-单条-信息
    Route::post('/item/pricing-info-text-set', $controller.'@operate_item_pricing_info_text_set');
    Route::post('/item/pricing-info-time-set', $controller.'@operate_item_pricing_info_time_set');
    Route::post('/item/pricing-info-radio-set', $controller.'@operate_item_pricing_info_option_set');
    Route::post('/item/pricing-info-select-set', $controller.'@operate_item_pricing_info_option_set');
    Route::post('/item/pricing-info-select2-set', $controller.'@operate_item_pricing_info_option_set');
    // 编辑-附件
    Route::match(['get','post'], '/item/pricing-get-attachment-html', $controller.'@operate_item_pricing_get_attachment_html');
    Route::post('/item/pricing-info-attachment-set', $controller.'@operate_item_pricing_info_attachment_set');
    Route::post('/item/pricing-info-attachment-delete', $controller.'@operate_item_pricing_info_attachment_delete');

    // 删除 & 恢复
    Route::post('/item/pricing-admin-delete', $controller.'@operate_item_pricing_admin_delete');
    Route::post('/item/pricing-admin-restore', $controller.'@operate_item_pricing_admin_restore');
    Route::post('/item/pricing-admin-delete-permanently', $controller.'@operate_item_pricing_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/item/pricing-admin-enable', $controller.'@operate_item_pricing_admin_enable');
    Route::post('/item/pricing-admin-disable', $controller.'@operate_item_pricing_admin_disable');

    // 列表
    Route::match(['get','post'], '/item/pricing-list', $controller.'@view_item_pricing_list');
    Route::match(['get','post'], '/item/pricing-list-for-all', $controller.'@view_item_pricing_list_for_all');

    // 修改-列表
    Route::match(['get','post'], '/item/pricing-modify-record', $controller.'@view_item_pricing_modify_record');








    /*
     * 订单管理
     */
    // select2
    Route::match(['get','post'], '/item/order_select2_client', $controller.'@operate_order_select2_client');
    Route::match(['get','post'], '/item/order_select2_car', $controller.'@operate_order_select2_car');
    Route::match(['get','post'], '/item/order_select2_circle', $controller.'@operate_order_select2_circle');
    Route::match(['get','post'], '/item/order_select2_route', $controller.'@operate_order_select2_route');
    Route::match(['get','post'], '/item/order_select2_pricing', $controller.'@operate_order_select2_pricing');
    Route::match(['get','post'], '/item/order_select2_trailer', $controller.'@operate_order_select2_trailer');
    Route::match(['get','post'], '/item/order_list_select2_car', $controller.'@operate_order_list_select2_car');
    Route::match(['get','post'], '/item/order_select2_driver', $controller.'@operate_order_select2_driver');
    // 创建 & 修改
    Route::match(['get','post'], '/item/order-create', $controller.'@operate_item_order_create');
    Route::match(['get','post'], '/item/order-edit', $controller.'@operate_item_order_edit');
    // 获取
    Route::match(['get','post'], '/item/order-get', $controller.'@operate_item_order_get');
    Route::match(['get','post'], '/item/order-get-html', $controller.'@operate_item_order_get_html');
    Route::match(['get','post'], '/item/order-get-attachment-html', $controller.'@operate_item_order_get_attachment_html');
    // 删除 & 恢复
    Route::post('/item/order-delete', $controller.'@operate_item_order_delete');
    Route::post('/item/order-restore', $controller.'@operate_item_order_restore');
    Route::post('/item/order-delete-permanently', $controller.'@operate_item_order_delete_permanently');
    // 启用 & 禁用
    Route::post('/item/order-enable', $controller.'@operate_item_order_enable');
    Route::post('/item/order-disable', $controller.'@operate_item_order_disable');
    // 发布 & 完成 & 备注
    Route::post('/item/order-abandon', $controller.'@operate_item_order_abandon');
    Route::post('/item/order-publish', $controller.'@operate_item_order_publish');
    Route::post('/item/order-complete', $controller.'@operate_item_order_complete');
    Route::post('/item/order-remark-edit', $controller.'@operate_item_order_remark_edit');

    // 列表
    Route::match(['get','post'], '/item/order-list', $controller.'@view_item_order_list');
    Route::match(['get','post'], '/item/order-list-for-all', $controller.'@view_item_order_list_for_all');

    // 订单-基本信息
    Route::post('/item/order-info-text-set', $controller.'@operate_item_order_info_text_set');
    Route::post('/item/order-info-time-set', $controller.'@operate_item_order_info_time_set');
    Route::post('/item/order-info-radio-set', $controller.'@operate_item_order_info_option_set');
    Route::post('/item/order-info-select-set', $controller.'@operate_item_order_info_option_set');
    Route::post('/item/order-info-select2-set', $controller.'@operate_item_order_info_option_set');
    Route::post('/item/order-info-client-set', $controller.'@operate_item_order_info_client_set');
    Route::post('/item/order-info-car-set', $controller.'@operate_item_order_info_car_set');
    // 订单-附件
    Route::post('/item/order-info-attachment-set', $controller.'@operate_item_order_info_attachment_set');
    Route::post('/item/order-info-attachment-delete', $controller.'@operate_item_order_info_attachment_delete');


    // 订单-行程信息
    Route::post('/item/order-travel-set', $controller.'@operate_item_order_travel_set');
    // 订单-财务信息
    Route::match(['get','post'], '/item/order-finance-record', $controller.'@view_item_order_finance_record');
    Route::post('/item/order-finance-record-create', $controller.'@operate_item_order_finance_record_create');
    Route::post('/item/order-finance-record-edit', $controller.'@operate_item_order_finance_record_edit');
    // 订单-修改信息
    Route::match(['get','post'], '/item/order-modify-record', $controller.'@view_item_order_modify_record');








    /*
     * 环线管理
     */
    // select2
    Route::match(['get','post'], '/item/circle_select2_car', $controller.'@operate_circle_select2_car');
    Route::match(['get','post'], '/item/circle_select2_order_list', $controller.'@operate_circle_select2_order_list');
    // 创建 & 修改
    Route::match(['get','post'], '/item/circle-create', $controller.'@operate_item_circle_create');
    Route::match(['get','post'], '/item/circle-edit', $controller.'@operate_item_circle_edit');

    // 编辑-单条-信息
    Route::post('/item/circle-info-text-set', $controller.'@operate_item_circle_info_text_set');
    Route::post('/item/circle-info-time-set', $controller.'@operate_item_circle_info_time_set');
    Route::post('/item/circle-info-radio-set', $controller.'@operate_item_circle_info_option_set');
    Route::post('/item/circle-info-select-set', $controller.'@operate_item_circle_info_option_set');
    Route::post('/item/circle-info-select2-set', $controller.'@operate_item_circle_info_option_set');

    // 删除 & 恢复
    Route::post('/item/circle-admin-delete', $controller.'@operate_item_circle_admin_delete');
    Route::post('/item/circle-admin-restore', $controller.'@operate_item_circle_admin_restore');
    Route::post('/item/circle-admin-delete-permanently', $controller.'@operate_item_circle_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/item/circle-admin-enable', $controller.'@operate_item_circle_admin_enable');
    Route::post('/item/circle-admin-disable', $controller.'@operate_item_circle_admin_disable');

    // 列表
    Route::match(['get','post'], '/item/circle-list', $controller.'@view_item_circle_list');
    Route::match(['get','post'], '/item/circle-list-for-all', $controller.'@view_item_circle_list_for_all');

    Route::match(['get','post'], '/item/circle-analysis', $controller.'@get_item_circle_analysis');
    Route::match(['get','post'], '/item/circle-finance-record', $controller.'@get_item_circle_finance_record');








    /*
     * 内容管理
     */
    Route::match(['get','post'], '/item/item-create', $controller.'@operate_item_item_create');
    Route::match(['get','post'], '/item/item-edit', $controller.'@operate_item_item_edit');
    // 【内容管理】删除 & 恢复 & 永久删除
    Route::post('/item/item-delete', $controller.'@operate_item_item_delete');
    Route::post('/item/item-restore', $controller.'@operate_item_item_restore');
    Route::post('/item/item-delete-permanently', $controller.'@operate_item_item_delete_permanently');
    // 【内容管理】启用 & 禁用
    Route::post('/item/item-enable', $controller.'@operate_item_item_enable');
    Route::post('/item/item-disable', $controller.'@operate_item_item_disable');
    // 【内容管理】发布
    Route::post('/item/item-publish', $controller.'@operate_item_item_publish');
    // 【内容管理】完成 & 备注
    Route::post('/item/item-complete', $controller.'@operate_item_item_complete');
    Route::post('/item/item-remark-edit', $controller.'@operate_item_item_remark_edit');

    // 【内容管理】批量操作
    Route::post('/item/item-operate-bulk', $controller.'@operate_item_item_operate_bulk');
    // 【内容管理】批量操作 - 删除 & 恢复 & 永久删除
    Route::post('/item/item-delete-bulk', $controller.'@operate_item_item_delete_bulk');
    Route::post('/item/item-restore-bulk', $controller.'@operate_item_item_restore_bulk');
    Route::post('/item/item-delete-permanently-bulk', $controller.'@operate_item_item_delete_permanently_bulk');
    // 【内容管理】批量操作 - 启用 & 禁用
    Route::post('/item/item-enable-bulk', $controller.'@operate_item_item_enable_bulk');
    Route::post('/item/item-disable-bulk', $controller.'@operate_item_item_disable_bulk');




    /*
     * 任务管理
     */
    // 【任务管理】删除 & 恢复 & 永久删除
    Route::post('/item/task-admin-delete', $controller.'@operate_item_task_admin_delete');
    Route::post('/item/task-admin-restore', $controller.'@operate_item_task_admin_restore');
    Route::post('/item/task-admin-delete-permanently', $controller.'@operate_item_task_admin_delete_permanently');
    // 【任务管理】启用 & 禁用
    Route::post('/item/task-admin-enable', $controller.'@operate_item_task_admin_enable');
    Route::post('/item/task-admin-disable', $controller.'@operate_item_task_admin_disable');
    // 【任务管理】批量操作
    Route::post('/item/task-admin-operate-bulk', $controller.'@operate_item_task_admin_operate_bulk');
    Route::post('/item/task-admin-delete-bulk', $controller.'@operate_item_task_admin_delete_bulk');
    Route::post('/item/task-admin-restore-bulk', $controller.'@operate_item_task_admin_restore_bulk');
    Route::post('/item/task-admin-delete-permanently-bulk', $controller.'@operate_item_task_admin_delete_permanently_bulk');





    /*
     * 任务管理
     */
    Route::match(['get','post'], '/item/task-list-import', $controller.'@operate_item_task_list_import');

    Route::match(['get','post'], '/item/task-create', $controller.'@operate_item_task_create');
    Route::match(['get','post'], '/item/task-edit', $controller.'@operate_item_task_edit');
    Route::post('/item/task-enable', $controller.'@operate_item_task_enable');
    Route::post('/item/task-disable', $controller.'@operate_item_task_disable');
    Route::post('/item/task-delete', $controller.'@operate_item_task_delete');
    Route::post('/item/task-restore', $controller.'@operate_item_task_restore');
    Route::post('/item/task-delete-permanently', $controller.'@operate_item_task_delete_permanently');
    Route::post('/item/task-publish', $controller.'@operate_item_task_publish');
    Route::post('/item/task-complete', $controller.'@operate_item_task_complete');
    Route::post('/item/task-remark-edit', $controller.'@operate_item_task_remark_edit');














    Route::match(['get','post'], '/item/item-list', $controller.'@view_item_list');
    Route::match(['get','post'], '/item/item-list-for-all', $controller.'@view_item_list_for_all');

    Route::match(['get','post'], '/item/task-list-for-all', $controller.'@view_task_list_for_all');
    Route::match(['get','post'], '/item/task-list-for-finished', $controller.'@view_task_list_for_finished');






    /*
     * statistic
     */
    Route::match(['get','post'], '/finance/finance-list-for-all', $controller.'@view_finance_list_for_all');
    Route::post('/finance/finance-delete', $controller.'@operate_finance_delete');
    Route::post('/finance/finance-confirm', $controller.'@operate_finance_confirm');








    /*
     * statistic
     */
    Route::match(['get','post'], '/statistic/statistic-list-for-all', $controller.'@view_statistic_list_for_all');
    Route::match(['get','post'], '/statistic/statistic-index', $controller.'@view_statistic_index');
    Route::match(['get','post'], '/statistic/statistic-user', $controller.'@view_statistic_user');


});

