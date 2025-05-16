<?php


Route::get('/', function () {
    dd('route-admin');
});

Route::get('/test', function () {

    $str = "Hello, World! 123";

    $input = request('input','');
    $result = searchCityCode($input, config('location_city.city'));
    dd($result);



});
Route::get('/test-memcached', function () {
    $key = 'test_key_' . time(); // 确保唯一性
    Cache::put($key, 'test_value', 10);
    $value = Cache::get($key);
    return $value === 'test_value' ? '成功' : '失败';
});


$controller = "DKAdminController";

Route::match(['get','post'], 'login', $controller.'@login');
Route::match(['get','post'], 'logout', $controller.'@logout');
Route::match(['get','post'], 'logout_without_token', $controller.'@logout_without_token');

Route::match(['get','post'], 'test-', $controller.'@logout_without_token');


/*
 * 超级管理员系统（后台）
 * 需要登录
 */
Route::group(['middleware' => ['yh.admin.login']], function () {

    $controller = 'DKAdminController';


    Route::match(['get','post'], '/data/phone-import-by-txt', $controller.'@operate_data_phone_import_by_txt');


    Route::post('/is_only_me', $controller.'@check_is_only_me');

    Route::get('/404', $controller.'@view_admin_404');
    Route::get('/okcc-test', $controller.'@okcc_test');

    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');



    // select2
    Route::post('/v1/operate/select2/select2_department', $controller.'@v1_operate_select2_department');
    Route::post('/v1/operate/select2/select2_staff', $controller.'@v1_operate_select2_staff');
    Route::post('/v1/operate/select2/select2_company', $controller.'@v1_operate_select2_company');
    Route::post('/v1/operate/select2/select2_client', $controller.'@v1_operate_select2_client');
    Route::post('/v1/operate/select2/select2_project', $controller.'@v1_operate_select2_project');


    // 【部门-管理】
    Route::post('/v1/operate/department/datatable-list-query', $controller.'@v1_operate_for_department_datatable_list_query');
    Route::post('/v1/operate/department/item-get', $controller.'@v1_operate_for_department_item_get');
    Route::post('/v1/operate/department/item-save', $controller.'@v1_operate_for_department_item_save');


    // 【员工-管理】
    Route::post('/v1/operate/staff/datatable-list-query', $controller.'@v1_operate_for_staff_datatable_list_query');
    Route::post('/v1/operate/staff/item-get', $controller.'@v1_operate_for_staff_item_get');
    Route::post('/v1/operate/staff/item-save', $controller.'@v1_operate_for_staff_item_save');


    // 【公司-管理】
    Route::post('/v1/operate/company/datatable-list-query', $controller.'@v1_operate_for_company_datatable_list_query');
    Route::post('/v1/operate/company/item-get', $controller.'@v1_operate_for_company_item_get');
    Route::post('/v1/operate/company/item-save', $controller.'@v1_operate_for_company_item_save');


    // 【客户-管理】
    Route::post('/v1/operate/client/datatable-list-query', $controller.'@v1_operate_for_client_datatable_list_query');
    Route::post('/v1/operate/client/item-get', $controller.'@v1_operate_for_client_item_get');
    Route::post('/v1/operate/client/item-save', $controller.'@v1_operate_for_client_item_save');


    // 【项目-管理】
    Route::post('/v1/operate/project/datatable-list-query', $controller.'@v1_operate_for_project_datatable_list_query');
    Route::post('/v1/operate/project/item-get', $controller.'@v1_operate_for_project_item_get');
    Route::post('/v1/operate/project/item-save', $controller.'@v1_operate_for_project_item_save');


    // 【地区-管理】
    Route::post('/v1/operate/location/datatable-list-query', $controller.'@v1_operate_for_location_datatable_list_query');
    Route::post('/v1/operate/location/item-get', $controller.'@v1_operate_for_location_item_get');
    Route::post('/v1/operate/location/item-save', $controller.'@v1_operate_for_location_item_save');


    // 【工单-管理】
    Route::post('/v1/operate/order/datatable-list-query', $controller.'@v1_operate_for_order_datatable_list_query');
//    Route::post('/v1/operate/order/luxury/datatable-list-query', $controller.'@v1_operate_for_order_luxury_datatable_list_query');
    Route::post('/v1/operate/order/item-operation-record-datatable-query', $controller.'@v1_operate_for_order_item_operation_record_datatable_query');
    Route::post('/v1/operate/order/item-get', $controller.'@v1_operate_for_order_item_get');
    Route::post('/v1/operate/order/item-save', $controller.'@v1_operate_for_order_item_save');
    Route::post('/v1/operate/order/luxury/item-save', $controller.'@v1_operate_for_order_luxury_item_save');
    Route::post('/v1/operate/order/aesthetic/item-save', $controller.'@v1_operate_for_order_aesthetic_item_save');
    Route::post('/v1/operate/order/item-publish', $controller.'@v1_operate_for_order_item_publish');
    Route::post('/v1/operate/order/item-inspect', $controller.'@v1_operate_for_order_item_inspect');
    Route::post('/v1/operate/order/item-deliver', $controller.'@v1_operate_for_order_item_deliver');
    Route::post('/v1/operate/order/item-get-api-call-record', $controller.'@v1_operate_for_order_item_get_api_call_record');


    // 【交付-管理】
    Route::post('/v1/operate/delivery/datatable-list-query', $controller.'@v1_operate_for_delivery_datatable_list_query');




    // 【导出-管理】
    Route::post('/v1/operate/record/datatable-list-query', $controller.'@v1_operate_for_record_datatable_list_query');

    Route::get('/v1/operate/statistic/order-export', $controller.'@v1_operate_for_statistic_order_export');
    Route::get('/v1/operate/statistic/order-export-by-ids', $controller.'@v1_operate_for_statistic_order_export_by_ids');
    Route::get('/v1/operate/statistic/delivery-export', $controller.'@v1_operate_for_statistic_delivery_export');
    Route::get('/v1/operate/statistic/duplicate-export', $controller.'@v1_operate_for_statistic_duplicate_export');






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


    // 【通用】批量-指派状态
    Route::post('/v1/operate/universal/bulk-assign-status', $controller.'@v1_operate_for_universal_bulk_assign_status');
    Route::post('/v1/operate/universal/bulk-assign-staff', $controller.'@v1_operate_for_universal_bulk_assign_staff');
    Route::post('/v1/operate/universal/bulk-api-push', $controller.'@v1_operate_for_universal_bulk_api_push');



    Route::post('/v1/operate/statistic/comprehensive-overview', $controller.'@v1_operate_for_get_statistic_data_of_comprehensive_overview');

    Route::post('/v1/operate/statistic/statistic-comprehensive', $controller.'@v1_operate_for_get_statistic_data_of_statistic_comprehensive');
    Route::post('/v1/operate/statistic/statistic-comprehensive-daily', $controller.'@v1_operate_for_get_statistic_data_of_statistic_comprehensive_daily');


    Route::post('/v1/operate/statistic/statistic-call-list', $controller.'@v1_operate_for_get_statistic_data_of_statistic_call_list');
    Route::post('/v1/operate/statistic/statistic-call-daily-overview', $controller.'@v1_operate_for_get_statistic_data_of_statistic_call_daily_overview');
    Route::post('/v1/operate/statistic/statistic-call-daily-month', $controller.'@v1_operate_for_get_statistic_data_of_statistic_call_daily_month');


    Route::post('/v1/operate/statistic/marketing/company-overview', $controller.'@v1_operate_for_get_statistic_data_of_company_overview');
    Route::post('/v1/operate/statistic/marketing/company-daily', $controller.'@v1_operate_for_get_statistic_data_of_company_daily');
    Route::post('/v1/operate/statistic/marketing/project', $controller.'@v1_operate_for_get_statistic_data_of_marketing_project');
    Route::post('/v1/operate/statistic/marketing/client', $controller.'@v1_operate_for_get_statistic_data_of_marketing_client');


    Route::post('/v1/operate/statistic/production/caller-overview', $controller.'@v1_operate_for_get_statistic_data_of_production_caller_overview');
    Route::post('/v1/operate/statistic/production/caller-rank', $controller.'@v1_operate_for_get_statistic_data_of_production_caller_rank');
    Route::post('/v1/operate/statistic/production/caller-recent', $controller.'@v1_operate_for_get_statistic_data_of_production_caller_recent');
    Route::post('/v1/operate/statistic/production/caller-daily', $controller.'@v1_operate_for_get_statistic_data_of_production_caller_daily');
    Route::post('/v1/operate/statistic/production/inspector-overview', $controller.'@v1_operate_for_get_statistic_data_of_production_inspector_overview');
    Route::post('/v1/operate/statistic/production/deliverer-overview', $controller.'@v1_operate_for_get_statistic_data_of_production_deliverer_overview');

    Route::post('/v1/operate/statistic/production/project', $controller.'@v1_operate_for_get_statistic_data_of_production_project');
    Route::post('/v1/operate/statistic/production/department', $controller.'@v1_operate_for_get_statistic_data_of_production_department');





});

