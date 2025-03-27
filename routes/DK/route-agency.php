<?php


Route::get('/me', function () {
    dd('route-agency');
});
//Route::get('{any}', function () {
//    dd('route-agency-any');
//});


$controller = "DKAgencyController";

Route::match(['get','post'], 'login', $controller.'@login');
Route::match(['get','post'], 'logout', $controller.'@logout');
Route::match(['get','post'], 'logout_without_token', $controller.'@logout_without_token');

Route::match(['get','post'], '/cc_api/okcc/receiving-result', $controller.'@operate_api_OKCC_receiving_result');

/*
 * 超级管理员系统（后台）
 * 需要登录
 */
Route::group(['middleware' => []], function () {

    $controller = 'DKAgencyController';

    Route::post('/is_only_me', $controller.'@check_is_only_me');

    Route::get('/404', $controller.'@view_admin_404');

    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');
});

Route::group(['middleware' => []], function () {

    $controller = 'DKAgencyController';


//    Route::post('/is_only_me', $controller.'@check_is_only_me');
    Route::get('/', $controller.'@view_admin_index');
//    Route::get('/404', $controller.'@view_admin_404');


    /*
     * 个人信息管理
     */
    Route::get('/my-account/my-profile-info-index/', $controller.'@view_my_profile_info_index');
    Route::match(['get','post'], '/my-account/my-profile-info-edit', $controller.'@operate_my_profile_info_edit');
//    Route::match(['get','post'], '/my-account/my-password-change', $controller.'@operate_my_account_password_change');




    Route::post('/delivery/delivery-list', $controller.'@get_datatable_delivery_list');
    Route::post('/delivery/delivery-daily', $controller.'@get_datatable_delivery_daily');
    Route::post('/delivery/delivery-project', $controller.'@get_datatable_delivery_project');

//    Route::get('/delivery/delivery-export-by-ids', $controller.'@operate_delivery_export_by_ids');



















    $reconciliationController = 'DKAgencyReconciliationController';

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

