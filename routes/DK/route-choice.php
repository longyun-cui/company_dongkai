<?php


Route::get('/', function () {
    dd('route-choice');
});


$controller = "DKAdmin2_Controller";

Route::match(['get','post'], 'login', $controller.'@login');
Route::match(['get','post'], 'logout', $controller.'@logout');
Route::match(['get','post'], 'logout_without_token', $controller.'@logout_without_token');


Route::match(['get', 'post'], '/api/okcc/receivingResult', $controller.'@operate_api_okcc_receivingResult');




/*
 * 超级管理员系统（后台）
 * 需要登录
 */
Route::group(['middleware' => ['dk.admin_2.login']], function () {

    $controller = 'DKAdmin2_Controller';

    Route::post('/is_only_me', $controller.'@check_is_only_me');

    Route::get('/404', $controller.'@view_admin_404');

    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');
});

Route::group(['middleware' => ['dk.admin_2.login','dk.admin_2.password_change']], function () {

    $controller = 'DKAdmin2_Controller';


//    Route::post('/is_only_me', $controller.'@check_is_only_me');
    Route::get('/', $controller.'@view_admin_index');
//    Route::get('/404', $controller.'@view_admin_404');




    Route::match(['get','post'], '/select2/select2_project', $controller.'@operate_select2_project');
    Route::match(['get','post'], '/select2/select2_customer', $controller.'@operate_select2_customer');








    /*
     * 个人信息管理
     */
    Route::get('/my-account/my-profile-info-index/', $controller.'@view_my_profile_info_index');
    Route::match(['get','post'], '/my-account/my-profile-info-edit', $controller.'@operate_my_profile_info_edit');
//    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');




    /*
     * 客户管理
     */
    // 列表
    Route::match(['get','post'], '/user/customer-list', $controller.'@view_user_customer_list');
    Route::match(['get','post'], '/user/customer-list-for-all', $controller.'@view_user_customer_list_for_all');
    // 修改列表
    Route::match(['get','post'], '/user/customer-modify-record', $controller.'@view_user_customer_modify_record');
    // 创建 & 修改
    Route::match(['get','post'], '/user/customer-create', $controller.'@operate_user_customer_create');
    Route::match(['get','post'], '/user/customer-edit', $controller.'@operate_user_customer_edit');
    // 编辑-信息
    Route::post('/user/customer-info-text-set', $controller.'@operate_customer_info_text_set');
    Route::post('/user/customer-info-time-set', $controller.'@operate_customer_info_time_set');
    Route::post('/user/customer-info-radio-set', $controller.'@operate_customer_info_option_set');
    Route::post('/user/customer-info-select-set', $controller.'@operate_customer_info_option_set');
    Route::post('/user/customer-info-select2-set', $controller.'@operate_customer_info_option_set');
    // 【用户-员工管理】修改密码
    Route::match(['get','post'], '/user/customer-password-admin-change', $controller.'@operate_user_customer_password_admin_change');
    Route::match(['get','post'], '/user/customer-password-admin-reset', $controller.'@operate_user_customer_password_admin_reset');
    Route::match(['get','post'], '/user/customer-login', $controller.'@operate_user_customer_login');
    // 删除 & 恢复
    Route::post('/user/customer-admin-delete', $controller.'@operate_user_customer_admin_delete');
    Route::post('/user/customer-admin-restore', $controller.'@operate_user_customer_admin_restore');
    Route::post('/user/customer-admin-delete-permanently', $controller.'@operate_user_customer_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/user/customer-admin-enable', $controller.'@operate_user_customer_admin_enable');
    Route::post('/user/customer-admin-disable', $controller.'@operate_user_customer_admin_disable');
    // 财务
    Route::match(['get','post'], '/user/customer-company-recharge-record', $controller.'@view_user_customer_recharge_record');
    Route::post('/user/customer-finance-recharge-create', $controller.'@operate_user_customer_finance_recharge_create');
    Route::post('/user/customer-finance-recharge-edit', $controller.'@operate_user_customer_finance_recharge_edit');









    /*
     * 部门管理
     */
    // select2
    Route::match(['get','post'], '/department/department_select2_leader', $controller.'@operate_department_select2_leader');
    Route::match(['get','post'], '/department/department_select2_superior_department', $controller.'@operate_department_select2_superior_department');
    // 列表
    Route::match(['get','post'], '/department/department-list', $controller.'@view_department_list');
    Route::match(['get','post'], '/department/department-list-for-all', $controller.'@view_department_list_for_all');
    // 修改列表
    Route::match(['get','post'], '/department/department-modify-record', $controller.'@view_department_modify_record');
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








    /*
     * 用户-员工管理
     */
    Route::match(['get','post'], '/user/user_select2_district', $controller.'@operate_user_select2_district');
    Route::match(['get','post'], '/user/user_select2_choice', $controller.'@operate_user_select2_choice');
    Route::match(['get','post'], '/user/user_select2_superior', $controller.'@operate_user_select2_superior');
    Route::match(['get','post'], '/user/user_select2_department', $controller.'@operate_user_select2_department');

    // 列表
    Route::match(['get','post'], '/user/staff-list', $controller.'@view_user_staff_list');
    Route::match(['get','post'], '/user/staff-list-for-all', $controller.'@view_user_staff_list_for_all');
    // 修改列表
    Route::match(['get','post'], '/user/staff-modify-record', $controller.'@view_user_staff_modify_record');
    // 创建 & 修改
    Route::match(['get','post'], '/user/staff-create', $controller.'@operate_user_staff_create');
    Route::match(['get','post'], '/user/staff-edit', $controller.'@operate_user_staff_edit');
    // 编辑-信息
    Route::post('/user/staff-info-text-set', $controller.'@operate_staff_info_text_set');
    Route::post('/user/staff-info-time-set', $controller.'@operate_staff_info_time_set');
    Route::post('/user/staff-info-radio-set', $controller.'@operate_staff_info_option_set');
    Route::post('/user/staff-info-select-set', $controller.'@operate_staff_info_option_set');
    Route::post('/user/staff-info-select2-set', $controller.'@operate_staff_info_option_set');
    // 修改密码
    Route::match(['get','post'], '/user/staff-password-admin-change', $controller.'@operate_user_staff_password_admin_change');
    Route::match(['get','post'], '/user/staff-password-admin-reset', $controller.'@operate_user_staff_password_admin_reset');
    Route::match(['get','post'], '/user/user-login', $controller.'@operate_user_user_login');
    // 删除 & 恢复 & 永久删除
    Route::post('/user/staff-admin-delete', $controller.'@operate_user_staff_admin_delete');
    Route::post('/user/staff-admin-restore', $controller.'@operate_user_staff_admin_restore');
    Route::post('/user/staff-admin-delete-permanently', $controller.'@operate_user_staff_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/user/staff-admin-enable', $controller.'@operate_user_staff_admin_enable');
    Route::post('/user/staff-admin-disable', $controller.'@operate_user_staff_admin_disable');
    // 解锁
    Route::post('/user/staff-admin-unlock', $controller.'@operate_user_staff_admin_unlock');
    // 晋升
    Route::post('/user/staff-admin-promote', $controller.'@operate_user_staff_admin_promote');
    Route::post('/user/staff-admin-demote', $controller.'@operate_user_staff_admin_demote');










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











    // 列表
    Route::match(['get','post'], '/item/telephone-list', $controller.'@view_item_telephone_list');
    // 修改信息
    Route::match(['get','post'], '/item/telephone-modify-record', $controller.'@view_item_telephone_modify_record');

    // 创建 & 修改
    Route::match(['get','post'], '/item/telephone-create', $controller.'@operate_item_telephone_create');
    Route::match(['get','post'], '/item/telephone-edit', $controller.'@operate_item_telephone_edit');
    // 导入
    Route::match(['get','post'], '/item/telephone-import', $controller.'@operate_item_telephone_import');










    /*
     * 订单管理
     */
    // select2
    Route::match(['get','post'], '/item/item_select2_user', $controller.'@operate_item_select2_user');
    Route::match(['get','post'], '/item/item_select2_customer', $controller.'@operate_item_select2_customer');
    Route::match(['get','post'], '/item/item_select2_project', $controller.'@operate_item_select2_project');
    Route::match(['get','post'], '/item/item_select2_team', $controller.'@operate_item_select2_team');

    Route::match(['get','post'], '/item/order_select2_project', $controller.'@operate_order_select2_project');
    Route::match(['get','post'], '/item/order_select2_customer', $controller.'@operate_order_select2_customer');

    // 列表
    Route::match(['get','post'], '/item/clue-list', $controller.'@view_item_clue_list');
    // 修改信息
    Route::match(['get','post'], '/item/clue-modify-record', $controller.'@view_item_clue_modify_record');

    // 创建 & 修改
    Route::match(['get','post'], '/item/clue-create', $controller.'@operate_item_clue_create');
    Route::match(['get','post'], '/item/clue-edit', $controller.'@operate_item_clue_edit');
    // 导入
    Route::match(['get','post'], '/item/clue-import', $controller.'@operate_item_clue_import');

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

    Route::post('/item/clue-put-on-shelf', $controller.'@operate_item_clue_put_on_shelf');
    Route::post('/item/clue-put-on-shelf-by-bulk', $controller.'@operate_item_clue_put_on_shelf_by_bulk');
    Route::post('/item/clue-put-off-shelf', $controller.'@operate_item_clue_put_off_shelf');

    // 订单-基本信息
    Route::post('/item/order-info-text-set', $controller.'@operate_item_order_info_text_set');
    Route::post('/item/order-info-time-set', $controller.'@operate_item_order_info_time_set');
    Route::post('/item/order-info-radio-set', $controller.'@operate_item_order_info_option_set');
    Route::post('/item/order-info-select-set', $controller.'@operate_item_order_info_option_set');
    Route::post('/item/order-info-select2-set', $controller.'@operate_item_order_info_option_set');
    Route::post('/item/order-info-customer-set', $controller.'@operate_item_order_info_customer_set');
    Route::post('/item/order-info-project-set', $controller.'@operate_item_order_info_project_set');
    // 订单-附件
    Route::post('/item/order-info-attachment-set', $controller.'@operate_item_order_info_attachment_set');
    Route::post('/item/order-info-attachment-delete', $controller.'@operate_item_order_info_attachment_delete');


    // 订单-财务信息
    Route::match(['get','post'], '/item/order-finance-record', $controller.'@view_item_order_finance_record');
    Route::post('/item/order-finance-record-create', $controller.'@operate_item_order_finance_record_create');
    Route::post('/item/order-finance-record-edit', $controller.'@operate_item_order_finance_record_edit');








    /*
     * 上架
     */
    // 列表
    Route::match(['get','post'], '/item/choice-list', $controller.'@view_item_choice_list');
    // 修改信息
    Route::match(['get','post'], '/item/choice-modify-record', $controller.'@view_item_choice_modify_record');
    // 删除 & 恢复
    Route::post('/item/choice-delete', $controller.'@operate_item_choice_delete');
    Route::post('/item/choice-restore', $controller.'@operate_item_choice_restore');
    Route::post('/item/choice-delete-permanently', $controller.'@operate_item_choice_delete_permanently');
    // 启用 & 禁用
    Route::post('/item/choice-enable', $controller.'@operate_item_choice_enable');
    Route::post('/item/choice-disable', $controller.'@operate_item_choice_disable');
    // 发布 & 完成 & 备注
    Route::post('/item/choice-exported', $controller.'@operate_item_choice_exported');
    Route::post('/item/choice-bulk-exported', $controller.'@operate_item_choice_bulk_exported');

    Route::post('/item/choice-verify', $controller.'@operate_item_choice_verify');
    Route::post('/item/choice-inspect', $controller.'@operate_item_choice_inspect');
    Route::post('/item/choice-publish', $controller.'@operate_item_choice_publish');
    Route::post('/item/choice-complete', $controller.'@operate_item_choice_complete');
    Route::post('/item/choice-abandon', $controller.'@operate_item_choice_abandon');
    Route::post('/item/choice-reuse', $controller.'@operate_item_choice_reuse');
    Route::post('/item/choice-remark-edit', $controller.'@operate_item_choice_remark_edit');
    Route::post('/item/choice-follow', $controller.'@operate_item_choice_follow');
    Route::post('/item/choice-quality-evaluate', $controller.'@operate_item_choice_quality_evaluate');




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

    Route::match(['get','post'], '/statistic/statistic-choice', $controller.'@view_statistic_choice');
    Route::match(['get','post'], '/statistic/statistic-choice-by-customer', $controller.'@view_statistic_choice_by_customer');
    Route::match(['get','post'], '/statistic/statistic-choice-by-project', $controller.'@view_statistic_choice_by_project');
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










});

