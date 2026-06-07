<?php
namespace App\Repositories\DK\DK_API;

use App\Models\DK\DK_Common\DK_Common__Company;
use App\Models\DK\DK_Common\DK_Common__Department;
use App\Models\DK\DK_Common\DK_Common__Team;
use App\Models\DK\DK_Common\DK_Common__Staff;
use App\Models\DK\DK_Common\DK_Common__Mac_Address;
use App\Models\DK\DK_Common\DK_Common__Record__by_Operation;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;


class DK_API__StaffClientAppRepository {

    private $env;
    private $auth_check;
    private $me;
    private $me_admin;
    private $modelUser;
    private $modelOrder;
    private $view_blade_403;
    private $view_blade_404;


    public function __construct()
    {
    }


    public function staff_client_app__verify_mac_address($post_data)
    {
        $return = [];
        $return['allowed'] = false;
        $return['message'] = '';

        $mac_address = !empty($post_data['mac']) ? $post_data['mac'] : null;
        if(!$mac_address)
        {
            $return['message'] = 'MAC地址为空！';
        }

        $mac = DK_Common__Mac_Address::where('mac_address',$mac_address)->first();
        if($mac)
        {
            if($mac->item_status == 1)
            {
                $return['allowed'] = true;
                $return['customerName'] = $mac->api_customerName;
                $return['userName'] = $mac->api_userName;
                $return['password'] = $mac->api_password;
            }
            else
            {
                $return['message'] = '该MAC地址已被禁用！';
            }
        }
        else
        {
            $return['message'] = 'MAC地址未注册，请先注册！';
        }

        return json_encode($return);

    }



}