Route::group(['middleware' => ['yh.admin.login','dk.admin.password_change']], function () {

    $controller = 'DKAdminController';


//    Route::post('/is_only_me', $controller.'@check_is_only_me');
    Route::get('/', $controller.'@view_admin_index');
    Route::get('/index1', $controller.'@view_admin_index1');
//    Route::get('/404', $controller.'@view_admin_404');


    /*
     * 个人信息管理
     */
    Route::get('/my-account/my-profile-info-index/', $controller.'@view_my_profile_info_index');
    Route::match(['get','post'], '/my-account/my-profile-info-edit', $controller.'@operate_my_profile_info_edit');
//    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');





    // select2
    Route::match(['get','post'], '/select2/select2_company', $controller.'@operate_select2_company');



    /*
     * 客户管理
     */
    // 列表
    Route::match(['get','post'], '/user/client-list', $controller.'@view_user_client_list');
    // 修改列表
    Route::match(['get','post'], '/user/client-modify-record', $controller.'@view_user_client_modify_record');
    // 创建 & 修改
    Route::match(['get','post'], '/user/client-create', $controller.'@operate_user_client_create');
    Route::match(['get','post'], '/user/client-edit', $controller.'@operate_user_client_edit');
    // 登录
    Route::post('/user/client-login', $controller.'@operate_client_login');
    // 编辑-信息
    Route::post('/user/client-info-text-set', $controller.'@operate_client_info_text_set');
    Route::post('/user/client-info-time-set', $controller.'@operate_client_info_time_set');
    Route::post('/user/client-info-radio-set', $controller.'@operate_client_info_option_set');
    Route::post('/user/client-info-select-set', $controller.'@operate_client_info_option_set');
    Route::post('/user/client-info-select2-set', $controller.'@operate_client_info_option_set');
    // 【用户-员工管理】修改密码
    Route::match(['get','post'], '/user/client-password-admin-change', $controller.'@operate_user_client_password_admin_change');
    Route::match(['get','post'], '/user/client-password-admin-reset', $controller.'@operate_user_client_password_admin_reset');
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
     * 部门管理
     */
    // 列表
    Route::match(['get','post'], '/company/company-list', $controller.'@view_company_list');
    // 修改列表
    Route::match(['get','post'], '/company/company-modify-record', $controller.'@view_company_modify_record');
    // 创建 & 修改
    Route::match(['get','post'], '/company/company-create', $controller.'@operate_company_create');
    Route::match(['get','post'], '/company/company-edit', $controller.'@operate_company_edit');
    // 登录
    Route::post('/company/company-login', $controller.'@operate_company_login');
    // 编辑-信息
    Route::post('/company/company-info-text-set', $controller.'@operate_company_info_text_set');
    Route::post('/company/company-info-time-set', $controller.'@operate_company_info_time_set');
    Route::post('/company/company-info-radio-set', $controller.'@operate_company_info_option_set');
    Route::post('/company/company-info-select-set', $controller.'@operate_company_info_option_set');
    Route::post('/company/company-info-select2-set', $controller.'@operate_company_info_option_set');
    // 修改密码
    Route::match(['get','post'], '/company/company-password-admin-reset', $controller.'@operate_company_password_admin_reset');
    // 删除 & 恢复
    Route::post('/company/company-admin-delete', $controller.'@operate_company_admin_delete');
    Route::post('/company/company-admin-restore', $controller.'@operate_company_admin_restore');
    Route::post('/company/company-admin-delete-permanently', $controller.'@operate_company_admin_delete_permanently');
    // 启用 & 禁用
    Route::post('/company/company-admin-enable', $controller.'@operate_company_admin_enable');
    Route::post('/company/company-admin-disable', $controller.'@operate_company_admin_disable');









    /*
     * 部门管理
     */
    // select2
    Route::match(['get','post'], '/department/department_select2_leader', $controller.'@operate_department_select2_leader');
    Route::match(['get','post'], '/department/department_select2_superior_department', $controller.'@operate_department_select2_superior_department');
    // 列表
    Route::match(['get','post'], '/department/department-list', $controller.'@view_department_list');
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
    Route::match(['get','post'], '/user/user_select2_sales', $controller.'@operate_user_select2_sales');
    Route::match(['get','post'], '/user/user_select2_superior', $controller.'@operate_user_select2_superior');
    Route::match(['get','post'], '/user/user_select2_department', $controller.'@operate_user_select2_department');

    // 列表
    Route::match(['get','post'], '/user/staff-list', $controller.'@view_user_staff_list');
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




















    /*
     * 订单管理
     */
    // select2
    Route::match(['get','post'], '/item/item_select2_user', $controller.'@operate_item_select2_user');
    Route::match(['get','post'], '/item/item_select2_client', $controller.'@operate_item_select2_client');
    Route::match(['get','post'], '/item/item_select2_project', $controller.'@operate_item_select2_project');
    Route::match(['get','post'], '/item/item_select2_team', $controller.'@operate_item_select2_team');

    Route::match(['get','post'], '/item/order_select2_project', $controller.'@operate_order_select2_project');
    Route::match(['get','post'], '/item/order_select2_client', $controller.'@operate_order_select2_client');

    // 创建 & 修改
    Route::match(['get','post'], '/item/order-create', $controller.'@operate_item_order_create');
    Route::match(['get','post'], '/item/order-edit', $controller.'@operate_item_order_edit');
    // 导入
    Route::match(['get','post'], '/item/order-import', $controller.'@operate_item_order_import');
    Route::match(['get','post'], '/item/order-import-for-admin', $controller.'@operate_item_order_import_for_admin');
    Route::match(['get','post'], '/item/order-import-for-admin-by-txt', $controller.'@operate_item_order_import_for_admin_by_txt');

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
    //
    Route::post('/item/order-download-recording', $controller.'@operate_item_order_download_recording');

    // 列表
    Route::match(['get','post'], '/item/order-list', $controller.'@view_item_order_list');

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




    // 概览
    Route::match(['get','post'], '/statistic/statistic-company-overview', $controller.'@view_statistic_company_overview');
    Route::post('/statistic/statistic-get-data-for-company-overview', $controller.'@get_statistic_data_for_company_overview');
    // 日报
    Route::match(['get','post'], '/statistic/statistic-company-daily', $controller.'@view_statistic_company_daily');
    Route::post('/statistic/statistic-get-data-for-company-daily', $controller.'@get_statistic_data_for_company_daily');
    // 旗下项目
    Route::match(['get','post'], '/statistic/statistic-company-project', $controller.'@view_statistic_company_project');
    Route::post('/statistic/statistic-get-data-for-company-project', $controller.'@get_statistic_data_for_company_project');




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
    Route::get('/download/call-recording-download', $controller.'@operate_download_call_recording_download');


});

