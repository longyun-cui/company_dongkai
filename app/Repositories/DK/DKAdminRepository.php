<?php
namespace App\Repositories\DK;

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

use App\Models\DK\DK_Client;
use App\Models\DK\DK_Client_Funds_Recharge;
use App\Models\DK\DK_Client_Funds_Using;

use App\Models\DK_Client\DK_Client_User;
use App\Models\DK_Client\DK_Client_Finance_Daily;

use App\Models\DK\YH_Attachment;
use App\Models\DK\YH_Item;

use App\Models\DK_CC\DK_CC_Call_Record;
use App\Models\DK_CC\DK_CC_Call_Statistic;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;

class DKAdminRepository {

    private $env;
    private $auth_check;
    private $me;
    private $me_admin;
    private $modelUser;
    private $modelItem;
    private $view_blade_403;
    private $view_blade_404;

    public function __construct()
    {
        $this->modelUser = new DK_User;
        $this->modelItem = new YH_Item;

        $this->view_blade_403 = env('TEMPLATE_DK_ADMIN').'entrance.errors.403';
        $this->view_blade_404 = env('TEMPLATE_DK_ADMIN').'entrance.errors.404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');
    }


    // 登录情况
    public function get_me()
    {
        if(Auth::guard("yh_admin")->check())
        {
            $this->auth_check = 1;
            $this->me = Auth::guard("yh_admin")->user();
            view()->share('me',$this->me);
        }
        else $this->auth_check = 0;

        view()->share('auth_check',$this->auth_check);

        if(isMobileEquipment()) $is_mobile_equipment = 1;
        else $is_mobile_equipment = 0;
        view()->share('is_mobile_equipment',$is_mobile_equipment);
    }




    // 返回（后台）主页视图
    public function view_admin_index()
    {
        $this->get_me();
        $me = $this->me;

        if($me->id > 10000)
        {
            $record["creator_id"] = $me->id;
            $record["record_category"] = 1; // record_category=1 browse/share
            $record["record_type"] = 1; // record_type=1 browse
            $record["page_type"] = 1; // page_type=1 default platform
            $record["page_module"] = 1; // page_module=1 index
            $record["page_num"] = 0;
            $record["open"] = "root";
            $record["from"] = request('from',NULL);
            $this->record_for_user_visit($record);
        }

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





        // 工单统计
        // 员工统计
        $query_order = DK_Order::select(DB::raw("
                    count(*) as order_count_for_all,
                    count(IF(created_type = 9, TRUE, NULL)) as order_count_for_export,
                    count(IF(created_type = 1 AND is_published = 1, TRUE, NULL)) as order_count_for_published,
                    count(IF(created_type = 1 AND is_published != 1, TRUE, NULL)) as order_count_for_unpublished,
                    count(IF(created_type = 1 AND is_published = 1 AND is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(created_type = 1 AND is_published = 1 AND inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(created_type = 1 AND is_published = 1 AND inspected_result = '拒绝', TRUE, NULL)) as order_count_for_refused,
                    count(IF(created_type = 1 AND is_published = 1 AND inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(created_type = 1 AND is_published = 1 AND inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside,
                    
                    count(IF(created_type = 1 AND is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(created_type = 1 AND delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(created_type = 1 AND delivered_result = '待交付', TRUE, NULL)) as order_count_for_delivered_uncompleted,
                    count(IF(created_type = 1 AND delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(created_type = 1 AND delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(created_type = 1 AND delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(created_type = 1 AND delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "));



        // 本月每日工单量
        $query_this_month = DK_Order::select('id','published_at')
            ->whereBetween('published_at',[$this_month_start_timestamp,$this_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(published_at,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(published_at,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(published_at,'%e') as day,
                    count(*) as sum
                "));

        // 上月每日工单量
        $query_last_month = DK_Order::select('id','published_at')
            ->whereBetween('published_at',[$last_month_start_timestamp,$last_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(published_at,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(published_at,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(published_at,'%e') as day,
                    count(*) as sum
                "));


        // 团队经理
        if($me->user_type == 41)
        {

            $query_order->where('department_district_id',$me->department_district_id);
            $query_this_month->where('department_district_id',$me->department_district_id);
            $query_last_month->where('department_district_id',$me->department_district_id);
        }
        // 客服经理
        if($me->user_type == 81)
        {

            $query_order->where('department_manager_id',$me->id);
            $query_this_month->where('department_manager_id',$me->id);
            $query_last_month->where('department_manager_id',$me->id);
        }
        // 客服主管
        if($me->user_type == 84)
        {
            $query_order->where('department_supervisor_id', $me->id);
            $query_this_month->where('department_supervisor_id', $me->id);
            $query_last_month->where('department_supervisor_id', $me->id);
        }
        // 客服
        if($me->user_type == 88)
        {
            $query_order->where('creator_id', $me->id);
            $query_this_month->where('creator_id', $me->id);
            $query_last_month->where('creator_id', $me->id);
        }
        // 质检经理
        if($me->user_type == 71)
        {
            if($me->department_district_id)
            {
                $query_order->where('department_district_id',$me->department_district_id);
                $query_this_month->where('department_district_id',$me->department_district_id);
                $query_last_month->where('department_district_id',$me->department_district_id);
            }

        }
        // 质检员
        if($me->user_type == 77)
        {
            $query_order->where('inspector_id', $me->id);
            $query_this_month->where('inspector_id', $me->id);
            $query_last_month->where('inspector_id', $me->id);

        }


        $query_order = $query_order->get();

        $return['order_count'] = $query_order[0];


        $statistics_order_this_month_data = $query_this_month->get()->keyBy('day');
        $return['statistics_order_this_month_data'] = $statistics_order_this_month_data;

        $statistics_order_last_month_data = $query_last_month->get()->keyBy('day');
        $return['statistics_order_last_month_data'] = $statistics_order_last_month_data;



        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.index';
        return view($view_blade)->with($return);
    }
    public function view_admin_index1()
    {
        $this->get_me();
        $me = $this->me;


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





        // 工单统计
        $query_order_count_for_all = DK_Order::select('id');
        $query_order_count_for_export = DK_Order::where('created_type', 9);
        $query_order_count_for_unpublished = DK_Order::whereIn('created_type', [1,99])->where('is_published', 0);
        $query_order_count_for_published = DK_Order::whereIn('created_type', [1,99])->where('is_published', 1);
        $query_order_count_for_waiting_for_inspect = DK_Order::whereIn('created_type', [1,99])->where('is_published', 1)->where('inspected_status', 0);
        $query_order_count_for_inspected = DK_Order::whereIn('created_type', [1,99])->where('is_published', 1)->where('inspected_status', '<>', 0);
        $query_order_count_for_accepted = DK_Order::whereIn('created_type', [1,99])->where('is_published', 1)->where('inspected_result','通过');
        $query_order_count_for_refused = DK_Order::whereIn('created_type', [1,99])->where('is_published', 1)->where('inspected_result','拒绝');
        $query_order_count_for_accepted_inside = DK_Order::whereIn('created_type', [1,99])->where('is_published', 1)->where('inspected_result','内部通过');
        $query_order_count_for_repeat = DK_Order::whereIn('created_type', [1,99])->where('is_published', 1)->where('is_repeat','>',0);



        // 本月每日工单量
        $query_this_month = DK_Order::select('id','published_at')
            ->whereBetween('published_at',[$this_month_start_timestamp,$this_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(published_at,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(published_at,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(published_at,'%e') as day,
                    count(*) as sum
                "));

        // 上月每日工单量
        $query_last_month = DK_Order::select('id','published_at')
            ->whereBetween('published_at',[$last_month_start_timestamp,$last_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(published_at,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(published_at,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(published_at,'%e') as day,
                    count(*) as sum
                "));


        // 客服经理
        if($me->user_type == 41)
        {
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();

//            $query_order_count_for_all->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_unpublished->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_published->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_waiting_for_inspect->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_inspected->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_accepted->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_refused->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_accepted_inside->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_repeat->whereIn('creator_id',$sub_subordinates_array);

//            $query_this_month->whereIn('creator_id',$sub_subordinates_array);
//            $query_last_month->whereIn('creator_id',$sub_subordinates_array);


            $query_order_count_for_all->where('department_manager_id',$me->id);
            $query_order_count_for_export->where('department_manager_id',$me->id);
            $query_order_count_for_unpublished->where('department_manager_id',$me->id);
            $query_order_count_for_published->where('department_manager_id',$me->id);
            $query_order_count_for_waiting_for_inspect->where('department_manager_id',$me->id);
            $query_order_count_for_inspected->where('department_manager_id',$me->id);
            $query_order_count_for_accepted->where('department_manager_id',$me->id);
            $query_order_count_for_refused->where('department_manager_id',$me->id);
            $query_order_count_for_accepted_inside->where('department_manager_id',$me->id);
            $query_order_count_for_repeat->where('department_manager_id',$me->id);

            $query_this_month->where('department_manager_id',$me->id);
            $query_last_month->where('department_manager_id',$me->id);
        }
        // 客服经理
        if($me->user_type == 81)
        {
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();

//            $query_order_count_for_all->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_unpublished->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_published->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_waiting_for_inspect->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_inspected->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_accepted->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_refused->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_accepted_inside->whereIn('creator_id',$sub_subordinates_array);
//            $query_order_count_for_repeat->whereIn('creator_id',$sub_subordinates_array);

//            $query_this_month->whereIn('creator_id',$sub_subordinates_array);
//            $query_last_month->whereIn('creator_id',$sub_subordinates_array);


            $query_order_count_for_all->where('department_manager_id',$me->id);
            $query_order_count_for_export->where('department_manager_id',$me->id);
            $query_order_count_for_unpublished->where('department_manager_id',$me->id);
            $query_order_count_for_published->where('department_manager_id',$me->id);
            $query_order_count_for_waiting_for_inspect->where('department_manager_id',$me->id);
            $query_order_count_for_inspected->where('department_manager_id',$me->id);
            $query_order_count_for_accepted->where('department_manager_id',$me->id);
            $query_order_count_for_refused->where('department_manager_id',$me->id);
            $query_order_count_for_accepted_inside->where('department_manager_id',$me->id);
            $query_order_count_for_repeat->where('department_manager_id',$me->id);

            $query_this_month->where('department_manager_id',$me->id);
            $query_last_month->where('department_manager_id',$me->id);
        }
        // 客服主管
        if($me->user_type == 84)
        {
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//
//            $query_order_count_for_all->whereIn('creator_id',$subordinates_array);
//            $query_order_count_for_unpublished->whereIn('creator_id',$subordinates_array);
//            $query_order_count_for_published->whereIn('creator_id',$subordinates_array);
//            $query_order_count_for_waiting_for_inspect->whereIn('creator_id',$subordinates_array);
//            $query_order_count_for_inspected->whereIn('creator_id',$subordinates_array);
//            $query_order_count_for_accepted->whereIn('creator_id',$subordinates_array);
//            $query_order_count_for_refused->whereIn('creator_id',$subordinates_array);
//            $query_order_count_for_accepted_inside->whereIn('creator_id',$subordinates_array);
//            $query_order_count_for_repeat->whereIn('creator_id',$subordinates_array);
//
//            $query_this_month->whereIn('creator_id',$subordinates_array);
//            $query_last_month->whereIn('creator_id',$subordinates_array);


            $query_order_count_for_all->where('department_supervisor_id',$me->id);
            $query_order_count_for_export->where('department_supervisor_id',$me->id);
            $query_order_count_for_unpublished->where('department_supervisor_id', $me->id);
            $query_order_count_for_published->where('department_supervisor_id', $me->id);
            $query_order_count_for_waiting_for_inspect->where('department_supervisor_id', $me->id);
            $query_order_count_for_inspected->where('department_supervisor_id', $me->id);
            $query_order_count_for_accepted->where('department_supervisor_id', $me->id);
            $query_order_count_for_refused->where('department_supervisor_id', $me->id);
            $query_order_count_for_accepted_inside->where('department_supervisor_id', $me->id);
            $query_order_count_for_repeat->where('department_supervisor_id', $me->id);

            $query_this_month->where('department_supervisor_id', $me->id);
            $query_last_month->where('department_supervisor_id', $me->id);
        }
        // 客服
        if($me->user_type == 88)
        {
            $query_order_count_for_all->where('creator_id', $me->id);
            $query_order_count_for_export->where('creator_id', $me->id);
            $query_order_count_for_unpublished->where('creator_id', $me->id);
            $query_order_count_for_published->where('creator_id', $me->id);
            $query_order_count_for_waiting_for_inspect->where('creator_id', $me->id);
            $query_order_count_for_inspected->where('creator_id', $me->id);
            $query_order_count_for_accepted->where('creator_id', $me->id);
            $query_order_count_for_refused->where('creator_id', $me->id);
            $query_order_count_for_accepted_inside->where('creator_id', $me->id);
            $query_order_count_for_repeat->where('creator_id', $me->id);

            $query_this_month->where('creator_id', $me->id);
            $query_last_month->where('creator_id', $me->id);
        }
        // 质检经理
        if($me->user_type == 71)
        {
//            $subordinates = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $query->where('is_published','<>',0)->whereHas('project_er', function ($query) use ($subordinates) {
//                $query->whereIn('user_id', $subordinates);
//            });

            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
            $project_array = DK_Project::select('id')->whereIn('user_id',$subordinates_array)->get()->pluck('id')->toArray();

            $query_order_count_for_all->whereIn('project_id', $project_array);
            $query_order_count_for_unpublished->whereIn('project_id', $project_array);
            $query_order_count_for_published->whereIn('project_id', $project_array);
            $query_order_count_for_waiting_for_inspect->whereIn('project_id', $project_array);
            $query_order_count_for_inspected->whereIn('project_id', $project_array);
            $query_order_count_for_accepted->whereIn('project_id', $project_array);
            $query_order_count_for_refused->whereIn('project_id', $project_array);
            $query_order_count_for_accepted_inside->whereIn('project_id', $project_array);
            $query_order_count_for_repeat->whereIn('project_id', $project_array);

            $query_this_month->whereIn('project_id', $project_array);
            $query_last_month->whereIn('project_id', $project_array);

        }
        // 质检员
        if($me->user_type == 77)
        {
//            $query->where('is_published','<>',0)->whereHas('project_er', function ($query) use ($me) {
//                $query->where('user_id', $me->id);
//            });

            $project_array = DK_Project::select('id')->where('user_id',$me->id)->get()->pluck('id')->toArray();

            $query_order_count_for_all->whereIn('project_id', $project_array);
            $query_order_count_for_unpublished->whereIn('project_id', $project_array);
            $query_order_count_for_published->whereIn('project_id', $project_array);
            $query_order_count_for_waiting_for_inspect->whereIn('project_id', $project_array);
            $query_order_count_for_inspected->whereIn('project_id', $project_array);
            $query_order_count_for_accepted->whereIn('project_id', $project_array);
            $query_order_count_for_refused->whereIn('project_id', $project_array);
            $query_order_count_for_accepted_inside->whereIn('project_id', $project_array);
            $query_order_count_for_repeat->whereIn('project_id', $project_array);

            $query_this_month->whereIn('project_id', $project_array);
            $query_last_month->whereIn('project_id', $project_array);

        }



        $order_count_for_all = $query_order_count_for_all->count("*");
        $order_count_for_export = $query_order_count_for_export->count("*");
        $order_count_for_unpublished = $query_order_count_for_unpublished->count("*");
        $order_count_for_published = $query_order_count_for_published->count("*");
        $order_count_for_waiting_for_inspect = $query_order_count_for_waiting_for_inspect->count("*");
        $order_count_for_inspected = $query_order_count_for_inspected->count("*");
        $order_count_for_accepted = $query_order_count_for_accepted->count("*");
        $order_count_for_refused = $query_order_count_for_refused->count("*");
        $order_count_for_accepted_inside = $query_order_count_for_accepted_inside->count("*");
        $order_count_for_repeat = $query_order_count_for_repeat->count("*");


        $return['order_count_for_all'] = $order_count_for_all;
        $return['order_count_for_export'] = $order_count_for_export;
        $return['order_count_for_unpublished'] = $order_count_for_unpublished;
        $return['order_count_for_published'] = $order_count_for_published;
        $return['order_count_for_waiting_for_inspect'] = $order_count_for_waiting_for_inspect;
        $return['order_count_for_inspected'] = $order_count_for_inspected;
        $return['order_count_for_accepted'] = $order_count_for_accepted;
        $return['order_count_for_refused'] = $order_count_for_refused;
        $return['order_count_for_accepted_inside'] = $order_count_for_accepted_inside;
        $return['order_count_for_repeat'] = $order_count_for_repeat;




        $statistics_order_this_month_data = $query_this_month->get()->keyBy('day');
        $return['statistics_order_this_month_data'] = $statistics_order_this_month_data;

        $statistics_order_last_month_data = $query_last_month->get()->keyBy('day');
        $return['statistics_order_last_month_data'] = $statistics_order_last_month_data;



        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.index';
        return view($view_blade)->with($return);
    }


    // 返回（后台）主页视图
    public function view_admin_404()
    {
        $this->get_me();
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.errors.404';
        return view($view_blade);
    }




    /*
     * 用户基本信息
     */
    // 【基本信息】返回视图
    public function view_my_profile_info_index()
    {
        $this->get_me();
        $me = $this->me;

        $return['data'] = $me;

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.my-account.my-profile-info-index';
        return view($view_blade)->with($return);
    }
    // 【基本信息】返回-编辑-视图
    public function view_my_profile_info_edit()
    {
        $this->get_me();
        $me = $this->me;

        $return['data'] = $me;

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.my-account.my-profile-info-edit';
        return view($view_blade)->with($return);
    }
    // 【基本信息】保存数据
    public function operate_my_profile_info_save($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }

            $mine_data = $post_data;
            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            $bool = $me->fill($mine_data)->save();
            if($bool)
            {
                // 头像
                if(!empty($post_data["portrait_img"]))
                {
                    // 删除原文件
                    $mine_original_file = $me->portrait_img;
                    if(!empty($mine_original_file) && file_exists(storage_path("resource/" . $mine_original_file)))
                    {
                        unlink(storage_path("resource/" . $mine_original_file));
                    }

                    $result = upload_file_storage($post_data["attachment"]);
                    if($result["result"])
                    {
                        $me->portrait_img = $result["local"];
                        $me->save();
                    }
                    else throw new Exception("upload--portrait-img--file--fail");
                }

            }
            else throw new Exception("insert--item--fail");

            DB::commit();
            return response_success(['id'=>$me->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }

    // 【密码】返回修改视图
    public function view_my_account_password_change()
    {
        $this->get_me();
        $me = $this->me;

        $return['data'] = $me;

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.my-account.my-account-password-change';
        return view($view_blade)->with($return);
    }
    // 【密码】保存数据
    public function operate_my_account_password_change_save($post_data)
    {
        $messages = [
            'password_pre.required' => '请输入旧密码',
            'password_new.required' => '请输入新密码',
            'password_confirm.required' => '请输入确认密码',
        ];
        $v = Validator::make($post_data, [
            'password_pre' => 'required',
            'password_new' => 'required',
            'password_confirm' => 'required'
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $password_pre = request()->get('password_pre');
        $password_new = request()->get('password_new');
        $password_confirm = request()->get('password_confirm');

        if($password_new == $password_confirm)
        {
            $this->get_me();
            $me = $this->me;
            if(password_check($password_pre,$me->password))
            {
                $me->password = password_encode($password_new);
                $bool = $me->save();
                if($bool) return response_success([], '密码修改成功！');
                else return response_fail([], '密码修改失败！');
            }
            else
            {
                return response_fail([], '原密码有误！');
            }
        }
        else return response_error([],'两次密码输入不一致！');
    }







    /*
     * 客户管理
     */
    // 【客户】返回-列表-视图
    public function view_user_client_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61])) return view($this->view_blade_403);

        $return['menu_active_of_client_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.user.client-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【客户】返回-列表-数据
    public function get_user_client_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Client::select('*')
            ->with(['creator'])
            ->whereIn('user_category',[11])
            ->whereIn('user_type',[0,1,9,11,19,21,22,41,61]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【客户】【修改记录】返回-列表-视图
    public function view_user_client_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_client_modify_list'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.user.client-modify-list';
        return view($view_blade)->with($return);
    }
    // 【客户】【修改记录】返回-列表-数据
    public function get_user_client_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Record::select('*')
            ->with(['creator'])
            ->where(['record_object'=>21, 'operate_object'=>21,'item_id'=>$id]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【客户】返回-添加-视图
    public function view_user_client_create()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61])) return view($this->view_blade_403);

        $item_type = 'item';
        $item_type_text = '客户';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/client-list-for-all';

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.user.client-edit';
        return view($view_blade)->with([
            'operate'=>'create',
            'operate_id'=>0,
            'category'=>'item',
            'type'=>$item_type,
            'item_type_text'=>$item_type_text,
            'title_text'=>$title_text,
            'list_text'=>$list_text,
            'list_link'=>$list_link,
        ]);
    }
    // 【客户】返回-编辑-视图
    public function view_user_client_edit()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61])) return view($this->view_blade_403);

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.user.client-edit';

        $item_type = 'item';
        $item_type_text = '客户';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/client-list-for-all';

        if($id == 0)
        {
            return view($view_blade)->with([
                'operate'=>'create',
                'operate_id'=>0,
                'category'=>'item',
                'type'=>$item_type,
                'item_type_text'=>$item_type_text,
                'title_text'=>$title_text,
                'list_text'=>$list_text,
                'list_link'=>$list_link,
            ]);
        }
        else
        {
            $mine = DK_Client::with(['parent'])->find($id);
            if($mine)
            {
                if(!in_array($mine->user_category,[0,1,9,11,88])) return view(env('TEMPLATE_DK_ADMIN').'errors.404');
                $mine->custom = json_decode($mine->custom);
                $mine->custom2 = json_decode($mine->custom2);
                $mine->custom3 = json_decode($mine->custom3);

                return view($view_blade)->with([
                    'operate'=>'edit',
                    'operate_id'=>$id,
                    'data'=>$mine,
                    'category'=>'item',
                    'type'=>$item_type,
                    'item_type_text'=>$item_type_text,
                    'title_text'=>$title_text,
                    'list_text'=>$list_text,
                    'list_link'=>$list_link,
                ]);
            }
            else return view(env('TEMPLATE_DK_ADMIN').'errors.404');
        }
    }
    // 【客户】保存数据
    public function operate_user_client_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'username.required' => '请输入客户名称！',
//            'username.unique' => '该客户已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'username' => 'required',
//            'username' => 'required|unique:dk_client,username',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_username_exist = DK_Client::select('id')->where('username',$post_data["username"])->count();
            if($is_username_exist) return response_error([],"该客户名已存在，请勿重复添加！");

            $is_mobile_exist = DK_Client::select('id')->where('mobile',$post_data["client_admin_mobile"])->count();
            if($is_mobile_exist) return response_error([],"该电话已存在，请勿重复添加！");

            $is_mobile_exist = DK_Client_User::select('id')->where('mobile',$post_data["client_admin_mobile"])->count();
            if($is_mobile_exist) return response_error([],"该电话已存在，请勿重复添加！");

            $client = new DK_Client;
            $client_data["user_category"] = 11;
            $client_staff_data["user_type"] = 11;
            $client_data["active"] = 1;
            $client_data["creator_id"] = $me->id;
            $client_data["username"] = $post_data["username"];
            $client_data["mobile"] = $post_data["client_admin_mobile"];
            $client_data["client_admin_name"] = $post_data["client_admin_name"];
            $client_data["client_admin_mobile"] = $post_data["client_admin_mobile"];
            $client_data["is_ip"] = $post_data["is_ip"];
            $client_data["ip_whitelist"] = $post_data["ip_whitelist"];
            $client_data["password"] = password_encode("12345678");

            $client_staff = new DK_Client_User;
            $client_staff_data["user_category"] = 11;
            $client_staff_data["user_type"] = 11;
            $client_staff_data["active"] = 1;
            $client_staff_data["username"] = $post_data["client_admin_name"];
            $client_staff_data["mobile"] = $post_data["client_admin_mobile"];
            $client_staff_data["creator_id"] = 0;
            $client_staff_data["password"] = password_encode("12345678");
        }
        else if($operate == 'edit') // 编辑
        {
            // 该客户是否存在
            $client = DK_Client::find($operate_id);
            if(!$client) return response_error([],"该客户不存在，刷新页面重试！");

            $client_data["username"] = $post_data["username"];
            $client_data["mobile"] = $post_data["client_admin_mobile"];
            $client_data["client_admin_name"] = $post_data["client_admin_name"];
            $client_data["client_admin_mobile"] = $post_data["client_admin_mobile"];
            $client_data["is_ip"] = $post_data["is_ip"];
            $client_data["ip_whitelist"] = $post_data["ip_whitelist"];

            // 客户名是否存在
            $is_username_exist = DK_Client::select('id')->where('id','<>',$operate_id)->where('username',$post_data["username"])->count();
            if($is_username_exist) return response_error([],"该客户名已存在，不能修改成此客户名！");

            // 客户管理员是否存在
            $client_staff = DK_Client_User::where('id', $client->admin_id)->first();
            if($client_staff)
            {
                // 客户管理员存在

                // 判断电话是否重复
                $is_mobile_exist = DK_Client_User::select('id')->where('id','<>',$client->admin_id)->where('mobile',$post_data["client_admin_mobile"])->count();
                if($is_mobile_exist) return response_error([],"该电话已存在，不能修改成此电话！");

                $client_staff_data["username"] = $post_data["client_admin_name"];
                $client_staff_data["mobile"] = $post_data["client_admin_mobile"];
            }
            else
            {
                // 客户管理员不存在

                $client_staff = new DK_Client_User;
                $client_staff_data["user_category"] = 11;
                $client_staff_data["user_type"] = 11;
                $client_staff_data["active"] = 1;
                $client_staff_data["client_id"] = $client->id;
                $client_staff_data["username"] = $post_data["client_admin_name"];
                $client_staff_data["mobile"] = $post_data["client_admin_mobile"];
                $client_staff_data["creator_id"] = 0;
                $client_staff_data["password"] = password_encode("12345678");
            }

        }
        else return response_error([],"参数有误！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }


            $bool = $client->fill($client_data)->save();
            if($bool)
            {
                if($operate == 'create')
                {
                    $client_staff_data["client_id"] = $client->id;
                }

                $bool_1 = $client_staff->fill($client_staff_data)->save();
                if($bool_1)
                {
                    if($operate == 'create')
                    {
                        $client->client_admin_id = $client_staff->id;
                        $bool = $client->save();
                        if(!$bool) throw new Exception("update--client--fail");
                    }
                }
                else throw new Exception("insert--client-staff--fail");
            }
            else throw new Exception("insert--client--fail");

            DB::commit();
            return response_success(['id'=>$client->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【客户】【文本-信息】设置-文本-类型
    public function operate_client_info_text_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'user-client-info-text-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client::withTrashed()->find($id);
        if(!$item) return response_error([],"该【客户】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $item->timestamps = false;
            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 21;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $column_value;

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【客户】【时间-信息】修改-时间-类型
    public function operate_client_info_time_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'user-client-info-time-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client::withTrashed()->find($id);
        if(!$item) return response_error([],"该【客户】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];
        $time_type = $post_data["time_type"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->$column_key = strtotime($column_value);
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 21;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_type"] = $time_type;
                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = strtotime($column_value);

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【客户】【选项-信息】修改-radio-select-[option]-类型
    public function operate_client_info_option_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'user-client-info-option-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client::withTrashed()->find($id);
        if(!$item) return response_error([],"该【客户】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $item->timestamps = false;
            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 21;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $column_value;

                    if(in_array($column_key,['leader_id']))
                    {
                        $record_data["before_id"] = $before;
                        $record_data["after_id"] = $column_value;
                    }


                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【客户】【附件】添加
    public function operate_client_info_attachment_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'user-client-attachment-set') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");

        $item = DK_Client::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");
        if(!in_array($me->user_type,[0,1,11,19])) return response_error([],"你没有操作权限！");

//        $operate_type = $post_data["operate_type"];
//        $column_key = $post_data["column_key"];
//        $column_value = $post_data["column_value"];


        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            // 多图
            $multiple_images = [];
            if(!empty($post_data["multiple_images"][0]))
            {
                // 添加图片
                foreach ($post_data["multiple_images"] as $n => $f)
                {
                    if(!empty($f))
                    {
                        $result = upload_img_storage($f,'','dk/attachment','');
                        if($result["result"])
                        {
                            $attachment = new YH_Attachment;

                            $attachment_data["operate_object"] = 41;
                            $attachment_data['item_id'] = $item_id;
                            $attachment_data['attachment_name'] = $post_data["attachment_name"];
                            $attachment_data['attachment_src'] = $result["local"];
                            $bool = $attachment->fill($attachment_data)->save();
                            if($bool)
                            {
                                $record = new DK_Record;

                                $record_data["ip"] = Get_IP();
                                $record_data["record_object"] = 21;
                                $record_data["record_category"] = 11;
                                $record_data["record_type"] = 1;
                                $record_data["creator_id"] = $me->id;
                                $record_data["item_id"] = $item_id;
                                $record_data["operate_object"] = 21;
                                $record_data["operate_category"] = 71;
                                $record_data["operate_type"] = 1;

                                $record_data["column_name"] = 'attachment';
                                $record_data["after"] = $attachment_data['attachment_src'];

                                $bool_1 = $record->fill($record_data)->save();
                                if($bool_1)
                                {
                                }
                                else throw new Exception("insert--record--fail");
                            }
                            else throw new Exception("insert--attachment--fail");
                        }
                        else throw new Exception("upload--attachment--file--fail");
                    }
                }
            }


            // 单图
            if(!empty($post_data["attachment_file"]))
            {
                $attachment = new YH_Attachment;

//                $result = upload_storage($post_data["portrait"]);
//                $result = upload_storage($post_data["portrait"], null, null, 'assign');
                $result = upload_img_storage($post_data["attachment_file"],'','dk/attachment','');
                if($result["result"])
                {
                    $attachment_data["operate_object"] = 41;
                    $attachment_data['item_id'] = $item_id;
                    $attachment_data['attachment_name'] = $post_data["attachment_name"];
                    $attachment_data['attachment_src'] = $result["local"];
                    $bool = $attachment->fill($attachment_data)->save();
                    if($bool)
                    {
                        $record = new DK_Record;

                        $record_data["ip"] = Get_IP();
                        $record_data["record_object"] = 21;
                        $record_data["record_category"] = 11;
                        $record_data["record_type"] = 1;
                        $record_data["creator_id"] = $me->id;
                        $record_data["item_id"] = $item_id;
                        $record_data["operate_object"] = 41;
                        $record_data["operate_category"] = 71;
                        $record_data["operate_type"] = 1;

                        $record_data["column_name"] = 'attachment';
                        $record_data["after"] = $attachment_data['attachment_src'];

                        $bool_1 = $record->fill($record_data)->save();
                        if($bool_1)
                        {
                        }
                        else throw new Exception("insert--record--fail");
                    }
                    else throw new Exception("insert--attachment--fail");
                }
                else throw new Exception("upload--attachment--file--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【客户】【附件】删除
    public function operate_client_info_attachment_delete($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'user-client-attachment-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = YH_Attachment::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【附件】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->delete();  // 普通删除
            if($bool)
            {
                $record = new DK_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["item_id"] = $item->item_id;
                $record_data["operate_object"] = 21;
                $record_data["operate_category"] = 71;
                $record_data["operate_type"] = 91;

                $record_data["column_name"] = 'attachment';
                $record_data["before"] = $item->attachment_src;

                $bool_1 = $record->fill($record_data)->save();
                if($bool_1)
                {
                }
                else throw new Exception("insert--record--fail");
            }
            else throw new Exception("attachment--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【客户】【附件】获取
    public function operate_client_get_attachment_html($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client::with([
            'attachment_list' => function($query) { $query->where(['record_object'=>21, 'operate_object'=>41]); }
        ])->withTrashed()->find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.item-assign-html-for-attachment';
        $html = view($view_blade)->with(['item_list'=>$item->attachment_list])->__toString();

        return response_success(['html'=>$html],"");
    }


    // 【客户】管理员-修改密码
    public function operate_user_client_password_admin_change($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
            'user-password.required' => '请输入密码！',
            'user-password-confirm.required' => '请输入确认密码！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
            'user-password' => 'required',
            'user-password-confirm' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'client-password-admin-change') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Client::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该操作权限！");
//        if(in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该员工的操作权限！");
        if($user->id == $me->id) return response_error([],"你不能操作你自己！");
        if($user->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        $password = $post_data["user-password"];
        $confirm = $post_data["user-password-confirm"];
        if($password != $confirm) return response_error([],"两次密码不一致！");

//        if(!password_is_legal($password)) ;
        $pattern = '/^[a-zA-Z0-9]{1}[a-zA-Z0-9]{5,19}$/i';
        if(!preg_match($pattern,$password)) return response_error([],"密码格式不正确！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->password = password_encode($password);
            $bool = $user->save();
            if(!$bool) throw new Exception("update--user--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【客户】管理员-重置密码
    public function operate_user_client_password_admin_reset($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'client-password-admin-reset') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $client = DK_Client::withTrashed()->find($id);
        if(!$client) return response_error([],"该客户不存在，刷新页面重试！");

        $client_staff = DK_Client_User::withTrashed()->where('client_id',$client->id)->first();
        if(!$client_staff) return response_error([],"该客户不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该操作权限！");
//        if(in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该员工的操作权限！");
//        if($user->id == $me->id) return response_error([],"你不能操作你自己！");
//        if($user->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $client_staff->password = password_encode('12345678');
            $bool = $client_staff->save();
            if(!$bool) throw new Exception("update--client-staff--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【客户】管理员-启用
    public function operate_user_client_admin_enable($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'client-admin-enable') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Client::find($id);
        if(!$user) return response_error([],"该【客户】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,19,61])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->user_status = 1;
            $user->timestamps = false;
            $bool = $user->save();
            if(!$bool) throw new Exception("update--client--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【客户】管理员-禁用
    public function operate_user_client_admin_disable($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'client-admin-disable') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Client::find($id);
        if(!$user) return response_error([],"该【客户】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,19,61])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->user_status = 9;
            $user->timestamps = false;
            $bool = $user->save();
            if(!$bool) throw new Exception("update--client--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }





    // 【客户】【财务往来记录】返回-列表-视图
    public function view_user_client_finance_recharge_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = YH_User::select('id','username')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_order_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.order-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【客户】【财务往来记录】返回-列表-数据
    public function get_user_client_recharge_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Finance_Funds_Recharge::select('*')
            ->with(['creator','confirmer','company_er'])
            ->where(['company_id'=>$id]);

        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");


        if(!empty($post_data['type']))
        {
            if($post_data['type'] == "income")
            {
                $query->where('finance_type', 1);
            }
            else if($post_data['type'] == "refund")
            {
                $query->where('finance_type', 21);
            }
        }

        if(!empty($post_data['finance_type']))
        {
            if(in_array($post_data['finance_type'],[1,21]))
            {
                $query->where('finance_type', $post_data['finance_type']);
            }
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【客户】添加-财务数据-保存数据（充值）
    public function operate_user_client_finance_recharge_create($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'client_id.required' => 'client_id.required.',
            'transaction_date.required' => '请选择交易日期！',
            'transaction_title.required' => '请填写费用类型！',
            'transaction_type.required' => '请填写支付方式！',
            'transaction_amount.required' => '请填写金额！',
//            'transaction_account.required' => '请填写交易账号！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'client_id' => 'required',
            'transaction_date' => 'required',
            'transaction_title' => 'required',
            'transaction_type' => 'required',
            'transaction_amount' => 'required',
//            'transaction_account' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;

        // 权限
        if(!in_array($me->user_type,[0,1,11])) return response_error([],"你没有操作权限！");


//        $operate = $post_data["operate"];
//        $operate_id = $post_data["operate_id"];

        $transaction_date = $post_data['transaction_date'];
        $transaction_date_timestamp = strtotime($post_data['transaction_date']);
        if($transaction_date_timestamp > time('Y-m-d')) return response_error([],"指定日期不能大于今天！");

        $client_id = $post_data["client_id"];
        $client = DK_Client::find($client_id);
        if(!$client) return response_error([],"该【客户】不存在，刷新页面重试！");

        // 交易类型 收入 || 支出
        $finance_type = $post_data["finance_type"];
        if(!in_array($finance_type,[1,91,101])) return response_error([],"交易类型错误！");

        $transaction_amount = $post_data["transaction_amount"];
        if(!is_numeric($transaction_amount)) return response_error([],"交易金额必须为数字！");
//        if($transaction_amount <= 0) return response_error([],"交易金额必须大于零！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $FinanceRecord = new DK_Client_Funds_Recharge;

//            if(in_array($me->user_type,[11,19,41,42]))
//            {
//                $FinanceRecord_data['is_confirmed'] = 1;
//            }

            $FinanceRecord_data['creator_id'] = $me->id;
            $FinanceRecord_data['finance_category'] = 11;
            $FinanceRecord_data['finance_type'] = $finance_type;
            $FinanceRecord_data['client_id'] = $post_data["client_id"];
            $FinanceRecord_data['title'] = $post_data["transaction_title"];
            $FinanceRecord_data['transaction_date'] = $transaction_date;
            $FinanceRecord_data['transaction_time'] = $transaction_date_timestamp;
            $FinanceRecord_data['transaction_type'] = $post_data["transaction_type"];
            $FinanceRecord_data['transaction_amount'] = $post_data["transaction_amount"];
//            $FinanceRecord_data['transaction_account'] = $post_data["transaction_account"];
            $FinanceRecord_data['transaction_receipt_account'] = $post_data["transaction_receipt_account"];
            $FinanceRecord_data['transaction_payment_account'] = $post_data["transaction_payment_account"];
            $FinanceRecord_data['transaction_order'] = $post_data["transaction_order"];
            $FinanceRecord_data['description'] = $post_data["transaction_description"];

            $mine_data = $post_data;

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['operate_category']);
            unset($mine_data['operate_type']);

            $bool = $FinanceRecord->fill($FinanceRecord_data)->save();
            if($bool)
            {
                $client = DK_Client::lockForUpdate()->find($client_id);

//                if(in_array($me->user_type,[11,19,41,42]))
                if(in_array($me->user_type,[-1]))
                {
                    if($finance_type == 1)
                    {
                        $client->funds_recharge_total = $client->funds_recharge_total + $transaction_amount;
//                        $client->funds_balance = $client->funds_balance + $transaction_amount;
                    }
                    else if($finance_type == 101)
                    {
                        $client->funds_recharge_total = $client->funds_recharge_total - $transaction_amount;
//                        $client->funds_balance = $company->funds_balance - $transaction_amount;
                    }
                }
                else
                {
                    if($finance_type == 1)
                    {
                        $client->funds_recharge_total = $client->funds_recharge_total + $transaction_amount;
//                        $client->funds_balance = $client->funds_balance + $transaction_amount;
                    }
                    else if($finance_type == 101)
                    {
                        $client->funds_recharge_total = $client->funds_recharge_total - $transaction_amount;
//                        $client->funds_balance = $client->funds_balance - $transaction_amount;
                    }
                }

                $bool_1 = $client->save();
                if($bool_1)
                {
                }
                else throw new Exception("update--company--fail");
            }
            else throw new Exception("insert--finance--fail");

            DB::commit();
            return response_success(['id'=>$FinanceRecord->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【客户】【结算】返回-列表-视图
    public function view_company_funds_using_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = YH_User::select('id','username')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_order_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.order-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【客户】【结算】返回-列表-数据
    public function get_company_funds_using_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $project_list = DK_Finance_Project::select('id')->where('channel_id',$id)->get()->toArray();
        $query = DK_Finance_Funds_Using::select('*')
            ->with(['creator','confirmer','project_er'])
            ->whereIn('project_id',$project_list);

        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");


        if(!empty($post_data['type']))
        {
            if($post_data['type'] == "income")
            {
                $query->where('finance_type', 1);
            }
            else if($post_data['type'] == "refund")
            {
                $query->where('finance_type', 21);
            }
        }

        if(!empty($post_data['finance_type']))
        {
            if(in_array($post_data['finance_type'],[1,21]))
            {
                $query->where('finance_type', $post_data['finance_type']);
            }
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }








    /*
     * 部门管理
     */
    // select2
    public function operate_department_select2_leader($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(empty($post_data['keyword']))
        {
            $query =DK_User::select(['id','username as text'])
                ->where(['user_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_User::select(['id','username as text'])->where('username','like',"%$keyword%")
                ->where(['user_status'=>1]);
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'manager') $query->where(['user_type'=>81]);
            else if($type == 'supervisor') $query->where(['user_type'=>84]);
            else $query->where(['user_type'=>81]);
        }
        else $query->where(['user_type'=>81]);

        if($me->user_type == 81)
        {
            $query->where('department_district_id',$me->department_district_id);
        }

        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }
    //
    public function operate_department_select2_superior_department($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(empty($post_data['keyword']))
        {
            $query =DK_Department::select(['id','name as text'])
                ->where(['item_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Department::select(['id','name as text'])->where('name','like',"%$keyword%")
                ->where(['item_status'=>1]);
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'district') $query->where(['department_type'=>11]);
            else if($type == 'group') $query->where(['department_type'=>21]);
            else $query->where(['department_type'=>11]);
        }
        else $query->where(['department_type'=>11]);

        if($me->user_type == 81)
        {
            $query->where('id',$me->department_district_id);
        }

        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }


    // 【部门】返回-列表-视图
    public function view_department_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,41])) return view($this->view_blade_403);

        $return['menu_active_of_department_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.department.department-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【部门】返回-列表-数据
    public function get_department_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query = DK_Department::select(['id','item_status','name','department_type','leader_id','superior_department_id','remark','creator_id','created_at','updated_at','deleted_at'])
            ->withTrashed()
            ->with([
                'creator'=>function($query) { $query->select(['id','username','true_name']); },
                'leader'=>function($query) { $query->select(['id','username','true_name']); },
                'superior_department_er'=>function($query) { $query->select(['id','name']); }
            ]);

        if(in_array($me->user_type,[41,81]))
        {
            $query->where('superior_department_id',$me->department_district_id);
        }

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");

        // 部门类型 [大区|组]
        if(!empty($post_data['department_type']))
        {
            if(!in_array($post_data['department_type'],[-1,0]))
            {
                $query->where('department_type', $post_data['department_type']);
            }
        }

        // 状态 [|]
        if(!empty($post_data['item_status']))
        {
            if(!in_array($post_data['item_status'],[-1,0]))
            {
                $query->where('item_status', $post_data['item_status']);
            }
        }
        else
        {
            $query->where('item_status', 1);
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("rank", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());

        foreach($list as $k => $v)
        {
            if($v->department_type == 11)
            {
                $v->district_id = $v->id;
            }
            else if($v->department_type == 21)
            {
                $v->district_id = $v->superior_department_id;
            }

            $v->district_group_id = $v->district_id.'.'.$v->id;
        }
//        $list = $list->sortBy(['rank'=>'asc'])->values();
//        $list = $list->sortBy(function ($item, $key) {
//            return $item['district_group_id'];
//        })->values();
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }


    // 【部门】【修改记录】返回-列表-视图
    public function view_department_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_car_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.department-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【部门】【修改记录】返回-列表-数据
    public function get_department_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Record::select('*')
            ->with(['creator'])
            ->where(['record_object'=>21, 'operate_object'=>31,'item_id'=>$id]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【部门】返回-添加-视图
    public function view_department_create()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,41])) return view($this->view_blade_403);

        $item_type = 'item';
        $item_type_text = '部门';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/department/department-list-for-all';

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.department.department-edit';
        return view($view_blade)->with([
            'operate'=>'create',
            'operate_id'=>0,
            'category'=>'item',
            'type'=>$item_type,
            'item_type_text'=>$item_type_text,
            'title_text'=>$title_text,
            'list_text'=>$list_text,
            'list_link'=>$list_link,
        ]);
    }
    // 【部门】返回-编辑-视图
    public function view_department_edit()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,41])) return view($this->view_blade_403);

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.department.department-edit';

        $item_type = 'item';
        $item_type_text = '部门';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/department/department-list-for-all';

        if($id == 0)
        {
            return view($view_blade)->with([
                'operate'=>'create',
                'operate_id'=>0,
                'category'=>'item',
                'type'=>$item_type,
                'item_type_text'=>$item_type_text,
                'title_text'=>$title_text,
                'list_text'=>$list_text,
                'list_link'=>$list_link,
            ]);
        }
        else
        {
            $mine = DK_Department::with('leader')->find($id);
            if($mine)
            {
//                if(!in_array($mine->user_category,[1,9,11,88])) return view(env('TEMPLATE_DK_ADMIN').'errors.404');
                $mine->custom = json_decode($mine->custom);
                $mine->custom2 = json_decode($mine->custom2);
                $mine->custom3 = json_decode($mine->custom3);

                return view($view_blade)->with([
                    'operate'=>'edit',
                    'operate_id'=>$id,
                    'data'=>$mine,
                    'category'=>'item',
                    'type'=>$item_type,
                    'item_type_text'=>$item_type_text,
                    'title_text'=>$title_text,
                    'list_text'=>$list_text,
                    'list_link'=>$list_link,
                ]);
            }
            else return view(env('TEMPLATE_DK_ADMIN').'errors.404');
        }
    }
    // 【部门】保存数据
    public function operate_department_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'name.required' => '请输入部门名称！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'name' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,41])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_exist = DK_Department::select('id')->where('name',$post_data["name"])->count();
            if($is_exist) return response_error([],"该【部门】已存在，请勿重复添加！");

            $mine = new DK_Department;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_Department::find($operate_id);
            if(!$mine) return response_error([],"该【部门】不存在，刷新页面重试！");
        }
        else return response_error([],"参数有误！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }

            $mine_data = $post_data;

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['category']);
            unset($mine_data['type']);

            if(in_array($me->user_type,[41,61,71,81]))
            {
                $mine_data['superior_department_id'] = $me->department_district_id;
            }


            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
            }
            else throw new Exception("insert--department--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【部门】【文本-信息】设置-文本-类型
    public function operate_department_info_text_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'department-info-text-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Department::withTrashed()->find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $item->timestamps = false;
            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 31;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $column_value;

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门】【时间-信息】修改-时间-类型
    public function operate_department_info_time_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'department-info-time-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Department::withTrashed()->find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];
        $time_type = $post_data["time_type"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->$column_key = strtotime($column_value);
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 31;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_type"] = $time_type;
                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = strtotime($column_value);

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门】【选项-信息】修改-radio-select-[option]-类型
    public function operate_department_info_option_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'department-info-option-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Department::withTrashed()->find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $item->timestamps = false;
            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 31;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $column_value;

                    if(in_array($column_key,['leader_id']))
                    {
                        $record_data["before_id"] = $before;
                        $record_data["after_id"] = $column_value;
                    }


                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门】【附件】添加
    public function operate_department_info_attachment_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'department-attachment-set') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");

        $item = DK_Department::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");
        if(!in_array($me->user_type,[0,1,11,19])) return response_error([],"你没有操作权限！");

//        $operate_type = $post_data["operate_type"];
//        $column_key = $post_data["column_key"];
//        $column_value = $post_data["column_value"];


        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            // 多图
            $multiple_images = [];
            if(!empty($post_data["multiple_images"][0]))
            {
                // 添加图片
                foreach ($post_data["multiple_images"] as $n => $f)
                {
                    if(!empty($f))
                    {
                        $result = upload_img_storage($f,'','dk/attachment','');
                        if($result["result"])
                        {
                            $attachment = new YH_Attachment;

                            $attachment_data["operate_object"] = 41;
                            $attachment_data['item_id'] = $item_id;
                            $attachment_data['attachment_name'] = $post_data["attachment_name"];
                            $attachment_data['attachment_src'] = $result["local"];
                            $bool = $attachment->fill($attachment_data)->save();
                            if($bool)
                            {
                                $record = new DK_Record;

                                $record_data["ip"] = Get_IP();
                                $record_data["record_object"] = 21;
                                $record_data["record_category"] = 11;
                                $record_data["record_type"] = 1;
                                $record_data["creator_id"] = $me->id;
                                $record_data["item_id"] = $item_id;
                                $record_data["operate_object"] = 31;
                                $record_data["operate_category"] = 71;
                                $record_data["operate_type"] = 1;

                                $record_data["column_name"] = 'attachment';
                                $record_data["after"] = $attachment_data['attachment_src'];

                                $bool_1 = $record->fill($record_data)->save();
                                if($bool_1)
                                {
                                }
                                else throw new Exception("insert--record--fail");
                            }
                            else throw new Exception("insert--attachment--fail");
                        }
                        else throw new Exception("upload--attachment--file--fail");
                    }
                }
            }


            // 单图
            if(!empty($post_data["attachment_file"]))
            {
                $attachment = new YH_Attachment;

//                $result = upload_storage($post_data["portrait"]);
//                $result = upload_storage($post_data["portrait"], null, null, 'assign');
                $result = upload_img_storage($post_data["attachment_file"],'','dk/attachment','');
                if($result["result"])
                {
                    $attachment_data["operate_object"] = 41;
                    $attachment_data['item_id'] = $item_id;
                    $attachment_data['attachment_name'] = $post_data["attachment_name"];
                    $attachment_data['attachment_src'] = $result["local"];
                    $bool = $attachment->fill($attachment_data)->save();
                    if($bool)
                    {
                        $record = new DK_Record;

                        $record_data["ip"] = Get_IP();
                        $record_data["record_object"] = 21;
                        $record_data["record_category"] = 11;
                        $record_data["record_type"] = 1;
                        $record_data["creator_id"] = $me->id;
                        $record_data["item_id"] = $item_id;
                        $record_data["operate_object"] = 41;
                        $record_data["operate_category"] = 71;
                        $record_data["operate_type"] = 1;

                        $record_data["column_name"] = 'attachment';
                        $record_data["after"] = $attachment_data['attachment_src'];

                        $bool_1 = $record->fill($record_data)->save();
                        if($bool_1)
                        {
                        }
                        else throw new Exception("insert--record--fail");
                    }
                    else throw new Exception("insert--attachment--fail");
                }
                else throw new Exception("upload--attachment--file--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门】【附件】删除
    public function operate_department_info_attachment_delete($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'department-attachment-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = YH_Attachment::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【附件】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->delete();  // 普通删除
            if($bool)
            {
                $record = new DK_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["item_id"] = $item->item_id;
                $record_data["operate_object"] = 31;
                $record_data["operate_category"] = 71;
                $record_data["operate_type"] = 91;

                $record_data["column_name"] = 'attachment';
                $record_data["before"] = $item->attachment_src;

                $bool_1 = $record->fill($record_data)->save();
                if($bool_1)
                {
                }
                else throw new Exception("insert--record--fail");
            }
            else throw new Exception("attachment--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门】【附件】获取
    public function operate_department_get_attachment_html($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Department::with([
            'attachment_list' => function($query) { $query->where(['record_object'=>21, 'operate_object'=>41]); }
        ])->withTrashed()->find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.item-assign-html-for-attachment';
        $html = view($view_blade)->with(['item_list'=>$item->attachment_list])->__toString();

        return response_success(['html'=>$html],"");
    }


    // 【部门】管理员-删除
    public function operate_department_admin_delete($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'department-admin-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = DK_Department::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->delete();  // 普通删除
            if(!$bool) throw new Exception("department--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门】管理员-恢复
    public function operate_department_admin_restore($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'operate.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'department-admin-restore') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_Department::withTrashed()->find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->restore();
            if(!$bool) throw new Exception("department--restore--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门】管理员-彻底删除
    public function operate_department_admin_delete_permanently($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'department-admin-delete-permanently') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_Project::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_copy = $item;

            $bool = $item->forceDelete();
            if(!$bool) throw new Exception("department--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门】管理员-启用
    public function operate_department_admin_enable($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'department-admin-enable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_Department::find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_status = 1;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("update--department--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门】管理员-禁用
    public function operate_department_admin_disable($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'department-admin-disable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_Department::find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_status = 9;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("update--department--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }














    /*
     * 用户系统
     */


    // 【select2】
    public function operate_business_select2_user($post_data)
    {
        $me = Auth::guard('admin')->user();
        if(empty($post_data['keyword']))
        {
            $list =User::select(['id','username as text'])
                ->where(['userstatus'=>'正常','status'=>1])
                ->whereIn('usergroup',['Agent','Agent2'])
                ->orderBy('id','desc')
                ->get()
                ->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =User::select(['id','username as text'])
                ->where(['userstatus'=>'正常','status'=>1])
                ->whereIn('usergroup',['Agent','Agent2'])
                ->where('sitename','like',"%$keyword%")
                ->orderBy('id','desc')
                ->get()
                ->toArray();
        }
        array_unshift($list, ['id'=>0,'text'=>'【全部代理】']);
        return $list;
    }








    /*
     * STAFF 员工管理
     */
    //
    public function operate_user_select2_sales($post_data)
    {
//        $type = $post_data['type'];
//        if($type == 0) $district_type = 0;
//        else if($type == 1) $district_type = 0;
//        else if($type == 2) $district_type = 1;
//        else if($type == 3) $district_type = 2;
//        else if($type == 4) $district_type = 3;
//        else if($type == 21) $district_type = 4;
//        else if($type == 31) $district_type = 21;
//        else if($type == 41) $district_type = 31;
//        else $district_type = 0;
//        if(!is_numeric($type)) return view(env('TEMPLATE_DK_ADMIN').'errors.404');
//        if(!in_array($type,[1,2,3,10,11,88])) return view(env('TEMPLATE_DK_ADMIN').'errors.404');

        if(empty($post_data['keyword']))
        {
            $list =DK_User::select(['id','username as text'])
                ->where(['user_category'=>11])->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =DK_User::select(['id','username as text'])->where('username','like',"%$keyword%")
                ->where(['user_category'=>11])->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        return $list;
    }


    //
    public function operate_user_select2_superior($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $query =DK_User::select(['id','true_name as text'])
                ->where(['user_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_User::select(['id','true_name as text'])->where('username','like',"%$keyword%")
                ->where(['user_status'=>1]);
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'inspector') $query->where(['user_type'=>71]);
            else if($type == 'customer_service_supervisor') $query->where(['user_type'=>81]);
            else if($type == 'customer_service') $query->where(['user_type'=>84]);
        }
        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }
    //
    public function operate_user_select2_department($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $query =DK_Department::select(['id','name as text'])
                ->where(['item_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Department::select(['id','name as text'])->where('name','like',"%$keyword%")
                ->where(['item_status'=>1]);
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'district') $query->where(['department_type'=>11]);
            else if($type == 'group')
            {
                $id = $post_data['superior_id'];
                $query->where(['department_type'=>21,'superior_department_id'=>$id]);
            }
        }
        else $query->where(['department_type'=>11]);

        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }



    // 【员工】返回-列表-视图
    public function view_user_staff_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,21,31,41,61,71,81])) return view($this->view_blade_403);

        if(in_array($me->user_type,[0,1,9,11]))
        {
            $department_district_list = DK_Department::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
            $return['department_district_list'] = $department_district_list;
        }

        $return['menu_active_of_staff_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.user.staff-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【员工】返回-列表-数据
    public function get_user_staff_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_User::withTrashed()->select('*')
            ->with(['creator','superior','department_district_er','department_group_er'])
            ->whereIn('user_category',[11]);

        if($me->user_type == 11)
        {
            $query->whereIn('user_type',[41,61,66,71,77,81,84,88]);
        }
        else if($me->user_type == 61)
        {
            $query->whereIn('user_type',[66]);
        }
        else if($me->user_type == 41)
        {
            $query->where('department_district_id',$me->department_district_id)
                ->whereIn('user_type',[71,77,81,84,88]);
        }
        else if($me->user_type == 71)
        {
            $query->where('department_district_id',$me->department_district_id)
                ->whereIn('user_type',[77]);
        }
        else if($me->user_type == 81)
        {
            $query->where('department_district_id',$me->department_district_id)
                ->whereIn('user_type',[84,88]);
        }
//            ->whereHas('fund', function ($query1) { $query1->where('totalfunds', '>=', 1000); } )
//            ->with('ep','parent','fund')
//            ->withCount([
//                'members'=>function ($query) { $query->where('usergroup','Agent2'); },
//                'fans'=>function ($query) { $query->where('usergroup','Service'); }
//            ]);
//            ->where(['userstatus'=>'正常','status'=>1])
//            ->whereIn('usergroup',['Agent','Agent2']);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['mobile'])) $query->where('mobile', $post_data['mobile']);


        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->where('department_district_id', $post_data['department_district']);
            }
        }

        // 员工类型
        if(!empty($post_data['user_type']))
        {
            if(!in_array($post_data['user_type'],[-1,0]))
            {
                $query->where('user_type', $post_data['user_type']);
            }
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);
        }
//        dd($total);
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【客户管理】【修改记录】返回-列表-视图
    public function view_user_staff_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_staff_modify_list'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.user.staff-modify-list';
        return view($view_blade)->with($return);
    }
    // 【客户管理】【修改记录】返回-列表-数据
    public function get_user_staff_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Record::select('*')
            ->with(['creator'])
            ->where(['record_object'=>21, 'operate_object'=>41,'item_id'=>$id]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【员工】返回-添加-视图
    public function view_user_staff_create()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,21,31,41,61,71,81])) return view($this->view_blade_403);

        $item_type = 'item';
        $item_type_text = '用户';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/staff-list-for-all';

        $return_data['operate'] = 'create';
        $return_data['operate_id'] = 0;
        $return_data['category'] = 'user';
        $return_data['type'] = $item_type;
        $return_data['item_type_text'] = $item_type_text;
        $return_data['title_text'] = $title_text;
        $return_data['list_text'] = $list_text;
        $return_data['list_link'] = $list_link;

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.user.staff-edit';
        return view($view_blade)->with($return_data);
    }
    // 【员工】返回-编辑-视图
    public function view_user_staff_edit()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,19,21,31,41,61,71,81])) return view($this->view_blade_403);

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.user.staff-edit';

        $item_type = 'item';
        $item_type_text = '用户';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/staff-list-for-all';

        $return_data['operate'] = 'create';
        $return_data['operate_id'] = 0;
        $return_data['category'] = 'user';
        $return_data['type'] = $item_type;
        $return_data['item_type_text'] = $item_type_text;
        $return_data['title_text'] = $title_text;
        $return_data['list_text'] = $list_text;
        $return_data['list_link'] = $list_link;

        if($id == 0)
        {
            return view($view_blade)->with($return_data);
        }
        else
        {
            $mine = DK_User::with(['parent','superior'])->find($id);
            if($mine)
            {
                if($me->user_type == 81)
                {
                    if($mine->department_district_id != $me->department_district_id)
                    {
                        return view($this->view_blade_403);
                    }
                }

//                $mine->custom = json_decode($mine->custom);

                $return_data['operate'] = 'edit';
                $return_data['operate_id'] = $id;
                $return_data['data'] = $mine;

                return view($view_blade)->with($return_data);
            }
            else return view(env('TEMPLATE_DK_ADMIN').'entrance.errors.404');
        }
    }
    // 【员工】保存数据
    public function operate_user_staff_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => '参数有误！',
            'true_name.required' => '请输入用户名！',
            'mobile.required' => '请输入电话！',
//            'mobile.unique' => '电话已存在！',
            'api_staffNo.required' => '请输入坐席用户ID！',
            'api_staffNo.numeric' => '坐席用户ID必须为数字！',
            'api_staffNo.min' => '坐席用户ID必须为数字，并且不小于0！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'true_name' => 'required',
            'mobile' => 'required',
//            'mobile' => 'required|unique:dk_user,mobile',
            'api_staffNo' => 'required|numeric|min:0',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,21,31,41,61,71,81])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];


        if($post_data['api_staffNo'] > 0)
        {
            $api_staffNo_is_exist = DK_User::where('api_staffNo',$post_data['api_staffNo'])->first();
            if($api_staffNo_is_exist) return response_error([],"坐席用户ID重复，请更换再试一次！");
        }

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_exist = DK_User::where('mobile',$post_data['mobile'])->first();
            if($is_exist) return response_error([],"工号已存在！");

            $mine = new DK_User;
            $post_data["user_status"] = 0;
            $post_data["user_category"] = 11;
            $post_data["active"] = 1;
            $post_data["password"] = password_encode("12345678");
            $post_data["creator_id"] = $me->id;
            $post_data['username'] = $post_data['true_name'];
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_User::find($operate_id);
            if(!$mine) return response_error([],"该用户不存在，刷新页面重试！");
            if($mine->mobile != $post_data['mobile'])
            {
                $is_exist = DK_User::where('mobile',$post_data['mobile'])->first();
                if($is_exist) return response_error([],"工号重复，请更换工号再试一次！");
            }
        }
        else return response_error([],"参数有误！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }

            $mine_data = $post_data;

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['category']);
            unset($mine_data['type']);

            if(in_array($me->user_type,[41,61,71,81]))
            {
                $mine_data['department_district_id'] = $me->department_district_id;
            }
//            if($me->user_type == 81)
//            {
//                $mine_data['department_district_id'] = $me->department_district_id;
//            }

            if($post_data["user_type"] == 71 || $post_data["user_type"] == 77)
            {
//                $mine_data['department_district_id'] = $me->department_district_id;
//                unset($mine_data['department_district_id']);
                unset($mine_data['department_group_id']);
            }
            else if($post_data["user_type"] == 81)
            {
                unset($mine_data['department_group_id']);
            }


            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
                if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
                {
                    $user_ext = new DK_UserExt;
                    $user_ext_create['user_id'] = $mine->id;
                    $bool_2 = $user_ext->fill($user_ext_create)->save();
                    if(!$bool_2) throw new Exception("insert--user-ext--failed");
                }

                // 头像
                if(!empty($post_data["portrait"]))
                {
                    // 删除原图片
                    $mine_portrait_img = $mine->portrait_img;
                    if(!empty($mine_portrait_img) && file_exists(storage_resource_path($mine_portrait_img)))
                    {
                        unlink(storage_resource_path($mine_portrait_img));
                    }

//                    $result = upload_storage($post_data["portrait"]);
//                    $result = upload_storage($post_data["portrait"], null, null, 'assign');
                    $result = upload_img_storage($post_data["portrait"],'portrait_for_user_by_user_'.$mine->id,'dk/unique/portrait_for_user','');
                    if($result["result"])
                    {
                        $mine->portrait_img = $result["local"];
                        $mine->save();
                    }
                    else throw new Exception("upload--portrait_img--file--fail");
                }
                else
                {
                    if($operate == 'create')
                    {
                        $portrait_path = "dk/unique/portrait_for_user/".date('Y-m-d');
                        if (!is_dir(storage_resource_path($portrait_path)))
                        {
                            mkdir(storage_resource_path($portrait_path), 0777, true);
                        }
                        copy(storage_resource_path("materials/portrait/user0.jpeg"), storage_resource_path($portrait_path."/portrait_for_user_by_user_".$mine->id.".jpeg"));
                        $mine->portrait_img = $portrait_path."/portrait_for_user_by_user_".$mine->id.".jpeg";
                        $mine->save();
                    }
                }

            }
            else throw new Exception("insert--user--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【员工】【文本-信息】设置-文本-类型
    public function operate_staff_info_text_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'user-staff-info-text-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_User::withTrashed()->find($id);
        if(!$item) return response_error([],"该【客户】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $item->timestamps = false;
            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 41;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $column_value;

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工】【时间-信息】修改-时间-类型
    public function operate_staff_info_time_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'user-staff-info-time-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_User::withTrashed()->find($id);
        if(!$item) return response_error([],"该【客户】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];
        $time_type = $post_data["time_type"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->$column_key = strtotime($column_value);
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 41;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_type"] = $time_type;
                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = strtotime($column_value);

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工】【选项-信息】修改-radio-select-[option]-类型
    public function operate_staff_info_option_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'user-staff-info-option-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_User::withTrashed()->find($id);
        if(!$item) return response_error([],"该【客户】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $item->timestamps = false;
            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 41;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $column_value;

                    if(in_array($column_key,['leader_id']))
                    {
                        $record_data["before_id"] = $before;
                        $record_data["after_id"] = $column_value;
                    }


                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工】【附件】添加
    public function operate_staff_info_attachment_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'user-staff-attachment-set') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");

        $item = DK_User::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");
        if(!in_array($me->user_type,[0,1,11,19])) return response_error([],"你没有操作权限！");

//        $operate_type = $post_data["operate_type"];
//        $column_key = $post_data["column_key"];
//        $column_value = $post_data["column_value"];


        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            // 多图
            $multiple_images = [];
            if(!empty($post_data["multiple_images"][0]))
            {
                // 添加图片
                foreach ($post_data["multiple_images"] as $n => $f)
                {
                    if(!empty($f))
                    {
                        $result = upload_img_storage($f,'','dk/attachment','');
                        if($result["result"])
                        {
                            $attachment = new YH_Attachment;

                            $attachment_data["operate_object"] = 41;
                            $attachment_data['item_id'] = $item_id;
                            $attachment_data['attachment_name'] = $post_data["attachment_name"];
                            $attachment_data['attachment_src'] = $result["local"];
                            $bool = $attachment->fill($attachment_data)->save();
                            if($bool)
                            {
                                $record = new DK_Record;

                                $record_data["ip"] = Get_IP();
                                $record_data["record_object"] = 21;
                                $record_data["record_category"] = 11;
                                $record_data["record_type"] = 1;
                                $record_data["creator_id"] = $me->id;
                                $record_data["item_id"] = $item_id;
                                $record_data["operate_object"] = 41;
                                $record_data["operate_category"] = 71;
                                $record_data["operate_type"] = 1;

                                $record_data["column_name"] = 'attachment';
                                $record_data["after"] = $attachment_data['attachment_src'];

                                $bool_1 = $record->fill($record_data)->save();
                                if($bool_1)
                                {
                                }
                                else throw new Exception("insert--record--fail");
                            }
                            else throw new Exception("insert--attachment--fail");
                        }
                        else throw new Exception("upload--attachment--file--fail");
                    }
                }
            }


            // 单图
            if(!empty($post_data["attachment_file"]))
            {
                $attachment = new YH_Attachment;

//                $result = upload_storage($post_data["portrait"]);
//                $result = upload_storage($post_data["portrait"], null, null, 'assign');
                $result = upload_img_storage($post_data["attachment_file"],'','dk/attachment','');
                if($result["result"])
                {
                    $attachment_data["operate_object"] = 41;
                    $attachment_data['item_id'] = $item_id;
                    $attachment_data['attachment_name'] = $post_data["attachment_name"];
                    $attachment_data['attachment_src'] = $result["local"];
                    $bool = $attachment->fill($attachment_data)->save();
                    if($bool)
                    {
                        $record = new DK_Record;

                        $record_data["ip"] = Get_IP();
                        $record_data["record_object"] = 21;
                        $record_data["record_category"] = 11;
                        $record_data["record_type"] = 1;
                        $record_data["creator_id"] = $me->id;
                        $record_data["item_id"] = $item_id;
                        $record_data["operate_object"] = 41;
                        $record_data["operate_category"] = 71;
                        $record_data["operate_type"] = 1;

                        $record_data["column_name"] = 'attachment';
                        $record_data["after"] = $attachment_data['attachment_src'];

                        $bool_1 = $record->fill($record_data)->save();
                        if($bool_1)
                        {
                        }
                        else throw new Exception("insert--record--fail");
                    }
                    else throw new Exception("insert--attachment--fail");
                }
                else throw new Exception("upload--attachment--file--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工】【附件】删除
    public function operate_staff_info_attachment_delete($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'user-client-attachment-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = YH_Attachment::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【附件】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->delete();  // 普通删除
            if($bool)
            {
                $record = new DK_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["item_id"] = $item->item_id;
                $record_data["operate_object"] = 41;
                $record_data["operate_category"] = 71;
                $record_data["operate_type"] = 91;

                $record_data["column_name"] = 'attachment';
                $record_data["before"] = $item->attachment_src;

                $bool_1 = $record->fill($record_data)->save();
                if($bool_1)
                {
                }
                else throw new Exception("insert--record--fail");
            }
            else throw new Exception("attachment--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工】【附件】获取
    public function operate_staff_get_attachment_html($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_User::with([
            'attachment_list' => function($query) { $query->where(['record_object'=>21, 'operate_object'=>41]); }
        ])->withTrashed()->find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.item-assign-html-for-attachment';
        $html = view($view_blade)->with(['item_list'=>$item->attachment_list])->__toString();

        return response_success(['html'=>$html],"");
    }


    // 【员工】管理员-修改密码
    public function operate_user_staff_password_admin_change($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
            'user-password.required' => '请输入密码！',
            'user-password-confirm.required' => '请输入确认密码！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
            'user-password' => 'required',
            'user-password-confirm' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-password-admin-change') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该操作权限！");
//        if(in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该员工的操作权限！");
        if($user->id == $me->id) return response_error([],"你不能删除你自己！");
        if($user->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        $password = $post_data["user-password"];
        $confirm = $post_data["user-password-confirm"];
        if($password != $confirm) return response_error([],"两次密码不一致！");

//        if(!password_is_legal($password)) ;
        $pattern = '/^[a-zA-Z0-9]{1}[a-zA-Z0-9]{5,19}$/i';
        if(!preg_match($pattern,$password)) return response_error([],"密码格式不正确！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->password = password_encode($password);
            $bool = $user->save();
            if(!$bool) throw new Exception("update--user--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工】管理员-重置密码
    public function operate_user_staff_password_admin_reset($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-password-admin-reset') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21,41,81])) return response_error([],"你没有该操作权限！");
//        if(in_array($me->user_type,[0,1,9,11,19,21,81])) return response_error([],"你没有该员工的操作权限！");
        if($user->id == $me->id) return response_error([],"你不能删除你自己！");
        if($user->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->password = password_encode('12345678');
            $bool = $user->save();
            if(!$bool) throw new Exception("update--user--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【员工】管理员-删除
    public function operate_user_staff_admin_delete($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-admin-delete') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,11])) return response_error([],"你没有权限！");

        $user = DK_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该操作权限！");
//        if(in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该员工的操作权限！");
        if($user->id == $me->id) return response_error([],"你不能删除你自己！");
        if($user->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->timestamps = false;
            $bool = $user->delete();  // 普通删除
//            $bool = $user->forceDelete();  // 永久删除
            if(!$bool) throw new Exception("user--delete--fail");
            DB::commit();

            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工】管理员-恢复
    public function operate_user_staff_admin_restore($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-admin-restore') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,11])) return response_error([],"你没有权限！");

        $user = DK_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该操作权限！");
//        if(in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该员工的操作权限！");
        if($user->id == $me->id) return response_error([],"你不能恢复你自己！");
        if($user->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更好的员工！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->timestamps = false;
            $bool = $user->restore();  // 恢复
            if(!$bool) throw new Exception("user--restore--fail");
            DB::commit();

            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工】管理员-永久删除
    public function operate_user_staff_admin_delete_permanently($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-admin-delete-permanently') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,11])) return response_error([],"你没有权限！");

        $user = DK_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该操作权限！");
//        if(in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该员工的操作权限！");
        if($user->id == $me->id) return response_error([],"你不能删除你自己！");
        if($user->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更好的员工！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->timestamps = false;
//            $bool = $user->delete();  // 普通删除
            $bool = $user->forceDelete();  // 永久删除
            if(!$bool) throw new Exception("user--delete--fail");
            DB::commit();

            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【员工】管理员-启用
    public function operate_user_staff_admin_enable($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-admin-enable') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_User::find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($me->user_category != 0) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->user_status = 1;
            $user->login_error_num = 0;
            $user->timestamps = false;
            $bool = $user->save();
            if(!$bool) throw new Exception("update--user--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工】管理员-禁用
    public function operate_user_staff_admin_disable($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-admin-disable') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_User::find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($me->user_category != 0) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->user_status = 9;
            $user->timestamps = false;
            $bool = $user->save();
            if(!$bool) throw new Exception("update--user--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【员工】管理员-解锁
    public function operate_user_staff_admin_unlock($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-admin-unlock') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_User::find($id);
        if(!$user) return response_error([],"该【员工】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($me->user_category != 0) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->user_status = 1;
            $user->login_error_num = 0;
            $user->timestamps = false;
            $bool = $user->save();
            if(!$bool) throw new Exception("update--user--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【员工】管理员-晋升
    public function operate_user_staff_admin_promote($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-admin-promote') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_User::find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");
        if($user->user_type != 88) return response_error([],"只有客服才可以晋升！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,41])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->user_type = 84;
            $user->timestamps = false;
            $bool = $user->save();
            if(!$bool) throw new Exception("update--user--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工】管理员-降职
    public function operate_user_staff_admin_demote($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-admin-demote') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_User::find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");
        if($user->user_type != 84) return response_error([],"只有客服主管才可能降职！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,41])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->user_type = 88;
            $user->timestamps = false;
            $bool = $user->save();
            if(!$bool) throw new Exception("update--user--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }












    /*
     * ITEM 内容管理
     */
    // 【内容】返回-添加-视图
    public function view_item_item_create()
    {
        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9])) return view(env('TEMPLATE_ROOT_FRONT').'errors.404');

        $operate_category = 'item';
        $operate_type = 'item';
        $operate_type_text = '内容';
        $title_text = '添加'.$operate_type_text;
        $list_text = $operate_type_text.'列表';
        $list_link = '/item/item-list';

        $return['operate'] = 'create';
        $return['operate_id'] = 0;
        $return['operate_category'] = $operate_category;
        $return['operate_type'] = $operate_type;
        $return['operate_type_text'] = $operate_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.item-edit';
        return view($view_blade)->with($return);
    }
    // 【内容】返回-编辑-视图
    public function view_item_item_edit($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,21,22])) return view(env('TEMPLATE_DK_ADMIN').'errors.404');

        $id = $post_data["item-id"];
        $mine = $this->modelItem->with(['owner'])->find($id);
        if(!$mine) return view(env('TEMPLATE_DK_ADMIN').'errors.404');


        $operate_category = 'item';

        if($mine->item_type == 0)
        {
            $operate_type = 'item';
            $operate_type_text = '内容';
            $list_link = '/home/item/item-list-for-all';
        }
        else if($mine->item_type == 1)
        {
            $operate_type = 'article';
            $operate_type_text = '文章';
            $list_link = '/home/item/item-article-list';
        }
        else if($mine->item_type == 9)
        {
            $operate_type = 'activity';
            $operate_type_text = '活动';
            $list_link = '/home/item/item-list-for-activity';
        }
        else if($mine->item_type == 11)
        {
            $operate_type = 'menu_type';
            $operate_type_text = '书目';
            $list_link = '/home/item/item-list-for-menu_type';
        }
        else if($mine->item_type == 18)
        {
            $operate_type = 'time_line';
            $operate_type_text = '时间线';
            $list_link = '/home/item/item-list-for-time_line';
        }
        else if($mine->item_type == 88)
        {
            $operate_type = 'advertising';
            $operate_type_text = '广告';
            $list_link = '/home/item/item-list-for-advertising';
        }
        else
        {
            $operate_type = 'item';
            $operate_type_text = '内容';
            $list_link = '/admin/item/item-list';
        }

        $title_text = '编辑'.$operate_type_text;
        $list_text = $operate_type_text.'列表';


        $return['operate_id'] = $id;
        $return['operate_category'] = $operate_category;
        $return['operate_type'] = $operate_type;
        $return['operate_type_text'] = $operate_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.item-edit';
        if($id == 0)
        {
            $return['operate'] = 'create';
            return view($view_blade)->with($return);
        }
        else
        {
            $mine = $this->modelItem->with(['owner'])->find($id);
            if($mine)
            {
                $mine->custom = json_decode($mine->custom);
                $mine->custom2 = json_decode($mine->custom2);
                $mine->custom3 = json_decode($mine->custom3);

                $return['operate'] = 'edit';
                $return['data'] = $mine;
                return view($view_blade)->with($return);
            }
            else return response("该内容不存在！", 404);
        }
    }
    // 【内容】保存-数据
    public function operate_item_item_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required',
            'title.required' => '请输入标题！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'title' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9])) return response_error([],"用户类型错误！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];
        $operate_category = $post_data["operate_category"];
        $operate_type = $post_data["operate_type"];

        if($operate == 'create') // 添加 ( $id==0，添加一个内容 )
        {
            $mine = new YH_Item;
            $post_data["item_category"] = 1;
            $post_data["owner_id"] = $me->id;
            $post_data["creator_id"] = $me->id;
            $post_data["item_category"] = 11;

//            if($type == 'item') $post_data["item_type"] = 0;
//            else if($type == 'article') $post_data["item_type"] = 1;
//            else if($type == 'activity') $post_data["item_type"] = 9;
//            else if($type == 'menu_type') $post_data["item_type"] = 11;
//            else if($type == 'time_line') $post_data["item_type"] = 18
//            else if($type == 'advertising') $post_data["item_type"] = 88;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = $this->modelItem->find($operate_id);
            if(!$mine) return response_error([],"该内容不存在，刷新页面重试！");
//            if($me->id != $me_admin->id)
//            {
//                if($mine->creator_id != $me_admin->id) return response_error([],"不是你创建的，你没有操作权限！");
//            }
//            $post_data["updater_id"] = $me_admin->id;
        }
        else return response_error([],"参数【operate】有误！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }


            $mine_data = $post_data;
            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['operate_category']);
            unset($mine_data['operate_type']);

            if(!empty($post_data['start'])) {
                $mine_data['time_type'] = 1;
                $mine_data['start_time'] = strtotime($post_data['start']);
            }

            if(!empty($post_data['end'])) {
                $mine_data['time_type'] = 1;
                $mine_data['end_time'] = strtotime($post_data['end']);
            }

            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {

                // 封面图片
                if(!empty($post_data["cover"]))
                {
                    // 删除原封面图片
                    $mine_cover_pic = $mine->cover_pic;
                    if(!empty($mine_cover_pic) && file_exists(storage_resource_path($mine_cover_pic)))
                    {
                        unlink(storage_resource_path($mine_cover_pic));
                    }

                    $result = upload_img_storage($post_data["cover"],'','dk/common');
                    if($result["result"])
                    {
                        $mine->cover_pic = $result["local"];
                        $mine->save();
                    }
                    else throw new Exception("upload--cover_pic--fail");
                }

                // 附件
                if(!empty($post_data["attachment"]))
                {
                    // 删除原附件
                    $mine_cover_pic = $mine->attachment;
                    if(!empty($mine_cover_pic) && file_exists(storage_resource_path($mine_cover_pic)))
                    {
                        unlink(storage_resource_path($mine_cover_pic));
                    }

                    $result = upload_file_storage($post_data["attachment"],'','attachment');
                    if($result["result"])
                    {
                        $mine->attachment_name = $result["name"];
                        $mine->attachment_src = $result["local"];
                        $mine->save();
                    }
                    else throw new Exception("upload--attachment_file--fail");
                }

            }
            else throw new Exception("insert--item--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【内容】获取详情
    public function operate_item_item_get($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => '请输入ID！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = Def_Item::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        return response_success($item,"");

    }


    // 【内容】删除
    public function operate_item_item_delete($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = YH_Item::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
        if($item->owner_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            if($item->item_active == 0 && $item->owner_id != $me->id)
            {
                $item_copy = $item;

                $item->timestamps = false;
                $bool = $item->forceDelete();
                $bool = $item->delete();  // 普通删除
                if(!$bool) throw new Exception("item--delete--fail");
                DB::commit();

                $this->delete_the_item_files($item_copy);
            }
            else
            {
                $item->timestamps = false;
//                $bool = $item->delete();  // 普通删除
                $bool = $item->forceDelete();  // 永久删除
                if(!$bool) throw new Exception("item--delete--fail");
                DB::commit();
            }

            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【内容】恢复
    public function operate_item_item_restore($post_data)
    {
        $messages = [
            'operate.required' => '参数有误！',
            'item_id.required' => '请输入ID！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-restore') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Item::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"用户类型错误！");
        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->restore();
            if(!$bool) throw new Exception("item--restore--fail");
            DB::commit();

            $item_html = $this->get_the_item_html($item);
            return response_success(['item_html'=>$item_html]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【内容】彻底删除
    public function operate_item_item_delete_permanently($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-delete-permanently') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Item::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($id == $me->advertising_id)
            {
                $me->timestamps = false;
                $me->advertising_id = 0;
                $bool_0 = $me->save();
                if(!$bool_0) throw new Exception("update--user--fail");
            }

            $item_copy = $item;

            $bool = $item->forceDelete();
            if(!$bool) throw new Exception("item--delete--fail");
            DB::commit();

            $this->delete_the_item_files($item_copy);

            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【内容】批量-删除
    public function operate_item_item_delete_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => 'bulk_item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $item->timestamps = false;
                    $bool = $item->delete();
                    if(!$bool) throw new Exception("delete--item--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }
    // 【内容】批量-删除
    public function operate_item_item_restore_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => 'bulk_item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $item->timestamps = false;
                    $bool = $item->restore();
                    if(!$bool) throw new Exception("item--restore--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }
    // 【内容】批量-彻底删除
    public function operate_item_item_delete_permanently_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => 'bulk_item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $bool = $item->forceDelete();
                    if(!$bool) throw new Exception("delete--item--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }


    // 【ITEM】批量-操作（全部操作）
    public function operate_item_item_operate_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => 'bulk_item_id.required.',
            'bulk_item_operate.required' => 'bulk_item_operate.required.',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
            'bulk_item_operate' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }
//        dd($post_data);

        $this->get_me();
        $me = $this->me;

        $item_operate = $post_data["bulk_item_operate"];
        if(!in_array($item_operate,['启用','禁用','删除','永久删除'])) return response_error([],"参数【operate】有误！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $current_time = date('Y-m-d H:i:s');
//            $keyword_owner = User::where("id",$item->creator_id)->lockForUpdate()->first();

            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"id有误，刷新页面试试！");

                if($item_operate == "启用") $item_data["item_status"] = 1;
                elseif($item_operate == "禁用") $item_data["item_status"] = 9;
                else $item_data = [];
//                dd($item_data);

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $bool = $item->fill($item_data)->save();
                    if(!$bool) throw new Exception("update--item--fail");
                }
                else throw new Exception('关键词不存在，刷新页面试试！');
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }
    // 【内容】批量-启用
    public function operate_item_item_enable_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => '请选择关键词！',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $update["item_status"] = 1;
                    $bool = $item->fill($update)->save();
                    if($bool)
                    {
                    }
                    else throw new Exception("update--item--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }
    // 【内容】批量-禁用
    public function operate_item_item_disable_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => '请选择关键词！',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $update["item_status"] = 9;
                    $bool = $item->fill($update)->save();
                    if($bool)
                    {
                    }
                    else throw new Exception("update--item--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }




    // 【任务管理】批量-操作（全部操作）
    public function operate_item_task_admin_operate_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => 'bulk_item_id.required.',
            'bulk_item_operate.required' => 'bulk_item_operate.required.',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
            'bulk_item_operate' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }
//        dd($post_data);

        $this->get_me();
        $me = $this->me;

        $item_operate = $post_data["bulk_item_operate"];
        if(!in_array($item_operate,['启用','禁用','删除','永久删除'])) return response_error([],"参数【operate】有误！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $current_time = date('Y-m-d H:i:s');
//            $keyword_owner = User::where("id",$item->creator_id)->lockForUpdate()->first();

            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"id有误，刷新页面试试！");

                if($item_operate == "启用") $item_data["item_status"] = 1;
                elseif($item_operate == "禁用") $item_data["item_status"] = 9;
                else $item_data = [];

                $item = YH_Task::find($item_id);
                if($item)
                {
                    $bool = $item->fill($item_data)->save();
                    if(!$bool) throw new Exception("update--item--fail");
                }
                else throw new Exception('关键词不存在，刷新页面试试！');
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }
    // 【任务管理】管理员-批量-删除
    public function operate_item_task_admin_delete_bulk($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'bulk_item_id.required' => 'bulk_item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'task-admin-delete-bulk') return response_error([],"参数[operate]有误！");

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Task::withTrashed()->find($item_id);
                if($item)
                {
                    $item->timestamps = false;
//                    $bool = $item->delete();  // 普通删除
                    $bool = $item->forceDelete();  // 永久删除
                    if(!$bool) throw new Exception("delete--task--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }
    // 【任务管理】管理员-批量-删除
    public function operate_item_task_admin_restore_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => 'bulk_item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $item->timestamps = false;
                    $bool = $item->restore();
                    if(!$bool) throw new Exception("item--restore--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }
    // 【任务管理】管理员-批量-彻底删除
    public function operate_item_task_admin_delete_permanently_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => 'bulk_item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $bool = $item->forceDelete();
                    if(!$bool) throw new Exception("delete--item--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }








    /*
     * 地域管理
     */

    // 【地域】select2
    public function operate_district_select2_district($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $query =DK_District::select(['id','district_district as text'])
                ->where(['district_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_District::select(['id','district_district as text'])->where('district_district','like',"%$keyword%")
                ->where(['district_status'=>1]);
        }

        $city = $post_data['district_city'];
        $query->where(['district_city'=>$city]);

        $list = $query->orderBy('id','desc')->get()->toArray();
//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);


        if(count($list) > 0)
        {
            $district_district_array = explode("-",$list[0]['text']);
            foreach($district_district_array as $key => $value)
            {
                $district_district_array[$key] = ['id'=>$value,'text'=>$value];
            }
        }
        else
        {
            $district_district_array = [];
        }


        return $district_district_array;
    }


    // 【地域】返回-列表-视图
    public function view_item_district_list($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61])) return view($this->view_blade_403);

        $return['menu_active_of_district_list'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.district-list';
        return view($view_blade)->with($return);
    }
    // 【地域】返回-列表-数据
    public function get_item_district_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query = DK_District::select('*')
            ->withTrashed()
            ->with(['creator']);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");

        // 状态 [|]
        if(!empty($post_data['district_status']))
        {
            if(!in_array($post_data['district_status'],[-1,0]))
            {
                $query->where('district_status', $post_data['district_status']);
            }
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 100;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }


    // 【地域】返回-添加-视图
    public function view_item_district_create()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61])) return view($this->view_blade_403);

        $item_type = 'item';
        $item_type_text = '项目';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/district-list';

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.district-edit';
        return view($view_blade)->with([
            'operate'=>'create',
            'operate_id'=>0,
            'category'=>'item',
            'type'=>$item_type,
            'item_type_text'=>$item_type_text,
            'title_text'=>$title_text,
            'list_text'=>$list_text,
            'list_link'=>$list_link,
        ]);
    }
    // 【地域】返回-编辑-视图
    public function view_item_district_edit()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61])) return view($this->view_blade_403);

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.district-edit';

        $item_type = 'item';
        $item_type_text = '项目';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/district-list';

        if($id == 0)
        {
            return view($view_blade)->with([
                'operate'=>'create',
                'operate_id'=>0,
                'category'=>'item',
                'type'=>$item_type,
                'item_type_text'=>$item_type_text,
                'title_text'=>$title_text,
                'list_text'=>$list_text,
                'list_link'=>$list_link,
            ]);
        }
        else
        {
            $mine = DK_District::find($id);
            if($mine)
            {
//                if(!in_array($mine->user_category,[1,9,11,88])) return view(env('TEMPLATE_DK_ADMIN').'errors.404');
//                $mine->custom = json_decode($mine->custom);
//                $mine->custom2 = json_decode($mine->custom2);
//                $mine->custom3 = json_decode($mine->custom3);

                return view($view_blade)->with([
                    'operate'=>'edit',
                    'operate_id'=>$id,
                    'data'=>$mine,
                    'category'=>'item',
                    'type'=>$item_type,
                    'item_type_text'=>$item_type_text,
                    'title_text'=>$title_text,
                    'list_text'=>$list_text,
                    'list_link'=>$list_link,
                ]);
            }
            else return view(env('TEMPLATE_DK_ADMIN').'errors.404');
        }
    }
    // 【地域】保存数据
    public function operate_item_district_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'district_city.required' => '请输入城市名称！',
            'district_district.required' => '请输入城市名称！',
//            'name.unique' => '该项目已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'district_city' => 'required',
            'district_district' => 'required',
//            'name' => 'required|unique:dk_project,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }
//        dd($post_data);


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个项目 )
        {
            $is_exist = DK_District::select('id')->where('district_city',$post_data["district_city"])->count();
            if($is_exist) return response_error([],"该【城市】已存在，请勿重复添加！");

            $mine = new DK_District;
            $post_data["active"] = 1;
            $post_data["district_status"] = 1;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_District::find($operate_id);
            if(!$mine) return response_error([],"该【城市】不存在，刷新页面重试！");
        }
        else return response_error([],"参数有误！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }

            $mine_data = $post_data;

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['category']);
            unset($mine_data['type']);


            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
            }
            else throw new Exception("insert--district--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【地域】管理员-删除
    public function operate_item_district_admin_delete($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'district-admin-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = DK_District::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【城市】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->delete();  // 普通删除
            if(!$bool) throw new Exception("district--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【地域】管理员-恢复
    public function operate_item_district_admin_restore($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'operate.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'district-admin-restore') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_District::withTrashed()->find($id);
        if(!$item) return response_error([],"该【城市】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->restore();
            if(!$bool) throw new Exception("project--restore--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【地域】管理员-彻底删除
    public function operate_item_district_admin_delete_permanently($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'district-admin-delete-permanently') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_District::withTrashed()->find($id);
        if(!$item) return response_error([],"该【城市】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_copy = $item;

            $bool = $item->forceDelete();
            if(!$bool) throw new Exception("district--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【地域】管理员-启用
    public function operate_item_district_admin_enable($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'district-admin-enable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_District::find($id);
        if(!$item) return response_error([],"该【城市】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->district_status = 1;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("update--district--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【地域】管理员-禁用
    public function operate_item_district_admin_disable($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'district-admin-disable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_District::find($id);
        if(!$item) return response_error([],"该【城市】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->district_status = 9;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("update--district--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }








    /*
     * 项目管理
     */
    // 【项目】返回-列表-视图
    public function view_item_project_list($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,41,71,81])) return view($this->view_blade_403);

        $return['menu_active_of_project_list'] = 'active menu-open';
        if(in_array($me->user_type, [41,71,81]))
        {
            $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.project-list-for-department';
        }
        else $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.project-list';
        return view($view_blade)->with($return);
    }
    // 【项目】返回-列表-数据
    public function get_item_project_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query = DK_Project::select('*')
            ->withTrashed()
            ->with(['creator','client_er','inspector_er']);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");

        // 状态 [|]
        if(!empty($post_data['item_status']))
        {
            if(!in_array($post_data['item_status'],[-1,0]))
            {
                $query->where('item_status', $post_data['item_status']);
            }
        }
        else
        {
            $query->where('item_status', 1);
        }

        if(in_array($me->user_type, [41,71,81]))
        {
            $department_district_id = $me->department_district_id;
            $project_list = DK_Pivot_Team_Project::select('project_id')->where('team_id',$department_district_id)->get();
            $query->whereIn('id',$project_list);

            $department_district_id = $me->department_district_id;
            $inspector_list = DK_User::select('id')->whereIn('user_type',[71,77])->where('department_district_id',$department_district_id)->get();
            $query->with(['pivot_project_user'=>function($query) use($inspector_list) { $query->whereIn('user_id',$inspector_list); }]);
        }
        else
        {
            $query->with(['pivot_project_user','pivot_project_team']);
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 100;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("name", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }


    // 【项目】【修改记录】返回-列表-视图
    public function view_item_project_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_car_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.project-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【项目】【修改记录】返回-列表-数据
    public function get_item_project_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Record::select('*')
            ->with(['creator'])
            ->where(['record_object'=>21,'operate_object'=>61,'item_id'=>$id]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【项目】返回-添加-视图
    public function view_item_project_create()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61])) return view($this->view_blade_403);

        $item_type = 'item';
        $item_type_text = '项目';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/project-list';

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.project-edit';
        return view($view_blade)->with([
            'operate'=>'create',
            'operate_id'=>0,
            'category'=>'item',
            'type'=>$item_type,
            'item_type_text'=>$item_type_text,
            'title_text'=>$title_text,
            'list_text'=>$list_text,
            'list_link'=>$list_link,
        ]);
    }
    // 【项目】返回-编辑-视图
    public function view_item_project_edit()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61])) return view($this->view_blade_403);

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.project-edit';

        $item_type = 'item';
        $item_type_text = '项目';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/project-list';

        if($id == 0)
        {
            return view($view_blade)->with([
                'operate'=>'create',
                'operate_id'=>0,
                'category'=>'item',
                'type'=>$item_type,
                'item_type_text'=>$item_type_text,
                'title_text'=>$title_text,
                'list_text'=>$list_text,
                'list_link'=>$list_link,
            ]);
        }
        else
        {
            $mine = DK_Project::with('inspector_er')->find($id);
            if($mine)
            {
//                if(!in_array($mine->user_category,[1,9,11,88])) return view(env('TEMPLATE_DK_ADMIN').'errors.404');
                $mine->custom = json_decode($mine->custom);
                $mine->custom2 = json_decode($mine->custom2);
                $mine->custom3 = json_decode($mine->custom3);

                return view($view_blade)->with([
                    'operate'=>'edit',
                    'operate_id'=>$id,
                    'data'=>$mine,
                    'category'=>'item',
                    'type'=>$item_type,
                    'item_type_text'=>$item_type_text,
                    'title_text'=>$title_text,
                    'list_text'=>$list_text,
                    'list_link'=>$list_link,
                ]);
            }
            else return view(env('TEMPLATE_DK_ADMIN').'errors.404');
        }
    }
    // 【项目】保存数据
    public function operate_item_project_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'name.required' => '请输入项目名称！',
//            'name.unique' => '该项目已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'name' => 'required',
//            'name' => 'required|unique:dk_project,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }
//        dd($post_data);


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个项目 )
        {
            $is_exist = DK_Project::select('id')->where('name',$post_data["name"])->count();
            if($is_exist) return response_error([],"该【项目】已存在，请勿重复添加！");

            $mine = new DK_Project;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_Project::find($operate_id);
            if(!$mine) return response_error([],"该【项目】不存在，刷新页面重试！");
        }
        else return response_error([],"参数有误！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }

            $mine_data = $post_data;

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['category']);
            unset($mine_data['type']);


            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
                if(!empty($post_data["peoples"]))
                {
//                    $product->peoples()->attach($post_data["peoples"]);
                    $current_time = time();
                    $peoples = $post_data["peoples"];
                    foreach($peoples as $p)
                    {
                        $people_insert[$p] = ['creator_id'=>$me->id,'department_id'=>$me->department_district_id,'relation_type'=>1,'created_at'=>$current_time,'updated_at'=>$current_time];
                    }
                    $mine->pivot_project_user()->sync($people_insert);
//                    $mine->pivot_project_user()->syncWithoutDetaching($people_insert);
                }
                else
                {
                    $mine->pivot_project_user()->detach();
                }

                if(!empty($post_data["teams"]))
                {
//                    $product->peoples()->attach($post_data["peoples"]);
                    $current_time = time();
                    $teams = $post_data["teams"];
                    foreach($teams as $t)
                    {
                        $team_insert[$t] = ['relation_type'=>1,'created_at'=>$current_time,'updated_at'=>$current_time];
                    }
                    $mine->pivot_project_team()->sync($team_insert);
//                    $mine->pivot_product_people()->syncWithoutDetaching($people_insert);
                }
                else
                {
                    $mine->pivot_project_team()->detach();
                }
            }
            else throw new Exception("insert--project--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【项目】【文本-信息】设置-文本-类型
    public function operate_item_project_info_text_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-project-info-text-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Project::withTrashed()->find($id);
        if(!$item) return response_error([],"该【项目】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $item->timestamps = false;
            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 61;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $column_value;

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【项目】【时间-信息】修改-时间-类型
    public function operate_item_project_info_time_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-project-info-time-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Project::withTrashed()->find($id);
        if(!$item) return response_error([],"该【项目】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];
        $time_type = $post_data["time_type"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->$column_key = strtotime($column_value);
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 61;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_type"] = $time_type;
                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = strtotime($column_value);

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【项目】【选项-信息】修改-radio-select-[option]-类型
    public function operate_item_project_info_option_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-project-info-option-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Project::withTrashed()->find($id);
        if(!$item) return response_error([],"该【项目】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($column_key == "inspector_list")
            {
                if(!empty($post_data["column_value"]))
                {
//                    $product->peoples()->attach($post_data["peoples"]);
                    $current_time = time();
                    $inspector_list = $post_data["column_value"];
                    foreach($inspector_list as $i)
                    {
                        $inspector_list_insert[$i] = ['creator_id'=>$me->id,'department_id'=>$me->department_district_id,'relation_type'=>1,'created_at'=>$current_time,'updated_at'=>$current_time];
                    }
                    $item->pivot_project_user()->wherePivot('department_id',$me->department_district_id)->sync($inspector_list_insert);
//                    $mine->pivot_project_user()->syncWithoutDetaching($people_insert);
                }
                else
                {
                    $item->pivot_project_user()->wherePivot('department_id',$me->department_district_id)->detach();
                }
            }
            else
            {
                $item->timestamps = false;
                $item->$column_key = $column_value;
                $bool = $item->save();
                if(!$bool) throw new Exception("item--update--fail");
            }

            if(false) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
//                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                if(true)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 61;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $column_value;

                    if(in_array($column_key,['client_id']))
                    {
                        $record_data["before_id"] = $before;
                        $record_data["after_id"] = $column_value;
                    }

                    if($column_key == 'client_id')
                    {
                        $record_data["before_client_id"] = $before;
                        $record_data["after_client_id"] = $column_value;
                    }

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【项目】【附件】添加
    public function operate_item_project_info_attachment_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-project-attachment-set') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");

        $item = DK_Project::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【项目】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");
        if(!in_array($me->user_type,[0,1,11,19])) return response_error([],"你没有操作权限！");

//        $operate_type = $post_data["operate_type"];
//        $column_key = $post_data["column_key"];
//        $column_value = $post_data["column_value"];


        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            // 多图
            $multiple_images = [];
            if(!empty($post_data["multiple_images"][0]))
            {
                // 添加图片
                foreach ($post_data["multiple_images"] as $n => $f)
                {
                    if(!empty($f))
                    {
                        $result = upload_img_storage($f,'','dk/attachment','');
                        if($result["result"])
                        {
                            $attachment = new YH_Attachment;

                            $attachment_data["operate_object"] = 41;
                            $attachment_data['item_id'] = $item_id;
                            $attachment_data['attachment_name'] = $post_data["attachment_name"];
                            $attachment_data['attachment_src'] = $result["local"];
                            $bool = $attachment->fill($attachment_data)->save();
                            if($bool)
                            {
                                $record = new DK_Record;

                                $record_data["ip"] = Get_IP();
                                $record_data["record_object"] = 21;
                                $record_data["record_category"] = 11;
                                $record_data["record_type"] = 1;
                                $record_data["creator_id"] = $me->id;
                                $record_data["item_id"] = $item_id;
                                $record_data["operate_object"] = 61;
                                $record_data["operate_category"] = 71;
                                $record_data["operate_type"] = 1;

                                $record_data["column_name"] = 'attachment';
                                $record_data["after"] = $attachment_data['attachment_src'];

                                $bool_1 = $record->fill($record_data)->save();
                                if($bool_1)
                                {
                                }
                                else throw new Exception("insert--record--fail");
                            }
                            else throw new Exception("insert--attachment--fail");
                        }
                        else throw new Exception("upload--attachment--file--fail");
                    }
                }
            }


            // 单图
            if(!empty($post_data["attachment_file"]))
            {
                $attachment = new YH_Attachment;

//                $result = upload_storage($post_data["portrait"]);
//                $result = upload_storage($post_data["portrait"], null, null, 'assign');
                $result = upload_img_storage($post_data["attachment_file"],'','dk/attachment','');
                if($result["result"])
                {
                    $attachment_data["operate_object"] = 41;
                    $attachment_data['item_id'] = $item_id;
                    $attachment_data['attachment_name'] = $post_data["attachment_name"];
                    $attachment_data['attachment_src'] = $result["local"];
                    $bool = $attachment->fill($attachment_data)->save();
                    if($bool)
                    {
                        $record = new DK_Record;

                        $record_data["ip"] = Get_IP();
                        $record_data["record_object"] = 21;
                        $record_data["record_category"] = 11;
                        $record_data["record_type"] = 1;
                        $record_data["creator_id"] = $me->id;
                        $record_data["item_id"] = $item_id;
                        $record_data["operate_object"] = 41;
                        $record_data["operate_category"] = 71;
                        $record_data["operate_type"] = 1;

                        $record_data["column_name"] = 'attachment';
                        $record_data["after"] = $attachment_data['attachment_src'];

                        $bool_1 = $record->fill($record_data)->save();
                        if($bool_1)
                        {
                        }
                        else throw new Exception("insert--record--fail");
                    }
                    else throw new Exception("insert--attachment--fail");
                }
                else throw new Exception("upload--attachment--file--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【项目】【附件】删除
    public function operate_item_project_info_attachment_delete($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'car-attachment-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = YH_Attachment::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【附件】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->delete();  // 普通删除
            if($bool)
            {
                $record = new DK_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["item_id"] = $item->item_id;
                $record_data["operate_object"] = 61;
                $record_data["operate_category"] = 71;
                $record_data["operate_type"] = 91;

                $record_data["column_name"] = 'attachment';
                $record_data["before"] = $item->attachment_src;

                $bool_1 = $record->fill($record_data)->save();
                if($bool_1)
                {
                }
                else throw new Exception("insert--record--fail");
            }
            else throw new Exception("attachment--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【项目】【附件】获取
    public function operate_item_project_get_attachment_html($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Project::with([
                'attachment_list' => function($query) { $query->where(['record_object'=>21, 'operate_object'=>61]); }
            ])->withTrashed()->find($id);
        if(!$item) return response_error([],"该【项目】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.item-assign-html-for-attachment';
        $html = view($view_blade)->with(['item_list'=>$item->attachment_list])->__toString();

        return response_success(['html'=>$html],"");
    }


    // 【项目】管理员-删除
    public function operate_item_project_admin_delete($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'project-admin-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = DK_Project::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【项目】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->delete();  // 普通删除
            if(!$bool) throw new Exception("project--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【项目】管理员-恢复
    public function operate_item_project_admin_restore($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'operate.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'project-admin-restore') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_Project::withTrashed()->find($id);
        if(!$item) return response_error([],"该【项目】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->restore();
            if(!$bool) throw new Exception("project--restore--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【项目】管理员-彻底删除
    public function operate_item_project_admin_delete_permanently($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'project-admin-delete-permanently') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_Project::withTrashed()->find($id);
        if(!$item) return response_error([],"该【项目】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_copy = $item;

            $bool = $item->forceDelete();
            if(!$bool) throw new Exception("project--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【项目】管理员-启用
    public function operate_item_project_admin_enable($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'project-admin-enable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_Project::find($id);
        if(!$item) return response_error([],"该【项目】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_status = 1;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("update--project--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【项目】管理员-禁用
    public function operate_item_project_admin_disable($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'project-admin-disable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_Project::find($id);
        if(!$item) return response_error([],"该【项目】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_status = 9;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("update--project--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }












    /*
     * 工单管理
     */
    // 【工单】select2
    public function operate_item_select2_user($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(empty($post_data['keyword']))
        {
            $query =DK_User::select(['id','username as text'])
                ->where(['user_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_User::select(['id','username as text'])->where('username','like',"%$keyword%")
                ->where(['user_status'=>1]);
        }

        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $department_district_id = $me->department_district_id;
            $query->where('department_district_id',$department_district_id);
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'inspector') $query->where(['user_type'=>77]);
        }
        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }
    //
    public function operate_item_select2_team($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $query = DK_Department::select(['id','name as text'])
                ->where(['item_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query = DK_Department::select(['id','name as text'])->where('name','like',"%$keyword%")
                ->where(['item_status'=>1]);
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'district') $query->where(['department_type'=>11]);
            else if($type == 'group')
            {
                $id = $post_data['superior_id'];
                $query->where(['department_type'=>21,'superior_department_id'=>$id]);
            }
        }
        else $query->where(['department_type'=>11]);

        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }
    //
    public function operate_item_select2_project($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(empty($post_data['keyword']))
        {
            $query = DK_Project::select(['id','name as text']);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query = DK_Project::select(['id','name as text'])->where('name','like',"%$keyword%");
        }

        $query->where('item_status',1);
//        $query->where(['user_status'=>1,'user_category'=>11]);
//        $query->whereIn('user_type',[41,61,88]);

//        if(in_array($me->user_type,[81,84,88]))
        if(!in_array($me->department_district_id,[0]))
        {
            $department_district_id = $me->department_district_id;
            $project_list = DK_Pivot_Team_Project::select('project_id')->where('team_id',$department_district_id)->get();
            $query->whereIn('id',$project_list);
        }

        $list = $query->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }
    //
    public function operate_item_select2_client($post_data)
    {
//        $type = $post_data['type'];
//        if($type == 0) $district_type = 0;
//        else if($type == 1) $district_type = 0;
//        else if($type == 2) $district_type = 1;
//        else if($type == 3) $district_type = 2;
//        else if($type == 4) $district_type = 3;
//        else if($type == 21) $district_type = 4;
//        else if($type == 31) $district_type = 21;
//        else if($type == 41) $district_type = 31;
//        else $district_type = 0;
//        if(!is_numeric($type)) return view(env('TEMPLATE_DK_ADMIN').'errors.404');
//        if(!in_array($type,[1,2,3,10,11,88])) return view(env('TEMPLATE_DK_ADMIN').'errors.404');

        if(empty($post_data['keyword']))
        {
            $list =DK_Client::select(['id','username as text'])
                ->where(['user_status'=>1,'user_category'=>11])
//                ->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =DK_Client::select(['id','username as text'])->where('username','like',"%$keyword%")
                ->where(['user_status'=>1,'user_category'=>11])
//                ->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }


    // 【工单】返回-列表-视图
    public function view_item_order_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 记录
        if(!empty($post_data['record']))
        {
            if($post_data['record'] == 'record')
            {
                $this->record_for_user_operate(21,11,1,$me->id,0,71,0);
            }
        }

        // 显示数量
        if(!empty($post_data['length']))
        {
            if(is_numeric($post_data['length']) && $post_data['length'] > 0) $view_data['length'] = $post_data['length'];
            else $view_data['length'] = 10;
        }
        else $view_data['length'] = 10;
        // 第几页
        if(!empty($post_data['page']))
        {
            if(is_numeric($post_data['page']) && $post_data['page'] > 0) $view_data['page'] = $post_data['page'];
            else $view_data['page'] = 1;
        }
        else $view_data['page'] = 1;




        // 工单ID
        if(!empty($post_data['order_id']))
        {
            if(is_numeric($post_data['order_id']) && $post_data['order_id'] > 0) $view_data['order_id'] = $post_data['order_id'];
            else $view_data['order_id'] = '';
        }
        else $view_data['order_id'] = '';

        // 提交日期
        if(!empty($post_data['assign']))
        {
            if($post_data['assign']) $view_data['assign'] = $post_data['assign'];
            else $view_data['assign'] = '';
        }
        else $view_data['assign'] = '';

        // 起始时间
        if(!empty($post_data['assign_start']))
        {
            if($post_data['assign_start']) $view_data['start'] = $post_data['assign_start'];
            else $view_data['start'] = '';
        }
        else $view_data['start'] = '';

        // 截止时间
        if(!empty($post_data['assign_ended']))
        {
            if($post_data['assign_ended']) $view_data['ended'] = $post_data['assign_ended'];
            else $view_data['ended'] = '';
        }
        else $view_data['ended'] = '';

        // 员工
        if(!empty($post_data['staff_id']))
        {
            if(is_numeric($post_data['staff_id']) && $post_data['staff_id'] > 0) $view_data['staff_id'] = $post_data['staff_id'];
            else $view_data['staff_id'] = -1;
        }
        else $view_data['staff_id'] = -1;

        // 部门-大区
        if(!empty($post_data['department_district_id']))
        {
            if(is_numeric($post_data['department_district_id']) && $post_data['department_district_id'] > 0) $view_data['department_district_id'] = $post_data['department_district_id'];
            else $view_data['department_district_id'] = -1;
        }
        else $view_data['department_district_id'] = -1;



        // 客户
        if(!empty($post_data['client_id']))
        {
            if(is_numeric($post_data['client_id']) && $post_data['client_id'] > 0) $view_data['client_id'] = $post_data['client_id'];
            else $view_data['client_id'] = -1;
        }
        else $view_data['client_id'] = -1;




        // 项目
        if(!empty($post_data['project_id']))
        {
            if(is_numeric($post_data['project_id']) && $post_data['project_id'] > 0)
            {
                $project = DK_Project::select(['id','name'])->find($post_data['project_id']);
                if($project)
                {
                    $view_data['project_id'] = $post_data['project_id'];
                    $view_data['project_name'] = $project->name;
                }
                else $view_data['project_id'] = -1;
            }
            else $view_data['project_id'] = -1;
        }
        else $view_data['project_id'] = -1;

        // 客户姓名
        if(!empty($post_data['client_name']))
        {
            if($post_data['client_name']) $view_data['client_name'] = $post_data['client_name'];
            else $view_data['client_name'] = '';
        }
        else $view_data['client_'] = '';
        // 客户电话
        if(!empty($post_data['client_phone']))
        {
            if($post_data['client_phone']) $view_data['client_phone'] = $post_data['client_phone'];
            else $view_data['client_phone'] = '';
        }
        else $view_data['client_phone'] = '';

        // 是否+V
        if(!empty($post_data['is_wx']))
        {
            if(is_numeric($post_data['is_wx']) && $post_data['is_wx'] > 0) $view_data['is_wx'] = $post_data['is_wx'];
            else $view_data['is_wx'] = -1;
        }
        else $view_data['is_wx'] = -1;

        // 是否重复
        if(!empty($post_data['is_repeat']))
        {
            if(is_numeric($post_data['is_repeat']) && $post_data['is_repeat'] > 0) $view_data['is_repeat'] = $post_data['is_repeat'];
            else $view_data['is_repeat'] = -1;
        }
        else $view_data['is_repeat'] = -1;

        // 类型
        if(!empty($post_data['order_type']))
        {
            if(is_numeric($post_data['order_type']) && $post_data['order_type'] > 0) $view_data['order_type'] = $post_data['order_type'];
            else $view_data['order_type'] = -1;
        }
        else $view_data['order_type'] = -1;


        // 审核状态
        if(!empty($post_data['inspected_status']))
        {
            $view_data['inspected_status'] = $post_data['inspected_status'];
        }
        else $view_data['inspected_status'] = -1;

        // 交付状态
        if(!empty($post_data['delivered_status']))
        {
            $view_data['delivered_status'] = $post_data['delivered_status'];
        }
        else $view_data['delivered_status'] = -1;

        // 城市
        if(!empty($post_data['district_city']))
        {
            $view_data['district_city'] = $post_data['district_city'];
        }
        else $view_data['district_city'] = -1;

        // 区域
        if(!empty($post_data['district_district']))
        {
            $view_data['district_district'] = $post_data['district_district'];
        }
        else $view_data['district_district'] = -1;

//        dd($view_data);


        $client_list = DK_Client::select('id','username')->where('user_category',11)->get();
        $view_data['client_list'] = $client_list;

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        if($me->user_type == 41)
        {
            $staff_list = DK_User::select('id','username')
                ->where('user_category',11)
                ->where('department_district_id',$me->department_district_id)
                ->whereIn('user_type',[81,84,88])
                ->get();
        }
        else if($me->user_type == 81)
        {
            $staff_list = DK_User::select('id','username')
                ->where('user_category',11)
                ->where('department_district_id',$me->department_district_id)
                ->whereIn('user_type',[81,84,88])
                ->get();
        }
        else if($me->user_type == 84)
        {
            $staff_list = DK_User::select('id','username')
                ->where('user_category',11)
                ->where('department_group_id',$me->department_group_id)
                ->whereIn('user_type',[84,88])
                ->get();
        }
        else
        {
            $staff_list = DK_User::select('id','username')
                ->where('user_category',11)
                ->whereIn('user_type',[81,84,88])
                ->get();
        }
        $view_data['staff_list'] = $staff_list;

        $project_list = DK_Project::select('id','name')->whereIn('item_type',[1,21])->get();
        $view_data['project_list'] = $project_list;

        $district_city_list = DK_District::select('id','district_city')->whereIn('district_status',[1])->get();
        $view_data['district_city_list'] = $district_city_list;

        if(!empty($post_data['district_city']))
        {
            $district_district_list = DK_District::select('district_district')->where('district_city',$post_data['district_city'])->whereIn('district_status',[1])->get();
            if(count($district_district_list) > 0)
            {
                $district_district_array = explode("-",$district_district_list[0]->district_district);
                $view_data['district_district_list'] = $district_district_array;
            }
            else
            {
                $view_data['district_district_list'] = [];
            }
        }

        $view_data['menu_active_of_order_list_for_all'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.order-list-for-all';
        return view($view_blade)->with($view_data);
    }
    // 【工单】返回-列表-数据
    public function get_item_order_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Order::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->with([
                'creator',
                'owner'=>function($query) { $query->select('id','username'); },
                'client_er'=>function($query) { $query->select('id','username'); },
                'inspector',
                'deliverer',
                'project_er',
                'department_district_er',
                'department_group_er',
                'department_manager_er',
                'department_supervisor_er'
            ]);
//            ->whereIn('user_category',[11])
//            ->whereIn('user_type',[0,1,9,11,19,21,22,41,61,88]);
//            ->whereHas('fund', function ($query1) { $query1->where('totalfunds', '>=', 1000); } )
//            ->withCount([
//                'members'=>function ($query) { $query->where('usergroup','Agent2'); },
//                'fans'=>function ($query) { $query->rderwhere('usergroup','Service'); }
//            ]);
//            ->where(['userstatus'=>'正常','status'=>1])
//            ->whereIn('usergroup',['Agent','Agent2']);

//        $me->load(['subordinate_er' => function ($query) {
//            $query->select('id');
//        }]);


        // 部门经理
        if($me->user_type == 41)
        {
//            $subordinates = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $subordinates_subordinates = DK_User::select('id')->whereIn('superior_id',$subordinates)->get()->pluck('id')->toArray();
//            $subordinates_list = array_merge($subordinates_subordinates,$subordinates);
//            $subordinates_list[] = $me->id;
//            $query->whereIn('creator_id',$subordinates_list);
            $district_staff_list = DK_User::select('id')->where('department_district_id',$me->department_district_id)->get()->pluck('id')->toArray();
            $query->whereIn('creator_id',$district_staff_list);
        }
        // 客服经理
        if($me->user_type == 81)
        {
//            $subordinates = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $subordinates_subordinates = DK_User::select('id')->whereIn('superior_id',$subordinates)->get()->pluck('id')->toArray();
//            $subordinates_list = array_merge($subordinates_subordinates,$subordinates);
//            $subordinates_list[] = $me->id;
//            $query->whereIn('creator_id',$subordinates_list);
            $district_staff_list = DK_User::select('id')->where('department_district_id',$me->department_district_id)->get()->pluck('id')->toArray();
            $query->whereIn('creator_id',$district_staff_list);
        }
        // 客服主管
        if($me->user_type == 84)
        {
//            $subordinates = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $subordinates[] = $me->id;
//            $query->whereIn('creator_id',$subordinates);
            $group_staff_list = DK_User::select('id')->where('department_group_id',$me->department_group_id)->get()->pluck('id')->toArray();
            $query->whereIn('creator_id',$group_staff_list);
        }
        // 客服
        if($me->user_type == 88)
        {
            $query->where('creator_id', $me->id);
        }
        // 质检经理
        if($me->user_type == 71)
        {
            // 一对一
//            $subordinates = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $query->where('is_published','<>',0)->whereHas('project_er', function ($query) use ($subordinates) {
//                $query->whereIn('user_id', $subordinates);
//            });
            // 多对对
            $subordinates = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
            $project_list = DK_Pivot_User_Project::select('project_id')->whereIn('user_id',$subordinates)->get()->pluck('project_id')->toArray();
            $query->where('is_published','<>',0)->whereIn('project_id', $project_list);
            if($me->department_district_id != 0)
            {
                $query->where('department_district_id',$me->department_district_id);
            }
        }
        // 质检员
        if($me->user_type == 77)
        {
            // 一对一
//            $query->where('is_published','<>',0)->whereHas('project_er', function ($query) use ($me) {
//                $query->where('user_id', $me->id);
//            });
            // 多对多
            $project_list = DK_Pivot_User_Project::select('project_id')->where('user_id',$me->id)->get()->pluck('project_id')->toArray();
            $query->where('is_published','<>',0)->whereIn('project_id', $project_list);
            if($me->department_district_id != 0)
            {
                $query->where('department_district_id',$me->department_district_id);
            }
        }

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['client_name'])) $query->where('client_name', $post_data['client_name']);
        if(!empty($post_data['client_phone'])) $query->where('client_phone', 'like', "%{$post_data['client_phone']}");

        if(!empty($post_data['assign'])) $query->whereDate(DB::Raw("from_unixtime(published_at)"), $post_data['assign']);
        if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '>=', $post_data['assign_start']);
        if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '<=', $post_data['assign_ended']);


        if(!empty($post_data['delivered_date'])) $query->whereDate(DB::Raw("from_unixtime(delivered_at)"), $post_data['delivered_date']);


//        if(!empty($post_data['district_city'])) $query->where('location_city', $post_data['district_city']);
//        if(!empty($post_data['district_district'])) $query->where('location_district', $post_data['district_district']);
        if(!empty($post_data['district_city']))
        {
            if(!in_array($post_data['district_city'],[-1]))
            {
                $query->where('location_city', $post_data['district_city']);
            }
        }
        if(!empty($post_data['district_district']))
        {
            if(!in_array($post_data['district_district'],[-1]))
            {
//                $query->where('location_district', $post_data['district_district']);
                $query->whereIn('location_district', $post_data['district_district']);
            }
        }


        // 员工
        if(!empty($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1,0]))
            {
                $query->where('creator_id', $post_data['staff']);
            }
        }


        // 部门-大区
//        if(!empty($post_data['department_district']))
//        {
//            if(!in_array($post_data['department_district'],[-1,0]))
//            {
//                $query->where('department_district_id', $post_data['department_district']);
//            }
//        }
        if(!empty($post_data['department_district']))
        {
            if(count($post_data['department_district']))
            {
                $query->whereIn('department_district_id', $post_data['department_district']);
            }
        }


        // 客户
        if(isset($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1]))
            {
                $query->where('client_id', $post_data['client']);
            }
        }

        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1]))
            {
                $query->where('project_id', $post_data['project']);
            }
        }


        // 工单类型 [自有|空单|配货|调车]
        if(isset($post_data['order_type']))
        {
            if(!in_array($post_data['order_type'],[-1]))
            {
                $query->where('car_owner_type', $post_data['order_type']);
            }
        }


        // 创建方式 [人工|导入|api]
        if(isset($post_data['created_type']))
        {
            if(!in_array($post_data['created_type'],[-1,'-1']))
            {
                $query->where('created_type', $post_data['created_type']);
            }
        }



        // 是否+V
        if(!empty($post_data['is_wx']))
        {
            if(!in_array($post_data['is_wx'],[-1]))
            {
                $query->where('is_wx', $post_data['is_wx']);
            }
        }

        // 审核状态
        if(!empty($post_data['inspected_status']))
        {
            $inspected_status = $post_data['inspected_status'];
            if(in_array($inspected_status,['待发布','待审核','已审核']))
            {
                if($inspected_status == '待发布')
                {
                    $query->where('is_published', 0);
                }
                else if($inspected_status == '待审核')
                {
                    $query->where('is_published', 1)->whereIn('inspected_status', [0,9]);
                }
                else if($inspected_status == '已审核')
                {
                    $query->where('inspected_status', 1);
                }
            }
        }
        // 审核结果
        if(!empty($post_data['inspected_result']))
        {
//            $inspected_result = $post_data['inspected_result'];
//            if(in_array($inspected_result,config('info.inspected_result')))
//            {
//                $query->where('inspected_result', $inspected_result);
//            }
            if(count($post_data['inspected_result']))
            {
                $query->whereIn('inspected_result', $post_data['inspected_result']);
            }
        }

        // 交付状态
        if(!empty($post_data['delivered_status']))
        {
            $delivered_status = $post_data['delivered_status'];
            if(in_array($delivered_status,['待交付','已交付','已操作','已处理']))
            {
                if($delivered_status == '待交付')
                {
                    $query->where('delivered_status', 0);
                }
                else if($delivered_status == '已交付')
                {
                    $query->where('delivered_status', 1);
                }
                else if($delivered_status == '已操作')
                {
                    $query->where('delivered_status', 1);
                }
                else if($delivered_status == '已处理')
                {
                    $query->where('delivered_status', 1);
                }
            }
        }
        // 交付结果
        if(!empty($post_data['delivered_result']))
        {
            if(count($post_data['delivered_result']))
            {
                $query->whereIn('delivered_result', $post_data['delivered_result']);
            }
        }





        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 10;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);

            if($v->creator_id == $me->id)
            {
                $list[$k]->is_me = 1;
                $v->is_me = 1;
            }
            else
            {
                $list[$k]->is_me = 0;
                $v->is_me = 0;
            }

            if(in_array($me->user_type,[0,1,9,11]))
            {
            }
            else if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                if(($v->published_at > 0) && (($time - $v->published_at) > 86400))
                {
                    $client_phone = $v->client_phone;
                    $v->client_phone = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }
            else if(in_array($me->user_type,[41,81,84,88]))
            {
                $time = time();
                if(!$v->is_me || (($v->published_at > 0) && (($time - $v->published_at) > 86400)))
                {
//                    $len = strlen($client_phone);  // 字符串长度
                    $client_phone = $v->client_phone;
                    if(is_numeric($client_phone))
                    {
                        $v->client_phone = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                    }
                }
            }

        }
//        dd($list->toArray());


        if($me->id > 10000)
        {
            $record["creator_id"] = $me->id;
            $record["record_category"] = 1; // record_category=1 browse/share
            $record["record_type"] = 1; // record_type=1 browse
            $record["page_type"] = 1; // page_type=1 default platform
            $record["page_module"] = 2; // page_module=2 other
            $record["page_num"] = ($skip / $limit) + 1;
            $record["open"] = "order-list";
            $record["from"] = request('from',NULL);
            $this->record_for_user_visit($record);
        }


        return datatable_response($list, $draw, $total);
    }


    // 【工单】【修改记录】返回-列表-视图
    public function view_item_order_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_order_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.order-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【工单】【修改记录】返回-列表-数据
    public function get_item_order_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Record::select('*')
            ->with([
                'creator',
                'before_client_er'=>function($query) { $query->select('id','username'); },
                'after_client_er'=>function($query) { $query->select('id','username'); },
                'before_project_er'=>function($query) { $query->select('id','name'); },
                'after_project_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where(['order_id'=>$id]);
//            ->where(['record_object'=>21,'operate_object'=>61,'item_id'=>$id]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!in_array($me->user_type,[0,1,9,11,61,66]))
        {
            $query->whereNotIn('operate_category',[96]);
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【工单】返回-导入-视图
    public function view_item_order_import()
    {
        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9])) return view(env('TEMPLATE_ROOT_FRONT').'errors.404');

        $operate_category = 'item';
        $operate_type = 'item';
        $operate_type_text = '工单';
        $title_text = '导入'.$operate_type_text;
        $list_text = $operate_type_text.'列表';
        $list_link = '/item/order-list-for-all';

        $return['operate'] = 'create';
        $return['operate_id'] = 0;
        $return['operate_category'] = $operate_category;
        $return['operate_type'] = $operate_type;
        $return['operate_type_text'] = $operate_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.order-import';
        return view($view_blade)->with($return);
    }
    // 【工单】保存-导入-数据
    public function operate_item_order_import_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required',
            'project_id.required' => '请填选择项目！',
            'project_id.numeric' => '选择项目参数有误！',
            'project_id.min' => '请填选择项目！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'project_id' => 'required|numeric|min:1',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,81,84,88])) return response_error([],"你没有操作权限！");

        $me->load(['department_district_er','department_group_er']);
        $department_district_id = $me->department_district_id;
        $department_group_id = $me->department_group_id;
        if($me->department_district_er) $department_manager_id = $me->department_district_er->leader_id;
        else $department_manager_id = 0;
        if($me->department_group_er) $department_supervisor_id = $me->department_group_er->leader_id;
        else $department_supervisor_id = 0;

        $project_id = $post_data['project_id'];

        // 单文件
        if(!empty($post_data["excel-file"]))
        {

//            $result = upload_storage($post_data["attachment"]);
//            $result = upload_storage($post_data["attachment"], null, null, 'assign');
            $result = upload_file_storage($post_data["excel-file"],null,'dk/unique/attachment','');
            if($result["result"])
            {
//                $mine->attachment_name = $result["name"];
//                $mine->attachment_src = $result["local"];
//                $mine->save();
                $attachment_file = storage_resource_path($result["local"]);

                $data = Excel::load($attachment_file, function($reader) {

//                  $reader->takeColumns(3);
                    $reader->limitColumns(10);

//                  $reader->takeRows(1000);
                    $reader->limitRows(5000);

//                  $reader->ignoreEmpty();

//                  $data = $reader->all();
//                  $data = $reader->toArray();

                })->get()->toArray();

                $order_data = [];
                foreach($data as $key => $value)
                {
                    if(is_numeric($value['client_phone']))
                    {
                        $temp_date['client_name'] = $value['client_name'];
                        $temp_date['client_phone'] = intval($value['client_phone']);
                        $temp_date['wx_id'] = $value['wx_id'];
                        if(!empty($value['wx_id'])) $temp_date['is_wx'] = 1;
                        else $temp_date['is_wx'] = 0;
                        $temp_date['client_intention'] = $value['client_intention'];
                        $temp_date['location_city'] = $value['location_city'];
                        $temp_date['location_district'] = $value['location_district'];
                        $temp_date['teeth_count'] = $value['teeth_count'];
                        $temp_date['recording_address'] = $value['recording_address'];
                        $temp_date['description'] = $value['description'];

                        $order_data[] = $temp_date;
                    }
                }

                // 启动数据库事务
                DB::beginTransaction();
                try
                {

                    foreach($order_data as $key => $value)
                    {
                        $order = new DK_Order;

                        $is_repeat = DK_Order::where(['project_id'=>$project_id,'client_phone'=>$value['client_phone']])
                            ->where('is_published','>',0)->count("*");
                        $order->is_repeat = $is_repeat;

                        $order->project_id = $project_id;
                        $order->creator_id = $me->id;
                        $order->created_type = 9;
//                        $order->inspected_status = 1;
//                        $order->inspected_result = '通过';
                        $order->client_name = $value['client_name'];
                        $order->client_phone = $value['client_phone'];
                        $order->wx_id = $value['wx_id'];
                        $order->is_wx = $value['is_wx'];

                        $order->client_intention = $value['client_intention'];

                        $order->location_city = $value['location_city'];
                        $order->location_district = $value['location_district'];

                        $order->teeth_count = $value['teeth_count'];
                        $order->recording_address = $value['recording_address'];
                        $order->description = $value['description'];

                        $order->department_district_id = $department_district_id;
                        $order->department_group_id = $department_group_id;
                        $order->department_manager_id = $department_manager_id;
                        $order->department_supervisor_id = $department_supervisor_id;


                        $order->is_published = 1;
                        $order->published_at = time();

                        $bool = $order->save();
                        if(!$bool) throw new Exception("insert--order--fail");
                    }

                    DB::commit();
                    return response_success(['count'=>count($order_data)]);
                }
                catch (Exception $e)
                {
                    DB::rollback();
                    $msg = '操作失败，请重试！';
                    $msg = $e->getMessage();
//                    exit($e->getMessage());
                    return response_fail([],$msg);
                }
            }
            else return response_error([],"upload--attachment--fail");
        }
        else return response_error([],"清选择Excel文件！");




        // 多文件
//        dd($post_data["multiple-excel-file"]);
        $count = 0;
        $multiple_files = [];
        if(!empty($post_data["multiple-excel-file"]))
        {
            // 添加图片
            foreach ($post_data["multiple-excel-file"] as $n => $f)
            {
                if(!empty($f))
                {
                    $result = upload_file_storage($f,null,'dk/unique/attachment','');
                    if($result["result"])
                    {
                        $attachment_file = storage_resource_path($result["local"]);
                        $data = Excel::load($attachment_file, function($reader) {

                            $reader->limitColumns(10);
                            $reader->limitRows(5000);

                        })->get()->toArray();

                        $order_data = [];
                        foreach($data as $key => $value)
                        {
                            if(is_numeric($value['client_phone']))
                            {
                                $temp_date['client_name'] = $value['client_name'];
                                $temp_date['client_phone'] = intval($value['client_phone']);
                                $temp_date['wx_id'] = $value['wx_id'];
                                if(!empty($value['wx_id'])) $temp_date['is_wx'] = 1;
                                else $temp_date['is_wx'] = 0;
                                $temp_date['client_intention'] = $value['client_intention'];
                                $temp_date['location_city'] = $value['location_city'];
                                $temp_date['location_district'] = $value['location_district'];
                                $temp_date['teeth_count'] = $value['teeth_count'];
                                $temp_date['recording_address'] = $value['recording_address'];
                                $temp_date['description'] = $value['description'];

                                $order_data[] = $temp_date;
                            }
                        }

                        // 启动数据库事务
                        DB::beginTransaction();
                        try
                        {

                            foreach($order_data as $key => $value)
                            {
                                $order = new DK_Order;

                                $is_repeat = DK_Order::where(['project_id'=>$project_id,'client_phone'=>$value['client_phone']])
                                    ->where('is_published','>',0)->count("*");
                                $order->is_repeat = $is_repeat;

                                $order->project_id = $project_id;
                                $order->creator_id = $me->id;
                                $order->created_type = 9;
//                                $order->inspected_status = 1;
//                                $order->inspected_result = '通过';
                                $order->client_name = $value['client_name'];
                                $order->client_phone = $value['client_phone'];
                                $order->wx_id = $value['wx_id'];
                                $order->is_wx = $value['is_wx'];

                                $order->client_intention = $value['client_intention'];

                                $order->location_city = $value['location_city'];
                                $order->location_district = $value['location_district'];

                                $order->teeth_count = $value['teeth_count'];
                                $order->recording_address = $value['recording_address'];
                                $order->description = $value['description'];

                                $order->department_district_id = $department_district_id;
                                $order->department_group_id = $department_group_id;
                                $order->department_manager_id = $department_manager_id;
                                $order->department_supervisor_id = $department_supervisor_id;

                                $order->is_published = 1;
                                $order->published_at = time();

                                $bool = $order->save();
                                if(!$bool) throw new Exception("insert--order--fail");
                            }

                            DB::commit();
                            $count += count($order_data);
                        }
                        catch (Exception $e)
                        {
                            DB::rollback();
                            $msg = '操作失败，请重试！';
                            $msg = $e->getMessage();
                            return response_fail([],$msg);
                        }

                    }
                    else return response_error([],"upload--attachment--fail");
                }

            }

            return response_success(['count'=>$count]);
        }

    }


    // 【工单】返回-导入-视图
    public function view_item_order_import_for_admin()
    {
        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9])) return view(env('TEMPLATE_ROOT_FRONT').'errors.404');

        $operate_category = 'item';
        $operate_type = 'item';
        $operate_type_text = '工单';
        $title_text = '管理员导入'.$operate_type_text;
        $list_text = $operate_type_text.'列表';
        $list_link = '/item/order-list-for-all';

        $return['operate'] = 'create';
        $return['operate_id'] = 0;
        $return['operate_category'] = $operate_category;
        $return['operate_type'] = $operate_type;
        $return['operate_type_text'] = $operate_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.order-import-for-admin';
        return view($view_blade)->with($return);
    }
    // 【工单】保存-导入-数据
    public function operate_item_order_import_for_admin_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required',
            'project_id.required' => '请填选择项目！',
            'project_id.numeric' => '选择项目参数有误！',
            'project_id.min' => '请填选择项目！',
            'client_id.required' => '请填选择客户！',
            'client_id.numeric' => '选择客户参数有误！',
            'client_id.min' => '请填选择客户！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'project_id' => 'required|numeric|min:0',
            'client_id' => 'required|numeric|min:0',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        $project_id = $post_data['project_id'];
        $client_id = $post_data['client_id'];
        if($project_id > 0 || $client_id > 0)
        {
        }
        else return response_error([],"项目和客户必须选择一个！");

        // 单文件
        if(!empty($post_data["excel-file"]))
        {

//            $result = upload_storage($post_data["attachment"]);
//            $result = upload_storage($post_data["attachment"], null, null, 'assign');
            $result = upload_file_storage($post_data["excel-file"],null,'dk/unique/attachment','');
            if($result["result"])
            {
//                $mine->attachment_name = $result["name"];
//                $mine->attachment_src = $result["local"];
//                $mine->save();
                $attachment_file = storage_resource_path($result["local"]);

                $data = Excel::load($attachment_file, function($reader) {

//                  $reader->takeColumns(3);
                    $reader->limitColumns(1);

//                  $reader->takeRows(1000);
                    $reader->limitRows(20001);

//                  $reader->ignoreEmpty();

//                  $data = $reader->all();
//                  $data = $reader->toArray();

                })->get()->toArray();

                $order_data = [];
                foreach($data as $key => $value)
                {
                    if($value['client_phone'])
                    {
//                        $temp_date['client_phone'] = intval($value['client_phone']);
                        $temp_date['client_phone'] = $value['client_phone'];
                        $order_data[] = $temp_date;
                    }
                }

                // 启动数据库事务
                DB::beginTransaction();
                try
                {

                    foreach($order_data as $key => $value)
                    {
                        $order = new DK_Order;

                        $order->client_id = $client_id;
                        $order->project_id = $project_id;
                        $order->creator_id = $me->id;
                        $order->created_type = 9;
                        $order->is_published = 1;
                        $order->inspected_status = 1;
                        $order->inspected_result = '通过';
                        $order->client_phone = $value['client_phone'];

                        $bool = $order->save();
                        if(!$bool) throw new Exception("insert--order--fail");
                    }

                    DB::commit();
                    return response_success(['count'=>count($order_data)]);
                }
                catch (Exception $e)
                {
                    DB::rollback();
                    $msg = '操作失败，请重试！';
                    $msg = $e->getMessage();
//                    exit($e->getMessage());
                    return response_fail([],$msg);
                }
            }
            else return response_error([],"upload--attachment--fail");
        }
        else return response_error([],"清选择Excel文件！");




        // 多文件
//        dd($post_data["multiple-excel-file"]);
        $count = 0;
        $multiple_files = [];
        if(!empty($post_data["multiple-excel-file"]))
        {
            // 添加图片
            foreach ($post_data["multiple-excel-file"] as $n => $f)
            {
                if(!empty($f))
                {
                    $result = upload_file_storage($f,null,'dk/unique/attachment','');
                    if($result["result"])
                    {
                        $attachment_file = storage_resource_path($result["local"]);
                        $data = Excel::load($attachment_file, function($reader) {

                            $reader->limitColumns(1);
                            $reader->limitRows(5001);

                        })->get()->toArray();

                        $order_data = [];
                        foreach($data as $key => $value)
                        {
                            if($value['client_phone'])
                            {
                                $temp_date['client_phone'] = intval($value['client_phone']);
                                $order_data[] = $temp_date;
                            }
                        }

                        // 启动数据库事务
                        DB::beginTransaction();
                        try
                        {

                            foreach($order_data as $key => $value)
                            {
                                $order = new DK_Order;

                                $order->project_id = $project_id;
                                $order->creator_id = $me->id;
                                $order->created_type = 9;
                                $order->is_published = 1;
                                $order->inspected_status = 1;
                                $order->inspected_result = '通过';
                                $order->client_phone = $value['client_phone'];

                                $bool = $order->save();
                                if(!$bool) throw new Exception("insert--order--fail");
                            }

                            DB::commit();
                            $count += count($order_data);
                        }
                        catch (Exception $e)
                        {
                            DB::rollback();
                            $msg = '操作失败，请重试！';
                            $msg = $e->getMessage();
                            return response_fail([],$msg);
                        }

                    }
                    else return response_error([],"upload--attachment--fail");
                }

            }

            return response_success(['count'=>$count]);
        }

    }


    // 【工单】返回-添加-视图
    public function view_item_order_create()
    {
        $this->get_me();

        $item_type = 'item';
        $item_type_text = '工单';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/order-list-for-all';

        $return['operate'] = 'create';
        $return['operate_id'] = 0;
        $return['category'] = 'item';
        $return['type'] = $item_type;
        $return['item_type_text'] = $item_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;


        $district_city_list = DK_District::select('id','district_city')->whereIn('district_status',[1])->get();
        $return['district_city_list'] = $district_city_list;

//        $district_district_list = DK_District::select('district_district')->where('district_city',$post_data['district_city'])->whereIn('district_status',[1])->get();
//        if(count($district_district_list) > 0)
//        {
//            $district_district_array = explode("-",$district_district_list[0]->district_district);
//            $return['district_district_list'] = $district_district_array;
//        }
//        else
//        {
//            $return['district_district_list'] = [];
//        }

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.order-edit';
        return view($view_blade)->with($return);
    }
    // 【工单】返回-编辑-视图
    public function view_item_order_edit()
    {
        $this->get_me();
        $me = $this->me;

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.order-edit';

        $item_type = 'item';
        $item_type_text = '工单';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/order-list-for-all';

        $return['operate'] = 'edit';
        $return['operate_id'] = $id;
        $return['category'] = 'item';
        $return['type'] = $item_type;
        $return['item_type_text'] = $item_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;


        $district_city_list = DK_District::select('id','district_city')->whereIn('district_status',[1])->get();
        $return['district_city_list'] = $district_city_list;


        if($id == 0)
        {
            $return['operate'] = 'create';
            $return['operate_id'] = 0;

            return view($view_blade)->with($return);
        }
        else
        {
            $mine = DK_Order::find($id);
            if($mine)
            {
//                if($mine->deleted_at) return view(env('TEMPLATE_DK_ADMIN').'entrance.errors.404');
//                else
                {
//                    $mine->custom = json_decode($mine->custom);
//                    $mine->custom2 = json_decode($mine->custom2);
//                    $mine->custom3 = json_decode($mine->custom3);

                    $district_district_list = DK_District::select('district_district')->where('district_city',$mine->location_city)->whereIn('district_status',[1])->get();
                    if(count($district_district_list) > 0)
                    {
                        $district_district_array = explode("-",$district_district_list[0]->district_district);
                        $return['district_district_list'] = $district_district_array;
                    }
                    else
                    {
                        $return['district_district_list'] = [];
                    }

                    $return['data'] = $mine;

                    return view($view_blade)->with($return);
                }
            }
            else return view(env('TEMPLATE_DK_ADMIN').'entrance.errors.404');
        }
    }
    // 【工单】保存数据
    public function operate_item_order_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'project_id.required' => '请填选择项目！',
            'project_id.numeric' => '选择项目参数有误！',
            'project_id.min' => '请填选择项目！',
            'client_name.required' => '请填写客户信息！',
            'client_phone.required' => '请填写客户电话！',
            'client_phone.numeric' => '客户电话格式有误！',
            'client_intention.required' => '请选择客户意向！',
//            'location_city.required' => '请选择城市！',
//            'location_district.required' => '请选择行政区！',
            'description.required' => '请输入通话小结！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'project_id' => 'required|numeric|min:1',
            'client_name' => 'required',
            'client_phone' => 'required|numeric',
            'client_intention' => 'required',
//            'location_city' => 'required',
//            'location_district' => 'required',
            'description' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $location_city = $post_data["location_city"];
        $location_district = $post_data["location_district"];
        $custom_location_city = $post_data["custom_location_city"];
        $custom_location_district = $post_data["custom_location_district"];

        if(!empty($location_city) && !empty($location_district))
        {
        }
        else
        {
            if(!empty($custom_location_city) && !empty($custom_location_district))
            {
            }
            else return response_error([],"请选择城市和区域！");
        }
//        dd($custom_location_city.$custom_location_district);


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,81,84,88])) return response_error([],"你没有操作权限！");

        $me->load(['department_district_er','department_group_er']);

        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $mine = new DK_Order;
            $post_data["item_category"] = 1;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;

//            $is_repeat = DK_Order::where('client_phone',$post_data['client_phone'])->where('project_id',$post_data['project_id'])->count("*");
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_Order::find($operate_id);
            if(!$mine) return response_error([],"该工单不存在，刷新页面重试！");

            if(in_array($me->user_type,[84,88]) && $mine->creator_id != $me->id) return response_error([],"该【工单】不是你的，你不能操作！");

//            $is_repeat = DK_Order::where('client_phone',$post_data['client_phone'])->where('project_id',$post_data['project_id'])->where('id','<>',$operate_id)->count("*");
        }
        else return response_error([],"参数有误！");

//        $post_data['is_repeat'] = $is_repeat;

        if(!empty($post_data['project_id']))
        {
            $project = DK_Project::find($post_data['project_id']);
            if(!$project) return response_error([],"选择【项目】不存在，刷新页面重试！");
        }



        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }

            // 指派日期
            if(!empty($post_data['assign_date']))
            {
                $post_data['assign_time'] = strtotime($post_data['assign_date']);
            }
//            else $post_data['assign_time'] = 0;



            $mine_data = $post_data;
            $mine_data['department_district_id'] = $me->department_district_id;
            $mine_data['department_group_id'] = $me->department_group_id;
            if($me->department_district_er) $mine_data['department_manager_id'] = $me->department_district_er->leader_id;
            if($me->department_group_er) $mine_data['department_supervisor_id'] = $me->department_group_er->leader_id;

            if(!empty($custom_location_city) && !empty($custom_location_district))
            {
                $mine_data['location_city'] = $custom_location_city;
                $mine_data['location_district'] = $custom_location_district;
            }

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['operate_category']);
            unset($mine_data['operate_type']);

            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
//                if(!empty($post_data['circle_id']))
//                {
//                    $circle_data['order_id'] = $mine->id;
//                    $circle_data['creator_id'] = $me->id;
//                    $circle->pivot_order_list()->attach($circle_data);  //
////                    $circle->pivot_order_list()->syncWithoutDetaching($circle_data);  //
//                }
            }
            else throw new Exception("insert--order--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【工单】【文本】修改-文本-类型
    public function operate_item_order_info_text_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-order-info-text-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["order_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该【工单】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;

        if($column_key == "client_phone")
        {
            if(!in_array($me->user_type,[0,1,11,61,66,71,77,81,84,88])) return response_error([],"你没有操作权限！");
        }
        else if($column_key == "inspected_description")
        {
            if(!in_array($me->user_type,[0,1,11,61,66,71,77])) return response_error([],"你没有操作权限！");
        }
        else if($column_key == "description")
        {
            if(!in_array($me->user_type,[0,1,11,61,66,71,77,81,84,88])) return response_error([],"你没有操作权限！");
        }
        else
        {
            if(!in_array($me->user_type,[0,1,11,61,66,81,84,88])) return response_error([],"你没有操作权限！");
        }

        if(in_array($me->user_type,[84,88]) && $item->creator_id != $me->id) return response_error([],"该【工单】不是你的，你不能操作！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $is_repeat = 0;
            if($column_key == "client_phone")
            {
                $project_id = $item->project_id;
                $client_phone = $item->client_phone;

                $is_repeat = DK_Order::where(['project_id'=>$project_id,'client_phone'=>$column_value])
                    ->where('id','<>',$id)->where('is_published','>',0)->count("*");
//

                $item->is_repeat = $is_repeat;
//                dd($item->is_repeat);
            }

            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $column_value;

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success(['is_repeat'=>$is_repeat]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单】【时间】修改-时间-类型
    public function operate_item_order_info_time_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-order-info-time-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["order_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该工单不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];
        $time_type = $post_data["time_type"];

        $before = $item->$column_key;


        // 应出发时间
        if($column_key == "should_departure_time" && $item->should_arrival_time)
        {
            if(strtotime($column_value) >= $item->should_arrival_time)
            {
                return response_error([],"应出发时间不能超过应到达时间！");
            }
        }
        // 应到达时间
        if($column_key == "should_arrival_time" && $item->should_departure_time)
        {
            if(strtotime($column_value) <= $item->should_departure_time)
            {
                return response_error([],"应到达时间不能在应出发时间之前！");
            }
        }

        // 实际出发时间
        if($column_key == "actual_departure_time")
        {
            if(strtotime($column_value) > time()) return response_error([],"时间不能超过当前！");
            if($item->actual_arrival_time)
            {
                if(strtotime($column_value) >= $item->actual_arrival_time)
                {
                    return response_error([],"出发时间不能超过到达时间！");
                }
            }
        }
        // 实际到达时间
        if($column_key == "actual_arrival_time")
        {
            if(strtotime($column_value) > time()) return response_error([],"时间不能超过当前！");
            if($item->actual_departure_time)
            {
                if(strtotime($column_value) <= $item->actual_departure_time)
                {
                    return response_error([],"到达时间不能在出发时间之前！");
                }
            }
            else return response_error([],"请先填写出发时间！");
        }

        // 经停到达时间
        if($column_key == "stopover_arrival_time")
        {
            if(!$item->stopover_place) return response_error([],"没有经停点！");
            if(strtotime($column_value) > time()) return response_error([],"时间不能超过当前！");
            if($item->actual_departure_time)
            {
                if(strtotime($column_value) <= $item->actual_departure_time)
                {
                    return response_error([],"经停点-到达时间不能在（实际）出发时间之前！");
                }
            }
            else return response_error([],"请先填写（实际）出发时间！");
        }
        // 经停出发时间
        if($column_key == "stopover_departure_time")
        {
            if(!$item->stopover_place) return response_error([],"没有经停点！");
            if(strtotime($column_value) > time()) return response_error([],"时间不能超过当前！");
            if($item->stopover_arrival_time)
            {
                if(strtotime($column_value) <= $item->stopover_arrival_time)
                {
                    return response_error([],"（经停点）出发时间不能在（经停点）到达时间之前！");
                }
            }
            else return response_error([],"请先填写（经停点）到达时间！");
        }


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->$column_key = strtotime($column_value);
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_type"] = $time_type;
                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = strtotime($column_value);

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单】【选项】修改-radio-select-[option]-类型
    public function operate_item_order_info_option_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-order-info-option-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["order_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该【工单】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;
        $after = $column_value;

        if($column_key == "location_city")
        {
            if(!in_array($me->user_type,[0,1,11,61,66,71,77,81,84,88])) return response_error([],"你没有操作权限！");
        }
        else
        {
            if(!in_array($me->user_type,[0,1,11,61,66,71,77,81,84,88])) return response_error([],"你没有操作权限！");
        }

        if(in_array($me->user_type,['client_id','project_id']))
        {
            if(in_array($column_value,["-1",-1])) return response_error([],"选择有误！");
        }


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($column_key == "project_id")
            {
                if($column_value == 0)
                {
                }
                else
                {
                    $project = DK_Project::withTrashed()->find($column_value);
                    if(!$project) throw new Exception("该【项目】不存在，刷新页面重试！");

                    $project_id = $item->project_id;
                    $client_phone = $item->client_phone;

                    $is_repeat = DK_Order::where(['project_id'=>$column_value,'client_phone'=>$client_phone])
                        ->where('id','<>',$id)->where('is_published','>',0)->count("*");
                    $item->is_repeat = $is_repeat;
                }
            }
            else if($column_key == "location_city")
            {
                $column_key2 = $post_data["column_key2"];
                $column_value2 = $post_data["column_value2"];

                $before = $item->location_city.' - '.$item->location_district;
                $after = $column_value.' - '.$column_value2;

                $item->$column_key2 = $column_value2;
            }

            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("order--update--fail");
            else
            {


                // 需要记录(已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $after;

                    if(in_array($column_key,['client_id','project_id']))
                    {
                        $record_data["before_id"] = $before;
                        $record_data["after_id"] = $column_value;
                    }



                    if($column_key == 'client_id')
                    {
                        $record_data["before_client_id"] = $before;
                        $record_data["after_client_id"] = $column_value;
                    }
                    else if($column_key == 'project_id')
                    {
                        $record_data["before_project_id"] = $before;
                        $record_data["after_project_id"] = $column_value;
                    }

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单】【附件】添加
    public function operate_item_order_info_attachment_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-order-attachment-set') return response_error([],"参数[operate]有误！");
        $order_id = $post_data["order_id"];
        if(intval($order_id) !== 0 && !$order_id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($order_id);
        if(!$item) return response_error([],"该【工单】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");
        if(!in_array($me->user_type,[0,1,11,81,82,88])) return response_error([],"你没有操作权限！");

//        $operate_type = $post_data["operate_type"];
//        $column_key = $post_data["column_key"];
//        $column_value = $post_data["column_value"];


//        dd($post_data);
        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            // 多图
            $multiple_images = [];
            if(!empty($post_data["multiple_images"][0]))
            {
                // 添加图片
                foreach ($post_data["multiple_images"] as $n => $f)
                {
                    if(!empty($f))
                    {
                        $result = upload_img_storage($f,'','dk/attachment','');
                        if($result["result"])
                        {
                            $attachment = new YH_Attachment;

                            $attachment_data["operate_object"] = 71;
                            $attachment_data['order_id'] = $order_id;
                            $attachment_data['item_id'] = $order_id;
                            $attachment_data['attachment_name'] = $post_data["attachment_name"];
                            $attachment_data['attachment_src'] = $result["local"];
                            $bool = $attachment->fill($attachment_data)->save();
                            if($bool)
                            {
                                $record = new DK_Record;

                                $record_data["ip"] = Get_IP();
                                $record_data["record_object"] = 21;
                                $record_data["record_category"] = 11;
                                $record_data["record_type"] = 1;
                                $record_data["creator_id"] = $me->id;
                                $record_data["order_id"] = $order_id;
                                $record_data["operate_object"] = 71;
                                $record_data["operate_category"] = 71;
                                $record_data["operate_type"] = 1;

                                $record_data["column_name"] = 'attachment';
                                $record_data["after"] = $attachment_data['attachment_src'];

                                $bool_1 = $record->fill($record_data)->save();
                                if($bool_1)
                                {
                                }
                                else throw new Exception("insert--record--fail");
                            }
                            else throw new Exception("insert--attachment--fail");
                        }
                        else throw new Exception("upload--attachment--file--fail");
                    }
                }
            }


            // 单图
            if(!empty($post_data["attachment_file"]))
            {
                $attachment = new YH_Attachment;

//                $result = upload_storage($post_data["portrait"]);
//                $result = upload_storage($post_data["portrait"], null, null, 'assign');
                $result = upload_img_storage($post_data["attachment_file"],'','dk/attachment','');
                if($result["result"])
                {
                    $attachment_data["operate_object"] = 71;
                    $attachment_data['order_id'] = $order_id;
                    $attachment_data['item_id'] = $order_id;
                    $attachment_data['attachment_name'] = $post_data["attachment_name"];
                    $attachment_data['attachment_src'] = $result["local"];
                    $bool = $attachment->fill($attachment_data)->save();
                    if($bool)
                    {
                        $record = new DK_Record;

                        $record_data["ip"] = Get_IP();
                        $record_data["record_object"] = 21;
                        $record_data["record_category"] = 11;
                        $record_data["record_type"] = 1;
                        $record_data["creator_id"] = $me->id;
                        $record_data["order_id"] = $order_id;
                        $record_data["operate_object"] = 71;
                        $record_data["operate_category"] = 71;
                        $record_data["operate_type"] = 1;

                        $record_data["column_name"] = 'attachment';
                        $record_data["after"] = $attachment_data['attachment_src'];

                        $bool_1 = $record->fill($record_data)->save();
                        if($bool_1)
                        {
                        }
                        else throw new Exception("insert--record--fail");
                    }
                    else throw new Exception("insert--attachment--fail");
                }
                else throw new Exception("upload--attachment--file--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单】【附件】删除
    public function operate_item_order_info_attachment_delete($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-attachment-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = YH_Attachment::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【附件】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,82,88])) return response_error([],"你没有操作权限！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->delete();  // 普通删除
            if($bool)
            {
                $record = new DK_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $item->order_id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 71;
                $record_data["operate_type"] = 91;

                $record_data["column_name"] = 'attachment';
                $record_data["before"] = $item->attachment_src;

                $bool_1 = $record->fill($record_data)->save();
                if($bool_1)
                {
                }
                else throw new Exception("insert--record--fail");
            }
            else throw new Exception("attachment--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【工单】获取-详情-数据
    public function operate_item_order_get($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["order_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::with(['client_er','car_er','trailer_er'])->withTrashed()->find($id);
        if(!$item) return response_error([],"该工单不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $item->should_departure_time_html = date("Y-m-d H:i", $item->should_departure_time);
        $item->should_arrival_time_html = date("Y-m-d H:i", $item->should_arrival_time);

        $item->is_actual_departure = $item->actual_departure_time ? 1 : 0;
        if($item->is_actual_departure) $item->actual_departure_time_html = date("Y-m-d H:i", $item->actual_departure_time);

        $item->is_actual_arrival = $item->actual_arrival_time ? 1 : 0;
        if($item->is_actual_arrival) $item->actual_arrival_time_html = date("Y-m-d H:i", $item->actual_arrival_time);

        $item->is_stopover = $item->stopover_place ? 1 : 0;
        if($item->is_stopover)
        {
            $item->is_stopover_arrival = $item->stopover_arrival_time ? 1 : 0;
            if($item->is_stopover_arrival) $item->stopover_arrival_time_html = date("Y-m-d H:i", $item->stopover_arrival_time);

            $item->is_stopover_departure = $item->stopover_departure_time ? 1 : 0;
            if($item->is_stopover_departure) $item->stopover_departure_time_html = date("Y-m-d H:i", $item->stopover_departure_time);
        }

        return response_success($item,"");

    }
    // 【工单】获取-详情-视图
    public function operate_item_order_get_html($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["order_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::with(['client_er','car_er','trailer_er'])->withTrashed()->find($id);
        if(!$item) return response_error([],"该工单不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        if($item->car_owner_type == 1)
        {
            $item->car_owner_type_name = "自有";
            $item->car = $item->car_er ? $item->car_er->name : '';
            $item->trailer = $item->trailer_er ? $item->trailer_er->name : '';
        }
        else if($item->car_owner_type == 21)
        {
            $item->car_owner_type_name = "外请·调车";
            $item->car = $item->outside_car;
            $item->trailer = $item->outside_trailer;
        }
        else if($item->car_owner_type == 41)
        {
            $item->car_owner_type_name = "外配·配货";
            $item->car = $item->car_er ? $item->car_er->name : '';
            $item->trailer = $item->trailer_er ? $item->trailer_er->name : '';
//            $item->car = $item->outside_car;
//            $item->trailer = $item->outside_trailer;
        }

        $item->should_departure_time_html = date("Y-m-d H:i", $item->should_departure_time);
        $item->should_arrival_time_html = date("Y-m-d H:i", $item->should_arrival_time);

        $item->is_actual_departure = $item->actual_departure_time ? 1 : 0;
        if($item->is_actual_departure) $item->actual_departure_time_html = date("Y-m-d H:i", $item->actual_departure_time);

        $item->is_actual_arrival = $item->actual_arrival_time ? 1 : 0;
        if($item->is_actual_arrival) $item->actual_arrival_time_html = date("Y-m-d H:i", $item->actual_arrival_time);

        $item->is_stopover = $item->stopover_place ? 1 : 0;
        if($item->is_stopover)
        {
            $item->is_stopover_arrival = $item->stopover_arrival_time ? 1 : 0;
            if($item->is_stopover_arrival) $item->stopover_arrival_time_html = date("Y-m-d H:i", $item->stopover_arrival_time);

            $item->is_stopover_departure = $item->stopover_departure_time ? 1 : 0;
            if($item->is_stopover_departure) $item->stopover_departure_time_html = date("Y-m-d H:i", $item->stopover_departure_time);
        }


        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.order-info-html';
        $html = view($view_blade)->with(['data'=>$item])->__toString();

        return response_success(['html'=>$html],"");

    }
    // 【工单】获取-附件-视图
    public function operate_item_order_get_attachment_html($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["order_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::with(['attachment_list'])->withTrashed()->find($id);
        if(!$item) return response_error([],"该工单不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.order-assign-html-for-attachment';
        $html = view($view_blade)->with(['item_list'=>$item->attachment_list])->__toString();

        return response_success(['html'=>$html],"");
    }


    // 【工单】删除
    public function operate_item_order_delete($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-delete') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,84,88])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
        if(in_array($me->user_type,[81,84,88]))
        {
            if($item->creator_id != $me->id) return response_error([],"你没有操作权限！");
        }

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            if($item->is_published == 0 && $item->creator_id == $me->id)
            {
                $item_copy = $item;

                $item->timestamps = false;
//                $bool = $item->forceDelete();  // 永久删除
                $bool = $item->delete();  // 普通删除
                if(!$bool) throw new Exception("item--delete--fail");
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $item_id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 101;
                    $record_data["operate_type"] = 1;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
                }

                DB::commit();
                $this->delete_the_item_files($item_copy);
            }
            else
            {
                $item->timestamps = false;
                $bool = $item->delete();  // 普通删除
//                $bool = $item->forceDelete();  // 永久删除
                if(!$bool) throw new Exception("item--delete--fail");
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $item_id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 101;
                    $record_data["operate_type"] = 1;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
                }

                DB::commit();
            }

            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单】发布
    public function operate_item_order_publish($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-publish') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,81,84,88])) return response_error([],"你没有操作权限！");
        if(in_array($me->user_type,[88]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $project_id = $item->project_id;
        $client_phone = $item->client_phone;

        $is_repeat = DK_Order::where(['project_id'=>$project_id,'client_phone'=>$client_phone])
            ->where('id','<>',$id)->where('is_published','>',0)->count("*");
        if($is_repeat == 0)
        {
            $is_repeat = DK_Pivot_Client_Delivery::where(['project_id'=>$project_id,'client_phone'=>$client_phone])->count("*");
        }

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($item->inspected_status == 1)
            {
                $item->inspected_status = 9;
            }

            $item->is_repeat = $is_repeat;
            $item->is_published = 1;
            $item->published_at = time();
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 11;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单】完成
    public function operate_item_order_complete($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-complete') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,82,88])) return response_error([],"用户类型错误！");
//        if(me->user_type ==88 && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->is_completed = 1;
            $item->completer_id = $me->id;
            $item->completed_at = time();
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 100;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }
            
            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单】弃用
    public function operate_item_order_abandon($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-abandon') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,82,88])) return response_error([],"用户类型错误！");
//        if($item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_status = 97;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 97;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单】复用
    public function operate_item_order_reuse($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-reuse') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,82,88])) return response_error([],"用户类型错误！");
//        if($item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_status = 1;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 98;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单】验证
    public function operate_item_order_verify($post_data)
    {
//        dd($post_data);
        return response_success([]);
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-verify') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,71,81])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->verifier_id = $me->id;
            $item->verified_at = time();
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 91;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单】审核
    public function operate_item_order_inspect($post_data)
    {
//        dd($post_data);
//        return response_success([]);
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-inspect') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66,71,77])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $inspected_result = $post_data["inspected_result"];
        if(!in_array($inspected_result,config('info.inspected_result'))) return response_error([],"审核结果非法！");
        $inspected_description = $post_data["inspected_description"];


        $before = $item->inspected_result;

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->inspector_id = $me->id;
            $item->inspected_status = 1;
            $item->inspected_result = $inspected_result;
            if($inspected_description) $item->inspected_description = $inspected_description;
            $item->inspected_at = time();
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 92;
                $record_data["operate_type"] = 1;
                $record_data["description"] = $inspected_description;

                $record_data["before"] = $before;
                $record_data["after"] = $inspected_result;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

//            if($item->created_type == 99 && in_array($inspected_result,['通过','重复','内部通过']))
            if($item->created_type == 99 && in_array($inspected_result,['通过']))
            {
                $call_record_id = $item->call_record_id;
                $call_record = DK_CC_Call_Record::find($call_record_id);

                if($call_record)
                {
                    $call_statistic_id = $call_record->call_statistic_id;

                    if($call_statistic_id != 0)
                    {
                        $statistic = DK_CC_Call_Statistic::lockForUpdate()->find($call_statistic_id);
                        if($statistic) $statistic->increment('order_count_for_effective');
                    }
                    else
                    {
                        $call_date = date('Y-m-d',strtotime($call_record->startTime));
                        $statistic_where['call_date'] = $call_date;
                        $statistic_where['provinceName'] = $call_record->area;
                        $statistic_where['cityName'] = $call_record->city;
                        $statistic_where['trunkIndex'] = $call_record->trunkIndex;
                        $statistic = DK_CC_Call_Statistic::lockForUpdate()->where($statistic_where)->first();
                        if($statistic) $statistic->increment('order_count_for_effective');
                    }
                }


            }


            DB::commit();

            if(in_array($inspected_result,['通过','重复','内部通过',]))
            {
                if($item->api_is_pushed == 0)
                {
                    $push_data["item"] = "补牙";
                    $push_data["name"] = $item->client_name;
                    $push_data["phone"] = $item->client_phone;
                    $push_data["intention"] = $item->client_intention;
                    $push_data["city"] = $item->location_city;
                    $push_data["area"] = $item->location_district;
                    $push_data["toothcount"] = $item->teeth_count;
                    $push_data["addvx"] = (($item->is_wx) ? '是' : '否');
                    $push_data["vxaccount"] = (($item->wx_id) ? $item->wx_id : '');
                    $push_data["source"] = $item->channel_source;
                    $push_data["description"] = $item->description;

                    $request_result = $this->operate_api_push_order($push_data);

                    if($request_result['success'])
                    {
                        $result = json_decode($request_result['result']);
                        if($result->code == 0)
                        {
                            $item->api_is_pushed = 1;
                            $item->save();
                            return response_success([],"审核成功，推送成功!");
                        }
                        else
                        {
                            return response_error([],"审核成功，推送返回失败!");
                        }
                    }
                    else
                    {
                        return response_error([],"审核成功，接口推送失败!");
                    }
                }
            }


            return response_success([],"审核完成!");
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单】交付
    public function operate_item_order_deliver($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
            'project_id.required' => '请选择项目',
            'client_id.required' => '请选择客户',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
            'project_id' => 'required',
            'client_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-deliver') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");
        $client_phone = $item->client_phone;

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $delivered_result = $post_data["delivered_result"];
        if(!in_array($delivered_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");


        $project_id = $post_data["project_id"];
        $client_id = $post_data["client_id"];
        if(in_array($project_id,['-1','0',-1,0]) && in_array($client_id,['-1','0',-1,0]))
        {
            $project = DK_Project::find($item->project_id);
            if($project->client_id != 0) $delivered_client_id = $project->client_id;
            else $delivered_client_id = 0;
//            dd($delivered_client_id);

            $delivered_project_id = $item->project_id;
        }
        else if(!in_array($project_id,['-1','0',-1,0]) && !in_array($client_id,['-1','0',-1,0]))
        {
            $project = DK_Project::find($project_id);
            if(!$project) return response_error([],"项目不存在！");

            $client = DK_Client::find($client_id);
            if(!$client) return response_error([],"客户不存在！");

            $delivered_project_id = $project_id;
            $delivered_client_id = $client_id;
        }
        else
        {
            return response_error([],"项目和客户必须同时选择或同时不选！");
        }



        // 是否已经分发
        $is_distributed_list = DK_Pivot_Client_Delivery::where(['client_id'=>$client_id,'client_phone'=>$client_phone])->get();
        if(count($is_distributed_list) > 0)
        {
            return response_error([],"该客户已经交付过该号码，不可以重复分发！");
        }

        // 是否已经交付
        $is_order_list = DK_Order::with('project_er')->where(['client_phone'=>$client_phone,'delivered_result'=>'已交付'])->get();
//        dd($is_order_list->toArray());

        if(count($is_order_list) > 0)
        {
            foreach($is_order_list as $o)
            {
                if($o->client_id == $delivered_client_id)
                {
                    return response_error([],"该号码已交付过该客户，不要重复交付！");
                }

//                if($o->project_er->client_id == $delivered_client_id)
//                {
//                    return dd($o->project_er->client_id.'-'.$delivered_client_id);
//                    return response_error([],"该号码已交付过【默认】客户，不要重复交付！");
//                }
            }
        }

        $before = $item->delivered_result;

        $delivered_description = $post_data["delivered_description"];
        $recording_address = $post_data["recording_address"];

        $is_distributive_condition = $post_data["is_distributive_condition"];

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($delivered_client_id != "-1" && $delivered_result == "已交付")
            {
                $pivot_delivery = DK_Pivot_Client_Delivery::where(['pivot_type'=>95,'order_id'=>$item->id])->first();
                if($pivot_delivery)
                {
                    $pivot_delivery->project_id = $delivered_project_id;
                    $pivot_delivery->client_id = $delivered_client_id;
                    $pivot_delivery->delivered_result = $delivered_result;
                    $bool_0 = $pivot_delivery->save();
                    if(!$bool_0) throw new Exception("pivot_client_delivery--update--fail");
                }
                else
                {
                    $pivot_delivery = new DK_Pivot_Client_Delivery;
                    $pivot_delivery_data["pivot_type"] = 95;
                    $pivot_delivery_data["project_id"] = $delivered_project_id;
                    $pivot_delivery_data["client_id"] = $delivered_client_id;
                    $pivot_delivery_data["original_project_id"] = $item->project_id;
                    $pivot_delivery_data["order_id"] = $item->id;
                    $pivot_delivery_data["client_phone"] = $item->client_phone;
                    $pivot_delivery_data["delivered_result"] = $delivered_result;
                    $pivot_delivery_data["creator_id"] = $me->id;

                    $bool_0 = $pivot_delivery->fill($pivot_delivery_data)->save();
                    if(!$bool_0) throw new Exception("insert--pivot_client_delivery--fail");
                }
            }

            $item->is_distributive_condition = $is_distributive_condition;
            $item->client_id = $delivered_client_id;
            $item->deliverer_id = $me->id;
            $item->delivered_status = 1;
            $item->delivered_result = $delivered_result;
            $item->delivered_description = $delivered_description;
            $item->recording_address = $recording_address;
            $item->delivered_at = time();
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 95;
                $record_data["operate_type"] = 1;
                $record_data["column_name"] = "delivered_result";

                $record_data["before"] = $before;
                $record_data["after"] = $delivered_result;

//                $record_data["before_client_id"] = $before;
//                $record_data["after_client_id"] = $client_id;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单】批量-交付
    public function operate_item_order_bulk_deliver($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
            'project_id.required' => 'project_id.required.',
            'client_id.required' => 'client_id.required.',
            'delivered_result.required' => 'delivered_result.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
            'project_id' => 'required',
            'client_id' => 'required',
            'delivered_result' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-delivered-bulk') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $delivered_result = $post_data["delivered_result"];
        if(!in_array($delivered_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");


        $project_id = $post_data["project_id"];
        $client_id = $post_data["client_id"];
        if(in_array($project_id,['-1','0',-1,0]) && in_array($client_id,['-1','0',-1,0]))
        {
//            $project = DK_Project::find($item->project_id);
//            if($project->client_id != 0) $client_id = $project->client_id;
//
//            $delivered_project_id = $item->project_id;
        }
        else if(!in_array($project_id,['-1','0',-1,0]) && !in_array($client_id,['-1','0',-1,0]))
        {
            $project = DK_Project::find($project_id);
            if(!$project) return response_error([],"项目不存在！");

            $client = DK_Client::find($client_id);
            if(!$client) return response_error([],"客户不存在！");

            $delivered_project_id = $project_id;
        }
        else
        {
            return response_error([],"项目和客户必须同时选择或同时不选！");
        }


        $delivered_description = $post_data["delivered_description"];

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $delivered_para['project_id'] = $project_id;
//            $delivered_para['client_id'] = $client_id;
//            $delivered_para['deliverer_id'] = $me->id;
//            $delivered_para['delivered_status'] = 1;
//            $delivered_para['delivered_result'] = $delivered_result;
//            $delivered_para['delivered_at'] = time();

//            $bool = DK_Order::whereIn('id',$ids_array)->update($delivered_para);
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
//            }

            foreach($ids_array as $key => $id)
            {


                $item = DK_Order::withTrashed()->find($id);
                if(!$item) return response_error([],"该内容不存在，刷新页面重试！");


                if(in_array($project_id,['-1','0',-1,0]) && in_array($client_id,['-1','0',-1,0]))
                {
                    $project = DK_Project::find($item->project_id);
                    if($project->client_id != 0) $delivered_client_id = $project->client_id;
                    else $delivered_client_id = 0;

                    $delivered_project_id = $item->project_id;
                }
                else
                {
                    $delivered_project_id = $project_id;
                    $delivered_client_id = $client_id;
                }


                if(!in_array($delivered_client_id,['-1','0',-1,0]) && $delivered_result == "已交付")
                {
                    $pivot_delivery = DK_Pivot_Client_Delivery::where(['pivot_type'=>95,'order_id'=>$id])->first();
                    if($pivot_delivery)
                    {
                        $pivot_delivery->project_id = $delivered_project_id;
                        $pivot_delivery->client_id = $delivered_client_id;
                        $pivot_delivery->delivered_result = $delivered_result;
                        $bool_0 = $pivot_delivery->save();
                        if(!$bool_0) throw new Exception("pivot_client_delivery--update--fail");
                    }
                    else
                    {
                        $pivot_delivery = new DK_Pivot_Client_Delivery;
                        $pivot_delivery_data["pivot_type"] = 95;
                        $pivot_delivery_data["project_id"] = $delivered_project_id;
                        $pivot_delivery_data["client_id"] = $delivered_client_id;
                        $pivot_delivery_data["original_project_id"] = $item->project_id;
                        $pivot_delivery_data["order_id"] = $item->id;
                        $pivot_delivery_data["client_phone"] = $item->client_phone;
                        $pivot_delivery_data["delivered_result"] = $delivered_result;
                        $pivot_delivery_data["creator_id"] = $me->id;

                        $bool_0 = $pivot_delivery->fill($pivot_delivery_data)->save();
                        if(!$bool_0) throw new Exception("insert--pivot_client_delivery--fail");
                    }
                }


                $before = $item->delivered_result;

                $item->client_id = $delivered_client_id;
                $item->deliverer_id = $me->id;
                $item->delivered_status = 1;
                $item->delivered_result = $delivered_result;
                $item->delivered_description = $delivered_description;
                $item->delivered_at = time();
                $bool = $item->save();
                if(!$bool) throw new Exception("item--update--fail");
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 95;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "delivered_result";

                    $record_data["before"] = $before;
                    $record_data["after"] = $delivered_result;

//                $record_data["before_client_id"] = $before;
//                $record_data["after_client_id"] = $client_id;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
                }

            }


            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单】【获取】已交付记录
    public function operate_item_order_deliver_get_delivered($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-deliver-get-delivered') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该工单不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $client_phone = $item->client_phone;


        $order_repeat = DK_Order::select('id','client_id','project_id','client_phone','creator_id')
            ->with([
                'creator'=>function($query) { $query->select('id','username'); },
                'client_er'=>function($query) { $query->select('id','username'); },
                'project_er'=>function($query) { $query->select('id','name'); }
            ])->where(['client_phone'=>$client_phone])
//            ->where('id','<>',$id)
//            ->where('delivered_status',1)
            ->where(function ($query) use($id) {
                $query->where('id','<>',$id)
                    ->orWhere(function($query) use($id) { $query->where('id',$id)->where('delivered_status',1); } );
            })
            ->where('is_published','>',0)
            ->get();
        $return['order_repeat'] = $order_repeat;

        $deliver_repeat = DK_Pivot_Client_Delivery::select('id','client_id','order_id','project_id','client_phone','creator_id')
            ->with([
                'creator'=>function($query) { $query->select('id','username'); },
                'client_er'=>function($query) { $query->select('id','username'); },
                'project_er'=>function($query) { $query->select('id','name'); }
            ])->where(['client_phone'=>$client_phone])->get();
        $return['deliver_repeat'] = $deliver_repeat;


        return response_success($return,"");

    }
    // 【工单】分发
    public function operate_item_order_distribute($post_data)
    {
//        dd($post_data);
//        return response_success([]);
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
            'project_id.required' => '请选择项目',
            'client_id.required' => '请选择客户',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
            'project_id' => 'required',
            'client_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-distribute') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");
        $client_phone = $item->client_phone;

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $client_id = $post_data["client_id"];
        if($client_id == "-1")
        {
            return response_error([],"请选择客户！");
//            $project = DK_Project::find($item->project_id);
//            if($project->client_id != 0) $client_id = $project->client_id;
        }
        $client = DK_Client::find($client_id);
        if(!$client) return response_error([],"客户不存在！");

        $project_id = $post_data["project_id"];
        if($project_id == "-1")
        {
            return response_error([],"请选择项目！");
//            $project = DK_Project::find($item->project_id);
//            if($project->client_id != 0) $client_id = $project->client_id;
        }
        $project = DK_Project::find($project_id);
        if(!$project) return response_error([],"项目不存在！");

        $delivered_result = $post_data["delivered_result"];
        if(!in_array($delivered_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");

        // 是否已经分发
        $is_distributed_list = DK_Pivot_Client_Delivery::where(['client_id'=>$client_id,'client_phone'=>$client_phone])->get();
        if(count($is_distributed_list) > 0)
        {
            return response_error([],"该客户已经交付过该号码，不可以重复分发！");
        }

        // 是否已经交付
        $is_order_list = DK_Order::with('project_er')->where(['client_phone'=>$client_phone,'delivered_result'=>'已交付'])->get();
        if(count($is_order_list) > 0)
        {
            foreach($is_order_list as $o)
            {
                if($o->client_id == $client_id)
                {
                    return response_error([],"该号码已在其他工单交付过该客户，不可以重复分发！");
                }

                if($o->project_er->client_id == $client_id)
                {
                    return response_error([],"该号码已在其他工单交付过默认客户，不可以重复分发！");
                }
            }
        }


        $before = $item->delivered_result;

//        $delivered_description = $post_data["delivered_description"];
//        $recording_address = $post_data["recording_address"];

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            if($client_id != "-1")
//            {
            $pivot_delivery = new DK_Pivot_Client_Delivery;
            $pivot_delivery_data["pivot_type"] = 96;
            $pivot_delivery_data["project_id"] = $project_id;
            $pivot_delivery_data["client_id"] = $client_id;
            $pivot_delivery_data["original_project_id"] = $item->project_id;
            $pivot_delivery_data["order_id"] = $item->id;
            $pivot_delivery_data["client_phone"] = $item->client_phone;
            $pivot_delivery_data["delivered_result"] = $delivered_result;
            $pivot_delivery_data["creator_id"] = $me->id;

            $bool_0 = $pivot_delivery->fill($pivot_delivery_data)->save();
            if(!$bool_0) throw new Exception("insert--pivot_client_delivery--fail");
//            }

//            $item->client_id = $client_id;
//            $item->deliverer_id = $me->id;
//            $item->delivered_status = 1;
//            $item->delivered_result = $delivered_result;
//            $item->delivered_description = $delivered_description;
//            $item->recording_address = $recording_address;
//            $item->delivered_at = time();
//            $bool = $item->save();
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
                $record = new DK_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 96;
                $record_data["operate_type"] = 1;
                $record_data["column_name"] = "client_id";

                $record_data["before"] = $before;
                $record_data["after"] = $client_id;

//                $record_data["before_client_id"] = $before;
//                $record_data["after_client_id"] = $client_id;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
//            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单】分发
    public function operate_item_order_download_recording($post_data)
    {
//        dd($post_data);
//        return response_success([]);
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-download-recording') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");
        $client_phone = $item->client_phone;

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,71,77,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

//        $call_list = DK_CC_Call_Record::select('id','recordFile')->where('staffNo',$item->api_staffNo)->where('callee',$item->client_phone)->get();

        $file_list = [];
        $i['name'] = $item->client_phone.'-'.$item->id.'.mp3';
        $i['path'] = $item->recording_address;
        $i['url'] = $item->recording_address;
        $i['call_record_id'] = $item->call_record_id;
        $file_list[] = $i;

        return response_success(json_encode($file_list));

    }




    // 【api】推送订单
    public function operate_api_push_order($post_data)
    {
        $this->get_me();
        $me = $this->me;
        $customer = $me->customer_er;


        $url = "http://8.142.7.121:9091/api/zl/order?token=zd1a1e02dbe547dt";

//        $API_ID = $customer->api_id;
//        $API_Password = $customer->api_password;
//        $timestamp = time();
//        $seq = $id;
//        $digest = md5($API_ID.'@'.$timestamp.'@'.$seq.'@'.$API_Password);


        $post_data = json_encode($post_data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json"));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // post数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); // post的变量
        $result = curl_exec($ch);
        if(curl_errno($ch))
        {
            $return['success'] = false;
            $return['msg'] =  "cURL Error: " . curl_error($ch);
        }
        else
        {
            $return['success'] = true;
            $return['result'] =  $result;
        }
        curl_close($ch);
        return $return;
    }









    /*
     * 交付管理
     */
    // 【交付】返回-列表-视图
    public function view_item_delivery_list($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66])) return view($this->view_blade_403);


        // 显示数量
        if(!empty($post_data['record']))
        {
            if($post_data['record'] == 'record')
            {
                $this->record_for_user_operate(21,11,1,$me->id,0,71,0);
            }
        }

        // 显示数量
        if(!empty($post_data['length']))
        {
            if(is_numeric($post_data['length']) && $post_data['length'] > 0) $view_data['length'] = $post_data['length'];
            else $view_data['length'] = 20;
        }
        else $view_data['length'] = 10;
        // 第几页
        if(!empty($post_data['page']))
        {
            if(is_numeric($post_data['page']) && $post_data['page'] > 0) $view_data['page'] = $post_data['page'];
            else $view_data['page'] = 1;
        }
        else $view_data['page'] = 1;


        // 工单ID
        if(!empty($post_data['order_id']))
        {
            if(is_numeric($post_data['order_id']) && $post_data['order_id'] > 0) $view_data['order_id'] = $post_data['order_id'];
            else $view_data['order_id'] = '';
        }
        else $view_data['order_id'] = '';

        // 提交日期
        if(!empty($post_data['assign']))
        {
            if($post_data['assign']) $view_data['assign'] = $post_data['assign'];
            else $view_data['assign'] = '';
        }
        else $view_data['assign'] = '';

        // 起始时间
        if(!empty($post_data['assign_start']))
        {
            if($post_data['assign_start']) $view_data['start'] = $post_data['assign_start'];
            else $view_data['start'] = '';
        }
        else $view_data['start'] = '';

        // 截止时间
        if(!empty($post_data['assign_ended']))
        {
            if($post_data['assign_ended']) $view_data['ended'] = $post_data['assign_ended'];
            else $view_data['ended'] = '';
        }
        else $view_data['ended'] = '';


        // 客户
        if(!empty($post_data['client_id']))
        {
            if(is_numeric($post_data['client_id']) && $post_data['client_id'] > 0)
            {
                $client = DK_Client::select(['id','username'])->find($post_data['client_id']);
                if($client)
                {
                    $view_data['client_id'] = $post_data['client_id'];
                    $view_data['client_name'] = $client->username;
                }
                else $view_data['client_id'] = -1;
            }
            else $view_data['client_id'] = -1;
        }
        else $view_data['client_id'] = -1;

        // 项目
        if(!empty($post_data['project_id']))
        {
            if(is_numeric($post_data['project_id']) && $post_data['project_id'] > 0)
            {
                $project = DK_Project::select(['id','name'])->find($post_data['project_id']);
                if($project)
                {
                    $view_data['project_id'] = $post_data['project_id'];
                    $view_data['project_name'] = $project->name;
                }
                else $view_data['project_id'] = -1;
            }
            else $view_data['project_id'] = -1;
        }
        else $view_data['project_id'] = -1;

        // 客户姓名
        if(!empty($post_data['client_name']))
        {
            if($post_data['client_name']) $view_data['client_name'] = $post_data['client_name'];
            else $view_data['client_name'] = '';
        }
        else $view_data['client_'] = '';
        // 客户电话
        if(!empty($post_data['client_phone']))
        {
            if($post_data['client_phone']) $view_data['client_phone'] = $post_data['client_phone'];
            else $view_data['client_phone'] = '';
        }
        else $view_data['client_phone'] = '';

        // 审核状态
        if(!empty($post_data['inspected_status']))
        {
            $view_data['inspected_status'] = $post_data['inspected_status'];
        }
        else $view_data['inspected_status'] = -1;


        $client_list = DK_Client::select('id','username')->get();
        $view_data['client_list'] = $client_list;

        $project_list = DK_Project::select('id','name')->get();
        $view_data['project_list'] = $project_list;

        $view_data['menu_active_of_delivery_list'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.delivery-list';
        return view($view_blade)->with($view_data);
    }
    // 【交付】返回-列表-数据
    public function get_item_delivery_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Pivot_Client_Delivery::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
//            ->where('client_id',$me->id)
            ->with([
                'inspector_er',
                'original_project_er',
                'client_er',
                'project_er',
                'order_er',
                'creator'
            ]);
//            ->whereIn('user_category',[11])
//            ->whereIn('user_type',[0,1,9,11,19,21,22,41,61,88]);
//            ->whereHas('fund', function ($query1) { $query1->where('totalfunds', '>=', 1000); } )
//            ->withCount([
//                'members'=>function ($query) { $query->where('usergroup','Agent2'); },
//                'fans'=>function ($query) { $query->rderwhere('usergroup','Service'); }
//            ]);
//            ->where(['userstatus'=>'正常','status'=>1])
//            ->whereIn('usergroup',['Agent','Agent2']);

//        $me->load(['subordinate_er' => function ($query) {
//            $query->select('id');
//        }]);

        if(in_array($me->user_type,[66]))
        {
            $query->where('creator_id',$me->id);
        }


        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['order_id'])) $query->where('order_id', $post_data['order_id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['client_name'])) $query->where('client_name', $post_data['client_name']);
        if(!empty($post_data['client_phone'])) $query->where('client_phone', $post_data['client_phone']);

        if(!empty($post_data['assign'])) $query->whereDate(DB::Raw("from_unixtime(created_at)"), $post_data['assign']);
        if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '>=', $post_data['assign_start']);
        if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '<=', $post_data['assign_ended']);


        // 客户
        if(isset($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1]))
            {
                $query->where('client_id', $post_data['client']);
            }
        }

        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1]))
            {
                $query->where('project_id', $post_data['project']);
            }
        }

        // 交付类型
        if(!empty($post_data['delivery_type']))
        {
            if(!in_array($post_data['delivery_type'],[-1]))
            {
                $query->where('pivot_type', $post_data['delivery_type']);
            }
        }

        // 交付结果
        if(!empty($post_data['delivered_result']))
        {
            if(count($post_data['delivered_result']))
            {
                $query->whereIn('delivered_result', $post_data['delivered_result']);
            }
        }




        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 10;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);

            $list[$k]->content_decode = json_decode($v->content);
        }
//        dd($list->toArray());


        if($me->id > 10000)
        {
            $record["creator_id"] = $me->id;
            $record["record_category"] = 1; // record_category=1 browse/share
            $record["record_type"] = 1; // record_type=1 browse
            $record["page_type"] = 1; // page_type=1 default platform
            $record["page_module"] = 2; // page_module=2 other
            $record["page_num"] = ($skip / $limit) + 1;
            $record["open"] = "delivery-list";
            $record["from"] = request('from',NULL);
            $this->record_for_user_visit($record);
        }


        return datatable_response($list, $draw, $total);
    }


    // 【交付】删除
    public function operate_item_delivery_delete($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'delivery-delete') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");

        $item = DK_Pivot_Client_Delivery::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,61,66])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
        if(in_array($me->user_type,[66]))
        {
            if($item->creator_id != $me->id) return response_error([],"你没有操作权限！");
        }

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            if($item->is_published == 0 && $item->creator_id == $me->id)
            {
                $item_copy = $item;

                $item->timestamps = false;
//                $bool = $item->forceDelete();  // 永久删除
                $bool = $item->delete();  // 普通删除
                if(!$bool) throw new Exception("item--delete--fail");
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $item_id;
                    $record_data["operate_object"] = 91;
                    $record_data["operate_category"] = 101;
                    $record_data["operate_type"] = 1;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
                }

                DB::commit();
                $this->delete_the_item_files($item_copy);
            }
            else
            {
                $item->timestamps = false;
                $bool = $item->delete();  // 普通删除
//                $bool = $item->forceDelete();  // 永久删除
                if(!$bool) throw new Exception("item--delete--fail");
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $item_id;
                    $record_data["operate_object"] = 91;
                    $record_data["operate_category"] = 101;
                    $record_data["operate_type"] = 1;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
                }

                DB::commit();
            }

            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【交付】导出状态
    public function operate_item_delivery_exported($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'delivery-exported') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Pivot_Client_Delivery::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
        if(in_array($me->user_type,[66]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->is_exported = 1;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 91;
                $record_data["operate_category"] = 99;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【交付】批量-导出状态
    public function operate_item_delivery_bulk_exported($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
            'operate_result.required' => 'operate_result.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
            'operate_result' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'delivery-exported-bulk') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_result = $post_data["operate_result"];
//        if(!in_array($operate_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $delivered_para['is_exported'] = $operate_result;

//            $bool = DK_Order::whereIn('id',$ids_array)->update($delivered_para);
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
//            }

            foreach($ids_array as $key => $id)
            {
                $item = DK_Pivot_Client_Delivery::withTrashed()->find($id);
                if(!$item) return response_error([],"该内容不存在，刷新页面重试！");


                $before = $item->is_exported;

                $item->is_exported = $operate_result;
                $bool = $item->save();
                if(!$bool) throw new Exception("item--update--fail");
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 91;
                    $record_data["operate_category"] = 99;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "is_exported";

                    $record_data["before"] = $before;
                    $record_data["after"] = $operate_result;

//                $record_data["before_client_id"] = $before;
//                $record_data["after_client_id"] = $client_id;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
                }

            }


            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }








    /*
     * 分发管理
     */
    // 【分发管理】返回-列表-视图
    public function view_item_distribution_list($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66])) return view($this->view_blade_403);


        // 显示数量
        if(!empty($post_data['record']))
        {
            if($post_data['record'] == 'record')
            {
                $this->record_for_user_operate(21,11,1,$me->id,0,71,0);
            }
        }

        // 显示数量
        if(!empty($post_data['length']))
        {
            if(is_numeric($post_data['length']) && $post_data['length'] > 0) $view_data['length'] = $post_data['length'];
            else $view_data['length'] = 20;
        }
        else $view_data['length'] = 20;
        // 第几页
        if(!empty($post_data['page']))
        {
            if(is_numeric($post_data['page']) && $post_data['page'] > 0) $view_data['page'] = $post_data['page'];
            else $view_data['page'] = 1;
        }
        else $view_data['page'] = 1;


        // 工单ID
        if(!empty($post_data['order_id']))
        {
            if(is_numeric($post_data['order_id']) && $post_data['order_id'] > 0) $view_data['order_id'] = $post_data['order_id'];
            else $view_data['order_id'] = '';
        }
        else $view_data['order_id'] = '';

        // 提交日期
        if(!empty($post_data['assign']))
        {
            if($post_data['assign']) $view_data['assign'] = $post_data['assign'];
            else $view_data['assign'] = '';
        }
        else $view_data['assign'] = '';

        // 起始时间
        if(!empty($post_data['assign_start']))
        {
            if($post_data['assign_start']) $view_data['start'] = $post_data['assign_start'];
            else $view_data['start'] = '';
        }
        else $view_data['start'] = '';

        // 截止时间
        if(!empty($post_data['assign_ended']))
        {
            if($post_data['assign_ended']) $view_data['ended'] = $post_data['assign_ended'];
            else $view_data['ended'] = '';
        }
        else $view_data['ended'] = '';


        // 客户
        if(!empty($post_data['client_id']))
        {
            if(is_numeric($post_data['client_id']) && $post_data['client_id'] > 0)
            {
                $client = DK_Client::select(['id','username'])->find($post_data['client_id']);
                if($client)
                {
                    $view_data['client_id'] = $post_data['client_id'];
                    $view_data['client_name'] = $client->username;
                }
                else $view_data['client_id'] = -1;
            }
            else $view_data['client_id'] = -1;
        }
        else $view_data['client_id'] = -1;

        // 项目
        if(!empty($post_data['project_id']))
        {
            if(is_numeric($post_data['project_id']) && $post_data['project_id'] > 0)
            {
                $project = DK_Project::select(['id','name'])->find($post_data['project_id']);
                if($project)
                {
                    $view_data['project_id'] = $post_data['project_id'];
                    $view_data['project_name'] = $project->name;
                }
                else $view_data['project_id'] = -1;
            }
            else $view_data['project_id'] = -1;
        }
        else $view_data['project_id'] = -1;

        // 客户姓名
        if(!empty($post_data['client_name']))
        {
            if($post_data['client_name']) $view_data['client_name'] = $post_data['client_name'];
            else $view_data['client_name'] = '';
        }
        else $view_data['client_'] = '';
        // 客户电话
        if(!empty($post_data['client_phone']))
        {
            if($post_data['client_phone']) $view_data['client_phone'] = $post_data['client_phone'];
            else $view_data['client_phone'] = '';
        }
        else $view_data['client_phone'] = '';

        // 审核状态
        if(!empty($post_data['inspected_status']))
        {
            $view_data['inspected_status'] = $post_data['inspected_status'];
        }
        else $view_data['inspected_status'] = -1;


        $client_list = DK_Client::select('id','username')->get();
        $view_data['client_list'] = $client_list;

        $project_list = DK_Project::select('id','name')->get();
        $view_data['project_list'] = $project_list;

        $view_data['menu_active_of_distribution_list'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.item.distribution-list';
        return view($view_blade)->with($view_data);
    }
    // 【分发管理】返回-列表-数据
    public function get_item_distribution_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Pivot_Client_Delivery::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
//            ->where('client_id',$me->id)
            ->with([
                'inspector_er',
                'client_er',
                'project_er',
                'order_er',
                'creator'
            ]);
//            ->whereIn('user_category',[11])
//            ->whereIn('user_type',[0,1,9,11,19,21,22,41,61,88]);
//            ->whereHas('fund', function ($query1) { $query1->where('totalfunds', '>=', 1000); } )
//            ->withCount([
//                'members'=>function ($query) { $query->where('usergroup','Agent2'); },
//                'fans'=>function ($query) { $query->rderwhere('usergroup','Service'); }
//            ]);
//            ->where(['userstatus'=>'正常','status'=>1])
//            ->whereIn('usergroup',['Agent','Agent2']);

//        $me->load(['subordinate_er' => function ($query) {
//            $query->select('id');
//        }]);

        if(in_array($me->user_type,[66]))
        {
            $query->where('creator_id',$me->id);
        }


        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['order_id'])) $query->where('order_id', $post_data['order_id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['client_name'])) $query->where('client_name', $post_data['client_name']);
        if(!empty($post_data['client_phone'])) $query->where('client_phone', $post_data['client_phone']);

        if(!empty($post_data['assign'])) $query->whereDate(DB::Raw("from_unixtime(created_at)"), $post_data['assign']);
        if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '>=', $post_data['assign_start']);
        if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '<=', $post_data['assign_ended']);


        // 客户
        if(isset($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1]))
            {
                $query->where('client_id', $post_data['client']);
            }
        }

        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1]))
            {
                $query->where('project_id', $post_data['project']);
            }
        }

        // 交付结果
        if(!empty($post_data['delivered_result']))
        {
            if(count($post_data['delivered_result']))
            {
                $query->whereIn('delivered_result', $post_data['delivered_result']);
            }
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 20;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);

            $list[$k]->content_decode = json_decode($v->content);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }








    /*
     * Finance 财务
     */
    // 【财务】返回-列表-视图
    public function view_finance_daily_list($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 显示数量
        if(!empty($post_data['record']))
        {
            if($post_data['record'] == 'record')
            {
                $this->record_for_user_operate(21,11,1,$me->id,0,71,0);
            }
        }

        // 显示数量
        if(!empty($post_data['length']))
        {
            if(is_numeric($post_data['length']) && $post_data['length'] > 0) $view_data['length'] = $post_data['length'];
            else $view_data['length'] = 20;
        }
        else $view_data['length'] = 20;
        // 第几页
        if(!empty($post_data['page']))
        {
            if(is_numeric($post_data['page']) && $post_data['page'] > 0) $view_data['page'] = $post_data['page'];
            else $view_data['page'] = 1;
        }
        else $view_data['page'] = 1;


        // 日报ID
        if(!empty($post_data['daily_id']))
        {
            if(is_numeric($post_data['daily_id']) && $post_data['daily_id'] > 0) $view_data['daily_id'] = $post_data['daily_id'];
            else $view_data['daily_id'] = '';
        }
        else $view_data['daily_id'] = '';

        // 提交日期
        if(!empty($post_data['assign']))
        {
            if($post_data['assign']) $view_data['assign'] = $post_data['assign'];
            else $view_data['assign'] = '';
        }
        else $view_data['assign'] = '';

        // 起始时间
        if(!empty($post_data['assign_start']))
        {
            if($post_data['assign_start']) $view_data['start'] = $post_data['assign_start'];
            else $view_data['start'] = '';
        }
        else $view_data['start'] = '';

        // 截止时间
        if(!empty($post_data['assign_ended']))
        {
            if($post_data['assign_ended']) $view_data['ended'] = $post_data['assign_ended'];
            else $view_data['ended'] = '';
        }
        else $view_data['ended'] = '';


        // 类型
        if(!empty($post_data['order_type']))
        {
            if(is_numeric($post_data['order_type']) && $post_data['order_type'] > 0) $view_data['order_type'] = $post_data['order_type'];
            else $view_data['order_type'] = -1;
        }
        else $view_data['order_type'] = -1;


        $client_list = DK_Client::select('id','username')->get();
        $view_data['client_list'] = $client_list;


//        dd($view_data);


        $view_data['menu_active_of_finance_daily_list'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.finance.daily-list';
        return view($view_blade)->with($view_data);
    }
    // 【财务】返回-列表-数据
    public function get_finance_daily_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Client_Finance_Daily::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->with(['creator','client_er']);


        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");


//        if(!empty($post_data['assign'])) $query->whereDate("assign_date", $post_data['assign']);
//        if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '>=', $post_data['assign_start']);
//        if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '<=', $post_data['assign_ended']);


        // 客户
        if(isset($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1]))
            {
                $query->where('client_id', $post_data['client']);
            }
        }


        if(!empty($post_data['time_type']))
        {
            if($post_data['time_type'] == "month")
            {
                // 指定月份
                if(!empty($post_data['month']))
                {
                    $month_arr = explode('-', $post_data['month']);
                    $month_year = $month_arr[0];
                    $month_month = $month_arr[1];
                    $query->whereYear("assign_date", $month_year)->whereMonth("assign_date", $month_month);
                }
            }
            else if($post_data['time_type'] == "date")
            {
                // 指定日期
                if(!empty($post_data['date']))
                {
                    $query->whereDate("assign_date", $post_data['date']);
                }
            }
            else if($post_data['time_type'] == "period")
            {
                if(!empty($post_data['assign_start']))
                {
                    $query->whereDate("assign_date", ">=", $post_data['assign_start']);
                }
                if(!empty($post_data['assign_ended']))
                {
                    $query->whereDate("assign_date", "<=", $post_data['assign_ended']);
                }
            }
            else
            {}
        }


        // 统计
        $daily_total = (clone $query)->select(DB::raw("
                    sum(delivery_quantity) as total_of_delivery_quantity,
                    sum(delivery_quantity_of_invalid) as total_of_delivery_quantity_of_invalid,
                    sum(total_daily_cost) as total_of_total_daily_cost
                "))
            ->first();
//        dd($daily_total->toArray());
//        $daily_total = $daily_total[0];


        $total_data = [];
        $total_data['id'] = '合计';
        $total_data['name'] = '--';
        $total_data['assign_date'] = '--';
        $total_data['creator_id'] = 0;
        $total_data['client_id'] = 0;

        $total_data['delivery_quantity'] = $daily_total->total_of_delivery_quantity;
        $total_data['delivery_quantity_of_invalid'] = $daily_total->total_of_delivery_quantity_of_invalid;
        $total_data['cooperative_unit_price'] = '--';

        $total_data['total_daily_cost'] = $daily_total->total_of_total_daily_cost;


        $total_data['created_at'] = "--";
        $total_data['updated_at'] = "--";
        $total_data['description'] = "--";


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            if($v->creator_id == $me->id)
            {
                $list[$k]->is_me = 1;
                $v->is_me = 1;
            }
            else
            {
                $list[$k]->is_me = 0;
                $v->is_me = 0;
            }
        }
//        dd($list->toArray());

        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }


    // 【财务】【修改记录】返回-列表-视图
    public function view_finance_daily_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_client_modify_list'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.user.client-modify-list';
        return view($view_blade)->with($return);
    }
    // 【财务】【修改记录】返回-列表-数据
    public function get_finance_daily_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Record::select('*')
            ->with(['creator'])
            ->where(['record_object'=>21, 'operate_object'=>86,'item_id'=>$id]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【财务】返回-添加-视图
    public function view_finance_daily_list_build()
    {
        $this->get_me();

        $item_type = 'item';
        $item_type_text = '财务日报';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/finance/daily-list';

        $return['operate'] = 'create';
        $return['operate_id'] = 0;
        $return['category'] = 'item';
        $return['type'] = $item_type;
        $return['item_type_text'] = $item_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.finance.daily-list-build';
        return view($view_blade)->with($return);
    }
    // 【财务】保存数据
    public function operate_finance_daily_list_build($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'assign_date.required' => '请填选择日期！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'assign_date' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        if($operate != 'daily-list-build') return response_error([],"参数[operate]有误！");

        $assign_date = $post_data['assign_date'];
        $format = 'Y-m-d';
        $d = DateTime::createFromFormat($format, $assign_date);
        if(!($d && $d->format($format) === $assign_date)) return response_error([],"日期不合法！");


        $client_list = DK_Client::select('id','cooperative_unit_price')->get()->keyBy('id');
//        dd($client_list->toArray());


        // 交付统计
        $delivery_statistic = DK_Pivot_Client_Delivery::select('id','client_id','created_at')
            ->addSelect(DB::raw("
                    FROM_UNIXTIME(created_at,'%Y-%m-%d') as formatted_date,
                    FROM_UNIXTIME(created_at,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(created_at,'%e') as day,
                    count(*) as total_of_count
                "))
            ->whereDate(DB::Raw("from_unixtime(created_at)"), $post_data['assign_date'])
            ->groupBy(DB::raw("DATE(FROM_UNIXTIME(created_at))"))
            ->groupBy('client_id')
            ->get();
//        dd($delivery_statistic->toArray());

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            // 指派日期
            if(!empty($post_data['assign_date']))
            {
                $post_data['assign_time'] = strtotime($post_data['assign_date']);
            }
//            else $post_data['assign_time'] = 0;

            foreach($delivery_statistic as $k => $v)
            {
                $mine_data['client_id'] = $v->client_id;
                $mine_data['assign_date'] = $assign_date;
                $mine_data['cooperative_unit_price'] = $client_list[$v->client_id]->cooperative_unit_price;
                $mine_data['delivery_quantity'] = $v->total_of_count;
                $mine_data['total_daily_cost'] = $mine_data['cooperative_unit_price'] * $v->total_of_count;

                $mine = DK_Client_Finance_Daily::where(['client_id'=>$v->client_id,'assign_date'=>$assign_date])->first();
                if($mine)
                {
                }
                else
                {
                    $mine = new DK_Client_Finance_Daily;
                    $bool = $mine->fill($mine_data)->save();
                    if($bool)
                    {
                    }
                    else throw new Exception("insert--DK_Client_Finance_Daily--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【财务】【文本-信息】设置-文本-类型
    public function operate_finance_daily_info_text_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'finance-daily-info-text-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client_Finance_Daily::withTrashed()->lockForUpdate()->find($id);
        if(!$item) return response_error([],"该【财务日报】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $item->timestamps = false;
            $item->$column_key = $column_value;
            $item->total_daily_cost = ($item->delivery_quantity - $item->delivery_quantity_of_invalid) * $item->cooperative_unit_price;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 86;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $column_value;

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【财务】【时间-信息】修改-时间-类型
    public function operate_finance_daily_info_time_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'finance-daily-info-time-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client_Finance_Daily::withTrashed()->find($id);
        if(!$item) return response_error([],"该【财务日报】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];
        $time_type = $post_data["time_type"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->$column_key = strtotime($column_value);
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 86;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_type"] = $time_type;
                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = strtotime($column_value);

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【财务】【选项-信息】修改-radio-select-[option]-类型
    public function operate_finance_daily_info_option_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'finance-daily-info-option-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client_Finance_Daily::withTrashed()->find($id);
        if(!$item) return response_error([],"该【财务报告】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $item->timestamps = false;
            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 86;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $column_value;

                    if(in_array($column_key,['leader_id']))
                    {
                        $record_data["before_id"] = $before;
                        $record_data["after_id"] = $column_value;
                    }


                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }








    /*
     * Statistic 流量统计
     */
    // 【统计】
    public function view_statistic_index($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 日期
        if(!empty($post_data['date']))
        {
            if($post_data['date']) $view_data['date'] = $post_data['date'];
            else $view_data['date'] = '';
        }
        else $view_data['date'] = '';

        // 月份
        if(!empty($post_data['month']))
        {
            if($post_data['month']) $view_data['month'] = $post_data['month'];
            else $view_data['month'] = '';
        }
        else $view_data['month'] = '';


        $staff_list = DK_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();
        $client_list = DK_Client::select('id','username')->where('user_category',11)->get();
        $project_list = DK_Project::select('id','name')->whereIn('item_type',[1,21])->get();
        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();

        $view_data['staff_list'] = $staff_list;
        $view_data['client_list'] = $client_list;
        $view_data['project_list'] = $project_list;
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_index'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-index';
        return view($view_blade)->with($view_data);
    }
    // 【统计】
    public function view_statistic_user($post_data)
    {
        $this->get_me();
        $me = $this->me;
        dd($me);

        $messages = [
            'user-id.required' => 'user-id is required.',
        ];
        $v = Validator::make($post_data, [
            'user-id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $user_id = $post_data["user-id"];
        $user = DK_User::find($user_id);

        $this_month = date('Y-m');
        $this_month_year = date('Y');
        $this_month_month = date('m');
        $last_month = date('Y-m',strtotime('last month'));
        $last_month_year = date('Y',strtotime('last month'));
        $last_month_month = date('m',strtotime('last month'));


        // 电话量
        $query = YH_TASK::select(
            DB::raw("DATE(FROM_UNIXTIME(created_at)) as date"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(completed_at),'%Y-%m') as month"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(completed_at),'%c') as month_0"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(completed_at),'%e') as day"),
            DB::raw('count(*) as count')
        )
            ->groupBy(DB::raw("DATE(FROM_UNIXTIME(completed_at))"))
            ->whereYear(DB::raw("DATE(FROM_UNIXTIME(completed_at))"),$this_month_year)
            ->whereMonth(DB::raw("DATE(FROM_UNIXTIME(completed_at))"),$this_month_month)
            ->where(['is_completed'=>1,'owner_id'=>$user_id]);

        $all = $query->get()->keyBy('day');
        $dialog = $query->whereIn('item_result',[1,19])->get()->keyBy('day');
        $plus_wx = $query->where('item_result',19)->get()->keyBy('day');


        // 总转化率【占比】
        $all_rate = YH_TASK::select('item_result',DB::raw('count(*) as count'))
            ->groupBy('item_result')
            ->where(['is_completed'=>1,'owner_id'=>$user_id])
            ->get();
        foreach($all_rate as $k => $v)
        {
            if($v->item_result == 0) $all_rate[$k]->name = "未选择";
            else if($v->item_result == 1) $all_rate[$k]->name = "通话";
            else if($v->item_result == 19)  $all_rate[$k]->name = "加微信";
            else if($v->item_result == 71)  $all_rate[$k]->name = "未接";
            else if($v->item_result == 72)  $all_rate[$k]->name = "拒接";
            else if($v->item_result == 51)  $all_rate[$k]->name = "打错了";
            else if($v->item_result == 99)  $all_rate[$k]->name = "空号";
            else $all_rate[$k]->name = "其他";
        }


        // 今日转化率【占比】
        $today_rate = YH_TASK::select('item_result',DB::raw('count(*) as count'))
            ->groupBy('item_result')
            ->where(['is_completed'=>1,'owner_id'=>$user_id])
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(completed_at))"),date('Y-m-d'))
            ->get();
        foreach($today_rate as $k => $v)
        {
            if($v->item_result == 0) $today_rate[$k]->name = "未选择";
            else if($v->item_result == 1) $today_rate[$k]->name = "通话";
            else if($v->item_result == 19)  $today_rate[$k]->name = "加微信";
            else if($v->item_result == 71)  $today_rate[$k]->name = "未接";
            else if($v->item_result == 72)  $today_rate[$k]->name = "拒接";
            else if($v->item_result == 51)  $today_rate[$k]->name = "打错了";
            else if($v->item_result == 99)  $today_rate[$k]->name = "空号";
            else $today_rate[$k]->name = "其他";
        }


        $view_data["head_title"] = '【'.$user->true_name.'】的工作统计';
        $view_data["all"] = $all;
        $view_data["dialog"] = $dialog;
        $view_data["plus_wx"] = $plus_wx;
        $view_data["all_rate"] = $all_rate;
        $view_data["today_rate"] = $today_rate;

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-user';
        return view($view_blade)->with($view_data);
    }
    // 【统计】
    public function view_statistic_item($post_data)
    {
        $messages = [
            'id.required' => 'id required',
        ];
        $v = Validator::make($post_data, [
            'id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $item_id = $post_data["id"];
        $item = Def_Item::find($item_id);

        $this_month = date('Y-m');
        $this_month_year = date('Y');
        $this_month_month = date('m');
        $last_month = date('Y-m',strtotime('last month'));
        $last_month_year = date('Y',strtotime('last month'));
        $last_month_month = date('m',strtotime('last month'));


        // 访问量【统计】
        $data = Def_Record::select(
            DB::raw("DATE(FROM_UNIXTIME(created_at)) as date"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(created_at),'%Y-%m') as month"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(created_at),'%c') as month_0"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(created_at),'%e') as day"),
            DB::raw('count(*) as count')
        )
            ->groupBy(DB::raw("DATE(FROM_UNIXTIME(created_at))"))
            ->whereYear(DB::raw("DATE(FROM_UNIXTIME(created_at))"),$this_month_year)
            ->whereMonth(DB::raw("DATE(FROM_UNIXTIME(created_at))"),$this_month_month)
            ->where(['record_category'=>1,'record_type'=>1])
            ->where('item_id',$item_id)
            ->get();
        $data = $data->keyBy('day');




        // 打开设备类型【占比】
        $open_device_type = Def_Record::select('open_device_type',DB::raw('count(*) as count'))
            ->groupBy('open_device_type')
            ->where(['record_category'=>1,'record_type'=>1])
            ->where('item_id',$item_id)
            ->get();
        foreach($open_device_type as $k => $v)
        {
            if($v->open_device_type == 0) $open_device_type[$k]->name = "默认";
            else if($v->open_device_type == 1) $open_device_type[$k]->name = "移动端";
            else if($v->open_device_type == 2)  $open_device_type[$k]->name = "PC端";
        }

        // 打开系统类型【占比】
        $open_system = Def_Record::select('open_system',DB::raw('count(*) as count'))
            ->groupBy('open_system')
            ->where(['record_category'=>1,'record_type'=>1])
            ->where('item_id',$item_id)
            ->get();

        // 打开APP类型【占比】
        $open_app = Def_Record::select('open_app',DB::raw('count(*) as count'))
            ->groupBy('open_app')
            ->where(['record_category'=>1,'record_type'=>1])
            ->where('item_id',$item_id)
            ->get();




        // 分享【统计】
        $shared_data = Def_Record::select(
            DB::raw("DATE(FROM_UNIXTIME(created_at)) as date"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(created_at),'%Y-%m') as month"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(created_at),'%c') as month_0"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(created_at),'%e') as day"),
            DB::raw('count(*) as count')
        )
            ->groupBy(DB::raw("DATE(FROM_UNIXTIME(created_at))"))
            ->whereYear(DB::raw("DATE(FROM_UNIXTIME(created_at))"),$this_month_year)
            ->whereMonth(DB::raw("DATE(FROM_UNIXTIME(created_at))"),$this_month_month)
            ->where(['record_category'=>1,'record_type'=>2])
            ->where('item_id',$item_id)
            ->get();
        $shared_data = $shared_data->keyBy('day');


        // 分享【占比】
        $shared_data_scale = Def_Record::select('record_module',DB::raw('count(*) as count'))
            ->groupBy('record_module')
            ->where(['record_category'=>1,'record_type'=>2])
            ->where('item_id',$item_id)
            ->get();
        foreach($shared_data_scale as $k => $v)
        {
            if($v->record_module == 1) $shared_data_scale[$k]->name = "微信好友|QQ好友";
            else if($v->record_module == 2) $shared_data_scale[$k]->name = "朋友圈|QQ空间";
            else $shared_data_scale[$k]->name = "其他";
        }


        $view_data["item"] = $item;
        $view_data["data"] = $data;
        $view_data["open_device_type"] = $open_device_type;
        $view_data["open_app"] = $open_app;
        $view_data["open_system"] = $open_system;
        $view_data["shared_data"] = $shared_data;
        $view_data["shared_data_scale"] = $shared_data_scale;

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-item';
        return view($view_blade)->with($view_data);
    }
    // 【统计】返回（后台）主页视图
    public function get_statistic_data($post_data)
    {
        $this->get_me();
        $me = $this->me;

//        $condition = request()->all();
//        $return['condition'] = $condition;
//
//        $condition['task-list-type'] = 'unfinished';
//        $parameter_result = http_build_query($condition);
//        return redirect('/?'.$parameter_result);


        $this_month = date('Y-m');
        $this_month_start_date = date('Y-m-1'); // 本月开始日期
        $this_month_ended_date = date('Y-m-t'); // 本月结束日期
        $this_month_start_datetime = date('Y-m-1 00:00:00'); // 本月开始时间
        $this_month_ended_datetime = date('Y-m-t 23:59:59'); // 本月结束时间
        $this_month_start_timestamp = strtotime($this_month_start_datetime); // 本月开始时间戳
        $this_month_ended_timestamp = strtotime($this_month_ended_datetime); // 本月结束时间戳

        $last_month_start_date = date('Y-m-1',strtotime('last month')); // 上月开始时间
        $last_month_ended_date = date('Y-m-t',strtotime('last month')); // 上月开始时间
        $last_month_start_datetime = date('Y-m-1 00:00:00',strtotime('last month')); // 上月开始时间
        $last_month_ended_datetime = date('Y-m-t 23:59:59',strtotime('last month')); // 上月结束时间
        $last_month_start_timestamp = strtotime($last_month_start_datetime); // 上月开始时间戳
        $last_month_ended_timestamp = strtotime($last_month_ended_datetime); // 上月结束时间戳



        $the_month  = isset($post_data['month']) ? $post_data['month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-1',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-1 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $the_last_month_timestamp = strtotime('last month', $the_month_timestamp);
        $the_last_month_start_date = date('Y-m-1',$the_last_month_timestamp); // 指定月份-上月-开始时间
        $the_last_month_ended_date = date('Y-m-t',$the_last_month_timestamp); // 指定月份-上月-开始时间
        $the_last_month_start_datetime = date('Y-m-1 00:00:00',$the_last_month_timestamp); // 指定月份-上月-开始时间
        $the_last_month_ended_datetime = date('Y-m-t 23:59:59',$the_last_month_timestamp); // 指定月份-上月-结束时间
        $the_last_month_start_timestamp = strtotime($the_last_month_start_datetime); // 指定月份-上月-开始时间戳
        $the_last_month_ended_timestamp = strtotime($the_last_month_ended_datetime); // 指定月份-上月-结束时间戳



        $type = isset($post_data['type']) ? $post_data['type']  : '';

        $staff_id = 0;
        $client_id = 0;
        $car_id = 0;
        $route_id = 0;
        $pricing_id = 0;

        if($type == 'component')
        {
            // 员工
            if(!empty($post_data['staff']))
            {
                if(!in_array($post_data['staff'],[-1,0]))
                {
                    $staff_id = $post_data['staff'];
                }
            }
            // 客户
            if(!empty($post_data['client']))
            {
                if(!in_array($post_data['client'],[-1,0]))
                {
                    $client_id = $post_data['client'];
                }
            }
            // 项目
            if(!empty($post_data['car']))
            {
                if(!in_array($post_data['car'],[-1,0]))
                {
                    $car_id = $post_data['car'];
                }
            }
            // 线路
            if(!empty($post_data['route']))
            {
                if(!in_array($post_data['route'],[-1,0]))
                {
                    $route_id = $post_data['route'];
                }
            }
            // 定价
            if(!empty($post_data['pricing']))
            {
                if(!in_array($post_data['pricing'],[-1,0]))
                {
                    $pricing_id = $post_data['pricing'];
                }
            }
        }

        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');




        // 工单统计
        $order_count_for_all = DK_Order::select('*')->count("*");
        $order_count_for_unpublished = DK_Order::where('is_published', 0)->count("*");
        $order_count_for_published = DK_Order::where('is_published', 1)->count("*");
        $order_count_for_waiting_for_inspect = DK_Order::where('is_published', 1)->where('inspected_status', 0)->count("*");
        $order_count_for_inspected = DK_Order::where('is_published', 1)->where('inspected_status', '<>', 0);
        $order_count_for_accepted = DK_Order::where('is_published', 1)->where('inspected_result','通过');
        $order_count_for_refused = DK_Order::where('is_published', 1)->where('inspected_result','拒绝');
        $order_count_for_accepted_inside = DK_Order::where('is_published', 1)->where('inspected_result','内部通过');
        $order_count_for_repeat = DK_Order::where('is_published', 1)->where('is_repeat','>',0);



        $return['order_count_for_all'] = $order_count_for_all;
        $return['order_count_for_inspected'] = $order_count_for_inspected;
        $return['order_count_for_accepted'] = $order_count_for_accepted;
        $return['order_count_for_refused'] = $order_count_for_refused;
        $return['order_count_for_repeat'] = $order_count_for_repeat;
        $return['order_count_for_rate'] = round(($order_count_for_accepted * 100 / $order_count_for_all),2);




        // 工单统计

        // 本月每日工单量
        $query_for_order_this_month = DK_Order::select('id','assign_time')
            ->whereBetween('assign_time',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(assign_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(assign_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(assign_time,'%e') as day,
                    count(*) as sum
                "));

        if($staff_id) $query_for_order_this_month->where('creator_id',$staff_id);
        if($client_id) $query_for_order_this_month->where('client_id',$client_id);
        if($car_id) $query_for_order_this_month->where('car_id',$car_id);
        if($route_id) $query_for_order_this_month->where('route_id',$route_id);
        if($pricing_id) $query_for_order_this_month->where('pricing_id',$pricing_id);


        $statistics_data_for_order_this_month = $query_for_order_this_month->get()->keyBy('day');
        $return_data['statistics_data_for_order_this_month'] = $statistics_data_for_order_this_month;

        // 上月每日工单量
        $query_for_order_last_month = DK_Order::select('id','assign_time')
            ->whereBetween('assign_time',[$the_last_month_start_timestamp,$the_last_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(assign_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(assign_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(assign_time,'%e') as day,
                    count(*) as sum
                "));

        if($staff_id) $query_for_order_last_month->where('creator_id',$staff_id);
        if($client_id) $query_for_order_last_month->where('client_id',$client_id);
        if($car_id) $query_for_order_last_month->where('car_id',$car_id);
        if($route_id) $query_for_order_last_month->where('route_id',$route_id);
        if($pricing_id) $query_for_order_last_month->where('pricing_id',$pricing_id);

        $statistics_data_for_order_last_month = $query_for_order_last_month->get()->keyBy('day');
        $return_data['statistics_data_for_order_last_month'] = $statistics_data_for_order_last_month;




        return response_success($return_data,"");
    }
    // 【统计】返回-综合-数据
    public function get_statistic_data_for_comprehensive($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_date  = isset($post_data['date']) ? $post_data['date']  : date('Y-m-d');
        $the_date_timestamp = strtotime($the_date);

        $the_month  = isset($post_data['month']) ? $post_data['month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-1',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-1 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

//        $the_last_month_timestamp = strtotime('last month', $the_month_timestamp);
//        $the_last_month_start_date = date('Y-m-1',$the_last_month_timestamp); // 指定月份-上月-开始日期
//        $the_last_month_ended_date = date('Y-m-t',$the_last_month_timestamp); // 指定月份-上月-结束日期
//        $the_last_month_start_datetime = date('Y-m-1 00:00:00',$the_last_month_timestamp); // 指定月份-上月-开始时间
//        $the_last_month_ended_datetime = date('Y-m-t 23:59:59',$the_last_month_timestamp); // 指定月份-上月-结束时间
//        $the_last_month_start_timestamp = strtotime($the_last_month_start_datetime); // 指定月份-上月-开始时间戳
//        $the_last_month_ended_timestamp = strtotime($the_last_month_ended_datetime); // 指定月份-上月-结束时间戳



        $type = isset($post_data['type']) ? $post_data['type']  : '';


        $query = DK_Order::select('id');
        $query_distributed = DK_Pivot_Client_Delivery::select('id')->where('pivot_type',96);

        if($me->user_type == 41)
        {
            $query->where('department_district_id',$me->department_district_id);
        }
        else if($me->user_type == 81)
        {
            $query->where('department_manager_id',$me->id);
        }
        else if($me->user_type == 84)
        {
            $query->where('department_supervisor_id',$me->id);
        }
        else if($me->user_type == 88)
        {
            $query->where('creator_id',$me->id);
        }
        else if($me->user_type == 71)
        {
            $query->where('inspector_id',$me->id);
        }
        else if($me->user_type == 77)
        {
            $query->where('inspector_id',$me->id);
        }


        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0]))
            {
                $query->where('project_id', $post_data['project']);
                $query_distributed->where('project_id', $post_data['project']);
            }
        }


        // 部门-大区
//        if(!empty($post_data['department_district']))
//        {
//            if(!in_array($post_data['department_district'],[-1,0]))
//            {
//                $query->where('department_district_id', $post_data['department_district']);
//            }
//        }
        if(!empty($post_data['department_district']))
        {
            if(count($post_data['department_district']))
            {
                $query->whereIn('department_district_id', $post_data['department_district']);
            }
        }








        // 分发当天数据
        $query_distributed_of_all = (clone $query_distributed)
            ->select(DB::raw("
                    count(*) as distributed_count_for_all
                "))
            ->get();
        $return_data['distributed_of_all_for_all'] = $query_distributed_of_all[0]->distributed_count_for_all;




        // 工单统计
        // 总量统计
        $query_order_of_all = (clone $query)->whereIn('created_type',[1,99])
            ->select(DB::raw("
                    count(*) as order_count_for_all,
                    count(IF(is_published = 0, TRUE, NULL)) as order_count_for_unpublished,
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_published,
                    
                    count(IF(is_published = 1 AND inspected_status <> 0, TRUE, NULL)) as order_count_for_inspected_all,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_inspected_accepted,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_inspected_accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_inspected_repeated,
                    count(IF(inspected_result = '拒绝', TRUE, NULL)) as order_count_for_inspected_refused,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered_all,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->get();

        $order_of_all_for_all = $query_order_of_all[0]->order_count_for_all;
        $order_of_all_for_unpublished = $query_order_of_all[0]->order_count_for_unpublished;
        $order_of_all_for_published = $query_order_of_all[0]->order_count_for_published;

        $return_data['order_of_all_for_all'] = $order_of_all_for_all;
        $return_data['order_of_all_for_unpublished'] = $order_of_all_for_unpublished;
        $return_data['order_of_all_for_published'] = $order_of_all_for_published;


        $order_of_all_for_inspected_all = $query_order_of_all[0]->order_count_for_inspected_all;
        $order_of_all_for_inspected_accepted = $query_order_of_all[0]->order_count_for_inspected_accepted;
        $order_of_all_for_inspected_accepted_inside = $query_order_of_all[0]->order_count_for_inspected_accepted_inside;
        $order_of_all_for_inspected_refused = $query_order_of_all[0]->order_count_for_inspected_refused;
        $order_of_all_for_inspected_repeated = $query_order_of_all[0]->order_count_for_inspected_repeated;

        $return_data['order_of_all_for_inspected_all'] = $order_of_all_for_inspected_all;
        $return_data['order_of_all_for_inspected_accepted'] = $order_of_all_for_inspected_accepted;
        $return_data['order_of_all_for_inspected_accepted_inside'] = $order_of_all_for_inspected_accepted_inside;
        $return_data['order_of_all_for_inspected_refused'] = $order_of_all_for_inspected_refused;
        $return_data['order_of_all_for_inspected_repeated'] = $order_of_all_for_inspected_repeated;


        $order_of_all_for_delivered_all = $query_order_of_all[0]->order_count_for_delivered_all;
        $order_of_all_for_delivered_completed = $query_order_of_all[0]->order_count_for_delivered_completed;
        $order_of_all_for_delivered_inside = $query_order_of_all[0]->order_count_for_delivered_inside;
        $order_of_all_for_delivered_tomorrow = $query_order_of_all[0]->order_count_for_delivered_tomorrow;
        $order_of_all_for_delivered_repeated = $query_order_of_all[0]->order_count_for_delivered_repeated;
        $order_of_all_for_delivered_rejected = $query_order_of_all[0]->order_count_for_delivered_rejected;
        $order_of_all_for_delivered_effective = $order_of_all_for_delivered_completed + $order_of_all_for_delivered_tomorrow + $order_of_all_for_delivered_inside;
        if($order_of_all_for_all)
        {
            $order_of_all_for_delivered_effective_rate = round(($order_of_all_for_delivered_effective * 100 / $order_of_all_for_all),2);
        }
        else $order_of_all_for_delivered_effective_rate = 0;

        $return_data['order_of_all_for_delivered_all'] = $order_of_all_for_delivered_all;
        $return_data['order_of_all_for_delivered_completed'] = $order_of_all_for_delivered_completed;
        $return_data['order_of_all_for_delivered_inside'] = $order_of_all_for_delivered_inside;
        $return_data['order_of_all_for_delivered_tomorrow'] = $order_of_all_for_delivered_tomorrow;
        $return_data['order_of_all_for_delivered_repeated'] = $order_of_all_for_delivered_repeated;
        $return_data['order_of_all_for_delivered_rejected'] = $order_of_all_for_delivered_rejected;
        $return_data['order_of_all_for_delivered_effective'] = $order_of_all_for_delivered_effective;
        $return_data['order_of_all_for_delivered_effective_rate'] = $order_of_all_for_delivered_effective_rate;




        $query_delivered_of_all = (clone $query)
            ->whereIn('created_type',[1,99])
            ->select(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as delivered_count_for_all,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as delivered_count_for_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as delivered_count_for_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as delivered_count_for_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as delivered_count_for_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as delivered_count_for_rejected
                "))
            ->get();

        $deliverer_of_all_for_all = $query_delivered_of_all[0]->delivered_count_for_all;
        $deliverer_of_all_for_completed = $query_delivered_of_all[0]->delivered_count_for_completed;
        $deliverer_of_all_for_inside = $query_delivered_of_all[0]->delivered_count_for_inside;
        $deliverer_of_all_for_tomorrow = $query_delivered_of_all[0]->delivered_count_for_tomorrow;
        $deliverer_of_all_for_repeated = $query_delivered_of_all[0]->delivered_count_for_repeated;
        $deliverer_of_all_for_rejected = $query_delivered_of_all[0]->delivered_count_for_rejected;


        $return_data['deliverer_of_all_for_all'] = $deliverer_of_all_for_all;
        $return_data['deliverer_of_all_for_completed'] = $deliverer_of_all_for_completed;
        $return_data['deliverer_of_all_for_inside'] = $deliverer_of_all_for_inside;
        $return_data['deliverer_of_all_for_tomorrow'] = $deliverer_of_all_for_tomorrow;
        $return_data['deliverer_of_all_for_repeated'] = $deliverer_of_all_for_repeated;
        $return_data['deliverer_of_all_for_rejected'] = $deliverer_of_all_for_rejected;








        // 分发当天数据
        $query_distributed_of_today = (clone $query_distributed)->whereDate(DB::raw("DATE(FROM_UNIXTIME(updated_at))"),$the_date)
            ->select(DB::raw("
                    count(*) as distributed_count_for_all
                "))
            ->get();
        $return_data['distributed_of_today_for_all'] = $query_distributed_of_today[0]->distributed_count_for_all;




        // 客服报单-当天统计
        $query_order_of_today = (clone $query)->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_date)
            ->whereIn('created_type',[1,99])
            ->select(DB::raw("
                    count(*) as order_count_for_all,
                    count(IF(is_published = 0, TRUE, NULL)) as order_count_for_unpublished,
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_published,
                    
                    count(IF(is_published = 1 AND inspected_status <> 0, TRUE, NULL)) as order_count_for_inspected_all,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_inspected_accepted,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_inspected_accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_inspected_repeated,
                    count(IF(inspected_result = '拒绝', TRUE, NULL)) as order_count_for_inspected_refused,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered_all,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->get();

        $order_of_today_for_all = $query_order_of_today[0]->order_count_for_all;
        $order_of_today_for_unpublished = $query_order_of_today[0]->order_count_for_unpublished;
        $order_of_today_for_published = $query_order_of_today[0]->order_count_for_published;

        $return_data['order_of_today_for_all'] = $order_of_today_for_all;
        $return_data['order_of_today_for_unpublished'] = $order_of_today_for_unpublished;
        $return_data['order_of_today_for_published'] = $order_of_today_for_published;


        $order_of_today_for_inspected_all = $query_order_of_today[0]->order_count_for_inspected_all;
        $order_of_today_for_inspected_accepted = $query_order_of_today[0]->order_count_for_inspected_accepted;
        $order_of_today_for_inspected_accepted_inside = $query_order_of_today[0]->order_count_for_inspected_accepted_inside;
        $order_of_today_for_inspected_refused = $query_order_of_today[0]->order_count_for_inspected_refused;
        $order_of_today_for_inspected_repeated = $query_order_of_today[0]->order_count_for_inspected_repeated;

        $return_data['order_of_today_for_inspected_all'] = $order_of_today_for_inspected_all;
        $return_data['order_of_today_for_inspected_accepted'] = $order_of_today_for_inspected_accepted;
        $return_data['order_of_today_for_inspected_accepted_inside'] = $order_of_today_for_inspected_accepted_inside;
        $return_data['order_of_today_for_inspected_refused'] = $order_of_today_for_inspected_refused;
        $return_data['order_of_today_for_inspected_repeated'] = $order_of_today_for_inspected_repeated;


        $order_of_today_for_delivered_all = $query_order_of_today[0]->order_count_for_delivered_all;
        $order_of_today_for_delivered_completed = $query_order_of_today[0]->order_count_for_delivered_completed;
        $order_of_today_for_delivered_inside = $query_order_of_today[0]->order_count_for_delivered_inside;
        $order_of_today_for_delivered_tomorrow = $query_order_of_today[0]->order_count_for_delivered_tomorrow;
        $order_of_today_for_delivered_repeated = $query_order_of_today[0]->order_count_for_delivered_repeated;
        $order_of_today_for_delivered_rejected = $query_order_of_today[0]->order_count_for_delivered_rejected;
        $order_of_today_for_delivered_effective = $order_of_today_for_delivered_completed + $order_of_today_for_delivered_tomorrow + $order_of_today_for_delivered_inside;
        if($order_of_today_for_all)
        {
            $order_of_today_for_delivered_effective_rate = round(($order_of_today_for_delivered_effective * 100 / $order_of_today_for_all),2);
        }
        else $order_of_today_for_delivered_effective_rate = 0;

        $return_data['order_of_today_for_delivered_all'] = $order_of_today_for_delivered_all;
        $return_data['order_of_today_for_delivered_completed'] = $order_of_today_for_delivered_completed;
        $return_data['order_of_today_for_delivered_inside'] = $order_of_today_for_delivered_inside;
        $return_data['order_of_today_for_delivered_tomorrow'] = $order_of_today_for_delivered_tomorrow;
        $return_data['order_of_today_for_delivered_repeated'] = $order_of_today_for_delivered_repeated;
        $return_data['order_of_today_for_delivered_rejected'] = $order_of_today_for_delivered_rejected;
        $return_data['order_of_today_for_delivered_effective'] = $order_of_today_for_delivered_effective;
        $return_data['order_of_today_for_delivered_effective_rate'] = $order_of_today_for_delivered_effective_rate;


        // 交付人员-工作统计
        $query_delivered_of_today = (clone $query)->whereDate(DB::raw("DATE(FROM_UNIXTIME(delivered_at))"),$the_date)
            ->whereIn('created_type',[1,99])
            ->select(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as delivered_count_for_all,
                    count(IF(delivered_status = 1 AND DATE(FROM_UNIXTIME(published_at)) = '{$the_date}', TRUE, NULL)) as delivered_count_for_all_by_same_day,
                    count(IF(delivered_status = 1 AND DATE(FROM_UNIXTIME(published_at)) <> '{$the_date}', TRUE, NULL)) as delivered_count_for_all_by_other_day,
                    
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as delivered_count_for_completed,
                    count(IF(delivered_result = '已交付' AND DATE(FROM_UNIXTIME(published_at)) = '{$the_date}', TRUE, NULL)) as delivered_count_for_completed_by_same_day,
                    count(IF(delivered_result = '已交付' AND DATE(FROM_UNIXTIME(published_at)) <> '{$the_date}', TRUE, NULL)) as delivered_count_for_completed_by_other_day,
                    
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as delivered_count_for_inside,
                    count(IF(delivered_result = '内部交付' AND DATE(FROM_UNIXTIME(published_at)) = '{$the_date}', TRUE, NULL)) as delivered_count_for_inside_by_same_day,
                    count(IF(delivered_result = '内部交付' AND DATE(FROM_UNIXTIME(published_at)) <> '{$the_date}', TRUE, NULL)) as delivered_count_for_inside_by_other_day,
                    
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as delivered_count_for_tomorrow,
                    
                    count(IF(delivered_result = '重复', TRUE, NULL)) as delivered_count_for_repeated,
                    count(IF(delivered_result = '重复' AND DATE(FROM_UNIXTIME(published_at)) = '{$the_date}', TRUE, NULL)) as delivered_count_for_repeated_by_same_day,
                    count(IF(delivered_result = '重复' AND DATE(FROM_UNIXTIME(published_at)) <> '{$the_date}', TRUE, NULL)) as delivered_count_for_repeated_by_other_day,
                    
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as delivered_count_for_rejected,
                    count(IF(delivered_result = '驳回' AND DATE(FROM_UNIXTIME(published_at)) = '{$the_date}', TRUE, NULL)) as delivered_count_for_rejected_by_same_day,
                    count(IF(delivered_result = '驳回' AND DATE(FROM_UNIXTIME(published_at)) <> '{$the_date}', TRUE, NULL)) as delivered_count_for_rejected_by_other_day
                "))
            ->get();

        $deliverer_of_today_for_all = $query_delivered_of_today[0]->delivered_count_for_all;
        $deliverer_of_today_for_all_by_same_day = $query_delivered_of_today[0]->delivered_count_for_all_by_same_day;
        $deliverer_of_today_for_all_by_other_day = $query_delivered_of_today[0]->delivered_count_for_all_by_other_day;

        $deliverer_of_today_for_completed = $query_delivered_of_today[0]->delivered_count_for_completed;
        $deliverer_of_today_for_completed_by_same_day = $query_delivered_of_today[0]->delivered_count_for_completed_by_same_day;
        $deliverer_of_today_for_completed_by_other_day = $query_delivered_of_today[0]->delivered_count_for_completed_by_other_day;

        $deliverer_of_today_for_inside = $query_delivered_of_today[0]->delivered_count_for_inside;
        $deliverer_of_today_for_inside_by_same_day = $query_delivered_of_today[0]->delivered_count_for_inside_by_same_day;
        $deliverer_of_today_for_inside_by_other_day = $query_delivered_of_today[0]->delivered_count_for_inside_by_other_day;

        $deliverer_of_today_for_tomorrow = $query_delivered_of_today[0]->delivered_count_for_tomorrow;
        $deliverer_of_today_for_tomorrow_by_same_day = $query_delivered_of_today[0]->delivered_count_for_tomorrow_by_same_day;
        $deliverer_of_today_for_tomorrow_by_other_day = $query_delivered_of_today[0]->delivered_count_for_tomorrow_by_other_day;

        $deliverer_of_today_for_repeated = $query_delivered_of_today[0]->delivered_count_for_repeated;
        $deliverer_of_today_for_repeated_by_same_day = $query_delivered_of_today[0]->delivered_count_for_repeated_by_same_day;
        $deliverer_of_today_for_repeated_by_other_day = $query_delivered_of_today[0]->delivered_count_for_repeated_by_other_day;

        $deliverer_of_today_for_rejected = $query_delivered_of_today[0]->delivered_count_for_rejected;
        $deliverer_of_today_for_rejected_by_same_day = $query_delivered_of_today[0]->delivered_count_for_rejected_by_same_day;
        $deliverer_of_today_for_rejected_by_other_day = $query_delivered_of_today[0]->delivered_count_for_rejected_by_other_day;


        $return_data['deliverer_of_today_for_all'] = $deliverer_of_today_for_all;
        $return_data['deliverer_of_today_for_all_by_same_day'] = $deliverer_of_today_for_all_by_same_day;
        $return_data['deliverer_of_today_for_all_by_other_day'] = $deliverer_of_today_for_all_by_other_day;

        $return_data['deliverer_of_today_for_completed'] = $deliverer_of_today_for_completed;
        $return_data['deliverer_of_today_for_completed_by_same_day'] = $deliverer_of_today_for_completed_by_same_day;
        $return_data['deliverer_of_today_for_completed_by_other_day'] = $deliverer_of_today_for_completed_by_other_day;

        $return_data['deliverer_of_today_for_inside'] = $deliverer_of_today_for_inside;
        $return_data['deliverer_of_today_for_inside_by_same_day'] = $deliverer_of_today_for_inside_by_same_day;
        $return_data['deliverer_of_today_for_inside_by_other_day'] = $deliverer_of_today_for_inside_by_other_day;

        $return_data['deliverer_of_today_for_tomorrow'] = $deliverer_of_today_for_tomorrow;
        $return_data['deliverer_of_today_for_tomorrow_by_same_day'] = $deliverer_of_today_for_tomorrow_by_same_day;
        $return_data['deliverer_of_today_for_tomorrow_by_other_day'] = $deliverer_of_today_for_tomorrow_by_other_day;

        $return_data['deliverer_of_today_for_repeated'] = $deliverer_of_today_for_repeated;
        $return_data['deliverer_of_today_for_repeated_by_same_day'] = $deliverer_of_today_for_repeated_by_same_day;
        $return_data['deliverer_of_today_for_repeated_by_other_day'] = $deliverer_of_today_for_repeated_by_other_day;

        $return_data['deliverer_of_today_for_rejected'] = $deliverer_of_today_for_rejected;
        $return_data['deliverer_of_today_for_rejected_by_same_day'] = $deliverer_of_today_for_rejected_by_same_day;
        $return_data['deliverer_of_today_for_rejected_by_other_day'] = $deliverer_of_today_for_rejected_by_other_day;








        // 分发当月数据
        $query_distributed_of_month = (clone $query_distributed)->whereBetween('updated_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->select(DB::raw("
                    count(*) as distributed_count_for_all
                "))
            ->get();
        $return_data['distributed_of_month_for_all'] = $query_distributed_of_month[0]->distributed_count_for_all;




        // 当月统计
        $query_order_of_month = (clone $query)->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->whereIn('created_type',[1,99])
            ->select(DB::raw("
                    count(*) as order_count_for_all,
                    count(IF(is_published = 0, TRUE, NULL)) as order_count_for_unpublished,
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_published,
                    
                    count(IF(is_published = 1 AND inspected_status <> 0, TRUE, NULL)) as order_count_for_inspected_all,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_inspected_accepted,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_inspected_accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_inspected_repeated,
                    count(IF(inspected_result = '拒绝', TRUE, NULL)) as order_count_for_inspected_refused,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered_all,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->get();


        $order_of_month_for_all = $query_order_of_month[0]->order_count_for_all;
        $order_of_month_for_unpublished = $query_order_of_month[0]->order_count_for_unpublished;
        $order_of_month_for_published = $query_order_of_month[0]->order_count_for_published;

        $return_data['order_of_month_for_all'] = $order_of_month_for_all;
        $return_data['order_of_month_for_unpublished'] = $order_of_month_for_unpublished;
        $return_data['order_of_month_for_published'] = $order_of_month_for_published;


        $order_of_month_for_inspected_all = $query_order_of_month[0]->order_count_for_inspected_all;
        $order_of_month_for_inspected_accepted = $query_order_of_month[0]->order_count_for_inspected_accepted;
        $order_of_month_for_inspected_accepted_inside = $query_order_of_month[0]->order_count_for_inspected_accepted_inside;
        $order_of_month_for_inspected_refused = $query_order_of_month[0]->order_count_for_inspected_refused;
        $order_of_month_for_inspected_repeated = $query_order_of_month[0]->order_count_for_inspected_repeated;

        $return_data['order_of_month_for_inspected_all'] = $order_of_month_for_inspected_all;
        $return_data['order_of_month_for_inspected_accepted'] = $order_of_month_for_inspected_accepted;
        $return_data['order_of_month_for_inspected_accepted_inside'] = $order_of_month_for_inspected_accepted_inside;
        $return_data['order_of_month_for_inspected_refused'] = $order_of_month_for_inspected_refused;
        $return_data['order_of_month_for_inspected_repeated'] = $order_of_month_for_inspected_repeated;


        $order_of_month_for_delivered_all = $query_order_of_month[0]->order_count_for_delivered_all;
        $order_of_month_for_delivered_completed = $query_order_of_month[0]->order_count_for_delivered_completed;
        $order_of_month_for_delivered_inside = $query_order_of_month[0]->order_count_for_delivered_inside;
        $order_of_month_for_delivered_tomorrow = $query_order_of_month[0]->order_count_for_delivered_tomorrow;
        $order_of_month_for_delivered_repeated = $query_order_of_month[0]->order_count_for_delivered_repeated;
        $order_of_month_for_delivered_rejected = $query_order_of_month[0]->order_count_for_delivered_rejected;
        $order_of_month_for_delivered_effective = $order_of_month_for_delivered_completed + $order_of_month_for_delivered_tomorrow + $order_of_month_for_delivered_inside;
        if($order_of_month_for_all)
        {
            $order_of_month_for_delivered_effective_rate = round(($order_of_month_for_delivered_effective * 100 / $order_of_month_for_all),2);
        }
        else $order_of_month_for_delivered_effective_rate = 0;

        $return_data['order_of_month_for_delivered_all'] = $order_of_month_for_delivered_all;
        $return_data['order_of_month_for_delivered_completed'] = $order_of_month_for_delivered_completed;
        $return_data['order_of_month_for_delivered_inside'] = $order_of_month_for_delivered_inside;
        $return_data['order_of_month_for_delivered_tomorrow'] = $order_of_month_for_delivered_tomorrow;
        $return_data['order_of_month_for_delivered_repeated'] = $order_of_month_for_delivered_repeated;
        $return_data['order_of_month_for_delivered_rejected'] = $order_of_month_for_delivered_rejected;
        $return_data['order_of_month_for_delivered_effective'] = $order_of_month_for_delivered_effective;
        $return_data['order_of_month_for_delivered_effective_rate'] = $order_of_month_for_delivered_effective_rate;




        $query_delivered_of_month = (clone $query)->whereBetween('delivered_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->whereIn('created_type',[1,99])
            ->select(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as delivered_count_for_all,
                    count(IF(delivered_status = 1 AND published_at > '{$the_month_start_timestamp}' AND published_at < '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_all_by_same_day,
                    count(IF(delivered_status = 1 AND published_at < '{$the_month_start_timestamp}' AND published_at > '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_all_by_other_day,
                    
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as delivered_count_for_completed,
                    count(IF(delivered_result = '已交付' AND published_at > '{$the_month_start_timestamp}' AND published_at < '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_completed_by_same_day,
                    count(IF(delivered_result = '已交付' AND published_at < '{$the_month_start_timestamp}' AND published_at > '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_completed_by_other_day,
                    
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as delivered_count_for_inside,
                    count(IF(delivered_result = '内部交付' AND published_at > '{$the_month_start_timestamp}' AND published_at < '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_inside_by_same_day,
                    count(IF(delivered_result = '内部交付' AND published_at < '{$the_month_start_timestamp}' AND published_at > '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_inside_by_other_day,
                    
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as delivered_count_for_tomorrow,
                    
                    count(IF(delivered_result = '重复', TRUE, NULL)) as delivered_count_for_repeated,
                    count(IF(delivered_result = '重复' AND published_at > '{$the_month_start_timestamp}' AND published_at < '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_repeated_by_same_day,
                    count(IF(delivered_result = '重复' AND published_at < '{$the_month_start_timestamp}' AND published_at > '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_repeated_by_other_day,
                    
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as delivered_count_for_rejected,
                    count(IF(delivered_result = '驳回' AND published_at > '{$the_month_start_timestamp}' AND published_at < '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_rejected_by_same_day,
                    count(IF(delivered_result = '驳回' AND published_at < '{$the_month_start_timestamp}' AND published_at > '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_rejected_by_other_day
                "))
            ->get();

        $deliverer_of_month_for_all = $query_delivered_of_month[0]->delivered_count_for_all;
        $deliverer_of_month_for_all_by_same_day = $query_delivered_of_month[0]->delivered_count_for_all_by_same_day;
        $deliverer_of_month_for_all_by_other_day = $query_delivered_of_month[0]->delivered_count_for_all_by_other_day;

        $deliverer_of_month_for_completed = $query_delivered_of_month[0]->delivered_count_for_completed;
        $deliverer_of_month_for_completed_by_same_day = $query_delivered_of_month[0]->delivered_count_for_completed_by_same_day;
        $deliverer_of_month_for_completed_by_other_day = $query_delivered_of_month[0]->delivered_count_for_completed_by_other_day;

        $deliverer_of_month_for_inside = $query_delivered_of_month[0]->delivered_count_for_inside;
        $deliverer_of_month_for_inside_by_same_day = $query_delivered_of_month[0]->delivered_count_for_inside_by_same_day;
        $deliverer_of_month_for_inside_by_other_day = $query_delivered_of_month[0]->delivered_count_for_inside_by_other_day;

        $deliverer_of_month_for_tomorrow = $query_delivered_of_month[0]->delivered_count_for_tomorrow;
        $deliverer_of_month_for_tomorrow_by_same_day = $query_delivered_of_month[0]->delivered_count_for_tomorrow_by_same_day;
        $deliverer_of_month_for_tomorrow_by_other_day = $query_delivered_of_month[0]->delivered_count_for_tomorrow_by_other_day;

        $deliverer_of_month_for_repeated = $query_delivered_of_month[0]->delivered_count_for_repeated;
        $deliverer_of_month_for_repeated_by_same_day = $query_delivered_of_month[0]->delivered_count_for_repeated_by_same_day;
        $deliverer_of_month_for_repeated_by_other_day = $query_delivered_of_month[0]->delivered_count_for_repeated_by_other_day;

        $deliverer_of_month_for_rejected = $query_delivered_of_month[0]->delivered_count_for_rejected;
        $deliverer_of_month_for_rejected_by_same_day = $query_delivered_of_month[0]->delivered_count_for_rejected_by_same_day;
        $deliverer_of_month_for_rejected_by_other_day = $query_delivered_of_month[0]->delivered_count_for_rejected_by_other_day;


        $return_data['deliverer_of_month_for_all'] = $deliverer_of_month_for_all;
        $return_data['deliverer_of_month_for_all_by_same_day'] = $deliverer_of_month_for_all_by_same_day;
        $return_data['deliverer_of_month_for_all_by_other_day'] = $deliverer_of_month_for_all_by_other_day;

        $return_data['deliverer_of_month_for_completed'] = $deliverer_of_month_for_completed;
        $return_data['deliverer_of_month_for_completed_by_same_day'] = $deliverer_of_month_for_completed_by_same_day;
        $return_data['deliverer_of_month_for_completed_by_other_day'] = $deliverer_of_month_for_completed_by_other_day;

        $return_data['deliverer_of_month_for_inside'] = $deliverer_of_month_for_inside;
        $return_data['deliverer_of_month_for_inside_by_same_day'] = $deliverer_of_month_for_inside_by_same_day;
        $return_data['deliverer_of_month_for_inside_by_other_day'] = $deliverer_of_month_for_inside_by_other_day;

        $return_data['deliverer_of_month_for_tomorrow'] = $deliverer_of_month_for_tomorrow;
        $return_data['deliverer_of_month_for_tomorrow_by_same_day'] = $deliverer_of_month_for_tomorrow_by_same_day;
        $return_data['deliverer_of_month_for_tomorrow_by_other_day'] = $deliverer_of_month_for_tomorrow_by_other_day;

        $return_data['deliverer_of_month_for_repeated'] = $deliverer_of_month_for_repeated;
        $return_data['deliverer_of_month_for_repeated_by_same_day'] = $deliverer_of_month_for_repeated_by_same_day;
        $return_data['deliverer_of_month_for_repeated_by_other_day'] = $deliverer_of_month_for_repeated_by_other_day;

        $return_data['deliverer_of_month_for_rejected'] = $deliverer_of_month_for_rejected;
        $return_data['deliverer_of_month_for_rejected_by_same_day'] = $deliverer_of_month_for_rejected_by_same_day;
        $return_data['deliverer_of_month_for_rejected_by_other_day'] = $deliverer_of_month_for_rejected_by_other_day;




        return response_success($return_data,"");
    }
    // 【统计】返回-财务-数据
    public function get_statistic_data_for_order($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_month  = isset($post_data['month']) ? $post_data['month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-1 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $the_last_month_timestamp = strtotime('last month', $the_month_timestamp);
        $the_last_month_start_date = date('Y-m-1',$the_last_month_timestamp); // 指定月份-上月-开始时间
        $the_last_month_ended_date = date('Y-m-t',$the_last_month_timestamp); // 指定月份-上月-开始时间
        $the_last_month_start_datetime = date('Y-m-1 00:00:00',$the_last_month_timestamp); // 指定月份-上月-开始时间
        $the_last_month_ended_datetime = date('Y-m-t 23:59:59',$the_last_month_timestamp); // 指定月份-上月-结束时间
        $the_last_month_start_timestamp = strtotime($the_last_month_start_datetime); // 指定月份-上月-开始时间戳
        $the_last_month_ended_timestamp = strtotime($the_last_month_ended_datetime); // 指定月份-上月-结束时间戳



        $type = isset($post_data['type']) ? $post_data['type']  : '';

        $staff_isset = 0;
        $client_isset = 0;
        $route_isset = 0;
        $pricing_isset = 0;
        $car_isset = 0;
        $trailer_isset = 0;


        // 员工
        if(isset($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1]))
            {
                $staff_isset = 1;
                $staff_id = $post_data['staff'];
            }
        }
        // 客户
        if(isset($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1]))
            {
                $client_isset = 1;
                $client_id = $post_data['client'];
            }
        }



        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');


        // 工单统计


        // 本月每日工单量
        $query_for_order_this_month = DK_Order::select('id','assign_time')
            ->whereBetween('assign_time',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(assign_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(assign_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(assign_time,'%e') as day,
                    count(*) as quantity,
                    sum(amount + oil_card_amount) as income_sum
                "));

        if($staff_isset) $query_for_order_this_month->where('creator_id', $staff_id);
        if($client_isset) $query_for_order_this_month->where('client_id', $client_id);


        $statistics_data_for_order_this_month = $query_for_order_this_month->get()->keyBy('day');
        $return_data['statistics_data_for_order_this_month'] = $statistics_data_for_order_this_month;

        // 上月每日工单量
        $query_for_order_last_month = DK_Order::select('id','assign_time')
            ->whereBetween('assign_time',[$the_last_month_start_timestamp,$the_last_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(assign_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(assign_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(assign_time,'%e') as day,
                    count(*) as quantity,
                    sum(amount + oil_card_amount) as income_sum
                "));

        if($staff_isset) $query_for_order_last_month->where('creator_id', $staff_id);
        if($client_isset) $query_for_order_last_month->where('client_id', $client_id);



        $statistics_data_for_order_last_month = $query_for_order_last_month->get()->keyBy('day');
        $return_data['statistics_data_for_order_last_month'] = $statistics_data_for_order_last_month;


        return response_success($return_data,"");
    }


    // 【统计】排名
    public function view_statistic_rank()
    {
        $this->get_me();
        $me = $this->me;

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        if($me->user_type == 81)
        {
            $view_data['department_district_id'] = $me->department_district_id;
            $department_group_list = DK_Department::select('id','name')->where('superior_department_id',$me->department_district_id)->get();
            $view_data['department_group_list'] = $department_group_list;
        }

        $view_data['menu_active_of_statistic_rank'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-rank';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_rank($post_data)
    {
        $this->get_me();
        $me = $this->me;



        $rank_object_type  = isset($post_data['rank_object_type'])  ? $post_data['rank_object_type']  : 'staff';
        $rank_staff_type  = isset($post_data['rank_staff_type'])  ? $post_data['rank_staff_type']  : 88;
//        dd($rank_staff_type);


        if($rank_staff_type == 41)
        {
            // 工单统计
            $query_order = DK_Order::select('department_manager_id')
                ->groupBy('department_manager_id');
        }
        else if($rank_staff_type == 81)
        {
            // 工单统计
            $query_order = DK_Order::select('department_manager_id')
                ->groupBy('department_manager_id');
        }
        else if($rank_staff_type == 84)
        {
            // 工单统计
            $query_order = DK_Order::select('department_supervisor_id')
                ->groupBy('department_supervisor_id');
        }
        else
        {
            // 工单统计
            $query_order = DK_Order::select('creator_id')
                ->groupBy('creator_id');
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');
            $query_order->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query_order->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
        }
        else
        {
        }

        $query_order->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "));



        if($rank_staff_type == 41)
        {
            // 工单统计
            $query_order = $query_order->get()->keyBy('department_manager_id')->toArray();
        }
        else if($rank_staff_type == 81)
        {
            // 工单统计
            $query_order = $query_order->get()->keyBy('department_manager_id')->toArray();
        }
        else if($rank_staff_type == 84)
        {
            // 工单统计
            $query_order = $query_order->get()->keyBy('department_supervisor_id')->toArray();
        }
        else
        {
            // 工单统计
            $query_order = $query_order->get()->keyBy('creator_id')->toArray();
        }




        $use = [];
        $use['is_manager'] = 0;
        $use['is_supervisor'] = 0;
        $use['is_customer_service'] = 0;
        $use['is_day'] = 0;
        $use['is_month'] = 0;
        $use['project_id'] = 0;
        $use['the_day'] = 0;
        $use['the_month_start_timestamp'] = 0;
        $use['the_month_ended_timestamp'] = 0;





        $query = DK_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id'])
            ->where('user_status',1)
            ->with([
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ]);


        // 部门
        if($me->user_type == 41)
        {
            // 根据部门（大区）查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 81)
        {
            // 根据部门（大区）查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据部门（小组）查看
            $query->where('department_group_id', $me->department_group_id);
        }


        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->where('department_district_id', $post_data['department_district']);
            }
        }
        // 部门-小组
        if(!empty($post_data['department_group']))
        {
            if(!in_array($post_data['department_group'],[-1,0]))
            {
                $query->where('department_group_id', $post_data['department_group']);
            }
        }


        if($rank_staff_type == 81)
        {
            $query->where('user_type', 81);
        }
        else if($rank_staff_type == 84)
        {
            $query->where('user_type', 84);
        }
        else
        {
            $query->where('department_district_id','>',0)
                ->where('department_group_id','>',0)
                ->whereIn('user_type',[81,84,88]);
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("department_district_id", "asc")->orderBy("department_group_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_all = $query_order[$v->id]['order_count_for_all'];
                $list[$k]->order_count_for_inspected = $query_order[$v->id]['order_count_for_inspected'];
                $list[$k]->order_count_for_accepted = $query_order[$v->id]['order_count_for_accepted'];
                $list[$k]->order_count_for_refused = $query_order[$v->id]['order_count_for_refused'];
                $list[$k]->order_count_for_repeated = $query_order[$v->id]['order_count_for_repeated'];
                $list[$k]->order_count_for_accepted_inside = $query_order[$v->id]['order_count_for_accepted_inside'];

                $list[$k]->order_count_for_delivered = $query_order[$v->id]['order_count_for_delivered'];
                $list[$k]->order_count_for_delivered_completed = $query_order[$v->id]['order_count_for_delivered_completed'];
                $list[$k]->order_count_for_delivered_inside = $query_order[$v->id]['order_count_for_delivered_inside'];
                $list[$k]->order_count_for_delivered_tomorrow = $query_order[$v->id]['order_count_for_delivered_tomorrow'];
                $list[$k]->order_count_for_delivered_repeated = $query_order[$v->id]['order_count_for_delivered_repeated'];
                $list[$k]->order_count_for_delivered_rejected = $query_order[$v->id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->order_count_for_all = 0;
                $list[$k]->order_count_for_inspected = 0;
                $list[$k]->order_count_for_accepted = 0;
                $list[$k]->order_count_for_refused = 0;
                $list[$k]->order_count_for_repeated = 0;
                $list[$k]->order_count_for_accepted_inside = 0;

                $list[$k]->order_count_for_delivered = 0;
                $list[$k]->order_count_for_delivered_completed = 0;
                $list[$k]->order_count_for_delivered_inside = 0;
                $list[$k]->order_count_for_delivered_tomorrow = 0;
                $list[$k]->order_count_for_delivered_repeated = 0;
                $list[$k]->order_count_for_delivered_rejected = 0;
            }

            // 审核
            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;

            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_inspected - $v->order_count_for_refused - $v->order_count_for_repeated;
            // 有效率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_effective = round(($v->order_count_for_effective * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_effective = 0;


            // 交付
            // 有效交付量
            $list[$k]->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow + $v->order_count_for_delivered_inside;

            // 有效交付率
            if($v->order_count_for_delivered > 0)
            {
                $list[$k]->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered),2);
            }
            else $list[$k]->order_rate_for_delivered_effective = 0;

            // 有效交付量
            $list[$k]->order_count_for_delivered_actual = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow;

            // 有效交付率
            if($v->order_count_for_delivered > 0)
            {
                $list[$k]->order_rate_for_delivered_actual = round(($v->order_count_for_delivered_actual * 100 / $v->order_count_for_delivered),2);
            }
            else $list[$k]->order_rate_for_delivered_actual = 0;

        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    // 【统计】员工排名
    public function view_statistic_rank_by_staff()
    {
        $this->get_me();
        $me = $this->me;

        $view_data['menu_active_of_statistic_rank_by_staff'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-rank-by-staff';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_rank_by_staff($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_User::select(['id','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->where('department_district_id','>',0)
            ->where('department_group_id','>',0)
            ->whereIn('user_type',[84,88]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");


        // 项目
        $project_id = 0;
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[0,-1]))
            {
//                $query->where('project_id', $post_data['project']);
                $project_id = $post_data['project'];
            }
        }

        // bumen经理
        if($me->user_type == 41)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据部门查看
            $query->where('department_group_id', $me->department_group_id);
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($the_day,$project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                },
                'order_list as order_count_for_inspected'=>function($query) use($the_day,$project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                },
                'order_list as order_count_for_accepted'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '通过');
                },
                'order_list as order_count_for_refused'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '拒绝');
                },
                'order_list as order_count_for_repeated'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '重复');
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '内部通过');
                }
            ]);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳


            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                },
                'order_list as order_count_for_inspected'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                },
                'order_list as order_count_for_accepted'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_refused'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '拒绝')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_repeated'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '重复')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '内部通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                }
            ]);

        }
        else
        {
            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_inspected'=>function($query) use($project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted'=>function($query) use($project_id) {
                    $query->where('inspected_result', '通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_refused'=>function($query) use($project_id) {
                    $query->where('inspected_result', '拒绝')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_repeated'=>function($query) use($project_id) {
                    $query->where('inspected_result', '重复')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($project_id) {
                    $query->where('inspected_result', '内部通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                }
            ]);
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("department_district_id", "asc")->orderBy("department_group_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;

            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_inspected - $v->order_count_for_refused - $v->order_count_for_repeated;
            // 有效率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_effective = round(($v->order_count_for_effective * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_effective = 0;
        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    public function get_statistic_data_for_rank_by_staff_of_group($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 员工统计
        $query_order = DK_Order::select('creator_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside
                "))
            ->groupBy('creator_id');

        // 项目
        $project_id = 0;
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[0,-1]))
            {
                $project_id = $post_data['project'];
                $query_order->where('project_id', $project_id);
            }
        }

        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');
            $query_order->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query_order->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
        }
        else
        {
        }

        $query_order = $query_order->get()->keyBy('creator_id')->toArray();


        $query = DK_User::select(['id','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->where('department_district_id','>',0)
            ->where('department_group_id','>',0)
            ->whereIn('user_type',[84,88]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        dd($me->department_district_id);


        // 部门经理
        if($me->user_type == 41)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据部门查看
            $query->where('department_group_id', $me->department_group_id);
        }



        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("department_district_id", "asc")->orderBy("department_group_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_all = $query_order[$v->id]['order_count_for_all'];
                $list[$k]->order_count_for_inspected = $query_order[$v->id]['order_count_for_inspected'];
                $list[$k]->order_count_for_accepted = $query_order[$v->id]['order_count_for_accepted'];
                $list[$k]->order_count_for_refused = $query_order[$v->id]['order_count_for_refused'];
                $list[$k]->order_count_for_repeated = $query_order[$v->id]['order_count_for_repeated'];
                $list[$k]->order_count_for_accepted_inside = $query_order[$v->id]['order_count_for_accepted_inside'];
            }
            else
            {
                $list[$k]->order_count_for_all = 0;
                $list[$k]->order_count_for_inspected = 0;
                $list[$k]->order_count_for_accepted = 0;
                $list[$k]->order_count_for_refused = 0;
                $list[$k]->order_count_for_repeated = 0;
                $list[$k]->order_count_for_accepted_inside = 0;
            }

            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;

            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_inspected - $v->order_count_for_refused - $v->order_count_for_repeated;
            // 有效率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_effective = round(($v->order_count_for_effective * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_effective = 0;
        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    // 【统计】部门排名
    public function view_statistic_rank_by_department()
    {
        $this->get_me();
        $me = $this->me;

        $view_data['menu_active_of_statistic_rank_by_department'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-rank-by-department';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_rank_by_department($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_User::select(['id','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->where('department_district_id','>',0)
            ->where('department_group_id','>',0)
            ->whereIn('user_type',[84,88]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");


        // 项目
        $project_id = 0;
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[0,-1]))
            {
//                $query->where('project_id', $post_data['project']);
                $project_id = $post_data['project'];
            }
        }

        // 客服经理
        if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据部门查看
            $query->where('department_group_id', $me->department_group_id);
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($the_day,$project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                },
                'order_list as order_count_for_inspected'=>function($query) use($the_day,$project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                },
                'order_list as order_count_for_accepted'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '通过');
                },
                'order_list as order_count_for_refused'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '拒绝');
                },
                'order_list as order_count_for_repeated'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '重复');
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '内部通过');
                }
            ]);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳


            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                },
                'order_list as order_count_for_inspected'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                },
                'order_list as order_count_for_accepted'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_refused'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '拒绝')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_repeated'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '重复')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '内部通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                }
            ]);

        }
        else
        {
            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_inspected'=>function($query) use($project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted'=>function($query) use($project_id) {
                    $query->where('inspected_result', '通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_refused'=>function($query) use($project_id) {
                    $query->where('inspected_result', '拒绝')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_repeated'=>function($query) use($project_id) {
                    $query->where('inspected_result', '重复')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($project_id) {
                    $query->where('inspected_result', '内部通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                }
            ]);
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("department_district_id", "asc")->orderBy("department_group_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;

            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_inspected - $v->order_count_for_refused - $v->order_count_for_repeated;
            // 有效率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_effective = round(($v->order_count_for_effective * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_effective = 0;
        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }


    // 【统计】近期表现
    public function view_statistic_recent()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,81,84])) return view($this->view_blade_403);

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        if($me->user_type == 81)
        {
            $view_data['department_district_id'] = $me->department_district_id;
            $department_group_list = DK_Department::select('id','name')->where('superior_department_id',$me->department_district_id)->get();
            $view_data['department_group_list'] = $department_group_list;
        }

        $view_data['menu_active_of_statistic_recent'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-recent';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_recent($post_data)
    {
        $this->get_me();
        $me = $this->me;



        $rank_object_type  = isset($post_data['rank_object_type'])  ? $post_data['rank_object_type']  : 'staff';
        $rank_staff_type  = isset($post_data['rank_staff_type'])  ? $post_data['rank_staff_type']  : 88;
//        dd($rank_staff_type);


        if($rank_staff_type == 41)
        {
            // 工单统计
            $query_order = DK_Order::select('department_manager_id','published_at')
                ->groupBy('department_manager_id');
        }
        else if($rank_staff_type == 81)
        {
            // 工单统计
            $query_order = DK_Order::select('department_manager_id','published_at')
                ->groupBy('department_manager_id');
        }
        else if($rank_staff_type == 84)
        {
            // 工单统计
            $query_order = DK_Order::select('department_supervisor_id','published_at')
                ->groupBy('department_supervisor_id');
        }
        else
        {
            // 工单统计
            $query_order = DK_Order::select('creator_id','published_at')
                ->groupBy('creator_id');
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');
            $query_order->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query_order->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
        }
        else
        {
            $query_order->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),'>',date("Y-m-d",strtotime("-7 day")))
                ->addSelect(DB::raw("
                    FROM_UNIXTIME(published_at,'%Y-%m-%d') as date_day,
                    FROM_UNIXTIME(published_at,'%e') as day,
                    count(*) as sum
                "))
                ->groupBy(DB::raw("DATE(FROM_UNIXTIME(published_at))"));
        }

        $query_order->addSelect(DB::raw("
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside
                "));



        if($rank_staff_type == 41)
        {
            // 工单统计
            $order_list = $query_order->get()->groupBy('department_manager_id')->toArray();
        }
        else if($rank_staff_type == 81)
        {
            // 工单统计
            $order_list = $query_order->get()->groupBy('department_manager_id')->toArray();
        }
        else if($rank_staff_type == 84)
        {
            // 工单统计
            $order_list = $query_order->get()->groupBy('department_supervisor_id')->toArray();
        }
        else
        {
            // 工单统计
            $order_list = $query_order->get();
        }

        foreach($order_list as $k => $v)
        {
            $v->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow + $v->order_count_for_delivered_inside;

            $date_day = date_create($v->date_day);
            $today = date_create(date('Y-m-d'));

            $diff = $today->diff($date_day)->days;
            $v->diff = $diff;
        }
        $order_list = $order_list->groupBy('creator_id');
        foreach($order_list as $k => $v)
        {
            $order_list[$k] = $v->keyBy('diff');
        }
        $order_list = $order_list->toArray();
//        dd($order_list);




        $use = [];
        $use['is_manager'] = 0;
        $use['is_supervisor'] = 0;
        $use['is_customer_service'] = 0;
        $use['is_day'] = 0;
        $use['is_month'] = 0;
        $use['project_id'] = 0;
        $use['the_day'] = 0;
        $use['the_month_start_timestamp'] = 0;
        $use['the_month_ended_timestamp'] = 0;





        $query = DK_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id'])
            ->where('user_status',1)
            ->with([
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ]);


        // 部门
        if($me->user_type == 41)
        {
            // 根据部门（大区）查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 81)
        {
            // 根据部门（大区）查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据部门（小组）查看
            $query->where('department_group_id', $me->department_group_id);
        }


        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->where('department_district_id', $post_data['department_district']);
            }
        }
        // 部门-小组
        if(!empty($post_data['department_group']))
        {
            if(!in_array($post_data['department_group'],[-1,0]))
            {
                $query->where('department_group_id', $post_data['department_group']);
            }
        }


        if($rank_staff_type == 81)
        {
            $query->where('user_type', 81);
        }
        else if($rank_staff_type == 84)
        {
            $query->where('user_type', 84);
        }
        else
        {
            $query->where('department_district_id','>',0)
                ->where('department_group_id','>',0)
                ->whereIn('user_type',[81,84,88]);
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("department_district_id", "asc")->orderBy("department_group_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            if(isset($order_list[$v->id]))
            {
//                if(isset($order_list[$v->id][7])) $list[$k]->order_7 = $order_list[$v->id][7]['order_count_for_delivered_effective'];
//                else $list[$k]->order_7 = 0;
                if(isset($order_list[$v->id][6])) $list[$k]->order_6 = $order_list[$v->id][6]['order_count_for_delivered_effective'];
                else $list[$k]->order_6 = 0;
                if(isset($order_list[$v->id][5])) $list[$k]->order_5 = $order_list[$v->id][5]['order_count_for_delivered_effective'];
                else $list[$k]->order_5 = 0;
                if(isset($order_list[$v->id][4])) $list[$k]->order_4 = $order_list[$v->id][4]['order_count_for_delivered_effective'];
                else $list[$k]->order_4 = 0;
                if(isset($order_list[$v->id][3])) $list[$k]->order_3 = $order_list[$v->id][3]['order_count_for_delivered_effective'];
                else $list[$k]->order_3 = 0;
                if(isset($order_list[$v->id][2])) $list[$k]->order_2 = $order_list[$v->id][2]['order_count_for_delivered_effective'];
                else $list[$k]->order_2 = 0;
                if(isset($order_list[$v->id][1])) $list[$k]->order_1 = $order_list[$v->id][1]['order_count_for_delivered_effective'];
                else $list[$k]->order_1 = 0;
                if(isset($order_list[$v->id][0])) $list[$k]->order_0 = $order_list[$v->id][0]['order_count_for_delivered_effective'];
                else $list[$k]->order_0 = 0;
            }
            else
            {
//                $list[$k]->order_7 = 0;
                $list[$k]->order_6 = 0;
                $list[$k]->order_5 = 0;
                $list[$k]->order_4 = 0;
                $list[$k]->order_3 = 0;
                $list[$k]->order_2 = 0;
                $list[$k]->order_1 = 0;
                $list[$k]->order_0 = 0;
            }


        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }


    // 【统计】客服看板
    public function view_statistic_customer_service()
    {
        $this->get_me();
        $me = $this->me;

        $view_data['menu_active_of_statistic_customer_service'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-customer-service';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_customer_service($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); }
            ])
            ->where('user_status',1)
            ->where('department_district_id','>',0)
            ->where('department_group_id','>',0)
            ->whereIn('user_type',[84,88]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");


        // 项目
        $project_id = 0;
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[0,-1]))
            {
//                $query->where('project_id', $post_data['project']);
                $project_id = $post_data['project'];
            }
        }

        // 客服经理
        if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据部门查看
            $query->where('department_group_id', $me->department_group_id);
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->with([
                'department_district_er' => function($query) use($the_day,$project_id) {
                    $query->select(['id','name','leader_id'])->with([
                        'leader' => function($query) use($the_day,$project_id) {
                            $query->select(['id','username'])
                                ->withCount([
                                    'order_list_for_manager as district_count_for_all' => function($query) use($the_day,$project_id) {
                                        $query->where('is_published', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_manager as district_count_for_inspected' => function($query) use($the_day,$project_id) {
                                        $query->where('is_published', 1)->where('inspected_status', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_manager as district_count_for_accepted' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '通过')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_manager as district_count_for_refused' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '拒绝')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_manager as district_count_for_repeated' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '重复')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    }
                                ]);
                        }
                    ]);
                },
                'department_group_er' => function($query) use($the_day,$project_id) {
                    $query->select(['id','name','leader_id'])->with([
                        'leader' => function($query) use($the_day,$project_id) {
                            $query->select(['id','username'])
                                ->withCount([
                                    'order_list_for_supervisor as group_count_for_all' => function($query) use($the_day,$project_id) {
                                        $query->where('is_published', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_supervisor as group_count_for_inspected' => function($query) use($the_day,$project_id) {
                                        $query->where('is_published', 1)->where('inspected_status', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_supervisor as group_count_for_accepted' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '通过')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_supervisor as group_count_for_refused' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '拒绝')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_supervisor as group_count_for_repeated' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '重复')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    }
                                ]);
                        }
                    ]);
                },
            ]);

            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($the_day,$project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                },
                'order_list as order_count_for_inspected'=>function($query) use($the_day,$project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                },
                'order_list as order_count_for_accepted'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '通过');
                },
                'order_list as order_count_for_refused'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '拒绝');
                },
                'order_list as order_count_for_repeated'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '重复');
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '内部通过');
                }
            ]);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query->with([
                'department_district_er' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->select(['id','name','leader_id'])->with([
                        'leader' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                            $query->select(['id','username'])
                                ->withCount([
                                    'order_list_for_manager as district_count_for_all' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('is_published', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_manager as district_count_for_inspected' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('is_published', 1)->where('inspected_status', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_manager as district_count_for_accepted' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('inspected_result', '通过')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_manager as district_count_for_refused' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('inspected_result', '拒绝')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_manager as district_count_for_repeated' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('inspected_result', '重复')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    }
                                ]);
                        }
                    ]);
                },
                'department_group_er' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->select(['id','name','leader_id'])->with([
                        'leader' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                            $query->select(['id','username'])
                                ->withCount([
                                    'order_list_for_supervisor as group_count_for_all' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('is_published', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_supervisor as group_count_for_inspected' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('is_published', 1)->where('inspected_status', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_supervisor as group_count_for_accepted' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('inspected_result', '通过')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_supervisor as group_count_for_refused' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('inspected_result', '拒绝')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_supervisor as group_count_for_repeated' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('inspected_result', '重复')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    }
                                ]);
                        }
                    ]);
                },
            ]);

            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                },
                'order_list as order_count_for_inspected'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                },
                'order_list as order_count_for_accepted'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_refused'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '拒绝')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_repeated'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '重复')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '内部通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                }
            ]);

        }
        else
        {
            $query->with([
                'department_district_er' => function($query) use($project_id) {
                    $query->select(['id','name','leader_id'])->with([
                        'leader' => function($query) use($project_id) {
                            $query->select(['id','username'])
                                ->withCount([
                                    'order_list_for_manager as district_count_for_all' => function($query) use($project_id) {
                                        $query->where('is_published', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_manager as district_count_for_inspected' => function($query) use($project_id) {
                                        $query->where('is_published', 1)->where('inspected_status', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_manager as district_count_for_accepted' => function($query) use($project_id) {
                                        $query->where('inspected_result', '通过')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_manager as district_count_for_refused' => function($query) use($project_id) {
                                        $query->where('inspected_result', '拒绝')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_manager as district_count_for_repeated' => function($query) use($project_id) {
                                        $query->where('inspected_result', '重复')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    }
                                ]);
                        }
                    ]);
                },
                'department_group_er' => function($query) use($project_id) {
                    $query->select(['id','name','leader_id'])->with([
                        'leader' => function($query) use($project_id) {
                            $query->select(['id','username'])
                                ->withCount([
                                    'order_list_for_supervisor as group_count_for_all' => function($query) use($project_id) {
                                        $query->where('is_published', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_supervisor as group_count_for_inspected' => function($query) use($project_id) {
                                        $query->where('is_published', 1)->where('inspected_status', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_supervisor as group_count_for_accepted' => function($query) use($project_id) {
                                        $query->where('inspected_result', '通过')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_supervisor as group_count_for_refused' => function($query) use($project_id) {
                                        $query->where('inspected_result', '拒绝')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_supervisor as group_count_for_repeated' => function($query) use($project_id) {
                                        $query->where('inspected_result', '重复')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    }
                                ]);
                        }
                    ]);
                },
            ]);

            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_inspected'=>function($query) use($project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted'=>function($query) use($project_id) {
                    $query->where('inspected_result', '通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_refused'=>function($query) use($project_id) {
                    $query->where('inspected_result', '拒绝')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_repeated'=>function($query) use($project_id) {
                    $query->where('inspected_result', '重复')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($project_id) {
                    $query->where('inspected_result', '内部通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                }
            ]);
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("department_district_id", "asc")->orderBy("department_group_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;

            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_inspected - $v->order_count_for_refused - $v->order_count_for_repeated;




            // 小组数据
            // 总单数
            if(isset($v->department_group_er->leader->group_count_for_all))
            {
                $v->group_count_for_all = $v->department_group_er->leader->group_count_for_all;
            }
            else $v->group_count_for_all = 0;
            // 已审核单数
            if(isset($v->department_group_er->leader->group_count_for_inspected))
            {
                $v->group_count_for_inspected = $v->department_group_er->leader->group_count_for_inspected;
            }
            else $v->group_count_for_inspected = 0;
            // 通过单数
            if(isset($v->department_group_er->leader->group_count_for_accepted))
            {
                $v->group_count_for_accepted = $v->department_group_er->leader->group_count_for_accepted;
            }
            // 拒绝单数
            if(isset($v->department_group_er->leader->group_count_for_refused))
            {
                $v->group_count_for_refused = $v->department_group_er->leader->group_count_for_refused;
            }
            // 重复单数
            if(isset($v->department_group_er->leader->group_count_for_repeated))
            {
                $v->group_count_for_repeated = $v->department_group_er->leader->group_count_for_repeated;
            }
            $v->group_count_for_repeated = 0;

            // 有效单量
            $v->group_count_for_effective = $v->group_count_for_inspected - $v->group_count_for_refused - $v->group_count_for_repeated;

            $v->group_count_for_accepted_inside = 0;

            // 有效率
            if($v->group_count_for_all > 0)
            {
                $v->group_rate_for_accepted = round(($v->group_count_for_accepted * 100 / $v->group_count_for_all),2);
            }
            else $v->group_rate_for_accepted = 0;




            // 大区数据
            // 总单数
            if(isset($v->department_district_er->leader->district_count_for_all))
            {
                $v->district_count_for_all = $v->department_district_er->leader->district_count_for_all;
            }
            else $v->district_count_for_all = 0;
            // 已审核单数
            if(isset($v->department_district_er->leader->district_count_for_inspected))
            {
                $v->district_count_for_inspected = $v->department_district_er->leader->district_count_for_inspected;
            }
            else $v->district_count_for_inspected = 0;
            // 通过单数
            if(isset($v->department_district_er->leader->district_count_for_accepted))
            {
                $v->district_count_for_accepted = $v->department_district_er->leader->district_count_for_accepted;
            }
            else $v->district_count_for_accepted = 0;
            // 拒绝单数
            if(isset($v->department_district_er->leader->district_count_for_refused))
            {
                $v->district_count_for_refused = $v->department_district_er->leader->district_count_for_refused;
            }
            else $v->district_count_for_refused = 0;
            // 重复单数
            if(isset($v->department_district_er->leader->district_count_for_repeated))
            {
                $v->district_count_for_repeated = $v->department_district_er->leader->district_count_for_repeated;
            }
            else $v->district_count_for_repeated = 0;

            // 有效单量
            $v->district_count_for_effective = $v->district_count_for_inspected - $v->district_count_for_refused - $v->district_count_for_repeated;

            $v->district_count_for_accepted_inside = 0;

            // 有效率
            if($v->district_count_for_all > 0)
            {
                $v->district_rate_for_accepted = round(($v->district_count_for_accepted * 100 / $v->district_count_for_all),2);
            }
            else $v->district_rate_for_accepted = 0;

            $v->district_merge = 0;
            $v->group_merge = 0;
        }
//        dd($list->toArray());

        $grouped_by_district = $list->groupBy('department_district_id');
        foreach ($grouped_by_district as $k => $v)
        {
            $v[0]->district_merge = count($v);

            $grouped_by_group = $list->groupBy('department_group_id');
            foreach ($grouped_by_group as $key => $val)
            {
                $val[0]->group_merge = count($val);
            }
        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    public function get_statistic_data_for_customer_service_by_group($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 员工统计
        $query_order = DK_Order::select('creator_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->groupBy('creator_id');


        // 员工（经理）统计
        $query_order_for_manager = DK_Order::select('department_manager_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->groupBy('department_manager_id');


        // 员工（组长）统计
        $query_order_for_supervisor = DK_Order::select('department_supervisor_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->groupBy('department_supervisor_id');


        // 项目
        $project_id = 0;
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[0,-1]))
            {
                $project_id = $post_data['project'];

                $query_order->where('project_id', $project_id);
                $query_order_for_manager->where('project_id', $project_id);
                $query_order_for_supervisor->where('project_id', $project_id);
            }
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query_order->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
            $query_order_for_manager->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
            $query_order_for_supervisor->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);

        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query_order->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
            $query_order_for_manager->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
            $query_order_for_supervisor->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
        }
        else
        {
        }


        $query_order = $query_order->get()->keyBy('creator_id')->toArray();
        $query_order_for_manager = $query_order_for_manager->get()->keyBy('department_manager_id')->toArray();
        $query_order_for_supervisor = $query_order_for_supervisor->get()->keyBy('department_supervisor_id')->toArray();
//        dd($query_order);



        $query = DK_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
            ->with([
//                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name','leader_id'])->with(['leader']); },
                'department_group_er' => function($query) { $query->select(['id','name','leader_id'])->with(['leader']); }
            ])
            ->where('user_status',1)
            ->where('department_district_id','>',0)
            ->where('department_group_id','>',0)
            ->whereIn('user_type',[84,88]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");



        // 部门经理
        if($me->user_type == 41)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }
        // 客服经理
        else if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据部门查看
            $query->where('department_group_id', $me->department_group_id);
        }



        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("department_district_id", "asc")->orderBy("department_group_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {

            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_all = $query_order[$v->id]['order_count_for_all'];
                $list[$k]->order_count_for_inspected = $query_order[$v->id]['order_count_for_inspected'];
                $list[$k]->order_count_for_accepted = $query_order[$v->id]['order_count_for_accepted'];
                $list[$k]->order_count_for_refused = $query_order[$v->id]['order_count_for_refused'];
                $list[$k]->order_count_for_repeated = $query_order[$v->id]['order_count_for_repeated'];
                $list[$k]->order_count_for_accepted_inside = $query_order[$v->id]['order_count_for_accepted_inside'];

                $list[$k]->order_count_for_delivered = $query_order[$v->id]['order_count_for_delivered'];
                $list[$k]->order_count_for_delivered_completed = $query_order[$v->id]['order_count_for_delivered_completed'];
                $list[$k]->order_count_for_delivered_inside = $query_order[$v->id]['order_count_for_delivered_inside'];
                $list[$k]->order_count_for_delivered_tomorrow = $query_order[$v->id]['order_count_for_delivered_tomorrow'];
                $list[$k]->order_count_for_delivered_repeated = $query_order[$v->id]['order_count_for_delivered_repeated'];
                $list[$k]->order_count_for_delivered_rejected = $query_order[$v->id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->order_count_for_all = 0;
                $list[$k]->order_count_for_inspected = 0;
                $list[$k]->order_count_for_accepted = 0;
                $list[$k]->order_count_for_refused = 0;
                $list[$k]->order_count_for_repeated = 0;
                $list[$k]->order_count_for_accepted_inside = 0;

                $list[$k]->order_count_for_delivered = 0;
                $list[$k]->order_count_for_delivered_completed = 0;
                $list[$k]->order_count_for_delivered_inside = 0;
                $list[$k]->order_count_for_delivered_tomorrow = 0;
                $list[$k]->order_count_for_delivered_repeated = 0;
                $list[$k]->order_count_for_delivered_rejected = 0;
            }

            // 审核
            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_inspected - $v->order_count_for_refused - $v->order_count_for_repeated;

            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;

            // 交付
            // 有效交付量
            $v->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow + $v->order_count_for_delivered_inside;

            // 有效交付率
            if($v->order_count_for_delivered > 0)
            {
                $v->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered),2);
            }
            else $v->order_rate_for_delivered_effective = 0;

            // 实际产量
            $v->order_count_for_delivered_actual = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow;
            // 实际产率
            if($v->order_count_for_delivered > 0)
            {
                $v->order_rate_for_delivered_actual = round(($v->order_count_for_delivered_actual * 100 / $v->order_count_for_delivered),2);
            }
            else $v->order_rate_for_delivered_actual = 0;




            // 组长
            if($v->department_group_er) $supervisor_id = $v->department_group_er->leader_id;
            else $supervisor_id = -1;
            if($v->department_group_er && isset($query_order_for_supervisor[$supervisor_id]))
            {
                $list[$k]->group_count_for_all = $query_order_for_supervisor[$supervisor_id]['order_count_for_all'];
                $list[$k]->group_count_for_inspected = $query_order_for_supervisor[$supervisor_id]['order_count_for_inspected'];
                $list[$k]->group_count_for_accepted = $query_order_for_supervisor[$supervisor_id]['order_count_for_accepted'];
                $list[$k]->group_count_for_refused = $query_order_for_supervisor[$supervisor_id]['order_count_for_refused'];
                $list[$k]->group_count_for_repeated = $query_order_for_supervisor[$supervisor_id]['order_count_for_repeated'];

                $list[$k]->group_count_for_delivered = $query_order_for_supervisor[$supervisor_id]['order_count_for_delivered'];
                $list[$k]->group_count_for_delivered_completed = $query_order_for_supervisor[$supervisor_id]['order_count_for_delivered_completed'];
                $list[$k]->group_count_for_delivered_inside = $query_order_for_supervisor[$supervisor_id]['order_count_for_delivered_inside'];
                $list[$k]->group_count_for_delivered_tomorrow = $query_order_for_supervisor[$supervisor_id]['order_count_for_delivered_tomorrow'];
                $list[$k]->group_count_for_delivered_repeated = $query_order_for_supervisor[$supervisor_id]['order_count_for_delivered_repeated'];
                $list[$k]->group_count_for_delivered_rejected = $query_order_for_supervisor[$supervisor_id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->group_count_for_all = 0;
                $list[$k]->group_count_for_inspected = 0;
                $list[$k]->group_count_for_accepted = 0;
                $list[$k]->group_count_for_refused = 0;
                $list[$k]->group_count_for_repeated = 0;

                $list[$k]->group_count_for_delivered = 0;
                $list[$k]->group_count_for_delivered_completed = 0;
                $list[$k]->group_count_for_delivered_inside = 0;
                $list[$k]->group_count_for_delivered_tomorrow = 0;
                $list[$k]->group_count_for_delivered_repeated = 0;
                $list[$k]->group_count_for_delivered_rejected = 0;
            }

            // 审核
            $v->group_count_for_accepted_inside = 0;
            // 有效单量
            $v->group_count_for_effective = $v->group_count_for_inspected - $v->group_count_for_refused - $v->group_count_for_repeated;
            // 有效率
            if($v->group_count_for_all > 0)
            {
                $v->group_rate_for_accepted = round(($v->group_count_for_accepted * 100 / $v->group_count_for_all),2);
            }
            else $v->group_rate_for_accepted = 0;


            // 交付
            // 有效交付量
            $v->group_count_for_delivered_effective = $v->group_count_for_delivered_completed + $v->group_count_for_delivered_tomorrow + $v->group_count_for_delivered_inside;
            // 有效交付率
            if($v->group_count_for_delivered > 0)
            {
                $v->group_rate_for_delivered_effective = round(($v->group_count_for_delivered_effective * 100 / $v->group_count_for_delivered),2);
            }
            else $v->group_rate_for_delivered_effective = 0;

            // 实际产量
            $v->group_count_for_delivered_actual = $v->group_count_for_delivered_completed + $v->group_count_for_delivered_tomorrow;
            // 实际产率
            if($v->group_count_for_delivered > 0)
            {
                $v->group_rate_for_delivered_actual = round(($v->group_count_for_delivered_actual * 100 / $v->group_count_for_delivered),2);
            }
            else $v->group_rate_for_delivered_actual = 0;




            // 经理
            if($v->department_district_er) $manager_id = $v->department_district_er->leader_id;
            else $manager_id = -1;
            if($v->department_district_er && isset($query_order_for_manager[$manager_id]))
            {
                $list[$k]->district_count_for_all = $query_order_for_manager[$manager_id]['order_count_for_all'];
                $list[$k]->district_count_for_inspected = $query_order_for_manager[$manager_id]['order_count_for_inspected'];
                $list[$k]->district_count_for_accepted = $query_order_for_manager[$manager_id]['order_count_for_accepted'];
                $list[$k]->district_count_for_refused = $query_order_for_manager[$manager_id]['order_count_for_refused'];
                $list[$k]->district_count_for_repeated = $query_order_for_manager[$manager_id]['order_count_for_repeated'];

                $list[$k]->district_count_for_delivered = $query_order_for_manager[$manager_id]['order_count_for_delivered'];
                $list[$k]->district_count_for_delivered_completed = $query_order_for_manager[$manager_id]['order_count_for_delivered_completed'];
                $list[$k]->district_count_for_delivered_inside = $query_order_for_manager[$manager_id]['order_count_for_delivered_inside'];
                $list[$k]->district_count_for_delivered_tomorrow = $query_order_for_manager[$manager_id]['order_count_for_delivered_tomorrow'];
                $list[$k]->district_count_for_delivered_repeated = $query_order_for_manager[$manager_id]['order_count_for_delivered_repeated'];
                $list[$k]->district_count_for_delivered_rejected = $query_order_for_manager[$manager_id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->district_count_for_all = 0;
                $list[$k]->district_count_for_inspected = 0;
                $list[$k]->district_count_for_accepted = 0;
                $list[$k]->district_count_for_refused = 0;
                $list[$k]->district_count_for_repeated = 0;

                $list[$k]->district_count_for_delivered = 0;
                $list[$k]->district_count_for_delivered_completed = 0;
                $list[$k]->district_count_for_delivered_inside = 0;
                $list[$k]->district_count_for_delivered_tomorrow = 0;
                $list[$k]->district_count_for_delivered_repeated = 0;
                $list[$k]->district_count_for_delivered_rejected = 0;
            }

            // 审核
            $v->district_count_for_accepted_inside = 0;
            // 有效单量
            $v->district_count_for_effective = $v->district_count_for_inspected - $v->district_count_for_refused - $v->district_count_for_repeated;
            // 有效率
            if($v->district_count_for_all > 0)
            {
                $v->district_rate_for_accepted = round(($v->district_count_for_accepted * 100 / $v->district_count_for_all),2);
            }
            else $v->district_rate_for_accepted = 0;


            // 交付
            // 有效交付量
            $v->district_count_for_delivered_effective = $v->district_count_for_delivered_completed + $v->district_count_for_delivered_tomorrow + $v->district_count_for_delivered_inside;
            // 有效交付率
            if($v->district_count_for_delivered > 0)
            {
                $v->district_rate_for_delivered_effective = round(($v->district_count_for_delivered_effective * 100 / $v->district_count_for_delivered),2);
            }
            else $v->district_rate_for_delivered_effective = 0;

            // 实际产量
            $v->district_count_for_delivered_actual = $v->district_count_for_delivered_completed + $v->district_count_for_delivered_tomorrow;
            // 实际产率
            if($v->district_count_for_delivered > 0)
            {
                $v->district_rate_for_delivered_actual = round(($v->district_count_for_delivered_actual * 100 / $v->district_count_for_delivered),2);
            }
            else $v->district_rate_for_delivere_actual = 0;


            $v->district_merge = 0;
            $v->group_merge = 0;
        }
//        dd($list->toArray());

        $grouped_by_district = $list->groupBy('department_district_id');
        foreach ($grouped_by_district as $k => $v)
        {
            $v[0]->district_merge = count($v);

            $grouped_by_group = $list->groupBy('department_group_id');
            foreach ($grouped_by_group as $key => $val)
            {
                $val[0]->group_merge = count($val);
            }
        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    // 【统计】质检看板
    public function view_statistic_inspector()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,71,61])) return view($this->view_blade_403);

        $view_data['menu_active_of_statistic_inspector'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-inspector';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_inspector($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); }
            ])
            ->where('user_status',1)
            ->whereIn('user_category',[11])
            ->whereIn('user_type',[71,77]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        // 审核经理
        if($me->user_type == 71)
        {
            $query->where(function ($query) use($me) {
                $query->where('id',$me->id)->orWhereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );
            });
        }

        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->withCount([
                'order_list_for_inspector as order_count_for_inspected'=>function($query) use($the_day) {
                    $query->where('inspected_status', '<>', 0)
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(inspected_at))"),$the_day);
                }
            ]);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query->withCount([
                'order_list_for_inspector as order_count_for_inspected'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp) {
                    $query->where('inspected_status', '<>', 0)
                        ->whereBetween('inspected_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                }
            ]);
        }
        else
        {
            $query->withCount([
                'order_list_for_inspector as order_count_for_inspected'=>function($query) {
                    $query->where('inspected_status', '<>', 0);
                }
            ]);
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("superior_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            if($v->user_type == 71)
            {
                $v->superior_id = $v->id;
                $v->superior = $v->id;
                $superior = ['id'=>$v->id,'username'=>$v->username,'true_name'=>$v->true_name];
                $v->superior = json_decode(json_encode($superior));
                $list[$k]->superior =  json_decode(json_encode($superior));
            }
        }
//        dd($list->toArray());

        $a = [];
        $list = $list->sortBy('superior_id');
        $grouped = $list->sortBy('superior_id')->groupBy('superior_id');
        foreach ($grouped as $k => $v)
        {
            $order_sum_for_all = 0;
            $order_sum_for_inspected = 0;

            foreach ($v as $key => $val)
            {
//                $order_sum_for_all += $val->order_count_for_all;
                $order_sum_for_inspected += $val->order_count_for_inspected;
            }


            foreach ($v as $key => $val)
            {
                $v[$key]->merge = 0;
//                $v[$key]->order_sum_for_all = $order_sum_for_all;
                $v[$key]->order_sum_for_inspected = $order_sum_for_inspected;

//                if($order_sum_for_all > 0)
//                {
//                    $v[$key]->order_average_rate_for_inspected = round(($order_sum_for_inspected * 100 / $order_sum_for_all),2);
//                }
//                else $v[$key]->order_average_rate_for_inspected = 0;
            }

            $v[0]->merge = count($v);
        }
        $collapsed = $grouped->collapse();

        return datatable_response($collapsed, $draw, $total);
    }
    public function get_statistic_data_for_inspector_by_group($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 员工统计
        $query_order = DK_Order::select('inspector_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝', TRUE, NULL)) as order_count_for_refused
                "))
            ->groupBy('inspector_id');


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query_order->whereDate(DB::raw("DATE(FROM_UNIXTIME(inspected_at))"),$the_day);

        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query_order->whereBetween('inspected_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
        }
        else
        {
        }


        $query_order = $query_order->get()->keyBy('inspector_id')->toArray();




        $query = DK_User::select(['id','mobile','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name','leader_id']); },
            ])
            ->where('user_status',1)
            ->whereIn('user_category',[11])
            ->whereIn('user_type',[71,77]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        // 审核经理
//        if($me->user_type == 71)
//        {
//            $query->where(function ($query) use($me) {
//                $query->where('id',$me->id)->orWhereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );
//            });
//        }

        // 根据部门查看
        if($me->department_district_id > 0)
        {
            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();


        // 数据拼接
        foreach ($list as $k => $v)
        {
            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_inspected = $query_order[$v->id]['order_count_for_inspected'];
                $list[$k]->order_count_for_accepted = $query_order[$v->id]['order_count_for_accepted'];
                $list[$k]->order_count_for_refused = $query_order[$v->id]['order_count_for_refused'];
            }
            else
            {
                $list[$k]->order_count_for_inspected = 0;
                $list[$k]->order_count_for_accepted = 0;
                $list[$k]->order_count_for_refused = 0;
            }
        }



        foreach ($list as $k => $v)
        {
            if($v->user_type == 71)
            {
                $v->superior_id = $v->id;
                $v->superior = $v->id;
                $superior = ['id'=>$v->id,'username'=>$v->username,'true_name'=>$v->true_name];
                $v->superior = json_decode(json_encode($superior));
                $list[$k]->superior =  json_decode(json_encode($superior));
            }
        }
//        dd($list->toArray());

        $a = [];
//        $list = $list->sortBy('department_district_id');
//        $grouped = $list->sortBy('department_district_id')->groupBy('department_district_id');
        $grouped = $list->groupBy('department_district_id');
        foreach ($grouped as $k => $v)
        {
            $order_sum_for_all = 0;
            $order_sum_for_inspected = 0;

            foreach ($v as $key => $val)
            {
//                $order_sum_for_all += $val->order_count_for_all;
                $order_sum_for_inspected += $val->order_count_for_inspected;
            }


            foreach ($v as $key => $val)
            {
                $v[$key]->merge = 0;
//                $v[$key]->order_sum_for_all = $order_sum_for_all;
                $v[$key]->order_sum_for_inspected = $order_sum_for_inspected;

//                if($order_sum_for_all > 0)
//                {
//                    $v[$key]->order_average_rate_for_inspected = round(($order_sum_for_inspected * 100 / $order_sum_for_all),2);
//                }
//                else $v[$key]->order_average_rate_for_inspected = 0;
            }

            $v[0]->merge = count($v);
        }
        $collapsed = $grouped->collapse();

        return datatable_response($collapsed, $draw, $total);
    }
    // 【统计】运营看板
    public function view_statistic_deliverer()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,61])) return view($this->view_blade_403);

        $view_data['menu_active_of_statistic_operation'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-deliverer';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_deliverer($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); }
            ])
            ->where('user_status',1)
            ->whereIn('user_category',[11])
            ->whereIn('user_type',[61,66]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        // 运营经理
        if($me->user_type == 61)
        {
            $query->where(function ($query) use($me) {
                $query->where('id',$me->id)->orWhereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );
            });
        }

        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->withCount([
                'order_list_for_deliverer as order_count_for_delivered'=>function($query) use($the_day) {
                    $query->where('inspected_status', '<>', 0)
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(delivered_at))"),$the_day);
                }
            ]);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query->withCount([
                'order_list_for_inspector as order_count_for_inspected'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp) {
                    $query->where('inspected_status', '<>', 0)
                        ->whereBetween('inspected_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                }
            ]);
        }
        else
        {
            $query->withCount([
                'order_list_for_inspector as order_count_for_inspected'=>function($query) {
                    $query->where('inspected_status', '<>', 0);
                }
            ]);
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("superior_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            if($v->user_type == 71)
            {
                $v->superior_id = $v->id;
                $v->superior = $v->id;
                $superior = ['id'=>$v->id,'username'=>$v->username,'true_name'=>$v->true_name];
                $v->superior = json_decode(json_encode($superior));
                $list[$k]->superior =  json_decode(json_encode($superior));
            }
        }
//        dd($list->toArray());

        $a = [];
        $list = $list->sortBy('superior_id');
        $grouped = $list->sortBy('superior_id')->groupBy('superior_id');
        foreach ($grouped as $k => $v)
        {
            $order_sum_for_all = 0;
            $order_sum_for_inspected = 0;

            foreach ($v as $key => $val)
            {
//                $order_sum_for_all += $val->order_count_for_all;
                $order_sum_for_inspected += $val->order_count_for_inspected;
            }


            foreach ($v as $key => $val)
            {
                $v[$key]->merge = 0;
//                $v[$key]->order_sum_for_all = $order_sum_for_all;
                $v[$key]->order_sum_for_inspected = $order_sum_for_inspected;

//                if($order_sum_for_all > 0)
//                {
//                    $v[$key]->order_average_rate_for_inspected = round(($order_sum_for_inspected * 100 / $order_sum_for_all),2);
//                }
//                else $v[$key]->order_average_rate_for_inspected = 0;
            }

            $v[0]->merge = count($v);
        }
        $collapsed = $grouped->collapse();

        return datatable_response($collapsed, $draw, $total);
    }
    public function get_statistic_data_for_deliverer_by_group($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,61])) return view($this->view_blade_403);

        // 员工统计
        $query_order = DK_Order::select('inspector_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(delivered_result = '拒绝', TRUE, NULL)) as order_count_for_refused
                "))
            ->groupBy('deliverer_id');


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query_order->whereDate(DB::raw("DATE(FROM_UNIXTIME(delivered_at))"),$the_day);

        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query_order->whereBetween('inspected_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
        }
        else
        {
        }


        $query_order = $query_order->get()->keyBy('inspector_id')->toArray();




        $query = DK_User::select(['id','mobile','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name','leader_id']); },
            ])
            ->where('user_status',1)
            ->whereIn('user_category',[11])
            ->whereIn('user_type',[71,77]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        // 审核经理
//        if($me->user_type == 71)
//        {
//            $query->where(function ($query) use($me) {
//                $query->where('id',$me->id)->orWhereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );
//            });
//        }

        // 根据部门查看
        if($me->department_district_id > 0)
        {
            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();


        // 数据拼接
        foreach ($list as $k => $v)
        {
            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_inspected = $query_order[$v->id]['order_count_for_inspected'];
                $list[$k]->order_count_for_accepted = $query_order[$v->id]['order_count_for_accepted'];
                $list[$k]->order_count_for_refused = $query_order[$v->id]['order_count_for_refused'];
            }
            else
            {
                $list[$k]->order_count_for_inspected = 0;
                $list[$k]->order_count_for_accepted = 0;
                $list[$k]->order_count_for_refused = 0;
            }
        }



        foreach ($list as $k => $v)
        {
            if($v->user_type == 71)
            {
                $v->superior_id = $v->id;
                $v->superior = $v->id;
                $superior = ['id'=>$v->id,'username'=>$v->username,'true_name'=>$v->true_name];
                $v->superior = json_decode(json_encode($superior));
                $list[$k]->superior =  json_decode(json_encode($superior));
            }
        }
//        dd($list->toArray());

        $a = [];
//        $list = $list->sortBy('department_district_id');
//        $grouped = $list->sortBy('department_district_id')->groupBy('department_district_id');
        $grouped = $list->groupBy('department_district_id');
        foreach ($grouped as $k => $v)
        {
            $order_sum_for_all = 0;
            $order_sum_for_inspected = 0;

            foreach ($v as $key => $val)
            {
//                $order_sum_for_all += $val->order_count_for_all;
                $order_sum_for_inspected += $val->order_count_for_inspected;
            }


            foreach ($v as $key => $val)
            {
                $v[$key]->merge = 0;
//                $v[$key]->order_sum_for_all = $order_sum_for_all;
                $v[$key]->order_sum_for_inspected = $order_sum_for_inspected;

//                if($order_sum_for_all > 0)
//                {
//                    $v[$key]->order_average_rate_for_inspected = round(($order_sum_for_inspected * 100 / $order_sum_for_all),2);
//                }
//                else $v[$key]->order_average_rate_for_inspected = 0;
            }

            $v[0]->merge = count($v);
        }
        $collapsed = $grouped->collapse();

        return datatable_response($collapsed, $draw, $total);
    }




    // 【统计】交付看板
    public function view_statistic_delivery()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,81,84,71,77,61,66])) return view($this->view_blade_403);

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_delivery'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-delivery';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_delivery($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        if(in_array($me->user_type,[41,81,84]))
        {
            $department_district_id = $me->department_district_id;
        }
        else $department_district_id = 0;


        // 工单统计
        $query_order = DK_Order::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(delivered_at))"),$the_day)
            ->when($department_district_id, function ($query) use ($department_district_id) {
                return $query->where('department_district_id', $department_district_id);
            })
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        $query = DK_Project::select('*')
            ->where('item_status', 1)
            ->withTrashed()
            ->with(['creator','inspector_er','pivot_project_user','pivot_project_team']);

        if(in_array($me->user_type,[41,81,84]))
        {
            $department_district_id = $me->department_district_id;
            $project_list = DK_Pivot_Team_Project::select('project_id')->where('team_id',$department_district_id)->get();
            $query->whereIn('id',$project_list);
        }

        if(in_array($me->user_type,[71,77]))
        {
            $department_district_id = $me->department_district_id;
            if($me->department_district_id > 0)
            {
                $project_list = DK_Pivot_Team_Project::select('project_id')->where('team_id',$department_district_id)->get();
                $query->whereIn('id',$project_list);
            }
        }

        if($me->user_type == 77)
        {
            $project_list = DK_Pivot_User_Project::select('project_id')->where('user_id',$me->id)->get();
            $query->whereIn('id',$project_list);
        }

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->whereHas('pivot_project_team',  function ($query) use($post_data) {
                    $query->where('team_id', $post_data['department_district']);
                });
            }
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("name", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());


        $total_data = [];
        $total_data['id'] = '统计';
        $total_data['name'] = '所有项目';
        $total_data['pivot_project_team'] = [];
        $total_data['daily_goal'] = 0;

        $total_data['order_count_for_delivered'] = 0;
        $total_data['order_count_for_delivered_completed'] = 0;
        $total_data['order_count_for_delivered_inside'] = 0;
        $total_data['order_count_for_delivered_tomorrow'] = 0;
        $total_data['order_count_for_delivered_repeated'] = 0;
        $total_data['order_count_for_delivered_rejected'] = 0;

        $total_data['order_count_for_delivered_per'] = 0;
        $total_data['order_count_for_delivered_effective'] = 0;
        $total_data['order_count_for_delivered_effective_per'] = 0;
        $total_data['order_count_for_delivered_actual'] = 0;
        $total_data['order_count_for_delivered_today'] = 0;

        $total_data['remark'] = '';

        foreach ($list as $k => $v)
        {

            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_delivered = $query_order[$v->id]['order_count_for_delivered'];
                $list[$k]->order_count_for_delivered_completed = $query_order[$v->id]['order_count_for_delivered_completed'];
                $list[$k]->order_count_for_delivered_tomorrow = $query_order[$v->id]['order_count_for_delivered_tomorrow'];
                $list[$k]->order_count_for_delivered_inside = $query_order[$v->id]['order_count_for_delivered_inside'];
                $list[$k]->order_count_for_delivered_repeated = $query_order[$v->id]['order_count_for_delivered_repeated'];
                $list[$k]->order_count_for_delivered_rejected = $query_order[$v->id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->order_count_for_delivered = 0;
                $list[$k]->order_count_for_delivered_completed = 0;
                $list[$k]->order_count_for_delivered_tomorrow = 0;
                $list[$k]->order_count_for_delivered_inside = 0;
                $list[$k]->order_count_for_delivered_repeated = 0;
                $list[$k]->order_count_for_delivered_rejected = 0;
            }



            // 交付
            // 今日交付 = 已交付 + 内部交付
            $list[$k]->order_count_for_delivered_today = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_inside + $v->order_count_for_delivered_repeated + $v->order_count_for_delivered_rejected;
            // 有效交付 = 已交付 + 内部交付
            $list[$k]->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_inside;
            // 实际产出 = 已交付 + 内部交付
            $list[$k]->order_count_for_delivered_actual = $v->order_count_for_delivered_completed;


            // 有效交付率
            if($v->order_count_for_delivered_today > 0)
            {
                $list[$k]->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered_today),2);
            }
            else $list[$k]->order_rate_for_delivered_effective = 0;

            // 实际交付率
            if($v->order_count_for_delivered_today > 0)
            {
                $list[$k]->order_rate_for_delivered_actual = round(($v->order_count_for_delivered_actual * 100 / $v->order_count_for_delivered_today),2);
            }
            else $list[$k]->order_rate_for_delivered_actual = 0;


            $total_data['daily_goal'] += $v->daily_goal;

            $total_data['order_count_for_delivered'] += $v->order_count_for_delivered;
            $total_data['order_count_for_delivered_completed'] += $v->order_count_for_delivered_completed;
            $total_data['order_count_for_delivered_inside'] += $v->order_count_for_delivered_inside;
            $total_data['order_count_for_delivered_tomorrow'] += $v->order_count_for_delivered_tomorrow;
            $total_data['order_count_for_delivered_repeated'] += $v->order_count_for_delivered_repeated;
            $total_data['order_count_for_delivered_rejected'] += $v->order_count_for_delivered_rejected;

            $total_data['order_count_for_delivered_today'] += $v->order_count_for_delivered_today;
            $total_data['order_count_for_delivered_effective'] += $v->order_count_for_delivered_effective;
            $total_data['order_count_for_delivered_actual'] += $v->order_count_for_delivered_actual;

        }


        // 交付
        // 有效交付率
        if($total_data['order_count_for_delivered_today'] > 0)
        {
            $total_data['order_rate_for_delivered_effective'] = round(($total_data['order_count_for_delivered_effective'] * 100 / $total_data['order_count_for_delivered_today']),2);
        }
        else $total_data['order_rate_for_delivered_effective'] = 0;
        // 实际交付率
        if($total_data['order_count_for_delivered_today'] > 0)
        {
            $total_data['order_rate_for_delivered_actual'] = round(($total_data['order_count_for_delivered_actual'] * 100 / $total_data['order_count_for_delivered_today']),2);
        }
        else $total_data['order_rate_for_delivered_actual'] = 0;


        return datatable_response($list, $draw, $total);

    }
    // 【统计】交付看板
    public function view_statistic_delivery_by_client()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return view($this->view_blade_403);

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_delivery_by_client'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-delivery-by-client';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_delivery_by_client($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        if(in_array($me->user_type,[41,81,84]))
        {
            $department_district_id = $me->department_district_id;
        }
        else $department_district_id = 0;


        // 工单统计
        $query_order = DK_Order::select('client_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(delivered_at))"),$the_day)
            ->when($department_district_id, function ($query) use ($department_district_id) {
                return $query->where('department_district_id', $department_district_id);
            })
            ->groupBy('client_id')
            ->get()
            ->keyBy('client_id')
            ->toArray();


        $query = DK_Client::select('*')
//            ->where('item_status', 1)
            ->withTrashed()
            ->with(['creator']);


        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->whereHas('pivot_project_team',  function ($query) use($post_data) {
                    $query->where('team_id', $post_data['department_district']);
                });
            }
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("name", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());


        $total_data = [];
        $total_data['id'] = '统计';
        $total_data['name'] = '所有项目';
        $total_data['pivot_project_team'] = [];
        $total_data['daily_goal'] = 0;

        $total_data['order_count_for_delivered'] = 0;
        $total_data['order_count_for_delivered_completed'] = 0;
        $total_data['order_count_for_delivered_inside'] = 0;
        $total_data['order_count_for_delivered_tomorrow'] = 0;
        $total_data['order_count_for_delivered_repeated'] = 0;
        $total_data['order_count_for_delivered_rejected'] = 0;

        $total_data['order_count_for_delivered_per'] = 0;
        $total_data['order_count_for_delivered_effective'] = 0;
        $total_data['order_count_for_delivered_effective_per'] = 0;
        $total_data['order_count_for_delivered_actual'] = 0;
        $total_data['order_count_for_delivered_today'] = 0;

        $total_data['remark'] = '';

        foreach ($list as $k => $v)
        {

            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_delivered = $query_order[$v->id]['order_count_for_delivered'];
                $list[$k]->order_count_for_delivered_completed = $query_order[$v->id]['order_count_for_delivered_completed'];
                $list[$k]->order_count_for_delivered_tomorrow = $query_order[$v->id]['order_count_for_delivered_tomorrow'];
                $list[$k]->order_count_for_delivered_inside = $query_order[$v->id]['order_count_for_delivered_inside'];
                $list[$k]->order_count_for_delivered_repeated = $query_order[$v->id]['order_count_for_delivered_repeated'];
                $list[$k]->order_count_for_delivered_rejected = $query_order[$v->id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->order_count_for_delivered = 0;
                $list[$k]->order_count_for_delivered_completed = 0;
                $list[$k]->order_count_for_delivered_tomorrow = 0;
                $list[$k]->order_count_for_delivered_inside = 0;
                $list[$k]->order_count_for_delivered_repeated = 0;
                $list[$k]->order_count_for_delivered_rejected = 0;
            }



            // 交付
            // 今日交付 = 已交付 + 内部交付
            $list[$k]->order_count_for_delivered_today = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_inside + $v->order_count_for_delivered_repeated + $v->order_count_for_delivered_rejected;
            // 有效交付 = 已交付 + 内部交付
            $list[$k]->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_inside;
            // 实际产出 = 已交付 + 内部交付
            $list[$k]->order_count_for_delivered_actual = $v->order_count_for_delivered_completed;


            // 有效交付率
            if($v->order_count_for_delivered_today > 0)
            {
                $list[$k]->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered_today),2);
            }
            else $list[$k]->order_rate_for_delivered_effective = 0;

            // 实际交付率
            if($v->order_count_for_delivered_today > 0)
            {
                $list[$k]->order_rate_for_delivered_actual = round(($v->order_count_for_delivered_actual * 100 / $v->order_count_for_delivered_today),2);
            }
            else $list[$k]->order_rate_for_delivered_actual = 0;


            $total_data['daily_goal'] += $v->daily_goal;

            $total_data['order_count_for_delivered'] += $v->order_count_for_delivered;
            $total_data['order_count_for_delivered_completed'] += $v->order_count_for_delivered_completed;
            $total_data['order_count_for_delivered_inside'] += $v->order_count_for_delivered_inside;
            $total_data['order_count_for_delivered_tomorrow'] += $v->order_count_for_delivered_tomorrow;
            $total_data['order_count_for_delivered_repeated'] += $v->order_count_for_delivered_repeated;
            $total_data['order_count_for_delivered_rejected'] += $v->order_count_for_delivered_rejected;

            $total_data['order_count_for_delivered_today'] += $v->order_count_for_delivered_today;
            $total_data['order_count_for_delivered_effective'] += $v->order_count_for_delivered_effective;
            $total_data['order_count_for_delivered_actual'] += $v->order_count_for_delivered_actual;

        }


        // 交付
        // 有效交付率
        if($total_data['order_count_for_delivered_today'] > 0)
        {
            $total_data['order_rate_for_delivered_effective'] = round(($total_data['order_count_for_delivered_effective'] * 100 / $total_data['order_count_for_delivered_today']),2);
        }
        else $total_data['order_rate_for_delivered_effective'] = 0;
        // 实际交付率
        if($total_data['order_count_for_delivered_today'] > 0)
        {
            $total_data['order_rate_for_delivered_actual'] = round(($total_data['order_count_for_delivered_actual'] * 100 / $total_data['order_count_for_delivered_today']),2);
        }
        else $total_data['order_rate_for_delivered_actual'] = 0;


        return datatable_response($list, $draw, $total);

    }
    // 【统计】交付看板
    public function view_statistic_delivery_by_project()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,81,84,71,77,61,66])) return view($this->view_blade_403);

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_delivery'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-delivery';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_delivery_by_project($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        if(in_array($me->user_type,[41,81,84]))
        {
            $department_district_id = $me->department_district_id;
        }
        else $department_district_id = 0;


        // 团队统计
        $query_order = DK_Order::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(delivered_at))"),$the_day)
            ->when($department_district_id, function ($query) use ($department_district_id) {
                return $query->where('department_district_id', $department_district_id);
            })
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        $query = DK_Project::select('*')
            ->where('item_status', 1)
            ->withTrashed()
            ->with(['creator','inspector_er','pivot_project_user','pivot_project_team']);

        if(in_array($me->user_type,[41,81,84]))
        {
            $department_district_id = $me->department_district_id;
            $project_list = DK_Pivot_Team_Project::select('project_id')->where('team_id',$department_district_id)->get();
            $query->whereIn('id',$project_list);
        }

        if(in_array($me->user_type,[71,77]))
        {
            $department_district_id = $me->department_district_id;
            if($me->department_district_id > 0)
            {
                $project_list = DK_Pivot_Team_Project::select('project_id')->where('team_id',$department_district_id)->get();
                $query->whereIn('id',$project_list);
            }
        }

        if($me->user_type == 77)
        {
            $project_list = DK_Pivot_User_Project::select('project_id')->where('user_id',$me->id)->get();
            $query->whereIn('id',$project_list);
        }

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->whereHas('pivot_project_team',  function ($query) use($post_data) {
                    $query->where('team_id', $post_data['department_district']);
                });
            }
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("name", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());


        $total_data = [];
        $total_data['id'] = '统计';
        $total_data['name'] = '所有项目';
        $total_data['pivot_project_team'] = [];
        $total_data['daily_goal'] = 0;

        $total_data['order_count_for_delivered'] = 0;
        $total_data['order_count_for_delivered_completed'] = 0;
        $total_data['order_count_for_delivered_inside'] = 0;
        $total_data['order_count_for_delivered_tomorrow'] = 0;
        $total_data['order_count_for_delivered_repeated'] = 0;
        $total_data['order_count_for_delivered_rejected'] = 0;

        $total_data['order_count_for_delivered_per'] = 0;
        $total_data['order_count_for_delivered_effective'] = 0;
        $total_data['order_count_for_delivered_effective_per'] = 0;
        $total_data['order_count_for_delivered_actual'] = 0;
        $total_data['order_count_for_delivered_today'] = 0;

        $total_data['remark'] = '';

        foreach ($list as $k => $v)
        {

            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_delivered = $query_order[$v->id]['order_count_for_delivered'];
                $list[$k]->order_count_for_delivered_completed = $query_order[$v->id]['order_count_for_delivered_completed'];
                $list[$k]->order_count_for_delivered_tomorrow = $query_order[$v->id]['order_count_for_delivered_tomorrow'];
                $list[$k]->order_count_for_delivered_inside = $query_order[$v->id]['order_count_for_delivered_inside'];
                $list[$k]->order_count_for_delivered_repeated = $query_order[$v->id]['order_count_for_delivered_repeated'];
                $list[$k]->order_count_for_delivered_rejected = $query_order[$v->id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->order_count_for_delivered = 0;
                $list[$k]->order_count_for_delivered_completed = 0;
                $list[$k]->order_count_for_delivered_tomorrow = 0;
                $list[$k]->order_count_for_delivered_inside = 0;
                $list[$k]->order_count_for_delivered_repeated = 0;
                $list[$k]->order_count_for_delivered_rejected = 0;
            }



            // 交付
            // 今日交付 = 已交付 + 内部交付
            $list[$k]->order_count_for_delivered_today = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_inside + $v->order_count_for_delivered_repeated + $v->order_count_for_delivered_rejected;
            // 有效交付 = 已交付 + 内部交付
            $list[$k]->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_inside;
            // 实际产出 = 已交付 + 内部交付
            $list[$k]->order_count_for_delivered_actual = $v->order_count_for_delivered_completed;


            // 有效交付率
            if($v->order_count_for_delivered_today > 0)
            {
                $list[$k]->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered_today),2);
            }
            else $list[$k]->order_rate_for_delivered_effective = 0;

            // 实际交付率
            if($v->order_count_for_delivered_today > 0)
            {
                $list[$k]->order_rate_for_delivered_actual = round(($v->order_count_for_delivered_actual * 100 / $v->order_count_for_delivered_today),2);
            }
            else $list[$k]->order_rate_for_delivered_actual = 0;


            $total_data['daily_goal'] += $v->daily_goal;

            $total_data['order_count_for_delivered'] += $v->order_count_for_delivered;
            $total_data['order_count_for_delivered_completed'] += $v->order_count_for_delivered_completed;
            $total_data['order_count_for_delivered_inside'] += $v->order_count_for_delivered_inside;
            $total_data['order_count_for_delivered_tomorrow'] += $v->order_count_for_delivered_tomorrow;
            $total_data['order_count_for_delivered_repeated'] += $v->order_count_for_delivered_repeated;
            $total_data['order_count_for_delivered_rejected'] += $v->order_count_for_delivered_rejected;

            $total_data['order_count_for_delivered_today'] += $v->order_count_for_delivered_today;
            $total_data['order_count_for_delivered_effective'] += $v->order_count_for_delivered_effective;
            $total_data['order_count_for_delivered_actual'] += $v->order_count_for_delivered_actual;

        }


        // 交付
        // 有效交付率
        if($total_data['order_count_for_delivered_today'] > 0)
        {
            $total_data['order_rate_for_delivered_effective'] = round(($total_data['order_count_for_delivered_effective'] * 100 / $total_data['order_count_for_delivered_today']),2);
        }
        else $total_data['order_rate_for_delivered_effective'] = 0;
        // 实际交付率
        if($total_data['order_count_for_delivered_today'] > 0)
        {
            $total_data['order_rate_for_delivered_actual'] = round(($total_data['order_count_for_delivered_actual'] * 100 / $total_data['order_count_for_delivered_today']),2);
        }
        else $total_data['order_rate_for_delivered_actual'] = 0;


        return datatable_response($list, $draw, $total);

    }


    // 【统计】项目看板
    public function view_statistic_project()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,81,84,71,77,61,66])) return view($this->view_blade_403);

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_project'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-project';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_project($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        if(in_array($me->user_type,[41,81,84]))
        {
            $department_district_id = $me->department_district_id;
        }
        else $department_district_id = 0;


        // 团队统计
        $query_order = DK_Order::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
            ->when($department_district_id, function ($query) use ($department_district_id) {
                return $query->where('department_district_id', $department_district_id);
            })
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        $query = DK_Project::select('*')
            ->where('item_status', 1)
            ->withTrashed()
            ->with(['creator','inspector_er','pivot_project_user','pivot_project_team']);

        if(in_array($me->user_type,[41,81,84]))
        {
            $department_district_id = $me->department_district_id;
            $project_list = DK_Pivot_Team_Project::select('project_id')->where('team_id',$department_district_id)->get();
            $query->whereIn('id',$project_list);
        }

        if(in_array($me->user_type,[71,77]))
        {
            $department_district_id = $me->department_district_id;
            if($me->department_district_id > 0)
            {
                $project_list = DK_Pivot_Team_Project::select('project_id')->where('team_id',$department_district_id)->get();
                $query->whereIn('id',$project_list);
            }
        }

        if($me->user_type == 77)
        {
            $project_list = DK_Pivot_User_Project::select('project_id')->where('user_id',$me->id)->get();
            $query->whereIn('id',$project_list);
        }

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->whereHas('pivot_project_team',  function ($query) use($post_data) {
                    $query->where('team_id', $post_data['department_district']);
                });
            }
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("name", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());


        $total_data = [];
        $total_data['id'] = '统计';
        $total_data['name'] = '所有项目';
        $total_data['pivot_project_team'] = [];
        $total_data['daily_goal'] = 0;
        $total_data['order_count_for_all'] = 0;
        $total_data['order_count_for_inspected'] = 0;
        $total_data['order_count_for_accepted'] = 0;
        $total_data['order_count_for_refused'] = 0;
        $total_data['order_count_for_repeated'] = 0;
        $total_data['order_count_for_accepted_inside'] = 0;

        $total_data['order_count_for_delivered'] = 0;
        $total_data['order_count_for_delivered_completed'] = 0;
        $total_data['order_count_for_delivered_inside'] = 0;
        $total_data['order_count_for_delivered_tomorrow'] = 0;
        $total_data['order_count_for_delivered_repeated'] = 0;
        $total_data['order_count_for_delivered_rejected'] = 0;

        $total_data['order_count_for_delivered_per'] = 0;
        $total_data['order_count_for_delivered_effective'] = 0;
        $total_data['order_count_for_delivered_effective_per'] = 0;
        $total_data['order_count_for_delivered_actual'] = 0;

        $total_data['remark'] = '';

        foreach ($list as $k => $v)
        {

            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_all = $query_order[$v->id]['order_count_for_all'];
                $list[$k]->order_count_for_inspected = $query_order[$v->id]['order_count_for_inspected'];
                $list[$k]->order_count_for_accepted = $query_order[$v->id]['order_count_for_accepted'];
                $list[$k]->order_count_for_refused = $query_order[$v->id]['order_count_for_refused'];
                $list[$k]->order_count_for_repeated = $query_order[$v->id]['order_count_for_repeated'];
                $list[$k]->order_count_for_accepted_inside = $query_order[$v->id]['order_count_for_accepted_inside'];

                $list[$k]->order_count_for_delivered = $query_order[$v->id]['order_count_for_delivered'];
                $list[$k]->order_count_for_delivered_completed = $query_order[$v->id]['order_count_for_delivered_completed'];
                $list[$k]->order_count_for_delivered_tomorrow = $query_order[$v->id]['order_count_for_delivered_tomorrow'];
                $list[$k]->order_count_for_delivered_inside = $query_order[$v->id]['order_count_for_delivered_inside'];
                $list[$k]->order_count_for_delivered_repeated = $query_order[$v->id]['order_count_for_delivered_repeated'];
                $list[$k]->order_count_for_delivered_rejected = $query_order[$v->id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->order_count_for_all = 0;
                $list[$k]->order_count_for_inspected = 0;
                $list[$k]->order_count_for_accepted = 0;
                $list[$k]->order_count_for_refused = 0;
                $list[$k]->order_count_for_repeated = 0;
                $list[$k]->order_count_for_accepted_inside = 0;

                $list[$k]->order_count_for_delivered = 0;
                $list[$k]->order_count_for_delivered_completed = 0;
                $list[$k]->order_count_for_delivered_tomorrow = 0;
                $list[$k]->order_count_for_delivered_inside = 0;
                $list[$k]->order_count_for_delivered_repeated = 0;
                $list[$k]->order_count_for_delivered_rejected = 0;
            }

            // 审核
            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_inspected - $v->order_count_for_refused - $v->order_count_for_repeated;
            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;
            // 完成率
            if($v->daily_goal > 0)
            {
                $list[$k]->order_rate_for_achieved = round(($v->order_count_for_accepted * 100 / $v->daily_goal),2);
            }
            else
            {
                if($v->order_count_for_accepted > 0) $list[$k]->order_rate_for_achieved = 100;
                else $list[$k]->order_rate_for_achieved = 0;
            }


            // 交付
            // 有效交付量
            $list[$k]->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow + $v->order_count_for_delivered_inside;
            // 实际交付量
            $list[$k]->order_count_for_delivered_actual = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow;


            // 有效交付率
            if($v->order_count_for_delivered > 0)
            {
                $list[$k]->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered),2);
            }
            else $list[$k]->order_rate_for_delivered_effective = 0;



            $total_data['daily_goal'] += $v->daily_goal;

            $total_data['order_count_for_all'] += $v->order_count_for_all;

            $total_data['order_count_for_inspected'] += $v->order_count_for_inspected;
            $total_data['order_count_for_accepted'] += $v->order_count_for_accepted;
            $total_data['order_count_for_refused'] += $v->order_count_for_refused;
            $total_data['order_count_for_repeated'] += $v->order_count_for_repeated;
            $total_data['order_count_for_accepted_inside'] += $v->order_count_for_accepted_inside;

            $total_data['order_count_for_delivered'] += $v->order_count_for_delivered;
            $total_data['order_count_for_delivered_completed'] += $v->order_count_for_delivered_completed;
            $total_data['order_count_for_delivered_inside'] += $v->order_count_for_delivered_inside;
            $total_data['order_count_for_delivered_tomorrow'] += $v->order_count_for_delivered_tomorrow;
            $total_data['order_count_for_delivered_repeated'] += $v->order_count_for_delivered_repeated;
            $total_data['order_count_for_delivered_rejected'] += $v->order_count_for_delivered_rejected;

            $total_data['order_count_for_delivered_effective'] += $v->order_count_for_delivered_effective;
            $total_data['order_count_for_delivered_actual'] += $v->order_count_for_delivered_actual;


        }


        // 审核
        // 有效单量
        $total_data['order_count_for_effective'] = $total_data['order_count_for_inspected'] - $total_data['order_count_for_refused'] - $total_data['order_count_for_repeated'];
        // 通过率
        if($total_data['order_count_for_all'] > 0)
        {
            $total_data['order_rate_for_accepted'] = round(($total_data['order_count_for_accepted'] * 100 / $total_data['order_count_for_all']),2);
        }
        else $total_data['order_rate_for_accepted'] = 0;
        // 完成率
        if($total_data['daily_goal'] > 0)
        {
            $total_data['order_rate_for_achieved'] = round(($total_data['order_count_for_accepted'] * 100 / $total_data['daily_goal']),2);
        }
        else
        {
            if($total_data['order_count_for_accepted'] > 0) $total_data['order_rate_for_achieved'] = 100;
            else $total_data['order_rate_for_achieved'] = 0;
        }

        // 审核

        // 有效交付率
        if($total_data['order_count_for_delivered'] > 0)
        {
            $total_data['order_rate_for_delivered_effective'] = round(($total_data['order_count_for_delivered_effective'] * 100 / $total_data['order_count_for_delivered']),2);
        }
        else $total_data['order_rate_for_delivered_effective'] = 0;


        $total_data = $total_data;



        return datatable_response($list, $draw, $total);

    }


    // 【统计】部门看板
    public function view_statistic_department()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19])) return view($this->view_blade_403);

        $department_district_list = DK_Department::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_department'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-department';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_department($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

        // 部门统计
        $query_order = DK_Order::select('department_district_id')
            ->addSelect(DB::raw("
                    count(DISTINCT creator_id) as staff_count,
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                    
                "))
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
            ->groupBy('department_district_id')
            ->get()
            ->keyBy('department_district_id')
            ->toArray();
//        dd($query_order);

        $query = DK_Department::select('id','name')
//            ->withCount([
//                'department_district_staff_list as staff_count' => function($query) use($the_day) {
//                    $query->whereHas('order_list', function($query) use($the_day) {
//                        $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
//                    });
//                },
//                'order_list_for_district as order_count_for_all'=>function($query) use($the_day) {
//                    $query->where('is_published', 1)->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
//                },
//                'order_list_for_district as order_count_for_accepted'=>function($query) use($the_day) {
//                    $query->where('inspected_result', '通过')->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
//                }
//            ])
            ->where('department_type',11)
            ->where(['item_status'=>1]);

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        $total_data = [];
        $total_data['id'] = '统计';
        $total_data['name'] = '所有区';
        $total_data['staff_count'] = 0;
        $total_data['order_count_for_all'] = 0;
        $total_data['order_count_for_inspected'] = 0;
        $total_data['order_count_for_accepted'] = 0;
        $total_data['order_count_for_refused'] = 0;
        $total_data['order_count_for_repeated'] = 0;
        $total_data['order_count_for_accepted_inside'] = 0;

        $total_data['order_count_for_delivered'] = 0;
        $total_data['order_count_for_delivered_completed'] = 0;
        $total_data['order_count_for_delivered_inside'] = 0;
        $total_data['order_count_for_delivered_tomorrow'] = 0;
        $total_data['order_count_for_delivered_repeated'] = 0;
        $total_data['order_count_for_delivered_rejected'] = 0;

        $total_data['order_count_for_delivered_per'] = 0;
        $total_data['order_count_for_delivered_effective'] = 0;
        $total_data['order_count_for_delivered_effective_per'] = 0;
        $total_data['order_count_for_delivered_actual'] = 0;
        $total_data['order_count_for_delivered_actual_per'] = 0;



        foreach ($list as $k => $v)
        {
            if(isset($query_order[$v->id]))
            {
                $list[$k]->staff_count = $query_order[$v->id]['staff_count'];
                $list[$k]->order_count_for_all = $query_order[$v->id]['order_count_for_all'];
                $list[$k]->order_count_for_inspected = $query_order[$v->id]['order_count_for_inspected'];
                $list[$k]->order_count_for_accepted = $query_order[$v->id]['order_count_for_accepted'];
                $list[$k]->order_count_for_refused = $query_order[$v->id]['order_count_for_refused'];
                $list[$k]->order_count_for_repeated = $query_order[$v->id]['order_count_for_repeated'];
                $list[$k]->order_count_for_accepted_inside = $query_order[$v->id]['order_count_for_accepted_inside'];

                $list[$k]->order_count_for_delivered = $query_order[$v->id]['order_count_for_delivered'];
                $list[$k]->order_count_for_delivered_completed = $query_order[$v->id]['order_count_for_delivered_completed'];
                $list[$k]->order_count_for_delivered_inside = $query_order[$v->id]['order_count_for_delivered_inside'];
                $list[$k]->order_count_for_delivered_tomorrow = $query_order[$v->id]['order_count_for_delivered_tomorrow'];
                $list[$k]->order_count_for_delivered_repeated = $query_order[$v->id]['order_count_for_delivered_repeated'];
                $list[$k]->order_count_for_delivered_rejected = $query_order[$v->id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->staff_count = 0;
                $list[$k]->order_count_for_all = 0;
                $list[$k]->order_count_for_inspected = 0;
                $list[$k]->order_count_for_accepted = 0;
                $list[$k]->order_count_for_refused = 0;
                $list[$k]->order_count_for_repeated = 0;
                $list[$k]->order_count_for_accepted_inside = 0;

                $list[$k]->order_count_for_delivered = 0;
                $list[$k]->order_count_for_delivered_completed = 0;
                $list[$k]->order_count_for_delivered_inside = 0;
                $list[$k]->order_count_for_delivered_tomorrow = 0;
                $list[$k]->order_count_for_delivered_repeated = 0;
                $list[$k]->order_count_for_delivered_rejected = 0;
            }

            // 审核
            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;

            // 人均提交量
            if($v->staff_count > 0)
            {
                $list[$k]->order_count_for_all_per = round(($v->order_count_for_all / $v->staff_count),2);
            }
            else $list[$k]->order_count_for_all_per = 0;

            // 人均通过量
            if($v->staff_count > 0)
            {
                $list[$k]->order_count_for_accepted_per = round(($v->order_count_for_accepted / $v->staff_count),2);
            }
            else $list[$k]->order_count_for_accepted_per = 0;


            // 交付
            // 有效交付量
            $list[$k]->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow + $v->order_count_for_delivered_inside;
            // 实际产出
            $list[$k]->order_count_for_delivered_actual = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow;


            // 人均交付量
            if($v->staff_count > 0)
            {
                $list[$k]->order_count_for_delivered_per = round(($v->order_count_for_delivered / $v->staff_count),2);
            }
            else $list[$k]->order_count_for_delivered_per = 0;

            // 人均交付有效量
            if($v->staff_count > 0)
            {
                $list[$k]->order_count_for_delivered_effective_per = round(($v->order_count_for_delivered_effective / $v->staff_count),2);
            }
            else $list[$k]->order_count_for_delivered_effective_per = 0;

            // 人均实际产出
            if($v->staff_count > 0)
            {
                $list[$k]->order_count_for_delivered_actual_per = round(($v->order_count_for_delivered_actual / $v->staff_count),2);
            }
            else $list[$k]->order_count_for_delivered_actual_per = 0;

            // 有效交付率
            if($v->order_count_for_delivered > 0)
            {
                $list[$k]->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered),2);
            }
            else $list[$k]->order_rate_for_delivered_effective = 0;



            $total_data['staff_count'] += $v->staff_count;
            $total_data['order_count_for_all'] += $v->order_count_for_all;
            $total_data['order_count_for_inspected'] += $v->order_count_for_inspected;
            $total_data['order_count_for_accepted'] += $v->order_count_for_accepted;
            $total_data['order_count_for_refused'] += $v->order_count_for_refused;
            $total_data['order_count_for_repeated'] += $v->order_count_for_repeated;
            $total_data['order_count_for_accepted_inside'] += $v->order_count_for_accepted_inside;

            $total_data['order_count_for_delivered'] += $v->order_count_for_delivered;
            $total_data['order_count_for_delivered_completed'] += $v->order_count_for_delivered_completed;
            $total_data['order_count_for_delivered_inside'] += $v->order_count_for_delivered_inside;
            $total_data['order_count_for_delivered_tomorrow'] += $v->order_count_for_delivered_tomorrow;
            $total_data['order_count_for_delivered_repeated'] += $v->order_count_for_delivered_repeated;
            $total_data['order_count_for_delivered_rejected'] += $v->order_count_for_delivered_rejected;

            $total_data['order_count_for_delivered_effective'] += $v->order_count_for_delivered_effective;
            $total_data['order_count_for_delivered_actual'] += $v->order_count_for_delivered_actual;

        }

        // 通过率
        if($total_data['order_count_for_all'] > 0)
        {
            $total_data['order_rate_for_accepted'] = round(($total_data['order_count_for_accepted'] * 100 / $total_data['order_count_for_all']),2);
        }
        else $total_data['order_rate_for_accepted'] = 0;

        // 人均提交量
        if($total_data['staff_count'] > 0)
        {
            $total_data['order_count_for_all_per'] = round(($total_data['order_count_for_all'] / $total_data['staff_count']),2);
        }
        else $total_data['order_count_for_all_per'] = 0;

        // 人均通过量
        if($total_data['staff_count'] > 0)
        {
            $total_data['order_count_for_accepted_per'] = round(($total_data['order_count_for_accepted'] / $total_data['staff_count']),2);
        }
        else $total_data['order_count_for_accepted_per'] = 0;



        // 人均交付量
        if($total_data['staff_count'] > 0)
        {
            $total_data['order_count_for_delivered_per'] = round(($total_data['order_count_for_delivered'] / $total_data['staff_count']),2);
        }
        else $total_data['order_count_for_all_per'] = 0;

        // 人均有效交付量
        if($total_data['staff_count'] > 0)
        {
            $total_data['order_count_for_delivered_effective_per'] = round(($total_data['order_count_for_delivered_effective'] / $total_data['staff_count']),2);
        }
        else $total_data['order_count_for_delivered_effective_per'] = 0;

        // 人均实际产出
        if($total_data['staff_count'] > 0)
        {
            $total_data['order_count_for_delivered_actual_per'] = round(($total_data['order_count_for_delivered_actual'] / $total_data['staff_count']),2);
        }
        else $total_data['order_count_for_delivered_actual_per'] = 0;

        // 有效交付率
        if($total_data['order_count_for_delivered'] > 0)
        {
            $total_data['order_rate_for_delivered_effective'] = round(($total_data['order_count_for_delivered_effective'] * 100 / $total_data['order_count_for_delivered']),2);
        }
        else $total_data['order_rate_for_delivered_effective'] = 0;

        $total_data = $total_data;

//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }




    // 【统计】员工-客服
    public function view_staff_statistic_customer_service($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff = DK_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id'])
            ->with([
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->find($post_data['staff_id']);
        $view_data['staff'] = $staff;

        $view_data['title_text'] = $staff->username;
        $view_data['menu_active_of_statistic_department'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-staff-customer-service';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_staff_customer_service($post_data)
    {
        $this->get_me();
        $me = $this->me;


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


        $staff_id = $post_data['staff_id'];


        $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');



        $query_this_month = DK_Order::select('creator_id','published_at')
            ->where('creator_id',$staff_id)
//            ->whereBetween('published_at',[$this_month_start_timestamp,$this_month_ended_timestamp])  // 当月
            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(published_at,'%Y-%m-%d')"))
            ->addSelect(DB::raw("
                    FROM_UNIXTIME(published_at,'%Y-%m-%d') as date_day,
                    FROM_UNIXTIME(published_at,'%e') as day,
                    count(*) as sum
                "))
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                    
                "));

        $total = $query_this_month->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        $list = $query_this_month->get();
//        dd($statistics_order_this_month_data);




        $total_data = [];
        $total_data['creator_id'] = $staff_id;
        $total_data['published_at'] = 0;
        $total_data['date_day'] = '统计';
        $total_data['staff_count'] = 0;
        $total_data['order_count_for_all'] = 0;
        $total_data['order_count_for_inspected'] = 0;
        $total_data['order_count_for_accepted'] = 0;
        $total_data['order_count_for_refused'] = 0;
        $total_data['order_count_for_repeated'] = 0;
        $total_data['order_count_for_accepted_inside'] = 0;

        $total_data['order_count_for_delivered'] = 0;
        $total_data['order_count_for_delivered_completed'] = 0;
        $total_data['order_count_for_delivered_inside'] = 0;
        $total_data['order_count_for_delivered_tomorrow'] = 0;
        $total_data['order_count_for_delivered_repeated'] = 0;
        $total_data['order_count_for_delivered_rejected'] = 0;

        $total_data['order_count_for_delivered_per'] = 0;
        $total_data['order_count_for_delivered_effective'] = 0;
        $total_data['order_count_for_delivered_effective_per'] = 0;
        $total_data['order_count_for_delivered_actual'] = 0;
        $total_data['order_count_for_delivered_actual_per'] = 0;



        foreach ($list as $k => $v)
        {

            // 审核
            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;

            // 人均提交量
            if($v->staff_count > 0)
            {
                $list[$k]->order_count_for_all_per = round(($v->order_count_for_all / $v->staff_count),2);
            }
            else $list[$k]->order_count_for_all_per = 0;

            // 人均通过量
            if($v->staff_count > 0)
            {
                $list[$k]->order_count_for_accepted_per = round(($v->order_count_for_accepted / $v->staff_count),2);
            }
            else $list[$k]->order_count_for_accepted_per = 0;


            // 交付
            // 有效交付量
            $list[$k]->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow + $v->order_count_for_delivered_inside;
            // 实际产出
            $list[$k]->order_count_for_delivered_actual = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow;



            // 有效交付率
            if($v->order_count_for_delivered > 0)
            {
                $list[$k]->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered),2);
            }
            else $list[$k]->order_rate_for_delivered_effective = 0;



            $total_data['order_count_for_all'] += $v->order_count_for_all;
            $total_data['order_count_for_inspected'] += $v->order_count_for_inspected;
            $total_data['order_count_for_accepted'] += $v->order_count_for_accepted;
            $total_data['order_count_for_refused'] += $v->order_count_for_refused;
            $total_data['order_count_for_repeated'] += $v->order_count_for_repeated;
            $total_data['order_count_for_accepted_inside'] += $v->order_count_for_accepted_inside;

            $total_data['order_count_for_delivered'] += $v->order_count_for_delivered;
            $total_data['order_count_for_delivered_completed'] += $v->order_count_for_delivered_completed;
            $total_data['order_count_for_delivered_inside'] += $v->order_count_for_delivered_inside;
            $total_data['order_count_for_delivered_tomorrow'] += $v->order_count_for_delivered_tomorrow;
            $total_data['order_count_for_delivered_repeated'] += $v->order_count_for_delivered_repeated;
            $total_data['order_count_for_delivered_rejected'] += $v->order_count_for_delivered_rejected;

            $total_data['order_count_for_delivered_effective'] += $v->order_count_for_delivered_effective;
            $total_data['order_count_for_delivered_actual'] += $v->order_count_for_delivered_actual;

        }

        // 通过率
        if($total_data['order_count_for_all'] > 0)
        {
            $total_data['order_rate_for_accepted'] = round(($total_data['order_count_for_accepted'] * 100 / $total_data['order_count_for_all']),2);
        }
        else $total_data['order_rate_for_accepted'] = 0;



        // 有效交付率
        if($total_data['order_count_for_delivered'] > 0)
        {
            $total_data['order_rate_for_delivered_effective'] = round(($total_data['order_count_for_delivered_effective'] * 100 / $total_data['order_count_for_delivered']),2);
        }
        else $total_data['order_rate_for_delivered_effective'] = 0;

        $list[] = $total_data;

//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }




    // 【流量统计】返回-列表-视图
    public function view_statistic_list_for_all($post_data)
    {
        $view_data["menu_active_statistic_list_for_all"] = 'active';
        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-list-for-all';
        return view($view_blade)->with($view_data);
    }
    // 【流量统计】返回-列表-数据
    public function get_statistic_list_for_all_datatable($post_data)
    {
        $me = Auth::guard("staff_admin")->user();
        $query = Def_Record::select('*')
            ->with(['creator','object','item']);

        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");

        if(!empty($post_data['open_device_type']))
        {
            if($post_data['open_device_type'] == "0")
            {
            }
            else if(in_array($post_data['open_system'],[1,2]))
            {
                $query->where('open_device_type',$post_data['open_device_type']);
            }
            else if($post_data['open_device_type'] == "Unknown")
            {
                $query->where('open_device_type',"Unknown");
            }
            else if($post_data['open_device_type'] == "Others")
            {
                $query->whereNotIn('open_device_type',[1,2]);
            }
            else
            {
                $query->where('open_device_type',$post_data['open_device_type']);
            }
        }
        else
        {
//            $query->whereIn('open_system',['Android','iPhone','iPad','Mac','Windows']);
        }

        if(!empty($post_data['open_system']))
        {
            if($post_data['open_system'] == "0")
            {
            }
            else if($post_data['open_system'] == "1")
            {
                $query->whereIn('open_system',['Android','iPhone','iPad','Mac','Windows']);
            }
            else if(in_array($post_data['open_system'],['Android','iPhone','iPad','Mac','Windows']))
            {
                $query->where('open_system',$post_data['open_system']);
            }
            else if($post_data['open_system'] == "Unknown")
            {
                $query->where('open_system',"Unknown");
            }
            else if($post_data['open_system'] == "Others")
            {
                $query->whereNotIn('open_system',['Android','iPhone','iPad','Mac','Windows']);
            }
            else
            {
                $query->where('open_system',$post_data['open_system']);
            }
        }
        else
        {
//            $query->whereIn('open_system',['Android','iPhone','iPad','Mac','Windows']);
        }

        if(!empty($post_data['open_browser']))
        {
            if($post_data['open_browser'] == "0")
            {
            }
            else if($post_data['open_browser'] == "1")
            {
                $query->whereIn('open_browser',['Chrome','Firefox','Safari']);
            }
            else if(in_array($post_data['open_browser'],['Chrome','Firefox','Safari']))
            {
                $query->where('open_browser',$post_data['open_browser']);
            }
            else if($post_data['open_browser'] == "Unknown")
            {
                $query->where('open_browser',"Unknown");
            }
            else if($post_data['open_browser'] == "Others")
            {
                $query->whereNotIn('open_browser',['Chrome','Firefox','Safari']);
            }
            else
            {
                $query->where('open_browser',$post_data['open_browser']);
            }
        }
        else
        {
//            $query->whereIn('open_browser',['Chrome','Firefox','Safari']);
        }

        if(!empty($post_data['open_app']))
        {
            if($post_data['open_app'] == "0")
            {
            }
            else if($post_data['open_app'] == "1")
            {
                $query->whereIn('open_app',['WeChat','QQ','Alipay']);
            }
            else if(in_array($post_data['open_app'],['WeChat','QQ','Alipay']))
            {
                $query->where('open_app',$post_data['open_app']);
            }
            else if($post_data['open_app'] == "Unknown")
            {
                $query->where('open_app',"Unknown");
            }
            else if($post_data['open_app'] == "Others")
            {
                $query->whereNotIn('open_app',['WeChat','QQ','Alipay']);
            }
            else
            {
                $query->where('open_app',$post_data['open_app']);
            }
        }
        else
        {
//            $query->whereIn('open_app',['WeChat','QQ']);
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 20;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);
            $list[$k]->description = replace_blank($v->description);

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }








    /*
     * Export 数据导出
     */
    // 【数据导出】
    public function view_statistic_export()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);



        if($me->id > 10000)
        {
            $record["creator_id"] = $me->id;
            $record["record_category"] = 1; // record_category=1 browse/share
            $record["record_type"] = 1; // record_type=1 browse
            $record["page_type"] = 1; // page_type=1 default platform
            $record["page_module"] = 3; // page_module=2 other
            $record["page_num"] = 0;
            $record["open"] = "export";
            $record["from"] = request('from',NULL);
            $this->record_for_user_visit($record);
        }


        $project_list = DK_Project::select('id','name')->whereIn('item_type',[1,21])->get();
        $staff_list = DK_User::select('id','username','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();
        $client_list = DK_Client::select('id','username','true_name')->where('user_category',11)->get();

        $view_data['project_list'] = $project_list;
        $view_data['staff_list'] = $staff_list;
        $view_data['client_list'] = $client_list;


        $view_data['menu_active_of_statistic_export'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-export';
        return view($view_blade)->with($view_data);
    }
    // 【数据导出】工单
    public function operate_statistic_export_for_order($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $time = time();

        $record_operate_type = 1;
        $record_column_type = null;
        $record_before = '';
        $record_after = '';

        $export_type = isset($post_data['export_type']) ? $post_data['export_type']  : '';
        if($export_type == "month")
        {
            $the_month  = isset($post_data['month']) ? $post_data['month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $start_timestamp = $the_month_start_timestamp;
            $ended_timestamp = $the_month_ended_timestamp;

            $record_operate_type = 11;
            $record_column_type = 'month';
            $record_before = $the_month;
            $record_after = $the_month;
        }
        else if($export_type == "day")
        {
            $the_day  = isset($post_data['day']) ? $post_data['day']  : date('Y-m-d');

            $record_operate_type = 31;
            $record_column_type = 'day';
            $record_before = $the_day;
            $record_after = $the_day;
        }
        else if($export_type == "latest")
        {
            $record_last = DK_Record::select('*')
                ->where(['creator_id'=>$me->id,'operate_category'=>109,'operate_type'=>99])
                ->orderBy('id','desc')->first();

            if($record_last) $start_timestamp = $record_last->after;
            else $start_timestamp = 0;

            $ended_timestamp = $time;

            $record_operate_type = 99;
            $record_column_type = 'datetime';
            $record_before = '';
            $record_after = $time;
        }
        else
        {
            $the_start  = isset($post_data['order_start']) ? $post_data['order_start'].':00'  : '';
            $the_ended  = isset($post_data['order_ended']) ? $post_data['order_ended'].':59'  : '';

            $the_start_timestamp  = strtotime($the_start);
            $the_ended_timestamp  = strtotime($the_ended);

            $record_operate_type = 1;
            $record_before = $the_start;
            $record_after = $the_ended;
        }


        $client_id = 0;
        $staff_id = 0;
        $project_id = 0;

        // 客户
        if(!empty($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,0]))
            {
                $client_id = $post_data['client'];
            }
        }

        // 员工
        if(!empty($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1,0]))
            {
                $staff_id = $post_data['staff'];
            }
        }

        // 项目
        $project_title = '';
        $record_data_title = '';
        if(!empty($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0]))
            {
                $project_id = $post_data['project'];
                $project_er = DK_Project::find($project_id);
                if($project_er)
                {
                    $project_title = '【'.$project_er->name.'】';
                    $record_data_title = $project_er->name;
                }
            }
        }

        // 审核结果
        $inspected_result = 0;
        if(!empty($post_data['inspected_result']))
        {
            if(!in_array($post_data['inspected_result'],['-1','0']))
            {
                $inspected_result = $post_data['inspected_result'];
            }
        }


        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');
        $the_day  = isset($post_data['day'])  ? $post_data['day']  : date('Y-m-d');


        // 工单
        $query = DK_Order::select('*')
            ->with([
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ]);

//        if(in_array($me->user_type,[77]))
//        {
//            $query->where('inspector_id',$me->id);
//        }


        if($export_type == "month")
        {
            $query->whereBetween('inspected_at',[$start_timestamp,$ended_timestamp]);
        }
        else if($export_type == "day")
        {
            $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(inspected_at))"),$the_day);
        }
        else if($export_type == "latest")
        {
            $query->whereBetween('inspected_at',[$start_timestamp,$time]);
        }
        else
        {
            if(!empty($post_data['order_start']))
            {
//                $query->whereDate(DB::raw("FROM_UNIXTIME(inspected_at,'%Y-%m-%d')"), '>=', $post_data['order_start']);
                $query->where('inspected_at', '>=', $the_start_timestamp);
            }
            if(!empty($post_data['order_ended']))
            {
//                $query->whereDate(DB::raw("FROM_UNIXTIME(inspected_at,'%Y-%m-%d')"), '<=', $post_data['order_ended']);
                $query->where('inspected_at', '<=', $the_ended_timestamp);
            }
        }


        if($client_id) $query->where('client_id',$client_id);
        if($staff_id) $query->where('creator_id',$staff_id);
        if($project_id) $query->where('project_id',$project_id);
        if($inspected_result) $query->where('inspected_result',$inspected_result);

//        $data = $query->orderBy('inspected_at','desc')->orderBy('id','desc')->get();
//        $data = $query->orderBy('published_at','desc')->orderBy('id','desc')->get();
        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
//            $cellData[$k]['channel_source'] = $v['channel_source'];
            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];
            $cellData[$k]['recording_address'] = $v['recording_address'];

            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
            'client_er_name'=>'客户',
            'delivered_at'=>'交付时间',
            'creator_name'=>'创建人',
            'team'=>'团队',
            'published_time'=>'提交时间',
            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
            'is_repeat'=>'是否重复',
            'inspector_name'=>'审核人',
            'inspected_time'=>'审核时间',
            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 109;
        $record_data["operate_type"] = $record_operate_type;
        $record_data["column_type"] = $record_column_type;
        $record_data["before"] = $record_before;
        $record_data["after"] = $record_after;
        if($project_id)
        {
            $record_data["item_id"] = $project_id;
            $record_data["title"] = $record_data_title;
        }

        $record->fill($record_data)->save();


        $month_title = '';
        $time_title = '';
        if($export_type == "month")
        {
            $month_title = '【'.$the_month.'月】';
        }
        else if($export_type == "day")
        {
            $month_title = '【'.$the_day.'】';
        }
        else if($export_type == "latest")
        {
            $month_title = '【最新】';
        }
        else
        {
            if($the_start && $the_ended)
            {
                $time_title = '【'.$the_start.' - '.$the_ended.'】';
            }
            else if($the_start)
            {
                $time_title = '【'.$the_start.'】';
            }
            else if($the_ended)
            {
                $time_title = '【'.$the_ended.'】';
            }
        }


        $title = '【工单】'.date('Ymd.His').$project_title.$month_title.$time_title;

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10,
                    'B'=>10,
                    'C'=>20,
                    'D'=>10,
                    'E'=>20,
                    'F'=>20,
                    'G'=>20,
                    'H'=>10,
                    'I'=>10,
                    'J'=>16,
                    'K'=>16,
                    'L'=>10,
                    'M'=>10,
                    'N'=>10,
                    'O'=>60,
                    'P'=>60,
                    'Q'=>10,
                    'R'=>10,
                    'S'=>10,
                    'T'=>30,
                    'U'=>20
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');





    }
    // 【数据导出】工单
    public function operate_statistic_export_for_order_by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;

        // 工单
        $query = DK_Order::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->whereIn('id',$ids_array);

//        if(in_array($me->user_type,[77]))
//        {
//            $query->where('inspector_id',$me->id);
//        }



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
//            $cellData[$k]['channel_source'] = $v['channel_source'];
            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];
            $cellData[$k]['recording_address'] = $v['recording_address'];

            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
            'client_er_name'=>'客户',
            'delivered_at'=>'交付时间',
            'creator_name'=>'创建人',
            'team'=>'团队',
            'published_time'=>'提交时间',
            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
            'is_repeat'=>'是否重复',
            'inspector_name'=>'审核人',
            'inspected_time'=>'审核时间',
            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 109;
        $record_data["operate_type"] = $record_operate_type;
        $record_data["column_type"] = $record_column_type;
        $record_data["before"] = $record_before;
        $record_data["after"] = $record_after;
        $record_data["title"] = $record_title;

        $record->fill($record_data)->save();




        $title = '【工单】'.date('Ymd.His').'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10,
                    'B'=>10,
                    'C'=>20,
                    'D'=>10,
                    'E'=>20,
                    'F'=>20,
                    'G'=>20,
                    'H'=>10,
                    'I'=>10,
                    'J'=>16,
                    'K'=>16,
                    'L'=>10,
                    'M'=>10,
                    'N'=>10,
                    'O'=>60,
                    'P'=>60,
                    'Q'=>10,
                    'R'=>10,
                    'S'=>10,
                    'T'=>30,
                    'U'=>20
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');





    }






    // 【内容】返回-内容-HTML
    public function get_the_user_html($item)
    {
        $item->custom = json_decode($item->custom);
        $user_array[0] = $item;
        $return['user_list'] = $user_array;

        // method A
        $item_html = view(env('TEMPLATE_STAFF_FRONT').'component.user-list')->with($return)->__toString();
//        // method B
//        $item_html = view(env('TEMPLATE_STAFF_FRONT').'component.item-list')->with($return)->render();
//        // method C
//        $view = view(env('TEMPLATE_STAFF_FRONT').'component.item-list')->with($return);
//        $item_html=response($view)->getContent();

        return $item_html;
    }

    // 【内容】返回-内容-HTML
    public function get_the_item_html($item)
    {
        $item->custom = json_decode($item->custom);
        $item_array[0] = $item;
        $return['item_list'] = $item_array;

        // method A
        $item_html = view(env('TEMPLATE_STAFF_FRONT').'component.item-list')->with($return)->__toString();
//        // method B
//        $item_html = view(env('TEMPLATE_STAFF_FRONT').'component.item-list')->with($return)->render();
//        // method C
//        $view = view(env('TEMPLATE_STAFF_FRONT').'component.item-list')->with($return);
//        $item_html=response($view)->getContent();

        return $item_html;
    }

    // 【内容】删除-内容-附属文件
    public function delete_the_item_files($item)
    {
        $mine_id = $item->id;
        $mine_cover_pic = $item->cover_pic;
        $mine_attachment_src = $item->attachment_src;
        $mine_content = $item->content;

        // 删除二维码
        if(file_exists(storage_path("resource/unique/qr_code/".'qr_code_item_'.$mine_id.'.png')))
        {
            unlink(storage_path("resource/unique/qr_code/".'qr_code_item_'.$mine_id.'.png'));
        }

        // 删除原封面图片
        if(!empty($mine_cover_pic) && file_exists(storage_path("resource/" . $mine_cover_pic)))
        {
            unlink(storage_path("resource/" . $mine_cover_pic));
        }

        // 删除原附件
        if(!empty($mine_attachment_src) && file_exists(storage_path("resource/" . $mine_attachment_src)))
        {
            unlink(storage_path("resource/" . $mine_attachment_src));
        }

        // 删除UEditor图片
        $img_tags = get_html_img($mine_content);
        foreach ($img_tags[2] as $img)
        {
            if (!empty($img) && file_exists(public_path($img)))
            {
                unlink(public_path($img));
            }
        }
    }





    // 【修改记录】返回-列表-数据
    public function get_record_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Record::select('*')->withTrashed()
            ->with('creator')
//            ->where(['owner_id'=>100,'item_category'=>100])
//            ->where('item_type', '!=',0);
            ->where(['record_object'=>21,'operate_object'=>71]);

        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['tag'])) $query->where('tag', 'like', "%{$post_data['tag']}%");


        if($me->user_type == 11)
        {
            // 总经理
        }
        // 质检经理
        else if($me->user_type == 71)
        {

            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();

            $query->where(function($query) use($me,$subordinates_array) {
                $query->where('creator_id',$me->id)->orWhereIn('creator_id',$subordinates_array);
            });

        }
        else if($me->user_type == 77)
        {
            // 质检员
            $query->where('creator_id',$me->id);

        }
        else
        {

        }

        $item_type = isset($post_data['item_type']) ? $post_data['item_type'] : '';
        if($item_type == "record") $query->where('operate_category', 109);
//        else if($item_type == "object") $query->where('item_type', 1);
//        else if($item_type == "people") $query->where('item_type', 11);
//        else if($item_type == "product") $query->where('item_type', 22);
//        else if($item_type == "event") $query->where('item_type', 33);
//        else if($item_type == "conception") $query->where('item_type', 91);


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 20;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->description = replace_blank($v->description);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }



    /*
     * 说明
     *
     */


    // 【记录】
    public function record_for_user_visit($post_data)
    {
        $record = new DK_Record_Visit();

        $browseInfo = getBrowserInfo();
        $post_data["browser_info"] = $browseInfo['browser_info'];
        $post_data["referer"] = $browseInfo['referer'];
        $type = $browseInfo['type'];
        if($type == "Mobile") $post_data["open_device_type"] = 1;
        else if($type == "PC") $post_data["open_device_type"] = 2;
        $post_data["open_device_name"] = $browseInfo['device_name'];
        $post_data["open_system"] = $browseInfo['system'];
        $post_data["open_browser"] = $browseInfo['browser'];
        $post_data["open_app"] = $browseInfo['app'];
        $post_data["open_NetType"] = $browseInfo['open_NetType'];
        $post_data["open_is_spider"] = $browseInfo['is_spider'];

        $post_data["ip"] = Get_IP();
        $bool = $record->fill($post_data)->save();
        if($bool) return true;
        else return false;
    }


    // 【用户操作记录】
    public function record_for_user_operate($record_object,$record_category,$record_type,$creator_id,$item_id,$operate_object,$operate_category,$operate_type = 0,$column_key = '',$before = '',$after = '')
    {
        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = $record_object;
        $record_data["record_category"] = $record_category;
        $record_data["record_type"] = $record_type;
        $record_data["creator_id"] = $creator_id;
        $record_data["item_id"] = $item_id;
        $record_data["operate_object"] = $operate_object;
        $record_data["operate_category"] = $operate_category;
        $record_data["operate_type"] = $operate_type;
        $record_data["column_name"] = $column_key;
        $record_data["before"] = $before;
        $record_data["after"] = $after;

        $bool = $record->fill($record_data)->save();
        if($bool) return true;
        else return false;
    }




    // 【K】【内容】【全部】返回-列表-视图
    public function view_record_visit_list($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 操作
        if(!empty($post_data['record_type']))
        {
            if($post_data['record_type'] != '-1') $view_data['record_type'] = $post_data['record_type'];
            else $view_data['record_type'] = '-1';
        }
        else $view_data['record_type'] = -1;

        //设备
        if(!empty($post_data['open_device_type']))
        {
            if($post_data['open_device_type'] != '-1') $view_data['open_device_type'] = $post_data['open_device_type'];
            else $view_data['open_device_type'] = -1;
        }
        else $view_data['open_device_type'] = -1;

        // 系统
        if(!empty($post_data['open_system']))
        {
            if($post_data['open_system'] != '-1') $view_data['open_system'] = $post_data['open_system'];
            else $view_data['open_system'] = -1;
        }
        else $view_data['open_system'] = -1;

        // 浏览器
        if(!empty($post_data['open_browser']))
        {
            if($post_data['open_browser'] != '-1') $view_data['open_browser'] = $post_data['open_browser'];
            else $view_data['open_browser'] = -1;
        }
        else $view_data['open_browser'] = -1;

        // APP
        if(!empty($post_data['open_app']))
        {
            if($post_data['open_app'] != '-1') $view_data['open_app'] = $post_data['open_app'];
            else $view_data['open_app'] = -1;
        }
        else $view_data['open_app'] = -1;

        $staff_list = DK_User::select('id','username')
            ->where('user_category',11)
//            ->whereIn('user_type',[81,84,88])
            ->get();
        $view_data['staff_list'] = $staff_list;


        $view_data['menu_active_of_record_visit_list'] = 'active';

        $view_blade = env('TEMPLATE_DK_ADMIN').'entrance.record.visit-list';
        return view($view_blade)->with($view_data);
    }
    // 【K】【内容】【全部】返回-列表-数据
    public function get_record_visit_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;
        $query = DK_Record_Visit::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','username'); },
                'user'=>function($query) { $query->select('id','username'); },
                'object'
            ]);

        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");


        // 员工
        if(!empty($post_data['record_staff']))
        {
            if(!in_array($post_data['record_staff'],[-1,0]))
            {
                $query->where(function ($query) use($post_data) {
                    $query->where('creator_id', $post_data['record_staff'])->orWhere('page_num', $post_data['record_staff']);
                });
            }
        }


        if(!empty($post_data['record_category']))
        {
            if(!in_array($post_data['record_category'],["-1",-1,0]))
            {
                $query->where('record_category', $post_data['record_category']);
            }
        }

        if(!empty($post_data['record_type']))
        {
            if(!in_array($post_data['record_type'],["-1",-1]))
            {
                $query->where('record_type', $post_data['record_type']);
            }
        }

        if(!empty($post_data['open_device_type']))
        {
            if($post_data['open_device_type'] == "-1")
            {
            }
            else if(in_array($post_data['open_system'],[1,2]))
            {
                $query->where('open_device_type',$post_data['open_device_type']);
            }
            else if($post_data['open_device_type'] == "Unknown")
            {
                $query->where('open_device_type',"Unknown");
            }
            else if($post_data['open_device_type'] == "Others")
            {
                $query->whereNotIn('open_device_type',[1,2]);
            }
            else
            {
                $query->where('open_device_type',$post_data['open_device_type']);
            }
        }
        else
        {
//            $query->whereIn('open_device_type',[1,2]);
        }

        if(!empty($post_data['open_system']))
        {
            if($post_data['open_system'] == "-1")
            {
            }
            else if($post_data['open_system'] == "1")
            {
                $query->whereIn('open_system',['Android','iPhone','iPad','Mac','Windows']);
            }
            else if(in_array($post_data['open_system'],['Android','iPhone','iPad','Mac','Windows']))
            {
                $query->where('open_system',$post_data['open_system']);
            }
            else if($post_data['open_system'] == "Unknown")
            {
                $query->where('open_system',"Unknown");
            }
            else if($post_data['open_system'] == "Others")
            {
                $query->whereNotIn('open_system',['Android','iPhone','iPad','Mac','Windows']);
            }
            else
            {
                $query->where('open_system',$post_data['open_system']);
            }
        }
        else
        {
//            $query->whereIn('open_system',['Android','iPhone','iPad','Mac','Windows']);
        }

        if(!empty($post_data['open_browser']))
        {
            if($post_data['open_browser'] == "-1")
            {
            }
            else if($post_data['open_browser'] == "1")
            {
                $query->whereIn('open_browser',['Chrome','Firefox','Safari']);
            }
            else if(in_array($post_data['open_browser'],['Chrome','Firefox','Safari']))
            {
                $query->where('open_browser',$post_data['open_browser']);
            }
            else if($post_data['open_browser'] == "Unknown")
            {
                $query->where('open_browser',"Unknown");
            }
            else if($post_data['open_browser'] == "Others")
            {
                $query->whereNotIn('open_browser',['Chrome','Firefox','Safari']);
            }
            else
            {
                $query->where('open_browser',$post_data['open_browser']);
            }
        }
        else
        {
//            $query->whereIn('open_browser',['Chrome','Firefox','Safari']);
        }

        if(!empty($post_data['open_app']))
        {
            if($post_data['open_app'] == "-1")
            {
            }
            else if($post_data['open_app'] == "1")
            {
                $query->whereIn('open_app',['WeChat','QQ','Alipay']);
            }
            else if(in_array($post_data['open_app'],['WeChat','QQ','Alipay','Douyin']))
            {
                $query->where('open_app',$post_data['open_app']);
            }
            else if($post_data['open_app'] == "Unknown")
            {
                $query->where('open_app',"Unknown");
            }
            else if($post_data['open_app'] == "Others")
            {
                $query->whereNotIn('open_app',['WeChat','QQ','Alipay','Douyin']);
            }
            else
            {
                $query->where('open_app',$post_data['open_app']);
            }
        }
        else
        {
//            $query->whereIn('open_app',['WeChat','QQ']);
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 20;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            if(!empty($v->ip) && empty($v->ip_info))
//            {
//                $ip = $v->ip;
//                $ip_info = get_ip_info($ip);
//                $record = K_Record::find($v->id);
//                $record->ip_info = $ip_info['adcode']['o'];
//                $record->save();
//                $list[$k]->ip_info = $ip_info['adcode']['o'];
//            }

//            $list[$k]->encode_id = encode($v->id);
//            $list[$k]->description = replace_blank($v->description);

        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }







    // 【电话池】返回-导入-视图
    public function operate_download_file_download($post_data)
    {
        $type = $post_data['type'];
//        dd($type);

        if($type == 'url')
        {

//            $date = date('Y-m-d');
//            $upload_path = <<<EOF
//resource/dk/admin/telephone/$date/
//EOF;
//            $storage_path = storage_path($upload_path);
//            if (!is_dir($storage_path))
//            {
//                mkdir($storage_path, 0766, true);
//            }

            $url = $post_data['url'];

            if(!empty($post_data['name']))
            {
                $name = $post_data['name'];
                $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

//                $file = $storage_path.$name;
            }
            else
            {
                $url_path = parse_url($url, PHP_URL_PATH);
                $name = substr($url_path, strrpos($url_path, '/') + 1);
                $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

//                $file = $storage_path.$name;
            }

            $file = file_get_contents($url);
            return Response::make($file, 200)
                ->header('Content-Type', 'application/'.$extension)
                ->header('Content-Disposition', "attachment; filename=$name");

//            file_put_contents($file, $data);
        }
        else if($type == 'path')
        {
            $file = $post_data['path'];
        }
        else
        {
            $file = $post_data['path'];
        }

        if(!empty($post_data['name']))
        {
            $name = $post_data['name'];
            return response()->download($file,$name);
        }
        else return response()->download($file);


    }

    public function operate_download_call_recording_download($post_data)
    {
        $call_record_id = $post_data['call_record_id'];
        $call = DK_CC_Call_Record::find($call_record_id);
        $record_url = 'https://feiniji.cn'.$call->recordFile;


        $name = $call->callee.'-'.$call->id.'.mp3';
        $extension = 'mp3';
//        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        $file = file_get_contents($record_url);
        return Response::make($file, 200)
            ->header('Content-Type', 'application/'.$extension)
            ->header('Content-Disposition', "attachment; filename=$name");

    }



}