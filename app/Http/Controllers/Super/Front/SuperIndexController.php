<?php

namespace App\Http\Controllers\Super\Front;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SuperIndexController extends Controller
{
    //
    private $service;
    private $repo;
    public function __construct()
    {
    }

    // 导航
    public function index()
    {
        $return['auth_check'] = 0;
        $view_blade = env('TEMPLATE_SUPER_FRONT').'entrance.index';
        return view($view_blade)->with($return);
    }

    // 导航
    public function navigation()
    {
        return view('GPS.entrance.navigation');
    }

    // 测试
    public function test_list()
    {
        return view('GPS.entrance.test-list');
    }

    // 工具
    public function tool_list()
    {
        return view('GPS.entrance.tool-list');
    }

    // 模板
    public function template_list()
    {
        return view('GPS.entrance.template-list');
    }




    //
    public function tool()
    {
        $type = request()->get("type");
        if($type == "type")
        {
            return response_success([],"type");
        }
        // 生成密码
        else if($type == "password_encode")
        {
            $password = request("password");
            $password_encode = password_encode($password);
            return response_success(['password_encode'=>$password_encode]);
        }
        else if($type == "xx")
        {
            return response_success([]);
        }
    }



}
