<?php


Route::get('/me', function () {
    dd('route-admin');
});


$controller = "DKCCController";

Route::match(['get','post'], 'login', $controller.'@login');
Route::match(['get','post'], 'logout', $controller.'@logout');
Route::match(['get','post'], 'logout_without_token', $controller.'@logout_without_token');

Route::match(['get','post'], '/cc_api/okcc/receiving-result', $controller.'@operate_api_OKCC_receiving_result');

/*
 * 超级管理员系统（后台）
 * 需要登录
 */
Route::group(['middleware' => ['dk.cc.login']], function () {

    $controller = 'DKCCController';

    Route::post('/is_only_me', $controller.'@check_is_only_me');

    Route::get('/404', $controller.'@view_admin_404');

    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');
});

Route::group(['middleware' => ['dk.cc.login','dk.cc.password_change']], function () {

    $controller = 'DKCCController';


//    Route::post('/is_only_me', $controller.'@check_is_only_me');
    Route::get('/', $controller.'@view_admin_index');
//    Route::get('/404', $controller.'@view_admin_404');



    Route::get('/job/update_pools', $controller.'@operate_job_update_pools');



    /*
     * 个人信息管理
     */
    Route::get('/my-account/my-profile-info-index/', $controller.'@view_my_profile_info_index');
    Route::match(['get','post'], '/my-account/my-profile-info-edit', $controller.'@operate_my_profile_info_edit');
//    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');


    // select2
    Route::match(['get','post'], '/company/team_select2_leader', $controller.'@operate_department_select2_leader');
    Route::match(['get','post'], '/company/team_select2_superior_department', $controller.'@operate_department_select2_superior_department');


    /*
     * 公司管理-团队
     */
    // 列表
    Route::match(['get','post'], '/company/team-list', $controller.'@view_company_team_list');
    // 修改列表
    Route::match(['get','post'], '/company/team-modify-record', $controller.'@view_company_team_modify_record');
    // 创建 & 修改
    Route::match(['get','post'], '/company/team-create', $controller.'@operate_company_team_create');
    Route::match(['get','post'], '/company/team-edit', $controller.'@operate_company_team_edit');
    // 编辑-信息
    Route::post('/company/team-info-text-set', $controller.'@operate_company_team_info_text_set');
    Route::post('/company/team-info-time-set', $controller.'@operate_company_team_info_time_set');
    Route::post('/company/team-info-radio-set', $controller.'@operate_company_team_info_option_set');
    Route::post('/company/team-info-select-set', $controller.'@operate_company_team_info_option_set');
    Route::post('/company/team-info-select2-set', $controller.'@operate_company_team_info_option_set');
    // 删除 & 恢复
    Route::post('/company/team-admin-delete', $controller.'@operate_company_team_admin_delete');
    Route::post('/company/team-admin-restore', $controller.'@operate_v_admin_restore');
    Route::post('/company/team-admin-delete-permanently', $controller.'@operate_company_team_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/company/team-admin-enable', $controller.'@operate_company_team_admin_enable');
    Route::post('/company/team-admin-disable', $controller.'@operate_company_team_admin_disable');

    Route::post('/company/team-login-okcc', $controller.'@operate_company_team_login_okcc');




    /*
     * 用户-员工管理
     */
    Route::match(['get','post'], '/user/user_select2_district', $controller.'@operate_user_select2_district');
    Route::match(['get','post'], '/user/user_select2_sales', $controller.'@operate_user_select2_sales');
    Route::match(['get','post'], '/user/user_select2_superior', $controller.'@operate_user_select2_superior');
    Route::match(['get','post'], '/user/user_select2_department', $controller.'@operate_user_select2_department');

    // 列表
    Route::match(['get','post'], '/company/staff-list', $controller.'@view_company_staff_list');
    // 修改列表
    Route::match(['get','post'], '/company/staff-modify-record', $controller.'@view_company_staff_modify_record');
    // 创建 & 修改
    Route::match(['get','post'], '/company/staff-create', $controller.'@operate_company_staff_create');
    Route::match(['get','post'], '/company/staff-edit', $controller.'@operate_company_staff_edit');
    // 编辑-信息
    Route::post('/company/staff-info-text-set', $controller.'@operate_staff_info_text_set');
    Route::post('/company/staff-info-time-set', $controller.'@operate_staff_info_time_set');
    Route::post('/company/staff-info-radio-set', $controller.'@operate_staff_info_option_set');
    Route::post('/company/staff-info-select-set', $controller.'@operate_staff_info_option_set');
    Route::post('/company/staff-info-select2-set', $controller.'@operate_staff_info_option_set');
    // 修改密码
    Route::match(['get','post'], '/company/staff-password-admin-change', $controller.'@operate_company_staff_password_admin_change');
    Route::match(['get','post'], '/company/staff-password-admin-reset', $controller.'@operate_company_staff_password_admin_reset');
    Route::match(['get','post'], '/user/user-login', $controller.'@operate_user_user_login');
    // 删除 & 恢复 & 永久删除
    Route::post('/company/staff-admin-delete', $controller.'@operate_company_staff_admin_delete');
    Route::post('/company/staff-admin-restore', $controller.'@operate_company_staff_admin_restore');
    Route::post('/company/staff-admin-delete-permanently', $controller.'@operate_company_staff_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/company/staff-admin-enable', $controller.'@operate_company_staff_admin_enable');
    Route::post('/company/staff-admin-disable', $controller.'@operate_company_staff_admin_disable');
    // 解锁
    Route::post('/company/staff-admin-unlock', $controller.'@operate_company_staff_admin_unlock');
    // 晋升
    Route::post('/company/staff-admin-promote', $controller.'@operate_company_staff_admin_promote');
    Route::post('/company/staff-admin-demote', $controller.'@operate_company_staff_admin_demote');




    /*
     * 客户管理
     */
    // 列表
    Route::match(['get','post'], '/user/client-list', $controller.'@view_user_client_list');
    Route::match(['get','post'], '/user/client-list-for-all', $controller.'@view_user_client_list_for_all');
    // 修改列表
    Route::match(['get','post'], '/user/client-modify-record', $controller.'@view_user_client_modify_record');
    // 创建 & 修改
    Route::match(['get','post'], '/user/client-create', $controller.'@operate_user_client_create');
    Route::match(['get','post'], '/user/client-edit', $controller.'@operate_user_client_edit');
    // 编辑-信息
    Route::post('/user/client-info-text-set', $controller.'@operate_client_info_text_set');
    Route::post('/user/client-info-time-set', $controller.'@operate_client_info_time_set');
    Route::post('/user/client-info-radio-set', $controller.'@operate_client_info_option_set');
    Route::post('/user/client-info-select-set', $controller.'@operate_client_info_option_set');
    Route::post('/user/client-info-select2-set', $controller.'@operate_client_info_option_set');
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
    // 财务
    Route::match(['get','post'], '/user/client-company-recharge-record', $controller.'@view_user_client_recharge_record');
    Route::post('/user/client-finance-recharge-create', $controller.'@operate_user_client_finance_recharge_create');
    Route::post('/user/client-finance-recharge-edit', $controller.'@operate_user_client_finance_recharge_edit');











    /*
     * 电话管理
     */
    // 列表
    Route::match(['get','post'], '/pool/telephone-list', $controller.'@view_pool_telephone_list');
    // 导入 & 创建 & 修改
    Route::match(['get','post'], '/pool/telephone-import', $controller.'@operate_pool_telephone_import');
    Route::match(['get','post'], '/pool/telephone-blacklist-import', $controller.'@operate_service__telephone_blacklist_import');
    Route::match(['get','post'], '/pool/telephone-create', $controller.'@operate_service_telephone_create');
    Route::match(['get','post'], '/pool/telephone-edit', $controller.'@operate_service_telephone_edit');
    Route::match(['get','post'], '/pool/telephone-blacklist-import', $controller.'@operate_service_telephone_blacklist_import');
    // 下载
    Route::post('/pool/telephone-download', $controller.'@operate_pool_telephone_download');
    /*
     * 任务管理
     */
    // 列表
    Route::match(['get','post'], '/pool/task-list', $controller.'@view_pool_task_list');
    // 【任务】下载
    Route::post('/pool/task-file-download', $controller.'@operate_pool_task_file_download');











    /*
     * 电话管理
     */
    // 列表
    Route::match(['get','post'], '/service/telephone-list', $controller.'@view_service_telephone_list');
    // 导入 & 创建 & 修改
    Route::match(['get','post'], '/service/telephone-import', $controller.'@operate_service_telephone_import');
    Route::match(['get','post'], '/service/telephone-blacklist-import', $controller.'@operate_service__telephone_blacklist_import');
    Route::match(['get','post'], '/service/telephone-create', $controller.'@operate_service_telephone_create');
    Route::match(['get','post'], '/service/telephone-edit', $controller.'@operate_service_telephone_edit');
    Route::match(['get','post'], '/service/telephone-blacklist-import', $controller.'@operate_service_telephone_blacklist_import');
    // 下载
    Route::post('/service/telephone-download', $controller.'@operate_service_telephone_download');




    /*
     * 任务管理
     */
    // 列表
    Route::match(['get','post'], '/service/task-list', $controller.'@view_service_task_list');
    // 导入 & 创建 & 修改
    Route::match(['get','post'], '/service/task-import', $controller.'@operate_service_task_import');
    Route::match(['get','post'], '/service/task-create', $controller.'@operate_service_task_create');
    Route::match(['get','post'], '/service/task-edit', $controller.'@operate_service_task_edit');
    // 【任务】删除 & 恢复 & 永久删除
    Route::post('/service/task-admin-delete', $controller.'@operate_service_task_admin_delete');
    Route::post('/service/task-admin-restore', $controller.'@operate_service_task_admin_restore');
    Route::post('/service/task-admin-delete-permanently', $controller.'@operate_service_task_admin_delete_permanently');
    // 【任务】启用 & 禁用
    Route::post('/service/task-admin-enable', $controller.'@operate_service_task_admin_enable');
    Route::post('/service/task-admin-disable', $controller.'@operate_service_task_admin_disable');
    // 【任务】批量操作
    Route::post('/service/task-admin-operate-bulk', $controller.'@operate_service_task_admin_operate_bulk');
    Route::post('/service/task-admin-delete-bulk', $controller.'@operate_service_task_admin_delete_bulk');
    Route::post('/service/task-admin-restore-bulk', $controller.'@operate_service_task_admin_restore_bulk');
    Route::post('/service/task-admin-delete-permanently-bulk', $controller.'@operate_service_task_admin_delete_permanently_bulk');
    // 【任务】下载
    Route::post('/service/task-file-download', $controller.'@operate_service_task_file_download');
    // 【任务】操作
    Route::post('/service/task-enable', $controller.'@operate_service_task_enable');
    Route::post('/service/task-disable', $controller.'@operate_service_task_disable');
    Route::post('/service/task-delete', $controller.'@operate_service_task_delete');
    Route::post('/service/task-restore', $controller.'@operate_service_task_restore');
    Route::post('/service/task-delete-permanently', $controller.'@operate_service_task_delete_permanently');
    Route::post('/service/task-publish', $controller.'@operate_service_task_publish');
    Route::post('/service/task-complete', $controller.'@operate_service_task_complete');
    Route::post('/service/task-remark-edit', $controller.'@operate_service_task_remark_edit');








    /*
     * 通话管理
     */
    // 列表
    Route::match(['get','post'], '/service/call-record-list', $controller.'@view_service_call_record_list');
    Route::match(['get','post'], '/service/call-statistic-list', $controller.'@view_service_call_statistic_list');







    /*
     * 地域管理
     */
    // select2
    Route::match(['get','post'], '/district/district_select2_city', $controller.'@operate_district_select2_city');
    Route::match(['get','post'], '/district/district_select2_district', $controller.'@operate_district_select2_district');
    // 创建 & 修改
    Route::match(['get','post'], '/district/district-create', $controller.'@operate_item_district_create');
    Route::match(['get','post'], '/district/district-edit', $controller.'@operate_item_district_edit');
    // 列表
    Route::match(['get','post'], '/district/district-list', $controller.'@view_item_district_list');

    // 删除 & 恢复
    Route::post('/district/district-admin-delete', $controller.'@operate_item_district_admin_delete');
    Route::post('/district/district-admin-restore', $controller.'@operate_item_district_admin_restore');
    Route::post('/district/district-admin-delete-permanently', $controller.'@operate_item_district_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/district/district-admin-enable', $controller.'@operate_item_district_admin_enable');
    Route::post('/district/district-admin-disable', $controller.'@operate_item_district_admin_disable');




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




















    /*
     * 订单管理
     */

    // 创建 & 修改
    Route::match(['get','post'], '/item/order-create', $controller.'@operate_item_order_create');
    Route::match(['get','post'], '/item/order-edit', $controller.'@operate_item_order_edit');
    // 导入
    Route::match(['get','post'], '/item/order-import', $controller.'@operate_item_order_import');
    Route::match(['get','post'], '/item/order-import-for-admin', $controller.'@operate_item_order_import_for_admin');

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
    Route::post('/item/order-bulk-deliver', $controller.'@operate_item_order_bulk_deliver');
    Route::post('/item/order-deliver-get-delivered', $controller.'@operate_item_order_deliver_get_delivered');
    Route::post('/item/order-distribute', $controller.'@operate_item_order_distribute');

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


    // 订单-财务信息
    Route::match(['get','post'], '/item/order-finance-record', $controller.'@view_item_order_finance_record');
    Route::post('/item/order-finance-record-create', $controller.'@operate_item_order_finance_record_create');
    Route::post('/item/order-finance-record-edit', $controller.'@operate_item_order_finance_record_edit');
    // 订单-修改信息
    Route::match(['get','post'], '/item/order-modify-record', $controller.'@view_item_order_modify_record');








    /*
     * 交付
     */
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
    Route::post('/item/delivery-exported', $controller.'@operate_item_delivery_exported');
    Route::post('/item/delivery-bulk-exported', $controller.'@operate_item_delivery_bulk_exported');

    Route::post('/item/delivery-verify', $controller.'@operate_item_delivery_verify');
    Route::post('/item/delivery-inspect', $controller.'@operate_item_delivery_inspect');
    Route::post('/item/delivery-publish', $controller.'@operate_item_delivery_publish');
    Route::post('/item/delivery-complete', $controller.'@operate_item_delivery_complete');
    Route::post('/item/delivery-abandon', $controller.'@operate_item_delivery_abandon');
    Route::post('/item/delivery-reuse', $controller.'@operate_item_delivery_reuse');
    Route::post('/item/delivery-remark-edit', $controller.'@operate_item_delivery_remark_edit');
    Route::post('/item/delivery-follow', $controller.'@operate_item_delivery_follow');
    Route::post('/item/delivery-quality-evaluate', $controller.'@operate_item_delivery_quality_evaluate');




    /*
     * 分发
     */
    // 列表
    Route::match(['get','post'], '/item/distribution-list', $controller.'@view_item_distribution_list');





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
    // 修改列表
    Route::match(['get','post'], '/finance/daily-modify-record', $controller.'@view_finance_daily_modify_record');
    // 创建 & 修改
    Route::match(['get','post'], '/finance/daily-list-build', $controller.'@operate_finance_daily_list_build');
    // 编辑-信息
    Route::post('/finance/daily-info-text-set', $controller.'@operate_finance_daily_info_text_set');
    Route::post('/finance/daily-info-time-set', $controller.'@operate_finance_daily_info_time_set');
    Route::post('/finance/daily-info-radio-set', $controller.'@operate_finance_daily_info_option_set');
    Route::post('/finance/daily-info-select-set', $controller.'@operate_finance_daily_info_option_set');
    Route::post('/finance/daily-info-select2-set', $controller.'@operate_finance_daily_info_option_set');












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

    Route::match(['get','post'], '/statistic/statistic-recent', $controller.'@view_statistic_recent');

    Route::match(['get','post'], '/statistic/statistic-delivery', $controller.'@view_statistic_delivery');
    Route::match(['get','post'], '/statistic/statistic-delivery-by-client', $controller.'@view_statistic_delivery_by_client');
    Route::match(['get','post'], '/statistic/statistic-delivery-by-project', $controller.'@view_statistic_delivery_by_project');
    Route::match(['get','post'], '/statistic/statistic-project', $controller.'@view_statistic_project');
    Route::match(['get','post'], '/statistic/statistic-department', $controller.'@view_statistic_department');
    Route::match(['get','post'], '/statistic/statistic-customer-service', $controller.'@view_statistic_customer_service');
    Route::match(['get','post'], '/statistic/statistic-inspector', $controller.'@view_statistic_inspector');

    Route::post('/statistic/statistic-get-data-for-department', $controller.'@get_statistic_data_for_department');
    Route::post('/statistic/statistic-get-data-for-customer-service', $controller.'@get_statistic_data_for_customer_service');
    Route::post('/statistic/statistic-get-data-for-inspector', $controller.'@get_statistic_data_for_inspector');
    Route::post('/statistic/statistic-get-data-for-deliverer', $controller.'@get_statistic_data_for_deliverer');


    Route::match(['get','post'], '/staff-statistic/statistic-customer-service', $controller.'@view_staff_statistic_customer_service');








    /*
     * export 数据导出
     */
    Route::match(['get','post'], '/statistic/statistic-export', $controller.'@operate_statistic_export');
    Route::match(['get','post'], '/statistic/statistic-export-for-order', $controller.'@operate_statistic_export_for_order');
    Route::match(['get','post'], '/statistic/statistic-export-for-order-by-ids', $controller.'@operate_statistic_export_for_order_by_ids');
    Route::match(['get','post'], '/statistic/statistic-export-for-circle', $controller.'@operate_statistic_export_for_circle');
    Route::match(['get','post'], '/statistic/statistic-export-for-finance', $controller.'@operate_statistic_export_for_finance');



    Route::match(['get','post'], '/item/record-list-for-all', $controller.'@view_record_list_for_all');

    Route::match(['get','post'], '/record/visit-list', $controller.'@view_record_visit_list');




    /*
     * download 下载
     */
    Route::get('/download/file-download', $controller.'@operate_download_file_download');
    Route::get('/download/file-create-txt', $controller.'@operate_download_file_create_txt');


});

