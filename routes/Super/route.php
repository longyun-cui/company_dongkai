<?php


/*
 * 超级管理员-后台管理
 */
Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {


    /*
     * 登录
     */
    Route::group([], function () {

        $controller = "SuperAuthController";

        Route::match(['get','post'], 'login', $controller.'@login');
        Route::match(['get','post'], 'logout', $controller.'@logout');

    });


    /*
     * 后台管理，需要登录
     */
    Route::group(['middleware' => 'super'], function () {


        Route::group(['prefix' => 'sql'], function () {

            $controller = "SuperSqlController";

            Route::get('/init', $controller.'@sql_init');
            Route::get('/insert', $controller.'@sql_insert');

        });




        $controller = "SuperAdminController";


        Route::get('/', $controller.'@index');
        Route::get('index', $controller.'@index');


        /*
         * info
         */
        Route::match(['get','post'], '/info/', $controller.'@view_info_index');
        Route::match(['get','post'], '/info/index', $controller.'@view_info_index');
        Route::match(['get','post'], '/info/edit', $controller.'@operate_info_edit');
        Route::match(['get','post'], '/info/password-reset', $controller.'@operate_info_password_reset');




        /*
         * user
         */
        Route::match(['get','post'], '/user/user_select2_district', $controller.'@operate_user_select2_district');

        Route::match(['get','post'], '/user/user-create', $controller.'@operate_user_user_create');
        Route::match(['get','post'], '/user/user-edit', $controller.'@operate_user_user_edit');

        Route::match(['get','post'], '/user/user-list-for-all', $controller.'@view_user_list_for_all');
        Route::match(['get','post'], '/user/user-list-for-individual', $controller.'@view_user_list_for_individual');
        Route::match(['get','post'], '/user/user-list-for-doc', $controller.'@view_user_list_for_doc');
        Route::match(['get','post'], '/user/user-list-for-org', $controller.'@view_user_list_for_org');
        Route::match(['get','post'], '/user/user-list-for-sponsor', $controller.'@view_user_list_for_sponsor');




        /*
         * item
         */
        Route::match(['get','post'], '/item/item-list-for-all', $controller.'@view_item_list_for_all');
        Route::match(['get','post'], '/item/item-list-for-atom', $controller.'@view_item_list_for_atom');
        Route::match(['get','post'], '/item/item-list-for-doc', $controller.'@view_item_list_for_doc');




        /*
         * district
         */
        Route::match(['get','post'], '/district/district_select2_parent', $controller.'@operate_district_select2_parent');

        Route::match(['get','post'], '/district/district-create', $controller.'@operate_district_create');
        Route::match(['get','post'], '/district/district-edit', $controller.'@operate_district_edit');

        Route::match(['get','post'], '/district/district-list-for-all', $controller.'@view_district_list_for_all');




        /*
         * statistic
         */
        Route::match(['get','post'], '/statistic', $controller.'@view_statistic_index');
        Route::match(['get','post'], '/statistic/index', $controller.'@view_statistic_index');
        Route::match(['get','post'], '/statistic/statistic-index', $controller.'@view_statistic_index');
        Route::match(['get','post'], '/statistic/statistic-user', $controller.'@view_statistic_user');
        Route::match(['get','post'], '/statistic/statistic-item', $controller.'@view_statistic_item');
        Route::match(['get','post'], '/statistic/statistic-all-list', $controller.'@view_statistic_all_list');






        Route::match(['get','post'], '/user/user-login', $controller.'@operate_user_user_login');


    });


});




/*
 * 超级管理员
 */
Route::group(['namespace' => 'Front'], function () {


    $controller = "SuperIndexController";

    Route::get('/', $controller.'@index');

    Route::get('item/{id?}', $controller.'@view_item');
    Route::get('org-item/{id?}', $controller.'@view_item');


});

