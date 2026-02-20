<?php
namespace App\Repositories\DK\DK_Staff;

use App\Models\DK\DK_Common\DK_Common__Company;
use App\Models\DK\DK_Common\DK_Common__Department;
use App\Models\DK\DK_Common\DK_Common__Team;
use App\Models\DK\DK_Common\DK_Common__Staff;

use App\Models\DK\DK_Common\DK_Common__Location;

use App\Models\DK\DK_Common\DK_Common__Client;
use App\Models\DK\DK_Common\DK_Common__Project;
use App\Models\DK\DK_Common\DK_Common__Order;

use App\Models\DK\DK_Common\DK_Pivot__Department_Project;
use App\Models\DK\DK_Common\DK_Pivot__Staff_Project;
use App\Models\DK\DK_Common\DK_Pivot__Team_Project;

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
        $this->view_blade_403 = env('DK_STAFF__TEMPLATE').'403';
        $this->view_blade_404 = env('DK_STAFF__TEMPLATE').'404';

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




    // 返回【主页】视图
    public function view__staff__index()
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

//        $project_list

        $project_query = DK_Common__Project::select('id','name')->where('item_status',1);
        // 客服部
        if($me->staff_category == 41)
        {
            if($me->staff_position == 31)
            {
                // 部门总监
                $project_ids = DK_Pivot__Department_Project::select('project_id')->where('department_id',$me->department_id)->get()->pluck('project_id')->toArray();
//                $project_list = DK_Common__Project::select('id','name')->where('item_status',1)->whereIn('id',$project_ids)->get();
                $project_query->whereIn('id',$project_ids)->get();
            }
            if(in_array($me->staff_position,[41,51,61,71,99]))
            {
                // 团队成员
                $project_ids = DK_Pivot__Team_Project::select('project_id')->where('team_id',$me->team_id)->get()->pluck('project_id')->toArray();
                $project_query->whereIn('id',$project_ids)->get();
//                $project_list = DK_Common__Project::select('id','name')->where('item_status',1)->whereIn('id',$project_ids)->get();
//                $view_data['project_list'] = $project_list;
            }
        }

        // 质检部 & 复核部
        if(in_array($me->staff_category,[51,61]))
        {
            if($me->staff_position == 31)
            {
                // 部门总监
                $project_ids = DK_Pivot__Department_Project::select('project_id')->where('department_id',$me->department_id)->get()->pluck('project_id')->toArray();
                $project_query->whereIn('id',$project_ids)->get();
//                $project_list = DK_Common__Project::select('id','name')->where('item_status',1)->whereIn('id',$project_ids)->get();
//                $view_data['project_list'] = $project_list;
            }
            else if($me->staff_position == 41)
            {
                // 团队经理（多对对）
                $project_ids = DK_Pivot__Team_Project::select('project_id')->where('team_id',$me->team_id)->get()->pluck('project_id')->toArray();
                $project_query->whereIn('id',$project_ids)->get();
//                $project_list = DK_Common__Project::select('id','name')->where('item_status',1)->whereIn('id',$project_ids)->get();
//                $view_data['project_list'] = $project_list;
            }
            else if($me->staff_position == 61)
            {
                // 小组主管（多对对）
                $staff_ids = DK_Common__Staff::select('id')->where('team_group_id',$me->id)->get()->pluck('id')->toArray();
                $project_ids = DK_Pivot__Staff_Project::select('project_id')->whereIn('staff_id',$staff_ids)->get()->pluck('project_id')->toArray();
                $project_query->whereIn('id',$project_ids)->get();
//                $project_list = DK_Common__Project::select('id','name')->where('item_status',1)->whereIn('id',$project_ids)->get();
//                $view_data['project_list'] = $project_list;
            }
            else if($me->staff_position == 99)
            {
                // 职员（多对多）
                $project_ids = DK_Pivot__Staff_Project::select('project_id')->where('staff_id',$me->id)->get()->pluck('project_id')->toArray();
                $project_query->whereIn('id',$project_ids)->get();
//                $project_list = DK_Common__Project::select('id','name')->where('item_status',1)->whereIn('id',$project_ids)->get();
//                $view_data['project_list'] = $project_list;
            }
        }

        // 运营部
        if($me->staff_category == 71)
        {

            if($me->staff_position == 31)
            {
                // 部门总监
            }
            else if($me->staff_position == 41)
            {
                // 团队经理
            }
            else if($me->staff_position == 61)
            {
                // 小组主管
            }
            else if($me->staff_position == 99)
            {
                // 职员
            }
        }

        $project_list = $project_query->get();
        $view_data['project_list'] = $project_list;


        $location_city_list = DK_Common__Location::select('id','location_city')->whereIn('item_status',[1])->get();
        $view_data['location_city_list'] = $location_city_list;

        $view_blade = env('DK_STAFF__TEMPLATE').'index';
        return view($view_blade)->with($view_data);
    }



    // 返回【403】页面
    public function view__staff__403()
    {
        $this->get_me();
        return view($this->view_blade_403);
    }

    // 返回【404】页面
    public function view__staff__404()
    {
        $this->get_me();
        return view($this->view_blade_404);
    }


}