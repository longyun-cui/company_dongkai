<?php
namespace App\Repositories\DK;

use App\Models\DK_Finance\DK_Finance_User;
use App\Models\DK_Finance\DK_Finance_Company;
use App\Models\DK_Finance\DK_Finance_Project;
use App\Models\DK_Finance\DK_Finance_Daily;
use App\Models\DK_Finance\DK_Finance_Record;
use App\Models\DK_Finance\DK_Finance_Funds_Recharge;
use App\Models\DK_Finance\DK_Finance_Funds_Using;

use App\Models\DK\DK_District;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;

class DKFinanceRepository {

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
        $this->modelUser = new DK_Finance_User;
//        $this->modelItem = new YH_Item;

        $this->view_blade_403 = env('TEMPLATE_DK_FINANCE').'entrance.errors.403';
        $this->view_blade_404 = env('TEMPLATE_DK_FINANCE').'entrance.errors.404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');
    }


    // 登录情况
    public function get_me()
    {
        if(Auth::guard("dk_finance_user")->check())
        {
            $this->auth_check = 1;
            $this->me = Auth::guard("dk_finance_user")->user();
            view()->share('me',$this->me);
        }
        else $this->auth_check = 0;

        view()->share('auth_check',$this->auth_check);

        if(isMobileEquipment()) $is_mobile_equipment = 1;
        else $is_mobile_equipment = 0;
        view()->share('is_mobile_equipment',$is_mobile_equipment);
    }




    // 返回（后台）主页视图
    public function view_finance_index_0()
    {
        $this->get_me();
        $me = $this->me;


        $company_list = DK_Finance_Company::select('id','name')->where('company_category',1)->get();
        $view_data['company_list'] = $company_list;

        $channel_list = DK_Finance_Company::select('id','name')->where('company_category',11)->get();
        $view_data['channel_list'] = $channel_list;

        $business_list = DK_Finance_User::select('id','username')->where('user_type',61)->get();
        $view_data['business_list'] = $business_list;

        $project_list = DK_Finance_Project::select('id','name')->whereIn('item_type',[1,21])->get();
        $view_data['project_list'] = $project_list;

        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.index';
        return view($view_blade)->with($view_data);
    }
    public function view_finance_index()
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





        // 日报统计
        // 员工统计
        $query_order = DK_Finance_Daily::select(DB::raw("
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



        // 本月每日日报量
        $query_this_month = DK_Finance_Daily::select('id','published_at')
            ->whereBetween('published_at',[$this_month_start_timestamp,$this_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(published_at,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(published_at,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(published_at,'%e') as day,
                    count(*) as sum
                "));

        // 上月每日日报量
        $query_last_month = DK_Finance_Daily::select('id','published_at')
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



        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.index';
        return view($view_blade)->with($return);
    }
    public function view_finance_index1()
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





        // 日报统计
        $query_order_count_for_all = DK_Finance_Daily::select('id');
        $query_order_count_for_export = DK_Finance_Daily::where('created_type', 9);
        $query_order_count_for_unpublished = DK_Finance_Daily::where('created_type', 1)->where('is_published', 0);
        $query_order_count_for_published = DK_Finance_Daily::where('created_type', 1)->where('is_published', 1);
        $query_order_count_for_waiting_for_inspect = DK_Finance_Daily::where('created_type', 1)->where('is_published', 1)->where('inspected_status', 0);
        $query_order_count_for_inspected = DK_Finance_Daily::where('created_type', 1)->where('is_published', 1)->where('inspected_status', '<>', 0);
        $query_order_count_for_accepted = DK_Finance_Daily::where('created_type', 1)->where('is_published', 1)->where('inspected_result','通过');
        $query_order_count_for_refused = DK_Finance_Daily::where('created_type', 1)->where('is_published', 1)->where('inspected_result','拒绝');
        $query_order_count_for_accepted_inside = DK_Finance_Daily::where('created_type', 1)->where('is_published', 1)->where('inspected_result','内部通过');
        $query_order_count_for_repeat = DK_Finance_Daily::where('created_type', 1)->where('is_published', 1)->where('is_repeat','>',0);



        // 本月每日日报量
        $query_this_month = DK_Finance_Daily::select('id','published_at')
            ->whereBetween('published_at',[$this_month_start_timestamp,$this_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(published_at,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(published_at,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(published_at,'%e') as day,
                    count(*) as sum
                "));

        // 上月每日日报量
        $query_last_month = DK_Finance_Daily::select('id','published_at')
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
//            $subordinates_array = DK_Finance_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Finance_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();

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
//            $subordinates_array = DK_Finance_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Finance_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();

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
//            $subordinates_array = DK_Finance_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
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
//            $subordinates = YH_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $query->where('is_published','<>',0)->whereHas('project_er', function ($query) use ($subordinates) {
//                $query->whereIn('user_id', $subordinates);
//            });

            $subordinates_array = DK_Finance_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
            $project_array = DK_Finance_Project::select('id')->whereIn('user_id',$subordinates_array)->get()->pluck('id')->toArray();

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

            $project_array = DK_Finance_Project::select('id')->where('user_id',$me->id)->get()->pluck('id')->toArray();

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



        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.index';
        return view($view_blade)->with($return);
    }


    // 返回（后台）主页视图
    public function view_finance_404()
    {
        $this->get_me();
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.errors.404';
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

        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.my-account.my-profile-info-index';
        return view($view_blade)->with($return);
    }
    // 【基本信息】返回-编辑-视图
    public function view_my_profile_info_edit()
    {
        $this->get_me();
        $me = $this->me;

        $return['data'] = $me;

        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.my-account.my-profile-info-edit';
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

        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.my-account.my-account-password-change';
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








    //
    public function operate_select2_user($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(empty($post_data['keyword']))
        {
            $query =DK_Finance_User::select(['id','username as text'])
                ->where(['user_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Finance_User::select(['id','username as text'])->where('name','like',"%$keyword%")
                ->where(['user_status'=>1]);
        }

        if(!empty($post_data['category']))
        {
            $category = $post_data['category'];
            if($category == 'admin') $query->where(['user_category'=>1]);
            else if($category == 'staff') $query->where(['user_category'=>11]);
//            else $query->where(['user_category'=>1]);
        }
//        else $query->where(['company_category'=>1]);

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'super') $query->where(['user_type'=>1]);
            else if($type == 'admin') $query->where(['user_type'=>11]);
            else if($type == 'finance') $query->where(['user_type'=>31]);
            else if($type == 'channel') $query->where(['user_type'=>41]);
            else if($type == 'business') $query->where(['user_type'=>61]);
//            else $query->where(['user_type'=>1]);
        }
//        else $query->where(['user_type'=>1]);

        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }
    //
    public function operate_select2_company($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(empty($post_data['keyword']))
        {
            $query =DK_Finance_Company::select(['id','name as text'])
                ->where(['item_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Finance_Company::select(['id','name as text'])->where('name','like',"%$keyword%")
                ->where(['item_status'=>1]);
        }


        if(!empty($post_data['category']))
        {
            $category = $post_data['category'];
            if($category == 'company') $query->where(['user_category'=>1]);
            else if($category == 'channel') $query->where(['company_category'=>11]);
//            else $query->where(['company_category'=>1]);
        }
//        else $query->where(['company_category'=>1]);

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'direct') $query->where(['company_type'=>1]);
            else if($type == 'agent') $query->where(['company_type'=>11]);
//            else $query->where(['company_type'=>1]);
        }
//        else $query->where(['company_type'=>1]);


        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }
    //
    public function operate_select2_project($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(empty($post_data['keyword']))
        {
            $query =DK_Finance_Project::select(['id','name as text'])
                ->where(['item_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Finance_Project::select(['id','name as text'])->where('name','like',"%$keyword%")
                ->where(['item_status'=>1]);
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'company') $query->where(['project_type'=>1]);
            else if($type == 'channel') $query->where(['project_type'=>11]);
//            else $query->where(['project_type'=>1]);
        }
//        else $query->where(['project_type'=>1]);

        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }








    /*
     * 客户管理
     */
    // 【客户管理】返回-添加-视图
    public function view_user_user_create()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19])) return view($this->view_blade_403);

        $item_type = 'item';
        $item_type_text = '客户';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/user-list-for-all';

        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.user.user-edit';
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
    // 【客户管理】返回-编辑-视图
    public function view_user_user_edit()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19])) return view($this->view_blade_403);

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.user.user-edit';

        $item_type = 'item';
        $item_type_text = '客户';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/user-list-for-all';

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
            $mine = DK_Finance_User::with(['parent','channel_er'])->find($id);
            if($mine)
            {
                if(!in_array($mine->user_category,[0,1,9,11,88])) return view(env('TEMPLATE_DK_FINANCE').'errors.404');
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
            else return view(env('TEMPLATE_DK_FINANCE').'errors.404');
        }
    }
    // 【客户管理】保存数据
    public function operate_user_user_save($post_data)
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
//            'username' => 'required|unique:dk_user,username',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_exist = DK_Finance_User::select('id')->where('username',$post_data["username"])->count();
            if($is_exist) return response_error([],"该客户名已存在，请勿重复添加！");

            $mine = new DK_Finance_User;
            $post_data["user_category"] = 11;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
            $post_data["password"] = password_encode("12345678");
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_Finance_User::find($operate_id);
            if(!$mine) return response_error([],"该客户不存在，刷新页面重试！");
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


    // 【用户-员工管理】管理员-修改密码
    public function operate_user_user_password_admin_change($post_data)
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
        if($operate != 'user-password-admin-change') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Finance_User::withTrashed()->find($id);
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
    // 【用户-员工管理】管理员-重置密码
    public function operate_user_user_password_admin_reset($post_data)
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
        if($operate != 'user-password-admin-reset') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Finance_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

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


    // 【客户管理】管理员-启用
    public function operate_user_user_admin_enable($post_data)
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
        if($operate != 'user-admin-enable') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Finance_User::find($id);
        if(!$user) return response_error([],"该【客户】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->user_status = 1;
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
    // 【客户管理】管理员-禁用
    public function operate_user_user_admin_disable($post_data)
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
        if($operate != 'user-admin-disable') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Finance_User::find($id);
        if(!$user) return response_error([],"该【客户】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

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


    // 【客户管理】返回-列表-视图
    public function view_user_user_list($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19])) return view($this->view_blade_403);

        $return['menu_active_of_user_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.user.user-list';
        return view($view_blade)->with($return);
    }
    // 【客户管理】返回-列表-数据
    public function get_user_user_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Finance_User::select('*')
            ->with(['creator','channel_er'])
            ->whereIn('user_category',[11])
            ->whereIn('user_type',[0,1,9,11,19,21,22,31,33,41,61,88]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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


    // 【客户管理】【修改记录】返回-列表-视图
    public function view_user_user_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_Finance_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_user_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.user.user-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【客户管理】【修改记录】返回-列表-数据
    public function get_user_user_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Finance_Record::select('*')
            ->with(['creator'])
            ->where(['record_object'=>21, 'operate_object'=>41,'item_id'=>$id]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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
     * 公司与渠道管理
     */
    //
    public function operate_company_select2_leader($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(empty($post_data['keyword']))
        {
            $query =DK_Finance_User::select(['id','username as text'])
                ->where(['user_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Finance_User::select(['id','username as text'])->where('username','like',"%$keyword%")
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
    public function operate_company_select2_superior_company($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(empty($post_data['keyword']))
        {
            $query =DK_Finance_Company::select(['id','name as text'])
                ->where(['item_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Finance_Company::select(['id','name as text'])->where('name','like',"%$keyword%")
                ->where(['item_status'=>1]);
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'company') $query->where(['company_category'=>1]);
            else if($type == 'channel') $query->where(['company_category'=>11]);
            else $query->where(['company_category'=>1]);
        }
        else $query->where(['company_category'=>1]);

        if($me->user_type == 81)
        {
            $query->where('id',$me->department_district_id);
        }

        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }


    // 【公司与渠道管理】返回-添加-视图
    public function view_company_create()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,41])) return view($this->view_blade_403);

        $item_type = 'item';
        $item_type_text = '公司';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/company/company-list-for-all';

        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.company.company-edit';
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
    // 【公司与渠道管理】返回-编辑-视图
    public function view_company_edit()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,41])) return view($this->view_blade_403);

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.company.company-edit';

        $item_type = 'item';
        $item_type_text = '公司';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/company/company-list-for-all';

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
            $mine = DK_Finance_Company::with('leader')->find($id);
            if($mine)
            {
//                if(!in_array($mine->user_category,[1,9,11,88])) return view(env('TEMPLATE_DK_FINANCE').'errors.404');
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
            else return view(env('TEMPLATE_DK_FINANCE').'errors.404');
        }
    }
    // 【公司与渠道管理】保存数据
    public function operate_company_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'name.required' => '请输入公司或渠道名称！',
//            'name.unique' => '该公司或渠道号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'name' => 'required',
//            'name' => 'required|unique:DK_Finance_Company,name',
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
            $is_exist = DK_Finance_Company::select('id')->where('name',$post_data["name"])->count();
            if($is_exist) return response_error([],"该【公司或渠道】已存在，请勿重复添加！");

            $mine = new DK_Finance_Company;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_Finance_Company::find($operate_id);
            if(!$mine) return response_error([],"该【公司或渠道】不存在，刷新页面重试！");
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
                $mine_data['superior_company_id'] = $me->department_district_id;
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


    // 【公司与渠道管理】【文本-信息】设置-文本-类型
    public function operate_company_info_text_set($post_data)
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
        if($operate != 'item-company-info-text-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Finance_Company::withTrashed()->find($id);
        if(!$item) return response_error([],"该【公司或渠道】不存在，刷新页面重试！");

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
                    $record = new DK_Finance_Record;

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
    // 【公司与渠道管理】【时间-信息】修改-时间-类型
    public function operate_company_info_time_set($post_data)
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

        $item = DK_Finance_Company::withTrashed()->find($id);
        if(!$item) return response_error([],"该【公司或渠道】不存在，刷新页面重试！");

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
                    $record = new DK_Finance_Record;

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
    // 【公司与渠道管理】【选项-信息】修改-radio-select-[option]-类型
    public function operate_company_info_option_set($post_data)
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

        $item = DK_Finance_Company::withTrashed()->find($id);
        if(!$item) return response_error([],"该【公司或渠道】不存在，刷新页面重试！");

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
                    $record = new DK_Finance_Record;

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
    // 【公司与渠道管理】【附件】添加
    public function operate_company_info_attachment_set($post_data)
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

        $item = DK_Finance_Company::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【公司或渠道】不存在，刷新页面重试！");

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
                                $record = new DK_Finance_Record;

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
                        $record = new DK_Finance_Record;

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
    // 【公司与渠道管理】【附件】删除
    public function operate_company_info_attachment_delete($post_data)
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
                $record = new DK_Finance_Record;

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
    // 【公司与渠道管理】【附件】获取
    public function operate_company_get_attachment_html($post_data)
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

        $item = DK_Finance_Company::with([
            'attachment_list' => function($query) { $query->where(['record_object'=>21, 'operate_object'=>41]); }
        ])->withTrashed()->find($id);
        if(!$item) return response_error([],"该【公司或渠道】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.item-assign-html-for-attachment';
        $html = view($view_blade)->with(['item_list'=>$item->attachment_list])->__toString();

        return response_success(['html'=>$html],"");
    }


    // 【公司与渠道管理】管理员-删除
    public function operate_company_admin_delete($post_data)
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

        $item = DK_Finance_Company::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【公司或渠道】不存在，刷新页面重试！");

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
    // 【公司与渠道管理】管理员-恢复
    public function operate_company_admin_restore($post_data)
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

        $item = DK_Finance_Project::withTrashed()->find($id);
        if(!$item) return response_error([],"该【公司或渠道】不存在，刷新页面重试！");

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
    // 【公司与渠道管理】管理员-彻底删除
    public function operate_company_admin_delete_permanently($post_data)
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

        $item = DK_Finance_Project::withTrashed()->find($id);
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
    // 【公司与渠道管理】管理员-启用
    public function operate_company_admin_enable($post_data)
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

        $item = DK_Finance_Company::find($id);
        if(!$item) return response_error([],"该【公司或渠道】不存在，刷新页面重试！");

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
    // 【公司与渠道管理】管理员-禁用
    public function operate_company_admin_disable($post_data)
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

        $item = DK_Finance_Company::find($id);
        if(!$item) return response_error([],"该【公司或渠道】不存在，刷新页面重试！");

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


    // 【公司与渠道管理】返回-列表-视图
    public function view_company_list($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,41])) return view($this->view_blade_403);

        $return['menu_active_of_company_list'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.company.company-list';
        return view($view_blade)->with($return);
    }
    // 【公司与渠道管理】返回-列表-数据
    public function get_company_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query = DK_Finance_Company::select('*')
            ->withTrashed()
            ->with([
                    'creator'=>function($query) { $query->select(['id','username','true_name']); },
//                    'leader'=>function($query) { $query->select(['id','username','true_name']); },
                    'superior_company_er'=>function($query) { $query->select(['id','name']); }
                ]);

        if(in_array($me->user_type,[41,81]))
        {
            $query->where('superior_company_id',$me->department_district_id);
        }

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");

        // 公司类型 [公司|渠道]
        if(!empty($post_data['company_category']))
        {
            if(!in_array($post_data['company_category'],[-1,0]))
            {
                $query->where('company_category', $post_data['company_category']);
            }
        }
        // 渠道类型 [直营|代理]
        if(!empty($post_data['company_type']))
        {
            if(!in_array($post_data['company_type'],[-1,0]))
            {
                $query->where('company_type', $post_data['company_type']);
            }
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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
//        dd($list->toArray());

        foreach($list as $k => $v)
        {
            if($v->department_type == 11)
            {
                $v->district_id = $v->id;
            }
            else if($v->department_type == 21)
            {
                $v->district_id = $v->superior_company_id;
            }

            $v->district_group_id = $v->district_id.'.'.$v->id;
        }
//        $list = $list->sortBy(['district_id'=>'asc'])->values();
//        $list = $list->sortBy(function ($item, $key) {
//            return $item['district_group_id'];
//        })->values();
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }


    // 【公司与渠道管理】【修改记录】返回-列表-视图
    public function view_company_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_Finance_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_car_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.company-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【公司与渠道管理】【修改记录】返回-列表-数据
    public function get_company_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Finance_Record::select('*')
            ->with(['creator'])
            ->where(['record_object'=>21, 'operate_object'=>41,'item_id'=>$id]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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


    // 【公司与渠道管理】【财务往来记录】返回-列表-视图
    public function view_company_recharge_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = YH_User::select('id','username')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_order_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.order-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【公司与渠道管理】【财务往来记录】返回-列表-数据
    public function get_company_recharge_record_datatable($post_data)
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
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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

    // 【公司与渠道管理】添加-财务数据-保存数据（充值）
    public function operate_company_finance_record_create($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'company_id.required' => 'company_id.required.',
            'transaction_date.required' => '请选择交易日期！',
            'transaction_title.required' => '请填写费用类型！',
            'transaction_type.required' => '请填写支付方式！',
            'transaction_amount.required' => '请填写金额！',
//            'transaction_account.required' => '请填写交易账号！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'company_id' => 'required',
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
        if(!in_array($me->user_type,[0,1,11,41,42,81,82,88])) return response_error([],"你没有操作权限！");


//        $operate = $post_data["operate"];
//        $operate_id = $post_data["operate_id"];

        $transaction_date_timestamp = strtotime($post_data['transaction_date']);
        if($transaction_date_timestamp > time('Y-m-d')) return response_error([],"指定日期不能大于今天！");

        $company_id = $post_data["company_id"];
        $company = DK_Finance_Company::find($company_id);
        if(!$company) return response_error([],"该【渠道】不存在，刷新页面重试！");

        // 交易类型 收入 || 支出
        $finance_type = $post_data["finance_type"];
        if(!in_array($finance_type,[1,21])) return response_error([],"交易类型错误！");

        $transaction_amount = $post_data["transaction_amount"];
        if(!is_numeric($transaction_amount)) return response_error([],"交易金额必须为数字！");
        if($transaction_amount <= 0) return response_error([],"交易金额必须大于零！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $FinanceRecord = new DK_Finance_Funds_Recharge;

//            if(in_array($me->user_type,[11,19,41,42]))
//            {
//                $FinanceRecord_data['is_confirmed'] = 1;
//            }

            $FinanceRecord_data['creator_id'] = $me->id;
            $FinanceRecord_data['finance_category'] = 11;
            $FinanceRecord_data['finance_type'] = $finance_type;
            $FinanceRecord_data['company_id'] = $post_data["company_id"];
            $FinanceRecord_data['title'] = $post_data["transaction_title"];
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
                $company = DK_Finance_Company::lockForUpdate()->find($company_id);

//                if(in_array($me->user_type,[11,19,41,42]))
                if(in_array($me->user_type,[-1]))
                {
                    if($finance_type == 1)
                    {
                        $company->funds_recharge_total = $company->funds_recharge_total + $transaction_amount;
//                        $company->funds_balance = $company->funds_balance + $transaction_amount;
                    }
                    else if($finance_type == 91)
                    {
                        $company->funds_recharge_total = $company->funds_recharge_total - $transaction_amount;
//                        $company->funds_balance = $company->funds_balance - $transaction_amount;
                    }
                }
                else
                {
                    if($finance_type == 1)
                    {
                        $company->funds_recharge_total = $company->funds_recharge_total + $transaction_amount;
//                        $company->funds_balance = $company->funds_balance + $transaction_amount;
                    }
                    else if($finance_type == 91)
                    {
                        $company->funds_recharge_total = $company->funds_recharge_total - $transaction_amount;
//                        $company->funds_balance = $company->funds_balance - $transaction_amount;
                    }
                }

                $bool_1 = $company->save();
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


    // 【公司与渠道管理】【结算】返回-列表-视图
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
    // 【公司与渠道管理】【结算】返回-列表-数据
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
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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








    //
    public function operate_user_select2_superior($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $query =DK_Finance_User::select(['id','true_name as text'])
                ->where(['user_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Finance_User::select(['id','true_name as text'])->where('username','like',"%$keyword%")
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
            $query =DK_Finance_Company::select(['id','name as text'])
                ->where(['item_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Finance_Company::select(['id','name as text'])->where('name','like',"%$keyword%")
                ->where(['item_status'=>1]);
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'district') $query->where(['department_type'=>11]);
            else if($type == 'group')
            {
                $id = $post_data['superior_id'];
                $query->where(['department_type'=>21,'superior_company_id'=>$id]);
            }
        }
        else $query->where(['department_type'=>11]);

        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }




    // 【用户-员工管理】返回-添加-视图
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

        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.user.staff-edit';
        return view($view_blade)->with($return_data);
    }
    // 【用户-员工管理】返回-编辑-视图
    public function view_user_staff_edit()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,19,21,31,41,61,71,81])) return view($this->view_blade_403);

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.user.staff-edit';

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
            $mine = DK_Finance_User::with(['parent','superior'])->find($id);
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
            else return view(env('TEMPLATE_DK_FINANCE').'entrance.errors.404');
        }
    }
    // 【用户-员工管理】保存数据
    public function operate_user_staff_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => '参数有误！',
            'true_name.required' => '请输入用户名！',
            'mobile.required' => '请输入电话！',
//            'mobile.unique' => '电话已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'true_name' => 'required',
            'mobile' => 'required',
//            'mobile' => 'required|unique:dk_user,mobile',
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

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_exist = DK_Finance_User::where('mobile',$post_data['mobile'])->first();
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
            $mine = DK_Finance_User::find($operate_id);
            if(!$mine) return response_error([],"该用户不存在，刷新页面重试！");
            if($mine->mobile != $post_data['mobile'])
            {
                $is_exist = DK_Finance_User::where('mobile',$post_data['mobile'])->first();
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
//                if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
//                {
//                    $user_ext = new YH_UserExt;
//                    $user_ext_create['user_id'] = $mine->id;
//                    $bool_2 = $user_ext->fill($user_ext_create)->save();
//                    if(!$bool_2) throw new Exception("insert--user-ext--failed");
//                }

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


    // 【用户-员工管理】管理员-修改密码
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

        $user = DK_Finance_User::withTrashed()->find($id);
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
    // 【用户-员工管理】管理员-重置密码
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

        $user = DK_Finance_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21,81])) return response_error([],"你没有该操作权限！");
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




    // 【用户-员工管理】管理员-删除
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

        $user = DK_Finance_User::withTrashed()->find($id);
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
    // 【用户-员工管理】管理员-恢复
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

        $user = DK_Finance_User::withTrashed()->find($id);
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
    // 【用户-员工管理】管理员-永久删除
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

        $user = DK_Finance_User::withTrashed()->find($id);
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


    // 【用户-员工管理】管理员-启用
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

        $user = DK_Finance_User::find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($me->user_category != 0) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->user_status = 1;
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
    // 【用户-员工管理】管理员-禁用
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

        $user = DK_Finance_User::find($id);
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

    // 【用户-员工管理】管理员-晋升
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

        $user = DK_Finance_User::find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");
        if($user->user_type != 88) return response_error([],"只有客服才可以晋升！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,81])) return response_error([],"你没有操作权限！");

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
    // 【用户-员工管理】管理员-降职
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

        $user = DK_Finance_User::find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");
        if($user->user_type != 84) return response_error([],"只有客服主管才可能降职！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,81])) return response_error([],"你没有操作权限！");

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




    // 【用户-员工管理】返回-列表-视图
    public function view_staff_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,21,31,41,61,71,81])) return view($this->view_blade_403);

        if(in_array($me->user_type,[0,1,9,11]))
        {
            $department_district_list = DK_Finance_Company::select('id','name')->where('department_type',11)->get();
            $return['department_district_list'] = $department_district_list;
        }

        $return['menu_active_of_staff_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.user.staff-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【用户-员工管理】返回-列表-数据
    public function get_staff_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Finance_User::withTrashed()->select('*')
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


        // 公司或渠道-大区
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



    





    /*
     * 项目管理
     */
    // 【项目】返回-列表-视图
    public function view_item_project_list($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $return['menu_active_of_project_list'] = 'active menu-open';
        if(in_array($me->user_type, [41,71,81]))
        {
            $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.project-list-for-department';
        }
        else $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.project-list';
        return view($view_blade)->with($return);
    }
    // 【项目】返回-列表-数据
    public function get_item_project_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 团队统计
        $query_daily = DK_Finance_Daily::select('project_id')
            ->addSelect(DB::raw("
                    sum(delivery_quantity) as total_delivery_quantity,
                    sum(delivery_quantity_of_invalid) as total_delivery_quantity_of_invalid,
                    sum(total_daily_cost) as total_cost
                "))
//            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();

        $query = DK_Finance_Project::select('*')
            ->withTrashed()
            ->with(['creator','company_er','channel_er','business_or']);

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

        if(in_array($me->user_type, [41]))
        {
            $channel_id = $me->channel_id;
            $project_list = DK_Finance_Project::select('id')->where('channel_id',$channel_id)->get();
            $query->whereIn('id',$project_list);

            $department_district_id = $me->department_district_id;
            $inspector_list = DK_Finance_User::select('id')->whereIn('user_type',[71,77])->where('department_district_id',$department_district_id)->get();
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
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());


        foreach($list as $k => $v)
        {

            if(isset($query_daily[$v->id]))
            {
                $list[$k]->total_delivery_quantity = $query_daily[$v->id]['total_delivery_quantity'];
                $list[$k]->total_delivery_quantity_of_invalid = $query_daily[$v->id]['total_delivery_quantity_of_invalid'];
                $list[$k]->total_cost = $query_daily[$v->id]['total_cost'];
            }
            else
            {
                $list[$k]->total_delivery_quantity = 0;
                $list[$k]->total_delivery_quantity_of_invalid = 0;
                $list[$k]->total_cost = 0;
            }

        }

        return datatable_response($list, $draw, $total);
    }


    // 【项目】【修改记录】返回-列表-视图
    public function view_item_project_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_Finance_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_car_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.project-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【项目】【修改记录】返回-列表-数据
    public function get_item_project_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Finance_Record::select('*')
            ->with(['creator'])
            ->where(['record_object'=>21,'operate_object'=>41,'item_id'=>$id]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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


    // 【项目】【结算】返回-列表-视图
    public function view_project_funds_using_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = YH_User::select('id','username')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_order_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.order-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【项目】【结算】返回-列表-数据
    public function get_project_funds_using_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Finance_Funds_Using::select('*')
            ->with(['creator','confirmer','company_er'])
            ->where(['project_id'=>$id]);

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
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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

    // 【项目】添加-结算-保存数据（结算）
    public function operate_project_funds_using_create($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'project_id.required' => 'project_id.required.',
            'transaction_date.required' => '请选择交易日期！',
            'transaction_title.required' => '请填写费用类型！',
            'transaction_type.required' => '请填写支付方式！',
            'transaction_amount.required' => '请填写金额！',
//            'transaction_account.required' => '请填写交易账号！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'project_id' => 'required',
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
        if(!in_array($me->user_type,[0,1,11,41,42,81,82,88])) return response_error([],"你没有操作权限！");


//        $operate = $post_data["operate"];
//        $operate_id = $post_data["operate_id"];

        $transaction_date_timestamp = strtotime($post_data['transaction_date']);
        if($transaction_date_timestamp > time('Y-m-d')) return response_error([],"指定日期不能大于今天！");

        $project_id = $post_data["project_id"];
        $project = DK_Finance_Project::find($project_id);
        if(!$project) return response_error([],"该【渠道】不存在，刷新页面重试！");

        // 交易类型 收入 || 支出
        $finance_type = $post_data["finance_type"];
        if(!in_array($finance_type,[1,21])) return response_error([],"交易类型错误！");

        $transaction_amount = $post_data["transaction_amount"];
        if(!is_numeric($transaction_amount)) return response_error([],"交易金额必须为数字！");
        if($transaction_amount <= 0) return response_error([],"交易金额必须大于零！");

        $company = DK_Finance_Company::find($project->channel_id);
        $balance = $company->funds_recharge_total - $company->settled_amount;
        if($balance < $transaction_amount) return response_error([],"余额不足，请先充值！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $FinanceRecord = new DK_Finance_Funds_Using;

//            if(in_array($me->user_type,[11,19,41,42]))
//            {
//                $FinanceRecord_data['is_confirmed'] = 1;
//            }

            $FinanceRecord_data['creator_id'] = $me->id;
            $FinanceRecord_data['finance_category'] = 11;
            $FinanceRecord_data['finance_type'] = $finance_type;
            $FinanceRecord_data['project_id'] = $post_data["project_id"];
            $FinanceRecord_data['title'] = $post_data["transaction_title"];
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
                $project = DK_Finance_Project::lockForUpdate()->find($project_id);
                $company = DK_Finance_Company::lockForUpdate()->find($project->channel_id);

//                if(in_array($me->user_type,[11,19,41,42]))
                if(in_array($me->user_type,[-1]))
                {
                    if($finance_type == 1)
                    {
                        $project->settled_amount = $project->settled_amount + $transaction_amount;
                        $company->settled_amount = $company->settled_amount + $transaction_amount;
                    }
                    else if($finance_type == 91)
                    {
                        $project->settled_amount = $project->settled_amount - $transaction_amount;
                        $company->settled_amount = $company->settled_amount - $transaction_amount;
                    }
                }
                else
                {
                    if($finance_type == 1)
                    {
                        $project->settled_amount = $project->settled_amount + $transaction_amount;
                        $company->settled_amount = $company->settled_amount + $transaction_amount;
                    }
                    else if($finance_type == 91)
                    {
                        $project->settled_amount = $project->settled_amount - $transaction_amount;
                        $company->settled_amount = $company->settled_amount - $transaction_amount;
                    }
                }

                $bool_1 = $project->save();
                if($bool_1)
                {
                }
                else throw new Exception("update--project--fail");

                $bool_2 = $company->save();
                if($bool_2)
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

        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.project-edit';
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
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.project-edit';

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
            $mine = DK_Finance_Project::with('inspector_er')->find($id);
            if($mine)
            {
//                if(!in_array($mine->user_category,[1,9,11,88])) return view(env('TEMPLATE_DK_FINANCE').'errors.404');
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
            else return view(env('TEMPLATE_DK_FINANCE').'errors.404');
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
            $is_exist = DK_Finance_Project::select('id')->where('name',$post_data["name"])->count();
            if($is_exist) return response_error([],"该【项目】已存在，请勿重复添加！");

            $mine = new DK_Finance_Project;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_Finance_Project::find($operate_id);
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

        $item = DK_Finance_Project::withTrashed()->find($id);
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
                    $record = new DK_Finance_Record;

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

        $item = DK_Finance_Project::withTrashed()->find($id);
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
                    $record = new DK_Finance_Record;

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

        $item = DK_Finance_Project::withTrashed()->find($id);
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

            $item->timestamps = false;
            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
//            if(false) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
//                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                if(false)
                {
                }
                else
                {
                    $record = new DK_Finance_Record;

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

                    if(in_array($column_key,['user_id']))
                    {
                        $record_data["before_id"] = $before;
                        $record_data["after_id"] = $column_value;
                    }

                    if($column_key == 'user_id')
                    {
                        $record_data["before_user_id"] = $before;
                        $record_data["after_user_id"] = $column_value;
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

        $item = DK_Finance_Project::withTrashed()->find($item_id);
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
                                $record = new DK_Finance_Record;

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
                        $record = new DK_Finance_Record;

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
                $record = new DK_Finance_Record;

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

        $item = DK_Finance_Project::with([
                'attachment_list' => function($query) { $query->where(['record_object'=>21, 'operate_object'=>41]); }
            ])->withTrashed()->find($id);
        if(!$item) return response_error([],"该【项目】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.item-assign-html-for-attachment';
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

        $item = DK_Finance_Project::withTrashed()->find($item_id);
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

        $item = DK_Finance_Project::withTrashed()->find($id);
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

        $item = DK_Finance_Project::withTrashed()->find($id);
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

        $item = DK_Finance_Project::find($id);
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

        $item = DK_Finance_Project::find($id);
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
     * 日报管理
     */
    //
    public function operate_item_select2_user($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(empty($post_data['keyword']))
        {
            $query =DK_Finance_User::select(['id','username as text'])
                ->where(['user_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Finance_User::select(['id','username as text'])->where('username','like',"%$keyword%")
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
    public function operate_item_select2_company($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $query = DK_Finance_Company::select(['id','name as text'])
                ->where(['item_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query = DK_Finance_Company::select(['id','name as text'])->where('name','like',"%$keyword%")
                ->where(['item_status'=>1]);
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'company')
            {
                $query->where(['company_category'=>1]);
            }
            else if($type == 'channel')
            {
                $query->where(['company_category'=>11]);
//                $id = $post_data['superior_id'];
//                $query->where(['company_type'=>11,'superior_company_id'=>$id]);
            }
        }
//        else $query->where(['company_category'=>1]);

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
            $query = DK_Finance_Project::select(['id','name as text']);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query = DK_Finance_Project::select(['id','name as text'])->where('name','like',"%$keyword%");
        }

        $query->where('item_status',1);
//        $query->where(['user_status'=>1,'user_category'=>11]);
//        $query->whereIn('user_type',[41,61,88]);

        if(in_array($me->user_type,[41]))
        {
            $channel_id = $me->channel_id;
            $project_list = DK_Finance_Project::select('id')->where('channel_id',$channel_id)->get();
            $query->whereIn('id',$project_list);
        }

        $list = $query->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }
    //
    public function operate_item_select2_user_1($post_data)
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
//        if(!is_numeric($type)) return view(env('TEMPLATE_DK_FINANCE').'errors.404');
//        if(!in_array($type,[1,2,3,10,11,88])) return view(env('TEMPLATE_DK_FINANCE').'errors.404');

        if(empty($post_data['keyword']))
        {
            $list =DK_Finance_User::select(['id','username as text'])
                ->where(['user_status'=>1,'user_category'=>11])
//                ->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =DK_Finance_User::select(['id','username as text'])->where('username','like',"%$keyword%")
                ->where(['user_status'=>1,'user_category'=>11])
//                ->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }








    // 【日报管理】返回-导入-视图
    public function view_item_daily_import()
    {
        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9])) return view(env('TEMPLATE_ROOT_FRONT').'errors.404');

        $operate_category = 'item';
        $operate_type = 'item';
        $operate_type_text = '日报';
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

        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.daily-edit-for-import';
        return view($view_blade)->with($return);
    }
    // 【日报管理】保存-导入-数据
    public function operate_item_daily_import_save($post_data)
    {
//        $messages = [
//            'operate.required' => 'operate.required',
//            'car_id.required' => '请选择项目！',
//        ];
//        $v = Validator::make($post_data, [
//            'operate' => 'required',
//            'car_id' => 'required',
//        ], $messages);
//        if ($v->fails())
//        {
//            $messages = $v->errors();
//            return response_error([],$messages->first());
//        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,19,31])) return response_error([],"你没有操作权限！");

        // 附件
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
            }
            else throw new Exception("upload--attachment--fail");
        }

        $attachment_file = storage_resource_path($result["local"]);

        $data = Excel::load($attachment_file, function($reader) {

//            $reader->takeColumns(50);
            $reader->limitColumns(20);

//            $reader->takeRows(100);
            $reader->limitRows(1001);

//            $reader->ignoreEmpty();

//            $data = $reader->all();
//            $data = $reader->toArray();

        })->get()->toArray();


        $daily_data = [];

        foreach($data as $key => $value)
        {
            $temp_date = [];
            $temp_date['id'] = $key;

            // 客户-使用名称
            $project_name = trim($value['project_name']);
            if(!empty($project_name))
            {
                $project = DK_Finance_Project::where('name',$project_name)->first();
                if($project) $temp_date['project_id'] = $project->id;
                else $temp_date['project_id'] = 0;

//                $temp_date['project_id'] = $value['project_id'];  // 项目id
                $temp_date['assign_date'] = $value['assign_date'];  // 日期
                $temp_date['outbound_background'] = $value['outbound_background'];  // 外呼后台
                $temp_date['attendance_manpower'] = floatval($value['attendance_manpower']);  // 出勤人力
                $temp_date['delivery_quantity'] = intval($value['delivery_quantity']);  // 交付量
                $temp_date['call_charge_daily_cost'] = floatval($value['call_charge_daily_cost']);  // 当日话费
                $temp_date['manpower_daily_wage'] = floatval($value['manpower_daily_wage']);  // 人力日薪
                $temp_date['call_charge_coefficient'] = floatval($value['call_charge_coefficient']);  // 话费系数
                $temp_date['material_coefficient'] = floatval($value['material_coefficient']);  // 物料系数
                $temp_date['taxes_coefficient'] = floatval($value['taxes_coefficient']);  // 税费系数

                $temp_date['description'] = trim($value['description']);  // 备注  [string]

                $temp_date['manpower_daily_cost'] = $temp_date['manpower_daily_wage'] * $temp_date['attendance_manpower'];
                if($temp_date['call_charge_coefficient']  ==  0)
                {
                    $temp_date['material_daily_quantity'] = 0;
                    $temp_date['material_daily_cost'] = 0;
                }
                else
                {
                    $temp_date['material_daily_quantity'] = $temp_date['call_charge_daily_cost'] / $temp_date['call_charge_coefficient'];
                    $temp_date['material_daily_cost'] = ($temp_date['call_charge_daily_cost'] / $temp_date['call_charge_coefficient']) * $temp_date['material_coefficient'];
                }
                $temp_date['taxes_daily_cost'] = $temp_date['taxes_coefficient'] * $temp_date['delivery_quantity'];
                $temp_date['total_daily_cost'] = $temp_date['manpower_daily_cost'] + $temp_date['call_charge_daily_cost'] + $temp_date['material_daily_cost'] + $temp_date['taxes_daily_cost'];

                $daily_data[] = $temp_date;
            }
        }


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            foreach($daily_data as $key => $value)
            {
                $daily = new DK_Finance_Daily;

                $daily->created_type = 9;
                $daily->creator_id = $me->id;
                $daily->project_id = $value['project_id'];  // 项目id
                $daily->assign_date = $value['assign_date'];  // 日期
                $daily->outbound_background = $value['outbound_background'];  // 外呼后台
                $daily->attendance_manpower = $value['attendance_manpower'];  // 出勤人力
                $daily->delivery_quantity = $value['delivery_quantity'];  // 交付量
                $daily->call_charge_daily_cost = $value['call_charge_daily_cost'];  // 当日话费

                $daily->manpower_daily_wage = $value['manpower_daily_wage'];  // 人力日薪
                $daily->call_charge_coefficient = $value['call_charge_coefficient'];  // 话费系数
                $daily->material_coefficient = $value['material_coefficient'];  // 物料系数
                $daily->taxes_coefficient = $value['taxes_coefficient'];  // 税费系数

                $daily->manpower_daily_cost = $value['manpower_daily_cost'];  // 人力成本
                $daily->material_daily_quantity = $value['material_daily_quantity'];  // 物料量
                $daily->material_daily_cost = $value['material_daily_cost'];  // 物料成本
                $daily->taxes_daily_cost = $value['taxes_daily_cost'];  // 税费成本
                $daily->total_daily_cost = $value['total_daily_cost'];  // 税费系数


                $bool = $daily->save();
                if(!$bool) throw new Exception("insert--daily--fail");
            }

            DB::commit();
            return response_success(['count'=>count($daily_data)]);
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


    // 【日报管理】返回-添加-视图
    public function view_item_daily_create()
    {
        $this->get_me();

        $item_type = 'item';
        $item_type_text = '日报';
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

        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.daily-edit';
        return view($view_blade)->with($return);
    }
    // 【日报管理】返回-编辑-视图
    public function view_item_daily_edit()
    {
        $this->get_me();
        $me = $this->me;

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.daily-edit';

        $item_type = 'item';
        $item_type_text = '日报';
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
            $mine = DK_Finance_Daily::find($id);
            if($mine)
            {
//                if($mine->deleted_at) return view(env('TEMPLATE_DK_FINANCE').'entrance.errors.404');
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
            else return view(env('TEMPLATE_DK_FINANCE').'entrance.errors.404');
        }
    }
    // 【日报管理】保存数据
    public function operate_item_daily_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'project_id.required' => '请填选择项目！',
            'project_id.numeric' => '选择项目参数有误！',
            'project_id.min' => '请填选择项目！',
            'assign_date.required' => '请填写日期！',
            'outbound_background.required' => '请填写外呼后台！',
            'attendance_manpower.required' => '请填写出勤人力！',
            'delivery_quantity.required' => '请填写交付量！',
            'call_charge_daily_cost.required' => '请填写当日总话费！',
            'manpower_daily_wage.required' => '请填写人力日薪！',
            'call_charge_coefficient.required' => '请填写话费系数！',
            'material_coefficient.required' => '请填写物料系数！',
            'taxes_coefficient.required' => '请填写税费系数！',
//            'description.required' => '请输入备注！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'project_id' => 'required|numeric|min:1',
            'assign_date' => 'required',
            'outbound_background' => 'required',
            'attendance_manpower' => 'required|numeric',
            'delivery_quantity' => 'required|numeric',
            'call_charge_daily_cost' => 'required',
            'manpower_daily_wage' => 'required',
            'call_charge_coefficient' => 'required',
            'material_coefficient' => 'required',
            'taxes_coefficient' => 'required',
//            'description' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,31])) return response_error([],"你没有操作权限！");

        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $mine = new DK_Finance_Daily;
            $post_data["item_category"] = 1;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;

//            $is_repeat = DK_Finance_Daily::where('user_phone',$post_data['user_phone'])->where('project_id',$post_data['project_id'])->count("*");
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_Finance_Daily::find($operate_id);
            if(!$mine) return response_error([],"该【日报】不存在，刷新页面重试！");

            if(in_array($me->user_type,[84,88]) && $mine->creator_id != $me->id) return response_error([],"该【日报】不是你的，你不能操作！");

        }
        else return response_error([],"参数有误！");

//        $post_data['is_repeat'] = $is_repeat;

        if(!empty($post_data['project_id']))
        {
            $project = DK_Finance_Project::find($post_data['project_id']);
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
//            if(!empty($post_data['assign_date']))
//            {
//                $post_data['assign_time'] = strtotime($post_data['assign_date']);
//            }
//            else $post_data['assign_time'] = 0;



            $mine_data = $post_data;

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['operate_category']);
            unset($mine_data['operate_type']);

            $mine_data['manpower_daily_cost'] = $mine_data['manpower_daily_wage'] * $mine_data['attendance_manpower'];
            if($mine_data['call_charge_coefficient'] == 0)
            {
                $mine_data['material_daily_quantity'] = 0;
                $mine_data['material_daily_cost'] = 0;
            }
            else
            {
                $mine_data['material_daily_quantity'] = $mine_data['call_charge_daily_cost'] / $mine_data['call_charge_coefficient'];
                $mine_data['material_daily_cost'] = $mine_data['call_charge_daily_cost'] * $mine_data['call_charge_coefficient'] * $mine_data['material_coefficient'];
            }
            $mine_data['taxes_daily_cost'] = $mine_data['taxes_coefficient'] * $mine_data['delivery_quantity'];
            $mine_data['total_daily_cost'] = $mine_data['manpower_daily_cost'] + $mine_data['call_charge_daily_cost'] + $mine_data['material_daily_cost'] + $mine_data['taxes_daily_cost'];

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


    // 【日报管理】获取-详情-数据
    public function operate_item_daily_get($post_data)
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

        $item = DK_Finance_Daily::with(['user_er','car_er','trailer_er'])->withTrashed()->find($id);
        if(!$item) return response_error([],"该日报不存在，刷新页面重试！");

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
    // 【日报管理】获取-详情-视图
    public function operate_item_daily_get_html($post_data)
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

        $item = DK_Finance_Daily::with(['user_er','car_er','trailer_er'])->withTrashed()->find($id);
        if(!$item) return response_error([],"该日报不存在，刷新页面重试！");

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


        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.order-info-html';
        $html = view($view_blade)->with(['data'=>$item])->__toString();

        return response_success(['html'=>$html],"");

    }
    // 【日报管理】获取-附件-视图
    public function operate_item_daily_get_attachment_html($post_data)
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

        $item = DK_Finance_Daily::with(['attachment_list'])->withTrashed()->find($id);
        if(!$item) return response_error([],"该日报不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.order-assign-html-for-attachment';
        $html = view($view_blade)->with(['item_list'=>$item->attachment_list])->__toString();

        return response_success(['html'=>$html],"");
    }


    // 【日报管理】删除
    public function operate_item_daily_delete($post_data)
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

        $item = DK_Finance_Daily::withTrashed()->find($item_id);
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
                    $record = new DK_Finance_Record;

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
                    $record = new DK_Finance_Record;

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
    // 【日报管理】发布
    public function operate_item_daily_publish($post_data)
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

        $item = DK_Finance_Daily::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,81,84,88])) return response_error([],"你没有操作权限！");
        if(in_array($me->user_type,[88]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $project_id = $item->project_id;
        $user_phone = $item->user_phone;

        $is_repeat = DK_Finance_Daily::where(['project_id'=>$project_id,'user_phone'=>$user_phone])
            ->where('id','<>',$id)->where('is_published','>',0)->count("*");

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
                $record = new DK_Finance_Record;

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
    // 【日报管理】完成
    public function operate_item_daily_complete($post_data)
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

        $item = DK_Finance_Daily::withTrashed()->find($id);
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
                $record = new DK_Finance_Record;

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
    // 【日报管理】弃用
    public function operate_item_daily_abandon($post_data)
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

        $item = DK_Finance_Daily::withTrashed()->find($id);
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
                $record = new DK_Finance_Record;

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
    // 【日报管理】复用
    public function operate_item_daily_reuse($post_data)
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

        $item = DK_Finance_Daily::withTrashed()->find($id);
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
                $record = new DK_Finance_Record;

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
    // 【日报管理】验证
    public function operate_item_daily_verify($post_data)
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

        $item = DK_Finance_Daily::withTrashed()->find($id);
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
                $record = new DK_Finance_Record;

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
    // 【日报管理】审核
    public function operate_item_daily_inspect($post_data)
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

        $item = DK_Finance_Daily::withTrashed()->find($id);
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
                $record = new DK_Finance_Record;

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
    // 【日报管理】交付
    public function operate_item_daily_deliver($post_data)
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
        if($operate != 'order-deliver') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Finance_Daily::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $user_id = $post_data["user_id"];
//        $user = DK_Finance_User::find($user_id);
//        if(!$user) return response_error([],"客户不存在！");

        $delivered_result = $post_data["delivered_result"];
        if(!in_array($delivered_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");

        $before = $item->delivered_result;

        $delivered_description = $post_data["delivered_description"];
        $recording_address = $post_data["recording_address"];

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->user_id = $user_id;
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
                $record = new DK_Finance_Record;

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

//                $record_data["before_user_id"] = $before;
//                $record_data["after_user_id"] = $user_id;

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
    // 【日报管理】批量-交付
    public function operate_item_daily_bulk_deliver($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
//            'user_id.required' => 'user_id.required.',
            'delivered_result.required' => 'delivered_result.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
//            'user_id' => 'required',
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

        $user_id = $post_data["user_id"];
//        $user = DK_Finance_User::find($user_id);
//        if(!$user) return response_error([],"客户不存在！");

        $delivered_description = $post_data["delivered_description"];

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $delivered_para['user_id'] = $user_id;
            $delivered_para['deliverer_id'] = $me->id;
            $delivered_para['delivered_status'] = 1;
            $delivered_para['delivered_result'] = $delivered_result;
            $delivered_para['delivered_at'] = time();

//            $bool = DK_Finance_Daily::whereIn('id',$ids_array)->update($delivered_para);
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
//            }

            foreach($ids_array as $key => $id)
            {
                $item = DK_Finance_Daily::withTrashed()->find($id);
                if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

                $before = $item->delivered_result;

                $item->user_id = $user_id;
                $item->deliverer_id = $me->id;
                $item->delivered_status = 1;
                $item->delivered_result = $delivered_result;
                $item->delivered_description = $delivered_description;
                $item->delivered_at = time();
                $bool = $item->save();
                if(!$bool) throw new Exception("item--update--fail");
                else
                {
                    $record = new DK_Finance_Record;

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

//                $record_data["before_user_id"] = $before;
//                $record_data["after_user_id"] = $user_id;

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


    // 【日报管理】【文本】修改-文本-类型
    public function operate_item_daily_info_text_set($post_data)
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

        $item = DK_Finance_Daily::withTrashed()->find($id);
        if(!$item) return response_error([],"该【日报】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;

        if(in_array($column_key,config('finance.common.daily_field')))
        {
            if(!in_array($me->user_type,[0,1,11,31])) return response_error([],"你没有操作权限！");
        }

//        if(in_array($me->user_type,[84,88]) && $item->creator_id != $me->id) return response_error([],"该【日报】不是你的，你不能操作！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item = DK_Finance_Daily::withTrashed()->lockForUpdate()->find($id);

            if(in_array($column_key,config('finance.common.daily_finance_field')))
            {
                $item->$column_key = $column_value;
                $item->manpower_daily_cost = $item->manpower_daily_wage * $item->attendance_manpower;
                if($item->call_charge_coefficient == 0)
                {
                    $item->material_daily_quantity = 0;
                    $item->material_daily_cost = 0;
                }
                else
                {
                    $item->material_daily_quantity = $item->call_charge_daily_cost / $item->call_charge_coefficient;
                    $item->material_daily_cost = $item->call_charge_daily_cost / $item->call_charge_coefficient * $item->material_coefficient;
                }
                $item->taxes_daily_cost = $item->taxes_coefficient * $item->delivery_quantity;
                $item->total_daily_cost = $item->manpower_daily_cost + $item->call_charge_daily_cost + $item->material_daily_cost + $item->taxes_daily_cost;
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
                    $record = new DK_Finance_Record;

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
    // 【日报管理】【时间】修改-时间-类型
    public function operate_item_daily_info_time_set($post_data)
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

        $item = DK_Finance_Daily::withTrashed()->find($id);
        if(!$item) return response_error([],"该日报不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];
        $time_type = $post_data["time_type"];
        $time_data_type = $post_data["time_data_type"];

        if($time_data_type == 'date')
        {
            $before = $item->$column_key;
        }
        else
        {
            $before = $item->$column_key;
        }



        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($time_data_type == 'date')
            {
                $item->$column_key = $column_value;
                $after = $column_value;
            }
            else
            {
                $item->$column_key = strtotime($column_value);
                $after = strtotime($column_value);
            }

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
                    $record = new DK_Finance_Record;

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
                    $record_data["after"] = $after;

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
    // 【日报管理】【选项】修改-radio-select-[option]-类型
    public function operate_item_daily_info_option_set($post_data)
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

        $item = DK_Finance_Daily::withTrashed()->find($id);
        if(!$item) return response_error([],"该【日报】不存在，刷新页面重试！");

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

        if(in_array($me->user_type,['user_id','project_id']))
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
                    $project = DK_Finance_Project::withTrashed()->find($column_value);
                    if(!$project) throw new Exception("该【项目】不存在，刷新页面重试！");
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
                    $record = new DK_Finance_Record;

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

                    if(in_array($column_key,['user_id','project_id']))
                    {
                        $record_data["before_id"] = $before;
                        $record_data["after_id"] = $column_value;
                    }



                    if($column_key == 'user_id')
                    {
                        $record_data["before_user_id"] = $before;
                        $record_data["after_user_id"] = $column_value;
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
    // 【日报管理】【附件】添加
    public function operate_item_daily_info_attachment_set($post_data)
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

        $item = DK_Finance_Daily::withTrashed()->find($order_id);
        if(!$item) return response_error([],"该【日报】不存在，刷新页面重试！");

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
                                $record = new DK_Finance_Record;

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
                        $record = new DK_Finance_Record;

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
    // 【日报管理】【附件】删除
    public function operate_item_daily_info_attachment_delete($post_data)
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
                $record = new DK_Finance_Record;

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




    // 【日报管理】返回-列表-视图
    public function view_item_daily_list($post_data)
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
            else $view_data['length'] = 40;
        }
        else $view_data['length'] = 40;
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


        // 项目
        if(!empty($post_data['project_id']))
        {
            if(is_numeric($post_data['project_id']) && $post_data['project_id'] > 0)
            {
                $project = DK_Finance_Project::select(['id','name'])->find($post_data['project_id']);
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


        // 类型
        if(!empty($post_data['order_type']))
        {
            if(is_numeric($post_data['order_type']) && $post_data['order_type'] > 0) $view_data['order_type'] = $post_data['order_type'];
            else $view_data['order_type'] = -1;
        }
        else $view_data['order_type'] = -1;


//        dd($view_data);


        $user_list = DK_Finance_User::select('id','username')->where('user_category',11)->get();
        $view_data['user_list'] = $user_list;

        if($me->user_type == 41)
        {
            $staff_list = DK_Finance_User::select('id','username')
                ->where('user_category',11)
                ->whereIn('user_type',[81,84,88])
                ->get();
        }


        $company_list = DK_Finance_Company::select('id','name')->where('company_category',1)->get();
        $view_data['company_list'] = $company_list;

        $channel_list = DK_Finance_Company::select('id','name')->where('company_category',11)->get();
        $view_data['channel_list'] = $channel_list;

        $business_list = DK_Finance_User::select('id','username')->where('user_type',61)->get();
        $view_data['business_list'] = $business_list;

        $project_list = DK_Finance_Project::select('id','name')->whereIn('item_type',[1,21])->get();
        $view_data['project_list'] = $project_list;



        $view_data['menu_active_of_daily_list'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.daily-list';
        return view($view_blade)->with($view_data);
    }
    // 【日报管理】返回-列表-数据
    public function get_item_daily_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Finance_Daily::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->with([
                'creator',
                'project_er'=>function($query) {
                    $query->select('id','name','channel_id','business_id')->with([
                        'channel_er'=>function($query1) { $query1->select('id','name'); },
                        'business_or'=>function($query2) { $query2->select('id','username'); }
                    ]);
                },
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

        if($me->user_type == 41)
        {
            $project_list = DK_Finance_Project::select('id')->where('channel_id',$me->channel_id)->get();
            $query->whereIn('project_id',$project_list);
        }


        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['assign'])) $query->whereDate("assign_date", $post_data['assign']);
        if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '>=', $post_data['assign_start']);
        if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '<=', $post_data['assign_ended']);




        if(!empty($post_data['time_type']))
        {
            if($post_data['time_type'] == "month")
            {
                // 指定月份
                if(!empty($post_data['time']))
                {
                    $month_arr = explode('-', $post_data['time']);
                    $month_year = $month_arr[0];
                    $month_month = $month_arr[1];
                    $query->whereYear("assign_date", $month_year)->whereMonth("assign_date", $month_month);
                }
            }
            else if($post_data['time_type'] == "date")
            {
                // 指定日期
                if(!empty($post_data['time']))
                {
                    $query->whereDate("assign_date", $post_data['time']);
                }
            }
            else if($post_data['time_type'] == "period")
            {
            }
            else
            {}
        }


        // 公司
        if(isset($post_data['company']))
        {
            if(!in_array($post_data['company'],[-1]))
            {
                $channel_list = DK_Finance_Company::select('id')->where('superior_company_id',$post_data['company'])->get()->toArray();
                $project_list = DK_Finance_Project::select('id')->whereIn('channel_id',$channel_list)->get()->toArray();
                $query->whereIn('project_id', $project_list);
            }
        }
        // 渠道
        if(isset($post_data['channel']))
        {
            if(!in_array($post_data['channel'],[-1]))
            {
                $project_list = DK_Finance_Project::select('id')->where('channel_id',$post_data['channel'])->get()->toArray();
                $query->whereIn('project_id', $project_list);
            }
        }
        // 商务
        if(isset($post_data['business']))
        {
            if(!in_array($post_data['business'],[-1]))
            {
                $project_list = DK_Finance_Project::select('id')->where('business_id',$post_data['business'])->get()->toArray();
                $query->whereIn('project_id', $project_list);
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


        // 统计
        $daily_total = (clone $query)->select(DB::raw("
                    sum(attendance_manpower) as total_of_attendance_manpower,
                    sum(delivery_quantity) as total_of_delivery_quantity,
                    sum(delivery_quantity_of_invalid) as total_of_delivery_quantity_of_invalid,
                    sum(call_charge_daily_cost) as total_of_call_charge_daily_cost,
                    sum(manpower_daily_cost) as total_of_manpower_daily_cost,
                    sum(material_daily_quantity) as total_of_material_daily_quantity,
                    sum(material_daily_cost) as total_of_material_daily_cost,
                    sum(taxes_daily_cost) as total_of_taxes_daily_cost,
                    sum(total_daily_cost) as total_of_total_daily_cost
                "))
            ->get();
//        dd($daily_total->toArray());
        $daily_total = $daily_total[0];


        $total_data = [];
        $total_data['id'] = '合计';
        $total_data['name'] = '--';
        $total_data['project_id'] = '--';
        $total_data['assign_date'] = '--';
        $total_data['outbound_background'] = '--';
        $total_data['date_day'] = '统计';
        $total_data['creator_id'] = 0;
        $total_data['channel_id'] = 0;

        $total_data['attendance_manpower'] = $daily_total->total_of_attendance_manpower;
        $total_data['delivery_quantity'] = $daily_total->total_of_delivery_quantity;
        $total_data['delivery_quantity_of_invalid'] = $daily_total->total_of_delivery_quantity_of_invalid;

        $total_data['manpower_daily_cost'] = $daily_total->total_of_manpower_daily_cost;
        $total_data['call_charge_daily_cost'] = $daily_total->total_of_call_charge_daily_cost;
        $total_data['material_daily_quantity'] = $daily_total->total_of_material_daily_quantity;
        $total_data['material_daily_cost'] = $daily_total->total_of_material_daily_cost;
        $total_data['taxes_daily_cost'] = $daily_total->total_of_taxes_daily_cost;
        $total_data['total_daily_cost'] = $daily_total->total_of_total_daily_cost;

        $total_data['manpower_daily_wage'] = "--";
        $total_data['call_charge_coefficient'] = "--";
        $total_data['material_coefficient'] = "--";
        $total_data['taxes_coefficient'] = "--";

        $total_data['created_at'] = "--";
        $total_data['description'] = "--";


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




    // 【日报管理】【修改记录】返回-列表-视图
    public function view_item_daily_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_Finance_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_order_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.item.order-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【日报管理】【修改记录】返回-列表-数据
    public function get_item_daily_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Finance_Record::select('*')
            ->with([
                'creator',
                'before_user_er'=>function($query) { $query->select('id','username'); },
                'after_user_er'=>function($query) { $query->select('id','username'); },
                'before_project_er'=>function($query) { $query->select('id','name'); },
                'after_project_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where(['order_id'=>$id]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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
     * Statistic 流量统计
     */
    // 【统计】
    public function view_statistic_index()
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_Finance_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();
        $user_list = DK_Finance_User::select('id','username')->where('user_category',11)->get();
        $project_list = DK_Finance_Project::select('id','name')->whereIn('item_type',[1,21])->get();
        $department_district_list = DK_Finance_Company::select('id','name')->where('department_type',11)->get();

        $view_data['staff_list'] = $staff_list;
        $view_data['user_list'] = $user_list;
        $view_data['project_list'] = $project_list;
        $view_data['department_district_list'] = $department_district_list;


        $view_data['menu_active_of_statistic_index'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-index';
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
        $user = DK_Finance_User::find($user_id);

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

        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-user';
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

        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-item';
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
        $user_id = 0;
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
            if(!empty($post_data['user']))
            {
                if(!in_array($post_data['user'],[-1,0]))
                {
                    $user_id = $post_data['user'];
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




        // 日报统计
        $order_count_for_all = DK_Finance_Daily::select('*')->count("*");
        $order_count_for_unpublished = DK_Finance_Daily::where('is_published', 0)->count("*");
        $order_count_for_published = DK_Finance_Daily::where('is_published', 1)->count("*");
        $order_count_for_waiting_for_inspect = DK_Finance_Daily::where('is_published', 1)->where('inspected_status', 0)->count("*");
        $order_count_for_inspected = DK_Finance_Daily::where('is_published', 1)->where('inspected_status', '<>', 0);
        $order_count_for_accepted = DK_Finance_Daily::where('is_published', 1)->where('inspected_result','通过');
        $order_count_for_refused = DK_Finance_Daily::where('is_published', 1)->where('inspected_result','拒绝');
        $order_count_for_accepted_inside = DK_Finance_Daily::where('is_published', 1)->where('inspected_result','内部通过');
        $order_count_for_repeat = DK_Finance_Daily::where('is_published', 1)->where('is_repeat','>',0);



        $return['order_count_for_all'] = $order_count_for_all;
        $return['order_count_for_inspected'] = $order_count_for_inspected;
        $return['order_count_for_accepted'] = $order_count_for_accepted;
        $return['order_count_for_refused'] = $order_count_for_refused;
        $return['order_count_for_repeat'] = $order_count_for_repeat;
        $return['order_count_for_rate'] = round(($order_count_for_accepted * 100 / $order_count_for_all),2);




        // 日报统计

        // 本月每日日报量
        $query_for_order_this_month = DK_Finance_Daily::select('id','assign_time')
            ->whereBetween('assign_time',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(assign_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(assign_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(assign_time,'%e') as day,
                    count(*) as sum
                "));

        if($staff_id) $query_for_order_this_month->where('creator_id',$staff_id);
        if($user_id) $query_for_order_this_month->where('user_id',$user_id);
        if($car_id) $query_for_order_this_month->where('car_id',$car_id);
        if($route_id) $query_for_order_this_month->where('route_id',$route_id);
        if($pricing_id) $query_for_order_this_month->where('pricing_id',$pricing_id);


        $statistics_data_for_order_this_month = $query_for_order_this_month->get()->keyBy('day');
        $return_data['statistics_data_for_order_this_month'] = $statistics_data_for_order_this_month;

        // 上月每日日报量
        $query_for_order_last_month = DK_Finance_Daily::select('id','assign_time')
            ->whereBetween('assign_time',[$the_last_month_start_timestamp,$the_last_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(assign_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(assign_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(assign_time,'%e') as day,
                    count(*) as sum
                "));

        if($staff_id) $query_for_order_last_month->where('creator_id',$staff_id);
        if($user_id) $query_for_order_last_month->where('user_id',$user_id);
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


        $query = DK_Finance_Daily::select('id');

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
            }
        }


        // 公司或渠道-大区
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




        // 日报统计
        // 总量统计
        $query_order = (clone $query)->select(DB::raw("
                    count(*) as order_count_for_all,
                    count(IF(is_published = 0, TRUE, NULL)) as order_count_for_unpublished,
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_published,
                    count(IF(is_published = 1 AND inspected_status <> 0, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside
                "))
            ->get();

        $query_delivered = (clone $query)->select(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->get();

        $order_count_for_all = $query_order[0]->order_count_for_all;
        $order_count_for_inspected = $query_order[0]->order_count_for_inspected;
        $order_count_for_accepted = $query_order[0]->order_count_for_accepted;
        $order_count_for_accepted_inside = $query_order[0]->order_count_for_accepted_inside;
        $order_count_for_refused = $query_order[0]->order_count_for_refused;
        $order_count_for_repeated = $query_order[0]->order_count_for_repeated;

        $return_data['order_count_for_all'] = $order_count_for_all;
        $return_data['order_count_for_inspected'] = $order_count_for_inspected;
        $return_data['order_count_for_accepted'] = $order_count_for_accepted;
        $return_data['order_count_for_accepted_inside'] = $order_count_for_accepted_inside;
        $return_data['order_count_for_refused'] = $order_count_for_refused;
        $return_data['order_count_for_repeated'] = $order_count_for_repeated;
        if($order_count_for_inspected)
        {
            $return_data['order_count_for_rate'] = round(($order_count_for_accepted * 100 / $order_count_for_inspected),2);
        }
        else $return_data['order_count_for_rate'] = 0;


        $order_count_for_delivered = $query_delivered[0]->order_count_for_delivered;
        $order_count_for_delivered_completed = $query_delivered[0]->order_count_for_delivered_completed;
        $order_count_for_delivered_inside = $query_delivered[0]->order_count_for_delivered_inside;
        $order_count_for_delivered_tomorrow = $query_delivered[0]->order_count_for_delivered_tomorrow;
        $order_count_for_delivered_repeated = $query_delivered[0]->order_count_for_delivered_repeated;
        $order_count_for_delivered_rejected = $query_delivered[0]->order_count_for_delivered_rejected;
        $order_count_for_delivered_effective = $order_count_for_delivered_completed + $order_count_for_delivered_inside + $order_count_for_delivered_tomorrow;

        $return_data['order_count_for_delivered'] = $order_count_for_delivered;
        $return_data['order_count_for_delivered_completed'] = $order_count_for_delivered_completed;
        $return_data['order_count_for_delivered_inside'] = $order_count_for_delivered_inside;
        $return_data['order_count_for_delivered_tomorrow'] = $order_count_for_delivered_tomorrow;
        $return_data['order_count_for_delivered_repeated'] = $order_count_for_delivered_repeated;
        $return_data['order_count_for_delivered_rejected'] = $order_count_for_delivered_rejected;
        $return_data['order_count_for_delivered_effective'] = $order_count_for_delivered_effective;
        if($order_count_for_delivered)
        {
            $return_data['order_rate_for_delivered_effective'] = round(($order_count_for_delivered_effective * 100 / $order_count_for_delivered),2);
        }
        else $return_data['order_rate_for_delivered_effective'] = 0;


        // 当天统计
        $query_order_of_today = (clone $query)->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_date)
            ->select(DB::raw("
                    count(*) as order_count_for_all,
                    count(IF(is_published = 0, TRUE, NULL)) as order_count_for_unpublished,
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_published,
                    count(IF(is_published = 1 AND inspected_status <> 0, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside
                "))
            ->get();

        $query_delivered_of_today = (clone $query)->whereDate(DB::raw("DATE(FROM_UNIXTIME(delivered_at))"),$the_date)
            ->select(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->get();

        $order_count_of_today_for_all = $query_order_of_today[0]->order_count_for_all;
        $order_count_of_today_for_inspected = $query_order_of_today[0]->order_count_for_inspected;
        $order_count_of_today_for_accepted = $query_order_of_today[0]->order_count_for_accepted;
        $order_count_of_today_for_accepted_inside = $query_order_of_today[0]->order_count_for_accepted_inside;
        $order_count_of_today_for_refused = $query_order_of_today[0]->order_count_for_refused;
        $order_count_of_today_for_repeated = $query_order_of_today[0]->order_count_for_repeated;

        $return_data['order_count_of_today_for_all'] = $order_count_of_today_for_all;
        $return_data['order_count_of_today_for_inspected'] = $order_count_of_today_for_inspected;
        $return_data['order_count_of_today_for_accepted'] = $order_count_of_today_for_accepted;
        $return_data['order_count_of_today_for_accepted_inside'] = $order_count_of_today_for_accepted_inside;
        $return_data['order_count_of_today_for_refused'] = $order_count_of_today_for_refused;
        $return_data['order_count_of_today_for_repeated'] = $order_count_of_today_for_repeated;
        if($order_count_of_today_for_inspected)
        {
            $return_data['order_count_of_today_for_rate'] = round(($order_count_of_today_for_accepted * 100 / $order_count_of_today_for_inspected),2);
        }
        else $return_data['order_count_of_today_for_rate'] = 0;


        $order_count_of_today_for_delivered = $query_delivered_of_today[0]->order_count_for_delivered;
        $order_count_of_today_for_delivered_completed = $query_delivered_of_today[0]->order_count_for_delivered_completed;
        $order_count_of_today_for_delivered_inside = $query_delivered_of_today[0]->order_count_for_delivered_inside;
        $order_count_of_today_for_delivered_tomorrow = $query_delivered_of_today[0]->order_count_for_delivered_tomorrow;
        $order_count_of_today_for_delivered_repeated = $query_delivered_of_today[0]->order_count_for_delivered_repeated;
        $order_count_of_today_for_delivered_rejected = $query_delivered_of_today[0]->order_count_for_delivered_rejected;
        $order_count_of_today_for_delivered_effective = $order_count_of_today_for_delivered_completed + $order_count_of_today_for_delivered_inside + $order_count_of_today_for_delivered_tomorrow;

        $return_data['order_count_of_today_for_delivered'] = $order_count_of_today_for_delivered;
        $return_data['order_count_of_today_for_delivered_completed'] = $order_count_of_today_for_delivered_completed;
        $return_data['order_count_of_today_for_delivered_inside'] = $order_count_of_today_for_delivered_inside;
        $return_data['order_count_of_today_for_delivered_tomorrow'] = $order_count_of_today_for_delivered_tomorrow;
        $return_data['order_count_of_today_for_delivered_repeated'] = $order_count_of_today_for_delivered_repeated;
        $return_data['order_count_of_today_for_delivered_rejected'] = $order_count_of_today_for_delivered_rejected;
        $return_data['order_count_of_today_for_delivered_effective'] = $order_count_of_today_for_delivered_effective;
        if($order_count_of_today_for_delivered)
        {
            $return_data['order_rate_of_today_for_delivered_effective'] = round(($order_count_of_today_for_delivered_effective * 100 / $order_count_of_today_for_delivered),2);
        }
        else $return_data['order_rate_of_today_for_delivered_effective'] = 0;


        // 当月统计
        $query_order_of_month = (clone $query)->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->select(DB::raw("
                    count(*) as order_count_for_all,
                    count(IF(is_published = 0, TRUE, NULL)) as order_count_for_unpublished,
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_published,
                    count(IF(is_published = 1 AND inspected_status <> 0, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside
                "))
            ->get();
        $query_delivered_of_month = (clone $query)->whereBetween('delivered_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->select(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->get();

        $order_count_of_month_for_all = $query_order_of_month[0]->order_count_for_all;
        $order_count_of_month_for_inspected = $query_order_of_month[0]->order_count_for_inspected;
        $order_count_of_month_for_accepted = $query_order_of_month[0]->order_count_for_accepted;
        $order_count_of_month_for_accepted_inside = $query_order_of_month[0]->order_count_for_accepted_inside;
        $order_count_of_month_for_refused = $query_order_of_month[0]->order_count_for_refused;
        $order_count_of_month_for_repeated = $query_order_of_month[0]->order_count_for_repeated;

        $return_data['order_count_of_month_for_all'] = $order_count_of_month_for_all;
        $return_data['order_count_of_month_for_inspected'] = $order_count_of_month_for_inspected;
        $return_data['order_count_of_month_for_accepted'] = $order_count_of_month_for_accepted;
        $return_data['order_count_of_month_for_accepted_inside'] = $order_count_of_month_for_accepted_inside;
        $return_data['order_count_of_month_for_refused'] = $order_count_of_month_for_refused;
        $return_data['order_count_of_month_for_repeated'] = $order_count_of_month_for_repeated;
        if($order_count_of_month_for_inspected)
        {
            $return_data['order_count_of_month_for_rate'] = round(($order_count_of_month_for_accepted * 100 / $order_count_of_month_for_inspected),2);
        }
        else $return_data['order_count_of_month_for_rate'] = 0;


        $order_count_of_month_for_delivered = $query_delivered_of_month[0]->order_count_for_delivered;
        $order_count_of_month_for_delivered_completed = $query_delivered_of_month[0]->order_count_for_delivered_completed;
        $order_count_of_month_for_delivered_inside = $query_delivered_of_month[0]->order_count_for_delivered_inside;
        $order_count_of_month_for_delivered_tomorrow = $query_delivered_of_month[0]->order_count_for_delivered_tomorrow;
        $order_count_of_month_for_delivered_repeated = $query_delivered_of_month[0]->order_count_for_delivered_repeated;
        $order_count_of_month_for_delivered_rejected = $query_delivered_of_month[0]->order_count_for_delivered_rejected;
        $order_count_of_month_for_delivered_effective = $order_count_of_month_for_delivered_completed + $order_count_of_month_for_delivered_inside + $order_count_of_month_for_delivered_tomorrow;

        $return_data['order_count_of_month_for_delivered'] = $order_count_of_month_for_delivered;
        $return_data['order_count_of_month_for_delivered_completed'] = $order_count_of_month_for_delivered_completed;
        $return_data['order_count_of_month_for_delivered_inside'] = $order_count_of_month_for_delivered_inside;
        $return_data['order_count_of_month_for_delivered_tomorrow'] = $order_count_of_month_for_delivered_tomorrow;
        $return_data['order_count_of_month_for_delivered_repeated'] = $order_count_of_month_for_delivered_repeated;
        $return_data['order_count_of_month_for_delivered_rejected'] = $order_count_of_month_for_delivered_rejected;
        $return_data['order_count_of_month_for_delivered_effective'] = $order_count_of_month_for_delivered_effective;
        if($order_count_of_month_for_delivered)
        {
            $return_data['order_rate_of_month_for_delivered_effective'] = round(($order_count_of_month_for_delivered_effective * 100 / $order_count_of_month_for_delivered),2);
        }
        else $return_data['order_rate_of_month_for_delivered_effective'] = 0;



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
        $user_isset = 0;
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
        if(isset($post_data['user']))
        {
            if(!in_array($post_data['user'],[-1]))
            {
                $user_isset = 1;
                $user_id = $post_data['user'];
            }
        }



        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');


        // 日报统计


        // 本月每日日报量
        $query_for_order_this_month = DK_Finance_Daily::select('id','assign_time')
            ->whereBetween('assign_time',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(assign_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(assign_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(assign_time,'%e') as day,
                    count(*) as quantity,
                    sum(amount + oil_card_amount) as income_sum
                "));

        if($staff_isset) $query_for_order_this_month->where('creator_id', $staff_id);
        if($user_isset) $query_for_order_this_month->where('user_id', $user_id);


        $statistics_data_for_order_this_month = $query_for_order_this_month->get()->keyBy('day');
        $return_data['statistics_data_for_order_this_month'] = $statistics_data_for_order_this_month;

        // 上月每日日报量
        $query_for_order_last_month = DK_Finance_Daily::select('id','assign_time')
            ->whereBetween('assign_time',[$the_last_month_start_timestamp,$the_last_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(assign_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(assign_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(assign_time,'%e') as day,
                    count(*) as quantity,
                    sum(amount + oil_card_amount) as income_sum
                "));

        if($staff_isset) $query_for_order_last_month->where('creator_id', $staff_id);
        if($user_isset) $query_for_order_last_month->where('user_id', $user_id);



        $statistics_data_for_order_last_month = $query_for_order_last_month->get()->keyBy('day');
        $return_data['statistics_data_for_order_last_month'] = $statistics_data_for_order_last_month;


        return response_success($return_data,"");
    }


    // 【统计】排名
    public function view_statistic_rank()
    {
        $this->get_me();
        $me = $this->me;

        $department_district_list = DK_Finance_Company::select('id','name')->where('department_type',11)->get();
        $view_data['department_district_list'] = $department_district_list;

        if($me->user_type == 81)
        {
            $view_data['department_district_id'] = $me->department_district_id;
            $department_group_list = DK_Finance_Company::select('id','name')->where('superior_company_id',$me->department_district_id)->get();
            $view_data['department_group_list'] = $department_group_list;
        }

        $view_data['menu_active_of_statistic_rank'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-rank';
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
            // 日报统计
            $query_order = DK_Finance_Daily::select('department_manager_id')
                ->groupBy('department_manager_id');
        }
        else if($rank_staff_type == 81)
        {
            // 日报统计
            $query_order = DK_Finance_Daily::select('department_manager_id')
                ->groupBy('department_manager_id');
        }
        else if($rank_staff_type == 84)
        {
            // 日报统计
            $query_order = DK_Finance_Daily::select('department_supervisor_id')
                ->groupBy('department_supervisor_id');
        }
        else
        {
            // 日报统计
            $query_order = DK_Finance_Daily::select('creator_id')
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
            // 日报统计
            $query_order = $query_order->get()->keyBy('department_manager_id')->toArray();
        }
        else if($rank_staff_type == 81)
        {
            // 日报统计
            $query_order = $query_order->get()->keyBy('department_manager_id')->toArray();
        }
        else if($rank_staff_type == 84)
        {
            // 日报统计
            $query_order = $query_order->get()->keyBy('department_supervisor_id')->toArray();
        }
        else
        {
            // 日报统计
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





        $query = DK_Finance_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id'])
            ->where('user_status',1)
            ->with([
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ]);


        // 公司或渠道
        if($me->user_type == 41)
        {
            // 根据公司或渠道（大区）查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 81)
        {
            // 根据公司或渠道（大区）查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据公司或渠道（小组）查看
            $query->where('department_group_id', $me->department_group_id);
        }


        // 公司或渠道-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->where('department_district_id', $post_data['department_district']);
            }
        }
        // 公司或渠道-小组
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
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-rank-by-staff';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_rank_by_staff($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Finance_User::select(['id','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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
//            $subordinates_array = DK_Finance_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Finance_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据公司或渠道查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_Finance_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Finance_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据公司或渠道查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据公司或渠道查看
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
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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
        $query_order = DK_Finance_Daily::select('creator_id')
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


        $query = DK_Finance_User::select(['id','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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


        // 公司或渠道经理
        if($me->user_type == 41)
        {
            // 根据属下查看
//            $subordinates_array = DK_Finance_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Finance_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据公司或渠道查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_Finance_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Finance_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据公司或渠道查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据公司或渠道查看
            $query->where('department_group_id', $me->department_group_id);
        }



        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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
    // 【统计】公司或渠道排名
    public function view_statistic_rank_by_department()
    {
        $this->get_me();
        $me = $this->me;

        $view_data['menu_active_of_statistic_rank_by_department'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-rank-by-department';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_rank_by_department($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Finance_User::select(['id','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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
//            $subordinates_array = DK_Finance_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Finance_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据公司或渠道查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据公司或渠道查看
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
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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

        $department_district_list = DK_Finance_Company::select('id','name')->where('department_type',11)->get();
        $view_data['department_district_list'] = $department_district_list;

        if($me->user_type == 81)
        {
            $view_data['department_district_id'] = $me->department_district_id;
            $department_group_list = DK_Finance_Company::select('id','name')->where('superior_company_id',$me->department_district_id)->get();
            $view_data['department_group_list'] = $department_group_list;
        }

        $view_data['menu_active_of_statistic_recent'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-recent';
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
            // 日报统计
            $query_order = DK_Finance_Daily::select('department_manager_id','published_at')
                ->groupBy('department_manager_id');
        }
        else if($rank_staff_type == 81)
        {
            // 日报统计
            $query_order = DK_Finance_Daily::select('department_manager_id','published_at')
                ->groupBy('department_manager_id');
        }
        else if($rank_staff_type == 84)
        {
            // 日报统计
            $query_order = DK_Finance_Daily::select('department_supervisor_id','published_at')
                ->groupBy('department_supervisor_id');
        }
        else
        {
            // 日报统计
            $query_order = DK_Finance_Daily::select('creator_id','published_at')
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
            // 日报统计
            $order_list = $query_order->get()->groupBy('department_manager_id')->toArray();
        }
        else if($rank_staff_type == 81)
        {
            // 日报统计
            $order_list = $query_order->get()->groupBy('department_manager_id')->toArray();
        }
        else if($rank_staff_type == 84)
        {
            // 日报统计
            $order_list = $query_order->get()->groupBy('department_supervisor_id')->toArray();
        }
        else
        {
            // 日报统计
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





        $query = DK_Finance_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id'])
            ->where('user_status',1)
            ->with([
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ]);


        // 公司或渠道
        if($me->user_type == 41)
        {
            // 根据公司或渠道（大区）查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 81)
        {
            // 根据公司或渠道（大区）查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据公司或渠道（小组）查看
            $query->where('department_group_id', $me->department_group_id);
        }


        // 公司或渠道-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->where('department_district_id', $post_data['department_district']);
            }
        }
        // 公司或渠道-小组
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
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-customer-service';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_customer_service($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Finance_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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
//            $subordinates_array = DK_Finance_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Finance_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据公司或渠道查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据公司或渠道查看
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
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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
        $query_order = DK_Finance_Daily::select('creator_id')
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
        $query_order_for_manager = DK_Finance_Daily::select('department_manager_id')
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
        $query_order_for_supervisor = DK_Finance_Daily::select('department_supervisor_id')
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



        $query = DK_Finance_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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



        // 公司或渠道经理
        if($me->user_type == 41)
        {
            // 根据属下查看
//            $subordinates_array = DK_Finance_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Finance_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据公司或渠道查看
            $query->where('department_district_id', $me->department_district_id);
        }
        // 客服经理
        else if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_Finance_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Finance_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据公司或渠道查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据公司或渠道查看
            $query->where('department_group_id', $me->department_group_id);
        }



        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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
        if(!in_array($me->user_type,[0,1,9,11,41,71])) return view($this->view_blade_403);

        $view_data['menu_active_of_statistic_inspector'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-inspector';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_inspector($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Finance_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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
        $query_order = DK_Finance_Daily::select('inspector_id')
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




        $query = DK_Finance_User::select(['id','mobile','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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

        // 根据公司或渠道查看
        if($me->department_district_id > 0)
        {
            // 根据公司或渠道查看
            $query->where('department_district_id', $me->department_district_id);
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-deliverer';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_deliverer($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Finance_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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
        $query_order = DK_Finance_Daily::select('inspector_id')
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




        $query = DK_Finance_User::select(['id','mobile','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
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

        // 根据公司或渠道查看
        if($me->department_district_id > 0)
        {
            // 根据公司或渠道查看
            $query->where('department_district_id', $me->department_district_id);
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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





    // 【统计】【项目报表】显示-视图
    public function view_statistic_project()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,81,84,71,77,61,66])) return view($this->view_blade_403);

        // 显示数量
        if(!empty($post_data['length']))
        {
            if(is_numeric($post_data['length']) && $post_data['length'] > 0) $view_data['length'] = $post_data['length'];
            else $view_data['length'] = 20;
        }
        else $view_data['length'] = 50;
        // 第几页
        if(!empty($post_data['page']))
        {
            if(is_numeric($post_data['page']) && $post_data['page'] > 0) $view_data['page'] = $post_data['page'];
            else $view_data['page'] = 1;
        }
        else $view_data['page'] = 1;

        $company_list = DK_Finance_Company::select('id','name')->where('company_category',1)->get();
        $view_data['company_list'] = $company_list;

        $channel_list = DK_Finance_Company::select('id','name')->where('company_category',11)->get();
        $view_data['channel_list'] = $channel_list;

        if($me->user_type == 41)
        {
            $project_list = DK_Finance_Project::select('id','name')->where('channel_id',$me->channel_id)->get();
            $view_data['project_list'] = $project_list;
        }
        else
        {
            $project_list = DK_Finance_Project::select('id','name')->get();
            $view_data['project_list'] = $project_list;
        }

        $view_data['menu_active_of_statistic_project'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-project';
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
        $query_order = DK_Finance_Daily::select('project_id')
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


        $query = DK_Finance_Project::select('*')
            ->where('item_status', 1)
            ->withTrashed()
            ->with(['creator','inspector_er','pivot_project_user','pivot_project_team']);

        if(in_array($me->user_type,[41]))
        {
            $channel_id = $me->channel_id;
            $project_list = DK_Finance_Project::select('id')->where('channel_id',$channel_id)->get();
            $query->whereIn('id',$project_list);
        }


        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 公司或渠道-大区
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



        $total_data = $total_data;



        return datatable_response($list, $draw, $total);

    }
    // 【统计】【项目报表】返回-日报列表-数据
    public function get_statistic_data_for_project_of_daily_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Finance_Daily::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->with([
                'creator',
                'project_er'=>function($query) {
                    $query->select('id','name')->with([
                        'channel_er'=>function($query1) { $query1->select('id','name'); },
                        'business_or'=>function($query1) { $query1->select('id','username'); }
                    ]);
                },
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

        if($me->user_type == 41)
        {
            $project_list = DK_Finance_Project::select('id')->where('channel_id',$me->channel_id)->get();
            $query->whereIn('project_id',$project_list);
        }


        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['assign'])) $query->whereDate('assign_date', $post_data['assign']);
        if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '>=', $post_data['assign_start']);
        if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '<=', $post_data['assign_ended']);

        if(!empty($post_data['month']))
        {
            $month_arr = explode('-', $post_data['month']);
            $month_year = $month_arr[0];
            $month_month = $month_arr[1];
            $query->whereYear("assign_date", $month_year)->whereMonth("assign_date", $month_month);
        }


        // 公司
        if(isset($post_data['company']))
        {
            if(!in_array($post_data['company'],[-1]))
            {
                $channel_list = DK_Finance_Company::select('id')->where('superior_company_id',$post_data['company'])->get()->toArray();
                $project_list = DK_Finance_Project::select('id')->whereIn('channel_id',$channel_list)->get()->toArray();
                $query->whereIn('project_id', $project_list);
            }
        }
        // 渠道
        if(isset($post_data['channel']))
        {
            if(!in_array($post_data['channel'],[-1]))
            {
                $project_list = DK_Finance_Project::select('id')->where('channel_id',$post_data['channel'])->get()->toArray();
                $query->whereIn('project_id', $project_list);
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
        return datatable_response($list, $draw, $total);
    }
    // 【统计】【项目报表】返回-图标-数据
    public function get_statistic_data_for_project_of_chart($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query_for_daily = DK_Finance_Daily::select('*')
//            ->where('finance_type',1)
            ->groupBy("assign_date")
            ->select(DB::raw("
                    assign_date as date,
                    DAY(assign_date) as day,
                    count(*) as quantity,
                    sum(attendance_manpower) as attendance_manpower_total,
                    sum(delivery_quantity) as delivery_quantity_total,
                    sum(delivery_quantity_of_invalid) as delivery_quantity_of_invalid_total,
                    sum(total_daily_cost) as total_daily_cost_total
                "));

        if($me->user_type == 41)
        {
            $project_list = DK_Finance_Project::select('id')->where('channel_id',$me->channel_id)->get();
            $query_for_daily->whereIn('project_id',$project_list);
        }


        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1]))
            {
                $query_for_daily->where('project_id', $post_data['project']);
            }
        }

        // 指定月份
        if(!empty($post_data['month']))
        {
            $month_arr = explode('-', $post_data['month']);
            $month_year = $month_arr[0];
            $month_month = $month_arr[1];
            $query_for_daily->whereYear("assign_date", $month_year)->whereMonth("assign_date", $month_month);
        }


        $statistics_data_for_daily = $query_for_daily->get()->keyBy('day');

        foreach($statistics_data_for_daily as $k => $v)
        {
            // 人均
            if($v->attendance_manpower_total == 0)
            {
                $statistics_data_for_daily[$k]->per_capita = 0;
            }
            else
            {
                $statistics_data_for_daily[$k]->per_capita = $v->total_daily_cost_total / $v->attendance_manpower_total;
            }

            // 单均
            if($v->delivery_quantity_total == 0)
            {
                $statistics_data_for_daily[$k]->unit_average = 0;
            }
            else
            {
                $statistics_data_for_daily[$k]->unit_average = $v->total_daily_cost_total / $v->delivery_quantity_total;
            }
        }
//        dd($statistics_data_for_daily->toArray());
        $return_data['statistics_data'] = $statistics_data_for_daily;

        return response_success($return_data,"");
    }


    // 【统计】【公司&渠道-报表】显示-视图
    public function view_statistic_company()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,81,84,71,77,61,66])) return view($this->view_blade_403);

        $company_list = DK_Finance_Company::select('id','name')->where('company_category',1)->get();
        $view_data['company_list'] = $company_list;

        $channel_list = DK_Finance_Company::select('id','name')->where('company_type',11)->get();
        $view_data['channel_list'] = $channel_list;

        $project_list = DK_Finance_Project::select('id','name')->get();
        $view_data['project_list'] = $project_list;

        $view_data['menu_active_of_statistic_company'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-company';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_company($post_data)
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
        $query_order = DK_Finance_Daily::select('project_id')
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


        $query = DK_Finance_Project::select('*')
            ->where('item_status', 1)
            ->withTrashed()
            ->with(['creator','inspector_er','pivot_project_user','pivot_project_team']);

        if(in_array($me->user_type,[41]))
        {
            $channel_id = $me->channel_id;
            $project_list = DK_Finance_Project::select('id')->where('channel_id',$channel_id)->get();
            $query->whereIn('id',$project_list);
        }


        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 公司或渠道-大区
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
    // 【统计】【公司&渠道-报表】返回-项目列表-数据
    public function get_statistic_data_for_company_of_project_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 团队统计
        $query_daily = DK_Finance_Daily::select('project_id')
            ->addSelect(DB::raw("
                    sum(delivery_quantity) as total_delivery_quantity,
                    sum(delivery_quantity_of_invalid) as total_delivery_quantity_of_invalid,
                    sum(total_daily_cost) as total_cost
                "))
//            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
            ->groupBy('project_id');

        if(!empty($post_data['month']))
        {
            $month_arr = explode('-', $post_data['month']);
            $month_year = $month_arr[0];
            $month_month = $month_arr[1];
            $query_daily->whereYear("assign_date", $month_year)->whereMonth("assign_date", $month_month);
        }

        if(!empty($post_data['date']))
        {
            $query_daily->whereDate("assign_date", $post_data['date']);
        }

        $query_daily = $query_daily->get()
            ->keyBy('project_id')
            ->toArray();


        $query = DK_Finance_Project::select('*')
            ->withTrashed()
            ->with(['creator','company_er','channel_er','business_or']);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 公司
        if(isset($post_data['company']))
        {
            if(!in_array($post_data['company'],[-1]))
            {
                $channel_list = DK_Finance_Company::select('id')->where('superior_company_id',$post_data['company'])->get()->toArray();
                $query->whereIn('channel_id', $channel_list);
            }
        }
        // 渠道
        if(isset($post_data['channel']))
        {
            if(!in_array($post_data['channel'],[-1]))
            {
                $query->where('channel_id', $post_data['channel']);
            }
        }
        // 商务
        if(isset($post_data['business']))
        {
            if(!in_array($post_data['business'],[-1]))
            {
                $query->where('business_id', $post_data['business']);
            }
        }
        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1]))
            {
                $query->where('id', $post_data['project']);
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


        $total_data = [];
        $total_data['id'] = '总计';
        $total_data['name'] = '--';
        $total_data['date_day'] = '统计';
        $total_data['channel_id'] = 0;
        $total_data['business_id'] = 0;

        $total_data['total_delivery_quantity'] = 0;
        $total_data['total_delivery_quantity_of_invalid'] = 0;
        $total_data['delivery_effective_quantity'] = 0;
        $total_data['total_cost'] = 0;
        $total_data['channel_unit_price'] = 0;
        $total_data['channel_cost'] = 0;
        $total_data['cooperative_unit_price'] = 0;
        $total_data['cooperative_cost'] = 0;
        $total_data['settled_amount'] = 0;
        $total_data['balance'] = 0;

        foreach($list as $k => $v)
        {

            if(isset($query_daily[$v->id]))
            {
                $list[$k]->total_delivery_quantity = $query_daily[$v->id]['total_delivery_quantity'];
                $list[$k]->total_delivery_quantity_of_invalid = $query_daily[$v->id]['total_delivery_quantity_of_invalid'];
                $list[$k]->total_cost = $query_daily[$v->id]['total_cost'];
            }
            else
            {
                $list[$k]->total_delivery_quantity = 0;
                $list[$k]->total_delivery_quantity_of_invalid = 0;
                $list[$k]->total_cost = 0;
            }
            $list[$k]->channel_cost = 0;
            $list[$k]->cooperative_cost = 0;
            $list[$k]->balance = 0;
            $list[$k]->cooperative_cost = ($v->cooperative_unit_price * ($v->total_delivery_quantity - $v->total_delivery_quantity_of_invalid));


            $total_data['total_delivery_quantity'] += $v->total_delivery_quantity;
            $total_data['total_delivery_quantity_of_invalid'] += $v->total_delivery_quantity_of_invalid;
            $total_data['total_cost'] += $v->total_cost;

            $total_data['channel_cost'] += ($v->channel_unit_price * $v->total_delivery_quantity);

            $total_data['cooperative_cost'] += ($v->cooperative_unit_price * ($v->total_delivery_quantity - $v->total_delivery_quantity_of_invalid));

            $total_data['settled_amount'] += $v->settled_amount;
        }

        $total_data['channel_unit_price'] = 0;
        $total_data['cooperative_unit_price'] = 0;
        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }


    // 【统计】【公司报表】
    public function view_statistic_company1()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19])) return view($this->view_blade_403);

        $company_list = DK_Finance_Company::select('id','name')->where('company_category',1)->get();
        $view_data['company_list'] = $company_list;

        $view_data['menu_active_of_statistic_company'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-company';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_company1($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

        // 公司或渠道统计
        $query_order = DK_Finance_Daily::select('department_district_id')
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

        $query = DK_Finance_Company::select('id','name')
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




    // 【统计】【业务报表】显示-视图
    public function view_statistic_service()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,31,41])) return view($this->view_blade_403);

        // 显示数量
        if(!empty($post_data['length']))
        {
            if(is_numeric($post_data['length']) && $post_data['length'] > 0) $view_data['length'] = $post_data['length'];
            else $view_data['length'] = 20;
        }
        else $view_data['length'] = 40;
        // 第几页
        if(!empty($post_data['page']))
        {
            if(is_numeric($post_data['page']) && $post_data['page'] > 0) $view_data['page'] = $post_data['page'];
            else $view_data['page'] = 1;
        }
        else $view_data['page'] = 1;

        $company_list = DK_Finance_Company::select('id','name')->where('company_category',1)->get();
        $view_data['company_list'] = $company_list;

        $channel_list = DK_Finance_Company::select('id','name')->where('company_category',11)->get();
        $view_data['channel_list'] = $channel_list;

        $business_list = DK_Finance_User::select('id','username')->where('user_type',61)->get();
        $view_data['business_list'] = $business_list;

        $project_list = DK_Finance_Project::select('id','name')->get();
        $view_data['project_list'] = $project_list;

        $view_data['menu_active_of_statistic_service'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-service';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_service($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        if(in_array($me->user_type,[41]))
        {
            $department_district_id = $me->department_district_id;
        }
        else $department_district_id = 0;


        // 团队统计
        $query_order = DK_Finance_Daily::select('project_id')
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


        $query = DK_Finance_Project::select('*')
            ->where('item_status', 1)
            ->withTrashed()
            ->with(['creator','inspector_er','pivot_project_user','pivot_project_team']);

        if(in_array($me->user_type,[41]))
        {
            $channel_id = $me->channel_id;
            $project_list = DK_Finance_Project::select('id')->where('channel_id',$channel_id)->get();
            $query->whereIn('id',$project_list);
        }


        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 公司或渠道-大区
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
    // 【统计】【业务报表】返回-项目列表-数据
    public function get_statistic_data_for_service_of_project_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 日报统计
        $query_daily = DK_Finance_Daily::select('project_id')
            ->addSelect(DB::raw("
                    sum(delivery_quantity) as total_delivery_quantity,
                    sum(delivery_quantity_of_invalid) as total_delivery_quantity_of_invalid,
                    sum(total_daily_cost) as total_cost
                "))
//            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
            ->groupBy('project_id');


        if(!empty($post_data['time_type']))
        {
            if($post_data['time_type'] == "month")
            {
                if(!empty($post_data['month']))
                {
                    $month_arr = explode('-', $post_data['month']);
                    $month_year = $month_arr[0];
                    $month_month = $month_arr[1];
                    $query_daily->whereYear("assign_date", $month_year)->whereMonth("assign_date", $month_month);
                }
            }
            else if($post_data['time_type'] == "date")
            {
                if(!empty($post_data['date']))
                {
                    $query_daily->whereDate("assign_date", $post_data['date']);
                }
            }
            else if($post_data['time_type'] == "period")
            {
            }
            else
            {}
        }


        $query_daily = $query_daily->get()
            ->keyBy('project_id')
            ->toArray();


        $query = DK_Finance_Project::select('*')
            ->withTrashed()
            ->with(['creator','company_er','channel_er','business_or']);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 公司
        if(isset($post_data['company']))
        {
            if(!in_array($post_data['company'],[-1]))
            {
                $channel_list = DK_Finance_Company::select('id')->where('superior_company_id',$post_data['company'])->get()->toArray();
                $query->whereIn('channel_id', $channel_list);
            }
        }
        // 渠道
        if(isset($post_data['channel']))
        {
            if(!in_array($post_data['channel'],[-1]))
            {
                $query->where('channel_id', $post_data['channel']);
            }
        }
        // 商务
        if(isset($post_data['business']))
        {
            if(!in_array($post_data['business'],[-1]))
            {
                $query->where('business_id', $post_data['business']);
            }
        }
        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1]))
            {
                $query->where('id', $post_data['project']);
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


        $total_data = [];
        $total_data['id'] = '总计';
        $total_data['name'] = '总计';
        $total_data['date_day'] = '总计';
        $total_data['channel_id'] = 0;
        $total_data['business_id'] = 0;

        $total_data['total_delivery_quantity'] = 0;
        $total_data['total_delivery_quantity_of_invalid'] = 0;
        $total_data['delivery_effective_quantity'] = 0;
        $total_data['total_cost'] = 0;
        $total_data['channel_unit_price'] = 0;
        $total_data['channel_cost'] = 0;
        $total_data['cooperative_unit_price'] = 0;
        $total_data['cooperative_cost'] = 0;
        $total_data['settled_amount'] = 0;
        $total_data['balance'] = 0;

        foreach($list as $k => $v)
        {

            if(isset($query_daily[$v->id]))
            {
                $list[$k]->total_delivery_quantity = $query_daily[$v->id]['total_delivery_quantity'];
                $list[$k]->total_delivery_quantity_of_invalid = $query_daily[$v->id]['total_delivery_quantity_of_invalid'];
                $list[$k]->total_cost = $query_daily[$v->id]['total_cost'];
            }
            else
            {
                $list[$k]->total_delivery_quantity = 0;
                $list[$k]->total_delivery_quantity_of_invalid = 0;
                $list[$k]->total_cost = 0;
            }
            $list[$k]->channel_cost = 0;
            $list[$k]->cooperative_cost = 0;
            $list[$k]->balance = 0;
            $list[$k]->cooperative_cost = ($v->cooperative_unit_price * ($v->total_delivery_quantity - $v->total_delivery_quantity_of_invalid));


            $total_data['total_delivery_quantity'] += $v->total_delivery_quantity;
            $total_data['total_delivery_quantity_of_invalid'] += $v->total_delivery_quantity_of_invalid;
            $total_data['total_cost'] += $v->total_cost;

            $total_data['channel_cost'] += ($v->channel_unit_price * $v->total_delivery_quantity);

            $total_data['cooperative_cost'] += ($v->cooperative_unit_price * ($v->total_delivery_quantity - $v->total_delivery_quantity_of_invalid));

            $total_data['settled_amount'] += $v->settled_amount;
        }

        $total_data['channel_unit_price'] = 0;
        $total_data['cooperative_unit_price'] = 0;
        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }
    // 【统计】【业务报表】返回-日报列表-数据
    public function get_statistic_data_for_service_of_daily_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Finance_Daily::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->with([
                'creator',
                'project_er'=>function($query) {
                    $query->select('id','name','channel_id','business_id')->with([
                        'channel_er'=>function($query1) { $query1->select('id','name'); },
                        'business_or'=>function($query2) { $query2->select('id','username'); }
                    ]);
                },
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

        if($me->user_type == 41)
        {
            $project_list = DK_Finance_Project::select('id')->where('channel_id',$me->channel_id)->get();
            $query->whereIn('project_id',$project_list);
        }


        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['assign'])) $query->whereDate('assign_date', $post_data['assign']);
        if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '>=', $post_data['assign_start']);
        if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '<=', $post_data['assign_ended']);


        if(!empty($post_data['time_type']))
        {
            if($post_data['time_type'] == "month")
            {
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
                if(!empty($post_data['date']))
                {
                    $query->whereDate("assign_date", $post_data['date']);
                }
            }
            else if($post_data['time_type'] == "period")
            {
            }
            else
            {}
        }


        // 公司
        if(isset($post_data['company']))
        {
            if(!in_array($post_data['company'],[-1]))
            {
                $channel_list = DK_Finance_Company::select('id')->where('superior_company_id',$post_data['company'])->get()->toArray();
                $project_list = DK_Finance_Project::select('id')->whereIn('channel_id',$channel_list)->get()->toArray();
                $query->whereIn('project_id', $project_list);
            }
        }
        // 渠道
        if(isset($post_data['channel']))
        {
            if(!in_array($post_data['channel'],[-1]))
            {
                $project_list = DK_Finance_Project::select('id')->where('channel_id',$post_data['channel'])->get()->toArray();
                $query->whereIn('project_id', $project_list);
            }
        }
        // 商务
        if(isset($post_data['business']))
        {
            if(!in_array($post_data['business'],[-1]))
            {
                $project_list = DK_Finance_Project::select('id')->where('business_id',$post_data['business'])->get()->toArray();
                $query->whereIn('project_id', $project_list);
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
        else $query->orderBy("assign_date", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();


        $total_data = [];
        $total_data['id'] = '总计';
        $total_data['name'] = '--';
        $total_data['project_id'] = '--';
        $total_data['assign_date'] = '--';
        $total_data['outbound_background'] = '--';
        $total_data['date_day'] = '统计';
        $total_data['channel_id'] = 0;

        $total_data['attendance_manpower'] = 0;
        $total_data['delivery_quantity'] = 0;
        $total_data['delivery_quantity_of_invalid'] = 0;

        $total_data['manpower_daily_cost'] = 0;
        $total_data['call_charge_daily_cost'] = 0;
        $total_data['material_daily_quantity'] = 0;
        $total_data['material_daily_cost'] = 0;
        $total_data['taxes_daily_cost'] = 0;
        $total_data['total_daily_cost'] = 0;

        $total_data['manpower_daily_wage'] = "--";
        $total_data['call_charge_coefficient'] = "--";
        $total_data['material_coefficient'] = "--";
        $total_data['taxes_coefficient'] = "--";

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

            $total_data['attendance_manpower'] += $v->attendance_manpower;
            $total_data['delivery_quantity'] += $v->delivery_quantity;
            $total_data['delivery_quantity_of_invalid'] += $v->delivery_quantity_of_invalid;

            $total_data['manpower_daily_cost'] += $v->manpower_daily_cost;
            $total_data['call_charge_daily_cost'] += $v->call_charge_daily_cost;
            $total_data['material_daily_cost'] += $v->material_daily_cost;
            $total_data['taxes_daily_cost'] += $v->taxes_daily_cost;
            $total_data['total_daily_cost'] += $v->total_daily_cost;
        }
//        dd($list->toArray());

        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }
    // 【统计】【项目报表】返回-图标-数据
    public function get_statistic_data_for_service_of_daily_chart($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query_for_daily = DK_Finance_Daily::select('*')
//            ->where('finance_type',1)
            ->groupBy("assign_date")
            ->select(DB::raw("
                    assign_date as date,
                    DAY(assign_date) as day,
                    count(*) as quantity,
                    sum(attendance_manpower) as attendance_manpower_total,
                    sum(delivery_quantity) as delivery_quantity_total,
                    sum(delivery_quantity_of_invalid) as delivery_quantity_of_invalid_total,
                    sum(total_daily_cost) as total_daily_cost_total
                "));

        if($me->user_type == 41)
        {
            $project_list = DK_Finance_Project::select('id')->where('channel_id',$me->channel_id)->get();
            $query_for_daily->whereIn('project_id',$project_list);
        }


        // 公司
        if(isset($post_data['company']))
        {
            if(!in_array($post_data['company'],[-1]))
            {
                $channel_list = DK_Finance_Company::select('id')->where('superior_company_id',$post_data['company'])->get()->toArray();
                $project_list = DK_Finance_Project::select('id')->whereIn('channel_id',$channel_list)->get()->toArray();
                $query_for_daily->whereIn('project_id', $project_list);
            }
        }
        // 渠道
        if(isset($post_data['channel']))
        {
            if(!in_array($post_data['channel'],[-1]))
            {
                $project_list = DK_Finance_Project::select('id')->where('channel_id',$post_data['channel'])->get()->toArray();
                $query_for_daily->whereIn('project_id', $project_list);
            }
        }
        // 商务
        if(isset($post_data['business']))
        {
            if(!in_array($post_data['business'],[-1]))
            {
                $query_for_daily->where('business_id', $post_data['business']);
            }
        }
        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1]))
            {
                $query_for_daily->where('project_id', $post_data['project']);
            }
        }


        if(!empty($post_data['time_type']))
        {
            if($post_data['time_type'] == "month")
            {
                // 指定月份
                if(!empty($post_data['time']))
                {
                    $month_arr = explode('-', $post_data['time']);
                    $month_year = $month_arr[0];
                    $month_month = $month_arr[1];
                    $query_for_daily->whereYear("assign_date", $month_year)->whereMonth("assign_date", $month_month);
                }
            }
            else if($post_data['time_type'] == "date")
            {
                // 指定日期
                if(!empty($post_data['time']))
                {
                    $query_for_daily->whereDate("assign_date", $post_data['time']);
                }
            }
            else if($post_data['time_type'] == "period")
            {
            }
            else
            {}
        }




        $statistics_data_for_daily = $query_for_daily->get()->keyBy('day');

        foreach($statistics_data_for_daily as $k => $v)
        {
            // 人均
            if($v->attendance_manpower_total == 0)
            {
                $statistics_data_for_daily[$k]->per_capita = 0;
            }
            else
            {
                $statistics_data_for_daily[$k]->per_capita = $v->total_daily_cost_total / $v->attendance_manpower_total;
            }

            // 单均
            if($v->delivery_quantity_total == 0)
            {
                $statistics_data_for_daily[$k]->unit_average = 0;
            }
            else
            {
                $statistics_data_for_daily[$k]->unit_average = $v->total_daily_cost_total / $v->delivery_quantity_total;
            }
        }
//        dd($statistics_data_for_daily->toArray());
        $return_data['statistics_data'] = $statistics_data_for_daily;

        return response_success($return_data,"");
    }




    // 【统计】【业务报表】显示-视图
    public function view_statistic_company_overview($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,31,41])) return view($this->view_blade_403);

        // 显示数量
        if(!empty($post_data['length']))
        {
            if(is_numeric($post_data['length']) && $post_data['length'] > 0) $view_data['length'] = $post_data['length'];
            else $view_data['length'] = 20;
        }
        else $view_data['length'] = 40;
        // 第几页
        if(!empty($post_data['page']))
        {
            if(is_numeric($post_data['page']) && $post_data['page'] > 0) $view_data['page'] = $post_data['page'];
            else $view_data['page'] = 1;
        }
        else $view_data['page'] = 1;


        // 公司ID
        if(!empty($post_data['company_id']))
        {
            if(is_numeric($post_data['company_id']) && $post_data['company_id'] > 0)
            {
                $company = DK_Finance_Company::find($post_data['company_id']);
                if($company)
                {
                    $view_data['company_id'] = $post_data['company_id'];
                    $view_data['company_name'] = $company->name;
                }
                else $view_data['company_id'] = '';
            }
            else $view_data['company_id'] = '';
        }
        else $view_data['company_id'] = '';


        $company_list = DK_Finance_Company::select('id','name')->where('company_category',1)->get();
        $view_data['company_list'] = $company_list;

        $channel_list = DK_Finance_Company::select('id','name')->where('company_category',11)->get();
        $view_data['channel_list'] = $channel_list;

        $business_list = DK_Finance_User::select('id','username')->where('user_type',61)->get();
        $view_data['business_list'] = $business_list;

        $project_list = DK_Finance_Project::select('id','name')->get();
        $view_data['project_list'] = $project_list;

        $view_data['menu_active_of_statistic_company_overview'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-company-overview';
        return view($view_blade)->with($view_data);
    }
    // 【公司与渠道管理】返回-列表-数据
    public function get_statistic_data_for_company_overview_of_channel_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 日报统计
        $query_daily = DK_Finance_Daily::select('project_id')
            ->addSelect(DB::raw("
                    sum(attendance_manpower) as total_of_attendance_manpower,
                    sum(delivery_quantity) as total_of_delivery_quantity,
                    sum(delivery_quantity_of_invalid) as total_of_delivery_quantity_of_invalid,
                    sum(call_charge_daily_cost) as total_of_call_charge_daily_cost,
                    sum(total_daily_cost) as total_cost
                "))
//            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
            ->with(["project_er"=>function($query) { $query->select('id','channel_id','channel_unit_price','cooperative_unit_price'); }])
            ->groupBy('project_id');


        if(!empty($post_data['time_type']))
        {
            if($post_data['time_type'] == "month")
            {
                if(!empty($post_data['month']))
                {
                    $month_arr = explode('-', $post_data['month']);
                    $month_year = $month_arr[0];
                    $month_month = $month_arr[1];
                    $query_daily->whereYear("assign_date", $month_year)->whereMonth("assign_date", $month_month);
                }
            }
            else if($post_data['time_type'] == "date")
            {
                if(!empty($post_data['date']))
                {
                    $query_daily->whereDate("assign_date", $post_data['date']);
                }
            }
            else if($post_data['time_type'] == "period")
            {
            }
            else
            {}
        }


        $daily_for_project = $query_daily->get()
            ->keyBy('project_id')
            ->toArray();
//        dd($daily_for_project);

        $channel_list = [];
        foreach($daily_for_project as $k => $v)
        {
            if($v["project_er"])
            {
                $channel_id = $v["project_er"]["channel_id"];
                if(empty($channel_list[$channel_id]))
                {
                    $channel_list[$channel_id] = [];
                    $channel_list[$channel_id]["total_of_attendance_manpower"] = $v["total_of_attendance_manpower"];
                    $channel_list[$channel_id]["total_of_delivery_quantity"] = $v["total_of_delivery_quantity"];
                    $channel_list[$channel_id]["total_of_delivery_quantity_of_invalid"] = $v["total_of_delivery_quantity_of_invalid"];
                    $channel_list[$channel_id]["total_of_call_charge_daily_cost"] = $v["total_of_call_charge_daily_cost"];
                    $channel_list[$channel_id]["total_of_total_cost"] = $v["total_cost"];
                    $channel_list[$channel_id]["total_of_channel_cost"] = $v["total_of_delivery_quantity"] * $v["project_er"]["channel_unit_price"];
                    $channel_list[$channel_id]["total_of_revenue"] = ($v["total_of_delivery_quantity"] - $v["total_of_delivery_quantity_of_invalid"]) * $v["project_er"]["cooperative_unit_price"];
                }
                else
                {
                    $channel_list[$channel_id]["total_of_attendance_manpower"] += $v["total_of_attendance_manpower"];
                    $channel_list[$channel_id]["total_of_delivery_quantity"] += $v["total_of_delivery_quantity"];
                    $channel_list[$channel_id]["total_of_delivery_quantity_of_invalid"] += $v["total_of_delivery_quantity_of_invalid"];
                    $channel_list[$channel_id]["total_of_call_charge_daily_cost"] += $v["total_of_call_charge_daily_cost"];
                    $channel_list[$channel_id]["total_of_total_cost"] += $v["total_cost"];
                    $channel_list[$channel_id]["total_of_channel_cost"] += ($v["total_of_delivery_quantity"] * $v["project_er"]["channel_unit_price"]);
                    $channel_list[$channel_id]["total_of_revenue"] += (($v["total_of_delivery_quantity"] - $v["total_of_delivery_quantity_of_invalid"]) * $v["project_er"]["cooperative_unit_price"]);
                }
            }
        }



        $query = DK_Finance_Company::select('*')
            ->withTrashed()
            ->with([
                'creator'=>function($query) { $query->select(['id','username','true_name']); },
//                    'leader'=>function($query) { $query->select(['id','username','true_name']); },
                'superior_company_er'=>function($query) { $query->select(['id','name']); }
            ])
            ->where('company_category',11);

        if(in_array($me->user_type,[41]))
        {
            $query->where('superior_company_id',$me->superior_company_id);
        }
        else if(in_array($me->user_type,[61]))
        {
        }

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");

        // 公司类型 [公司|渠道]
        if(!empty($post_data['company_category']))
        {
            if(!in_array($post_data['company_category'],[-1,0]))
            {
                $query->where('company_category', $post_data['company_category']);
            }
        }
        // 渠道类型 [直营|代理]
        if(!empty($post_data['company_type']))
        {
            if(!in_array($post_data['company_type'],[-1,0]))
            {
                $query->where('company_type', $post_data['company_type']);
            }
        }




        // 公司
        if(isset($post_data['company']))
        {
            if(!in_array($post_data['company'],[-1]))
            {
                $query->where('superior_company_id', $post_data['company']);
            }
        }
        // 渠道
        if(isset($post_data['channel']))
        {
            if(!in_array($post_data['channel'],[-1]))
            {
                $query->where('id', $post_data['channel']);
            }
        }
        // 商务
        if(isset($post_data['business']))
        {
            if(!in_array($post_data['business'],[-1]))
            {
                $query->where('business_id', $post_data['business']);
            }
        }
        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1]))
            {
                $query->where('id', $post_data['project']);
            }
        }



        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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
        $total_data['id'] = '总计';
        $total_data['superior_company_id'] = 0;
        $total_data['name'] = '--';

        $total_data['total_of_attendance_manpower'] = 0;
        $total_data['total_of_delivery_quantity'] = 0;
        $total_data['total_of_delivery_quantity_of_invalid'] = 0;
        $total_data['total_of_call_charge_daily_cost'] = 0;
        $total_data['total_of_total_cost'] = 0;
        $total_data['total_of_channel_cost'] = 0;
        $total_data['total_of_all_cost'] = 0;
        $total_data['total_of_revenue'] = 0;
        $total_data['total_of_profile'] = 0;

        foreach($list as $k => $v)
        {
            $list[$k]->total_of_attendance_manpower = $channel_list[($v->id)]["total_of_attendance_manpower"];
            $list[$k]->total_of_delivery_quantity = $channel_list[($v->id)]["total_of_delivery_quantity"];
            $list[$k]->total_of_delivery_quantity_of_invalid = $channel_list[($v->id)]["total_of_delivery_quantity_of_invalid"];
            $list[$k]->total_of_call_charge_daily_cost = $channel_list[($v->id)]["total_of_call_charge_daily_cost"];
            $list[$k]->total_of_total_cost = $channel_list[($v->id)]["total_of_total_cost"];
            $list[$k]->total_of_channel_cost = $channel_list[($v->id)]["total_of_channel_cost"];

            $total_of_all_cost = $channel_list[($v->id)]["total_of_total_cost"] + $channel_list[($v->id)]["total_of_channel_cost"];
            $list[$k]->total_of_all_cost = $total_of_all_cost;

            $list[$k]->total_of_revenue = $channel_list[($v->id)]["total_of_revenue"];

            $total_of_profile = $channel_list[($v->id)]["total_of_revenue"] - $total_of_all_cost;
            $list[$k]->total_of_profile = $total_of_profile;

            $total_data['total_of_attendance_manpower'] += $channel_list[($v->id)]["total_of_attendance_manpower"];
            $total_data['total_of_delivery_quantity'] += $channel_list[($v->id)]["total_of_delivery_quantity"];
            $total_data['total_of_delivery_quantity_of_invalid'] += $channel_list[($v->id)]["total_of_delivery_quantity_of_invalid"];
            $total_data['total_of_call_charge_daily_cost'] += $channel_list[($v->id)]["total_of_call_charge_daily_cost"];
            $total_data['total_of_total_cost'] += $channel_list[($v->id)]["total_of_total_cost"];
            $total_data['total_of_channel_cost'] += $channel_list[($v->id)]["total_of_channel_cost"];

            $total_data['total_of_all_cost'] += $total_of_all_cost;

            $total_data['total_of_revenue'] += $channel_list[($v->id)]["total_of_revenue"];
            $total_data['total_of_profile'] += $total_of_profile;
        }
//        $list = $list->sortBy(['district_id'=>'asc'])->values();
//        $list = $list->sortBy(function ($item, $key) {
//            return $item['district_group_id'];
//        })->values();
//        dd($list->toArray());

        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }




    // 【流量统计】返回-列表-视图
    public function view_statistic_list_for_all($post_data)
    {
        $view_data["menu_active_statistic_list_for_all"] = 'active';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-list-for-all';
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

        $project_list = DK_Finance_Project::select('id','name')->whereIn('item_type',[1,21])->get();
        $staff_list = DK_Finance_User::select('id','username','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();
        $user_list = DK_Finance_User::select('id','username','true_name')->where('user_category',11)->get();

        $view_data['project_list'] = $project_list;
        $view_data['staff_list'] = $staff_list;
        $view_data['user_list'] = $user_list;


        $view_data['menu_active_of_statistic_export'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-export';
        return view($view_blade)->with($view_data);
    }
    // 【数据导出】日报
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
            $record_last = DK_Finance_Record::select('*')
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


        $user_id = 0;
        $staff_id = 0;
        $project_id = 0;

        // 客户
        if(!empty($post_data['user']))
        {
            if(!in_array($post_data['user'],[-1,0]))
            {
                $user_id = $post_data['user'];
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
                $project_er = DK_Finance_Project::find($project_id);
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


        // 日报
        $query = DK_Finance_Daily::select('*')
            ->with([
                'user_er'=>function($query) { $query->select('id','username','true_name'); },
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


        if($user_id) $query->where('user_id',$user_id);
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

            $cellData[$k]['user_er_name'] = $v['user_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
            $cellData[$k]['channel_source'] = $v['channel_source'];
            $cellData[$k]['user_name'] = $v['user_name'];
            $cellData[$k]['user_phone'] = $v['user_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $user_phone = $v['user_phone'];
                    $cellData[$k]['user_phone'] = substr($user_phone, 0, 3).'****'.substr($user_phone, -4);
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
            'user_er_name'=>'客户',
            'delivered_at'=>'交付时间',
            'creator_name'=>'创建人',
            'team'=>'团队',
            'published_time'=>'提交时间',
            'project_er_name'=>'项目',
            'channel_source'=>'渠道来源',
            'user_name'=>'客户姓名',
            'user_phone'=>'客户电话',
            'wx_id'=>'微信号',
            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'is_repeat'=>'是否重复',
            'inspector_name'=>'审核人',
            'inspected_time'=>'审核时间',
            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Finance_Record;

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


        $title = '【日报】'.date('Ymd.His').$project_title.$month_title.$time_title;

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部日报', function($sheet) use($cellData) {
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
                    'O'=>10,
                    'P'=>60,
                    'Q'=>10,
                    'R'=>10,
                    'S'=>20,
                    'T'=>10
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');





    }
    // 【数据导出】日报
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

        // 日报
        $query = DK_Finance_Daily::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'user_er'=>function($query) { $query->select('id','username','true_name'); },
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

            $cellData[$k]['user_er_name'] = $v['user_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
            $cellData[$k]['channel_source'] = $v['channel_source'];
            $cellData[$k]['user_name'] = $v['user_name'];
            $cellData[$k]['user_phone'] = $v['user_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $user_phone = $v['user_phone'];
                    $cellData[$k]['user_phone'] = substr($user_phone, 0, 3).'****'.substr($user_phone, -4);
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
            'user_er_name'=>'客户',
            'delivered_at'=>'交付时间',
            'creator_name'=>'创建人',
            'team'=>'团队',
            'published_time'=>'提交时间',
            'project_er_name'=>'项目',
            'channel_source'=>'渠道来源',
            'user_name'=>'客户姓名',
            'user_phone'=>'客户电话',
            'wx_id'=>'微信号',
            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'is_repeat'=>'是否重复',
            'inspector_name'=>'审核人',
            'inspected_time'=>'审核时间',
            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Finance_Record;

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




        $title = '【日报】'.date('Ymd.His').'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部日报', function($sheet) use($cellData) {
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
                    'O'=>10,
                    'P'=>60,
                    'Q'=>10,
                    'R'=>10,
                    'S'=>20,
                    'T'=>10
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





    // 【内容】【全部】返回-列表-数据
    public function get_record_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Finance_Record::select('*')->withTrashed()
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

            $subordinates_array = DK_Finance_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();

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





    // 【财务管理】返回-列表-视图
    public function view_record_funds_recharge_list($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $view_data["menu_active_of_record_funds_recharge"] = 'active';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.record.funds-recharge-list';
        return view($view_blade)->with($view_data);
    }
    // 【财务管理】返回-列表-数据
    public function get_record_funds_recharge_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

//        $id  = $post_data["id"];
        $query = DK_Finance_Funds_Recharge::select('*')
            ->with([
                'creator',
                'confirmer',
                'company_er'
            ]);

        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['order_id'])) $query->where('order_id', $post_data['order_id']);
        if(!empty($post_data['transaction_amount'])) $query->where('transaction_amount', $post_data['transaction_amount']);
        if(!empty($post_data['transaction_type'])) $query->where('transaction_type', $post_data['transaction_type']);
        if(!empty($post_data['transaction_receipt_account'])) $query->where('transaction_receipt_account', $post_data['transaction_receipt_account']);
        if(!empty($post_data['transaction_payment_account'])) $query->where('transaction_payment_account', $post_data['transaction_payment_account']);
        if(!empty($post_data['transaction_order'])) $query->where('transaction_order', $post_data['transaction_order']);

        if(!empty($post_data['transaction_time'])) $query->whereDate(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"), $post_data['transaction_time']);
        if(!empty($post_data['transaction_start'])) $query->whereDate(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"), '>=', $post_data['transaction_start']);
        if(!empty($post_data['transaction_ended'])) $query->whereDate(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"), '<=', $post_data['transaction_ended']);


        // 类型：收入 | 支出
        if(!empty($post_data['finance_type']))
        {
            if(in_array($post_data['finance_type'],[1,91]))
            {
                $query->where('finance_type', $post_data['finance_type']);
            }
        }
        // 确认
        if(isset($post_data['finance_confirm']))
        {
            if(in_array($post_data['finance_confirm'],[0,1]))
            {
                if($post_data['finance_confirm'] == 1) $query->where('is_confirmed', 1);
                else if($post_data['finance_confirm'] == 0) $query->where('is_confirmed', '!=', 1);

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


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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

//            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
//            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【财务管理】返回-列表-视图
    public function view_record_funds_using_list($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $view_data["menu_active_of_record_funds_using"] = 'active';
        $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.record.funds-using-list';
        return view($view_blade)->with($view_data);
    }
    // 【财务管理】返回-列表-数据
    public function get_record_funds_using_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

//        $id  = $post_data["id"];
        $query = DK_Finance_Funds_Using::select('*')
            ->with([
                'creator',
                'confirmer',
                'project_er'
            ]);

        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['order_id'])) $query->where('order_id', $post_data['order_id']);
        if(!empty($post_data['transaction_amount'])) $query->where('transaction_amount', $post_data['transaction_amount']);
        if(!empty($post_data['transaction_type'])) $query->where('transaction_type', $post_data['transaction_type']);
        if(!empty($post_data['transaction_receipt_account'])) $query->where('transaction_receipt_account', $post_data['transaction_receipt_account']);
        if(!empty($post_data['transaction_payment_account'])) $query->where('transaction_payment_account', $post_data['transaction_payment_account']);
        if(!empty($post_data['transaction_order'])) $query->where('transaction_order', $post_data['transaction_order']);

        if(!empty($post_data['transaction_time'])) $query->whereDate(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"), $post_data['transaction_time']);
        if(!empty($post_data['transaction_start'])) $query->whereDate(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"), '>=', $post_data['transaction_start']);
        if(!empty($post_data['transaction_ended'])) $query->whereDate(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"), '<=', $post_data['transaction_ended']);


        // 类型：收入 | 支出
        if(!empty($post_data['finance_type']))
        {
            if(in_array($post_data['finance_type'],[1,91]))
            {
                $query->where('finance_type', $post_data['finance_type']);
            }
        }
        // 确认
        if(isset($post_data['finance_confirm']))
        {
            if(in_array($post_data['finance_confirm'],[0,1]))
            {
                if($post_data['finance_confirm'] == 1) $query->where('is_confirmed', 1);
                else if($post_data['finance_confirm'] == 0) $query->where('is_confirmed', '!=', 1);

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


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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

//            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
//            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }





    /*
     * 说明
     *
     */


    // 【访问记录】
    public function record($post_data)
    {
        $record = new K_Record();

        $browseInfo = getBrowserInfo();
        $type = $browseInfo['type'];
        if($type == "Mobile") $post_data["open_device_type"] = 1;
        else if($type == "PC") $post_data["open_device_type"] = 2;

        $post_data["referer"] = $browseInfo['referer'];
        $post_data["open_system"] = $browseInfo['system'];
        $post_data["open_browser"] = $browseInfo['browser'];
        $post_data["open_app"] = $browseInfo['app'];

        $post_data["ip"] = Get_IP();
        $bool = $record->fill($post_data)->save();
        if($bool) return true;
        else return false;
    }


    // 【访问记录】
    public function record_for_user_operate($record_object,$record_category,$record_type,$creator_id,$item_id,$operate_object,$operate_category,$operate_type = 0,$column_key = '',$before = '',$after = '')
    {
        $record = new DK_Finance_Record;

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




}