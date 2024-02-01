<?php


Route::get('/', function () {
    dd('route-admin');
});


$controller = "DKAdminController";

Route::match(['get','post'], 'login', $controller.'@login');
Route::match(['get','post'], 'logout', $controller.'@logout');
Route::match(['get','post'], 'logout_without_token', $controller.'@logout_without_token');


/*
 * 超级管理员系统（后台）
 * 需要登录
 */
Route::group(['middleware' => ['yh.admin.login']], function () {

    $controller = 'DKAdminController';


    Route::post('/is_only_me', $controller.'@check_is_only_me');
    Route::get('/', $controller.'@view_admin_index');
    Route::get('/404', $controller.'@view_admin_404');


    /*
     * 个人信息管理
     */
    Route::get('/my-account/my-profile-info-index/', $controller.'@view_my_profile_info_index');
    Route::match(['get','post'], '/my-account/my-profile-info-edit', $controller.'@operate_my_profile_info_edit');
    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');








    /*
     * 客户管理
     */
    // 创建 & 修改
    Route::match(['get','post'], '/user/client-create', $controller.'@operate_user_client_create');
    Route::match(['get','post'], '/user/client-edit', $controller.'@operate_user_client_edit');
    // 【用户-员工管理】修改密码
    Route::match(['get','post'], '/user/client-password-admin-change', $controller.'@operate_user_client_password_admin_change');
    Route::match(['get','post'], '/user/client-password-admin-reset', $controller.'@operate_user_client_password_admin_reset');
    Route::match(['get','post'], '/user/client-login', $controller.'@operate_user_client_login');
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
     * 部门管理
     */
    Route::match(['get','post'], '/department/department_select2_leader', $controller.'@operate_department_select2_leader');
    Route::match(['get','post'], '/department/department_select2_superior_department', $controller.'@operate_department_select2_superior_department');
    // 创建 & 修改
    Route::match(['get','post'], '/department/department-create', $controller.'@operate_department_create');
    Route::match(['get','post'], '/department/department-edit', $controller.'@operate_department_edit');

    // 编辑-信息
    Route::post('/department/department-info-text-set', $controller.'@operate_department_info_text_set');
    Route::post('/department/department-info-time-set', $controller.'@operate_department_info_time_set');
    Route::post('/department/department-info-radio-set', $controller.'@operate_department_info_option_set');
    Route::post('/department/department-info-select-set', $controller.'@operate_department_info_option_set');
    Route::post('/department/department-info-select2-set', $controller.'@operate_department_info_option_set');

    // 删除 & 恢复
    Route::post('/department/department-admin-delete', $controller.'@operate_department_admin_delete');
    Route::post('/department/department-admin-restore', $controller.'@operate_department_admin_restore');
    Route::post('/department/department-admin-delete-permanently', $controller.'@operate_department_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/department/department-admin-enable', $controller.'@operate_department_admin_enable');
    Route::post('/department/department-admin-disable', $controller.'@operate_department_admin_disable');

    // 列表
    Route::match(['get','post'], '/department/department-list', $controller.'@view_department_list');
    Route::match(['get','post'], '/department/department-list-for-all', $controller.'@view_department_list_for_all');

    // 部门-修改信息
    Route::match(['get','post'], '/department/department-modify-record', $controller.'@view_department_modify_record');




    /*
     * 用户-员工管理
     */
    Route::match(['get','post'], '/user/user_select2_district', $controller.'@operate_user_select2_district');
    Route::match(['get','post'], '/user/user_select2_sales', $controller.'@operate_user_select2_sales');
    Route::match(['get','post'], '/user/user_select2_superior', $controller.'@operate_user_select2_superior');
    Route::match(['get','post'], '/user/user_select2_department', $controller.'@operate_user_select2_department');

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
     * 车辆管理
     */
    // 创建 & 修改
    Route::match(['get','post'], '/item/project-create', $controller.'@operate_item_project_create');
    Route::match(['get','post'], '/item/project-edit', $controller.'@operate_item_project_edit');

    // 编辑-信息
    Route::post('/item/car-info-text-set', $controller.'@operate_item_car_info_text_set');
    Route::post('/item/car-info-time-set', $controller.'@operate_item_car_info_time_set');
    Route::post('/item/car-info-radio-set', $controller.'@operate_item_car_info_option_set');
    Route::post('/item/car-info-select-set', $controller.'@operate_item_car_info_option_set');
    Route::post('/item/car-info-select2-set', $controller.'@operate_item_car_info_option_set');
    // 编辑-附件
    Route::match(['get','post'], '/item/project-get-attachment-html', $controller.'@operate_item_project_get_attachment_html');
    Route::post('/item/project-info-attachment-set', $controller.'@operate_item_project_info_attachment_set');
    Route::post('/item/project-info-attachment-delete', $controller.'@operate_item_project_info_attachment_delete');

    // 删除 & 恢复
    Route::post('/item/project-admin-delete', $controller.'@operate_item_project_admin_delete');
    Route::post('/item/project-admin-restore', $controller.'@operate_item_project_admin_restore');
    Route::post('/item/project-admin-delete-permanently', $controller.'@operate_item_project_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/item/project-admin-enable', $controller.'@operate_item_project_admin_enable');
    Route::post('/item/project-admin-disable', $controller.'@operate_item_project_admin_disable');

    // 列表
    Route::match(['get','post'], '/item/project-list', $controller.'@view_item_project_list');
    Route::match(['get','post'], '/item/project-list-for-all', $controller.'@view_item_project_list_for_all');

    // 车辆-修改信息
    Route::match(['get','post'], '/item/project-modify-record', $controller.'@view_item_project_modify_record');




















    /*
     * 订单管理
     */
    // select2
    Route::match(['get','post'], '/item/item_select2_user', $controller.'@operate_item_select2_user');
    Route::match(['get','post'], '/item/item_select2_client', $controller.'@operate_item_select2_client');
    Route::match(['get','post'], '/item/item_select2_project', $controller.'@operate_item_select2_project');

    Route::match(['get','post'], '/item/order_select2_project', $controller.'@operate_order_select2_project');
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
    // 导入
    Route::match(['get','post'], '/item/order-import', $controller.'@operate_item_order_import');

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
    Route::post('/item/order-verify', $controller.'@operate_item_order_verify');
    Route::post('/item/order-publish', $controller.'@operate_item_order_publish');
    Route::post('/item/order-inspect', $controller.'@operate_item_order_inspect');
    Route::post('/item/order-complete', $controller.'@operate_item_order_complete');
    Route::post('/item/order-abandon', $controller.'@operate_item_order_abandon');
    Route::post('/item/order-reuse', $controller.'@operate_item_order_reuse');
    Route::post('/item/order-remark-edit', $controller.'@operate_item_order_remark_edit');
    Route::post('/item/order-deliver', $controller.'@operate_item_order_deliver');

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

    Route::match(['get','post'], '/item/circle-detail', $controller.'@view_item_circle_detail');
    // 修改-列表
    Route::match(['get','post'], '/item/circle-modify-record', $controller.'@view_item_circle_modify_record');

    Route::match(['get','post'], '/item/circle-detail-analysis', $controller.'@get_item_circle_detail_analysis');
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










    /*
     * finance 财务管理
     */
    Route::match(['get','post'], '/finance/finance-list-for-all', $controller.'@view_finance_list_for_all');
    // 修改-列表
    Route::match(['get','post'], '/finance/finance-modify-record', $controller.'@view_finance_modify_record');

    // 导入
    Route::match(['get','post'], '/finance/finance-import', $controller.'@operate_finance_import');
    // 确认 & 删除
    Route::post('/finance/finance-delete', $controller.'@operate_finance_delete');
    Route::post('/finance/finance-confirm', $controller.'@operate_finance_confirm');

    // 编辑-单条-信息
    Route::post('/finance/finance-info-text-set', $controller.'@operate_finance_info_text_set');
    Route::post('/finance/finance-info-time-set', $controller.'@operate_finance_info_time_set');
    Route::post('/finance/finance-info-radio-set', $controller.'@operate_finance_info_option_set');
    Route::post('/finance/finance-info-select-set', $controller.'@operate_finance_info_option_set');
    Route::post('/finance/finance-info-select2-set', $controller.'@operate_finance_info_option_set');








    /*
     * statistic 数据统计
     */
    Route::match(['get','post'], '/statistic/statistic-list-for-all', $controller.'@view_statistic_list_for_all');
    Route::match(['get','post'], '/statistic/statistic-index', $controller.'@view_statistic_index');
    Route::match(['get','post'], '/statistic/statistic-user', $controller.'@view_statistic_user');

    Route::post('/statistic/statistic-get-data-for-comprehensive', $controller.'@get_statistic_data_for_comprehensive');
    Route::post('/statistic/statistic-get-data-for-order', $controller.'@get_statistic_data_for_order');
    Route::post('/statistic/statistic-get-data-for-finance', $controller.'@get_statistic_data_for_finance');


    Route::match(['get','post'], '/statistic/statistic-rank', $controller.'@view_statistic_rank');
    Route::match(['get','post'], '/statistic/statistic-rank-by-staff', $controller.'@view_statistic_rank_by_staff');
    Route::match(['get','post'], '/statistic/statistic-rank-by-department', $controller.'@view_statistic_rank_by_department');

    Route::match(['get','post'], '/statistic/statistic-department', $controller.'@view_statistic_department');
    Route::match(['get','post'], '/statistic/statistic-customer-service', $controller.'@view_statistic_customer_service');
    Route::match(['get','post'], '/statistic/statistic-inspector', $controller.'@view_statistic_inspector');

    Route::post('/statistic/statistic-get-data-for-department', $controller.'@get_statistic_data_for_department');
    Route::post('/statistic/statistic-get-data-for-customer-service', $controller.'@get_statistic_data_for_customer_service');
    Route::post('/statistic/statistic-get-data-for-inspector', $controller.'@get_statistic_data_for_inspector');




    /*
     * export 数据导出
     */
    Route::match(['get','post'], '/statistic/statistic-export', $controller.'@operate_statistic_export');
    Route::match(['get','post'], '/statistic/statistic-export-for-order', $controller.'@operate_statistic_export_for_order');
    Route::match(['get','post'], '/statistic/statistic-export-for-order-by-ids', $controller.'@operate_statistic_export_for_order_by_ids');
    Route::match(['get','post'], '/statistic/statistic-export-for-circle', $controller.'@operate_statistic_export_for_circle');
    Route::match(['get','post'], '/statistic/statistic-export-for-finance', $controller.'@operate_statistic_export_for_finance');



    Route::match(['get','post'], '/item/record-list-for-all', $controller.'@view_record_list_for_all');


});

