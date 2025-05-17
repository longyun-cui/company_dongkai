<?php


Route::get('/', function () {
    dd('route-client');
});


$controller = "DKClientController";

Route::match(['get','post'], 'login', $controller.'@login');
Route::match(['get','post'], 'logout', $controller.'@logout');
Route::match(['get','post'], 'logout_without_token', $controller.'@logout_without_token');



Route::match(['get','post'], '/data/voice_record', $controller.'@view_data_voice_record');
Route::match(['get','post'], '/data/order-detail', $controller.'@view_data_of_order_detail');
Route::match(['get','post'], '/data/delivery-detail', $controller.'@view_data_of_delivery_detail');


/*
 * 超级管理员系统（后台）
 * 需要登录
 */
Route::group(['middleware' => ['dk.client.staff.login']], function () {

    $controller = 'DKClientController';


    Route::post('/is_only_me', $controller.'@check_is_only_me');
    Route::post('/is_ip_login', $controller.'@check_is_ip_login');
    Route::get('/', $controller.'@view_admin_index');
    Route::get('/404', $controller.'@view_admin_404');




    Route::post('/query_last_delivery', $controller.'@query_last_delivery');






    Route::post('/v1/operate/select2/select2-department', $controller.'@v1_operate_for_select2_department');
    Route::post('/v1/operate/select2/select2-staff', $controller.'@v1_operate_for_select2_staff');
    Route::post('/v1/operate/select2/select2-contact', $controller.'@v1_operate_for_select2_contact');


    // 【部门-管理】
    Route::post('/item/item-edit-for-department', $controller.'@operate_item_edit_for_department');
    Route::post('/item/item-get-for-department', $controller.'@operate_item_get_for_department');

    // 【客户-管理】
    Route::post('/item/item-edit-for-staff', $controller.'@operate_staff_edit_by_admin');
    Route::post('/item/item-get-for-staff', $controller.'@operate_staff_get_by_admin');




    // 【部门-管理】
    Route::post('/v1/operate/department/datatable-list-query', $controller.'@v1_operate_for_department_datatable_list_query');
    Route::post('/v1/operate/department/item-get', $controller.'@v1_operate_for_department_item_get');
    Route::post('/v1/operate/department/item-save', $controller.'@v1_operate_for_department_item_save');


    // 【员工-管理】
    Route::post('/v1/operate/staff/datatable-list-query', $controller.'@v1_operate_for_staff_datatable_list_query');
    Route::post('/v1/operate/staff/item-get', $controller.'@v1_operate_for_staff_item_get');
    Route::post('/v1/operate/staff/item-save', $controller.'@v1_operate_for_staff_item_save');


    // 【联系渠道-管理】
    Route::post('/v1/operate/contact/datatable-list-query', $controller.'@v1_operate_for_contact_datatable_list_query');
    Route::post('/v1/operate/contact/item-get', $controller.'@v1_operate_for_contact_item_get');
    Route::post('/v1/operate/contact/item-save', $controller.'@v1_operate_for_contact_item_save');




    // 【交付-管理】
    Route::post('/v1/operate/delivery/datatable-list-query', $controller.'@v1_operate_for_delivery_datatable_list_query');
    Route::post('/v1/operate/delivery/item-get', $controller.'@v1_operate_for_delivery_item_get');

    // 客户跟进
    Route::post('/v1/operate/delivery/item-customer-save', $controller.'@v1_operate_for_delivery_item_customer_save');
    Route::post('/v1/operate/delivery/item-callback-save', $controller.'@v1_operate_for_delivery_item_callback_save');
    Route::post('/v1/operate/delivery/item-come-save', $controller.'@v1_operate_for_delivery_item_come_save');
    Route::post('/v1/operate/delivery/item-follow-save', $controller.'@v1_operate_for_delivery_item_follow_save');
    Route::post('/v1/operate/delivery/item-trade-save', $controller.'@v1_operate_for_delivery_item_trade_save');

    // 工单质量评价
    Route::post('/v1/operate/delivery/item-quality-evaluate', $controller.'@v1_operate_for_delivery_item_quality_evaluate');



    // 【交易-管理】
    Route::post('/v1/operate/trade/datatable-list-query', $controller.'@v1_operate_for_trade_datatable_list_query');
    Route::post('/v1/operate/trade/item-get', $controller.'@v1_operate_for_trade_item_get');
    Route::post('/v1/operate/trade/item-save', $controller.'@v1_operate_for_trade_item_save');
    Route::post('/v1/operate/trade/item-confirm', $controller.'@v1_operate_for_trade_item_confirm');
    Route::post('/v1/operate/trade/item-delete', $controller.'@v1_operate_for_trade_item_delete');




    // 【通用】删除 & 恢复 & 永久删除
    Route::post('/v1/operate/universal/item-delete-by-admin', $controller.'@v1_operate_for_universal_item_delete_by_admin');
    Route::post('/v1/operate/universal/item-restore-by-admin', $controller.'@v1_operate_for_universal_item_restore_by_admin');
    Route::post('/v1/operate/universal/item-delete-permanently-by-admin', $controller.'@v1_operate_for_universal_item_delete_permanently_by_admin');
    // 【通用】重置密码 & 删除密码
    Route::post('/v1/operate/universal/password-reset-by-admin', $controller.'@v1_operate_for_universal_password_reset_by_admin');
    Route::post('/v1/operate/universal/password-change-by-admin', $controller.'@v1_operate_for_universal_password_change_by_admin');
    // 【通用】启用 & 禁用
    Route::post('/v1/operate/universal/item-enable-by-admin', $controller.'@v1_operate_for_universal_item_enable_by_admin');
    Route::post('/v1/operate/universal/item-disable-by-admin', $controller.'@v1_operate_for_universal_item_disable_by_admin');
    // 【通用】晋升 & 降职
    Route::post('/v1/operate/universal/item-promote-by-admin', $controller.'@v1_operate_for_universal_item_promote_by_admin');
    Route::post('/v1/operate/universal/item-demote-by-admin', $controller.'@v1_operate_for_universal_item_demote_by_admin');
    // 【通用】登录
    Route::post('/v1/operate/universal/item-login-by-admin', $controller.'@v1_operate_for_universal_item_login_by_admin');
    // 【通用】字段修改
    Route::post('/v1/operate/universal/field-set', $controller.'@v1_operate_for_universal_field_set');



    // 【用户】字段修改
    Route::post('/v1/operate/user/field-set', $controller.'@v1_operate_for_user_field_set');
    Route::post('/v1/operate/parent-client/field-set', $controller.'@v1_operate_for_parent_client_field_set');


    Route::post('/v1/operate/delivery/automatic-dispatching-by-admin', $controller.'@v1_operate_for_delivery_automatic_dispatching_by_admin');




    Route::post('/v1/operate/delivery/item-follow-record-datatable-query', $controller.'@v1_operate_for_delivery_item_follow_record_datatable_query');






    // 【交付-管理】
    Route::post('/delivery/delivery-list', $controller.'@get_datatable_delivery_list');
    Route::post('/delivery/delivery-daily', $controller.'@get_datatable_delivery_daily');
    Route::post('/delivery/delivery-project', $controller.'@get_datatable_delivery_project');

    Route::get('/delivery/delivery-export-by-ids', $controller.'@operate_delivery_export_by_ids');


    // 【财务-管理】
    Route::post('/finance/finance-daily', $controller.'@get_datatable_finance_daily');


    // 【通用】删除 & 恢复 & 永久删除
    Route::post('/item/item-delete-by-admin', $controller.'@operate_item_delete_by_admin');
    Route::post('/item/item-restore-by-admin', $controller.'@operate_item_restore_by_admin');
    Route::post('/item/item-delete-permanently-by-admin', $controller.'@operate_item_delete_permanently_by_admin');
    // 【通用】重置密码 & 删除密码
    Route::post('/item/item-password-reset-by-admin', $controller.'@operate_item_password_reset_by_admin');
    Route::post('/item/item-password-change-by-admin', $controller.'@operate_item_password_change_by_admin');
    // 【通用】启用 & 禁用
    Route::post('/item/item-enable-by-admin', $controller.'@operate_item_enable_by_admin');
    Route::post('/item/item-disable-by-admin', $controller.'@operate_item_disable_by_admin');
    // 【通用】晋升 & 降职
    Route::post('/item/item-promote-by-admin', $controller.'@operate_item_promote_by_admin');
    Route::post('/item/item-demote-by-admin', $controller.'@operate_item_demote_by_admin');


    // 【通用】批量-指派状态
    Route::post('/item/bulk-assign-status', $controller.'@operate_bulk_assign_status');
    Route::post('/item/bulk-assign-staff', $controller.'@operate_bulk_assign_staff');
    Route::post('/item/bulk-api-push', $controller.'@operate_bulk_api_push');









    Route::post('/v1/operate/statistic/production/staff-rank', $controller.'@v1_operate_for_get_statistic_data_of_production_staff_rank');
    Route::post('/v1/operate/statistic/production/staff-daily', $controller.'@v1_operate_for_get_statistic_data_of_production_staff_daily');





    Route::match(['get','post'], '/v1/operate/statistic/statistic-export-for-delivery-by-ids', $controller.'@v1_operate_statistic_export_for_delivery_by_ids');










    /*
     * 个人信息管理
     */
    Route::get('/my-account/my-profile-info-index/', $controller.'@view_my_profile_info_index');
    Route::match(['get','post'], '/my-account/my-profile-info-edit', $controller.'@operate_my_profile_info_edit');
    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');




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

    // 车辆-修改信息
    Route::match(['get','post'], '/item/project-modify-record', $controller.'@view_item_project_modify_record');







    // 列表
    Route::match(['get','post'], '/item/delivery-list', $controller.'@view_item_delivery_list');
    // 删除 & 恢复
    Route::post('/item/delivery-delete', $controller.'@operate_item_delivery_delete');
    Route::post('/item/delivery-restore', $controller.'@operate_item_delivery_restore');
    Route::post('/item/delivery-delete-permanently', $controller.'@operate_item_delivery_delete_permanently');
    // 启用 & 禁用
    Route::post('/item/delivery-enable', $controller.'@operate_item_delivery_enable');
    Route::post('/item/delivery-disable', $controller.'@operate_item_delivery_disable');
    // 发布 & 完成 & 备注
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
    Route::post('/item/delivery-bulk-api-push', $controller.'@operate_item_delivery_bulk_api_push');




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













    $reconciliationController = 'DKClientReconciliationController';

    // 对账系统
    Route::get('/reconciliation', $reconciliationController.'@view_reconciliation_index');


    // 【select2】
    Route::post('/reconciliation/v1/operate/select2/select2-project', $reconciliationController.'@reconciliation_v1_operate_select2_project');




    // 【项目-管理】
    Route::post('/reconciliation/v1/operate/project/datatable-list-query', $reconciliationController.'@reconciliation_v1_operate_for_project_datatable_list_query');
    Route::post('/reconciliation/v1/operate/project/item-get', $reconciliationController.'@reconciliation_v1_operate_for_project_item_get');
    Route::post('/reconciliation/v1/operate/project/item-save', $reconciliationController.'@reconciliation_v1_operate_for_project_item_save');
    Route::post('/reconciliation/v1/operate/project/statistic-daily', $reconciliationController.'@reconciliation_v1_operate_for_project_statistic_daily');




    // 【每日结算-管理】
    Route::post('/reconciliation/v1/operate/daily/datatable-list-query', $reconciliationController.'@reconciliation_v1_operate_for_daily_datatable_list_query');
    Route::post('/reconciliation/v1/operate/daily/item-get', $reconciliationController.'@reconciliation_v1_operate_for_daily_item_get');
    Route::post('/reconciliation/v1/operate/daily/item-save', $reconciliationController.'@reconciliation_v1_operate_for_daily_item_save');



    // 【通用】删除 & 恢复 & 永久删除
    Route::post('/reconciliation/v1/operate/universal/item-delete-by-admin', $reconciliationController.'@reconciliation_v1_operate_for_universal_item_delete_by_admin');
    Route::post('/reconciliation/v1/operate/universal/item-restore-by-admin', $reconciliationController.'@reconciliation_v1_operate_for_universal_item_restore_by_admin');
    Route::post('/reconciliation/v1/operate/universal/item-delete-permanently-by-admin', $reconciliationController.'@reconciliation_v1_operate_for_universal_item_delete_permanently_by_admin');
    // 【通用】启用 & 禁用
    Route::post('/reconciliation/v1/operate/universal/item-enable-by-admin', $reconciliationController.'@reconciliation_v1_operate_for_universal_item_enable_by_admin');
    Route::post('/reconciliation/v1/operate/universal/item-disable-by-admin', $reconciliationController.'@reconciliation_v1_operate_for_universal_item_disable_by_admin');
    // 【通用】字段修改
    Route::post('/reconciliation/v1/operate/universal/field-set', $reconciliationController.'@reconciliation_v1_operate_for_universal_field_set');



    Route::post('/reconciliation/v1/operate/project/item-recharge-save', $reconciliationController.'@reconciliation_v1_operate_for_project_item_recharge_save');
    Route::post('/reconciliation/v1/operate/daily/item-settle-save', $reconciliationController.'@reconciliation_v1_operate_for_daily_item_settle_save');




    // 【交易-管理】
    Route::post('/reconciliation/v1/operate/trade/datatable-list-query', $reconciliationController.'@reconciliation_v1_operate_for_trade_datatable_list_query');
    Route::post('/reconciliation/v1/operate/trade/item-get', $reconciliationController.'@reconciliation_v1_operate_for_trade_item_get');
    Route::post('/reconciliation/v1/operate/trade/item-save', $reconciliationController.'@reconciliation_v1_operate_for_trade_item_save');
    Route::post('/reconciliation/v1/operate/trade/item-confirm', $reconciliationController.'@reconciliation_v1_operate_for_trade_item_confirm');
    Route::post('/reconciliation/v1/operate/trade/item-delete', $reconciliationController.'@reconciliation_v1_operate_for_trade_item_delete');




    Route::post('/reconciliation/v1/operate/operation/item-operation-datatable-query', $reconciliationController.'@reconciliation_v1_operate_for_item_operation_record_datatable_query');




});

