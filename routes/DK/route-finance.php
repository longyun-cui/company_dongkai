<?php


Route::get('/', function () {
    dd('route-finance');
});


$controller = "DKFinanceController";

Route::match(['get','post'], 'login', $controller.'@login');
Route::match(['get','post'], 'logout', $controller.'@logout');
Route::match(['get','post'], 'logout_without_token', $controller.'@logout_without_token');


/*
 * 超级管理员系统（后台）
 * 需要登录
 */
Route::group(['middleware' => ['dk.finance.user.login']], function () {

    $controller = 'DKFinanceController';

    Route::post('/is_only_me', $controller.'@check_is_only_me');

    Route::get('/404', $controller.'@view_finance_404');

    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');
});

Route::group(['middleware' => ['dk.finance.user.login','dk.admin.password_change']], function () {

    $controller = 'DKFinanceController';


//    Route::post('/is_only_me', $controller.'@check_is_only_me');
    Route::get('/', $controller.'@view_finance_index');
//    Route::get('/404', $controller.'@view_finance_404');


    /*
     * 个人信息管理
     */
    Route::get('/my-account/my-profile-info-index/', $controller.'@view_my_profile_info_index');
    Route::match(['get','post'], '/my-account/my-profile-info-edit', $controller.'@operate_my_profile_info_edit');
//    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');





    Route::match(['get','post'], '/select2/select2_user', $controller.'@operate_select2_user');
    Route::match(['get','post'], '/select2/select2_company', $controller.'@operate_select2_company');
    Route::match(['get','post'], '/select2/select2_project', $controller.'@operate_select2_project');



    /*
     * 客户管理
     */
    // 创建 & 修改
    Route::match(['get','post'], '/user/user-create', $controller.'@operate_user_user_create');
    Route::match(['get','post'], '/user/user-edit', $controller.'@operate_user_user_edit');
    // 【用户-员工管理】修改密码
    Route::match(['get','post'], '/user/user-password-admin-change', $controller.'@operate_user_user_password_admin_change');
    Route::match(['get','post'], '/user/user-password-admin-reset', $controller.'@operate_user_user_password_admin_reset');
    Route::match(['get','post'], '/user/user-login', $controller.'@operate_user_user_login');
    // 删除 & 恢复
    Route::post('/user/user-admin-delete', $controller.'@operate_user_user_admin_delete');
    Route::post('/user/user-admin-restore', $controller.'@operate_user_user_admin_restore');
    Route::post('/user/user-admin-delete-permanently', $controller.'@operate_user_user_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/user/user-admin-enable', $controller.'@operate_user_user_admin_enable');
    Route::post('/user/user-admin-disable', $controller.'@operate_user_user_admin_disable');

    // 列表
    Route::match(['get','post'], '/user/user-list', $controller.'@view_user_user_list');








    /*
     * 公司&渠道管理
     */
    Route::match(['get','post'], '/company/company_select2_leader', $controller.'@operate_company_select2_leader');
    Route::match(['get','post'], '/company/company_select2_superior_company', $controller.'@operate_company_select2_superior_company');
    // 创建 & 修改
    Route::match(['get','post'], '/company/company-create', $controller.'@operate_company_create');
    Route::match(['get','post'], '/company/company-edit', $controller.'@operate_company_edit');

    // 编辑-信息
    Route::post('/company/company-info-text-set', $controller.'@operate_company_info_text_set');
    Route::post('/company/company-info-time-set', $controller.'@operate_company_info_time_set');
    Route::post('/company/company-info-radio-set', $controller.'@operate_company_info_option_set');
    Route::post('/company/company-info-select-set', $controller.'@operate_company_info_option_set');
    Route::post('/company/company-info-select2-set', $controller.'@operate_company_info_option_set');

    // 删除 & 恢复
    Route::post('/company/company-admin-delete', $controller.'@operate_company_admin_delete');
    Route::post('/company/company-admin-restore', $controller.'@operate_company_admin_restore');
    Route::post('/company/company-admin-delete-permanently', $controller.'@operate_company_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/company/company-admin-enable', $controller.'@operate_company_admin_enable');
    Route::post('/company/company-admin-disable', $controller.'@operate_company_admin_disable');

    // 列表
    Route::match(['get','post'], '/company/company-list', $controller.'@view_company_list');
    Route::match(['get','post'], '/company/company-list-for-all', $controller.'@view_company_list_for_all');


    // 渠道-财务信息
    Route::match(['get','post'], '/company/company-recharge-record', $controller.'@view_company_recharge_record');
    Route::post('/company/company-finance-record-create', $controller.'@operate_company_finance_record_create');
    Route::post('/company/company-finance-record-edit', $controller.'@operate_company_finance_record_edit');
    // 渠道
    Route::match(['get','post'], '/company/company-funds-using-record', $controller.'@view_company_funds_using_record');


    // 修改信息
    Route::match(['get','post'], '/company/company-modify-record', $controller.'@view_company_modify_record');




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
    // 【用户-员工管理】晋升
    Route::post('/user/staff-admin-promote', $controller.'@operate_user_staff_admin_promote');
    Route::post('/user/staff-admin-demote', $controller.'@operate_user_staff_admin_demote');

    // 列表
    Route::match(['get','post'], '/user/staff-list', $controller.'@view_user_staff_list');
    Route::match(['get','post'], '/user/staff-list-for-all', $controller.'@view_staff_list_for_all');









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


    // 渠道-财务信息
    Route::match(['get','post'], '/project/project-funds-using-record', $controller.'@view_project_funds_using_record');
    Route::post('/project/project-funds-using-create', $controller.'@operate_project_funds_using_create');
    Route::post('/project/project-funds-using-edit', $controller.'@operate_project_funds_using_edit');


    // 修改信息
    Route::match(['get','post'], '/item/project-modify-record', $controller.'@view_item_project_modify_record');




















    /*
     * 订单管理
     */
    // select2
    Route::match(['get','post'], '/item/item_select2_user', $controller.'@operate_item_select2_user');
    Route::match(['get','post'], '/item/item_select2_project', $controller.'@operate_item_select2_project');
    Route::match(['get','post'], '/item/item_select2_company', $controller.'@operate_item_select2_company');


    // 创建 & 修改
    Route::match(['get','post'], '/item/daily-create', $controller.'@operate_item_daily_create');
    Route::match(['get','post'], '/item/daily-edit', $controller.'@operate_item_daily_edit');
    // 导入
    Route::match(['get','post'], '/item/daily-import', $controller.'@operate_item_daily_import');

    // 获取
    Route::match(['get','post'], '/item/daily-get', $controller.'@operate_item_daily_get');
    Route::match(['get','post'], '/item/daily-get-html', $controller.'@operate_item_daily_get_html');
    Route::match(['get','post'], '/item/daily-get-attachment-html', $controller.'@operate_item_daily_get_attachment_html');
    // 删除 & 恢复
    Route::post('/item/daily-delete', $controller.'@operate_item_daily_delete');
    Route::post('/item/daily-restore', $controller.'@operate_item_daily_restore');
    Route::post('/item/daily-delete-permanently', $controller.'@operate_item_daily_delete_permanently');
    // 启用 & 禁用
    Route::post('/item/daily-enable', $controller.'@operate_item_daily_enable');
    Route::post('/item/daily-disable', $controller.'@operate_item_daily_disable');
    // 发布 & 完成 & 备注
    Route::post('/item/daily-verify', $controller.'@operate_item_daily_verify');
    Route::post('/item/daily-publish', $controller.'@operate_item_daily_publish');
    Route::post('/item/daily-inspect', $controller.'@operate_item_daily_inspect');
    Route::post('/item/daily-complete', $controller.'@operate_item_daily_complete');
    Route::post('/item/daily-abandon', $controller.'@operate_item_daily_abandon');
    Route::post('/item/daily-reuse', $controller.'@operate_item_daily_reuse');
    Route::post('/item/daily-remark-edit', $controller.'@operate_item_daily_remark_edit');
    Route::post('/item/daily-deliver', $controller.'@operate_item_daily_deliver');
    Route::post('/item/daily-bulk-deliver', $controller.'@operate_item_daily_bulk_deliver');

    // 列表
    Route::match(['get','post'], '/item/daily-list', $controller.'@view_item_daily_list');

    // 订单-基本信息
    Route::post('/item/daily-info-text-set', $controller.'@operate_item_daily_info_text_set');
    Route::post('/item/daily-info-time-set', $controller.'@operate_item_daily_info_time_set');
    Route::post('/item/daily-info-radio-set', $controller.'@operate_item_daily_info_option_set');
    Route::post('/item/daily-info-select-set', $controller.'@operate_item_daily_info_option_set');
    Route::post('/item/daily-info-select2-set', $controller.'@operate_item_daily_info_option_set');
    // 订单-附件
    Route::post('/item/daily-info-attachment-set', $controller.'@operate_item_daily_info_attachment_set');
    Route::post('/item/daily-info-attachment-delete', $controller.'@operate_item_daily_info_attachment_delete');


    // 订单-财务信息
    Route::match(['get','post'], '/item/daily-finance-record', $controller.'@view_item_daily_finance_record');
    Route::post('/item/daily-finance-record-create', $controller.'@operate_item_daily_finance_record_create');
    Route::post('/item/daily-finance-record-edit', $controller.'@operate_item_daily_finance_record_edit');
    // 订单-修改信息
    Route::match(['get','post'], '/item/daily-modify-record', $controller.'@view_item_daily_modify_record');










    /*
     * statistic 数据统计
     */
    Route::match(['get','post'], '/statistic/statistic-list-for-all', $controller.'@view_statistic_list_for_all');
    Route::match(['get','post'], '/statistic/statistic-index', $controller.'@view_statistic_index');
    Route::match(['get','post'], '/statistic/statistic-user', $controller.'@view_statistic_user');

    Route::post('/statistic/statistic-get-data-for-comprehensive', $controller.'@get_statistic_data_for_comprehensive');
    Route::post('/statistic/statistic-get-data-for-order', $controller.'@get_statistic_data_for_order');
    Route::post('/statistic/statistic-get-data-for-finance', $controller.'@get_statistic_data_for_finance');

    // 项目报表
    Route::match(['get','post'], '/statistic/statistic-project', $controller.'@view_statistic_project');
    Route::post('/statistic/statistic-get-data-for-project', $controller.'@get_statistic_data_for_project');
    Route::post('/statistic/statistic-get-data-for-project-of-daily-list', $controller.'@get_statistic_data_for_project_of_daily_list_datatable');
    Route::post('/statistic/statistic-get-data-for-project-of-chart', $controller.'@get_statistic_data_for_project_of_chart');

    // 渠道报表
    Route::match(['get','post'], '/statistic/statistic-channel', $controller.'@view_statistic_channel');
    Route::post('/statistic/statistic-get-data-for-channel', $controller.'@get_statistic_data_for_channel');
    Route::post('/statistic/statistic-get-data-for-channel-of-project-list', $controller.'@get_statistic_data_for_channel_of_project_list_datatable');
    Route::post('/statistic/statistic-get-data-for-channel-of-chart', $controller.'@get_statistic_data_for_channel_of_chart');

    // 公司报表
    Route::match(['get','post'], '/statistic/statistic-company', $controller.'@view_statistic_company');
    Route::post('/statistic/statistic-get-data-for-company', $controller.'@get_statistic_data_for_company');
    Route::post('/statistic/statistic-get-data-for-company-of-project-list', $controller.'@get_statistic_data_for_company_of_project_list_datatable');
    Route::post('/statistic/statistic-get-data-for-company-of-chart', $controller.'@get_statistic_data_for_company_of_chart');

    // 业务报表
    Route::match(['get','post'], '/statistic/statistic-service', $controller.'@view_statistic_service');
    Route::post('/statistic/statistic-get-data-for-service', $controller.'@get_statistic_data_for_service');
    Route::post('/statistic/statistic-get-data-for-service-of-daily-list', $controller.'@get_statistic_data_for_service_of_daily_list_datatable');
    Route::post('/statistic/statistic-get-data-for-service-of-project-list', $controller.'@get_statistic_data_for_service_of_project_list_datatable');
    Route::post('/statistic/statistic-get-data-for-service-of-daily-chart', $controller.'@get_statistic_data_for_service_of_daily_chart');

    // 业务报表
    Route::match(['get','post'], '/statistic/statistic-company-overview', $controller.'@view_statistic_company_overview');
//    Route::post('/statistic/statistic-get-data-for-service', $controller.'@get_statistic_data_for_service');
    Route::post('/statistic/statistic-get-data-for-company-overview-of-channel-list', $controller.'@get_statistic_data_for_company_overview_of_channel_list_datatable');
//    Route::post('/statistic/statistic-get-data-for-service-of-project-list', $controller.'@get_statistic_data_for_service_of_project_list_datatable');
//    Route::post('/statistic/statistic-get-data-for-service-of-daily-chart', $controller.'@get_statistic_data_for_service_of_daily_chart');






    /*
     * export 数据导出
     */
    Route::match(['get','post'], '/statistic/statistic-export', $controller.'@operate_statistic_export');
    Route::match(['get','post'], '/statistic/statistic-export-for-order', $controller.'@operate_statistic_export_for_order');
    Route::match(['get','post'], '/statistic/statistic-export-for-order-by-ids', $controller.'@operate_statistic_export_for_order_by_ids');
    Route::match(['get','post'], '/statistic/statistic-export-for-circle', $controller.'@operate_statistic_export_for_circle');
    Route::match(['get','post'], '/statistic/statistic-export-for-finance', $controller.'@operate_statistic_export_for_finance');






    Route::match(['get','post'], '/record/record-list-for-all', $controller.'@view_record_list_for_all');
    Route::match(['get','post'], '/record/funds-recharge-list', $controller.'@view_record_funds_recharge_list');
    Route::match(['get','post'], '/record/funds-using-list', $controller.'@view_record_funds_using_list');


});

