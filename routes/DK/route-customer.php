<?php


Route::get('/', function () {
    dd('route-client');
});


$controller = "DKCustomerController";

Route::match(['get','post'], 'login', $controller.'@login');
Route::match(['get','post'], 'logout', $controller.'@logout');
Route::match(['get','post'], 'logout_without_token', $controller.'@logout_without_token');


/*
 * 超级管理员系统（后台）
 * 需要登录
 */
Route::group(['middleware' => ['dk.customer.staff.login']], function () {

    $controller = 'DKCustomerController';


    Route::post('/is_only_me', $controller.'@check_is_only_me');
    Route::post('/is_ip_login', $controller.'@check_is_ip_login');
    Route::get('/', $controller.'@view_admin_index');
    Route::get('/404', $controller.'@view_admin_404');


    /*
     * 个人信息管理
     */
    Route::get('/my-account/my-profile-info-index/', $controller.'@view_my_profile_info_index');
    Route::match(['get','post'], '/my-account/my-profile-info-edit', $controller.'@operate_my_profile_info_edit');
    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');


    // 列表
    Route::post('/setting/setting-customer', $controller.'@operate_setting_customer');


    /*
     * select2
     */
    Route::match(['get','post'], '/select2/select2_city', $controller.'@operate_select2_city');
    Route::match(['get','post'], '/select2/select2_district', $controller.'@operate_select2_district');


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
    // 列表
    Route::match(['get','post'], '/user/staff-list', $controller.'@view_user_staff_list');
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
    // 【用户-员工管理】晋升
    Route::post('/user/staff-admin-promote', $controller.'@operate_user_staff_admin_promote');
    Route::post('/user/staff-admin-demote', $controller.'@operate_user_staff_admin_demote');















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
     * 项目管理
     */
    // 创建 & 修改
    Route::match(['get','post'], '/item/project-create', $controller.'@operate_item_project_create');
    Route::match(['get','post'], '/item/project-edit', $controller.'@operate_item_project_edit');

    // 编辑-信息
    Route::post('/item/project-info-text-set', $controller.'@operate_item_project_info_text_set');
    Route::post('/item/project-info-time-set', $controller.'@operate_item_project_info_time_set');
    Route::post('/item/project-info-radio-set', $controller.'@operate_item_project_info_option_set');
    Route::post('/item/project-info-select-set', $controller.'@operate_item_project_info_option_set');
    Route::post('/item/project-info-select2-set', $controller.'@operate_item_project_info_option_set');
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

    // 项目-修改信息
    Route::match(['get','post'], '/item/project-modify-record', $controller.'@view_item_project_modify_record');





    // 列表
    Route::match(['get','post'], '/item/clue-list', $controller.'@view_item_clue_list');
    Route::match(['get','post'], '/item/clue-list-for-preferential', $controller.'@view_item_clue_list_for_preferential');
    // 订单-修改信息
    Route::match(['get','post'], '/item/clue-modify-record', $controller.'@view_item_clue_modify_record');
    // 操作
    Route::post('/item/clue-take', $controller.'@operate_item_clue_take');
    Route::post('/item/clue-back', $controller.'@operate_item_clue_back');




    // 列表
    Route::match(['get','post'], '/mine/clue-list', $controller.'@view_mine_clue_list');
    // 订单-修改信息
    Route::match(['get','post'], '/mine/clue-modify-record', $controller.'@view_mine_clue_modify_record');
    // 操作
    Route::post('/mine/clue-back', $controller.'@operate_mine_clue_back');
    Route::post('/mine/clue-purchase', $controller.'@operate_mine_clue_purchase');








    // 列表
    Route::match(['get','post'], '/item/delivery-list', $controller.'@view_item_delivery_list');
    // 删除 & 恢复
    Route::post('/item/delivery-delete', $controller.'@operate_item_delivery_delete');
    Route::post('/item/delivery-restore', $controller.'@operate_item_delivery_restore');
    Route::post('/item/delivery-delete-permanently', $controller.'@operate_item_delivery_delete_permanently');
    // 启用 & 禁用
    Route::post('/item/delivery-enable', $controller.'@operate_item_delivery_enable');
    Route::post('/item/delivery-disable', $controller.'@operate_item_delivery_disable');
    // 操作
    Route::post('/item/delivery-verify', $controller.'@operate_item_delivery_verify');
    Route::post('/item/delivery-inspect', $controller.'@operate_item_delivery_inspect');
    Route::post('/item/delivery-publish', $controller.'@operate_item_delivery_publish');
    Route::post('/item/delivery-complete', $controller.'@operate_item_delivery_complete');
    Route::post('/item/delivery-abandon', $controller.'@operate_item_delivery_abandon');
    Route::post('/item/delivery-reuse', $controller.'@operate_item_delivery_reuse');
    Route::post('/item/delivery-remark-edit', $controller.'@operate_item_delivery_remark_edit');
    Route::post('/item/delivery-follow', $controller.'@operate_item_delivery_follow');
    Route::post('/item/delivery-quality-evaluate', $controller.'@operate_item_delivery_quality_evaluate');
    Route::post('/item/delivery-bulk-exported-status', $controller.'@operate_item_delivery_bulk_exported_status');
    Route::post('/item/delivery-bulk-assign-status', $controller.'@operate_item_delivery_bulk_assign_status');
    Route::post('/item/delivery-bulk-assign-staff', $controller.'@operate_item_delivery_bulk_assign_staff');




    /*
     * 订单管理
     */
    // select2
    Route::match(['get','post'], '/item/item_select2_user', $controller.'@operate_item_select2_user');
    Route::match(['get','post'], '/item/item_select2_project', $controller.'@operate_item_select2_project');

    Route::match(['get','post'], '/item/order_select2_project', $controller.'@operate_order_select2_project');
    Route::match(['get','post'], '/item/order_select2_client', $controller.'@operate_order_select2_client');
    Route::match(['get','post'], '/item/order_select2_project', $controller.'@operate_order_select2_project');
    Route::match(['get','post'], '/item/order_select2_circle', $controller.'@operate_order_select2_circle');
    Route::match(['get','post'], '/item/order_select2_route', $controller.'@operate_order_select2_route');
    Route::match(['get','post'], '/item/order_select2_pricing', $controller.'@operate_order_select2_pricing');
    Route::match(['get','post'], '/item/order_select2_trailer', $controller.'@operate_order_select2_trailer');
    Route::match(['get','post'], '/item/order_list_select2_project', $controller.'@operate_order_list_select2_project');
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
    Route::post('/item/order-inspect', $controller.'@operate_item_order_inspect');
    Route::post('/item/order-publish', $controller.'@operate_item_order_publish');
    Route::post('/item/order-complete', $controller.'@operate_item_order_complete');
    Route::post('/item/order-abandon', $controller.'@operate_item_order_abandon');
    Route::post('/item/order-reuse', $controller.'@operate_item_order_reuse');
    Route::post('/item/order-remark-edit', $controller.'@operate_item_order_remark_edit');
    Route::post('/item/order-follow', $controller.'@operate_item_order_follow');
    Route::post('/item/order-quality-evaluate', $controller.'@operate_item_order_quality_evaluate');

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
    Route::post('/item/order-info-project-set', $controller.'@operate_item_order_info_project_set');
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
     * finance 财务
     */
    // 列表
    Route::match(['get','post'], '/finance/daily-list', $controller.'@view_finance_daily_list');








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
    Route::match(['get','post'], '/statistic/statistic-customer-service', $controller.'@view_statistic_customer_service');
    Route::match(['get','post'], '/statistic/statistic-inspector', $controller.'@view_statistic_inspector');

    Route::post('/statistic/statistic-get-data-for-department', $controller.'@get_statistic_data_for_department');
    Route::post('/statistic/statistic-get-data-for-customer-service', $controller.'@get_statistic_data_for_customer_service');
    Route::post('/statistic/statistic-get-data-for-inspector', $controller.'@get_statistic_data_for_inspector');


    // 月报
    Route::match(['get','post'], '/statistic/statistic-delivery-by-daily', $controller.'@view_statistic_delivery_by_daily');
    Route::post('/statistic/statistic-get-data-for-delivery', $controller.'@get_statistic_data_for_delivery');
    Route::post('/statistic/statistic-get-data-for-delivery-of-project-list', $controller.'@get_statistic_data_for_delivery_of_project_list_datatable');
    Route::post('/statistic/statistic-get-data-for-delivery-of-daily-list', $controller.'@get_statistic_data_for_delivery_of_daily_list_datatable');
    Route::post('/statistic/statistic-get-data-for-delivery-of-daily-chart', $controller.'@get_statistic_data_for_delivery_of_daily_chart');




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

