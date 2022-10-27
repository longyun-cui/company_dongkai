<?php
namespace App\Repositories\Admin;

use App\Models\RootModule;
use App\Models\RootMenu;
use App\Models\RootItem;
use App\Models\RootTemplate;

use App\Repositories\Common\CommonRepository;
use App\Repositories\RootCommonRepository;

use Response, Auth, Validator, DB, Exception, Cache;
use QrCode;

class AdminRepository {

    private $model;
    public function __construct()
    {
//        $this->model = new Root;
    }

    // 返回（后台）主页视图
    public function view_admin_index()
    {
        return view('admin.index');
    }



}