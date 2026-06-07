<?php


Route::get('/', function () {
    dd('dk.api');
});


$controller = "DKAPIController";


Route::match(['get','post'], '/staff-client-app/verify-mac-address', $controller.'@staff_client_app__verify_mac_address');

