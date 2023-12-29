<?php


$controller = "ZYIndexController";

//Route::get('/', $controller."@view_root");
Route::get('/', function () {
    $view_blade = env('TEMPLATE_YH_STAFF').'entrance.root';
    return view($view_blade);
});

/*
 * 样式开发
 */






