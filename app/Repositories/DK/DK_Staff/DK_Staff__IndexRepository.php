<?php
namespace App\Repositories\DK\DK_Staff;

use App\Models\DK\DK_Company;
use App\Models\DK\DK_Department;
use App\Models\DK\DK_District;
use App\Models\DK\DK_Pivot_Client_Delivery;
use App\Models\DK\DK_User;
use App\Models\DK\DK_UserExt;
use App\Models\DK\DK_Project;
use App\Models\DK\DK_Pivot_User_Project;
use App\Models\DK\DK_Pivot_Team_Project;
use App\Models\DK\DK_Order;
use App\Models\DK\DK_Record;
use App\Models\DK\DK_Record_Visit;

use App\Models\DK\DK_Statistic_Project_daily;
use App\Models\DK\DK_Statistic_Client_daily;
use App\Models\DK\DK_Statistic_Record;

use App\Models\DK\DK_Client;
use App\Models\DK\DK_Client_Funds_Recharge;
use App\Models\DK\DK_Client_Funds_Using;

use App\Models\DK_Client\DK_Client_User;
use App\Models\DK_Client\DK_Client_Finance_Daily;

use App\Models\DK\YH_Attachment;
use App\Models\DK\YH_Item;

use App\Models\DK_CC\DK_CC_Call_Record;
use App\Models\DK_CC\DK_CC_Call_Record_Current;
use App\Models\DK_CC\DK_CC_Call_Statistic;

use App\Models\DK_VOS\DK_VOS_CDR;

use App\Models\DK_A\DK_A_Order;

use App\Models\DK\DK_API_BY_Received;


use App\Jobs\DK_Client\AutomaticDispatchingJob;
use App\Jobs\DK\BYApReceivedJob;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;

class DK_Staff__IndexRepository {

    private $env;
    private $auth_check;
    private $me;
    private $me_admin;
    private $view_blade_403;
    private $view_blade_404;

    public function __construct()
    {

        $this->view_blade_403 = env('TEMPLATE_DK_ADMIN').'entrance.errors.403';
        $this->view_blade_404 = env('TEMPLATE_DK_ADMIN').'entrance.errors.404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');
    }


    // 登录情况
    public function get_me()
    {
        if(Auth::guard("dk_staff_user")->check())
        {
            $admin = Auth::guard("dk_staff_user")->user();

//            $department_district_id = $admin->department_district_id;
//            $department_group_id = $admin->department_group_id;
//
//            if($department_district_id > 0)
//            {
//                $department_district = DK_Department::find($department_district_id);
//                if($department_district)
//                {
//                    if($department_district->item_status != 1)
//                    {
//                        return response_error([],'员工所属团队已禁用！');
//                    }
//                }
//                else return response_error([],'员工所属团队不存在！');
//            }
//
//            if($department_group_id > 0)
//            {
//                $department_group = DK_Department::find($department_group_id);
//                if($department_group)
//                {
//                    if($department_group->item_status != 1)
//                    {
//                        return response_error([],'员工所属小组已禁用！');
//                    }
//                }
//                else return response_error([],'员工所属小组不存在！');
//            }

            $this->auth_check = 1;
            $this->me = Auth::guard("dk_staff_user")->user();

            view()->share('me',$this->me);
        }
        else $this->auth_check = 0;

        view()->share('auth_check',$this->auth_check);

        if(isMobileEquipment()) $is_mobile_equipment = 1;
        else $is_mobile_equipment = 0;
        view()->share('is_mobile_equipment',$is_mobile_equipment);
    }




    // 返回主页视图
    public function view_staff_index()
    {
        $this->get_me();
        $me = $this->me;

//        if($me->id > 10000)
//        {
//            $record["creator_id"] = $me->id;
//            $record["record_category"] = 1; // record_category=1 browse/share
//            $record["record_type"] = 1; // record_type=1 browse
//            $record["page_type"] = 1; // page_type=1 default platform
//            $record["page_module"] = 1; // page_module=1 index
//            $record["page_num"] = 0;
//            $record["open"] = "root";
//            $record["from"] = request('from',NULL);
//            $this->record_for_user_visit($record);
//        }

//        $condition = request()->all();
//        $return['condition'] = $condition;
//
//        $condition['task-list-type'] = 'unfinished';
//        $parameter_result = http_build_query($condition);
//        return redirect('/?'.$parameter_result);


        $this_month = date('Y-m');
        $this_month_start_date = date('Y-m-01'); // 本月开始日期
        $this_month_ended_date = date('Y-m-t'); // 本月结束日期
        $this_month_start_datetime = date('Y-m-01 00:00:00'); // 本月开始时间
        $this_month_ended_datetime = date('Y-m-t 23:59:59'); // 本月结束时间
        $this_month_start_timestamp = strtotime($this_month_start_date); // 本月开始时间戳
        $this_month_ended_timestamp = strtotime($this_month_ended_datetime); // 本月结束时间戳

        $last_month_start_date = date('Y-m-01',strtotime('last month')); // 上月开始时间
        $last_month_ended_date = date('Y-m-t',strtotime('last month')); // 上月开始时间
        $last_month_start_datetime = date('Y-m-01 00:00:00',strtotime('last month')); // 上月开始时间
        $last_month_ended_datetime = date('Y-m-t 23:59:59',strtotime('last month')); // 上月结束时间
        $last_month_start_timestamp = strtotime($last_month_start_date); // 上月开始时间戳
        $last_month_ended_timestamp = strtotime($last_month_ended_datetime); // 上月月结束时间戳



        $view_blade = env('DK_STAFF__TEMPLATE').'index';
        return view($view_blade);
    }




}